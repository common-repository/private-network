<?php
/*  Copyright 2008  Andrea Belvedere  (email : scieck at gmail dot com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
class pnAuthenticate
{
	protected $_contact;
	protected $_admin;
	protected $_http;
	protected $_errors;
	protected $_secret;
	protected $_nonce;

	public function __construct(pnContact $contact, pnAdmin $admin)
	{
		$this->_errors = array();
		$this->_contact = $contact;
		$this->_admin = $admin;
		$this->_http = new pnHTTP();
		$this->_secret = null;
		$this->_nonce = null;
	}

	public function pnInitialize()
	{
		$params = array('pn_protocol' => PN_PROTOCOL,
						'pn_action' => 'pn-initialize',
						'pn-ik-from' => $this->_admin->identity_key,
						'pn-ik-to' => $this->_contact->identity_key);
		$x509 = null;
		$sub = null;
		try {
			$x509 = pnPEM::ReadX509Cert($this->_contact->cert);
			$sub = $x509->getSubject();
		}
		catch (Exception $ex) {
			$this->addError('Init', $ex->getMessage());
			return false;
		}
		if (!$this->_http->submit($this->_contact->url, $params)) {
			$this->addError('Init', $this->_http->error);
			return false;
		}
		if ($this->_http->status != 200) {
			$this->addError('Fatal', "{$url} ".__('returned error:'). " {$http->response_code}");
			return false;
		}
		$status = $this->_http->getParam('status');
		if ($status != 'success') {
			$err = $this->_http->getParam('error');
			$this->addError('Fatal', is_null($err) ? __('Unknown server response.') : $err);
			return false;
		}
		$b64_signature = $this->_http->getParam('pn_signature');
		$b64_nonce = $this->_http->getParam('pn_nonce');

		if (empty($b64_signature) || empty($b64_nonce)) {
			$this->addError('Fatal', __('Invalid Response from: ').$this->_contact->url);
			return false;
		}
		$signature = base64_decode($b64_signature);
		$nonce = base64_decode($b64_nonce);
		if (($signature === FALSE) && ($nonce === FALSE)) {
			$this->addError('Fatal', __('Invalid Response from: ').$this->_contact->url);
			return false;
		}
		// verify signature
		try {
			if (!$x509->verify($nonce, $signature)) {
				$this->addError('Fatal', __('Invalid Signature from: ').$this->_contact->url);
				return false;			
			}
		}
		catch (Exception $ex) {
			$this->addError('Init', $ex->getMessage());
			return false;
		}
		// decrypt the nonce
		$nonce_dec = null;
		try {
			$priKey = pnPEM::ReadRSAPrivate($this->_admin->pkey);
			$nonce_dec = $priKey->decrypt($nonce);
		}
		catch (Exception $ex) {
			$this->addError('Init', $ex->getMessage());
			return false;			
		}
		// split the decrypted nonce at ':',
		// the first half is the $nonce that will be sent 
		// on the clear, the second half is half of the secret key
		$ans = split(':', $nonce_dec);
		if (count($ans) != 2) {
			$this->addError('Fatal', __('Invalid Response from: ').$this->_contact->url);
			return false;			
		}
		$this->_nonce = $ans[0];
		$this->_secret = $ans[1];
		return true;
	}

	public function pnContinue()
	{
		if (empty($this->_secret) || empty($this->_nonce)) {
			$this->addError("Cont", __('Invalid State'));
			return false;
		}
		$halfsec = base64_encode(hash('sha256', uniqid(mt_rand(), true), true));
		$to_encrypt = $this->_nonce . ':' . $halfsec;

		$contact_x509 = null;
		$admin_pkey = null;
		$enc_nonce = null;
		$signature = null;
		try {
			$contact_x509 = pnPEM::ReadX509Cert($this->_contact->cert);
			$admin_pkey = pnPEM::ReadRSAPrivate($this->_admin->pkey);
			$enc_nonce = $contact_x509->encrypt($to_encrypt);
			$signature = $admin_pkey->sign($enc_nonce);
		}
		catch (Exception $ex) {
			$this->addError("Cont", $ex->getMessage());
			return false;
		}
		if (is_null($enc_nonce) || is_null($signature)) {
			$this->addError("Cont", __("Encryption Errors"));
			return false;			
		}
		$params = array('pn_protocol' => PN_PROTOCOL,
						'pn_action' => 'pn-continue',
						'pn-session' => $this->_nonce,
						'pn-signature' => base64_encode($signature),
						'pn-nonce' => base64_encode($enc_nonce));

		if (!$this->_http->submit($this->_contact->url, $params)) {
			$this->addError('Init', $this->_http->error);
			return false;
		}
		if ($this->_http->status != 200) {
			$this->addError('Fatal', "{$url} ".__('returned error:'). " {$this->_http->response_code}");
			return false;
		}
		$status = $this->_http->getParam('status');
		if ($status != 'success') {
			$err = $this->_http->getParam('error');
			$this->addError('Fatal', is_null($err) ? __('Unknown server response.') : $err);
			return false;
		}
		$this->_secret .= $halfsec;
		$session = $this->_http->getParam('pn-session');
		if (strcmp($session, $this->_nonce) == 0) {
			return $this->_http->getParam('pn-content');
		}
		else {
			return __('Invalid Session from: ').$this->_contact->url;
		}
	}

	public function hasError($name = null)
	{
		if (is_null($name)) {
			return !empty($this->_errors);
		}
		return array_key_exists($name, $this->_errors);
	}

	public function getError($name = null)
	{
		if (is_null($name)) {
			return $this->_errors;
		}
		return array_key_exists($name, $this->_errors) ? $this->_errors[$name] : null;
	}

	protected function addError($name, $error)
	{
		$this->_errors[$name] = $error;
	}
}