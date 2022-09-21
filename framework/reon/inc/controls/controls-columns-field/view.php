<div <?php echo wp_kses_post( ReonUtil::array_to_attributes( apply_filters( 'reon/control-parent-attributes', $attr, $g_field ) ) ); ?>>
    <table>
        <?php
        if ( isset( $g_field[ 'inner_title' ] ) || isset( $g_field[ 'inner_desc' ] ) ) {
            ?>

            <tr class="rn-ui-section rn-ui-big-section rn-ui-title-section" style="display: table-caption;">
                <td class="rn-ui-block" colspan="<?php echo esc_attr( count( $clm_fields ) ); ?>">
                    <div class="rn-ui-head rn-center">
                        <?php if ( isset( $g_field[ 'inner_title' ] ) && $g_field[ 'inner_title' ] != '' ) {
                            ?>
                            <h6 class="rn-ui-head-title"><?php echo esc_attr( $g_field[ 'inner_title' ] ); ?></h6>
                        <?php }
                        ?>
                        <?php if ( isset( $g_field[ 'inner_desc' ] ) && $g_field[ 'inner_desc' ] != '' ) {
                            ?>
                            <p class="rn-ui-head-desc">
                                <?php echo wp_kses_post( $g_field[ 'inner_desc' ] ); ?>
                            </p>
                            <?php
                        }
                        ?>
                    </div>

                </td><!-- .rn-ui-block -->
            </tr>

        <?php } ?>
        <?php
        if ( isset( $g_field[ 'inner_title' ] ) || isset( $g_field[ 'inner_desc' ] ) ) {
            ?>
            <tr>
                <td colspan="<?php echo esc_attr( count( $clm_fields ) ); ?>">
                    <div class="rn-columns-title-gap"></div>
                </td>
            </tr>
        <?php } ?>
        <tr>
            <?php
            $args = ReonUtil::get_screen_args_from_field( $g_field );

            if ( count( $clm_fields ) > 0 ) {

                $total_columns = 1;

                if ( isset( $g_field[ 'columns' ] ) ) {
                    $total_columns = $g_field[ 'columns' ];
                }

                foreach ( $clm_fields as $fld ) {

                    $column_size = 1;

                    if ( isset( $fld[ 'column_size' ] ) ) {
                        $column_size = $fld[ 'column_size' ];
                    }

                    $column_attr = array();

                    if ( isset( $fld[ 'column_attributes' ] ) ) {
                        $column_attr = $fld[ 'column_attributes' ];
                    }

                    $column_clss = array();

                    if ( isset( $fld[ 'column_classes' ] ) ) {
                        $column_clss = $fld[ 'column_classes' ];
                    }

                    $column_no_size = false;

                    if ( isset( $fld[ 'column_no_size' ] ) ) {
                        $column_no_size = $fld[ 'column_no_size' ];
                    }

                    $column_width = '';

                    if ( isset( $fld[ 'column_width' ] ) ) {
                        $column_width = $fld[ 'column_width' ];
                    }



                    $column_no_title = false;

                    if ( isset( $fld[ 'column_no_title' ] ) ) {
                        $column_no_title = $fld[ 'column_no_title' ];
                    }

                    $field = ReonCore::process_default_field( $fld );
                    $field[ 'label' ] = array();
                    $field[ 'parent_id' ] = $g_field[ 'id' ];
                    $field[ 'center_head' ] = false;
                    $field = ReonUtil::get_screen_args_from_field( $g_field, $field );

                    if ( $is_grey_parent == true ) {
                        $field[ 'grey-parent' ] = true;
                    }

                    if ( !isset( $g_field[ 'merge_fields' ] ) || $g_field[ 'merge_fields' ] === true ) {
                        $field[ 'in_prefix' ] = $g_field[ 'in_prefix' ] . $g_field[ 'id' ] . $g_field[ 'in_postfix' ] . '[';
                    } else {
                        $field[ 'in_prefix' ] = $g_field[ 'in_prefix' ];
                    }


                    $field[ 'in_postfix' ] = ']';

                    if ( isset( $field[ 'fields' ] ) && isset( $field[ 'merge_fields' ] ) && $field[ 'merge_fields' ] === false ) {
                        $field_ids = ReonUtil::get_controls_field_ids( $field[ 'fields' ] );
                        foreach ( $field_ids as $field_id ) {
                            if ( isset( $g_field[ 'value' ][ $field_id ] ) ) {
                                $field[ 'value' ][ $field_id ] = $g_field[ 'value' ][ $field_id ];
                            }
                        }
                    } else if ( isset( $g_field[ 'value' ][ $field[ 'id' ] ] ) ) {
                        $field[ 'value' ] = $g_field[ 'value' ][ $field[ 'id' ] ];
                    } else {
                        if ( isset( $field[ 'default' ] ) ) {
                            $field[ 'value' ] = $field[ 'default' ];
                        }
                    }
                    $column_title = '';

                    if ( isset( $field[ 'column_title' ] ) ) {
                        $tooltip = '';
                        ob_start();
                        do_action( 'reon/render-control-tooltip', $field[ 'tooltip' ] );
                        $tooltip = ob_get_clean();
                        $field[ 'tooltip' ] = array( 'title' => '' );
                        $column_title = '<h6 class="rn-columns-title">' . esc_html( $field[ 'column_title' ] ) . $tooltip . '</h6>';
                    } else if ( $column_no_title ) {
                        $column_title = '<h6 class="rn-columns-title"><span class="rn-columns-no-title"> </span></h6>';
                    }

                    $col_attr = array();

                    if ( !$column_no_size && !empty( $column_width ) ) {

                        $col_attr = array(
                            'style' => 'width:' . $column_width . ';',
                        );
                    } else if ( !$column_no_size ) {

                        $col_attr = array(
                            'style' => 'width:' . ($column_size / $total_columns) * 100 . '%;',
                        );
                    }

                    $column_clss = apply_filters( 'reon/control-columns-classes', $column_clss, $field );

                    if ( count( $column_clss ) ) {

                        $col_attr[ 'class' ] = ReonUtil::array_to_classes( $column_clss );
                    }

                    foreach ( $column_attr as $column_attr_key => $column_attr_value ) {

                        $col_attr[ $column_attr_key ] = $column_attr_value;
                    }
                    ?>
                    <td <?php echo wp_kses_post( ReonUtil::array_to_attributes( apply_filters( 'reon/control-columns-attributes', $col_attr, $field ) ) ); ?>>
                        <?php echo wp_kses_post($column_title); ?>
                        <?php do_action( 'reon/render-control-' . $field[ 'type' ], $field ); ?>
                    </td>
                    <?php
                }
            }
            ?>
        </tr>
    </table>
</div>