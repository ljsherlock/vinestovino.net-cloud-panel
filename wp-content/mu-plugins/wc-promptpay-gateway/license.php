<?php

namespace Boostpress\Plugins\WC_Promptpay_Gateway;

class License
{
    public function __construct()
    {
        /**
         * ID
         */
        $this->id = 'BP-PROMPTPAY-GATEWAY';

        /*
         * This product_unique_id must match with product id on live site
         * Don't forget to change this property when copy License class to another place 
         */
        $this->product_unique_id = 'BP-PROMPTPAY-GATEWAY';

        /**
         * This plugin_name property used for display to user
         */
        $this->plugin_name = 'WC Promptpay Gateway';

        /**
         * License host url
         */
        $this->sl_app_api_url = 'https://boostpress.com/index.php';

        /**
         * Local website domain name (website that install boostpress plugin)
         * Use for register to license server
         */
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $this->sl_instance = str_replace($protocol, "", get_bloginfo('wpurl'));
        
        /**
         * Must match with main plugin's text domain
         */
        $this->language_domain = 'wc-promptpay-gateway';

        /**
         * Plugin file name 
         */
        $this->plugin_filename = 'wc-promptpay-gateway.php';

        /**
         * Plugin file path
         */
        $this->plugin_filepath =  plugin_dir_path(__FILE__).$this->plugin_filename;

        /**
         * Plugin base name 
         */
        $this->plugin_basename = plugin_basename($this->plugin_filepath);

        /**
         * Plugin current version 
         */
        $plugin_data = get_file_data($this->plugin_filepath, array('Version' => 'Version'), false);
        $this->sl_version = $plugin_data['Version'];

        /**
         * WooCommerce software license API number
         * Now 1.1
         */
        $this->api_version = 1.1;

        /**
         * Slug is folder's name
         */
        $this->slug = 'wc-promptpay-gateway';


        /**
         * option_name in database
         */
        $this->option_name = strtolower($this->product_unique_id).'_license';


        /**
         * expire_option in database
         */
        $this->expire_option = strtolower($this->product_unique_id).'_expire';


        /**
         * daily_option in database
         */
        $this->daily_option = strtolower($this->product_unique_id).'_daily';


        /**
         * Nonce field name
         */
        $this->nonce_field_name = strtolower($this->product_unique_id).'_nonce_field';


        /**
         * license field name
         */
        $this->license_field_name = strtolower($this->product_unique_id).'_license_field';


        /* Add activation license page */
        add_action('admin_menu', array($this, 'activation_license_page'));
        add_action('init', array($this, 'license_activate'));
        add_action('init', array($this, 'license_deactivate'));


        /**
         * Check license expiration once a day
         */
        add_action('init', array($this, 'check_license_status'));


        /* Notice administrator */
        add_action('init', array($this, 'force_notice'));


        /**
         * Add settings link under plugin name on plugins page.
         */
        add_filter('plugin_action_links', array($this, 'plugin_action_links'), 10, 2);
        

        /**
         * Run updater
         */
        add_action('after_setup_theme', array($this, 'run_updater'));
    }

    
    /**
     * Show notice to administrator if plugin is'n activated.
     */
    public function force_notice()
    {
        $license_options = $this->get_license_options();
        $expire_options = $this->get_expire_options();

        if ($license_options['status'] == 'unactivated') {
            add_action('admin_notices', function () {
                ?>
                <div class="notice notice-error">
                    <p><?php printf(__('%1$s is not activated. Click %2$s to enter license key.', $this->language_domain), $this->plugin_name, '<a href="options-general.php?page='.strtolower($this->product_unique_id).'">' . __('License key page', $this->language_domain) . '</a>'); ?></p>
                </div>
                <?php
            });
        }

        if ($expire_options['license_expire'] == 'yes') {
            add_action('admin_notices', function () {
                ?>
                <div class="notice notice-error">
                    <p><?php printf(__('%1$s is expired. Please contact admin@boostpress.com OR LINE <a href="https://line.me/R/ti/p/%40bcb6703p">@boostpress</a> OR Facebook <a href="https://www.facebook.com/Boostpress.co">@boostpress.co</a>', $this->language_domain), $this->plugin_name); ?></p>
                </div>
                <?php
            });
        }
    }


    /**
     * Get kasikorn payment gateway license options
     * @return array
     */
    public function get_license_options()
    {
        $defaults = array(
            'key'    => '',
            'status' => 'unactivated',
        );
        return wp_parse_args(get_option($this->option_name), $defaults);
    }


