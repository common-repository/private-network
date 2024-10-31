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
class FormProcessor_pnProcessContacts extends pnFormProcessor
{
	protected $_reload;
	protected $_contacts;
	protected $_admin;

	public function __construct(pnContactCollection $contacts)
	{
		parent::__construct();
		$this->_reload = false;
		$this->_contacts = $contacts;
		$this->_admin = new pnAdmin();
	}

	public function process(array $params = array())
	{
		try {
			$this->_admin->load();
		}
		catch (Exception $ex) {
			$this->addError('Fatal', $ex->getMessage());
		}
		if ($this->isPost()) {
			$action = $params['pn_action'];
			if ($action == 'add_contact') {
				$this->_processAddContact($params);
			}
			else {
				foreach ($params as $key => $value) {
					if (strcmp($key, 'delete_contact') == 0) {
						$this->_processDeleteContact($params);
						break;
					}
					else if (strstr($key, 'contact_action_') !== FALSE) {
						$contact = new pnContact();
						$pos = strrpos($key, '_');
						$contact->id = substr($key, ++$pos);
						$contact->load();
						switch ($contact->status) {
						case pnContact::AWAITING_CONF_LOCAL:
							$this->_processConfirmContact($contact);
							break;
						case pnContact::DISABLED:
							$this->_processEnableContact($contact);
							break;
						case pnContact::ENABLED:
							$this->_processDisableContact($contact);
							break;
						case pnContact::CERT_REPLACE:
							$this->_processCertReplace($contact);
							break;
						default:
							$this->addError('Fatal', __('Contact in unrecognized status'));
						}
						break;
					}
				}
			}
		}
		$this->_contacts->load();
		return !$this->hasError();
	}

	protected function _processCertReplace($contact)
	{
		$new_x509 = null;
		$sub = array();
		try {
			$new_x509 = pnPEM::ReadX509Cert($contact->cert_replace);
			$sub = $new_x509->getSubject();
		}
		catch (Exception $ex) {
			$this->addError('Fatal', $ex->getMessage());
			return false;
		}

		if (strcmp($contact->cert_replace, $contact->cert) == 0) {
			$contact->status = pnContact::DISABLED;
		}
		else {
			$contact->status = pnContact::AWAITING_CONF_LOCAL;
			$contact->display_name = $sub['commonName'];
			$contact->email = $sub['X509v3 Subject Alternative Name'];
			$contact->cert = $contact->cert_replace;
			$contact->identity_key = base64_encode(hash('sha256', $contact->cert, TRUE));
		}

		$contact->cert_replace = null;
		try {
			$contact->save();
		}
		catch (Exception $ex) {
			$this->addError('Fatal', $ex->getMessage());
		}
		return $this->_reload = !$this->hasError();
	}
	
	protected function _processDisableContact($contact)
	{
		$contact->status = pnContact::DISABLED;
		try {
			$contact->save();
		}
		catch (Exception $ex) {
			$this->addError('Fatal', $ex->getMessage());
		}
		return $this->_reload = !$this->hasError();
	}

	protected function _processEnableContact($contact)
	{
		$contact->status = pnContact::ENABLED;
		try {
			$contact->save();
		}
		catch (Exception $ex) {
			$this->addError('Fatal', $ex->getMessage());
		}
		return $this->_reload = !$this->hasError();
	}

	protected function _processConfirmContact($contact)
	{
		if (!isset($this->_admin->cert)) {
			$this->addError('Fatal', __('Your must create a valid Certificate before confirming a new contact.'));
			return false;
		}
		$params = array('pn_protocol' => PN_PROTOCOL,
						'pn_x509' => $this->_admin->cert,
						'pn_action' => 'confirm-contact',
						'pn_user_login' => $contact->user_login);
		$http = new pnHTTP();
		if (!$http->submit($contact->url, $params)) {
			$this->addError('Fatal', $http->error);
			return false;
		}
		if ($http->status != 200) {
			$this->addError('Fatal', "{$url} ".__('returned error:'). " {$http->response_code}");
			return false;
		}
		$r_params = $http->getParams();
		$status = isset($r_params['status']) ? $r_params['status'] : '';
		if ($status != 'success') {
			$err = isset($r_params['error']) ? $r_params['error'] : __('Unknown server response.');
			$this->addError('Fatal', $err);
			return false;
		}
		$contact->status = pnContact::DISABLED;
		try {
			$contact->save();
		}
		catch (Exception $ex) {
			$this->addError('Fatal', $ex->getMessage());
		}
		return $this->_reload = !$this->hasError();
	}

