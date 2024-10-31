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
abstract class pnDataObject
{
	protected $_fields = array();
	protected $_filters = array();
	protected $_table = '';


	public function __construct($table)
	{
		$this->_table = $table;
	}

	public function addField($name, $value=null)
	{
		$this->_fields[$name] = $value;
	}

	protected function _addFilter($name, $value=null)
	{
		if (is_array($name)) {
			$this->_filters = array_merge($this->_filters, $name);
		}
		else {
			$this->_filters[$name] = $value;
		}
	}

	public function __get($name)
	{
		return array_key_exists($name, $this->_fields) ? $this->_fields[$name] : null;
	}
		
	public function __set($name, $value)
	{
		if (array_key_exists($name, $this->_fields)) {
			$this->_fields[$name] = $value;
			return true;
		}
		return false;
	}

	public function __isset($key)
	{
		return isset($this->_fields[$key]) ? $this->_fields[$key] != null : FALSE;
	}

	public function load()
	{
		global $wpdb;
		if (empty($this->_fields['id'])) {
			throw new Exception("Invalid id {$this->id} in ".get_class($this));
		}
		$query = "SELECT * FROM {$this->_table} WHERE id={$this->id}";
		$res = $wpdb->get_row($query, ARRAY_A);
		if (is_array($res)) {
			foreach ($res as $key => $value) {
				if (array_key_exists($key, $this->_fields)) {
					$this->_fields[$key] = $value;
				}
			}
		}
	}
		
	public function save()
	{
		if (empty($this->_fields['id'])) {
			$this->_create();
		}
		else {
			global $wpdb;
			$params = $this->_getFields();
			if ($wpdb->update($this->_table, $params, array('id' => $this->id)) === false) {
				throw new Exception($wpdb->last_error);
			}
		}
	}


	/**
	 * Returns the table fields and values except the id
	 */
	protected function _getFields()
	{
		$params = array();
		foreach ($this->_fields as $key => $value) {
			if ($key != 'id') {
				$params[$key] = $value;
			}
		}
		return $params;
	}

	protected function _create()
	{
		global $wpdb;
		$params = $this->_getFields();
		if ($wpdb->insert($this->_table, $params) === false) {
			throw new Exception($wpdb->last_error);
		}
		$this->id = $wpdb->insert_id;
		$this->postInsert();
	}
		
	public function delete()
	{
		if (empty($this->_fields['id'])) {
			throw new Exception(__("Error: Set 'id' before calling method 'delete' in ").get_class($this));
		}
		global $wpdb;
		$query = "DELETE FROM {$this->_table} WHERE id={$this->id}";
		if ($wpdb->query($query) === false) {
			throw new Exception($wpdb->last_error);
		}
	}

	public function drop()
	{
		global $wpdb;
		if($wpdb->get_var("SHOW TABLES LIKE '{$this->_table}'") != $this->_table) return;

		$query = "DROP TABLE {$this->_table}";
		$wpdb->query($query);
	}


	public function __toString()
	{
		return print_r($this->_fields, TRUE);
	}

	protected function postInsert()
	{
		return true;
	}
}
