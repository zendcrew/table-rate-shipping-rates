(function($) {
    "use strict";
    $.fn.rn_autoid = function(options) {



        return this.each(function() {
            // Default options.

            var settings = $.extend({
                autoid_id: 'autoid',
                autoid_prefix: '',
                autoid_suffix: '',
            }, options);

            var obj = $(this);
            var opt = settings;
            prepere_options(obj);


            if (obj.val() == '') {
                obj.val(get_autoid_value(settings.autoid_id, settings.autoid_prefix, settings.autoid_suffix));
            }

            function get_autoid_value(autoid, prefix, suffix) {
                var auto_value = 0;
                if (jQuery.data(document.body, autoid)) {
                    auto_value = jQuery.data(document.body, autoid);
                } else {
                    auto_value = get_date_int();
                }

                auto_value++;
                jQuery.data(document.body, autoid, auto_value);

                return prefix + auto_value + suffix;
            }

            function get_date_int() {
                var dt = new Date();
                var dt_str = '' + dt.getMonth();
                dt_str = dt_str + '' + dt.getDate();
                dt_str = dt_str + '' + dt.getHours();
                dt_str = dt_str + '' + dt.getMinutes();
                dt_str = dt_str + '' + dt.getSeconds();
                
                var dt_int = Number(dt_str);
                if (isNaN(dt_str)) {
                    dt_int = dt.getTime();
                }

                return dt_int;
            }

            function prepere_options(optobj) {
                if (optobj.attr('data-autoid_id') && optobj.attr('data-autoid_id') != '') {
                    opt.autoid_id = optobj.attr('data-autoid_id');
                }

                if (optobj.attr('data-autoid_prefix')) {
                    opt.autoid_prefix = optobj.attr('data-autoid_prefix');
                }
                if (optobj.attr('data-autoid_suffix')) {
                    opt.autoid_suffix = optobj.attr('data-autoid_suffix');
                }
            }
        });


    };

}(jQuery));

