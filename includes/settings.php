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
			<th scope="col" class="edd_compare_fields"><?php _e( 'Fields', 'edd-compare-products' ); ?></th>
			<th scope="col" class="edd_compare_field_label"><?php _e( 'Label', 'edd-compare-products' ); ?></th>
			<th scope="col"><?php _e( 'Remove', 'edd-compare-products' ); ?></th>
		</tr>
		</thead>
		<?php if( ! empty( $fields ) ) : ?>
			<?php foreach( $fields as $key => $field ) : ?>
				<tr>
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
							'placeholder' => __( 'Choose a meta_field', 'edd-compare-products' )
						) );
						?>
					</td>
					<td class="edd_compare_meta_field_label">
						<input type="text" name="meta_fields[<?php echo $key; ?>][label]" value="<?php echo $field['label']; ?>"/>
					</td>
					<td>
						<span class="edd_remove_compare_field button-secondary"><?php _e( 'Remove Field', 'edd-compare-products' ); ?></span>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php else : ?>
			<tr>
				<td class="edd_compare_fields">
					<?php
					echo EDD()->html->select( array(
						'options'          => edd_compare_products_get_meta_fields(),
						'name'             => 'meta_fields[0][meta_field]',
						'show_option_all'  => false,
						'show_option_none' => false,
						'class'            => 'edd-select edd-compare-meta_field',
						'chosen'           => false,
						'placeholder' => __( 'Choose a meta_field', 'edd-compare-products' )
					) ); ?>
				</td>
				<td class="edd_compare_field_label">
					<?php echo EDD()->html->text( array(
						'name'             => 'meta_fields[0][label]'
					) ); ?>
				</td>
				<td><span class="edd_remove_compare_field button-secondary"><?php _e( 'Remove Field', 'edd-compare-products' ); ?></span></td>
			</tr>
		<?php endif; ?>
	</table>
	<p>
		<span class="button-secondary" id="edd_add_compare_field"><?php _e( 'Add Field', 'edd-compare-products' ); ?></span>
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
	$remove = array(
		'_edit_lock',
		'_edit_last',
		'_thumbnail_id',
		'_variable_pricing',
		'_edd_default_price_id',
	);
	foreach ( $fields as $field ) {
		$type = get_post_meta( $download[0]->ID, $field, true );
		if ( in_array( $field, $remove ) || ! is_string( $type ) ) {
			unset( $fields[$field] );
		}
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
	$query = "
        SELECT DISTINCT($wpdb->postmeta.meta_key)
        FROM $wpdb->posts
        LEFT JOIN $wpdb->postmeta
        ON $wpdb->posts.ID = $wpdb->postmeta.post_id
        WHERE $wpdb->posts.post_type = '%s'
        AND $wpdb->postmeta.meta_key != ''
        AND $wpdb->postmeta.meta_key NOT RegExp '(^[0-9].+$)'
        AND $wpdb->postmeta.meta_key NOT RegExp '(^[0-9]+$)'
    ";
	$meta_keys = $wpdb->get_col($wpdb->prepare($query, $post_type));

	$data = array();
	foreach ( $meta_keys as $value ) {
			$data[$value] = $value;
	}

	$data['thumbnail'] = 'thumbnail';

	return edd_compare_remove_some_meta_fields( $data );
}

/**
 * Sanitize meta fields
 *
 * @param $input
 * @since 0.1
 * @return mixed
 */
function edd_compare_settings_sanitize_meta_fields( $input ) {

	if( ! current_user_can( 'manage_shop_settings' ) ) {
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
			'name' => '<strong>' . __( 'Compare Products Settings', 'edd-compare-products' ) . '</strong>',
			'desc' => __( 'Configure EDD Compare Products Settings', 'edd-compare-products' ),
			'type' => 'header',
		),
		array(
			'id' 		=> 'edd-compare-products-page',
			'name' 		=> __('Default Comparison Page', 'edd-compare-products'),
			'desc' 		=> __('Which Page contains the [edd_compare_products] shortcode?', 'edd-compare-products'),
			'type' 		=> 'select',
			'options' 	=> edd_get_pages(),
		),
		array(
			'id' 		=> 'edd-compare-products-default-ids',
			'name' 		=> __('Default Downloads', 'edd-compare-products'),
			'desc' 		=> __('Comma separated list of download IDs.', 'edd-compare-products'),
			'type' 		=> 'text',
		),
		array(
			'id' 		=> 'edd-compare-products-default-style',
			'name' 		=> __('Default Table Style', 'edd-compare-products'),
			'type' 		=> 'select',
			'options' => array(
				'edd-compare-h-scroll' => 'Horizontal Scroll',
			),
		),
		array(
			'id' => 'edd_compare_products_meta_fields',
			'name' => '<strong>' . __( 'Meta fields', 'edd-compare-products' ) . '</strong>',
			'desc' => __( 'Define default meta fields to show in comparison table.', 'edd-compare-products' ),
			'type' => 'compare_products_meta_fields'
		),
		array(
			'id' 		=> 'edd-compare-products-button-text',
			'name' 		=> __('Compare Button Text', 'edd-compare-products'),
			'desc' 		=> __('Default is "Compare".', 'edd-compare-products'),
			'type' 		=> 'text',
		),
		array(
			'id' 		=> 'edd-compare-products-go-button-text',
			'name' 		=> __('Go Button Text', 'edd-compare-products'),
			'desc' 		=> __('After someone has selected the items they would like to compare, the buttons display a message. The default is "Go to Comparison".', 'edd-compare-products'),
			'type' 		=> 'text',
		),
	);

	return array_merge( $settings, $new_settings );
}