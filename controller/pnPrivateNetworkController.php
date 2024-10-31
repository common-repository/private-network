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
class pnPrivateNetworkController
{
	public function __construct()
	{
		add_action('wp_head', array(&$this, 'pnHead'));
		add_action('init', array(&$this, 'processRequest'));
		add_filter('the_content', array(&$this, 'processContent'),9);
	}

	// i.e. [pn-SeJGgRvqd6ANLgdcBiV/zAaBBjZUY8ma8tsijm+GjYU=]
	public function processContent($content)
	{
		$regex = '/\[\s*pn-\s*([a-z0-9\/\+]{43}[=]{0,2})\s*\]/i';
		return preg_replace_callback($regex, array(&$this, 'callbackProcessContent'), $content, 1);
	}

	public function callbackProcessContent($matches)
	{
		$contact = new pnContact();
		$admin = new pnAdmin();
		if (!isset($admin->id)) {
			return __('Administrator log-in is required to view this post.');
		}
		try {
			$admin->load();
		}
		catch (Exception $ex) {
			return nl2br($ex->getMessage());
		}
		if (!isset($admin->identity_key)) {
			return __('View of this post is reserved to Administrators with a valid Certificate.');
		}
		if (!$contact->loadContact($matches[1])) {
			return __('Contact with tag: ').$matches[0]. __(' not found.');
		}
		if (strcmp($admin->identity_key, $contact->identity_key) == 0) {
			return $matches[0];
		}
		$auth = new pnAuthenticate($contact, $admin);
		if (!$auth->pnInitialize()) {
			return nl2br(implode('<br />', $auth->getError()));
		}
		$ppost = $auth->pnContinue();
		if ($ppost === FALSE) {
			return nl2br(implode('<br />', $auth->getError()));
		}
		return $ppost;
	}

	public function processRequest()
	{
		$action = isset($_POST['pn_action']) ? $_POST['pn_action'] : null;
		$status = array();
		switch ($action) {
		case 'add-contact':
			$status = $this->_processAddContact();
			break;
		case 'confirm-contact':
			$status = $this->_processConfirmContact();
			break;
		case 'pn-initialize':
			$status = $this->_processPnInitialize();
			break;
		case 'pn-continue':
			$status = $this->_processPnContinue();
			break;
		default:
			return;
		}
		$this->_sendResponse($status);
	}

	protected function _processPnContinue()
	{
		$status = array();
		$pnSession = new pnSession();
		$session = isset($_POST['pn-session']) ? $_POST['pn-session'] : null;
		$signature = isset($_POST['pn-signature']) ? $_POST['pn-signature'] : null;
		$pn_nonce = isset($_POST['pn-nonce']) ? $_POST['pn-nonce'] : null;
		if (empty($signature) || empty($pn_nonce) || !isset($session)) {
			$status['status'] = 'failure';
			$status['error'] = __('Invalid Request');
			return $status;
		}
		$pnSession->session = $session;
		try {
			if (!$pnSession->load()) {
				$status['status'] = 'failure';
				$status['error'] = __("Missing Session.");
				return $status;				
			}
		}
		catch (Exception $ex) {
			$status['status'] = 'failure';
			$status['error'] = __("Remote Server Error. ").$ex->getMessage();
			return $status;			
		}
		// check ip
		$ip = $_SERVER['REMOTE_ADDR'];
		if ($ip != $pnSession->ip) {
			$status['status'] = 'failure';
			$status['error'] = __("IP mismatch");
			return $status;
		}
		$admin = new pnAdmin();
		$contact = new pnContact();
		try {
			if (!$admin->loadAdmin($pnSession->to_ik)) {
				$status['status'] = 'failure';
				$status['error'] = __('Contact not found.');
				return $status;
			}
			if (!$contact->loadContact($pnSession->from_ik, $admin->id)) {
				$status['status'] = 'failure';
				$status['error'] = __('Remote contact not found.');
				return $status;				
			}			
		}
		catch (Exception $ex) {
			$status['status'] = 'failure';
			$status['error'] = __("Remote Server Error. ").$ex->getMessage();
			return $status;
		}
		// verify signature
		$pn_nonce = base64_decode($pn_nonce);
		$signature = base64_decode($signature);
		try {
			$x509 = pnPEM::ReadX509Cert($contact->cert);
			if (!$x509->verify($pn_nonce, $signature)) {
				$status['status'] = 'failure';
				$status['error'] = __("Invalid Signature.");
				return $status;	
			}
		}
		catch (Exception $ex) {
			$status['status'] = 'failure';
			$status['error'] = __("Remote Server Error. ").$ex->getMessage();
			return $status;			
		}
		// decrypt data
		try {
			$pkey = pnPEM::ReadRSAPrivate($admin->pkey);
			$pn_nonce = $pkey->decrypt($pn_nonce);
		}
		catch (Exception $ex) {
			$status['status'] = 'failure';
			$status['error'] = __("Remote Server Error. ").$ex->getMessage();
			return $status;			
		}

		// split the decrypted nonce at ':',
		// the first half is the $nonce that will be sent 
		// on the clear, the second half is the other half of the secret key
		$ans = split(':', $pn_nonce);
		if (count($ans) != 2) {
			$status['status'] = 'failure';
			$status['error'] = __("Invalid Request from: ").$contact->url;
			return $status;			
		}
		// compare the session keys, the one that was encrypted and
		// the one sent on the clear MUST be equal
		if (strcmp($ans[0], $pnSession->session) != 0) {
			$status['status'] = 'failure';
			$status['error'] = __("Session mismatch.");
			return $status;
		}
		$pnSession->secret .= $ans[1];
		try {
			$pnSession->save();
		}
		catch (Exception $ex) {
			$status['status'] = 'failure';
			$status['error'] = __("Remote Server Error ").$ex->getMessage();
			return $status;
		}

		$status['status'] = 'success';
		$status['pn-session'] = $session;
		if ($contact->status == pnContact::ENABLED) {
			try {
				$status['pn-content'] = $this->_getData($admin, $contact);
			}
			catch (Exception $ex) {
				$status['status'] = 'failure';
				$status['error'] = __("Remote Server Error ").$ex->getMessage();
			}
		}
		else {
			$status['pn-content'] = __('No posts from: ').get_option('siteurl');
		}
		return $status;
	}

