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
class FormProcessor_pnProcessCertificate extends pnFormProcessor
{
	protected $_admin;
	protected $_show;
	protected $_reload;

	public function __construct()
	{
		parent::__construct();
		$this->_admin = new pnAdmin();
		$this->_reload = false;
	}

	public function process(array $params = array())
	{
		try {
			$this->_admin->load();
		}
		catch (Exception $ex) {
			$this->addError('Fatal', $ex->getMessage());
			return false;
		}
		if ($this->isPost()) {
			$action = $params['pn_action'];
			if ($action == 'create_cert') {
				return $this->_processCreateCert($params);
			}
			else if ($action == 'delete_cert') {
				return $this->_processDeleteCert($params);
			}
		}
		else {
			if (!isset($this->_admin->cert)) {
				return $this->_processLoadUser();
			}
			else {
				return $this->_processLoadCert();
			}
		}
	}

	protected function _processDeleteCert($params)
	{
		$this->subject = array();
		$this->validTo = 0;
		try {
			$x509 = pnPEM::ReadX509Cert($this->_admin->cert);
			$this->subject = $x509->getSubject();
			$this->validTo = $x509->getValidTo();
		}
		catch (Exception $ex) {
			$this->addError('Fatal', $ex->getMessage());
			return false;
		}
		$dedelete = $params['dodelete'];
		if ($dedelete != 'yes') {
			$this->addError('dodelete', __('Check this box to delete the Certificate'));
			$this->_show = 'load';
			return false;
		}
		try {
			$this->_admin->delete();
		}
		catch (Exception $ex) {
			$this->addError('Fatal', $ex->getMessage());
			return false;
		}

		$contacts = new pnContactCollection();
		try {
			$contacts->load();
			foreach ($contacts as $contact) {
				$contact->status = pnContact::AWAITING_CONF_LOCAL;
				$contact->save();
			}
		}
		catch (Exception $ex) {
			$this->addError('Fatal', $ex->getMessage());
			return false;
		}		
		
		$this->_reload = true;
		$this->_show = 'create';
		return true;
	}

	protected function _processLoadCert()
	{
		$this->subject = array();
		$this->validTo = 0;
		try {
			$x509 = pnPEM::ReadX509Cert($this->_admin->cert);
			$this->subject = $x509->getSubject();
			$this->validTo = $x509->getValidTo();
			$this->identity_key = $this->_admin->identity_key;
		}
		catch (Exception $ex) {
			$this->addError('Fatal', $ex->getMessage());
			return false;
		}
		$this->_show = 'load';
		return true;
	}

	protected function _processLoadUser()
	{
		global $current_user;
		get_currentuserinfo();

		$this->name = $current_user->user_login;
		$this->commonName = $current_user->display_name;
		$this->organizationName = get_option('siteurl');
		$this->subjectAltName = $current_user->user_email;
		$this->countryName = '';
		$this->stateOrProvinceName = '';
		$this->localityName = '';

		$this->_show = 'create';
		return true;
	}

	protected function _processCreateCert($params)
	{
		global $current_user;
		get_currentuserinfo();

		$this->name = $this->sanitize($params['name']);
		$this->commonName = $this->sanitize($params['commonName']);
		$this->organizationName = $this->sanitize($params['organizationName']);
		$this->subjectAltName = $this->sanitize($params['subjectAltName']);
		$this->countryName = $this->sanitize($params['countryName']);
		$this->stateOrProvinceName = $this->sanitize($params['stateOrProvinceName']);
		$this->localityName = $this->sanitize($params['localityName']);

		if (!isset($this->commonName)) {
			$this->addError('commonName', __('Please enter a valid Name'));
		}
		if (!isset($this->organizationName)) {
			$this->addError('organizationName', __('Please enter a valid URL'));
		}
		if (!isset($this->subjectAltName)) {
			$this->addError('subjectAltName', __('Please enter a valid Email Address'));
		}
		if (!isset($this->countryName)) {
			$this->addError('countryName', __('Please enter a valid Country'));
		}
		if (!isset($this->localityName)) {
			$this->addError('localityName', __('Please enter a valid City'));
		}
		if (!$this->hasError('subjectAltName') && !$this->validEmail($this->subjectAltName)) {
			$this->addError('subjectAltName', __('Please enter a valid Email Address'));
		}
		if (!isset($this->name)) {
			$this->addError('Fatal', __('Invalid parameters.'));
		}
		if ($this->hasError()) {
			$this->_show = 'create';
			return false;
		}
		if (!isset($this->stateOrProvinceName)) {
			$this->stateOrProvinceName = $this->countryName;
		}

		$this->organizationName = trailingslashit($this->organizationName);
		$this->organizationName = sanitize_url($this->organizationName, array('http'));
		if (empty($this->organizationName)) {
			$this->organizationName = get_option('siteurl');
			$this->addError('organizationName', "Only \"http\" protocol supported at the moment.");
			$this->_show = 'create';
			return false;
		}
		$dn = array('name' => $this->name,
					'commonName' => $this->commonName,
					'subjectAltName' => $this->subjectAltName,
					'countryName' => $this->countryName,
					'stateOrProvinceName' => $this->stateOrProvinceName,
					'localityName' => $this->localityName,
					'organizationName' => $this->organizationName);


		$rsa = new pnRSA();
		$x509 = new pnX509();
		try {
			$rsa->generate();
			$x509->generate($dn, $rsa);
			$pem = pnPEM::WriteX509Cert($x509);
			$this->_admin->cert = $pem;
			$this->_admin->pkey = pnPEM::WriteRSAPrivate($rsa);
			$this->_admin->user_login = $this->name;
			$this->_admin->identity_key = base64_encode(hash('sha256', $pem, TRUE));
			$this->_admin->save();
		}
		catch (Exception $ex) {
			$this->addError('Fatal', $ex->getMessage());
			$this->_show = 'create';
			return false;							
		}

		$this->_reload = true;
		return true;
	}

	public function reload()
	{
		return $this->_reload;
	}

	public function getView()
	{
		return $this->_show;
	}
}