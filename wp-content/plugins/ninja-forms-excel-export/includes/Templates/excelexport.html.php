<div class="wrap">

    <h1><?php _e('Excel Export','ninja-forms-spreadsheet') ?></h1>

    <h2 class="nav-tab-wrapper">
        <span class="nav-tab nav-tab-active"><?php _e('Excel Export','ninja-forms-spreadsheet') ?></span>
    </h2>
    
    <div id="poststuff">
        <form action="" id="nf_spreadsheet_export_form" method="POST">
            <?php if( $errors ): ?>
                <?php foreach( $errors as $error_id => $error ): ?>
                    <?php $message = $error . " <a href='#$error_id'>" . __( 'Fix it.', 'ninja-forms' ) . '</a>'; ?>
                    <?php Ninja_Forms::template( 'admin-notice.html.php', array( 'class' => 'error', 'message' => $message ) ); ?>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php foreach( $grouped_settings as $group => $settings ) : ?>
                <div id="ninja_forms_metabox_<?php echo $group; ?>_settings" class="postbox">
                <span class="item-controls">
                    <a class="item-edit metabox-item-edit" id="edit_id" title="Edit Menu Item" href="#">Edit Menu Item</a>
                </span>
                    <h3 class="hndle"><span><?php echo $groups[ $group ][ 'label' ]; ?></span></h3>
                    <div class="inside" style="">
                        <table class="form-table">
                            <tbody>
                            <?php foreach( $settings as $key => $setting ) : ?>

                            <?php if( 'prompt' == $setting[ 'type' ] ) continue; ?>

                            <tr id="<?php echo ( isset( $setting['key'] ) ? 'row_spreadsheet_export_'.$setting['key'] : 'row_'.$setting['id'] ); ?>" data-key="<?php echo ( isset( $setting['key'] ) ? $setting[ 'key' ]:''); ?>">
                                <th scope="row">
                                    <label for="<?php echo ( isset( $setting['key'] ) ? 'spreadsheet_export_field_'.$setting['key'] : $setting['id'] ); ?>"><?php echo $setting[ 'label' ]; ?></label>
                                </th>
                                <td>
                                    <?php
                                    switch ( $setting[ 'type' ] ) {
                                        case 'html':
                                            echo $setting[ 'html'];
                                            break;
                                        case 'desc' :
                                            echo $setting[ 'value' ];
                                            break;
                                        case 'textbox' :
                                            echo "<input type='text' class='code widefat' name='{$setting['id']}' id='" . ( isset( $setting['key'] ) ? 'spreadsheet_export_field_'.$setting['key'] : $setting['id'] ) . "' value='{$setting['value']}'>";
                                            break;
                                        case 'checkbox' :
                                            $checked = ( $setting[ 'value' ] ) ? 'checked' : '';
                                            echo "<input type='hidden' name='{$setting['id']}' value='0'>";
                                            echo "<input type='checkbox' name='{$setting['id']}' value='1' id='" . ( isset( $setting['key'] ) ? 'spreadsheet_export_field_'.$setting['key'] : $setting['id'] ) . "' class='widefat' $checked>";
                                            break;
                                        case 'select' :
                                            echo "<select name='{$setting['id']}' id='" . ( isset( $setting['key'] ) ? 'spreadsheet_export_field_'.$setting['key'] : $setting['id'] ) . "'>";
                                            foreach( $setting['options'] as $option ) {
                                                $selected = ( $setting['value'] == $option['value'] ) ? 'selected="selected"' : '';
                                                echo "<option value='{$option['value']}' {$selected}>{$option['label']}</option>";
                                            }
                                            echo "</select>";
                                            break;
                                    }
                                    if( isset( $setting[ 'desc' ] ) ) {
                                        echo "<p class='description'>" . $setting[ 'desc' ] . "</p>";
                                    }
                                    ?>
                                    <?php
                                    if( isset( $setting[ 'errors' ] ) ){
                                        foreach( $setting[ 'errors' ] as $error_id => $error ){
                                            echo "<div id='$error_id' class='error'><p>$error</p></div>";
                                        }
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>

            <input type="hidden" name="spreadsheet_export_tmp_name" id="spreadsheet_export_tmp_name" value="<?php echo uniqid(); ?>">
            <input type="hidden" name="spreadsheet_export_iteration" id="spreadsheet_export_iteration" value="0">


        </form>
    </div>



</div>