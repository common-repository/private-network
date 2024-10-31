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
class pnSession extends pnDataObject
{
	// number of seconds a session is live
	const SESSION_TIMEOUT = 60;

	public function __construct()
	{
		global $wpdb;
		parent::__construct($wpdb->prefix . 'pn_session');
		$this->addField('session');
		$this->addField('tstamp');
		$this->addField('secret');
		$this->addField('from_ik');
		$this->addField('to_ik');
		$this->addField('ip');
	}

	public function load()
	{
		global $wpdb;
		if (!isset($this->session)) {
			throw new Exception(__("Missing session"));
		}
		$query = '';
		$timeout = time() - self::SESSION_TIMEOUT;

		$query = "SELECT * FROM {$this->_table} WHERE session='{$wpdb->escape($this->session)}' AND tstamp>{$timeout}";
		$ret = $wpdb->get_row($query, ARRAY_A);
		if (is_array($ret)) {
			foreach ($ret as $key => $value) {
				$this->$key = $value;
			}
		}
		return isset($this->secret);
	}

	public function save()
	{
		global $wpdb;
		if (!isset($this->session)) {
			throw new Exception(__("Missing session"));
		}
		$data = add_magic_quotes($this->_fields);
		$fields = array_keys($this->_fields);
		return $wpdb->query("REPLACE INTO {$this->_table} (`" . implode('`,`',$fields) . "`) VALUES ('".implode("','",$data)."')");
	}

	public function cleanSession($session='')
	{
		global $wpdb;
		if($wpdb->get_var("SHOW TABLES LIKE '{$this->_table}'") != $this->_table) return;

		$query = '';
		if (empty($session)) {
			$timeout = time() - self::SESSION_TIMEOUT;
			$query = "DELETE FROM {$this->_table} WHERE tstamp<{$timeout}";
		}
		else {
			$query = "DELETE FROM {$this->_table} WHERE session='{$wpdb->escape($session)}'";
		}
		$wpdb->query($query);
	}

	public function create()
	{
		global $wpdb;
		if($wpdb->get_var("SHOW TABLES LIKE '{$this->_table}'") != $this->_table) {

			$query = "CREATE TABLE " . $this->_table . " (
                  session VARCHAR(64) PRIMARY KEY,
                  tstamp BIGINT UNSIGNED NOT NULL,
                  secret VARCHAR(128) NOT NULL,
                  from_ik VARCHAR(64) NOT NULL,
                  to_ik VARCHAR(64) NOT NULL,
                  ip VARCHAR(64) NOT NULL
	            ) charset=utf8;";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($query);

			add_option("pn_db_version", PN_DB_VERSION);
		}
		
		$db_ver = get_option("pn_db_version");

		if ($db_ver != PN_DB_VERSION) {

			$query = "CREATE TABLE " . $this->_table . " (
                  session VARCHAR(64) PRIMARY KEY,
                  tstamp BIGINT UNSIGNED NOT NULL,
                  secret VARCHAR(128) NOT NULL,
                  from_ik VARCHAR(64) NOT NULL,
                  to_ik VARCHAR(64) NOT NULL,
                  ip VARCHAR(64) NOT NULL
	            ) charset=utf8;";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($query);

			update_option("pn_db_version", PN_DB_VERSION);			
		}
	}
}