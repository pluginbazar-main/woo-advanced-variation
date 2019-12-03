;(function ($, window, document, vswoo_object) {

    "use strict";

    /**
     * Reset Variation on Single Product Page
     */
    $(document).on('click', ".reset_variations", function () {
        $('.vswoo-variation-swatches').find('.vswoo-attribute-content').removeClass('attribute-selected');
    });


    /**
     * Change selection on Single Product Page
     */
    $(document).on('click', ".vswoo-attribute-content", function () {

        var attribute_val = $(this).data('attribute_val');

        $(this).parent().find('select').val(attribute_val);

        $(this).parent().find('.vswoo-attribute-content').removeClass('attribute-selected');
        $(this).addClass('attribute-selected');

        $('.variations_form').trigger('check_variations');
    });


    /**
     * Add to cart on Popup
     */
    $(document).on('click', ".vswoo_popup_container .vswoo_btn_addtocart", function () {

        var product_id = $(this).attr('variation_id'), __HTML__ = $(this).html();

        if (typeof product_id === 'undefined' || product_id == 0) {

            product_id = $('.vswoo_default_variation_id').attr('variation_id');
            if (typeof product_id === 'undefined' || product_id == 0) return;
        }

        $(this).html('Adding...');

        $.ajax(
            {
                type: 'POST',
                context: this,
                url: vswoo_object.vswoo_ajaxurl,
                data: {
                    "action": "vswoo_ajax_add_to_cart",
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

    });


    /**
     * Update price on Popup upon Attribute selection
     */
    $(document).on('click', ".vswoo_popup_container .vswoo_popup_section .section_meta", function () {

        $('.vswoo_variation_selection .vswoo_btn_addtocart').attr('variation_id', 0);
        $('.vswoo_variation_selection .section_price .section_content').html('Loading...');

        $(this).parent().find('.attribute-selected').removeClass('attribute-selected');
        $(this).addClass('attribute-selected');

        var product_id = $('.vswoo_variation_selection').attr("product_id"),
            selection = {};

        $(".vswoo_popup_section").each(function (index) {

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
                url: vswoo_object.vswoo_ajaxurl,
                data: {
                    "action": "vswoo_ajax_load_selection_price",
                    "product_id": product_id,
                    "selection": selection,
                },
                success: function (response) {

                    if (response.success) {
                        $('.vswoo_variation_selection .vswoo_btn_addtocart').attr('variation_id', response.data.variation_id);
                        $('.vswoo_variation_selection .section_price .section_content').html(response.data.html);
                    }
                }
            });


    });


    /**
     * Open Popup on Shop Page
     */
    $(document).on('click', ".product_type_variable", function () {

        var product_id = $(this).data('product_id');

        if (typeof product_id === 'undefined' || product_id.length == 0) return true;

        $('.vswoo_variation_selection').fadeIn();

        $.ajax(
            {
                type: 'POST',
                context: this,
                url: vswoo_object.vswoo_ajaxurl,
                data: {
                    "action": "vswoo_ajax_load_variation_selection_box",
                    "product_id": product_id,
                },
                success: function (response) {

                    $('.vswoo_variation_selection').attr("product_id", product_id);

                    if (response.success) {
                        $('.vswoo_variation_selection .vswoo_popup_content').html(response.data);
                    }
                }
            });

        return false;
    });


    /**
     * Close Popup on click button
     */
    $(document).on('click', ".vswoo_variation_selection .vswoo_btn_close", function () {

        $('.vswoo_variation_selection').fadeOut();
        $('.vswoo_variation_selection .vswoo_popup_content').html("<span class='dashicons dashicons-admin-generic dashicons-spin'></span>");
        $('.vswoo_variation_selection .vswoo_btn_addtocart').attr('variation_id', 0);
    });


    /**
     * Close Popup on click outside of Popup box
     */
    $(document).mouseup(function (e) {

        let container = $('.vswoo_popup_container .vswoo_popup_box');

        if (!container.is(e.target) && container.has(e.target).length === 0) {
            $('.vswoo_variation_selection').fadeOut();
            $('.vswoo_variation_selection .vswoo_popup_content').html("<span class='dashicons dashicons-admin-generic dashicons-spin'></span>");
            $('.vswoo_variation_selection .vswoo_btn_addtocart').attr('variation_id', 0);
        }
    });

})(jQuery, window, document, vswoo_ajax);