	protected function _getData(pnAdmin $admin, pnContact $contact)
	{
		global $wpdb;
		$opt = new pnDataObjectUtil();
		$cat_inc_public = $opt->getOption(pnACL::PN_CAT_INC_PUBLIC."_{$contact->id}");
		$tag_inc_public = $opt->getOption(pnACL::PN_TAG_INC_PUBLIC."_{$contact->id}");
		if ($cat_inc_public == 'yes') {
			$cat_inc_public = '';
		}
		else {
			$cat_inc_public = " AND p.post_status='private'";
		}
		if ($tag_inc_public == 'yes') {
			$tag_inc_public = '';
		}
		else {
			$tag_inc_public = " AND p.post_status='private'";
		}
		$regex = '\[\s*pn-\s*[a-zA-Z0-9\/\+]{43}[=]{0,2}\s*\]';
		$acls = new pnACLCollection();
		$acls->filterOn('admin_id=', $admin->id);
		$acls->filterOn('contact_id=', $contact->id);
		$acls->load();
		$postsid = array();
		foreach ($acls as $acl) {
			if ($acl->share_type == 'category') {
				$query="SELECT p.ID FROM $wpdb->posts as p JOIN wp_term_relationships AS tr ON p.ID=tr.object_id ".
					"JOIN wp_term_taxonomy AS tt ON tt.term_taxonomy_id=tr.term_taxonomy_id ".
					"JOIN wp_pn_acl AS acl ON acl.share_id=tt.term_id ".
					"WHERE p.post_author={$admin->id} AND p.post_content NOT REGEXP '{$regex}' ".
					"AND p.post_type='post' AND tt.term_id={$acl->share_id}{$cat_in_public}";

				$ans = $wpdb->get_col($query);
				if (is_array($ans)) {
					$postsid = array_merge($postsid, $ans);
				}
			}
			if ($acl->share_type == 'tag') {
				$query="SELECT p.ID FROM $wpdb->posts as p JOIN wp_term_relationships AS tr ON p.ID=tr.object_id ".
					"JOIN wp_term_taxonomy AS tt ON tt.term_taxonomy_id=tr.term_taxonomy_id ".
					"JOIN wp_pn_acl AS acl ON acl.share_id=tt.term_id ".
					"WHERE p.post_author={$admin->id} AND p.post_content NOT REGEXP '{$regex}' ".
					"AND p.post_type='post' AND tt.term_id={$acl->share_id}{$tag_inc_public}";

				$ans = $wpdb->get_col($query);
				if (is_array($ans)) {
					$postsid = array_merge($postsid, $ans);
				}
			}
			if ($acl->share_type == 'post') {
				$postsid[] = $acl->share_id;
			}
			if ($acl->share_type == 'page') {
				$postsid[] = $acl->share_id;
			}
		}
		$postsid = array_unique($postsid);
		$args = array('include' => implode(',',$postsid),
					  'numberposts' => -1,
					  'post_type' => 'any',
					  'post_status' => "all");

		$pageposts = null;
		if (!empty($postsid)) {
			$pageposts = get_posts($args);
		}
		$tpl = new pnTemplater(PN_DIR . '/view');
		$tpl->set('pageposts', $pageposts);
		return $tpl->fetch("pn-posts.tpl.php");
	}

