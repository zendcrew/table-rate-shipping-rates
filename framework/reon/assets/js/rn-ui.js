//jquery-1.12.0.js
jQuery(document).ready(function($) {
    "use strict";
    rn_init($('.rn-ui-app'));
    function rn_init(rn_ui_app) {
        rn_ui_app.children('.rn-ui-section').each(function() {
            var ui_section = $(this);
            if (ui_section.hasClass('rn-repeater-ui')) {
                ui_section.children('.rn-ui-block').children('.rn-field-wrapper').children('.rn-repeater').rn_repeater({
                    section_header_ready: function(content) {
                        rn_ui(content);
                    },
                    columns_head_ready: function(content) {
                        rn_ui(content);
                    },
                    section_body_ready: function(content) {
                        rn_init(content);
                    },
                    section_added: function(content) {
                        $('.rn-option-page').trigger('rn_ui_content_added', content);
                        $('.rn-option-page').trigger('rn_ui_change');
                    },
                    section_cloned: function(content) {
                        rn_clean_ui(content);
                        $('.rn-option-page').trigger('rn_ui_content_added', content);
                        $('.rn-option-page').trigger('rn_ui_change');
                    },
                    footer_ready: function(content) {
                        rn_ui(content);
                    },
                    section_collapsed: function() {
                        $('.rn-option-page').trigger('rn_ui_change');
                    },
                    section_expanded: function() {
                        $('.rn-option-page').trigger('rn_ui_change');
                    },
                    section_removed: function() {
                        $('.rn-option-page').trigger('rn_ui_change');
                    },
                    update_input_attribute: function(input, value, attr) {
                        if (attr == 'id' && input.hasClass('rn-opt-multi')) {
                            input.attr('data-opt-id', value);
                            if (input.attr('data-opt-active') && (input.attr('data-opt-active') == true || input.attr('data-opt-active') == 'true')) {
                                input.attr('id', value);
                            }
                        } else if (attr == 'name' && input.hasClass('rn-opt-multi')) {
                            input.attr('data-opt-name', value);
                            if (input.attr('data-opt-active') && (input.attr('data-opt-active') == true || input.attr('data-opt-active') == 'true')) {
                                input.attr('name', value);
                            }
                        } else {
                            input.attr(attr, value);
                        }
                    },
                });

                if (ui_section.hasClass('rn-fold')) {
                    ui_section.foldjs({
                        fold_changed: function() {
                            $('.rn-option-page').trigger('rn_ui_change');
                        }
                    });
                }
            } else {
                rn_ui(ui_section);
            }

        });
    }

    function rn_ui(el) {

//ColorPicker
//===========
        el.find('.rn-spectrum input').each(function() {
            var sp = $(this);
            var sp_width = 120;
            var sp_w = sp.css('width');
            sp.spectrum({
                containerClassName: 'rn-sp-container',
                allowEmpty: true,
                showInput: true,
                showPalette: false,
                showAlpha: true,
                cancelText: "Cancel",
                chooseText: "Done",
                showInitial: true,
                preferredFormat: 'hex',
                move: function(color) {
                    try {
                        if (color.getAlpha() == 1) {
                            sp.val(color.toHexString());
                        } else {
                            sp.val(color.toRgbString());
                        }
                        sp.trigger('change');
                    } catch (e) {

                    }

                },
                show: function() {
                    sp.data('specrum', 1);
                    sp.data('specrum_color', sp.val());
                },
                hide: function() {
                    if (sp.data('specrum') == 1) {
                        sp.val(sp.data('specrum_color'));
                        sp.trigger('change');
                    }
                },
                change: function(color) {
                    try {
                        if (color.getAlpha() == 1) {
                            sp.val(color.toHexString());
                        } else {
                            sp.val(color.toRgbString());
                        }
                        sp.trigger('change');
                    } catch (e) {

                    }
                    sp.data('specrum', 0);
                }
            });
            sp.siblings('.sp-replacer').find('.sp-dd').html(sp.attr('title'));
            if (sp_w != '0px') {
                sp_width = sp_w;
            }
            sp.siblings('.sp-replacer').find('.sp-preview').css('width', sp_width);
        });
        //========
        //Select2
        //========
        el.find('.rn-select2 select').each(function() {
            var slct = $(this);
            var opt = {
                dropdownCssClass: 'rn-container'
            };
            if (slct.attr('data-minimum-results-forsearch')) {
                opt.minimumResultsForSearch = parseInt(slct.attr('data-minimum-results-forsearch'));
            }
            if (slct.attr('data-ajaxsource')) {
                opt.ajax = {
                    cache: true,
                    data: function(params) {

                        var query = {
                            term: params.term || '',
                            ajaxsource: slct.attr('data-ajaxsource'),
                            action: 'rn_get_data_list_ajax'
                        };

                        if (slct.attr('data-ajaxsource_pagesize')) {
                            query.pagesize = slct.attr('data-ajaxsource_pagesize');
                        }

                        if (slct.attr('data-ajaxsource_show_value')) {
                            query.ajaxsource_show_value = slct.attr('data-ajaxsource_show_value');
                        }

                        if (slct.attr('data-ajaxsource_disabled_filter')) {
                            query.ajaxsource_disabled_filter = slct.attr('data-ajaxsource_disabled_filter');
                        }

                        if (slct.attr('data-ajaxsource_value_col')) {
                            query.ajaxsource_value_col = slct.attr('data-ajaxsource_value_col');
                        }

                        if (slct.attr('data-ajaxsource_value_col_pre')) {
                            query.ajaxsource_value_col_pre = slct.attr('data-ajaxsource_value_col_pre');
                        }

                        return query;
                    },
                    transport: function(params, success, failure) {
                        var data = params.data;
                        rn_send_request(data, function(result) {
                            success(result, params);
                        }, failure);
                    },
                    processResults: function(data, params) {
                        params.term = params.term || '';
                        var dta = {results: []};
                        for (var i = 0; i < data.results.length; i++) {
                            var search = new RegExp(params.term.trim(), 'i');
                            if (data.results[i].text.trim().match(search)) {
                                data.results[i].text = data.results[i].text.replace('&amp;', '&');
                                dta.results.push(data.results[i]);
                            }
                        }
                        return dta;
                    }
                };
            }
            slct.select2(opt);

            var deft_style = slct.parents('.rn-select2').find('.select2-search__field').attr('style');
            var deft_style_wdth = '';
            if (deft_style) {
                deft_style_wdth = deft_style.replace(' ', '');
            }
            if (deft_style_wdth == 'width:100px;' || deft_style_wdth == 'width:0px;') {
                slct.parents('.rn-select2').find('.select2-search__field').removeAttr('style');
            }

            slct.on('change', function() {
                var select_val = slct.val();

                if ($.type(select_val) == 'array') {
                    for (var i = 0; i < select_val.length; i++) {
                        slct.find('option').each(function() {
                            var opt = $(this);
                            if (opt.attr('value') == select_val[i]) {
                                opt.attr('data-rd_slt', 'on');
                            }
                        });

                    }
                } else if ($.type(select_val) == 'string') {
                    slct.find('option').each(function() {
                        var opt = $(this);
                        if (opt.attr('value') == select_val) {
                            opt.attr('data-rd_slt', 'on');
                        }
                    });
                }

                slct.find('option').each(function() {
                    var opt = $(this);
                    if (opt.attr('data-rd_slt') == 'on') {
                        opt.attr('selected', 'selected');
                        opt.removeAttr('data-rd_slt');
                    } else {
                        opt.removeAttr('selected');
                    }
                });

                $('.rn-option-page').trigger('rn_ui_change');
            });
        });
        //=========
        //ButtonSet
        //=========
        el.find('.rn-btnset').buttonset();





        //==============
        //DateTimePicker
        //==============
        el.find('.rn-datetimepicker').rn_datetime();

        //Spinner
        //===========
        el.find('.rn-spinner input').each(function() {
            var sp = $(this);


            sp.on('change', function() {
                if (sp.attr('data-offset') == 'false') {
                    if (sp.attr('data-max') && sp.val() > sp.attr('data-max')) {
                        sp.val(sp.attr('data-max'));
                    }
                    if (sp.attr('data-min') && sp.val() < sp.attr('data-min')) {
                        sp.val(sp.attr('data-min'));
                    }
                }
            });
            sp.spinner({
                min: sp.attr('data-min'),
                max: sp.attr('data-max'),
                step: sp.attr('data-step'),
                start: sp.val()
            });
            sp.parent().find('.ui-icon').removeAttr('style').html('');
        });

        //===========
        //noUI Slider
        //===========
        el.find('.rn-noui').noui();

        //======
        //AutoId
        //======
        el.find('.rn-ui-autoid').rn_autoid();


        //======
        //TipTip 
        //======
        el.find('.rn-tips').each(function() {
            var tptp = $(this);
            var opt = {maxWidth: "auto", edgeOffset: 10, defaultPosition: 'top',
                enter: function() {
                    $('#tiptip_holder').attr('data-rn-tooltip', 'yes');
                }};
            if (tptp.attr('data-position') && tptp.attr('data-position') != '') {
                opt.defaultPosition = tptp.attr('data-position');
            }
            tptp.tipTip(opt);
        });



        //================
        //Fold Elements
        //================
        if (el.hasClass('rn-fold')) {
            el.foldjs();
        }
        el.find('.rn-fold').foldjs({
            fold_changed: function() {
                $('.rn-option-page').trigger('rn_ui_change');
            }
        });

    }

    function rn_clean_ui(el) {
        //=============
        //Clean Select2
        //=============
        el.find('.rn-select2 select').removeClass('select2-hidden-accessible');
        el.find('.select2-container').remove();

        //====================
        //Clean DateTimePicker
        //====================
        el.find('.rn-datetimepicker').removeClass('hasDatepicker');


        //=============
        //Clean Spinner
        //=============
        var sp = el.find('.rn-spinner .ui-spinner input').unwrap();
        sp.removeAttr('aria-valuemin');
        sp.removeAttr('aria-valuemax');
        sp.removeAttr('aria-valuenow');
        sp.removeAttr('autocomplete');
        sp.removeAttr('role');
        sp.removeAttr('name');
        sp.removeAttr('id');
        sp.removeClass('ui-spinner-input');
        el.find('.rn-spinner a').remove();

        //=============
        //Clean Slider
        //=============
        el.find('.rn-slider-track .rn-noui').removeClass('noUi-background').removeClass('noUi-target').removeClass('noUi-ltr').removeClass('noUi-rtl').removeClass('noUi-ltr').removeClass('noUi-horizontal');
        el.find('.rn-slider-track .noUi-base').remove();


        //======
        //AutoId
        //======
        el.find('.rn-ui-autoid').val('');

    }

    function rn_send_request(data, onsuccess, onerror) {
        jQuery.post(ajaxurl, data, function(response, status, xhr) {
            onsuccess(response, status, xhr);
        }).fail(function() {
            onerror();
        });
    }












});
