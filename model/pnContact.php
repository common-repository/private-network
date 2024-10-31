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
class pnContact extends pnDataObject
{
	// status
	const AWAITING_CONF_LOCAL = 0;
	const AWAITING_CONF_REMOTE = 1;
	const ENABLED = 3;
	const DISABLED = 4;
	const CERT_REPLACE = 5;

	// ip
	const IP_VERIFIED = 1;
	const IP_UNVERIFIED = 0;


	public function __construct()
	{
		global $wpdb, $user_ID;
		get_currentuserinfo();

		parent::__construct($wpdb->prefix . 'pn_contact');
		$this->addField('id');
		$this->addField('wp_id', $user_ID);
		$this->addField('url');
		$this->addField('display_name');
		$this->addField('user_login');
		$this->addField('email');
		$this->addField('status');
		$this->addField('cert');
		$this->addField('cert_replace');
		$this->addField('ip');
		$this->addField('ip_verified', 0);
		$this->addField('identity_key');
		$this->addField('position', 0);
	}


	public function ipVerifyStatus()
	{
		switch ($this->ip_verified) {
		case self::IP_UNVERIFIED:
			return __('NOT Verified');
		default:
			return __('Verified');
		}
	}

	public function actionRequired()
	{
		if (self::IP_UNVERIFIED) return true;
		switch ($this->status) {
		case self::ENABLED:
		case self::DISABLED:
			return false;
		default:
			return true;
		}
	}

	public function getStatus()
	{
		switch ($this->status) {
		case self::AWAITING_CONF_LOCAL:
			return __('Confirm Contact');
		case self::AWAITING_CONF_REMOTE:
			return __('Awaiting Confirmation');
		case self::ENABLED:
			return __('Enabled');
		case self::DISABLED:
			return __('Disabled');
		case self::CERT_REPLACE:
			return __('Certificate Update');
		default:
			return __('Unknown');
		}
	}

	public function loadContact($identity_key, $wp_id = 0)
	{
		global $wpdb, $user_ID;
		get_currentuserinfo();
		if (empty($wp_id) && empty($user_ID)) {
			return false;
		}
		if (empty($wp_id)) {
			$wp_id = $user_ID;
		}
		$query = "SELECT * FROM {$this->_table} WHERE identity_key='{$wpdb->escape($identity_key)}' AND wp_id={$wp_id}";
		$ret = $wpdb->get_row($query, ARRAY_A);
		if (is_array($ret)) {
			foreach ($ret as $key => $value) {
				$this->$key = $value;
			}
		}
		return isset($this->id);
	}

	/**
	 * @return NULL if contact does not exist, or int contact id if contact exist
	 */
	public function contactExist($url, $user_login, $wp_id=0)
	{
		global $wpdb;
		if ($wp_id == 0) {
			$wp_id = $this->wp_id;
		}
		$query = "SELECT id FROM {$this->_table} WHERE url='{$wpdb->escape($url)}' ".
			"AND wp_id={$wp_id} AND user_login='{$wpdb->escape($user_login)}'";

		return $wpdb->get_var($query);
	}

	public function create()
	{
		global $wpdb;
		if($wpdb->get_var("SHOW TABLES LIKE '{$this->_table}'") != $this->_table) {

			$query = "CREATE TABLE " . $this->_table . " (
                  id SERIAL,
                  wp_id BIGINT UNSIGNED NOT NULL,
                  url VARCHAR(255) NOT NULL,
                  display_name VARCHAR(250) NOT NULL,
                  user_login VARCHAR(64),
                  email VARCHAR(100),
                  status TINYINT UNSIGNED NOT NULL,
                  cert TEXT,
                  cert_replace TEXT,
                  ip VARCHAR(64),
                  ip_verified TINYINT NOT NULL,
                  identity_key VARCHAR(64),
                  position BIGINT UNSIGNED NOT NULL DEFAULT 0,
                  UNIQUE (wp_id,url,user_login),
                  UNIQUE (wp_id,identity_key),
                  INDEX (wp_id,identity_key)
	            ) charset=utf8;";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($query);

			add_option("pn_db_version", PN_DB_VERSION);
		}
		
		$db_ver = get_option("pn_db_version");

		if ($db_ver != PN_DB_VERSION) {

			if ($db_ver = '1.0') {
				$query = "DROP INDEX identity_key ON {$this->_table}";
				$wpdb->query($query);
			}

			$query .= "CREATE TABLE " . $this->_table . " (
                  id SERIAL,
                  wp_id BIGINT UNSIGNED NOT NULL,
                  url VARCHAR(255) NOT NULL,
                  display_name VARCHAR(250) NOT NULL,
                  user_login VARCHAR(64),
                  email VARCHAR(100),
                  status TINYINT UNSIGNED NOT NULL,
                  cert TEXT,
                  cert_replace TEXT,
                  ip VARCHAR(64),
                  ip_verified TINYINT NOT NULL,
                  identity_key VARCHAR(64),
                  position BIGINT UNSIGNED NOT NULL DEFAULT 0,
                  UNIQUE (wp_id,url,user_login),
                  UNIQUE (wp_id,identity_key),
                  INDEX (wp_id,identity_key)
	            ) charset=utf8;";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($query);

			update_option("pn_db_version", PN_DB_VERSION);			
		}
	}

	protected function postInsert()
	{
		global $wpdb;
		$params = array('position' => $this->id);
		return $wpdb->update($this->_table, $params, array('id' => $this->id));
	}
}