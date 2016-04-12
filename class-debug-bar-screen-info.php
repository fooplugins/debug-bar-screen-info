<?php
/**
 * Debug Bar Screen Info
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
 * Debug Bar Admin Screen Info class.
 */
class Debug_Bar_Admin_Screen_Info {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @var     string
	 *
	 * @since   1.0.0
	 */
	protected $version = '1.1.1';

	/**
	 * Unique identifier for your plugin.
	 *
	 * @var      string
	 *
	 * @since    1.0.0
	 */
	protected $plugin_slug = 'debug-bar-screen-info';

	/**
	 * Instance of this class.
	 *
	 * @var      object
	 *
	 * @since    1.0.0
	 */
	protected static $instance = null;


	/**
	 * Return an instance of this class.
	 *
	 * @return    object    A single instance of this class.
	 *
	 * @since     1.0.0
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}


	/**
	 * Initialize the plugin.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {
		add_filter( 'debug_bar_panels', array( $this, 'screen_info_panel' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}


	/**
	 * Enqueue scripts.
	 */
	public function enqueue_scripts() {
		$suffix = ( ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min' );
		wp_enqueue_style( $this->plugin_slug, plugins_url( 'css/debug-bar-screen-info' . $suffix . '.css', __FILE__ ), array( 'debug-bar' ), $this->version );
		unset( $suffix );
	}


	/**
	 * Create the screen info debug bar tab.
	 *
	 * @param array $panels Existing debug bar panels.
	 *
	 * @return array
	 */
	public function screen_info_panel( $panels ) {
		$this->load_textdomain( $this->plugin_slug );

		require_once 'class-debug-bar-screen-info-panel.php';
		$panels[] = new Debug_Bar_Screen_Info_Panel( __( 'Screen Info', 'debug-bar-screen-info' ), array( $this, 'screen_info_render' ) );
		return $panels;
	}


	/**
	 * Load the plugin text strings.
	 *
	 * Compatible with use of the plugin in the must-use plugins directory.
	 *
	 * @param string $domain Text domain to load.
	 */
	protected function load_textdomain( $domain ) {
		if ( is_textdomain_loaded( $domain ) ) {
			return;
		}

		$lang_path = dirname( plugin_basename( __FILE__ ) ) . '/languages';
		if ( false === strpos( __FILE__, basename( WPMU_PLUGIN_DIR ) ) ) {
			load_plugin_textdomain( $domain, false, $lang_path );
		} else {
			load_muplugin_textdomain( $domain, $lang_path );
		}
	}


	/**
	 * Render the screen info.
	 *
	 * @return string
	 */
	public function screen_info_render() {
		/*
		 * Set parentage of current page.
		 * Isn't set yet as it is set from admin_header.php which is run after the admin bar has loaded
		 * on the admin side.
		 */
		if ( ( isset( $GLOBALS['current_screen'] ) && is_object( $GLOBALS['current_screen'] ) ) && ( isset( $GLOBALS['parent_file'] ) && is_string( $GLOBALS['parent_file'] ) && '' !== $GLOBALS['parent_file'] ) ) {
			$GLOBALS['current_screen']->set_parentage( $GLOBALS['parent_file'] );
		}

		$screen = get_current_screen();

		if ( isset( $screen ) && is_object( $screen ) ) {

			$properties = get_object_vars( $screen );

			if ( ! empty( $properties ) && is_array( $properties ) ) {

				$output = '
		<h2><span>' . esc_html__( 'Screen:', 'debug-bar-screen-info' ) . '</span>' . esc_html( $screen->id ) . '</h2>
		<h2><span>' . esc_html__( 'Properties:', 'debug-bar-screen-info' ) . '</span>' . count( $properties ) . '</h2>';

				uksort( $properties, 'strnatcasecmp' );

				if ( ! class_exists( 'Debug_Bar_Pretty_Output' ) ) {
					require_once plugin_dir_path( __FILE__ ) . 'inc/debug-bar-pretty-output/class-debug-bar-pretty-output.php';
				}

				if ( defined( 'Debug_Bar_Pretty_Output::VERSION' ) ) {
					add_filter( 'db_pretty_output_table_header', array( $this, 'filter_pretty_output_table_header_row' ) );
					add_filter( 'db_pretty_output_table_body_row', array( $this, 'filter_pretty_output_table_body_row' ), 10, 2 );

					$output .= Debug_Bar_Pretty_Output::get_table( $properties, __( 'Property', 'debug-bar-screen-info' ), __( 'Value', 'debug-bar-screen-info' ), $this->plugin_slug );

					remove_filter( 'db_pretty_output_table_header', array( $this, 'filter_pretty_output_table_header_row' ) );
					remove_filter( 'db_pretty_output_table_body_row', array( $this, 'filter_pretty_output_table_body_row' ), 10, 2 );

				}
				else {
					/*
					 * An old version of the pretty output class was loaded,
					 * the explanations will not be added to the table.
					 */
					ob_start();
					Debug_Bar_Pretty_Output::render_table( $properties, __( 'Property', 'debug-bar-screen-info' ), __( 'Value', 'debug-bar-screen-info' ), $this->plugin_slug );
					$output .= ob_get_contents();
					ob_end_clean();
				}
			}
		} else {
			$output = '<h2>' . esc_html__( 'No Screen Info Found', 'debug-bar-screen-info' ) . '</h2>';
		}

		/* TRANSLATORS: %s = the "href" element for the link. */
		$output .= '<p>' . sprintf( wp_kses_post( __( 'For more information, see the <a %s>Codex on WP_Screen</a>', 'debug-bar-screen-info' ) ), 'href="http://codex.wordpress.org/Class_Reference/WP_Screen" target="_blank" title="' . esc_attr__( 'View the WordPress codex on WP Screen', 'debug-bar-screen-info' ) . '">' ) . '</p>';

		return $output;
	}


	/**
	 * Adjust the default output of the pretty printing for the table headers.
	 *
	 * @param string $row Current table row.
	 *
	 * @return string
	 */
	public function filter_pretty_output_table_header_row( $row ) {
		$replace = '	<th>' . esc_html__( 'Significance', 'debug-bar-screen-info' ) . '</th>
			</tr>';
		$row     = str_replace( '</tr>', $replace, $row );

		return $row;
	}


	/**
	 * Adjust the default output of the pretty printing for the table content.
	 *
	 * @param string $row Current table row.
	 * @param string $key Key for the current table row.
	 *
	 * @return string
	 */
	public function filter_pretty_output_table_body_row( $row, $key ) {
		$explain = array(
			'id'			=> __( 'The unique ID of the screen.', 'debug-bar-screen-info' ),
			'action'		=> __( 'Any action associated with the screen.', 'debug-bar-screen-info' ),
			'base'			=> __( 'The base type of the screen. This is typically the same as id but with any post types and taxonomies stripped.', 'debug-bar-screen-info' ),
			'is_network'	=> __( 'Whether this is a multi-site network admin screen.', 'debug-bar-screen-info' ),
			'is_user'		=> __( 'Whether this is a user admin screen.', 'debug-bar-screen-info' ),
			'parent_base'	=> __( 'The base menu parent.', 'debug-bar-screen-info' ),
			'parent_file'	=> __( 'The parent_file for the screen per the admin menu system.', 'debug-bar-screen-info' ),
			'post_type'		=> __( 'The post type associated with the screen, if any.', 'debug-bar-screen-info' ),
			'taxonomy'		=> __( 'The taxonomy associated with the screen, if any.', 'debug-bar-screen-info' ),
		);

		$replace = '	<td class="' . esc_attr( $this->plugin_slug ) . '-explain">' . ( isset( $explain[ $key ] ) ? esc_html( $explain[ $key ] ) : '&nbsp;' ) . '
				</td>
			</tr>';
		$row     = str_replace( '</tr>', $replace, $row );

		return $row;
	}
}
