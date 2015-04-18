<?php
/**
 * Debug Bar Screen Info
 *
 * @package		WordPress\Plugins\debug-bar-screen-info
 * @author		Brad Vincent <brad@fooplugins.com>
 * @link		https://github.com/fooplugins/debug-bar-screen-info
 * @version		1.1.3
 * @copyright	2013 FooPlugins LLC
 * @license		http://creativecommons.org/licenses/GPL/2.0/ GNU General Public License, version 2 or higher
 */
class Debug_Bar_Admin_Screen_Info {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $version = '1.1.1';

	/**
	 * Unique identifier for your plugin.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'debug-bar-screen-info';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Initialize the plugin
	 *
	 * @since     1.0.0
	 */
	private function __construct() {
		add_filter( 'debug_bar_panels', array( $this, 'screen_info_panel' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function enqueue_scripts() {
		$suffix = ( ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min' );
		wp_enqueue_style( $this->plugin_slug, plugins_url( 'css/debug-bar-screen-info' . $suffix . '.css', __FILE__ ), array( 'debug-bar' ), $this->version );
		unset( $suffix );
	}

	public function screen_info_panel( $panels ) {
		load_plugin_textdomain( $this->plugin_slug, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		require_once ( 'class-debug-bar-screen-info-panel.php' );
		$panel = new Debug_Bar_Screen_Info_Panel();
		$panel->set_tab( __( 'Screen Info', $this->plugin_slug ), array( $this, 'screen_info_render' ) );
		$panels[] = $panel;
		return $panels;
	}


	public function screen_info_render() {

		/* Set parentage of current page
		   Isn't set yet as it is set from admin_header.php which is run after the admin bar has loaded
		   on the admin side */
		if ( ( isset( $GLOBALS['current_screen'] ) && is_object( $GLOBALS['current_screen'] ) ) && ( isset( $GLOBALS['parent_file'] ) && is_string( $GLOBALS['parent_file'] ) && $GLOBALS['parent_file'] !== '' ) ) {
			$GLOBALS['current_screen']->set_parentage( $GLOBALS['parent_file'] );
		}

		$screen = get_current_screen();

		if ( isset( $screen ) && is_object( $screen ) ) {

			$properties = get_object_vars( $screen );

			if ( is_array( $properties ) && $properties !== array() ) {

				$output = '
		<h2><span>' . esc_html__( 'Screen:', $this->plugin_slug ) . '</span>' . esc_html( $screen->id ) . '</h2>
		<h2><span>' . esc_html__( 'Properties:', $this->plugin_slug ) . '</span>' . count( $properties ) . '</h2>';

				uksort( $properties, 'strnatcasecmp' );

				if ( ! class_exists( 'Debug_Bar_Pretty_Output' ) ) {
					require_once plugin_dir_path( __FILE__ ) . 'inc/debug-bar-pretty-output/class-debug-bar-pretty-output.php';
				}

				if ( defined( 'Debug_Bar_Pretty_Output::VERSION' ) ) {
					add_filter( 'db_pretty_output_table_header', array( $this, 'filter_pretty_output_table_header_row' ) );
					add_filter( 'db_pretty_output_table_body_row', array( $this, 'filter_pretty_output_table_body_row' ), 10, 2 );

					$output .= Debug_Bar_Pretty_Output::get_table( $properties, __( 'Property', $this->plugin_slug ), __( 'Value', $this->plugin_slug ), $this->plugin_slug );

					remove_filter( 'db_pretty_output_table_header', array( $this, 'filter_pretty_output_table_header_row' ) );
					remove_filter( 'db_pretty_output_table_body_row', array( $this, 'filter_pretty_output_table_body_row' ), 10, 2 );
				}
				else {
					/* An old version of the pretty output class was loaded,
					   the explanations will not be added to the table */
					ob_start();
					Debug_Bar_Pretty_Output::render_table( $properties, __( 'Property', $this->plugin_slug ), __( 'Value', $this->plugin_slug ), $this->plugin_slug );
					$output .= ob_get_contents();
					ob_end_clean();
				}
			}
		}
		else {
			$output = '<h2>' . esc_html__( 'No Screen Info Found', $this->plugin_slug ) . '</h2>';
		}

		$output .= '<p>' . sprintf( esc_html__( 'For more information, see the %sCodex on WP_Screen', $this->plugin_slug ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Screen" target="_blank" title="' . esc_attr__( 'View the WordPress codex on WP Screen', $this->plugin_slug ) . '">' ) . '</a></p>';

		return $output;
	}


	public function filter_pretty_output_table_header_row( $row ) {
		$replace = '	<th>' . esc_html__( 'Significance', $this->plugin_slug ) . '</th>
			</tr>';
		$row     = str_replace( '</tr>', $replace, $row );

		return $row;
	}

	public function filter_pretty_output_table_body_row( $row, $key ) {
		$explain = array(
			'id'			=> __( 'The unique ID of the screen.', $this->plugin_slug ),
			'action'		=> __( 'Any action associated with the screen.', $this->plugin_slug ),
			'base'			=> __( 'The base type of the screen. This is typically the same as id but with any post types and taxonomies stripped.', $this->plugin_slug ),
			'is_network'	=> __( 'Whether this is a multi-site network admin screen.', $this->plugin_slug ),
			'is_user'		=> __( 'Whether this is a user admin screen.', $this->plugin_slug ),
			'parent_base'	=> __( 'The base menu parent.', $this->plugin_slug ),
			'parent_file'	=> __( 'The parent_file for the screen per the admin menu system.', $this->plugin_slug ),
			'post_type'		=> __( 'The post type associated with the screen, if any.', $this->plugin_slug ),
			'taxonomy'		=> __( 'The taxonomy associated with the screen, if any.', $this->plugin_slug ),
		);

		$replace = '	<td class="' . esc_attr( $this->plugin_slug ) . '-explain">' . ( isset( $explain[ $key ] ) ? esc_html( $explain[ $key ] ) : '&nbsp;' ) . '
				</td>
			</tr>';
		$row     = str_replace( '</tr>', $replace, $row );

		return $row;
	}

}
