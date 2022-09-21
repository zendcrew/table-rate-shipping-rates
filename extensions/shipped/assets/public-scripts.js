jQuery(document).ready(function($) {
    "use strict";

    if ('' != wtars_shipped.update_triggers) {

        $('form.checkout').on('change', wtars_shipped.update_triggers, function() {
            $('body').trigger('update_checkout');
        });
    }
    run_shipped_tips();

    $(document.body).on("updated_checkout", function(e, data) {

        run_shipped_tips();
    });

    $(document.body).on('updated_cart_totals', function() {

        run_shipped_tips();
    });

    function run_shipped_tips() {

        $('.fee').each(function() {
            var shipped_fee = $(this);
            var shipped_fee_tips = shipped_fee.find('.wtars_shipped_tooltip_mv');
            shipped_fee.find('th').append(shipped_fee_tips);
            shipped_fee.find('td').find('.wtars_shipped_tooltip_mv').remove();
            shipped_fee_tips.removeClass('wtars_shipped_tooltip_mv');
        });

        $('.wtars_shipped_tooltip').tipTip({ defaultPosition: 'top' });
    }

});