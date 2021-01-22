<?php
/**
 * Slugs Manager - Core Class
 *
 * @version 2.4.0
 * @since   2.4.0
 * @author  Algoritmika Ltd
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_Slugs_Manager_Core' ) ) :

class Alg_Slugs_Manager_Core {

	/**
	 * Constructor.
	 *
	 * @version 2.4.0
	 * @since   2.4.0
	 */
	function __construct() {
		add_action( 'admin_init', array( $this, 'manage_old_slugs' ) );
	}

	/*
	 * delete_old_slugs.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function delete_old_slugs( $post_ids = false ) {
		global $wpdb;
		$table = $wpdb->prefix . 'postmeta';
		$query = "DELETE FROM {$table} WHERE meta_key = '_wp_old_slug'" . ( $post_ids ? " AND post_id = {$post_ids}" : '' );
		$wpdb->get_results( $query );
	}

	/*
	 * get_old_slugs.
	 *
	 * @version 2.4.0
	 * @since   2.4.0
	 */
	function get_old_slugs() {
		global $wpdb;
		$table = $wpdb->prefix . 'postmeta';
		$query = "SELECT * FROM {$table} WHERE meta_key = '_wp_old_slug' ORDER BY post_id";
		return $wpdb->get_results( $query );
	}

	/*
	 * manage_old_slugs.
	 *
	 * @version 2.4.0
	 * @since   2.0.0
	 */
	function manage_old_slugs() {
		// Remove old slugs
		if ( isset( $_POST['alg_slugs_manager_remove_old_slugs'] ) ) {
			$old_slugs     = $this->get_old_slugs();
			$num_old_slugs = count( $old_slugs );
			if ( $num_old_slugs > 0 ) {
				// Old slugs found
				$this->delete_old_slugs();
				$old_slugs_after_deletion    = $this->get_old_slugs();
				$this->old_slugs_deleted_num = ( $num_old_slugs - count( $old_slugs_after_deletion ) );
				add_action( 'admin_notices', array( $this, 'admin_notice_old_slugs_deleted' ) );
			}
		}
	}

	/**
	 * admin_notice_old_slugs_deleted.
	 *
	 * @version 2.4.0
	 * @since   2.4.0
	 */
	function admin_notice_old_slugs_deleted() {
		$message = sprintf( __( 'Removing old slugs from database finished! %d old slug(s) deleted.', 'remove-old-slugspermalinks' ), $this->old_slugs_deleted_num );
		echo '<div class="notice notice-success is-dismissible"><p>' . $message . '</p></div>';
	}

}

endif;

return new Alg_Slugs_Manager_Core();
