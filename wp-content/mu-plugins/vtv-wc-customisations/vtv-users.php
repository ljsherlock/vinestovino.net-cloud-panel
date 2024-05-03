<?php

/**
 * How will I test this?
 * 
 * I will need to note the feature for each of the grouped hooks
 * I will also need to know the journey required
 * What kind of user is required
 * And any other variables.
 */

/**
 * Snippets / Features to disable and test:
 * 'Company user type'
 * 'Remove Credit Card for Company'
 * 'User Meta FIelds'
 * 'User Registration'
 */

if ( ! class_exists( 'VTVUsers' ) ) {

    class VTVUsers {

        public function __construct() {
            // In order of functional importance
            // grouped by feature / function

            /**
             * View profile page of any user.
             * Confirm company role is in drop down.
             * 
             * Edit: Once added, a role has to be removed, so if the role already exists
             * in the database, nothing is going to happen.
             * Ref: https://github.com/WordPress/wordpress-develop/blob/efead24c16b84bfb1b5b5e83748e8654e77e54d2/src/wp-includes/class-wp-roles.php#L156-L159
             */
            add_filter( 'after_setup_theme', array( $this, 'add_company_user_type' ) );
            
            /**
             * View registration page of any user
             * Confirm fields are rendered
             */
            // add_filter( 'woocommerce_register_form_start', array( $this, 'wc_add_html_register_fields' ) );
            add_filter( 'user_register', array( $this, 'wc_save_html_register_fields' ), 10, 1 );

            /**
             * View profile page of any user.
             * Confirm fields are rendered
             */
            add_filter( 'show_user_profile', array( $this, 'add_html_profile_fields' ) );
            add_filter( 'edit_user_profile', array( $this, 'add_html_profile_fields' ) );

            /**
             * View profile page of any user.
             * Change all fields
             * Confirm that changes have been saved.
             */
            add_filter( 'personal_options_update', array( $this, 'save_html_profile_fields' ) );
            add_filter( 'edit_user_profile_update', array( $this, 'save_html_profile_fields' ) );

            /**
             * View account details page of any user.
             * Confirm fields are rendered
             * Change all fields
             * Confirm that changes have been saved.
             */
            add_filter( 'woocommerce_edit_account_form', array( $this, 'wc_add_account_details' ) );
            add_filter( 'woocommerce_save_account_details', array( $this, 'wc_save_account_details' ) );

            /**
             * As a user of role company
             * Add items to basket
             * Go to checkout
             * Comfirm that credit card is disabled 
             */
            add_filter( 'body_class', array( $this, 'add_role_to_body_class' ) );
            wp_enqueue_script('wc-hide-credit-card',
                VTV_WC_CUSTOMISATIONS_URL . '/assets/wc-hide-credit-card.js', array('jquery'),'1.0.0',
            );
        }

        public function add_company_user_type () {
            
            add_role( 'company', 'Company', array( 'read' => true ) );
        }

        public function add_role_to_body_class ( $classes ) {

            $user = wp_get_current_user();
            $roles = ( array ) $user->roles;
            if(isset($roles[0])) {
                return array_merge( $classes, array( 'user-type-' . $roles[0] ) );
            }
        }
    
        public function add_html_profile_fields ( $user ) {
            ?>
            <h3><?php _e("Company Information", "blank"); ?></h3>
        
            <table class="form-table">
            <tr>
                <th><label for="tax_id"><?php _e("Tax ID"); ?></label></th>
                <td>
                    <input type="text" name="tax_id" id="tax_id" value="<?php echo esc_attr( get_the_author_meta( 'tax_id', $user->ID ) ); ?>" class="regular-text" /><br />
                    <span class="description"><?php _e("Please enter your tax id."); ?></span>
                </td>
            </tr>
            </table>
            <?php 
        }
        
        public function save_html_profile_fields ( $user_id ) {
            
            if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'update-user_' . $user_id ) ) {
                return;
            }
            
            if ( !current_user_can( 'edit_user', $user_id ) ) { 
                return false; 
            }
            update_user_meta( $user_id, 'tax_id', $_POST['tax_id'] );
        }

        public function wc_add_html_register_fields () {
            ?>
            <div style="display: flex; column-gap: 2em;" class="form-row form-row-wide">
                <div style="display: flex;">
                    <input type="radio" name="user_type" id="individual" value="individual" checked />
                    <label for="individual" style="margin-left: 8px;">Individual</label>
                </div> 	
                <div style="display: flex;">	
                    <input type="radio" name="user_type" id="company"  value="company" />   
                    <label for="company" style="margin-left: 8px;">Company</label>
                </div> 
            </div>	
        
            <p class="form-row form-row-first">
                <label for="reg_billing_first_name"><?php _e( 'First name', 'woocommerce' ); ?><span class="required">*</span></label>
                <input type="text" class="input-text" name="billing_first_name" id="reg_billing_first_name" required />
            </p>
            <p class="form-row form-row-last">
                <label for="reg_billing_last_name"><?php _e( 'Last name', 'woocommerce' ); ?><span class="required">*</span></label>
                <input type="text" class="input-text" name="billing_last_name" id="reg_billing_last_name" required />
            </p>
            <div id="hidden_company_fields" style="display:none; transition: 1s opacity ease-in; opacity: 0;">
                <p class="form-row form-row-wide">
                    <label for="reg_billing_company"><?php _e( 'Company', 'woocommerce' ); ?></label>
                    <input type="text" class="input-text" name="billing_company" id="reg_billing_company" />
                </p> 
                <p class="form-row form-row-wide">
                    <label for="tax_id"><?php _e( 'Tax ID', 'woocommerce' ); ?></label>
                    <input type="text" class="input-text" name="tax_id" id="reg_tax_id" />
                </p> 
            </div>
            <p class="form-row form-row-wide">
                <label for="reg_billing_phone"><?php _e( 'Phone', 'woocommerce' ); ?></label>
                <input type="text" class="input-text" name="billing_phone" id="reg_billing_phone" />
            </p>
            <div class="clear"></div>
        
            <script>
                document.getElementById('company').addEventListener('click', function() {
                    document.getElementById('hidden_company_fields').style.display = 'block';
                    document.getElementById('hidden_company_fields').style.opacity = '1';
                    
                });
                document.getElementById('individual').addEventListener('click', function() {
                    document.getElementById('hidden_company_fields').style.opacity = '0';
                    document.getElementById('hidden_company_fields').style.display = 'none';
                })
            </script>
            <?php
        }

        public function wc_save_html_register_fields ( $customer_id ) {

            if ( isset( $_POST['billing_first_name'] ) ) {
                // WordPress default first name field.
                update_user_meta( $customer_id, 'first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
                update_user_meta( $customer_id, 'account_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
        
                // WooCommerce billing first name.
                update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
            }
        
            if ( isset( $_POST['billing_last_name'] ) ) {
                // WordPress default last name field.
                update_user_meta( $customer_id, 'last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
                update_user_meta( $customer_id, 'account_last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
        
                // WooCommerce billing last name.
                update_user_meta( $customer_id, 'billing_last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
            }
        
            if ( isset( $_POST['tax_id'] ) ) {
                // WordPress default last name field.
                update_user_meta( $customer_id, 'tax_id', sanitize_text_field( $_POST['tax_id'] ) );
            }
        
            if ( isset( $_POST['billing_company'] ) ) {
                // WordPress default last name field.
                update_user_meta( $customer_id, 'billing_company', sanitize_text_field( $_POST['billing_company'] ) );
            }
            
            if ( isset( $_POST['billing_phone'] ) ) {
                update_user_meta( $customer_id, 'billing_phone', sanitize_text_field( $_POST['billing_phone'] ) );
            }
            
            if( $_POST['user_type'] === 'company' ) {
                $u = new WP_User( $customer_id );
                $u->set_role( 'company' );
    
                // Set new name + nickname
                $new_name = $_POST['billing_company'];
                
                // Update user user_login
                wp_update_user(
                    array( 
                        'ID'         => $customer_id,
                        'user_nicename' => $new_name,
                        'display_name' => $new_name
                    )
                );
            
                
                // Update user meta
                update_user_meta( $customer_id, 'nickname', $new_name );
            }
        
            wp_set_current_user($customer_id);
            wp_set_auth_cookie($customer_id);
            wp_redirect( home_url() );
            // exit();
        }

        public function wc_add_account_details () {

            $user = wp_get_current_user();
            $roles = ( array ) $user->roles;

            if(	$roles[0] === 'company' || $roles[0] === 'administrator'):
                
                ?>
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="tax_id"><?php esc_html_e( 'Company Name', 'your-text-domain' ); ?></label>
                    <input 
                        type="text"
                        class="woocommerce-Input woocommerce-Input--text input-text" 
                        name="billing_company" 
                        id="billing_company"    
                        value="<?php echo esc_attr( $user->billing_company ); ?>" 
                    />
                </p>
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="tax_id"><?php esc_html_e( 'Tax ID', 'your-text-domain' ); ?></label>
                    <input 
                        type="text" 
                        class="woocommerce-Input woocommerce-Input--text input-text"
                        name="tax_id" id="tax_id" 
                        value="<?php echo esc_attr( $user->tax_id ); ?>" 
                    />
                </p>
                <?php

            endif;
        }

        public function wc_save_account_details  ($user_id ) {

            if ( isset( $_POST['billing_company'] ) ) {
                update_user_meta( $user_id, 'billing_company', sanitize_text_field( $_POST['billing_company'] ) );
            }

            if ( isset( $_POST['tax_id'] ) ) {
                update_user_meta( $user_id, 'tax_id', sanitize_text_field( $_POST['tax_id'] ) );
            }
        }
    }
}