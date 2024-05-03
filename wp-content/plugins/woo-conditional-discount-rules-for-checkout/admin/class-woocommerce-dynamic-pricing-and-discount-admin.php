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
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
class Woocommerce_Dynamic_Pricing_And_Discount_Pro_Admin
{
    const  wdpad_post_type = 'wc_dynamic_pricing' ;
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
     * @param string $plugin_name The name of this plugin.
     * @param string $version     The version of this plugin.
     *
     * @since    1.0.0
     *
     */
    public function __construct( $plugin_name, $version )
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }
    
    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles( $hook )
    {
        
        if ( false !== strpos( $hook, '_page_wcdrfc' ) ) {
            wp_enqueue_style(
                $this->plugin_name . '-jquery-ui-css',
                plugin_dir_url( __FILE__ ) . 'css/jquery-ui.min.css',
                array(),
                $this->version,
                'all'
            );
            wp_enqueue_style(
                $this->plugin_name . '-jquery-timepicker-css',
                plugin_dir_url( __FILE__ ) . 'css/jquery.timepicker.min.css',
                array(),
                $this->version,
                'all'
            );
            wp_enqueue_style(
                $this->plugin_name . 'font-awesome',
                plugin_dir_url( __FILE__ ) . 'css/font-awesome.min.css',
                array(),
                $this->version,
                'all'
            );
            wp_enqueue_style(
                $this->plugin_name . '-webkit-css',
                plugin_dir_url( __FILE__ ) . 'css/webkit.css',
                array(),
                $this->version,
                'all'
            );
            wp_enqueue_style(
                $this->plugin_name . 'main-style',
                plugin_dir_url( __FILE__ ) . 'css/style.css',
                array(),
                'all'
            );
            wp_enqueue_style(
                $this->plugin_name . 'media-css',
                plugin_dir_url( __FILE__ ) . 'css/media.css',
                array(),
                'all'
            );
            wp_enqueue_style(
                $this->plugin_name . 'select2-min',
                plugin_dir_url( __FILE__ ) . 'css/select2.min.css',
                array(),
                'all'
            );
            wp_enqueue_style(
                $this->plugin_name . 'plugin-new-style',
                plugin_dir_url( __FILE__ ) . 'css/plugin-new-style.css',
                array(),
                'all'
            );
            if ( !(wcdrfc_fs()->is__premium_only() && wcdrfc_fs()->can_use_premium_code()) ) {
                wp_enqueue_style(
                    $this->plugin_name . 'upgrade-dashboard-style',
                    plugin_dir_url( __FILE__ ) . 'css/wcdrfc-upgrade-dashboard.css',
                    array(),
                    'all'
                );
            }
            wp_enqueue_style(
                $this->plugin_name . 'plugin-setup-wizard',
                plugin_dir_url( __FILE__ ) . 'css/plugin-setup-wizard.css',
                array(),
                'all'
            );
            wp_enqueue_style( 'wp-color-picker' );
        }
    
    }
    
    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts( $hook )
    {
        wp_enqueue_style( 'wp-jquery-ui-dialog' );
        wp_enqueue_script( 'jquery-ui-accordion' );
        
        if ( false !== strpos( $hook, '_page_wcdrfc' ) ) {
            wp_enqueue_script(
                $this->plugin_name . '-tablesorter-js',
                plugin_dir_url( __FILE__ ) . 'js/jquery.tablesorter.js',
                array( 'jquery' ),
                $this->version,
                false
            );
            wp_enqueue_script(
                $this->plugin_name,
                plugin_dir_url( __FILE__ ) . 'js/woocommerce-dynamic-pricing-and-discount-admin.js',
                array(
                'jquery',
                'jquery-ui-dialog',
                'jquery-ui-accordion',
                'jquery-ui-sortable',
                'wp-color-picker'
            ),
                $this->version,
                false
            );
            wp_enqueue_script( $this->plugin_name . '-select2', plugin_dir_url( __FILE__ ) . 'js/select2.min.js', array(
                'jquery',
                'jquery-ui-dialog',
                'jquery-ui-accordion',
                'jquery-ui-datepicker'
            ) );
            wp_enqueue_script(
                $this->plugin_name . '-timepicker-js',
                plugin_dir_url( __FILE__ ) . 'js/jquery.timepicker.js',
                array( 'jquery' ),
                $this->version,
                false
            );
            wp_enqueue_script( 'jquery-tiptip' );
            wp_enqueue_script( 'jquery-blockui' );
            wp_localize_script( $this->plugin_name, 'coditional_vars', array(
                'dpb_api_url'                    => WDPAD_STORE_URL,
                'setup_wizard_ajax_nonce'        => wp_create_nonce( 'wizard_ajax_nonce' ),
                'ajaxurl'                        => admin_url( 'admin-ajax.php' ),
                'plugin_url'                     => plugin_dir_url( __FILE__ ),
                'product_qty_msg'                => esc_html__( 'This rule will only work if you have selected any one Product Specific option.', 'woo-conditional-discount-rules-for-checkout' ),
                'product_count_msg'              => esc_html__( 'This rule will work if you have selected any one Product Specific option or it will apply to all products.', 'woo-conditional-discount-rules-for-checkout' ),
                'note'                           => esc_html__( 'Note: ', 'woo-conditional-discount-rules-for-checkout' ),
                'warning_msg6'                   => esc_html__( 'You need to select product specific option in Discount Rules for product based option', 'woo-conditional-discount-rules-for-checkout' ),
                'error_msg'                      => esc_html__( 'Please add Discount Rules value', 'woo-conditional-discount-rules-for-checkout' ),
                'warning_msg_per_qty'            => esc_html__( 'Please choose atleast one product or product variation or category or tag condition', 'woo-conditional-discount-rules-for-checkout' ),
                'discount_cost_msg'              => esc_html__( 'Please add discount value which will apply on cart/checkout.', 'woo-conditional-discount-rules-for-checkout' ),
                'select_country'                 => esc_html__( 'Select a Country', 'woo-conditional-discount-rules-for-checkout' ),
                'select_product'                 => esc_html__( 'Select a Product', 'woo-conditional-discount-rules-for-checkout' ),
                'select_category'                => esc_html__( 'Select a Category', 'woo-conditional-discount-rules-for-checkout' ),
                'select_user'                    => esc_html__( 'Select a User', 'woo-conditional-discount-rules-for-checkout' ),
                'select_float_number'            => esc_html__( '0.00', 'woo-conditional-discount-rules-for-checkout' ),
                'select_integer_number'          => esc_html__( '10', 'woo-conditional-discount-rules-for-checkout' ),
                'select2_per_product_ajax'       => 10,
                'select2_product_placeholder'    => esc_html__( 'Select product', 'woo-conditional-discount-rules-for-checkout' ),
                'wcdrfc_ajax_verification_nonce' => wp_create_nonce( 'wcdrfc_ajax_verification' ),
            ) );
        }
    
    }
    
    /**
     * Set Active menu
     */
    public function wdpad_active_menu()
    {
        $screen = get_current_screen();
        if ( !empty($screen) && false !== strpos( $screen->id, '_page_wcdrfc' ) ) {
            ?>
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
    
    public function wdpad_dot_store_menu_conditional()
    {
        $plugin_name = WDPAD_PLUGIN_NAME;
        global  $GLOBALS ;
        if ( empty($GLOBALS['admin_page_hooks']['dots_store']) ) {
            add_menu_page(
                'DotStore Plugins',
                __( 'DotStore Plugins', 'woo-conditional-discount-rules-for-checkout' ),
                'null',
                'dots_store',
                array( $this, 'dot_store_menu_page' ),
                'dashicons-marker',
                25
            );
        }
        $get_hook = add_submenu_page(
            'dots_store',
            WDPAD_PLUGIN_NAME,
            WDPAD_PLUGIN_NAME,
            'manage_options',
            'wcdrfc-rules-list',
            array( $this, 'wdpad_list_page' )
        );
        add_action( "load-{$get_hook}", array( $this, "dpad_screen_options" ) );
        add_submenu_page(
            'dots_store',
            'Get Started',
            'Get Started',
            'manage_options',
            'wcdrfc-page-get-started',
            array( $this, 'wdpad_get_started_page' )
        );
        add_submenu_page(
            'dots_store',
            'Introduction',
            'Introduction',
            'manage_options',
            'wcdrfc-page-information',
            array( $this, 'wdpad_information_page' )
        );
        add_submenu_page(
            'dots_store',
            'General Settings',
            'General Settings',
            'manage_options',
            'wcdrfc-page-general-settings',
            array( $this, 'wdpad_general_settings_page' )
        );
        add_submenu_page(
            'dots_store',
            'Dashboard',
            'Dashboard',
            'manage_options',
            'wcdrfc-upgrade-dashboard',
            array( $this, 'wcdrfc_free_user_upgrade_page' )
        );
        $page_menu = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        if ( !empty($page_menu) && false !== strpos( $page_menu, 'wcdrfc' ) ) {
            remove_filter( 'update_footer', 'core_update_footer' );
        }
    }
    
    public function dot_store_menu_page()
    {
    }
    
    public function wdpad_information_page()
    {
        require_once plugin_dir_path( __FILE__ ) . 'partials/wcdrfc-pro-information-page.php';
    }
    
    public function wdpad_list_page()
    {
        require_once plugin_dir_path( __FILE__ ) . 'partials/wcdrfc-pro-list-page.php';
        $dpad_rule_lising_obj = new DPAD_Rule_Listing_Page();
        $dpad_rule_lising_obj->dpad_sj_output();
    }
    
    public function wdpad_get_started_page()
    {
        require_once plugin_dir_path( __FILE__ ) . 'partials/wcdrfc-pro-get-started-page.php';
    }
    
    /**
     * Screen option for discount rule list
     *
     * @since    1.0.0
     */
    public function dpad_screen_options()
    {
        $args = array(
            'label'   => esc_html__( 'List Per Page', 'woo-conditional-discount-rules-for-checkout' ),
            'default' => 1,
            'option'  => 'dpad_per_page',
        );
        add_screen_option( 'per_page', $args );
        if ( !class_exists( 'WC_Discount_Rules_Table' ) ) {
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/class-wc-discount-rules-table.php';
        }
        $sagar = new WC_Discount_Rules_Table();
        $sagar->_column_headers = $sagar->get_column_info();
    }
    
    /**
     * General Settings page
     * 
     * @since   2.4.0
     */
    public function wdpad_general_settings_page()
    {
        require_once plugin_dir_path( __FILE__ ) . '/partials/wcdrfc-general-settings.php';
    }
    
    /**
     * Premium version info page
     *
     * @since   2.4.0
     */
    public function wcdrfc_free_user_upgrade_page()
    {
        require_once plugin_dir_path( __FILE__ ) . '/partials/wcdrfc-upgrade-dashboard.php';
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
    public function wdpad_set_screen_options( $status, $option, $value )
    {
        $dpad_screens = array( 'dpad_per_page' );
        if ( 'dpad_per_page' === $option ) {
            $value = ( !empty($value) && $value > 0 ? $value : 1 );
        }
        if ( in_array( $option, $dpad_screens, true ) ) {
            return $value;
        }
        return $status;
    }
    
    /**
     * Product specific starts
     */
    public function wdpad_product_dpad_conditions_values_ajax()
    {
        // Security check
        check_ajax_referer( 'wcdrfc_ajax_verification', 'security' );
        // Add new conditions
        $condition = filter_input( INPUT_POST, 'condition', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $count = filter_input( INPUT_POST, 'count', FILTER_SANITIZE_NUMBER_INT );
        $condition = ( isset( $condition ) ? $condition : '' );
        $count = ( isset( $count ) ? $count : '' );
        $html = '';
        
        if ( $condition === 'country' ) {
            $html .= wp_json_encode( $this->wdpad_get_country_list( $count, [], true ) );
        } elseif ( $condition === 'city' ) {
            $html .= 'textarea';
        } elseif ( $condition === 'product' ) {
            $html .= wp_json_encode( $this->wdpad_get_product_list(
                $count,
                [],
                '',
                true
            ) );
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
        
        echo  wp_kses( $html, allowed_html_tags() ) ;
        wp_die();
        // this is required to terminate immediately and return a proper response
    }
    
    /**
     * Function for select country list
     *
     * @param string $count
     * @param array  $selected
     *
     * @return string
     */
    public function wdpad_get_country_list( $count = '', $selected = array(), $json = false )
    {
        $countries_obj = new WC_Countries();
        $getCountries = $countries_obj->__get( 'countries' );
        if ( $json ) {
            return $this->convert_array_to_json( $getCountries );
        }
        $html = '<select name="dpad[product_dpad_conditions_values][value_' . $count . '][]" class="product_dpad_conditions_values product_discount_select product_discount_select multiselect2 product_dpad_conditions_values_country" multiple="multiple">';
        if ( !empty($getCountries) ) {
            foreach ( $getCountries as $code => $country ) {
                $selectedVal = ( is_array( $selected ) && !empty($selected) && in_array( $code, $selected, true ) ? 'selected=selected' : '' );
                $html .= '<option value="' . $code . '" ' . $selectedVal . '>' . $country . '</option>';
            }
        }
        $html .= '</select>';
        return $html;
    }
    
    public function convert_array_to_json( $arr )
    {
        $filter_data = [];
        foreach ( $arr as $key => $value ) {
            $option = [];
            $option['name'] = $value;
            $option['attributes']['value'] = $key;
            $filter_data[] = $option;
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
    public function wdpad_get_product_options( $count = '', $selected = array() )
    {
        global  $sitepress ;
        if ( !empty($sitepress) ) {
            $default_lang = $sitepress->get_default_language();
        }
        $all_selected_product_ids = array();
        if ( !empty($selected) && is_array( $selected ) ) {
            foreach ( $selected as $product_id ) {
                $_product = wc_get_product( $product_id );
                
                if ( 'product_variation' === $_product->post_type ) {
                    $all_selected_product_ids[] = $_product->get_parent_id();
                    //parent_id;
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
        $html = '';
        if ( isset( $get_all_products->posts ) && !empty($get_all_products->posts) ) {
            foreach ( $get_all_products->posts as $get_all_product ) {
                $_product = wc_get_product( $get_all_product->ID );
                
                if ( $_product->is_type( 'variable' ) ) {
                    $variations = $_product->get_available_variations();
                    foreach ( $variations as $value ) {
                        
                        if ( !empty($sitepress) ) {
                            $defaultlang_variation_product_id = apply_filters(
                                'wpml_object_id',
                                $value['variation_id'],
                                'product',
                                true,
                                $default_lang
                            );
                        } else {
                            $defaultlang_variation_product_id = $value['variation_id'];
                        }
                        
                        $baselang_variation_product_ids[] = $defaultlang_variation_product_id;
                    }
                }
                
                
                if ( $_product->is_type( 'simple' ) ) {
                    
                    if ( !empty($sitepress) ) {
                        $defaultlang_simple_product_id = apply_filters(
                            'wpml_object_id',
                            $get_all_product->ID,
                            'product',
                            true,
                            $default_lang
                        );
                    } else {
                        $defaultlang_simple_product_id = $get_all_product->ID;
                    }
                    
                    $defaultlang_simple_product_ids[] = $defaultlang_simple_product_id;
                }
            
            }
        }
        $baselang_product_ids = array_merge( $baselang_variation_product_ids, $defaultlang_simple_product_ids );
        if ( isset( $baselang_product_ids ) && !empty($baselang_product_ids) ) {
            foreach ( $baselang_product_ids as $baselang_product_id ) {
                $selected = array_map( 'intval', $selected );
                $selectedVal = ( is_array( $selected ) && !empty($selected) && in_array( $baselang_product_id, $selected, true ) ? 'selected=selected' : '' );
                if ( '' !== $selectedVal ) {
                    $html .= '<option value="' . $baselang_product_id . '" ' . $selectedVal . '>' . '#' . $baselang_product_id . ' - ' . get_the_title( $baselang_product_id ) . '</option>';
                }
            }
        }
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
    public function wdpad_get_selected_product_list(
        $count = '',
        $selected = array(),
        $action = '',
        $json = false
    )
    {
        if ( empty($selected) ) {
            $selected = array();
        }
        global  $sitepress ;
        if ( !empty($sitepress) ) {
            $default_lang = $sitepress->get_default_language();
        }
        $post_in = '';
        
        if ( 'edit' === $action ) {
            $post_in = $selected;
            $posts_per_page = -1;
        } else {
            $post_in = '';
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
        $html = '<select id="product-filter-' . $count . '" rel-id="' . $count . '" name="dpad_selected_product_list[]" class="all-products-variations" multiple="multiple">';
        if ( isset( $get_all_products->posts ) && !empty($get_all_products->posts) ) {
            foreach ( $get_all_products->posts as $get_all_product ) {
                
                if ( !empty($sitepress) ) {
                    $new_product_id = apply_filters(
                        'wpml_object_id',
                        $get_all_product->ID,
                        'product',
                        true,
                        $default_lang
                    );
                } else {
                    $new_product_id = $get_all_product->ID;
                }
                
                $selected = array_map( 'intval', $selected );
                $selectedVal = ( is_array( $selected ) && !empty($selected) && in_array( $new_product_id, $selected, true ) ? 'selected=selected' : '' );
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
    public function wdpad_get_product_list(
        $count = '',
        $selected = array(),
        $action = '',
        $json = false
    )
    {
        $selected = ( !empty($selected) ? $selected : array() );
        //this need to extra check as some time we got blank STRING.
        global  $sitepress ;
        if ( !empty($sitepress) ) {
            $default_lang = $sitepress->get_default_language();
        }
        $post_in = '';
        
        if ( 'edit' === $action ) {
            $post_in = $selected;
            $posts_per_page = -1;
        } else {
            $post_in = '';
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
        $html = '<select id="product-filter-' . $count . '" rel-id="' . $count . '" name="dpad[product_dpad_conditions_values][value_' . $count . '][]" class="product_filter_select2 product_discount_select product_dpad_conditions_values" multiple="multiple">';
        if ( isset( $get_all_products->posts ) && !empty($get_all_products->posts) ) {
            foreach ( $get_all_products->posts as $get_all_product ) {
                
                if ( !empty($sitepress) ) {
                    $new_product_id = apply_filters(
                        'wpml_object_id',
                        $get_all_product->ID,
                        'product',
                        true,
                        $default_lang
                    );
                } else {
                    $new_product_id = $get_all_product->ID;
                }
                
                $selected = array_map( 'intval', $selected );
                $selectedVal = ( is_array( $selected ) && !empty($selected) && in_array( $new_product_id, $selected, true ) ? 'selected=selected' : '' );
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
     * Function for select cat list
     *
     * @param string $count
     * @param array  $selected
     *
     * @return string
     */
    public function wdpad_get_category_list( $count = '', $selected = array(), $json = false )
    {
        $filter_categories = [];
        global  $sitepress ;
        $taxonomy = 'product_cat';
        $post_status = 'publish';
        $orderby = 'name';
        $hierarchical = 1;
        $empty = 0;
        if ( !empty($sitepress) ) {
            $default_lang = $sitepress->get_default_language();
        }
        $args = array(
            'post_type'      => 'product',
            'post_status'    => $post_status,
            'taxonomy'       => $taxonomy,
            'orderby'        => $orderby,
            'hierarchical'   => $hierarchical,
            'hide_empty'     => $empty,
            'posts_per_page' => -1,
        );
        $get_all_categories = get_categories( $args );
        $html = '<select rel-id="' . $count . '" name="dpad[product_dpad_conditions_values][value_' . $count . '][]" class="product_dpad_conditions_values product_discount_select product_discount_select multiselect2" multiple="multiple">';
        if ( isset( $get_all_categories ) && !empty($get_all_categories) ) {
            foreach ( $get_all_categories as $get_all_category ) {
                
                if ( !empty($sitepress) ) {
                    $new_cat_id = apply_filters(
                        'wpml_object_id',
                        $get_all_category->term_id,
                        'product_cat',
                        true,
                        $default_lang
                    );
                } else {
                    $new_cat_id = $get_all_category->term_id;
                }
                
                $selected = array_map( 'intval', $selected );
                $selectedVal = ( is_array( $selected ) && !empty($selected) && in_array( $new_cat_id, $selected, true ) ? 'selected=selected' : '' );
                $category = get_term_by( 'id', $new_cat_id, 'product_cat' );
                $parent_category = get_term_by( 'id', $category->parent, 'product_cat' );
                
                if ( $category->parent > 0 ) {
                    $html .= '<option value=' . $category->term_id . ' ' . $selectedVal . '>' . '#' . $parent_category->name . '->' . $category->name . '</option>';
                    $filter_categories[$category->term_id] = '#' . $parent_category->name . '->' . $category->name;
                } else {
                    $html .= '<option value=' . $category->term_id . ' ' . $selectedVal . '>' . $category->name . '</option>';
                    $filter_categories[$category->term_id] = $category->name;
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
     * Function for select user list
     *
     */
    public function wdpad_get_user_list( $count = '', $selected = array(), $json = false )
    {
        $filter_users = [];
        $get_all_users = get_users();
        $html = '<select rel-id="' . $count . '" name="dpad[product_dpad_conditions_values][value_' . $count . '][]" class="product_dpad_conditions_values product_discount_select product_discount_select multiselect2" multiple="multiple">';
        if ( isset( $get_all_users ) && !empty($get_all_users) ) {
            foreach ( $get_all_users as $get_all_user ) {
                $selected = array_map( 'intval', $selected );
                $selectedVal = ( is_array( $selected ) && !empty($selected) && in_array( (int) $get_all_user->data->ID, $selected, true ) ? 'selected=selected' : '' );
                $html .= '<option value="' . $get_all_user->data->ID . '" ' . $selectedVal . '>' . $get_all_user->data->user_login . '</option>';
                $filter_users[$get_all_user->data->ID] = $get_all_user->data->user_login;
            }
        }
        $html .= '</select>';
        if ( $json ) {
            return $this->convert_array_to_json( $filter_users );
        }
        return $html;
    }
    
    public function wdpad_welcome_conditional_dpad_screen_do_activation_redirect()
    {
        $this->wdpad_register_post_type();
        // if no activation redirect
        if ( !get_transient( '_welcome_screen_wdpad_pro_mode_activation_redirect_data' ) ) {
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
        wp_safe_redirect( add_query_arg( array(
            'page' => 'wcdrfc-page-get-started',
        ), admin_url( 'admin.php' ) ) );
        exit;
    }
    
    /**
     * Register post type
     *
     * @since    2.3.0
     */
    public function wdpad_register_post_type()
    {
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
    
    public function wdpad_remove_admin_submenus()
    {
        remove_submenu_page( 'dots_store', 'dots_store' );
        remove_submenu_page( 'dots_store', 'wcdrfc-page-information' );
        remove_submenu_page( 'dots_store', 'wcdrfc-rule-add-new' );
        remove_submenu_page( 'dots_store', 'wcdrfc-page-get-started' );
        remove_submenu_page( 'dots_store', 'wcdrfc-page-import-export' );
        remove_submenu_page( 'dots_store', 'wcdrfc-page-general-settings' );
        remove_submenu_page( 'dots_store', 'wcdrfc-page-licenses' );
        remove_submenu_page( 'dots_store', 'wcdrfc-upgrade-dashboard' );
        // Dotstore menu icon css
        echo  '<style>
            .toplevel_page_dots_store .dashicons-marker::after{content:"";border:3px solid;position:absolute;top:14px;left:15px;border-radius:50%;opacity: 0.6;}
		    li.toplevel_page_dots_store:hover .dashicons-marker::after,li.toplevel_page_dots_store.current .dashicons-marker::after{opacity: 1;}
		    @media only screen and (max-width: 960px){
		    	.toplevel_page_dots_store .dashicons-marker::after{left:14px;}
		    }
        </style>' ;
    }
    
    /**
     * Get simple and variable products on Ajax
     *
     * @since 1.0.0
     *
     */
    public function wdpad_simple_and_variation_product_list_ajax()
    {
        // Security check
        check_ajax_referer( 'wcdrfc_ajax_verification', 'security' );
        // Get products
        global  $sitepress ;
        if ( !empty($sitepress) ) {
            $default_lang = $sitepress->get_default_language();
        }
        $json = true;
        $filter_product_list = [];
        $request_value = filter_input( INPUT_GET, 'value', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $posts_per_page = filter_input( INPUT_GET, 'posts_per_page', FILTER_VALIDATE_INT );
        $offset = filter_input( INPUT_GET, 'offset', FILTER_VALIDATE_INT );
        $post_value = ( isset( $request_value ) ? sanitize_text_field( $request_value ) : '' );
        $posts_per_page = ( isset( $posts_per_page ) ? intval( $posts_per_page ) : 0 );
        $offset = ( isset( $offset ) ? intval( $offset ) : 0 );
        $baselang_simple_product_ids = array();
        $baselang_variation_product_ids = array();
        function wdpad_posts_where( $where, $wp_query )
        {
            global  $wpdb ;
            $search_term = $wp_query->get( 'search_pro_title' );
            
            if ( !empty($search_term) ) {
                $search_term_like = $wpdb->esc_like( $search_term );
                $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $search_term_like ) . '%\'';
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
            'offset'           => $posts_per_page * ($offset - 1),
        );
        add_filter(
            'posts_where',
            'wdpad_posts_where',
            10,
            2
        );
        $get_wp_query = new WP_Query( $product_args );
        remove_filter(
            'posts_where',
            'wdpad_posts_where',
            10,
            2
        );
        $get_all_products = $get_wp_query->posts;
        $baselang_product_ids = array();
        if ( isset( $get_all_products ) && !empty($get_all_products) ) {
            foreach ( $get_all_products as $get_all_product ) {
                $_product = wc_get_product( $get_all_product->ID );
                
                if ( !$_product->is_type( 'variable' ) ) {
                    
                    if ( !empty($sitepress) ) {
                        $defaultlang_simple_product_id = apply_filters(
                            'wpml_object_id',
                            $get_all_product->ID,
                            'product',
                            true,
                            $default_lang
                        );
                    } else {
                        $defaultlang_simple_product_id = $get_all_product->ID;
                    }
                    
                    $baselang_product_ids[] = $defaultlang_simple_product_id;
                }
            
            }
        }
        $html = '';
        if ( isset( $baselang_product_ids ) && !empty($baselang_product_ids) ) {
            foreach ( $baselang_product_ids as $baselang_product_id ) {
                $html .= '<option value="' . $baselang_product_id . '">' . '#' . $baselang_product_id . ' - ' . get_the_title( $baselang_product_id ) . '</option>';
                $filter_product_list[] = array( $baselang_product_id, '#' . $baselang_product_id . ' - ' . get_the_title( $baselang_product_id ) );
            }
        }
        
        if ( $json ) {
            echo  wp_json_encode( $filter_product_list ) ;
            wp_die();
        }
        
        echo  wp_kses( $html, allowed_html_tags() ) ;
        wp_die();
    }
    
    /**
     * Get products on Ajax 
     *
     * @since 1.0.0
     *
     */
    public function wdpad_product_dpad_conditions_values_product_ajax()
    {
        // Security check
        check_ajax_referer( 'wcdrfc_ajax_verification', 'security' );
        // Get products
        global  $sitepress ;
        if ( !empty($sitepress) ) {
            $default_lang = $sitepress->get_default_language();
        }
        $json = true;
        $filter_product_list = [];
        $request_value = filter_input( INPUT_GET, 'search', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $posts_per_page = filter_input( INPUT_GET, 'posts_per_page', FILTER_VALIDATE_INT );
        $offset = filter_input( INPUT_GET, 'offset', FILTER_VALIDATE_INT );
        $post_value = ( isset( $request_value ) ? sanitize_text_field( $request_value ) : '' );
        $posts_per_page = ( isset( $posts_per_page ) ? intval( $posts_per_page ) : 10 );
        $offset = ( isset( $offset ) ? intval( $offset ) : 0 );
        $baselang_product_ids = array();
        function wdpad_posts_where( $where, $wp_query )
        {
            global  $wpdb ;
            $search_term = $wp_query->get( 'search_pro_title' );
            
            if ( isset( $search_term ) ) {
                $search_term_like = $wpdb->esc_like( $search_term );
                $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $search_term_like ) . '%\'';
            }
            
            return $where;
        }
        
        $product_args = array(
            'post_type'      => 'product',
            'posts_per_page' => $posts_per_page,
            'offset'         => $posts_per_page * ($offset - 1),
            's'              => $post_value,
            'post_status'    => 'publish',
            'orderby'        => 'title',
            'order'          => 'ASC',
            'show_posts'     => -1,
        );
        add_filter(
            'posts_where',
            'wdpad_posts_where',
            10,
            2
        );
        $wp_query = new WP_Query( $product_args );
        remove_filter(
            'posts_where',
            'wdpad_posts_where',
            10,
            2
        );
        $get_all_products = $wp_query->posts;
        if ( isset( $get_all_products ) && !empty($get_all_products) ) {
            foreach ( $get_all_products as $get_all_product ) {
                
                if ( !empty($sitepress) ) {
                    $defaultlang_product_id = apply_filters(
                        'wpml_object_id',
                        $get_all_product->ID,
                        'product',
                        true,
                        $default_lang
                    );
                } else {
                    $defaultlang_product_id = $get_all_product->ID;
                }
                
                $baselang_product_ids[] = $defaultlang_product_id;
            }
        }
        $html = '';
        if ( isset( $baselang_product_ids ) && !empty($baselang_product_ids) ) {
            foreach ( $baselang_product_ids as $baselang_product_id ) {
                $_product = wc_get_product( $baselang_product_id );
                $html .= '<option value="' . $baselang_product_id . '">' . '#' . $baselang_product_id . ' - ' . get_the_title( $baselang_product_id ) . '</option>';
                
                if ( $_product->get_type() === 'simple' ) {
                    
                    if ( $_product->get_type() === 'variable' ) {
                        $vari = "(All variation)";
                    } else {
                        $vari = "";
                    }
                    
                    $filter_product = array();
                    $filter_product['id'] = $baselang_product_id;
                    $filter_product['text'] = '#' . $baselang_product_id . ' - ' . get_the_title( $baselang_product_id ) . $vari;
                    $filter_product_list[] = $filter_product;
                }
            
            }
        }
        
        if ( $json ) {
            echo  wp_json_encode( $filter_product_list ) ;
            wp_die();
        }
        
        echo  wp_kses( $html, allowed_html_tags() ) ;
        wp_die();
    }
    
    public function wdpad_product_dpad_conditions_varible_values_product_ajax()
    {
        // Security check
        check_ajax_referer( 'wcdrfc_ajax_verification', 'security' );
        // Get variable products
        $json = true;
        global  $sitepress ;
        $post_value = filter_input( INPUT_GET, 'search', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $posts_per_page = filter_input( INPUT_GET, 'posts_per_page', FILTER_VALIDATE_INT );
        $offset = filter_input( INPUT_GET, 'offset', FILTER_VALIDATE_INT );
        $post_value = ( isset( $post_value ) ? $post_value : '' );
        $posts_per_page = ( isset( $posts_per_page ) ? intval( $posts_per_page ) : 10 );
        $offset = ( isset( $offset ) ? intval( $offset ) : 0 );
        $baselang_product_ids = array();
        if ( !empty($sitepress) ) {
            $default_lang = $sitepress->get_default_language();
        }
        function wdpad_posts_wheres( $where, $wp_query )
        {
            global  $wpdb ;
            $search_term = $wp_query->get( 'search_pro_title' );
            
            if ( isset( $search_term ) ) {
                $search_term_like = $wpdb->esc_like( $search_term );
                $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $search_term_like ) . '%\'';
            }
            
            return $where;
        }
        
        $product_args = array(
            'post_type'        => 'product',
            'posts_per_page'   => $posts_per_page,
            'offset'           => $posts_per_page * ($offset - 1),
            'search_pro_title' => $post_value,
            'post_status'      => 'publish',
            'orderby'          => 'title',
            'order'            => 'ASC',
        );
        add_filter(
            'posts_where',
            'wdpad_posts_wheres',
            10,
            2
        );
        $get_all_products = new WP_Query( $product_args );
        remove_filter(
            'posts_where',
            'wdpad_posts_wheres',
            10,
            2
        );
        if ( !empty($get_all_products) ) {
            foreach ( $get_all_products->posts as $get_all_product ) {
                $_product = wc_get_product( $get_all_product->ID );
                
                if ( $_product->is_type( 'variable' ) ) {
                    $variations = $_product->get_available_variations();
                    foreach ( $variations as $value ) {
                        
                        if ( !empty($sitepress) ) {
                            $defaultlang_product_id = apply_filters(
                                'wpml_object_id',
                                $value['variation_id'],
                                'product',
                                true,
                                $default_lang
                            );
                        } else {
                            $defaultlang_product_id = $value['variation_id'];
                        }
                        
                        $baselang_product_ids[] = $defaultlang_product_id;
                    }
                }
            
            }
        }
        $html = '';
        $filter_variable_product_list = [];
        if ( isset( $baselang_product_ids ) && !empty($baselang_product_ids) ) {
            foreach ( $baselang_product_ids as $baselang_product_id ) {
                $html .= '<option value="' . $baselang_product_id . '">' . '#' . $baselang_product_id . ' - ' . get_the_title( $baselang_product_id ) . '</option>';
                $filter_variable_product = array();
                $filter_variable_product['id'] = $baselang_product_id;
                $filter_variable_product['text'] = '#' . $baselang_product_id . ' - ' . str_replace( '&#8211;', '-', get_the_title( $baselang_product_id ) );
                $filter_variable_product_list[] = $filter_variable_product;
            }
        }
        
        if ( $json ) {
            echo  wp_json_encode( $filter_variable_product_list ) ;
            wp_die();
        }
        
        echo  wp_kses( $html, allowed_html_tags() ) ;
        wp_die();
    }
    
    function wdpad_admin_footer_review()
    {
        echo  sprintf( wp_kses( __( 'If you like <strong>%2$s</strong> plugin, please leave us ★★★★★ ratings on <a href="%1$s" target="_blank">DotStore</a>.', 'woo-conditional-discount-rules-for-checkout' ), array(
            'strong' => array(),
            'a'      => array(
            'href'   => array(),
            'target' => 'blank',
        ),
        ) ), esc_url( 'https://wordpress.org/support/plugin/woo-conditional-discount-rules-for-checkout/reviews/#new-post' ), esc_html( WDPAD_PLUGIN_NAME ) ) ;
    }
    
    function conditional_discount_sorting()
    {
        global  $plugin_public ;
        check_ajax_referer( 'sorting_conditional_fee_action', 'sorting_conditional_fee' );
        $post_type = self::wdpad_post_type;
        $getPaged = filter_input( INPUT_POST, 'paged', FILTER_SANITIZE_NUMBER_INT );
        $getListingArray = filter_input(
            INPUT_POST,
            'listing',
            FILTER_SANITIZE_NUMBER_INT,
            FILTER_REQUIRE_ARRAY
        );
        $paged = ( !empty($getPaged) ? $getPaged : 1 );
        $listinbgArray = ( !empty($getListingArray) ? array_map( 'intval', wp_unslash( $getListingArray ) ) : array() );
        $query_args = array(
            'post_type'      => $post_type,
            'post_status'    => array( 'publish', 'draft' ),
            'posts_per_page' => -1,
            'orderby'        => array(
            'menu_order' => 'ASC',
            'post_date'  => 'DESC',
        ),
            'fields'         => 'ids',
        );
        $post_list = new WP_Query( $query_args );
        $results = $post_list->posts;
        //Create the list of ID's
        $objects_ids = array();
        foreach ( $results as $result ) {
            $objects_ids[] = (int) $result;
        }
        //Here we switch order
        $per_page = get_user_option( 'dpad_per_page' );
        $per_page = ( !empty($per_page) || $per_page > 1 ? $per_page : 1 );
        $edit_start_at = $paged * $per_page - $per_page;
        $index = 0;
        for ( $i = $edit_start_at ;  $i < $edit_start_at + $per_page ;  $i++ ) {
            if ( !isset( $objects_ids[$i] ) ) {
                break;
            }
            $objects_ids[$i] = (int) $listinbgArray[$index];
            $index++;
        }
        //Update the menu_order within database
        foreach ( $objects_ids as $menu_order => $id ) {
            $data = array(
                'menu_order' => $menu_order,
                'ID'         => $id,
            );
            wp_update_post( $data );
            clean_post_cache( $id );
        }
        //Refresh our cache after bulk delete
        $plugin_public->wdpad_action_on_discount_list( true );
        wp_send_json_success( array(
            'message' => esc_html__( 'Discount rule has been updated.', 'woo-conditional-discount-rules-for-checkout' ),
        ) );
    }
    
    public function dpad_updated_message( $message, $validation_msg )
    {
        if ( empty($message) ) {
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
        
        if ( !empty($updated_message) ) {
            echo  sprintf( '<div id="message" class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html( $updated_message ) ) ;
            return false;
        }
        
        
        if ( !empty($failed_messsage) ) {
            echo  sprintf( '<div id="message" class="notice notice-error is-dismissible"><p>%s</p></div>', esc_html( $failed_messsage ) ) ;
            return false;
        }
        
        
        if ( !empty($validated_messsage) ) {
            echo  sprintf( '<div id="message" class="notice notice-error is-dismissible"><p>%s</p></div>', esc_html( $validated_messsage ) ) ;
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
    public function wdpad_change_status_from_list_section()
    {
        // Security check
        check_ajax_referer( 'wcdrfc_ajax_verification', 'security' );
        // Change rule status
        $get_current_dpad_id = filter_input( INPUT_POST, 'current_dpad_id', FILTER_SANITIZE_NUMBER_INT );
        $get_current_value = filter_input( INPUT_POST, 'current_value', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        if ( !isset( $get_current_dpad_id ) ) {
            wp_send_json_error( esc_html__( 'Something went wrong', 'woo-conditional-discount-rules-for-checkout' ) );
        }
        $post_id = ( isset( $get_current_dpad_id ) ? absint( $get_current_dpad_id ) : '' );
        $current_value = ( isset( $get_current_value ) ? sanitize_text_field( $get_current_value ) : '' );
        
        if ( 'true' === $current_value ) {
            update_post_meta( $post_id, 'dpad_settings_status', 'on' );
            wp_send_json_success( esc_html__( 'Discount status has been enabled successfully.', 'woo-conditional-discount-rules-for-checkout' ) );
        } else {
            update_post_meta( $post_id, 'dpad_settings_status', 'off' );
            wp_send_json_success( esc_html__( 'Discount status has been disabled successfully.', 'woo-conditional-discount-rules-for-checkout' ) );
        }
    
    }
    
    public function wdpad_save_general_settings()
    {
        $nonce = filter_input( INPUT_POST, 'dpad_save_general_setting_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        if ( !wp_verify_nonce( $nonce, 'dpad_save_general_setting' ) ) {
            return false;
        }
        $get_adjustment_discount_type = filter_input( INPUT_POST, 'dpad_gs_adjustment_discount_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $get_adjustment_discount_type = ( !empty($get_adjustment_discount_type) ? $get_adjustment_discount_type : 'first' );
        update_option( 'wdpad_gs_adjustment_discount_type', $get_adjustment_discount_type );
        $get_sequential_discount = filter_input( INPUT_POST, 'dpad_gs_sequential_discount', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $get_sequential_discount = ( !empty($get_sequential_discount) ? $get_sequential_discount : 'no' );
        update_option( 'wdpad_gs_sequential_discount', $get_sequential_discount );
        $redirect_url = add_query_arg( array(
            'page'    => 'wcdrfc-page-general-settings',
            'message' => 'validated',
        ), admin_url( 'admin.php' ) );
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
    public function wdpad_default_hidden_columns( $hidden, WP_Screen $screen, $use_defaults )
    {
        
        if ( false === $hidden && !empty($screen->id) && false !== strpos( $screen->id, '_page_wcdrfc-rules-list' ) ) {
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
    public function wdpad_get_promotional_bar( $plugin_slug = '' )
    {
        $promotional_bar_upi_url = WDPAD_STORE_URL . 'wp-json/dpb-promotional-banner/v2/dpb-promotional-banner?' . wp_rand();
        $promotional_banner_request = wp_remote_get( $promotional_bar_upi_url );
        //phpcs:ignore
        
        if ( empty($promotional_banner_request->errors) ) {
            $promotional_banner_request_body = $promotional_banner_request['body'];
            $promotional_banner_request_body = json_decode( $promotional_banner_request_body, true );
            echo  '<div class="dynamicbar_wrapper">' ;
            if ( !empty($promotional_banner_request_body) && is_array( $promotional_banner_request_body ) ) {
                foreach ( $promotional_banner_request_body as $promotional_banner_request_body_data ) {
                    $promotional_banner_id = $promotional_banner_request_body_data['promotional_banner_id'];
                    $promotional_banner_cookie = $promotional_banner_request_body_data['promotional_banner_cookie'];
                    $promotional_banner_image = $promotional_banner_request_body_data['promotional_banner_image'];
                    $promotional_banner_description = $promotional_banner_request_body_data['promotional_banner_description'];
                    $promotional_banner_button_group = $promotional_banner_request_body_data['promotional_banner_button_group'];
                    $dpb_schedule_campaign_type = $promotional_banner_request_body_data['dpb_schedule_campaign_type'];
                    $promotional_banner_target_audience = $promotional_banner_request_body_data['promotional_banner_target_audience'];
                    
                    if ( !empty($promotional_banner_target_audience) ) {
                        $plugin_keys = array();
                        
                        if ( is_array( $promotional_banner_target_audience ) ) {
                            foreach ( $promotional_banner_target_audience as $list ) {
                                $plugin_keys[] = $list['value'];
                            }
                        } else {
                            $plugin_keys[] = $promotional_banner_target_audience['value'];
                        }
                        
                        $display_banner_flag = false;
                        if ( in_array( 'all_customers', $plugin_keys, true ) || in_array( $plugin_slug, $plugin_keys, true ) ) {
                            $display_banner_flag = true;
                        }
                    }
                    
                    if ( true === $display_banner_flag ) {
                        
                        if ( 'default' === $dpb_schedule_campaign_type ) {
                            $banner_cookie_show = filter_input( INPUT_COOKIE, 'banner_show_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            $banner_cookie_visible_once = filter_input( INPUT_COOKIE, 'banner_show_once_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            $flag = false;
                            
                            if ( empty($banner_cookie_show) && empty($banner_cookie_visible_once) ) {
                                setcookie( 'banner_show_' . $promotional_banner_cookie, 'yes', time() + 86400 * 7 );
                                //phpcs:ignore
                                setcookie( 'banner_show_once_' . $promotional_banner_cookie, 'yes' );
                                //phpcs:ignore
                                $flag = true;
                            }
                            
                            $banner_cookie_show = filter_input( INPUT_COOKIE, 'banner_show_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            
                            if ( !empty($banner_cookie_show) || true === $flag ) {
                                $banner_cookie = filter_input( INPUT_COOKIE, 'banner_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                                $banner_cookie = ( isset( $banner_cookie ) ? $banner_cookie : '' );
                                
                                if ( empty($banner_cookie) && 'yes' !== $banner_cookie ) {
                                    ?>
                            	<div class="dpb-popup <?php 
                                    echo  ( isset( $promotional_banner_cookie ) ? esc_html( $promotional_banner_cookie ) : 'default-banner' ) ;
                                    ?>">
                                    <?php 
                                    
                                    if ( !empty($promotional_banner_image) ) {
                                        ?>
                                        <img src="<?php 
                                        echo  esc_url( $promotional_banner_image ) ;
                                        ?>"/>
                                        <?php 
                                    }
                                    
                                    ?>
                                    <div class="dpb-popup-meta">
                                        <p>
                                            <?php 
                                    echo  wp_kses_post( str_replace( array( '<p>', '</p>' ), '', $promotional_banner_description ) ) ;
                                    if ( !empty($promotional_banner_button_group) ) {
                                        foreach ( $promotional_banner_button_group as $promotional_banner_button_group_data ) {
                                            ?>
                                                    <a href="<?php 
                                            echo  esc_url( $promotional_banner_button_group_data['promotional_banner_button_link'] ) ;
                                            ?>" target="_blank"><?php 
                                            echo  esc_html( $promotional_banner_button_group_data['promotional_banner_button_text'] ) ;
                                            ?></a>
                                                    <?php 
                                        }
                                    }
                                    ?>
                                    	</p>
                                    </div>
                                    <a href="javascript:void(0);" data-bar-id="<?php 
                                    echo  esc_attr( $promotional_banner_id ) ;
                                    ?>" data-popup-name="<?php 
                                    echo  ( isset( $promotional_banner_cookie ) ? esc_html( $promotional_banner_cookie ) : 'default-banner' ) ;
                                    ?>" class="dpbpop-close"><svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10"><path id="Icon_material-close" data-name="Icon material-close" d="M17.5,8.507,16.493,7.5,12.5,11.493,8.507,7.5,7.5,8.507,11.493,12.5,7.5,16.493,8.507,17.5,12.5,13.507,16.493,17.5,17.5,16.493,13.507,12.5Z" transform="translate(-7.5 -7.5)" fill="#acacac"/></svg></a>
                                </div>
                                <?php 
                                }
                            
                            }
                        
                        } else {
                            $banner_cookie_show = filter_input( INPUT_COOKIE, 'banner_show_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            $banner_cookie_visible_once = filter_input( INPUT_COOKIE, 'banner_show_once_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            $flag = false;
                            
                            if ( empty($banner_cookie_show) && empty($banner_cookie_visible_once) ) {
                                setcookie( 'banner_show_' . $promotional_banner_cookie, 'yes' );
                                //phpcs:ignore
                                setcookie( 'banner_show_once_' . $promotional_banner_cookie, 'yes' );
                                //phpcs:ignore
                                $flag = true;
                            }
                            
                            $banner_cookie_show = filter_input( INPUT_COOKIE, 'banner_show_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            
                            if ( !empty($banner_cookie_show) || true === $flag ) {
                                $banner_cookie = filter_input( INPUT_COOKIE, 'banner_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                                $banner_cookie = ( isset( $banner_cookie ) ? $banner_cookie : '' );
                                
                                if ( empty($banner_cookie) && 'yes' !== $banner_cookie ) {
                                    ?>
                    			<div class="dpb-popup <?php 
                                    echo  ( isset( $promotional_banner_cookie ) ? esc_html( $promotional_banner_cookie ) : 'default-banner' ) ;
                                    ?>">
                                    <?php 
                                    
                                    if ( !empty($promotional_banner_image) ) {
                                        ?>
                                            <img src="<?php 
                                        echo  esc_url( $promotional_banner_image ) ;
                                        ?>"/>
                                        <?php 
                                    }
                                    
                                    ?>
                                    <div class="dpb-popup-meta">
                                        <p>
                                            <?php 
                                    echo  wp_kses_post( str_replace( array( '<p>', '</p>' ), '', $promotional_banner_description ) ) ;
                                    if ( !empty($promotional_banner_button_group) ) {
                                        foreach ( $promotional_banner_button_group as $promotional_banner_button_group_data ) {
                                            ?>
                                                    <a href="<?php 
                                            echo  esc_url( $promotional_banner_button_group_data['promotional_banner_button_link'] ) ;
                                            ?>" target="_blank"><?php 
                                            echo  esc_html( $promotional_banner_button_group_data['promotional_banner_button_text'] ) ;
                                            ?></a>
                                                    <?php 
                                        }
                                    }
                                    ?>
                                        </p>
                                    </div>
                                    <a href="javascript:void(0);" data-popup-name="<?php 
                                    echo  ( isset( $promotional_banner_cookie ) ? esc_html( $promotional_banner_cookie ) : 'default-banner' ) ;
                                    ?>" class="dpbpop-close"><svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10"><path id="Icon_material-close" data-name="Icon material-close" d="M17.5,8.507,16.493,7.5,12.5,11.493,8.507,7.5,7.5,8.507,11.493,12.5,7.5,16.493,8.507,17.5,12.5,13.507,16.493,17.5,17.5,16.493,13.507,12.5Z" transform="translate(-7.5 -7.5)" fill="#acacac"/></svg></a>
                                </div>
                                <?php 
                                }
                            
                            }
                        
                        }
                    
                    }
                }
            }
            echo  '</div>' ;
        }
    
    }
    
    /**
     * Get and save plugin setup wizard data
     * 
     * @since    2.4.0
     * 
     */
    public function wdpad_plugin_setup_wizard_submit()
    {
        check_ajax_referer( 'wizard_ajax_nonce', 'nonce' );
        $survey_list = filter_input( INPUT_GET, 'survey_list', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        if ( !empty($survey_list) && 'Select One' !== $survey_list ) {
            update_option( 'wdpad_where_hear_about_us', $survey_list );
        }
        wp_die();
    }
    
    /**
     * Send setup wizard data to sendinblue
     * 
     * @since    2.4.0
     * 
     */
    public function wdpad_send_wizard_data_after_plugin_activation()
    {
        $send_wizard_data = filter_input( INPUT_GET, 'send-wizard-data', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        if ( isset( $send_wizard_data ) && !empty($send_wizard_data) ) {
            
            if ( !get_option( 'wdpad_data_submited_in_sendiblue' ) ) {
                $wdpad_where_hear = get_option( 'wdpad_where_hear_about_us' );
                $get_user = wcdrfc_fs()->get_user();
                $data_insert_array = array();
                if ( isset( $get_user ) && !empty($get_user) ) {
                    $data_insert_array = array(
                        'user_email'              => $get_user->email,
                        'ACQUISITION_SURVEY_LIST' => $wdpad_where_hear,
                    );
                }
                $feedback_api_url = WDPAD_STORE_URL . 'wp-json/dotstore-sendinblue-data/v2/dotstore-sendinblue-data?' . wp_rand();
                $query_url = $feedback_api_url . '&' . http_build_query( $data_insert_array );
                
                if ( function_exists( 'vip_safe_wp_remote_get' ) ) {
                    $response = vip_safe_wp_remote_get(
                        $query_url,
                        3,
                        1,
                        20
                    );
                } else {
                    $response = wp_remote_get( $query_url );
                    //phpcs:ignore
                }
                
                
                if ( !is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
                    update_option( 'wdpad_data_submited_in_sendiblue', '1' );
                    delete_option( 'wdpad_where_hear_about_us' );
                }
            
            }
        
        }
    }

}