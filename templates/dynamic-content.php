<?php
/**
 * Template: Dynamic Styles and Popup Box
 *
 * @copyright Pluginbazar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}  // if direct access


global $vswoo_settings;

?>
<div class="vswoo_popup_container vswoo_variation_selection">
    <div class="vswoo_popup_box">

        <div class="vswoo_popup_title">
			<?php print $vswoo_settings->get_option_value( 'vswoo_popup_box_title' ); ?>
        </div>

        <div class="vswoo_popup_content">
            <span class="dashicons dashicons-admin-generic dashicons-spin"></span>
        </div>

        <div class="vswoo_popup_buttons">
            <div class="button vswoo_btn vswoo_btn_close">
	            <?php print $vswoo_settings->get_option_value( 'vswoo_popup_box_btn_cancel_text' ); ?>
            </div>
            <div class="button vswoo_btn vswoo_btn_addtocart">
	            <?php print $vswoo_settings->get_option_value( 'vswoo_popup_box_btn_atc_text' ); ?>
            </div>
        </div>

        <div class="vswoo_popup_payment">
            <div class="vswoo_payment_title">
				<?php print $vswoo_settings->get_option_value( 'vswoo_popup_box_footer_title' ); ?>
            </div>

            <div class="vswoo_payment_items">
                <?php foreach ( $vswoo_settings->get_option_value( 'vswoo_popup_box_payment_icons', array() ) as $pm_icon ) :
                    printf( '<img class="vswoo_payment_item" alt="%1$s" src="%2$sassets/images/%1$s.ico">', $pm_icon, VSWOO_PLUGIN_URL );
                endforeach; ?>
            </div>
        </div>
    </div>
</div>
