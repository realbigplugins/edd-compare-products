<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load admin scripts
 *
 * @since       0.1
 * @global      array $edd_settings_page The slug for the EDD settings page
 * @global      string $post_type The type of post that we are editing
 * @return      void
 */
function edd_compare_products_admin_scripts( $hook ) {
	global $edd_settings_page, $post_type;
	if ( $hook == $edd_settings_page ) {
		wp_enqueue_script( 'edd_compare_products_admin_js', EDD_COMPARE_PRODUCTS_URL . 'assets/js/admin.js' );
		wp_enqueue_style( 'edd_compare_products_admin_css', EDD_COMPARE_PRODUCTS_URL . 'assets/css/admin.css' );
	}
}

add_action( 'admin_enqueue_scripts', 'edd_compare_products_admin_scripts', 100 );
/**
 * Load frontend scripts
 *
 * @since       0.1
 * @return      void
 */
function edd_compare_products_scripts() {
	wp_enqueue_script( 'edd_compare_products_js', EDD_COMPARE_PRODUCTS_URL . 'assets/js/scripts.js', array( 'jquery', 'jquery-ui-core', 'jquery-effects-slide' ) );
	wp_enqueue_style( 'edd_compare_products_css', EDD_COMPARE_PRODUCTS_URL . 'assets/css/styles.css' );
}

/**
 * Load style/scripts on default compare page
 *
 * @since 0.1.1
 */
function edd_compare_products_enqueue() {
	global $edd_options;

	if ( isset( $edd_options['edd-compare-products-page'] ) ) {
		$compare_page = $edd_options['edd-compare-products-page'];
	} else {
		return;
	}

	if ( is_page( $compare_page ) ) {
		edd_compare_products_scripts();
	}
}

add_action( 'edd_download_before', 'edd_compare_products_scripts' );
add_action( 'wp', 'edd_compare_products_enqueue' );