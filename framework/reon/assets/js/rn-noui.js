(function ($) {
"use strict";
    $.fn.noui = function (options) {

        return this.each(function () {
            var settings = $.extend({
                start: 0,
                step: 1,
                range: {}
            }, options);

            var obj = $(this);
            var opt = settings;

            var units = obj.attr('data-units') ? (obj.attr('data-units')).split(",") : [];

            var inner_change = false;
            var update_change = false;


            var inputleft = obj.parents('.rn-slider').find('.rn-noui-left');
            var inputright = obj.parents('.rn-slider').find('.rn-noui-right');

            inner_change = true
            create(obj, inputleft, inputright);
            inner_change = false;
            var decimals = 0;

            inputleft.on('change',function () {
                if (update_change == false) {
                    inner_change = true;
                    destroy(obj);
                    create(obj, inputleft, inputright);
                    obj[0].noUiSlider.set([inputleft.val(), null]);
                    inner_change = false;
                }
            });

            inputright.on('change',function () {
                if (update_change == false) {
                    inner_change = true;
                    destroy(obj);
                    create(obj, inputleft, inputright);

                    obj[0].noUiSlider.set([null, inputright.val()]);
                    inner_change = false;
                }
            });




            function create(obj, inpleft, inpright) {
                prepere_options(obj, inpleft, inpright);
                noUiSlider.create(obj[0], opt);
                if (obj.hasClass('noUi-connect')) {
                    obj.removeClass('noUi-connect').find('.noUi-base').addClass('noUi-connect');
                }
                obj[0].noUiSlider.on('update', function (values, handle) {

                    var value = values[handle];
                    var unit = obj.attr('data-postfix') ? obj.attr('data-postfix') : '';
                    var inp = (!handle) ? inpleft : inpright;

                    var ivalue = sanitize_input_value(value, 0) + get_value_unit(value, unit);
                    if (inner_change == true) {
                        ivalue = sanitize_input_value(inp.val(), 0) + get_value_unit(value, unit);
                        if (obj.attr('data-empty') && inp.val() == '') {
                            ivalue = sanitize_input_value(obj.attr('data-empty'), 0) + unit;
                        }
                        if (!obj.attr('data-empty') && inp.val() == '') {
                            ivalue = '';
                        }
                    }
                    inp[0].value = ivalue;
                    update_change = true;
                    inp.trigger('change');
                    update_change = false;


                });
            }

            function destroy(obj) {
                obj[0].noUiSlider.destroy();
            }

            function prepere_options(optobj, inpleft, inpright) {
                var pfx = get_value_unit(inpleft.val(), optobj.attr('data-postfix'));
                var min = get_min(optobj, (pfx == '%') ? 'percent' : pfx);
                var max = get_max(optobj, (pfx == '%') ? 'percent' : pfx);
                var step = get_step(optobj, (pfx == '%') ? 'percent' : pfx);
                var margin = get_margin(optobj, (pfx == '%') ? 'percent' : pfx);
                var limit = get_limit(optobj, (pfx == '%') ? 'percent' : pfx);
                decimals = get_decimals(optobj, (pfx == '%') ? 'percent' : pfx);

                opt.step = step;
                opt.range = {'min': min, 'max': max};

                if (optobj.attr('data-connect') == 'true') {
                    opt.connect = true;
                } else if (optobj.attr('data-connect') == 'lower' || optobj.attr('data-connect') == 'upper') {
                    opt.connect = optobj.attr('data-connect');
                } else {
                    opt.connect = false;
                }
                if (optobj.hasClass('noui-range')) {
                    opt.margin = margin;
                    opt.limit = limit;
                }

                if (optobj.attr('data-behaviour') != '') {
                    opt.behaviour = optobj.attr('data-behaviour');
                }
                if (optobj.attr('data-direction') != '') {
                    opt.direction = optobj.attr('data-direction');
                }



                if (inpleft.length == 1 && inpright.length == 1) {
                    opt.start = [sanitize_input_value(inpleft.val(), 0), sanitize_input_value(inpright.val(), 50)];
                } else if (inpleft.length == 1) {
                    opt.start = sanitize_input_value(inpleft.val(), 0);
                } else {
                    opt.start = 0;
                }


                opt.format = window.wNumb({decimals: decimals, postfix: pfx});


            }

            function sanitize_input_value(invalue, default_value) {

                for (var i = 0; i < units.length; i++) {
                    if (invalue.endsWith(units[i])) {
                        return parseFloat(invalue.replace(units[i], ''));
                    }
                }
                if (invalue != '') {
                    return parseFloat(invalue);
                }
                return default_value;
            }

            function get_value_unit(invalue, default_unit) {
                if (default_unit == '') {
                    return '';
                }
                for (var i = 0; i < units.length; i++) {
                    if (invalue.endsWith(units[i])) {
                        return units[i];
                    }
                }

                return default_unit;
            }

            function get_min(obj, unit) {
                var mvalue = '';
                if (obj.attr('data-min-' + unit)) {
                    mvalue = obj.attr('data-min-' + unit);
                } else if (obj.attr('data-min')) {
                    mvalue = obj.attr('data-min');
                }

                return sanitize_input_value(mvalue, 0);
            }

            function get_max(obj, unit) {
                var mvalue = '';

                if (obj.attr('data-max-' + unit)) {
                    mvalue = obj.attr('data-max-' + unit);
                } else if (obj.attr('data-max')) {
                    mvalue = obj.attr('data-max');
                }
                return sanitize_input_value(mvalue, 200);
            }

            function get_step(obj, unit) {
                var mvalue = '';
                if (obj.attr('data-step-' + unit)) {
                    mvalue = obj.attr('data-step-' + unit);
                } else if (obj.attr('data-step')) {
                    mvalue = obj.attr('data-step');
                }
                return sanitize_input_value(mvalue, 1);
            }

            function get_margin(obj, unit) {
                var mvalue = '';
                if (obj.attr('data-margin-' + unit)) {
                    mvalue = obj.attr('data-margin-' + unit);
                } else if (obj.attr('data-margin')) {
                    mvalue = obj.attr('data-margin');
                }
                return sanitize_input_value(mvalue, 0);
            }

            function get_limit(obj, unit) {
                var mvalue = '';
                if (obj.attr('data-limit-' + unit)) {
                    mvalue = obj.attr('data-limit-' + unit);
                } else if (obj.attr('data-limit')) {
                    mvalue = obj.attr('data-limit');
                }
                return sanitize_input_value(mvalue, get_max(obj, unit));
            }

            function get_decimals(obj, unit) {
                var mvalue = '0';
                if (obj.attr('data-decimals-' + unit)) {
                    mvalue = obj.attr('data-decimals-' + unit);
                } else if (obj.attr('data-decimals')) {
                    mvalue = obj.attr('data-decimals');
                }
                return parseInt(mvalue);
            }

        });


    };

}(jQuery));