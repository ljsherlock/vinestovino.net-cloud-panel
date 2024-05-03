<?php

// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
require_once plugin_dir_path( __FILE__ ) . 'header/plugin-header.php';
$allowed_tooltip_html = wp_kses_allowed_html( 'post' )['span'];
global  $plugin_admin ;
$validated = filter_input( INPUT_GET, 'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
if ( !empty($validated) && isset( $validated ) ) {
    $plugin_admin->dpad_updated_message( 'setting_saved', '' );
}
$adjustment_discount_type = get_option( 'wdpad_gs_adjustment_discount_type' );
$adjustment_discount_type = ( !empty($adjustment_discount_type) ? $adjustment_discount_type : 'first' );
$sequential_discount = get_option( 'wdpad_gs_sequential_discount' );
$sequential_discount = ( !empty($sequential_discount) ? $sequential_discount : 'no' );
?>
<div class="general-setting-page-wrap">
    <form method="POST" name="dpad_general_settings" action="<?php 
echo  esc_url( admin_url( 'admin-post.php' ) ) ;
?>">
        <div class="wdpad-main-table res-cl element-shadow">
        <h2><?php 
esc_html_e( 'General Settings', 'woo-conditional-discount-rules-for-checkout' );
?></h2>
            <table class="form-table table-outer general-setting-table">
                <tbody>
                    <tr valign="top">
                        <th class="titledesc" scope="row">
                            <label for="dpad_gs_adjustment_discount_type">
                                <?php 
esc_html_e( 'Apply Adjustment Discount (In Pro)', 'woo-conditional-discount-rules-for-checkout' );
?>
                            </label>
                        </th>
                        <td class="forminp">
                            <?php 
?>
                                <select name="dpad_gs_adjustment_discount_type" id="dpad_gs_adjustment_discount_type" class="" disabled>
                                    <option value=""><?php 
esc_html_e( 'First rule matched', 'woo-conditional-discount-rules-for-checkout' );
?></option> <!-- apply only on first accerance -->
                                </select>
                            <?php 
?>
                        </td>
                        <tr valign="top" class="adt_all">
                            <th class="titledesc" scope="row">
                                <label>
                                    <?php 
esc_html_e( 'Apply all same rule sequentially (In Pro)', 'woo-conditional-discount-rules-for-checkout' );
?></label>
                            </th>
                            <td class="forminp">
                                <?php 
?>
                                        <label>
                                            <input type="radio" name="dpad_gs_sequential_discount" value="" disabled/>
                                            <?php 
esc_html_e( 'Yes', 'woo-conditional-discount-rules-for-checkout' );
?>
                                        </label>
                                        <label>
                                            <input type="radio" name="dpad_gs_sequential_discount" value="" disabled/>
                                            <?php 
esc_html_e( 'No', 'woo-conditional-discount-rules-for-checkout' );
?>
                                        </label>
                                <?php 
?>
                            </td>
                        </tr>
                    </tr>
                </tbody>
            </table>
            <input type="hidden" name="action" value="dpad_save_general_settings" />
            <?php 
wp_nonce_field( 'dpad_save_general_setting', 'dpad_save_general_setting_nonce' );
?>
        </div>
        <?php 
?>
    </form>
</div>

<!-- End of general setting page -->
<?php 
require_once plugin_dir_path( __FILE__ ) . 'header/plugin-footer.php';