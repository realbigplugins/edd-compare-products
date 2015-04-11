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

	if ( ! empty( $_GET['compare'] ) ) {
		$download_ids = array_filter( explode( ',', $_GET['compare'] ), 'is_numeric' );

		if ( $download_ids ) {
			$output = '';
			foreach ( $download_ids as $download_id ) {
				$download = edd_get_download( $download_id );
				$output .= ( is_object( $download ) ) ? $download->post_title : '';
			}

			return $output;
		} else {
			return 'No downloads to compare (no numeric values).';
		}
	} else {
		return 'No downloads to compare (no URL parameters).';
	}
}

add_shortcode( 'edd_compare_products', 'edd_compare_products_shortcode' );