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
class pnAdminController
{
	protected $_tpl;

	public function __construct()
	{
		add_action('init', array(&$this, 'processRequest'));
		add_action('admin_head', array(&$this, 'adminHead'));
		add_action('admin_menu', array(&$this, 'adminPage'));

		$this->_tpl = new pnTemplater(PN_DIR .'/view/admin');
		$this->_tpl->set('url', $_SERVER['PHP_SELF'].'?page='.$_GET['page']);
	}

	public function adminPage()
	{
		add_submenu_page('options-general.php', __('Private Network Settings', PN_LOCALIZE), 
						 __('Private Network', PN_LOCALIZE), 8, __FILE__, array(&$this, 'addAdminPage'));
	}

	public function processRequest()
	{
		$sub = isset($_GET['sub']) ? $_GET['sub'] : '';
		$this->_tpl->set('sub', $sub);
		if (empty($sub)) {
			$this->_tpl->set_template('index.tpl.php');
		}
		else if ($sub == 'certificate') {
			$fp = new FormProcessor_pnProcessCertificate();
			$fp->process($_REQUEST);
			if (!$fp->hasError() && $fp->reload()) {
				wp_redirect($_SERVER['PHP_SELF'].'?page='.$_GET['page']."&sub=".$sub);
				exit();
			}
			$this->_tpl->set('fp', $fp);
			$this->_tpl->set('cert', $fp->getView());
			$this->_tpl->set_template('certificate.tpl.php');
		}
		else if ($sub == 'contacts') {
			$contacts = new pnContactCollection();
			$fp = new FormProcessor_pnProcessContacts($contacts);
			$fp->process($_REQUEST);
			if (!$fp->hasError() && $fp->reload()) {
				wp_redirect($_SERVER['PHP_SELF'].'?page='.$_GET['page']."&sub=".$sub);
				exit();
			}
			$this->_tpl->set('contacts', $contacts);
			$this->_tpl->set('fp', $fp);
			$this->_tpl->set_template('contacts.tpl.php');
		}
		else if (preg_match("/contact_(\d+)/", $sub, $matches) == 1) {
			$contact = new pnContact();
			$contact->id = $matches[1];
			$acl = new pnACLCollection();
			$fp = new FormProcessor_pnProcessContactAcl($contact, $acl);
			$fp->process($_POST);
			$this->_tpl->set('db', new pnDataObjectUtil());
			$this->_tpl->set('fp', $fp);
			$this->_tpl->set('acls', $acl);
			$this->_tpl->set('contact', $contact);
			$this->_tpl->set_template('contact.tpl.php');
		}
	}

	public function addAdminPage()
	{
		$this->_tpl->display();
	}

	public function adminHead()
	{
		echo '<link rel="stylesheet" type="text/css" href="'. PN_URL . '/css/pn-admin.css" media="all" />';
	}
}