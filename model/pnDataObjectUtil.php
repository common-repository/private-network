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
class pnDataObjectUtil
{
	public function __construct() {}

	public function getTags(array $args = array())
	{
		return get_terms('post_tag', $args);
	}

	public function getCategories()
	{
		return get_categories(array('hide_empty' => false));
	}

	public function getPosts($post_status = 'private')
	{
		global $wpdb, $user_ID;
		get_currentuserinfo();
		$regex = '\[\s*pn-\s*[a-zA-Z0-9\/\+]{43}[=]{0,2}\s*\]';
		$query = "SELECT ID,post_title FROM $wpdb->posts WHERE $wpdb->posts.post_status='$post_status' AND ".
			"$wpdb->posts.post_author={$user_ID} AND $wpdb->posts.post_content NOT REGEXP '{$regex}' ".
			"AND $wpdb->posts.post_type='post' ORDER BY $wpdb->posts.post_date";
		return $wpdb->get_results($query, OBJECT);
	}

	public function getPages($post_status = 'private')
	{
		global $wpdb, $user_ID;
		get_currentuserinfo();
		$regex = '\[\s*pn-\s*[a-zA-Z0-9\/\+]{43}[=]{0,2}\s*\]';
		$query = "SELECT ID,post_title FROM $wpdb->posts WHERE $wpdb->posts.post_status='$post_status' AND ".
			"$wpdb->posts.post_author={$user_ID} AND $wpdb->posts.post_content NOT REGEXP '{$regex}' ".
			"AND $wpdb->posts.post_type='page' ORDER BY $wpdb->posts.post_date";
		return $wpdb->get_results($query, OBJECT);
	}

	public function setOption($name, $value='')
	{
		if (get_option($name) === false) {
			add_option($name, $value);
		}
		else {
			update_option($name, $value);
		}
	}

	public function getOption($name)
	{
		return get_option($name);
	}
}