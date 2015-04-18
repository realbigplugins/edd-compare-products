<?php
/**
 * Created by PhpStorm.
 * User: kylemaurer
 * Date: 4/8/15
 * Time: 6:20 PM
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

function edd_compare_products_add_compare_button( $purchase_form, $args ) {

	$class = implode( ' ', array( $args['style'], $args['color'], trim( $args['class'] ) ) );
	$button = '<button class="edd-submit button ' . esc_attr( $class ) . '" onclick="eddCompareURL(' . $args["download_id"] . ')">Compare</button>';
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