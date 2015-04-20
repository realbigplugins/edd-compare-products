<?php
/**
 * Created by PhpStorm.
 * User: kylemaurer
 * Date: 4/12/15
 * Time: 3:52 PM
 */

function edd_compare_products_meta_fields_callback( $args ) {
	global $edd_options;
	$fields = edd_compare_get_meta_fields();
	ob_start(); ?>
	<p><?php echo $args['desc']; ?></p>
	<table id="edd_tax_rates" class="wp-list-table widefat fixed posts">
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
						<span class="edd_remove_tax_rate button-secondary"><?php _e( 'Remove Field', 'edd-compare-products' ); ?></span>
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
				<td><span class="edd_remove_tax_rate button-secondary"><?php _e( 'Remove Field', 'edd-compare-products' ); ?></span></td>
			</tr>
		<?php endif; ?>
	</table>
	<p>
		<span class="button-secondary" id="edd_add_tax_rate"><?php _e( 'Add Field', 'edd-compare-products' ); ?></span>
	</p>
	<?php
	echo ob_get_clean();
}

function edd_compare_products_get_meta_fields() {
	$download = get_posts( 'post_type=download&posts_per_page=1' );
	$fields = get_post_custom_keys( $download[0]->ID );
	$data = array();
	foreach ( $fields as $value ) {
		$type = get_post_meta( $download[0]->ID, $value, true );
		if ( is_string( $type ) ) {
			$data[$value] = $value;
		}
	}
	$data['thumbnail'] = 'thumbnail';
	return $data;
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
	);

	return array_merge( $settings, $new_settings );
}