<?php
/**
 * Debug Bar Screen Info - Debug Bar Panel
 *
 * @package		WordPress\Plugins\debug-bar-screen-info
 * @author		Brad Vincent <brad@fooplugins.com>
 * @link		https://github.com/fooplugins/debug-bar-screen-info
 * @version		1.1.3
 * @copyright	2013 FooPlugins LLC
 * @license		http://creativecommons.org/licenses/GPL/2.0/ GNU General Public License, version 2 or higher
 */
class Debug_Bar_Screen_Info_Panel extends Debug_Bar_Panel {

	private $tab_name;
	private $tab;
	private $callback;

	public function init() {
		$this->title( $this->tab );
	}

	public function set_tab( $name, $callback ) {
		$this->tab_name = strtolower( preg_replace( '#[^a-z0-9]#msiU', '', $name ) );
		$this->tab      = $name;
		$this->callback = $callback;
		$this->title( $this->tab );
	}

	public function prerender() {
		$this->set_visible( is_admin() );
	}

	public function render() {
		echo call_user_func( $this->callback );
	}
}