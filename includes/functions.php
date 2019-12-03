<?php
/*
* @Author 		Pluginbazar
* Copyright: 	2015 Pluginbazar
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}  // if direct access


if ( ! function_exists( 'vswoo_get_custom_attribute_type' ) ) {
	/**
	 * Return Custom Attribute type by giving product ID and Attribute Name
	 *
	 * @param int $product_id
	 * @param string $attribute_name
	 *
	 * @return mixed|void
	 */
	function vswoo_get_custom_attribute_type( $product_id = 0, $attribute_name = '' ) {

		$_attributes = get_post_meta( $product_id, 'vswoo_product_attributes', true );
		$_attributes = empty( $_attributes ) ? array() : $_attributes;

		$attribute_name    = sanitize_title( $attribute_name );
		$_attribute_values = isset( $_attributes[ $attribute_name ] ) ? $_attributes[ $attribute_name ] : array();
		$_attribute_values = empty( $_attribute_values ) ? array() : reset( $_attribute_values );

		$_attribute_type = isset( $_attribute_values['type'] ) ? $_attribute_values['type'] : 'select';

		return apply_filters( 'vswoo_filters_get_custom_attribute_type', $_attribute_type, $product_id, $attribute_name );
	}
}


if ( ! function_exists( 'vswoo_get_custom_attribute_option_val_by_term_slug' ) ) {
	/**
	 * Return Attribute option value by giving product ID and Term slug
	 *
	 * @param int $product_id
	 * @param string $attribute_name
	 * @param string $term_slug
	 *
	 * @return mixed|void
	 */
	function vswoo_get_custom_attribute_option_val_by_term_slug( $product_id = 0, $attribute_name = '', $term_slug = '' ) {

		$_attributes = get_post_meta( $product_id, 'vswoo_product_attributes', true );
		$_attributes = empty( $_attributes ) ? array() : $_attributes;

		$attribute_name    = sanitize_title( $attribute_name );
		$_attribute_values = isset( $_attributes[ $attribute_name ] ) ? $_attributes[ $attribute_name ] : array();
		$_attribute_values = empty( $_attribute_values ) ? array() : reset( $_attribute_values );

		$_attribute_value = isset( $_attribute_values['value'] ) ? $_attribute_values['value'] : array();
		$_attribute_value = empty( $_attribute_value ) ? array() : $_attribute_value;

		$_term_value = isset( $_attribute_value[ $term_slug ] ) ? $_attribute_value[ $term_slug ] : '';

		return apply_filters( 'vswoo_filters_get_custom_attribute_option_val_by_term_slug', $_term_value, $product_id, $attribute_name, $term_slug );
	}
}


if ( ! function_exists( 'vswoo_get_attribute_content_by_term_id' ) ) {
	function vswoo_get_attribute_content_by_term_id( $term_id = false ) {

		if ( ! $term_id ) {
			return;
		}

		$term         = get_term_by( 'term_taxonomy_id', $term_id );
		$attribute_id = wc_attribute_taxonomy_id_by_name( $term->taxonomy );
		$attribute    = wc_get_attribute( $attribute_id );
		$field_id     = vswoo_get_attribute_field_id( $attribute->type );
		$field_val    = get_term_meta( $term_id, $field_id, true );

		if ( empty( $field_val ) ) {
			return;
		}


		ob_start();

		switch ( $attribute->type ) {
			case 'type_colors' :
				printf( '<div class="vswoo-attribute-content type-colors tt--top" aria-label="%s - %s" style="background: %s;"></div>', vswoo_get_taxonomy_label( $term->taxonomy ), $term->name, $field_val );
				break;

			case 'type_images' :

				printf( '<div class="vswoo-attribute-content type-images tt--top" aria-label="%s - %s" ><img src="%s"></div>', vswoo_get_taxonomy_label( $term->taxonomy ), $term->name, wp_get_attachment_url( $field_val ) );
				break;

			case 'type_buttons' :
				printf( '<div class="vswoo-attribute-content type-buttons tt--top" aria-label="%s - %s" >%s</div>', vswoo_get_taxonomy_label( $term->taxonomy ), $term->name, $field_val );
				break;

			default :
				printf( '<span class="vswoo-attribute-content vswoo-no-content"></span>' );
		}

		return apply_filters( 'vswoo_get_attribute_content_by_term_id', ob_get_clean(), $term_id );
	}
}