	/*
	protected function _getPrivateData(pnAdmin $admin)
	{
		global $wpdb;
		$regex = '\[\s*pn-\s*[a-zA-Z0-9\/\+]{43}[=]{0,2}\s*\]';
		$query = "SELECT $wpdb->posts.* FROM $wpdb->posts WHERE $wpdb->posts.post_status='private' AND ".
			"$wpdb->posts.post_author={$admin->id} AND $wpdb->posts.post_content NOT REGEXP '{$regex}' ".
			"ORDER BY $wpdb->posts.post_date DESC LIMIT 10";
		$pageposts = $wpdb->get_results($query, OBJECT);

		$tpl = new pnTemplater(PN_DIR . '/view');
		$tpl->set('pageposts', $pageposts);
		return $tpl->fetch("pn-posts.tpl.php");
	}
	*/

	protected function _processPnInitialize()
	{
		$status = array();
		$ik_from = isset($_POST['pn-ik-from']) ? $_POST['pn-ik-from'] : null;
		$ik_to = isset($_POST['pn-ik-to']) ? $_POST['pn-ik-to'] : null;
		if (empty($ik_from) || empty($ik_to)) {
			$status['status'] = 'failure';
			$status['error'] = __('Invalid Request');
			return $status;			
		}
		$admin = new pnAdmin();
		$contact = new pnContact();
		try {
			if (!$admin->loadAdmin($ik_to)) {
				$status['status'] = 'failure';
				$status['error'] = __('Contact not found.');
				return $status;
			}
			if (!$contact->loadContact($ik_from, $admin->id)) {
				$status['status'] = 'failure';
				$status['error'] = __('Remote contact not found.');
				return $status;				
			}
		}
		catch (Exception $ex) {
			$status['status'] = 'failure';
			$status['error'] = __("Remote Server Error.");
			return $status;
		}
		// check IP
		$ip = $_SERVER['REMOTE_ADDR'];
		if ($contact->ip != $ip) {
			$status['status'] = 'failure';
			$status['error'] = __("IP mismatch");
			return $status;
		}
		// this $nonce will be sent back on the clear
		$nonce = base64_encode(hash('sha256', uniqid(mt_rand(), true), true));
		// this $halfsec will be stored as half of the secret key 
		// (together with the other half could be used between the to hosts to encrypt data)
		$halfsec = base64_encode(hash('sha256', uniqid(mt_rand(), true), true));
		$contact_x509 = null;
		$admin_pkey = null;
		$enc_nonce = null;
		$signature = null;

		$to_encrypt = $nonce.':'.$halfsec;
		try {
			$contact_x509 = pnPEM::ReadX509Cert($contact->cert);
			$admin_pkey = pnPEM::ReadRSAPrivate($admin->pkey);
			$enc_nonce = $contact_x509->encrypt($to_encrypt);
			$signature = $admin_pkey->sign($enc_nonce);
		}
		catch (Exception $ex) {
			$status['status'] = 'failure';
			$status['error'] = __("Remote Server Error. ").$ex->getMessage();
			return $status;			
		}
		if (is_null($enc_nonce) || is_null($signature)) {
			$status['status'] = 'failure';
			$status['error'] = __("Remote Server Error.");
			return $status;
		}
		$status['status'] = 'success';
		$status['pn_signature'] = base64_encode($signature);
		$status['pn_nonce'] = base64_encode($enc_nonce);

		$pnSession = new pnSession();
		$pnSession->session = $nonce;
		$pnSession->secret = $halfsec;
		$pnSession->from_ik = $ik_from;
		$pnSession->to_ik = $ik_to;
		$pnSession->ip = $ip;
		$pnSession->tstamp = time();
		try {
			$pnSession->save();
		}
		catch (Exception $ex) {
			$status['status'] = 'failure';
			$status['error'] = __("Remote Server Error. ").$ex->getMessage();
			return $status;
		}
		return $status;
	}

