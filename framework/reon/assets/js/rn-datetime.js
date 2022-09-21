(function ($) {
"use strict";
    $.fn.rn_datetime = function (options) {



        return this.each(function () {
            // Default options.

            var settings = $.extend({}, options);

            var obj = $(this);
            var opt = settings;
            prepere_options(obj);

            $('#ui-datepicker-div').remove();

            opt.beforeShow = function (dp, gg) {
                gg.dpDiv.addClass('rn-datetimepicker');
                if ($(dp).attr('id') == 'rn-date') {
                    gg.dpDiv.addClass('rn-change-date');
                } else {
                    gg.dpDiv.removeClass('rn-change-date');
                }
                
               
            };


            if (obj.hasClass('rn-dateonly')) {
                obj.datepicker(opt);
            } else if (obj.hasClass('rn-timeonly')) {

                obj.timepicker(opt);
               
            } else {
                obj.datetimepicker(opt);
            }


            function prepere_options(optobj) {

                if (optobj.attr('data-time-locale')) {
                    if (rn_uiv.culture[optobj.attr('data-time-locale')]) {
                        $.extend(opt, rn_uiv.culture[optobj.attr('data-time-locale')]);
                        opt.isRTL = false;
                        if (opt.is_RTL == 'true') {
                            opt.isRTL = true;
                        }
                    }
                }

                if (optobj.attr('data-date-locale')) {
                    if (rn_uiv.culture[optobj.attr('data-date-locale')]) {
                        $.extend(opt, rn_uiv.culture[optobj.attr('data-date-locale')]);
                        opt.isRTL = false;
                        if (opt.is_RTL == 'true') {
                            opt.isRTL = true;
                        }
                    }
                }

                $.extend(opt, prepere_date_options(optobj));
                $.extend(opt, prepere_time_options(optobj));


            }

            function prepere_date_options(optobj) {
                var new_opt = {};
                new_opt.showButtonPanel = true;
                new_opt.changeMonth = false;
                new_opt.changeYear = false;
                new_opt.showWeek = false;
                new_opt.showOtherMonths = false;
                new_opt.selectOtherMonths = false;
                new_opt.dateFormat = 'mm-dd-yy';
                if (optobj.attr('data-date-format')) {
                    new_opt.dateFormat = obj.attr('data-date-format');
                }
                if (optobj.attr('data-number-of-months') && optobj.attr('data-number-of-months') != '') {
                    new_opt.numberOfMonths = parseInt(optobj.attr('data-number-of-months'));
                }

                if (optobj.attr('data-min-date')) {
                    new_opt.minDate = obj.attr('data-min-date');
                }

                if (optobj.attr('data-max-date')) {
                    new_opt.maxDate = optobj.attr('data-max-date');
                }

                if (optobj.attr('data-show-button-panel') && optobj.attr('data-show-button-panel') == 'true') {
                    new_opt.showButtonPanel = true;
                }

                if (optobj.attr('data-change-month') && optobj.attr('data-change-month') == 'true') {
                    new_opt.changeMonth = true;
                }

                if (optobj.attr('data-change-year') && optobj.attr('data-change-year') == 'true') {
                    new_opt.changeYear = true;
                }

                if (optobj.attr('data-show-week') && optobj.attr('data-show-week') == 'true') {
                    new_opt.showWeek = true;
                }

                if (optobj.attr('data-first-day') && optobj.attr('data-first-day') != '') {
                    new_opt.firstDay = parseInt(optobj.attr('data-first-day'));
                }

                if (optobj.attr('data-show-other-months') && optobj.attr('data-show-other-months') == 'true') {
                    new_opt.showOtherMonths = true;
                }
                if (optobj.attr('data-select-other-months') && optobj.attr('data-select-other-months') == 'true') {
                    new_opt.selectOtherMonths = true;
                }
                return new_opt;
            }

            function prepere_time_options(optobj) {
                //Time Settings
                var new_opt = {};
                new_opt.controlType = 'select';
                new_opt.showButtonPanel = true;
                new_opt.oneLine = false;
                new_opt.timeFormat = "HH:mm:ss z";

                if (optobj.attr('data-time-format')) {
                    new_opt.timeFormat = optobj.attr('data-time-format');
                }

                if (optobj.attr('data-one-line') && optobj.attr('data-one-line') == 'true') {
                    new_opt.oneLine = true;
                }

                if (optobj.attr('data-step-hour')) {
                    new_opt.stepHour = optobj.attr('data-step-hour');
                }

                if (optobj.attr('data-step-minute')) {
                    new_opt.stepMinute = optobj.attr('data-step-minute');
                }

                if (optobj.attr('data-step-second')) {
                    new_opt.stepSecond = optobj.attr('data-step-second');
                }
                return new_opt;
            }
        });


    };

}(jQuery));