if ( ! function_exists( 'vswoo_get_taxonomy_label' ) ) {
	function vswoo_get_taxonomy_label( $taxonomy = false, $replace_prefix = true ) {

		if ( ! $taxonomy ) {
			return;
		}

		$taxonomy_obj   = get_taxonomy( $taxonomy );
		$taxonomy_label = $taxonomy_obj ? $taxonomy_obj->label : '';
		$taxonomy_label = $replace_prefix ? str_replace( 'Product', '', $taxonomy_label ) : $taxonomy_label;

		return apply_filters( 'vswoo_get_taxonomy_label', $taxonomy_label, $taxonomy );
	}
}


if ( ! function_exists( 'vswoo_get_attribute_col_label' ) ) {
	function vswoo_get_attribute_col_label( $taxonomy = false, $attr_type = false ) {

		if ( ! $taxonomy ) {
			$taxonomy = isset( $_GET['taxonomy'] ) ? sanitize_text_field( $_GET['taxonomy'] ) : '';
		}

		if ( ! $attr_type ) {
			$attribute_id = wc_attribute_taxonomy_id_by_name( $taxonomy );
			$attribute    = wc_get_attribute( $attribute_id );
			$attr_type    = $attribute->type;
		}

		switch ( $attr_type ) {
			case 'type_colors' :
				$label = esc_html__( 'Color', VSWOO_TEXTDOMAIN );
				break;

			case 'type_images' :
				$label = esc_html__( 'Image', VSWOO_TEXTDOMAIN );
				break;

			case 'type_buttons' :
				$label = esc_html__( 'Button', VSWOO_TEXTDOMAIN );
				break;

			default :
				$label = esc_html__( 'Label', VSWOO_TEXTDOMAIN );
		}

		return apply_filters( 'vswoo_get_attribute_col_label', $label, $taxonomy );
	}
}


if ( ! function_exists( 'vswoo_get_attribute_field_id' ) ) {
	/**
	 * Return Attribute Field's Label by Attribute Type
	 *
	 * @param $attr_type
	 *
	 * @return mixed
	 */
	function vswoo_get_attribute_field_id( $attr_type ) {

		switch ( $attr_type ) {
			case 'type_colors' :
				$field_id = 'vswoo_attribute_color';
				break;

			case 'type_images' :
				$field_id = 'vswoo_attribute_image';
				break;

			case 'type_buttons' :
				$field_id = 'vswoo_attribute_button';
				break;

			default :
				$field_id = '';
		}

		return apply_filters( 'vswoo_get_attribute_field_label', $field_id, $attr_type );
	}
}


if ( ! function_exists( 'vswoo_get_attribute_field_type' ) ) {
	/**
	 * Return Attribute Field's Type by Attribute Type
	 *
	 * @param $attr_type
	 *
	 * @return mixed
	 */
	function vswoo_get_attribute_field_type( $attr_type ) {

		switch ( $attr_type ) {
			case 'type_colors' :
				$field_type = 'colorpicker';
				break;

			case 'type_images' :
				$field_type = 'media';
				break;

			case 'type_buttons' :
				$field_type = 'text';
				break;

			default :
				$field_type = '';
		}

		return apply_filters( 'vswoo_get_attribute_field_type', $field_type, $attr_type );
	}
}


if ( ! function_exists( 'vswoo_get_attribute_field_description' ) ) {
	/**
	 * Return Attribute Field's description by Attribute Type
	 *
	 * @param $attr_type
	 *
	 * @return mixed
	 */
	function vswoo_get_attribute_field_description( $attr_type ) {

		switch ( $attr_type ) {
			case 'type_colors' :
				$desc = esc_html__( 'Set custom colors for this Taxonomy', VSWOO_TEXTDOMAIN );
				break;

			case 'type_images' :
				$desc = esc_html__( 'Select image for this Taxonomy', VSWOO_TEXTDOMAIN );
				break;

			case 'type_buttons' :
				$desc = esc_html__( 'Add button text for this Taxonomy', VSWOO_TEXTDOMAIN );
				break;

			default :
				$desc = esc_html__( 'Attribute field description', VSWOO_TEXTDOMAIN );
		}

		return apply_filters( 'vswoo_get_attribute_field_description', $desc, $attr_type );
	}
}


