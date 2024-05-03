<?php
// If this file is called directly, abort.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}
	
	/**
	 * Define the Freemius API related functionality
	 *
	 * @link       https://www.thedotstore.com
	 * @since      4.2.0
	 *
	 * @package    Woocommerce_Dynamic_Pricing_And_Discount_Pro
	 * @subpackage Woocommerce_Dynamic_Pricing_And_Discount_Pro/includes
	 */
	
	/**
	 * Define the Freemius API related functionality.
	 *
	 * This class defines all code necessary to run during the plugin's activation.
	 *
	 * @since      4.2.0
	 * @package    Woocommerce_Dynamic_Pricing_And_Discount_Pro
	 * @subpackage Woocommerce_Dynamic_Pricing_And_Discount_Pro/includes
	 * @author     Thedotstore <inquiry@thedotstore.com>
	 */
	class Woocommerce_Dynamic_Pricing_And_Discount_Pro_Freemius_API extends Freemius {
		/**
         * Initialize the class and set its properties.
         *
         * @since    4.2.0
         */
        public function __construct() {
        }

        public function wdpad_freemius_activate() {
            
            check_ajax_referer( 'wcdrfc_fs_nonce', 'security' );

            $fs = wcdrfc_fs();
            
            $site = $fs->get_site();

            $available_license = ( $fs->is_free_plan() && ! fs_is_network_admin() ) ? true : false;  
            
            $license_id = filter_input( INPUT_POST, 'license_key', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            
            if( $available_license && $license_id ){
                
                if ( ! $fs->has_api_connectivity()  ) {
                    // No connectivity OR the user already opted-in to Freemius.
                    wp_send_json_error( array( 'message' => esc_html__( 'It looks like the API connection failed.', 'woo-conditional-discount-rules-for-checkout' )), 200 );
                }
                
                //This is provided by Leo (freemius developer)
                $return_data = $fs->activate_migrated_license( $license_id );
               
                if($return_data['success']){
                    wp_send_json_success( array( 'message' => esc_html__( 'Plugin has been activated!', 'woo-conditional-discount-rules-for-checkout' )), 200 );
                } else {
                    wp_send_json_error( array( 'message' => $return_data['error'] ), 200 );
                }
            }
            wp_send_json_error( array( 'message' => esc_html__( 'It looks like the license activation failed.', 'woo-conditional-discount-rules-for-checkout' )), 200 );

        }

        public function wdpad_freemius_deactivate(){
            
            check_ajax_referer( 'wcdrfc_fs_nonce', 'security' );

            $fs = wcdrfc_fs();

            if ( ! $fs->has_api_connectivity()  ) {
                // No connectivity OR the user already opted-in to Freemius.
                wp_send_json_error( array( 'message' => esc_html__( 'It looks like the API connection failed.', 'woo-conditional-discount-rules-for-checkout' )), 200 );
            }

            $fs->_deactivate_license();

            $site = $fs->get_site();
            $available_free_license = ( ! fs_is_network_admin() ) ? $fs->_get_available_premium_license( $site->is_localhost() ) : null;
            
            if ( is_object( $available_free_license ) ) {
                // If free plan activated it's means Plan has been deactivated.
                wp_send_json_success( array( 'message' => esc_html__( 'Plugin has been deactivated!', 'woo-conditional-discount-rules-for-checkout' )), 200 );
            } else {
                //Else it's not deactivated.
                wp_send_json_error( array( 'message' => esc_html__( 'It looks like the license deactivation failed.', 'woo-conditional-discount-rules-for-checkout' )), 200 );
            }
        }

        public function wdpad_freemius_sync(){
            
            check_ajax_referer( 'wcdrfc_fs_nonce', 'security' );

            $fs = wcdrfc_fs();
            
            if ( ! $fs->has_api_connectivity()  ) {
                // No connectivity OR the user already opted-in to Freemius.
                wp_send_json_error( array( 'message' => esc_html__( 'It looks like the API connection failed.', 'woo-conditional-discount-rules-for-checkout' )), 200 );
            }

            $fs->_sync_licenses();
            wp_send_json_success( array( 'message' => esc_html__( 'Plugin licenses has been synced!', 'woo-conditional-discount-rules-for-checkout' )), 200 );
        }
	}