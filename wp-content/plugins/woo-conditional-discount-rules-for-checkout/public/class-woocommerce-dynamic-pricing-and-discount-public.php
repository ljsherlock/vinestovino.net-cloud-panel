<?php

//phpcs:ignore
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
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
class Woocommerce_Dynamic_Pricing_And_Discount_Pro_Public
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private  $plugin_name ;
    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private  $version ;
    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     *
     * @param      string $plugin_name The name of the plugin.
     * @param      string $version     The version of this plugin.
     */
    public function __construct( $plugin_name, $version )
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }
    
    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
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
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'css/woocommerce-dynamic-pricing-and-discount-public.css',
            array(),
            $this->version,
            'all'
        );
    }
    
    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
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
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'js/woocommerce-dynamic-pricing-and-discount-public.js',
            array( 'jquery' ),
            $this->version,
            false
        );
        wp_localize_script( $this->plugin_name, 'my_ajax_object', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
        ) );
    }
    
    function woocommerce_locate_template_product_wdpad_conditions( $template, $template_name, $template_path )
    {
        global  $woocommerce ;
        $_template = $template;
        if ( !$template_path ) {
            $template_path = $woocommerce->template_url;
        }
        $plugin_path = woocommerce_conditional_discount_rules_for_checkout_path() . '/woocommerce/';
        $template = locate_template( array( $template_path . $template_name, $template_name ) );
        // Modification: Get the template from this plugin, if it exists
        if ( !$template && file_exists( $plugin_path . $template_name ) ) {
            $template = $plugin_path . $template_name;
        }
        if ( !$template ) {
            $template = $_template;
        }
        // Return what we found
        return $template;
    }
    
    /**
     * @param $package
     */
    public function conditional_wdpad_add_to_cart( $package )
    {
        global  $woocommerce ;
        //Get all discount IDs with WPML compatibile
        $get_all_dpad = $this->wdpad_action_on_discount_list();

        // ljsherlock customisation 1 START
        // Reduce applicable discunts down to the highest discount.
        $applicable_discounts = array();

        $combine_cost = 0;
        if ( !empty($get_all_dpad) ) {
            foreach ( $get_all_dpad as $dpad_id ) {
                $discount_check = $this->wdpad_check_discount_condition( $dpad_id );

                $applicable_discounts[] = $discount_check;
                
                if ( $discount_check ) {
                    $getFeeType = get_post_meta( $dpad_id, 'dpad_settings_select_dpad_type', true );
                    
                    if ( isset( $getFeeType ) && !empty($getFeeType) && $getFeeType === 'bogo' ) {
                        //BOGO related things
                    } elseif ( isset( $getFeeType ) && !empty($getFeeType) && $getFeeType === 'adjustment' ) {
                        //Adjustment related things
                    }
                }
            }

            arsort($applicable_discounts);
            $new_array = $applicable_discounts;
            $max = max($new_array);
            $results = array();
            foreach ($new_array as $key => $val) {
                if ($val !== false && $val['dpad_cost'] == $max['dpad_cost']) {
                    $results[] = $val;
                }
            }

            foreach($results as $discount) {
                $woocommerce->cart->add_fee(
                    $discount['title'],
                    -1 * $discount['dpad_cost'],
                    true,
                    ''
                );
            }
        }
        // ljsherlock customisation 1 END
    }
    
    public function wdpad_check_discount_condition( $dpad_id )
    {
        if ( is_admin() ) {
            return false;
        }
        global 
            $woocommerce,
            $woocommerce_wpml,
            $sitepress,
            $current_user,
            $pagenow
        ;
        if ( is_null( $woocommerce->cart ) ) {
            return false;
        }
        //Check discount enable or not
        $getFeeStatus = get_post_meta( $dpad_id, 'dpad_settings_status', true );
        if ( isset( $getFeeStatus ) && $getFeeStatus === 'off' ) {
            return false;
        }
        
        if ( !empty($sitepress) ) {
            $default_lang = $sitepress->get_default_language();
        } else {
            $get_site_language = get_bloginfo( "language" );
            
            if ( false !== strpos( $get_site_language, '-' ) ) {
                $get_site_language_explode = explode( '-', $get_site_language );
                $default_lang = $get_site_language_explode[0];
            } else {
                $default_lang = $get_site_language;
            }
        
        }
        
        $cart_array = $woocommerce->cart->get_cart();
        $cart_sub_total = $woocommerce->cart->get_subtotal();
        $subtax = $woocommerce->cart->get_subtotal_tax();
        $wtdc = get_option( 'woocommerce_tax_display_cart' );
        if ( isset( $subtax ) && !empty($subtax) && 'incl' === $wtdc ) {
            $cart_sub_total = $cart_sub_total + $subtax;
        }
        $cart_final_products_array = array();
        $cart_products_subtotal = 0;
        $final_is_passed_general_rule = $new_is_passed = $final_passed = array();
        //First order for user End
        $is_passed = array();
        $cart_based_qty = 0;
        foreach ( $cart_array as $woo_cart_item_for_qty ) {
            $cart_based_qty += $woo_cart_item_for_qty['quantity'];
        }
        $dpad_title = get_the_title( $dpad_id );
        $title = ( !empty($dpad_title) ? __( $dpad_title, 'woo-conditional-discount-rules-for-checkout' ) : __( 'Fee', 'woo-conditional-discount-rules-for-checkout' ) );
        $getFeesCostOriginal = get_post_meta( $dpad_id, 'dpad_settings_product_cost', true );
        $getFeeType = get_post_meta( $dpad_id, 'dpad_settings_select_dpad_type', true );
        
        if ( isset( $woocommerce_wpml ) && !empty($woocommerce_wpml->multi_currency) ) {
            
            if ( isset( $getFeeType ) && !empty($getFeeType) && $getFeeType === 'fixed' ) {
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
        $get_condition_array = get_post_meta( $dpad_id, 'dynamic_pricing_metabox', true );
        $general_rule_match = 'all';
        /* Percentage Logic Start */
        
        if ( isset( $getFeesCost ) && !empty($getFeesCost) ) {
            
            if ( isset( $getFeeType ) && !empty($getFeeType) && $getFeeType === 'percentage' ) {
                
                if ( $getFeesPerQtyFlag === 'on' ) {
                    $products_based_qty = 0;
                    $products_based_subtotal = 0;
                    $products_based_rule = $this->wdpad_product_qty_on_rules_ps(
                        $dpad_id,
                        $cart_array,
                        $products_based_qty,
                        $products_based_subtotal,
                        $sitepress,
                        $default_lang
                    );
                    
                    if ( !empty($products_based_rule) ) {
                        if ( array_key_exists( '0', $products_based_rule ) ) {
                            $products_based_qty = $products_based_rule[0];
                        }
                        if ( array_key_exists( '1', $products_based_rule ) ) {
                            $products_based_subtotal = $products_based_rule[1];
                        }
                    }
                    
                    $percentage_fee = $products_based_subtotal * $getFeesCost / 100;
                    
                    if ( $getFeesPerQty === 'qty_cart_based' ) {
                        $dpad_cost = $percentage_fee + ($cart_based_qty - 1) * $extraProductCost;
                    } else {
                        if ( $getFeesPerQty === 'qty_product_based' ) {
                            $dpad_cost = $percentage_fee + ($products_based_qty - 1) * $extraProductCost;
                        }
                    }
                
                } else {
                    $dpad_cost = $cart_sub_total * $getFeesCost / 100;
                }
            
            } else {
                $fixed_fee = $getFeesCost;
                
                if ( $getFeesPerQtyFlag === 'on' ) {
                    
                    if ( $getFeesPerQty === 'qty_cart_based' ) {
                        $dpad_cost = $fixed_fee + ($cart_based_qty - 1) * $extraProductCost;
                    } else {
                        if ( $getFeesPerQty === 'qty_product_based' ) {
                            $dpad_cost = $fixed_fee + ($products_based_qty - 1) * $extraProductCost;
                        }
                    }
                
                } else {
                    $dpad_cost = $fixed_fee;
                }
            
            }
        
        } else {
            $dpad_cost = 0;
        }
        
        $sale_product_check = get_post_meta( $dpad_id, 'dpad_sale_product', true );
        $wc_curr_version = $this->dpad_get_woo_version_number();
        
        if ( !empty($get_condition_array) ) {
            $country_array = array();
            $city_array = array();
            $state_array = array();
            $postcode_array = array();
            $zone_array = array();
            $product_array = array();
            $variableproduct_array = array();
            $category_array = array();
            $tag_array = array();
            $product_qty_array = array();
            $product_count_array = array();
            $user_array = array();
            $user_role_array = array();
            $user_mail_array = array();
            $cart_total_array = array();
            $cart_totalafter_array = array();
            $total_spent_order_array = array();
            $spent_order_count_array = array();
            $last_spent_order_array = array();
            $user_repeat_product_array = array();
            $quantity_array = array();
            $weight_array = array();
            $coupon_array = array();
            $shipping_class_array = array();
            $payment_gateway = array();
            $shipping_methods = array();
            $shipping_total_array = array();
            foreach ( $get_condition_array as $key => $value ) {
                if ( array_search( 'country', $value, true ) ) {
                    $country_array[$key] = $value;
                }
                if ( array_search( 'city', $value, true ) ) {
                    $city_array[$key] = $value;
                }
                if ( array_search( 'state', $value, true ) ) {
                    $state_array[$key] = $value;
                }
                if ( array_search( 'postcode', $value, true ) ) {
                    $postcode_array[$key] = $value;
                }
                if ( array_search( 'zone', $value, true ) ) {
                    $zone_array[$key] = $value;
                }
                if ( array_search( 'product', $value, true ) ) {
                    $product_array[$key] = $value;
                }
                if ( array_search( 'variableproduct', $value, true ) ) {
                    $variableproduct_array[$key] = $value;
                }
                if ( array_search( 'category', $value, true ) ) {
                    $category_array[$key] = $value;
                }
                if ( array_search( 'tag', $value, true ) ) {
                    $tag_array[$key] = $value;
                }
                if ( array_search( 'product_qty', $value, true ) ) {
                    $product_qty_array[$key] = $value;
                }
                if ( array_search( 'product_count', $value, true ) ) {
                    $product_count_array[$key] = $value;
                }
                if ( array_search( 'user', $value, true ) ) {
                    $user_array[$key] = $value;
                }
                if ( array_search( 'user_role', $value, true ) ) {
                    $user_role_array[$key] = $value;
                }
                if ( array_search( 'user_mail', $value, true ) ) {
                    $user_mail_array[$key] = $value;
                }
                if ( array_search( 'cart_total', $value, true ) ) {
                    $cart_total_array[$key] = $value;
                }
                if ( array_search( 'cart_totalafter', $value, true ) ) {
                    $cart_totalafter_array[$key] = $value;
                }
                if ( array_search( 'total_spent_order', $value, true ) ) {
                    $total_spent_order_array[$key] = $value;
                }
                if ( array_search( 'spent_order_count', $value, true ) ) {
                    $spent_order_count_array[$key] = $value;
                }
                if ( array_search( 'last_spent_order', $value, true ) ) {
                    $last_spent_order_array[$key] = $value;
                }
                if ( array_search( 'user_repeat_product', $value, true ) ) {
                    $user_repeat_product_array[$key] = $value;
                }
                if ( array_search( 'quantity', $value, true ) ) {
                    $quantity_array[$key] = $value;
                }
                if ( array_search( 'weight', $value, true ) ) {
                    $weight_array[$key] = $value;
                }
                if ( array_search( 'coupon', $value, true ) ) {
                    $coupon_array[$key] = $value;
                }
                if ( array_search( 'shipping_class', $value, true ) ) {
                    $shipping_class_array[$key] = $value;
                }
                if ( array_search( 'payment', $value, true ) ) {
                    $payment_gateway[$key] = $value;
                }
                if ( array_search( 'shipping_method', $value, true ) ) {
                    $shipping_methods[$key] = $value;
                }
                if ( array_search( 'shipping_total', $value, true ) ) {
                    $shipping_total_array[$key] = $value;
                }
            }
            /**
             * Location Specific Start
             */
            //Check if is country exist
            
            if ( is_array( $country_array ) && isset( $country_array ) && !empty($country_array) && !empty($cart_array) ) {
                $country_passed = $this->wdpad_match_country_rules( $country_array, $general_rule_match );
                
                if ( 'yes' === $country_passed ) {
                    $is_passed['has_dpad_based_on_country'] = 'yes';
                } else {
                    $is_passed['has_dpad_based_on_country'] = 'no';
                }
            
            }
            
            /**
             * Location Specific End
             */
            /**
             *  Product Specific Start
             */
            //Check if is product exist
            
            if ( is_array( $product_array ) && isset( $product_array ) && !empty($product_array) && !empty($cart_array) ) {
                $product_passed = $this->wdpad_match_simple_products_rule(
                    $cart_array,
                    $product_array,
                    $sale_product_check,
                    $general_rule_match,
                    $default_lang
                );
                
                if ( 'yes' === $product_passed ) {
                    $is_passed['has_dpad_based_on_product'] = 'yes';
                } else {
                    $is_passed['has_dpad_based_on_product'] = 'no';
                }
            
            }
            
            //Check if is Category exist
            
            if ( is_array( $category_array ) && isset( $category_array ) && !empty($category_array) && !empty($cart_array) ) {
                $category_passed = $this->wdpad_match_category_rule(
                    $cart_array,
                    $category_array,
                    $sale_product_check,
                    $general_rule_match,
                    $default_lang
                );
                
                if ( 'yes' === $category_passed ) {
                    $is_passed['has_dpad_based_on_category'] = 'yes';
                } else {
                    $is_passed['has_dpad_based_on_category'] = 'no';
                }
            
            }
            
            //Check if product count exist
            
            if ( is_array( $product_count_array ) && isset( $product_count_array ) && !empty($product_count_array) && !empty($cart_array) ) {
                $quantity_total = 0;
                $is_sub_passed = array();
                $quantity_total = $this->dpad_product_count_on_rules_ps(
                    $dpad_id,
                    $cart_array,
                    0,
                    0,
                    $sitepress,
                    $default_lang
                );
                if ( $quantity_total === 0 ) {
                    $quantity_total = count( $cart_array );
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
            
            if ( is_array( $user_array ) && isset( $user_array ) && !empty($user_array) && !empty($cart_array) && is_user_logged_in() ) {
                $user_passed = $this->wdpad_match_user_rule( $user_array, $general_rule_match );
                // die(var_dump($user_passed));
                if ( 'yes' === $user_passed ) {
                    $is_passed['has_dpad_based_on_user'] = 'yes';
                } else {
                    $is_passed['has_dpad_based_on_user'] = 'no';
                }
            
            }
            
            /**
             * Purchase History End
             */
            /**
             * Cart Specific Start
             */
            //Check if is Cart Subtotal (Before Discount) exist
            
            if ( is_array( $cart_total_array ) && isset( $cart_total_array ) && !empty($cart_total_array) && !empty($cart_array) ) {
                $total = 0;
                $product_ids_on_sale = wc_get_product_ids_on_sale();
                
                if ( "exclude" === $sale_product_check ) {
                    foreach ( $cart_array as $value ) {
                        $product_id = ( $value['variation_id'] ? intval( $value['variation_id'] ) : intval( $value['product_id'] ) );
                        if ( !in_array( $product_id, $product_ids_on_sale, true ) ) {
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
                
                
                if ( isset( $woocommerce_wpml ) && !empty($woocommerce_wpml->multi_currency) ) {
                    $new_total = $woocommerce_wpml->multi_currency->prices->unconvert_price_amount( $total );
                } else {
                    $new_total = $total;
                }
                
                settype( $new_total, 'float' );
                $cart_total_before_passed = $this->wdpad_match_cart_subtotal_before_discount_rule( $new_total, $cart_total_array, $general_rule_match );
                
                if ( 'yes' === $cart_total_before_passed ) {
                    $is_passed['has_dpad_based_on_cart_total'] = 'yes';
                } else {
                    $is_passed['has_dpad_based_on_cart_total'] = 'no';
                }
            
            }
            
            //Check if is quantity exist
            
            if ( is_array( $quantity_array ) && isset( $quantity_array ) && !empty($quantity_array) && !empty($cart_array) ) {
                $quantity_total = 0;
                $product_ids_on_sale = wc_get_product_ids_on_sale();
                $is_sub_passed = array();
                foreach ( $cart_array as $woo_cart_item ) {
                    $product_type = $woo_cart_item['data']->get_type();
                    $product_id = ( $woo_cart_item['variation_id'] ? intval( $woo_cart_item['variation_id'] ) : intval( $woo_cart_item['product_id'] ) );
                    if ( false === strpos( $product_type, 'bundle' ) ) {
                        
                        if ( "exclude" === $sale_product_check ) {
                            if ( !in_array( $product_id, $product_ids_on_sale, true ) ) {
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
            
            /**
             * Shipping Specific End
             */
            
            if ( isset( $is_passed ) && !empty($is_passed) && is_array( $is_passed ) ) {
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
        
        
        if ( empty($final_is_passed_general_rule) || '' === $final_is_passed_general_rule || null === $final_is_passed_general_rule ) {
            $new_is_passed['passed'] = 'no';
        } else {
            
            if ( !empty($final_is_passed_general_rule) && in_array( 'no', $final_is_passed_general_rule, true ) ) {
                $new_is_passed['passed'] = 'no';
            } else {
                
                if ( empty($final_is_passed_general_rule) && in_array( '', $final_is_passed_general_rule, true ) ) {
                    $new_is_passed['passed'] = 'no';
                } else {
                    if ( !empty($final_is_passed_general_rule) && in_array( 'yes', $final_is_passed_general_rule, true ) ) {
                        $new_is_passed['passed'] = 'yes';
                    }
                }
            
            }
        
        }
        
        
        if ( in_array( 'no', $new_is_passed, true ) ) {
            $final_passed['passed'] = 'no';
        } else {
            $final_passed['passed'] = 'yes';
        }
        
        if ( isset( $final_passed ) && !empty($final_passed) && is_array( $final_passed ) ) {
            if ( !in_array( 'no', $final_passed, true ) ) {
                
                if ( $this->wdpad_check_date_and_time_condition( $dpad_id ) ) {
                    //For Fixed and Percentage discount type
                    if ( isset( $getFeeType ) && !empty($getFeeType) && ('fixed' === $getFeeType || 'percentage' === $getFeeType) ) {
                        return array('dpad_cost' => $dpad_cost, 'title' => $title);
                    }
                }
            
            }
        }
        return false;
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
    public function wdpad_match_country_rules( $country_array, $general_rule_match )
    {
        $selected_country = WC()->customer->get_shipping_country();
        $is_passed = array();
        foreach ( $country_array as $key => $country ) {
            
            if ( 'is_equal_to' === $country['product_dpad_conditions_is'] ) {
                if ( !empty($country['product_dpad_conditions_values']) ) {
                    
                    if ( in_array( $selected_country, $country['product_dpad_conditions_values'], true ) ) {
                        $is_passed[$key]['has_dpad_based_on_country'] = 'yes';
                    } else {
                        $is_passed[$key]['has_dpad_based_on_country'] = 'no';
                    }
                
                }
                if ( empty($country['product_dpad_conditions_values']) ) {
                    $is_passed[$key]['has_dpad_based_on_country'] = 'yes';
                }
            }
            
            if ( 'not_in' === $country['product_dpad_conditions_is'] ) {
                if ( !empty($country['product_dpad_conditions_values']) ) {
                    
                    if ( in_array( $selected_country, $country['product_dpad_conditions_values'], true ) || in_array( 'all', $country['product_dpad_conditions_values'], true ) ) {
                        $is_passed[$key]['has_dpad_based_on_country'] = 'no';
                    } else {
                        $is_passed[$key]['has_dpad_based_on_country'] = 'yes';
                    }
                
                }
            }
        }
        $main_is_passed = $this->dpad_check_all_passed_general_rule( $is_passed, 'has_dpad_based_on_country', $general_rule_match );
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
    public function wdpad_match_simple_products_rule(
        $cart_array,
        $product_array,
        $sale_product_check,
        $general_rule_match,
        $default_lang
    )
    {
        global  $sitepress ;
        $is_passed = array();
        $cart_products_array = array();
        $cart_product = $this->dpad_array_column( $cart_array, 'product_id' );
        $product_ids_on_sale = wc_get_product_ids_on_sale();
        if ( "exclude" === $sale_product_check ) {
            $cart_product = array_diff( $cart_product, $product_ids_on_sale );
        }
        if ( isset( $cart_product ) && !empty($cart_product) ) {
            foreach ( $cart_product as $key => $cart_product_id ) {
                
                if ( !empty($sitepress) ) {
                    $cart_products_array[] = apply_filters(
                        'wpml_object_id',
                        $cart_product_id,
                        'product',
                        true,
                        $default_lang
                    );
                } else {
                    $cart_products_array[] = $cart_product_id;
                }
            
            }
        }
        foreach ( $product_array as $key => $product ) {
            
            if ( !empty($product['product_dpad_conditions_values']) ) {
                if ( 'is_equal_to' === $product['product_dpad_conditions_is'] ) {
                    foreach ( $product['product_dpad_conditions_values'] as $product_id ) {
                        settype( $product_id, 'integer' );
                        
                        if ( in_array( $product_id, dpad_convert_array_to_int( $cart_products_array ), true ) ) {
                            $is_passed[$key]['has_dpad_based_on_product'] = 'yes';
                            break;
                        } else {
                            $is_passed[$key]['has_dpad_based_on_product'] = 'no';
                        }
                    
                    }
                }
                if ( $product['product_dpad_conditions_is'] === 'not_in' ) {
                    foreach ( $product['product_dpad_conditions_values'] as $product_id ) {
                        settype( $product_id, 'integer' );
                        
                        if ( in_array( $product_id, dpad_convert_array_to_int( $cart_products_array ), true ) ) {
                            $is_passed[$key]['has_dpad_based_on_product'] = 'no';
                            break;
                        } else {
                            $is_passed[$key]['has_dpad_based_on_product'] = 'yes';
                        }
                    
                    }
                }
            }
        
        }
        $main_is_passed = $this->dpad_check_all_passed_general_rule( $is_passed, 'has_dpad_based_on_product', $general_rule_match );
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
    public function wdpad_match_category_rule(
        $cart_array,
        $category_array,
        $sale_product_check,
        $general_rule_match,
        $default_lang
    )
    {
        global  $sitepress ;
        $is_passed = array();
        $cart_product = $this->dpad_array_column( $cart_array, 'product_id' );
        $cart_category_id_array = array();
        $cart_products_array = array();
        $product_ids_on_sale = wc_get_product_ids_on_sale();
        foreach ( $cart_array as $value ) {
            $cart_product_id = ( !empty($value['variation_id']) && 0 !== $value['variation_id'] ? $value['variation_id'] : $value['product_id'] );
            
            if ( !empty($sitepress) ) {
                $cart_products_array[] = apply_filters(
                    'wpml_object_id',
                    $cart_product_id,
                    'product',
                    true,
                    $default_lang
                );
            } else {
                $cart_products_array[] = $cart_product_id;
            }
        
        }
        if ( "exclude" === $sale_product_check ) {
            $cart_product = array_diff( $cart_product, $product_ids_on_sale );
        }
        foreach ( $cart_products_array as $product ) {
            $prod_obj = wc_get_product( $product );
            if ( 'simple' !== $prod_obj->get_type() ) {
                $product = $prod_obj->get_parent_id();
            }
            $cart_product_category = wp_get_post_terms( $product, 'product_cat', array(
                'fields' => 'ids',
            ) );
            if ( isset( $cart_product_category ) && !empty($cart_product_category) && is_array( $cart_product_category ) ) {
                $cart_category_id_array[] = $cart_product_category;
            }
        }
        $get_cat_all = array_unique( $this->wdpad_array_flatten( $cart_category_id_array ) );
        foreach ( $category_array as $key => $category ) {
            
            if ( !empty($category['product_dpad_conditions_values']) ) {
                if ( $category['product_dpad_conditions_is'] === 'is_equal_to' ) {
                    foreach ( $category['product_dpad_conditions_values'] as $category_id ) {
                        settype( $category_id, 'integer' );
                        
                        if ( in_array( $category_id, dpad_convert_array_to_int( $get_cat_all ), true ) ) {
                            $is_passed[$key]['has_dpad_based_on_category'] = 'yes';
                            break;
                        } else {
                            $is_passed[$key]['has_dpad_based_on_category'] = 'no';
                        }
                    
                    }
                }
                if ( $category['product_dpad_conditions_is'] === 'not_in' ) {
                    foreach ( $category['product_dpad_conditions_values'] as $category_id ) {
                        settype( $category_id, 'integer' );
                        
                        if ( in_array( $category_id, dpad_convert_array_to_int( $get_cat_all ), true ) ) {
                            $is_passed[$key]['has_dpad_based_on_category'] = 'no';
                            break;
                        } else {
                            $is_passed[$key]['has_dpad_based_on_category'] = 'yes';
                        }
                    
                    }
                }
            }
        
        }
        $main_is_passed = $this->dpad_check_all_passed_general_rule( $is_passed, 'has_dpad_based_on_category', $general_rule_match );
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
    public function wdpad_match_product_count_rule( $quantity_total, $product_count_array, $general_rule_match )
    {
        $is_passed = array();
        $quantity_total = ( $quantity_total > 0 ? $quantity_total : 0 );
        foreach ( $product_count_array as $key => $quantity ) {
            settype( $quantity['product_dpad_conditions_values'], 'float' );
            
            if ( !empty($quantity['product_dpad_conditions_values']) ) {
                if ( $quantity['product_dpad_conditions_is'] === 'is_equal_to' ) {
                    
                    if ( $quantity_total === $quantity['product_dpad_conditions_values'] ) {
                        $is_passed[$key]['has_dpad_based_on_product_count'] = 'yes';
                    } else {
                        $is_passed[$key]['has_dpad_based_on_product_count'] = 'no';
                        break;
                    }
                
                }
                if ( $quantity['product_dpad_conditions_is'] === 'less_equal_to' ) {
                    
                    if ( $quantity['product_dpad_conditions_values'] >= $quantity_total ) {
                        $is_passed[$key]['has_dpad_based_on_product_count'] = 'yes';
                    } else {
                        $is_passed[$key]['has_dpad_based_on_product_count'] = 'no';
                        break;
                    }
                
                }
                if ( $quantity['product_dpad_conditions_is'] === 'less_then' ) {
                    
                    if ( $quantity['product_dpad_conditions_values'] > $quantity_total ) {
                        $is_passed[$key]['has_dpad_based_on_product_count'] = 'yes';
                    } else {
                        $is_passed[$key]['has_dpad_based_on_product_count'] = 'no';
                        break;
                    }
                
                }
                if ( $quantity['product_dpad_conditions_is'] === 'greater_equal_to' ) {
                    
                    if ( $quantity['product_dpad_conditions_values'] <= $quantity_total ) {
                        $is_passed[$key]['has_dpad_based_on_product_count'] = 'yes';
                    } else {
                        $is_passed[$key]['has_dpad_based_on_product_count'] = 'no';
                        break;
                    }
                
                }
                if ( $quantity['product_dpad_conditions_is'] === 'greater_then' ) {
                    
                    if ( $quantity['product_dpad_conditions_values'] < $quantity_total ) {
                        $is_passed[$key]['has_dpad_based_on_product_count'] = 'yes';
                    } else {
                        $is_passed[$key]['has_dpad_based_on_product_count'] = 'no';
                        break;
                    }
                
                }
                if ( $quantity['product_dpad_conditions_is'] === 'not_in' ) {
                    
                    if ( $quantity_total === $quantity['product_dpad_conditions_values'] ) {
                        $is_passed[$key]['has_dpad_based_on_product_count'] = 'no';
                        break;
                    } else {
                        $is_passed[$key]['has_dpad_based_on_product_count'] = 'yes';
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
    public function wdpad_match_user_rule( $user_array, $general_rule_match )
    {
        $is_passed = array();
        $current_user_id = get_current_user_id();
        settype( $current_user_id, 'integer' );
        foreach ( $user_array as $key => $user ) {
            if ( 'is_equal_to' === $user['product_dpad_conditions_is'] ) {
                
                if ( in_array( $current_user_id, dpad_convert_array_to_int( $user['product_dpad_conditions_values'] ), true ) ) {
                    $is_passed[$key]['has_dpad_based_on_user'] = 'yes';
                } else {
                    $is_passed[$key]['has_dpad_based_on_user'] = 'no';
                }
            
            }
            if ( 'not_in' === $user['product_dpad_conditions_is'] ) {
                
                if ( in_array( $current_user_id, dpad_convert_array_to_int( $user['product_dpad_conditions_values'] ), true ) ) {
                    $is_passed[$key]['has_dpad_based_on_user'] = 'no';
                } else {
                    $is_passed[$key]['has_dpad_based_on_user'] = 'yes';
                }
            
            }
        }
        $main_is_passed = $this->dpad_check_all_passed_general_rule( $is_passed, 'has_dpad_based_on_user', $general_rule_match );
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
    public function wdpad_match_cart_subtotal_before_discount_rule( $new_total, $cart_total_array, $general_rule_match )
    {
        $is_passed = array();
        foreach ( $cart_total_array as $key => $cart_total ) {
            settype( $cart_total['product_dpad_conditions_values'], 'float' );
            
            if ( !empty($cart_total['product_dpad_conditions_values']) ) {
                if ( $cart_total['product_dpad_conditions_is'] === 'is_equal_to' ) {
                    
                    if ( $cart_total['product_dpad_conditions_values'] === $new_total ) {
                        $is_passed[$key]['has_dpad_based_on_cart_total'] = 'yes';
                    } else {
                        $is_passed[$key]['has_dpad_based_on_cart_total'] = 'no';
                        break;
                    }
                
                }
                if ( $cart_total['product_dpad_conditions_is'] === 'less_equal_to' ) {
                    
                    if ( $cart_total['product_dpad_conditions_values'] >= $new_total ) {
                        $is_passed[$key]['has_dpad_based_on_cart_total'] = 'yes';
                    } else {
                        $is_passed[$key]['has_dpad_based_on_cart_total'] = 'no';
                        break;
                    }
                
                }
                if ( $cart_total['product_dpad_conditions_is'] === 'less_then' ) {
                    
                    if ( $cart_total['product_dpad_conditions_values'] > $new_total ) {
                        $is_passed[$key]['has_dpad_based_on_cart_total'] = 'yes';
                    } else {
                        $is_passed[$key]['has_dpad_based_on_cart_total'] = 'no';
                        break;
                    }
                
                }
                if ( $cart_total['product_dpad_conditions_is'] === 'greater_equal_to' ) {
                    
                    if ( $cart_total['product_dpad_conditions_values'] <= $new_total ) {
                        $is_passed[$key]['has_dpad_based_on_cart_total'] = 'yes';
                    } else {
                        $is_passed[$key]['has_dpad_based_on_cart_total'] = 'no';
                        break;
                    }
                
                }
                if ( $cart_total['product_dpad_conditions_is'] === 'greater_then' ) {
                    
                    if ( $cart_total['product_dpad_conditions_values'] < $new_total ) {
                        $is_passed[$key]['has_dpad_based_on_cart_total'] = 'yes';
                    } else {
                        $is_passed[$key]['has_dpad_based_on_cart_total'] = 'no';
                        break;
                    }
                
                }
                if ( $cart_total['product_dpad_conditions_is'] === 'not_in' ) {
                    
                    if ( $new_total === $cart_total['product_dpad_conditions_values'] ) {
                        $is_passed[$key]['has_dpad_based_on_cart_total'] = 'no';
                        break;
                    } else {
                        $is_passed[$key]['has_dpad_based_on_cart_total'] = 'yes';
                    }
                
                }
            }
        
        }
        $main_is_passed = $this->dpad_check_all_passed_general_rule( $is_passed, 'has_dpad_based_on_cart_total', $general_rule_match );
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
    public function wdpad_match_cart_based_qty_rule( $quantity_total, $quantity_array, $general_rule_match )
    {
        $quantity_total = ( $quantity_total > 0 ? $quantity_total : 0 );
        $is_passed = array();
        settype( $quantity_total, 'float' );
        foreach ( $quantity_array as $key => $quantity ) {
            settype( $quantity['product_dpad_conditions_values'], 'integer' );
            
            if ( !empty($quantity['product_dpad_conditions_values']) ) {
                if ( $quantity['product_dpad_conditions_is'] === 'is_equal_to' ) {
                    
                    if ( $quantity_total === $quantity['product_dpad_conditions_values'] ) {
                        $is_passed[$key]['has_dpad_based_on_quantity'] = 'yes';
                    } else {
                        $is_passed[$key]['has_dpad_based_on_quantity'] = 'no';
                        break;
                    }
                
                }
                if ( $quantity['product_dpad_conditions_is'] === 'less_equal_to' ) {
                    
                    if ( $quantity['product_dpad_conditions_values'] >= $quantity_total ) {
                        $is_passed[$key]['has_dpad_based_on_quantity'] = 'yes';
                    } else {
                        $is_passed[$key]['has_dpad_based_on_quantity'] = 'no';
                        break;
                    }
                
                }
                if ( $quantity['product_dpad_conditions_is'] === 'less_then' ) {
                    
                    if ( $quantity['product_dpad_conditions_values'] > $quantity_total ) {
                        $is_passed[$key]['has_dpad_based_on_quantity'] = 'yes';
                    } else {
                        $is_passed[$key]['has_dpad_based_on_quantity'] = 'no';
                        break;
                    }
                
                }
                if ( $quantity['product_dpad_conditions_is'] === 'greater_equal_to' ) {
                    
                    if ( $quantity['product_dpad_conditions_values'] <= $quantity_total ) {
                        $is_passed[$key]['has_dpad_based_on_quantity'] = 'yes';
                    } else {
                        $is_passed[$key]['has_dpad_based_on_quantity'] = 'no';
                        break;
                    }
                
                }
                if ( $quantity['product_dpad_conditions_is'] === 'greater_then' ) {
                    
                    if ( $quantity['product_dpad_conditions_values'] < $quantity_total ) {
                        $is_passed[$key]['has_dpad_based_on_quantity'] = 'yes';
                    } else {
                        $is_passed[$key]['has_dpad_based_on_quantity'] = 'no';
                        break;
                    }
                
                }
                if ( $quantity['product_dpad_conditions_is'] === 'not_in' ) {
                    
                    if ( $quantity_total === $quantity['product_dpad_conditions_values'] ) {
                        $is_passed[$key]['has_dpad_based_on_quantity'] = 'no';
                        break;
                    } else {
                        $is_passed[$key]['has_dpad_based_on_quantity'] = 'yes';
                    }
                
                }
            }
        
        }
        $main_is_passed = $this->dpad_check_all_passed_general_rule( $is_passed, 'has_dpad_based_on_quantity', $general_rule_match );
        return $main_is_passed;
    }
    
    /**
     * Change shipping label when shipping method is taxable
     *
     * @param $label
     * @param $method
     * @return $label
     */
    public function wdpad_change_shipping_title( $label, $method )
    {
        $total_tax = 0;
        $current_currency = get_woocommerce_currency_symbol();
        if ( !empty($method->get_taxes()) ) {
            foreach ( $method->get_taxes() as $shipping_tax ) {
                $total_tax += $shipping_tax;
            }
        }
        if ( $total_tax > 0 ) {
            $label .= sprintf(
                wp_kses_post( ' %1$s(Tax: %3$s)%2$s' ),
                '<strong>',
                '</strong>',
                $current_currency . $total_tax
            );
        }
        return $label;
    }
    
    /**
     * Add discount message on product details page after add to cart button
     *
     * @param $label
     * @param $method
     * @return $label
     */
    public function wdpad_content_after_addtocart_button()
    {
        //This condition put here because "WooCommerce Product Table" by Barn2 Plugins use same hook to display product list in tabular form
        if ( !is_singular() ) {
            return;
        }
        global  $product ;
        $productid = $product->get_id();
        
        if ( $product->is_type( 'variable' ) ) {
            $variations = $product->get_available_variations();
            $variations_ids = wp_list_pluck( $variations, 'variation_id' );
        }
        
        $get_all_discounts = $this->wdpad_action_on_discount_list();
        foreach ( $get_all_discounts as $discount_id ) {
            $getMsgChecked = get_post_meta( $discount_id, 'dpad_chk_discount_msg', true );
            $getrulestatus = get_post_meta( $discount_id, 'dpad_settings_status', true );
            $forSpecificProduct = get_post_meta( $discount_id, 'dpad_chk_discount_msg_selected_product', true );
            if ( 'on' === $getrulestatus ) {
                
                if ( !empty($getMsgChecked) && "on" === $getMsgChecked ) {
                    $discount_msg_bg_color = ( get_post_meta( $discount_id, 'dpad_discount_msg_bg_color', true ) ? get_post_meta( $discount_id, 'dpad_discount_msg_bg_color', true ) : '#ffcaca' );
                    $discount_msg_text_color = ( get_post_meta( $discount_id, 'dpad_discount_msg_text_color', true ) ? get_post_meta( $discount_id, 'dpad_discount_msg_text_color', true ) : '#000000' );
                    $getDiscountMsg = esc_html__( get_post_meta( $discount_id, 'dpad_discount_msg_text', true ), 'woo-conditional-discount-rules-for-checkout' );
                    $discount_msg_show = false;
                    
                    if ( !empty($forSpecificProduct) && 'on' === $forSpecificProduct ) {
                        $selectedProductList = (array) get_post_meta( $discount_id, 'dpad_selected_product_list', true );
                        
                        if ( $product->is_type( 'variable' ) ) {
                            foreach ( $variations_ids as $variations_id ) {
                                if ( !empty($getDiscountMsg) && in_array( $variations_id, $selectedProductList, true ) ) {
                                    echo  sprintf(
                                        wp_kses_post( '<div class="dpad_discount_message dpad_variation dpad_variation_%1$d" style="background:%2$s;color:%3$s;"><span>%4$s</span></div>' ),
                                        intval( $variations_id ),
                                        esc_html( $discount_msg_bg_color ),
                                        esc_html( $discount_msg_text_color ),
                                        wp_kses_post( html_entity_decode( $getDiscountMsg ) )
                                    ) ;
                                }
                            }
                        } else {
                            if ( in_array( $productid, $selectedProductList, true ) ) {
                                $discount_msg_show = true;
                            }
                        }
                    
                    } else {
                        $discount_msg_show = true;
                    }
                    
                    if ( $discount_msg_show && !empty($getDiscountMsg) ) {
                        echo  sprintf(
                            wp_kses_post( '<div class="dpad_discount_message" style="background:%s;color:%s"><span>%s</span></div>' ),
                            esc_html( $discount_msg_bg_color ),
                            esc_html( $discount_msg_text_color ),
                            wp_kses_post( html_entity_decode( $getDiscountMsg ) )
                        ) ;
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
    public function conditional_wdpad_exclude_cart_fees_taxes( $taxes, $fee, $cart )
    {
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
        global  $wpdb, $woocommerce ;
        $country = strtoupper( wc_clean( $woocommerce->customer->get_shipping_country() ) );
        $state = strtoupper( wc_clean( $woocommerce->customer->get_shipping_state() ) );
        $continent = strtoupper( wc_clean( WC()->countries->get_continent_code_for_country( $country ) ) );
        $postcode = wc_normalize_postcode( wc_clean( $woocommerce->customer->get_shipping_postcode() ) );
        $cache_key = WC_Cache_Helper::get_cache_prefix( 'shipping_zones' ) . 'wc_shipping_zone_' . md5( sprintf(
            '%s+%s+%s',
            $country,
            $state,
            $postcode
        ) );
        $matching_zone_id = wp_cache_get( $cache_key, 'shipping_zones' );
        
        if ( false === $matching_zone_id ) {
            // Postcode range and wildcard matching
            $postcode_locations = array();
            $zones = WC_Shipping_Zones::get_zones();
            if ( !empty($zones) ) {
                foreach ( $zones as $zone ) {
                    if ( !empty($zone['zone_locations']) ) {
                        foreach ( $zone['zone_locations'] as $zone_location ) {
                            $location = new stdClass();
                            
                            if ( 'postcode' === $zone_location->type ) {
                                $location->zone_id = $zone['zone_id'];
                                $location->location_code = $zone_location->code;
                                $postcode_locations[] = $location;
                            }
                        
                        }
                    }
                }
            }
            
            if ( $postcode_locations ) {
                $zone_ids_with_postcode_rules = array_map( 'absint', wp_list_pluck( $postcode_locations, 'zone_id' ) );
                $matches = wc_postcode_location_matcher(
                    $postcode,
                    $postcode_locations,
                    'zone_id',
                    'location_code',
                    $country
                );
                $do_not_match = array_unique( array_diff( $zone_ids_with_postcode_rules, array_keys( $matches ) ) );
                if ( !empty($do_not_match) ) {
                    $criteria = $do_not_match;
                }
            }
            
            // Get matching zones
            // phpcs:disable
            
            if ( !empty($criteria) ) {
                $matching_zone_id = $wpdb->get_var( $wpdb->prepare(
                    "\n                    SELECT zones.zone_id FROM {$wpdb->prefix}woocommerce_shipping_zones as zones\n                    LEFT OUTER JOIN {$wpdb->prefix}woocommerce_shipping_zone_locations as locations ON zones.zone_id = locations.zone_id AND location_type != 'postcode'\n                    WHERE ( ( location_type = 'country' AND location_code = %s )\n                    OR ( location_type = 'state' AND location_code = %s )\n                    OR ( location_type = 'continent' AND location_code = %s )\n                    OR ( location_type IS NULL ) )\n                    AND zones.zone_id NOT IN (%s)\n                    ORDER BY zone_order ASC LIMIT 1\n                ",
                    $country,
                    $country . ':' . $state,
                    $continent,
                    implode( ',', $do_not_match )
                ) );
            } else {
                $matching_zone_id = $wpdb->get_var( $wpdb->prepare(
                    "\n                    SELECT zones.zone_id FROM {$wpdb->prefix}woocommerce_shipping_zones as zones\n                    LEFT OUTER JOIN {$wpdb->prefix}woocommerce_shipping_zone_locations as locations ON zones.zone_id = locations.zone_id AND location_type != 'postcode'\n                    WHERE ( ( location_type = 'country' AND location_code = %s )\n                    OR ( location_type = 'state' AND location_code = %s )\n                    OR ( location_type = 'continent' AND location_code = %s )\n                    OR ( location_type IS NULL ) )\n                    ORDER BY zone_order ASC LIMIT 1\n                ",
                    $country,
                    $country . ':' . $state,
                    $continent
                ) );
            }
            
            // phpcs:enable
            wp_cache_set( $cache_key, $matching_zone_id, 'shipping_zones' );
        }
        
        return ( $matching_zone_id ? $matching_zone_id : 0 );
    }
    
    public function dpad_array_column( array $input, $columnKey, $indexKey = null )
    {
        $array = array();
        foreach ( $input as $value ) {
            if ( !isset( $value[$columnKey] ) ) {
                return false;
            }
            
            if ( is_null( $indexKey ) ) {
                $array[] = $value[$columnKey];
            } else {
                if ( !isset( $value[$indexKey] ) ) {
                    return false;
                }
                if ( !is_scalar( $value[$indexKey] ) ) {
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        
        }
        return $array;
    }
    
    public function wdpad_array_flatten( $array )
    {
        if ( !is_array( $array ) ) {
            return false;
        }
        $result = array();
        foreach ( $array as $key => $value ) {
            
            if ( is_array( $value ) ) {
                $result = array_merge( $result, $this->wdpad_array_flatten( $value ) );
            } else {
                $result[$key] = $value;
            }
        
        }
        return $result;
    }
    
    function dpad_get_woo_version_number()
    {
        // If get_plugins() isn't available, require it
        if ( !function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        // Create the plugins folder and file variables
        $plugin_folder = get_plugins( '/' . 'woocommerce' );
        $plugin_file = 'woocommerce.php';
        // If the plugin version number is set, return it
        
        if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
            return $plugin_folder[$plugin_file]['Version'];
        } else {
            return null;
        }
    
    }
    
    /*
     * Get WooCommerce version number
     */
    public function dpad_remove_currency( $price )
    {
        $args = array(
            'decimal_separator'  => wc_get_price_decimal_separator(),
            'thousand_separator' => wc_get_price_thousand_separator(),
        );
        $wc_currency_symbol = get_woocommerce_currency_symbol();
        $cleanText = wp_strip_all_tags( $price );
        $new_price = str_replace( $wc_currency_symbol, '', $cleanText );
        $tnew_price = str_replace( $args['thousand_separator'], '', $new_price );
        $dnew_price = str_replace( $args['decimal_separator'], '.', $tnew_price );
        $new_price2 = preg_replace( '/[^.\\d]/', '', $dnew_price );
        return $new_price2;
    }
    
    /*
     * Enable ajax refresh for email field
     */
    function wdpad_trigger_update_checkout_on_change( $fields )
    {
        $fields['billing']['billing_email']['class'][] = 'update_totals_on_change';
        return $fields;
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
    public function wdpad_product_qty_on_rules_ps(
        $fees_id,
        $cart_array,
        $products_based_qty,
        $products_based_subtotal,
        $sitepress,
        $default_lang
    )
    {
        $get_condition_array = get_post_meta( $fees_id, 'dynamic_pricing_metabox', true );
        $all_rule_check = array();
        if ( !empty($get_condition_array) ) {
            foreach ( $get_condition_array as $condition ) {
                
                if ( array_search( 'product', $condition, true ) ) {
                    $site_product_id = '';
                    $cart_final_products_array = array();
                    $product_dpad_conditions_values = ( isset( $condition['product_dpad_conditions_values'] ) && !empty($condition['product_dpad_conditions_values']) ? array_map( 'intval', $condition['product_dpad_conditions_values'] ) : array() );
                    // Product Condition Start
                    
                    if ( 'is_equal_to' === $condition['product_dpad_conditions_is'] ) {
                        if ( !empty($product_dpad_conditions_values) ) {
                            foreach ( $cart_array as $value ) {
                                
                                if ( !empty($value['variation_id']) && 0 !== $value['variation_id'] ) {
                                    $product_id_lan = $value['variation_id'];
                                } else {
                                    $product_id_lan = $value['product_id'];
                                }
                                
                                $_product = wc_get_product( $product_id_lan );
                                $line_item_subtotal = (double) $this->dpad_remove_currency( WC()->cart->get_product_subtotal( $_product, $value['quantity'] ) );
                                
                                if ( !empty($sitepress) ) {
                                    $site_product_id = apply_filters(
                                        'wpml_object_id',
                                        $product_id_lan,
                                        'product',
                                        true,
                                        $default_lang
                                    );
                                } else {
                                    $site_product_id = $product_id_lan;
                                }
                                
                                
                                if ( !$_product->is_virtual( 'yes' ) && false === strpos( $_product->get_type(), 'bundle' ) ) {
                                    
                                    if ( in_array( $site_product_id, $product_dpad_conditions_values, true ) ) {
                                        $prod_qty = ( $value['quantity'] ? $value['quantity'] : 0 );
                                        
                                        if ( array_key_exists( $site_product_id, $cart_final_products_array ) ) {
                                            $product_data_explode = explode( "||", $cart_final_products_array[$site_product_id] );
                                            $cart_product_qty = json_decode( $product_data_explode[0] );
                                            $prod_qty += $cart_product_qty;
                                        }
                                        
                                        $cart_final_products_array[$site_product_id] = $prod_qty . "||" . $line_item_subtotal;
                                    }
                                
                                } else {
                                    
                                    if ( false !== strpos( $_product->get_type(), 'bundle' ) ) {
                                        $prod_qty = 0;
                                        $cart_final_products_array[$site_product_id] = $prod_qty . "||" . $line_item_subtotal;
                                    }
                                
                                }
                            
                            }
                        }
                    } elseif ( 'not_in' === $condition['product_dpad_conditions_is'] ) {
                        if ( !empty($product_dpad_conditions_values) ) {
                            foreach ( $cart_array as $value ) {
                                
                                if ( !empty($value['variation_id']) && 0 !== $value['variation_id'] ) {
                                    $product_id_lan = $value['variation_id'];
                                } else {
                                    $product_id_lan = $value['product_id'];
                                }
                                
                                $_product = wc_get_product( $product_id_lan );
                                $line_item_subtotal = (double) $this->dpad_remove_currency( WC()->cart->get_product_subtotal( $_product, $value['quantity'] ) );
                                
                                if ( !empty($sitepress) ) {
                                    $site_product_id = apply_filters(
                                        'wpml_object_id',
                                        $product_id_lan,
                                        'product',
                                        true,
                                        $default_lang
                                    );
                                } else {
                                    $site_product_id = $product_id_lan;
                                }
                                
                                
                                if ( !$_product->is_virtual( 'yes' ) && false === strpos( $_product->get_type(), 'bundle' ) ) {
                                    
                                    if ( !in_array( $site_product_id, $product_dpad_conditions_values, true ) ) {
                                        $prod_qty = ( $value['quantity'] ? $value['quantity'] : 0 );
                                        
                                        if ( array_key_exists( $site_product_id, $cart_final_products_array ) ) {
                                            $product_data_explode = explode( "||", $cart_final_products_array[$site_product_id] );
                                            $cart_product_qty = json_decode( $product_data_explode[0] );
                                            $prod_qty += $cart_product_qty;
                                        }
                                        
                                        $cart_final_products_array[$site_product_id] = $prod_qty . "||" . $line_item_subtotal;
                                    }
                                
                                } else {
                                    
                                    if ( false !== strpos( $_product->get_type(), 'bundle' ) ) {
                                        $prod_qty = 0;
                                        $cart_final_products_array[$site_product_id] = $prod_qty . "||" . $line_item_subtotal;
                                    }
                                
                                }
                            
                            }
                        }
                    }
                    
                    if ( !empty($cart_final_products_array) ) {
                        foreach ( $cart_final_products_array as $prd_id => $cart_item ) {
                            $cart_item_explode = explode( "||", $cart_item );
                            $all_rule_check[$prd_id]['qty'] = $cart_item_explode[0];
                            $all_rule_check[$prd_id]['subtotal'] = $cart_item_explode[1];
                        }
                    }
                    // Product Condition End
                }
                
                
                if ( array_search( 'variableproduct', $condition, true ) ) {
                    $site_product_id = '';
                    $cart_final_var_products_array = array();
                    $product_dpad_conditions_values = ( isset( $condition['product_dpad_conditions_values'] ) && !empty($condition['product_dpad_conditions_values']) ? array_map( 'intval', $condition['product_dpad_conditions_values'] ) : array() );
                    // Variable Product Condition Start
                    
                    if ( 'is_equal_to' === $condition['product_dpad_conditions_is'] ) {
                        if ( !empty($product_dpad_conditions_values) ) {
                            foreach ( $cart_array as $value ) {
                                
                                if ( !empty($value['variation_id']) && 0 !== $value['variation_id'] ) {
                                    $product_id_lan = $value['variation_id'];
                                } else {
                                    $product_id_lan = $value['product_id'];
                                }
                                
                                $_product = wc_get_product( $product_id_lan );
                                $line_item_subtotal = (double) $this->dpad_remove_currency( WC()->cart->get_product_subtotal( $_product, $value['quantity'] ) );
                                
                                if ( !empty($sitepress) ) {
                                    $site_product_id = apply_filters(
                                        'wpml_object_id',
                                        $product_id_lan,
                                        'product',
                                        true,
                                        $default_lang
                                    );
                                } else {
                                    $site_product_id = $product_id_lan;
                                }
                                
                                
                                if ( !$_product->is_virtual( 'yes' ) && false === strpos( $_product->get_type(), 'bundle' ) ) {
                                    
                                    if ( in_array( $site_product_id, $product_dpad_conditions_values, true ) ) {
                                        $prod_qty = ( $value['quantity'] ? $value['quantity'] : 0 );
                                        $cart_final_var_products_array[$site_product_id] = $prod_qty . "||" . $line_item_subtotal;
                                    }
                                
                                } else {
                                    
                                    if ( false !== strpos( $_product->get_type(), 'bundle' ) ) {
                                        $prod_qty = 0;
                                        $cart_final_var_products_array[$site_product_id] = $prod_qty . "||" . $line_item_subtotal;
                                    }
                                
                                }
                            
                            }
                        }
                    } elseif ( 'not_in' === $condition['product_dpad_conditions_is'] ) {
                        if ( !empty($product_dpad_conditions_values) ) {
                            foreach ( $cart_array as $value ) {
                                
                                if ( !empty($value['variation_id']) && 0 !== $value['variation_id'] ) {
                                    $product_id_lan = $value['variation_id'];
                                } else {
                                    $product_id_lan = $value['product_id'];
                                }
                                
                                $_product = wc_get_product( $product_id_lan );
                                $line_item_subtotal = (double) $this->dpad_remove_currency( WC()->cart->get_product_subtotal( $_product, $value['quantity'] ) );
                                
                                if ( !empty($sitepress) ) {
                                    $site_product_id = apply_filters(
                                        'wpml_object_id',
                                        $product_id_lan,
                                        'product',
                                        true,
                                        $default_lang
                                    );
                                } else {
                                    $site_product_id = $product_id_lan;
                                }
                                
                                
                                if ( !$_product->is_virtual( 'yes' ) && false === strpos( $_product->get_type(), 'bundle' ) ) {
                                    
                                    if ( !in_array( $site_product_id, $product_dpad_conditions_values, true ) ) {
                                        $prod_qty = ( $value['quantity'] ? $value['quantity'] : 0 );
                                        $cart_final_var_products_array[$site_product_id] = $prod_qty . "||" . $line_item_subtotal;
                                    }
                                
                                } else {
                                    
                                    if ( false !== strpos( $_product->get_type(), 'bundle' ) ) {
                                        $prod_qty = 0;
                                        $cart_final_var_products_array[$site_product_id] = $prod_qty . "||" . $line_item_subtotal;
                                    }
                                
                                }
                            
                            }
                        }
                    }
                    
                    if ( !empty($cart_final_var_products_array) ) {
                        foreach ( $cart_final_var_products_array as $prd_id => $cart_item ) {
                            $cart_item_explode = explode( "||", $cart_item );
                            $all_rule_check[$prd_id]['qty'] = $cart_item_explode[0];
                            $all_rule_check[$prd_id]['subtotal'] = $cart_item_explode[1];
                        }
                    }
                    // Variable Product Condition End
                }
                
                
                if ( array_search( 'category', $condition, true ) ) {
                    // Category Condition Start
                    $final_cart_products_cats_ids = array();
                    $cart_final_cat_products_array = array();
                    $product_dpad_conditions_values = ( isset( $condition['product_dpad_conditions_values'] ) && !empty($condition['product_dpad_conditions_values']) ? array_map( 'intval', $condition['product_dpad_conditions_values'] ) : array() );
                    $all_cats = get_terms( array(
                        'taxonomy' => 'product_cat',
                        'fields'   => 'ids',
                    ) );
                    
                    if ( 'is_equal_to' === $condition['product_dpad_conditions_is'] ) {
                        if ( !empty($product_dpad_conditions_values) ) {
                            foreach ( $product_dpad_conditions_values as $category_id ) {
                                settype( $category_id, 'integer' );
                                $final_cart_products_cats_ids[] = $category_id;
                            }
                        }
                    } elseif ( 'not_in' === $condition['product_dpad_conditions_is'] ) {
                        if ( !empty($product_dpad_conditions_values) ) {
                            $final_cart_products_cats_ids = array_diff( $all_cats, $product_dpad_conditions_values );
                        }
                    }
                    
                    $final_cart_products_cats_ids = array_map( 'intval', $final_cart_products_cats_ids );
                    $terms = array();
                    $cart_value_array = array();
                    foreach ( $cart_array as $value ) {
                        
                        if ( !empty($value['variation_id']) && 0 !== $value['variation_id'] ) {
                            $product_id = $value['variation_id'];
                        } else {
                            $product_id = $value['product_id'];
                        }
                        
                        $_product = wc_get_product( $product_id );
                        $line_item_subtotal = (double) $this->dpad_remove_currency( WC()->cart->get_product_subtotal( $_product, $value['quantity'] ) );
                        $cart_value_array[] = $value;
                        $term_ids = wp_get_post_terms( $value['product_id'], 'product_cat', array(
                            'fields' => 'ids',
                        ) );
                        foreach ( $term_ids as $term_id ) {
                            $prod_qty = ( $value['quantity'] ? $value['quantity'] : 0 );
                            if ( false !== strpos( $_product->get_type(), 'bundle' ) ) {
                                $prod_qty = 0;
                            }
                            $product_id = ( $value['variation_id'] ? $value['variation_id'] : $product_id );
                            
                            if ( in_array( $term_id, $final_cart_products_cats_ids, true ) ) {
                                
                                if ( array_key_exists( $product_id, $terms ) && array_key_exists( $term_id, $terms[$product_id] ) ) {
                                    $term_data_explode = explode( "||", $terms[$product_id][$term_id] );
                                    $cart_term_qty = json_decode( $term_data_explode[0] );
                                    $prod_qty += $cart_term_qty;
                                }
                                
                                $terms[$product_id][$term_id] = $prod_qty . "||" . $line_item_subtotal;
                            }
                        
                        }
                    }
                    foreach ( $terms as $cart_product_key => $main_term_data ) {
                        foreach ( $main_term_data as $cart_term_id => $term_data ) {
                            $term_data_explode = explode( "||", $term_data );
                            $cart_term_qty = json_decode( $term_data_explode[0] );
                            $cart_term_subtotal = json_decode( $term_data_explode[1] );
                            if ( in_array( $cart_term_id, $final_cart_products_cats_ids, true ) ) {
                                $cart_final_cat_products_array[$cart_product_key][$cart_term_id] = $cart_term_qty . "||" . $cart_term_subtotal;
                            }
                        }
                    }
                    if ( !empty($cart_final_cat_products_array) ) {
                        foreach ( $cart_final_cat_products_array as $prd_id => $main_cart_item ) {
                            foreach ( $main_cart_item as $term_id => $cart_item ) {
                                $cart_item_explode = explode( "||", $cart_item );
                                $all_rule_check[$prd_id]['qty'] = $cart_item_explode[0];
                                $all_rule_check[$prd_id]['subtotal'] = $cart_item_explode[1];
                            }
                        }
                    }
                    // Category Condition End
                }
                
                
                if ( array_search( 'tag', $condition, true ) ) {
                    // Tag Condition Start
                    $final_cart_products_tag_ids = array();
                    $cart_final_tag_products_array = array();
                    $product_dpad_conditions_values = ( isset( $condition['product_dpad_conditions_values'] ) && !empty($condition['product_dpad_conditions_values']) ? array_map( 'intval', $condition['product_dpad_conditions_values'] ) : array() );
                    $all_tags = get_terms( array(
                        'taxonomy' => 'product_tag',
                        'fields'   => 'ids',
                    ) );
                    
                    if ( 'is_equal_to' === $condition['product_dpad_conditions_is'] ) {
                        if ( !empty($product_dpad_conditions_values) ) {
                            foreach ( $product_dpad_conditions_values as $tag_id ) {
                                $final_cart_products_tag_ids[] = $tag_id;
                            }
                        }
                    } elseif ( 'not_in' === $condition['product_dpad_conditions_is'] ) {
                        if ( !empty($product_dpad_conditions_values) ) {
                            $final_cart_products_tag_ids = array_diff( $all_tags, $product_dpad_conditions_values );
                        }
                    }
                    
                    $final_cart_products_tag_ids = array_map( 'intval', $final_cart_products_tag_ids );
                    $tags = array();
                    $cart_value_array = array();
                    foreach ( $cart_array as $value ) {
                        
                        if ( !empty($value['variation_id']) && 0 !== $value['variation_id'] ) {
                            $product_id = $value['variation_id'];
                        } else {
                            $product_id = $value['product_id'];
                        }
                        
                        $_product = wc_get_product( $product_id );
                        $line_item_subtotal = (double) $this->dpad_remove_currency( WC()->cart->get_product_subtotal( $_product, $value['quantity'] ) );
                        $cart_value_array[] = $value;
                        $tag_ids = wp_get_post_terms( $value['product_id'], 'product_tag', array(
                            'fields' => 'ids',
                        ) );
                        foreach ( $tag_ids as $tag_id ) {
                            $prod_qty = ( $value['quantity'] ? $value['quantity'] : 0 );
                            if ( false !== strpos( $_product->get_type(), 'bundle' ) ) {
                                $prod_qty = 0;
                            }
                            $product_id = ( $value['variation_id'] ? $value['variation_id'] : $product_id );
                            
                            if ( in_array( $tag_id, $final_cart_products_tag_ids, true ) ) {
                                
                                if ( array_key_exists( $product_id, $tags ) && array_key_exists( $tag_id, $tags[$product_id] ) ) {
                                    $term_data_explode = explode( "||", $tags[$product_id][$tag_id] );
                                    $cart_term_qty = json_decode( $term_data_explode[0] );
                                    $prod_qty += $cart_term_qty;
                                }
                                
                                $tags[$product_id][$tag_id] = $prod_qty . "||" . $line_item_subtotal;
                            }
                        
                        }
                    }
                    foreach ( $tags as $cart_product_key => $main_tag_data ) {
                        foreach ( $main_tag_data as $cart_tag_id => $tag_data ) {
                            $tag_data_explode = explode( "||", $tag_data );
                            $cart_tag_qty = json_decode( $tag_data_explode[0] );
                            $cart_tag_subtotal = json_decode( $tag_data_explode[1] );
                            if ( !empty($final_cart_products_tag_ids) ) {
                                if ( in_array( $cart_tag_id, $final_cart_products_tag_ids, true ) ) {
                                    $cart_final_tag_products_array[$cart_product_key][$cart_tag_id] = $cart_tag_qty . "||" . $cart_tag_subtotal;
                                }
                            }
                        }
                    }
                    if ( !empty($cart_final_tag_products_array) ) {
                        foreach ( $cart_final_tag_products_array as $prd_id => $main_cart_item ) {
                            foreach ( $main_cart_item as $term_id => $cart_item ) {
                                $cart_item_explode = explode( "||", $cart_item );
                                $all_rule_check[$prd_id]['qty'] = $cart_item_explode[0];
                                $all_rule_check[$prd_id]['subtotal'] = $cart_item_explode[1];
                            }
                        }
                    }
                }
            
            }
        }
        if ( !empty($all_rule_check) ) {
            foreach ( $all_rule_check as $cart_item ) {
                $products_based_qty += ( isset( $cart_item['qty'] ) ? $cart_item['qty'] : 0 );
                $products_based_subtotal += ( isset( $cart_item['subtotal'] ) ? $cart_item['subtotal'] : 0 );
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
    public function dpad_product_count_on_rules_ps(
        $fees_id,
        $cart_array,
        $products_based_qty,
        $products_based_subtotal,
        $sitepress,
        $default_lang
    )
    {
        $get_condition_array = get_post_meta( $fees_id, 'dynamic_pricing_metabox', true );
        $final_count = 0;
        if ( !empty($get_condition_array) ) {
            foreach ( $get_condition_array as $condition ) {
                
                if ( array_search( 'product', $condition, true ) ) {
                    // Product Condition Start
                    $site_product_id = '';
                    $cart_final_products_array = array();
                    $product_dpad_conditions_values = ( isset( $condition['product_dpad_conditions_values'] ) && !empty($condition['product_dpad_conditions_values']) ? array_map( 'intval', $condition['product_dpad_conditions_values'] ) : array() );
                    
                    if ( 'is_equal_to' === $condition['product_dpad_conditions_is'] ) {
                        if ( !empty($product_dpad_conditions_values) ) {
                            foreach ( $cart_array as $value ) {
                                
                                if ( !empty($value['variation_id']) && 0 !== $value['variation_id'] ) {
                                    $product_id_lan = $value['variation_id'];
                                } else {
                                    $product_id_lan = $value['product_id'];
                                }
                                
                                $_product = wc_get_product( $product_id_lan );
                                $line_item_subtotal = (double) $value['line_subtotal'] + (double) $value['line_subtotal_tax'];
                                
                                if ( !empty($sitepress) ) {
                                    $site_product_id = apply_filters(
                                        'wpml_object_id',
                                        $product_id_lan,
                                        'product',
                                        true,
                                        $default_lang
                                    );
                                } else {
                                    $site_product_id = $product_id_lan;
                                }
                                
                                if ( !$_product->is_virtual( 'yes' ) && false === strpos( $_product->get_type(), 'bundle' ) ) {
                                    if ( in_array( $site_product_id, $product_dpad_conditions_values, true ) ) {
                                        
                                        if ( !array_key_exists( $site_product_id, $cart_final_products_array ) ) {
                                            $final_count++;
                                            $cart_final_products_array[$site_product_id] = $final_count;
                                        }
                                    
                                    }
                                }
                            }
                        }
                    } elseif ( 'not_in' === $condition['product_dpad_conditions_is'] ) {
                        if ( !empty($product_dpad_conditions_values) ) {
                            foreach ( $cart_array as $value ) {
                                
                                if ( !empty($value['variation_id']) && 0 !== $value['variation_id'] ) {
                                    $product_id_lan = $value['variation_id'];
                                } else {
                                    $product_id_lan = $value['product_id'];
                                }
                                
                                $_product = wc_get_product( $product_id_lan );
                                $line_item_subtotal = (double) $value['line_subtotal'] + (double) $value['line_subtotal_tax'];
                                
                                if ( !empty($sitepress) ) {
                                    $site_product_id = apply_filters(
                                        'wpml_object_id',
                                        $product_id_lan,
                                        'product',
                                        true,
                                        $default_lang
                                    );
                                } else {
                                    $site_product_id = $product_id_lan;
                                }
                                
                                if ( !$_product->is_virtual( 'yes' ) && false === strpos( $_product->get_type(), 'bundle' ) ) {
                                    if ( !in_array( $site_product_id, $product_dpad_conditions_values, true ) ) {
                                        
                                        if ( !array_key_exists( $site_product_id, $cart_final_products_array ) ) {
                                            $final_count++;
                                            $cart_final_products_array[$site_product_id] = $final_count;
                                        }
                                    
                                    }
                                }
                            }
                        }
                    }
                
                }
                
                
                if ( array_search( 'variableproduct', $condition, true ) ) {
                    // Variable Product Condition Start
                    $site_product_id = '';
                    $cart_final_var_products_array = array();
                    $product_dpad_conditions_values = ( isset( $condition['product_dpad_conditions_values'] ) && !empty($condition['product_dpad_conditions_values']) ? array_map( 'intval', $condition['product_dpad_conditions_values'] ) : array() );
                    
                    if ( 'is_equal_to' === $condition['product_dpad_conditions_is'] ) {
                        if ( !empty($product_dpad_conditions_values) ) {
                            foreach ( $cart_array as $value ) {
                                
                                if ( !empty($value['variation_id']) && 0 !== $value['variation_id'] ) {
                                    $product_id_lan = $value['variation_id'];
                                } else {
                                    $product_id_lan = $value['product_id'];
                                }
                                
                                $_product = wc_get_product( $product_id_lan );
                                $line_item_subtotal = (double) $value['line_subtotal'] + (double) $value['line_subtotal_tax'];
                                
                                if ( !empty($sitepress) ) {
                                    $site_product_id = apply_filters(
                                        'wpml_object_id',
                                        $product_id_lan,
                                        'product',
                                        true,
                                        $default_lang
                                    );
                                } else {
                                    $site_product_id = $product_id_lan;
                                }
                                
                                if ( !$_product->is_virtual( 'yes' ) && false === strpos( $_product->get_type(), 'bundle' ) ) {
                                    if ( in_array( $site_product_id, $product_dpad_conditions_values, true ) ) {
                                        
                                        if ( !array_key_exists( $site_product_id, $cart_final_var_products_array ) ) {
                                            $final_count++;
                                            $cart_final_var_products_array[$site_product_id] = $final_count;
                                        }
                                    
                                    }
                                }
                            }
                        }
                    } elseif ( 'not_in' === $condition['product_dpad_conditions_is'] ) {
                        if ( !empty($product_dpad_conditions_values) ) {
                            foreach ( $cart_array as $value ) {
                                
                                if ( !empty($value['variation_id']) && 0 !== $value['variation_id'] ) {
                                    $product_id_lan = $value['variation_id'];
                                } else {
                                    $product_id_lan = $value['product_id'];
                                }
                                
                                $_product = wc_get_product( $product_id_lan );
                                $line_item_subtotal = (double) $value['line_subtotal'] + (double) $value['line_subtotal_tax'];
                                
                                if ( !empty($sitepress) ) {
                                    $site_product_id = apply_filters(
                                        'wpml_object_id',
                                        $product_id_lan,
                                        'product',
                                        true,
                                        $default_lang
                                    );
                                } else {
                                    $site_product_id = $product_id_lan;
                                }
                                
                                if ( !$_product->is_virtual( 'yes' ) && false === strpos( $_product->get_type(), 'bundle' ) ) {
                                    if ( !in_array( $site_product_id, $product_dpad_conditions_values, true ) ) {
                                        
                                        if ( !array_key_exists( $site_product_id, $cart_final_var_products_array ) ) {
                                            $final_count++;
                                            $cart_final_var_products_array[$site_product_id] = $final_count;
                                        }
                                    
                                    }
                                }
                            }
                        }
                    }
                    
                    // Variable Product Condition End
                }
                
                
                if ( array_search( 'category', $condition, true ) ) {
                    $final_cart_products_cats_ids = array();
                    $product_dpad_conditions_values = ( isset( $condition['product_dpad_conditions_values'] ) && !empty($condition['product_dpad_conditions_values']) ? array_map( 'intval', $condition['product_dpad_conditions_values'] ) : array() );
                    $all_cats = get_terms( array(
                        'taxonomy' => 'product_cat',
                        'fields'   => 'ids',
                    ) );
                    
                    if ( 'is_equal_to' === $condition['product_dpad_conditions_is'] ) {
                        if ( !empty($product_dpad_conditions_values) ) {
                            foreach ( $product_dpad_conditions_values as $category_id ) {
                                settype( $category_id, 'integer' );
                                $final_cart_products_cats_ids[] = $category_id;
                            }
                        }
                    } elseif ( 'not_in' === $condition['product_dpad_conditions_is'] ) {
                        if ( !empty($product_dpad_conditions_values) ) {
                            $final_cart_products_cats_ids = array_diff( $all_cats, $product_dpad_conditions_values );
                        }
                    }
                    
                    $final_cart_products_cats_ids = array_map( 'intval', $final_cart_products_cats_ids );
                    $cart_value_array = array();
                    $cart_final_products_array = array();
                    foreach ( $cart_array as $value ) {
                        
                        if ( !empty($value['variation_id']) && 0 !== $value['variation_id'] ) {
                            $product_id = $value['variation_id'];
                        } else {
                            $product_id = $value['product_id'];
                        }
                        
                        $_product = wc_get_product( $product_id );
                        $line_item_subtotal = (double) $value['line_subtotal'] + (double) $value['line_subtotal_tax'];
                        $cart_value_array[] = $value;
                        $term_ids = wp_get_post_terms( $value['product_id'], 'product_cat', array(
                            'fields' => 'ids',
                        ) );
                        foreach ( $term_ids as $term_id ) {
                            $product_id = ( $value['variation_id'] ? $value['variation_id'] : $product_id );
                            if ( in_array( $term_id, $final_cart_products_cats_ids, true ) ) {
                                
                                if ( !array_key_exists( $product_id, $cart_final_products_array ) ) {
                                    $final_count++;
                                    $cart_final_products_array[$product_id] = $final_count;
                                }
                            
                            }
                        }
                    }
                }
                
                
                if ( array_search( 'tag', $condition, true ) ) {
                    // Tag Condition Start
                    $final_cart_products_tag_ids = array();
                    $product_dpad_conditions_values = ( isset( $condition['product_dpad_conditions_values'] ) && !empty($condition['product_dpad_conditions_values']) ? array_map( 'intval', $condition['product_dpad_conditions_values'] ) : array() );
                    $all_tags = get_terms( array(
                        'taxonomy' => 'product_tag',
                        'fields'   => 'ids',
                    ) );
                    
                    if ( 'is_equal_to' === $condition['product_dpad_conditions_is'] ) {
                        if ( !empty($product_dpad_conditions_values) ) {
                            foreach ( $product_dpad_conditions_values as $tag_id ) {
                                $final_cart_products_tag_ids[] = $tag_id;
                            }
                        }
                    } elseif ( 'not_in' === $condition['product_dpad_conditions_is'] ) {
                        if ( !empty($product_dpad_conditions_values) ) {
                            $final_cart_products_tag_ids = array_diff( $all_tags, $product_dpad_conditions_values );
                        }
                    }
                    
                    $final_cart_products_tag_ids = array_map( 'intval', $final_cart_products_tag_ids );
                    $cart_value_array = array();
                    $cart_final_products_array = array();
                    foreach ( $cart_array as $value ) {
                        
                        if ( !empty($value['variation_id']) && 0 !== $value['variation_id'] ) {
                            $product_id = $value['variation_id'];
                        } else {
                            $product_id = $value['product_id'];
                        }
                        
                        $_product = wc_get_product( $product_id );
                        $line_item_subtotal = (double) $value['line_subtotal'] + (double) $value['line_subtotal_tax'];
                        $cart_value_array[] = $value;
                        $tag_ids = wp_get_post_terms( $value['product_id'], 'product_tag', array(
                            'fields' => 'ids',
                        ) );
                        foreach ( $tag_ids as $tag_id ) {
                            $product_id = ( $value['variation_id'] ? $value['variation_id'] : $product_id );
                            if ( in_array( $tag_id, $final_cart_products_tag_ids, true ) ) {
                                
                                if ( !array_key_exists( $product_id, $cart_final_products_array ) ) {
                                    $final_count++;
                                    $cart_final_products_array[$product_id] = $final_count;
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
    public function dpad_check_all_passed_general_rule( $is_passed, $has_fee_based, $general_rule_match )
    {
        $main_is_passed = 'no';
        $flag = array();
        
        if ( !empty($is_passed) ) {
            foreach ( $is_passed as $key => $is_passed_value ) {
                
                if ( 'yes' === $is_passed_value[$has_fee_based] ) {
                    $flag[$key] = true;
                } else {
                    $flag[$key] = false;
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
     * Price format
     *
     * @param string $price
     *
     * @return string $price
     * @since  1.3.3
     *
     */
    public function wdpad_price_format( $price )
    {
        $price = floatval( $price );
        return $price;
    }
    
    /**
     * get cart subtotal
     *
     * @return float $cart_subtotal
     * @since  1.5.2
     *
     */
    public function wdpad_get_cart_subtotal()
    {
        $get_customer = WC()->cart->get_customer();
        $get_customer_vat_exempt = WC()->customer->get_is_vat_exempt();
        $tax_display_cart = WC()->cart->get_tax_price_display_mode();
        $wc_prices_include_tax = wc_prices_include_tax();
        $tax_enable = wc_tax_enabled();
        $cart_subtotal = 0;
        
        if ( true === $tax_enable ) {
            
            if ( true === $wc_prices_include_tax ) {
                
                if ( 'incl' === $tax_display_cart && !($get_customer && $get_customer_vat_exempt) ) {
                    $cart_subtotal += WC()->cart->get_subtotal() + WC()->cart->get_subtotal_tax();
                } else {
                    $cart_subtotal += WC()->cart->get_subtotal();
                }
            
            } else {
                
                if ( 'incl' === $tax_display_cart && !($get_customer && $get_customer_vat_exempt) ) {
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
    public function wdpad_check_date_and_time_condition( $dpad_id )
    {
        $dt_valid = false;
        $getFeeStartDate = get_post_meta( $dpad_id, 'dpad_settings_start_date', true );
        $getFeeEndDate = get_post_meta( $dpad_id, 'dpad_settings_end_date', true );
        $getFeeStartTime = get_post_meta( $dpad_id, 'dpad_time_from', true );
        $getFeeEndTime = get_post_meta( $dpad_id, 'dpad_time_to', true );
        //check condition
        $local_nowtimestamp = current_time( 'timestamp' );
        $currentDate = strtotime( gmdate( 'd-m-Y' ) );
        $feeStartDate = ( isset( $getFeeStartDate ) && $getFeeStartDate !== '' ? strtotime( $getFeeStartDate ) : '' );
        $feeEndDate = ( isset( $getFeeEndDate ) && $getFeeEndDate !== '' ? strtotime( $getFeeEndDate ) : '' );
        $feeStartTime = ( isset( $getFeeStartTime ) && $getFeeStartTime !== '' ? strtotime( $getFeeStartTime ) : '' );
        $feeEndTime = ( isset( $getFeeEndTime ) && $getFeeEndTime !== '' ? strtotime( $getFeeEndTime ) : '' );
        if ( ($currentDate >= $feeStartDate || $feeStartDate === '') && ($currentDate <= $feeEndDate || $feeEndDate === '') && ($local_nowtimestamp >= $feeStartTime || $feeStartTime === '') && ($local_nowtimestamp <= $feeEndTime || $feeEndTime === '') ) {
            $dt_valid = true;
        }
        return $dt_valid;
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
    public function wdpad_action_on_discount_list( $update = false )
    {
        global  $sitepress ;
        $discount_ids = [];
        if ( $update ) {
            delete_option( 'wpdad_discount_id_list' );
        }
        $discount_ids = get_option( 'wpdad_discount_id_list' );
        
        if ( !$discount_ids ) {
            
            if ( !empty($sitepress) ) {
                $default_lang = $sitepress->get_default_language();
            } else {
                $get_site_language = get_bloginfo( "language" );
                
                if ( false !== strpos( $get_site_language, '-' ) ) {
                    $get_site_language_explode = explode( '-', $get_site_language );
                    $default_lang = $get_site_language_explode[0];
                } else {
                    $default_lang = $get_site_language;
                }
            
            }
            
            $dpad_args = array(
                'post_type'        => 'wc_dynamic_pricing',
                'post_status'      => 'publish',
                'orderby'          => 'menu_order',
                'order'            => 'ASC',
                'posts_per_page'   => -1,
                'suppress_filters' => false,
                'fields'           => 'ids',
            );
            $get_all_dpad_query = new WP_Query( $dpad_args );
            $get_all_dpad = $get_all_dpad_query->get_posts();
            
            if ( isset( $get_all_dpad ) && !empty($get_all_dpad) ) {
                foreach ( $get_all_dpad as $dpad_id ) {
                    
                    if ( !empty($sitepress) ) {
                        $discount_ids[] = apply_filters(
                            'wpml_object_id',
                            $dpad_id,
                            'wc_dynamic_pricing',
                            true,
                            $default_lang
                        );
                    } else {
                        $discount_ids[] = $dpad_id;
                    }
                
                }
                $discount_ids = array_unique( $discount_ids );
            }
            
            add_option( 'wpdad_discount_id_list', $discount_ids );
        }
        
        return $discount_ids;
    }

}