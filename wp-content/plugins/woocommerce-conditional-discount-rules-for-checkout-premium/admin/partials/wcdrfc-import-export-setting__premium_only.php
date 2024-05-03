<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
require_once( plugin_dir_path( __FILE__ ) . 'header/plugin-header.php' );
?>
<div class="wdpad-main-table res-cl element-shadow">
    <h2><?php esc_html_e( 'Import / Export Settings', 'woo-conditional-discount-rules-for-checkout' ); ?></h2>
    <table class="table-outer import-export-table">
        <tbody>
            <tr>
                <td scope="row">
                    <label for="blogname">
                        <?php echo esc_html__( 'Export Discount Rules', 'woo-conditional-discount-rules-for-checkout' ); ?>
                    </label>
                </td>
                <td>
                    <form method="post">
                        <div class="wdpad_main_container">
                            <p class="wdpad_button_container export_settings_container">
                                <input type="button" name="wdpad_export_settings" id="wdpad_export_settings" class="button button-primary" value="<?php esc_attr_e( 'Export', 'woo-conditional-discount-rules-for-checkout' ); ?>" />
                            </p>
                            <p class="wdpad_content_container export_settings_container">
                                <?php wp_nonce_field( 'wdpad_export_action_nonce', 'wdpad_export_action_nonce' ); ?>
                                <input type="hidden" name="wdpad_export_action" value="wdpad_export_settings_action"/>
                                <strong><?php esc_html_e( 'Export the discount rules settings for this site as a .json file. This allows you to easily import the configuration into another site.', 'woo-conditional-discount-rules-for-checkout' ); ?></strong>
                            </p>
                        </div>
                    </form>
                </td>
            </tr>
            <tr>
                <td scope="row">
                    <label for="blogname">
                        <?php echo esc_html__( 'Import Discount Rules', 'woo-conditional-discount-rules-for-checkout' ); ?>
                    </label>
                </td>
                <td>
                    <form method="post" enctype="multipart/form-data">
                        <div class="wdpad_main_container">
                            <p>
                                <input type="file" name="import_file"/>
                            </p>
                            <p class="wdpad_button_container import_settings_container">
                                <input type="button" name="wdpad_import_setting" id="wdpad_import_setting" class="button button-primary" value="<?php esc_attr_e( 'Import', 'woo-conditional-discount-rules-for-checkout' ); ?>" />
                            </p>
                            <p class="wdpad_content_container">
                                <?php wp_nonce_field( 'wdpad_import_action_nonce', 'wdpad_import_action_nonce' ); ?>
                                <input type="hidden" name="wdpad_import_action" value="wdpad_import_settings_action"/>
                                <strong><?php esc_html_e( 'Import the discount rules settings from a .json file. This file can be obtained by exporting the settings on another site using the form above.', 'woo-conditional-discount-rules-for-checkout' ); ?></strong>
                            </p>
                        </div>
                    </form>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<?php require_once( plugin_dir_path( __FILE__ ) . 'header/plugin-footer.php' ); ?>