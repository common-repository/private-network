<?php
/**
 * Copyright (c) 2003 Brian E. Lozier (brian@massassi.net)
 *
 * set_vars() method contributed by Ricardo Garcia (Thanks!)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 */
 /**
  * class modifield by Andrea Belvedere (scieck at gamil dot com)
  * 
  * - changed name from Template to pnTemplater
  * - upgraded syntax to php5
  * - added display method
  * - added $template attribute
  */
class pnTemplater 
{
	protected  $vars; /// Holds all the template variables
	protected $path; /// Path to the templates
	protected $template; // holds the template (file) to show or return

	/**
	 * Constructor
	 *
	 * @param string $path the path to the templates
	 *
	 * @return void
	 */
	public function __construct($path = null) 
	{
		$this->path = $path;
		$this->vars = array();
		$this->template = '';
	}

	/**
	 * Set the path to the template files.
	 *
	 * @param string $path path to template files
	 *
	 * @return void
	 */
	public function set_path($path) 
	{
		$this->path = $path;
	}

	/**
	 * Set a template variable.
	 *
	 * @param string $name name of the variable to set
	 * @param mixed $value the value of the variable
	 *
	 * @return void
	 */
	public function set($name, $value) 
	{
		$this->vars[$name] = $value;
	}

	/**
	 * Set a bunch of variables at once using an associative array.
	 *
	 * @param array $vars array of vars to set
	 * @param bool $clear whether to completely overwrite the existing vars
	 *
	 * @return void
	 */
	public function set_vars($vars, $clear = false) 
	{
		if($clear) {
			$this->vars = $vars;
		}
		else {
			if(is_array($vars)) $this->vars = array_merge($this->vars, $vars);
		}
	}

	public function set_template($template)
	{
		$this->template = $template;
	}


	/**
	 * Open, parse, and echo the template file.
	 *
	 * @param string string the template file name
	 *
	 * @return void
	 */
	public function display($file = '') 
	{
		if (empty($file)) {
			$file = $this->template;
		}
		extract($this->vars);          // Extract the vars to local namespace
		ob_start();                    // Start output buffering
		include($this->path . "/$file");  // Include the file
		ob_end_flush();
	}

	/**
	 * Open, parse, and return the template file.
	 *
	 * @param string string the template file name
	 *
	 * @return string
	 */
	public function fetch($file = '') 
	{
		if (empty($file)) {
			$file = $this->template;
		}
		extract($this->vars);          // Extract the vars to local namespace
		ob_start();                    // Start output buffering
		include($this->path . "/$file");  // Include the file
		$contents = ob_get_contents(); // Get the contents of the buffer
		ob_end_clean();                // End buffering and discard
		return $contents;              // Return the contents
	}
}
