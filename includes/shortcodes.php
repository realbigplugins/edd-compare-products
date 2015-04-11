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
			$output = '';
			foreach ( $download_ids as $download_id ) {
				$download = edd_get_download( $download_id );
				$output .= ( is_object( $download ) ) ? $download->post_title : '';
			}
			if ( empty( $output ) ) {
				$output = 'No downloads defined.';
			}

		} else {
			$output = 'No downloads found.';
		}
	} else {
		$output = 'Zero downloads found.';
	}

	return $output;
}

add_shortcode( 'edd_compare_products', 'edd_compare_products_shortcode' );