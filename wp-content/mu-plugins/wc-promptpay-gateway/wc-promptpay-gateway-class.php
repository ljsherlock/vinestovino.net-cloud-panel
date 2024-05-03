<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

//Check class WC_Payment_Gateway is exists
if (class_exists("WC_Payment_Gateway"))
{
    class WC_Gateway_Promptpay extends WC_Payment_Gateway
    {
        /**
         * Constructor for the gateway.
         *
         * @access public
         * @return void
         */
        public function __construct()
        {
            global $woocommerce;

            $this->id = 'promptpay';
            $this->method_title = __('Promptpay', 'wc-promptpay-gateway');
            $this->icon = plugin_dir_url(__FILE__) . 'images/promptpay-logo.jpg';
            $this->has_fields = true;

            // Load the form fields.
            $this->init_form_fields();

            // Load the settings.
            $this->init_settings();

            // Define user set variables
            $this->title = $this->get_option('title');
            $this->promptpay_type = $this->get_option('promptpay_type');
            $this->promptpay_number = $this->get_option('promptpay_number');
            $this->description = $this->get_option('description');

            // Save options
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

            add_action( 'woocommerce_thankyou_'. $this->id, array( $this, 'thankyou_page' ) );

        }

        /**
         * Initialise Gateway Settings Form Fields
         *
         * @access public
         * @return void
         */
        public function init_form_fields()
        {
            $this->form_fields = array(
                'enabled' => array(
                    'title' => __('Enable/Disable', 'wc-promptpay-gateway'),
                    'type' => 'checkbox',
                    'label' => __('Enable Promptpay Checkout', 'wc-promptpay-gateway'),
                    'default' => 'no'
                ),
                'title' => array(
                    'title'       => __('Title', 'wc-promptpay-gateway'),
                    'type'        => 'text',
                    'description' => __('This controls the title which the user sees during checkout.', 'wc-promptpay-gateway'),
                    'default'     => __('Promptpay payment', 'wc-promptpay-gateway'),
                    'desc_tip'    => true,
                ),
                'promptpay_type' => array(
                    'title' => __('Select type of promptpay', 'wc-promptpay-gateway'),
                    'type'    => 'select',
                    'options' => array(
                        'mobile'     => __( 'Mobile', 'wc-promptpay-gateway' ),
                        'idcard'     => __( 'ID Card', 'wc-promptpay-gateway' ),
                    ),
                    'label' => __('Select type of promptpay', 'wc-promptpay-gateway'),
                    'default' => 'no'
                ),
                'promptpay_number' => array(
                    'title' => __('Promptpay Number', 'wc-promptpay-gateway'),
                    'type' => 'text',
                    'label' => __('Promptpay Number', 'wc-promptpay-gateway'),
                    'desc_tip' => true,
                    'description' => __('Please input number only', 'wc-promptpay-gateway'),
                ),
                'description' => array(
                    'title' => __('Description', 'wc-promptpay-gateway'),
                    'type' => 'textarea',
                    'default' => __('Pay securely with Promtpay Payment Gateway', 'wc-promptpay-gateway'),
                    'description' => __('This controls the description which the user sees during checkout', 'wc-promptpay-gateway'),
                ),
            );
        }

        /**
         * Admin Panel Options
         * - Options for bits like 'title' and availability on a country-by-country basis
         *
         * @access public
         * @return void
         */
        public function admin_options()
        {
            ?>
            <h3><?php _e('Promptpay Checkout', 'wc-promptpay-gateway'); ?></h3>

            <table class="form-table">
                <?php $this->generate_settings_html(); ?>
            </table>
            <?php
        }

        /**
         * Process the payment and return the result
         *
         * @access public
         * @param int $order_id
         * @return array
         */
        public function process_payment($order_id)
        {
            $order = wc_get_order( $order_id );
            
            // Generate QR Code
            $promptpay = new \KS\PromptPay();

            $target = $this->promptpay_number;
            $promptpay_dir = Boostpress\Plugins\WC_Promptpay_Gateway\Utilities::get_qr_dir();
            $promptpay_dir_url = Boostpress\Plugins\WC_Promptpay_Gateway\Utilities::get_qr_dir_uri();
            $qr_name = Boostpress\Plugins\WC_Promptpay_Gateway\Utilities::get_qr_name($order_id);
            
            // ljsherlock save the url in the order meta for use outside of the plugin.
            $order->update_meta_data('qr_code_img_url', $promptpay_dir_url . '/' . $qr_name );

            $savePath = $promptpay_dir.'/'.$qr_name;
            $amount = $order->get_total();
            $amount = round($amount, 2);
            $promptpay->generateQrCode($savePath, $target, $amount, 200);

            // Mark as on-hold (we're awaiting the payment)
            $order->update_status( 'on-hold', __('Awaiting Promptpay payment', 'wc-promptpay-gateway') );

            // Reduce stock levels
            wc_reduce_stock_levels( $order_id );

            // Remove cart
            WC()->cart->empty_cart();

            // Return thankyou redirect
            return array(
                'result'    => 'success',
                'redirect'  => $this->get_return_url( $order ),
            );
        }

        /**
         * Show More options
         *
         * @access public
         * @param void
         * @return void
         */
        public function payment_fields() {
            global $woocommerce;

            $promptpay  = '';

            if($this->promptpay_type == 'mobile'){
                $promptpay = $this->promptpay_number;
                $promptpay = $this->format_mobile_number($promptpay);
            }else{
                $promptpay = $this->promptpay_number;
                $promptpay = $this->format_idcard($promptpay);
            }
            $amount = $woocommerce->cart->get_total();
            $description = $this->description;
            ?>
            <p><?php echo $description; ?></p>
            <table>
                <tr>
                    <td>Promptpay Number</td>
                    <td><?php echo $promptpay; ?></td>
                <tr>
                <tr>
                    <td>Amount</td>
                    <td><?php echo $amount; ?></td>
                <tr>
            </table>
            <?php
        }

        /**
         * Output for the order received page.
         *
         * @param int $order_id
         * @ref https://github.com/kittinan/php-promptpay-qr
         */
        public function thankyou_page( $order_id )
        {   
            $promptpay_dir_uri = Boostpress\Plugins\WC_Promptpay_Gateway\Utilities::get_qr_dir_uri();
            $qr_name = Boostpress\Plugins\WC_Promptpay_Gateway\Utilities::get_qr_name($order_id);

            ?>
            <div style="max-width: 35%; min-width: 299px; border: 1px solid; margin: auto; margin-bottom: 3em; border-radius: 10px; padding: 10px;margin-left: 0;
}">
            <?php

            printf('<img src="https://vinestovino.net/wp-content/uploads/2024/01/prompt-pay.jpg" style="width: 130px;display: block; margin: auto;" />');
            printf('<img style="display: block; margin:auto; margin-bottom: 1em;"src="%s" />', $promptpay_dir_uri.'/'.$qr_name);
            printf('<p style="text-align:center;" class="scantxt">To complete your order, please scan the QR code with your Banking app to transfer your payment.</p>');

            ?>
            </div>
            <?php

        }

        /**
         * Use for format thai mobile number
         * @ref https://arjunphp.com/format-phone-number-using-php/
         * @param string $mobile
         * @return string $mobile
         */
        public function format_mobile_number($mobile){
            if(empty($mobile)){
                return;
            }

            $mobile = preg_replace("/^1?(\S{3})(\S{3})(\S{4})$/", "$1-$2-$3", $mobile);
            return $mobile;
        }

        /**
         * Use for format thai National ID Card
         * @ref https://arjunphp.com/format-phone-number-using-php/
         * @param string $idcard
         * @return string $idcard
         */
        public function format_idcard($idcard){
            if(empty($idcard)){
                return;
            }

            $idcard = preg_replace("/^1?(\S{1})(\S{4})(\S{5})(\S{1})(\S{2})$/", "$1-$2-$3-$4-$5", $idcard);
            return $idcard;
        }

    }
}