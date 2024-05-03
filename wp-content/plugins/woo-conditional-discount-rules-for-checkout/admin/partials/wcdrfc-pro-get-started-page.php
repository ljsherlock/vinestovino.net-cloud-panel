<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once(plugin_dir_path( __FILE__ ).'header/plugin-header.php' );
?>
<div class="wdpad-main-table res-cl element-shadow">
    <h2><?php esc_html_e( 'Getting Started', 'woo-conditional-discount-rules-for-checkout' ); ?></h2>
    <table class="table-outer">
        <tbody>
        <tr>
            <td class="fr-2">
                <p class="block textgetting">
                    <?php esc_html_e( 'Store owners can create Conditional Discount Rules in your WooCommerce store easily. you can set different discount rules as per below:', 'woo-conditional-discount-rules-for-checkout' ); ?>
                </p>
                <ul class="block textgetting">
                    <?php echo esc_html( '- Bulk discounts','woo-conditional-discount-rules-for-checkout'). '<br>'. 
                        esc_html('- Country Discount','woo-conditional-discount-rules-for-checkout'). 
                        '<br>'.
                        esc_html('- Cart Discount','woo-conditional-discount-rules-for-checkout'). 
                        '<br>'.
                        esc_html('- Special offers','woo-conditional-discount-rules-for-checkout').
                        '<br>'.
                        esc_html('- Category Discount','woo-conditional-discount-rules-for-checkout').
                        '<br>'.
                        esc_html('- User role-based discounts and more.','woo-conditional-discount-rules-for-checkout');
                        

                        ?>
                </ul>
                <p class="block textgetting">
                    <?php esc_html_e( 'It is a valuable tool for store owners for set dynamic product new price and special discount offer for the customer.', 'woo-conditional-discount-rules-for-checkout' ); ?>
                </p>
                <p class="block textgetting">
                    <strong><?php esc_html_e( 'Step 1', 'woo-conditional-discount-rules-for-checkout' ); ?>
                        :</strong> <?php esc_html_e( 'Add Conditional Discount Rules', 'woo-conditional-discount-rules-for-checkout' ); ?>
                </p>
                <p class="block textgetting"><?php esc_html_e( 'Add Conditional Discount Rules title, discount value and set Conditional Discount Rules as per your requirement.', 'woo-conditional-discount-rules-for-checkout' ); ?>
                </p>
                <span class="gettingstarted">
                    <img style="border: 2px solid #e9e9e9;margin-top: 1%;margin-bottom: 2%;" src="<?php echo esc_url(WDPAD_PLUGIN_URL) . 'admin/images/Getting_Started_01.png'; ?>">
                </span>
                <p class="block gettingstarted textgetting">
                    <strong><?php esc_html_e( 'Step 2', 'woo-conditional-discount-rules-for-checkout' ); ?>
                        :</strong> <?php esc_html_e( 'All Conditional Discount Rules display here. As per below screenshot', 'woo-conditional-discount-rules-for-checkout' ); ?>
                    <span class="gettingstarted">
                        <img style="border: 2px solid #e9e9e9;margin-top: 2%;margin-bottom: 2%;"
                            src="<?php echo esc_url(WDPAD_PLUGIN_URL) . 'admin/images/Getting_Started_02.png'; ?>">
                    </span>
                </p>
                <p class="block gettingstarted textgetting">
                    <strong><?php esc_html_e( 'Step 3', 'woo-conditional-discount-rules-for-checkout' ); ?>
                        :</strong> <?php esc_html_e( 'View Conditional Discount Ruless applied on the checkout page as per below.', 'woo-conditional-discount-rules-for-checkout' ); ?>
                    <span class="gettingstarted">
                        <img style="border: 2px solid #e9e9e9;margin-top: 3%;" src="<?php echo esc_url(WDPAD_PLUGIN_URL) . 'admin/images/Getting_Started_03.png'; ?>">
                    </span>
                </p>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<?php require_once( plugin_dir_path( __FILE__ ) . 'header/plugin-footer.php' ); ?>