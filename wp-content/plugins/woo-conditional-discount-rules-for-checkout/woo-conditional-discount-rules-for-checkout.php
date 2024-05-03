<?php

/**
 * Plugin Name: Dynamic Pricing and Discount Rules for WooCommerce
 * Plugin URI:        https://www.thedotstore.com/
 * Description:       With this plugin, you can create and manage complex discount rules in WooCommerce store without the help of a developer.
 * Version:           2.4.1
 * Author:            theDotstore
 * Author URI:        https://www.thedotstore.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-conditional-discount-rules-for-checkout
 * Domain Path:       /languages
 * 
 * WP tested up to:     6.3.1
 * WC tested up to:     8.1.0
 * Requires PHP:        7.2
 * Requires at least:   5.0
 * 
 */
// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( function_exists( 'wcdrfc_fs' ) ) {
    wcdrfc_fs()->set_basename( false, __FILE__ );
    return;
} else {
    
    if ( !function_exists( 'wcdrfc_fs' ) ) {
        // Create a helper function for easy SDK access.
        function wcdrfc_fs()
        {
            global  $wcdrfc_fs ;
            
            if ( !isset( $wcdrfc_fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $wcdrfc_fs = fs_dynamic_init( array(
                    'id'               => '3790',
                    'slug'             => 'woocommerce-conditional-discount-rules-for-checkout',
                    'type'             => 'plugin',
                    'public_key'       => 'pk_25ead80d772c8e17b872aa4b62cb8',
                    'is_premium'       => false,
                    'premium_suffix'   => 'Premium',
                    'has_addons'       => false,
                    'has_paid_plans'   => true,
                    'is_org_compliant' => false,
                    'trial'            => array(
                    'days'               => 14,
                    'is_require_payment' => true,
                ),
                    'menu'             => array(
                    'slug'       => 'wcdrfc-page-get-started',
                    'first-path' => 'admin.php?page=wcdrfc-page-get-started',
                    'contact'    => false,
                    'support'    => false,
                ),
                    'is_live'          => true,
                ) );
            }
            
            return $wcdrfc_fs;
        }
        
        // Init Freemius.
        wcdrfc_fs();
        // Signal that SDK was initiated.
        do_action( 'wcdrfc_fs_loaded' );
    }

}

/**
 * Hide freemius account tab
 *
 * @since    2.4.0
 */

if ( !function_exists( 'wdpad_hide_account_tab' ) ) {
    function wdpad_hide_account_tab()
    {
        return true;
    }
    
    wcdrfc_fs()->add_filter( 'hide_account_tabs', 'wdpad_hide_account_tab' );
}

/**
 * Include plugin header on freemius account page
 *
 * @since    2.4.0
 */

if ( !function_exists( 'wdpad_load_plugin_header_after_account' ) ) {
    function wdpad_load_plugin_header_after_account()
    {
        require_once plugin_dir_path( __FILE__ ) . 'admin/partials/header/plugin-header.php';
    }
    
    wcdrfc_fs()->add_action( 'after_account_details', 'wdpad_load_plugin_header_after_account' );
}

/**
 * Hide billing and payments details from freemius account page
 *
 * @since    2.4.0
 */

if ( !function_exists( 'wdpad_hide_billing_and_payments_info' ) ) {
    function wdpad_hide_billing_and_payments_info()
    {
        return true;
    }
    
    wcdrfc_fs()->add_action( 'hide_billing_and_payments_info', 'wdpad_hide_billing_and_payments_info' );
}

/**
 * Hide powerd by popup from freemius account page
 *
 * @since    3.9.3
 */

if ( !function_exists( 'wdpad_hide_freemius_powered_by' ) ) {
    function wdpad_hide_freemius_powered_by()
    {
        return true;
    }
    
    wcdrfc_fs()->add_action( 'hide_freemius_powered_by', 'wdpad_hide_freemius_powered_by' );
}

//HPOS Compatibility declare
add_action( 'before_woocommerce_init', 'wdpad_hpos_compatibility_declaration' );
if ( !function_exists( 'wdpad_hpos_compatibility_declaration' ) ) {
    function wdpad_hpos_compatibility_declaration()
    {
        if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
        }
    }

}
/**
 * Start plugin setup wizard before license activation screen
 *
 * @since    3.9.3
 */

if ( !function_exists( 'wdpad_load_plugin_setup_wizard_connect_before' ) ) {
    function wdpad_load_plugin_setup_wizard_connect_before()
    {
        require_once plugin_dir_path( __FILE__ ) . 'admin/partials/wcdrfc-plugin-setup-wizard.php';
        ?>
        <div class="tab-panel" id="step5">
            <div class="ds-wizard-wrap">
                <div class="ds-wizard-content">
                    <h2 class="cta-title"><?php 
        echo  esc_html__( 'Activate Plugin', 'woo-conditional-discount-rules-for-checkout' ) ;
        ?></h2>
                </div>
        <?php 
    }
    
    wcdrfc_fs()->add_action( 'connect/before', 'wdpad_load_plugin_setup_wizard_connect_before' );
}

