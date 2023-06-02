"use strict";
jQuery(document).ready(function ($) {
    //Select Default Tab
    var current = getSelectableLi();
    setSelecttion(current);

    init_import_export();
    init_wc_page();

    setTimeout(function () {
        init_rn_form_save_popup();
    }, 1000);

//Mouse Click Tab Selection
    $(".rn-option-page-tabs").find('a').each(function () {
        var anc_link = $(this);
        if (anc_link.attr('data-active-group')) {
            anc_link.click(function () {

                $(".rn-option-page-tabs").attr('data-active', anc_link.parent().attr('data-key'));
                var c = getSelectableLi();
                setSelecttion(c);
                return false;
            });
        }

    });


    $('.rn-option-page').on('rn_ui_change', function () {
        rn_adjust_aside();
    });

//Perform Tab Selection
    function setSelecttion(current) {
        var anidur = 300;
        var option_tabs = $(".rn-option-page-tabs");


        option_tabs.find('li').each(function () {
            $(this).removeClass('active');
            if ($(this).attr('data-key') == current) {
                $(this).addClass('active');

                var anc = $(this).find('a').attr('href');
                $('.rn-option-page-group').each(function () {
                    if ('#' + $(this).attr('id') != anc) {
                        $(this).hide().removeClass('active-group');
                    } else {
                        $('.rn-reset-section, .rn-save-section').attr('data-active-group', $(anc).attr('id'));
                        $(anc).fadeIn(200, function () {
                            $(anc).addClass('active-group');
                            rn_adjust_aside();
                        });
                    }

                });

                if ($(this).hasClass('rn-option-subli')) {
                    var parentli = $(this).parents('.rn-option-li');
                    if (parentli.hasClass('activeparent') != true) {
                        deSelectSiblings(parentli, anidur);
                        parentli.addClass('activeparent');
                        var parent = parentli.find('.rn-option-page-tabs-sublist');
                        parent.removeAttr('style').css('display', 'block');
                        var h = parent.outerHeight() + 'px';
                        parent.css('height', '0px').animate({height: h}, anidur, function () {
                            parent.removeAttr('style').css('display', 'block');
                        });
                    }
                } else {
                    deSelectSiblings($(this), anidur);
                    if ($(this).hasClass('haschild') == true) {
                        $(this).addClass('activeparent');
                        var parent = $(this).find('.rn-option-page-tabs-sublist');
                        parent.removeAttr('style').css('display', 'block');
                        var h = parent.outerHeight() + 'px';
                        parent.css('height', '0px').animate({height: h}, anidur, function () {
                            parent.removeAttr('style').css('display', 'block');
                        });
                    }
                }
            }
        });


        $('.rn-save-all, .rn-save-section').on('click', function () {
            rn_save_option($(this));



            $('.rn-option-page').parents('.rn-woocommerce').each(function () {
                var wc_cont = $(this);

                if (wc_cont.hasClass('rn-no-wc-save')) {
                    wc_cont.find('.woocommerce-save-button').click();
                }

            });



            return false;
        });

        $('.rn-reset-all, .rn-reset-section').on('click', function () {
            rn_reset_option($(this));
            return false;
        });


    }

//Hide other subtabs
    function deSelectSiblings(currentli, anidur) {
        currentli.siblings('li').each(function () {
            var sbl = $(this);
            if (sbl.hasClass('activeparent') == true) {
                sbl.removeClass('activeparent');
                var childul = sbl.find('.rn-option-page-tabs-sublist');
                childul.removeAttr('style').css('display', 'block');
                var h = childul.outerHeight() + 'px';
                childul.css('height', h).animate({height: '0px'}, anidur, function () {
                    childul.removeAttr('style').css('display', 'none');
                });
            }
        });
    }

//Correct Selected Index
    function getSelectableLi() {
        var key = $(".rn-option-page-tabs").attr('data-active');
        var fkey = 0;
        $(".rn-option-page-tabs").find('.rn-option-li').each(function () {
            var option_li = $(this);

            if (option_li.attr('data-key') == key) {

                if (option_li.hasClass("empty-group")) {
                    fkey = option_li.find('.rn-option-subli').first().attr('data-key');
                } else {
                    fkey = option_li.attr('data-key');
                }
            }
        });
        if (fkey == 0) {
            $(".rn-option-page-tabs").find('.rn-option-subli').each(function () {
                var option_li = $(this);
                if (option_li.attr('data-key') == key) {
                    fkey = option_li.attr('data-key');
                }
            });
        }
        return fkey;
    }




    function rn_adjust_aside() {

        var aside = $('.rn-option-page-aside');
        var w = aside.outerWidth();
        aside.removeAttr('style');
        aside.css('width', w + 'px');
        var main_h = $('.rn-option-page-body').outerHeight();
        var aside_h = aside.outerHeight();

        if (main_h > aside_h) {
            var h = main_h + 2;
            aside.css('height', h + 'px');
        }
    }

    function rn_save_option(btn) {

        var section_id = '';
        if (btn.attr('data-active-group')) {
            section_id = btn.attr('data-active-group');
        }

        var data = {
            section_id: section_id,
            action: 'rn_save_option',
            option_name: $('.rn-option-page').attr('data-option-name'),
            options: $('.rn-option-page-groups').serializeForm()

        };

        if (section_id != '') {
            data.options = $('#' + section_id).serializeForm();
        }

        var nonce_fields = $('.rn-option-page-groups').serializeForm();

        var nonce_id = "option_page_" + $('.rn-option-page').attr('data-option-name');

        data[nonce_id] = nonce_fields[nonce_id];
        data['_wp_http_referer'] = nonce_fields['_wp_http_referer'];
        data['reon_instance_id'] = $('.reon_instance_id').val();
        rn_show_wait(true, function () {
            option_page_ajax(data, function (response, status, xhr) {
                if (response.status != 200) {
                    alert(response.status_message);
                } else {
                    if (section_id != '') {
                        $('#' + section_id).removeClass('rn-option-page-group-dirty');
                    } else {
                        $('.rn-option-page-group').removeClass('rn-option-page-group-dirty');
                    }
                }
                rn_show_wait(false);
            }, function () {
                alert($('.rn-option-page').attr('data-ajax-save-error'));
                rn_show_wait(false);
            });
        });


    }

    function rn_reset_option(btn) {
        var section_id = '';
        if (btn.attr('data-active-group')) {
            section_id = btn.attr('data-active-group');
        }

        var data = {
            section_id: section_id,
            option_name: $('.rn-option-page').attr('data-option-name'),
            action: 'rn_reset_option'
        };



        var nonce_fields = $('.rn-option-page-groups').serializeForm();

        var nonce_id = "option_page_" + $('.rn-option-page').attr('data-option-name');
        data[nonce_id] = nonce_fields[nonce_id];
        data['_wp_http_referer'] = nonce_fields['_wp_http_referer'];
        data['reon_instance_id'] = $('.reon_instance_id').val();

        rn_show_wait(true, function () {
            option_page_ajax(data, function (response, status, xhr) {
                if (response.status != 200) {
                    alert(response.status_message);
                } else {
                    $('.rn-option-page-group').removeClass('rn-option-page-group-dirty');
                    window.location.reload(true);
                }
                rn_show_wait(false);
            }, function () {
                alert($('.rn-option-page').attr('data-ajax-reset-error'));
                rn_show_wait(false);
            });
        });

    }

    function rn_show_wait(show, callback) {
        var icn = $('.rn-option-page').find('.rn-ajax-icon');
        var overlay = $('.rn-option-page').find('.rn-ajax-overlay');

        if (show == true) {
            icn.css('display', 'inline-block');
            overlay.fadeIn(300, function () {
                if ($.isFunction(callback)) {
                    callback();
                }
            });
        } else {
            icn.css('display', 'none');
            overlay.fadeOut(300, function () {
                overlay.hide();
            });
        }
    }

    function init_import_export() {
        $('.rn-import-from-url').on('click', function () {
            $('.rn-data-import-panel,.rn-url-export-panel,.rn-data-export-panel').hide();
            $('.rn-data-importnow-panel').show();
            $('.rn-url-import-panel').fadeIn(500, function () {
                $('.rn-import-btn').attr('data-import-type', 'url');
                rn_adjust_aside();
            });
            return false;
        });

        $('.rn-import-from-data').on('click', function () {
            $('.rn-url-import-panel').hide();
            $('.rn-url-export-panel').hide();
            $('.rn-data-export-panel').hide();
            $('.rn-data-importnow-panel').show();

            $('.rn-data-import-panel').fadeIn(500, function () {
                $('.rn-import-btn').attr('data-import-type', 'data');
                rn_adjust_aside();
            });
            return false;
        });

        var import_btn = $('.rn-import-btn');
        import_btn.on('click', function () {
            rn_import_now();
            return false;
        });


        $('.rn-export-url').on('click', function () {
            $('.rn-data-export-panel,.rn-url-import-panel,.rn-data-import-panel,.rn-data-importnow-panel').hide();
            $('.rn-url-export-panel').fadeIn(500, function () {
                rn_adjust_aside();
            });
            return false;
        });

        $('.rn-export-data').on('click', function () {
            $('.rn-url-export-panel,.rn-url-import-panel,.rn-data-import-panel,.rn-data-importnow-panel').hide();

            var export_impt = $('#rn_export_data');
            rn_show_wait(true, function () {
                option_page_ajax_get(export_impt.attr('data-option-url'), function (response, status, xhr) {

                    export_impt.val(JSON.stringify(response));
                    $('.rn-data-export-panel').fadeIn(500, function () {
                        rn_adjust_aside();
                    });
                    rn_show_wait(false);
                }, function () {

                    rn_show_wait(false);
                });
            });


            return false;
        });
        $('.rn-export-download-data').on('click', function () {
            $('.rn-url-export-panel,.rn-data-export-panel,.rn-url-import-panel,.rn-data-import-panel,.rn-data-importnow-panel').hide();
        });
    }

    function rn_import_now() {
        if ($('.rn-import-btn').attr('data-import-type') == 'url') {
            rn_show_wait(true, function () {
                option_page_ajax_get($('#rn_import_field_data').val(), function (response, status, xhr) {
                    rn_send_import(JSON.stringify(response));
                    rn_show_wait(false);
                }, function () {
                    rn_show_wait(false);
                });
            });

        } else {
            rn_send_import($('#rn_import_field_data').val());
        }


    }


    function rn_send_import(rn_option) {
        var data = {
            option_name: $('.rn-option-page').attr('data-option-name'),
            action: 'rn_import_option',

            import_data: JSON.parse(rn_option),

        };



        var nonce_fields = $('.rn-option-page-groups').serializeForm();

        var nonce_id = "option_page_" + $('.rn-option-page').attr('data-option-name');
        data[nonce_id] = nonce_fields[nonce_id];
        data['_wp_http_referer'] = nonce_fields['_wp_http_referer'];
        data['reon_instance_id'] = $('.reon_instance_id').val();

        rn_show_wait(true, function () {
            option_page_ajax(data, function (response, status, xhr) {

                if (response.status != 200) {
                    alert(response.status_message);
                } else {
                    $('.rn-option-page-group').removeClass('rn-option-page-group-dirty');
                    window.location.reload(true);
                }
                rn_show_wait(false);
            }, function () {
                alert($('.rn-option-page').attr('data-ajax-save-error'));
                rn_show_wait(false);
            });
        });

    }

    function option_page_ajax(data, onsuccess, onerror) {
        jQuery.post(ajaxurl, data, function (response, status, xhr) {
            onsuccess(response, status, xhr);
        }).fail(function () {
            onerror();
        });
    }

    function option_page_ajax_get(url, onsuccess, onerror) {
        jQuery.get(url, [], function (response, status, xhr) {
            onsuccess(response, status, xhr);
        }).fail(function () {
            onerror();
        });
    }



    //========================
    //WooCommerce page scripts
    //========================

    function init_wc_page() {
        var allow_wc_save = true;



        $('.rn-option-page').parents('.woocommerce').each(function () {
            var wc_cont = $(this);
            if ((wc_cont.find('.rn-option-page-buttons').length > 0)) {
                allow_wc_save = false;
            }
            if (allow_wc_save == false) {
                wc_cont.addClass('rn-no-wc-save');
                $('.form-table').addClass('rn-form-table').removeClass('form-table');
            }

            if (!wc_cont.hasClass('rn-metabox-wc')) {
                wc_cont.children('form#mainform').addClass('rn-woocommerce');
            }


        });


        $('form.rn-woocommerce').each(function () {
            var rn_wc = $(this);

            if (allow_wc_save == false) {
                rn_wc.addClass('rn-no-wc-save');
                rn_wc.find('.woocommerce-save-button').parents('p').hide();

                rn_wc.on('submit', function () {
                    return false;
                });
            } else {
                init_wc_form();
            }

        });
    }

    function init_wc_form() {

        var wc_form = $('form.rn-woocommerce');
        if (wc_form.attr('data-rn_ready') && wc_form.attr('data-rn_ready') == 'yes') {
            return;
        }
        wc_form.attr('data-rn_ready', 'yes');
        wc_form.on('submit', function () {
            if (wc_form.attr('data-rn_completed') && wc_form.attr('data-rn_completed') == 'yes') {

                return true;
            } else {
                rn_save_wc_option(function () {
                    wc_form.attr('data-rn_completed', 'yes');
                    wc_form.submit();
                });
                return true;
            }

        });
    }

    function rn_save_wc_option(callback) {

        var section_id = '';

        var data = {
            section_id: section_id,
            action: 'rn_save_option',
            option_name: $('.rn-option-page').attr('data-option-name'),
            options: $('.rn-option-page-groups').serializeForm()

        };

        if (section_id != '') {
            data.options = $('#' + section_id).serializeForm();
        }

        var nonce_fields = $('.rn-option-page-groups').serializeForm();

        var nonce_id = "option_page_" + $('.rn-option-page').attr('data-option-name');
        data[nonce_id] = nonce_fields[nonce_id];
        data['_wp_http_referer'] = nonce_fields['_wp_http_referer'];
        data['reon_instance_id'] = $('.reon_instance_id').val();

        rn_show_wait(true, function () {
            option_page_ajax(data, function (response, status, xhr) {
                if (response.status != 200) {
                    alert(response.status_message);

                } else {
                    callback();
                }
                rn_show_wait(false);
            }, function () {
                alert($('.rn-option-page').attr('data-ajax-save-error'));
                rn_show_wait(false);
            });
        });


    }

    //========================
    //Confirm Option Page Save
    //========================

    function init_rn_form_save_popup() {

        $('.rn-option-page').each(function () {
            if (!$(this).hasClass('rn_is_wc')) {
                var opt_page = $(this);
                var opt_page_g = opt_page.find('.rn-option-page-group');
                process_rn_form_save_popup(opt_page_g, false);
                opt_page.on('rn_ui_content_added', function () {
                    process_rn_form_save_popup(opt_page_g, true);
                });
                window.onbeforeunload = rn_form_save_popup;
            }
        });

    }

    function process_rn_form_save_popup(contents, imadiate) {
        contents.find('input,select,textarea').each(function () {
            var inp = $(this);

            var can_imp = (inp.parents('.rn-repeater-templates').length > 0);

            if (!inp.attr('data-opt-page-ready') && can_imp == false) {
                inp.attr('data-opt-page-ready', 'yes');

                if (imadiate == true) {
                    inp.parents('.rn-option-page-group').addClass('rn-option-page-group-dirty');
                }
                inp.on('change', function () {
                    inp.parents('.rn-option-page-group').addClass('rn-option-page-group-dirty');
                });
            }

        });
    }

    function rn_form_save_popup() {
        if ($('.rn-option-page-group-dirty').length > 0) {
            return '';
        }
    }



});


