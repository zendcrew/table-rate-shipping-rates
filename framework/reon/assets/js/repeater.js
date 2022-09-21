(function ($) {
    "use strict";
    $.fn.rn_repeater = function (options) {
        return this.each(function () {
            // Default options.
            var settings = $.extend({
                accordion: true,
                select_cloned: false,
                select_default: false,
                select_new: false,
                select_all: false,
                max_sections: 1000,
                max_sections_msg: 'Too many sections',
                selectors: {
                    repeater_container: '.rn-repeater',
                    sections_container: '.rn-repeater-sections',
                    section: '.rn-repeater-section',
                    section_head: '.rn-repeater-section-head',
                    section_head_subcontainers: '.rn-repeater-head-controls,.rn-repeater-head-buttons',
                    active_section: '.rn-active-section',
                    delete_button: '.rn-repeater-head-delete',
                    clone_button: '.rn-repeater-head-duplicate',
                    sortable_button: '.rn-repeater-head-sort',
                    sortable_section: '.rn-sortable-repeater',
                    columns_head: '.rn-repeater-head',
                    section_head: '.rn-repeater-section-head',
                    section_body: '.rn-repeater-section-body',
                    section_body_inner: '.rn-repeater-section-inner',
                    option_parents_path: '.rn-container,.rn-repeater-pfx',
                    section_body_app_path: '.rn-ui-subapp',
                    repeater_footer: '.rn-repeater-template-adder',
                    repeater_footer_adder_list: '.rn-repeater-template-list',
                    repeater_footer_adder_btn: '.rn-repeater-template-btn',
                    repeater_templates_container: '.rn-repeater-templates'

                },
                css_classes: {
                    collapsible_repeater: 'rn-repeater-collapsible',
                    simple_repeater: 'rn-simple-repeater',
                    template_container: 'rn-repeater-templates',
                    sortable_state: 'rn-sortable-repeater-state',
                    panel_repeater: 'rn-repeater-panel',
                    active_section: 'rn-active-section',
                    repeater_clickable: 'rn-repeater-clickable'
                },
                columns_head_ready: null,
                section_header_ready: null,
                section_body_ready: null,
                section_added: null,
                section_cloned: null,
                section_expanded: null,
                section_collapsed: null,
                section_removed: null,
                update_input_attribute: null,
                footer_ready: null,
                get_input_display_text: null,
            }, options);

            var obj = $(this);
            var opt = settings;
            prepere_options(obj);
            init();


            function init() {
                sortable_repeater(obj.children(opt.selectors.sortable_section));
                process_sections_order(obj.children(opt.selectors.sections_container).children(opt.selectors.section));
                obj.children(opt.selectors.sections_container).children(opt.selectors.section).each(function () {
                    process_section($(this));
                });
                process_repeater_footer(obj.children(opt.selectors.repeater_footer));


                if (opt.select_all == true) {
                    obj.children(opt.selectors.sections_container).children(opt.selectors.section).each(function () {
                        expand_section($(this), false);
                    });
                } else if (!obj.hasClass(opt.css_classes.panel_repeater) && opt.select_default == true) {

                    var is_collapsible = obj.hasClass(opt.css_classes.collapsible_repeater);

                    if (is_collapsible == true) {
                        obj.children(opt.selectors.sections_container).children(opt.selectors.section).first().each(function () {
                            select_section($(this));
                        });
                    } else {
                        obj.children(opt.selectors.sections_container).children(opt.selectors.section).each(function () {
                            select_section($(this));
                        });
                    }

                }





                obj.children(opt.selectors.columns_head).each(function () {
                    var col_head = $(this);
                    if ($.isFunction(opt.columns_head_ready)) {
                        opt.columns_head_ready(col_head);
                    }
                });




            }

            function sortable_repeater(sections_container) {
                var srtbl = {
                    handle: opt.selectors.sortable_button,
                    placeholder: opt.css_classes.sortable_state,
                    axis: "y",
                    opacity: 0.7,
                    revert: true,
                    update: function (event, ui) {
                        process_sections_order(sections_container.children(opt.selectors.section));
                    }
                }
                if (sections_container.attr('data-sortable-connectwith') && sections_container.attr('data-sortable-connectwith') != '') {
                    srtbl.connectWith = '.' + sections_container.attr('data-sortable-connectwith');
                }
                sections_container.sortable(srtbl);
            }

            function process_section(section) {
                section.children(opt.selectors.section_head).each(function () {
                    process_section_head($(this));
                });

                var s_contents = section.children(opt.selectors.section_body).children(opt.selectors.section_body_inner).children(opt.selectors.section_body_app_path);
                if ($.isFunction(opt.section_body_ready)) {
                    opt.section_body_ready(s_contents);
                }

                section.find('input,textarea,select').each(function () {
                    var inp = $(this);
                    if (inp.attr('data-repeater-title') && inp.attr('data-repeater-title') == obj.attr('data-repeater-id')) {
                        inp.on('change', function () {
                            process_section_title(section, inp, get_input_value(inp));
                        });
                        if (inp[0].tagName != 'SELECT') {
                            inp.on('keyup', function () {
                                process_section_title(section, inp, get_input_value(inp));
                            });
                        }
                        process_section_title(section, inp, get_input_value(inp));
                    }

                    if (inp.attr('data-repeater-subtitle') && inp.attr('data-repeater-subtitle') == obj.attr('data-repeater-id')) {

                        inp.on('change', function () {
                            process_section_title(section, inp, get_input_value(inp));
                        });

                        inp.on('keyup', function () {
                            process_section_title(section, inp, get_input_value(inp));
                        });
                        process_section_title(section, inp, get_input_value(inp));
                    }

                });
            }

            function process_section_head(s_head) {
                s_head.disableSelection();

                var is_collapsible = s_head.parent().parent().parent().hasClass(opt.css_classes.collapsible_repeater);

                if (is_collapsible == true) {
                    s_head.on('click', function () {
                        var section = s_head.parent();
                        select_section(section);
                    });
                }
                s_head.find(opt.selectors.clone_button).on('click', function () {
                    if (get_can_add_section()) {
                        clone_section(s_head.parent());
                    } else {
                        prompt_cant_add_section();
                    }

                });

                s_head.find(opt.selectors.delete_button).on('click', function () {
                    remove_section(s_head.parent());
                });

                s_head.find(opt.selectors.section_head_subcontainers).on('click', function (e) {
                    if (e.target) {
                        var target = $(e.target);
                        if (target.hasClass(opt.css_classes.repeater_clickable)) {
                            return true;
                        }
                    }

                    return false;
                });

                init_dyn(s_head);

                if ($.isFunction(opt.section_header_ready)) {
                    opt.section_header_ready(s_head);
                }




                s_head.find('input,textarea,select').last().parents('.rn-field,.rn-group').addClass('rn-last');

            }

            function process_section_title(section, input, inp_value) {

                var rp_title = section.children('.rn-repeater-section-head').find('.rn-repeater-head-title');
                if (input.attr('data-repeater-title')) {
                    rp_title.attr('data-repeater-title', inp_value);
                }
                if (input.attr('data-repeater-subtitle')) {
                    rp_title.attr('data-repeater-subtitle', inp_value);
                }
                var rp_t = '';
                var rp_st = '';


                if (rp_title.attr('data-repeater-title')) {
                    rp_t = rp_title.attr('data-repeater-title');
                }

                if (rp_t == '' && rp_title.attr('data-repeater-default-title')) {
                    rp_t = rp_title.attr('data-repeater-default-title');
                }

                if (rp_title.attr('data-repeater-subtitle')) {
                    rp_st = rp_title.attr('data-repeater-subtitle');
                }

                if (rp_st == '' && rp_title.attr('data-repeater-default-subtitle')) {
                    rp_st = rp_title.attr('data-repeater-default-subtitle');
                }

                rp_title.html(rp_t + '<span>' + rp_st + '</span>');

            }

            function process_sections_order(sections) {
                var cnt = 0;
                sections.each(function () {
                    var section = $(this);
                    if (!obj.hasClass(opt.css_classes.panel_repeater)) {
                        section.attr('data-repeater-pfx', '[' + cnt + ']');
                        generate_input_attr(section);
                    }
                    cnt++;
                });

                set_section_count();
            }

            function remove_section(section) {
                section.fadeOut(300, function () {
                    section.remove();
                    if ($.isFunction(opt.section_removed)) {
                        opt.section_removed();
                    }
                    process_sections_order(obj.children(opt.selectors.sections_container).children(opt.selectors.section));
                });
            }

            function add_section(template) {
                var new_section = $(template.html()).appendTo(obj.children(opt.selectors.sections_container));
                var styles = '';
                if (new_section.attr('style'))
                {
                    styles = new_section.attr('style');
                }
                process_sections_order(obj.children(opt.selectors.sections_container).children(opt.selectors.section));
                new_section.addClass('rn-max-h');
                process_section(new_section);
                if ($.isFunction(opt.section_added)) {
                    opt.section_added(new_section);
                }
                new_section.hide().fadeIn(300, function () {
                    new_section.removeClass('rn-max-h');
                    process_added_section_dyn(new_section);
                    if (styles != '' && new_section.addAttr) {
                        new_section.addAttr('style', styles);
                    }
                    if (opt.select_new === true) {
                        select_section(new_section);
                    }
                });

            }

            function clone_section(section) {
                try {
                    var cloned = section.clone();
                    var styles = '';
                    if (cloned.attr('style'))
                    {
                        styles = cloned.attr('style');
                    }
                    cloned.removeClass(opt.css_classes.active_section);

                    cloned.insertAfter(section).hide().fadeIn(300, function () {
                        if (styles != '' && cloned.addAttr) {
                            cloned.addAttr('style', styles);
                        }
                        if (opt.select_cloned === true) {
                            select_section(cloned);
                        }
                        if ($.isFunction(opt.section_cloned)) {
                            opt.section_cloned(cloned);
                        }
                        process_sections_order(obj.children(opt.selectors.sections_container).children(opt.selectors.section));
                        process_section(cloned);
                    });
                } catch (excp) {

                }

            }

            function process_repeater_footer(footer) {

                footer.find(opt.selectors.repeater_footer_adder_btn).on('click', function (evt) {
                    evt.preventDefault();

                    if (get_can_add_section()) {
                        var tmp_list = footer.find(opt.selectors.repeater_footer_adder_list);

                        if (tmp_list.length > 0) {
                            var temp_id = tmp_list.find(":selected").val();
                            obj.children(opt.selectors.repeater_templates_container).children().each(function () {
                                var tmplt = $(this);
                                if (tmplt.attr('data-repeater-template-id') == temp_id) {
                                    add_section(tmplt);
                                    return false;
                                }
                            });
                        } else {
                            obj.children(opt.selectors.repeater_templates_container).children().each(function () {
                                add_section($(this));
                                return false;
                            });
                        }
                    } else {
                        prompt_cant_add_section();
                    }
                    return false;
                });
                if ($.isFunction(opt.footer_ready)) {
                    opt.footer_ready(footer);
                }
            }

            function select_section(section) {
                if (!obj.hasClass(opt.css_classes.simple_repeater)) {

                    if (opt.accordion == true) {

                        if (section.hasClass(opt.css_classes.active_section)) {
                            collapse_section(section);
                        } else {
                            expand_section(section, true);
                        }
                    } else {

                        toggle_section(section);
                    }
                }
            }

            function toggle_section(section) {
                if (section.hasClass(opt.css_classes.active_section)) {
                    collapse_section(section);
                } else {
                    expand_section(section, false);
                }
            }

            function expand_section(section, close_siblings) {
                if (close_siblings == true) {
                    section.siblings(opt.selectors.active_section).each(function () {
                        collapse_section($(this));
                    });
                }

                var styles = '';
                var c_h = section.outerHeight();
                if (section.attr('style')) {
                    styles = section.attr('style');
                }

                section.addClass(opt.css_classes.active_section);
                var h = section.outerHeight();
                section.css('overflow', 'hidden').css('height', c_h + 'px').animate({
                    height: h + 'px'
                }, 300, function () {
                    section.removeAttr('style');

                    if (styles != '' && section.addAttr) {
                        section.addAttr('style', styles);
                    }

                    if ($.isFunction(opt.section_expanded)) {
                        opt.section_expanded(section);
                    }
                });
            }

            function collapse_section(section) {
                var styles = '';
                var h = section.children(opt.selectors.section_head).outerHeight();
                if (section.attr('style')) {
                    styles = section.attr('style');
                }

                section.css('overflow', 'hidden').animate({
                    height: h + 'px'
                }, 300, function () {
                    section.removeClass(opt.css_classes.active_section);
                    section.removeAttr('style');
                    if (styles != '' && section.addAttr) {
                        section.addAttr('style', styles);
                    }

                    if ($.isFunction(opt.section_collapsed)) {
                        opt.section_collapsed(section);
                    }
                });
            }

            function prompt_cant_add_section() {
                alert(opt.max_sections_msg);
            }

            function get_can_add_section() {
                if (get_section_count() < opt.max_sections) {
                    return true;
                }
                return false;
            }

            function get_section_count() {
                if (obj.attr('data-sections_count')) {
                    return obj.attr('data-sections_count');
                }
                return 0;
            }

            function set_section_count() {
                var sctn_count = obj.children(opt.selectors.sections_container).children(opt.selectors.section).length;
                obj.attr('data-sections_count', sctn_count);
            }

            function prepere_options(opt_obj) {
                if (opt_obj.attr('data-repeater-accordion') && opt_obj.attr('data-repeater-accordion') != '') {
                    if (opt_obj.attr('data-repeater-accordion') == true || opt_obj.attr('data-repeater-accordion') == 'true') {
                        opt.accordion = true;
                    } else {
                        opt.accordion = false;
                    }
                }


                if (opt_obj.attr('data-repeater-select-all') && opt_obj.attr('data-repeater-select-all') != '') {
                    if (opt_obj.attr('data-repeater-select-all') == true || opt_obj.attr('data-repeater-select-all') == 'true') {

                        opt.select_all = true;
                    } else {
                        opt.select_all = false;
                    }
                }

                if (opt_obj.attr('data-repeater-select-cloned') && opt_obj.attr('data-repeater-select-cloned') != '') {
                    if (opt_obj.attr('data-repeater-select-cloned') == true || opt_obj.attr('data-repeater-select-cloned') == 'true') {
                        opt.select_cloned = true;
                    } else {
                        opt.select_cloned = false;
                    }
                }
                if (opt_obj.attr('data-repeater-select-default') && opt_obj.attr('data-repeater-select-default') != '') {
                    if (opt_obj.attr('data-repeater-select-default') == true || opt_obj.attr('data-repeater-select-default') == 'true') {
                        opt.select_default = true;
                    } else {
                        opt.select_default = false;
                    }
                }



                if (opt_obj.attr('data-repeater-select-new') && opt_obj.attr('data-repeater-select-new') != '') {
                    if (opt_obj.attr('data-repeater-select-new') == true || opt_obj.attr('data-repeater-select-new') == 'true') {
                        opt.select_new = true;
                    } else {
                        opt.select_new = false;
                    }
                }

                if (opt_obj.attr('data-max_sections') && opt_obj.attr('data-max_sections') != '') {
                    opt.max_sections = opt_obj.attr('data-max_sections');
                }

                if (opt_obj.attr('data-max_sections_msg')) {
                    opt.max_sections_msg = opt_obj.attr('data-max_sections_msg');
                }

            }

            function generate_input_attr(section) {
                section.find('input,textarea,select').each(function () {
                    var inpt = $(this);

                    if (inpt.attr('id')) {
                        inpt.removeAttr('id');
                    }
                    if (inpt.attr('name')) {
                        inpt.removeAttr('name');
                    }
                    if (input_is_template(inpt) == false) {
                        if (inpt.attr('data-repeater-name')) {
                            var attr = '';
                            inpt.parents(opt.selectors.option_parents_path).each(function () {
                                var pr = $(this);
                                if (pr.attr('data-repeater-pfx')) {
                                    attr = pr.attr('data-repeater-pfx') + attr;
                                }
                            });
                            var name_attr = attr + '[' + inpt.attr('data-repeater-name') + ']';
                            if (inpt.attr('data-repeater-name-i')) {
                                name_attr = name_attr + '[' + inpt.attr('data-repeater-name-i') + ']';
                            }
                            var id_attr = generate_input_id_from_value(name_attr, inpt.parents(opt.selectors.option_parents_path));

                            if ($.isFunction(opt.update_input_attribute)) {
                                opt.update_input_attribute(inpt, name_attr, 'name');
                                opt.update_input_attribute(inpt, id_attr, 'id');
                            } else {
                                inpt.attr('name', name_attr);
                                inpt.attr('id', id_attr);
                            }
                        }
                    }
                });

                section.find('input,textarea,select').each(function () {
                    var inpt = $(this);
                    if (inpt.attr('data-repeater-in-template')) {
                        if (inpt.attr('id')) {
                            inpt.removeAttr('id');
                        }
                        if (inpt.attr('name')) {
                            inpt.removeAttr('name');
                        }
                        inpt.removeAttr('data-repeater-in-template');
                    }
                });

            }

            function generate_input_id_from_value(value, parents) {
                var first_pfx = '';
                if (parents.length > 0) {
                    if ($(parents[parents.length - 1]).attr('data-repeater-pfx')) {
                        first_pfx = $(parents[parents.length - 1]).attr('data-repeater-pfx');
                    }
                }
                return value.replace(/\[|\]/g, '').replace(first_pfx, '');
            }

            function input_is_template(input) {

                input.parents(opt.selectors.repeater_templates_container).each(function () {
                    input.attr('data-repeater-in-template', 'yes');
                    return true;
                });
                return false;
            }

            function get_input_value(input) {
                if ($.isFunction(opt.get_input_display_text)) {
                    return opt.get_input_display_text(input);
                } else {
                    if (input[0].tagName == 'SELECT') {
                        var rsl = '';
                        input.find(':selected').each(function () {
                            var option = $(this);
                            if (rsl != '')
                                rsl != ', ';

                            rsl += option.text();
                        });
                        return rsl;
                    } else {
                        return input.val();
                    }
                }
            }

            function init_dyn(contents) {
                contents.find('.rn-dyn-switcher select').each(function () {
                    var switcher = $(this);
                    contents.find('.rn-dyn-switcher select').on('change', function () {
                        update_switcher_contents(switcher);
                    });

                });

            }

            function process_added_section_dyn(section) {
                section.find('.rn-dyn-switcher select').each(function () {
                    var switcher = $(this);
                    update_switcher_contents(switcher);
                });
            }

            function update_switcher_contents(switcher) {
                var contents = get_dyn_contents(switcher.val());

                if (switcher.attr('data-dyn_switcher_width')) {
                    contents.css('width', switcher.attr('data-dyn_switcher_width'));
                }
                var switcher_parent = switcher.parents('.rn-repeater-head-controls');
                switcher_parent.find('.rn-field-wrapper').each(function () {
                    var others = $(this);
                    if (others.attr('data-dyn_switcher_target') == switcher.attr('data-dyn_switcher_id')) {
                        others.remove();
                    }
                });
                if (contents.length == 0) {
                    return;
                }
                switcher.parents('.rn-repeater-section-head').addClass('rn-head-max-h');
                switcher_parent.append(contents);

                contents.hide();
                if (switcher.attr('data-dyn_switcher_exclude')) {
                    remove_dyn_exclude(switcher, switcher_parent);
                }

                process_sections_order(obj.children(opt.selectors.sections_container).children(opt.selectors.section));

                if ($.isFunction(opt.section_header_ready)) {
                    opt.section_header_ready(contents);
                }

                contents.fadeIn(300, function () {
                    switcher.parents('.rn-repeater-section-head').removeClass('rn-head-max-h');
                });

            }

            function remove_dyn_exclude(switcher, contents) {
                var excludes = switcher.attr('data-dyn_switcher_exclude').split(',');
                for (var i = 0; i < excludes.length; i++) {
                    var fld_id = excludes[i];
                    contents.find('.rn-field-wrapper').each(function () {
                        var dyn_field = $(this);
                        if (dyn_field.attr('data-dyn_field_id') && dyn_field.attr('data-dyn_field_id') == fld_id) {
                            dyn_field.remove();
                        }
                    });
                }
            }

            function get_dyn_contents(dyn_ref) {
                var dyn_cont = $('.rn-dynamic-contents').find('#dyn_' + dyn_ref);
                if (dyn_cont.length > 0) {
                    return $(dyn_cont.html());
                }
                return '';
            }
        });
    };

}(jQuery));