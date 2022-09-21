<?php
include_once 'fields-option-page.php';
include_once 'fields-metabox.php';
include_once 'fields-metabox-wc.php';
include_once 'fields-fold.php';






add_action( 'reon/render-hidden-field', 'reon_render_hidden_field', 10, 1 );
if ( !function_exists( 'reon_render_hidden_field' ) ) {

    function reon_render_hidden_field( $field ) {
        $classes = array();
        if ( isset( $field[ 'field_css_class' ] ) ) {
            $classes = $field[ 'field_css_class' ];
        }
        $classes[] = 'rn-ui-section';
        $classes[] = 'rn-ui-big-section';
        $classes[] = 'rn-ui-hidden';
        $attributes = array( 'class' => esc_attr(ReonUtil::array_to_classes( apply_filters( 'reon/field-classes', $classes, $field )) ) );
        ?>
        <tr <?php echo wp_kses_post(ReonUtil::array_to_attributes( apply_filters( 'reon/field-attributes', $attributes, $field )) ); ?>>
            <td class="rn-ui-block" colspan="2">
                <?php do_action( 'reon/render-control-' . $field[ 'type' ], $field ); ?>
            </td><!-- .rn-ui-block -->
        </tr><!-- .rn-ui-section -->
        <?php
    }

}





add_action( 'reon/render-normal-field', 'reon_render_normal_field', 10, 1 );
if ( !function_exists( 'reon_render_normal_field' ) ) {

    function reon_render_normal_field( $field ) {
        $classes = array();
        if ( isset( $field[ 'field_css_class' ] ) ) {
            $classes = $field[ 'field_css_class' ];
        }
        $classes[] = 'rn-ui-section';

        if ( isset( $field[ 'first' ] ) && $field[ 'first' ] == true ) {
            $classes[] = 'rn-first';
        }
        if ( isset( $field[ 'last' ] ) && $field[ 'last' ] == true ) {
            $classes[] = 'rn-last';
        }

        $attributes = array( 'class' => ReonUtil::array_to_classes( apply_filters( 'reon/field-classes', $classes, $field ) ) );
        ?>
        <tr <?php echo wp_kses_post(ReonUtil::array_to_attributes( apply_filters( 'reon/field-attributes', $attributes, $field )) ); ?>>
            <td class="rn-ui-head"<?php echo isset( $field[ 'head_width' ] ) ? ' style="width:' . esc_attr( $field[ 'head_width' ] ) . ';"' : ''; ?>>
                <?php do_action( 'reon/render-field-head', $field ); ?>
            </td><!-- .rn-ui-head -->
            <td class="rn-ui-block">
                <?php do_action( 'reon/render-control-' . $field[ 'type' ], $field ); ?>
                <div class="rn-clear"></div>
            </td><!-- .rn-ui-block -->
        </tr><!-- .rn-ui-section -->
        <?php
    }

}
add_action( 'reon/render-panel_title', 'reon_render_panel_title_field', 10, 1 );
if ( !function_exists( 'reon_render_panel_title_field' ) ) {

    function reon_render_panel_title_field( $field ) {
        $classes = array();
        if ( isset( $field[ 'field_css_class' ] ) ) {
            $classes = $field[ 'field_css_class' ];
        }
        $classes[] = 'rn-ui-section';
        $classes[] = 'rn-ui-big-section';
        $classes[] = 'rn-ui-title-section';

        if ( isset( $field[ 'first' ] ) && $field[ 'first' ] == true ) {
            $classes[] = 'rn-first-title';
        }
        if ( isset( $field[ 'last' ] ) && $field[ 'last' ] == true ) {
            $classes[] = 'rn-last';
        }
        $attributes = array( 'class' => ReonUtil::array_to_classes( apply_filters( 'reon/field-classes', $classes, $field ) ) );
        ?>
        <tr <?php echo wp_kses_post(ReonUtil::array_to_attributes( apply_filters( 'reon/field-attributes', $attributes, $field )) ); ?>>
            <td class="rn-ui-block" colspan="2">
                <?php
                if ( $field[ 'title' ] != '' || $field[ 'desc' ] != '' ) {
                    ?>
                    <div class="rn-ui-head<?php echo wp_kses_post(($field[ 'center_head' ] == true) ? ' rn-center' : ''); ?>">
                        <?php do_action( 'reon/render-field-head', $field ); ?>
                    </div>
                    <?php
                }
                ?>
                <?php
                if ( $field[ 'center_head' ] != true ) {
                    ?>
                    <div class="rn-clear"></div>
                    <?php
                }
                ?>

            </td><!-- .rn-ui-block -->
        </tr><!-- .rn-ui-section -->
        <?php
    }

}