    /**
     * Get kasikorn payment gateway expired license
     * @return array
     */
    public function get_expire_options()
    {
        $defaults = array(
            'license_expire' => '',
        );
        return wp_parse_args(get_option($this->expire_option), $defaults);
    }


    /**
     * Get daily task
     * @return array
     */
    public function get_daily_options()
    {
        $defaults = array(
            'last_execute' => '',
        );
        return wp_parse_args(get_option($this->daily_option), $defaults);
    }


    /**
     * License activation
     */
    public function license_activate()
    {
        if (isset($_POST[$this->nonce_field_name]) && wp_verify_nonce($_POST[$this->nonce_field_name], 'activate')) {

            $license_key = $_POST[$this->license_field_name];

            $args = array(
                'woo_sl_action'     => 'activate',
                'licence_key'       => $license_key,
                'product_unique_id' => $this->product_unique_id,
                'domain'            => $this->sl_instance
            );
            $request_uri = $this->sl_app_api_url . '?' . http_build_query($args);
            $data = wp_remote_get($request_uri);

            if ( $data instanceof \WP_Error ||  is_wp_error($data) || $data['response']['code'] != 200) {
                add_action('admin_notices', function () {
                    ?>
                    <div class="notice notice-error">
                        <p><?php _e('There was a problem establishing a connection to the API server', $this->language_domain); ?></p>
                    </div>
                    <?php
                });
 
            }else{

                $data_body = json_decode($data['body']);
                $data_body = $data_body[0];
                if (isset($data_body->status)) {
                    if ($data_body->status == 'success' && $data_body->status_code == 's100') {
                        $params = array(
                            'key'    => $license_key,
                            'status' => 'activated',
                        );
                        update_option($this->option_name, $params);
    
                        // Update expire option
                        $params = array(
                            'license_expire'    => '',
                        );
                        update_option($this->expire_option, $params);
    
                        add_action('admin_notices', function () {
                            ?>
                            <div class="notice notice-success">
                                <p><?php _e('The license is active and the software is active', $this->language_domain); ?></p>
                            </div>
                            <?php
                        });
    
                    } else {
                        add_action('admin_notices', function () {
                            ?>
                            <div class="notice notice-error">
                                <p><?php _e('There was a problem activating the license', $this->language_domain); ?></p>
                            </div>
                            <?php
                        });
                    }
                } else {
                    add_action('admin_notices', function () {
                        ?>
                        <div class="notice notice-error">
                            <p><?php _e('There was a problem establishing a connection to the API server. Please try again', $this->language_domain); ?></p>
                        </div>
                        <?php
                    });
                }
            }
        }
    }


    /**
     * Check license status once a day
     */
    public function check_license_status()
    {
        // Is today already check
        $daily = $this->get_daily_options();

        if($daily['last_execute'] != date('Y-m-d')){

            $options = $this->get_license_options();
            $license_key = $options['key'];
            $status = $options['status'];

            if($license_key && $status=='activated'){

                $args = array(
                    'woo_sl_action'     => 'status-check',
                    'licence_key'       => $license_key,
                    'product_unique_id' => $this->product_unique_id,
                    'domain'            => $this->sl_instance
                );
                $request_uri = $this->sl_app_api_url . '?' . http_build_query($args);
                $data = wp_remote_get($request_uri);
        
                if (!is_wp_error($data) && $data['response']['code'] == 200) {

                    $data_body = json_decode($data['body']);
                    $data_body = $data_body[0];

                    if($data_body->license_status === 'expired'){
                        // Update expire option
                        $params = array(
                            'license_expire'    => 'yes',
                        );
                        update_option($this->expire_option, $params);
                    }else{
                        // Update expire option
                        $params = array(
                            'license_expire'    => '',
                        );
                        update_option($this->expire_option, $params);
                    }

                }

                // Today we already check expiry license 
                $params = array(
                    'last_execute' => date('Y-m-d'),
                );
                update_option($this->daily_option, $params);

            }
        }
    }


