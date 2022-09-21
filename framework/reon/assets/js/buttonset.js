(function ($) {
"use strict";
    $.fn.buttonset = function (options) {



        return this.each(function () {
            // Default options.
           
            var settings = $.extend({
                multiple: false,
            }, options);

            var obj = $(this);
            var opt = settings;
            prepere_options(obj);

            obj.find('.rn-opt-set').disableSelection();

            if (opt.multiple == true) {
                obj.find('.rn-opt-set').on('click', function (event) {
                    event.preventDefault();
                    var btn = $(this);
                    if (btn.hasClass('opt-off')) {
                        btn.addClass('active');
                        obj.find('input').each(function () {
                            var inpt = $(this);
                            if (inpt.hasClass('rn-opt-empty')) {
                                inpt.attr('id', inpt.attr('data-opt-id'));
                                inpt.attr('name', inpt.attr('data-opt-name'));
                                inpt.attr('data-opt-active', true);
                                inpt.val(inpt.attr('data-value'));
                                inpt.trigger('change');
                            } else {
                                btn.siblings().removeClass('active');
                                inpt.removeAttr('id');
                                inpt.removeAttr('name');
                                inpt.attr('data-opt-active', false);
                                inpt.val('');
                                inpt.trigger('change');
                            }
                        });
                    } else {

                        if (btn.hasClass('active')) {
                            btn.removeClass('active');
                            var btn_id = btn.attr('data-optbtn');
                            obj.find('input').each(function () {
                                var inpt = $(this);
                                if (inpt.attr('data-optbtn') == btn_id) {
                                    inpt.removeAttr('id');
                                    inpt.removeAttr('name');
                                    inpt.attr('data-opt-active', false);
                                    inpt.val('');
                                    inpt.trigger('change');
                                }
                            });

                            if (obj.find('.active').length <= 0) {
                                btn.siblings('.opt-off').addClass('active');
                                var empt = obj.find('.rn-opt-empty');
                                empt.attr('id', empt.attr('data-opt-id'));
                                empt.attr('name', empt.attr('data-opt-name'));
                                empt.attr('data-opt-active', true);
                                empt.val(empt.attr('data-value'));
                                empt.trigger('change');
                            }
                        } else {
                            btn.siblings('.opt-off').removeClass('active');
                            btn.addClass('active');
                            var btn_id = btn.attr('data-optbtn');
                            obj.find('input').each(function () {
                                var inpt = $(this);
                                if (inpt.attr('data-optbtn') == btn_id) {
                                    inpt.attr('id', inpt.attr('data-opt-id'));
                                    inpt.attr('name', inpt.attr('data-opt-name'));
                                    inpt.attr('data-opt-active', true);
                                    inpt.val(btn.attr('data-value'));
                                    inpt.trigger('change');
                                }
                            });

                            var empt = obj.find('.rn-opt-empty');
                            empt.removeAttr('id');
                            empt.removeAttr('name');
                            empt.attr('data-opt-active', false);
                            empt.val('');
                            empt.trigger('change');
                        }

                    }



                });
            } else {
                obj.find('.rn-opt-set').on('click', function (event) {
                    event.preventDefault();
                    var btn = $(this);
                    btn.siblings(".rn-opt-set").removeClass('active');
                    btn.addClass('active');

                    var inp = obj.find('.rn-opt-value');
                    if (inp.length == 1) {
                        inp.val(btn.attr('data-value'));
                        inp.trigger('change');
                    }
                });
            }


            function prepere_options(optobj) {
                if (optobj.attr('data-multiple') && optobj.attr('data-multiple') == 'true') {
                    opt.multiple = true;
                }
            }
        });


    };

}(jQuery));