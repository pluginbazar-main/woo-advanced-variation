<?php
/**
 * Template: Dynamic Styles and Popup Box
 *
 * @copyright Pluginbazar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}  // if direct access


global $wav_settings;

?>
<div class="wav_popup_container wav_variation_selection">
    <div class="wav_popup_box">

        <div class="wav_popup_title">
			<?php print $wav_settings->get_option_value( 'wav_popup_box_title' ); ?>
        </div>

        <div class="wav_popup_content">
            <span class="dashicons dashicons-admin-generic dashicons-spin"></span>
        </div>

        <div class="wav_popup_buttons">
            <div class="button wav_btn wav_btn_close">
	            <?php print $wav_settings->get_option_value( 'wav_popup_box_btn_cancel_text' ); ?>
            </div>
            <div class="button wav_btn wav_btn_addtocart">
	            <?php print $wav_settings->get_option_value( 'wav_popup_box_btn_atc_text' ); ?>
            </div>
        </div>

        <div class="wav_popup_payment">
            <div class="wav_payment_title">
				<?php print $wav_settings->get_option_value( 'wav_popup_box_footer_title' ); ?>
            </div>

            <div class="wav_payment_items">
                <?php foreach ( $wav_settings->get_option_value( 'wav_popup_box_payment_icons', array() ) as $pm_icon ) :
                    printf( '<img class="wav_payment_item" alt="%1$s" src="%2$sassets/images/%1$s.ico">', $pm_icon, WAV_PLUGIN_URL );
                endforeach; ?>
            </div>
        </div>
    </div>
</div>
