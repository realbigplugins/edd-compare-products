<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * When applicable, add the compare button after the EDD purchase form button
 * This only applies to the default Archive
 *
 * @param $purchase_form
 * @param $args
 *
 * @return string
 */
function edd_compare_products_add_compare_button_archive( $purchase_form, $args ) {

	if ( edd_compare_products_get_compare_url() === false || ! is_archive() ) {
		return $purchase_form;
	}

	global $edd_options;
	$page = $edd_options['edd-compare-products-page'];
	if ( is_page( $page ) ) {
		return $purchase_form;
	}
    
    $args = apply_filters( 'edd_compare_products_purchase_link_args', $args );

	$text = ( array_key_exists( 'edd-compare-products-button-text', $edd_options ) && ! empty( $edd_options['edd-compare-products-button-text'] ) ) ? $edd_options['edd-compare-products-button-text'] : 'Compare';
	$go_text = ( array_key_exists( 'edd-compare-products-go-button-text', $edd_options ) && ! empty( $edd_options['edd-compare-products-go-button-text'] ) ) ? $edd_options['edd-compare-products-go-button-text'] : 'Go to Comparison';

	$class  = implode( ' ', array( $args['style'], $args['color'], trim( $args['class'] ) ) );
	$button = '<button class="edd-submit button edd-compare-button ' . esc_attr( $class ) . '" id="edd-compare-button-' . $args["download_id"] . '" onclick="eddCompareURL(' . $args["download_id"] . ')">' . esc_attr( $text ) . '</button>';
	$button .= '<a class="edd-submit edd-compare-go button edd-compare-button ' . esc_attr( $class ) . '" href="" id="edd-compare-go-button-' . $args["download_id"] . '" style="display:none">' . esc_attr( $go_text ) . '</a>';

	return $purchase_form . $button;
}

/**
 * This adds the Compare Button to the [downloads] shortcode, which can be put into any Content Type
 */
function edd_compare_products_add_compare_button_downloads_shortcode() {
    
    global $edd_options;
    global $post;
    
    if ( edd_compare_products_get_compare_url() === false ) return false;
    
    $text = ( array_key_exists( 'edd-compare-products-button-text', $edd_options ) && ! empty( $edd_options['edd-compare-products-button-text'] ) ) ? $edd_options['edd-compare-products-button-text'] : 'Compare';
    
    $go_text = ( array_key_exists( 'edd-compare-products-go-button-text', $edd_options ) && ! empty( $edd_options['edd-compare-products-go-button-text'] ) ) ? $edd_options['edd-compare-products-go-button-text'] : 'Go to Comparison';
    
    // Grabbed from edd_get_purchase_link() to replicate $class for the Archive version
    // It isn't perfect, but it calls the same Filters and our own Filter is added at the end to allow corrections
    $args = apply_filters( 'edd_purchase_link_defaults', array(
		'style'       => edd_get_option( 'button_style', 'button' ),
		'color'       => edd_get_option( 'checkout_color', 'blue' ),
		'class'       => 'edd-submit'
	) );
    
    // Override color if color == inherit
	$args['color'] = ( $args['color'] == 'inherit' ) ? '' : $args['color'];
    
    $args = apply_filters( 'edd_purchase_link_args', $args );
    
    $args = apply_filters( 'edd_compare_products_purchase_link_args', $args );
    
    $class  = implode( ' ', array( $args['style'], $args['color'], trim( $args['class'] ) ) );
    
    ?>

    <button class="edd-submit button edd-compare-button <?php echo esc_attr( $class ); ?>" id="edd-compare-button-<?php echo $post->ID; ?>" onclick="eddCompareURL( <?php echo $post->ID; ?> )">
        <?php echo esc_attr( $text ); ?>
    </button>

    <a class="edd-submit edd-compare-go button edd-compare-button <?php echo esc_attr( $class ); ?>" href="" id="edd-compare-go-button-<?php echo $post->ID; ?>" style="display:none">
        <?php echo esc_attr( $go_text ); ?>
    </a>

    <?php
    
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
    
    if ( is_page( $compare_page ) || ( ! is_single() ) ) {
        $link = edd_compare_products_get_compare_url();
        $arg = ( strpos( $link, '?' ) ) ? '&' : '?';
        echo '<div id="edd-compare-url" style="display: none">' . $link . $arg . 'compare=</div>';
    }
    
}