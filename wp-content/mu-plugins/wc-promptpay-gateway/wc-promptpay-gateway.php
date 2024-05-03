<?php
/**
 * Plugin Name:         WC Promptpay Gateway
 * Plugin URI:          http://boostpress.com/
 * Description:         Promptpay payment for WooCommerce
 * Version:             2.1
 * Author:              Boostpress
 * Author URI:          http://boostpress.com/
 * License:             commercial
 * Text Domain:         wc-promptpay-gateway
 * Domain Path:         /languages/
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

define('PROMPTPAY_PLUGIN_DIR', dirname( plugin_basename( __FILE__ ) ));
define('PROMPTPAY_PLUGIN_PATH', plugin_dir_path(__FILE__));

load_plugin_textdomain( 'wc-promptpay-gateway', false, PROMPTPAY_PLUGIN_DIR . '/languages' );

require(PROMPTPAY_PLUGIN_PATH . '/vendor/autoload.php');
require(PROMPTPAY_PLUGIN_PATH . '/license.php');
require(PROMPTPAY_PLUGIN_PATH . '/utilities.php');

class WC_Promptpay_Gateway
{
    public function __construct()
    {
        /**
         * Add settings link under plugin name on plugins page.
         */
        add_filter('plugin_action_links', array($this, 'plugin_action_links'), 10, 2);

        /**
         * Create payment class
         */
        add_action('plugins_loaded', array($this, 'init_promptpay_gateway_class'));

        /**
         * Add QR payment gateway into woocommerce payment gateway
         */
        add_filter('woocommerce_payment_gateways', array($this, 'add_promptpay_gateway'));


        /**
         * Attach qr file into customer email
         */
        add_filter( 'woocommerce_email_attachments', array($this, 'attach_qr'), 999, 3 );


        /** Enable license */
        $this->license = new Boostpress\Plugins\WC_Promptpay_Gateway\License();
    }


    /**
     * Add link to settings page under plugin name
     * @param $links
     * @param $file
     * @return mixed
     */
    public function plugin_action_links($links, $file)
    {
        if ($file != plugin_basename(__FILE__)) {
            return $links;
        }

        $settings_link = '<a href="admin.php?page=wc-settings&tab=checkout&section=promptpay">' . __('Settings', 'wc-kasikorn-kpgw') . '</a>';

        array_unshift($links, $settings_link);

        return $links;
    }


    /**
     * Attach qr image into email
     * @param $attachments
     * @param $email_id
     * @param $order
     * @return array
     */
    public function attach_qr( $attachments, $email_id, $order )
    {
        if( $order instanceof WC_Order && $order->get_payment_method() == 'promptpay' ){
            $order_id = $order->get_id();
            $fileurl = Boostpress\Plugins\WC_Promptpay_Gateway\Utilities::get_qr_dir().'/'.Boostpress\Plugins\WC_Promptpay_Gateway\Utilities::get_qr_name($order_id);
            $attachments[] = $fileurl;
        }

        return $attachments;
    }


    /**
     * Separate Promptpay Gateway Class into new file and include it.
     * For good file's structure
     */
    public function init_promptpay_gateway_class()
    {
        include 'wc-promptpay-gateway-class.php';
    }


    /**
     * Add Promptpay Gateway into WooCommerce
     * @param array $methods
     * @return array
     */
    function add_promptpay_gateway($methods)
    {
        $methods[] = 'WC_Gateway_Promptpay';
        return $methods;
    }

}

new WC_Promptpay_Gateway();
