<?php
/*
* @Author 		Pluginbazar
* Copyright: 	2015 Pluginbazar
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}  // if direct access


$options = array(
	'page_nav'      => esc_html__( 'Options', VSWOO_TEXTDOMAIN ),
	'page_settings' => array(
		'section_popup' => array(
			'title'       => esc_html__( 'Popup box', VSWOO_TEXTDOMAIN ),
			'description' => esc_html__( 'Update settings for Popup box on Shop Page. This popup box might be not showing/working if your active theme do not have any Add to cart button in the shop page.', VSWOO_TEXTDOMAIN ),
			'options'     => array(
				array(
					'id'          => 'vswoo_popup_box_title',
					'title'       => esc_html__( 'Texts', VSWOO_TEXTDOMAIN ),
					'details'     => esc_html__( 'Popup box title', VSWOO_TEXTDOMAIN ),
					'type'        => 'text',
					'placeholder' => esc_html__( 'Select product options', VSWOO_TEXTDOMAIN ),
					'default'     => esc_html__( 'Select options', VSWOO_TEXTDOMAIN ),
				),
				array(
					'id'          => 'vswoo_popup_box_footer_title',
					'details'     => esc_html__( 'Popup box footer title', VSWOO_TEXTDOMAIN ),
					'type'        => 'text',
					'placeholder' => esc_html__( 'We accept', VSWOO_TEXTDOMAIN ),
					'default'     => esc_html__( 'We accept', VSWOO_TEXTDOMAIN ),
				),
				array(
					'id'          => 'vswoo_popup_box_btn_atc_text',
					'title'       => esc_html__( 'Button Texts', VSWOO_TEXTDOMAIN ),
					'details'     => esc_html__( 'Add to cart button text', VSWOO_TEXTDOMAIN ),
					'type'        => 'text',
					'placeholder' => esc_html__( 'Add to cart', VSWOO_TEXTDOMAIN ),
					'default'     => esc_html__( 'Add to cart', VSWOO_TEXTDOMAIN ),
				),
				array(
					'id'          => 'vswoo_popup_box_btn_cancel_text',
					'details'     => esc_html__( 'Cancel button text', VSWOO_TEXTDOMAIN ),
					'type'        => 'text',
					'placeholder' => esc_html__( 'Cancel', VSWOO_TEXTDOMAIN ),
					'default'     => esc_html__( 'Continue Shopping', VSWOO_TEXTDOMAIN ),
				),
				array(
					'id'      => 'vswoo_popup_box_payment_icons',
					'title'   => esc_html__( 'Payment methods', VSWOO_TEXTDOMAIN ),
					'type'    => 'checkbox',
					'args'    => array(
						'paypal'     => esc_html__( 'Paypal', VSWOO_TEXTDOMAIN ),
						'visa'       => esc_html__( 'VISA', VSWOO_TEXTDOMAIN ),
						'mastercard' => esc_html__( 'Mastercard', VSWOO_TEXTDOMAIN ),
						'aexpress'   => esc_html__( 'American Express', VSWOO_TEXTDOMAIN ),
						'amazon'     => esc_html__( 'Amazon', VSWOO_TEXTDOMAIN ),
					),
					'default' => array( 'paypal', 'visa', 'mastercard', 'aexpress', 'amazon' ),
				),

			)
		), // section_popup
	),
);

$popup_styles = array(
	'page_nav'      => esc_html__( 'Popup Styles', VSWOO_TEXTDOMAIN ),
	'page_settings' => array(

		'section_style_header' => array(
			'title'       => esc_html__( 'Styles - Popup Box', VSWOO_TEXTDOMAIN ),
			'description' => esc_html__( 'Change styling for popup variation selection box on Shop page', VSWOO_TEXTDOMAIN ),
			'options'     => array(

				// Header styling
				array(
					'id'          => 'vswoo_popup_title_fontsize',
					'title'       => esc_html__( 'Popup Title', VSWOO_TEXTDOMAIN ),
					'details'     => esc_html__( 'Popup title font size', VSWOO_TEXTDOMAIN ),
					'placeholder' => esc_html__( '18', VSWOO_TEXTDOMAIN ),
					'type'        => 'number',
					'disabled'    => true,
				),
				array(
					'id'       => 'vswoo_popup_title_color',
					'details'  => esc_html__( 'Popup title color', VSWOO_TEXTDOMAIN ),
					'type'     => 'colorpicker',
					'disabled' => true,

				),
				array(
					'id'       => 'vswoo_popup_title_bg',
					'details'  => esc_html__( 'Popup title background color', VSWOO_TEXTDOMAIN ),
					'type'     => 'colorpicker',
					'disabled' => true,

				),
				array(
					'id'       => 'vswoo_popup_title_fontstyle',
					'details'  => esc_html__( 'Popup title font style', VSWOO_TEXTDOMAIN ),
					'type'     => 'select',
					'args'     => array(
						'normal-normal' => esc_html__( 'Normal', VSWOO_TEXTDOMAIN ),
						'normal-bold'   => esc_html__( 'Normal - Bold', VSWOO_TEXTDOMAIN ),
						'italic-normal' => esc_html__( 'Italic - Normal', VSWOO_TEXTDOMAIN ),
						'italic-bold'   => esc_html__( 'Italic - Bold', VSWOO_TEXTDOMAIN ),
					),
					'disabled' => true,
				),

				// Content stying
				array(
					'id'          => 'vswoo_attribute_title_fontsize',
					'title'       => esc_html__( 'Popup Attribute', VSWOO_TEXTDOMAIN ),
					'details'     => esc_html__( 'Attribute title font size', VSWOO_TEXTDOMAIN ),
					'type'        => 'number',
					'placeholder' => esc_html__( '16', VSWOO_TEXTDOMAIN ),
					'disabled'    => true,
				),
				array(
					'id'       => 'vswoo_attribute_title_color',
					'details'  => esc_html__( 'Attribute title color', VSWOO_TEXTDOMAIN ),
					'type'     => 'colorpicker',
					'disabled' => true,
				),
				array(
					'id'          => 'vswoo_popup_button_fontsize',
					'details'     => esc_html__( 'Popup button font size', VSWOO_TEXTDOMAIN ),
					'type'        => 'number',
					'placeholder' => esc_html__( '14', VSWOO_TEXTDOMAIN ),
					'disabled'    => true,
				),
				array(
					'id'       => 'vswoo_popup_button_color',
					'details'  => esc_html__( 'Popup button color', VSWOO_TEXTDOMAIN ),
					'type'     => 'colorpicker',
					'disabled' => true,
				),
				array(
					'id'       => 'vswoo_popup_button_atc_bgcolor',
					'details'  => esc_html__( 'Button `Add to cart` background color', VSWOO_TEXTDOMAIN ),
					'type'     => 'colorpicker',
					'disabled' => true,
				),
				array(
					'id'       => 'vswoo_popup_button_cancel_bgcolor',
					'details'  => esc_html__( 'Button `Cancel` background color', VSWOO_TEXTDOMAIN ),
					'type'     => 'colorpicker',
					'disabled' => true,
				),

				// Footer Styling
				array(
					'id'          => 'vswoo_popup_footer_fontsize',
					'title'       => esc_html__( 'Popup Footer', VSWOO_TEXTDOMAIN ),
					'details'     => esc_html__( 'Footer text font size', VSWOO_TEXTDOMAIN ),
					'type'        => 'number',
					'placeholder' => esc_html__( '14', VSWOO_TEXTDOMAIN ),
					'disabled'    => true,
				),
				array(
					'id'       => 'vswoo_popup_footer_color',
					'details'  => esc_html__( 'Footer text color', VSWOO_TEXTDOMAIN ),
					'type'     => 'colorpicker',
					'disabled' => true,
				),
				array(
					'id'       => 'vswoo_popup_footer_bg_color',
					'details'  => esc_html__( 'Footer background color', VSWOO_TEXTDOMAIN ),
					'type'     => 'colorpicker',
					'disabled' => true,
				),
			)
		),

	),
);

$attribute_styles = array(
	'page_nav'      => esc_html__( 'Attribute Styles', VSWOO_TEXTDOMAIN ),
	'page_settings' => array(

		'section_style_header' => array(
			'title'       => esc_html__( 'Styles - Variation Attribute', VSWOO_TEXTDOMAIN ),
			'description' => esc_html__( 'Change styling for variation attributes on both single product page and shop page', VSWOO_TEXTDOMAIN ),
			'options'     => array(

				// Type - Colors
				array(
					'id'          => 'vswoo_attr_colors_border_width',
					'title'       => esc_html__( 'Type - Colors', VSWOO_TEXTDOMAIN ),
					'details'     => esc_html__( 'Border width (in px)', VSWOO_TEXTDOMAIN ),
					'placeholder' => esc_html__( '3', VSWOO_TEXTDOMAIN ),
					'type'        => 'range',
					'min'         => 0,
					'max'         => 10,
					'disabled'    => true,
				),
				array(
					'id'          => 'vswoo_attr_colors_border_radius',
					'details'     => esc_html__( 'Border Radius. Note: For % put minus sign (-) before the value. Example: -50', VSWOO_TEXTDOMAIN ),
					'placeholder' => esc_html__( '5', VSWOO_TEXTDOMAIN ),
					'type'        => 'number',
					'disabled'    => true,
				),
				array(
					'id'       => 'vswoo_attr_colors_border_color',
					'details'  => esc_html__( 'Border Color', VSWOO_TEXTDOMAIN ),
					'type'     => 'colorpicker',
					'disabled' => true,
				),

				// Type - Images
				array(
					'id'          => 'vswoo_attr_images_border_width',
					'title'       => esc_html__( 'Type - Images', VSWOO_TEXTDOMAIN ),
					'details'     => esc_html__( 'Border width (in px)', VSWOO_TEXTDOMAIN ),
					'placeholder' => esc_html__( '3', VSWOO_TEXTDOMAIN ),
					'type'        => 'range',
					'min'         => 0,
					'max'         => 10,
					'disabled'    => true,
				),
				array(
					'id'          => 'vswoo_attr_images_border_radius',
					'details'     => esc_html__( 'Border Radius. Note: For % put minus sign (-) before the value. Example: -50', VSWOO_TEXTDOMAIN ),
					'placeholder' => esc_html__( '5', VSWOO_TEXTDOMAIN ),
					'type'        => 'number',
					'disabled'    => true,
				),
				array(
					'id'       => 'vswoo_attr_images_border_color',
					'details'  => esc_html__( 'Border Color', VSWOO_TEXTDOMAIN ),
					'type'     => 'colorpicker',
					'disabled' => true,
				),

				// Type - Buttons
				array(
					'id'          => 'vswoo_attr_buttons_border_width',
					'title'       => esc_html__( 'Type - Buttons', VSWOO_TEXTDOMAIN ),
					'details'     => esc_html__( 'Border width (in px)', VSWOO_TEXTDOMAIN ),
					'placeholder' => esc_html__( '3', VSWOO_TEXTDOMAIN ),
					'type'        => 'range',
					'min'         => 0,
					'max'         => 10,
					'disabled'    => true,
				),
				array(
					'id'          => 'vswoo_attr_buttons_border_radius',
					'details'     => esc_html__( 'Border Radius. Note: For % put minus sign (-) before the value. Example: -50', VSWOO_TEXTDOMAIN ),
					'placeholder' => esc_html__( '5', VSWOO_TEXTDOMAIN ),
					'type'        => 'number',
					'disabled'    => true,
				),
				array(
					'id'       => 'vswoo_attr_buttons_border_color',
					'details'  => esc_html__( 'Border Color', VSWOO_TEXTDOMAIN ),
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
	'menu_title'      => esc_html__( 'Variation Swatches', VSWOO_TEXTDOMAIN ),
	'page_title'      => esc_html__( 'Advanced Variation Swatches', VSWOO_TEXTDOMAIN ),
	'menu_page_title' => esc_html__( 'Advanced Variation Swatches - Settings', VSWOO_TEXTDOMAIN ),
	'disabled_notice' => esc_html__( 'Premium Feature', VSWOO_TEXTDOMAIN ),
	'capability'      => "manage_woocommerce",
	'menu_slug'       => "advanced-variation",
	'parent_slug'     => "woocommerce",
	'pages'           => array(
		'vswoo_options'          => $options,
		'vswoo_popup_styles'     => $popup_styles,
		'vswoo_attribute_styles' => $attribute_styles,
	),
);

global $vswoo_settings;

$vswoo_settings = new PB_Settings( $options_data );