    /**
     * License deactivation
     */
    public function license_deactivate()
    {
        if (isset($_POST[$this->nonce_field_name]) && wp_verify_nonce($_POST[$this->nonce_field_name], 'deactivate')) {

            $license_options = $this->get_license_options();

            $license_key = $license_options['key'];

            $args = array(
                'woo_sl_action'     => 'deactivate',
                'licence_key'       => $license_key,
                'product_unique_id' => $this->product_unique_id,
                'domain'            => $this->sl_instance
            );
            $request_uri = $this->sl_app_api_url . '?' . http_build_query($args);
            $data = wp_remote_get($request_uri);

            if (is_wp_error($data) || $data['response']['code'] != 200) {
                add_action('admin_notices', function () {
                    ?>
                    <div class="notice notice-success">
                        <p><?php _e('There was a problem establishing a connection to the API server', $this->language_domain); ?></p>
                    </div>
                    <?php
                });
            }else{

                $data_body = json_decode($data['body']);
                $data_body = $data_body[0];
                if (isset($data_body->status)) {
                    if ($data_body->status == 'success' && $data_body->status_code == 's201') {
                        $params = array(
                            'key'    => '',
                            'status' => 'unactivated',
                        );
                        update_option($this->option_name, $params);
    
                        add_action('admin_notices', function () {
                            ?>
                            <div class="notice notice-success">
                                <p><?php _e('The license is deactive and the software is deactive', $this->language_domain); ?></p>
                            </div>
                            <?php
                        });
    
                    } else {
                        add_action('admin_notices', function () {
                            ?>
                            <div class="notice notice-error">
                                <p><?php _e('There was a problem deactivating the license', $this->language_domain); ?></p>
                            </div>
                            <?php
                        });
                    }
                } else {
                    add_action('admin_notices', function () {
                        ?>
                        <div class="notice notice-error">
                            <p><?php _e('There was a problem establishing a connection to the API server. Please try again', $this->language_domain); ?></p>
                        </div>
                        <?php
                    });
                }
            }

        }
    }


    /**
     * Adds activation license page
     */
    public function activation_license_page()
    {
        add_submenu_page(
            'options-general.php',
            __(sprintf('%s License', $this->plugin_name), $this->language_domain),
            __(sprintf('%s License', $this->plugin_name), $this->language_domain),
            'manage_options',
            strtolower($this->product_unique_id),
            array($this, 'activation_license_page_content')
        );
    }


