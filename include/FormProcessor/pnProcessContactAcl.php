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
class FormProcessor_pnProcessContactAcl extends pnFormProcessor
{
	protected $_reload;
	protected $_contact;
	protected $_admin;
	protected $_acl;
	protected $_db;

	public function __construct(pnContact $contact, pnACLCollection $acl)
	{
		parent::__construct();
		$this->_reload = false;
		$this->_contact = $contact;
		$this->_admin = new pnAdmin();
		$this->_acl = $acl;
		$this->_db = new pnDataObjectUtil();
	}

	public function process(array $params = array())
	{
		try {
			$this->_admin->load();
			$this->_contact->load();
			$this->_acl->filterOn('admin_id=', $this->_admin->id);
			$this->_acl->filterOn('contact_id=', $this->_contact->id);
		}
		catch (Exception $ex) {
			$this->addError('Fatal', $ex->getMessage());
		}
		if ($this->isPost()) {
			$pn_action = isset($params['pn_action']) ? $params['pn_action'] : null;
			switch ($pn_action) {
			case 'pn-add-acl':
				$this->_processAddAcl($params);
				break;
			case 'pn-delete-acl':
				$this->_processDeleteAcl($params);
				break;
			default:
				$this->addError('Fatal', __('Error: Unknown Request'));
			}
		}
		try {
			$this->_acl->load();
			$this->cat_inc_public = $this->_db->getOption(pnACL::PN_CAT_INC_PUBLIC."_{$this->_contact->id}");
			if ($this->cat_inc_public === false) {
				$this->cat_inc_public = 'no';
			}
			$this->tag_inc_public = $this->_db->getOption(pnACL::PN_TAG_INC_PUBLIC."_{$this->_contact->id}");
			if ($this->tag_inc_public === false) {
				$this->tag_inc_public = 'no';
			}
		}
		catch (Exception $ex) {
			$this->addError('Fatal', $ex->getMessage());
		}
		return !$this->hasError();
	}

	protected function _processDeleteAcl($params)
	{
		$checked = $params['checked'];
		if (empty($checked)) {
			$this->addError('Fatal', __('Please check an ACL item to delete.'));
			return false;
		}
		foreach ($checked as $id) {
			$acl = new pnACL();
			$acl->id = $id;
			try {
				$acl->delete();
			}
			catch (Exception $ex) {
				$this->addError('Fatal', $ex->getMessage());
			}
		}
		if (!$this->hasError()) {
			$this->setMsg(__("ACL item(s) deleted."));
		}
		return !$this->hasError();
	}

	protected function _processAddAcl($params)
	{
		$categories = isset($params['pn_categories']) ? $params['pn_categories'] : array();
		$tags = isset($params['pn_tags']) ? $params['pn_tags'] : array();
		$posts = isset($params['pn_posts']) ? $params['pn_posts'] : array();
		$pages = isset($params['pn_pages']) ? $params['pn_pages'] : array();
		$cat_inc_public = isset($params['pn_cat_inc_public']) ? $params['pn_cat_inc_public'] : 0;
		$tag_inc_public = isset($params['pn_tag_inc_public']) ? $params['pn_tag_inc_public'] : 0;
		try {
			foreach ($categories as $id) {
				$cat = get_category($id);
				$acl = new pnACL();
				$acl->admin_id = $this->_admin->id;
				$acl->contact_id = $this->_contact->id;
				$acl->share_type = 'category';
				$acl->share_id = $id;
				$acl->display_name = $cat->cat_name;
				$acl->save();
			}
			foreach ($tags as $id) {
				$tag = get_tag($id);
				$acl = new pnACL();
				$acl->admin_id = $this->_admin->id;
				$acl->contact_id = $this->_contact->id;
				$acl->share_type = 'tag';
				$acl->share_id = $id;
				$acl->display_name = $tag->name;
				$acl->save();
			}
			foreach ($posts as $id) {
				$post = get_post($id);
				$acl = new pnACL();
				$acl->admin_id = $this->_admin->id;
				$acl->contact_id = $this->_contact->id;
				$acl->share_type = 'post';
				$acl->share_id = $id;
				$acl->display_name = $post->post_title;
				$acl->save();
			}
			foreach ($pages as $id) {
				$page = get_post($id);
				$acl = new pnACL();
				$acl->admin_id = $this->_admin->id;
				$acl->contact_id = $this->_contact->id;
				$acl->share_type = 'page';
				$acl->share_id = $id;
				$acl->display_name = $page->post_title;
				$acl->save();
			}
			if ($cat_inc_public == 1) {
				$this->_db->setOption(pnACL::PN_CAT_INC_PUBLIC."_{$this->_contact->id}", 'yes');
			}
			else {
				$this->_db->setOption(pnACL::PN_CAT_INC_PUBLIC."_{$this->_contact->id}", 'no');
			}
			if ($tag_inc_public == 1) {
				$this->_db->setOption(pnACL::PN_TAG_INC_PUBLIC."_{$this->_contact->id}", 'yes');
			}
			else {
				$this->_db->setOption(pnACL::PN_TAG_INC_PUBLIC."_{$this->_contact->id}", 'no');
			}
		}
		catch (Exception $ex) {
			$this->addError('Fatal', $ex->getMessage());
		}
		if (!$this->hasError()) {
			$this->setMsg(__("ACL Updated"));
		}
	}
}