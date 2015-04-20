<?php
/**
 * Created by PhpStorm.
 * User: kylemaurer
 * Date: 4/8/15
 * Time: 6:20 PM
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function edd_compare_products_add_compare_button( $purchase_form, $args ) {

	global $edd_options;
	$page = $edd_options['edd-compare-products-page'];
	if ( ! $page ) {
		return $purchase_form;
	}

	$class  = implode( ' ', array( $args['style'], $args['color'], trim( $args['class'] ) ) );
	$button = '<button class="edd-submit button ' . esc_attr( $class ) . '" id="edd-compare-button-' . $args["download_id"] . '" onclick="eddCompareURL(' . $args["download_id"] . ')">Compare</button>';
	$button = $button . '<a class="edd-submit edd-compare-go button ' . esc_attr( $class ) . '" href="" id="edd-compare-go-button-' . $args["download_id"] . '" style="display:none">Go to Comparison</a>';

	return $purchase_form . $button;
}

function edd_compare_products_get_compare_url() {
	global $edd_options;
	$url = get_permalink( $edd_options['edd-compare-products-page'] );

	return $url;
}

function edd_compare_products_url() {
	echo '<div id="edd-compare-url">' . edd_compare_products_get_compare_url() . '?compare=</div>';
}