if ( ! function_exists( 'vswoo_get_attribute_field_label' ) ) {
	/**
	 * Return Attribute Field's Label by Attribute Type
	 *
	 * @param $attr_type
	 *
	 * @return mixed
	 */
	function vswoo_get_attribute_field_label( $attr_type ) {

		switch ( $attr_type ) {
			case 'type_colors' :
				$label = esc_html__( 'Select Color', VSWOO_TEXTDOMAIN );
				break;

			case 'type_images' :
				$label = esc_html__( 'Select Image', VSWOO_TEXTDOMAIN );
				break;

			case 'type_buttons' :
				$label = esc_html__( 'Button Text', VSWOO_TEXTDOMAIN );
				break;

			default :
				$label = esc_html__( 'Attribute field label', VSWOO_TEXTDOMAIN );
		}

		return apply_filters( 'vswoo_get_attribute_field_label', $label, $attr_type );
	}
}


if ( ! function_exists( 'vswoo_get_template' ) ) {
	function vswoo_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {

		$located = vswoo_locate_template( $template_name, $template_path, $default_path );

		if ( ! file_exists( $located ) ) {
			return new WP_Error( 'invalid_data', __( '%s does not exist.', VSWOO_TEXTDOMAIN ), '<code>' . $located . '</code>' );
		}

		$located = apply_filters( 'vswoo_filters_get_template', $located, $template_name, $args, $template_path, $default_path );

		do_action( 'vswoo_before_template_part', $template_name, $template_path, $located, $args );

		include $located;

		do_action( 'vswoo_after_template_part', $template_name, $template_path, $located, $args );
	}
}


if ( ! function_exists( 'vswoo_locate_template' ) ) {
	function vswoo_locate_template( $template_name, $template_path = '', $default_path = '' ) {

		global $wpdl;

		if ( ! $template_path ) {
			$template_path = '/woocommerce';
		}

		if ( ! $default_path ) {
			$default_path = untrailingslashit( VSWOO_PLUGIN_DIR ) . '/templates/';
		}

		// Look within passed path within the theme - this is priority.
		$template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name,
				$template_name,
			)
		);

		// Get default template/.
		if ( ! $template ) {
			$template = $default_path . $template_name;
		}

		// Return what we found.
		return apply_filters( 'vswoo_filters_locate_template', $template, $template_name, $template_path );
	}
}


if ( ! function_exists( 'vswoo_get_product_variation_price_html' ) ) {
	/**
	 * Get Variation Price HTML
	 *
	 * @param $variation_id
	 *
	 * @return string
	 */
	function vswoo_get_product_variation_price_html( $variation_id ) {

		$p_variation = wc_get_product( $variation_id );

		if ( ! $p_variation ) {
			return '';
		}

		$p_regular    = $p_variation->get_regular_price();
		$p_display    = wc_get_price_to_display( $p_variation );
		$woo_currency = get_woocommerce_currency_symbol();

		ob_start();

		if ( ! empty( $p_regular ) && $p_display != $p_regular ) {
			printf( '<del>%s %s</del>', $p_regular, $woo_currency );
		}

		if ( ! empty( $p_display ) ) {
			printf( '<ins>%s %s</ins>', $p_display, $woo_currency );
		} else {
			printf( '<span class="no_price">%s</span>', esc_html__( 'No Price found', VSWOO_TEXTDOMAIN ) );
		}

		return ob_get_clean();
	}
}


if ( ! function_exists( 'vswoo_get_product_variation_id' ) ) {
	/**
	 * Return Variation ID from Product
	 *
	 * @param WC_Product $product
	 * @param $attributes
	 *
	 * @return mixed
	 * @throws Exception
	 */
	function vswoo_get_product_variation_id( \WC_Product $product, $attributes ) {

		foreach ( $attributes as $key => $value ) {

			if ( strpos( $key, 'attribute_' ) === 0 ) {
				continue;
			}

			unset( $attributes[ $key ] );
			$attributes[ sprintf( 'attribute_%s', $key ) ] = $value;
		}

		$data_store = WC_Data_Store::load( 'product' );

		return $data_store->find_matching_product_variation( $product, $attributes );
	}
}
