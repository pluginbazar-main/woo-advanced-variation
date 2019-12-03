<?php
/*
* @Author 		Pluginbazar
* Copyright: 	2015 Pluginbazar
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}  // if direct access

$pb_settings            = new PB_Settings();
$product_id             = empty( $post_id = get_the_ID() ) ? absint( wp_unslash( $_POST['post_id'] ) ) : $post_id;
$attribute_taxonomy     = $attribute->get_taxonomy_object();
$vswoo_product_attributes = get_post_meta( $product_id, 'vswoo_product_attributes', true );
$vswoo_product_attributes = empty( $vswoo_product_attributes ) ? array() : $vswoo_product_attributes;
$_attribute_name        = sanitize_title( $attribute->get_name() );
$attribute_type         = isset( $vswoo_product_attributes[ $_attribute_name ][ $i ]['type'] ) ? $vswoo_product_attributes[ $_attribute_name ][ $i ]['type'] : '';
$attribute_options_val  = isset( $vswoo_product_attributes[ $_attribute_name ][ $i ]['value'] ) ? $vswoo_product_attributes[ $_attribute_name ][ $i ]['value'] : '';
$attribute_options_val  = empty( $attribute_options_val ) ? array() : $attribute_options_val;
$attribute_val_options  = array();


foreach ( $attribute->get_options() as $option ) {

	$value = isset( $attribute_options_val[ $option ] ) ? $attribute_options_val[ $option ] : '';

	$attribute_val_options[] = array(
		'id'    => "attribute_options_val[$i][$option]",
		'title' => sprintf( '%s: %s', vswoo_get_attribute_field_label( $attribute_type ), $option ),
		'type'  => vswoo_get_attribute_field_type( $attribute_type ),
		'value' => $value,
	);
}


?>

<tr>
    <td colspan="2">
        <p class="vswoo-notice vswoo-notice-warning"><?php esc_html_e( 'You may need to update product/attributes to apply changes', VSWOO_TEXTDOMAIN ); ?></p>
    </td>
</tr>

<tr>
    <td class="attribute_type">
        <label><?php esc_html_e( 'Attribute Type', 'woocommerce' ); ?>:</label>
        <select name="attribute_type[<?php echo esc_attr( $i ); ?>]" id="">
			<?php foreach ( wc_get_attribute_types() as $type => $label ) {
				printf( '<option %s value="%s">%s</option>', $attribute_type == $type ? 'selected' : '', $type, $label );
			} ?>
        </select>
    </td>
    <td rowspan="3">
		<?php if ( $attribute_type != 'select' ) {
			$pb_settings->generate_fields( array( array( 'options' => $attribute_val_options ) ), false, false );
		} ?>
    </td>
</tr>

