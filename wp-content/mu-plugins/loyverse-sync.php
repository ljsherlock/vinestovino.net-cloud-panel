<?php 
/**
 * Plugin Name: Loyverse Sync
 * Description: Sync WooCommerce products to Loyverse when they are updated
 * Version: 1.0
 * Author: Lewis Sherlock
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package EndurancePageCache
 */

 /**
  * Postman: https://web.postman.co/workspace/Loyverse~b710cdc8-4e76-4049-85fd-9c937ade3106/request/2866201-6e7d861d-c8b2-46f7-b2ae-41381b614ba5?tab=params
  * API Reference: https://developer.loyverse.com/docs/#tag/Items/paths/~1items/post
  */

if ( ! class_exists( 'Loyverse_Sync' ) ) {

    class Loyverse_Sync {

        /**
		 * Loyverse API Authorisation
		 *
		 * @var string
        */
        public $loyverse_auth = 'Authorization: Bearer 7bab7020ba7a48979a472db22faaeb1b';

        /**
		 * Loyverse API URL
		 *
		 * @var string
        */
        public $loyverse_api_url = 'https://api.loyverse.com/v1.0/items/';

        /**     
         * Loyverse Data
         * 
         * @var array
         */
        public $loyverse_id = '';

        /**
         * Loyverse Data
         * 
         * @var array
         */
        public $loyverse_data = array();

        /**
         * Original Product
         * 
         * The product object before changes were made
         * 
         * @var object
         */
        public $original_product;

        /**
         * Updated Product
         * 
         * The product object after changes were made
         * 
         * @var object
         */
        public $updated_product;
        
        /**
         * Original Post
         * 
         * The post object before changes were made. This is useful for getting WP properties 
         * 
         * @var object
         */
        public $original_post;

        /**
         * Post ID
         * 
         * @var int $post_id
         */
        public $post_id;
        
        /**
         * Post 
         * 
         * @var int $post_id
         */
        public $post;

        /**
         * Loyverse Sync Constructor
         */
        public function __construct() {
            add_filter( 'admin_notices', array( $this, 'vines_admin_notices1' ));
            add_filter( 'admin_notices', array( $this, 'vines_admin_notices2' ));
            add_filter( 'admin_notices', array( $this, 'vines_admin_notices_add' ));

            add_filter( 'pre_post_update', array( $this, 'pre_post_update' ), 10, 2);
            add_action( 'save_post', array( $this, 'save_post_product' ), 10, 3);
        }

        /**
         * Sets globals $original_product so that it can be compared to the updated 
         * product in save_post_product().
         * 
         * https://developer.wordpress.org/reference/hooks/pre_post_update/
         * 
         * @param int $post_id
         * @param array $post_data
         * 
         */
        public function pre_post_update($post_id, $post_data) {
            $this->original_product = wc_get_product($post_id);
            $this->original_post = get_post($post_id);
        }

        /**
         * Main function to sync products between WooCommerce and Loyverse.
         * 
         * Hook: save_post (https://developer.wordpress.org/reference/hooks/save_post/)
         * 
         * @param int post_id
         * @param object post (WP_Post)
         * @param boolean update
         * 
         */
        public function save_post_product($post_id, $post, $update) {
            $this->post = $post;
            $this->updated_product = wc_get_product( $post );

            // so that the hook only runs when a product is being set to publish e.g. save.
            if ($post->post_status != 'publish' || !$this->updated_product || $post->post_type != 'product' ) {
                return;
            } 

            // Must be updating existing, and be of type product.
            if( $this->is_new_post() ) {
                $response = $this->add_loyverse_product($post_id);

                $this->send_admin_notices($response, 'vinves_add_notice_query_var_successful_add');
            } else {
                if( $this->changes_present() ) {
                    $response = $this->update_loyverse_product();

                    $this->send_admin_notices($response, 'vinves_add_notice_query_var_success');
                } else {
                    /**
                     * @todo this is where I can test to see if there is difference betwene WP and Loyverse.
                     */
                    return;
                }
            }
        }

        /**
         * Send response admin notices for success and fail.
         * 
         * @param boolean $response
         * @param string $success_function
         */
        public function send_admin_notices($response, $success_function) {
            if($response) {
                add_action( 'redirect_post_location', array( $this, $success_function ));
            } else {
                add_action( 'admin_notices', array( $this, 'vinves_add_notice_query_var_fail' ));
            }
        }

        /**
         * Create new Loyverse product. I could send the product name first and use the returned object 
         * to update the rest of the data.
         * 
         * @param int $post_id
         * 
         * @return boolean
         */
        public function add_loyverse_product($post_id) {
            $this->loyverse_data['item_name'] = $this->updated_product->get_name();

            $response = $this->send_product_data_to_loyverse();

            $this->loyverse_data = json_decode($response);
            $this->loyverse_id = $this->loyverse_data->id;

            $this->set_data_to_add($post_id);
                
            $response = $this->send_product_data_to_loyverse(); // true/false

            if(isset($response->errors)) {
                $this->send_email_to_develoepr( 'Add Error', $response );
                return false;
            } else {
                return true;
            }
        }

        /**
         * update_loyverse_product
         * 
         * @return boolean
         */
        public function update_loyverse_product() {

            // Get Loyverse ID to match the Loyverse product
            $loyverseID = $this->updated_product->get_attribute('Loyverse ID');

            // Don't continue if it isn't set
            if ( ! empty( $loyverseID ) ) {
                $this->loyverse_data = json_decode( $this->get_current_loyverse_item( $loyverseID ) );

                $this->set_data_to_update();
                
                $response = $this->send_product_data_to_loyverse();

                if(isset($response->errors)) {
                    $this->send_email_to_develoepr(
                        'Update Error',
                        $response,
                    );
                    return false;
                } else {
                    return true;
                }
            } else {
                $this->send_email_to_develoepr(
                    'Missing Loyverse ID',
                    'The following product does not have a loyverse ID' . $this->post->get_permalink(),
                );
                return;
            }
        }

        /**
         * Checks if a post is new depending on the status of the post
         * 
         * @return boolean
         */
        public function is_new_post() {
            $status = get_post_status($this->original_post);
            return $status === 'auto-draft' || $status === 'draft';
        }

        /**
         * Compares the two products (original & updated) to check if there
         *  are changes or not
         * 
         * @return boolean 
         */
        public function changes_present() {
            
            $updated_product_data = $this->updated_product->get_data();
            $original_product_data = $this->original_product->get_data();
            
            unset($updated_product_data['date_modified']);
            unset($original_product_data['date_modified']);

            return $updated_product_data != $original_product_data;
        }
        
        /** 
         * Alters the Loyverse Data based on the updated WC product
        */
        public function set_data_to_update() {
            // Title
            if($this->original_product->get_name() !== $this->updated_product->get_name()) {
                $this->loyverse_data->item_name = $this->updated_product->get_name();
            }
            // Description
            if($this->original_product->get_description() !== $this->updated_product->get_description()) {
                $this->loyverse_data->description = $this->updated_product->get_description();
            }
            // Price
            if( isset( $_POST['_regular_price'] ) ) {
                $this->loyverse_data->variants[0]->default_price = (int) $_POST['_regular_price'];
                $this->loyverse_data->variants[0]->stores[0]->price = (int) $_POST['_regular_price'];
            }
        }

        /** 
         * Alters the Loyverse Data based on the updated WC product
         * 
         * The only difference is that this function doesn't have conditions
         * because it is a new product.
         * 
         * @param int $post_id
        */
        public function set_data_to_add($post_id) {

            $meta_id = $this->wcproduct_set_attributes(
                $post_id,
                array('Loyverse ID' => $this->loyverse_id)
            );

            update_post_meta( $post_id, '_get', $this->loyverse_data->variants[0]->sku );

            // Loyverse sets the pricing type to VARIABLE by default.
            $this->loyverse_data->variants[0]->default_pricing_type = "FIXED";
            $this->loyverse_data->variants[0]->stores[0]->pricing_type = "FIXED";
            // Title
            $this->loyverse_data->item_name = $this->updated_product->get_name();
            // Description
            $this->loyverse_data->description = $this->updated_product->get_description();
            // Price
            $this->loyverse_data->variants[0]->default_price = (int) $_POST['_regular_price'];
            $this->loyverse_data->variants[0]->stores[0]->price = (int) $_POST['_regular_price'];

            $sku = $this->updated_product->get_sku();
            
            if ( !empty($sku) ) {
                $this->loyverse_data->variants[0]->sku = $sku;   
            }
        }
        
        /**
         * Sets one or more product attributes.
         * 
         * @param int $post_id
         * @param array $attributes
         * 
         * @return int|boolean
         */
        public function wcproduct_set_attributes($post_id, $attributes) {

            $i = 0;
            // Loop through the attributes array
            foreach ($attributes as $name => $value) {
                $product_attributes[$i] = array (
                    'name' => htmlspecialchars( stripslashes( $name ) ), // set attribute name
                    'value' => $value, // set attribute value
                    'position' => 1,
                    'is_visible' => 1,
                    'is_variation' => 1,
                    'is_taxonomy' => 0
                );
        
                $i++;
            }
        
            // Now update the post with its new attributes
            return update_post_meta($post_id, '_product_attributes', $product_attributes);
        }
        
        /**
         * Creates or updates a Loyverse item
         * 
         * @return json $errors or created/updated $item
         */
        public function send_product_data_to_loyverse() {
            $curl = curl_init();
        
            $jsonData = json_encode($this->loyverse_data);
        
            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->loyverse_api_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $jsonData,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    $this->loyverse_auth,
                    'Cookie: AWSALB=Ek8+3lYDHs8sdN1mfINOxVSaBBwsq2qWjurLKMFQoZ1oMRJRg65MuyuASPaCLV/58k691eDFo3yvUL5O5wk0v+gPZdDC37SYYtlWJyG/RFzMYTbijFB9vwuc7Lro; AWSALBCORS=Ek8+3lYDHs8sdN1mfINOxVSaBBwsq2qWjurLKMFQoZ1oMRJRg65MuyuASPaCLV/58k691eDFo3yvUL5O5wk0v+gPZdDC37SYYtlWJyG/RFzMYTbijFB9vwuc7Lro'
                ),
            ));
        
            $response = curl_exec($curl);
            curl_close($curl);
        
            return $response;
        }
        
        /**
         * Gets a single Loyverse item
         * 
         * @param int $item_id
         * 
         * @return 
         */
        public function get_current_loyverse_item($item_id) {
            $curl = curl_init();
        
            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->loyverse_api_url . $item_id,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    $this->loyverse_auth,
                    'Cookie: AWSALB=TxncKbg75wZ9K/MIHydP7KXnGvEtfIbOv9if0TajQBjPU7ntpbo8QP6Si221I8rt+a03fRpaUXtv/Di14q6gXK6BIVSzcCJqDiO+1uO+y/21Op89KWU38C6TE3Fa; AWSALBCORS=TxncKbg75wZ9K/MIHydP7KXnGvEtfIbOv9if0TajQBjPU7ntpbo8QP6Si221I8rt+a03fRpaUXtv/Di14q6gXK6BIVSzcCJqDiO+1uO+y/21Op89KWU38C6TE3Fa'
                ),
            ));
        
            $response = curl_exec($curl);
            curl_close($curl);
                
            return $response;
        }

        /**
         * Send debug email to me
         * 
         * @param string $subject
         * @param string $content
         * 
         * @return boolean
         */
        public function send_email_to_develoepr($subject, $content) {
            wp_mail(
                'developer@ljsherlock.com',
                $subject, 
                $content, 
            );
            add_action( 'admin_notices', 'vinves_add_notice_query_var_fail' );
        }

        /**
         * Set successful update notice
         * 
         * @param string $location
         * @param int $post_id
         * 
         * @return string
         */
        public function vinves_add_notice_query_var_success( $location ) {

            remove_filter( 'redirect_post_location', array( $this, 'vinves_add_notice_query_var_success' ));

            return add_query_arg( array( 'LOYVERSE_SUCCESS' => 'ID' ), $location );
        }

        /**
         * Set successful add notice
         * 
         * @param string location
         * 
         * @return string
         */
        public function vinves_add_notice_query_var_successful_add( $location ) {

            remove_filter( 'redirect_post_location', array( $this, 'vinves_add_notice_query_var_successful_add' ));

            return add_query_arg( array( 'LOYVERSE_SUCCESS_ADD' => $this->loyverse_id ), $location );
        }
        
        /**
         * Set failed add/update notice
         * 
         * @param string location
         * 
         * @return string
         */
        public function vinves_add_notice_query_var_fail( $location ) {
            
            remove_filter( 'redirect_post_location', array( $this, 'vinves_add_notice_query_var_fail' ));

            return add_query_arg( array( 'LOYVERSE_FAILURE' => 'ID' ), $location );
        }
        
        /**
         * Print successful Loyverse update 
         */
        public function vines_admin_notices1() {
            
            if ( ! isset( $_GET['LOYVERSE_SUCCESS'] ) ) {
                return;
            }

              $this->print_notice('success', 'Loyverse item updated.');
        }

        /**
         * Print successful Loyverse add 
         */
        public function vines_admin_notices_add() {
            
            if ( ! isset( $_GET['LOYVERSE_SUCCESS_ADD'] ) ) {
                return;
            }

            $this->print_notice('success', 'Loyverse item added (ID: ' . $_GET['LOYVERSE_SUCCESS_ADD'] . ').');
        }
        
        /**
         * Print failed Loyverse add/update 
         */
        public function vines_admin_notices2() {

            if ( ! isset( $_GET['LOYVERSE_FAILURE'] ) ) {
                return;
            }
            
            $this->print_notice('error', 'Loyverse item failed to add/update.');
        }

        /**
         * @param string $notice_type
         * @param string $notice
         */
        public function print_notice($notice_type, $notice) {
            ?>
                <div id="loyverse" class="notice notice-<?= $notice_type ?> is-dismissible">
                    <p><?php _e( $notice, 'your-text-domain' ); ?></p>
                </div>
            <?php
        }

        /**
         * might be unnecessary but keeping it just incase.
         * 
         * Coveat: I think this fails because the post_data will always be ahead in time and so 
         * will always return TRUE.
         * 
         * @param object $post
         * 
         * @return boolean
         */
        public function is_new($post) {
            return $post->post_date === $post->post_modified;
        }
    }

    $ls = new loyverse_Sync();
}