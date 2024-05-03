<?php
/**
 * Fired during plugin activation.
 *
 * @link       http://www.multidots.co
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Woocommerce_Dynamic_Pricing_And_Discount_Pro
 * @subpackage Woocommerce_Dynamic_Pricing_And_Discount_Pro/includes
 * @author     Multidots <inquiry@multidots.in>
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class Woocommerce_Dynamic_Pricing_And_Discount_Pro_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        
		set_transient( '_welcome_screen_wdpad_pro_mode_activation_redirect_data', true, 30 );
		add_option( 'wdpad_version', WDPAD_PLUGIN_VERSION );

        //Refresh our cache on first time while activating plugin for latest list of dicsounts
        delete_option( 'wpdad_discount_id_list' );

		if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ),true  ) && ! is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) {
			wp_die( "<strong> WooCommerce Conditional Discount Rules For Checkout Pro</strong> Plugin requires <strong>WooCommerce</strong> <a href='" . esc_url(get_admin_url( null, 'plugins.php' )) . "'>Plugins page</a>." );
		} 
	}

}
