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
class pnACLCollection extends pnDataObjectCollection
{
	protected $_orderby;

	public function __construct()
	{
		global $wpdb;
		parent::__construct($wpdb->prefix . 'pn_acl');
		$this->_orderby = 'share_type,display_name ASC';
	}

	public function load($page=0, $items=0)
	{
		$res = $this->_select(array('*'), $this->_orderby, $page, $items);
		if (is_array($res)) {
			$size = count($res);
			for ($i = 0; $i < $size; $i++) {
				$acl = new pnACL();
				foreach ($res[$i] as $key => $value) {
					$acl->$key = $value;
				}
				$this->add($acl);
			}
		}		
	}

	public function filterOn($name, $value=null)
	{
		$this->_addFilter($name, $value);
	}

	public function filterOff($name)
	{
		$this->_removeFilter($name);
	}

	public function orderBy($orderby)
	{
		$this->_orderby = $orderby;
	}
}