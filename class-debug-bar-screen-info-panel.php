<?php
/**
 * Debug Bar Screen Info - Debug Bar Panel
 *
 * @package   WordPress\Plugins\debug-bar-screen-info
 * @author    Brad Vincent <brad@fooplugins.com>
 * @link      https://github.com/fooplugins/debug-bar-screen-info
 * @version   1.1.5
 * @copyright 2013-2016 FooPlugins LLC
 * @license   http://creativecommons.org/licenses/GPL/2.0/ GNU General Public License, version 2 or higher
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

/**
 * Debug Bar Screen Info Panel class.
 */
class Debug_Bar_Screen_Info_Panel extends Debug_Bar_Panel {

	/**
	 * Tab title.
	 *
	 * @var string
	 */
	private $tab;

	/**
	 * Panel rendering callback.
	 *
	 * @var mixed Callable PHP function/method.
	 */
	private $callback;

	/**
	 * Constructor.
	 *
	 * @param string $title    The title of the panel.
	 * @param mixed  $callback The callback to use to render the panel output.
	 */
	public function __construct( $title = '', $callback = '' ) {
		$this->tab      = $title;
		$this->callback = $callback;
		parent::__construct();
	}

	/**
	 * Initialize the panel.
	 */
	public function init() {
		$this->title( $this->tab );
	}

	/**
	 * Limit visibility of the output to super admins on multi-site and
	 * admins on non multi-site installations.
	 */
	public function prerender() {
		$this->set_visible( is_admin() );
	}

	/**
	 * Render the output.
	 */
	public function render() {
		echo call_user_func( $this->callback ); // WPCS: XSS ok.
	}
}