    /**
     * Activation license page content
     */
    function activation_license_page_content()
    {
        $license_options = $this->get_license_options();
        ?>
        <div class="wrap">
            <h1><?php _e(sprintf('%s License', $this->plugin_name), $this->language_domain); ?></h1>
            <form method="post" action="" novalidate="novalidate">
                <table class="form-table">
                    <tbody>
                    <?php if ($license_options['status'] == 'unactivated') { ?>
                        <tr>
                            <th scope="row">
                                <label for="<?php echo $this->license_field_name; ?>"><?php _e('License', $this->language_domain); ?></label>
                            </th>
                            <td>
                                <input name="<?php echo $this->license_field_name; ?>" type="text" id="<?php echo $this->license_field_name; ?>" placeholder="xxxxxxxx-xxxxxxxx-xxxxxxxx" value="" class="regular-text">
                                <input type="submit" class="button button-primary" value="<?php _e('Activate', $this->language_domain); ?>">
                                <?php wp_nonce_field('activate', $this->nonce_field_name); ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if ($license_options['status'] == 'activated') { ?>
                        <tr>
                            <th scope="row">
                                <label for="<?php echo $this->license_field_name; ?>"><?php _e('License', $this->language_domain); ?></label>
                            </th>
                            <td>
                                <span><?php echo $this->__hide_part($license_options['key']); ?></span>
                                <input type="submit" class="button button-primary" value="<?php _e('Unactivate', $this->language_domain); ?>">
                                <?php wp_nonce_field('deactivate', $this->nonce_field_name); ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </form>
        </div>
        <?php
    }


    /**
     * Used for hide some part of license key
     * @param string $key
     */
    private function __hide_part($key)
    {
        if (empty($key)) {
            return '';
        }

        $hidepart = '-xxxxxxxx-xxxxxxxx';
        $keypart = explode('-', $key);
        $hided_key = $keypart[0] . $hidepart;

        return $hided_key;
    }


    /**
     * Add settings link under plugin name on plugins page.
     */
    public function plugin_action_links($links, $file)
    {
        if ($file != $this->plugin_basename) {
            return $links;
        }

        $license_key_link = '<a href="options-general.php?page='.strtolower($this->product_unique_id).'">' . __('License', $this->language_domain) . '</a>';
        array_unshift($links, $license_key_link);

        return $links;
    }


    /**
     * Prepare request
     */
    public function prepare_request($action, $args = array())
    {
        global $wp_version;
        
        $license = $this->get_license_options();
         
        return array(
            'woo_sl_action'         => $action,
            'version'               => $this->sl_version,
            'product_unique_id'     => $this->product_unique_id,
            'licence_key'           => $license['key'],
            'domain'                => $this->sl_instance,
            'wp-version'            => $wp_version,
            'api_version'           => $this->api_version,
        );
    }


    /**
     * Private
     */
    private function postprocess_response( $response )
    {
        //include slug and plugin data
        $response->slug    =   $this->slug;
        $response->plugin  =   $this->plugin_name;
        
        //if sections are being set
        if ( isset ( $response->sections ) )
        $response->sections = (array)$response->sections;
        
        //if banners are being set
        if ( isset ( $response->banners ) )
        $response->banners = (array)$response->banners;
        
        //if icons being set, convert to array
        if ( isset ( $response->icons ) )
        $response->icons    =   (array)$response->icons;
        
        return $response;
        
    }


    /**
     * Updater
     */
    public function run_updater()
    {
        // Take over the update check
        add_filter('pre_set_site_transient_update_plugins', array($this, 'check_for_plugin_update'));

        // Take over the Plugin info screen
        add_filter('plugins_api', array($this, 'get_plugin_infobox'), 10, 3);
    }


    /**
     * Checking plugin new version 
     */
    public function check_for_plugin_update($checked_data)
    {
        global $wp_version;

        if ( !is_object( $checked_data ) ||  ! isset ( $checked_data->response ) )
            return $checked_data;
        
        $request_string = $this->prepare_request('plugin_update');
        if($request_string === FALSE)
            return $checked_data;
        
        // Start checking for an update
        $request_uri = $this->sl_app_api_url . '?' . http_build_query( $request_string , '', '&');
        
        //check if cached
        $data  =   get_site_transient( $this->id.'-check_for_plugin_update_' . md5( $request_uri ) );
        if  ( $data    === FALSE )
        {
            $data = wp_remote_get( 
                $request_uri, 
                array(
                    'timeout'     => 20,
                    'user-agent'  => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ),
                )
            );
            
            if(is_wp_error( $data ) || $data['response']['code'] != 200)
                return $checked_data;
                
            set_site_transient( $this->id.'-check_for_plugin_update_' . md5( $request_uri ), $data, 60 * 60 * 4 );
        }
                                    
        $response_block = json_decode($data['body']);
        
        if(!is_array($response_block) || count($response_block) < 1)
            return $checked_data;
        
        //retrieve the last message within the $response_block
        $response_block = $response_block[count($response_block) - 1];
        $response = isset($response_block->message) ? $response_block->message : '';
        
        if (is_object($response) && !empty($response)) // Feed the update data into WP updater
        {
            $response  =   $this->postprocess_response( $response );
            $checked_data->response[$this->plugin_basename] = $response;
        }
        
        return $checked_data;
    }
    

    /**
     * Get plugin information
     */
    public function get_plugin_infobox($def, $action, $args)
    {
        global $wp_version;

        if (!is_object($args) || !isset($args->slug) || $args->slug != $this->slug)
           return $def;

        $request_string = $this->prepare_request($action, $args);
        if($request_string === FALSE)
           return new \WP_Error('plugins_api_failed', __('An error occour when try to identify the pluguin.' , 'software-license') . '&lt;/p> &lt;p>&lt;a href=&quot;?&quot; onclick=&quot;document.location.reload(); return false;&quot;>'. __( 'Try again', 'software-license' ) .'&lt;/a>');;
        
        $request_uri = $this->sl_app_api_url . '?' . http_build_query( $request_string , '', '&');
        
        //check if cached
        $data  =   get_site_transient( $this->id.'-check_for_plugin_update_' . md5( $request_uri ) );
        
        if ( isset ( $_GET['force-check'] ) && $_GET['force-check']    ==  '1' )
           $data   =   FALSE;
        
        if  ( $data    === FALSE )
        {
            $data = wp_safe_remote_get( 
                $request_uri, array(
                    'timeout'     => 20,
                    'user-agent'  => 'WordPress/' . $wp_version . '; '.$this->plugin_name.'/' . $this->sl_version .'; ' . $this->sl_instance,
            ));
            
            if(is_wp_error( $data ) || $data['response']['code'] != 200)
                return new \WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.' , 'software-license') . '&lt;/p> &lt;p>&lt;a href=&quot;?&quot; onclick=&quot;document.location.reload(); return false;&quot;>'. __( 'Try again', 'software-license' ) .'&lt;/a>', $data->get_error_message());
                
            set_site_transient( $this->id.'-check_for_plugin_update_' . md5( $request_uri ), $data, 60 * 60 * 4 );
        }
        
        $response_block = json_decode($data['body']);
        //retrieve the last message within the $response_block
        $response_block = $response_block[count($response_block) - 1];
        $response = $response_block->message;
        
        if (is_object($response) && !empty($response))
        {
            $response  =   $this->postprocess_response( $response );
            
            return $response;
        }
    }


}
