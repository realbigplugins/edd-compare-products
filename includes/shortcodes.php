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
		if ( $download_ids ) :
            
            // We're going to build DOM in an Object Buffer, which helps keep things more readable.
            ob_start(); 
        
            $fields = edd_compare_get_meta_fields();

            ?>

            <section class="edd-compare-products <?php echo $edd_options["edd-compare-products-default-style"]; ?>">
                
                <div class="edd-compare-products-header">
                    <h2>Compare</h2>
                    <div class="actions">
                        <a href="#" class="reset">Reset</a>
                        <a href="#" class="filter">Filter</a>
                    </div>
                </div>
                
                <div class="edd-compare-products-table">
                    
                    <div class="features">
                        
                    <?php if ( $fields ) :
                        
                        $list_labels = '';
        
                        // To check if we've added the Thumbnail Row at the top
                        $thumbnail_title = false;

                        foreach( $fields as $field ) : 
                            
                            if ( $field['meta_field'] == 'thumbnail' ) : ?>
        
                            <div class="top-info">

                                <?php echo ( $field['label'] ) ? $field['label'] : $field['meta_field']; ?>

                            </div>
                                
                            <?php 
        
                            $thumbnail_title = true;
        
                            endif;
        
                            if ( $field['meta_field'] == 'thumbnail' ) continue;
        
                            $list_labels .= '<li>' . ( ( $field['label'] ) ? $field['label'] : $field['meta_field'] ) . '</li>';
                            
                        endforeach;
                        
                        if ( ! $thumbnail_title ) : ?>
        
                            <div class="top-info">

                                Title

                            </div>
        
                        <?php endif; ?>
                        
                        <ul class="edd-compare-products-features-list-labels">
                            
                            <?php echo $list_labels; ?>
                            
                        </ul>
                        
                    <?php endif; ?>
                        
                    </div> <!-- end .features -->

                    <div class="edd-compare-products-wrapper">
                        
                <?php // Products
                if ( $fields ) : ?>
                        
                        <ul class="edd-compare-products-columns">

                    <?php // Meta fields
                    foreach ( $download_ids as $download_id ) : 
                            
                        $download = edd_get_download( $download_id );
                        $features = '';
        
                        if ( is_object( $download ) ) : ?>
        
                            <li class="product">
                                
                                <?php if ( ! $thumbnail_title ) : ?>
                                
                                    <div class="top-info">
                                    
                                        <div class="check"></div>

                                        <?php if ( has_post_thumbnail( $download_id ) ) : ?>
                                            <a href="<?php echo get_permalink( $download_id ); ?>" title="<?php echo get_the_title( $download_id ); ?>">
                                                <?php echo get_the_post_thumbnail( $download_id, array( 75, 75 ) ); ?>
                                            </a>
                                        <?php endif; ?>

                                        <h3><a href="<?php echo get_permalink( $download_id ); ?>" title="<?php echo get_the_title( $download_id ); ?>"><?php echo get_the_title( $download_id ); ?></a></h3>

                                    </div>
                                
                                <?php endif; ?>

                        <?php foreach ( $fields as $field ) :

                                if ( $field['meta_field'] == 'thumbnail' ) : ?>

                                <div class="top-info">
                                    
                                    <div class="check"></div>
                                    
                                    <?php if ( has_post_thumbnail( $download_id ) ) : ?>
                                        <a href="<?php echo get_permalink( $download_id ); ?>" title="<?php echo get_the_title( $download_id ); ?>">
                                            <?php echo get_the_post_thumbnail( $download_id, array( 75, 75 ) ); ?>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <h3><a href="<?php echo get_permalink( $download_id ); ?>" title="<?php echo get_the_title( $download_id ); ?>"><?php echo get_the_title( $download_id ); ?></a></h3>
                                    
                                </div>

                                <?php elseif ( $field['meta_field'] == 'edd_price' ||
                                           $field['meta_field'] == '_edd_download_earnings' ||
                                           $field['meta_field'] == '_edd_download_sales' ) :
        
                                    $features .= '<li>' . edd_currency_filter( get_post_meta( $download_id, $field['meta_field'], true ) ) . '</li>';

                                else :
        
                                    $features .= '<li>' . get_post_meta( $download_id, $field['meta_field'], true ) . '</li>';

                                endif; ?>

                        <?php endforeach; // Each Field
        
                        // Buy Button
                        $features .= '<li class="buy-button">' . edd_get_purchase_link( array( 'download_id' => $download_id ) ) . '</li>' ?>
                                
                                <ul class="edd-compare-products-features-list">
                                    <?php echo $features; ?>
                                </ul>
                                
                            </li>

                        <?php endif; // If is_object()

                    endforeach; // echo Field for each download_id ?>
                            
                        </ul>
                        
                <?php endif; // If Fields ?>
                        
                    </div> <!-- end .edd-compare-products-wrapper -->
                    
                    <ul class="edd-compare-products-navigation">
                        <li>
                            <a href="#" class="prev inactive">Prev</a>
                        </li>
                        <li>
                            <a href="#" class="next">Next</a>
                        </li>
                    </ul>
                    
                </div> <!-- end .edd-compare-products-table -->
            </section> <!-- end .edd-compare-products -->

            <?php 
            
            // Everything gets assigned to $output at once. Much cleaner.
            $output = ob_get_contents();
            ob_end_clean();
                                    
        else :
            $output = __( 'The ' . edd_get_label_singular() . ' IDs provided are invalid. Please select some ' . edd_get_label_plural() . ' to compare.', EDD_Compare_Products_ID );
        endif; // If Download IDS
                                    
	}  // If $atts['ids']
    else {
		$output = __( 'There\'s currently nothing to compare. Please select some ' . edd_get_label_plural() . ' to compare.', EDD_Compare_Products_ID );
	}

	return $output;
}

add_shortcode( 'edd_compare_products', 'edd_compare_products_shortcode' );