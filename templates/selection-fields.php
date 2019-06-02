<?php
/**
 * Template: Selection Field
 *
 * @copyright Pluginbazar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}  // if direct access

global $product;

$options        = isset( $args['options'] ) ? $args['options'] : array();
$attribute_name = isset( $args['attribute_name'] ) ? $args['attribute_name'] : '';

if ( empty( $options ) || empty( $attribute_name ) ) {
	return;
}

$name                  = 'attribute_' . sanitize_title( $attribute_name );
$id                    = sanitize_title( $attribute_name );
$class                 = isset( $args['class'] ) ? $args['class'] : '';
$show_option_none      = isset( $args['show_option_none'] ) ? (bool) $args['show_option_none'] : true;
$show_option_none_text = isset( $args['show_option_none'] ) ? $args['show_option_none'] : __( 'Choose an option', 'woocommerce' );

if ( false === $args['selected'] && $attribute_name && $product instanceof WC_Product ) {
	$selected_key     = 'attribute_' . sanitize_title( $attribute_name );
	$args['selected'] = isset( $_REQUEST[ $selected_key ] ) ? wc_clean( wp_unslash( $_REQUEST[ $selected_key ] ) ) : $product->get_variation_default_attribute( $attribute_name );
}

?>

<select id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $class ); ?>"
        name="<?php echo esc_attr( $name ); ?>"
        data-attribute_name="attribute_<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>"
        data-show_option_none="<?php echo( $show_option_none ? 'yes' : 'no' ); ?>">

    <option value=""><?php echo esc_html( $show_option_none_text ); ?></option>

	<?php
	if ( $product && taxonomy_exists( $attribute_name ) ) :

		foreach ( wc_get_product_terms( $product->get_id(), $attribute_name, array( 'fields' => 'all' ) ) as $term ) :

			if ( ! in_array( $term->slug, $options, true ) ) {
				continue;
			}

			?>
            <option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( sanitize_title( $args['selected'] ), $term->slug ); ?>>
				<?php echo esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) ); ?>
            </option> <?php

		endforeach;

	else :

		foreach ( $options as $option ) :

			$selected = sanitize_title( $args['selected'] ) === $args['selected'] ? selected( $args['selected'], sanitize_title( $option ), false ) : selected( $args['selected'], $option, false );

			?>
            <option value="<?php echo esc_attr( $option ); ?>" <?php echo esc_attr( $selected ); ?>>
				<?php echo esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ); ?>
            </option> <?php

		endforeach;

	endif;
	?>

</select>