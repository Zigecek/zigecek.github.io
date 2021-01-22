<?php
/*
Plugin Name: Remove Old Slugs
Plugin URI: https://wpfactory.com/item/remove-old-slugs-wordpress-plugin/
Description: Plugin removes old slugs (permalinks) from database.
Version: 2.4.1
Author: Algoritmika Ltd
Author URI: https://algoritmika.com
Text Domain: remove-old-slugspermalinks
Domain Path: /langs
Copyright: © 2020 Algoritmika Ltd.
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_Slugs_Manager' ) ) :

/**
 * Main Alg_Slugs_Manager Class
 *
 * @class   Alg_Slugs_Manager
 * @version 2.4.0
 * @since   1.0.0
 */
final class Alg_Slugs_Manager {

	/**
	 * Plugin version.
	 *
	 * @var   string
	 * @since 2.3.0
	 */
	public $version = '2.4.1';

	/**
	 * @var   Alg_Slugs_Manager The single instance of the class
	 * @since 2.4.0
	 */
	protected static $_instance = null;

	/**
	 * Main Alg_Slugs_Manager Instance
	 *
	 * Ensures only one instance of Alg_Slugs_Manager is loaded or can be loaded.
	 *
	 * @version 2.4.0
	 * @since   2.4.0
	 * @static
	 * @return  Alg_Slugs_Manager - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/*
	 * Alg_Slugs_Manager Constructor.
	 *
	 * @version 2.4.0
	 * @since   1.0.0
	 * @access  public
	 * @todo    [dev] rename plugin to e.g. `Slugs Manager`
	 * @todo    [dev] maybe move *all* to `is_admin()`
	 */
	function __construct() {

		// Check for active plugins
		if ( 'remove-old-slugs.php' === basename( __FILE__ ) && $this->is_plugin_active( 'remove-old-slugs-pro/remove-old-slugs-pro.php' ) ) {
			return;
		}

		// Set up localisation
		load_plugin_textdomain( 'remove-old-slugspermalinks', false, dirname( plugin_basename( __FILE__ ) ) . '/langs/' );

		// Pro
		if ( 'remove-old-slugs-pro.php' === basename( __FILE__ ) ) {
			require_once( 'includes/pro/class-alg-slugs-manager-pro.php' );
		}

		// Include required files
		$this->includes();

		// Admin
		if ( is_admin() ) {
			$this->admin();
		}

		// Action
		do_action( 'alg_slugs_manager_plugin_loaded', __FILE__ );

	}

	/**
	 * is_plugin_active.
	 *
	 * @version 2.4.0
	 * @since   2.4.0
	 */
	function is_plugin_active( $plugin ) {
		return ( function_exists( 'is_plugin_active' ) ? is_plugin_active( $plugin ) :
			(
				in_array( $plugin, apply_filters( 'active_plugins', ( array ) get_option( 'active_plugins', array() ) ) ) ||
				( is_multisite() && array_key_exists( $plugin, ( array ) get_site_option( 'active_sitewide_plugins', array() ) ) )
			)
		);
	}

	/**
	 * includes.
	 *
	 * @version 2.4.0
	 * @since   2.4.0
	 */
	function includes() {
		// Core
		$this->core = require_once( 'includes/class-alg-slugs-manager-core.php' );
	}

	/**
	 * admin.
	 *
	 * @version 2.4.0
	 * @since   2.4.0
	 */
	function admin() {
		// Action links
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );
		// Settings
		require_once( 'includes/settings/class-alg-slugs-manager-settings.php' );
		// Version update
		if ( get_option( 'alg_slugs_manager_plugin_version', '' ) !== $this->version ) {
			add_action( 'admin_init', array( $this, 'version_updated' ) );
		}
	}

	/**
	 * action_links.
	 *
	 * @version 2.4.0
	 * @since   2.0.0
	 * @param   mixed $links
	 * @return  array
	 */
	function action_links( $links ) {
		$custom_links = array();
		$custom_links[] = '<a href="' . admin_url( 'tools.php?page=alg-remove-old-slugs' ) . '">' . __( 'Settings', 'remove-old-slugspermalinks' ) . '</a>';
		if ( 'remove-old-slugs.php' === basename( __FILE__ ) ) {
			$custom_links[] = '<a target="_blank" href="https://wpfactory.com/item/remove-old-slugs-wordpress-plugin/">' .
				__( 'Unlock All', 'remove-old-slugspermalinks' ) . '</a>';
		}
		return array_merge( $custom_links, $links );
	}

	/**
	 * version_updated.
	 *
	 * @version 2.4.0
	 * @since   2.4.0
	 */
	function version_updated() {
		update_option( 'alg_slugs_manager_plugin_version', $this->version );
	}

	/**
	 * plugin_url.
	 *
	 * @version 2.4.0
	 * @since   2.4.0
	 * @return  string
	 */
	function plugin_url() {
		return untrailingslashit( plugin_dir_url( __FILE__ ) );
	}

	/**
	 * plugin_path.
	 *
	 * @version 2.4.0
	 * @since   2.4.0
	 * @return  string
	 */
	function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

}

endif;

if ( ! function_exists( 'alg_slugs_manager' ) ) {
	/**
	 * Returns the main instance of Alg_Slugs_Manager to prevent the need to use globals.
	 *
	 * @version 2.4.0
	 * @since   2.4.0
	 * @return  Alg_Slugs_Manager
	 */
	function alg_slugs_manager() {
		return Alg_Slugs_Manager::instance();
	}
}

add_action( 'plugins_loaded', 'alg_slugs_manager' );
