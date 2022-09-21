(function($) {
    "use strict";
    $.fn.foldjs = function(options) {

        return this.each(function() {
            var settings = $.extend({
                parents_selector: '.rn-container,.rn-repeater-section',
                folded_class: 'rn-folded',
                fold_target: '',
                fold_target_attr: 'value',
                fold_value: [''],
                fold_comp: 'eq',
                fold_clear: false,
                fold_empty: '',
                fold_changed: null
            }, options);
            var obj = $(this);
            var opt = settings;
            prepere_options(obj);

            var fold_target;
            var should_fade = true;
            if (obj) {

                var fold_target = get_fold_target(opt.fold_target);

                fold_target.on('change', function() {
                    do_fold();

                });
                fold_target.on('rnv_change', function() {
                    do_fold();
                });
                do_fold();

            }

            function do_fold() {

                var fold_target_value;

                var obj_prev_value = obj.attr('data-fold-prev');

                if (opt.fold_target_attr == 'value') {

                    fold_target_value = fold_target.val();
                } else {

                    fold_target_value = fold_target.attr(opt.fold_target_attr);
                }


                if (!obj_prev_value) {

                    target_value_changed(fold_target_value);

                    obj.attr('data-fold-prev', fold_target_value);
                } else if (obj_prev_value != fold_target_value) {

                    target_value_changed(fold_target_value);

                    obj.attr('data-fold-prev', fold_target_value);
                }

            }

            function target_value_changed(new_value) {

                var changed = false;
                if ($.isArray(opt.fold_value)) {
                    for (var i = 0; i < opt.fold_value.length; i++) {
                        if (check_data(new_value, opt.fold_value[i]) == true) {
                            changed = true;
                        }
                    }

                } else if ($.type(opt.fold_value) === "string") {
                    if (check_data(new_value, opt.fold_value) == true) {
                        changed = true;
                    }
                }
                visible_changed(!changed);
            }

            function visible_changed(visible) {

                if (visible == true) {
                    if (should_fade == true) {

                        var bck_style = obj.attr('style');

                        obj.stop(true, true).fadeOut(300, function() {
                            obj.addClass(opt.folded_class);
                            obj.parents('.rn-fluid-width').addClass('rn-fluid-width-folded');
                            if (obj.hasClass('rn-ui-section')) {
                                process_prev_last();
                            }
                            if ($.isFunction(opt.fold_changed)) {
                                opt.fold_changed(obj);
                            }

                            obj.removeAttr('style');
                            if (bck_style) {
                                obj.attr('style', bck_style);
                            }
                        });
                    } else {
                        obj.addClass(opt.folded_class);
                        obj.parents('.rn-fluid-width').addClass('rn-fluid-width-folded');
                        if (obj.hasClass('rn-ui-section')) {
                            process_prev_last();
                        }
                        if ($.isFunction(opt.fold_changed)) {
                            opt.fold_changed(obj);
                        }
                    }

                    if (is_input() == true && opt.fold_clear == true) {

                        obj.val(opt.fold_empty);

                        obj.trigger('change');
                    }


                } else {
                    if (should_fade == true) {
                        var bck_style = obj.attr('style');
                        obj.stop(true, true).fadeIn(300, function() {
                            obj.removeClass(opt.folded_class);
                            obj.parents('.rn-fluid-width').removeClass('rn-fluid-width-folded');
                            if (obj.hasClass('rn-ui-section')) {
                                process_prev_last();
                            }
                            if ($.isFunction(opt.fold_changed)) {
                                opt.fold_changed(obj);
                            }
                            obj.removeAttr('style');
                            if (bck_style) {
                                obj.attr('style', bck_style);
                            }
                        });
                    } else {
                        obj.removeClass(opt.folded_class);
                        obj.parents('.rn-fluid-width').removeClass('rn-fluid-width-folded');
                        if (obj.hasClass('rn-ui-section')) {
                            process_prev_last();
                        }
                        if ($.isFunction(opt.fold_changed)) {
                            opt.fold_changed(obj);
                        }
                    }

                }
            }

            function process_prev_last() {

                var p_obj = obj.parent();

                p_obj.children().removeClass('rn-fold-last');

                var prev_obj;

                var p_chrildren = p_obj.children();

                var p_c_l = 0;
                if (p_chrildren.length) {
                    p_c_l = p_chrildren.length - 1;
                }
                var found = false;

                for (var i = p_c_l; i >= 0; i--) {
                    if (found == true) {
                        break;
                    }

                    var c_obj = $(p_chrildren[i]);
                    if (!c_obj.hasClass(opt.folded_class)) {
                        prev_obj = c_obj;
                        found = true;
                    }

                }

                if (found == true) {
                    prev_obj.addClass('rn-fold-last');
                }


            }

            function check_data(new_value, f_data) {

                if (opt.fold_comp == 'eq' && new_value == f_data) {
                    return true;
                }

                if (opt.fold_comp == 'neq' && new_value != f_data) {
                    return true;
                }

                if (opt.fold_comp == 'gt_eq' && parseFloat(new_value) >= parseFloat(f_data)) {
                    return true;
                }

                if (opt.fold_comp == 'lt_eq' && parseFloat(new_value) <= parseFloat(f_data)) {
                    return true;
                }

                if (opt.fold_comp == 'lt' && parseFloat(new_value) < parseFloat(f_data)) {
                    return true;
                }

                if (opt.fold_comp == 'gt' && parseFloat(new_value) > parseFloat(f_data)) {
                    return true;
                }

                return false;
            }

            function get_fold_target(target_id) {


                var parents = obj.parents(opt.parents_selector);
                for (var i = 0; i < parents.length; i++) {
                    var parent = $(parents[i]);
                    var target = parent.find('[data-fold-id="' + target_id + '"]');

                    if (target.length > 0) {
                        return $(target[0]);
                    }

                }

                return  $('#' + target_id);
            }

            function is_input() {

                var tagname = obj.prop("tagName").toLowerCase();
                if (tagname == 'input' || tagname == 'select' || tagname == 'textarea') {
                    return true;
                }
                return false;
            }


            function prepere_options(optobj) {

                if (optobj.attr('data-fold-target')) {
                    opt.fold_target = optobj.attr('data-fold-target');
                }

                if (optobj.attr('data-fold-target-attr')) {
                    opt.fold_target_attr = optobj.attr('data-fold-target-attr');
                }

                if (optobj.attr('data-fold-value')) {
                    var vl = get_fold_value(optobj.attr('data-fold-value'));
                    opt.fold_value = vl;
                } else {
                    opt.fold_value = [''];
                }

                if (optobj.attr('data-fold-comp')) {
                    opt.fold_comp = optobj.attr('data-fold-comp');
                }

                if (optobj.attr('data-fold-clear') && optobj.attr('data-fold-clear') == 'true') {
                    opt.fold_clear = true;
                }

                if (optobj.attr('data-fold-empty')) {
                    opt.fold_empty = optobj.attr('data-fold-empty');
                }
            }

            function get_fold_value(vl) {
                var str = vl.replace(/\[|\]|'/g, "");
                var ar = str.split(",");
                if (ar.length > 0) {
                    return ar;
                }
                return [''];
            }
        });
    };

}(jQuery));
