<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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

function edd_compare_products_get_compare_url() {
	global $edd_options;
	if ( isset( $edd_options['edd-compare-products-page'] ) ) {
		$url = get_permalink( $edd_options['edd-compare-products-page'] );
	} else {
		$url = false;
	}
	return $url;
}

function edd_compare_products_url() {
	echo '<div id="edd-compare-url">' . edd_compare_products_get_compare_url() . '?compare=</div>';
}