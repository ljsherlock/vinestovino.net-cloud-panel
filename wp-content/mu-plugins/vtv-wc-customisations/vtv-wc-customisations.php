<?php 
/**
 * Plugin Name: WooCommerce Customisations
 * Description: Customisations of WooCommerce for VinestoVino
 * Version: 1.0
 * Author: Lewis Sherlock
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

include_once('vtv-users.php');
// include_once('features/feat-product-suppliers.php');
// include_once('features/feat-user-suppliers-visible.php');

/**
 * -------------------------- FEATURES ------------------------------
 */
// $WC_Product_Suppliers = new WC_Product_Suppliers();

/** 
 * our next feature will be Adding 'Visible Suppliers' to Users.
 * - comma sep list of suppliers in meta field
 * -  
*/



if ( ! class_exists( 'VTVWooCommerceCustomisations' ) ) {
    class VTVWooCommerceCustomisations {

        public function __construct() {
            define( 'VTV_WC_CUSTOMISATIONS_DIR', plugin_dir_path( __FILE__ ) ); // has trailing slash
            define( 'VTV_WC_CUSTOMISATIONS_URL', plugin_dir_url( __FILE__ ) );

            add_filter( 'woocommerce_email_subject_new_order', array( $this, 'neworder_to_admin_email' ), 10, 2);
            add_filter( 'wpo_wcpdf_bulk_actions', array( $this, 'tax_invoice_action' ) );
            add_filter( 'script_loader_src', array( $this, 'tax_invoice_js' ), 10,  2);

            $this->hooks_for_emails();
        }

        public function hooks_for_emails () {
            add_filter( 'woocommerce_email_classes', array( $this, 'custom_emails' ), 10, 2);
            
            // add_filter( 'bulk_actions-wc-orders', array( $this, 'email_invoice_bulk_action_orders_list' ), 20);
            add_filter( 'bulk_actions-woocommerce_page_wc-orders', array( $this, 'email_invoice_bulk_action_orders_list' ), 20);
            
            // add_filter( 'handle_bulk_actions-edit-shop_order', array( $this, 'email_invoice_handle_bulk_action_orders_list' ), 10, 3 );
            // add_filter( 'handle_bulk_actions-wc-orders', array( $this, 'email_invoice_handle_bulk_action_orders_list' ), 10, 3 );
            add_filter( 'handle_bulk_actions-woocommerce_page_wc-orders', array( $this, 'email_invoice_handle_bulk_action_orders_list' ), 10, 3 );
            
            add_action( 'admin_notices', array( $this, 'email_invoice_bulk_action_admin_notice') );
        }

        /**
         * Adds customer first and last name to admin new order email subject
         * 
         * @param String $subject
         * @param objet $order
         */
        public function neworder_to_admin_email ($subject, $order) {
            $subject = 'WINE ORDER #' . $order->get_order_number() . ' [ ' . $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(). ' ] ';
            
            return $subject;
        }

        /**
         * 
         */
        public function tax_invoice_action ($actions) {
            $actions['tax-invoice'] = 'PDF Tax Invoice';

            return $actions;
        }

        /**
         * 
         */
        public function tax_invoice_js ($src, $handle ) {
            // Only filter the specific script we want.
            if ( 'wpo-wcpdf' === $handle && str_contains($src, '/assets/js/order-script') ) {
                // Add the argument to the exisitng URL.
                $src = VTV_WC_CUSTOMISATIONS_URL . 'assets/wpo-wcpdf-order-script.js';
            }
    
            // Return the filtered URL.
            return $src;
        }

        /**
         * 
         */
        public function custom_emails($emails) {
            include_once dirname( __FILE__ ) . '/emails/class-wc-email-customer-invoice-reminder.php';   
            
            $emails['WC_Email_Customer_Invoice_Reminder'] = new WC_Email_Customer_Invoice_Reminder();

            return $emails;
        }

        /* Add to admin order list bulk dropdown a custom action 'Email Invoice / Order Details to Customers' */
        public function email_invoice_bulk_action_orders_list( $actions ) {
            $actions['send_customer_invoice_reminder'] = __( 'Send Payment Reminder', 'woocommerce' );

            return $actions;
        }

        // Make the action from selected orders
        public function email_invoice_handle_bulk_action_orders_list( $redirect_to, $action, $post_ids ) {
            // die('test');
            
            if ( $action !== 'send_customer_invoice_reminder' )
                return $redirect_to; // Exit

            $processed_ids = array();

            foreach ( $post_ids as $orderid ) {

                // Send customer order email
                WC()->mailer()->emails['WC_Email_Customer_Invoice_Reminder']->trigger($orderid);

                // update count
                $processed_ids[] = $orderid;
            }

            return $redirect_to = add_query_arg( array(
                'send_emails' => '1',
                'processed_count' => count( $processed_ids ),
                'processed_ids' => implode( ',', $processed_ids ),
            ), $redirect_to );
        }

        // The results notice from bulk action on orders
        public function email_invoice_bulk_action_admin_notice() {
            if ( empty( $_REQUEST['send_emails'] ) ) return; // Exit

            $count = intval( $_REQUEST['processed_count'] );

            printf( '<div id="message" class="updated fade"><p>' .
                _n( 'Sent %s Payment Reminder Emails.',
                'Sent %s Payment Reminder Emails.',
                $count,
                'send_emails'
            ) . '</p></div>', $count );
        }
    }

    $vtvwcc = new VTVWooCommerceCustomisations();
}

$vtv_users =  new VTVUsers();
