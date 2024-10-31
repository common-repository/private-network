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
class pnACL extends pnDataObject
{
	const PN_CAT_INC_PUBLIC = 'pn_cat_inc_public';
	const PN_TAG_INC_PUBLIC = 'pn_tag_inc_public';
	
	protected $valid_type = array('category', 'tag', 'post', 'page');


	public function __construct()
	{
		global $wpdb;
		parent::__construct($wpdb->prefix . 'pn_acl');
		$this->addField('id');
		$this->addField('admin_id');
		$this->addField('contact_id');
		$this->addField('share_type');
		$this->addField('share_id');
		$this->addField('display_name');
	}

	public function __set($name, $value)
	{
		if (array_key_exists($name, $this->_fields)) {
			if (strcmp($name, 'share_type') == 0) {
				if (!in_array($value, $this->valid_type)) {
					throw new Exception(__("Error: Invalid share_type ").$value);
				}
			}
			$this->_fields[$name] = $value;
			return true;
		}
		return false;
	}

	public function save()
	{
		global $wpdb;
		$query = "REPLACE INTO {$this->_table} (admin_id,contact_id,share_type,share_id,display_name) ".
			"VALUES ($this->admin_id,$this->contact_id,'".$wpdb->escape($this->share_type)."',".
			"$this->share_id,'".$wpdb->escape($this->display_name)."')";

		if ($wpdb->query($query) === false) {
			throw new Exception($wpdb->last_error);
		}
		$this->id = $wpdb->insert_id;
	}

	public function create()
	{
		global $wpdb;
		if($wpdb->get_var("SHOW TABLES LIKE '{$this->_table}'") != $this->_table) {

			$query = "CREATE TABLE " . $this->_table . " (
                  id SERIAL,
                  admin_id BIGINT UNSIGNED NOT NULL,
                  contact_id BIGINT UNSIGNED NOT NULL,
                  share_type VARCHAR(255) NOT NULL,
                  share_id BIGINT UNSIGNED NOT NULL,
                  display_name VARCHAR(255) NOT NULL DEFAULT '',
                  INDEX(admin_id,contact_id),
                  UNIQUE(admin_id,contact_id,share_type,share_id)
	            ) charset=utf8;";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($query);

			add_option("pn_db_version", PN_DB_VERSION);
		}
		
		$db_ver = get_option("pn_db_version");

		if ($db_ver != PN_DB_VERSION) {

			$query = "CREATE TABLE " . $this->_table . " (
                  id SERIAL,
                  admin_id BIGINT UNSIGNED NOT NULL,
                  contact_id BIGINT UNSIGNED NOT NULL,
                  share_type VARCHAR(255) NOT NULL,
                  share_id BIGINT UNSIGNED NOT NULL,
                  display_name VARCHAR(255) NOT NULL DEFAULT '',
                  INDEX(admin_id,contact_id),
                  UNIQUE(admin_id,contact_id,share_type,share_id)
	            ) charset=utf8;";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($query);

			update_option("pn_db_version", PN_DB_VERSION);			
		}
	}
}