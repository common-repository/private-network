<?php
/*
  Plugin Name: Private Network
  Plugin URI: http://www.andreabelvedere.com/private-network
  Description: Allows Administrators at different WordPress installations, for example www.alice.com and www.bob.com, to share their posts within categories, posts with selected tags or single posts and pages. Within categories and tags is possible to share only private or include public posts.
  Author: Andrea Belvedere
  Version: 1.3
  Author URI: http://www.andreabelvedere.com/
*/
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
define('PN_VERSION', '1.3');
define('PN_DB_VERSION', '1.1');
define('PN_PROTOCOL', '1.0');
define('PN_LOCALIZE', 'private-network');

if (! defined('WP_CONTENT_DIR'))
    define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
if (! defined('WP_CONTENT_URL'))
    define('WP_CONTENT_URL', get_option('siteurl') . '/wp-content');

if (! defined('WP_PLUGIN_DIR'))
    define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins');
if (! defined('WP_PLUGIN_URL'))
    define('WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins');

define('PN_DIR', dirname(__FILE__));
define('PN_URL', WP_PLUGIN_URL . '/' . plugin_basename(dirname(__FILE__)));


function pn_autoload($class)
{
    if (class_exists($class, false) || interface_exists($class, false)) {
        return;
    }
    $classfile = str_replace("_", "/", $class) . ".php";

    $include_path = array(PN_DIR.'/controller',
                          PN_DIR.'/lib',
                          PN_DIR.'/include',
                          PN_DIR.'/model');

    foreach ($include_path as $path) {
        $classpath = $path.'/'.$classfile;
        if(file_exists($classpath)) {
            include_once $classpath;
            return;
        }
    }
}

class PrivateNetwork
{
    protected $_pnSession;

    public function __construct()
    {
        $this->_pnSession = new pnSession();
        register_activation_hook( __FILE__, array(&$this, 'activate_pn'));
        register_deactivation_hook( __FILE__, array(&$this, 'deactivate_pn'));
        add_action('shutdown', array(&$this->_pnSession, 'cleanSession'));
        if (is_admin()) {
            new pnAdminController();
        }
        else {
            new pnPrivateNetworkController();
        }
    }

    public function activate_pn()
    {
        $contact = new pnContact();
        $contact->create();
        $admin = new pnAdmin();
        $admin->create();
        $this->_pnSession->create();
        $acl = new pnACL();
        $acl->create();
    }

    public function deactivate_pn()
    {

    }
}
if (false === spl_autoload_functions())
{
    if (function_exists('__autoload')) {
        spl_autoload_register('__autoload');
    }
}
spl_autoload_register('pn_autoload');

new PrivateNetwork();