/**
 * End plugin setup wizard after license activation screen
 *
 * @since    3.9.3
 */

if ( !function_exists( 'wdpad_load_plugin_setup_wizard_connect_after' ) ) {
    function wdpad_load_plugin_setup_wizard_connect_after()
    {
        ?>
        </div>
        </div>
        </div>
        </div>
        <?php 
    }
    
    wcdrfc_fs()->add_action( 'connect/after', 'wdpad_load_plugin_setup_wizard_connect_after' );
}


if ( !function_exists( 'detect_plugin_deactivation' ) ) {
    add_action( 'deactivated_plugin', 'detect_plugin_deactivation' );
    function detect_plugin_deactivation( $plugin )
    {
        if ( $plugin === "woocommerce/woocommerce.php" ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
        }
    }

}

if ( !function_exists( 'allowed_html_tags' ) ) {
    function allowed_html_tags( $tags = array() )
    {
        $allowed_tags = array(
            'a'        => array(
            'href'  => array(),
            'title' => array(),
            'class' => array(),
        ),
            'ul'       => array(
            'class' => array(),
        ),
            'li'       => array(
            'class' => array(),
        ),
            'div'      => array(
            'class' => array(),
            'id'    => array(),
        ),
            'select'   => array(
            'id'       => array(),
            'name'     => array(),
            'class'    => array(),
            'multiple' => array(),
            'style'    => array(),
        ),
            'input'    => array(
            'id'    => array(),
            'value' => array(),
            'min'   => array(),
            'max'   => array(),
            'name'  => array(),
            'class' => array(),
            'type'  => array(),
        ),
            'textarea' => array(
            'id'    => array(),
            'name'  => array(),
            'class' => array(),
        ),
            'option'   => array(
            'id'       => array(),
            'selected' => array(),
            'name'     => array(),
            'value'    => array(),
        ),
            'br'       => array(),
            'em'       => array(),
            'strong'   => array(),
            'p'        => array(),
            'b'        => array(
            'style' => array(),
        ),
        );
        if ( !empty($tags) ) {
            foreach ( $tags as $key => $value ) {
                $allowed_tags[$key] = $value;
            }
        }
        return $allowed_tags;
    }

}
if ( !defined( 'WDPAD_PLUGIN_NAME' ) ) {
    define( 'WDPAD_PLUGIN_NAME', 'Dynamic Pricing and Discount Rules' );
}
if ( !defined( 'WDPAD_PLUGIN_VERSION' ) ) {
    define( 'WDPAD_PLUGIN_VERSION', 'v2.4.1' );
}
if ( !defined( 'WDPAD_PLUGIN_URL' ) ) {
    define( 'WDPAD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( !defined( 'WDPAD_PLUGIN_DIR' ) ) {
    define( 'WDPAD_PLUGIN_DIR', dirname( __FILE__ ) );
}
if ( !defined( 'WDPAD_PLUGIN_DIR_PATH' ) ) {
    define( 'WDPAD_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
}
if ( !defined( 'WDPAD_PLUGIN_BASENAME' ) ) {
    define( 'WDPAD_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}

if ( !defined( 'WDPAD_VERSION_LABEL' ) ) {
    define( 'WDPAD_VERSION_LABEL', 'FREE' );
    define( 'WDPAD_PROMOTIONAL_SLUG', 'basic_woo_discount' );
}

if ( !defined( 'WDPAD_STORE_URL' ) ) {
    define( 'WDPAD_STORE_URL', 'https://www.thedotstore.com/' );
}
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woocommerce-dynamic-pricing-and-discount-activator.php
 */
if ( !function_exists( 'activate_woocommerce_conditional_discount_rules_for_checkout_pro' ) ) {
    function activate_woocommerce_conditional_discount_rules_for_checkout_pro()
    {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-dynamic-pricing-and-discount-activator.php';
        Woocommerce_Dynamic_Pricing_And_Discount_Pro_Activator::activate();
    }

}
/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woocommerce-dynamic-pricing-and-discount-deactivator.php
 */
if ( !function_exists( 'deactivate_woocommerce_conditional_discount_rules_for_checkout_pro' ) ) {
    function deactivate_woocommerce_conditional_discount_rules_for_checkout_pro()
    {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-dynamic-pricing-and-discount-deactivator.php';
        Woocommerce_Dynamic_Pricing_And_Discount_Pro_Deactivator::deactivate();
    }

}

if ( !function_exists( 'dpad_deactivate_plugin' ) ) {
    add_action( 'admin_init', 'dpad_deactivate_plugin' );
    function dpad_deactivate_plugin()
    {
        
        if ( is_multisite() ) {
            $active_plugins = get_option( 'active_plugins', array() );
            
            if ( is_multisite() ) {
                $network_active_plugins = get_site_option( 'active_sitewide_plugins', array() );
                $active_plugins = array_merge( $active_plugins, array_keys( $network_active_plugins ) );
                $active_plugins = array_unique( $active_plugins );
            }
            
            if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', $active_plugins ), true ) ) {
                
                if ( wcdrfc_fs()->is__premium_only() && wcdrfc_fs()->can_use_premium_code() ) {
                    deactivate_plugins( 'woocommerce-conditional-discount-rules-for-checkout-premium/woo-conditional-discount-rules-for-checkout.php', true );
                } else {
                    deactivate_plugins( 'woocommerce-conditional-discount-rules-for-checkout/woo-conditional-discount-rules-for-checkout.php', true );
                }
            
            }
        } else {
            if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
                
                if ( wcdrfc_fs()->is__premium_only() && wcdrfc_fs()->can_use_premium_code() ) {
                    deactivate_plugins( 'woocommerce-conditional-discount-rules-for-checkout-premium/woo-conditional-discount-rules-for-checkout.php', true );
                } else {
                    deactivate_plugins( 'woocommerce-conditional-discount-rules-for-checkout/woo-conditional-discount-rules-for-checkout.php', true );
                }
            
            }
        }
    
    }

}

register_activation_hook( __FILE__, 'activate_woocommerce_conditional_discount_rules_for_checkout_pro' );
register_deactivation_hook( __FILE__, 'deactivate_woocommerce_conditional_discount_rules_for_checkout_pro' );
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-dynamic-pricing-and-discount.php';
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
if ( !function_exists( 'run_woocommerce_conditional_discount_rules_for_checkout_pro' ) ) {
    function run_woocommerce_conditional_discount_rules_for_checkout_pro()
    {
        $plugin = new Woocommerce_Dynamic_Pricing_And_Discount_Pro();
        $plugin->run();
    }

}

if ( !function_exists( 'wcdrcp_initialize_plugin' ) ) {
    function wcdrcp_initialize_plugin()
    {
        $active_plugins = get_option( 'active_plugins', array() );
        
        if ( is_multisite() ) {
            $network_active_plugins = get_site_option( 'active_sitewide_plugins', array() );
            $active_plugins = array_merge( $active_plugins, array_keys( $network_active_plugins ) );
            $active_plugins = array_unique( $active_plugins );
        }
        
        $wc_active = in_array( 'woocommerce/woocommerce.php', $active_plugins, true );
        
        if ( current_user_can( 'activate_plugins' ) && $wc_active !== true ) {
            add_action( 'admin_notices', 'wcdrcp_plugin_admin_notice' );
        } else {
            run_woocommerce_conditional_discount_rules_for_checkout_pro();
        }
    
    }
    
    add_action( 'plugins_loaded', 'wcdrcp_initialize_plugin' );
}

if ( !function_exists( 'wcdrcp_plugin_admin_notice' ) ) {
    function wcdrcp_plugin_admin_notice()
    {
        $vpe_plugin = esc_html__( 'Conditional Discount Rules For WooCommerce Checkout ', 'woo-conditional-discount-rules-for-checkout' );
        $wc_plugin = esc_html__( 'WooCommerce', 'woo-conditional-discount-rules-for-checkout' );
        ?>
        <div class="error">
            <p>
                <?php 
        echo  sprintf( esc_html__( '%1$s requires %2$s to be installed & activated!', 'woo-conditional-discount-rules-for-checkout' ), '<strong>' . esc_html( $vpe_plugin ) . '</strong>', '<a href="' . esc_url( 'https://wordpress.org/plugins/woocommerce/' ) . '" target="_blank"><strong>' . esc_html( $wc_plugin ) . '</strong></a>' ) ;
        ?>
            </p>
        </div>
        <?php 
    }

}
if ( !function_exists( 'woocommerce_conditional_discount_rules_for_checkout_path' ) ) {
    function woocommerce_conditional_discount_rules_for_checkout_path()
    {
        return untrailingslashit( plugin_dir_path( __FILE__ ) );
    }

}
if ( !function_exists( 'dpad_convert_array_to_int' ) ) {
    function dpad_convert_array_to_int( $arr )
    {
        foreach ( $arr as $key => $value ) {
            $arr[$key] = (int) $value;
        }
        return $arr;
    }

}