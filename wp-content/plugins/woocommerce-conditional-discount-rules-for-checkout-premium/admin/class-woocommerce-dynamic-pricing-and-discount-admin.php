<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       http://www.multidots.com
 * @since      1.0.0
 * @package    Woocommerce_Dynamic_Pricing_And_Discount_Pro
 * @subpackage Woocommerce_Dynamic_Pricing_And_Discount_Pro/admin
 * @author     Multidots <inquiry@multidots.in>
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class Woocommerce_Dynamic_Pricing_And_Discount_Pro_Admin {
	const wdpad_post_type = 'wc_dynamic_pricing';
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;
	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;
	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 *
	 * @since    1.0.0
	 *
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles( $hook ) {
        if( false !== strpos( $hook, '_page_wcdrfc' )) {
			wp_enqueue_style( $this->plugin_name . '-jquery-ui-css', plugin_dir_url( __FILE__ ) . 'css/jquery-ui.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name . '-jquery-timepicker-css', plugin_dir_url( __FILE__ ) . 'css/jquery.timepicker.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name . 'font-awesome', plugin_dir_url( __FILE__ ) . 'css/font-awesome.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name . '-webkit-css', plugin_dir_url( __FILE__ ) . 'css/webkit.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name . 'main-style', plugin_dir_url( __FILE__ ) . 'css/style.css', array(), 'all' );
			wp_enqueue_style( $this->plugin_name . 'media-css', plugin_dir_url( __FILE__ ) . 'css/media.css', array(), 'all' );
			wp_enqueue_style( $this->plugin_name . 'select2-min', plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), 'all' );
            wp_enqueue_style( $this->plugin_name . 'plugin-new-style', plugin_dir_url( __FILE__ ) . 'css/plugin-new-style.css', array(), 'all' );
            if ( !(wcdrfc_fs()->is__premium_only() && wcdrfc_fs()->can_use_premium_code()) ) {
                wp_enqueue_style( $this->plugin_name . 'upgrade-dashboard-style', plugin_dir_url( __FILE__ ) . 'css/wcdrfc-upgrade-dashboard.css', array(), 'all' );
			}
            wp_enqueue_style( $this->plugin_name . 'plugin-setup-wizard', plugin_dir_url( __FILE__ ) . 'css/plugin-setup-wizard.css', array(), 'all' );
            wp_enqueue_style( 'wp-color-picker' );
		}
	}
	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts( $hook ) {

		wp_enqueue_style( 'wp-jquery-ui-dialog' );
		wp_enqueue_script( 'jquery-ui-accordion' );
        
		if( false !== strpos( $hook, '_page_wcdrfc' )) {
			wp_enqueue_script( $this->plugin_name . '-tablesorter-js', plugin_dir_url( __FILE__ ) . 'js/jquery.tablesorter.js', array( 'jquery' ), $this->version, false );
			if ( wcdrfc_fs()->is__premium_only() ) {
				if ( wcdrfc_fs()->can_use_premium_code() ) {
					wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woocommerce-dynamic-pricing-and-discount-admin__premium_only.js', array(
						'jquery',
						'jquery-ui-dialog',
						'jquery-ui-accordion',
						'jquery-ui-sortable',
                        'wp-color-picker',
					), $this->version, false );
				} else {
					wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woocommerce-dynamic-pricing-and-discount-admin.js', array(
						'jquery',
						'jquery-ui-dialog',
						'jquery-ui-accordion',
						'jquery-ui-sortable',
                        'wp-color-picker'
					), $this->version, false );
				}
			} else {
				wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woocommerce-dynamic-pricing-and-discount-admin.js', array(
					'jquery',
					'jquery-ui-dialog',
					'jquery-ui-accordion',
					'jquery-ui-sortable',
                    'wp-color-picker'
				), $this->version, false );
			}
			
			wp_enqueue_script( $this->plugin_name . '-select2', plugin_dir_url( __FILE__ ) . 'js/select2.min.js', array(
				'jquery',
				'jquery-ui-dialog',
				'jquery-ui-accordion',
				'jquery-ui-datepicker',
			) );

			wp_enqueue_script( $this->plugin_name . '-timepicker-js', plugin_dir_url( __FILE__ ) . 'js/jquery.timepicker.js', array( 'jquery' ), $this->version, false );
			
			wp_enqueue_script( 'jquery-tiptip' );
            wp_enqueue_script( 'jquery-blockui' );

			if ( wcdrfc_fs()->is__premium_only() ) {
                if( wcdrfc_fs()->can_use_premium_code() ) {
                    wp_localize_script( $this->plugin_name, 'coditional_vars', array( 
                            'dpb_api_url'                       => WDPAD_STORE_URL,
                            'ajaxurl'                          	=> admin_url( 'admin-ajax.php' ),
                            'plugin_url' 						=> plugin_dir_url( __FILE__ ),
                            'delete'                           	=> esc_html__( 'Delete', 'woo-conditional-discount-rules-for-checkout' ),
                            'cart_qty'                         	=> esc_html__( 'Cart Qty', 'woo-conditional-discount-rules-for-checkout' ),
                            'min_quantity'                     	=> esc_html__( 'Min Quantity', 'woo-conditional-discount-rules-for-checkout' ),
                            'max_quantity'                     	=> esc_html__( 'Max Quantity', 'woo-conditional-discount-rules-for-checkout' ),
                            'cart_weight'                      	=> esc_html__( 'Cart Weight', 'woo-conditional-discount-rules-for-checkout' ),
                            'min_weight'                       	=> esc_html__( 'Min Weight', 'woo-conditional-discount-rules-for-checkout' ),
                            'max_weight'                       	=> esc_html__( 'Max Weight', 'woo-conditional-discount-rules-for-checkout' ),
                            'cart_subtotal'                    	=> esc_html__( 'Cart Subtotal', 'woo-conditional-discount-rules-for-checkout' ),
                            'min_subtotal'                     	=> esc_html__( 'Min Subtotal', 'woo-conditional-discount-rules-for-checkout' ),
                            'max_subtotal'                     	=> esc_html__( 'Max Subtotal', 'woo-conditional-discount-rules-for-checkout' ),
                            'amount'                           	=> esc_html__( 'Amount', 'woo-conditional-discount-rules-for-checkout' ),
                            'product_qty_msg' 					=> esc_html__( 'This rule will only work if you have selected any one Product Specific option.', 'woo-conditional-discount-rules-for-checkout' ),
                            'product_count_msg' 				=> esc_html__( 'This rule will work if you have selected any one Product Specific option or it will apply to all products.', 'woo-conditional-discount-rules-for-checkout' ),
                            'note'              				=> esc_html__( 'Note: ', 'woo-conditional-discount-rules-for-checkout' ),
                            'warning_msg6'      				=> esc_html__( 'You need to select product specific option in Discount Rules for product based option', 'woo-conditional-discount-rules-for-checkout' ),
                            'error_msg' 	    				=> esc_html__( 'Please add Discount Rules value', 'woo-conditional-discount-rules-for-checkout' ),
                            'warning_msg_per_qty'               => esc_html__( 'Please choose atleast one product or product variation or category or tag condition', 'woo-conditional-discount-rules-for-checkout' ),
                            'per_product'						=> esc_html__( 'Apply on Products', 'woo-conditional-discount-rules-for-checkout' ),
                            'select2_per_product_ajax'          => 10,
                            'select2_per_category_ajax'         => 2,
                            'select2_category_placeholder'      => esc_html__( 'Select category', 'woo-conditional-discount-rules-for-checkout' ),
                            'select2_product_placeholder'       => esc_html__( 'Select product', 'woo-conditional-discount-rules-for-checkout' ),
                            'bogo_validate_msg'                 => esc_html__( 'Please select Buy and Get product fields to save the cofiguration.', 'woo-conditional-discount-rules-for-checkout' ),
                            'bogo_copy_buy_product_text'        => esc_html__( 'Copy buy products', 'woo-conditional-discount-rules-for-checkout' ),
                            'discount_cost_msg'                 => esc_html__( 'Please add discount value which will apply on cart/checkout.', 'woo-conditional-discount-rules-for-checkout' ),
                            'adjustment_product_validate_msg'   => esc_html__( 'Please select get product to save the cofiguration.', 'woo-conditional-discount-rules-for-checkout' ),
                            'adjustment_category_validate_msg'  => esc_html__( 'Please select get category to save the cofiguration.', 'woo-conditional-discount-rules-for-checkout' ),
                            'select_country'                    => esc_html__( 'Select a Country', 'woo-conditional-discount-rules-for-checkout' ),
                            'select_city'                       => esc_html__( "City 1\nCity 2", 'woo-conditional-discount-rules-for-checkout' ),
                            'select_state'                      => esc_html__( 'Select a State', 'woo-conditional-discount-rules-for-checkout' ),
                            'select_postcode'				    => esc_html__( "Postcode 1\nPostcode 2", 'woo-conditional-discount-rules-for-checkout' ),
                            'select_zone'					    => esc_html__( 'Select a Zone', 'woo-conditional-discount-rules-for-checkout' ),
							'select_product'                    => esc_html__( 'Select a Product', 'woo-conditional-discount-rules-for-checkout' ),
							'select_user_repeat_product'        => esc_html__( 'Select a Product', 'woo-conditional-discount-rules-for-checkout' ),
                            'select_variableproduct'            => esc_html__( 'Select a Variable Product', 'woo-conditional-discount-rules-for-checkout' ),
							'select_category'                   => esc_html__( 'Select a Category', 'woo-conditional-discount-rules-for-checkout' ),
							'select_tag'                        => esc_html__( 'Select a Tag', 'woo-conditional-discount-rules-for-checkout' ),
							'select_sku'                        => esc_html__( 'Select a SKU', 'woo-conditional-discount-rules-for-checkout' ),
							'select_product_attribute'          => esc_html__( 'Select Product Attribute', 'woo-conditional-discount-rules-for-checkout' ),
                            'select_user'					    => esc_html__( 'Select a User', 'woo-conditional-discount-rules-for-checkout' ),
                            'select_user_role'			        => esc_html__( 'Select a User Role', 'woo-conditional-discount-rules-for-checkout' ),
                            'select_user_mail'			        => esc_html__( 'Please enter mail', 'woo-conditional-discount-rules-for-checkout' ),
                            'select_coupon'					    => esc_html__( 'Select a Coupon', 'woo-conditional-discount-rules-for-checkout' ),
                            'select_float_number'			    => esc_html__( '0.00', 'woo-conditional-discount-rules-for-checkout' ),
                            'select_integer_number'			    => esc_html__( '10', 'woo-conditional-discount-rules-for-checkout' ),
							'select_shipping_class'             => esc_html__( 'Select a Shipping Class', 'woo-conditional-discount-rules-for-checkout' ),
                            'select_shipping_method'            => esc_html__( 'Select a Shipping Method', 'woo-conditional-discount-rules-for-checkout' ),
                            'select_payment'				    => esc_html__( 'Select a Payment Gateway', 'woo-conditional-discount-rules-for-checkout' ),
                            'wcdrfc_ajax_verification_nonce'    => wp_create_nonce( 'wcdrfc_ajax_verification' ),
                        ) 
                    );
                } else {
                    wp_localize_script( $this->plugin_name, 'coditional_vars', array( 
                            'dpb_api_url'                       => WDPAD_STORE_URL,
                            'setup_wizard_ajax_nonce'           => wp_create_nonce( 'wizard_ajax_nonce' ),
                            'ajaxurl'                          	=> admin_url( 'admin-ajax.php' ),
                            'plugin_url' 						=> plugin_dir_url( __FILE__ ),
                            'product_qty_msg' 					=> esc_html__( 'This rule will only work if you have selected any one Product Specific option.', 'woo-conditional-discount-rules-for-checkout' ),
                            'product_count_msg' 				=> esc_html__( 'This rule will work if you have selected any one Product Specific option or it will apply to all products.', 'woo-conditional-discount-rules-for-checkout' ),
                            'note'              				=> esc_html__( 'Note: ', 'woo-conditional-discount-rules-for-checkout' ),
                            'warning_msg6'      				=> esc_html__( 'You need to select product specific option in Discount Rules for product based option', 'woo-conditional-discount-rules-for-checkout' ),
                            'error_msg' 	    				=> esc_html__( 'Please add Discount Rules value', 'woo-conditional-discount-rules-for-checkout' ),
                            'warning_msg_per_qty'               => esc_html__( 'Please choose atleast one product or product variation or category or tag condition', 'woo-conditional-discount-rules-for-checkout' ),
                            'discount_cost_msg'                 => esc_html__( 'Please add discount value which will apply on cart/checkout.', 'woo-conditional-discount-rules-for-checkout' ),
                            'select_country'                    => esc_html__( 'Select a Country', 'woo-conditional-discount-rules-for-checkout' ),
                            'select_product'                    => esc_html__( 'Select a Product', 'woo-conditional-discount-rules-for-checkout' ),
                            'select_category'                   => esc_html__( 'Select a Category', 'woo-conditional-discount-rules-for-checkout' ),
                            'select_user'					    => esc_html__( 'Select a User', 'woo-conditional-discount-rules-for-checkout' ),
                            'select_float_number'			    => esc_html__( '0.00', 'woo-conditional-discount-rules-for-checkout' ),
                            'select_integer_number'			    => esc_html__( '10', 'woo-conditional-discount-rules-for-checkout' ),
                            'select2_per_product_ajax'          => 10,
                            'select2_product_placeholder'       => esc_html__( 'Select product', 'woo-conditional-discount-rules-for-checkout' ),
                            'wcdrfc_ajax_verification_nonce'    => wp_create_nonce( 'wcdrfc_ajax_verification' ),
                        ) 
                    );
                }
            } else {
                wp_localize_script( $this->plugin_name, 'coditional_vars', array( 
                        'dpb_api_url'                       => WDPAD_STORE_URL,
                        'setup_wizard_ajax_nonce'           => wp_create_nonce( 'wizard_ajax_nonce' ),
                        'ajaxurl'                          	=> admin_url( 'admin-ajax.php' ),
                        'plugin_url' 						=> plugin_dir_url( __FILE__ ),
                        'product_qty_msg' 					=> esc_html__( 'This rule will only work if you have selected any one Product Specific option.', 'woo-conditional-discount-rules-for-checkout' ),
                        'product_count_msg' 				=> esc_html__( 'This rule will work if you have selected any one Product Specific option or it will apply to all products.', 'woo-conditional-discount-rules-for-checkout' ),
                        'note'              				=> esc_html__( 'Note: ', 'woo-conditional-discount-rules-for-checkout' ),
                        'warning_msg6'      				=> esc_html__( 'You need to select product specific option in Discount Rules for product based option', 'woo-conditional-discount-rules-for-checkout' ),
                        'error_msg' 	    				=> esc_html__( 'Please add Discount Rules value', 'woo-conditional-discount-rules-for-checkout' ),
                        'warning_msg_per_qty'               => esc_html__( 'Please choose atleast one product or product variation or category or tag condition', 'woo-conditional-discount-rules-for-checkout' ),
                        'discount_cost_msg'                 => esc_html__( 'Please add discount value which will apply on cart/checkout.', 'woo-conditional-discount-rules-for-checkout' ),
                        'select_country'                    => esc_html__( 'Select a Country', 'woo-conditional-discount-rules-for-checkout' ),
                        'select_product'                    => esc_html__( 'Select a Product', 'woo-conditional-discount-rules-for-checkout' ),
                        'select_category'                   => esc_html__( 'Select a Category', 'woo-conditional-discount-rules-for-checkout' ),
                        'select_user'					    => esc_html__( 'Select a User', 'woo-conditional-discount-rules-for-checkout' ),
                        'select_float_number'			    => esc_html__( '0.00', 'woo-conditional-discount-rules-for-checkout' ),
                        'select_integer_number'			    => esc_html__( '10', 'woo-conditional-discount-rules-for-checkout' ),
                        'select2_per_product_ajax'          => 10,
                        'select2_product_placeholder'       => esc_html__( 'Select product', 'woo-conditional-discount-rules-for-checkout' ),
                        'wcdrfc_ajax_verification_nonce'    => wp_create_nonce( 'wcdrfc_ajax_verification' ),
                    ) 
                );
            }
		}
	}
	/**
	 * Set Active menu
	 */
	public function wdpad_active_menu() {
		$screen = get_current_screen();
		if ( ! empty( $screen ) && ( false !== strpos( $screen->id, '_page_wcdrfc' ) ) ) { ?>
			<script type="text/javascript">
              jQuery(document).ready(function ($) {
                $('a[href="admin.php?page=wcdrfc-rules-list"]').parent().addClass('current');
                $('a[href="admin.php?page=wcdrfc-rules-list"]').addClass('current');

                $('a[href="admin.php?page=wcdrfc-rules-list"]').parents().addClass( 'current wp-has-current-submenu' );
              })
			</script>
			<?php
		}
	}
	public function wdpad_dot_store_menu_conditional() {
		$plugin_name = WDPAD_PLUGIN_NAME;
		global $GLOBALS;
		if ( empty( $GLOBALS['admin_page_hooks']['dots_store'] ) ) {
			add_menu_page(
				'DotStore Plugins', __( 'DotStore Plugins', 'woo-conditional-discount-rules-for-checkout' ), 'null', 'dots_store', array(
				$this,
				'dot_store_menu_page',
			), 'dashicons-marker', 25 );
		}
		$get_hook = add_submenu_page( 'dots_store', WDPAD_PLUGIN_NAME, WDPAD_PLUGIN_NAME, 'manage_options', 'wcdrfc-rules-list', array(
			$this,
			'wdpad_list_page',
		) );
        
		add_action( "load-$get_hook", array( $this, "dpad_screen_options" ) );
        add_submenu_page( 'dots_store', 'Get Started', 'Get Started', 'manage_options', 'wcdrfc-page-get-started', array(
			$this,
			'wdpad_get_started_page',
		) );
		add_submenu_page( 'dots_store', 'Introduction', 'Introduction', 'manage_options', 'wcdrfc-page-information', array(
			$this,
			'wdpad_information_page',
		) );
        add_submenu_page( 'dots_store', 'General Settings', 'General Settings', 'manage_options', 'wcdrfc-page-general-settings', array(
			$this,
			'wdpad_general_settings_page',
		) );
        if ( wcdrfc_fs()->is__premium_only() ) {
            if ( wcdrfc_fs()->can_use_premium_code() ) {
                add_submenu_page( 'dots_store', 'Import / Export', 'Import / Export', 'manage_options', 'wcdrfc-page-import-export', array(
					$this,
					'wdpad_import_export_discount__premium_only',
				) );
            } else {
                add_submenu_page( 'dots_store', 'Dashboard', 'Dashboard', 'manage_options', 'wcdrfc-upgrade-dashboard', array(
					$this,
					'wcdrfc_free_user_upgrade_page',
				) );
            }
        } else {
            add_submenu_page( 'dots_store', 'Dashboard', 'Dashboard', 'manage_options', 'wcdrfc-upgrade-dashboard', array(
                $this,
                'wcdrfc_free_user_upgrade_page',
            ) );
        }
        $page_menu = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( ! empty( $page_menu ) && ( false !== strpos( $page_menu, 'wcdrfc' ) ) ) {
            remove_filter( 'update_footer', 'core_update_footer' ); 
        }
	}
	public function dot_store_menu_page() {
	}
	public function wdpad_information_page() {
		require_once( plugin_dir_path( __FILE__ ) . 'partials/wcdrfc-pro-information-page.php' );
	}
	public function wdpad_list_page() {
		require_once( plugin_dir_path( __FILE__ ) . 'partials/wcdrfc-pro-list-page.php' );
		$dpad_rule_lising_obj = new DPAD_Rule_Listing_Page();
		$dpad_rule_lising_obj->dpad_sj_output();
	}
	public function wdpad_get_started_page() {
		require_once( plugin_dir_path( __FILE__ ) . 'partials/wcdrfc-pro-get-started-page.php' );
	}
	/**
	 * Screen option for discount rule list
	 *
	 * @since    1.0.0
	 */
	public function dpad_screen_options() {
		$args = array(
			'label'   => esc_html__( 'List Per Page', 'woo-conditional-discount-rules-for-checkout' ),
			'default' => 1,
			'option'  => 'dpad_per_page',
		);
		add_screen_option( 'per_page', $args );

        if ( ! class_exists( 'WC_Discount_Rules_Table' ) ) {
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/class-wc-discount-rules-table.php';
        }
        $sagar = new WC_Discount_Rules_Table();
        $sagar->_column_headers = $sagar->get_column_info();    
	}
    /**
	 * Import Export Setting page
	 *
     * @since    2.3.3
	 */
	public function wdpad_import_export_discount__premium_only() {
		require_once( plugin_dir_path( __FILE__ ) . '/partials/wcdrfc-import-export-setting__premium_only.php' );
	}
    /**
     * General Settings page
     * 
     * @since   2.4.0
     */
    public function wdpad_general_settings_page(){
        require_once( plugin_dir_path( __FILE__ ) . '/partials/wcdrfc-general-settings.php' );
    }
    /**
	 * Premium version info page
	 *
     * @since   2.4.0
	 */
	public function wcdrfc_free_user_upgrade_page() {
		require_once( plugin_dir_path( __FILE__ ) . '/partials/wcdrfc-upgrade-dashboard.php' );
	}
	/**
	 * Add screen option for per page
	 *
	 * @param bool   $status
	 * @param string $option
	 * @param int    $value
	 *
	 * @return int $value
	 * @since 1.0.0
	 *
	 */
	public function wdpad_set_screen_options( $status, $option, $value ) {
        
		$dpad_screens = array(
			'dpad_per_page',
		);
		if( 'dpad_per_page' === $option ){
			$value = !empty($value) && $value > 0 ? $value : 1;
		}
        
		if ( in_array( $option, $dpad_screens, true ) ) {
			return $value;
		}
		return $status;
	}

	/**
	 * Product specific starts
	 */
	public function wdpad_product_dpad_conditions_values_ajax() {
		// Security check
		check_ajax_referer( 'wcdrfc_ajax_verification', 'security' );

		// Add new conditions
		$condition = filter_input( INPUT_POST, 'condition', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$count     = filter_input( INPUT_POST, 'count', FILTER_SANITIZE_NUMBER_INT );
		$condition = isset( $condition ) ? $condition : '';
		$count     = isset( $count ) ? $count : '';
		$html      = '';
		if ( wcdrfc_fs()->is__premium_only() ) {
			if ( wcdrfc_fs()->can_use_premium_code() ) {
				if ( $condition === 'country' ) {
					$html .= wp_json_encode( $this->wdpad_get_country_list( $count, [], true ) );
				} elseif ( $condition === 'city' ) {
					$html .= 'textarea';
				} elseif ( $condition === 'state' ) {
					$html .= wp_json_encode( $this->wdpad_get_states_list__premium_only( $count, [], true ) );
				} elseif ( $condition === 'postcode' ) {
					$html .= 'textarea';
				} elseif ( $condition === 'zone' ) {
					$html .= wp_json_encode( $this->wdpad_get_zones_list__premium_only( $count, [], true ) );
				} elseif ( $condition === 'product' ) {
					$html .= wp_json_encode( $this->wdpad_get_product_list( $count, [], '', true ) );
				} elseif ( $condition === 'variableproduct' ) {
					$html .= wp_json_encode( $this->wdpad_get_varible_product_list__premium_only( $count, [], '', true ) );
				} elseif ( $condition === 'category' ) {
					$html .= wp_json_encode( $this->wdpad_get_category_list( $count, [], true ) );
				} elseif ( $condition === 'tag' ) {
					$html .= wp_json_encode( $this->wdpad_get_tag_list__premium_only( $count, [], true ) );
				} elseif ( $condition === 'product_qty' ) {
					$html .= 'input';
				} elseif ( $condition === 'product_count' ) {
					$html .= 'input';
				} elseif ( $condition === 'user' ) {
					$html .= wp_json_encode( $this->wdpad_get_user_list( $count, [], true ) );
				} elseif ( $condition === 'user_role' ) {
					$html .= wp_json_encode( $this->wdpad_get_user_role_list__premium_only( $count, [], true ) );
				} elseif ( $condition === 'user_mail' ) {
					$html .= 'textarea';
				} elseif ( $condition === 'cart_total' ) {
					$html .= 'input';
				} elseif ( $condition === 'cart_totalafter' ) {
					$html .= 'input';
				} elseif ( $condition === 'quantity' ) {
					$html .= 'input';
				} elseif ( $condition === 'total_spent_order' ) {
					$html .= 'input';
				} elseif ( $condition === 'spent_order_count' ) {
					$html .= 'input';
				} elseif ( $condition === 'last_spent_order' ) {
					$html .= 'input';
                } else if( $condition === 'user_repeat_product' ){
                    $html .= wp_json_encode( $this->wdpad_get_product_and_variation_list__premium_only( $count, [], true ) );
				} elseif ( $condition === 'weight' ) {
					$html .= 'input';
				} elseif ( $condition === 'coupon' ) {
					$html .= wp_json_encode( $this->wdpad_get_coupon_list__premium_only( $count, [], true ) );
				} elseif ( $condition === 'shipping_class' ) {
					$html .= wp_json_encode( $this->wdpad_get_advance_flat_rate_class__premium_only( $count, [], true ) );
				} elseif ( $condition === 'payment' ) {
					$html .= wp_json_encode( $this->wdpad_get_payment_methods__premium_only( $count, [], true ) );
				} elseif ( $condition === 'shipping_method' ) {
					$html .= wp_json_encode( $this->wdpad_get_active_shipping_methods__premium_only( $count, [], true ) );
				} elseif ( $condition === 'shipping_total' ) {
					$html .= 'input';
				}
			} else {
				if ( $condition === 'country' ) {
					$html .= wp_json_encode( $this->wdpad_get_country_list( $count, [], true ) );
				} elseif ( $condition === 'city' ) {
					$html .= 'textarea';
				} elseif ( $condition === 'product' ) {
					$html .= wp_json_encode( $this->wdpad_get_product_list( $count, [], '', true ) );
				} elseif ( $condition === 'category' ) {
					$html .= wp_json_encode( $this->wdpad_get_category_list( $count, [], true ) );
				} elseif ( $condition === 'user' ) {
					$html .= wp_json_encode( $this->wdpad_get_user_list( $count, [], true ) );
				} elseif ( $condition === 'cart_total' ) {
					$html .= 'input';
				} elseif ( $condition === 'quantity' ) {
					$html .= 'input';
				} elseif ( $condition === 'product_count' ) {
					$html .= 'input';
				}
			}
		} else {
			if ( $condition === 'country' ) {
				$html .= wp_json_encode( $this->wdpad_get_country_list( $count, [], true ) );
			} elseif ( $condition === 'city' ) {
				$html .= 'textarea';
			} elseif ( $condition === 'product' ) {
				$html .= wp_json_encode( $this->wdpad_get_product_list( $count, [], '', true ) );
			} elseif ( $condition === 'category' ) {
				$html .= wp_json_encode( $this->wdpad_get_category_list( $count, [], true ) );
			} elseif ( $condition === 'user' ) {
				$html .= wp_json_encode( $this->wdpad_get_user_list( $count, [], true ) );
			} elseif ( $condition === 'cart_total' ) {
				$html .= 'input';
			} elseif ( $condition === 'quantity' ) {
				$html .= 'input';
			} elseif ( $condition === 'product_count' ) {
				$html .= 'input';
			}
		}
		echo wp_kses( $html, allowed_html_tags() );
		wp_die(); // this is required to terminate immediately and return a proper response
	}
	/**
	 * Function for select country list
	 *
	 * @param string $count
	 * @param array  $selected
	 *
	 * @return string
	 */
	public function wdpad_get_country_list( $count = '', $selected = array(), $json = false ) {
		$countries_obj = new WC_Countries();
		$getCountries  = $countries_obj->__get( 'countries' );
		if ( $json ) {
			return $this->convert_array_to_json( $getCountries );
		}
		$html = '<select name="dpad[product_dpad_conditions_values][value_' . $count . '][]" class="product_dpad_conditions_values product_discount_select product_discount_select multiselect2 product_dpad_conditions_values_country" multiple="multiple">';
		if ( ! empty( $getCountries ) ) {
			foreach ( $getCountries as $code => $country ) {
				$selectedVal = is_array( $selected ) && ! empty( $selected ) && in_array( $code, $selected, true ) ? 'selected=selected' : '';
				$html        .= '<option value="' . $code . '" ' . $selectedVal . '>' . $country . '</option>';
			}
		}
		$html .= '</select>';
		return $html;
	}
	public function convert_array_to_json( $arr ) {
		$filter_data = [];
		foreach ( $arr as $key => $value ) {
			$option                        = [];
			$option['name']                = $value;
			$option['attributes']['value'] = $key;
			$filter_data[]                 = $option;
		}
		return $filter_data;
	}
	/**
	 * Get product list in advance pricing rules section
	 *
	 * @param string $count
	 * @param array  $selected
	 *
	 * @return mixed $html
	 * @since 1.0.0
	 *
	 */
	public function wdpad_get_product_options( $count = '', $selected = array() ) {
		global $sitepress;
		if ( ! empty( $sitepress ) ) {
			$default_lang = $sitepress->get_default_language();
		}
		
		$all_selected_product_ids = array();
		if ( ! empty( $selected ) && is_array( $selected ) ) {
			foreach ( $selected as $product_id ) {
				$_product = wc_get_product( $product_id );

				if ( 'product_variation' === $_product->post_type ) {
					$all_selected_product_ids[] = $_product->get_parent_id(); //parent_id;
				} else {
					$all_selected_product_ids[] = $product_id;
				}
			}
		}
		$all_selected_product_count = 900;
		$get_all_products = new WP_Query( array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => $all_selected_product_count,
			'post__in'       => $all_selected_product_ids,
		) );
		
		$baselang_variation_product_ids = array();
		$defaultlang_simple_product_ids = array();
		$html                           = '';
		
		
		if ( isset( $get_all_products->posts ) && ! empty( $get_all_products->posts ) ) {
			foreach ( $get_all_products->posts as $get_all_product ) {
				$_product = wc_get_product( $get_all_product->ID );
				
				if ( $_product->is_type( 'variable' ) ) {
					$variations = $_product->get_available_variations();
					foreach ( $variations as $value ) {
						if ( ! empty( $sitepress ) ) {
							$defaultlang_variation_product_id = apply_filters( 'wpml_object_id', $value['variation_id'], 'product', true, $default_lang );
						} else {
							$defaultlang_variation_product_id = $value['variation_id'];
						}
						$baselang_variation_product_ids[] = $defaultlang_variation_product_id;
					}
				}
				if ( $_product->is_type( 'simple' ) ) {
					if ( ! empty( $sitepress ) ) {
						$defaultlang_simple_product_id = apply_filters( 'wpml_object_id', $get_all_product->ID, 'product', true, $default_lang );
					} else {
						$defaultlang_simple_product_id = $get_all_product->ID;
					}
					$defaultlang_simple_product_ids[] = $defaultlang_simple_product_id;
				}
			}
		}
		$baselang_product_ids = array_merge( $baselang_variation_product_ids, $defaultlang_simple_product_ids );
		if ( isset( $baselang_product_ids ) && ! empty( $baselang_product_ids ) ) {
			foreach ( $baselang_product_ids as $baselang_product_id ) {
				$selected    = array_map( 'intval', $selected );
				$selectedVal = is_array( $selected ) && ! empty( $selected ) && in_array( $baselang_product_id, $selected, true ) ? 'selected=selected' : '';
				if ( '' !== $selectedVal ) {
					$html .= '<option value="' . $baselang_product_id . '" ' . $selectedVal . '>' . '#' . $baselang_product_id . ' - ' . get_the_title( $baselang_product_id ) . '</option>';
				}
			}
		}
		return $html;
	}
	/**
	 * Get category list in advance pricing rules section
	 *
	 * @param array $selected
	 *
	 * @return mixed $html
	 * @since 1.0.0
	 *
	 */
	public function wdpad_get_category_options__premium_only( $selected = array(), $json = false ) {
		global $sitepress;
		if ( ! empty( $sitepress ) ) {
			$default_lang = $sitepress->get_default_language();
		}
		$filter_category_list = [];
		$args                 = array(
			'taxonomy'     => 'product_cat',
			'orderby'      => 'name',
			'hierarchical' => 1,
			'hide_empty'   => 1,
		);
		$get_all_categories   = get_terms( 'product_cat', $args );
		$html                 = '';
		if ( isset( $get_all_categories ) && ! empty( $get_all_categories ) ) {
			foreach ( $get_all_categories as $get_all_category ) {
				if ( $get_all_category ) {
					if ( ! empty( $sitepress ) ) {
						$new_cat_id = apply_filters( 'wpml_object_id', $get_all_category->term_id, 'product_cat', true, $default_lang );
					} else {
						$new_cat_id = $get_all_category->term_id;
					}
					$category        = get_term_by( 'id', $new_cat_id, 'product_cat' );
					$parent_category = get_term_by( 'id', $category->parent, 'product_cat' );
					if ( ! empty( $selected ) ) {
						$selected    = array_map( 'intval', $selected );
						$selectedVal = is_array( $selected ) && ! empty( $selected ) && in_array( $new_cat_id, $selected, true ) ? 'selected=selected' : '';
						if ( $category->parent > 0 ) {
							$html .= '<option value=' . $category->term_id . ' ' . $selectedVal . '>' . '' . $parent_category->name . '->' . $category->name . '</option>';
						} else {
							$html .= '<option value=' . $category->term_id . ' ' . $selectedVal . '>' . $category->name . '</option>';
						}
					} else {
						if ( $category->parent > 0 ) {
							$filter_category_list[ $category->term_id ] = $parent_category->name . '->' . $category->name;
						} else {
							$filter_category_list[ $category->term_id ] = $category->name;
						}
					}
				}
			}
		}
		if ( true === $json ) {
			return wp_json_encode( $this->convert_array_to_json( $filter_category_list ) );
		} else {
			return $html;
		}
	}
    /**
	 * Get shipping class list in advance pricing rules section
	 *
	 * @param array $selected
	 *
	 * @return string $html
	 * @since  2.3.3
	 *
	 * @uses   WC_Shipping::get_shipping_classes()
	 *
	 */
	public function wdpad_get_class_options__premium_only( $selected = array(), $json = false ) {
		$shipping_classes           = WC()->shipping->get_shipping_classes();
		$filter_shipping_class_list = [];
		$html                       = '';
		if ( isset( $shipping_classes ) && ! empty( $shipping_classes ) ) {
			foreach ( $shipping_classes as $shipping_classes_key ) {
				$selectedVal                                               = ! empty( $selected ) && in_array( $shipping_classes_key->slug, $selected, true ) ? 'selected=selected' : '';
				$html                                                      .= '<option value="' . esc_attr( $shipping_classes_key->slug ) . '" ' . esc_attr( $selectedVal ) . '>' . esc_html( $shipping_classes_key->name ) . '</option>';
				$filter_shipping_class_list[ $shipping_classes_key->slug ] = $shipping_classes_key->name;
			}
		}
		if ( true === $json ) {
			return wp_json_encode( $this->convert_array_to_json( $filter_shipping_class_list ) );
		} else {
			return $html;
		}
	}
	/**
	 * Get the states for a country.
	 *
	 * @param string $count
	 * @param array  $selected
	 *
	 * @return string of states
	 */
	public function wdpad_get_states_list__premium_only( $count = '', $selected = array(), $json = false ) {
		$countries     = WC()->countries->get_allowed_countries();
		$filter_states = [];
		$html          = '<select name="dpad[product_dpad_conditions_values][value_' . $count . '][]" class="product_dpad_conditions_values product_discount_select product_discount_select multiselect2 product_dpad_conditions_values_state" multiple="multiple">';
		if ( isset( $countries ) && ! empty( $countries ) ) {
			foreach ( $countries as $key => $val ) {
				$states = WC()->countries->get_states( $key );
				if ( ! empty( $states ) ) {
					foreach ( $states as $state_key => $state_value ) {
						$selectedVal                              = is_array( $selected ) && ! empty( $selected ) && in_array( esc_attr( $key . ':' . $state_key ), $selected, true ) ? 'selected=selected' : '';
						$html                                     .= '<option value="' . esc_attr( $key . ':' . $state_key ) . '" ' . $selectedVal . '>' . esc_html( $val . ' -> ' . $state_value ) . '</option>';
						$filter_states[ $key . ':' . $state_key ] = $val . ' -> ' . $state_value;
					}
				}
			}	
		}
		$html .= '</select>';
		if ( $json ) {
			return $this->convert_array_to_json( $filter_states );
		}
		return $html;
	}
	public function wdpad_get_zones_list__premium_only( $count = '', $selected = array(), $json = false ) {
		$filter_zone = [];
		$raw_zones   = WC_Shipping_Zones::get_zones();
		$html        = '<select rel-id="' . $count . '" name="dpad[product_dpad_conditions_values][value_' . $count . '][]" class="product_dpad_conditions_values product_discount_select product_discount_select multiselect2" multiple="multiple">';
		if ( isset( $raw_zones ) && ! empty( $raw_zones ) ) {
			foreach ( $raw_zones as $zone ) {
				$selected                        = array_map( 'intval', $selected );
				$zone['zone_id']                 = (int) $zone['zone_id'];
				$selectedVal                     = is_array( $selected ) && ! empty( $selected ) && in_array( $zone['zone_id'], $selected, true ) ? 'selected=selected' : '';
				$html                            .= '<option value="' . $zone['zone_id'] . '" ' . $selectedVal . '>' . $zone['zone_name'] . '</option>';
				$filter_zone[ $zone['zone_id'] ] = $zone['zone_name'];
			}
		}
		if ( $json ) {
			return $this->convert_array_to_json( $filter_zone );
		}
		$html .= '</select>';
		return $html;
	}
	/**
	 * Function for select product list for selected product
	 *
	 * @param string $count
	 * @param array  $selected
	 *
	 * @return string
	 */
	public function wdpad_get_selected_product_list( $count = '', $selected = array(), $action = '', $json = false ) {
		
		if( empty($selected) ){ $selected = array(); }
		global $sitepress;
		if ( ! empty( $sitepress ) ) {
			$default_lang = $sitepress->get_default_language();
		}
		$post_in = '';
		if ( 'edit' === $action ) {
			$post_in        = $selected;
			$posts_per_page = - 1;
		} else {
			$post_in        = '';
			$posts_per_page = 10;
		}
		$product_args = array(
			'post_type'      => array( 'product', 'product_variation' ),
			'post_status'    => 'publish',
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'post__in'       => $post_in,
			'posts_per_page' => $posts_per_page,
		);
		$get_all_products = new WP_Query( $product_args );
		$html             = '<select id="product-filter-' . $count . '" rel-id="' . $count . '" name="dpad_selected_product_list[]" class="all-products-variations" multiple="multiple">';
		if ( isset( $get_all_products->posts ) && ! empty( $get_all_products->posts ) ) {
			foreach ( $get_all_products->posts as $get_all_product ) {
				if ( ! empty( $sitepress ) ) {
					$new_product_id = apply_filters( 'wpml_object_id', $get_all_product->ID, 'product', true, $default_lang );
				} else {
					$new_product_id = $get_all_product->ID;
				}
				$selected    = array_map( 'intval', $selected );
				$selectedVal = is_array( $selected ) && ! empty( $selected ) && in_array( $new_product_id, $selected, true ) ? 'selected=selected' : '';
                $html .= '<option value="' . $new_product_id . '" ' . $selectedVal . '>' . '#' . $new_product_id . ' - ' . get_the_title( $new_product_id ) . '</option>';
			}
		}
		$html .= '</select>';
		if ( $json ) {
			return [];
		}
		return $html;
	}
	/**
	 * Function for select product list
	 *
	 * @param string $count
	 * @param array  $selected
	 *
	 * @return string
	 */
	public function wdpad_get_product_list( $count = '', $selected = array(), $action = '', $json = false ) {
        $selected = !empty($selected) ? $selected : array(); //this need to extra check as some time we got blank STRING.
		global $sitepress;
		if ( ! empty( $sitepress ) ) {
			$default_lang = $sitepress->get_default_language();
		}
		$post_in = '';
		if ( 'edit' === $action ) {
			$post_in        = $selected;
			$posts_per_page = - 1;
		} else {
			$post_in        = '';
			$posts_per_page = 10;
		}
		$product_args = array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'post__in'       => $post_in,
			'posts_per_page' => $posts_per_page,
		);
		$get_all_products = new WP_Query( $product_args );
		$html             = '<select id="product-filter-' . $count . '" rel-id="' . $count . '" name="dpad[product_dpad_conditions_values][value_' . $count . '][]" class="product_filter_select2 product_discount_select product_dpad_conditions_values" multiple="multiple">';
		if ( isset( $get_all_products->posts ) && ! empty( $get_all_products->posts ) ) {
			foreach ( $get_all_products->posts as $get_all_product ) {
				if ( ! empty( $sitepress ) ) {
					$new_product_id = apply_filters( 'wpml_object_id', $get_all_product->ID, 'product', true, $default_lang );
				} else {
					$new_product_id = $get_all_product->ID;
				}
				$selected    = array_map( 'intval', $selected );
				$selectedVal = is_array( $selected ) && ! empty( $selected ) && in_array( $new_product_id, $selected, true ) ? 'selected=selected' : '';
				if ( $selectedVal !== '' ) {
					$html .= '<option value="' . $new_product_id . '" ' . $selectedVal . '>' . '#' . $new_product_id . ' - ' . get_the_title( $new_product_id ) . '</option>';
				}
			}
		}
		$html .= '</select>';
		if ( $json ) {
			return [];
		}
		return $html;
	}
	/**
	 * Function for select product list
	 *
	 */
	public function wdpad_get_varible_product_list__premium_only( $count = '', $selected = array(), $action = '', $json = false ) {
		global $sitepress;
		if ( ! empty( $sitepress ) ) {
			$default_lang = $sitepress->get_default_language();
		}
		$post_in = '';
		if ( 'edit' === $action ) {
			$post_in        = $selected;
			$posts_per_page = - 1;
		} else {
			$post_in        = '';
			$posts_per_page = 10;
		}
		$product_args = array(
			'post_type'      => 'product_variation',
			'post_status'    => 'publish',
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'post__in'       => $post_in,
			'posts_per_page' => $posts_per_page,
		);
		$get_all_products = new WP_Query( $product_args );
		$html = '<select id="var-product-filter-' . $count . '" rel-id="' . $count . '" name="dpad[product_dpad_conditions_values][value_' . $count . '][]" class="product_var_filter_select2 product_discount_select product_dpad_conditions_values multiselect2" multiple="multiple">';
		if ( isset( $get_all_products->posts ) && ! empty( $get_all_products->posts ) ) {
			foreach ( $get_all_products->posts as $get_all_product ) {
				if ( ! empty( $sitepress ) ) {
					$new_product_id = apply_filters( 'wpml_object_id', $get_all_product->ID, 'product', true, $default_lang );
				} else {
					$new_product_id = $get_all_product->ID;
				}
				$selected    = array_map( 'intval', $selected );
				$selectedVal = is_array( $selected ) && ! empty( $selected ) && in_array( $new_product_id, $selected, true ) ? 'selected=selected' : '';
				if ( $selectedVal !== '' ) {
					$html .= '<option value="' . $new_product_id . '" ' . $selectedVal . '>' . '#' . $new_product_id . ' - ' . get_the_title( $new_product_id ) . '</option>';
				}
			}
		}
		$html .= '</select>';
		if ( $json ) {
			return [];
		}
		return $html;
	}

    /**
	 * Function for select all products and variations
	 *
	 */
    public function wdpad_get_product_and_variation_list__premium_only( $count = '', $selected = array(), $json = false ){
        global $sitepress;
		if ( ! empty( $sitepress ) ) {
			$default_lang = $sitepress->get_default_language();
		}
        $product_args = array(
			'post_type'        => array( 'product', 'product_variation' ),
			'posts_per_page'   => -1,
			'post_status'      => 'publish',
			'orderby'          => 'title',
			'order'            => 'ASC',
            'post__in'         => $selected,
		);
        
		$get_wp_query = new WP_Query( $product_args );

		$get_all_products = $get_wp_query->posts;
        $baselang_product_ids = array();
		if ( isset( $get_all_products ) && ! empty( $get_all_products ) ) {
			foreach ( $get_all_products as $get_all_product ) {
				$_product = wc_get_product( $get_all_product->ID );
				if ( ! $_product->is_type( 'variable' ) ) {
                    if ( ! empty( $sitepress ) ) {
                        $defaultlang_simple_product_id = apply_filters( 'wpml_object_id', $get_all_product->ID, 'product', true, $default_lang );
                    } else {
                        $defaultlang_simple_product_id = $get_all_product->ID;
                    }
                    $baselang_product_ids[] = $defaultlang_simple_product_id;
				}
			}
		}
        $filter_product_list = array();
		$html = '<select name="dpad[product_dpad_conditions_values][value_' . $count . '][]" class="all-products-variations product_discount_select multiselect2" multiple>';
		if ( isset( $baselang_product_ids ) && ! empty( $baselang_product_ids ) ) {
			foreach ( $baselang_product_ids as $baselang_product_id ) {
                $selected    = array_map( 'intval', $selected );
				$selectedVal = is_array( $selected ) && ! empty( $selected ) && in_array( $baselang_product_id, $selected, true ) ? 'selected=selected' : '';
				if ( $selectedVal !== '' ) {
                    $html                  .= '<option value="' . $baselang_product_id . '" ' . $selectedVal . '>' . '#' . $baselang_product_id . ' - ' . get_the_title( $baselang_product_id ) . '</option>';
                    $filter_product_list[] = array( $baselang_product_id, '#' . $baselang_product_id . ' - ' . get_the_title( $baselang_product_id ) );
                }
			}
		}
        $html .= '</select>';
		if ( $json ) {
			return $filter_product_list;
		}
		return wp_kses( $html, allowed_html_tags() );
    }
	/**
	 * Function for select cat list
	 *
	 * @param string $count
	 * @param array  $selected
	 *
	 * @return string
	 */
	public function wdpad_get_category_list( $count = '', $selected = array(), $json = false ) {
		$filter_categories = [];
		global $sitepress;
		$taxonomy     = 'product_cat';
		$post_status  = 'publish';
		$orderby      = 'name';
		$hierarchical = 1;
		$empty        = 0;
		if ( ! empty( $sitepress ) ) {
			$default_lang = $sitepress->get_default_language();
		}
		$args               = array(
			'post_type'      => 'product',
			'post_status'    => $post_status,
			'taxonomy'       => $taxonomy,
			'orderby'        => $orderby,
			'hierarchical'   => $hierarchical,
			'hide_empty'     => $empty,
			'posts_per_page' => - 1,
		);
		$get_all_categories = get_categories( $args );
		$html               = '<select rel-id="' . $count . '" name="dpad[product_dpad_conditions_values][value_' . $count . '][]" class="product_dpad_conditions_values product_discount_select product_discount_select multiselect2" multiple="multiple">';
		if ( isset( $get_all_categories ) && ! empty( $get_all_categories ) ) {
			foreach ( $get_all_categories as $get_all_category ) {
				if ( ! empty( $sitepress ) ) {
					$new_cat_id = apply_filters( 'wpml_object_id', $get_all_category->term_id, 'product_cat', true, $default_lang );
				} else {
					$new_cat_id = $get_all_category->term_id;
				}
				$selected        = array_map( 'intval', $selected );
				$selectedVal     = is_array( $selected ) && ! empty( $selected ) && in_array( $new_cat_id, $selected, true ) ? 'selected=selected' : '';
				$category        = get_term_by( 'id', $new_cat_id, 'product_cat' );
				$parent_category = get_term_by( 'id', $category->parent, 'product_cat' );
				if ( $category->parent > 0 ) {
					$html                                    .= '<option value=' . $category->term_id . ' ' . $selectedVal . '>' . '#' . $parent_category->name . '->' . $category->name . '</option>';
					$filter_categories[ $category->term_id ] = '#' . $parent_category->name . '->' . $category->name;
				} else {
					$html                                    .= '<option value=' . $category->term_id . ' ' . $selectedVal . '>' . $category->name . '</option>';
					$filter_categories[ $category->term_id ] = $category->name;
				}
			}
		}
		$html .= '</select>';
		if ( $json ) {
			return $this->convert_array_to_json( $filter_categories );
		}
		return $html;
	}
	/**
	 * Function for select tag list
	 *
	 */
	public function wdpad_get_tag_list__premium_only( $count = '', $selected = array(), $json = false ) {
		global $sitepress;
		$filter_tags  = [];
		$taxonomy     = 'product_tag';
		$orderby      = 'name';
		$hierarchical = 1;
		$empty        = 0;
		if ( ! empty( $sitepress ) ) {
			$default_lang = $sitepress->get_default_language();
		}
		$args = array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'taxonomy'       => $taxonomy,
			'orderby'        => $orderby,
			'hierarchical'   => $hierarchical,
			'hide_empty'     => $empty,
			'posts_per_page' => - 1,
		);
		$get_all_tags = get_categories( $args );
		$html = '<select rel-id="' . $count . '" name="dpad[product_dpad_conditions_values][value_' . $count . '][]" class="product_dpad_conditions_values product_discount_select product_discount_select multiselect2" multiple="multiple">';
		if ( isset( $get_all_tags ) && ! empty( $get_all_tags ) ) {
			foreach ( $get_all_tags as $get_all_tag ) {
				if ( ! empty( $sitepress ) ) {
					$new_tag_id = apply_filters( 'wpml_object_id', $get_all_tag->term_id, 'product_tag', true, $default_lang );
				} else {
					$new_tag_id = $get_all_tag->term_id;
				}
				$selected    = array_map( 'intval', $selected );
				$selectedVal = is_array( $selected ) && ! empty( $selected ) && in_array( $new_tag_id, $selected, true ) ? 'selected=selected' : '';
				$tag         = get_term_by( 'id', $new_tag_id, 'product_tag' );
				$html                         .= '<option value="' . $tag->term_id . '" ' . $selectedVal . '>' . $tag->name . '</option>';
				$filter_tags[ $tag->term_id ] = $tag->name;
			}
		}
		$html .= '</select>';
		if ( $json ) {
			return $this->convert_array_to_json( $filter_tags );
		}
		return $html;
	}
	/**
	 * Function for select user list
	 *
	 */
	public function wdpad_get_user_list( $count = '', $selected = array(), $json = false ) {
		$filter_users  = [];
		$get_all_users = get_users();
		$html          = '<select rel-id="' . $count . '" name="dpad[product_dpad_conditions_values][value_' . $count . '][]" class="product_dpad_conditions_values product_discount_select product_discount_select multiselect2" multiple="multiple">';
		if ( isset( $get_all_users ) && ! empty( $get_all_users ) ) {
			foreach ( $get_all_users as $get_all_user ) {
				$selected                                = array_map( 'intval', $selected );
				$selectedVal                             = is_array( $selected ) && ! empty( $selected ) && in_array( (int) $get_all_user->data->ID, $selected, true ) ? 'selected=selected' : '';
				$html                                    .= '<option value="' . $get_all_user->data->ID . '" ' . $selectedVal . '>' . $get_all_user->data->user_login . '</option>';
				$filter_users[ $get_all_user->data->ID ] = $get_all_user->data->user_login;
			}
		}
		$html .= '</select>';
		if ( $json ) {
			return $this->convert_array_to_json( $filter_users );
		}
		return $html;
	}
	/**
	 * Get User role list
	 *
	 * @return unknown
	 */
	public function wdpad_get_user_role_list__premium_only( $count = '', $selected = array(), $json = false ) {
		$filter_user_roles = [];
		global $wp_roles;
		$html = '<select rel-id="' . $count . '" name="dpad[product_dpad_conditions_values][value_' . $count . '][]" class="product_dpad_conditions_values product_discount_select product_discount_select multiselect2" multiple="multiple">';
		if ( isset( $wp_roles->roles ) && ! empty( $wp_roles->roles ) ) {
			$defaultSel                 = ! empty( $selected ) && in_array( 'guest', $selected, true ) ? 'selected=selected' : '';
			$html                       .= '<option value="guest" ' . $defaultSel . '>Guest</option>';
			$filter_user_roles["guest"] = 'Guest';
			foreach ( $wp_roles->roles as $user_role_key => $get_all_role ) {
				$selectedVal                         = is_array( $selected ) && ! empty( $selected ) && in_array( $user_role_key, $selected, true ) ? 'selected=selected' : '';
				$html                                .= '<option value="' . $user_role_key . '" ' . $selectedVal . '>' . $get_all_role['name'] . '</option>';
				$filter_user_roles[ $user_role_key ] = $get_all_role['name'];
			}
		}
		$html .= '</select>';
		if ( $json ) {
			return $this->convert_array_to_json( $filter_user_roles );
		}
		return $html;
	}
	/**
	 * Function for get Coupon list
	 *
	 */
	public function wdpad_get_coupon_list__premium_only( $count = '', $selected = array(), $json = false ) {
		$filter_coupon_list = [];
		$get_all_coupon     = new WP_Query( array(
			'post_type'      => 'shop_coupon',
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
		) );
		$html               = '<select rel-id="' . $count . '" name="dpad[product_dpad_conditions_values][value_' . $count . '][]" class="product_dpad_conditions_values product_discount_select product_discount_select multiselect2" multiple="multiple">';
		if ( isset( $get_all_coupon->posts ) && ! empty( $get_all_coupon->posts ) ) {
			foreach ( $get_all_coupon->posts as $get_all_coupon ) {
				$selected                                  = array_map( 'intval', $selected );
				$selectedVal                               = is_array( $selected ) && ! empty( $selected ) && in_array( $get_all_coupon->ID, $selected, true ) ? 'selected=selected' : '';
				$html                                      .= '<option value="' . $get_all_coupon->ID . '" ' . $selectedVal . '>' . $get_all_coupon->post_title . '</option>';
				$filter_coupon_list[ $get_all_coupon->ID ] = $get_all_coupon->post_title;
			}
		}
		$html .= '</select>';
		if ( $json ) {
			return $this->convert_array_to_json( $filter_coupon_list );
		}
		return $html;
	}
	/**
	 * get all shipping class name
	 *
	 * @param string $count
	 * @param array  $selected
	 *
	 * @return string
	 */
	public function wdpad_get_advance_flat_rate_class__premium_only( $count = '', $selected = array(), $json = false ) {
		$filter_rate_class = [];
		$shipping_classes  = WC()->shipping->get_shipping_classes();
		$html              = '<select rel-id="' . $count . '" name="dpad[product_dpad_conditions_values][value_' . $count . '][]" class="product_dpad_conditions_values product_discount_select product_discount_select multiselect2" multiple="multiple">';
		$html              .= '<option value="">Select Class</option>';
		if ( isset( $shipping_classes ) && ! empty( $shipping_classes ) ) {
			foreach ( $shipping_classes as $shipping_classes_key ) {
				$shipping_classes_old                                = get_term_by( 'slug', $shipping_classes_key->slug, 'product_shipping_class' );
				$selected                                            = array_map( 'intval', $selected );
				$selectedVal                                         = ! empty( $selected ) && in_array( $shipping_classes_old->term_id, $selected, true ) ? 'selected=selected' : '';
				$html                                                .= '<option value="' . $shipping_classes_old->term_id . '" ' . $selectedVal . '>' . $shipping_classes_key->name . '</option>';
				$filter_rate_class[ $shipping_classes_old->term_id ] = $shipping_classes_key->name;
			}
		}
		$html .= '</select>';
		if ( $json ) {
			return $this->convert_array_to_json( $filter_rate_class );
		}
		return $html;
	}
	/**
	 * Function for select payment gateways
	 *
	 * @param string $count
	 * @param array  $selected
	 *
	 * @return string
	 */
	public function wdpad_get_payment_methods__premium_only( $count = '', $selected = array(), $json = false ) {
		$filter_payment_methods     = [];
		$available_payment_gateways = WC()->payment_gateways->get_available_payment_gateways();
		$html                       = '<select name="dpad[product_dpad_conditions_values][value_' . $count . '][]" class="product_dpad_conditions_values product_discount_select product_discount_select multiselect2" multiple="multiple">';
		if ( ! empty( $available_payment_gateways ) ) {
			foreach ( $available_payment_gateways as $available_gateways_key => $available_gateways_val ) {
				$selectedVal                                           = is_array( $selected ) && ! empty( $selected ) && in_array( $available_gateways_key, $selected, true ) ? 'selected=selected' : '';
				$html                                                  .= '<option value="' . $available_gateways_val->id . '" ' . $selectedVal . '>' . $available_gateways_val->title . '</option>';
				$filter_payment_methods[ $available_gateways_val->id ] = $available_gateways_val->title;
			}
		}
		$html .= '</select>';
		if ( $json ) {
			return $this->convert_array_to_json( $filter_payment_methods );
		}
		return $html;
	}
	/**
	 * Function for select shipping methods
	 *
	 * @param string $count
	 * @param array  $selected
	 *
	 * @return string
	 */
	public function wdpad_get_active_shipping_methods__premium_only( $count = '', $selected = array(), $json = false ) {
		$shipping_methods = [];
		$active_methods   = array();
		$shipping_methods = WC()->shipping->get_shipping_methods();
		foreach ( $shipping_methods as $id => $shipping_method ) {
			if ( isset( $shipping_method->enabled ) && 'yes' === $shipping_method->enabled ) {
				$method_args           = array(
					'id'           => $shipping_method->id,
					'method_title' => $shipping_method->method_title,
					'title'        => $shipping_method->title,
					'tax_status'   => $shipping_method->tax_status,
				);
				$active_methods[ $id ] = $method_args;
			}
		}
		$html = '<select name="dpad[product_dpad_conditions_values][value_' . $count . '][]" class="product_dpad_conditions_values product_discount_select product_discount_select multiselect2" multiple="multiple">';
		if ( ! empty( $active_methods ) ) {
			foreach ( $active_methods as $method_key => $method_val ) {
				$selectedVal                           = is_array( $selected ) && ! empty( $selected ) && in_array( $method_key, $selected, true ) ? 'selected=selected' : '';
				$html                                  .= '<option value="' . $method_val['id'] . '" ' . $selectedVal . '>' . $method_val['method_title'] . '</option>';
				$shipping_methods[ $method_val['id'] ] = $method_val['method_title'];
			}
		}
		if ( $json ) {
			return $this->convert_array_to_json( $shipping_methods );
		}
		$html .= '</select>';
		return $html;
	}
	public function wdpad_welcome_conditional_dpad_screen_do_activation_redirect() {
		$this->wdpad_register_post_type();
		// if no activation redirect
		if ( ! get_transient( '_welcome_screen_wdpad_pro_mode_activation_redirect_data' ) ) {
			return;
		}
		// Delete the redirect transient
		delete_transient( '_welcome_screen_wdpad_pro_mode_activation_redirect_data' );
		// if activating from network, or bulk
		$activate_multi = filter_input( INPUT_GET, 'activate-multi', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( is_network_admin() || isset( $activate_multi ) ) {
			return;
		}
		// Redirect to extra cost welcome  page
		wp_safe_redirect( add_query_arg( array( 'page' => 'wcdrfc-page-get-started' ), admin_url( 'admin.php' ) ) );
		exit;
	}

	/**
	 * Register post type
	 *
	 * @since    2.3.0
	 */
	public function wdpad_register_post_type() {
		register_post_type( self::wdpad_post_type, array(
			'labels'          => array(
				'name'          => __( 'Conditional Discount Rule', 'woo-conditional-discount-rules-for-checkout' ),
				'singular_name' => __( 'Conditional Discount Rule', 'woo-conditional-discount-rules-for-checkout' ),
			),
			'rewrite'         => false,
			'query_var'       => false,
			'public'          => false,
			'capability_type' => 'page',
		) );
	}
	public function wdpad_remove_admin_submenus() {
        remove_submenu_page( 'dots_store', 'dots_store' );
		remove_submenu_page( 'dots_store', 'wcdrfc-page-information' );
		remove_submenu_page( 'dots_store', 'wcdrfc-rule-add-new' );
		remove_submenu_page( 'dots_store', 'wcdrfc-page-get-started' );
		remove_submenu_page( 'dots_store', 'wcdrfc-page-import-export' );
		remove_submenu_page( 'dots_store', 'wcdrfc-page-general-settings' );
		remove_submenu_page( 'dots_store', 'wcdrfc-page-licenses' );
        remove_submenu_page( 'dots_store', 'wcdrfc-upgrade-dashboard' );
        
        if ( wcdrfc_fs()->is__premium_only() ) {
            if ( wcdrfc_fs()->can_use_premium_code() ) {
                //Free product CSS for order details page(item)
                echo '<style>
                    span.free-product {
                        background: dodgerblue;
                        color: #fff;
                        padding: 0px 5px 2px;
                        border-radius: 5px;
                    }
                </style>';
            }
        }

        // Dotstore menu icon css
        echo '<style>
            .toplevel_page_dots_store .dashicons-marker::after{content:"";border:3px solid;position:absolute;top:14px;left:15px;border-radius:50%;opacity: 0.6;}
		    li.toplevel_page_dots_store:hover .dashicons-marker::after,li.toplevel_page_dots_store.current .dashicons-marker::after{opacity: 1;}
		    @media only screen and (max-width: 960px){
		    	.toplevel_page_dots_store .dashicons-marker::after{left:14px;}
		    }
        </style>';
	}

	/**
	 * Get simple and variable products on Ajax
	 *
	 * @since 1.0.0
	 *
	 */
	public function wdpad_simple_and_variation_product_list_ajax() {
		// Security check
		check_ajax_referer( 'wcdrfc_ajax_verification', 'security' );

		// Get products
		global $sitepress;
		if ( ! empty( $sitepress ) ) {
			$default_lang 				= $sitepress->get_default_language();
		}
		$json                           = true;
		$filter_product_list            = [];
		$request_value                  = filter_input( INPUT_GET, 'value', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$posts_per_page                 = filter_input( INPUT_GET, 'posts_per_page', FILTER_VALIDATE_INT );
		$offset                         = filter_input( INPUT_GET, 'offset', FILTER_VALIDATE_INT );
		$post_value                     = isset( $request_value ) ? sanitize_text_field( $request_value ) : '';
		$posts_per_page                 = isset( $posts_per_page ) ? intval( $posts_per_page ) : 0;
		$offset                         = isset( $offset ) ? intval( $offset ) : 0;
		$baselang_simple_product_ids    = array();
		$baselang_variation_product_ids = array();
		function wdpad_posts_where( $where, $wp_query ) {
			global $wpdb;
			$search_term = $wp_query->get( 'search_pro_title' );
			if ( ! empty( $search_term ) ) {
				$search_term_like = $wpdb->esc_like( $search_term );
				$where            .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $search_term_like ) . '%\'';
			}
			return $where;
		}
		$product_args = array(
			'post_type'        => array( 'product', 'product_variation' ),
			'posts_per_page'   => $posts_per_page,
			'search_pro_title' => $post_value,
			'post_status'      => 'publish',
			'orderby'          => 'title',
			'order'            => 'ASC',
            'offset'           => $posts_per_page * ( $offset - 1 )
		);
        
		add_filter( 'posts_where', 'wdpad_posts_where', 10, 2 );
		$get_wp_query = new WP_Query( $product_args );
		remove_filter( 'posts_where', 'wdpad_posts_where', 10, 2 );

		$get_all_products = $get_wp_query->posts;
        $baselang_product_ids = array();
		if ( isset( $get_all_products ) && ! empty( $get_all_products ) ) {
			foreach ( $get_all_products as $get_all_product ) {
				$_product = wc_get_product( $get_all_product->ID );
				if ( ! $_product->is_type( 'variable' ) ) {
                    if ( ! empty( $sitepress ) ) {
                        $defaultlang_simple_product_id = apply_filters( 'wpml_object_id', $get_all_product->ID, 'product', true, $default_lang );
                    } else {
                        $defaultlang_simple_product_id = $get_all_product->ID;
                    }
                    $baselang_product_ids[] = $defaultlang_simple_product_id;
				}
			}
		}
		$html                 = '';
		if ( isset( $baselang_product_ids ) && ! empty( $baselang_product_ids ) ) {
			foreach ( $baselang_product_ids as $baselang_product_id ) {
				$html                  .= '<option value="' . $baselang_product_id . '">' . '#' . $baselang_product_id . ' - ' . get_the_title( $baselang_product_id ) . '</option>';
				$filter_product_list[] = array( $baselang_product_id, '#' . $baselang_product_id . ' - ' . get_the_title( $baselang_product_id ) );
			}
		}
		if ( $json ) {
			echo wp_json_encode( $filter_product_list );
			wp_die();
		}
		echo wp_kses( $html, allowed_html_tags() );
		wp_die();
	}

	/**
	 * Get products on Ajax 
	 *
	 * @since 1.0.0
	 *
	 */
	public function wdpad_product_dpad_conditions_values_product_ajax() {
		// Security check
		check_ajax_referer( 'wcdrfc_ajax_verification', 'security' );

		// Get products
        global $sitepress;
        if ( ! empty( $sitepress ) ) {
			$default_lang = $sitepress->get_default_language();
		}
        $json                   = true;
		$filter_product_list    = [];
		$request_value          = filter_input( INPUT_GET, 'search', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$posts_per_page         = filter_input( INPUT_GET, 'posts_per_page', FILTER_VALIDATE_INT );
		$offset                 = filter_input( INPUT_GET, 'offset', FILTER_VALIDATE_INT );
		$post_value             = isset( $request_value ) ? sanitize_text_field( $request_value ) : '';
		$posts_per_page         = isset( $posts_per_page ) ? intval( $posts_per_page ) : 10;
		$offset                 = isset( $offset ) ? intval( $offset ) : 0;
		$baselang_product_ids   = array();
		
		function wdpad_posts_where( $where, $wp_query ) {
			global $wpdb;
			$search_term = $wp_query->get( 'search_pro_title' );
			if ( isset( $search_term ) ) {
				$search_term_like = $wpdb->esc_like( $search_term );
				$where            .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $search_term_like ) . '%\'';
			}
			return $where;
		}
		$product_args = array(
			'post_type'      => 'product',
			'posts_per_page' => $posts_per_page,
			'offset'         => $posts_per_page * ( $offset - 1 ),
			's'              => $post_value,
			'post_status'    => 'publish',
			'orderby'        => 'title',
			'order'          => 'ASC',
			'show_posts'     => - 1,
		);
		add_filter( 'posts_where', 'wdpad_posts_where', 10, 2 );
		$wp_query = new WP_Query( $product_args );
		remove_filter( 'posts_where', 'wdpad_posts_where', 10, 2 );
		$get_all_products = $wp_query->posts;
		if ( isset( $get_all_products ) && ! empty( $get_all_products ) ) {
			foreach ( $get_all_products as $get_all_product ) {
				if ( ! empty( $sitepress ) ) {
					$defaultlang_product_id = apply_filters( 'wpml_object_id', $get_all_product->ID, 'product', true, $default_lang );
				} else {
					$defaultlang_product_id = $get_all_product->ID;
				}
				$baselang_product_ids[] = $defaultlang_product_id;
			}
		}
		$html = '';
		if ( isset( $baselang_product_ids ) && ! empty( $baselang_product_ids ) ) {
			foreach ( $baselang_product_ids as $baselang_product_id ) {
				$_product = wc_get_product( $baselang_product_id );
				$html     .= '<option value="' . $baselang_product_id . '">' . '#' . $baselang_product_id . ' - ' . get_the_title( $baselang_product_id ) . '</option>';
				if ( $_product->get_type() === 'simple' ) {
					if ( $_product->get_type() === 'variable' ) {
						$vari = "(All variation)";
					} else {
						$vari = "";
					}
					$filter_product = array();
					$filter_product['id']       = $baselang_product_id;
					$filter_product['text']     = '#' . $baselang_product_id . ' - ' . get_the_title( $baselang_product_id ) . $vari;
					$filter_product_list[]      = $filter_product;
				}
			}
		}
		if ( $json ) {
			echo wp_json_encode( $filter_product_list );
			wp_die();
		}
		echo wp_kses( $html, allowed_html_tags() );
		wp_die();
	}
	public function wdpad_product_dpad_conditions_varible_values_product_ajax() {
		// Security check
		check_ajax_referer( 'wcdrfc_ajax_verification', 'security' );

		// Get variable products
		$json = true;
		global $sitepress;
		$post_value     = filter_input( INPUT_GET, 'search', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$posts_per_page = filter_input( INPUT_GET, 'posts_per_page', FILTER_VALIDATE_INT );
		$offset         = filter_input( INPUT_GET, 'offset', FILTER_VALIDATE_INT );
		$post_value     = isset( $post_value ) ? $post_value : '';
		$posts_per_page = isset( $posts_per_page ) ? intval( $posts_per_page ) : 10;
		$offset         = isset( $offset ) ? intval( $offset ) : 0;
		$baselang_product_ids = array();
		if ( ! empty( $sitepress ) ) {
			$default_lang = $sitepress->get_default_language();
		}
		function wdpad_posts_wheres( $where, $wp_query ) {
			global $wpdb;
			$search_term = $wp_query->get( 'search_pro_title' );
			if ( isset( $search_term ) ) {
				$search_term_like = $wpdb->esc_like( $search_term );
				$where            .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $search_term_like ) . '%\'';
			}
			return $where;
		}
		$product_args = array(
			'post_type'        => 'product',
			'posts_per_page'   => $posts_per_page,
			'offset'           => $posts_per_page * ( $offset - 1 ),
			'search_pro_title' => $post_value,
			'post_status'      => 'publish',
			'orderby'          => 'title',
			'order'            => 'ASC',
		);
		add_filter( 'posts_where', 'wdpad_posts_wheres', 10, 2 );
		$get_all_products = new WP_Query( $product_args );
		remove_filter( 'posts_where', 'wdpad_posts_wheres', 10, 2 );
		if ( ! empty( $get_all_products ) ) {
			foreach ( $get_all_products->posts as $get_all_product ) {
				$_product = wc_get_product( $get_all_product->ID );
				if ( $_product->is_type( 'variable' ) ) {
					$variations = $_product->get_available_variations();
					foreach ( $variations as $value ) {
						if ( ! empty( $sitepress ) ) {
							$defaultlang_product_id = apply_filters( 'wpml_object_id', $value['variation_id'], 'product', true, $default_lang );
						} else {
							$defaultlang_product_id = $value['variation_id'];
						}
						$baselang_product_ids[] = $defaultlang_product_id;
					}
				}
			}
		}
		$html                         = '';
		$filter_variable_product_list = [];
		if ( isset( $baselang_product_ids ) && ! empty( $baselang_product_ids ) ) {
			foreach ( $baselang_product_ids as $baselang_product_id ) {
				$html .= '<option value="' . $baselang_product_id . '">' . '#' . $baselang_product_id . ' - ' . get_the_title( $baselang_product_id ) . '</option>';
				$filter_variable_product = array();
				$filter_variable_product['id']      = $baselang_product_id;
				$filter_variable_product['text']    = '#' . $baselang_product_id . ' - ' . str_replace( '&#8211;', '-', get_the_title( $baselang_product_id ) );
				$filter_variable_product_list[]     = $filter_variable_product;
			}
		}
		if ( $json ) {
			echo wp_json_encode( $filter_variable_product_list );
			wp_die();
		}
		echo wp_kses( $html, allowed_html_tags() );
		wp_die();
	}
	function wdpad_admin_footer_review() {
		if ( wcdrfc_fs()->is__premium_only() ) {
            if( wcdrfc_fs()->can_use_premium_code() ) {
                echo sprintf( wp_kses( __( 'If you like <strong>%2$s</strong> plugin, please leave us ★★★★★ ratings on <a href="%1$s" target="_blank">DotStore</a>.', 'woo-conditional-discount-rules-for-checkout' ), array(
                    'strong' => array(),
                    'a'      => array(
                        'href'   => array(),
                        'target' => 'blank',
                    ),
                ) ), esc_url( 'https://www.thedotstore.com/woocommerce-conditional-discount-rules-for-checkout#tab-reviews' ),
                esc_html( WDPAD_PLUGIN_NAME ) );
            } else {
                echo sprintf( wp_kses( __( 'If you like <strong>%2$s</strong> plugin, please leave us ★★★★★ ratings on <a href="%1$s" target="_blank">DotStore</a>.', 'woo-conditional-discount-rules-for-checkout' ), array(
                    'strong' => array(),
                    'a'      => array(
                        'href'   => array(),
                        'target' => 'blank',
                    ),
                ) ), esc_url( 'https://wordpress.org/support/plugin/woo-conditional-discount-rules-for-checkout/reviews/#new-post' ),
                esc_html( WDPAD_PLUGIN_NAME ) );
            }
        } else {
            echo sprintf( wp_kses( __( 'If you like <strong>%2$s</strong> plugin, please leave us ★★★★★ ratings on <a href="%1$s" target="_blank">DotStore</a>.', 'woo-conditional-discount-rules-for-checkout' ), array(
                'strong' => array(),
                'a'      => array(
                    'href'   => array(),
                    'target' => 'blank',
                ),
            ) ), esc_url( 'https://wordpress.org/support/plugin/woo-conditional-discount-rules-for-checkout/reviews/#new-post' ),
            esc_html( WDPAD_PLUGIN_NAME ) );
        }
	}
	function conditional_discount_sorting() {
        global $plugin_public;
		check_ajax_referer( 'sorting_conditional_fee_action', 'sorting_conditional_fee' );

		$post_type 			= self::wdpad_post_type;
		$getPaged      		= filter_input( INPUT_POST, 'paged', FILTER_SANITIZE_NUMBER_INT);
		$getListingArray	= filter_input( INPUT_POST, 'listing', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY );
		
		$paged     			= !empty( $getPaged ) ? $getPaged : 1;
		$listinbgArray     	= !empty( $getListingArray ) ? array_map( 'intval', wp_unslash( $getListingArray ) ) : array();

		$query_args = array(
			'post_type'      => $post_type,
			'post_status'    => array( 'publish', 'draft' ),
			'posts_per_page' => -1,
			'orderby'        => array( 
                'menu_order' =>'ASC', 
                'post_date' => 'DESC'
            ),
			'fields' 		 => 'ids'
		);
		$post_list = new WP_Query( $query_args );
		$results = $post_list->posts; 

		//Create the list of ID's
		$objects_ids = array();            
		foreach($results as $result) {
			$objects_ids[] = (int)$result; 
		}
        
		//Here we switch order
		$per_page = get_user_option( 'dpad_per_page' );
		$per_page = ( !empty( $per_page ) || $per_page > 1 ) ? $per_page : 1;
		$edit_start_at = $paged * $per_page - $per_page;
		$index = 0;
		for( $i = $edit_start_at; $i < ($edit_start_at + $per_page); $i++ ) {

			if( !isset($objects_ids[$i]) )
				break;
				
			$objects_ids[$i] = (int)$listinbgArray[$index];
			$index++;
		}
		//Update the menu_order within database
		foreach( $objects_ids as $menu_order => $id ) {
            $data = array( 'menu_order' => $menu_order, 'ID' => $id);
            wp_update_post( $data );
			clean_post_cache( $id );
		}
        //Refresh our cache after bulk delete
        $plugin_public->wdpad_action_on_discount_list(true);
		wp_send_json_success( array('message' => esc_html__( 'Discount rule has been updated.', 'woo-conditional-discount-rules-for-checkout' ) ) );
	}
	public function dpad_updated_message( $message, $validation_msg ){
		if ( empty( $message ) ) {
			return false;
		}

		if ( 'created' === $message ) {
			$updated_message = esc_html__( "Discount rule has been created.", 'woo-conditional-discount-rules-for-checkout' );
		} elseif ( 'saved' === $message ) {
			$updated_message = esc_html__( "Discount rule has been updated.", 'woo-conditional-discount-rules-for-checkout' );
		} elseif ( 'deleted' === $message ) {
			$updated_message = esc_html__( "Discount rule has been deleted.", 'woo-conditional-discount-rules-for-checkout' );
		} elseif ( 'duplicated' === $message ) {
			$updated_message = esc_html__( "Discount rule has been duplicated.", 'woo-conditional-discount-rules-for-checkout' );
		} elseif ( 'disabled' === $message ) {
			$updated_message = esc_html__( "Discount rule has been disabled.", 'woo-conditional-discount-rules-for-checkout' );
		} elseif ( 'enabled' === $message ) {
			$updated_message = esc_html__( "Discount rule has been enabled.", 'woo-conditional-discount-rules-for-checkout' );
		} elseif ( 'setting_saved' === $message ) {
			$updated_message = esc_html__( "General setting has been saved.", 'woo-conditional-discount-rules-for-checkout' );
		}
		if ( 'failed' === $message ) {
			$failed_messsage = esc_html__( "There was an error with saving data.", 'woo-conditional-discount-rules-for-checkout' );
		} elseif ( 'nonce_check' === $message ) {
			$failed_messsage = esc_html__( "There was an error with security check.", 'woo-conditional-discount-rules-for-checkout' );
		}
		if ( 'validated' === $message ) {
			$validated_messsage = esc_html( $validation_msg );
		}
		
		if ( ! empty( $updated_message ) ) {
			echo sprintf( '<div id="message" class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html( $updated_message ) );
			return false;
		}
		if ( ! empty( $failed_messsage ) ) {
			echo sprintf( '<div id="message" class="notice notice-error is-dismissible"><p>%s</p></div>', esc_html( $failed_messsage ) );
			return false;
		}
		if ( ! empty( $validated_messsage ) ) {
			echo sprintf( '<div id="message" class="notice notice-error is-dismissible"><p>%s</p></div>', esc_html( $validated_messsage ) );
			return false;
		}
	}

    /**
	 * Display simple and variable product list based product specific option in Advance Pricing Rules
	 *
	 * @since  2.3.3
	 *
     * @author SJ
     * 
	 */
    public function wdpad_change_status_from_list_section(){
    	// Security check
		check_ajax_referer( 'wcdrfc_ajax_verification', 'security' );

		// Change rule status
        $get_current_dpad_id = filter_input( INPUT_POST, 'current_dpad_id', FILTER_SANITIZE_NUMBER_INT );
		$get_current_value   = filter_input( INPUT_POST, 'current_value', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		
        if ( ! ( isset( $get_current_dpad_id ) ) ) {
            wp_send_json_error( esc_html__( 'Something went wrong', 'woo-conditional-discount-rules-for-checkout' ) );
		}

		$post_id       = isset( $get_current_dpad_id ) ? absint( $get_current_dpad_id ) : '';
		$current_value = isset( $get_current_value ) ? sanitize_text_field( $get_current_value ) : '';

		if ( 'true' === $current_value ) {
			update_post_meta( $post_id, 'dpad_settings_status', 'on' );
            wp_send_json_success( esc_html__( 'Discount status has been enabled successfully.', 'woo-conditional-discount-rules-for-checkout' ) );
		} else {
			update_post_meta( $post_id, 'dpad_settings_status', 'off' );
            wp_send_json_success( esc_html__( 'Discount status has been disabled successfully.', 'woo-conditional-discount-rules-for-checkout' ) );
		}
    }

    /**
	 * Fetch slug based on id
	 *
	 * @since    2.3.3
	 */
	public function wdpad_fetch_slug__premium_only( $id_array, $condition ) {
		$return_array = array();
		if ( ! empty( $id_array ) ) {
			foreach ( $id_array as $key => $ids ) {
				if ( 'product' === $condition || 'variableproduct' === $condition || 'cpp' === $condition ) {
					$get_posts = get_post( $ids );
					if ( ! empty( $get_posts ) ) {
						$return_array[] = $get_posts->post_name;
					}
				} elseif ( 'category' === $condition || 'cpc' === $condition ) {
					$term           = get_term( $ids, 'product_cat' );
					if ( $term ) {
						$return_array[] = $term->slug;
					}
				} elseif ( 'tag' === $condition ) {
					$tag            = get_term( $ids, 'product_tag' );
					if ( $tag ) {
						$return_array[] = $tag->slug;
					}
				} elseif ( 'shipping_class' === $condition ) {
					$shipping_class                        = get_term( $key, 'product_shipping_class' );
					if ( $shipping_class ) {
						$return_array[ $shipping_class->slug ] = $ids;
					}
				} elseif ( 'cpsc' === $condition ) {
					$return_array[] = $ids;
				} elseif ( 'cpp' === $condition ) {
					$cpp_posts = get_post( $ids );
					if ( ! empty( $cpp_posts ) ) {
						$return_array[] = $cpp_posts->post_name;
					}
				} else {
					$return_array[] = $ids;
				}
			}
		}
		return $return_array;
	}

    /**
	 * Fetch id based on slug
	 *
	 * @since    2.3.3
	 */
	public function wdpad_fetch_id__premium_only( $slug_array, $condition ) {
		$return_array = array();
		if ( ! empty( $slug_array ) ) {
			foreach ( $slug_array as $slugs ) {
				if ( 'product' === $condition ) {
					$post           = get_page_by_path( $slugs, OBJECT, 'product' ); // phpcs:ignore
					$id             = $post->ID;
					$return_array[] = $id;
				} elseif ( 'variableproduct' === $condition ) {
					$args           = array(
						'post_type'  	   => 'product_variation',
						'fields'    	   => 'ids',
						'name'      	   => $slugs
					);
					$variable_posts = new WP_Query( $args );
					if ( ! empty( $variable_posts->posts ) ) {
						foreach ( $variable_posts->posts as $val ) {
							$return_array[] = $val;
						}
					}
				} elseif ( 'category' === $condition || 'cpc' === $condition ) {
					$term           = get_term_by( 'slug', $slugs, 'product_cat' );
					if ( $term ) {
						$return_array[] = $term->term_id;
					}
				} elseif ( 'tag' === $condition ) {
					$term_tag       = get_term_by( 'slug', $slugs, 'product_tag' );
					if ( $term_tag ) {
						$return_array[] = $term_tag->term_id;
					}
				} elseif ( 'shipping_class' === $condition || 'cpsc' === $condition ) {
					$term_tag = get_term_by( 'slug', $slugs, 'product_shipping_class' );
					if ( $term_tag ) {
						$return_array[ $term_tag->term_id ] = $slugs;
					}
				} elseif ( 'cpp' === $condition ) {
					$args           = array(
						'post_type' 	   => array( 'product_variation', 'product' ),
                        'fields'    	   => 'ids',
						'name'         	   => $slugs,
					);
					$variable_posts = new WP_Query( $args );
					if ( ! empty( $variable_posts->posts ) ) {
						foreach ( $variable_posts->posts as $val ) {
							$return_array[] = $val;
						}
					}
				} else {
					$return_array[] = $slugs;
				}
			}
		}
		return $return_array;
	}

    /**
	 * Export discount rule funcitonality
	 *
	 * @since  2.3.3
     * 
	 */
    public function wdpad_export_settings_action__premium_only() {
        WP_Filesystem();
        global $wp_filesystem;

        //Check ajax nonce reference
        check_ajax_referer('wdpad_export_action_nonce', 'security');

        $get_all_discount_args  = array(
            'post_type'      => self::wdpad_post_type,
            'order'          => 'DESC',
            'posts_per_page' => -1,
            'orderby'        => 'ID',
            'post_status'    => array( 'publish', 'draft' ),
            'fields'         => 'ids'
        );
        $get_all_discount_query = new WP_Query( $get_all_discount_args );
        $get_all_discount       = $get_all_discount_query->get_posts();
        $get_all_discount_count = $get_all_discount_query->found_posts;
        $discount_data          = array();
        if ( $get_all_discount_count > 0 ) {                
            foreach ( $get_all_discount as $discount_id ) {
                
                $discount_data[$discount_id]['discount_title'] = get_the_title( $discount_id );
                $discount_data[$discount_id]['status'] = get_post_status( $discount_id );

                //All metas details
                $post_meta_data = get_post_meta( $discount_id, '', true );
                $exlude_meta = array( 'dynamic_pricing_metabox', 'sm_metabox_ap_product', 'sm_metabox_ap_product_subtotal', 'sm_metabox_ap_product_weight', 'sm_metabox_ap_category', 'sm_metabox_ap_category_subtotal', 'sm_metabox_ap_category_weight', 'sm_metabox_ap_shipping_class_subtotal');
                if( !empty($post_meta_data) ){
                    foreach( $post_meta_data as $post_meta_k => $post_meta_v ){
                        if( !in_array( $post_meta_k, $exlude_meta, true ) ){
                            $discount_data[$discount_id][$post_meta_k] = $post_meta_v[0];
                        }
                    }
                }

                //Discount Rules for checkout fields/rules
                $sm_metabox_customize = array();
                $discountconditionArray = get_post_meta( $discount_id, 'dynamic_pricing_metabox', true );
                if ( ! empty( $discountconditionArray ) ) {
                    foreach ( $discountconditionArray as $key => $val ) {
                        if( in_array( $val['product_dpad_conditions_condition'], array( 'product', 'variableproduct', 'category', 'tag'), true ) ) {
                            $product_dpad_conditions_values = $this->wdpad_fetch_slug__premium_only( $val['product_dpad_conditions_values'], $val['product_dpad_conditions_condition'] );
                            $sm_metabox_customize[ $key ]   = array(
                                'product_dpad_conditions_condition' => $val['product_dpad_conditions_condition'],
                                'product_dpad_conditions_is'        => $val['product_dpad_conditions_is'],
                                'product_dpad_conditions_values'    => $product_dpad_conditions_values,
                            );
                        } else {
                            $sm_metabox_customize[ $key ] = array(
                                'product_dpad_conditions_condition' => $val['product_dpad_conditions_condition'],
                                'product_dpad_conditions_is'        => $val['product_dpad_conditions_is'],
                                'product_dpad_conditions_values'    => $val['product_dpad_conditions_values'],
                            );
                        }
                    }
                }
                $discount_data[$discount_id]['dynamic_pricing_metabox'] = $sm_metabox_customize;
                
                //Advanced Discount Price Rules: Cost on Product data
                $sm_metabox_ap_product_customize = array();
                $sm_metabox_ap_product = get_post_meta( $discount_id, 'sm_metabox_ap_product', true );
                if ( ! empty( $sm_metabox_ap_product ) ) {
                    foreach ( $sm_metabox_ap_product as $key => $val ) {
                        $ap_fees_products_values = $this->wdpad_fetch_slug__premium_only( $val['ap_fees_products'], 'cpp' );
                        $sm_metabox_ap_product_customize[ $key ] = array(
                            'ap_fees_products'         	=> $ap_fees_products_values,
                            'ap_fees_ap_prd_min_qty'   	=> $val['ap_fees_ap_prd_min_qty'],
                            'ap_fees_ap_prd_max_qty'   	=> $val['ap_fees_ap_prd_max_qty'],
                            'ap_fees_ap_price_product' 	=> $val['ap_fees_ap_price_product'],
                            'ap_fees_ap_per_product' 	=> isset($val['ap_fees_ap_per_product']) && !empty($val['ap_fees_ap_per_product']) && strpos($val['ap_fees_ap_price_product'], '%') ? $val['ap_fees_ap_per_product'] : 'no',
                        );
                    }
                }
                $discount_data[$discount_id]['sm_metabox_ap_product'] = $sm_metabox_ap_product_customize;
                
                //Advanced Discount Price Rules: Cost on Product Subtotal data
                $sm_metabox_ap_product_subtotal = get_post_meta( $discount_id, 'sm_metabox_ap_product_subtotal', true );
                $sm_metabox_ap_product_subtotal_customize = array();
                if ( ! empty( $sm_metabox_ap_product_subtotal ) ) {
                    foreach ( $sm_metabox_ap_product_subtotal as $key => $val ) {
                        $ap_fees_product_subtotal_values  = $this->wdpad_fetch_slug__premium_only( $val['ap_fees_product_subtotal'], 'cpp' );
                        $sm_metabox_ap_product_subtotal_customize[ $key ] = array(
                            'ap_fees_product_subtotal'                 => $ap_fees_product_subtotal_values,
                            'ap_fees_ap_product_subtotal_min_subtotal' => $val['ap_fees_ap_product_subtotal_min_subtotal'],
                            'ap_fees_ap_product_subtotal_max_subtotal' => $val['ap_fees_ap_product_subtotal_max_subtotal'],
                            'ap_fees_ap_price_product_subtotal'        => $val['ap_fees_ap_price_product_subtotal'],
                        );
                    }
                }
                $discount_data[$discount_id]['sm_metabox_ap_product_subtotal'] = $sm_metabox_ap_product_subtotal_customize;
                
                //Advanced Discount Price Rules: Cost on Product Weight data
                $sm_metabox_ap_product_weight = get_post_meta( $discount_id, 'sm_metabox_ap_product_weight', true );
                $sm_metabox_ap_product_weight_customize = array();
                if ( ! empty( $sm_metabox_ap_product_weight ) ) {
                    foreach ( $sm_metabox_ap_product_weight as $key => $val ) {
                        $ap_fees_product_weight_values = $this->wdpad_fetch_slug__premium_only( $val['ap_fees_product_weight'], 'cpp' );
                        $sm_metabox_ap_product_weight_customize[ $key ] = array(
                            'ap_fees_product_weight'            => $ap_fees_product_weight_values,
                            'ap_fees_ap_product_weight_min_qty' => $val['ap_fees_ap_product_weight_min_qty'],
                            'ap_fees_ap_product_weight_max_qty' => $val['ap_fees_ap_product_weight_max_qty'],
                            'ap_fees_ap_price_product_weight'   => $val['ap_fees_ap_price_product_weight'],
                        );
                    }
                }
                $discount_data[$discount_id]['sm_metabox_ap_product_weight'] = $sm_metabox_ap_product_weight_customize;

                //Advanced Discount Price Rules: Cost on Category data
                $sm_metabox_ap_category = get_post_meta( $discount_id, 'sm_metabox_ap_category', true );
                $sm_metabox_ap_category_customize = array();
                if ( ! empty( $sm_metabox_ap_category ) ) {
                    foreach ( $sm_metabox_ap_category as $key => $val ) {
                        $ap_fees_category_values = $this->wdpad_fetch_slug__premium_only( $val['ap_fees_categories'], 'cpc' );
                        $sm_metabox_ap_category_customize[ $key ] = array(
                            'ap_fees_categories'        => $ap_fees_category_values,
                            'ap_fees_ap_cat_min_qty'    => $val['ap_fees_ap_cat_min_qty'],
                            'ap_fees_ap_cat_max_qty'    => $val['ap_fees_ap_cat_max_qty'],
                            'ap_fees_ap_price_category' => $val['ap_fees_ap_price_category'],
                        );
                    }
                }
                $discount_data[$discount_id]['sm_metabox_ap_category'] = $sm_metabox_ap_category_customize;

                //Advanced Discount Price Rules: Cost on Category Subtotal data
                $sm_metabox_ap_category_subtotal = get_post_meta( $discount_id, 'sm_metabox_ap_category_subtotal', true );
                $sm_metabox_ap_category_subtotal_customize = array();
                if ( ! empty( $sm_metabox_ap_category_subtotal ) ) {
                    foreach ( $sm_metabox_ap_category_subtotal as $key => $val ) {
                        $ap_fees_category_subtotal_values = $this->wdpad_fetch_slug__premium_only( $val['ap_fees_category_subtotal'], 'cpc' );
                        $sm_metabox_ap_category_subtotal_customize[ $key ] = array(
                            'ap_fees_category_subtotal'                 => $ap_fees_category_subtotal_values,
                            'ap_fees_ap_category_subtotal_min_subtotal' => $val['ap_fees_ap_category_subtotal_min_subtotal'],
                            'ap_fees_ap_category_subtotal_max_subtotal' => $val['ap_fees_ap_category_subtotal_max_subtotal'],
                            'ap_fees_ap_price_category_subtotal'        => $val['ap_fees_ap_price_category_subtotal'],
                        );
                    }
                }
                $discount_data[$discount_id]['sm_metabox_ap_category_subtotal'] = $sm_metabox_ap_category_subtotal_customize;

                //Advanced Discount Price Rules: Cost on Category Weight data
                $sm_metabox_ap_category_weight = get_post_meta( $discount_id, 'sm_metabox_ap_category_weight', true );
                $sm_metabox_ap_category_weight_customize = array();
                if ( ! empty( $sm_metabox_ap_category_weight ) ) {
                    foreach ( $sm_metabox_ap_category_weight as $key => $val ) {
                        $ap_fees_category_weight_values = $this->wdpad_fetch_slug__premium_only( $val['ap_fees_categories_weight'], 'cpc' );
                        $sm_metabox_ap_category_weight_customize[ $key ] = array(
                            'ap_fees_categories_weight'          => $ap_fees_category_weight_values,
                            'ap_fees_ap_category_weight_min_qty' => $val['ap_fees_ap_category_weight_min_qty'],
                            'ap_fees_ap_category_weight_max_qty' => $val['ap_fees_ap_category_weight_max_qty'],
                            'ap_fees_ap_price_category_weight'   => $val['ap_fees_ap_price_category_weight'],
                        );
                    }
                }
                $discount_data[$discount_id]['sm_metabox_ap_category_weight'] = $sm_metabox_ap_category_weight_customize;

                //Advanced Discount Price Rules: Cost on Shipping Class Subtotal data
                $sm_metabox_ap_shipping_class_subtotal  = get_post_meta( $discount_id, 'sm_metabox_ap_shipping_class_subtotal', true );
                $sm_metabox_ap_shipping_class_subtotal_customize = array();
                if ( ! empty( $sm_metabox_ap_shipping_class_subtotal ) ) {
                    foreach ( $sm_metabox_ap_shipping_class_subtotal as $key => $val ) {
                        $ap_fees_shipping_class_subtotal_values = $this->wdpad_fetch_slug__premium_only( $val['ap_fees_shipping_class_subtotals'], 'cpsc' );
                        $sm_metabox_ap_shipping_class_subtotal_customize[ $key ] = array(
                            'ap_fees_shipping_class_subtotals'                => $ap_fees_shipping_class_subtotal_values,
                            'ap_fees_ap_shipping_class_subtotal_min_subtotal' => $val['ap_fees_ap_shipping_class_subtotal_min_subtotal'],
                            'ap_fees_ap_shipping_class_subtotal_max_subtotal' => $val['ap_fees_ap_shipping_class_subtotal_max_subtotal'],
                            'ap_fees_ap_price_shipping_class_subtotal'        => $val['ap_fees_ap_price_shipping_class_subtotal'],
                        );
                    }
                }
                $discount_data[$discount_id]['sm_metabox_ap_shipping_class_subtotal'] = $sm_metabox_ap_shipping_class_subtotal_customize;
            }
        }

        //General Setting data
        $general_data_arr = array();
        $general_setting_keys = array( 'wdpad_gs_adjustment_discount_type', 'wdpad_gs_sequential_discount' );
        foreach( $general_setting_keys as $general_setting_key ){
            $general_data_arr[$general_setting_key] = get_option($general_setting_key);
        }
        if( !empty($general_data_arr ) ) {
            $discount_data['general_settings'] = $general_data_arr;
        }
        
        $file_name = 'wcdrfc_export_'.time().'.json';

        $path_data = wp_get_upload_dir();
        $save_path = $path_data['basedir'].'/wcdrfc_plugin_data/';
        $download_path = $path_data['baseurl'].'/wcdrfc_plugin_data/';

        //Create new directory for plugin JSON files store
        if( ! file_exists($save_path) ){
            wp_mkdir_p($save_path);    
        }

        //Remove all previous files
        $files = glob("$save_path/*.json");
        foreach ($files as $csv_file) {
            wp_delete_file($csv_file);
        }

        $json_data = wp_json_encode( $discount_data );

        //Save new data to JSON file
        $wp_filesystem->put_contents( $save_path.$file_name, $json_data );

        wp_send_json_success( array( 'message' => esc_html__( 'Data has been Exported!', 'woo-conditional-discount-rules-for-checkout' ), 'download_path' => $download_path.$file_name ));
    }

    /**
	 * Import discount rule funcitonality
	 *
	 * @since  2.3.3
     * 
	 */
    public function wdpad_import_settings_action__premium_only(){
        WP_Filesystem();
        global $wp_filesystem, $plugin_public;

        //Check ajax nonce reference
        check_ajax_referer('wdpad_import_action_nonce', 'security');

        $file_import_file_args      = array(
            'import_file' => array(
                'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'flags'  => FILTER_FORCE_ARRAY,
            ),
        );
        
        $attached_import_files__arr = filter_var_array( $_FILES, $file_import_file_args );
        $import_file = $attached_import_files__arr['import_file']['tmp_name'];
        if ( empty( $import_file ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Please upload a file to import', 'woo-conditional-discount-rules-for-checkout' ) ) );
        }

        $attached_import_files__arr_explode = explode( '.', $attached_import_files__arr['import_file']['name'] );
        $extension                          = end( $attached_import_files__arr_explode );
        if ( $extension !== 'json' ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Please upload a valid .json file', 'woo-conditional-discount-rules-for-checkout' ) ) );
        }
        
        $discount_data = $wp_filesystem->get_contents( $import_file );
        if ( ! empty( $discount_data ) ) {
            $discount_data_decode = json_decode( $discount_data, true );
            foreach( $discount_data_decode as $discount_key => $discount_val ){
                if( 'general_settings' === $discount_key ){
                    foreach( $discount_val as $dk => $dv ) {
                        update_option($dk, $dv);
                    }
                } else {
                    $discount_post    = array(
                        'post_title'  => $discount_val['discount_title'],
                        'post_status' => $discount_val['status'],
                        'post_type'   => self::wdpad_post_type
                    );
                    $fount_post = post_exists( $discount_val['discount_title'], '', '', self::wdpad_post_type );
                    if( $fount_post > 0 && !empty($fount_post) ){
                        $discount_post['ID'] = $fount_post;
                        $discount_id = wp_update_post( $discount_post );
                    } else {
                        $discount_id = wp_insert_post( $discount_post );
                    }
                    if ( '' !== $discount_id && 0 !== $discount_id && $discount_id > 0 ) {

                        //All metas details
                        $exlude_meta = array( 'discount_title', 'status', 'dynamic_pricing_metabox', 'sm_metabox_ap_product', 'sm_metabox_ap_product_subtotal', 'sm_metabox_ap_product_weight', 'sm_metabox_ap_category', 'sm_metabox_ap_category_subtotal', 'sm_metabox_ap_category_weight', 'sm_metabox_ap_shipping_class_subtotal');
                        foreach( $discount_val as $discount_meta_key => $discount_meta_val ){
                            if( !in_array( $discount_meta_key, $exlude_meta, true ) ){
                                $discount_meta_val  = maybe_unserialize($discount_meta_val);
                                update_post_meta( $discount_id, $discount_meta_key, $discount_meta_val );
                            }
                        }
                        
                        //Discount Rules for checkout fields/rules
                        $sm_metabox_customize = array();
                        if ( isset( $discount_val['dynamic_pricing_metabox'] ) && !empty( $discount_val['dynamic_pricing_metabox'] ) ) {
                            foreach ( $discount_val['dynamic_pricing_metabox'] as $key => $val ) {
                                if( in_array( $val['product_dpad_conditions_condition'], array( 'product', 'variableproduct', 'category', 'tag'), true ) ) {
                                    $product_dpad_conditions_values = $this->wdpad_fetch_id__premium_only( $val['product_dpad_conditions_values'], $val['product_dpad_conditions_condition'] );
                                    $sm_metabox_customize[ $key ]   = array(
                                        'product_dpad_conditions_condition' => $val['product_dpad_conditions_condition'],
                                        'product_dpad_conditions_is'        => $val['product_dpad_conditions_is'],
                                        'product_dpad_conditions_values'    => $product_dpad_conditions_values,
                                    );
                                } else {
                                    $sm_metabox_customize[ $key ] = array(
                                        'product_dpad_conditions_condition' => $val['product_dpad_conditions_condition'],
                                        'product_dpad_conditions_is'        => $val['product_dpad_conditions_is'],
                                        'product_dpad_conditions_values'    => $val['product_dpad_conditions_values'],
                                    );
                                }
                            }
                        }
                        update_post_meta( $discount_id, 'dynamic_pricing_metabox', $sm_metabox_customize );

                        //Advanced Discount Price Rules: Cost on Product data
                        $sm_metabox_ap_product_customize = array();
                        if ( isset( $discount_val['sm_metabox_ap_product'] ) && !empty( $discount_val['sm_metabox_ap_product'] ) ) {
                            foreach ( $discount_val['sm_metabox_ap_product'] as $key => $val ) {
                                $ap_fees_products_values = $this->wdpad_fetch_id__premium_only( $val['ap_fees_products'], 'cpp' );
                                $sm_metabox_ap_product_customize[ $key ] = array(
                                    'ap_fees_products'         	=> $ap_fees_products_values,
                                    'ap_fees_ap_prd_min_qty'   	=> $val['ap_fees_ap_prd_min_qty'],
                                    'ap_fees_ap_prd_max_qty'   	=> $val['ap_fees_ap_prd_max_qty'],
                                    'ap_fees_ap_price_product' 	=> $val['ap_fees_ap_price_product'],
                                    'ap_fees_ap_per_product' 	=> isset($val['ap_fees_ap_per_product']) && !empty($val['ap_fees_ap_per_product']) && strpos($val['ap_fees_ap_price_product'], '%') ? $val['ap_fees_ap_per_product'] : 'no',
                                );
                            }
                        }
                        update_post_meta( $discount_id, 'sm_metabox_ap_product', $sm_metabox_ap_product_customize );
                        
                        //Advanced Discount Price Rules: Cost on Product Subtotal data
                        $sm_metabox_ap_product_subtotal_customize = array();
                        if ( isset( $discount_val['sm_metabox_ap_product_subtotal'] ) && !empty( $discount_val['sm_metabox_ap_product_subtotal'] ) ) {
                            foreach ( $discount_val['sm_metabox_ap_product_subtotal'] as $key => $val ) {
                                $ap_fees_product_subtotal_values  = $this->wdpad_fetch_id__premium_only( $val['ap_fees_product_subtotal'], 'cpp' );
                                $sm_metabox_ap_product_subtotal_customize[ $key ] = array(
                                    'ap_fees_product_subtotal'                 => $ap_fees_product_subtotal_values,
                                    'ap_fees_ap_product_subtotal_min_subtotal' => $val['ap_fees_ap_product_subtotal_min_subtotal'],
                                    'ap_fees_ap_product_subtotal_max_subtotal' => $val['ap_fees_ap_product_subtotal_max_subtotal'],
                                    'ap_fees_ap_price_product_subtotal'        => $val['ap_fees_ap_price_product_subtotal'],
                                );
                            }
                        }
                        update_post_meta( $discount_id, 'sm_metabox_ap_product_subtotal', $sm_metabox_ap_product_subtotal_customize );
                        
                        //Advanced Discount Price Rules: Cost on Product Weight data
                        $sm_metabox_ap_product_weight_customize = array();
                        if ( isset( $discount_val['sm_metabox_ap_product_weight'] ) && !empty( $discount_val['sm_metabox_ap_product_weight'] ) ) {
                            foreach ( $discount_val['sm_metabox_ap_product_weight'] as $key => $val ) {
                                $ap_fees_product_weight_values = $this->wdpad_fetch_id__premium_only( $val['ap_fees_product_weight'], 'cpp' );
                                $sm_metabox_ap_product_weight_customize[ $key ] = array(
                                    'ap_fees_product_weight'            => $ap_fees_product_weight_values,
                                    'ap_fees_ap_product_weight_min_qty' => $val['ap_fees_ap_product_weight_min_qty'],
                                    'ap_fees_ap_product_weight_max_qty' => $val['ap_fees_ap_product_weight_max_qty'],
                                    'ap_fees_ap_price_product_weight'   => $val['ap_fees_ap_price_product_weight'],
                                );
                            }
                        }
                        update_post_meta( $discount_id, 'sm_metabox_ap_product_weight', $sm_metabox_ap_product_weight_customize );

                        //Advanced Discount Price Rules: Cost on Category data
                        $sm_metabox_ap_category_customize = array();
                        if ( isset( $discount_val['sm_metabox_ap_category'] ) && !empty( $discount_val['sm_metabox_ap_category'] ) ) {
                            foreach ( $discount_val['sm_metabox_ap_category'] as $key => $val ) {
                                $ap_fees_category_values = $this->wdpad_fetch_id__premium_only( $val['ap_fees_categories'], 'cpc' );
                                $sm_metabox_ap_category_customize[ $key ] = array(
                                    'ap_fees_categories'        => $ap_fees_category_values,
                                    'ap_fees_ap_cat_min_qty'    => $val['ap_fees_ap_cat_min_qty'],
                                    'ap_fees_ap_cat_max_qty'    => $val['ap_fees_ap_cat_max_qty'],
                                    'ap_fees_ap_price_category' => $val['ap_fees_ap_price_category'],
                                );
                            }
                        }
                        update_post_meta( $discount_id, 'sm_metabox_ap_category', $sm_metabox_ap_category_customize );

                        //Advanced Discount Price Rules: Cost on Category Subtotal data
                        $sm_metabox_ap_category_subtotal_customize = array();
                        if ( isset( $discount_val['sm_metabox_ap_category_subtotal'] ) && !empty( $discount_val['sm_metabox_ap_category_subtotal'] ) ) {
                            foreach ( $discount_val['sm_metabox_ap_category_subtotal'] as $key => $val ) {
                                $ap_fees_category_subtotal_values = $this->wdpad_fetch_id__premium_only( $val['ap_fees_category_subtotal'], 'cpc' );
                                $sm_metabox_ap_category_subtotal_customize[ $key ] = array(
                                    'ap_fees_category_subtotal'                 => $ap_fees_category_subtotal_values,
                                    'ap_fees_ap_category_subtotal_min_subtotal' => $val['ap_fees_ap_category_subtotal_min_subtotal'],
                                    'ap_fees_ap_category_subtotal_max_subtotal' => $val['ap_fees_ap_category_subtotal_max_subtotal'],
                                    'ap_fees_ap_price_category_subtotal'        => $val['ap_fees_ap_price_category_subtotal'],
                                );
                            }
                        }
                        update_post_meta( $discount_id, 'sm_metabox_ap_category_subtotal', $sm_metabox_ap_category_subtotal_customize );

                        //Advanced Discount Price Rules: Cost on Category Weight data
                        $sm_metabox_ap_category_weight_customize = array();
                        if ( isset( $discount_val['sm_metabox_ap_category_weight'] ) && !empty( $discount_val['sm_metabox_ap_category_weight'] ) ) {
                            foreach ( $discount_val['sm_metabox_ap_category_weight'] as $key => $val ) {
                                $ap_fees_category_weight_values = $this->wdpad_fetch_id__premium_only( $val['ap_fees_categories_weight'], 'cpc' );
                                $sm_metabox_ap_category_weight_customize[ $key ] = array(
                                    'ap_fees_categories_weight'          => $ap_fees_category_weight_values,
                                    'ap_fees_ap_category_weight_min_qty' => $val['ap_fees_ap_category_weight_min_qty'],
                                    'ap_fees_ap_category_weight_max_qty' => $val['ap_fees_ap_category_weight_max_qty'],
                                    'ap_fees_ap_price_category_weight'   => $val['ap_fees_ap_price_category_weight'],
                                );
                            }
                        }
                        update_post_meta( $discount_id, 'sm_metabox_ap_category_weight', $sm_metabox_ap_category_weight_customize );

                        //Advanced Discount Price Rules: Cost on Shipping Class Subtotal data
                        $sm_metabox_ap_shipping_class_subtotal_customize = array();
                        if ( isset( $discount_val['sm_metabox_ap_shipping_class_subtotal'] ) && !empty( $discount_val['sm_metabox_ap_shipping_class_subtotal'] ) ) {
                            foreach ( $discount_val['sm_metabox_ap_shipping_class_subtotal'] as $key => $val ) {
                                $ap_fees_shipping_class_subtotal_values = $this->wdpad_fetch_id__premium_only( $val['ap_fees_shipping_class_subtotals'], 'cpsc' );
                                $sm_metabox_ap_shipping_class_subtotal_customize[ $key ] = array(
                                    'ap_fees_shipping_class_subtotals'                => $ap_fees_shipping_class_subtotal_values,
                                    'ap_fees_ap_shipping_class_subtotal_min_subtotal' => $val['ap_fees_ap_shipping_class_subtotal_min_subtotal'],
                                    'ap_fees_ap_shipping_class_subtotal_max_subtotal' => $val['ap_fees_ap_shipping_class_subtotal_max_subtotal'],
                                    'ap_fees_ap_price_shipping_class_subtotal'        => $val['ap_fees_ap_price_shipping_class_subtotal'],
                                );
                            }
                        }
                        update_post_meta( $discount_id, 'sm_metabox_ap_shipping_class_subtotal', $sm_metabox_ap_shipping_class_subtotal_customize );
                    }
                }
            }

            //Refresh our cache after import process completed
            $plugin_public->wdpad_action_on_discount_list(true);
        } else {
            wp_send_json_error( array( 'message' => esc_html__( 'Blank .json file uploaded.', 'woo-conditional-discount-rules-for-checkout' ) ) );
        }

        wp_send_json_success( array( 'message' => esc_html__( 'Data has been Imported!', 'woo-conditional-discount-rules-for-checkout' ) ) );

    }

    /**
	 * Category search for Sdjustment discount type
	 *
	 * @since 2.4.0
	 *
	 */
	public function wdpad_category_list_ajax__premium_only() {
		// Security check
		check_ajax_referer( 'wcdrfc_ajax_verification', 'security' );

		// Get category
		global $sitepress;
		if ( ! empty( $sitepress ) ) {
			$default_lang 				= $sitepress->get_default_language();
		}
		$json                           = true;
		$filter_product_list            = [];
		$request_value                  = filter_input( INPUT_GET, 'value', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$post_value                     = isset( $request_value ) ? sanitize_text_field( $request_value ) : '';
		$posts_per_page                 = filter_input( INPUT_GET, 'posts_per_page', FILTER_VALIDATE_INT );
		$posts_per_page                 = isset( $posts_per_page ) ? intval( $posts_per_page ) : 0;
		$offset                         = filter_input( INPUT_GET, 'offset', FILTER_VALIDATE_INT );
		$offset                         = isset( $offset ) ? intval( $offset ) : 0;
		
		$args                 = array(
			'taxonomy'      => 'product_cat',
			'orderby'       => 'name',
			'hierarchical'  => 1,
			'hide_empty'    => 1,
            'search'        => $post_value,
            'number'        => $posts_per_page,
            'offset'        => $posts_per_page * ( $offset - 1 )
		);
		$get_all_categories   = get_terms( 'product_cat', $args );

		$html                 = '';
        $filter_category_list = array();
		if ( isset( $get_all_categories ) && ! empty( $get_all_categories ) ) {
            foreach ( $get_all_categories as $get_all_category ) {
				if ( $get_all_category ) {
					if ( ! empty( $sitepress ) ) {
						$new_cat_id = apply_filters( 'wpml_object_id', $get_all_category->term_id, 'product_cat', true, $default_lang );
					} else {
						$new_cat_id = $get_all_category->term_id;
					}
					$category = get_term_by( 'id', $new_cat_id, 'product_cat' );
                    if ( $category->parent > 0 ) {
                        $parent_list = get_term_parents_list($new_cat_id, 'product_cat', array('separator' => ' -> ', 'link' => false, 'inclusive' => false));
                        $filter_category_list[ $category->term_id ] = '#' . $category->term_id . ' - ' . $parent_list . $category->name;
                    } else {
                        $filter_category_list[ $category->term_id ] = '#' . $category->term_id . ' - ' . $category->name;
                    }
				}
			}
		}
		if ( $json ) {
			echo wp_json_encode( $filter_category_list );
			wp_die();
		}
		echo wp_kses( $html, allowed_html_tags() );
		wp_die();
	}

    public function wdpad_before_order_itemmeta__premium_only( $item_id, $item, $product ){
        $item_metas = $item->get_formatted_meta_data( '', true );
        if( !empty( $item_metas ) ){
            foreach( $item_metas as $item_meta ){
                if( '_dpad_get_discount_product' === $item_meta->key ) {
                    echo wp_kses_post( sprintf( ' - <span class="free-product">%s</span>', esc_html__( 'Free', 'woo-conditional-discount-rules-for-checkout' ) ) );
                }
            }
        }
    }

    public function wdpad_save_general_settings(){
        
        $nonce = filter_input( INPUT_POST, 'dpad_save_general_setting_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        if( ! wp_verify_nonce( $nonce, 'dpad_save_general_setting' ) ){
            return false;
        }

        $get_adjustment_discount_type = filter_input( INPUT_POST, 'dpad_gs_adjustment_discount_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $get_adjustment_discount_type = !empty($get_adjustment_discount_type) ? $get_adjustment_discount_type : 'first';
        update_option( 'wdpad_gs_adjustment_discount_type', $get_adjustment_discount_type );


        $get_sequential_discount = filter_input( INPUT_POST, 'dpad_gs_sequential_discount', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $get_sequential_discount = !empty($get_sequential_discount) ? $get_sequential_discount : 'no';
        update_option( 'wdpad_gs_sequential_discount', $get_sequential_discount );

        
        $redirect_url = add_query_arg( array( 'page' => 'wcdrfc-page-general-settings', 'message' => 'validated' ), admin_url( 'admin.php' ) );

        wp_redirect( $redirect_url, 301 );
        exit;

    }

    /**
     * Specify the columns we wish to hide by default.
     *
     * @param array     $hidden Columns set to be hidden.
     * @param WP_Screen $screen Screen object.
     * @param bool      $use_defaults Whether to show the default columns.
     *
     * @return array
     */
    public function wdpad_default_hidden_columns( $hidden, WP_Screen $screen, $use_defaults  ) {
        
        if( false === $hidden && !empty( $screen->id ) && false !== strpos( $screen->id, '_page_wcdrfc-rules-list' ) ){
            settype( $hidden, 'array' );
            $hidden = array_merge( $hidden, array( 'date' ) );
        }
        
        return $hidden;
    }

    /**
     * Get dynamic promotional bar of plugin
     *
     * @param   String  $plugin_slug  slug of the plugin added in the site option
     * @since    3.9.3
     * 
     * @return  null
     */
    public function wdpad_get_promotional_bar( $plugin_slug = '' ) {
        $promotional_bar_upi_url = WDPAD_STORE_URL . 'wp-json/dpb-promotional-banner/v2/dpb-promotional-banner?' . wp_rand();
        $promotional_banner_request    = wp_remote_get( $promotional_bar_upi_url );  //phpcs:ignore
        if ( empty( $promotional_banner_request->errors ) ) {
            $promotional_banner_request_body = $promotional_banner_request['body'];	
            $promotional_banner_request_body = json_decode( $promotional_banner_request_body, true );
            echo '<div class="dynamicbar_wrapper">';
            
            if ( ! empty( $promotional_banner_request_body ) && is_array( $promotional_banner_request_body ) ) {
                foreach ( $promotional_banner_request_body as $promotional_banner_request_body_data ) {
					$promotional_banner_id        	  	= $promotional_banner_request_body_data['promotional_banner_id'];
                    $promotional_banner_cookie          = $promotional_banner_request_body_data['promotional_banner_cookie'];
                    $promotional_banner_image           = $promotional_banner_request_body_data['promotional_banner_image'];
                    $promotional_banner_description     = $promotional_banner_request_body_data['promotional_banner_description'];
                    $promotional_banner_button_group    = $promotional_banner_request_body_data['promotional_banner_button_group'];
                    $dpb_schedule_campaign_type         = $promotional_banner_request_body_data['dpb_schedule_campaign_type'];
                    $promotional_banner_target_audience = $promotional_banner_request_body_data['promotional_banner_target_audience'];

                    if ( ! empty( $promotional_banner_target_audience ) ) {
                        $plugin_keys = array();
                        if(is_array ($promotional_banner_target_audience)) {
                            foreach($promotional_banner_target_audience as $list) {
                                $plugin_keys[] = $list['value'];
                            }
                        } else {
                            $plugin_keys[] = $promotional_banner_target_audience['value'];
                        }

                        $display_banner_flag = false;
                        if ( in_array ( 'all_customers', $plugin_keys, true ) || in_array ( $plugin_slug, $plugin_keys, true ) ) {
                            $display_banner_flag = true;
                        }
                    }
                    
                    if ( true === $display_banner_flag ) {
                        if ( 'default' === $dpb_schedule_campaign_type ) {
                            $banner_cookie_show         = filter_input( INPUT_COOKIE, 'banner_show_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            $banner_cookie_visible_once = filter_input( INPUT_COOKIE, 'banner_show_once_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            $flag                       = false;
                            if ( empty( $banner_cookie_show ) && empty( $banner_cookie_visible_once ) ) {
                                setcookie( 'banner_show_' . $promotional_banner_cookie, 'yes', time() + ( 86400 * 7 ) ); //phpcs:ignore
                                setcookie( 'banner_show_once_' . $promotional_banner_cookie, 'yes' ); //phpcs:ignore
                                $flag = true;
                            }

                            $banner_cookie_show = filter_input( INPUT_COOKIE, 'banner_show_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            if ( ! empty( $banner_cookie_show ) || true === $flag ) {
                                $banner_cookie = filter_input( INPUT_COOKIE, 'banner_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                                $banner_cookie = isset( $banner_cookie ) ? $banner_cookie : '';
                                if ( empty( $banner_cookie ) && 'yes' !== $banner_cookie ) { ?>
                            	<div class="dpb-popup <?php echo isset( $promotional_banner_cookie ) ? esc_html( $promotional_banner_cookie ) : 'default-banner'; ?>">
                                    <?php
                                    if ( ! empty( $promotional_banner_image ) ) {
                                        ?>
                                        <img src="<?php echo esc_url( $promotional_banner_image ); ?>"/>
                                        <?php
                                    }
                                    ?>
                                    <div class="dpb-popup-meta">
                                        <p>
                                            <?php
                                            echo wp_kses_post( str_replace( array( '<p>', '</p>' ), '', $promotional_banner_description ) );
                                            if ( ! empty( $promotional_banner_button_group ) ) {
                                                foreach ( $promotional_banner_button_group as $promotional_banner_button_group_data ) {
                                                    ?>
                                                    <a href="<?php echo esc_url( $promotional_banner_button_group_data['promotional_banner_button_link'] ); ?>" target="_blank"><?php echo esc_html( $promotional_banner_button_group_data['promotional_banner_button_text'] ); ?></a>
                                                    <?php
                                                }
                                            }
                                            ?>
                                    	</p>
                                    </div>
                                    <a href="javascript:void(0);" data-bar-id="<?php echo esc_attr($promotional_banner_id); ?>" data-popup-name="<?php echo isset( $promotional_banner_cookie ) ? esc_html( $promotional_banner_cookie ) : 'default-banner'; ?>" class="dpbpop-close"><svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10"><path id="Icon_material-close" data-name="Icon material-close" d="M17.5,8.507,16.493,7.5,12.5,11.493,8.507,7.5,7.5,8.507,11.493,12.5,7.5,16.493,8.507,17.5,12.5,13.507,16.493,17.5,17.5,16.493,13.507,12.5Z" transform="translate(-7.5 -7.5)" fill="#acacac"/></svg></a>
                                </div>
                                <?php
                                }
                            }
                        } else {

                            $banner_cookie_show         = filter_input( INPUT_COOKIE, 'banner_show_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            $banner_cookie_visible_once = filter_input( INPUT_COOKIE, 'banner_show_once_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            $flag                       = false;
                            if ( empty( $banner_cookie_show ) && empty( $banner_cookie_visible_once ) ) {
                                setcookie( 'banner_show_' . $promotional_banner_cookie, 'yes'); //phpcs:ignore
                                setcookie( 'banner_show_once_' . $promotional_banner_cookie, 'yes' ); //phpcs:ignore
                                $flag = true;
                            }

                            $banner_cookie_show = filter_input( INPUT_COOKIE, 'banner_show_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            if ( ! empty( $banner_cookie_show ) || true === $flag ) {

                                $banner_cookie = filter_input( INPUT_COOKIE, 'banner_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                                $banner_cookie = isset( $banner_cookie ) ? $banner_cookie : '';
                                if ( empty( $banner_cookie ) && 'yes' !== $banner_cookie ) { ?>
                    			<div class="dpb-popup <?php echo isset( $promotional_banner_cookie ) ? esc_html( $promotional_banner_cookie ) : 'default-banner'; ?>">
                                    <?php
                                    if ( ! empty( $promotional_banner_image ) ) {
                                        ?>
                                            <img src="<?php echo esc_url( $promotional_banner_image ); ?>"/>
                                        <?php
                                    }
                                    ?>
                                    <div class="dpb-popup-meta">
                                        <p>
                                            <?php
                                            echo wp_kses_post( str_replace( array( '<p>', '</p>' ), '', $promotional_banner_description ) );
                                            if ( ! empty( $promotional_banner_button_group ) ) {
                                                foreach ( $promotional_banner_button_group as $promotional_banner_button_group_data ) {
                                                    ?>
                                                    <a href="<?php echo esc_url( $promotional_banner_button_group_data['promotional_banner_button_link'] ); ?>" target="_blank"><?php echo esc_html( $promotional_banner_button_group_data['promotional_banner_button_text'] ); ?></a>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </p>
                                    </div>
                                    <a href="javascript:void(0);" data-popup-name="<?php echo isset( $promotional_banner_cookie ) ? esc_html( $promotional_banner_cookie ) : 'default-banner'; ?>" class="dpbpop-close"><svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10"><path id="Icon_material-close" data-name="Icon material-close" d="M17.5,8.507,16.493,7.5,12.5,11.493,8.507,7.5,7.5,8.507,11.493,12.5,7.5,16.493,8.507,17.5,12.5,13.507,16.493,17.5,17.5,16.493,13.507,12.5Z" transform="translate(-7.5 -7.5)" fill="#acacac"/></svg></a>
                                </div>
                                <?php
                                }
                            }
                        }
                    }
                }
            }
            echo '</div>';
        }
    }

    /**
     * Get and save plugin setup wizard data
     * 
     * @since    2.4.0
     * 
     */
    public function wdpad_plugin_setup_wizard_submit() {
    	check_ajax_referer( 'wizard_ajax_nonce', 'nonce' );

    	$survey_list = filter_input( INPUT_GET, 'survey_list', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

    	if ( !empty($survey_list) && 'Select One' !== $survey_list ) {
    		update_option('wdpad_where_hear_about_us', $survey_list);
    	}
		wp_die();
    }

    /**
     * Send setup wizard data to sendinblue
     * 
     * @since    2.4.0
     * 
     */
    public function wdpad_send_wizard_data_after_plugin_activation() {
    	$send_wizard_data = filter_input(INPUT_GET, 'send-wizard-data', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

		if ( isset( $send_wizard_data ) && !empty( $send_wizard_data ) ) {
			if ( !get_option('wdpad_data_submited_in_sendiblue') ) {
				$wdpad_where_hear = get_option('wdpad_where_hear_about_us');
				$get_user = wcdrfc_fs()->get_user();
				$data_insert_array = array();
				if ( isset( $get_user ) && !empty( $get_user ) ) {
					$data_insert_array = array(
						'user_email'              => $get_user->email,
						'ACQUISITION_SURVEY_LIST' => $wdpad_where_hear,
					);	
				}
				$feedback_api_url = WDPAD_STORE_URL . 'wp-json/dotstore-sendinblue-data/v2/dotstore-sendinblue-data?' . wp_rand();
				$query_url        = $feedback_api_url . '&' . http_build_query( $data_insert_array );
				if ( function_exists( 'vip_safe_wp_remote_get' ) ) {
					$response = vip_safe_wp_remote_get( $query_url, 3, 1, 20 );
				} else {
					$response = wp_remote_get( $query_url ); //phpcs:ignore
				}

				if ( ( !is_wp_error($response)) && (200 === wp_remote_retrieve_response_code( $response ) ) ) {
					update_option('wdpad_data_submited_in_sendiblue', '1');
					delete_option('wdpad_where_hear_about_us');
				}
			}
		}
    }
}