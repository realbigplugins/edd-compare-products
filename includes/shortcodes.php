<?php
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
	$defaults = isset( $edd_options['edd-compare-products-default-ids'] ) ? $edd_options['edd-compare-products-default-ids'] : array();
	$atts = shortcode_atts( array(
		'ids' => isset( $_GET['compare'] ) ? $_GET['compare'] : $defaults,
	), $atts );

	if ( $atts['ids'] ) {

		// Turn IDs into an array of numbers
		$download_ids = array_filter( explode( ',', $atts['ids'] ), 'is_numeric' );

		// Loop over the array of objects and display the results
		if ( $download_ids ) {
			$output  = '<div class="edd-compare-products ' . $edd_options["edd-compare-products-default-style"] . '"><table>';
			$results = '';
			// Header row
			$output .= '<thead><tr><th>Title</th>';
			foreach ( $download_ids as $download_id ) {
				$download = edd_get_download( $download_id );
				if ( is_object( $download ) ) {
					$results .= '<th><a href="' . get_permalink( $download->ID ) . '">' . $download->post_title . '</a></th>';
				}
			}
			$output = $output . $results;
			$output .= '</tr></thead>';

			$output .= '<tbody>';
			// Rows
			$fields = edd_compare_get_meta_fields();
			if ( $fields ) {
				// Meta fields
				foreach ( $fields as $field ) {
					$output .= '<tr>';
					$output .= '<td>';
					$output .= ( $field['label'] ) ? $field['label'] : $field['meta_field'];
					$output .= '</td>';
					foreach ( $download_ids as $download_id ) {
						$download = edd_get_download( $download_id );
						if ( is_object( $download ) ) {
							if ( $field['meta_field'] == 'thumbnail' ) {
								$value = get_the_post_thumbnail( $download_id, 'thumbnail' );
							} else {
								$value = get_post_meta( $download_id, $field['meta_field'], true );
							}
							$output .= '<td>' . $value . '</td>';
						}
					}
					$output .= '</tr>';
				}
				// Buy button
				$output .= '<tr><td> </td>';
				foreach ( $download_ids as $download_id ) {
					$download = edd_get_download( $download_id );
					if ( is_object( $download ) ) {
						$output .= '<td>' . edd_get_purchase_link( array( 'download_id' => $download_id ) ) . '</td>';
					}
				}
				$output .= '</tr>';
			}
			$output .= '</tbody>';
			$output .= '</table></div>';
			if ( empty( $results ) ) {
				$output = __( 'The ' . edd_get_label_singular() . ' IDs provided do not match any existing ' . edd_get_label_plural() . '. Please select some ' . edd_get_label_plural() . ' to compare.', 'edd-compare-products' );
			}
		} else {
			$output = __( 'The ' . edd_get_label_singular() . ' IDs provided are invalid. Please select some ' . edd_get_label_plural() . ' to compare.', 'edd-compare-products' );
		}
	} else {
		$output = __( 'There\'s currently nothing to compare. Please select some ' . edd_get_label_plural() . ' to compare.', 'edd-compare-products' );
	}

	return $output;
}

add_shortcode( 'edd_compare_products', 'edd_compare_products_shortcode' );