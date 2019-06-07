<?php
/*
* @Author 		Pluginbazar
* Copyright: 	2015 Pluginbazar
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}  // if direct access


$options = array(
	'page_nav'      => esc_html__( 'Options', WAV_TEXTDOMAIN ),
	'page_settings' => array(
		'section_popup' => array(
			'title'       => esc_html__( 'Popup box', WAV_TEXTDOMAIN ),
			'description' => esc_html__( 'Update settings for Popup box on Shop Page. This popup box might be not showing/working if your active theme do not have any Add to cart button in the shop page.', WAV_TEXTDOMAIN ),
			'options'     => array(
				array(
					'id'          => 'wav_popup_box_title',
					'title'       => esc_html__( 'Texts', WAV_TEXTDOMAIN ),
					'details'     => esc_html__( 'Popup box title', WAV_TEXTDOMAIN ),
					'type'        => 'text',
					'placeholder' => esc_html__( 'Select product options', WAV_TEXTDOMAIN ),
					'default'     => esc_html__( 'Select options', WAV_TEXTDOMAIN ),
				),
				array(
					'id'          => 'wav_popup_box_footer_title',
					'details'     => esc_html__( 'Popup box footer title', WAV_TEXTDOMAIN ),
					'type'        => 'text',
					'placeholder' => esc_html__( 'We accept', WAV_TEXTDOMAIN ),
					'default'     => esc_html__( 'We accept', WAV_TEXTDOMAIN ),
				),
				array(
					'id'          => 'wav_popup_box_btn_atc_text',
					'title'       => esc_html__( 'Button Texts', WAV_TEXTDOMAIN ),
					'details'     => esc_html__( 'Add to cart button text', WAV_TEXTDOMAIN ),
					'type'        => 'text',
					'placeholder' => esc_html__( 'Add to cart', WAV_TEXTDOMAIN ),
					'default'     => esc_html__( 'Add to cart', WAV_TEXTDOMAIN ),
				),
				array(
					'id'          => 'wav_popup_box_btn_cancel_text',
					'details'     => esc_html__( 'Cancel button text', WAV_TEXTDOMAIN ),
					'type'        => 'text',
					'placeholder' => esc_html__( 'Cancel', WAV_TEXTDOMAIN ),
					'default'     => esc_html__( 'Continue Shopping', WAV_TEXTDOMAIN ),
				),
				array(
					'id'      => 'wav_popup_box_payment_icons',
					'title'   => esc_html__( 'Payment methods', WAV_TEXTDOMAIN ),
					'type'    => 'checkbox',
					'args'    => array(
						'paypal'     => esc_html__( 'Paypal', WAV_TEXTDOMAIN ),
						'visa'       => esc_html__( 'VISA', WAV_TEXTDOMAIN ),
						'mastercard' => esc_html__( 'Mastercard', WAV_TEXTDOMAIN ),
						'aexpress'   => esc_html__( 'American Express', WAV_TEXTDOMAIN ),
						'amazon'     => esc_html__( 'Amazon', WAV_TEXTDOMAIN ),
					),
					'default' => array( 'paypal', 'visa', 'mastercard', 'aexpress', 'amazon' ),
				),

			)
		), // section_popup
	),
);

$popup_styles = array(
	'page_nav'      => esc_html__( 'Popup Styles', WAV_TEXTDOMAIN ),
	'page_settings' => array(

		'section_style_header' => array(
			'title'       => esc_html__( 'Styles - Popup Box', WAV_TEXTDOMAIN ),
			'description' => esc_html__( 'Change styling for popup variation selection box on Shop page', WAV_TEXTDOMAIN ),
			'options'     => array(

				// Header styling
				array(
					'id'          => 'wav_popup_title_fontsize',
					'title'       => esc_html__( 'Popup Title', WAV_TEXTDOMAIN ),
					'details'     => esc_html__( 'Popup title font size', WAV_TEXTDOMAIN ),
					'placeholder' => esc_html__( '18', WAV_TEXTDOMAIN ),
					'type'        => 'number',
					'disabled'    => true,
				),
				array(
					'id'       => 'wav_popup_title_color',
					'details'  => esc_html__( 'Popup title color', WAV_TEXTDOMAIN ),
					'type'     => 'colorpicker',
					'disabled' => true,

				),
				array(
					'id'       => 'wav_popup_title_bg',
					'details'  => esc_html__( 'Popup title background color', WAV_TEXTDOMAIN ),
					'type'     => 'colorpicker',
					'disabled' => true,

				),
				array(
					'id'       => 'wav_popup_title_fontstyle',
					'details'  => esc_html__( 'Popup title font style', WAV_TEXTDOMAIN ),
					'type'     => 'select',
					'args'     => array(
						'normal-normal' => esc_html__( 'Normal', WAV_TEXTDOMAIN ),
						'normal-bold'   => esc_html__( 'Normal - Bold', WAV_TEXTDOMAIN ),
						'italic-normal' => esc_html__( 'Italic - Normal', WAV_TEXTDOMAIN ),
						'italic-bold'   => esc_html__( 'Italic - Bold', WAV_TEXTDOMAIN ),
					),
					'disabled' => true,
				),

				// Content stying
				array(
					'id'          => 'wav_attribute_title_fontsize',
					'title'       => esc_html__( 'Popup Attribute', WAV_TEXTDOMAIN ),
					'details'     => esc_html__( 'Attribute title font size', WAV_TEXTDOMAIN ),
					'type'        => 'number',
					'placeholder' => esc_html__( '16', WAV_TEXTDOMAIN ),
					'disabled'    => true,
				),
				array(
					'id'       => 'wav_attribute_title_color',
					'details'  => esc_html__( 'Attribute title color', WAV_TEXTDOMAIN ),
					'type'     => 'colorpicker',
					'disabled' => true,
				),
				array(
					'id'          => 'wav_popup_button_fontsize',
					'details'     => esc_html__( 'Popup button font size', WAV_TEXTDOMAIN ),
					'type'        => 'number',
					'placeholder' => esc_html__( '14', WAV_TEXTDOMAIN ),
					'disabled'    => true,
				),
				array(
					'id'       => 'wav_popup_button_color',
					'details'  => esc_html__( 'Popup button color', WAV_TEXTDOMAIN ),
					'type'     => 'colorpicker',
					'disabled' => true,
				),
				array(
					'id'       => 'wav_popup_button_atc_bgcolor',
					'details'  => esc_html__( 'Button `Add to cart` background color', WAV_TEXTDOMAIN ),
					'type'     => 'colorpicker',
					'disabled' => true,
				),
				array(
					'id'       => 'wav_popup_button_cancel_bgcolor',
					'details'  => esc_html__( 'Button `Cancel` background color', WAV_TEXTDOMAIN ),
					'type'     => 'colorpicker',
					'disabled' => true,
				),

				// Footer Styling
				array(
					'id'          => 'wav_popup_footer_fontsize',
					'title'       => esc_html__( 'Popup Footer', WAV_TEXTDOMAIN ),
					'details'     => esc_html__( 'Footer text font size', WAV_TEXTDOMAIN ),
					'type'        => 'number',
					'placeholder' => esc_html__( '14', WAV_TEXTDOMAIN ),
					'disabled'    => true,
				),
				array(
					'id'       => 'wav_popup_footer_color',
					'details'  => esc_html__( 'Footer text color', WAV_TEXTDOMAIN ),
					'type'     => 'colorpicker',
					'disabled' => true,
				),
				array(
					'id'       => 'wav_popup_footer_bg_color',
					'details'  => esc_html__( 'Footer background color', WAV_TEXTDOMAIN ),
					'type'     => 'colorpicker',
					'disabled' => true,
				),
			)
		),

	),
);

$attribute_styles = array(
	'page_nav'      => esc_html__( 'Attribute Styles', WAV_TEXTDOMAIN ),
	'page_settings' => array(

		'section_style_header' => array(
			'title'       => esc_html__( 'Styles - Variation Attribute', WAV_TEXTDOMAIN ),
			'description' => esc_html__( 'Change styling for variation attributes on both single product page and shop page', WAV_TEXTDOMAIN ),
			'options'     => array(

				// Type - Colors
				array(
					'id'          => 'wav_attr_colors_border_width',
					'title'       => esc_html__( 'Type - Colors', WAV_TEXTDOMAIN ),
					'details'     => esc_html__( 'Border width (in px)', WAV_TEXTDOMAIN ),
					'placeholder' => esc_html__( '3', WAV_TEXTDOMAIN ),
					'type'        => 'range',
					'min'         => 0,
					'max'         => 10,
					'disabled'    => true,
				),
				array(
					'id'          => 'wav_attr_colors_border_radius',
					'details'     => esc_html__( 'Border Radius. Note: For % put minus sign (-) before the value. Example: -50', WAV_TEXTDOMAIN ),
					'placeholder' => esc_html__( '5', WAV_TEXTDOMAIN ),
					'type'        => 'number',
					'disabled'    => true,
				),
				array(
					'id'       => 'wav_attr_colors_border_color',
					'details'  => esc_html__( 'Border Color', WAV_TEXTDOMAIN ),
					'type'     => 'colorpicker',
					'disabled' => true,
				),

				// Type - Images
				array(
					'id'          => 'wav_attr_images_border_width',
					'title'       => esc_html__( 'Type - Images', WAV_TEXTDOMAIN ),
					'details'     => esc_html__( 'Border width (in px)', WAV_TEXTDOMAIN ),
					'placeholder' => esc_html__( '3', WAV_TEXTDOMAIN ),
					'type'        => 'range',
					'min'         => 0,
					'max'         => 10,
					'disabled'    => true,
				),
				array(
					'id'          => 'wav_attr_images_border_radius',
					'details'     => esc_html__( 'Border Radius. Note: For % put minus sign (-) before the value. Example: -50', WAV_TEXTDOMAIN ),
					'placeholder' => esc_html__( '5', WAV_TEXTDOMAIN ),
					'type'        => 'number',
					'disabled'    => true,
				),
				array(
					'id'       => 'wav_attr_images_border_color',
					'details'  => esc_html__( 'Border Color', WAV_TEXTDOMAIN ),
					'type'     => 'colorpicker',
					'disabled' => true,
				),

				// Type - Buttons
				array(
					'id'          => 'wav_attr_buttons_border_width',
					'title'       => esc_html__( 'Type - Buttons', WAV_TEXTDOMAIN ),
					'details'     => esc_html__( 'Border width (in px)', WAV_TEXTDOMAIN ),
					'placeholder' => esc_html__( '3', WAV_TEXTDOMAIN ),
					'type'        => 'range',
					'min'         => 0,
					'max'         => 10,
					'disabled'    => true,
				),
				array(
					'id'          => 'wav_attr_buttons_border_radius',
					'details'     => esc_html__( 'Border Radius. Note: For % put minus sign (-) before the value. Example: -50', WAV_TEXTDOMAIN ),
					'placeholder' => esc_html__( '5', WAV_TEXTDOMAIN ),
					'type'        => 'number',
					'disabled'    => true,
				),
				array(
					'id'       => 'wav_attr_buttons_border_color',
					'details'  => esc_html__( 'Border Color', WAV_TEXTDOMAIN ),
					'type'     => 'colorpicker',
					'disabled' => true,
				),


			)
		),

	),
);


$options_data = array(
	'add_in_menu'     => true,
	'menu_type'       => 'submenu',
	'menu_title'      => esc_html__( 'Variation Swatches', WAV_TEXTDOMAIN ),
	'page_title'      => esc_html__( 'Advanced Variation Swatches', WAV_TEXTDOMAIN ),
	'menu_page_title' => esc_html__( 'Advanced Variation Swatches - Settings', WAV_TEXTDOMAIN ),
	'disabled_notice' => esc_html__( 'Premium Feature', WAV_TEXTDOMAIN ),
	'capability'      => "manage_woocommerce",
	'menu_slug'       => "advanced-variation",
	'parent_slug'     => "woocommerce",
	'pages'           => array(
		'wav_options'          => $options,
		'wav_popup_styles'     => $popup_styles,
		'wav_attribute_styles' => $attribute_styles,
	),
);

global $wav_settings;

$wav_settings = new PB_Settings( $options_data );



