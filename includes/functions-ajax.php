<?php
/**
 * Ajax Calls for WooCommerce Advanced Variations
 */


if ( ! function_exists( 'vswoo_ajax_add_to_cart' ) ) {
	/**
	 * Add to cart Product or Variation and return the cart url
	 *
	 * @throws Exception
	 */
	function vswoo_ajax_add_to_cart() {

		$product_id = isset( $_POST['product_id'] ) ? sanitize_text_field( $_POST['product_id'] ) : 0;

		if ( WC()->cart->add_to_cart( $product_id ) ) {
			wp_send_json_success( wc_get_cart_url() );
		} else {
			wp_send_json_error( esc_html__( 'Error! Try again', VSWOO_TEXTDOMAIN ) );
		}
	}
}
add_action( 'wp_ajax_vswoo_ajax_add_to_cart', 'vswoo_ajax_add_to_cart' );
add_action( 'wp_ajax_nopriv_vswoo_ajax_add_to_cart', 'vswoo_ajax_add_to_cart' );


if ( ! function_exists( 'vswoo_ajax_load_selection_price' ) ) {
	/**
	 * Load price for variation selection
	 */
	function vswoo_ajax_load_selection_price() {

		$product_id   = isset( $_POST['product_id'] ) ? sanitize_text_field( $_POST['product_id'] ) : 0;
		$selection    = isset( $_POST['selection'] ) ? $_POST['selection'] : array();
		$_selection   = serialize( $selection );
		$product      = wc_get_product( $product_id );
		$curr_symbol  = get_woocommerce_currency_symbol();
		$variation_id = 0;
		$p_display    = 0;
		$p_regular    = 0;

		foreach ( $product->get_available_variations() as $variation ):

			$attributes = isset( $variation['attributes'] ) ? $variation['attributes'] : array();

			if ( $_selection != serialize( $attributes ) ) {
				continue;
			}

			$variation_id = isset( $variation['variation_id'] ) ? $variation['variation_id'] : 0;
			$p_display    = isset( $variation['display_price'] ) ? $variation['display_price'] : 0;
			$p_regular    = isset( $variation['display_regular_price'] ) ? $variation['display_regular_price'] : 0;

		endforeach;

		ob_start();

		if ( ! empty( $p_regular ) && $p_display != $p_regular ) {
			printf( '<del>%s %s</del>', $p_regular, $curr_symbol );
		}
		if ( ! empty( $p_display ) ) {
			printf( '<ins>%s %s</ins>', $p_display, $curr_symbol );
		} else {
			printf( '<span class="no_price">%s</span>', esc_html__( 'No Price found', VSWOO_TEXTDOMAIN ) );
		}

		wp_send_json_success( array( 'variation_id' => $variation_id, 'html' => ob_get_clean() ) );
	}
}
add_action( 'wp_ajax_vswoo_ajax_load_selection_price', 'vswoo_ajax_load_selection_price' );
add_action( 'wp_ajax_nopriv_vswoo_ajax_load_selection_price', 'vswoo_ajax_load_selection_price' );


if ( ! function_exists( 'vswoo_ajax_load_variation_selection_box' ) ) {
	/**
	 * Load Variation Box Data
	 *
	 * @throws Exception
	 */
	function vswoo_ajax_load_variation_selection_box() {

		ob_start();

		$product_id          = isset( $_POST['product_id'] ) ? sanitize_text_field( $_POST['product_id'] ) : 0;
		$product             = wc_get_product( $product_id );
		$def_attributes      = $product->get_default_attributes();
		$def_variation_id    = vswoo_get_product_variation_id( $product, $def_attributes );
		$def_variation_price = vswoo_get_product_variation_price_html( $def_variation_id );
		$def_variation_price = empty( $def_variation_price ) ? esc_html__( 'Please select choice to get price', VSWOO_TEXTDOMAIN ) : $def_variation_price;

		foreach ( $product->get_variation_attributes() as $attribute_name => $attributes ) {

			$attribute_id    = wc_attribute_taxonomy_id_by_name( $attribute_name );
			$attribute       = wc_get_attribute( $attribute_id );
			$attribute_type  = $attribute ? $attribute->type : vswoo_get_custom_attribute_type( $product->get_id(), $attribute_name );
			$attribute_label = wc_attribute_label( $attribute_name );

			?>

            <div class="vswoo_popup_section vswoo_popup_section_<?php echo esc_attr( $attribute_name ); ?>"
                 name="<?php echo esc_attr( strtolower( $attribute_name ) ); ?>">
                <div class="vswoo_section_title"><?php echo esc_html( $attribute_label ); ?></div>
                <div class="section_content">

					<?php
					foreach ( $attributes as $term_slug ) {

						if ( $attribute ) {
							$term       = get_term_by( 'slug', $term_slug, $attribute_name );
							$field_id   = vswoo_get_attribute_field_id( $attribute_type );
							$__opt_slug = $term->slug;
							$__opt_name = $term->name;
							$__opt_val  = get_term_meta( $term->term_id, $field_id, true );
						} else {
							$__opt_slug = $term_slug;
							$__opt_name = $term_slug;
							$__opt_val  = vswoo_get_custom_attribute_option_val_by_term_slug( $product->get_id(), $attribute_name, $term_slug );
						}

						$selected = in_array( $__opt_slug, $def_attributes ) ? 'attribute-selected' : '';

						switch ( $attribute_type ) {

							case 'type_colors' :
								printf( '<div class="section_meta vswoo-attribute-content type-colors tt--top %s" value="%s" data-attribute_val="%s" aria-label="%s - %s" style="background: %s;"></div>',
									$selected, $term_slug, $__opt_slug, $attribute_label, $__opt_name, $__opt_val
								);
								break;

							case 'type_images' :
								printf( '<div class="section_meta vswoo-attribute-content type-images tt--top %s" value="%s" data-attribute_val="%s" aria-label="%s - %s"><img src="%s"></div>',
									$selected, $term_slug, $__opt_slug, $attribute_label, $__opt_name, wp_get_attachment_url( $__opt_val )
								);
								break;

							case 'type_buttons' :
								printf( '<div class="section_meta vswoo-attribute-content type-buttons tt--top %s" value="%s" data-attribute_val="%s" aria-label="%s - %s">%s</div>',
									$selected, $term_slug, $__opt_slug, $attribute_label, $__opt_name, $__opt_val
								);
								break;

							default :
								printf( '<span class="section_meta vswoo-attribute-content vswoo-no-content"></span>' );
						}
					}
					?>
                </div>
            </div>
			<?php
		}

		?>
        <span class="vswoo_default_variation_id" variation_id="<?php echo esc_attr( $def_variation_id ); ?>"></span>
        <div class="vswoo_popup_section section_price">
            <div class="vswoo_section_title"><?php esc_html_e( 'Price', VSWOO_TEXTDOMAIN ); ?></div>
            <div class="section_content"><?php print( $def_variation_price ); ?></div>
        </div>
		<?php

		wp_send_json_success( ob_get_clean() );
	}
}
add_action( 'wp_ajax_vswoo_ajax_load_variation_selection_box', 'vswoo_ajax_load_variation_selection_box' );
add_action( 'wp_ajax_nopriv_vswoo_ajax_load_variation_selection_box', 'vswoo_ajax_load_variation_selection_box' );
