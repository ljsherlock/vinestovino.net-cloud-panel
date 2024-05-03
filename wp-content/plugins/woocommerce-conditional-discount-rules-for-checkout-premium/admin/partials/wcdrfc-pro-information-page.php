<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once(plugin_dir_path( __FILE__ ).'header/plugin-header.php' );
?>
<div class="wdpad-main-table res-cl element-shadow">
    <h2><?php esc_html_e( 'Quick info', 'woo-conditional-discount-rules-for-checkout' ); ?></h2>
    <table class="table-outer">
        <tbody>
        <tr>
            <td class="fr-1"><?php esc_html_e( 'Product Type', 'woo-conditional-discount-rules-for-checkout' ); ?></td>
            <td class="fr-2"><?php esc_html_e( 'WooCommerce Plugin', 'woo-conditional-discount-rules-for-checkout' ); ?></td>
        </tr>
        <tr>
            <td class="fr-1"><?php esc_html_e( 'Product Name', 'woo-conditional-discount-rules-for-checkout' ); ?></td>
            <td class="fr-2"><?php esc_html_e( WDPAD_PLUGIN_NAME, 'woo-conditional-discount-rules-for-checkout' ); ?></td>
        </tr>
        <tr>
            <td class="fr-1"><?php esc_html_e( 'Installed Version', 'woo-conditional-discount-rules-for-checkout' ); ?></td>
            <td class="fr-2"><?php echo esc_html( WDPAD_VERSION_LABEL ); ?> <?php echo esc_html(WDPAD_PLUGIN_VERSION); ?></td>
        </tr>
        <tr>
            <td class="fr-1"><?php esc_html_e( 'License & Terms of use', 'woo-conditional-discount-rules-for-checkout' ); ?></td>
            <td class="fr-2"><a target="_blank"
                                href="https://www.thedotstore.com/terms-and-conditions/"><?php esc_html_e( 'Click here', 'woo-conditional-discount-rules-for-checkout' ); ?></a><?php esc_html_e( ' to view license and terms of use.', 'woo-conditional-discount-rules-for-checkout' ); ?>
            </td>
        </tr>
        <tr>
            <td class="fr-1"><?php esc_html_e( 'Help & Support', 'woo-conditional-discount-rules-for-checkout' ); ?></td>
            <td class="fr-2">
                <ul style="margin: 0px;">
                    <li><a target="_blank"
                        href="<?php echo esc_url(site_url( 'wp-admin/admin.php?page=wcdrfc-page-get-started' )); ?>"><?php esc_html_e( 'Quick Start', 'woo-conditional-discount-rules-for-checkout' ); ?></a>
                    </li>
                    <li><a target="_blank"
                        href="https://docs.thedotstore.com/collection/318-conditional-discount-rules-for-woocommerce-checkout"><?php esc_html_e( 'Guide Documentation', 'woo-conditional-discount-rules-for-checkout' ); ?></a>
                    </li>
                    <li><a target="_blank"
                        href="https://www.thedotstore.com/support/"><?php esc_html_e( 'Support Forum', 'woo-conditional-discount-rules-for-checkout' ); ?></a></li>
                </ul>
            </td>
        </tr>
        <tr>
            <td class="fr-1"><?php esc_html_e( 'Localization', 'woo-conditional-discount-rules-for-checkout' ); ?></td>
            <td class="fr-2"><?php esc_html_e( 'English, Spanish', 'woo-conditional-discount-rules-for-checkout' ); ?></td>
        </tr>

        </tbody>
    </table>
</div>
<?php require_once( plugin_dir_path( __FILE__ ) . 'header/plugin-footer.php' ); ?>