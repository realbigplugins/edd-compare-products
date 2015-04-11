<?php
/**
 * Created by PhpStorm.
 * User: kylemaurer
 * Date: 4/8/15
 * Time: 6:14 PM
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Compare shortcode callback
 *
 * @param $atts
 *
 * @since 0.1
 * @return string
 */
function edd_compare_products_shortcode( $atts ) {

	global $edd_options;

	$atts = shortcode_atts( array(
		'ids' => isset( $_GET['compare'] ) ? $_GET['compare'] : $edd_options['edd-compare-products-default-ids'],
	), $atts );

	if ( $atts['ids'] ) {

		// Turn IDs into an array of numbers
		$download_ids = array_filter( explode( ',', $atts['ids'] ), 'is_numeric' );

		// Loop over the array of objects and display the results
		if ( $download_ids ) {
			$output = '<div class="edd-compare-products">';
			$results = '';
			foreach ( $download_ids as $download_id ) {
				$download = edd_get_download( $download_id );
				if ( is_object( $download ) ) {
					$results .= $download->post_title;
				}
			}
			$output .= '</div>';
			if ( empty( $results ) ) {
				$output = 'IDs do not match any downloads.';
			}
		} else {
			$output = 'No numerical IDs provided.';
		}
	} else {
		$output = 'No download IDs provided.';
	}

	return $output;
}

add_shortcode( 'edd_compare_products', 'edd_compare_products_shortcode' );