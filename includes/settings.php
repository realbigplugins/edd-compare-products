<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Display extension's settings
 *
 * @param $args
 */
function edd_compare_products_meta_fields_callback( $args ) {
	global $edd_options;
	$fields = edd_compare_get_meta_fields();
	ob_start(); ?>
	<p><?php echo $args['desc']; ?></p>
	<table id="edd_compare_fields" class="wp-list-table widefat fixed posts">
		<thead>
		<tr>
            <th scope="col" class="edd_compare_field_handle"></th>
			<th scope="col" class="edd_compare_fields"><?php _e( 'Fields', EDD_Compare_Products_ID ); ?></th>
			<th scope="col" class="edd_compare_field_label"><?php _e( 'Label', EDD_Compare_Products_ID ); ?></th>
			<th scope="col"><?php _e( 'Remove', EDD_Compare_Products_ID ); ?></th>
		</tr>
		</thead>
		<?php if ( ! empty( $fields ) ) : ?>
			<?php foreach ( $fields as $key => $field ) : ?>
				<tr>
                    <td><span class="handle dashicons dashicons-sort"></span></td>
					<td class="edd_compare_fields">
						<?php
						echo EDD()->html->select( array(
							'options'          => edd_compare_products_get_meta_fields(),
							'name'             => 'meta_fields[' . $key . '][meta_field]',
							'selected'         => $field['meta_field'],
							'show_option_all'  => false,
							'show_option_none' => false,
							'class'            => 'edd-select edd-compare-meta_field',
							'chosen'           => false,
							'placeholder'      => __( 'Choose a meta_field', EDD_Compare_Products_ID )
						) );
						?>
					</td>
					<td class="edd_compare_meta_field_label">
						<input type="text" name="meta_fields[<?php echo $key; ?>][label]"
						       value="<?php echo $field['label']; ?>"/>
					</td>
					<td>
						<span
							class="edd_remove_compare_field button-secondary"><?php _e( 'Remove Field', EDD_Compare_Products_ID ); ?></span>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php else : ?>
			<tr>
                <td><span class="handle dashicons dashicons-sort"></span></td>
				<td class="edd_compare_fields">
					<?php
					echo EDD()->html->select( array(
						'options'          => edd_compare_products_get_meta_fields(),
						'name'             => 'meta_fields[0][meta_field]',
						'show_option_all'  => false,
						'show_option_none' => false,
						'class'            => 'edd-select edd-compare-meta_field',
						'chosen'           => false,
						'placeholder'      => __( 'Choose a meta_field', EDD_Compare_Products_ID )
					) ); ?>
				</td>
				<td class="edd_compare_field_label">
					<?php echo EDD()->html->text( array(
						'name' => 'meta_fields[0][label]'
					) ); ?>
				</td>
				<td><span
						class="edd_remove_compare_field button-secondary"><?php _e( 'Remove Field', EDD_Compare_Products_ID ); ?></span>
				</td>
			</tr>
		<?php endif; ?>
	</table>
	<p>
		<span class="button-secondary"
		      id="edd_add_compare_field"><?php _e( 'Add Field', EDD_Compare_Products_ID ); ?></span>
	</p>
	<?php
	echo ob_get_clean();
}

/**
 * Remove unnecessary default meta fields and any that store non-strings
 *
 * @param $fields
 *
 * @return mixed
 */
function edd_compare_remove_some_meta_fields( $fields ) {
	
	$download = get_posts( 'post_type=download&posts_per_page=1' );
	
	/**
	 * Allows additional Meta Keys to be removed as needed
	 *
	 * @since 1.1.2
	 */
	$remove   = apply_filters( 'edd_compare_products_exclude_meta', array(
		'_edit_lock',
		'_edit_last',
		'_thumbnail_id',
		'_variable_pricing',
		'_edd_default_price_id',
	) );
	
	if ( ! empty( $download ) ) {
		foreach ( $fields as $field ) {
			$type = get_post_meta( $download[0]->ID, $field, true );
			// If any of those goofy keys above exist or any new ones that don't return strings, ditch them!
			if ( in_array( $field, $remove ) || ! is_string( $type ) ) {
				unset( $fields[ $field ] );
			}
		}
	} else {
		// No downloads published so....
		$fields = array( '0' => __( '-- No fields exist --', EDD_Compare_Products_ID ) );
	}

	return $fields;
}

/**
 * Returns array of all the meta fields we want
 *
 * @return mixed
 */
