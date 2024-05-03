<?php

namespace Boostpress\Plugins\WC_Promptpay_Gateway;

class Utilities
{
    /**
     * Get qr-code folder and generate folder if noexist
     * @param void
     * @return string $path
     */
    public static function get_qr_dir()
    {
        $upload_dir = wp_upload_dir();

        $promptpay_dir = $upload_dir['basedir'].'/promptpay';
        if ( ! file_exists( $promptpay_dir ) ) {
            wp_mkdir_p( $promptpay_dir );
        }

        return $promptpay_dir;
    }


    /**
     * Get qr-code folder uri
     * @param void
     * @return string
     */
    public static function get_qr_dir_uri()
    {
        $upload_dir = wp_upload_dir();
        return $upload_dir['baseurl'].'/promptpay';
    }


    /**
     * Get qr-code name
     * @param int order_id
     * @return string
     */
    public static function get_qr_name($order_id)
    {
        return "qr-{$order_id}.png";
    }

}
