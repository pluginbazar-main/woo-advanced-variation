;(function ($, window, document, wav_object) {

    "use strict";


    /**
     * Reset Variation on Single Product Page
     */
    $(document).on('click', ".reset_variations", function () {
        $('.wav-variation-swatches').find('.wav-attribute-content').removeClass('attribute-selected');
    })


    /**
     * Change selection on Single Product Page
     */
    $(document).on('click', ".wav-attribute-content", function () {

        var attribute_val = $(this).data('attribute_val');

        $(this).parent().find('select').val(attribute_val);

        $(this).parent().find('.wav-attribute-content').removeClass('attribute-selected');
        $(this).addClass('attribute-selected');

        $('.variations_form').trigger('check_variations');
    })


    /**
     * Add to cart on Popup
     */
    $(document).on('click', ".wav_popup_container .wav_btn_addtocart", function () {

        var product_id = $(this).attr('variation_id'), __HTML__ = $(this).html();

        if (typeof product_id === 'undefined' || product_id == 0) {

            product_id = $('.wav_default_variation_id').attr('variation_id');
            if (typeof product_id === 'undefined' || product_id == 0) return;
        }

        $(this).html('Adding...');

        $.ajax(
            {
                type: 'POST',
                context: this,
                url: wav_object.wav_ajaxurl,
                data: {
                    "action": "wav_ajax_add_to_cart",
                    "product_id": product_id,
                },
                success: function (response) {
                    if (response.success) {
                        $(this).html(__HTML__);
                        window.location.replace(response.data);
                    } else {
                        $(this).html(response.data);
                    }
                }
            });

    })


    /**
     * Update price on Popup upon Attribute selection
     */
    $(document).on('click', ".wav_popup_container .wav_popup_section .section_meta", function () {

        $('.wav_variation_selection .wav_btn_addtocart').attr('variation_id', 0);
        $('.wav_variation_selection .section_price .section_content').html('Loading...');

        $(this).parent().find('.attribute-selected').removeClass('attribute-selected');
        $(this).addClass('attribute-selected');

        var product_id = $('.wav_variation_selection').attr("product_id"),
            selection = {};

        $(".wav_popup_section").each(function (index) {

            var _name = $(this).attr('name'),
                _value = $(this).find('.attribute-selected').attr('value');

            if (typeof _name === 'undefined' || _name.length == 0 || typeof _value === 'undefined' || _value.length == 0) {
            } else {
                selection["attribute_" + _name] = _value;
            }
        })

        $.ajax(
            {
                type: 'POST',
                context: this,
                url: wav_object.wav_ajaxurl,
                data: {
                    "action": "wav_ajax_load_selection_price",
                    "product_id": product_id,
                    "selection": selection,
                },
                success: function (response) {

                    if (response.success) {
                        $('.wav_variation_selection .wav_btn_addtocart').attr('variation_id', response.data.variation_id);
                        $('.wav_variation_selection .section_price .section_content').html(response.data.html);
                    }
                }
            });


    })


    /**
     * Open Popup on Shop Page
     */
    $(document).on('click', ".product_type_variable", function () {

        var product_id = $(this).data('product_id');

        if (typeof product_id === 'undefined' || product_id.length == 0) return true;

        $('.wav_variation_selection').fadeIn();

        $.ajax(
            {
                type: 'POST',
                context: this,
                url: wav_object.wav_ajaxurl,
                data: {
                    "action": "wav_ajax_load_variation_selection_box",
                    "product_id": product_id,
                },
                success: function (response) {

                    $('.wav_variation_selection').attr("product_id", product_id);

                    if (response.success) {
                        $('.wav_variation_selection .wav_popup_content').html(response.data);
                    }
                }
            });

        return false;
    })


    /**
     * Close Popup on click button
     */
    $(document).on('click', ".wav_variation_selection .wav_btn_close", function () {
        $('.wav_variation_selection').fadeOut();
        $('.wav_variation_selection .wav_popup_content').html("<span class='dashicons dashicons-admin-generic dashicons-spin'></span>");
        $('.wav_variation_selection .wav_btn_addtocart').attr('variation_id', 0);
    })

})(jQuery, window, document, wav_ajax );