function edd_compare_products_get_meta_fields() {
	
	/**
	 * Initial Meta Key/Value pairs. This allows the Value to be something human readable
	 *
	 * @since 1.1.2
	 */
	$fields = apply_filters( 'edd_compare_products_meta', array() );

	global $wpdb;
	$post_type = 'download';
	$query     = "
        SELECT DISTINCT($wpdb->postmeta.meta_key)
        FROM $wpdb->posts
        LEFT JOIN $wpdb->postmeta
        ON $wpdb->posts.ID = $wpdb->postmeta.post_id
        WHERE $wpdb->posts.post_type = '%s'
        AND $wpdb->postmeta.meta_key != ''
        AND $wpdb->postmeta.meta_key NOT RegExp '(^[0-9].+$)'
        AND $wpdb->postmeta.meta_key NOT RegExp '(^[0-9]+$)'
    ";
	$meta_keys = $wpdb->get_col( $wpdb->prepare( $query, $post_type ) );

	foreach ( $meta_keys as $value ) {
		
		// Only add found Keys not provided by our Filter
		if ( ! isset( $fields[ $value ] ) ) {
			$fields[ $value ] = $value;
		}
		
	}
	
	$fields = edd_compare_remove_some_meta_fields( $fields );
	
	/**
	 * Allows additional Meta Keys to be added as needed, regardless of if there's a saved value or not
	 *
	 * @since 1.1.2
	 */
	$fields = apply_filters( 'edd_compare_products_meta_after', $fields );
	
	asort( $fields );

	return $fields;
	
}

/**
 * Sanitize meta fields
 *
 * @param $input
 *
 * @since 0.1
 * @return mixed
 */
function edd_compare_settings_sanitize_meta_fields( $input ) {

	if ( ! current_user_can( 'manage_shop_settings' ) ) {
		return $input;
	}

	$new_fields = ! empty( $_POST['meta_fields'] ) ? array_values( $_POST['meta_fields'] ) : array();

	update_option( 'edd_compare_products_meta_fields', $new_fields );

	return $input;
}

function edd_compare_get_meta_fields() {
	$fields = get_option( 'edd_compare_products_meta_fields', array() );

	return apply_filters( 'edd_compare_get_meta_fields', $fields );
}

/**
 * Add settings section
 *
 * @access      public
 * @since       1.1.0
 *
 * @param       array $settings The existing EDD settings sections array
 *
 * @return      array The modified EDD settings sections array
 */
function edd_compare_products_settings_section( $sections ) {
    $sections['edd-compare-products-settings'] = __( 'Compare Products', EDD_Compare_Products_ID );
    return $sections;
}

/**
 * Add settings
 *
 * @access      public
 * @since       0.1
 *
 * @param       array $settings The existing EDD settings array
 *
 * @return      array The modified EDD settings array
 */
function edd_compare_products_settings( $settings ) {
	$new_settings = array(
		array(
			'id'   => 'edd_compare_products_settings',
			'name' => '<strong>' . __( 'Compare Products Settings', EDD_Compare_Products_ID ) . '</strong>',
			'desc' => '<p class="description">' . __( 'Configure EDD Compare Products Settings', EDD_Compare_Products_ID ) . '</p>',
			'type' => 'header',
		),
		array(
			'id'      => 'edd-compare-products-page',
			'name'    => __( 'Default Comparison Page', EDD_Compare_Products_ID ),
			'desc'    => '<p class="description">' . __( 'Which Page contains the [edd_compare_products] shortcode?', EDD_Compare_Products_ID ) . '</p>',
			'type'    => 'select',
			'options' => edd_get_pages(),
		),
		array(
			'id'   => 'edd-compare-products-default-ids',
			'name' => __( 'Default Downloads', EDD_Compare_Products_ID ),
			'desc' => '<p class="description">' . __( 'Comma separated list of download IDs.', EDD_Compare_Products_ID ) . '</p>',
			'type' => 'text',
		),
		array(
			'id'      => 'edd-compare-products-default-style',
			'name'    => __( 'Default Table Style', EDD_Compare_Products_ID ),
			'type'    => 'select',
			'options' => array(
				'edd-compare-h-scroll' => __( 'Horizontal Scroll', EDD_Compare_Products_ID ),
			),
		),
		array(
			'id'   => 'edd_compare_products_meta_fields',
			'name' => '<strong>' . __( 'Meta fields', EDD_Compare_Products_ID ) . '</strong>',
			'desc' => '<p class="description">' . __( 'Define default meta fields to show in comparison table.', EDD_Compare_Products_ID ) . '</p>',
			'type' => 'compare_products_meta_fields'
		),
		array(
			'id'   => 'edd-compare-products-button-text',
			'name' => __( 'Compare Button Text', EDD_Compare_Products_ID ),
			'desc' => '<p class="description">' . __( 'Default is "Compare".', EDD_Compare_Products_ID ) . '</p>',
			'type' => 'text',
		),
		array(
			'id'   => 'edd-compare-products-go-button-text',
			'name' => __( 'Go Button Text', EDD_Compare_Products_ID ),
			'desc' => '<p class="description">' . __( 'After someone has selected the items they would like to compare, the buttons display a message. The default is "Go to Comparison".', EDD_Compare_Products_ID ) . '</p>',
			'type' => 'text',
		),
	);
    
    // If EDD is at version 2.5 or later...
    if ( version_compare( EDD_VERSION, 2.5, '>=' ) ) {
        // Place the Settings in our Settings Section
        $new_settings = array( 'edd-compare-products-settings' => $new_settings );
    }

	return array_merge( $settings, $new_settings );
}

