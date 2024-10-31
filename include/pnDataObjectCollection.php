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
abstract class pnDataObjectCollection implements Iterator
{
	protected $_pointer;
	protected $_collection;
	protected $_table;
	protected $_filters;


	public function __construct($table)
	{
		$this->_collection = array();
		$this->_pointer = 0;
		$this->_table = $table;
		$this->_filters = array();
	}

	public function add(pnDataObject $obj)
	{
		$this->_collection[] = $obj;
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

	protected function _removeFilter($name)
	{
		$filters = array();
		foreach ($this->_filters as $key => $value) {
			if ($key != $name) {
				$filters[$key] = $value;
			}
		}
		$this->_filters = $filters;
	}

	abstract public function load($page=0, $items=0);


	protected function _select(array $fields=array('*'), $order=null, $page=0, $items=0)
	{
		global $wpdb;
		$query = "SELECT " . implode(',', $fields) . " FROM {$this->_table}";
		if (!empty($this->_filters)) {
			$query .= " WHERE ";
			$filter = array();
			foreach ($this->_filters as $key => $value) {
				$filter[] = $key."'".$wpdb->escape($value)."'";
			}
			$query .= implode('AND ', $filter);
		}
		if (!is_null($order)) {
			$query .= " ORDER BY ".$order;
		}
		if ($items != 0) {
			if ($page == 0) $page = 1;
			$query .= " LIMIT " . (($items * $page) - $items) . ' ' . $items;
		}
		return $wpdb->get_results($query, ARRAY_A);
	}


    public function current()
    {
        if (isset($this->_collection[$this->_pointer])) {
            return $this->_collection[$this->_pointer];
        }
        return null;
    }

    public function key()
    {
        return $this->_pointer;
    }

    public function next()
    {
        if (isset($this->_collection[$this->_pointer])) {
            $type = $this->_collection[$this->_pointer];
            $this->_pointer++;
            return $type;
        }
        return null;
    }

    public function prev()
    {
        if (isset($this->_collection[$this->_pointer])) {
            $type = $this->_collection[$this->_pointer];
            $this->_pointer--;
            return $type;
        }
        return null;
    }

    public function rewind()
    {
        $this->_pointer = 0;
    }

    public function valid()
    {
        return (! is_null($this->current()));
    }

    public function size()
    {
        return count($this->_collection);
    }
}