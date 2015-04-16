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
	$button = '<a href="#" class="edd-submit button ' . esc_attr( $class ) . '">Compare</a>';
	return $purchase_form . $button;
}