	protected function _processDeleteContact($params)
	{
		$checked = $params['checked'];
		if (empty($checked)) {
			$this->addError('Fatal', __('Please check a Contact to delete.'));
			return false;
		}
		try {
			foreach ($checked as $id) {
				$contact = new pnContact();
				$contact->id = $id;
				$contact->delete();
			}
		}
		catch (Exception $ex) {
			$this->addError('Fatal', $ex->getMessage());
		}
		return $this->_reload = !$this->hasError();
	}

	protected function _processAddContact($params)
	{
		if (!isset($this->_admin->cert)) {
			$this->addError('Fatal', __('Your must create a valid Certificate before adding a new contact.'));
			return;
		}
		$this->contact_url = $this->sanitize($params['contact_url']);
		$this->contact_name = $this->sanitize($params['contact_name']);
		if (!isset($this->contact_url)) {
			$this->addError('contact_url', __('Please enter a valid url address, i.e: www.example.com or example.com'));
		}
		if (!isset($this->contact_name)) {
			$this->contact_name = 'admin';
		}
		if ($this->hasError()) {
			return false;
		}
		$url = trailingslashit($this->contact_url);
		// sanitize_url return empty string if protocol is not
		// included in the passed array
		$url = sanitize_url($url, array('http'));
		if (empty($url)) {
			$this->addError('Fatal', __('Only "http" protocol supported for now.'));
			return false;
		}
		// try to avoid to connect to it self
		$siteurl = trailingslashit(get_option('siteurl'));
		$siteurl = sanitize_url($siteurl, array('http'));
		if (strcasecmp($siteurl, $url) == 0) {
			$this->addError('Fatal', __('You cannot enter this site url.'));
			return false;
		}

		$contact = new pnContact();
		if ($contact->contactExist($url, $this->contact_name)) {
			$this->addError('Fatal', __('Contact already exist'));
			return false;
		}

		$params = array('pn_protocol' => PN_PROTOCOL,
						'pn_action' => 'add-contact',
						'pn_admin' => $this->contact_name,
						'pn_x509' => $this->_admin->cert);

		$http = new pnHTTP();
		if (!$http->submit($url, $params)) {
			$this->addError('Fatal', $http->error);
			return false;
		}
		if ($http->status != 200) {
			$this->addError('Fatal', "{$url} ".__('returned error:'). " {$http->response_code}");
			return false;
		}
		$r_params = $http->getParams();
		$status = isset($r_params['status']) ? $r_params['status'] : '';
		if ($status != 'success') {
			$err = isset($r_params['error']) ? $r_params['error'] : __('Unknown server response, pleace check contact url and try again.');
			$this->addError('Fatal', $err);
			return false;
		}

		$http->lastredirectaddr = trim($http->lastredirectaddr);
		if (!empty($http->lastredirectaddr)) {
			$url = trailingslashit($http->lastredirectaddr);
			$url = sanitize_url($url, array('http'));
			$id = $contact->contactExist($url, $this->contact_name);
			if (!is_null($id)) {
				$contact->id = $id;
			}
		}

		$contact->display_name = $this->contact_name;
		$contact->user_login = $this->contact_name;
		$contact->url = $url;
		$contact->status = pnContact::AWAITING_CONF_REMOTE;

		unset($this->contact_url);
		unset($this->contact_name);

		try {
			$contact->save();
		}
		catch (Exception $ex) {
			$this->addError('Fatal', $ex->getMessage());
		}

		return $this->_reload = !$this->hasError();
	}

	public function reload()
	{
		return $this->_reload;
	}
}