add_action( 'reon/render-fullwidth-field', 'reon_render_fullwidth_field', 10, 1 );
if ( !function_exists( 'reon_render_fullwidth_field' ) ) {

    function reon_render_fullwidth_field( $field ) {
        $classes = array();
        if ( isset( $field[ 'field_css_class' ] ) ) {
            $classes = $field[ 'field_css_class' ];
        }
        $classes[] = 'rn-ui-section';
        $classes[] = 'rn-ui-big-section';

        if ( isset( $field[ 'first' ] ) && $field[ 'first' ] == true ) {
            $classes[] = 'rn-first';
        }
        if ( isset( $field[ 'last' ] ) && $field[ 'last' ] == true ) {
            $classes[] = 'rn-last';
        }
        $attributes = array( 'class' => ReonUtil::array_to_classes( apply_filters( 'reon/field-classes', $classes, $field ) ) );
        ?>
        <tr <?php echo ReonUtil::array_to_attributes( apply_filters( 'reon/field-attributes', $attributes, $field ) ); ?>>
            <td class="rn-ui-block" colspan="2">
                <?php
                if ( $field[ 'title' ] != '' || $field[ 'desc' ] != '' ) {
                    ?>
                    <div class="rn-ui-head<?php echo ($field[ 'center_head' ] == true) ? ' rn-center' : ''; ?>">
                        <?php do_action( 'reon/render-field-head', $field ); ?>
                    </div>
                    <?php
                }
                ?>
                <?php do_action( 'reon/render-control-' . $field[ 'type' ], $field ); ?>
                <?php
                if ( $field[ 'center_head' ] != true ) {
                    ?>
                    <div class="rn-clear"></div>
                    <?php
                }
                ?>

            </td><!-- .rn-ui-block -->
        </tr><!-- .rn-ui-section -->
        <?php
    }

}

add_action( 'reon/render-heading-field', 'reon_render_heading_field', 10, 1 );
if ( !function_exists( 'reon_render_heading_field' ) ) {

    function reon_render_heading_field( $field ) {
        ?>
        <tr class="rn-ui-heading-section">
            <td colspan="2"><h5><?php echo esc_html( $field[ 'title' ] ); ?></h5></td>
        </tr>
        <?php
    }

}

add_action( 'reon/render-field-head', 'reon_render_field_head', 10, 1 );
if ( !function_exists( 'reon_render_field_head' ) ) {

    function reon_render_field_head( $field ) {
        ?>
        <?php if ( $field[ 'title' ] != '' ) {
            ?>
            <h6 class="rn-ui-head-title"><?php echo esc_html( $field[ 'title' ] ); ?></h6>
        <?php }
        ?>
        <?php if ( $field[ 'desc' ] != '' ) {
            ?>
            <p class="rn-ui-head-desc">
                <?php echo wp_kses_post( $field[ 'desc' ] ); ?>
            </p>
            <?php
        }
    }

}

add_action( 'reon/render-control-label', 'reon_render_control_label', 10, 1 );
if ( !function_exists( 'reon_render_control_label' ) ) {

    function reon_render_control_label( $label ) {

        $title = '';
        $icon = '';

        if ( isset( $label[ 'title' ] ) || isset( $label[ 'icon' ] ) ) {
            if ( isset( $label[ 'title' ] ) ) {
                $title = esc_html( $label[ 'title' ] );
            }
            if ( isset( $label[ 'icon' ] ) ) {
                $icon = '<i class="' . esc_attr( $label[ 'icon' ] ) . '"></i>';
            }
            if ( isset( $label[ 'single' ] ) && $label[ 'single' ] == true ) {
                ?>
                <label class="rn-field rn-single<?php echo esc_attr((isset( $label[ 'no-space' ] ) && $label[ 'no-space' ] == true) ? ' rn-no-space' : ''); ?>">
                    <span class="rn-label<?php echo esc_attr(($title == '') ? ' rn-icon-label' : ''); ?>"<?php echo (isset( $label[ 'style' ] )) ? ' style="' .  ReonUtil::array_to_styles( $label[ 'style' ] ) . '"' : '';// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo wp_kses_post($icon . $title); ?></span>
                </label>
                <?php
            } else {
                ?>
                <span class="rn-label<?php echo esc_attr(($title == '') ? ' rn-icon-label' : ''); ?>"<?php echo (isset( $label[ 'style' ] )) ? ' style="' . ReonUtil::array_to_styles( $label[ 'style' ] ) . '"' : '';// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo wp_kses_post($icon . $title); ?></span>
                <?php
            }
        }
    }

}

