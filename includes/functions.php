<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * When applicable, add the compare button after the EDD purchase form button
 *
 * @param $purchase_form
 * @param $args
 *
 * @return string
 */
function edd_compare_products_add_compare_button( $purchase_form, $args ) {

	if ( edd_compare_products_get_compare_url() == false || is_single() ) {
		return $purchase_form;
	}

	global $edd_options;
	$page = $edd_options['edd-compare-products-page'];
	if ( is_page( $page ) ) {
		return $purchase_form;
	}

	$text = ( array_key_exists( 'edd-compare-products-button-text', $edd_options ) && ! empty( $edd_options['edd-compare-products-button-text'] ) ) ? $edd_options['edd-compare-products-button-text'] : 'Compare';
	$go_text = ( array_key_exists( 'edd-compare-products-go-button-text', $edd_options ) && ! empty( $edd_options['edd-compare-products-go-button-text'] ) ) ? $edd_options['edd-compare-products-go-button-text'] : 'Go to Comparison';

	$class  = implode( ' ', array( $args['style'], $args['color'], trim( $args['class'] ) ) );
	$button = '<button class="edd-submit button edd-compare-button ' . esc_attr( $class ) . '" id="edd-compare-button-' . $args["download_id"] . '" onclick="eddCompareURL(' . $args["download_id"] . ')">' . esc_attr( $text ) . '</button>';
	$button .= '<a class="edd-submit edd-compare-go button edd-compare-button ' . esc_attr( $class ) . '" href="" id="edd-compare-go-button-' . $args["download_id"] . '" style="display:none">' . esc_attr( $go_text ) . '</a>';

	return $purchase_form . $button;
}

/**
 * Get the URL of the default comparison page defined in settings
 *
 * @return bool|string
 * @since 0.1
 */
function edd_compare_products_get_compare_url() {
	global $edd_options;
	if ( isset( $edd_options['edd-compare-products-page'] ) ) {
		$url = get_permalink( $edd_options['edd-compare-products-page'] );
	} else {
		$url = false;
	}
	return $url;
}

/**
 * Creates the URL which customers will go to after selecting downloads to compare
 *
 * @since 0.1
 */
function edd_compare_products_url() {
    
    global $edd_options;

	if ( isset( $edd_options['edd-compare-products-page'] ) ) {
		$compare_page = $edd_options['edd-compare-products-page'];
	} else {
		return;
	}
    
    if ( is_page( $compare_page ) || ( get_post_type() == 'download' && ! is_single() ) ) {
        $link = edd_compare_products_get_compare_url();
        $arg = ( strpos( $link, '?' ) ) ? '&' : '?';
        echo '<div id="edd-compare-url" style="display: none">' . $link . $arg . 'compare=</div>';
    }
    
}