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
 * Easy wrap on Snoopy (http://snoopy.sourceforge.net/)
 */
class pnHTTP extends pnSnoopy
{
	protected $_params;

	public function __construct()
	{
		parent::__construct();
		$this->_params = array();
	}

	public function getParams()
	{
		$kvp = split('&', $this->results);
		foreach ($kvp as $vps) {
			$vp = split('=', $vps);
			if (count($vp) == 2) {
				$this->_params[$vp[0]] = urldecode($vp[1]);
			}
		}
		return $this->_params;
	}

	public function getParam($name)
	{
		if (empty($this->_params)) {
			$this->getParams();
		}
		return array_key_exists($name, $this->_params) ? $this->_params[$name] : null;
	}

	public function submit($URI, $formvars="", $formfiles="")
	{
		if (!empty($this->_params)) {
			$this->_params = array();
		}
		return parent::submit($URI, $formvars, $formfiles);
	}
}