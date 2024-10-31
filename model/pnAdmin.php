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
class pnAdmin extends pnDataObject
{
	public function __construct()
	{
		global $wpdb, $user_ID, $user_login;
		get_currentuserinfo();

		parent::__construct($wpdb->prefix . 'pn_admin');
		$this->addField('id', $user_ID);
		$this->addField('user_login', base64_encode(hash('sha1', $user_login, TRUE)));
		$this->addField('cert');
		$this->addField('pkey');
		$this->addField('identity_key'); //sha256 of certificate
	}

	public static function IDFromUsername($username)
	{
		global $wpdb;
		$username = $wpdb->escape($username);
		$query = "SELECT ID FROM {$wpdb->users} WHERE $wpdb->users.user_login='$username'";
		return $wpdb->get_var($query);
	}

	public function loadAdmin($identity_key)
	{
		global $wpdb;
		if (empty($identity_key)) {
			throw new Exception("Missing valid param in pnAdmin::loadAdmin");
		}
		$query = "SELECT * FROM {$this->_table} WHERE identity_key='{$wpdb->escape($identity_key)}'";
		$res = $wpdb->get_row($query, ARRAY_A);
		if (is_array($res)) {
			foreach ($res as $key => $value) {
				$this->$key = $value;
			}
		}
		return isset($this->id);
	}

	public function save()
	{
		global $wpdb;
		$data = add_magic_quotes($this->_fields);
		$fields = array_keys($this->_fields);
		return $wpdb->query("REPLACE INTO {$this->_table} (`" . implode('`,`',$fields) . "`) VALUES ('".implode("','",$data)."')");
	}

	public function create()
	{
		global $wpdb;
		if($wpdb->get_var("SHOW TABLES LIKE '{$this->_table}'") != $this->_table) {

			$query = "CREATE TABLE " . $this->_table . " (
                  id BIGINT UNSIGNED PRIMARY KEY,
                  user_login VARCHAR(64) NOT NULL,
                  cert TEXT,
                  pkey TEXT,
                  identity_key VARCHAR(64),
                  UNIQUE (identity_key)
	            ) charset=utf8;";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($query);

			add_option("pn_db_version", PN_DB_VERSION);
		}
		
		$db_ver = get_option("pn_db_version");

		if ($db_ver != PN_DB_VERSION) {

			$query = "CREATE TABLE " . $this->_table . " (
                  id BIGINT UNSIGNED PRIMARY KEY,
                  user_login VARCHAR(64) NOT NULL,
                  cert TEXT,
                  pkey TEXT,
                  identity_key VARCHAR(64),
                  UNIQUE (identity_key)
	            ) charset=utf8;";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($query);

			update_option("pn_db_version", PN_DB_VERSION);			
		}
	}
}