/**
 * Localize some expected Keys so that they have a Value that is Human Readable
 * 
 * @param		array $fields Meta Keys and an Identifying Value
 *                                                    
 * @since		1.1.2
 * @return		array Meta Keys with Localized Values
 */
add_filter( 'edd_compare_products_meta', 'edd_compare_products_localize_meta' );
function edd_compare_products_localize_meta( $fields ) {
	
	$fields['_edd_button_behavior'] = __( 'EDD Button Behavior', EDD_Compare_Products_ID );
	$fields['_edd_commisions_enabled'] = __( 'EDD Commissions Enabled', EDD_Compare_Products_ID );
	$fields['_edd_download_earnings'] = sprintf( __( '%s Earnings', EDD_Compare_Products_ID ), edd_get_label_singular() );
	$fields['_edd_download_limit'] = sprintf( __( '% Limit', EDD_Compare_Products_ID ), edd_get_label_singular() );
	$fields['_edd_download_sales'] = sprintf( __( '%s Sales', EDD_Compare_Products_ID ), edd_get_label_singular() );
	$fields['_edd_download_use_git'] = sprintf( __( '%s Grabs File from Git', EDD_Compare_Products_ID ), edd_get_label_singular() );
	$fields['_edd_external_product_button'] = __( 'External Button Label', EDD_Compare_Products_ID );
	$fields['_edd_external_product_url'] = __( 'External Button URL', EDD_Compare_Products_ID );
	$fields['_edd_has_commission'] = __( 'Commission has been Awarded', EDD_Compare_Products_ID );
	$fields['_edd_mailchimp'] = sprintf( __( 'MailChimp List ID Buyers of a %s are Subscribed to', EDD_Compare_Products_ID ), edd_get_label_singular() );
	$fields['_edd_readme_location'] = __( 'Readme.txt Location', EDD_Compare_Products_ID );
	$fields['_edd_readme_meta'] = __( 'Readme Meta', EDD_Compare_Products_ID );
	$fields['_edd_readme_plugin_added'] = __( 'Readme Plugin Added Same as Publish Date', EDD_Compare_Products_ID );
	$fields['_edd_readme_plugin_homepage'] = __( 'Readme Plugin Homepage', EDD_Compare_Products_ID );
	$fields['_edd_readme_plugin_last_updated'] = __( 'Readme Plugin Updated Same as Last Updated Date', EDD_Compare_Products_ID );
	$fields['_edd_readme_sections'] = __( 'Readme Sections', EDD_Compare_Products_ID );
	$fields['_edd_sl_changelog'] = __( 'Changelog', EDD_Compare_Products_ID );
	$fields['_edd_sl_enabled'] = __( 'EDD Software Licensing Enabled', EDD_Compare_Products_ID );
	$fields['_edd_sl_exp_length'] = __( 'License Length', EDD_Compare_Products_ID );
	$fields['_edd_sl_exp_unit'] = __( 'License Length Unit', EDD_Compare_Products_ID );
	$fields['_edd_sl_keys'] = __( 'License Keys', EDD_Compare_Products_ID );
	$fields['_edd_sl_limit'] = __( 'License Activation Limit', EDD_Compare_Products_ID );
	$fields['_edd_sl_upgrade_file_key'] = __( 'Upgrade File Key', EDD_Compare_Products_ID );
	$fields['_edd_sl_version'] = __( 'Software Version', EDD_Compare_Products_ID );
	$fields['edd_period'] = __( 'Recurring Period', EDD_Compare_Products_ID );
	$fields['edd_price'] = __( 'Price', EDD_Compare_Products_ID );
	$fields['edd_product_notes'] = sprintf( __( '%s Notes', EDD_Compare_Products_ID ), edd_get_label_singular() );
	$fields['edd_recurring'] = __( 'Recurring', EDD_Compare_Products_ID );
	$fields['edd_sl_download_lifetime'] = __( 'Lifetime', EDD_Compare_Products_ID );
	$fields['thumbnail'] = __( 'Thumbnail', EDD_Compare_Products_ID );
	
	return $fields;
	
}
