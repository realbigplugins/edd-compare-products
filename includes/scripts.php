<?php
/**
 * Created by PhpStorm.
 * User: kylemaurer
 * Date: 4/8/15
 * Time: 6:17 PM
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;


/**
 * Load admin scripts
 *
 * @since       1.0.0
 * @global      array $edd_settings_page The slug for the EDD settings page
 * @global      string $post_type The type of post that we are editing
 * @return      void
 */
function edd_compare_products_admin_scripts( $hook ) {
	global $edd_settings_page, $post_type;
	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	/**Initi
	 * @todo		This block loads styles or scripts explicitly on the
	 *				EDD settings page.
	 */
	if( $hook == $edd_settings_page ) {
		wp_enqueue_script( 'edd_compare_products_admin_js', EDD_COMPARE_PRODUCTS_URL . '/assets/js/admin' . $suffix . '.js', array( 'jquery' ) );
		wp_enqueue_style( 'edd_compare_products_admin_css', EDD_COMPARE_PRODUCTS_URL . '/assets/css/admin' . $suffix . '.css' );
	}
}
add_action( 'admin_enqueue_scripts', 'edd_compare_products_admin_scripts', 100 );
/**
 * Load frontend scripts
 *
 * @since       1.0.0
 * @return      void
 */
function edd_compare_products_scripts( $hook ) {
	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_enqueue_script( 'edd_compare_products_js', EDD_COMPARE_PRODUCTS_URL . '/assets/js/scripts' . $suffix . '.js', array( 'jquery' ) );
	wp_enqueue_style( 'edd_compare_products_css', EDD_COMPARE_PRODUCTS_URL . '/assets/css/styles' . $suffix . '.css' );
}
add_action( 'wp_enqueue_scripts', 'edd_compare_products_scripts' );