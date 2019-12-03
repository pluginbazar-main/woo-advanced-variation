<?php
/**
 * Template: Single Variations
 *
 * @copyright Pluginbazar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}  // if direct access

global $product;

$attribute_name  = isset( $args['attribute'] ) ? $args['attribute'] : '';
$attribute_id    = wc_attribute_taxonomy_id_by_name( $attribute_name );
$attribute       = wc_get_attribute( $attribute_id );
$var_attributes  = $product->get_variation_attributes();
$def_attributes  = $product->get_default_attributes();
$this_attributes = isset( $var_attributes[ $attribute_name ] ) ? $var_attributes[ $attribute_name ] : array();

$selection_fields = array(
	'options'        => $this_attributes,
	'attribute_name' => $attribute_name,
	'selected'       => false,
);

$attribute_type = $attribute ? $attribute->type : vswoo_get_custom_attribute_type( $product->get_id(), $attribute_name );


?>
<div class="vswoo-variation-swatches">

	<?php vswoo_get_template( 'selection-fields.php', $selection_fields ); ?>

	<?php foreach ( $this_attributes as $term_slug ) :

		if ( $attribute ) {

			$term     = get_term_by( 'slug', $term_slug, $attribute_name );
			$field_id = vswoo_get_attribute_field_id( $attribute_type );

			$__opt_slug  = $term->slug;
			$__opt_label = vswoo_get_taxonomy_label( $term->taxonomy );
			$__opt_name  = $term->name;
			$__opt_val   = get_term_meta( $term->term_id, $field_id, true );
		} else {

			$__opt_slug  = $term_slug;
			$__opt_label = $attribute_name;
			$__opt_name  = $term_slug;
			$__opt_val   = vswoo_get_custom_attribute_option_val_by_term_slug( $product->get_id(), $attribute_name, $term_slug );
		}

		$selected = in_array( $__opt_slug, $def_attributes ) ? 'attribute-selected' : '';

		switch ( $attribute_type ) {

			case 'type_colors' :
				printf( '<div class="vswoo-attribute-content type-colors tt--top %s" data-attribute_val="%s" aria-label="%s - %s" style="background: %s;"></div>',
					$selected, $__opt_slug, $__opt_label, $__opt_name, $__opt_val
				);
				break;

			case 'type_images' :
				printf( '<div class="vswoo-attribute-content type-images tt--top %s" data-attribute_val="%s" aria-label="%s - %s" ><img src="%s"></div>',
					$selected, $__opt_slug, $__opt_label, $__opt_name, wp_get_attachment_url( $__opt_val )
				);
				break;

			case 'type_buttons' :
				printf( '<div class="vswoo-attribute-content type-buttons tt--top %s" data-attribute_val="%s" aria-label="%s - %s" >%s</div>',
					$selected, $__opt_slug, $__opt_label, $__opt_name, $__opt_val
				);
				break;

			default :
				printf( '<span class="vswoo-attribute-content vswoo-no-content"></span>' );
		}

	endforeach; ?>

</div>