	protected function _processConfirmContact()
	{
		$status = array();
		$pem_x509 = isset($_POST['pn_x509']) ? $_POST['pn_x509'] : null;
		$user_login = isset($_POST['pn_user_login']) ? trim($_POST['pn_user_login']) : null;
		if (empty($pem_x509)) {
			$status['status'] = 'failure';
			$status['error'] = __('Invalid Certificate1');
			return $status;
		}
		if (empty($user_login)) {
			$status['status'] = 'failure';
			$status['error'] = __('Invalid Request');
			return $status;
		}
		$admin_id = pnAdmin::IDFromUsername($user_login);
		if (empty($admin_id)) {
			$status['status'] = 'failure';
			$status['error'] = __('Invalid Request');
			return $status;			
		}
		$x509 = pnPEM::ReadX509Cert($pem_x509);
		$sub = $x509->getSubject();
		$url = trim($sub['organizationName']);
		if (empty($url)) {
			$status['status'] = 'failure';
			$status['error'] = __('Invalid Certificate2');
			return $status;			
		}
		$url = trailingslashit($url);
		$url = sanitize_url($url, array('http'));

		// check we have a host
		$host = @parse_url($url, PHP_URL_HOST);
		if ($host === FALSE) {
			$status['status'] = 'failure';
			$status['error'] = __('Invalid Certificate3'); 
			return $status;
		}
		// try to validate ip address
		$ip_verified = false;
		$ip = $_SERVER['REMOTE_ADDR'];
		$hip = gethostbyname($host);
		if ($hip == $ip) {
			$ip_verified = true;
		}

		$un_admin = trim($sub['name']);
		$name = trim($sub['commonName']);
		$email = trim($sub['X509v3 Subject Alternative Name']);
		if (empty($un_admin) || empty($name) || empty($email)) {
			$status['status'] = 'failure';
			$status['error'] = __('Invalid Certificate4');
			return $status;
		}
		$contact = new pnContact();
		$id = $contact->contactExist($url, $un_admin, $admin_id);
		if (empty($id)) {
			$status['status'] = 'failure';
			$status['error'] = __('Contact details for: ').$url.__(' were not found on ') . trailingslashit(get_option('siteurl'));
			return $status;
		}

		$contact->id = $id;
		$contact->load();

		switch ($contact->status) {
		case pnContact::AWAITING_CONF_REMOTE:
 			{
				$contact->identity_key = base64_encode(hash('sha256', $pem_x509, TRUE));
				$contact->status = pnContact::DISABLED;
				$contact->cert = $pem_x509;
				$contact->wp_id = $admin_id;
				$contact->url = $url;
				$contact->display_name = $name;
				$contact->user_login = $un_admin;
				$contact->email = $email;
				$contact->ip = $ip;
			}
			break;
		default:
			{
				if (strcmp($contact->cert, $pem_x509) == 0) {
					$contact->status = pnContact::DISABLED;
				}
				else {
					$contact->cert_replace = $pem_x509;
					$contact->status = pnContact::CERT_REPLACE;
				}
			}
		}

		$contact->ip_verified = $ip_verified;
		try {
			$contact->save();
		}
		catch (Exception $ex) {
			$status['status'] = 'failure';
			$status['error'] = $ex->getMessage();
			return $status;
		}

		$status['status'] = 'success';
		return $status;
	}

