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
	$remove   = array(
		'_edit_lock',
		'_edit_last',
		'_thumbnail_id',
		'_variable_pricing',
		'_edd_default_price_id',
	);
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

	$fields = array();
	foreach ( $meta_keys as $value ) {
		$fields[ $value ] = $value;
	}

	$fields['thumbnail'] = 'thumbnail';

	return edd_compare_remove_some_meta_fields( $fields );
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