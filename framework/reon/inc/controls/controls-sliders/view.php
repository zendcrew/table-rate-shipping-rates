<div class="<?php echo esc_attr(ReonUtil::array_to_classes($wrp_class)); ?>"<?php echo (isset($field['dyn_field_id'])) ? ' data-dyn_field_id="' . esc_attr($field['dyn_field_id']) . '"' : ''; ?>>
    <div <?php echo wp_kses_post(ReonUtil::array_to_attributes(apply_filters('reon/control-box-attributes', $box_attributes, $field))); ?>>
        <span class="rn-field rn-slider-input">
            <?php
            do_action('reon/render-control-label', $field['label']);
            ?>
            <input <?php echo wp_kses_post(ReonUtil::array_to_attributes(apply_filters('reon/control-attributes', $attributes, $field))); ?> />
        </span><?php do_action('reon/render-control-tooltip', $field['tooltip']); ?>
        <span class="rn-field rn-slider-track">
            <?php
            $units = array();
            $slider_attr = array(
                'style' => 'width:100px;',
                'data-empty' => '',
                'data-postfix' => '',
                'data-min' => '0',
                'data-max' => '100',
                'data-step' => '1',
                'data-decimals' => '0',
                'data-connect' => '', //lower or upper
                'data-margin' => '', // no need
                'data-limit' => '', // no need
                'data-behaviour' => '',
            );




            if (isset($field['slider_width'])) {
                $slider_attr['style'] = 'width:' . $field['slider_width'] . ';';
            }

            if (isset($field['connect']) && $field['connect'] == 'lower') {
                $slider_attr['data-connect'] = 'lower';
            }
            if (isset($field['connect']) && $field['connect'] == 'upper') {
                $slider_attr['data-connect'] = 'upper';
            }

            if (isset($field['behaviour'])) {
                $slider_attr['data-behaviour'] = $field['behaviour'];
            }

            if (isset($field['empty_value'])) {
                $slider_attr['data-empty'] = $field['empty_value'];
            }

            if (isset($field['postfix'])) {
                $slider_attr['data-postfix'] = $field['postfix'];
                $units[] = trim($field['postfix']);
            }

            if (isset($field['min'])) {
                $slider_attr['data-min'] = $field['min'];
            }

            if (isset($field['max'])) {
                $slider_attr['data-max'] = $field['max'];
            }

            if (isset($field['step'])) {
                $slider_attr['data-step'] = $field['step'];
            }

            if (isset($field['decimals'])) {
                $slider_attr['data-decimals'] = $field['decimals'];
            }
            if (isset($field['scales'])) {
                foreach ($field['scales'] as $scale) {

                    if (isset($scale['postfix'])) {
                        $pfx = trim($scale['postfix']);
                        if ($pfx == '%') {
                            $pfx = 'percent';
                        }

                        if (isset($scale['min'])) {
                            $slider_attr['data-min-' . $pfx] = $scale['min'];
                        }
                        if (isset($scale['max'])) {
                            $slider_attr['data-max-' . $pfx] = $scale['max'];
                        }

                        if (isset($scale['step'])) {
                            $slider_attr['data-step-' . $pfx] = $scale['step'];
                        }

                        if (isset($scale['decimals'])) {
                            $slider_attr['data-decimals-' . $pfx] = $scale['decimals'];
                        }
                        if (!in_array(trim($scale['postfix']), $units)) {
                            $units[] = trim($scale['postfix']);
                        }
                    } else {
                        if (isset($scale['min'])) {
                            $slider_attr['data-min'] = $scale['min'];
                        }
                        if (isset($scale['max'])) {
                            $slider_attr['data-max'] = $scale['max'];
                        }

                        if (isset($scale['step'])) {
                            $slider_attr['data-step'] = $scale['step'];
                        }

                        if (isset($scale['decimals'])) {
                            $slider_attr['data-decimals'] = $scale['decimals'];
                        }
                    }
                }
            }
            if (count($units) > 0) {
                $slider_attr['data-units'] = implode(',', $units);
            }
            ?>
            <span class="rn-noui" <?php echo wp_kses_post(ReonUtil::array_to_attributes($slider_attr)); ?>></span>
        </span>
    </div>
</div>