<div class="rn-repeater-head">
    <?php
    if ($is_sortable == true) {
        ?>
        <div class="rn-repeater-col-buttons">
            <span class="rn-repeater-col-sort"><i class="fa fa-bars"></i></span>
        </div>
        <?php
    }
    ?><div class="rn-repeater-col-titles">
    <?php
    if (count($left_titles) > 0) {
        ?><div class="rn-repeater-col-left" style="width:<?php echo esc_attr($left_width); ?>;">
            <?php
            foreach ($left_titles as $lcol) {

                $ttl_styles = '';
                if (isset($lcol['width'])) {
                    $ttl_styles = 'width:' . esc_attr($lcol['width']) . ';';
                }
                if (isset($lcol['offset'])) {
                    $ttl_styles = $ttl_styles . 'padding-left:' . esc_attr($lcol['offset']) . ';';
                }

                if ($ttl_styles != '') {
                    $ttl_styles = ' style="' . $ttl_styles . '"';
                }
                ?>
                    <div class="rn-repeater-col-title"<?php echo wp_kses_post($ttl_styles); ?>><?php echo esc_html($lcol['text']); ?><?php do_action('reon/render-control-tooltip', $lcol['tooltip']); ?></div>
                    <?php
                }
                ?></div><?php
        }
        if (count($right_titles) > 0) {
            ?><div class="rn-repeater-col-right" style="width:<?php echo esc_attr($right_width); ?>;">
                <?php

                foreach ($right_titles as $rcol) {
                    $ttl_styles = '';
                    if (isset($rcol['width'])) {
                        $ttl_styles = 'width:' . esc_attr($rcol['width']) . ';';
                    }
                    if (isset($rcol['offset'])) {
                        $ttl_styles = $ttl_styles . 'padding-right:' . esc_attr($rcol['offset']) . ';';
                    }

                    if ($ttl_styles != '') {
                        $ttl_styles = ' style="' . $ttl_styles . '"';
                    }
                    ?>
                    <div class="rn-repeater-col-title"<?php echo wp_kses_post($ttl_styles); ?>><?php echo esc_html($rcol['text']); ?><?php do_action('reon/render-control-tooltip', $rcol['tooltip']); ?></div>
                    <?php
                }
                ?></div><?php
        }
        ?>
    </div>
    <?php
    if ($white_spacer != '') {
        ?>
        <div class="rn-repeater-col-spacer">
            <span style="display:inline-block;width:<?php echo esc_attr($white_spacer); ?>;"></span>
        </div>
        <?php
    }
    ?>

</div>