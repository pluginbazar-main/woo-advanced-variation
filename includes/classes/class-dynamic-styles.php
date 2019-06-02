<?php
/**
 * Dynamic Styles Class
 *
 * @author       Pluginbazar
 * @copyright    2015 Pluginbazar
 */

class WAV_Dynamic_styles {

	/**
	 * Softly_Dynamic_styles constructor.
	 */
	public function __construct() {

		add_action( 'wp_head', array( $this, 'header_output' ), 999 );
	}

	/**
	 * This will output the custom WordPress settings to the live theme's WP head.
	 *
	 * @hooked wp_head
	 */
	public static function header_output() {

		printf( '<style>%s</style>', self::get_css() );
	}


	/**
	 * Return CSS Codes from the options settings
	 *
	 * @return false|string
	 */
	public static function get_css() {

		$styles          = array();
		$title_fontstyle = explode( '-', get_option( 'wav_popup_title_fontstyle' ) );

		// Popup Title
		$styles['.wav_popup_container .wav_popup_title']['font-size']   = get_option( 'wav_popup_title_fontsize' );
		$styles['.wav_popup_container .wav_popup_title']['color']       = get_option( 'wav_popup_title_color' );
		$styles['.wav_popup_container .wav_popup_title']['background']  = get_option( 'wav_popup_title_bg' );
		$styles['.wav_popup_container .wav_popup_title']['font-style']  = isset( $title_fontstyle[0] ) ? $title_fontstyle[0] : '';
		$styles['.wav_popup_container .wav_popup_title']['font-weight'] = isset( $title_fontstyle[1] ) ? $title_fontstyle[1] : '';

		// Popup Content - Attribute Title/Label
		$styles['.wav_popup_container .wav_popup_section .wav_section_title']['font-size'] = get_option( 'wav_attribute_title_fontsize' );
		$styles['.wav_popup_container .wav_popup_section .wav_section_title']['color']     = get_option( 'wav_attribute_title_color' );

		// Popup Content - Buttons
		$styles['.wav_popup_container .wav_popup_buttons .wav_btn']['font-size'] = get_option( 'wav_popup_button_fontsize' );
		$styles['.wav_popup_container .wav_popup_buttons .wav_btn']['color']     = get_option( 'wav_popup_button_color' );

		// Popup Content - ATC/Cancel Buttons
		$styles['.wav_popup_container .wav_btn.wav_btn_addtocart']['background'] = get_option( 'wav_popup_button_atc_bgcolor' );
		$styles['.wav_popup_container .wav_btn.wav_btn_close']['background']     = get_option( 'wav_popup_button_cancel_bgcolor' );

		$styles['.wav_popup_container .wav_btn.wav_btn_addtocart:hover']['background'] = self::get_darken_color( get_option( 'wav_popup_button_atc_bgcolor' ) );
		$styles['.wav_popup_container .wav_btn.wav_btn_close:hover']['background']     = self::get_darken_color( get_option( 'wav_popup_button_cancel_bgcolor' ) );

		// Popup Footer
		$styles['.wav_popup_container .wav_popup_payment']['font-size']  = get_option( 'wav_popup_footer_fontsize' );
		$styles['.wav_popup_container .wav_popup_payment']['color']      = get_option( 'wav_popup_footer_color' );
		$styles['.wav_popup_container .wav_popup_payment']['background'] = get_option( 'wav_popup_footer_bg_color' );


		// Attribute Type - Colors
		$styles['.wav-attribute-content.type-colors']['border-width']                    = get_option( 'wav_attr_colors_border_width' );
		$styles['.wav-attribute-content.type-colors']['border-radius']                   = get_option( 'wav_attr_colors_border_radius' );
		$styles['.wav-attribute-content.type-colors']['border-color']                    = get_option( 'wav_attr_colors_border_color' );
		$styles['.wav-attribute-content.type-colors.attribute-selected']['border-color'] = self::get_darken_color( get_option( 'wav_attr_colors_border_color' ), 1.75 );

		// Attribute Type - Images
		$styles['.wav-attribute-content.type-images']['border-width']                    = get_option( 'wav_attr_images_border_width' );
		$styles['.wav-attribute-content.type-images']['border-radius']                   = get_option( 'wav_attr_images_border_radius' );
		$styles['.wav-attribute-content.type-images img']['border-radius']               = get_option( 'wav_attr_images_border_radius' );
		$styles['.wav-attribute-content.type-images']['border-color']                    = get_option( 'wav_attr_images_border_color' );
		$styles['.wav-attribute-content.type-images.attribute-selected']['border-color'] = self::get_darken_color( get_option( 'wav_attr_images_border_color' ), 1.75 );

		// Attribute Type - Buttons
		$styles['.wav-attribute-content.type-buttons']['border-width']                    = get_option( 'wav_attr_buttons_border_width' );
		$styles['.wav-attribute-content.type-buttons']['border-radius']                   = get_option( 'wav_attr_buttons_border_radius' );
		$styles['.wav-attribute-content.type-buttons']['border-color']                    = get_option( 'wav_attr_buttons_border_color' );
		$styles['.wav-attribute-content.type-buttons.attribute-selected']['border-color'] = self::get_darken_color( get_option( 'wav_attr_buttons_border_color' ), 1.75 );


		ob_start();

		foreach ( $styles as $css_class => $this_styles ) {

			$css_styles = array();

			foreach ( $this_styles as $css_property => $property_value ) {

				if ( empty( $property_value ) ) {
					continue;
				}

				$put_px       = in_array( $css_property, array( 'font-size', 'border-width', 'border-radius' ) ) ? 'px' : '';

				if( (int) $property_value < 0 && $css_property == 'border-radius' ) {
					$put_px = '%';
					$property_value *= -1;
				}

				$css_styles[] = sprintf( '%s: %s%s;', $css_property, $property_value, $put_px );
			}

			if ( ! empty( $css_styles ) ) {
				printf( '%s{%s}', $css_class, implode( ' ', $css_styles ) );
			}
		}

		return ob_get_clean();
	}


	/**
	 * Return Darken Color of a Given color
	 *
	 * @param string $color | Empty will return empty darken value
	 * @param float $darker | Should be greater than 1
	 *
	 * @return string
	 */
	private static function get_darken_color( $color = '', $darker = 1.25 ) {

		if ( empty( $color ) ) {
			return '';
		}

		$hash = ( strpos( $color, '#' ) !== false ) ? '#' : '';
		$rgb  = ( strlen( $color ) == 7 ) ? str_replace( '#', '', $color ) : ( ( strlen( $color ) == 6 ) ? $color : false );
		if ( strlen( $rgb ) != 6 ) {
			return $hash . '000000';
		}
		$darker = ( $darker > 1 ) ? $darker : 1;

		list( $R16, $G16, $B16 ) = str_split( $rgb, 2 );

		$R = sprintf( "%02X", floor( hexdec( $R16 ) / $darker ) );
		$G = sprintf( "%02X", floor( hexdec( $G16 ) / $darker ) );
		$B = sprintf( "%02X", floor( hexdec( $B16 ) / $darker ) );

		return $hash . $R . $G . $B;
	}

}

new WAV_Dynamic_styles();