add_action( 'reon/render-control-tooltip', 'reon_render_control_tooltip', 10, 1 );
if ( !function_exists( 'reon_render_control_tooltip' ) ) {

    function reon_render_control_tooltip( $tooltip ) {

        if ( !is_array( $tooltip ) ) {
            $tooltip = array( 'title' => $tooltip );
        }

        if ( '' == $tooltip[ 'title' ] ) {
            return;
        }

        $is_bottom = false;

        if ( isset( $tooltip[ 'is_bottom' ] ) ) {

            $is_bottom = $tooltip[ 'is_bottom' ];
        }


        $attrs = array(
            'class' => ($is_bottom) ? 'rn-tips-bottom' : 'rn-tips',
            'title' => $tooltip[ 'title' ],
        );

        if ( isset( $tooltip[ 'position' ] ) && $tooltip[ 'position' ] != '' ) {

            $attrs[ 'data-position' ] = $tooltip[ 'position' ];
        }

        $inner_text = '';

        if ( $is_bottom ) {
            $inner_text = $tooltip[ 'title' ];
        }
        ?>
                <span <?php echo wp_kses_post(ReonUtil::array_to_attributes( $attrs )); ?> ><?php echo esc_attr( $inner_text ); ?></span>
        <?php
    }

}



add_filter( 'reon/control-attributes', 'reon_process_control_attributes', 10, 2 );
if ( !function_exists( 'reon_process_control_attributes' ) ) {

    function reon_process_control_attributes( $attributes, $fld ) {
        $attrs = $attributes;
        $field = $fld;
        $styles = array();
        $classes = array();


        if ( !isset( $attrs[ 'id' ] ) ) { // some controls may already set the id value
            if ( isset( $fld[ 'parent_id' ] ) ) {
                $attrs[ 'id' ] = $fld[ 'parent_id' ] . '_' . $fld[ 'id' ];
            } else {
                $attrs[ 'id' ] = $fld[ 'id' ];
            }
            if ( isset( $fld[ 'item_index' ] ) ) {
                $attrs[ 'id' ] = $attrs[ 'id' ] . '_' . $fld[ 'item_index' ];
            }
        }

        if ( isset( $field[ 'attributes' ] ) ) {
            foreach ( $field[ 'attributes' ] as $key => $value ) {
                $attrs[ $key ] = $value;
            }
        }



        if ( isset( $field[ 'width' ] ) && (!isset( $field[ 'box_width' ] ) || $field[ 'box_width' ] == '') ) {
            if ( !isset( $field[ 'style' ] ) ) {
                $field[ 'style' ] = array();
            }
            $field[ 'style' ][ 'width' ] = $field[ 'width' ];
        }



        if ( isset( $field[ 'style' ] ) ) {
            foreach ( $field[ 'style' ] as $key => $value ) {
                $styles[ $key ] = $value;
            }
            $attrs[ 'style' ] = ReonUtil::array_to_styles( apply_filters( 'reon/control-styles', $styles, $fld ) );
        }


        if ( isset( $field[ 'css_class' ] ) ) {
            foreach ( $field[ 'css_class' ] as $value ) {
                $classes[] = $value;
            }
        }

        $classes = apply_filters( 'reon/control-classes', $classes, $fld );
        if ( count( $classes ) > 0 ) {
            $attrs[ 'class' ] = ReonUtil::array_to_classes( $classes );
        }

        return $attrs;
    }

}


add_filter( 'reon/control-box-attributes', 'reon_process_control_box_width_attributes', 10, 2 );
if ( !function_exists( 'reon_process_control_box_width_attributes' ) ) {

    function reon_process_control_box_width_attributes( $attributes, $field ) {
        if ( isset( $field[ 'width' ] ) && isset( $field[ 'box_width' ] ) && $field[ 'box_width' ] != '' ) {
            $attributes[ 'style' ] = 'width:' . $field[ 'width' ] . ';';
            $field[ 'style' ][ 'width' ] = $field[ 'width' ];
        }
        return $attributes;
    }

}



