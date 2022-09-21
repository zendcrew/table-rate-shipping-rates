"use strict";
jQuery(document).ready(function ($) {
    if ($('.rn-metabox-wc,.rn-metabox').length > 0) {
        rn_metabox_init();
    }

    function rn_metabox_init() {

        $('#post').on('submit', function () {

            $('.rn-metabox-wc,.rn-metabox').each(function () {
                var rn_mta = $(this);

                var rn_mta_value = rn_mta.find('.reon_metabox_value');

                if (rn_mta_value.length > 0) {
                    rn_mta_value.val(JSON.stringify(rn_mta.serializeForm()));
                }
            });
        });

    }
});

