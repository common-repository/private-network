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
/**
 * Class inspired by the class with the same name
 * in the excelent book by Quentin Zervaas book: "Practical Web 2.0 Applications with PHP"
 */
abstract class pnFormProcessor
{
	protected $_errors;
	protected $_vals;
	protected $_msg;

	public function __construct()
	{
		$this->_errors = array();
		$this->_vals = array();
		$this->_msg = '';
	}

	abstract function process(array $params = array());

	public function sanitize($value)
	{
		return preg_replace('/[\r\n]+/', ' ', strip_tags(trim($value)));
	}

	public function validEmail($email)
	{
		return is_email($email);
	}

	public function isPost()
	{
		return (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') == 0);
	}

	public function addError($key, $val)
	{
		if (array_key_exists($key, $this->_errors)) {
			if (!is_array($this->_errors[$key]))
				$this->_errors[$key] = array($this->_errors[$key]);

			$this->_errors[$key][] = $val;
		}
		else
			$this->_errors[$key] = $val;
	}

	public function getError($key)
	{
		if ($this->hasError($key))
			return $this->_errors[$key];

		return null;
	}

	public function getErrors()
	{
		return $this->_errors;
	}

	public function hasError($key = null)
	{
		if (strlen($key) == 0)
			return count($this->_errors) > 0;

		return array_key_exists($key, $this->_errors);
	}

	public function __set($name, $value)
	{
		$this->_vals[$name] = $value;
	}

	public function __get($name)
	{
		return array_key_exists($name, $this->_vals) ? $this->_vals[$name] : null;
	}

	public function __isset($name)
	{
		if (array_key_exists($name, $this->_vals)) {
			return !empty($this->_vals[$name]);
		}
		return false;
	}

	public function __unset($name)
	{
		if (array_key_exists($name, $this->_vals)) {
			unset($this->_vals[$name]);
		}
	}

	public function setMsg($msg)
	{
		$this->_msg = $msg;
	}
	
	public function getMsg()
	{
		return $this->_msg;
	}
}
?>