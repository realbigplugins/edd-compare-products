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
            ob_start(); ?>

			<div class="edd-compare-products <?php echo $edd_options["edd-compare-products-default-style"]; ?>">
                <table>
			         <thead>
                         <tr>
                             <th>Title</th>
                    
                    <?php foreach ( $download_ids as $download_id ) : 
                        $download = edd_get_download( $download_id );
                        if ( is_object( $download ) ) : ?>
                            <th>
                                <a href="<?php the_permalink( $download->ID ); ?>"><?php echo $download->post_title; ?></a>
                            </th>
                        <?php endif; 
                    endforeach; ?>
                         
                         </tr>
                    </thead>

                    <tbody>
                        
                <?php // Rows
                $fields = edd_compare_get_meta_fields();
                if ( $fields ) :

                    // Meta fields
                    foreach ( $fields as $field ) : ?>
                        <tr>
                            
                            <td>
                                <?php echo ( $field['label'] ) ? $field['label'] : $field['meta_field']; ?>
                            </td>
                            
                        <?php foreach ( $download_ids as $download_id ) :
            
                            $download = edd_get_download( $download_id );
            
                            if ( is_object( $download ) ) :
                                if ( $field['meta_field'] == 'thumbnail' ) :
                                    $value = get_the_post_thumbnail( $download_id, 'thumbnail' );
                                elseif ( $field['meta_field'] == 'edd_price' ||
                                           $field['meta_field'] == '_edd_download_earnings' ||
                                           $field['meta_field'] == '_edd_download_sales' ) :
                                    $value = edd_currency_filter( get_post_meta( $download_id, $field['meta_field'], true ) ); 
                                else :
                                    $value = get_post_meta( $download_id, $field['meta_field'], true );
                                endif; ?>
            
                            <td>
                                <?php echo $value; ?>
                            </td>
            
                            <?php endif; // If is_object()
                        endforeach; // echo Field for each download_id ?>
                            
                        </tr>
                        
				    <?php endforeach; // Each Field ?>
                        
                        <tr>
                            <td> </td> <?php // Empty cell to offset past labels
                    
                    foreach ( $download_ids as $download_id ) : 
                        $download = edd_get_download( $download_id );
                        if ( is_object( $download ) ) : ?>
                            <td>
                                <?php echo edd_get_purchase_link( array( 'download_id' => $download_id ) ); ?>
                            </td>
                        <?php endif;
                    endforeach // Each download_id ?>
                            
                        </tr>
                        
                <?php endif; // If Field ?>
                        
                    </tbody>
                </table>
            </div> <!-- end .edd-compare-products -->

		<?php 
            
            // Everything gets assigned to $output at once. Much cleaner.
            $output = ob_get_contents();
            ob_end_clean();
                                    
        else :
			$output = __( 'The ' . edd_get_label_singular() . ' IDs provided are invalid. Please select some ' . edd_get_label_plural() . ' to compare.', 'edd-compare-products' );
		endif; // If Download IDS
                                    
	}  // If $atts['ids']
    else {
		$output = __( 'There\'s currently nothing to compare. Please select some ' . edd_get_label_plural() . ' to compare.', 'edd-compare-products' );
	}

	return $output;
}

add_shortcode( 'edd_compare_products', 'edd_compare_products_shortcode' );