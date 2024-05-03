<?php 
/**
 * Plugin Name: Vines to Vino Product Suppliers
 * Description: Add the ability for an admin to add suppliers to a product
 * and hide groups of products from users based on the same.
 * Version: 1.0
 * Author: Lewis Sherlock
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

include_once('features/feat-product-suppliers.php');
include_once('features/feat-user-suppliers-visible.php');

/**
 * -------------------------- FEATURES ------------------------------
 */

$WC_Product_Suppliers = new WC_Product_Suppliers();
$User_Suppluers_Visible =  new User_Suppluers_Visible();