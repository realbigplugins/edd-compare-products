<?php
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;


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
	if( $hook == $edd_settings_page ) {
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
function edd_compare_products_scripts( $hook ) {
	wp_enqueue_script( 'edd_compare_products_js', EDD_COMPARE_PRODUCTS_URL . 'assets/js/scripts.js' );
	wp_enqueue_style( 'edd_compare_products_css', EDD_COMPARE_PRODUCTS_URL . 'assets/css/styles.css' );
}
add_action( 'wp_enqueue_scripts', 'edd_compare_products_scripts' );