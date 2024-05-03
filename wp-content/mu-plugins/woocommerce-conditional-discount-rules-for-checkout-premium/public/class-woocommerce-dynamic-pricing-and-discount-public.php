<?php //phpcs:ignore
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woocommerce_Dynamic_Pricing_And_Discount_Pro
 * @subpackage Woocommerce_Dynamic_Pricing_And_Discount_Pro/public
 * @author     Multidots <inquiry@multidots.in>
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Woocommerce_Dynamic_Pricing_And_Discount_Pro_Public {

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
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woocommerce_Dynamic_Pricing_And_Discount_Pro_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocommerce_Dynamic_Pricing_And_Discount_Pro_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woocommerce-dynamic-pricing-and-discount-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woocommerce_Dynamic_Pricing_And_Discount_Pro_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocommerce_Dynamic_Pricing_And_Discount_Pro_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woocommerce-dynamic-pricing-and-discount-public.js', array( 'jquery' ), $this->version, false );

		wp_localize_script( $this->plugin_name, 'my_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	}

	function woocommerce_locate_template_product_wdpad_conditions( $template, $template_name, $template_path ) {

		global $woocommerce;

		$_template = $template;

		if ( ! $template_path ) {
			$template_path = $woocommerce->template_url;
		}

		$plugin_path = woocommerce_conditional_discount_rules_for_checkout_path() . '/woocommerce/';

		$template = locate_template(
			array(
				$template_path . $template_name,
				$template_name,
			)
		);

		// Modification: Get the template from this plugin, if it exists
		if ( ! $template && file_exists( $plugin_path . $template_name ) ) {
			$template = $plugin_path . $template_name;
		}

		if ( ! $template ) {
			$template = $_template;
		}

		// Return what we found
		return $template;
	}

	/**
	 * @param $package
	 */
	public function conditional_wdpad_add_to_cart( $package ) {

		global $woocommerce;
        
        //Get all discount IDs with WPML compatibile
		$get_all_dpad       = $this->wdpad_action_on_discount_list();
        $combine_cost       = 0;
		if ( ! empty( $get_all_dpad ) ) {

			foreach ( $get_all_dpad as $dpad_id ) {
				$discount_check = $this->wdpad_check_discount_condition( $dpad_id );
                if( $discount_check ){
                    $getFeeType = get_post_meta( $dpad_id, 'dpad_settings_select_dpad_type', true );
                    if ( isset( $getFeeType ) && ! empty( $getFeeType ) && $getFeeType === 'bogo' ) {
                        //BOGO related things 
                        
                    } elseif( isset( $getFeeType ) && ! empty( $getFeeType ) && $getFeeType === 'adjustment' ) {
                        //Adjustment related things 
                        
                    }
                }
			}
		}
	}

    public function wdpad_bogo_product_price_change__premium_only(){
        
        // This is necessary for WC 3.0+
        if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;

        // Avoiding hook repetition (when using price calculations for example | optional ) - In our case evey free product fire this hook so disable it now.
        // if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 )
        //     return;
        
        //For BOGO discount type
        foreach ( WC()->cart->cart_contents as $value ) {
            if( array_key_exists( 'dpad_get_discount_product', $value ) ) {
                $value['data']->set_price( 0 );
            }
        }
    }

    public function wdpad_check_discount_condition( $dpad_id ){

        // if ( is_admin() ){
        //     return false;
        // }
        
        global $woocommerce, $woocommerce_wpml, $sitepress, $current_user, $pagenow;

        // if( is_null($woocommerce->cart) ){
        //     return false;
        // }
		
        //Check discount enable or not
        // $getFeeStatus = get_post_meta( $dpad_id, 'dpad_settings_status', true );
        // if ( isset( $getFeeStatus ) && $getFeeStatus === 'off' ) {
        //     return false;
        // }

        if ( ! empty( $sitepress ) ) {
            $default_lang = $sitepress->get_default_language();
        } else {
            $get_site_language = get_bloginfo( "language" );
            if ( false !== strpos( $get_site_language, '-' ) ) {
                $get_site_language_explode = explode( '-', $get_site_language );
                $default_lang              = $get_site_language_explode[0];
            } else {
                $default_lang = $get_site_language;
            }
        }

        $cart_array         = $woocommerce->cart->get_cart();
		$cart_sub_total     = $woocommerce->cart->get_subtotal();
		$subtax             = $woocommerce->cart->get_subtotal_tax();
		$wtdc               = get_option( 'woocommerce_tax_display_cart' );
		if( isset( $subtax ) && !empty( $subtax ) && 'incl' === $wtdc ) {
			$cart_sub_total = $cart_sub_total + $subtax;	
		}
		$cart_final_products_array = array();
		$cart_products_subtotal    = 0;

        $final_is_passed_general_rule = $new_is_passed = $final_passed = array();

        //First order for user Start
        if ( wcdrfc_fs()->is__premium_only() ) {
            if ( wcdrfc_fs()->can_use_premium_code() ) {
                $getFirstOrderForUser   	= get_post_meta( $dpad_id, 'first_order_for_user', true );
                $firstOrderForUser   		= ( isset( $getFirstOrderForUser ) && ! empty( $getFirstOrderForUser ) && 'on' === $getFirstOrderForUser ) ? true : false;
                if( $firstOrderForUser && is_user_logged_in() ){
                    $current_user_id = get_current_user_id();
                    $check_for_user = $this->wdpad_check_first_order_for_user__premium_only( $current_user_id );
                    if( !$check_for_user ){
                        return false;
                    }
                }
                $getUserLoginStatus  = get_post_meta( $dpad_id, 'user_login_status', true );
                $userLoginStatus   = ( isset( $getUserLoginStatus ) && ! empty( $getUserLoginStatus ) && 'on' === $getUserLoginStatus ) ? true : false;
                if( $userLoginStatus && !is_user_logged_in() ){
                    return false;
                }
                
                $today =  strtolower( gmdate( "D" ) );
                $dpad_select_day_of_week = get_post_meta( $dpad_id, 'dpad_select_day_of_week', true ) ? get_post_meta( $dpad_id, 'dpad_select_day_of_week', true ) : 
                array();
                if( !in_array($today, $dpad_select_day_of_week, true) && !empty($dpad_select_day_of_week) ) {
                    return false;
                }
            }
        }
        //First order for user End

        $is_passed = array();
        $cart_based_qty = 0;

        foreach ( $cart_array as  $woo_cart_item_for_qty ) {
            $cart_based_qty += $woo_cart_item_for_qty['quantity'];
        }

        $dpad_title          = get_the_title( $dpad_id );
        $title               = ! empty( $dpad_title ) ? __( $dpad_title, 'woo-conditional-discount-rules-for-checkout' ) : __( 'Fee', 'woo-conditional-discount-rules-for-checkout' );
        $getFeesCostOriginal = get_post_meta( $dpad_id, 'dpad_settings_product_cost', true );
        $getFeeType          = get_post_meta( $dpad_id, 'dpad_settings_select_dpad_type', true );

        if ( isset( $woocommerce_wpml ) && ! empty( $woocommerce_wpml->multi_currency ) ) {
            if ( isset( $getFeeType ) && ! empty( $getFeeType ) && $getFeeType === 'fixed' ) {
                $getFeesCost = $woocommerce_wpml->multi_currency->prices->convert_price_amount( $getFeesCostOriginal );
            } else {
                $getFeesCost = $getFeesCostOriginal;
            }
        } else {
            $getFeesCost = $getFeesCostOriginal;
        }
        
        $getFeesPerQtyFlag = '';
        $getFeesPerQty = '';
        $extraProductCost = 0;
        if ( wcdrfc_fs()->is__premium_only() ) {
            if ( wcdrfc_fs()->can_use_premium_code() ) {
                $getFeesPerQtyFlag        = get_post_meta( $dpad_id, 'dpad_chk_qty_price', true );
                $getFeesPerQty            = get_post_meta( $dpad_id, 'dpad_per_qty', true );
                $extraProductCostOriginal = get_post_meta( $dpad_id, 'extra_product_cost', true );

                if ( isset( $woocommerce_wpml ) && ! empty( $woocommerce_wpml->multi_currency ) ) {
                    $extraProductCost = $woocommerce_wpml->multi_currency->prices->convert_price_amount( $extraProductCostOriginal );
                } else {
                    $extraProductCost = $extraProductCostOriginal;
                }
            }
        }

        $get_condition_array 	= get_post_meta( $dpad_id, 'dynamic_pricing_metabox', true );
        $general_rule_match 	= 'all';
        if ( wcdrfc_fs()->is__premium_only() ) {
            if ( wcdrfc_fs()->can_use_premium_code() ) {
                $cost_rule_match = get_post_meta( $dpad_id, 'cost_rule_match', true );
                if ( ! empty( $cost_rule_match ) ) {
                    if ( is_serialized( $cost_rule_match ) ) {
                        $cost_rule_match = maybe_unserialize( $cost_rule_match );
                    } else {
                        $cost_rule_match = $cost_rule_match;
                    }
                    if ( array_key_exists( 'general_rule_match', $cost_rule_match ) ) {
                        $general_rule_match = $cost_rule_match['general_rule_match'];
                    } else {
                        $general_rule_match = 'all';
                    }
                    if ( array_key_exists( 'cost_on_product_rule_match', $cost_rule_match ) ) {
                        $cost_on_product_rule_match = $cost_rule_match['cost_on_product_rule_match'];
                    } else {
                        $cost_on_product_rule_match = 'any';
                    }
                    if ( array_key_exists( 'cost_on_product_subtotal_rule_match', $cost_rule_match ) ) {
                        $cost_on_product_subtotal_rule_match = $cost_rule_match['cost_on_product_subtotal_rule_match'];
                    } else {
                        $cost_on_product_subtotal_rule_match = 'any';
                    }
                    if ( array_key_exists( 'cost_on_product_weight_rule_match', $cost_rule_match ) ) {
                        $cost_on_product_weight_rule_match = $cost_rule_match['cost_on_product_weight_rule_match'];
                    } else {
                        $cost_on_product_weight_rule_match = 'any';
                    }
                    if ( array_key_exists( 'cost_on_category_rule_match', $cost_rule_match ) ) {
                        $cost_on_category_rule_match = $cost_rule_match['cost_on_category_rule_match'];
                    } else {
                        $cost_on_category_rule_match = 'any';
                    }
                    if ( array_key_exists( 'cost_on_category_subtotal_rule_match', $cost_rule_match ) ) {
                        $cost_on_category_subtotal_rule_match = $cost_rule_match['cost_on_category_subtotal_rule_match'];
                    } else {
                        $cost_on_category_subtotal_rule_match = 'any';
                    }
                    if ( array_key_exists( 'cost_on_category_weight_rule_match', $cost_rule_match ) ) {
                        $cost_on_category_weight_rule_match = $cost_rule_match['cost_on_category_weight_rule_match'];
                    } else {
                        $cost_on_category_weight_rule_match = 'any';
                    }
                    if ( array_key_exists( 'cost_on_total_cart_qty_rule_match', $cost_rule_match ) ) {
                        $cost_on_total_cart_qty_rule_match = $cost_rule_match['cost_on_total_cart_qty_rule_match'];
                    } else {
                        $cost_on_total_cart_qty_rule_match = 'any';
                    }
                    if ( array_key_exists( 'cost_on_total_cart_weight_rule_match', $cost_rule_match ) ) {
                        $cost_on_total_cart_weight_rule_match = $cost_rule_match['cost_on_total_cart_weight_rule_match'];
                    } else {
                        $cost_on_total_cart_weight_rule_match = 'any';
                    }
                    if ( array_key_exists( 'cost_on_total_cart_subtotal_rule_match', $cost_rule_match ) ) {
                        $cost_on_total_cart_subtotal_rule_match = $cost_rule_match['cost_on_total_cart_subtotal_rule_match'];
                    } else {
                        $cost_on_total_cart_subtotal_rule_match = 'any';
                    }
                    if ( array_key_exists( 'cost_on_shipping_class_subtotal_rule_match', $cost_rule_match ) ) {
                        $cost_on_shipping_class_subtotal_rule_match = $cost_rule_match['cost_on_shipping_class_subtotal_rule_match'];
                    } else {
                        $cost_on_shipping_class_subtotal_rule_match = 'any';
                    }
                } else {
                    $cost_on_product_rule_match                 = 'any';
                    $cost_on_product_subtotal_rule_match        = 'any';
                    $cost_on_product_weight_rule_match          = 'any';
                    $cost_on_category_rule_match                = 'any';
                    $cost_on_category_subtotal_rule_match       = 'any';
                    $cost_on_category_weight_rule_match         = 'any';
                    $cost_on_total_cart_qty_rule_match          = 'any';
                    $cost_on_total_cart_weight_rule_match       = 'any';
                    $cost_on_total_cart_subtotal_rule_match     = 'any';
                    $cost_on_shipping_class_subtotal_rule_match = 'any';
                }
            }
        }
        /* Percentage Logic Start */
        if ( isset( $getFeesCost ) && ! empty( $getFeesCost ) ) {

            if ( isset( $getFeeType ) && ! empty( $getFeeType ) && $getFeeType === 'percentage' ) {

                if ( $getFeesPerQtyFlag === 'on' ) {

                    $products_based_qty         = 0;
                    $products_based_subtotal    = 0;

                    $products_based_rule = $this->wdpad_product_qty_on_rules_ps( $dpad_id, $cart_array, $products_based_qty, $products_based_subtotal, $sitepress, $default_lang );
                    if ( ! empty( $products_based_rule ) ) {
                        if ( array_key_exists( '0', $products_based_rule ) ) {
                            $products_based_qty = $products_based_rule[0];
                        }
                        if ( array_key_exists( '1', $products_based_rule ) ) {
                            $products_based_subtotal = $products_based_rule[1];
                        }
                    }
                
                    $percentage_fee = ( $products_based_subtotal * $getFeesCost ) / 100;

                    if ( $getFeesPerQty === 'qty_cart_based' ) {
                        $dpad_cost = $percentage_fee + ( ( $cart_based_qty - 1 ) * $extraProductCost );
                    } else if ( $getFeesPerQty === 'qty_product_based' ) {
                        $dpad_cost = $percentage_fee + ( ( $products_based_qty - 1 ) * $extraProductCost );
                    }
                } else {
                    $dpad_cost = ( $cart_sub_total * $getFeesCost ) / 100;
                }
            } else {
                $fixed_fee = $getFeesCost;
                if ( $getFeesPerQtyFlag === 'on' ) {
                    if ( $getFeesPerQty === 'qty_cart_based' ) {
                        $dpad_cost = $fixed_fee + ( ( $cart_based_qty - 1 ) * $extraProductCost );
                    } else if ( $getFeesPerQty === 'qty_product_based' ) {
                        $dpad_cost = $fixed_fee + ( ( $products_based_qty - 1 ) * $extraProductCost );
                    }
                } else {
                    $dpad_cost = $fixed_fee;
                }
            }
        } else {
            $dpad_cost = 0;
        };

        $sale_product_check = get_post_meta( $dpad_id, 'dpad_sale_product', true );
        $wc_curr_version = $this->dpad_get_woo_version_number();
        if ( ! empty( $get_condition_array ) ) {
            $country_array              = array();
            $city_array                 = array();
            $state_array                = array();
            $postcode_array             = array();
            $zone_array                 = array();
            $product_array              = array();
            $variableproduct_array      = array();
            $category_array             = array();
            $tag_array                  = array();
            $product_qty_array     	    = array();
            $product_count_array   	    = array();
            $user_array                 = array();
            $user_role_array            = array();
            $user_mail_array            = array();
            $cart_total_array           = array();
            $cart_totalafter_array      = array();
            $total_spent_order_array    = array();
            $spent_order_count_array    = array();
            $last_spent_order_array     = array();
            $user_repeat_product_array  = array();
            $quantity_array             = array();
            $weight_array               = array();
            $coupon_array               = array();
            $shipping_class_array       = array();
            $payment_gateway            = array();
            $shipping_methods           = array();
            $shipping_total_array       = array();
            foreach ( $get_condition_array as $key => $value ) {
                if ( array_search( 'country', $value,true ) ) {
                    $country_array[ $key ] = $value;
                }
                if ( array_search( 'city', $value,true ) ) {
                    $city_array[ $key ] = $value;
                }
                if ( array_search( 'state', $value,true ) ) {
                    $state_array[ $key ] = $value;
                }
                if ( array_search( 'postcode', $value,true ) ) {
                    $postcode_array[ $key ] = $value;
                }
                if ( array_search( 'zone', $value,true ) ) {
                    $zone_array[ $key ] = $value;
                }
                if ( array_search( 'product', $value,true ) ) {
                    $product_array[ $key ] = $value;
                }
                if ( array_search( 'variableproduct', $value,true ) ) {
                    $variableproduct_array[ $key ] = $value;
                }
                if ( array_search( 'category', $value,true ) ) {
                    $category_array[ $key ] = $value;
                }
                if ( array_search( 'tag', $value,true ) ) {
                    $tag_array[ $key ] = $value;
                }
                if ( array_search( 'product_qty', $value, true ) ) {
                    $product_qty_array[ $key ] = $value;
                }
                if ( array_search( 'product_count', $value, true ) ) {
                    $product_count_array[ $key ] = $value;
                }
                if ( array_search( 'user', $value,true ) ) {
                    $user_array[ $key ] = $value;
                }
                if ( array_search( 'user_role', $value,true ) ) {
                    $user_role_array[ $key ] = $value;
                }
                if ( array_search( 'user_mail', $value,true ) ) {
                    $user_mail_array[ $key ] = $value;
                }
                if ( array_search( 'cart_total', $value,true ) ) {
                    $cart_total_array[ $key ] = $value;
                }
                if ( array_search( 'cart_totalafter', $value,true ) ) {
                    $cart_totalafter_array[ $key ] = $value;
                }
                if ( array_search( 'total_spent_order', $value,true ) ) {
                    $total_spent_order_array[ $key ] = $value;
                }
                if ( array_search( 'spent_order_count', $value,true ) ) {
                    $spent_order_count_array[ $key ] = $value;
                }
                if ( array_search( 'last_spent_order', $value,true ) ) {
                    $last_spent_order_array[ $key ] = $value;
                }
                if ( array_search( 'user_repeat_product', $value,true ) ) {
                    $user_repeat_product_array[ $key ] = $value;
                }
                if ( array_search( 'quantity', $value,true ) ) {
                    $quantity_array[ $key ] = $value;
                }
                if ( array_search( 'weight', $value,true ) ) {
                    $weight_array[ $key ] = $value;
                }
                if ( array_search( 'coupon', $value,true ) ) {
                    $coupon_array[ $key ] = $value;
                }
                if ( array_search( 'shipping_class', $value,true ) ) {
                    $shipping_class_array[ $key ] = $value;
                }
                if ( array_search( 'payment', $value,true ) ) {
                    $payment_gateway[ $key ] = $value;
                }
                if ( array_search( 'shipping_method', $value,true ) ) {
                    $shipping_methods[ $key ] = $value;
                }
                if ( array_search( 'shipping_total', $value,true ) ) {
                    $shipping_total_array[ $key ] = $value;
                }
            }

            /**
             * Location Specific Start
             */
                //Check if is country exist
                if ( is_array( $country_array ) && isset( $country_array ) && ! empty( $country_array ) ) {
                    $country_passed = $this->wdpad_match_country_rules( $country_array, $general_rule_match );
                    if ( 'yes' === $country_passed ) {
                        $is_passed['has_dpad_based_on_country'] = 'yes';
                    } else {
                        $is_passed['has_dpad_based_on_country'] = 'no';
                    }
                }

                if ( wcdrfc_fs()->is__premium_only() ) {
                    if ( wcdrfc_fs()->can_use_premium_code() ) {
                        //Check if is city exist (Premium)
                        if ( is_array( $city_array ) && isset( $city_array ) && ! empty( $city_array ) ) {
                            $city_passed = $this->wdpad_match_city_rules__premium_only( $city_array, $general_rule_match );
                            if ( 'yes' === $city_passed ) {
                                $is_passed['has_dpad_based_on_city'] = 'yes';
                            } else {
                                $is_passed['has_dpad_based_on_city'] = 'no';
                            }
                        }

                        //Check if is state exist (Premium)
                        if ( is_array( $state_array ) && isset( $state_array ) && ! empty( $state_array ) ) {                           
                            $state_passed = $this->wdpad_match_state_rules__premium_only( $state_array, $general_rule_match );
                            if ( 'yes' === $state_passed ) {
                                $is_passed['has_dpad_based_on_state'] = 'yes';
                            } else {
                                $is_passed['has_dpad_based_on_state'] = 'no';
                            }
                        }

                        //Check if is postcode exist (Premium)
                        if ( is_array( $postcode_array ) && isset( $postcode_array ) && ! empty( $postcode_array ) ) {
                            $postcode_passed = $this->wdpad_match_postcode_rules__premium_only( $postcode_array, $general_rule_match );
                            if ( 'yes' === $postcode_passed ) {
                                $is_passed['has_dpad_based_on_postcode'] = 'yes';
                            } else {
                                $is_passed['has_dpad_based_on_postcode'] = 'no';
                            }
                        }

                        //Check if is zone exist (Premium)
                        if ( is_array( $zone_array ) && isset( $zone_array ) && ! empty( $zone_array ) ) {
                            $zone_passed = $this->wdpad_match_zone_rules__premium_only( $zone_array, $general_rule_match );
                            if ( 'yes' === $zone_passed ) {
                                $is_passed['has_dpad_based_on_zone'] = 'yes';
                            } else {
                                $is_passed['has_dpad_based_on_zone'] = 'no';
                            }
                        }
                    }
                }
            /**
             * Location Specific End
             */


            /**
             *  Product Specific Start
             */
                //Check if is product exist
                if ( is_array( $product_array ) && isset( $product_array ) && ! empty( $product_array ) ) {
                    $product_passed = $this->wdpad_match_simple_products_rule( $cart_array, $product_array, $sale_product_check, $general_rule_match, $default_lang );
                    if ( 'yes' === $product_passed ) {
                        $is_passed['has_dpad_based_on_product'] = 'yes';
                    } else {
                        $is_passed['has_dpad_based_on_product'] = 'no';
                    }
                }

                if ( wcdrfc_fs()->is__premium_only() ) {
                    if ( wcdrfc_fs()->can_use_premium_code() ) {
                        //Check if is variable product exist
                        if ( is_array( $variableproduct_array ) && isset( $variableproduct_array ) && ! empty( $variableproduct_array ) ) {
                            $variable_prd_passed = $this->wdpad_match_variable_products_rule__premium_only( $cart_array, $variableproduct_array, $sale_product_check, $general_rule_match, $default_lang );
                            if ( 'yes' === $variable_prd_passed ) {
                                $is_passed['has_dpad_based_on_variable_product'] = 'yes';
                            } else {
                                $is_passed['has_dpad_based_on_variable_product'] = 'no';
                            }
                        }
                    }
                }

                //Check if is Category exist
                if ( is_array( $category_array ) && isset( $category_array ) && ! empty( $category_array ) ) {
                    $category_passed = $this->wdpad_match_category_rule( $cart_array, $category_array, $sale_product_check, $general_rule_match, $default_lang );
                    if ( 'yes' === $category_passed ) {
                        $is_passed['has_dpad_based_on_category'] = 'yes';
                    } else {
                        $is_passed['has_dpad_based_on_category'] = 'no';
                    }
                }

                if ( wcdrfc_fs()->is__premium_only() ) {
                    if ( wcdrfc_fs()->can_use_premium_code() ) {
                        //Check if is tag exist
                        if ( is_array( $tag_array ) && isset( $tag_array ) && ! empty( $tag_array ) ) {
                            $tag_passed = $this->wdpad_match_tag_rule__premium_only( $cart_array, $tag_array, $sale_product_check, $general_rule_match, $default_lang );
                            if ( 'yes' === $tag_passed ) {
                                $is_passed['has_dpad_based_on_tag'] = 'yes';
                            } else {
                                $is_passed['has_dpad_based_on_tag'] = 'no';
                            }
                        }

                        //Check if product quantity exist
                        if ( is_array( $product_qty_array ) && isset( $product_qty_array ) && ! empty( $product_qty_array ) ) {
                            $products_based_qty = $this->wdpad_product_qty_on_rules_ps( $dpad_id, $cart_array, 0, 0, $sitepress, $default_lang );
                            $product_qty_passed = $this->wdpad_match_product_based_qty_rule__premium_only( $products_based_qty, $product_qty_array, $general_rule_match);
                            if ( 'yes' === $product_qty_passed ) {
                                $is_passed['has_dpad_based_on_product_qty'] = 'yes';
                            } else {
                                $is_passed['has_dpad_based_on_product_qty'] = 'no';
                            }
                        }
                    }
                }

                //Check if product count exist
                if ( is_array( $product_count_array ) && isset( $product_count_array ) && ! empty( $product_count_array ) ) {
                    $quantity_total = 0;
                    $is_sub_passed = array();
                    
                    $quantity_total = $this->dpad_product_count_on_rules_ps( $dpad_id, $cart_array, 0, 0, $sitepress, $default_lang );
                    if( $quantity_total === 0 ){
                        $quantity_total = count($cart_array);
                    }

                    settype( $quantity_total, 'integer' );
                    $product_count_passed = $this->wdpad_match_product_count_rule( $quantity_total, $product_count_array, $general_rule_match );
                    if ( 'yes' === $product_count_passed ) {
                        $is_passed['has_dpad_based_on_product_count'] = 'yes';
                    } else {
                        $is_passed['has_dpad_based_on_product_count'] = 'no';
                    }
                }
            /**
             * Product Specific End
             */

            /**
             * User Specific Start
             */
                //Check if is user exist
                if ( is_array( $user_array ) && isset( $user_array ) && ! empty( $user_array ) && is_user_logged_in()) {
                    $user_passed = $this->wdpad_match_user_rule( $user_array, $general_rule_match );
                    if ( 'yes' === $user_passed ) {
                        $is_passed['has_dpad_based_on_user'] = 'yes';
                    } else {
                        $is_passed['has_dpad_based_on_user'] = 'no';
                    }
                }

                if ( wcdrfc_fs()->is__premium_only() ) {
                    if ( wcdrfc_fs()->can_use_premium_code() ) {
                        //Check if is user role exist (Premium)
                        if ( is_array( $user_role_array ) && !empty($user_role_array) && isset( $user_role_array ) && ! empty( $user_role_array )  ) {
                            $user_role_passed = $this->wdpad_match_user_role_rule__premium_only( $user_role_array, $general_rule_match );
                            if ( 'yes' === $user_role_passed ) {
                                $is_passed['has_dpad_based_on_user_role'] = 'yes';
                            } else {
                                $is_passed['has_dpad_based_on_user_role'] = 'no';
                            }
                        }

                        //Check if is user mail exist (Premium)
                        if ( is_array( $user_mail_array ) && !empty($user_mail_array) && isset( $user_mail_array ) ) {

                            $dpad_checkout_data = filter_input( INPUT_POST, 'post_data', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            if ( !is_array( $dpad_checkout_data ) ) {
                                parse_str( $dpad_checkout_data, $post_data );
                            } else {
                                $post_data = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                            }
                            $billing_email = isset($post_data['billing_email']) ? sanitize_email($post_data['billing_email']) : '';
                            if ( !empty($billing_email) ) {
                                $current_user_mail = $billing_email;
                            } else {
                                $current_user_mail = $current_user->user_email;
                            }

                            $user_mail_passed = $this->wdpad_match_user_mail_rule__premium_only( $current_user_mail, $user_mail_array, $general_rule_match );
                            if ( 'yes' === $user_mail_passed ) {
                                $is_passed['has_dpad_based_on_user_mail'] = 'yes';
                            } else {
                                $is_passed['has_dpad_based_on_user_mail'] = 'no';
                            }
                        }
                    }
                }
            /**
             * User Specific End
             */

            /**
             * Purchase History Start
             */
                if ( wcdrfc_fs()->is__premium_only() ) {
                    if ( wcdrfc_fs()->can_use_premium_code() ) {
                        //Check if is Total order spent exist
                        if ( is_array( $total_spent_order_array ) && isset( $total_spent_order_array ) && ! empty( $total_spent_order_array ) && is_user_logged_in() ) {
                            $total_spent_order_passed = $this->wdpad_match_total_spent_order_rule__premium_only( $total_spent_order_array, $general_rule_match );
                            if ( 'yes' === $total_spent_order_passed ) {
                                $is_passed['has_dpad_based_on_total_spent_order'] = 'yes';
                            } else {
                                $is_passed['has_dpad_based_on_total_spent_order'] = 'no';
                            }
                        }

                        //Check if is Total order count exist
                        if ( is_array( $spent_order_count_array ) && isset( $spent_order_count_array ) && ! empty( $spent_order_count_array ) && is_user_logged_in() ) {
                            $spent_order_count_passed = $this->wdpad_match_spent_order_count_rule__premium_only( $spent_order_count_array, $general_rule_match );
                            if ( 'yes' === $spent_order_count_passed ) {
                                $is_passed['has_dpad_based_on_spent_order_count'] = 'yes';
                            } else {
                                $is_passed['has_dpad_based_on_spent_order_count'] = 'no';
                            }
                        }

                        //Check if is Last order spent exist
                        if ( is_array( $last_spent_order_array ) && isset( $last_spent_order_array ) && ! empty( $last_spent_order_array ) && is_user_logged_in() ) {
                            $last_spent_order_passed = $this->wdpad_match_last_spent_order_rule__premium_only( $last_spent_order_array, $general_rule_match );
                            if ( 'yes' === $last_spent_order_passed ) {
                                $is_passed['has_dpad_based_on_last_spent_order'] = 'yes';
                            } else {
                                $is_passed['has_dpad_based_on_last_spent_order'] = 'no';
                            }
                        }

                        //Check if user purchase specific product again or not
                        if ( is_array( $user_repeat_product_array ) && isset( $user_repeat_product_array ) && ! empty( $user_repeat_product_array ) && is_user_logged_in() ) {
                            $user_repeat_product_passed = $this->wdpad_match_user_repeat_product_rule__premium_only( $cart_array, $user_repeat_product_array, $sale_product_check, $general_rule_match, $default_lang );
                            if ( 'yes' === $user_repeat_product_passed ) {
                                $is_passed['has_dpad_based_on_user_repeat_product'] = 'yes';
                            } else {
                                $is_passed['has_dpad_based_on_user_repeat_product'] = 'no';
                            }
                        }
                    }
                }
            /**
             * Purchase History End
             */

            /**
             * Cart Specific Start
             */
                //Check if is Cart Subtotal (Before Discount) exist
                if ( is_array( $cart_total_array ) && isset( $cart_total_array ) && ! empty( $cart_total_array ) ) {

                    $total = 0;
                    $product_ids_on_sale = wc_get_product_ids_on_sale();

                    if( "exclude" === $sale_product_check ){
                        foreach($cart_array as $value){
                            $product_id = $value['variation_id'] ? intval($value['variation_id']) : intval($value['product_id']);
                            if( !in_array( $product_id, $product_ids_on_sale, true ) ){
                                $total += $this->dpad_remove_currency( WC()->cart->get_product_subtotal( $value['data'], $value['quantity'] ) );	
                            }
                        }
                    } else {
                        if ( $wc_curr_version >= 3.0 ) {
                            $total = $this->dpad_remove_currency( $woocommerce->cart->get_cart_subtotal() );
                        } else {
                            $total = $woocommerce->cart->subtotal;
                        }	
                    }
                    if ( isset( $woocommerce_wpml ) && ! empty( $woocommerce_wpml->multi_currency ) ) {
                        $new_total = $woocommerce_wpml->multi_currency->prices->unconvert_price_amount( $total );
                    } else {
                        $new_total = $total;
                    }
                    
                    settype( $new_total, 'float' );

                    $cart_total_before_passed = $this->wdpad_match_cart_subtotal_before_discount_rule( $new_total, $cart_total_array, $general_rule_match);
                    
                    if ( 'yes' === $cart_total_before_passed ) {
                        $is_passed['has_dpad_based_on_cart_total'] = 'yes';
                    } else {
                        $is_passed['has_dpad_based_on_cart_total'] = 'no';
                    }
                }

                if ( wcdrfc_fs()->is__premium_only() ) {
                    if ( wcdrfc_fs()->can_use_premium_code() ) {
                        //Check if is Cart Subtotal (After Discount) exist (Premium)
                        if ( is_array( $cart_totalafter_array ) && isset( $cart_totalafter_array ) && ! empty( $cart_totalafter_array ) ) {
                            $totalprice = 0;
                            $product_ids_on_sale = wc_get_product_ids_on_sale();

                            if( "exclude" === $sale_product_check ){
                                foreach($cart_array as $value){
                                    $product_id = $value['variation_id'] ? intval($value['variation_id']) : intval($value['product_id']);
                                    if( !in_array( $product_id, $product_ids_on_sale, true ) ){
                                        $totalprice += $this->dpad_remove_currency( WC()->cart->get_product_subtotal( $value['data'], $value['quantity'] ) );	
                                    }
                                }
                            } else {
                                if ( $wc_curr_version >= 3.0 ) {
                                    $totalprice = $this->dpad_remove_currency( $woocommerce->cart->get_cart_subtotal() );
                                } else {
                                    $totalprice = $woocommerce->cart->subtotal;
                                }
                            }
                            $is_sub_passed = array();
                            $new_resultprice = 0;
                            $totaldisc   = $this->dpad_remove_currency( $woocommerce->cart->get_total_discount() );
                            if( '' !== $totaldisc && 0.0 !== $totaldisc ) {
                                $resultprice = (float) $totalprice - (float) $totaldisc;
                                if ( isset( $woocommerce_wpml ) && ! empty( $woocommerce_wpml->multi_currency ) ) {
                                    $new_resultprice = $woocommerce_wpml->multi_currency->prices->unconvert_price_amount( $resultprice );
                                } else {
                                    $new_resultprice = $resultprice;
                                }
                                settype( $new_resultprice, 'float' );
                            }
                            
                            $cart_total_after_passed = $this->wdpad_match_cart_subtotal_after_discount_rule__premium_only( $new_resultprice, $cart_totalafter_array, $general_rule_match );
                            if ( 'yes' === $cart_total_after_passed ) {
                                $is_passed['has_dpad_based_on_cart_totalafter'] = 'yes';
                            } else {
                                $is_passed['has_dpad_based_on_cart_totalafter'] = 'no';
                            }
                        }
                    }
                }
                
                //Check if is quantity exist
                if ( is_array( $quantity_array ) && isset( $quantity_array ) && ! empty( $quantity_array ) ) {
                    
                    $quantity_total 		= 0;
                    $product_ids_on_sale 	= wc_get_product_ids_on_sale();
                    $is_sub_passed 			= array();

                    foreach ( $cart_array as  $woo_cart_item ) {
                        $product_type = $woo_cart_item['data']->get_type();
                        $product_id = $woo_cart_item['variation_id'] ? intval($woo_cart_item['variation_id']) : intval($woo_cart_item['product_id']);
                        if( false === strpos( $product_type, 'bundle' ) ) {
                            if( "exclude" === $sale_product_check ){
                                if( !in_array( $product_id, $product_ids_on_sale, true ) ){
                                    $quantity_total += $woo_cart_item['quantity'];
                                }
                            } else {
                                $quantity_total += $woo_cart_item['quantity'];
                            }
                        } 
                    }
                    settype( $quantity_total, 'integer' );

                    $quantity_passed = $this->wdpad_match_cart_based_qty_rule( $quantity_total, $quantity_array, $general_rule_match );
                    
                    if ( 'yes' === $quantity_passed ) {
                        $is_passed['has_dpad_based_on_quantity'] = 'yes';
                    } else {
                        $is_passed['has_dpad_based_on_quantity'] = 'no';
                    }
                }

                if ( wcdrfc_fs()->is__premium_only() ) {
                    if ( wcdrfc_fs()->can_use_premium_code() ) {
                        //Check if is weight exist (Premium)
                        if ( is_array( $weight_array ) && isset( $weight_array ) && ! empty( $weight_array ) ) {
                            $weight_total           = 0;
                            $product_ids_on_sale 	= wc_get_product_ids_on_sale();
                            foreach ( $cart_array as $woo_cart_item ) {
                                $product_weight = $woo_cart_item['data']->get_weight();
                                $product_type = $woo_cart_item['data']->get_type();
                                $product_id = $woo_cart_item['variation_id'] ? intval($woo_cart_item['variation_id']) : intval($woo_cart_item['product_id']);
                                if ( $product_weight > 0 && false === strpos( $product_type, 'bundle' ) ) {
                                    $woo_cart_item_quantity = $woo_cart_item['quantity'];
                                    if( "exclude" === $sale_product_check ){
                                        if( !in_array( $product_id, $product_ids_on_sale, true ) ){
                                            $weight_total += floatval( $product_weight ) * intval( $woo_cart_item_quantity );
                                        }
                                    } else {
                                        $weight_total += floatval( $product_weight ) * intval( $woo_cart_item_quantity );
                                    }
                                }							
                            }
                            $is_sub_passed = array();
                            settype( $weight_total, 'float' );
                            
                            $weight_passed = $this->wdpad_match_cart_total_weight_rule__premium_only( $weight_total, $weight_array, $general_rule_match );
                            
                            if ( 'yes' === $weight_passed ) {
                                $is_passed['has_dpad_based_on_weight'] = 'yes';
                            } else {
                                $is_passed['has_dpad_based_on_weight'] = 'no';
                            }
                        }

                        //Check if is coupon exist (Premium)
                        if ( is_array( $coupon_array ) && isset( $coupon_array ) && ! empty( $coupon_array ) ) {
                            $coupon_passed = $this->wdpad_match_coupon_rule__premium_only( $wc_curr_version, $coupon_array, $general_rule_match );
                            if ( 'yes' === $coupon_passed ) {
                                $is_passed['has_dpad_based_on_coupon'] = 'yes';
                            } else {
                                $is_passed['has_dpad_based_on_coupon'] = 'no';
                            }
                        }

                        //Check if is shipping class exist (Premium)
                        if ( is_array( $shipping_class_array ) && isset( $shipping_class_array ) && ! empty( $shipping_class_array ) ) {
                            $shipping_class_passed = $this->wdpad_match_shipping_class_rule__premium_only( $sale_product_check, $shipping_class_array, $general_rule_match, $default_lang );
                            if ( 'yes' === $shipping_class_passed ) {
                                $is_passed['has_dpad_based_on_shipping_class'] = 'yes';
                            } else {
                                $is_passed['has_dpad_based_on_shipping_class'] = 'no';
                            }
                        }
                    }
                }
            /**
             * Cart Specific End
             */
            
            /**
             * Payment Specific Start
             */
                if ( wcdrfc_fs()->is__premium_only() ) {
                    if ( wcdrfc_fs()->can_use_premium_code() ) {
                        //Check if is payment gateway exist (Premium)
                        if ( is_array( $payment_gateway ) && isset( $payment_gateway ) && ! empty( $payment_gateway ) ) {
                            $payment_gateway_passed = $this->wdpad_match_payment_gateway_rule__premium_only( $wc_curr_version, $payment_gateway, $general_rule_match );
                            if ( 'yes' === $payment_gateway_passed ) {
                                $is_passed['has_dpad_based_on_payment'] = 'yes';
                            } else {
                                $is_passed['has_dpad_based_on_payment'] = 'no';
                            }
                        }
                    }
                }
            /**
             * Payment Specific End
             */

            /**
             * Shipping Specific Start
             */
                if ( wcdrfc_fs()->is__premium_only() ) {
                    if ( wcdrfc_fs()->can_use_premium_code() ) {
                        //Check if is shipping method exist (Premium)
                        if ( is_array( $shipping_methods ) && isset( $shipping_methods ) && ! empty( $shipping_methods ) ) {
                            $shipping_method_passed = $this->wdpad_match_shipping_method_rule__premium_only( $wc_curr_version, $shipping_methods, $general_rule_match );
                            if ( 'yes' === $shipping_method_passed ) {
                                $is_passed['has_dpad_based_on_shipping_method'] = 'yes';
                            } else {
                                $is_passed['has_dpad_based_on_shipping_method'] = 'no';
                            }
                        }
                        
                        //Check if is shipping total with confition
                        if ( is_array( $shipping_total_array ) && isset( $shipping_total_array ) && ! empty( $shipping_total_array ) ) {
                            $shipping_total_passed = $this->wdpad_match_shipping_method_total_rule__premium_only( $shipping_total_array, $general_rule_match );
                            if ( 'yes' === $shipping_total_passed ) {
                                $is_passed['has_dpad_based_on_shipping_total'] = 'yes';
                            } else {
                                $is_passed['has_dpad_based_on_shipping_total'] = 'no';
                            }
                        }
                    }
                }
            /**
             * Shipping Specific End
             */
            
            if ( isset( $is_passed ) && ! empty( $is_passed ) && is_array( $is_passed ) ) {
                $fnispassed = array();
                foreach ( $is_passed as $val ) {
                    if ( '' !== $val ) {
                        $fnispassed[] = $val;
                    }
                }
                if ( 'all' === $general_rule_match ) {
                    if ( in_array( 'no', $fnispassed, true ) ) {
                        $final_is_passed_general_rule['passed'] = 'no';
                    } else {
                        $final_is_passed_general_rule['passed'] = 'yes';
                    }
                } else {
                    if ( in_array( 'yes', $fnispassed, true ) ) {
                        $final_is_passed_general_rule['passed'] = 'yes';
                    } else {
                        $final_is_passed_general_rule['passed'] = 'no';
                    }
                }
            }
        }
        if ( wcdrfc_fs()->is__premium_only() ) {
            if ( wcdrfc_fs()->can_use_premium_code() ) {
                $ap_rule_status = get_post_meta( $dpad_id, 'ap_rule_status', true );
                /* Start Advance Pricing Rules */
                if ( 'on' === $ap_rule_status ) {
                    $cost_on_product_status                         = get_post_meta( $dpad_id, 'cost_on_product_status', true );
                    $cost_on_product_subtotal_status                = get_post_meta( $dpad_id, 'cost_on_product_subtotal_status', true );
                    $cost_on_product_weight_status                  = get_post_meta( $dpad_id, 'cost_on_product_weight_status', true );
                    $cost_on_category_status                        = get_post_meta( $dpad_id, 'cost_on_category_status', true );
                    $cost_on_category_subtotal_status               = get_post_meta( $dpad_id, 'cost_on_category_subtotal_status', true );
                    $cost_on_category_weight_status                 = get_post_meta( $dpad_id, 'cost_on_category_weight_status', true );
                    $cost_on_total_cart_qty_status                  = get_post_meta( $dpad_id, 'cost_on_total_cart_qty_status', true );
                    $cost_on_total_cart_weight_status               = get_post_meta( $dpad_id, 'cost_on_total_cart_weight_status', true );
                    $cost_on_total_cart_subtotal_status             = get_post_meta( $dpad_id, 'cost_on_total_cart_subtotal_status', true );
                    $cost_on_shipping_class_subtotal_status         = get_post_meta( $dpad_id, 'cost_on_shipping_class_subtotal_status', true );
                    
                    $get_condition_array_ap_product                 = get_post_meta( $dpad_id, 'sm_metabox_ap_product', true );
                    $get_condition_array_ap_product_subtotal        = get_post_meta( $dpad_id, 'sm_metabox_ap_product_subtotal', true );
                    $get_condition_array_ap_product_weight          = get_post_meta( $dpad_id, 'sm_metabox_ap_product_weight', true );
                    
                    $get_condition_array_ap_category                = get_post_meta( $dpad_id, 'sm_metabox_ap_category', true );
                    $get_condition_array_ap_category_subtotal       = get_post_meta( $dpad_id, 'sm_metabox_ap_category_subtotal', true );
                    $get_condition_array_ap_category_weight         = get_post_meta( $dpad_id, 'sm_metabox_ap_category_weight', true );

                    $get_condition_array_ap_total_cart_qty          = get_post_meta( $dpad_id, 'sm_metabox_ap_total_cart_qty', true );
                    $get_condition_array_ap_total_cart_weight       = get_post_meta( $dpad_id, 'sm_metabox_ap_total_cart_weight', true );
                    $get_condition_array_ap_total_cart_subtotal     = get_post_meta( $dpad_id, 'sm_metabox_ap_total_cart_subtotal', true );

                    $get_condition_array_ap_shipping_class_subtotal = get_post_meta( $dpad_id, 'sm_metabox_ap_shipping_class_subtotal', true );
                    
                    $match_advance_rule                             = array();
                    if ( 'on' === $cost_on_product_status ) {
                        $match_advance_rule['hfbopq'] = $this->wdpad_match_product_per_qty__premium_only( $get_condition_array_ap_product, $cart_array, $sitepress, $default_lang, $cost_on_product_rule_match );
                    }				
                    if ( 'on' === $cost_on_product_subtotal_status ) {
                        $match_advance_rule['hfbops'] = $this->wdpad_match_product_subtotal__premium_only( $get_condition_array_ap_product_subtotal, $cart_array, $sitepress, $default_lang, $cost_on_product_subtotal_rule_match );
                    }			
                    if ( 'on' === $cost_on_product_weight_status ) {
                        $match_advance_rule['hfbopw'] = $this->wdpad_match_product_per_weight__premium_only( $get_condition_array_ap_product_weight, $cart_array, $sitepress, $default_lang, $cost_on_product_weight_rule_match );
                    }
                    if ( 'on' === $cost_on_category_status ) {
                        $match_advance_rule['hfbocs'] = $this->wdpad_match_category_per_qty__premium_only( $get_condition_array_ap_category, $cart_array, $sitepress, $default_lang, $cost_on_category_rule_match );
                    }
                    if ( 'on' === $cost_on_category_subtotal_status ) {
                        $match_advance_rule['hfbocs'] = $this->wdpad_match_category_subtotal__premium_only( $get_condition_array_ap_category_subtotal, $cart_array, $sitepress, $default_lang, $cost_on_category_subtotal_rule_match );
                    }
                    if ( 'on' === $cost_on_category_weight_status ) {
                        $match_advance_rule['hfbocw'] = $this->wdpad_match_category_per_weight__premium_only( $get_condition_array_ap_category_weight, $cart_array, $sitepress, $default_lang, $cost_on_category_weight_rule_match );
                    }
                    if ( 'on' === $cost_on_total_cart_qty_status ) {
                        $match_advance_rule['hfbotcq'] = $this->wdpad_match_total_cart_qty__premium_only( $get_condition_array_ap_total_cart_qty, $cart_array, $cost_on_total_cart_qty_rule_match );
                    }
                    if ( 'on' === $cost_on_total_cart_weight_status ) {
                        $match_advance_rule['hfbotcw'] = $this->wdpad_match_total_cart_weight__premium_only( $get_condition_array_ap_total_cart_weight, $cart_array, $cost_on_total_cart_weight_rule_match );
                    }
                    if ( 'on' === $cost_on_total_cart_subtotal_status ) {
                        $match_advance_rule['hfbotcs'] = $this->wdpad_match_total_cart_subtotal__premium_only( $get_condition_array_ap_total_cart_subtotal, $cart_array, $cost_on_total_cart_subtotal_rule_match );
                    }
                    if ( 'on' === $cost_on_shipping_class_subtotal_status ) {
                        $match_advance_rule['hfbscs'] = $this->wdpad_match_shipping_class_subtotal__premium_only( $get_condition_array_ap_shipping_class_subtotal, $cart_array, $sitepress, $default_lang, $cost_on_shipping_class_subtotal_rule_match );
                    }
                    
                    $advance_pricing_rule_cost = 0;
                    if ( isset( $match_advance_rule ) && ! empty( $match_advance_rule ) && is_array( $match_advance_rule ) ) {
                        foreach ( $match_advance_rule as $val ) {
                            if ( !empty($val) && '' !== $val['flag'] && 'yes' === $val['flag'] ) {
                                $advance_pricing_rule_cost += $val['total_amount'];
                            }
                        }
                    }
                    $advance_pricing_rule_cost = $this->wdpad_price_format( $advance_pricing_rule_cost );
                    $dpad_cost                 += $advance_pricing_rule_cost;
                }
            }
        }
        
        if ( empty( $final_is_passed_general_rule ) || '' === $final_is_passed_general_rule || null === $final_is_passed_general_rule ) {
            $new_is_passed['passed'] = 'no';
        } else if ( ! empty( $final_is_passed_general_rule ) && in_array( 'no', $final_is_passed_general_rule, true ) ) {
            $new_is_passed['passed'] = 'no';
        } else if ( empty( $final_is_passed_general_rule ) && in_array( '', $final_is_passed_general_rule, true ) ) {
            $new_is_passed['passed'] = 'no';
        } else if ( ! empty( $final_is_passed_general_rule ) && in_array( 'yes', $final_is_passed_general_rule, true ) ) {
            $new_is_passed['passed'] = 'yes';
        }
        if ( in_array( 'no', $new_is_passed, true ) ) {
            $final_passed['passed'] = 'no';
        } else {
            $final_passed['passed'] = 'yes';
        }
        
        if ( isset( $final_passed ) && ! empty( $final_passed ) && is_array( $final_passed ) ) {
            if ( ! in_array( 'no', $final_passed, true ) ) {
                if( $this->wdpad_check_date_and_time_condition($dpad_id) ){
                    //For Fixed and Percentage discount type
                    if( isset( $getFeeType ) && ! empty( $getFeeType ) && ( 'fixed' === $getFeeType || 'percentage' === $getFeeType ) ) {
                        $woocommerce->cart->add_fee( $title, ( -1 * $dpad_cost ), true, ''); //'Reduced rate',
                    }
                    return true;
                }
            }
        }
  
        return false;
    }

    public function wdpad_set_product_on_sale__premium_only( $on_sale, $product ) {

        //Not conflict with admin side JS
        if ( is_admin() || is_null($product) ){
            return $on_sale;
        }
        remove_filter('woocommerce_product_is_on_sale', array($this, 'wdpad_set_product_on_sale__premium_only'), 10);
        $all_discount_ids = $this->wdpad_action_on_discount_list();
        if( !empty($all_discount_ids) ){ 
            foreach($all_discount_ids as $dpad_id ){
                $getFeeStatus = get_post_meta( $dpad_id, 'dpad_settings_status', true );
                if( !empty($getFeeStatus) && 'on' === $getFeeStatus ){
                    $getFeeType = get_post_meta( $dpad_id, 'dpad_settings_select_dpad_type', true );
                    if( ! empty( $getFeeType ) && $getFeeType === 'adjustment' ) {
                        if( $this->wdpad_check_discount_condition( $dpad_id ) ) {
                            //For Adjustment discount type
                            $getAdjustmentType = get_post_meta( $dpad_id, 'dpad_settings_adjustment_type', true );
                            if( 'product' === $getAdjustmentType ){
                                $getGetProduct = intval(get_post_meta( $dpad_id, 'dpad_settings_get_product', true ));
                                if( $getGetProduct === $product->get_id() || ( 'variable' === $product->get_type() && in_array( $getGetProduct, $product->get_children(), true ) ) ) {    
                                    $on_sale = true;
                                }
                            } else if( 'category' === $getAdjustmentType ) {
                                $getGetCategory = intval(get_post_meta( $dpad_id, 'dpad_settings_get_category', true ));
                                $catProducts = $this->wdpad_get_products_by_cat_ids__premium_only($getGetCategory);
                                if( in_array( $product->get_id(), $catProducts, true ) || in_array( $product->get_parent_id(), $catProducts, true ) ) {    
                                    $on_sale = true;
                                }
                            }
                        }
                    }
                }
            }
        }
        add_filter('woocommerce_product_is_on_sale', array($this, 'wdpad_set_product_on_sale__premium_only'), 10, 2);
        return $on_sale;
    }

    public function wdpad_format_sale_price_only__premium_only( $price, $product ){

        //Not conflict with admin side JS
        if ( is_admin() ){
            return $price;
        }

        remove_filter( 'woocommerce_product_get_price', array($this, 'wdpad_format_sale_price_only__premium_only'), 10);
        remove_filter( 'woocommerce_product_variation_get_price',array($this, 'wdpad_format_sale_price_only__premium_only'), 10 );
        remove_filter( 'woocommerce_variation_prices_price',array($this, 'wdpad_format_sale_price_only__premium_only'), 10);

        $return_price_arr = array();
        
        $apply_discount = get_option('wdpad_gs_adjustment_discount_type') ? get_option('wdpad_gs_adjustment_discount_type') : 'first';

        $sequential_discount = get_option('wdpad_gs_sequential_discount');
        $apply_discount_subsequently = !empty($sequential_discount) && 'no' === $sequential_discount ? false : true;

        settype( $price, 'float' );
        //For fragment chnage jQuery(document.body).trigger('wc_fragment_refresh'); use with our condition (fully update on cart page only)
        $all_discount_ids = $this->wdpad_action_on_discount_list();

        if( !empty($all_discount_ids) ){ 
            foreach($all_discount_ids as $dpad_id ){
                $getFeeStatus = get_post_meta( $dpad_id, 'dpad_settings_status', true );
                if( !empty($getFeeStatus) && 'on' === $getFeeStatus ){  
                    $getFeeType = get_post_meta( $dpad_id, 'dpad_settings_select_dpad_type', true );
                    if( ! empty( $getFeeType ) && $getFeeType === 'adjustment' ) {
                        if( $this->wdpad_check_discount_condition( $dpad_id ) ) {
                            $product_actual_price = 0;
                            $calculate_product_price = false;
                            //For Adjustment discount type
                            $getAdjustmentType = get_post_meta( $dpad_id, 'dpad_settings_adjustment_type', true );
                            $getAdjustmentCost = floatval(get_post_meta( $dpad_id, 'dpad_settings_adjustment_cost', true ));

                            if( 'product' === $getAdjustmentType ){
                                $getGetProduct = intval(get_post_meta( $dpad_id, 'dpad_settings_get_product', true ));
                                if( $getGetProduct === $product->get_id() || ( 'variation' === $product->get_type() && in_array( $getGetProduct, $product->get_children(), true ) ) ) {                                    
                                    $calculate_product_price = true;
                                }
                            } else if( 'category' === $getAdjustmentType ) {
                                $getGetCategory = intval(get_post_meta( $dpad_id, 'dpad_settings_get_category', true ));
                                $catProducts = $this->wdpad_get_products_by_cat_ids__premium_only($getGetCategory);
                                if( in_array( $product->get_id(), $catProducts, true ) || ( 'variation' === $product->get_type() && in_array( $product->get_parent_id(), $catProducts, true ) ) ) {    
                                    $calculate_product_price = true;
                                }
                            }
                            if( $calculate_product_price ){
                                // phpcs:disable
                                // $price = $product->get_regular_price(); // we can use this line for applying adjustment on sale or regular price
                                // phpcs:enable
                                $product_actual_price = floatval( $price * ( $getAdjustmentCost / 100 ) );  
                                if( 'first' === $apply_discount ) {
        
                                    $price = floatval( $price - $product_actual_price);

                                    add_filter( 'woocommerce_product_get_price', array($this, 'wdpad_format_sale_price_only__premium_only'), 10, 2);
                                    add_filter( 'woocommerce_product_variation_get_price', array($this, 'wdpad_format_sale_price_only__premium_only'), 10, 2 );
                                    add_filter( 'woocommerce_variation_prices_price', array($this, 'wdpad_format_sale_price_only__premium_only'), 10, 2 );
                                    
                                    return $price;

                                } else if($apply_discount === 'all'){
                                    if( $apply_discount_subsequently ){
                                        $price -= $product_actual_price;
                                        $return_price_arr[$product->get_id()][]=$product_actual_price;
                                    } else {
                                        $return_price_arr[$product->get_id()][]=$product_actual_price;
                                    }
                                } else {
                                    $return_price_arr[$product->get_id()][]=$product_actual_price;
                                }
                            }
                        }
                    }
                }
            }
        }
        if( !empty($return_price_arr) && isset($return_price_arr[$product->get_id()]) && !empty($return_price_arr[$product->get_id()]) ){
            if( $apply_discount === 'biggest_discount' ){
                $price -= max( $return_price_arr[$product->get_id()] );
            } else if( $apply_discount === 'lowest_discount' ) {
                $price -= min( $return_price_arr[$product->get_id()] );
            } else if( !$apply_discount_subsequently ){
                $price -= array_sum( $return_price_arr[$product->get_id()] );
                $price = max($price, 0);
            }
        }

        add_filter( 'woocommerce_product_get_price', array($this, 'wdpad_format_sale_price_only__premium_only'), 10, 2);
        add_filter( 'woocommerce_product_variation_get_price', array($this, 'wdpad_format_sale_price_only__premium_only'), 10, 2 );
        add_filter( 'woocommerce_variation_prices_price', array($this, 'wdpad_format_sale_price_only__premium_only'), 10, 2 );

        return $price;
    }

    /**
     * This will automatically refresh transient for variable product price range updation
     */
    public function wdpad_get_variation_prices_hash__premium_only( $price_hash, $product, $for_display ){
        $price_hash['wdpad_discount'] = time(); //for dynamic change in product price range
        return $price_hash;
    }

    function wdpad_change_product_price_cart__premium_only( $price, $cart_item, $cart_item_key ) {

        //Not conflict with admin side JS
        if ( is_admin() ){
            return false;
        }

        $all_discount_ids = $this->wdpad_action_on_discount_list();
        if( !empty($all_discount_ids) ){ 
            foreach($all_discount_ids as $dpad_id ){
                $getFeeStatus = get_post_meta( $dpad_id, 'dpad_settings_status', true );
                if( !empty($getFeeStatus) && 'on' === $getFeeStatus ){
                    $getFeeType = get_post_meta( $dpad_id, 'dpad_settings_select_dpad_type', true );
                    if( $this->wdpad_check_discount_condition( $dpad_id ) ) {
                        if( ! empty( $getFeeType ) && $getFeeType === 'adjustment' && ! array_key_exists( 'dpad_get_discount_product', $cart_item ) ) {
                            //For Adjustment discount type
                            $getAdjustmentType = get_post_meta( $dpad_id, 'dpad_settings_adjustment_type', true );
                            if( 'product' === $getAdjustmentType ){
                                $getGetProduct = intval(get_post_meta( $dpad_id, 'dpad_settings_get_product', true ));
                                if( $getGetProduct === $cart_item['data']->get_id()) { 
                                    $price = wc_format_sale_price( wc_get_price_to_display( $cart_item['data'], array( 'price' => $cart_item['data']->get_regular_price() ) ), wc_get_price_to_display( $cart_item['data'] ) ) . $cart_item['data']->get_price_suffix();
                                }
                            } else if( 'category' === $getAdjustmentType ) {
                                $getGetCategory = intval(get_post_meta( $dpad_id, 'dpad_settings_get_category', true ));
                                $catProducts = $this->wdpad_get_products_by_cat_ids__premium_only($getGetCategory);
                                if( in_array( $cart_item['data']->get_id(), $catProducts, true ) || ( 'variation' === $cart_item['data']->get_type() && in_array( $cart_item['data']->get_parent_id(), $catProducts, true ) ) ) {    
                                    $price = wc_format_sale_price( wc_get_price_to_display( $cart_item['data'], array( 'price' => $cart_item['data']->get_regular_price() ) ), wc_get_price_to_display( $cart_item['data'] ) ) . $cart_item['data']->get_price_suffix();
                                }
                            }
                        } else if ( ! empty( $getFeeType ) && $getFeeType === 'bogo' ) {
                            //For BOGO discount type
                            if( array_key_exists( 'dpad_get_discount_product', $cart_item ) ) {
                                $price = wc_price( 0 ) . $cart_item['data']->get_price_suffix();
                            }
                        }
                    }
                }
            }
        }
        
        return $price;
    }

    /**
     * This function will load everytime to check chages in discount offer and act accordingly
     */
    public function wdpad_add_to_cart_action__premium_only( $url = false ){
       
        if ( is_admin() ){
            return false;
        }

        //Remove all BOGO products so we can add new/refresh rule product in cart
        $this->wdpad_reset_BOGO_products__premium_only();

        $all_discount_ids = $this->wdpad_action_on_discount_list();
        
        if( !empty($all_discount_ids) ){ 
            foreach($all_discount_ids as $dpad_id ){
                $getFeeStatus = get_post_meta( $dpad_id, 'dpad_settings_status', true );
                if( !empty($getFeeStatus) && 'on' === $getFeeStatus ){
                    $getFeeType = get_post_meta( $dpad_id, 'dpad_settings_select_dpad_type', true );
                    if( ! empty( $getFeeType ) && $getFeeType === 'bogo' ) {
                        if( $this->wdpad_check_discount_condition( $dpad_id ) ) {
                            $getBOGORuleset = get_post_meta( $dpad_id, 'sm_metabox_bogo_ruleset', true );
                            if( !empty($getBOGORuleset) ){
                                foreach($getBOGORuleset as $rule ){
                                    $total_buy_qty = 0;
                                    $getBuyProducts = isset($rule['bogo_buy_products']) ? array_map( 'intval', $rule['bogo_buy_products'] ) : array();
                                    if( !empty($getBuyProducts) ){
                                        foreach( $getBuyProducts as $getBuyProduct ){
                                            $in_cart = $this->wdpad_product_variation_exist_in_cart__premium_only($getBuyProduct);
                                            $product_cart_obj = WC()->cart->get_cart_item( $in_cart );
                                            if( !empty($product_cart_obj) && !isset($product_cart_obj['dpad_get_discount_product']) ){
                                                $total_buy_qty += $product_cart_obj['quantity'];
                                            }
                                        }

                                        $min_buy_qty = !empty( $rule['bogo_buy_products_min_qty'] ) ? absint( $rule['bogo_buy_products_min_qty'] ) : 0;
                                        $max_buy_qty = !empty( $rule['bogo_buy_products_max_qty'] ) ? absint( $rule['bogo_buy_products_max_qty'] ) : 0;
                                        $getGetProducts = !empty( $rule['bogo_get_products'] ) ? array_map( 'absint', $rule['bogo_get_products'] ) : 0;

                                        //Remove all free products
                                        foreach( $getGetProducts as $getGetProduct ) {
                                            $variation_id = 0;
                                            $variation = array();
                                            if ( 'product_variation' === get_post_type( $getGetProduct ) ) {
                                                $variation_id   = $getGetProduct;
                                                $getGetProduct  = wp_get_post_parent_id( $variation_id );
                                                $variation = $this->wdpad_get_variation_array_by_variation_id__premium_only( $variation_id );
                                            }
                                            $product_cart_id = WC()->cart->generate_cart_id( $getGetProduct, $variation_id, $variation, array( 'dpad_get_discount_product' => md5(implode('_',$getBuyProducts)) ) );

                                            $cart_item_key = WC()->cart->find_product_in_cart( $product_cart_id );
                                            if ( $cart_item_key ) {
                                                WC()->cart->remove_cart_item( $cart_item_key );
                                            }
                                        }
                                        
                                        if( $total_buy_qty >= $min_buy_qty && $total_buy_qty <= $max_buy_qty ){
                                            
                                            $get_free_qty = !empty( $rule['bogo_get_products_free_qty'] ) ? absint( $rule['bogo_get_products_free_qty'] ) : 0;
                                            foreach( $getGetProducts as $getGetProduct ) {
                                                $variation_id = 0;
                                                if ( 'product_variation' === get_post_type( $getGetProduct ) ) {
                                                    $variation_id   = $getGetProduct;
                                                    $getGetProduct  = wp_get_post_parent_id( $variation_id );
                                                }

                                                //We need extra custom meta which can diffrentiate normal add to cart and our auto add to cart, this below md5 with buyproduct id with _ imploded can not diffrentiate it.
                                                WC()->cart->add_to_cart( $getGetProduct, $get_free_qty, $variation_id, array(), array( 'dpad_get_discount_product' => md5(implode('_',$getBuyProducts)) ) );
                                            }
                                        } 
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Add custom meta data to product in cart
     */
    function wdpad_store_id__premium_only( $data, $product_id, $variation_id ) {

        $productId = $variation_id > 0 ? $variation_id : $product_id;

        $all_discount_ids = $this->wdpad_action_on_discount_list();
        if( !empty($all_discount_ids) ){ 
            foreach($all_discount_ids as $dpad_id ){
                $getFeeStatus = get_post_meta( $dpad_id, 'dpad_settings_status', true );
                if( !empty($getFeeStatus) && 'on' === $getFeeStatus ){
                    $getFeeType = get_post_meta( $dpad_id, 'dpad_settings_select_dpad_type', true );
                    if( ! empty( $getFeeType ) && $getFeeType === 'bogo' ) {
                        if( $this->wdpad_check_discount_condition( $dpad_id ) ) {
                            $getBOGORuleset = get_post_meta( $dpad_id, 'sm_metabox_bogo_ruleset', true );
                            if( !empty($getBOGORuleset) ){
                                foreach($getBOGORuleset as $rule ){
                                    $total_buy_qty = 0;
                                    $getBuyProducts = isset($rule['bogo_buy_products']) ? array_map( 'intval', $rule['bogo_buy_products'] ) : array();
                                    if( !empty($getBuyProducts) ){
                                        foreach( $getBuyProducts as $getBuyProduct){
                                            $in_cart = $this->wdpad_product_variation_exist_in_cart__premium_only($getBuyProduct);
                                            $product_cart_obj = WC()->cart->get_cart_item( $in_cart );
                                            if( !empty($product_cart_obj) && !isset($product_cart_obj['dpad_get_discount_product']) ){
                                                $total_buy_qty += $product_cart_obj['quantity'];
                                            }
                                        }
                                    }
                                    $min_buy_qty = !empty( $rule['bogo_buy_products_min_qty'] ) ? absint( $rule['bogo_buy_products_min_qty'] ) : 0;
                                    $max_buy_qty = !empty( $rule['bogo_buy_products_max_qty'] ) ? absint( $rule['bogo_buy_products_max_qty'] ) : 0;
                                    $getGetProducts = !empty( $rule['bogo_get_products'] ) ? array_map( 'absint', $rule['bogo_get_products'] ) : 0;
                                    
                                    if( $total_buy_qty >= $min_buy_qty && $total_buy_qty <= $max_buy_qty && in_array( $productId, $getGetProducts, true ) && array_key_exists( 'dpad_get_discount_product', $data) ){

                                        //If we want to show seperate qty of free product then use below code.
                                        $check_buy_product = explode("_", $data['dpad_get_discount_product']);
                                        if( $check_buy_product === $getBuyProducts ){   
                                            $data['dpad_get_discount_product'] = md5(implode('_',$getBuyProducts));
                                        }

                                        // phpcs:disable
                                        // //If we want to show combine qty of free product then use below line only
                                        // $data['dpad_get_discount_product'] = md5(implode('_',$getBuyProducts));
                                        // phpcs:enable
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $data;
    }

    /**
     * We can you data from posted data(while add to cart with custom meta data) and add to cart item.
     */
    function wdpad_cart_item_from_session__premium_only( $data, $values ) {

        $product_id = $data['variation_id'] > 0 ? $data['variation_id'] : $data['product_id'];

        $all_discount_ids = $this->wdpad_action_on_discount_list();
        if( !empty($all_discount_ids) ){ 
            foreach($all_discount_ids as $dpad_id ){
                $getFeeStatus = get_post_meta( $dpad_id, 'dpad_settings_status', true );
                if( !empty($getFeeStatus) && 'on' === $getFeeStatus ){
                    $getFeeType = get_post_meta( $dpad_id, 'dpad_settings_select_dpad_type', true );
                    if( ! empty( $getFeeType ) && $getFeeType === 'bogo' ) {
                        if( $this->wdpad_check_discount_condition( $dpad_id ) ) {
                            $getBOGORuleset = get_post_meta( $dpad_id, 'sm_metabox_bogo_ruleset', true );
                            if( !empty($getBOGORuleset) ){
                                foreach($getBOGORuleset as $rule ){
                                    $total_buy_qty = 0;
                                    $getBuyProducts = isset($rule['bogo_buy_products']) ? array_map( 'intval', $rule['bogo_buy_products'] ) : array();
                                    if( !empty($getBuyProducts) ){
                                        foreach( $getBuyProducts as $getBuyProduct ){
                                            $in_cart = $this->wdpad_product_variation_exist_in_cart__premium_only($getBuyProduct);
                                            $product_cart_obj = WC()->cart->get_cart_item( $in_cart );
                                            if( !empty($product_cart_obj) && !isset($product_cart_obj['dpad_get_discount_product']) ){
                                                $total_buy_qty += $product_cart_obj['quantity'];
                                            }
                                        }
                                    }
                                    $min_buy_qty = !empty( $rule['bogo_buy_products_min_qty'] ) ? absint( $rule['bogo_buy_products_min_qty'] ) : 0;
                                    $max_buy_qty = !empty( $rule['bogo_buy_products_max_qty'] ) ? absint( $rule['bogo_buy_products_max_qty'] ) : 0;
                                    $getGetProducts = !empty( $rule['bogo_get_products'] ) ? array_map( 'absint', $rule['bogo_get_products'] ) : 0;

                                    if( $total_buy_qty >= $min_buy_qty && $total_buy_qty <= $max_buy_qty && in_array( $product_id, $getGetProducts, true ) && array_key_exists( 'dpad_get_discount_product', $data) ){
                                        
                                        //If we want to show seperate qty of free product then use below code.
                                        $check_buy_product = explode("_", $data['dpad_get_discount_product']);
                                        if( $check_buy_product === $getBuyProducts ){   
                                            $data['dpad_get_discount_product'] = md5(implode('_',$getBuyProducts));
                                        }

                                        // phpcs:disable
                                        // //If we want to show combine qty of free product then use below line only
                                        // $data['dpad_get_discount_product'] = md5(implode('_',$getBuyProducts));
                                        // phpcs:enable
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $data;
    }

    /**
     * This function is use to add Free before product name in cart, so it will use after free product auto add to cart implementation 
     */
    public function wdpad_alter_item_name__premium_only( $product_name, $cart_item, $cart_item_key ){
        if( array_key_exists( 'dpad_get_discount_product', $cart_item ) ) {
            $product_name = wp_kses_post( sprintf( '<span class="free-product">%s</span> - %s', esc_html__( 'Free', 'woo-conditional-discount-rules-for-checkout' ), $product_name ) );
        }
        return $product_name;
    }

    /**
     * This function is use to remove product remove button, so it will use after free product auto add to cart implementation
     */
    public function wdpad_disabled_cart_free_item_remove_link__premium_only( $button_link, $cart_item_key ){
    
        // Get the current cart item
        $cart_item = WC()->cart->get_cart()[$cart_item_key];
    
        // If the targeted product is in cart we remove the button link
        if( array_key_exists( 'dpad_get_discount_product', $cart_item ) ) {
            $button_link = '';
        }
    
        return $button_link;
    }

    /**
     * This function is use to discplay quantity so it will use after free product auto add to cart implementation
     */
    public function wdpad_cart_item_quantity__premium_only( $product_quantity, $cart_item_key, $cart_item ){
        if( array_key_exists( 'dpad_get_discount_product', $cart_item ) ) {
            $product_quantity = sprintf( '%2$s <input type="hidden" name="cart[%1$s][qty]" value="%2$s" />', $cart_item_key, $cart_item['quantity'] );
        }
        return $product_quantity;
    }

    /**
     * Add discount data to order item meta 
     */
    public function wdpad_add_values_to_order_item_meta__premium_only( $item_id, $values ){
        if( isset($values['dpad_get_discount_product']) ){
            //For hiding this meta data from the customer, we have started it with an underscore
            wc_add_order_item_meta( $item_id, '_dpad_get_discount_product', $values['dpad_get_discount_product'] );
        }
    }

    /**
     * This function is use to add "Free" label before product name after order placed and we can see it on order summary page.
     */
    public function wdpad_order_item_name__premium_only( $product_name, $item, $is_visible ){

        $item_metas = $item->get_formatted_meta_data( '', true );
        if( !empty( $item_metas ) ){
            foreach( $item_metas as $item_meta ){
                if( '_dpad_get_discount_product' === $item_meta->key ) {
                    $product_name = wp_kses_post( sprintf( '<span class="free-product">%s</span> - %s', esc_html__( 'Free', 'woo-conditional-discount-rules-for-checkout' ), $product_name ) );
                }
            }
        }
        return $product_name;
    }

    /** 
     * Append CSS to all the email for BOGO tag
     */
    public function wdpad_append_css_to_emails__premium_only( $style_return , $email_obj ){
        $style_return .= '
            span.free-product {
                background: dodgerblue;
                color: #fff;
                padding: 0px 5px 2px;
                border-radius: 5px;
            }
        ';
        return $style_return;
    }

    // phpcs:disable
    //We will use this later when we implement free product auto add into cart for changes free product quantity.
    // public function wdpad_set_step_for_specific_variable_products( $args, $product ){
    //     global $sitepress;

    //     $dpad_args = array(
	// 		'post_type'      	=> 'wc_dynamic_pricing',
	// 		'post_status'    	=> 'publish',
	// 		'orderby'       	=> 'menu_order',
	// 		'order'          	=> 'ASC',
	// 		'posts_per_page' 	=> - 1,
	// 		'suppress_filters'	=> false,
	// 		'fields' 			=> 'ids'
	// 	);

	// 	$get_all_dpad_query = new WP_Query( $dpad_args );
	// 	$get_all_dpad       = $get_all_dpad_query->get_posts();

    //     if ( ! empty( $get_all_dpad ) ) {
	// 		foreach ( $get_all_dpad as $dpad ) {
                
    //             if ( ! empty( $sitepress ) ) {
	// 				$dpad_id = apply_filters( 'wpml_object_id', $dpad, 'wc_dynamic_pricing', true, $default_lang );
	// 			} else {
	// 				$dpad_id = $dpad;
	// 			}

    //             $getFeeStatus = get_post_meta( $dpad_id, 'dpad_settings_status', true );

    //             if ( isset( $getFeeStatus ) && $getFeeStatus === 'off' ) {
    //                 continue;
    //             }
    //             $getFeeType = get_post_meta( $dpad_id, 'dpad_settings_select_dpad_type', true );
    //             //If we need to give access to make get product quantity depend on buy product quantity
    //             // $quantities = WC()->cart->get_cart_item_quantities(); 
                
    //             if ( isset( $getFeeType ) && ! empty( $getFeeType ) && $getFeeType === 'bogo' ) {
    //                 //For BOGO discount type
    //                 $getGetProduct = intval(get_post_meta( $dpad_id, 'dpad_settings_get_product', true ));
    //                 $in_get_cart = $this->wdpad_product_variation_exist_in_cart__premium_only($getGetProduct);
                    
    //                 $getBuyProduct = intval(get_post_meta( $dpad_id, 'dpad_settings_buy_product', true ));
    //                 $is_buy_cart = $this->wdpad_product_variation_exist_in_cart__premium_only($getBuyProduct);

    //                 //Get item key for apply BOGO validation for same product
    //                 if( isset($args['input_name']) && !empty($args['input_name']) ) {
    //                     preg_match('/cart\[(.*?)\]\[qty\]/s', $args['input_name'], $cart_key_from_input_name);
    //                     if( !empty($cart_key_from_input_name)) {
    //                         $current_cart_item_obj = WC()->cart->get_cart_item($cart_key_from_input_name[1]);
    //                     }
    //                 }

    //                 if( $is_buy_cart && $in_get_cart ) {
    //                     if ( $product->get_id() === $getGetProduct && isset($current_cart_item_obj['dpad_get_discount_product']) && !empty($current_cart_item_obj['dpad_get_discount_product']) ) {
    //                         if( $getBuyProduct === $getGetProduct ){
    //                             $args['input_value'] = 1;
    //                             WC()->cart->set_quantity( $in_get_cart, 1 );
    //                         } else {
    //                             $args['max_value'] = $quantities[$getBuyProduct];
    //                             if( isset($args['input_value'] ) && !empty($args['input_value'] ) && $args['input_value'] > $quantities[$getBuyProduct] ){
    //                                 $args['input_value'] = $quantities[$getBuyProduct];
    //                                 WC()->cart->set_quantity( $in_get_cart, $quantities[$getBuyProduct] );
    //                             }
    //                         }
    //                     }
    //                 }
    //             }
    //         }
    //     }
    //     return $args;
    // }
    // phpcs:enable

    /**
	 * Match country rules
	 *
	 * @param array  $country_array
	 * @param string $general_rule_match
	 *
	 * @return string $is_passed
	 *
	 * @since    1.3.3
	 *
	 * @uses     WC_Customer::get_shipping_country()
	 *
	 */
	public function wdpad_match_country_rules( $country_array, $general_rule_match ) {
		$selected_country = WC()->customer->get_shipping_country();
		$is_passed        = array();
        
		foreach ( $country_array as $key => $country ) {
            if ( 'is_equal_to' === $country['product_dpad_conditions_is'] ) {
                if ( ! empty( $country['product_dpad_conditions_values'] ) ) {
                    if ( in_array( $selected_country, $country['product_dpad_conditions_values'], true ) ) {
                        $is_passed[ $key ]['has_dpad_based_on_country'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_country'] = 'no';
                    }
                }
                if ( empty( $country['product_dpad_conditions_values'] ) ) {
                    $is_passed[ $key ]['has_dpad_based_on_country'] = 'yes';
                }
            }
            if ( 'not_in' === $country['product_dpad_conditions_is'] ) {
                if ( ! empty( $country['product_dpad_conditions_values'] ) ) {
                    if ( in_array( $selected_country, $country['product_dpad_conditions_values'], true ) || in_array( 'all', $country['product_dpad_conditions_values'], true ) ) {
                        $is_passed[ $key ]['has_dpad_based_on_country'] = 'no';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_country'] = 'yes';
                    }
                }
            }
        }
        $main_is_passed = $this->dpad_check_all_passed_general_rule( $is_passed, 'has_dpad_based_on_country', $general_rule_match );

		return $main_is_passed;
	}

    /**
	 * Match city rules
	 *
	 * @param array  $city_array
	 * @param string $general_rule_match
	 *
	 * @return string $is_passed
	 *
	 * @since    1.3.3
	 *
	 * @uses     WC_Customer::get_shipping_city()
	 *
	 */
	public function wdpad_match_city_rules__premium_only( $city_array, $general_rule_match ) {
		$selected_city = WC()->customer->get_shipping_city();
		$is_passed        = array();
		foreach ( $city_array as $key => $city ) {
            if ( ! empty( $city['product_dpad_conditions_values'] ) ) {

                $citystr        = str_replace( PHP_EOL, "<br/>", $city['product_dpad_conditions_values'] );
                $city_val_array = explode( '<br/>', $citystr );
                $city_val_array = array_map( 'trim', $city_val_array );

                if ( 'is_equal_to' === $city['product_dpad_conditions_is'] ) {
                    if ( in_array( $selected_city, $city_val_array, true ) ) {
                        $is_passed[ $key ]['has_dpad_based_on_city'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_city'] = 'no';
                    }
                }
                if ( 'not_in' === $city['product_dpad_conditions_is'] ) {
                    if ( in_array( $selected_city, $city_val_array, true ) ) {
                        $is_passed[ $key ]['has_dpad_based_on_city'] = 'no';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_city'] = 'yes';
                    }
                }
            }
        }
        $main_is_passed = $this->dpad_check_all_passed_general_rule( $is_passed, 'has_dpad_based_on_city', $general_rule_match );

		return $main_is_passed;
	}

    /**
	 * Match state rules
	 *
	 * @param array  $state_array
	 * @param string $general_rule_match
	 *
	 * @return string $main_is_passed
	 *
	 * @uses     WC_Customer::get_shipping_state()
	 *
	 * @since    1.3.3
	 *
	 * @uses     WC_Customer::get_shipping_country()
	 */
	public function wdpad_match_state_rules__premium_only( $state_array, $general_rule_match ) {
		$country        = WC()->customer->get_shipping_country();
		$state          = WC()->customer->get_shipping_state();
		$selected_state = $country . ':' . $state;
		$is_passed      = array();
		foreach ( $state_array as $key => $state ) {
            if ( ! empty( $state['product_dpad_conditions_values'] ) ) {
                if ( 'is_equal_to' === $state['product_dpad_conditions_is'] ) {
                    if ( in_array( $selected_state, $state['product_dpad_conditions_values'], true ) ) {
                        $is_passed[ $key ]['has_dpad_based_on_state'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_state'] = 'no';
                    }
                }
                if ( 'not_in' === $state['product_dpad_conditions_is'] ) {
                    if ( in_array( $selected_state, $state['product_dpad_conditions_values'], true ) ) {
                        $is_passed[ $key ]['has_dpad_based_on_state'] = 'no';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_state'] = 'yes';
                    }
                }
            }
        }
        $main_is_passed = $this->dpad_check_all_passed_general_rule( $is_passed, 'has_dpad_based_on_state', $general_rule_match ); 

		return $main_is_passed;
	}

    /**
	 * Match postcode rules
	 *
	 * @param array  $postcode_array
	 * @param string $general_rule_match
	 *
	 * @return string $main_is_passed
	 *
	 * @since    1.3.3
	 *
	 * @uses     WC_Customer::get_shipping_postcode()
	 *
	 */
	public function wdpad_match_postcode_rules__premium_only( $postcode_array, $general_rule_match ) {
		$selected_postcode = WC()->customer->get_shipping_postcode();
		$is_passed         = array();
		foreach ( $postcode_array as $key => $postcode ) {
            if ( ! empty( $postcode['product_dpad_conditions_values'] ) ) {
                $postcodestr        = str_replace( PHP_EOL, "<br/>", $postcode['product_dpad_conditions_values'] );
                $postcode_val_array = explode( '<br/>', $postcodestr );
                $postcode_val_array = array_map( 'trim', $postcode_val_array );

                if ( 'is_equal_to' === $postcode['product_dpad_conditions_is'] ) {
                    if ( in_array( $selected_postcode, $postcode_val_array, true ) ) {
                        $is_passed[ $key ]['has_dpad_based_on_postcode'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_postcode'] = 'no';
                    }
                }
                if ( 'not_in' === $postcode['product_dpad_conditions_is'] ) {
                    if ( in_array( $selected_postcode, $postcode_val_array, true ) ) {
                        $is_passed[ $key ]['has_dpad_based_on_postcode'] = 'no';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_postcode'] = 'yes';
                    }
                }
            }
        }
        $main_is_passed = $this->dpad_check_all_passed_general_rule( $is_passed, 'has_dpad_based_on_postcode', $general_rule_match );

		return $main_is_passed;
	}

    /**
	 * Match zone rules
	 *
	 * @param array  $zone_array
	 * @param string $general_rule_match
	 *
	 * @return string $main_is_passed
	 *
	 * @since    1.3.3
	 *
	 * @uses     wdpad_get_shipping_zone()
	 * @uses     dpad_check_all_passed_general_rule()
	 *
	 */
	public function wdpad_match_zone_rules__premium_only( $zone_array, $general_rule_match ) {
        $get_zonelist    = $this->wdpad_get_shipping_zone();
		$is_passed = array();
		foreach ( $zone_array as $key => $zone ) {
            if ( ! empty( $zone['product_dpad_conditions_values'] ) ) {
                if ( 'is_equal_to' === $zone['product_dpad_conditions_is'] ) {
                    if ( in_array( $get_zonelist, $zone['product_dpad_conditions_values'],true ) ) {
                        $is_passed[ $key ]['has_dpad_based_on_zone'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_zone'] = 'no';
                    }
                }
                if ( 'not_in' === $zone['product_dpad_conditions_is'] ) {
                    if ( in_array( $get_zonelist, $zone['product_dpad_conditions_values'],true ) ) {
                        $is_passed[ $key ]['has_dpad_based_on_zone'] = 'no';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_zone'] = 'yes';
                    }
                }
            }
        }
        $main_is_passed = $this->dpad_check_all_passed_general_rule( $is_passed, 'has_dpad_based_on_zone', $general_rule_match );

		return $main_is_passed;
	}

    /**
	 * Match simple products rules
	 *
	 * @param array  $cart_product_ids_array
	 * @param array  $product_array
	 * @param string $general_rule_match
	 *
	 * @return string $main_is_passed
	 *
	 * @since    1.3.3
	 *
	 */
	public function wdpad_match_simple_products_rule( $cart_array, $product_array, $sale_product_check, $general_rule_match, $default_lang ) {
        global $sitepress;
		$is_passed = array();
		$cart_products_array = array();
        $cart_product        = $this->dpad_array_column( $cart_array, 'product_id' );
        $product_ids_on_sale = wc_get_product_ids_on_sale();

        if( "exclude" === $sale_product_check ){
            $cart_product = array_diff($cart_product, $product_ids_on_sale);
        }

        if ( isset( $cart_product ) && ! empty( $cart_product ) ) {
            foreach ( $cart_product as $key => $cart_product_id ) {
                if ( ! empty( $sitepress ) ) {
                    $cart_products_array[] = apply_filters( 'wpml_object_id', $cart_product_id, 'product', true, $default_lang );
                } else {
                    $cart_products_array[] = $cart_product_id;
                }
            }
        }

        foreach ( $product_array as $key => $product ) {
            if ( ! empty( $product['product_dpad_conditions_values'] ) ) {
                if ( 'is_equal_to' === $product['product_dpad_conditions_is'] ) {
                    foreach ( $product['product_dpad_conditions_values'] as $product_id ) {
                        settype( $product_id, 'integer' );
                        if ( in_array( $product_id, dpad_convert_array_to_int($cart_products_array), true ) ) {
                            $is_passed[ $key ]['has_dpad_based_on_product'] = 'yes';
                            break;
                        } else {
                            $is_passed[ $key ]['has_dpad_based_on_product'] = 'no';
                        }
                    }
                }
                if ( $product['product_dpad_conditions_is'] === 'not_in' ) {
                    foreach ( $product['product_dpad_conditions_values'] as $product_id ) {
                        settype( $product_id, 'integer' );
                        if ( in_array( $product_id, dpad_convert_array_to_int($cart_products_array), true ) ) {
                            $is_passed[ $key ]['has_dpad_based_on_product'] = 'no';
                            break;
                        } else {
                            $is_passed[ $key ]['has_dpad_based_on_product'] = 'yes';
                        }
                    }
                }
            }
        }
        $main_is_passed = $this->dpad_check_all_passed_general_rule( $is_passed, 'has_dpad_based_on_product', $general_rule_match );

		return $main_is_passed;
	}

    /**
	 * Match variable products rules
	 *
	 * @param array $cart_product_ids_array
	 * @param array $variableproduct_array
	 * * @param string $general_rule_match
	 *
	 * @return string $main_is_passed
	 *
	 * @since    1.3.3
	 *
	 */
	public function wdpad_match_variable_products_rule__premium_only( $cart_array, $variableproduct_array, $sale_product_check, $general_rule_match, $default_lang ) {
        global $sitepress;
		$is_passed      = array();
		$cart_products_array = array();
        $cart_product        = $this->dpad_array_column( $cart_array, 'variation_id' );
        $product_ids_on_sale = wc_get_product_ids_on_sale();
        
        if( "exclude" === $sale_product_check ){
            $cart_product = array_diff($cart_product, $product_ids_on_sale);
        }
        
        if ( isset( $cart_product ) && ! empty( $cart_product ) ) {
            foreach ( $cart_product as $key => $cart_product_id ) {
                if ( ! empty( $sitepress ) ) {
                    $cart_products_array[] = apply_filters( 'wpml_object_id', $cart_product_id, 'product', true, $default_lang );
                } else {
                    $cart_products_array[] = $cart_product_id;
                }
            }
        }
        foreach ( $variableproduct_array as $key => $product ) {
            if ( ! empty( $product['product_dpad_conditions_values'] ) ) {
                if ( $product['product_dpad_conditions_is'] === 'is_equal_to' ) {
                    foreach ( $product['product_dpad_conditions_values'] as $product_id ) {
                        settype( $product_id, 'integer' );
                        if ( in_array( $product_id, dpad_convert_array_to_int($cart_products_array), true ) ) {
                            $is_passed[ $key ]['has_dpad_based_on_variable_product'] = 'yes';
                            break;
                        } else {
                            $is_passed[ $key ]['has_dpad_based_on_variable_product'] = 'no';
                        }
                    }
                }
                if ( $product['product_dpad_conditions_is'] === 'not_in' ) {
                    foreach ( $product['product_dpad_conditions_values'] as $product_id ) {
                        settype( $product_id, 'integer' );
                        if ( in_array( $product_id, dpad_convert_array_to_int($cart_products_array), true ) ) {
                            $is_passed[ $key ]['has_dpad_based_on_variable_product'] = 'no';
                            break;
                        } else {
                            $is_passed[ $key ]['has_dpad_based_on_variable_product'] = 'yes';
                        }
                    }
                }
            }
        }
        $main_is_passed = $this->dpad_check_all_passed_general_rule( $is_passed, 'has_dpad_based_on_variable_product', $general_rule_match );

		return $main_is_passed;
	}

    /**
	 * Match category rules
	 *
	 * @param array  $cart_product_ids_array
	 * @param array  $category_array
	 * @param string $general_rule_match
	 *
	 * @return string $main_is_passed
	 *
	 * @since    1.3.3
	 *
	 * @uses     dpad_array_column()
	 * @uses     wp_get_post_terms()
	 */
	public function wdpad_match_category_rule( $cart_array, $category_array, $sale_product_check, $general_rule_match, $default_lang ) {
        global $sitepress;
		$is_passed              = array();
		$cart_product           = $this->dpad_array_column( $cart_array, 'product_id' );
        $cart_category_id_array = array();
        $cart_products_array    = array();
        $product_ids_on_sale 	= wc_get_product_ids_on_sale();
        
        foreach ( $cart_array as $value ) {
            $cart_product_id = ( ! empty( $value['variation_id'] ) && 0 !== $value['variation_id'] ) ? $value['variation_id'] : $value['product_id'];
            if ( ! empty( $sitepress ) ) {
                $cart_products_array[] = apply_filters( 'wpml_object_id', $cart_product_id, 'product', true, $default_lang );
            } else {
                $cart_products_array[] = $cart_product_id;
            }
        }

        if( "exclude" === $sale_product_check ){
            $cart_product = array_diff($cart_product, $product_ids_on_sale);
        }

        foreach ( $cart_products_array as $product ) {
            $prod_obj = wc_get_product($product);
            if( 'simple' !== $prod_obj->get_type() ) {
                $product = $prod_obj->get_parent_id();
            }

            $cart_product_category = wp_get_post_terms( $product, 'product_cat', array( 'fields' => 'ids' ) );

            if ( isset( $cart_product_category ) && ! empty( $cart_product_category ) && is_array( $cart_product_category ) ) {
                $cart_category_id_array[] = $cart_product_category;
            }
        }
        $get_cat_all = array_unique( $this->wdpad_array_flatten( $cart_category_id_array ) );
        foreach ( $category_array as $key => $category ) {
            if ( ! empty( $category['product_dpad_conditions_values'] ) ) {
                if ( $category['product_dpad_conditions_is'] === 'is_equal_to' ) {
                    foreach ( $category['product_dpad_conditions_values'] as $category_id ) {
                        settype( $category_id, 'integer' );
                        if ( in_array( $category_id, dpad_convert_array_to_int($get_cat_all), true ) ) {
                            $is_passed[ $key ]['has_dpad_based_on_category'] = 'yes';
                            break;
                        } else {
                            $is_passed[ $key ]['has_dpad_based_on_category'] = 'no';
                        }
                    }
                }
                if ( $category['product_dpad_conditions_is'] === 'not_in' ) {
                    foreach ( $category['product_dpad_conditions_values'] as $category_id ) {
                        settype( $category_id, 'integer' );
                        if ( in_array( $category_id, dpad_convert_array_to_int($get_cat_all), true ) ) {
                            $is_passed[ $key ]['has_dpad_based_on_category'] = 'no';
                            break;
                        } else {
                            $is_passed[ $key ]['has_dpad_based_on_category'] = 'yes';
                        }
                    }
                }
            }
        }
        $main_is_passed = $this->dpad_check_all_passed_general_rule( $is_passed, 'has_dpad_based_on_category', $general_rule_match );

		return $main_is_passed;
	}

    /**
	 * Match tag rules
	 *
	 * @param array  $cart_product_ids_array
	 * @param array  $tag_array
	 * @param string $general_rule_match
	 *
	 * @return string $main_is_passed
	 *
	 * @uses     wp_get_post_terms()
	 *
	 * @since    1.3.3
	 *
	 */
	public function wdpad_match_tag_rule__premium_only( $cart_array, $tag_array, $sale_product_check, $general_rule_match, $default_lang ) {
        global $sitepress;
		$tagid                  = array();
		$is_passed              = array();
        $cart_products_array    = array();
        $product_ids_on_sale 	= wc_get_product_ids_on_sale();
        
        foreach ( $cart_array as $value ) {
            $cart_product_id = ( ! empty( $value['variation_id'] ) && 0 !== $value['variation_id'] ) ? $value['variation_id'] : $value['product_id'];
            if ( ! empty( $sitepress ) ) {
                $cart_products_array[] = apply_filters( 'wpml_object_id', $cart_product_id, 'product', true, $default_lang );
            } else {
                $cart_products_array[] = $cart_product_id;
            }
        }
        
        if( "exclude" === $sale_product_check ){
            $cart_products_array = array_diff($cart_products_array, $product_ids_on_sale);
        }
        
        foreach ( $cart_products_array as $product ) {
            $prod_obj = wc_get_product($product);
            if( 'simple' !== $prod_obj->get_type() ) {
                $product = $prod_obj->get_parent_id();
            }

            $cart_product_tag = wp_get_post_terms( $product, 'product_tag', array( 'fields' => 'ids' ) );
            
            if ( isset( $cart_product_tag ) && ! empty( $cart_product_tag ) && is_array( $cart_product_tag ) ) {
                $tagid[] = $cart_product_tag;
            }
        }
        $get_tag_all = array_unique( $this->wdpad_array_flatten( $tagid ) );
        
        foreach ( $tag_array as $key => $tag ) {
            if ( ! empty( $tag['product_dpad_conditions_values'] ) ) {
                if ( $tag['product_dpad_conditions_is'] === 'is_equal_to' ) {
                    foreach ( $tag['product_dpad_conditions_values'] as $tag_id ) {
                        settype( $tag_id, 'integer' );
                        if ( in_array( $tag_id, $get_tag_all, true ) ) {
                            $is_passed[ $key ]['has_dpad_based_on_tag'] = 'yes';
                            break;
                        } else {
                            $is_passed[ $key ]['has_dpad_based_on_tag'] = 'no';
                        }
                    }
                }
                if ( $tag['product_dpad_conditions_is'] === 'not_in' ) {
                    foreach ( $tag['product_dpad_conditions_values'] as $tag_id ) {
                        settype( $tag_id, 'integer' );
                        if ( in_array( $tag_id, $get_tag_all, true ) ) {
                            $is_passed[ $key ]['has_dpad_based_on_tag'] = 'no';
                            break;
                        } else {
                            $is_passed[ $key ]['has_dpad_based_on_tag'] = 'yes';
                        }
                    }
                }
            }
        }
        $main_is_passed = $this->dpad_check_all_passed_general_rule( $is_passed, 'has_dpad_based_on_tag', $general_rule_match );

		return $main_is_passed;
	}

    /**
	 * Match rule based on product qty
	 *
	 * @param array  $cart_array
	 * @param array  $quantity_array
	 * @param string $general_rule_match
	 *
	 * @return string $main_is_passed
	 * @since    3.4
	 *
	 * @uses     WC_Cart::get_cart()
	 *
	 */
	public function wdpad_match_product_based_qty_rule__premium_only( $products_based_qty, $product_qty_array, $general_rule_match ) {
		$quantity_total = $products_based_qty[0] > 0 ? $products_based_qty[0] : 0;
        $is_passed 		= array();
        settype( $quantity_total, 'float' );

        foreach ( $product_qty_array as $key => $quantity ) {
            settype( $quantity['product_dpad_conditions_values'], 'float' );
            if ( ! empty( $quantity['product_dpad_conditions_values'] ) ) {
                if ( $quantity['product_dpad_conditions_is'] === 'is_equal_to' ) {
                    if ( $quantity_total === $quantity['product_dpad_conditions_values'] ) {
                        $is_passed[ $key ]['has_dpad_based_on_product_qty'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_product_qty'] = 'no';
                        break;
                    }
                }

                if ( $quantity['product_dpad_conditions_is'] === 'less_equal_to' ) {
                    if ( $quantity['product_dpad_conditions_values'] >= $quantity_total ) {
                        $is_passed[ $key ]['has_dpad_based_on_product_qty'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_product_qty'] = 'no';
                        break;
                    }
                }

                if ( $quantity['product_dpad_conditions_is'] === 'less_then' ) {
                    if ( $quantity['product_dpad_conditions_values'] > $quantity_total ) {
                        $is_passed[ $key ]['has_dpad_based_on_product_qty'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_product_qty'] = 'no';
                        break;
                    }
                }

                if ( $quantity['product_dpad_conditions_is'] === 'greater_equal_to' ) {
                    if ( $quantity['product_dpad_conditions_values'] <= $quantity_total ) {
                        $is_passed[ $key ]['has_dpad_based_on_product_qty'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_product_qty'] = 'no';
                        break;
                    }
                }

                if ( $quantity['product_dpad_conditions_is'] === 'greater_then' ) {
                    if ( $quantity['product_dpad_conditions_values'] < $quantity_total ) {
                        $is_passed[ $key ]['has_dpad_based_on_product_qty'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_product_qty'] = 'no';
                        break;
                    }
                }

                if ( $quantity['product_dpad_conditions_is'] === 'not_in' ) {
                    if ( $quantity_total === $quantity['product_dpad_conditions_values'] ) {
                        $is_passed[ $key ]['has_dpad_based_on_product_qty'] = 'no';
                        break;
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_product_qty'] = 'yes';
                    }
                }
            }
        }
        $main_is_passed = $this->dpad_check_all_passed_general_rule( $is_passed, 'has_dpad_based_on_product_qty', $general_rule_match );
		return $main_is_passed;
	}

    /**
	 * Match rule based on product count
	 *
	 * @param array  $cart_array
	 * @param array  $quantity_array
	 * @param string $general_rule_match
	 *
	 * @return string $main_is_passed
	 * @since    3.4
	 *
	 * @uses     WC_Cart::get_cart()
	 *
	 */
	public function wdpad_match_product_count_rule( $quantity_total, $product_count_array, $general_rule_match ) {
        $is_passed = array();
        $quantity_total = $quantity_total > 0 ? $quantity_total : 0;

		foreach ( $product_count_array as $key => $quantity ) {
            settype( $quantity['product_dpad_conditions_values'], 'float' );
            if ( ! empty( $quantity['product_dpad_conditions_values'] ) ) {

                if ( $quantity['product_dpad_conditions_is'] === 'is_equal_to' ) {
                    if ( $quantity_total === $quantity['product_dpad_conditions_values'] ) {
                        $is_passed[ $key ]['has_dpad_based_on_product_count'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_product_count'] = 'no';
                        break;
                    }
                }

                if ( $quantity['product_dpad_conditions_is'] === 'less_equal_to' ) {
                    if ( $quantity['product_dpad_conditions_values'] >= $quantity_total ) {
                        $is_passed[ $key ]['has_dpad_based_on_product_count'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_product_count'] = 'no';
                        break;
                    }
                }

                if ( $quantity['product_dpad_conditions_is'] === 'less_then' ) {
                    if ( $quantity['product_dpad_conditions_values'] > $quantity_total ) {
                        $is_passed[ $key ]['has_dpad_based_on_product_count'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_product_count'] = 'no';
                        break;
                    }
                }

                if ( $quantity['product_dpad_conditions_is'] === 'greater_equal_to' ) {
                    if ( $quantity['product_dpad_conditions_values'] <= $quantity_total ) {
                        $is_passed[ $key ]['has_dpad_based_on_product_count'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_product_count'] = 'no';
                        break;
                    }
                }

                if ( $quantity['product_dpad_conditions_is'] === 'greater_then' ) {
                    if ( $quantity['product_dpad_conditions_values'] < $quantity_total ) {
                        $is_passed[ $key ]['has_dpad_based_on_product_count'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_product_count'] = 'no';
                        break;
                    }
                }

                if ( $quantity['product_dpad_conditions_is'] === 'not_in' ) {
                    if ( $quantity_total === $quantity['product_dpad_conditions_values'] ) {
                        $is_passed[ $key ]['has_dpad_based_on_product_count'] = 'no';
                        break;
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_product_count'] = 'yes';
                    }
                }
            }
        }
        $main_is_passed = $this->dpad_check_all_passed_general_rule( $is_passed, 'has_dpad_based_on_product_count', $general_rule_match );
		return $main_is_passed;
	}

    /**
	 * Match user rules
	 *
	 * @param array  $user_array
	 * @param string $general_rule_match
	 *
	 * @return string $main_is_passed
	 *
	 * @uses     get_current_user_id()
	 *
	 * @since    1.3.3
	 *
	 * @uses     is_user_logged_in()
	 */
	public function wdpad_match_user_rule( $user_array, $general_rule_match ) {
		
		$is_passed          = array();
		$current_user_id    = get_current_user_id();
        settype( $current_user_id, 'integer' );

        foreach ( $user_array as $key => $user ) {
            if ( 'is_equal_to' === $user['product_dpad_conditions_is'] ) {
                if ( in_array( $current_user_id, dpad_convert_array_to_int($user['product_dpad_conditions_values']), true ) ) {
                    $is_passed[ $key ]['has_dpad_based_on_user'] = 'yes';
                } else {
                    $is_passed[ $key ]['has_dpad_based_on_user'] = 'no';
                }
            }
            if ( 'not_in' === $user['product_dpad_conditions_is'] ) {
                if ( in_array( $current_user_id, dpad_convert_array_to_int($user['product_dpad_conditions_values']), true ) ) {
                    $is_passed[ $key ]['has_dpad_based_on_user'] = 'no';
                } else {
                    $is_passed[ $key ]['has_dpad_based_on_user'] = 'yes';
                }
            }
        }
        $main_is_passed = $this->dpad_check_all_passed_general_rule( $is_passed, 'has_dpad_based_on_user', $general_rule_match );

		return $main_is_passed;
	}

    /**
	 * Match user role rules
	 *
	 * @param array  $user_role_array
	 * @param string $general_rule_match
	 *
	 * @return string $main_is_passed
	 *
	 * @since    1.3.3
	 *
	 * @uses     is_user_logged_in()
	 *
	 */
	public function wdpad_match_user_role_rule__premium_only( $user_role_array, $general_rule_match ) {
        global $current_user;
		$is_passed 		= array();
        if ( is_user_logged_in() ) {
            $current_user_role = $current_user->roles;
        } else {
            $current_user_role = array('guest');
        }
        if ( is_array( $current_user_role ) && isset( $current_user_role ) && ! empty( $current_user_role ) ) {
            foreach ( $user_role_array as $key => $user_role ) {
                if( ! empty($user_role['product_dpad_conditions_values'] ) ) {
                    foreach ( $current_user_role as $current_user_all_role ) {
                        if ( 'is_equal_to' === $user_role['product_dpad_conditions_is'] ) {
                            if ( in_array( $current_user_all_role, $user_role['product_dpad_conditions_values'], true ) ) {
                                $is_passed[ $key ]['has_dpad_based_on_user_role'] = 'yes';
                            } else {
                                $is_passed[ $key ]['has_dpad_based_on_user_role'] = 'no';
                            }
                        }
                        if ( 'not_in' === $user_role['product_dpad_conditions_is'] ) {
                            if ( in_array( $current_user_all_role, $user_role['product_dpad_conditions_values'], true ) ) {
                                $is_passed[ $key ]['has_dpad_based_on_user_role'] = 'no';
                            } else {
                                $is_passed[ $key ]['has_dpad_based_on_user_role'] = 'yes';
                            }
                        }
                    }
                }
            }
        }
        $main_is_passed = $this->dpad_check_all_passed_general_rule( $is_passed, 'has_dpad_based_on_user_role', $general_rule_match );

		return $main_is_passed;
	}

    /**
	 * Match user role rules
	 *
	 * @param array  $user_role_array
	 * @param string $general_rule_match
	 *
	 * @return string $main_is_passed
	 *
	 * @since    1.3.3
	 *
	 * @uses     is_user_logged_in()
	 *
	 */
	public function wdpad_match_user_mail_rule__premium_only( $current_user_mail, $user_mail_array, $general_rule_match ) {
		$is_passed 		= array();
        if ( isset( $current_user_mail ) && ! empty( $current_user_mail ) ) {
            
            $current_user_mail = explode("@",$current_user_mail);

            foreach ( $user_mail_array as $key => $user_mail ) {
                if( !empty($user_mail['product_dpad_conditions_values']) ){
                    
                    $usermailstr         = str_replace( PHP_EOL, "<br/>", $user_mail['product_dpad_conditions_values'] );
                    $user_mail_val_array = explode( '<br/>', $usermailstr );   
                    $user_mail_val_array = array_map( 'trim', $user_mail_val_array );
                    
                    if ( $user_mail['product_dpad_conditions_is'] === 'user_name' ) {

                        if( in_array( $current_user_mail[0], $user_mail_val_array, true) ) {
                            $is_passed[ $key ]['has_dpad_based_on_user_mail'] = 'yes';
                        } else {
                            $is_passed[ $key ]['has_dpad_based_on_user_mail'] = 'no';
                        }
                    } else if( $user_mail['product_dpad_conditions_is'] === 'domain_name' ) {
                        
                        if( in_array( $current_user_mail[1], $user_mail_val_array, true ) ) {
                            $is_passed[ $key ]['has_dpad_based_on_user_mail'] = 'yes';
                        } else {
                            $is_passed[ $key ]['has_dpad_based_on_user_mail'] = 'no';
                        }
                    } else {
                        $full_mail = implode("@",$current_user_mail);
                        if( in_array( $full_mail, $user_mail_val_array, true ) ) {
                            $is_passed[ $key ]['has_dpad_based_on_user_mail'] = 'yes';
                        } else {
                            $is_passed[ $key ]['has_dpad_based_on_user_mail'] = 'no';
                        }
                    }
                }
            }
        }
        $main_is_passed = $this->dpad_check_all_passed_general_rule( $is_passed, 'has_dpad_based_on_user_mail', $general_rule_match );

		return $main_is_passed;
	}

    /**
	 * Match user role rules
	 *
	 * @param array  $user_role_array
	 * @param string $general_rule_match
	 *
	 * @return string $main_is_passed
	 *
	 * @since    1.3.3
	 *
	 * @uses     is_user_logged_in()
	 *
	 */
    public function wdpad_match_total_spent_order_rule__premium_only( $total_spent_order_array, $general_rule_match ){
        global $current_user, $woocommerce_wpml;
        $totalprice 	= 0;
        $resultprice 	= wc_get_customer_total_spent( $current_user->ID );
        $is_passed 	= array();
        if ( isset( $woocommerce_wpml ) && ! empty( $woocommerce_wpml->multi_currency ) ) {
            $new_resultprice = $woocommerce_wpml->multi_currency->prices->unconvert_price_amount( $resultprice );
        } else {
            $new_resultprice = $resultprice;
        }
        settype($new_resultprice, 'float');

        foreach ( $total_spent_order_array as $key => $total_spent_order ) {
            settype($total_spent_order['product_dpad_conditions_values'], 'float');
            if ( $total_spent_order['product_dpad_conditions_is'] === 'is_equal_to' ) {
                if ( ! empty( $total_spent_order['product_dpad_conditions_values'] ) ) {
                    if ( $total_spent_order['product_dpad_conditions_values'] === $new_resultprice ) {
                        $is_passed[ $key ]['has_dpad_based_on_total_spent_order'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_total_spent_order'] = 'no';
                        break;
                    }
                }
            }
            if ( $total_spent_order['product_dpad_conditions_is'] === 'less_equal_to' ) {
                if ( ! empty( $total_spent_order['product_dpad_conditions_values'] ) ) {
                    if ( $total_spent_order['product_dpad_conditions_values'] >= $new_resultprice ) {
                        $is_passed[ $key ]['has_dpad_based_on_total_spent_order'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_total_spent_order'] = 'no';
                        break;
                    }
                }
            }
            if ( $total_spent_order['product_dpad_conditions_is'] === 'less_then' ) {
                if ( ! empty( $total_spent_order['product_dpad_conditions_values'] ) ) {
                    if ( $total_spent_order['product_dpad_conditions_values'] > $new_resultprice ) {
                        $is_passed[ $key ]['has_dpad_based_on_total_spent_order'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_total_spent_order'] = 'no';
                        break;
                    }
                }
            }
            if ( $total_spent_order['product_dpad_conditions_is'] === 'greater_equal_to' ) {
                if ( ! empty( $total_spent_order['product_dpad_conditions_values'] ) ) {
                    if ( $total_spent_order['product_dpad_conditions_values'] <= $new_resultprice ) {
                        $is_passed[ $key ]['has_dpad_based_on_total_spent_order'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_total_spent_order'] = 'no';
                        break;
                    }
                }
            }
            if ( $total_spent_order['product_dpad_conditions_is'] === 'greater_then' ) {
                if ( ! empty( $total_spent_order['product_dpad_conditions_values'] ) ) {
                    if ( $total_spent_order['product_dpad_conditions_values'] < $new_resultprice ) {
                        $is_passed[ $key ]['has_dpad_based_on_total_spent_order'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_total_spent_order'] = 'no';
                        break;
                    }
                }
            }
            if ( $total_spent_order['product_dpad_conditions_is'] === 'not_in' ) {
                if ( ! empty( $total_spent_order['product_dpad_conditions_values'] ) ) {
                    if ( $new_resultprice === $total_spent_order['product_dpad_conditions_values'] ) {
                        $is_passed[ $key ]['has_dpad_based_on_total_spent_order'] = 'no';
                        break;
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_total_spent_order'] = 'yes';
                    }
                }
            }
        }

        $main_is_passed = $this->dpad_check_all_passed_general_rule( $is_passed, 'has_dpad_based_on_total_spent_order', $general_rule_match );

        return $main_is_passed;
    }

     /**
	 * Match user role rules
	 *
	 * @param array  $user_role_array
	 * @param string $general_rule_match
	 *
	 * @return string $main_is_passed
	 *
	 * @since    1.3.3
	 *
	 * @uses     is_user_logged_in()
	 *
	 */
    public function wdpad_match_spent_order_count_rule__premium_only( $spent_order_count_array, $general_rule_match ){
        global $current_user;
        $user_id 		= $current_user->ID;
        $resultcount 	= $this->dpad_check_order_for_user__premium_only( $user_id, true);
        $is_passed 	= array();

        settype($resultcount, 'integer');
        
        foreach ( $spent_order_count_array as $key => $spent_order_count ) {
            settype($spent_order_count['product_dpad_conditions_values'], 'integer');
            if ( $spent_order_count['product_dpad_conditions_is'] === 'is_equal_to' ) {
                if ( ! empty( $spent_order_count['product_dpad_conditions_values'] ) ) {
                    if ( $spent_order_count['product_dpad_conditions_values'] === $resultcount ) {
                        $is_passed[ $key ]['has_dpad_based_on_spent_order_count'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_spent_order_count'] = 'no';
                        break;
                    }
                }
            }
            if ( $spent_order_count['product_dpad_conditions_is'] === 'less_equal_to' ) {
                if ( ! empty( $spent_order_count['product_dpad_conditions_values'] ) ) {
                    if ( $spent_order_count['product_dpad_conditions_values'] >= $resultcount ) {
                        $is_passed[ $key ]['has_dpad_based_on_spent_order_count'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_spent_order_count'] = 'no';
                        break;
                    }
                }
            }
            if ( $spent_order_count['product_dpad_conditions_is'] === 'less_then' ) {
                if ( ! empty( $spent_order_count['product_dpad_conditions_values'] ) ) {
                    if ( $spent_order_count['product_dpad_conditions_values'] > $resultcount ) {
                        $is_passed[ $key ]['has_dpad_based_on_spent_order_count'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_spent_order_count'] = 'no';
                        break;
                    }
                }
            }
            if ( $spent_order_count['product_dpad_conditions_is'] === 'greater_equal_to' ) {
                if ( ! empty( $spent_order_count['product_dpad_conditions_values'] ) ) {
                    if ( $spent_order_count['product_dpad_conditions_values'] <= $resultcount ) {
                        $is_passed[ $key ]['has_dpad_based_on_spent_order_count'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_spent_order_count'] = 'no';
                        break;
                    }
                }
            }
            if ( $spent_order_count['product_dpad_conditions_is'] === 'greater_then' ) {
                if ( ! empty( $spent_order_count['product_dpad_conditions_values'] ) ) {
                    if ( $spent_order_count['product_dpad_conditions_values'] < $resultcount ) {
                        $is_passed[ $key ]['has_dpad_based_on_spent_order_count'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_spent_order_count'] = 'no';
                        break;
                    }
                }
            }
            if ( $spent_order_count['product_dpad_conditions_is'] === 'not_in' ) {
                if ( ! empty( $spent_order_count['product_dpad_conditions_values'] ) ) {
                    if ( $resultcount === $spent_order_count['product_dpad_conditions_values'] ) {
                        $is_passed[ $key ]['has_dpad_based_on_spent_order_count'] = 'no';
                        break;
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_spent_order_count'] = 'yes';
                    }
                }
            }
        }
        $main_is_passed = $this->dpad_check_all_passed_general_rule( $is_passed, 'has_dpad_based_on_spent_order_count', $general_rule_match );

        return $main_is_passed;
    }

    /**
	 * Match user role rules
	 *
	 * @param array  $user_role_array
	 * @param string $general_rule_match
	 *
	 * @return string $main_is_passed
	 *
	 * @since    1.3.3
	 *
	 * @uses     is_user_logged_in()
	 *
	 */
    public function wdpad_match_last_spent_order_rule__premium_only( $last_spent_order_array, $general_rule_match){
        global $current_user, $woocommerce_wpml;
        $user_id 		= $current_user->ID;
        $resultprice 	= $this->dpad_check_order_for_user__premium_only($user_id);
        $is_passed 	= array();

        if ( isset( $woocommerce_wpml ) && ! empty( $woocommerce_wpml->multi_currency ) ) {
            $new_resultprice = $woocommerce_wpml->multi_currency->prices->unconvert_price_amount( $resultprice );
        } else {
            $new_resultprice = $resultprice;
        }
        settype($new_resultprice, 'float');
        
        foreach ( $last_spent_order_array as $key => $last_spent_order ) {
            settype($last_spent_order['product_dpad_conditions_values'], 'float');
            if ( $last_spent_order['product_dpad_conditions_is'] === 'is_equal_to' ) {
                if ( ! empty( $last_spent_order['product_dpad_conditions_values'] ) ) {
                    if ( $last_spent_order['product_dpad_conditions_values'] === $new_resultprice ) {
                        $is_passed[ $key ]['has_dpad_based_on_last_spent_order'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_last_spent_order'] = 'no';
                        break;
                    }
                }
            }
            if ( $last_spent_order['product_dpad_conditions_is'] === 'less_equal_to' ) {
                if ( ! empty( $last_spent_order['product_dpad_conditions_values'] ) ) {
                    if ( $last_spent_order['product_dpad_conditions_values'] >= $new_resultprice ) {
                        $is_passed[ $key ]['has_dpad_based_on_last_spent_order'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_last_spent_order'] = 'no';
                        break;
                    }
                }
            }
            if ( $last_spent_order['product_dpad_conditions_is'] === 'less_then' ) {
                if ( ! empty( $last_spent_order['product_dpad_conditions_values'] ) ) {
                    if ( $last_spent_order['product_dpad_conditions_values'] > $new_resultprice ) {
                        $is_passed[ $key ]['has_dpad_based_on_last_spent_order'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_last_spent_order'] = 'no';
                        break;
                    }
                }
            }
            if ( $last_spent_order['product_dpad_conditions_is'] === 'greater_equal_to' ) {
                if ( ! empty( $last_spent_order['product_dpad_conditions_values'] ) ) {
                    if ( $last_spent_order['product_dpad_conditions_values'] <= $new_resultprice ) {
                        $is_passed[ $key ]['has_dpad_based_on_last_spent_order'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_last_spent_order'] = 'no';
                        break;
                    }
                }
            }
            if ( $last_spent_order['product_dpad_conditions_is'] === 'greater_then' ) {
                if ( ! empty( $last_spent_order['product_dpad_conditions_values'] ) ) {
                    if ( $last_spent_order['product_dpad_conditions_values'] < $new_resultprice ) {
                        $is_passed[ $key ]['has_dpad_based_on_last_spent_order'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_last_spent_order'] = 'no';
                        break;
                    }
                }
            }
            if ( $last_spent_order['product_dpad_conditions_is'] === 'not_in' ) {
                if ( ! empty( $last_spent_order['product_dpad_conditions_values'] ) ) {
                    if ( $new_resultprice === $last_spent_order['product_dpad_conditions_values'] ) {
                        $is_passed[ $key ]['has_dpad_based_on_last_spent_order'] = 'no';
                        break;
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_last_spent_order'] = 'yes';
                    }
                }
            }
        }
        
        $main_is_passed = $this->dpad_check_all_passed_general_rule( $is_passed, 'has_dpad_based_on_last_spent_order', $general_rule_match );

        return $main_is_passed;
    }

    /**
	 * Match product id with user past order product ids
	 *
	 * @param array  $user_role_array
	 * @param string $general_rule_match
	 *
	 * @return string $main_is_passed
	 *
	 * @since    1.3.3
	 *
	 * @uses     is_user_logged_in()
	 *
	 */
    public function wdpad_match_user_repeat_product_rule__premium_only( $cart_array, $user_repeat_product_array, $sale_product_check, $general_rule_match, $default_lang ){

        global $current_user, $woocommerce_wpml;

        $user_id 		        = $current_user->ID;
        $user_products	        = $this->dpad_get_product_ids_from_order_of_user__premium_only($user_id);
        $is_passed 	            = array();

        $cart_products_array    = array();
        $cart_product           = array();
        foreach( $cart_array as $ca ){
            $cart_product[] = $ca['variation_id'] > 0 ? $ca['variation_id'] : $ca['product_id'];
        }
        $product_ids_on_sale    = wc_get_product_ids_on_sale();

        if( "exclude" === $sale_product_check ){
            $cart_product = array_diff($cart_product, $product_ids_on_sale);
        }

        if ( isset( $cart_product ) && ! empty( $cart_product ) ) {
            foreach ( $cart_product as $key => $cart_product_id ) {
                if ( ! empty( $sitepress ) ) {
                    $cart_product = apply_filters( 'wpml_object_id', $cart_product_id, 'product', true, $default_lang );
                } else {
                    $cart_product = $cart_product_id;
                }
                if( in_array( $cart_product, $user_products, true ) ){
                    $cart_products_array[] = $cart_product;
                }
            }
        }

        foreach ( $user_repeat_product_array as $key => $product ) {
            if ( ! empty( $product['product_dpad_conditions_values'] ) ) {
                if ( 'is_equal_to' === $product['product_dpad_conditions_is'] ) {
                    foreach ( $product['product_dpad_conditions_values'] as $product_id ) {
                        settype( $product_id, 'integer' );
                        if ( in_array( $product_id, dpad_convert_array_to_int($cart_products_array), true ) ) {
                            $is_passed[ $key ]['has_dpad_based_on_user_repeat_product'] = 'yes';
                            break;
                        } else {
                            $is_passed[ $key ]['has_dpad_based_on_user_repeat_product'] = 'no';
                        }
                    }
                }
                if ( $product['product_dpad_conditions_is'] === 'not_in' ) {
                    foreach ( $product['product_dpad_conditions_values'] as $product_id ) {
                        settype( $product_id, 'integer' );
                        if ( in_array( $product_id, dpad_convert_array_to_int($cart_products_array), true ) ) {
                            $is_passed[ $key ]['has_dpad_based_on_user_repeat_product'] = 'no';
                            break;
                        } else {
                            $is_passed[ $key ]['has_dpad_based_on_user_repeat_product'] = 'yes';
                        }
                    }
                }
            }
        }
        $main_is_passed = $this->dpad_check_all_passed_general_rule( $is_passed, 'has_dpad_based_on_user_repeat_product', $general_rule_match );

		return $main_is_passed;
    }

    /**
	 * Match rule based on cart subtotal before discount
	 *
	 * @param string $new_total
	 * @param array  $cart_total_array
	 * @param string $general_rule_match
	 *
	 * @return string $main_is_passed
	 *
	 * @uses     WC_Cart::get_subtotal()
	 *
	 * @since    1.3.3
	 *
	 */
	public function wdpad_match_cart_subtotal_before_discount_rule( $new_total, $cart_total_array, $general_rule_match ) {
		$is_passed = array();
        foreach ( $cart_total_array as $key => $cart_total ) {
            settype( $cart_total['product_dpad_conditions_values'], 'float' );

            if ( ! empty( $cart_total['product_dpad_conditions_values'] ) ) {
                if ( $cart_total['product_dpad_conditions_is'] === 'is_equal_to' ) {	
                    if ( $cart_total['product_dpad_conditions_values'] === $new_total ) {
                        $is_passed[ $key ]['has_dpad_based_on_cart_total'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_cart_total'] = 'no';
                        break;
                    }
                }

                if ( $cart_total['product_dpad_conditions_is'] === 'less_equal_to' ) {
                    if ( $cart_total['product_dpad_conditions_values'] >= $new_total ) {
                        $is_passed[ $key ]['has_dpad_based_on_cart_total'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_cart_total'] = 'no';
                        break;
                    }
                }

                if ( $cart_total['product_dpad_conditions_is'] === 'less_then' ) {
                    if ( $cart_total['product_dpad_conditions_values'] > $new_total ) {
                        $is_passed[ $key ]['has_dpad_based_on_cart_total'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_cart_total'] = 'no';
                        break;
                    }
                }

                if ( $cart_total['product_dpad_conditions_is'] === 'greater_equal_to' ) {
                    if ( $cart_total['product_dpad_conditions_values'] <= $new_total ) {
                        $is_passed[ $key ]['has_dpad_based_on_cart_total'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_cart_total'] = 'no';
                        break;
                    }
                }
            
                if ( $cart_total['product_dpad_conditions_is'] === 'greater_then' ) {
                    if ( $cart_total['product_dpad_conditions_values'] < $new_total ) {
                        $is_passed[ $key ]['has_dpad_based_on_cart_total'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_cart_total'] = 'no';
                        break;
                    }
                }
            
                if ( $cart_total['product_dpad_conditions_is'] === 'not_in' ) {
                    if ( $new_total === $cart_total['product_dpad_conditions_values'] ) {
                        $is_passed[ $key ]['has_dpad_based_on_cart_total'] = 'no';
                        break;
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_cart_total'] = 'yes';
                    }
                }
            }
        }
        $main_is_passed = $this->dpad_check_all_passed_general_rule( $is_passed, 'has_dpad_based_on_cart_total', $general_rule_match );

		return $main_is_passed;
	}

    /**
	 * Match rule based on cart subtotal after discount
	 *
	 * @param string $wc_curr_version
	 * @param array  $cart_totalafter_array
	 *
	 * @return array $is_passed
	 * @uses     WC_Cart::get_total_discount()
	 *
	 * @since    1.3.3
	 *
	 * @uses     WC_Cart::get_subtotal()
	 */
	public function wdpad_match_cart_subtotal_after_discount_rule__premium_only( $new_resultprice, $cart_totalafter_array, $general_rule_match ) {
		$is_passed = array();

        foreach ( $cart_totalafter_array as $key => $cart_totalafter ) {
            settype( $cart_totalafter['product_dpad_conditions_values'], 'float' );

            if ( ! empty( $cart_totalafter['product_dpad_conditions_values'] ) ) {

                if ( $cart_totalafter['product_dpad_conditions_is'] === 'is_equal_to' ) {
                    if ( $cart_totalafter['product_dpad_conditions_values'] === $new_resultprice ) {
                        $is_passed[ $key ]['has_dpad_based_on_cart_totalafter'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_cart_totalafter'] = 'no';
                        break;
                    }
                }
            
                if ( $cart_totalafter['product_dpad_conditions_is'] === 'less_equal_to' ) {
                    if ( $cart_totalafter['product_dpad_conditions_values'] >= $new_resultprice ) {
                        $is_passed[ $key ]['has_dpad_based_on_cart_totalafter'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_cart_totalafter'] = 'no';
                        break;
                    }
                }
            
                if ( $cart_totalafter['product_dpad_conditions_is'] === 'less_then' ) {
                    if ( $cart_totalafter['product_dpad_conditions_values'] > $new_resultprice ) {
                        $is_passed[ $key ]['has_dpad_based_on_cart_totalafter'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_cart_totalafter'] = 'no';
                        break;
                    }
                }
            
                if ( $cart_totalafter['product_dpad_conditions_is'] === 'greater_equal_to' ) {
                    if ( $cart_totalafter['product_dpad_conditions_values'] <= $new_resultprice ) {
                        $is_passed[ $key ]['has_dpad_based_on_cart_totalafter'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_cart_totalafter'] = 'no';
                        break;
                    }
                }
            
                if ( $cart_totalafter['product_dpad_conditions_is'] === 'greater_then' ) {
                    if ( $cart_totalafter['product_dpad_conditions_values'] < $new_resultprice ) {
                        $is_passed[ $key ]['has_dpad_based_on_cart_totalafter'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_cart_totalafter'] = 'no';
                        break;
                    }
                }
            
                if ( $cart_totalafter['product_dpad_conditions_is'] === 'not_in' ) {
                    if ( $new_resultprice === $cart_totalafter['product_dpad_conditions_values'] ) {
                        $is_passed[ $key ]['has_dpad_based_on_cart_totalafter'] = 'no';
                        break;
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_cart_totalafter'] = 'yes';
                    }
                }
            }
        }
        $main_is_passed = $this->dpad_check_all_passed_general_rule( $is_passed, 'has_dpad_based_on_cart_totalafter', $general_rule_match );

		return $main_is_passed;
	}

    /**
	 * Match rule based on cart qty
	 *
	 * @param array  $quantity_total
	 * @param array  $quantity_array
	 * @param string $general_rule_match
	 *
	 * @return string $main_is_passed
	 * @since    3.4
	 *
	 * @uses     WC_Cart::get_cart()
	 *
	 */
	public function wdpad_match_cart_based_qty_rule( $quantity_total, $quantity_array, $general_rule_match ) {
		$quantity_total = $quantity_total > 0 ? $quantity_total : 0;
        $is_passed 		= array();
        settype( $quantity_total, 'float' );

        foreach ( $quantity_array as $key => $quantity ) {
            settype( $quantity['product_dpad_conditions_values'], 'integer' );
            if ( ! empty( $quantity['product_dpad_conditions_values'] ) ) {

                if ( $quantity['product_dpad_conditions_is'] === 'is_equal_to' ) {
                    if ( $quantity_total === $quantity['product_dpad_conditions_values'] ) {
                        $is_passed[ $key ]['has_dpad_based_on_quantity'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_quantity'] = 'no';
                        break;
                    }
                }

                if ( $quantity['product_dpad_conditions_is'] === 'less_equal_to' ) {
                    if ( $quantity['product_dpad_conditions_values'] >= $quantity_total ) {
                        $is_passed[ $key ]['has_dpad_based_on_quantity'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_quantity'] = 'no';
                        break;
                    }
                }

                if ( $quantity['product_dpad_conditions_is'] === 'less_then' ) {
                    if ( $quantity['product_dpad_conditions_values'] > $quantity_total ) {
                        $is_passed[ $key ]['has_dpad_based_on_quantity'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_quantity'] = 'no';
                        break;
                    }
                }

                if ( $quantity['product_dpad_conditions_is'] === 'greater_equal_to' ) {
                    if ( $quantity['product_dpad_conditions_values'] <= $quantity_total ) {
                        $is_passed[ $key ]['has_dpad_based_on_quantity'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_quantity'] = 'no';
                        break;
                    }
                }

                if ( $quantity['product_dpad_conditions_is'] === 'greater_then' ) {
                    if ( $quantity['product_dpad_conditions_values'] < $quantity_total ) {
                        $is_passed[ $key ]['has_dpad_based_on_quantity'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_quantity'] = 'no';
                        break;
                    }
                }

                if ( $quantity['product_dpad_conditions_is'] === 'not_in' ) {
                    if ( $quantity_total === $quantity['product_dpad_conditions_values'] ) {
                        $is_passed[ $key ]['has_dpad_based_on_quantity'] = 'no';
                        break;
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_quantity'] = 'yes';
                    }
                }
            }
        }

        $main_is_passed = $this->dpad_check_all_passed_general_rule( $is_passed, 'has_dpad_based_on_quantity', $general_rule_match );

		return $main_is_passed;
	}

    /**
	 * Match rule based on total cart weight
	 *
	 * @param array $weight_array
	 *
	 * @return array $is_passed
	 * @since    1.3.3
	 *
	 * @uses     WC_Cart::get_cart()
	 *
	 */
	public function wdpad_match_cart_total_weight_rule__premium_only( $weight_total, $weight_array, $general_rule_match ) {
		$weight_total = $weight_total > 0 ? $weight_total : 0; 
		$is_passed = array();
		foreach ( $weight_array as $key => $weight ) {
            settype( $weight['product_dpad_conditions_values'], 'float' );
            if ( ! empty( $weight['product_dpad_conditions_values'] ) ) {

                if ( $weight['product_dpad_conditions_is'] === 'is_equal_to' ) {
                    if ( $weight_total === $weight['product_dpad_conditions_values'] ) {
                        $is_passed[ $key ]['has_dpad_based_on_weight'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_weight'] = 'no';
                        break;
                    }
                }

                if ( $weight['product_dpad_conditions_is'] === 'less_equal_to' ) {
                    if ( $weight['product_dpad_conditions_values'] >= $weight_total ) {
                        $is_passed[ $key ]['has_dpad_based_on_weight'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_weight'] = 'no';
                        break;
                    }
                }

                if ( $weight['product_dpad_conditions_is'] === 'less_then' ) {
                    if ( $weight['product_dpad_conditions_values'] > $weight_total ) {
                        $is_passed[ $key ]['has_dpad_based_on_weight'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_weight'] = 'no';
                        break;
                    }
                }
            
                if ( $weight['product_dpad_conditions_is'] === 'greater_equal_to' ) {
                    if ( $weight['product_dpad_conditions_values'] <= $weight_total ) {
                        $is_passed[ $key ]['has_dpad_based_on_weight'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_weight'] = 'no';
                        break;
                    }
                }

                if ( $weight['product_dpad_conditions_is'] === 'greater_then' ) {
                    if ( $weight['product_dpad_conditions_values'] < $weight_total ) {
                        $is_passed[ $key ]['has_dpad_based_on_weight'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_weight'] = 'no';
                        break;
                    }
                }
                
                if ( $weight['product_dpad_conditions_is'] === 'not_in' ) {
                    if ( $weight_total === $weight['product_dpad_conditions_values'] ) {
                        $is_passed[ $key ]['has_dpad_based_on_weight'] = 'no';
                        break;
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_weight'] = 'yes';
                    }
                }
            }
        }

        $main_is_passed = $this->dpad_check_all_passed_general_rule( $is_passed, 'has_dpad_based_on_weight', $general_rule_match );

		return $main_is_passed;
	}

    /**
	 * Match coupon role rules
	 *
	 * @param string $wc_curr_version
	 * @param array  $coupon_array
	 * @param string $general_rule_match
	 *
	 * @return string $main_is_passed
	 *
	 * @since    2.3.3
	 *
	 * @uses     WC_Cart::get_coupons()
	 * @uses     WC_Coupon::is_valid()
	 *
	 */
	public function wdpad_match_coupon_rule__premium_only( $wc_curr_version, $coupon_array, $general_rule_match ) {
		global $woocommerce;
		$couponId  = array();
        $is_passed = array();

        if ( $wc_curr_version >= 3.0 ) {
            $cart_coupon = WC()->cart->get_coupons();
        } else {
            $cart_coupon = isset( $woocommerce->cart->coupons ) && ! empty( $woocommerce->cart->coupons ) ? $woocommerce->cart->coupons : array();
        }

        if ( ! empty( $cart_coupon ) ) {
            foreach ( $cart_coupon as $cartCoupon ) {
                if ( $cartCoupon->is_valid() && isset( $cartCoupon ) && ! empty( $cartCoupon ) ) {
                    if ( $wc_curr_version >= 3.0 ) {
                        $couponId[] = $cartCoupon->get_id();
                    } else {
                        $couponId[] = $cartCoupon->id;
                    }
                }
            }
        }
        
        foreach ( $coupon_array as $key => $coupon ) {
            if ( ! empty( $coupon['product_dpad_conditions_values'] ) ) {
                $product_dpad_conditions_values = array_map( 'intval', $coupon['product_dpad_conditions_values'] );

                if ( 'is_equal_to' === $coupon['product_dpad_conditions_is'] ) {
                    foreach ( $product_dpad_conditions_values as $coupon_id ) {
                        settype( $coupon_id, 'integer' );
                        if ( in_array( $coupon_id, dpad_convert_array_to_int($couponId), true ) ) {
                            $is_passed[ $key ]['has_dpad_based_on_coupon'] = 'yes';
                            break;
                        } else {
                            $is_passed[ $key ]['has_dpad_based_on_coupon'] = 'no';
                        }
                    }
                }

                if ( 'not_in' === $coupon['product_dpad_conditions_is'] ) {
                    foreach ( $product_dpad_conditions_values as $coupon_id ) {
                        settype( $coupon_id, 'integer' );
                        if ( in_array( $coupon_id, dpad_convert_array_to_int($couponId), true ) ) {
                            $is_passed[ $key ]['has_dpad_based_on_coupon'] = 'no';
                            break;
                        } else {
                            $is_passed[ $key ]['has_dpad_based_on_coupon'] = 'yes';
                        }
                    }
                }
            }
        }
        $main_is_passed = $this->dpad_check_all_passed_general_rule( $is_passed, 'has_dpad_based_on_coupon', $general_rule_match );

		return $main_is_passed;
	}

    /**
	 * Match rule based on shipping class
	 *
	 * @param array $cart_array
	 * @param array $shipping_class_array
	 *
	 * @return array $is_passed
	 * @since    1.3.3
	 *
	 * @uses     get_the_terms()
	 *
	 */
	public function wdpad_match_shipping_class_rule__premium_only( $sale_product_check, $shipping_class_array, $general_rule_match, $default_lang ) {
        global $woocommerce;
		$_shippingclass         = array();
        $product_ids_on_sale 	= wc_get_product_ids_on_sale();
        $is_passed 			    = array();
        foreach ( $woocommerce->cart->get_cart() as $values ) {						
            $product_type = $values['data']->get_type();
            if( false === strpos( $product_type, 'bundle' ) ) {
                $product_id = $values['variation_id'] ? intval($values['variation_id']) : intval($values['product_id']);
                $terms = array();
                if( "exclude" === $sale_product_check ){
                    if( !in_array( $product_id, $product_ids_on_sale, true ) ){
                        $terms = get_the_terms( $product_id, 'product_shipping_class' );
                    }
                } else {
                    $terms = get_the_terms( $product_id, 'product_shipping_class' );
                }
                if ( !empty( $terms ) ) {
                    foreach ( $terms as $term ) {
                        if ( ! empty( $sitepress ) ) {
                            $_shippingclass[] = apply_filters( 'wpml_object_id', $term->term_id, 'product_shipping_class', true, $default_lang );
                        } else {
                            $_shippingclass[] = $term->term_id;
                        }
                    }
                }
            }
        }
        $get_shipping_class_all = array_unique( $this->wdpad_array_flatten( $_shippingclass ) );
        foreach ( $shipping_class_array as $key => $shipping_class ) {
            if ( ! empty( $shipping_class['product_dpad_conditions_values'] ) ) {
                if ( $shipping_class['product_dpad_conditions_is'] === 'is_equal_to' ) {
                    foreach ( $shipping_class['product_dpad_conditions_values'] as $shipping_class_id ) {
                        settype( $shipping_class_id, 'integer' );
                        if ( in_array( $shipping_class_id, dpad_convert_array_to_int($get_shipping_class_all), true ) ) {
                            $is_passed[ $key ]['has_dpad_based_on_shipping_class'] = 'yes';
                            break;
                        } else {
                            $is_passed[ $key ]['has_dpad_based_on_shipping_class'] = 'no';
                        }
                    }
                }
                if ( $shipping_class['product_dpad_conditions_is'] === 'not_in' ) {
                    foreach ( $shipping_class['product_dpad_conditions_values'] as $shipping_class_id ) {
                        settype( $shipping_class_id, 'integer' );
                        if ( in_array( $shipping_class_id, dpad_convert_array_to_int($get_shipping_class_all), true ) ) {
                            $is_passed[ $key ]['has_dpad_based_on_shipping_class'] = 'no';
                            break;
                        } else {
                            $is_passed[ $key ]['has_dpad_based_on_shipping_class'] = 'yes';
                        }
                    }
                }
            }
        }

        $main_is_passed = $this->dpad_check_all_passed_general_rule( $is_passed, 'has_dpad_based_on_shipping_class', $general_rule_match );

		return $main_is_passed;
	}

    /**
	 * Match rule based on payment gateway
	 *
	 * @param int   $wc_curr_version
	 * @param array $payment_gateway
	 * @param array $general_rule_match
	 *
	 * @return array $is_passed
	 * @since    2.3.3
	 *
	 */
	public function wdpad_match_payment_gateway_rule__premium_only( $wc_curr_version, $payment_gateway, $general_rule_match ) {
        global $woocommerce;
		$is_passed = array();
        if( $wc_curr_version >= 3.0 ) {
            $chosen_payment_method = WC()->session->get( 'chosen_payment_method' );
        } else {
            $chosen_payment_method  = $woocommerce->session->chosen_payment_method;
        }

        foreach ( $payment_gateway as $key => $payment ) {
            if ( $payment['product_dpad_conditions_is'] === 'is_equal_to' ) {
                if ( in_array( $chosen_payment_method, $payment['product_dpad_conditions_values'], true ) ) {
                    $is_passed[ $key ]['has_dpad_based_on_payment'] = 'yes';
                } else {
                    $is_passed[ $key ]['has_dpad_based_on_payment'] = 'no';
                }
            }
            if ( $payment['product_dpad_conditions_is'] === 'not_in' ) {
                if ( in_array( $chosen_payment_method, $payment['product_dpad_conditions_values'], true ) ) {
                    $is_passed[ $key ]['has_dpad_based_on_payment'] = 'no';
                } else {
                    $is_passed[ $key ]['has_dpad_based_on_payment'] = 'yes';
                }
            }
        }

        $main_is_passed = $this->dpad_check_all_passed_general_rule( $is_passed, 'has_dpad_based_on_payment', $general_rule_match );

		return $main_is_passed;
	}

    /**
	 * Match rule based on shipping method
	 *
	 * @param int   $wc_curr_version
	 * @param array $shipping_methods
	 * @param array $general_rule_match
	 *
	 * @return array $is_passed
	 * @since    2.3.3
	 *
	 */
	public function wdpad_match_shipping_method_rule__premium_only( $wc_curr_version, $shipping_methods, $general_rule_match ) {
		global $woocommerce;
		$is_passed = array();
		if ( $wc_curr_version >= 3.0 ) {
            $chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
        } else {
            $chosen_shipping_methods = $woocommerce->session->chosen_shipping_methods;
        }
        $chosen_shipping_methods_explode = explode( ':', $chosen_shipping_methods[0] );

        foreach ( $shipping_methods as $key => $method ) {
            if ( $method['product_dpad_conditions_is'] === 'is_equal_to' ) {
                if ( in_array( $chosen_shipping_methods_explode[0], $method['product_dpad_conditions_values'], true ) ) {
                    $is_passed[ $key ]['has_dpad_based_on_shipping_method'] = 'yes';
                } else {
                    $is_passed[ $key ]['has_dpad_based_on_shipping_method'] = 'no';
                }
            }
            if ( $method['product_dpad_conditions_is'] === 'not_in' ) {
                if ( in_array( $chosen_shipping_methods_explode[0], $method['product_dpad_conditions_values'], true ) ) {
                    $is_passed[ $key ]['has_dpad_based_on_shipping_method'] = 'no';
                } else {
                    $is_passed[ $key ]['has_dpad_based_on_shipping_method'] = 'yes';
                }
            }
        }
        $main_is_passed = $this->dpad_check_all_passed_general_rule( $is_passed, 'has_dpad_based_on_shipping_method', $general_rule_match );

		return $main_is_passed;
	}

    /**
	 * Match rule based on shipping method total
	 *
	 * @param int   $wc_curr_version
	 * @param array $shipping_methods
	 *
	 * @return array $is_passed
	 * @since    1.3.3
	 *
	 */
	public function wdpad_match_shipping_method_total_rule__premium_only( $shipping_total_array, $general_rule_match ) {
		global $woocommerce;
		$is_passed = array();
		$shipping_total = ( $woocommerce->cart->get_shipping_total() > 0 && !empty($woocommerce->cart->get_shipping_total()) ) ? $woocommerce->cart->get_shipping_total() : 0;
        $shipping_taxes = $woocommerce->cart->get_shipping_taxes();
        if( !empty($shipping_taxes) ){
            foreach($shipping_taxes as $shipping_tax){
                $shipping_total += $shipping_tax;
            }
        }
        settype( $shipping_total, 'float' );

        foreach ( $shipping_total_array as $key => $shipping ) {
            settype( $shipping['product_dpad_conditions_values'], 'float' );

            if ( $shipping['product_dpad_conditions_is'] === 'is_equal_to' ) {
                if ( ! empty( $shipping['product_dpad_conditions_values'] ) && $shipping['product_dpad_conditions_values'] >= 0 ) {
                    if ( $shipping_total === $shipping['product_dpad_conditions_values'] ) {
                        $is_passed[ $key ]['has_dpad_based_on_shipping_total'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_shipping_total'] = 'no';
                        break;
                    }
                }
            }
            if ( $shipping['product_dpad_conditions_is'] === 'less_equal_to' ) {
                if ( ! empty( $shipping['product_dpad_conditions_values'] ) && $shipping['product_dpad_conditions_values'] >= 0 ) {
                    if ( $shipping['product_dpad_conditions_values'] >= $shipping_total ) {
                        $is_passed[ $key ]['has_dpad_based_on_shipping_total'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_shipping_total'] = 'no';
                        break;
                    }
                }
            }
            if ( $shipping['product_dpad_conditions_is'] === 'less_then' ) {
                if ( ! empty( $shipping['product_dpad_conditions_values'] ) && $shipping['product_dpad_conditions_values'] >= 0 ) {
                    if ( $shipping['product_dpad_conditions_values'] > $shipping_total ) {
                        $is_passed[ $key ]['has_dpad_based_on_shipping_total'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_shipping_total'] = 'no';
                        break;
                    }
                }
            }
            if ( $shipping['product_dpad_conditions_is'] === 'greater_equal_to' ) {
                if ( ! empty( $shipping['product_dpad_conditions_values'] ) && $shipping['product_dpad_conditions_values'] >= 0 ) {
                    if ( $shipping['product_dpad_conditions_values'] <= $shipping_total ) {
                        $is_passed[ $key ]['has_dpad_based_on_shipping_total'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_shipping_total'] = 'no';
                        break;
                    }
                }
            }
            if ( $shipping['product_dpad_conditions_is'] === 'greater_then' ) {
                if ( ! empty( $shipping['product_dpad_conditions_values'] ) && $shipping['product_dpad_conditions_values'] >= 0 ) {
                    if ( $shipping['product_dpad_conditions_values'] < $shipping_total ) {
                        $is_passed[ $key ]['has_dpad_based_on_shipping_total'] = 'yes';
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_shipping_total'] = 'no';
                        break;
                    }
                }
            }
            if ( $shipping['product_dpad_conditions_is'] === 'not_in' ) {
                if ( ! empty( $shipping['product_dpad_conditions_values'] ) && $shipping['product_dpad_conditions_values'] >= 0 ) {
                    if ( $shipping_total === $shipping['product_dpad_conditions_values'] ) {
                        $is_passed[ $key ]['has_dpad_based_on_shipping_total'] = 'no';
                        break;
                    } else {
                        $is_passed[ $key ]['has_dpad_based_on_shipping_total'] = 'yes';
                    }
                }
            }
        }

        $main_is_passed = $this->dpad_check_all_passed_general_rule( $is_passed, 'has_dpad_based_on_shipping_total', $general_rule_match );

		return $main_is_passed;
	}

	/**
	 * Change shipping label when shipping method is taxable
	 *
	 * @param $label
	 * @param $method
	 * @return $label
	 */
	public function wdpad_change_shipping_title( $label, $method){
		$total_tax = 0;
		$current_currency = get_woocommerce_currency_symbol();
		if( !empty($method->get_taxes()) ){
			foreach($method->get_taxes() as $shipping_tax ){
				$total_tax += $shipping_tax;
			}
		}
		if($total_tax > 0){
			$label .= sprintf( wp_kses_post( ' %1$s(Tax: %3$s)%2$s' ), '<strong>', '</strong>', $current_currency.$total_tax );
		}
		return $label;
	}
	
	/**
	 * Check user's have first order or not
	 *
	 * @return boolean $order_check
	 * @since 2.2.0
	 *
	 */
	public function wdpad_check_first_order_for_user__premium_only( $user_id ) {

		$user_id = !empty($user_id) ? $user_id : get_current_user_id();

        $args = array( 
            'customer' => $user_id,
            'status' => array( 'wc-completed', 'wc-processing' ),
            'limit' => 1,
            'return' => 'ids'
        );

        $customer_orders = wc_get_orders( $args );

		// return "true" when customer has already at least one order (false if not)
	   	return count($customer_orders) > 0 ? false : true; 
	}

	/**
	 * Add discount message on product details page after add to cart button
	 *
	 * @param $label
	 * @param $method
	 * @return $label
	 */
	public function wdpad_content_after_addtocart_button(){
        
        //This condition put here because "WooCommerce Product Table" by Barn2 Plugins use same hook to display product list in tabular form
        if( !is_singular() ){
            return;
        }

		global $product;
        $productid = $product->get_id();
        if( $product->is_type('variable') ){
            $variations = $product->get_available_variations();
            $variations_ids = wp_list_pluck( $variations, 'variation_id' );
        }
        
        $get_all_discounts = $this->wdpad_action_on_discount_list();
        foreach( $get_all_discounts as $discount_id ) {
            $getMsgChecked      = get_post_meta( $discount_id, 'dpad_chk_discount_msg', true );
            $getrulestatus      = get_post_meta( $discount_id, 'dpad_settings_status', true );
            $forSpecificProduct = get_post_meta( $discount_id, 'dpad_chk_discount_msg_selected_product', true );
            if( 'on' === $getrulestatus ){
                if( !empty($getMsgChecked) && "on" === $getMsgChecked ){
                    
                    $discount_msg_bg_color = get_post_meta( $discount_id, 'dpad_discount_msg_bg_color', true ) ? get_post_meta( $discount_id, 'dpad_discount_msg_bg_color', true ) : '#ffcaca';
                    $discount_msg_text_color = get_post_meta( $discount_id, 'dpad_discount_msg_text_color', true ) ? get_post_meta( $discount_id, 'dpad_discount_msg_text_color', true ) : '#000000'; 
                    $getDiscountMsg = esc_html__( get_post_meta( $discount_id, 'dpad_discount_msg_text', true ), 'woo-conditional-discount-rules-for-checkout' );
                    $discount_msg_show = false;
                    
                    if( !empty( $forSpecificProduct ) && 'on' === $forSpecificProduct ){
                        $selectedProductList = (array) get_post_meta( $discount_id, 'dpad_selected_product_list', true );
                        if( $product->is_type('variable') ){
                            foreach( $variations_ids as $variations_id ) {
                                if( !empty($getDiscountMsg) && in_array( $variations_id, $selectedProductList, true ) ){
                                    echo sprintf( wp_kses_post( '<div class="dpad_discount_message dpad_variation dpad_variation_%1$d" style="background:%2$s;color:%3$s;"><span>%4$s</span></div>' ), intval($variations_id), esc_html($discount_msg_bg_color), esc_html($discount_msg_text_color), wp_kses_post(html_entity_decode($getDiscountMsg)) );
                                }
                            }
                        } else {
                            if( in_array( $productid, $selectedProductList, true ) ){
                                $discount_msg_show = true;
                            }
                        }
                    } else {
                        $discount_msg_show = true;
                    }

                    if( $discount_msg_show && !empty($getDiscountMsg) ){
                        echo sprintf( wp_kses_post( '<div class="dpad_discount_message" style="background:%s;color:%s"><span>%s</span></div>' ), esc_html($discount_msg_bg_color), esc_html($discount_msg_text_color), wp_kses_post(html_entity_decode($getDiscountMsg)) );
                    }
                }
            }
        }
	}

	/**
	 * Remove taxes from cart discount
	 *
	 * @param $taxes
	 * @param $fee
	 * @param $cart
     * 
     * @return $taxes
	 */
	public function conditional_wdpad_exclude_cart_fees_taxes( $taxes, $fee, $cart ) {
        return [];
	}

	/**
	 * Find a matching zone for a given package.
	 *
	 * @since  2.6.0
	 * @uses   wc_make_numeric_postcode()
	 * @return WC_Shipping_Zone
	 */
    public function wdpad_get_shipping_zone()
    {
        global $wpdb, $woocommerce;

        $country = strtoupper(wc_clean($woocommerce->customer->get_shipping_country()));
        $state = strtoupper(wc_clean($woocommerce->customer->get_shipping_state()));
        $continent = strtoupper(wc_clean(WC()->countries->get_continent_code_for_country($country)));
        $postcode = wc_normalize_postcode(wc_clean($woocommerce->customer->get_shipping_postcode()));
        $cache_key = WC_Cache_Helper::get_cache_prefix('shipping_zones') . 'wc_shipping_zone_' . md5(sprintf('%s+%s+%s', $country, $state, $postcode));
        $matching_zone_id = wp_cache_get($cache_key, 'shipping_zones');

        if (false === $matching_zone_id) {


            // Postcode range and wildcard matching
            $postcode_locations=array();
            $zones = WC_Shipping_Zones::get_zones();
            if(!empty($zones)){
                foreach ($zones as  $zone) {
                    if(!empty($zone['zone_locations'])){
                        foreach ($zone['zone_locations'] as $zone_location) {
                            $location=new stdClass();
                            if('postcode' === $zone_location->type){
                                $location->zone_id=$zone['zone_id'];
                                $location->location_code=$zone_location->code;
                                $postcode_locations[]= $location;   
                            }                        
                        }
                    }
                }                    
            }

            if ($postcode_locations) {
                $zone_ids_with_postcode_rules = array_map('absint', wp_list_pluck($postcode_locations, 'zone_id'));
                $matches = wc_postcode_location_matcher($postcode, $postcode_locations, 'zone_id', 'location_code', $country);
                $do_not_match = array_unique(array_diff($zone_ids_with_postcode_rules, array_keys($matches)));

                if (!empty($do_not_match)) {
                    $criteria =$do_not_match;
                }
            }
            
            // Get matching zones
            // phpcs:disable
            if(!empty($criteria)){
                $matching_zone_id = $wpdb->get_var($wpdb->prepare("
                    SELECT zones.zone_id FROM {$wpdb->prefix}woocommerce_shipping_zones as zones
                    LEFT OUTER JOIN {$wpdb->prefix}woocommerce_shipping_zone_locations as locations ON zones.zone_id = locations.zone_id AND location_type != 'postcode'
                    WHERE ( ( location_type = 'country' AND location_code = %s )
                    OR ( location_type = 'state' AND location_code = %s )
                    OR ( location_type = 'continent' AND location_code = %s )
                    OR ( location_type IS NULL ) )
                    AND zones.zone_id NOT IN (%s)
                    ORDER BY zone_order ASC LIMIT 1
                ",$country,$country . ':' . $state,$continent,implode(',', $do_not_match))); 
            } else {
                $matching_zone_id = $wpdb->get_var($wpdb->prepare("
                    SELECT zones.zone_id FROM {$wpdb->prefix}woocommerce_shipping_zones as zones
                    LEFT OUTER JOIN {$wpdb->prefix}woocommerce_shipping_zone_locations as locations ON zones.zone_id = locations.zone_id AND location_type != 'postcode'
                    WHERE ( ( location_type = 'country' AND location_code = %s )
                    OR ( location_type = 'state' AND location_code = %s )
                    OR ( location_type = 'continent' AND location_code = %s )
                    OR ( location_type IS NULL ) )
                    ORDER BY zone_order ASC LIMIT 1
                ",$country,$country . ':' . $state,$continent));
            }
            // phpcs:enable

            wp_cache_set($cache_key, $matching_zone_id, 'shipping_zones');
        }   

        return $matching_zone_id ? $matching_zone_id : 0;
    }


	public function dpad_array_column( array $input, $columnKey, $indexKey = null ) {
		$array = array();
		foreach ( $input as $value ) {
			if ( ! isset( $value[ $columnKey ] ) ) {

				return false;
			}
			if ( is_null( $indexKey ) ) {
				$array[] = $value[ $columnKey ];
			} else {
				if ( ! isset( $value[ $indexKey ] ) ) {
					
					return false;
				}
				if ( ! is_scalar( $value[ $indexKey ] ) ) {
					
					return false;
				}
				$array[ $value[ $indexKey ] ] = $value[ $columnKey ];
			}
		}

		return $array;
	}

	public function wdpad_array_flatten( $array ) {
		if ( ! is_array( $array ) ) {
			return false;
		}
		$result = array();
		foreach ( $array as $key => $value ) {
			if ( is_array( $value ) ) {
				$result = array_merge( $result, $this->wdpad_array_flatten( $value ) );
			} else {
				$result[ $key ] = $value;
			}
		}

		return $result;
	}

	function dpad_get_woo_version_number() {
		// If get_plugins() isn't available, require it
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		// Create the plugins folder and file variables
		$plugin_folder = get_plugins( '/' . 'woocommerce' );
		$plugin_file   = 'woocommerce.php';

		// If the plugin version number is set, return it
		if ( isset( $plugin_folder[ $plugin_file ]['Version'] ) ) {
			return $plugin_folder[ $plugin_file ]['Version'];
		} else {
			return null;
		}
	}

	/*
     * Get WooCommerce version number
     */

	public function dpad_remove_currency( $price ) {
        $args  = array(
            'decimal_separator'  => wc_get_price_decimal_separator(),
            'thousand_separator' => wc_get_price_thousand_separator(),
        );

        $wc_currency_symbol = get_woocommerce_currency_symbol();
        $cleanText          = wp_strip_all_tags($price);
		$new_price          = str_replace( $wc_currency_symbol, '', $cleanText );

        $tnew_price         = str_replace( $args['thousand_separator'], '', $new_price);
        $dnew_price         = str_replace( $args['decimal_separator'], '.', $tnew_price);
        $new_price2         = preg_replace( '/[^.\d]/', '', $dnew_price );
        
		return $new_price2;
	}

	/*
     * Enable ajax refresh for email field
     */
	function wdpad_trigger_update_checkout_on_change( $fields ) {

		$fields['billing']['billing_email']['class'][] = 'update_totals_on_change';

		return $fields;
	}

	/**
	 * Check order condition for user
	 *
	 * @return boolean $order_check
	 * @since 2.2.0
	 *
	 */
	public function dpad_check_order_for_user__premium_only( $user_id, $count = false ) {

		$user_id = !empty($user_id) ? $user_id : get_current_user_id();

		$numberposts = (!$count) ? 1 : -1;

        $args = array( 
            'customer' => $user_id,
            'status' => array( 'wc-completed', 'wc-processing' ),
            'limit' => $numberposts,
            'return' => 'ids'
        );
        $customer_orders = wc_get_orders( $args );

		// return "true" when customer has already at least one order (false if not)
		$total = 0;
		if(!$count){
			foreach ( $customer_orders as $customer_order ) {
				$order = wc_get_order( $customer_order );
				$total += $order->get_total();
			}
			return $total; 
		} else {
			return count($customer_orders);
		}
	}

    /**
	 * List product ids of user's past orders
	 *
     * @param int    $user_id
     * 
	 * @return boolean $product_ids
	 * @since 2.2.0
	 *
	 */
    public function dpad_get_product_ids_from_order_of_user__premium_only( $user_id ) {

        $user_id = !empty($user_id) ? $user_id : get_current_user_id();

        $product_ids = array();

        $order_status = apply_filters( 'wpdpad_user_repeat_product_order_statuses', array( 'wc-completed', 'wc-processing' ) );
        
        $args = array( 
            'customer' => $user_id,
            'status' => $order_status,
            'limit' => -1,            
        );
        
        $customer_orders = wc_get_orders( $args );
        
        foreach( $customer_orders as $customer_order ){
            $items = $customer_order->get_items();
            foreach ( $items as $item ) {
                $product_id = $item->get_variation_id() > 0 ? $item->get_variation_id() : $item->get_product_id();
                if( $product_id > 0 ){
                    $product_ids[] = $product_id;
                }
            }
        }
        return array_values(array_unique($product_ids));
    }

	/**
	 * Count qty for product based and cart based when apply per qty option is on. This rule will apply when advance pricing rule will disable
	 *
	 * @param int    $fees_id
	 * @param array  $cart_array
	 * @param int    $products_based_qty
	 * @param float  $products_based_subtotal
	 * @param string $sitepress
	 * @param string $default_lang
	 *
	 * @return array $products_based_qty, $products_based_subtotal
	 * @since 2.2.0
	 *
	 * @uses  get_post_meta()
	 * @uses  get_post()
	 * @uses  get_terms()
	 *
	 */
	public function wdpad_product_qty_on_rules_ps( $fees_id, $cart_array, $products_based_qty, $products_based_subtotal, $sitepress, $default_lang ) {
		$get_condition_array = get_post_meta( $fees_id, 'dynamic_pricing_metabox', true );
		$all_rule_check   = array();
		if ( ! empty( $get_condition_array ) ) {
			foreach ( $get_condition_array as $condition ) {
				if ( array_search( 'product', $condition, true ) ) {
					$site_product_id           = '';
					$cart_final_products_array = array();
                    $product_dpad_conditions_values = isset($condition['product_dpad_conditions_values']) && !empty($condition['product_dpad_conditions_values']) ? array_map('intval', $condition['product_dpad_conditions_values'] ) : array();
					// Product Condition Start
					if ( 'is_equal_to' === $condition['product_dpad_conditions_is'] ) {
						if ( ! empty( $product_dpad_conditions_values ) ) {
                            foreach ( $cart_array as $value ) {
                                if ( ! empty( $value['variation_id'] ) && 0 !== $value['variation_id'] ) {
                                    $product_id_lan = $value['variation_id'];
                                } else {
                                    $product_id_lan = $value['product_id'];
                                }
                                $_product = wc_get_product( $product_id_lan );
                                $line_item_subtotal = (float)$this->dpad_remove_currency(WC()->cart->get_product_subtotal( $_product, $value['quantity'] ));
                                if ( ! empty( $sitepress ) ) {
                                    $site_product_id = apply_filters( 'wpml_object_id', $product_id_lan, 'product', true, $default_lang );
                                } else {
                                    $site_product_id = $product_id_lan;
                                }
                                if ( ! ( $_product->is_virtual( 'yes' ) ) && false === strpos( $_product->get_type(), 'bundle' ) ) {
                                    if ( in_array( $site_product_id, $product_dpad_conditions_values, true ) ) {
                                        $prod_qty = $value['quantity'] ? $value['quantity'] : 0;
                                        if( array_key_exists($site_product_id, $cart_final_products_array) ){
                                            $product_data_explode   = explode( "||", $cart_final_products_array[ $site_product_id ] );
                                            $cart_product_qty   	= json_decode( $product_data_explode[0] );
                                            $prod_qty 				+= $cart_product_qty;
                                        }
                                        $cart_final_products_array[ $site_product_id ] = $prod_qty . "||" . $line_item_subtotal;
                                    }
                                } else {
                                    if ( false !== strpos( $_product->get_type(), 'bundle' ) ){
                                        $prod_qty = 0;
                                        $cart_final_products_array[ $site_product_id ] = $prod_qty . "||" . $line_item_subtotal;
                                    }
                                }
                            }
						}
					} elseif ( 'not_in' === $condition['product_dpad_conditions_is'] ) {
						if ( ! empty( $product_dpad_conditions_values ) ) {
                            foreach ( $cart_array as $value ) {
                                if ( ! empty( $value['variation_id'] ) && 0 !== $value['variation_id'] ) {
                                    $product_id_lan = $value['variation_id'];
                                } else {
                                    $product_id_lan = $value['product_id'];
                                }
                                $_product = wc_get_product( $product_id_lan );
                                $line_item_subtotal = (float)$this->dpad_remove_currency(WC()->cart->get_product_subtotal( $_product, $value['quantity'] ));
                                if ( ! empty( $sitepress ) ) {
                                    $site_product_id = apply_filters( 'wpml_object_id', $product_id_lan, 'product', true, $default_lang );
                                } else {
                                    $site_product_id = $product_id_lan;
                                }
                                if ( ! ( $_product->is_virtual( 'yes' ) ) && false === strpos( $_product->get_type(), 'bundle' ) ) {
                                    if ( ! in_array( $site_product_id, $product_dpad_conditions_values, true ) ) {
                                        $prod_qty = $value['quantity'] ? $value['quantity'] : 0;
                                        if( array_key_exists($site_product_id, $cart_final_products_array) ){
                                            $product_data_explode   = explode( "||", $cart_final_products_array[ $site_product_id ] );
                                            $cart_product_qty   	= json_decode( $product_data_explode[0] );
                                            $prod_qty 				+= $cart_product_qty;
                                        } 
                                        $cart_final_products_array[ $site_product_id ] = $prod_qty . "||" . $line_item_subtotal;
                                    }
                                } else {
                                    if ( false !== strpos( $_product->get_type(), 'bundle' ) ){
                                        $prod_qty = 0;
                                        $cart_final_products_array[ $site_product_id ] = $prod_qty . "||" . $line_item_subtotal;
                                    }
                                }
                            }
						}
					}
					if ( ! empty( $cart_final_products_array ) ) {
						foreach ( $cart_final_products_array as $prd_id => $cart_item ) {
							$cart_item_explode                     = explode( "||", $cart_item );
							$all_rule_check[ $prd_id ]['qty']      = $cart_item_explode[0];
							$all_rule_check[ $prd_id ]['subtotal'] = $cart_item_explode[1];
						}
					}
					// Product Condition End
				}
				if ( array_search( 'variableproduct', $condition, true ) ) {
					$site_product_id               = '';
					$cart_final_var_products_array = array();
                    $product_dpad_conditions_values = isset($condition['product_dpad_conditions_values']) && !empty($condition['product_dpad_conditions_values']) ? array_map('intval', $condition['product_dpad_conditions_values'] ) : array();
					// Variable Product Condition Start
					if ( 'is_equal_to' === $condition['product_dpad_conditions_is'] ) {
						if ( ! empty( $product_dpad_conditions_values ) ) {
                            foreach ( $cart_array as $value ) {
                                if ( ! empty( $value['variation_id'] ) && 0 !== $value['variation_id'] ) {
                                    $product_id_lan = $value['variation_id'];
                                } else {
                                    $product_id_lan = $value['product_id'];
                                }
                                $_product = wc_get_product( $product_id_lan );
                                $line_item_subtotal = (float)$this->dpad_remove_currency(WC()->cart->get_product_subtotal( $_product, $value['quantity'] ));
                                if ( ! empty( $sitepress ) ) {
                                    $site_product_id = apply_filters( 'wpml_object_id', $product_id_lan, 'product', true, $default_lang );
                                } else {
                                    $site_product_id = $product_id_lan;
                                }
                                if ( ! ( $_product->is_virtual( 'yes' ) ) && false === strpos( $_product->get_type(), 'bundle' ) ) {
                                    if ( in_array( $site_product_id, $product_dpad_conditions_values, true ) ) {
                                        $prod_qty = $value['quantity'] ? $value['quantity'] : 0;
                                        $cart_final_var_products_array[$site_product_id] = $prod_qty . "||" . $line_item_subtotal;
                                    }
                                } else {
                                    if ( false !== strpos( $_product->get_type(), 'bundle' ) ){
                                        $prod_qty = 0;
                                        $cart_final_var_products_array[$site_product_id] = $prod_qty . "||" . $line_item_subtotal;
                                    }
                                }
                            }
						}
					} elseif ( 'not_in' === $condition['product_dpad_conditions_is'] ) {
						if ( ! empty( $product_dpad_conditions_values ) ) {
                            foreach ( $cart_array as $value ) {
                                if ( ! empty( $value['variation_id'] ) && 0 !== $value['variation_id'] ) {
                                    $product_id_lan = $value['variation_id'];
                                } else {
                                    $product_id_lan = $value['product_id'];
                                }
                                $_product = wc_get_product( $product_id_lan );
                                $line_item_subtotal = (float)$this->dpad_remove_currency(WC()->cart->get_product_subtotal( $_product, $value['quantity'] ));
                                if ( ! empty( $sitepress ) ) {
                                    $site_product_id = apply_filters( 'wpml_object_id', $product_id_lan, 'product', true, $default_lang );
                                } else {
                                    $site_product_id = $product_id_lan;
                                }
                                if ( ! ( $_product->is_virtual( 'yes' ) ) && false === strpos( $_product->get_type(), 'bundle' ) ) {
                                    if ( ! in_array( $site_product_id, $product_dpad_conditions_values, true ) ) {
                                        $prod_qty = $value['quantity'] ? $value['quantity'] : 0;
                                        $cart_final_var_products_array[$site_product_id] = $prod_qty . "||" . $line_item_subtotal;
                                    }
                                } else {
                                    if ( false !== strpos( $_product->get_type(), 'bundle' ) ){
                                        $prod_qty = 0;
                                        $cart_final_var_products_array[$site_product_id] = $prod_qty . "||" . $line_item_subtotal;
                                    }
                                }
                            }
						}
					}
					if ( ! empty( $cart_final_var_products_array ) ) {
						foreach ( $cart_final_var_products_array as $prd_id => $cart_item ) {
							$cart_item_explode                     = explode( "||", $cart_item );
							$all_rule_check[ $prd_id ]['qty']      = $cart_item_explode[0];
							$all_rule_check[ $prd_id ]['subtotal'] = $cart_item_explode[1];
						}
					}
					// Variable Product Condition End
				}
				if ( array_search( 'category', $condition, true ) ) {
                    // Category Condition Start
					$final_cart_products_cats_ids  = array();
					$cart_final_cat_products_array = array();
                    $product_dpad_conditions_values = isset($condition['product_dpad_conditions_values']) && !empty($condition['product_dpad_conditions_values']) ? array_map('intval', $condition['product_dpad_conditions_values'] ) : array();
					$all_cats                      = get_terms(
						array(
							'taxonomy' => 'product_cat',
							'fields'   => 'ids',
						)
					);
					if ( 'is_equal_to' === $condition['product_dpad_conditions_is'] ) {
						if ( ! empty( $product_dpad_conditions_values ) ) {
							foreach ( $product_dpad_conditions_values as $category_id ) {
								settype( $category_id, 'integer' );
								$final_cart_products_cats_ids[] = $category_id;
							}
						}
					} elseif ( 'not_in' === $condition['product_dpad_conditions_is'] ) {
						if ( ! empty( $product_dpad_conditions_values ) ) {
							$final_cart_products_cats_ids = array_diff( $all_cats, $product_dpad_conditions_values );
						}
					}
					$final_cart_products_cats_ids = array_map( 'intval', $final_cart_products_cats_ids );
					$terms            = array();
					$cart_value_array = array();
					foreach ( $cart_array as $value ) {
						if ( ! empty( $value['variation_id'] ) && 0 !== $value['variation_id'] ) {
							$product_id = $value['variation_id'];
						} else {
							$product_id = $value['product_id'];
						}
						$_product = wc_get_product( $product_id );
                        $line_item_subtotal = (float)$this->dpad_remove_currency(WC()->cart->get_product_subtotal( $_product, $value['quantity'] ));
						$cart_value_array[] = $value;
						$term_ids           = wp_get_post_terms( $value['product_id'], 'product_cat', array( 'fields' => 'ids' ) );
						foreach ( $term_ids as $term_id ) {
							$prod_qty = $value['quantity'] ? $value['quantity'] : 0;
							if( false !== strpos( $_product->get_type(), 'bundle' ) ){
								$prod_qty = 0;
							}
							$product_id                       = ( $value['variation_id'] ) ? $value['variation_id'] : $product_id;
							if ( in_array( $term_id, $final_cart_products_cats_ids, true ) ) {
								if( array_key_exists($product_id,$terms) && array_key_exists($term_id,$terms[$product_id]) ){
									$term_data_explode  = explode( "||", $terms[ $product_id ][ $term_id ] );
									$cart_term_qty      = json_decode( $term_data_explode[0] );
									$prod_qty += $cart_term_qty;
								}
								$terms[ $product_id ][ $term_id ] = $prod_qty . "||" . $line_item_subtotal;
							}
						}
					}
					foreach ( $terms as $cart_product_key => $main_term_data ) {
						foreach ( $main_term_data as $cart_term_id => $term_data ) {
							$term_data_explode  = explode( "||", $term_data );
							$cart_term_qty      = json_decode( $term_data_explode[0] );
							$cart_term_subtotal = json_decode( $term_data_explode[1] );
							if ( in_array( $cart_term_id, $final_cart_products_cats_ids, true ) ) {
								$cart_final_cat_products_array[ $cart_product_key ][ $cart_term_id ] = $cart_term_qty . "||" . $cart_term_subtotal;
							}
						}
					}
					if ( ! empty( $cart_final_cat_products_array ) ) {
						foreach ( $cart_final_cat_products_array as $prd_id => $main_cart_item ) {
							foreach ( $main_cart_item as $term_id => $cart_item ) {
								$cart_item_explode                     = explode( "||", $cart_item );
								$all_rule_check[ $prd_id ]['qty']      = $cart_item_explode[0];
								$all_rule_check[ $prd_id ]['subtotal'] = $cart_item_explode[1];
							}
						}
					}
                    // Category Condition End
				}
				if ( array_search( 'tag', $condition, true ) ) {
					// Tag Condition Start
					$final_cart_products_tag_ids   = array();
					$cart_final_tag_products_array = array();
                    $product_dpad_conditions_values = isset($condition['product_dpad_conditions_values']) && !empty($condition['product_dpad_conditions_values']) ? array_map('intval', $condition['product_dpad_conditions_values'] ) : array();
					$all_tags                      = get_terms(
						array(
							'taxonomy' => 'product_tag',
							'fields'   => 'ids',
						)
					);
					if ( 'is_equal_to' === $condition['product_dpad_conditions_is'] ) {
						if ( ! empty( $product_dpad_conditions_values ) ) {
							foreach ( $product_dpad_conditions_values as $tag_id ) {
								$final_cart_products_tag_ids[] = $tag_id;
							}
						}
					} elseif ( 'not_in' === $condition['product_dpad_conditions_is'] ) {
						if ( ! empty( $product_dpad_conditions_values ) ) {
							$final_cart_products_tag_ids = array_diff( $all_tags, $product_dpad_conditions_values );
						}
					}
					$final_cart_products_tag_ids = array_map( 'intval', $final_cart_products_tag_ids );
					$tags                        = array();
					$cart_value_array            = array();
					foreach ( $cart_array as $value ) {
						if ( ! empty( $value['variation_id'] ) && 0 !== $value['variation_id'] ) {
							$product_id = $value['variation_id'];
						} else {
							$product_id = $value['product_id'];
						}
						$_product = wc_get_product( $product_id );
                        $line_item_subtotal = (float)$this->dpad_remove_currency(WC()->cart->get_product_subtotal( $_product, $value['quantity'] ));
						$cart_value_array[] = $value;
						$tag_ids            = wp_get_post_terms( $value['product_id'], 'product_tag', array( 'fields' => 'ids' ) );
						foreach ( $tag_ids as $tag_id ) {
							$prod_qty = $value['quantity'] ? $value['quantity'] : 0;
							if( false !== strpos( $_product->get_type(), 'bundle' ) ){
								$prod_qty = 0;
							}
							$product_id                       = ( $value['variation_id'] ) ? $value['variation_id'] : $product_id;
							if ( in_array( $tag_id, $final_cart_products_tag_ids, true ) ) {
								if( array_key_exists($product_id,$tags) && array_key_exists($tag_id,$tags[$product_id]) ){
									$term_data_explode  = explode( "||", $tags[ $product_id ][ $tag_id ] );
									$cart_term_qty      = json_decode( $term_data_explode[0] );
									$prod_qty += $cart_term_qty;
								}
								$tags[ $product_id ][ $tag_id ] = $prod_qty . "||" . $line_item_subtotal;
							}
						}
					}
					foreach ( $tags as $cart_product_key => $main_tag_data ) {
						foreach ( $main_tag_data as $cart_tag_id => $tag_data ) {
							$tag_data_explode  = explode( "||", $tag_data );
							$cart_tag_qty      = json_decode( $tag_data_explode[0] );
							$cart_tag_subtotal = json_decode( $tag_data_explode[1] );
							if ( ! empty( $final_cart_products_tag_ids ) ) {
								if ( in_array( $cart_tag_id, $final_cart_products_tag_ids, true ) ) {
									$cart_final_tag_products_array[ $cart_product_key ][ $cart_tag_id ] = $cart_tag_qty . "||" . $cart_tag_subtotal;
								}
							}
						}
					}
					if ( ! empty( $cart_final_tag_products_array ) ) {
						foreach ( $cart_final_tag_products_array as $prd_id => $main_cart_item ) {
							foreach ( $main_cart_item as $term_id => $cart_item ) {
								$cart_item_explode                     = explode( "||", $cart_item );
								$all_rule_check[ $prd_id ]['qty']      = $cart_item_explode[0];
								$all_rule_check[ $prd_id ]['subtotal'] = $cart_item_explode[1];
							}
						}
					}
				}
			}
		}
        
		if ( ! empty( $all_rule_check ) ) {
			foreach ( $all_rule_check as $cart_item ) {
				$products_based_qty      += isset($cart_item['qty'])?$cart_item['qty']:0;
				$products_based_subtotal += isset($cart_item['subtotal'])?$cart_item['subtotal']:0;
			}
		}
		if ( 0 === $products_based_qty ) {
			$products_based_qty = 1;
		}
		return array( $products_based_qty, $products_based_subtotal );
	}

	/**
	 * Count product based and cart based when apply per count option is on. This rule will apply when advance pricing rule will disable
	 *
	 * @param int    $fees_id
	 * @param array  $cart_array
	 * @param int    $products_based_qty
	 * @param float  $products_based_subtotal
	 * @param string $sitepress
	 * @param string $default_lang
	 *
	 * @return array $products_based_qty, $products_based_subtotal
	 * @since 2.2.0
	 *
	 * @uses  get_post_meta()
	 * @uses  get_post()
	 * @uses  get_terms()
	 *
	 */
	public function dpad_product_count_on_rules_ps( $fees_id, $cart_array, $products_based_qty, $products_based_subtotal, $sitepress, $default_lang ) {
		$get_condition_array = get_post_meta( $fees_id, 'dynamic_pricing_metabox', true );
		$final_count = 0;
		if ( ! empty( $get_condition_array ) ) {
			foreach ( $get_condition_array as $condition ) {
				if ( array_search( 'product', $condition, true ) ) {
					// Product Condition Start
					$site_product_id           = '';
					$cart_final_products_array = array();
                    $product_dpad_conditions_values = isset($condition['product_dpad_conditions_values']) && !empty($condition['product_dpad_conditions_values']) ? array_map('intval', $condition['product_dpad_conditions_values'] ) : array();
					if ( 'is_equal_to' === $condition['product_dpad_conditions_is'] ) {
						if ( ! empty( $product_dpad_conditions_values ) ) {
							foreach ( $cart_array as $value ) {
								if ( ! empty( $value['variation_id'] ) && 0 !== $value['variation_id'] ) {
									$product_id_lan = $value['variation_id'];
								} else {
									$product_id_lan = $value['product_id'];
								}
								$_product = wc_get_product( $product_id_lan );
								$line_item_subtotal = (float) $value['line_subtotal'] + (float) $value['line_subtotal_tax'];
								if ( ! empty( $sitepress ) ) {
									$site_product_id = apply_filters( 'wpml_object_id', $product_id_lan, 'product', true, $default_lang );
								} else {
									$site_product_id = $product_id_lan;
								}
								if ( ! ( $_product->is_virtual( 'yes' ) ) && false === strpos( $_product->get_type(), 'bundle' ) ) {
									if ( in_array( $site_product_id, $product_dpad_conditions_values, true ) ) {
                                        if( ! array_key_exists( $site_product_id, $cart_final_products_array ) ) {
                                            $final_count++;
                                            $cart_final_products_array[ $site_product_id ] = $final_count;
                                        }
									}
								}
							}
						}
					} elseif ( 'not_in' === $condition['product_dpad_conditions_is'] ) {
						if ( ! empty( $product_dpad_conditions_values ) ) {
							foreach ( $cart_array as $value ) {
								if ( ! empty( $value['variation_id'] ) && 0 !== $value['variation_id'] ) {
									$product_id_lan = $value['variation_id'];
								} else {
									$product_id_lan = $value['product_id'];
								}
								$_product = wc_get_product( $product_id_lan );
								$line_item_subtotal = (float) $value['line_subtotal'] + (float) $value['line_subtotal_tax'];
								if ( ! empty( $sitepress ) ) {
									$site_product_id = apply_filters( 'wpml_object_id', $product_id_lan, 'product', true, $default_lang );
								} else {
									$site_product_id = $product_id_lan;
								}
								if ( ! ( $_product->is_virtual( 'yes' ) ) && false === strpos( $_product->get_type(), 'bundle' ) ) {
									if ( ! in_array( $site_product_id, $product_dpad_conditions_values, true ) ) {
                                        if( ! array_key_exists( $site_product_id, $cart_final_products_array ) ) {
                                            $final_count++;
                                            $cart_final_products_array[ $site_product_id ] = $final_count;
                                        }
									}
								}
							}
						}
					}
				}
				if ( array_search( 'variableproduct', $condition, true ) ) {
					// Variable Product Condition Start
					$site_product_id               = '';
					$cart_final_var_products_array = array();
                    $product_dpad_conditions_values = isset($condition['product_dpad_conditions_values']) && !empty($condition['product_dpad_conditions_values']) ? array_map('intval', $condition['product_dpad_conditions_values'] ) : array();
					if ( 'is_equal_to' === $condition['product_dpad_conditions_is'] ) {
						if ( ! empty( $product_dpad_conditions_values ) ) {
							foreach ( $cart_array as $value ) {
								if ( ! empty( $value['variation_id'] ) && 0 !== $value['variation_id'] ) {
									$product_id_lan = $value['variation_id'];
								} else {
									$product_id_lan = $value['product_id'];
								}
								$_product = wc_get_product( $product_id_lan );
								$line_item_subtotal = (float) $value['line_subtotal'] + (float) $value['line_subtotal_tax'];
								if ( ! empty( $sitepress ) ) {
									$site_product_id = apply_filters( 'wpml_object_id', $product_id_lan, 'product', true, $default_lang );
								} else {
									$site_product_id = $product_id_lan;
								}
								if ( ! ( $_product->is_virtual( 'yes' ) ) && false === strpos( $_product->get_type(), 'bundle' ) ) {
									if ( in_array( $site_product_id, $product_dpad_conditions_values, true ) ) {
                                        if( ! array_key_exists( $site_product_id, $cart_final_var_products_array ) ) {
                                            $final_count++;
                                            $cart_final_var_products_array[ $site_product_id ] = $final_count;
                                        }
									}
								}
							}
						}
					} elseif ( 'not_in' === $condition['product_dpad_conditions_is'] ) {
						if ( ! empty( $product_dpad_conditions_values ) ) {
							foreach ( $cart_array as $value ) {
								if ( ! empty( $value['variation_id'] ) && 0 !== $value['variation_id'] ) {
									$product_id_lan = $value['variation_id'];
								} else {
									$product_id_lan = $value['product_id'];
								}
								$_product = wc_get_product( $product_id_lan );
								$line_item_subtotal = (float) $value['line_subtotal'] + (float) $value['line_subtotal_tax'];
								if ( ! empty( $sitepress ) ) {
									$site_product_id = apply_filters( 'wpml_object_id', $product_id_lan, 'product', true, $default_lang );
								} else {
									$site_product_id = $product_id_lan;
								}
								if ( ! ( $_product->is_virtual( 'yes' ) ) && false === strpos( $_product->get_type(), 'bundle' ) ) {
									if ( ! in_array( $site_product_id, $product_dpad_conditions_values, true ) ) {
                                        if( ! array_key_exists( $site_product_id, $cart_final_var_products_array ) ) {
                                            $final_count++;
                                            $cart_final_var_products_array[ $site_product_id ] = $final_count;
                                        }
									}
								}
							}
						}
					}
					// Variable Product Condition End
				}
				if ( array_search( 'category', $condition, true ) ) {
					$final_cart_products_cats_ids   = array();
                    $product_dpad_conditions_values = isset($condition['product_dpad_conditions_values']) && !empty($condition['product_dpad_conditions_values']) ? array_map('intval', $condition['product_dpad_conditions_values'] ) : array();
                    
					$all_cats                      = get_terms(
						array(
							'taxonomy' => 'product_cat',
							'fields'   => 'ids',
						)
					);
					if ( 'is_equal_to' === $condition['product_dpad_conditions_is'] ) {
						if ( ! empty( $product_dpad_conditions_values ) ) {
							foreach ( $product_dpad_conditions_values as $category_id ) {
								settype( $category_id, 'integer' );
								$final_cart_products_cats_ids[] = $category_id;
							}
						}
					} elseif ( 'not_in' === $condition['product_dpad_conditions_is'] ) {
						if ( ! empty( $product_dpad_conditions_values ) ) {
							$final_cart_products_cats_ids = array_diff( $all_cats, $product_dpad_conditions_values );
						}
					}
					$final_cart_products_cats_ids = array_map( 'intval', $final_cart_products_cats_ids );
					
					$cart_value_array = array();
                    $cart_final_products_array = array();
					foreach ( $cart_array as $value ) {
						if ( ! empty( $value['variation_id'] ) && 0 !== $value['variation_id'] ) {
							$product_id = $value['variation_id'];
						} else {
							$product_id = $value['product_id'];
						}
						$_product = wc_get_product( $product_id );
						$line_item_subtotal = (float) $value['line_subtotal'] + (float) $value['line_subtotal_tax'];
						$cart_value_array[] = $value;
						$term_ids           = wp_get_post_terms( $value['product_id'], 'product_cat', array( 'fields' => 'ids' ) );
						
						foreach ( $term_ids as $term_id ) {
							$product_id = ( $value['variation_id'] ) ? $value['variation_id'] : $product_id;
							if ( in_array( $term_id, $final_cart_products_cats_ids, true ) ) {
                                if( ! array_key_exists( $product_id, $cart_final_products_array ) ) {
                                    $final_count++;
                                    $cart_final_products_array[ $product_id ] = $final_count;
                                }
							}
						}
					}
				}
				if ( array_search( 'tag', $condition, true ) ) {
					// Tag Condition Start
					$final_cart_products_tag_ids    = array();
                    $product_dpad_conditions_values = isset($condition['product_dpad_conditions_values']) && !empty($condition['product_dpad_conditions_values']) ? array_map('intval', $condition['product_dpad_conditions_values'] ) : array();
					$all_tags                       = get_terms(
						array(
							'taxonomy' => 'product_tag',
							'fields'   => 'ids',
						)
					);
					if ( 'is_equal_to' === $condition['product_dpad_conditions_is'] ) {
						if ( ! empty( $product_dpad_conditions_values ) ) {
							foreach ( $product_dpad_conditions_values as $tag_id ) {
								$final_cart_products_tag_ids[] = $tag_id;
							}
						}
					} elseif ( 'not_in' === $condition['product_dpad_conditions_is'] ) {
						if ( ! empty( $product_dpad_conditions_values ) ) {
							$final_cart_products_tag_ids = array_diff( $all_tags, $product_dpad_conditions_values );
						}
					}
					$final_cart_products_tag_ids = array_map( 'intval', $final_cart_products_tag_ids );
					$cart_value_array            = array();
                    $cart_final_products_array   = array();
					foreach ( $cart_array as $value ) {
						if ( ! empty( $value['variation_id'] ) && 0 !== $value['variation_id'] ) {
							$product_id = $value['variation_id'];
						} else {
							$product_id = $value['product_id'];
						}
						$_product = wc_get_product( $product_id );
						$line_item_subtotal = (float) $value['line_subtotal'] + (float) $value['line_subtotal_tax'];
						$cart_value_array[] = $value;
						$tag_ids            = wp_get_post_terms( $value['product_id'], 'product_tag', array( 'fields' => 'ids' ) );
						foreach ( $tag_ids as $tag_id ) {
							$product_id                       = ( $value['variation_id'] ) ? $value['variation_id'] : $product_id;
							if ( in_array( $tag_id, $final_cart_products_tag_ids, true ) ) {
                                if( ! array_key_exists( $product_id, $cart_final_products_array ) ) {
                                    $final_count++;
                                    $cart_final_products_array[ $product_id ] = $final_count;
                                }
							}
						}
					}
				}
			}
		}
		return $final_count;
	}

	/**
	 * Find unique id based on given array
	 *
	 * @param array  $is_passed
	 * @param string $has_fee_based
	 * @param string $general_rule_match
	 *
	 * @return string $main_is_passed
	 * @since    3.6
	 *
	 */
	public function dpad_check_all_passed_general_rule( $is_passed, $has_fee_based, $general_rule_match ) {
		$main_is_passed = 'no';
		$flag           = array();
		if ( ! empty( $is_passed ) ) {
			foreach ( $is_passed as $key => $is_passed_value ) {
				if ( 'yes' === $is_passed_value[ $has_fee_based ] ) {
					$flag[ $key ] = true;
				} else {
					$flag[ $key ] = false;
				}
			}
			if ( 'any' === $general_rule_match ) {
				if ( in_array( true, $flag, true ) ) {
					$main_is_passed = 'yes';
				} else {
					$main_is_passed = 'no';
				}
			} else {
				if ( in_array( false, $flag, true ) ) {
					$main_is_passed = 'no';
				} else {
					$main_is_passed = 'yes';
				}
			}
		}

		return $main_is_passed;
	}

	/**
	 * Match product per qty rules
	 *
	 * @param array  $get_condition_array_ap_product
	 * @param array  $cart_products_array
	 * @param string $default_lang
	 *
	 * @return array $is_passed_advance_rule
	 * @since    1.3.3
	 *
	 * @uses     wdpad_count_qty_for_product()
	 *
	 */
	public function wdpad_match_product_per_qty__premium_only( $get_condition_array_ap_product, $woo_cart_array, $sitepress, $default_lang, $cost_on_product_rule_match ) {
		if ( ! empty( $woo_cart_array ) ) {
			$is_passed_from_here_prd = array();
			if ( ! empty( $get_condition_array_ap_product ) || '' !== $get_condition_array_ap_product ) {
				foreach ( $get_condition_array_ap_product as $key => $get_condition ) {
					if ( ! empty( $get_condition['ap_fees_products'] ) || '' !== $get_condition['ap_fees_products'] ) {
						$total_qws                 = $this->wdpad_get_count_qty__premium_only(
							$get_condition['ap_fees_products'], $woo_cart_array, $sitepress, $default_lang, 'product', 'qty'
						);
						$get_min_max               = $this->wdpad_check_min_max_qws__premium_only(
							$get_condition['ap_fees_ap_prd_min_qty'], $get_condition['ap_fees_ap_prd_max_qty'], $get_condition['ap_fees_ap_price_product'], 'qty'
						);
						$is_passed_from_here_prd[] = $this->wdpad_check_passed_rule__premium_only(
							$key, $get_min_max['min'], $get_min_max['max'], 'has_fee_based_on_cost_per_prd_qty', 'has_fee_based_on_cost_per_prd_price', $get_condition['ap_fees_ap_price_product'], $total_qws, 'qty'
						);
					}
				}
			}
			$main_is_passed = $this->wdpad_check_all_passed_advance_rule__premium_only(
				$is_passed_from_here_prd, 'has_fee_based_on_cost_per_prd_qty', 'has_fee_based_on_cost_per_prd_price', $cost_on_product_rule_match
			);
			
			return $main_is_passed;
		}
	}

    /**
	 * Cost for Product subtotal in advance pricing rules
	 *
	 * @param array  $get_condition_array_ap_product_subtotal
	 * @param array  $woo_cart_array
	 * @param string $sitepress
	 * @param string $default_lang
	 * @param string $cost_on_product_subtotal_rule_match
	 *
	 * @return array $main_is_passed
	 * @since 2.3.3
	 *
	 * @uses  WC_Cart::get_cart_contents_total()
	 * @uses  wp_get_post_terms()
	 * @uses  wc_get_product()
	 *
	 */
	public function wdpad_match_product_subtotal__premium_only( $get_condition_array_ap_product_subtotal, $woo_cart_array, $sitepress, $default_lang, $cost_on_product_subtotal_rule_match ) {
		if ( ! empty( $woo_cart_array ) ) {
			$is_passed_from_here_ps = array();
			if ( ! empty( $get_condition_array_ap_product_subtotal ) || '' !== $get_condition_array_ap_product_subtotal ) {
				foreach ( $get_condition_array_ap_product_subtotal as $key => $get_condition ) {
					$total_qws                = $this->wdpad_get_count_qty__premium_only(
						$get_condition['ap_fees_product_subtotal'], $woo_cart_array, $sitepress, $default_lang, 'product', 'subtotal'
					);
					$get_min_max              = $this->wdpad_check_min_max_qws__premium_only(
						$get_condition['ap_fees_ap_product_subtotal_min_subtotal'], $get_condition['ap_fees_ap_product_subtotal_max_subtotal'], $get_condition['ap_fees_ap_price_product_subtotal'], 'subtotal'
					);
					$is_passed_from_here_ps[] = $this->wdpad_check_passed_rule__premium_only(
						$key, $get_min_max['min'], $get_min_max['max'], 'has_fee_based_on_ps', 'has_fee_based_on_ps_price', $get_condition['ap_fees_ap_price_product_subtotal'], $total_qws, 'subtotal'
					);
				}
			}
			$main_is_passed = $this->wdpad_check_all_passed_advance_rule__premium_only(
				$is_passed_from_here_ps, 'has_fee_based_on_ps', 'has_fee_based_on_ps_price', $cost_on_product_subtotal_rule_match
			);

			return $main_is_passed;
		}
	}

    /**
	 * Match product per weight rules
	 *
	 * @param array  $get_condition_array_ap_product_weight
	 * @param array  $cart_products_array
	 * @param string $default_lang
     * @param string $cost_on_product_weight_rule_match
	 *
	 * @return array $is_passed_advance_rule
	 * @since    2.3.3
	 *
	 * @uses     wdpad_get_count_qty__premium_only()
	 * @uses     wdpad_check_min_max_qws__premium_only()
	 * @uses     wdpad_check_passed_rule__premium_only()
	 * @uses     wdpad_check_all_passed_advance_rule__premium_only()
	 *
	 */
	public function wdpad_match_product_per_weight__premium_only( $get_condition_array_ap_product_weight, $woo_cart_array, $sitepress, $default_lang, $cost_on_product_weight_rule_match ) {
		if ( ! empty( $woo_cart_array ) ) {
			$is_passed_from_here_prd = array();
			if ( ! empty( $get_condition_array_ap_product_weight ) || '' !== $get_condition_array_ap_product_weight ) {
				foreach ( $get_condition_array_ap_product_weight as $key => $get_condition ) {
					if ( ! empty( $get_condition['ap_fees_product_weight'] ) || '' !== $get_condition['ap_fees_product_weight'] ) {
						$total_qws                 = $this->wdpad_get_count_qty__premium_only(
							$get_condition['ap_fees_product_weight'], $woo_cart_array, $sitepress, $default_lang, 'product', 'weight'
						);
						$get_min_max               = $this->wdpad_check_min_max_qws__premium_only(
							$get_condition['ap_fees_ap_product_weight_min_qty'], $get_condition['ap_fees_ap_product_weight_max_qty'], $get_condition['ap_fees_ap_price_product_weight'], 'weight'
						);
						$is_passed_from_here_prd[] = $this->wdpad_check_passed_rule__premium_only(
							$key, $get_min_max['min'], $get_min_max['max'], 'has_fee_based_on_cost_ppw', 'has_fee_based_on_cost_ppw_price', $get_condition['ap_fees_ap_price_product_weight'], $total_qws, 'weight'
						);
					}
				}
			}
			$main_is_passed = $this->wdpad_check_all_passed_advance_rule__premium_only(
				$is_passed_from_here_prd, 'has_fee_based_on_cost_ppw', 'has_fee_based_on_cost_ppw_price', $cost_on_product_weight_rule_match
			);

			return $main_is_passed;
		}
	}

	/**
	 * Match category per qty rules
	 *
	 * @param array  $get_condition_array_ap_category
	 * @param array  $cart_products_array
	 * @param string $default_lang
	 *
	 * @return array $is_passed_advance_rule
	 * @uses     wdpad_get_count_qty__premium_only()
	 * @uses     wdpad_check_min_max_qws__premium_only()
	 * @uses     wdpad_check_passed_rule__premium_only()
	 * @uses     wdpad_check_all_passed_advance_rule__premium_only()
	 *
	 * @since    2.3.3
	 *
	 */
	public function wdpad_match_category_per_qty__premium_only( $get_condition_array_ap_category, $woo_cart_array, $sitepress, $default_lang, $cost_on_category_rule_match ) {
		if ( ! empty( $woo_cart_array ) ) {
			$is_passed_from_here_cat = array();
			if ( ! empty( $get_condition_array_ap_category ) || '' !== $get_condition_array_ap_category ) {
				foreach ( $get_condition_array_ap_category as $key => $get_condition ) {
					if ( ! empty( $get_condition['ap_fees_categories'] ) || '' !== $get_condition['ap_fees_categories'] ) {
						$total_qws                 = $this->wdpad_get_count_qty__premium_only(
							$get_condition['ap_fees_categories'], $woo_cart_array, $sitepress, $default_lang, 'category', 'qty'
						);
						$get_min_max               = $this->wdpad_check_min_max_qws__premium_only(
							$get_condition['ap_fees_ap_cat_min_qty'], $get_condition['ap_fees_ap_cat_max_qty'], $get_condition['ap_fees_ap_price_category'], 'qty'
						);
						$is_passed_from_here_cat[] = $this->wdpad_check_passed_rule__premium_only(
							$key, $get_min_max['min'], $get_min_max['max'], 'has_fee_based_on_per_category', 'has_fee_based_on_cost_per_cat_price', $get_condition['ap_fees_ap_price_category'], $total_qws, 'qty'
						);
					}
				}
			}
			$main_is_passed = $this->wdpad_check_all_passed_advance_rule__premium_only(
				$is_passed_from_here_cat, 'has_fee_based_on_per_category', 'has_fee_based_on_cost_per_cat_price', $cost_on_category_rule_match
			);

			return $main_is_passed;
		}
	}

    /**
	 * Cost for Category subtotal in advance pricing rules
	 *
	 * @param array  $get_condition_array_ap_category_subtotal
	 * @param array  $woo_cart_array
	 * @param string $sitepress
	 * @param string $default_lang
	 * @param string $cost_on_category_subtotal_rule_match
	 *
	 * @return array $main_is_passed
	 * @since 2.3.3
	 *
	 * @uses  WC_Cart::get_cart_contents_total()
	 * @uses  wp_get_post_terms()
	 * @uses  wc_get_product()
	 *
	 */
	public function wdpad_match_category_subtotal__premium_only( $get_condition_array_ap_category_subtotal, $woo_cart_array, $sitepress, $default_lang, $cost_on_category_subtotal_rule_match ) {
		if ( ! empty( $woo_cart_array ) ) {
			$is_passed_from_here_cs = array();
			if ( ! empty( $get_condition_array_ap_category_subtotal ) || '' !== $get_condition_array_ap_category_subtotal ) {
				foreach ( $get_condition_array_ap_category_subtotal as $key => $get_condition ) {
					$total_qws                = $this->wdpad_get_count_qty__premium_only(
						$get_condition['ap_fees_category_subtotal'], $woo_cart_array, $sitepress, $default_lang, 'category', 'subtotal'
					);
					$get_min_max              = $this->wdpad_check_min_max_qws__premium_only(
						$get_condition['ap_fees_ap_category_subtotal_min_subtotal'], $get_condition['ap_fees_ap_category_subtotal_max_subtotal'], $get_condition['ap_fees_ap_price_category_subtotal'], 'subtotal'
					);
					$is_passed_from_here_cs[] = $this->wdpad_check_passed_rule__premium_only(
						$key, $get_min_max['min'], $get_min_max['max'], 'has_fee_based_on_cs', 'has_fee_based_on_cs_price', $get_condition['ap_fees_ap_price_category_subtotal'], $total_qws, 'subtotal'
					);
				}
			}
			$main_is_passed = $this->wdpad_check_all_passed_advance_rule__premium_only(
				$is_passed_from_here_cs, 'has_fee_based_on_cs', 'has_fee_based_on_cs_price', $cost_on_category_subtotal_rule_match
			);

			return $main_is_passed;
		}
	}

	/**
	 * Match category per weight rules
	 *
	 * @param array  $get_condition_array_ap_category_weight
	 * @param array  $cart_products_array
	 * @param string $default_lang
	 *
	 * @return array $is_passed_advance_rule
	 * @uses     wdpad_get_count_qty__premium_only()
	 * @uses     wdpad_check_min_max_qws__premium_only()
	 * @uses     wdpad_check_passed_rule__premium_only()
	 * @uses     wdpad_check_all_passed_advance_rule__premium_only()
	 *
	 * @since    2.3.3
	 *
	 */
	public function wdpad_match_category_per_weight__premium_only( $get_condition_array_ap_category_weight, $woo_cart_array, $sitepress, $default_lang, $cost_on_category_weight_rule_match ) {
		if ( ! empty( $woo_cart_array ) ) {
			$is_passed_from_here_cat = array();
			if ( ! empty( $get_condition_array_ap_category_weight ) || '' !== $get_condition_array_ap_category_weight ) {
				foreach ( $get_condition_array_ap_category_weight as $key => $get_condition ) {
					if ( ! empty( $get_condition['ap_fees_categories_weight'] ) || '' !== $get_condition['ap_fees_categories_weight'] ) {
						$total_qws                 = $this->wdpad_get_count_qty__premium_only(
							$get_condition['ap_fees_categories_weight'], $woo_cart_array, $sitepress, $default_lang, 'category', 'weight'
						);
						$get_min_max               = $this->wdpad_check_min_max_qws__premium_only(
							$get_condition['ap_fees_ap_category_weight_min_qty'], $get_condition['ap_fees_ap_category_weight_max_qty'], $get_condition['ap_fees_ap_price_category_weight'], 'weight'
						);
						$is_passed_from_here_cat[] = $this->wdpad_check_passed_rule__premium_only(
							$key, $get_min_max['min'], $get_min_max['max'], 'has_fee_based_on_per_cw', 'has_fee_based_on_cost_per_cw', $get_condition['ap_fees_ap_price_category_weight'], $total_qws, 'weight'
						);
					}
				}
			}
			$main_is_passed = $this->wdpad_check_all_passed_advance_rule__premium_only(
				$is_passed_from_here_cat, 'has_fee_based_on_per_cw', 'has_fee_based_on_cost_per_cw', $cost_on_category_weight_rule_match
			);

			return $main_is_passed;
		}
	}

    /**
	 * Match total cart per qty rules
	 *
	 * @param array $get_condition_array_ap_total_cart_qty
	 * @param array $cart_products_array
	 *
	 * @return array $is_passed_advance_rule
	 * @uses     wdpad_check_min_max_qws__premium_only()
	 * @uses     wdpad_check_passed_rule__premium_only()
	 * @uses     wdpad_check_all_passed_advance_rule__premium_only()
	 *
	 * @since    2.3.3
	 *
	 */
	public function wdpad_match_total_cart_qty__premium_only( $get_condition_array_ap_total_cart_qty, $woo_cart_array, $cost_on_total_cart_qty_rule_match ) {
		if ( ! empty( $woo_cart_array ) ) {
			$is_passed_from_here_tcq = array();
			if ( ! empty( $get_condition_array_ap_total_cart_qty ) || '' !== $get_condition_array_ap_total_cart_qty ) {
				foreach ( $get_condition_array_ap_total_cart_qty as $key => $get_condition ) {
					$total_qws = 0;
					foreach ( $woo_cart_array as $woo_cart_item ) {
						$total_qws += $woo_cart_item['quantity'];
					}
					$get_min_max               = $this->wdpad_check_min_max_qws__premium_only(
						$get_condition['ap_fees_ap_total_cart_qty_min_qty'], $get_condition['ap_fees_ap_total_cart_qty_max_qty'], $get_condition['ap_fees_ap_price_total_cart_qty'], 'qty'
					);
					$is_passed_from_here_tcq[] = $this->wdpad_check_passed_rule__premium_only(
						$key, $get_min_max['min'], $get_min_max['max'], 'has_fee_based_on_tcq', 'has_fee_based_on_tcq_price', $get_condition['ap_fees_ap_price_total_cart_qty'], $total_qws, 'qty'
					);
				}
			}
			$main_is_passed = $this->wdpad_check_all_passed_advance_rule__premium_only(
				$is_passed_from_here_tcq, 'has_fee_based_on_tcq', 'has_fee_based_on_tcq_price', $cost_on_total_cart_qty_rule_match
			);

			return $main_is_passed;
		}
	}

/**
	 * Match total cart weight rules
	 *
	 * @param array $get_condition_array_ap_total_cart_weight
	 * @param array $cart_products_array
	 *
	 * @return array $is_passed_advance_rule
	 * @uses     wdpad_check_min_max_qws__premium_only()
	 * @uses     wdpad_check_passed_rule__premium_only()
	 * @uses     wdpad_check_all_passed_advance_rule__premium_only()
	 *
	 * @since    2.3.3
	 *
	 */
	public function wdpad_match_total_cart_weight__premium_only( $get_condition_array_ap_total_cart_weight, $woo_cart_array, $cost_on_total_cart_weight_rule_match ) {
		if ( ! empty( $woo_cart_array ) ) {
			$is_passed_from_here_tcw = array();
			if ( ! empty( $get_condition_array_ap_total_cart_weight ) || '' !== $get_condition_array_ap_total_cart_weight ) {
				foreach ( $get_condition_array_ap_total_cart_weight as $key => $get_condition ) {
					$total_qws = 0;
					foreach ( $woo_cart_array as $woo_cart_item ) {
						if ( ! empty( $woo_cart_item['variation_id'] ) || 0 !== $woo_cart_item['variation_id'] ) {
							$product_id_lan = $woo_cart_item['variation_id'];
						} else {
							$product_id_lan = $woo_cart_item['product_id'];
						}
						$_product = wc_get_product( $product_id_lan );
						if ( ! ( $_product->is_virtual( 'yes' ) ) ) {
							$total_qws += intval( $woo_cart_item['quantity'] ) * floatval( $_product->get_weight() );
						}
					}
					$get_min_max               = $this->wdpad_check_min_max_qws__premium_only(
						$get_condition['ap_fees_ap_total_cart_weight_min_weight'], $get_condition['ap_fees_ap_total_cart_weight_max_weight'], $get_condition['ap_fees_ap_price_total_cart_weight'], 'weight'
					);
					$is_passed_from_here_tcw[] = $this->wdpad_check_passed_rule__premium_only(
						$key, $get_min_max['min'], $get_min_max['max'], 'has_fee_based_on_tcw', 'has_fee_based_on_tcw_price', $get_condition['ap_fees_ap_price_total_cart_weight'], $total_qws, 'weight'
					);
				}
			}
			$main_is_passed = $this->wdpad_check_all_passed_advance_rule__premium_only(
				$is_passed_from_here_tcw, 'has_fee_based_on_tcw', 'has_fee_based_on_tcw_price', $cost_on_total_cart_weight_rule_match
			);

			return $main_is_passed;
		}
	}

    /**
	 * Cost for total cart subtotal in advance pricing rules
	 *
	 * @param array  $get_condition_array_ap_total_cart_subtotal
	 * @param array  $woo_cart_array
	 * @param string $cost_on_total_cart_subtotal_rule_match
	 *
	 * @return array $main_is_passed
	 * @since 3.4
	 *
	 * @uses  WC_Cart::get_cart_contents_total()
	 * @uses  wp_get_post_terms()
	 * @uses  wc_get_product()
	 *
	 */
	public function wdpad_match_total_cart_subtotal__premium_only( $get_condition_array_ap_total_cart_subtotal, $woo_cart_array, $cost_on_total_cart_subtotal_rule_match ) {
		if ( ! empty( $woo_cart_array ) ) {
			$is_passed_from_here_tcw = array();
			if ( ! empty( $get_condition_array_ap_total_cart_subtotal ) || '' !== $get_condition_array_ap_total_cart_subtotal ) {
				foreach ( $get_condition_array_ap_total_cart_subtotal as $key => $get_condition ) {
					$total_qws                 = $this->wdpad_get_cart_subtotal();
					$get_min_max               = $this->wdpad_check_min_max_qws__premium_only(
						$get_condition['ap_fees_ap_total_cart_subtotal_min_subtotal'], $get_condition['ap_fees_ap_total_cart_subtotal_max_subtotal'], $get_condition['ap_fees_ap_price_total_cart_subtotal'], 'weight'
					);
					$is_passed_from_here_tcw[] = $this->wdpad_check_passed_rule__premium_only(
						$key, $get_min_max['min'], $get_min_max['max'], 'has_fee_based_on_tcs', 'has_fee_based_on_tcs_price', $get_condition['ap_fees_ap_price_total_cart_subtotal'], $total_qws, 'weight'
					);
				}
			}
			$main_is_passed = $this->wdpad_check_all_passed_advance_rule__premium_only(
				$is_passed_from_here_tcw, 'has_fee_based_on_tcs', 'has_fee_based_on_tcs_price', $cost_on_total_cart_subtotal_rule_match
			);

			return $main_is_passed;
		}
	}

    /**
	 * Cost for Category subtotal in advance pricing rules
	 *
	 * @param array  $get_condition_array_ap_shipping_class_subtotal
	 * @param array  $woo_cart_array
	 * @param string $cost_on_shipping_class_subtotal_rule_match
	 *
	 * @return array $main_is_passed
	 * @since 2.3.3
	 *
	 * @uses  WC_Cart::get_cart_contents_total()
	 * @uses  wp_get_post_terms()
	 * @uses  wc_get_product()
	 *
	 */
	public function wdpad_match_shipping_class_subtotal__premium_only( $get_condition_array_ap_shipping_class_subtotal, $woo_cart_array, $sitepress, $default_lang, $cost_on_shipping_class_subtotal_rule_match ) {
		if ( ! empty( $woo_cart_array ) ) {
			$is_passed_from_here_scs = array();
			if ( ! empty( $get_condition_array_ap_shipping_class_subtotal ) || '' !== $get_condition_array_ap_shipping_class_subtotal ) {
				foreach ( $get_condition_array_ap_shipping_class_subtotal as $key => $get_condition ) {
					$total_qws                 = $this->wdpad_get_count_qty__premium_only(
						$get_condition['ap_fees_shipping_class_subtotals'], $woo_cart_array, $sitepress, $default_lang, 'shipping_class', apply_filters('ad_fee_shipping_class_default_behave', 'subtotal')
					);
					$get_min_max               = $this->wdpad_check_min_max_qws__premium_only(
						$get_condition['ap_fees_ap_shipping_class_subtotal_min_subtotal'], $get_condition['ap_fees_ap_shipping_class_subtotal_max_subtotal'], $get_condition['ap_fees_ap_price_shipping_class_subtotal'], 'subtotal'
					);
					$is_passed_from_here_scs[] = $this->wdpad_check_passed_rule__premium_only(
						$key, $get_min_max['min'], $get_min_max['max'], 'has_fee_based_on_scs', 'has_fee_based_on_scs_price', $get_condition['ap_fees_ap_price_shipping_class_subtotal'], $total_qws, 'subtotal'
					);
				}
			}
			$main_is_passed = $this->wdpad_check_all_passed_advance_rule__premium_only(
				$is_passed_from_here_scs, 'has_fee_based_on_scs', 'has_fee_based_on_scs_price', $cost_on_shipping_class_subtotal_rule_match
			);

			return $main_is_passed;
		}
	}

	/**
	 * Count qty for Product, Category and Total Cart
	 *
	 * @param array  $ap_selected_id
	 * @param array  $woo_cart_array
	 * @param string $sitepress
	 * @param string $default_lang
	 * @param string $type
	 * @param string $qws
	 *
	 * @return int $total
	 *
	 * @since 3.6
	 *
	 * @uses  wc_get_product()
	 * @uses  WC_Product::is_type()
	 * @uses  wp_get_post_terms()
	 * @uses  wdpad_get_prd_category_from_cart__premium_only()
	 *
	 */
	public function wdpad_get_count_qty__premium_only( $ap_selected_id, $woo_cart_array, $sitepress, $default_lang, $type, $qws ) {
		$total_qws = 0;
		if ( 'shipping_class' !== $type ) {
			$ap_selected_id = array_map( 'intval', $ap_selected_id );
		}
		foreach ( $woo_cart_array as $woo_cart_item ) {
			$main_product_id_lan = $woo_cart_item['product_id'];
			if ( ! empty( $woo_cart_item['variation_id'] ) || 0 !== $woo_cart_item['variation_id'] ) {
				$product_id_lan = $woo_cart_item['variation_id'];
			} else {
				$product_id_lan = $woo_cart_item['product_id'];
			}
			$_product = wc_get_product( $product_id_lan );
			if ( ! empty( $sitepress ) ) {
				$product_id_lan = intval( apply_filters( 'wpml_object_id', $product_id_lan, 'product', true, $default_lang ) );
			} else {
				$product_id_lan = intval( $product_id_lan );
			}
			if ( 'product' === $type ) {
				if ( in_array( $product_id_lan, $ap_selected_id, true ) ) {
					if ( 'qty' === $qws ) {
						$total_qws += intval( $woo_cart_item['quantity'] );
					}
					if ( 'weight' === $qws ) {
						$total_qws += intval( $woo_cart_item['quantity'] ) * floatval( $_product->get_weight() );
					}
					if ( 'subtotal' === $qws ) {
						if ( ! empty( $woo_cart_item['line_tax'] ) ) {
							$woo_cart_item['line_tax'] = $woo_cart_item['line_tax'];
						}
						$total_qws += $this->wdpad_get_specific_subtotal__premium_only( $woo_cart_item['line_subtotal'], $woo_cart_item['line_tax'] );
					}
				}
			}
			if ( 'category' === $type ) {
				$cat_id_list        = wp_get_post_terms( $main_product_id_lan, 'product_cat', array( 'fields' => 'ids' ) );
				$cat_id_list_origin = $this->wdpad_get_prd_category_from_cart__premium_only( $cat_id_list, $sitepress, $default_lang );
				if ( ! empty( $cat_id_list_origin ) && is_array( $cat_id_list_origin ) ) {
					foreach ( $ap_selected_id as $ap_fees_categories_key_val ) {
						if ( in_array( $ap_fees_categories_key_val, $cat_id_list_origin, true ) ) {
							if ( 'qty' === $qws ) {
								$total_qws += intval( $woo_cart_item['quantity'] );
							}
							if ( 'weight' === $qws ) {
								$total_qws += intval( $woo_cart_item['quantity'] ) * floatval( $_product->get_weight() );
							}
							if ( 'subtotal' === $qws ) {
								if ( ! empty( $woo_cart_item['line_tax'] ) ) {
									$woo_cart_item['line_tax'] = $woo_cart_item['line_tax'];
								}
								$total_qws += $this->wdpad_get_specific_subtotal__premium_only( $woo_cart_item['line_subtotal'], $woo_cart_item['line_tax'] );
							}
							break;
						}
					}
				}
			}
			if ( 'shipping_class' === $type ) {
				$prd_shipping_class = $_product->get_shipping_class();
				if ( in_array( $prd_shipping_class, $ap_selected_id, true ) ) {
					if ( 'qty' === $qws ) {
						$total_qws += intval( $woo_cart_item['quantity'] );
					}
					if ( 'weight' === $qws ) {
						$total_qws += intval( $woo_cart_item['quantity'] ) * floatval( $_product->get_weight() );
					}
					if ( 'subtotal' === $qws ) {
						if ( ! empty( $woo_cart_item['line_tax'] ) ) {
							$woo_cart_item['line_tax'] = $woo_cart_item['line_tax'];
						}
						$total_qws += $this->wdpad_get_specific_subtotal__premium_only( $woo_cart_item['line_subtotal'], $woo_cart_item['line_tax'] );
					}
				}
			}
		}

		return $total_qws;
	}

	/**
	 * Check Min and max qty, weight and subtotal
	 *
	 * @param int|float $min
	 * @param int|float $max
	 * @param float     $price
	 * @param string    $qws
	 *
	 * @return array
	 *
	 * @since 3.4
	 *
	 */
	public function wdpad_check_min_max_qws__premium_only( $min, $max, $price, $qws ) {
		$min_val = $min;
		if ( '' === $max || '0' === $max ) {
			$max_val = 2000000000;
		} else {
			$max_val = $max;
		}
		$price_val = $price;
		if ( 'qty' === $qws ) {
			settype( $min_val, 'integer' );
			settype( $max_val, 'integer' );
		} else {
			settype( $min_val, 'float' );
			settype( $max_val, 'float' );
		}

		return array(
			'min'   => $min_val,
			'max'   => $max_val,
			'price' => $price_val,
		);
	}
	/**
	 * Cgeck rule passed or not
	 *
	 * @param string    $key
	 * @param string    $min
	 * @param string    $max
	 * @param string    $hbc
	 * @param string    $hbp
	 * @param float     $price
	 * @param int|float $total_qws
	 * @param string    $qws
	 *
	 * @return array
	 * @since    3.6
	 *
	 */
	public function wdpad_check_passed_rule__premium_only( $key, $min, $max, $hbc, $hbp, $price, $total_qws, $qws ) {
		$is_passed_from_here_prd = array();
		if ( ( $min <= $total_qws ) && ( $total_qws <= $max ) ) {
			$is_passed_from_here_prd[ $hbc ][ $key ] = 'yes';
			$is_passed_from_here_prd[ $hbp ][ $key ] = $price;
		} else {
			$is_passed_from_here_prd[ $hbc ][ $key ] = 'no';
			$is_passed_from_here_prd[ $hbp ][ $key ] = $price;
		}

		return $is_passed_from_here_prd;
	}
	/**
	 * Find unique id based on given array
	 *
	 * @param array  $is_passed
	 * @param string $has_fee_checked
	 * @param string $has_fee_based
	 * @param string $advance_inside_rule_match
	 *
	 * @return array
	 * @since    3.6
	 *
	 */
	public function wdpad_check_all_passed_advance_rule__premium_only( $is_passed, $has_fee_checked, $has_fee_based, $advance_inside_rule_match ) {
		$get_cart_total = WC()->cart->get_cart_contents_total();
		$main_is_passed = 'no';
		$flag           = array();
		$sum_ammount    = 0;
		if ( ! empty( $is_passed ) ) {
			
			foreach ( $is_passed as $main_is_passed ) {
				foreach ( $main_is_passed[ $has_fee_checked ] as $key => $is_passed_value ) {
					if ( 'yes' === $is_passed_value ) {
						
						foreach ( $main_is_passed[ $has_fee_based ] as $hfb_key => $hfb_is_passed_value ) {
							if ( $hfb_key === $key ) {
								$final_price = $this->wdpad_check_percantage_price__premium_only( $hfb_is_passed_value, $get_cart_total );
								$sum_ammount += $final_price;
							}
						}
						$flag[ $key ] = true;
					} else {
						$flag[ $key ] = false;
					}
				}
			}
			if ( 'any' === $advance_inside_rule_match ) {
				if ( in_array( true, $flag, true ) ) {
					$main_is_passed = 'yes';
				} else {
					$main_is_passed = 'no';
				}
			} else {
				if ( in_array( false, $flag, true ) ) {
					$main_is_passed = 'no';
				} else {
					$main_is_passed = 'yes';
				}
			}
		}

		return array(
			'flag'         => $main_is_passed,
			'total_amount' => $sum_ammount,
		);
	}
	/**
	 * Add shipping rate
	 *
	 * @param int|float $min
	 * @param int|float $max
	 * @param float     $price
	 * @param int|float $count_total
	 * @param float     $get_cart_total
	 * @param float     $shipping_rate_cost
	 *
	 * @return float $shipping_rate_cost
	 *
	 * @since 3.4
	 *
	 */
	public function wdpad_check_percantage_price__premium_only( $price, $get_cart_total ) {
		if ( ! empty( $price ) ) {
			$is_percent = substr( $price, - 1 );
			if ( '%' === $is_percent ) {
				$percent = substr( $price, 0, - 1 );
				$percent = number_format( $percent, 2, '.', '' );
				if ( ! empty( $percent ) ) {
					$percent_total = ( $percent / 100 ) * $get_cart_total;
					$price         = $percent_total;
				}
			} else {
				$price = $this->wdpad_price_format( $price );
			}
		}

		return $price;
	}
	/**
	 * Price format
	 *
	 * @param string $price
	 *
	 * @return string $price
	 * @since  1.3.3
	 *
	 */
	public function wdpad_price_format( $price ) {
		$price = floatval( $price );

		return $price;
	}
	/**
	 * Get Product category from cart
	 *
	 * @param array  $cat_id_list
	 * @param string $sitepress
	 * @param string $default_lang
	 *
	 * @return array $cat_id_list_origin
	 *
	 * @since 3.6
	 *
	 */
	public function wdpad_get_prd_category_from_cart__premium_only( $cat_id_list, $sitepress, $default_lang ) {
		$cat_id_list_origin = array();
		if ( isset( $cat_id_list ) && ! empty( $cat_id_list ) ) {
			foreach ( $cat_id_list as $cat_id ) {
				if ( ! empty( $sitepress ) ) {
					$cat_id_list_origin[] = (int) apply_filters( 'wpml_object_id', $cat_id, 'product_cat', true, $default_lang );
				} else {
					$cat_id_list_origin[] = (int) $cat_id;
				}
			}
		}

		return $cat_id_list_origin;
	}

    /**
	 * Get specific subtotal for product and category
	 *
	 * @return float $subtotal
	 *
	 * @since    2.3.3
     * 
	 */
	public function wdpad_get_specific_subtotal__premium_only( $line_total, $line_tax ) {
		$get_customer            = WC()->cart->get_customer();
		$get_customer_vat_exempt = WC()->customer->get_is_vat_exempt();
		$tax_display_cart        = WC()->cart->get_tax_price_display_mode();
		$wc_prices_include_tax   = wc_prices_include_tax();
		$tax_enable              = wc_tax_enabled();
		$cart_subtotal           = 0;
		if ( true === $tax_enable ) {
			if ( true === $wc_prices_include_tax ) {
				if ( 'incl' === $tax_display_cart && ! ( $get_customer && $get_customer_vat_exempt ) ) {
					$cart_subtotal += $line_total + $line_tax;
				} else {
					$cart_subtotal += $line_total;
				}
			} else {
				if ( 'incl' === $tax_display_cart && ! ( $get_customer && $get_customer_vat_exempt ) ) {
					$cart_subtotal += $line_total + $line_tax;
				} else {
					$cart_subtotal += $line_total;
				}
			}
		} else {
			$cart_subtotal += $line_total;
		}

		return $cart_subtotal;
	}

    /**
	 * get cart subtotal
	 *
	 * @return float $cart_subtotal
	 * @since  1.5.2
	 *
	 */
	public function wdpad_get_cart_subtotal() {
		$get_customer            = WC()->cart->get_customer();
		$get_customer_vat_exempt = WC()->customer->get_is_vat_exempt();
		$tax_display_cart        = WC()->cart->get_tax_price_display_mode();
		$wc_prices_include_tax   = wc_prices_include_tax();
		$tax_enable              = wc_tax_enabled();
		$cart_subtotal           = 0;
		if ( true === $tax_enable ) {
			if ( true === $wc_prices_include_tax ) {
				if ( 'incl' === $tax_display_cart && ! ( $get_customer && $get_customer_vat_exempt ) ) {
					$cart_subtotal += WC()->cart->get_subtotal() + WC()->cart->get_subtotal_tax();
				} else {
					$cart_subtotal += WC()->cart->get_subtotal();
				}
			} else {
				if ( 'incl' === $tax_display_cart && ! ( $get_customer && $get_customer_vat_exempt ) ) {
					$cart_subtotal += WC()->cart->get_subtotal() + WC()->cart->get_subtotal_tax();
				} else {
					$cart_subtotal += WC()->cart->get_subtotal();
				}
			}
		} else {
			$cart_subtotal += WC()->cart->get_subtotal();
		}
		return $cart_subtotal;
	}

    /**
	 * Validate date and time conditions
	 *
     * @param integer $dpad_id discount ID
     * @return boolean $dt_valid
     * 
	 * @since  2.4.0
	 *
	 */
	public function wdpad_check_date_and_time_condition( $dpad_id ) {

        $dt_valid = false;

        $getFeeStartDate = get_post_meta( $dpad_id, 'dpad_settings_start_date', true );
        $getFeeEndDate   = get_post_meta( $dpad_id, 'dpad_settings_end_date', true );
        $getFeeStartTime = get_post_meta( $dpad_id, 'dpad_time_from', true );
        $getFeeEndTime   = get_post_meta( $dpad_id, 'dpad_time_to', true );

        //check condition
        $local_nowtimestamp = current_time( 'timestamp' );
        $currentDate  = strtotime( gmdate( 'd-m-Y' ) );
        $feeStartDate = isset( $getFeeStartDate ) && $getFeeStartDate !== '' ? strtotime( $getFeeStartDate ) : '';
        $feeEndDate   = isset( $getFeeEndDate ) && $getFeeEndDate !== '' ? strtotime( $getFeeEndDate ) : '';
        $feeStartTime = isset( $getFeeStartTime ) && $getFeeStartTime !== '' ? strtotime( $getFeeStartTime ) : '';
        $feeEndTime   = isset( $getFeeEndTime ) && $getFeeEndTime !== '' ? strtotime( $getFeeEndTime ) : '';

        if ( ( $currentDate >= $feeStartDate || $feeStartDate === '' ) && ( $currentDate <= $feeEndDate || $feeEndDate === '' ) && ( $local_nowtimestamp >= $feeStartTime || $feeStartTime === '' ) && ( $local_nowtimestamp <= $feeEndTime || $feeEndTime === '' ) ) {
            $dt_valid = true;
        }
        return $dt_valid;
    }

    /**
     * 
     * Check Product or Variation exist in cart or not.
	 *
     * @param integer   $check_id   product/variation ID
     * @return boolean  $exist      return status of product/variation exist or not with cart key
     * 
	 * @since  2.4.0
     * 
     */
    public function wdpad_product_variation_exist_in_cart__premium_only( $check_id ){

        $exist = false;
        $check_id = !empty($check_id) && $check_id > 0 ? intval($check_id) : 0;
        
        $cart_obj = WC()->cart->get_cart();
        $cart_key_data = array();
        foreach( $cart_obj as $cart_key => $cart_data ){
            if( !isset($cart_data['dpad_get_discount_product']) ){
                if( $cart_data['variation_id'] > 0 ) {
                    $check_product = $cart_data['variation_id'];
                    $cart_key_data[$check_product] = $cart_key;
                } else {
                    $check_product = $cart_data['product_id'];
                    $cart_key_data[$check_product] = $cart_key;
                }
            }
        }
        if( array_key_exists( $check_id, $cart_key_data ) ) {
            $exist = $cart_key_data[$check_id];
        }

        return $exist ;
    }

    /**
     * 
     * Get all discounts ID 
	 *
     * @param integer   $update         want to update transient or not
     * @return boolean  $discount_ids   return list of all discount IDs of our plugin
     * 
	 * @since  2.4.0
     * 
     */
    public function wdpad_action_on_discount_list( $update = false ) {

        global $sitepress;

        $discount_ids = [];

        if( $update ){
            delete_option( 'wpdad_discount_id_list' );
        }
        $discount_ids = get_option( 'wpdad_discount_id_list' );
        if( ! $discount_ids ) { 
            if ( ! empty( $sitepress ) ) {
                $default_lang = $sitepress->get_default_language();
            } else {
                $get_site_language = get_bloginfo( "language" );
                if ( false !== strpos( $get_site_language, '-' ) ) {
                    $get_site_language_explode = explode( '-', $get_site_language );
                    $default_lang              = $get_site_language_explode[0];
                } else {
                    $default_lang = $get_site_language;
                }
            }

            $dpad_args = array(
                'post_type'      	=> 'wc_dynamic_pricing',
                'post_status'    	=> 'publish',
                'orderby'       	=> 'menu_order',
                'order'          	=> 'ASC',
                'posts_per_page' 	=> - 1,
                'suppress_filters'	=> false,
                'fields' 			=> 'ids'
            );

            $get_all_dpad_query = new WP_Query( $dpad_args );
            $get_all_dpad       = $get_all_dpad_query->get_posts();

            if( isset( $get_all_dpad ) && !empty( $get_all_dpad ) ){
                foreach ( $get_all_dpad as $dpad_id ) {
                    if ( ! empty( $sitepress ) ) {
                        $discount_ids[] = apply_filters( 'wpml_object_id', $dpad_id, 'wc_dynamic_pricing', true, $default_lang );
                    } else {
                        $discount_ids[] = $dpad_id;
                    }
                }
                
                $discount_ids = array_unique($discount_ids);
            }
            add_option( 'wpdad_discount_id_list', $discount_ids );
        }

        return $discount_ids;
    }

    /**
	 * Returns an array of products belonging to given categories.
	 *
	 * @param array $categories List of categories IDs.
	 * @return array
	 */
    public function wdpad_get_products_by_cat_ids__premium_only( $categories ) {
		$terms = get_terms(
			array(
				'taxonomy' => 'product_cat',
				'include'  => $categories,
			)
		);

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return array();
		}

		$args = array(
			'category' => wc_list_pluck( $terms, 'slug' ),
			'limit'    => -1,
			'return'   => 'ids',
		);
		return wc_get_products( $args );
	}

    /**
	 * Returns an array of products belonging to given categories.
	 *
	 * @param array $variation_id Variation product ID
	 * @return array $variation return variation attribute array
	 */
    public function wdpad_get_variation_array_by_variation_id__premium_only( $variation_id ) {        

        $product_data = wc_get_product( $variation_id );
        if ( ! $product_data || 'trash' === $product_data->get_status() ) {
            return false;
        }

        $variation = array();
        
        if ( $product_data->is_type( 'variation' ) ) {
            
            $missing_attributes = array();
            $parent_data        = wc_get_product( $product_data->get_parent_id() );

            $variation_attributes = $product_data->get_variation_attributes();
            // Filter out 'any' variations, which are empty, as they need to be explicitly specified while adding to cart.
            $variation_attributes = array_filter( $variation_attributes );

            // Gather posted attributes.
            $posted_attributes = array();
            foreach ( $parent_data->get_attributes() as $attribute ) {
                if ( ! $attribute['is_variation'] ) {
                    continue;
                }
                $attribute_key = 'attribute_' . sanitize_title( $attribute['name'] );

                if ( isset( $variation[ $attribute_key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                    if ( $attribute['is_taxonomy'] ) {
                        // Don't use wc_clean as it destroys sanitized characters.
                        $value = sanitize_title( wp_unslash( $variation[ $attribute_key ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                    } else {
                        $value = html_entity_decode( wc_clean( wp_unslash( $variation[ $attribute_key ] ) ), ENT_QUOTES, get_bloginfo( 'charset' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                    }

                    // Don't include if it's empty.
                    if ( ! empty( $value ) || '0' === $value ) {
                        $posted_attributes[ $attribute_key ] = $value;
                    }
                }
            }

            // Merge variation attributes and posted attributes.
            $posted_and_variation_attributes = array_merge( $variation_attributes, $posted_attributes );

            // If no variation ID is set, attempt to get a variation ID from posted attributes.
            if ( empty( $variation_id ) ) {
                $data_store   = WC_Data_Store::load( 'product' );
                $variation_id = $data_store->find_matching_product_variation( $parent_data, $posted_attributes );
            }

            // Do we have a variation ID?
            if ( empty( $variation_id ) ) {
                throw new Exception( __( 'Please choose product options&hellip;', 'woo-conditional-discount-rules-for-checkout' ) );
            }

            // Check the data we have is valid.
            $variation_data = wc_get_product_variation_attributes( $variation_id );
            $attributes     = array();

            foreach ( $parent_data->get_attributes() as $attribute ) {
                if ( ! $attribute['is_variation'] ) {
                    continue;
                }

                // Get valid value from variation data.
                $attribute_key = 'attribute_' . sanitize_title( $attribute['name'] );
                $valid_value   = isset( $variation_data[ $attribute_key ] ) ? $variation_data[ $attribute_key ] : '';

                /**
                 * If the attribute value was posted, check if it's valid.
                 *
                 * If no attribute was posted, only error if the variation has an 'any' attribute which requires a value.
                 */
                if ( isset( $posted_and_variation_attributes[ $attribute_key ] ) ) {
                    $value = $posted_and_variation_attributes[ $attribute_key ];

                    // Allow if valid or show error.
                    if ( $valid_value === $value ) {
                        $attributes[ $attribute_key ] = $value;
                    } elseif ( '' === $valid_value && in_array( $value, $attribute->get_slugs(), true ) ) {
                        // If valid values are empty, this is an 'any' variation so get all possible values.
                        $attributes[ $attribute_key ] = $value;
                    } else {
                        /* translators: %s: Attribute name. */
                        throw new Exception( sprintf( __( 'Invalid value posted for %s', 'woo-conditional-discount-rules-for-checkout' ), wc_attribute_label( $attribute['name'] ) ) );
                    }
                } elseif ( '' === $valid_value ) {
                    $missing_attributes[] = wc_attribute_label( $attribute['name'] );
                }

                $variation = $attributes;
            }
            if ( ! empty( $missing_attributes ) ) {
                /* translators: %s: Attribute name. */
                throw new Exception( sprintf( _n( '%s is a required field', '%s are required fields', count( $missing_attributes ), 'woo-conditional-discount-rules-for-checkout' ), wc_format_list_of_items( $missing_attributes ) ) );
            }
            return $variation;
        }
    }

    /**
     * Remove all BOGO free product from cart
     */
    public function wdpad_reset_BOGO_products__premium_only(){
        
        if( !is_null(WC()->cart) && !WC()->cart->is_empty() ){
            $cart_obj = WC()->cart->get_cart();
            foreach( $cart_obj as $cart_key => $cart_data ){
                if( isset($cart_data['dpad_get_discount_product']) && !empty($cart_data['dpad_get_discount_product']) ){
                    if ( $cart_key ) {
                        WC()->cart->remove_cart_item( $cart_key );
                    }
                }
            }
        }
    }
}
