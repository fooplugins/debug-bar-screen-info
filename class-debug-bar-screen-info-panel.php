<?php
/**
 * Debug Bar Screen Info - Debug Bar Panel
 *
 * @package		WordPress\Plugins\debug-bar-screen-info
 * @author		Brad Vincent <brad@fooplugins.com>
 * @link		https://github.com/fooplugins/debug-bar-screen-info
 * @version		1.1.4
 * @copyright	2013 FooPlugins LLC
 * @license		http://creativecommons.org/licenses/GPL/2.0/ GNU General Public License, version 2 or higher
 */

class Debug_Bar_Screen_Info_Panel extends Debug_Bar_Panel {

	private $tab;
	private $callback;

	public function __construct( $title = '', $callback = '' ) {
		$this->tab      = $title;
		$this->callback = $callback;
		parent::__construct();
	}

	public function init() {
		$this->title( $this->tab );
	}

	public function prerender() {
		$this->set_visible( is_admin() );
	}

	public function render() {
		echo call_user_func( $this->callback );
	}
}