	protected function _processAddContact()
	{
		// makes sure requester has the right wp's url
		// for wp the url www.example.com is not equal to example.com
		$this->_checkRedirect();

		$status = array();
		$pem_x509 = isset($_POST['pn_x509']) ? $_POST['pn_x509'] : null;
		$un_admin = isset($_POST['pn_admin']) ? trim($_POST['pn_admin']) : null;
		if (empty($pem_x509)) {
			$status['status'] = 'failure';
			$status['error'] = __('Invalid Certificate');
			return $status;
		}
		if (empty($un_admin)) {
			$status['status'] = 'failure';
			$status['error'] = __('Invalid Contact Username');
			return $status;			
		}
		$admin_id = pnAdmin::IDFromUsername($un_admin);
		if (empty($admin_id)) {
			$status['status'] = 'failure';
			$status['error'] = __('Invalid Contact Username');
			return $status;
		}

		$x509 = pnPEM::ReadX509Cert($pem_x509);
		$sub = $x509->getSubject();
		$url = trim($sub['organizationName']);
		if (empty($url)) {
			$status['status'] = 'failure';
			$status['error'] = __('Invalid Certificate');
			return $status;			
		}
		$url = trailingslashit($url);
		$url = sanitize_url($url, array('http'));

		// try to avoid round connections, (connection to it self)
		$siteurl = trailingslashit(get_option('siteurl'));
		$siteurl = sanitize_url($siteurl, array('http'));
		if (strcasecmp($siteurl, $url) == 0) {
			$status['status'] = 'failure';
			$status['error'] = __('You cannot enter this site url.');
			return $status;			
		}

		// check we have a host
		$host = @parse_url($url, PHP_URL_HOST);
		if ($host === FALSE) {
			$status['status'] = 'failure';
			$status['error'] = __('Invalid Certificate'); 
			return $status;
		}

		// try to validate ip address
		$ip_verified = false;
		$ip = $_SERVER['REMOTE_ADDR'];
		$hip = gethostbyname($host);
		if ($hip == $ip) {
			$ip_verified = true;
		}

		// make sure we have a name and email
		$name = trim($sub['commonName']);
		$email = trim($sub['X509v3 Subject Alternative Name']);
		$user_login = trim($sub['name']);
		if (empty($name) || empty($email) || empty($user_login)) {
			$status['status'] = 'failure';
			$status['error'] = __('Invalid Certificate');
			return $status;			
		}
		if (!is_email($email)) {
			$status['status'] = 'failure';
			$status['error'] = __('Invalid Certificate');
			return $status;			
		}

		$identity_key = base64_encode(hash('sha256', $pem_x509, TRUE));
		$contact = new pnContact();
		$id = $contact->contactExist($url, $user_login, $admin_id);
		if (!empty($id)) {
			$contact->id = $id;
			$contact->load();
			// if we already have the same certificate only update status
			if (strcmp($contact->identity_key, $identity_key) == 0) {
				$contact->status = pnContact::AWAITING_CONF_LOCAL;
				$contact->ip_verified = $ip_verified;
				$contact->save();
				$status['status'] = 'success';
				return $status;
			}
			else if($contact->status != pnContact::AWAITING_CONF_REMOTE &&
					$contact->status != pnContact::AWAITING_CONF_LOCAL && isset($contact->cert)) {
				$contact->cert_replace = $pem_x509;
				$contact->status = pnContact::CERT_REPLACE;
				$contact->ip_verified = $ip_verified;
				$contact->save();
				$status['status'] = 'success';
				return $status;
			}
		}

		$contact->wp_id = $admin_id;
		$contact->url = $url;
		$contact->display_name = $name;
		$contact->user_login = $user_login;
		$contact->email = $email;
		$contact->status = pnContact::AWAITING_CONF_LOCAL;
		$contact->cert = $pem_x509;
		$contact->ip = $ip;
		$contact->ip_verified = $ip_verified;
		$contact->identity_key = $identity_key;
		try {
			$contact->save();
		}
		catch (Exception $ex) {
			$status['status'] = 'failure';
			$status['error'] = $ex->getMessage();
			return $status;
		}
		$status['status'] = 'success';
		return $status;
	}

	private function _sendResponse(array $status)
	{
		@header("Content-Type: application/x-www-form-urlencoded; charset=utf-8");
		$out = '';
		foreach ($status as $key => $value) {
			$out .= $key . '=' . urlencode($value) . '&';
		}
		if (strlen($out) > 0) {
			echo substr($out, 0, -1);
		}
		else {
			echo 'status=failure&error='.urlencode(__('Unknown Error'));
		}
		exit();
	}

	private function _checkRedirect()
	{
		// only care if is a Private Network request
		if (isset($_POST['pn_action'])) {
			$requested_url  = ( !empty($_SERVER['HTTPS'] ) && strtolower($_SERVER['HTTPS']) == 'on' ) ? 'https://' : 'http://';
			$requested_url .= $_SERVER['HTTP_HOST'];
			$requested_url .= $_SERVER['REQUEST_URI'];

			$site_url = get_bloginfo('siteurl');
		
			$r_url = @parse_url($requested_url);
			$s_url = @parse_url($site_url);

			if ($r_url['host'] != $s_url['host']) {
				wp_redirect(trailingslashit($site_url), 301);
				exit();
			}
		}
	}

	public function pnHead()
	{
		echo '<link rel="stylesheet" type="text/css" href="'. PN_URL . '/css/pn-style.css" />';
	}
}