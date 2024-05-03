<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once(plugin_dir_path( __FILE__ ).'header/plugin-header.php' );
$allowed_tooltip_html = wp_kses_allowed_html( 'post' )['span'];

global $plugin_admin;

$validated = filter_input( INPUT_GET, 'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
if( !empty($validated) && isset($validated) ){
    $plugin_admin->dpad_updated_message( 'setting_saved', '' );
}

$adjustment_discount_type = get_option('wdpad_gs_adjustment_discount_type');
$adjustment_discount_type = !empty($adjustment_discount_type) ? $adjustment_discount_type : 'first';

$sequential_discount = get_option('wdpad_gs_sequential_discount');
$sequential_discount = !empty($sequential_discount) ? $sequential_discount : 'no';
?>
<div class="general-setting-page-wrap">
    <form method="POST" name="dpad_general_settings" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
        <div class="wdpad-main-table res-cl element-shadow">
        <h2><?php esc_html_e( 'General Settings', 'woo-conditional-discount-rules-for-checkout' ); ?></h2>
            <table class="form-table table-outer general-setting-table">
                <tbody>
                    <tr valign="top">
                        <th class="titledesc" scope="row">
                            <label for="dpad_gs_adjustment_discount_type">
                                <?php if ( wcdrfc_fs()->is__premium_only() ) {
                                        if ( wcdrfc_fs()->can_use_premium_code() ) {
                                            esc_html_e( 'Apply Adjustment Discount', 'woo-conditional-discount-rules-for-checkout' ); 
                                        } else {
                                            esc_html_e( 'Apply Adjustment Discount (In Pro)', 'woo-conditional-discount-rules-for-checkout' );
                                        } 
                                    }else {
                                        esc_html_e( 'Apply Adjustment Discount (In Pro)', 'woo-conditional-discount-rules-for-checkout' );
                                    }
                                ?>
                            </label>
                        </th>
                        <td class="forminp">
                            <?php if ( wcdrfc_fs()->is__premium_only() ) {
                                if ( wcdrfc_fs()->can_use_premium_code() ) { ?>
                                    <select name="dpad_gs_adjustment_discount_type" id="dpad_gs_adjustment_discount_type" class="">
                                        <option value="first" <?php selected( $adjustment_discount_type ,'first' ); ?>><?php esc_html_e( 'First rule matched', 'woo-conditional-discount-rules-for-checkout' ); ?></option> <!-- apply only on first accerance -->
                                        <option value="biggest_discount" <?php selected( $adjustment_discount_type ,'biggest_discount' ); ?>><?php esc_html_e( 'Biggest discount only', 'woo-conditional-discount-rules-for-checkout' ); ?></option> <!-- apply only on large discount -->
                                        <option value="lowest_discount" <?php selected( $adjustment_discount_type ,'lowest_discount' ); ?>><?php esc_html_e( 'Lowest discount only', 'woo-conditional-discount-rules-for-checkout' ); ?></option> <!-- apply only on small discount -->
                                        <option value="all" <?php selected( $adjustment_discount_type ,'all' ); ?>><?php esc_html_e( 'All same rule combine', 'woo-conditional-discount-rules-for-checkout' ); ?></option> <!-- apply only on all discount sum -->
                                    </select>
                                    <?php echo wp_kses( wc_help_tip( esc_html__( 'This setting will only work for product adjustment discount type.', 'woo-conditional-discount-rules-for-checkout' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
                                <?php } else { ?>
                                    <select name="dpad_gs_adjustment_discount_type" id="dpad_gs_adjustment_discount_type" class="" disabled>
                                        <option value=""><?php esc_html_e( 'First rule matched', 'woo-conditional-discount-rules-for-checkout' ); ?></option> <!-- apply only on first accerance -->
                                    </select>
                                <?php }
                            } else { ?>
                                <select name="dpad_gs_adjustment_discount_type" id="dpad_gs_adjustment_discount_type" class="" disabled>
                                    <option value=""><?php esc_html_e( 'First rule matched', 'woo-conditional-discount-rules-for-checkout' ); ?></option> <!-- apply only on first accerance -->
                                </select>
                            <?php } ?>
                        </td>
                        <tr valign="top" class="adt_all">
                            <th class="titledesc" scope="row">
                                <label>
                                    <?php if ( wcdrfc_fs()->is__premium_only() ) {
                                            if ( wcdrfc_fs()->can_use_premium_code() ) {
                                                esc_html_e( 'Apply all same rule sequentially', 'woo-conditional-discount-rules-for-checkout' );
                                            } else {
                                                esc_html_e( 'Apply all same rule sequentially (In Pro)', 'woo-conditional-discount-rules-for-checkout' );
                                            }
                                        } else {
                                            esc_html_e( 'Apply all same rule sequentially (In Pro)', 'woo-conditional-discount-rules-for-checkout' );
                                        } ?></label>
                            </th>
                            <td class="forminp">
                                <?php if ( wcdrfc_fs()->is__premium_only() ) {
                                    if ( wcdrfc_fs()->can_use_premium_code() ) { ?>
                                            <label>
                                                <input type="radio" name="dpad_gs_sequential_discount" value="yes" <?php checked( $sequential_discount ,'yes' ); ?>/> <!-- discounts apply on discounted price -->
                                                <?php esc_html_e( 'Yes', 'woo-conditional-discount-rules-for-checkout' ); ?>
                                            </label>
                                            <label>
                                                <input type="radio" name="dpad_gs_sequential_discount" value="no" <?php checked( $sequential_discount ,'no' ); ?> /> <!-- discounts on main price -->
                                                <?php esc_html_e( 'No', 'woo-conditional-discount-rules-for-checkout' ); ?>
                                            </label>
                                            <?php echo wp_kses( wc_help_tip( esc_html__( 'This setting will only work for product adjustment discount type and it will apply only for all same rule matched.', 'woo-conditional-discount-rules-for-checkout' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
                                        <?php } else { ?>
                                            <label>
                                                <input type="radio" name="dpad_gs_sequential_discount" value="" disabled/>
                                                <?php esc_html_e( 'Yes', 'woo-conditional-discount-rules-for-checkout' ); ?>
                                            </label>
                                            <label>
                                                <input type="radio" name="dpad_gs_sequential_discount" value="" disabled/>
                                                <?php esc_html_e( 'No', 'woo-conditional-discount-rules-for-checkout' ); ?>
                                            </label>
                                        <?php } 
                                    } else { ?>
                                        <label>
                                            <input type="radio" name="dpad_gs_sequential_discount" value="" disabled/>
                                            <?php esc_html_e( 'Yes', 'woo-conditional-discount-rules-for-checkout' ); ?>
                                        </label>
                                        <label>
                                            <input type="radio" name="dpad_gs_sequential_discount" value="" disabled/>
                                            <?php esc_html_e( 'No', 'woo-conditional-discount-rules-for-checkout' ); ?>
                                        </label>
                                <?php } ?>
                            </td>
                        </tr>
                    </tr>
                </tbody>
            </table>
            <input type="hidden" name="action" value="dpad_save_general_settings" />
            <?php wp_nonce_field( 'dpad_save_general_setting', 'dpad_save_general_setting_nonce' ); ?>
        </div>
        <?php if ( wcdrfc_fs()->is__premium_only() ) {
            if ( wcdrfc_fs()->can_use_premium_code() ) { ?>
                <p class="submit">
                    <input type="submit" name="submitDiscount" class="submitDiscount button button-primary" />
                </p>
            <?php }
        } ?>
    </form>
</div>

<!-- End of general setting page -->
<?php require_once( plugin_dir_path( __FILE__ ) . 'header/plugin-footer.php' ); ?>