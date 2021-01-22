<?php
/**
 * Slugs Manager - Settings Class
 *
 * @version 2.4.0
 * @since   2.4.0
 * @author  Algoritmika Ltd
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_Slugs_Manager_Settings' ) ) :

class Alg_Slugs_Manager_Settings {

	/**
	 * Constructor.
	 *
	 * @version 2.4.0
	 * @since   2.4.0
	 */
	function __construct() {
		add_action( 'admin_menu', array( $this, 'add_plugin_options_page' ) );
		add_action( 'admin_init', array( $this, 'save_settings' ) );
	}

	/**
	 * save_settings.
	 *
	 * @version 2.4.0
	 * @since   2.4.0
	 */
	function save_settings() {
		if ( isset( $_POST['alg_remove_old_slugs_on_save_post'] ) ) {
			update_option( 'alg_remove_old_slugs_on_save_post_enabled', sanitize_text_field( $_POST['alg_remove_old_slugs_on_save_post_enabled'] ) );
		}
		if ( isset( $_POST['alg_remove_old_slugs_crons'] ) ) {
			update_option( 'alg_remove_old_slugs_cron_interval', sanitize_text_field( $_POST['alg_remove_old_slugs_crons_interval'] ) );
			do_action( 'alg_slugs_manager_save_settings_crons' );
		}
	}

	/*
	 * add_plugin_options_page.
	 *
	 * @version 2.2.0
	 * @since   1.0.0
	 */
	function add_plugin_options_page() {
		add_submenu_page(
			'tools.php',
			__( 'Old Slugs', 'remove-old-slugspermalinks' ),
			__( 'Old Slugs', 'remove-old-slugspermalinks' ),
			'manage_options',
			'alg-remove-old-slugs',
			array( $this, 'create_admin_page' )
		);
	}

	/*
	 * create_admin_page.
	 *
	 * @version 2.4.0
	 * @since   1.0.0
	 */
	function create_admin_page() {
		$html  = '';

		// Header
		$html .= '<div class="wrap">';
		$html .= '<h1>' . __( 'Old Slugs', 'remove-old-slugspermalinks' ) . '</h1>';
		$html .= '<p><em>' . __( 'This tool removes old slugs (permalinks) from database.', 'remove-old-slugspermalinks' ) . '</em></p>';

		// Old slugs
		$html .= $this->display_old_slugs_table();

		// Automatic clean ups
		$html .= $this->display_automatic_clean_ups_options();

		// Regenerate slugs
		$html .= $this->display_regenerate_slugs_options();

		// The end
		$html .= '</div>';

		echo $html;
	}

	/**
	 * display_old_slugs_table.
	 *
	 * @version 2.4.0
	 * @since   2.4.0
	 */
	function display_old_slugs_table() {
		$html          = '';
		$html         .= '<p>';
		$old_slugs     = alg_slugs_manager()->core->get_old_slugs();
		$num_old_slugs = count( $old_slugs );
		if ( $num_old_slugs > 0 ) {
			$table_data   = array();
			$table_data[] = array(
				'#',
				__( 'Old Slug', 'remove-old-slugspermalinks' ),
				__( 'Post ID', 'remove-old-slugspermalinks' ),
				__( 'Post Title', 'remove-old-slugspermalinks' ),
				__( 'Post Type', 'remove-old-slugspermalinks' ),
				__( 'Current Slug', 'remove-old-slugspermalinks' ),
			);
			$i = 0;
			foreach ( $old_slugs as $old_slug ) {
				$i++;
				$post_type    = get_post_type( $old_slug->post_id );
				$post_title   = get_the_title( $old_slug->post_id );
				$current_slug = get_post( $old_slug->post_id );
				$current_slug = $current_slug->post_name;
				$table_data[] = array(
					$i,
					$old_slug->meta_value,
					$old_slug->post_id,
					$post_title,
					$post_type,
					$current_slug,
				);
			}
			$html .= sprintf( __( '<p><strong>%d</strong> old slug(s) found:<p>', 'remove-old-slugspermalinks' ), $num_old_slugs );
			$html .= $this->get_table_html( $table_data, array( 'table_class' => 'widefat striped' ) );
			$html .= '<p>';
			$html .= '<form method="post" action="">';
			$html .= '<input class="button-primary" type="submit" name="alg_slugs_manager_remove_old_slugs" onclick="return confirm(\'' .
				__( 'Are you sure?', 'remove-old-slugspermalinks' ) . '\')" value="' . __( 'Remove old slugs', 'remove-old-slugspermalinks' ) . '"/>';
			$html .= '</form>';
			$html .= '</p>';
		} else {
			// None old slugs found
			$html .= '<strong><em>' . __( 'No old slugs found in database.', 'remove-old-slugspermalinks' ) . '</em></strong>';
		}
		$html .= '</p>';
		// Refresh link
		$html .= '<p><a href="' . admin_url( 'tools.php?page=alg-remove-old-slugs' ) . '">' .
			__( 'Refresh list', 'remove-old-slugspermalinks' ) . '</a></p>';
		return $html;
	}

	/*
	 * display_automatic_clean_ups_options.
	 *
	 * @version 2.4.0
	 * @since   2.4.0
	 */
	function display_automatic_clean_ups_options() {
		$html  = '';
		$html .= '<hr>';
		// Header
		$html .= '<h2>' . __( 'Automatic Clean Ups', 'remove-old-slugspermalinks' ) . '</h2>';
		$html .= apply_filters( 'alg_slugs_manager_core_settings', '<h4 style="padding: 20px; background-color: #e9eaaa;">' . sprintf(
			__( 'You will need %s plugin to enable automatic old slugs clean ups.', 'remove-old-slugspermalinks' ),
				'<a href="https://wpfactory.com/item/remove-old-slugs-wordpress-plugin/" target="_blank">' .
					__( 'Remove Old Slugs Pro', 'remove-old-slugspermalinks' ) . '</a>' ) . '</h4>' );
		if ( isset( $_GET['alg_debug'] ) && defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) {
			$html .= '<h4 style="padding: 20px; background-color: #dddddd;">' .
				__( '<code>DISABLE_WP_CRON</code> is set to <code>true</code> in your <code>wp-config.php</code> file - "Scheduled Clean Ups" won\'t work.', 'remove-old-slugspermalinks' ) .
			'</h4>';
		}
		// Scheduled clean ups
		$form_crons  = '';
		$form_crons .= '<form method="post" action="">';
		$intervals   = array(
			'disabled'   => __( 'Disabled', 'remove-old-slugspermalinks' ),
			'minutely'   => __( 'Every minute', 'remove-old-slugspermalinks' ),
			'hourly'     => __( 'Hourly', 'remove-old-slugspermalinks' ),
			'twicedaily' => __( 'Twice daily', 'remove-old-slugspermalinks' ),
			'daily'      => __( 'Daily', 'remove-old-slugspermalinks' ),
			'weekly'     => __( 'Weekly', 'remove-old-slugspermalinks' ),
		);
		$form_crons .= '<select style="width:150px;" name="alg_remove_old_slugs_crons_interval" id="alg_remove_old_slugs_crons_interval"' .
			apply_filters( 'alg_slugs_manager_core_settings', 'disabled' ). '>';
		$selected = esc_attr( get_option( 'alg_remove_old_slugs_cron_interval', 'disabled' ) );
		foreach ( $intervals as $interval_id => $interval_desc ) {
			$form_crons .= '<option value="' . $interval_id . '" ' . selected( $selected, $interval_id, false ) . '>' . $interval_desc . '</option>';
		}
		$form_crons .= '</select>' . ' ';
		$form_crons .= '<input class="button-primary" type="submit" name="alg_remove_old_slugs_crons" value="' . __( 'Save', 'remove-old-slugspermalinks' ) . '"' .
			apply_filters( 'alg_slugs_manager_core_settings', 'disabled' ). '/>';
		$form_crons .= '</form>';
		$cron_info = '';
		if ( wp_next_scheduled( 'alg_remove_old_slugs_cron' ) ) {
			$cron_info .= '<br><em>' . sprintf( __( 'Next old slugs clean up is scheduled on %s. Current time is %s.', 'remove-old-slugspermalinks' ),
				'<code>' . date_i18n( 'Y-m-d H:i:s', wp_next_scheduled( 'alg_remove_old_slugs_cron' ) ) . '</code>',
				'<code>' . date_i18n( 'Y-m-d H:i:s', time() ) . '</code>' ) . '</em>';
		}
		// Clean up on save post
		$form_on_save_post  = '';
		$form_on_save_post .= '<form method="post" action="">';
		$form_on_save_post .= '<select style="width:150px;" name="alg_remove_old_slugs_on_save_post_enabled" id="alg_remove_old_slugs_on_save_post_enabled"' .
			apply_filters( 'alg_slugs_manager_core_settings', 'disabled' ). '>';
		$selected = esc_attr( get_option( 'alg_remove_old_slugs_on_save_post_enabled', 'no' ) );
		$form_on_save_post .= '<option value="no" '  . selected( $selected, 'no',  false ) . '>' . __( 'No', 'remove-old-slugspermalinks' )  . '</option>';
		$form_on_save_post .= '<option value="yes" ' . selected( $selected, 'yes', false ) . '>' . __( 'Yes', 'remove-old-slugspermalinks' ) . '</option>';
		$form_on_save_post .= '</select>' . ' ';
		$form_on_save_post .= '<input class="button-primary" type="submit" name="alg_remove_old_slugs_on_save_post" value="' .
			__( 'Save', 'remove-old-slugspermalinks' ) . '"' . apply_filters( 'alg_slugs_manager_core_settings', 'disabled' ). '/>';
		$form_on_save_post .= '</form>';
		// Final output
		$table_data = array(
			array(
				'<strong>' . __( 'Scheduled Clean Ups', 'remove-old-slugspermalinks' ) . '</strong>',
				'<em>' . sprintf( __( 'Set old slugs to be cleared periodically (%s).', 'remove-old-slugspermalinks' ), implode( ', ', $intervals ) ) . '</em>' .
					$cron_info,
				$form_crons,
			),
			array(
				'<strong>' . __( 'Clean Up on Save Post', 'remove-old-slugspermalinks' ) . '</strong>',
				'<em>' . __( 'Set old slugs to be cleared automatically, when post is saved.', 'remove-old-slugspermalinks' ) . '</em>',
				$form_on_save_post,
			),
		);
		$html .= $this->get_table_html( $table_data, array( 'table_class' => 'widefat striped', 'table_heading_type' => 'none',
			'columns_styles' => array( 'width:20%;', 'width:40%;', 'width:20%;' ) ) );
		return $html;
	}

	/*
	 * display_regenerate_slugs_options.
	 *
	 * @version 2.4.0
	 * @since   2.4.0
	 */
	function display_regenerate_slugs_options() {
		$html  = '';
		$html .= '<hr>';
		$html .= '<h2>' . __( 'Regenerate Slugs', 'remove-old-slugspermalinks' ) . '</h2>';
		$html .= apply_filters( 'alg_slugs_manager_core_settings', '<h4 style="padding: 20px; background-color: #e9eaaa;">' . sprintf(
			__( 'You will need %s plugin to enable slugs regeneration.', 'remove-old-slugspermalinks' ),
				'<a href="https://wpfactory.com/item/remove-old-slugs-wordpress-plugin/" target="_blank">' .
					__( 'Remove Old Slugs Pro', 'remove-old-slugspermalinks' ) . '</a>' ) . '</h4>' );
		$form_regenerate  = '';
		$form_regenerate .= '<form method="post" action="">';
		$form_regenerate .= '<input class="button-primary" type="submit" name="alg_remove_old_slugs_regenerate_slugs"' .
			' onclick="return confirm(\'' . __( 'There is no undo for this action.', 'remove-old-slugspermalinks' ) . ' ' .
			__( 'Are you sure?', 'remove-old-slugspermalinks' ) . '\')"' . ' value="' . __( 'Regenerate', 'remove-old-slugspermalinks' ) . '"' .
			apply_filters( 'alg_slugs_manager_core_settings', 'disabled' ). '/>';
		$form_regenerate .= '</form>';
		$table_data = array(
			array(
				'<strong>' . __( 'Regenerate Slugs', 'remove-old-slugspermalinks' ) . '</strong>',
				'<em>' . __( 'Regenerate slug from <strong>title</strong> for all posts.', 'remove-old-slugspermalinks' ) . '</em>',
				$form_regenerate,
			),
		);
		$html .= $this->get_table_html( $table_data, array( 'table_class' => 'widefat striped', 'table_heading_type' => 'none',
			'columns_styles' => array( 'width:20%;', 'width:40%;', 'width:20%;' ) ) );
		return $html;
	}

	/**
	 * get_table_html.
	 *
	 * @version 2.4.0
	 * @since   2.0.0
	 */
	function get_table_html( $data, $args = array() ) {
		$defaults = array(
			'table_class'        => '',
			'table_style'        => '',
			'table_heading_type' => 'horizontal',
			'columns_classes'    => array(),
			'columns_styles'     => array(),
		);
		$args        = array_merge( $defaults, $args );
		$table_class = ( '' == $args['table_class'] ) ? '' : ' class="' . $args['table_class'] . '"';
		$table_style = ( '' == $args['table_style'] ) ? '' : ' style="' . $args['table_style'] . '"';
		$html        = '';
		$html       .= '<table' . $table_class . $table_style . '>';
		$html       .= '<tbody>';
		foreach( $data as $row_number => $row ) {
			$html .= '<tr>';
			foreach( $row as $column_number => $value ) {
				$th_or_td     = ( ( 0 === $row_number && 'horizontal' === $args['table_heading_type'] ) ||
					( 0 === $column_number && 'vertical' === $args['table_heading_type'] ) ) ? 'th' : 'td';
				$column_class = ( ! empty( $args['columns_classes'] ) && isset( $args['columns_classes'][ $column_number ] ) ) ?
					' class="' . $args['columns_classes'][ $column_number ] . '"' : '';
				$column_style = ( ! empty( $args['columns_styles'] )  && isset( $args['columns_styles'][ $column_number ] ) )  ?
					' style="' . $args['columns_styles'][ $column_number ]  . '"' : '';
				$html        .= '<' . $th_or_td . $column_class . $column_style . '>';
				$html        .= $value;
				$html        .= '</' . $th_or_td . '>';
			}
			$html .= '</tr>';
		}
		$html .= '</tbody>';
		$html .= '</table>';
		return $html;
	}

}

endif;

return new Alg_Slugs_Manager_Settings();
