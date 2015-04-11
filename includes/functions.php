<?php
/**
 * Created by PhpStorm.
 * User: kylemaurer
 * Date: 4/8/15
 * Time: 6:20 PM
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

function edd_compare_get_default_downloads() {
	$defaults = get_option( 'edd-compare-products-default-ids' );

	if ( !empty($defaults) ) {
		return explode( ',', $defaults );
	} else {
		return null;
	}
}