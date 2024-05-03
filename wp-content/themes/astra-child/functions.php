<?php
/**
 * Astra Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra Child
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_ASTRA_CHILD_VERSION', '1.0.0' );

/**
 * Enqueue styles
 */
function child_enqueue_styles() {
	wp_enqueue_style( 'astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_ASTRA_CHILD_VERSION, 'all' );
}
add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );






function control_search_results($query) {
    if ( is_search() ) {
		$query->set('posts_per_page', 20);

		$product_visibility_term_ids = wc_get_product_visibility_term_ids();

		$query->query_vars['tax_query'][] = [
			'taxonomy'  => 'product_visibility',
			'field'     => 'term_taxonomy_id',
			'terms' => $product_visibility_term_ids,
			'operator'  => 'NOT IN',
		];
	}

	return $query;
}
// add_filter('pre_get_posts', 'control_search_results', 99999);
// // At start of script
// $time_start = microtime(true); 

// // Anywhere else in the script
// echo 'Total execution time in seconds: ' . (microtime(true) - $time_start);


// add_action('register_post', 'set_custom_company_user_login');

function set_custom_company_user_login() {
	global $_POST;

	$_POST['username'] = 'radnom_username_yoo!';

	// grab company name
	// if ( isset( $_POST['company']) ) {
	// 	$user_login = ;
	// 	$nicename = ;
	// 	$display_name = ;
	// 	$nickname = ;
	// }
}

// function action_user_register( $user_id ) {
//     global $wpdb;
    
//     // Set new name + nickname
//     $new_name = 'something_' . $user_id;
    
//     // Update user user_login
//     $wpdb -> update( $wpdb -> users, 
//         array( 'user_login' => $new_name, 'user_nicename' => $new_name, 'display_name' => $new_name ), 
//         array( 'ID' => $user_id )
//     );
    
//     // Update user meta
//     update_user_meta( $user_id, 'nickname', $new_name );
// } 
// add_action( 'user_register', 'action_user_register', 999, 1 );


function prefix_change_user_login( $data, $update, $user_id, $userdata ){

	// DO NOT FORGET TO ADD PROPER NONCE AND REFERRER VALIDATION HERE!!!!

	if ( ! $update && $_POST['user_type'] === 'company')  {

		$new_name = $_POST['billing_company'];

		// In our case we get the new user login from the POSTed form value in WP Admin. If this is different in your use case, adapt!!
		$sanitized_user_login = sanitize_user( wp_unslash( strtolower( str_replace(' ', '_', $new_name) . $user_id ) ), true );

		/**
		 * Filters a username after it has been sanitized.
		 *
		 * This filter is called before the user is created or updated.
		 *
		 * @since 2.0.3
		 *
		 * @param string $sanitized_user_login Username after it has been sanitized.
		 */
		$pre_user_login = apply_filters( 'pre_user_login', $sanitized_user_login );
		$illegal_logins = (array) apply_filters( 'illegal_user_logins', array() );

		// Remove any non-printable chars from the login string to see if we have ended up with an empty username.
		$user_login = trim( $pre_user_login );

		if ( empty( $user_login )
			|| mb_strlen( $user_login ) > 60
			|| username_exists( $user_login )
			|| in_array( strtolower( $user_login ), array_map( 'strtolower', $illegal_logins ), true )
		) {
			return $data;
		}

		$data['user_login'] = $user_login;

		return $data;
	}

}
add_filter( 'wp_pre_insert_user_data', 'prefix_change_user_login', 10, 4 );


function change_all_user_logins() {

	if ( ! is_admin() ) {

		// get all users of role type COMPANY.
		$args = array(
			'role'    => 'company',
			'orderby' => 'user_nicename',
			'order'   => 'ASC'
		);
		$users = get_users( $args );

		echo 'total ' . count($users) . ' company users.';
		if(isset($_GET['process'])) {
			echo 'processing...';
		}

		echo '<ul>';
		foreach ( $users as $user ) {

			// var_dump($user);
			$company_name = get_user_meta( $user->ID, 'billing_company', true );
			$company_name = preg_replace("#[[:punct:]]#", "", $company_name);

			// if contains Thai skip
			// If no company name skip
			if( in_array( $user->ID, [292] ) || empty($company_name)) {
				continue;
			}
			
			// Limit username to 3 words 
			$new_name = limit_words($company_name, 3);
			// make loswercase + replace spaces with underscores.
			$new_name = strtolower(str_replace(' ', '_', $new_name));

			// if username exists add the user_id it.
			if ( username_exists( $new_name ) ) {
				$new_name .=  '_' . $user->ID;
			}

			// process the update
			if(isset($_GET['process'])) {
				$result = change_user_login($user->ID, $new_name);
				echo 'process';
			}

			// show results
			echo '<li>ID: ' . esc_html( $user->ID ) . '</li>';
			echo '<li>display_name: ' . esc_html( $user->display_name ) . '</li>';
			echo '<li>company_name: ' . $company_name . '</li>';
			echo '<li>user_login: ' . $user->user_login . '</li>';
			echo '<li>nickname: ' . $user->nickname . '</li>';
			echo '<li>new name: ' . $new_name . '</li>';
			echo '<li>update result: ' . $result . '</li>';
			echo '<li></li>';

		}
		echo '</ul>';
	}
}

function limit_words ($string, $word_limit) {
	$words = preg_split('/\s+/', $string);

    return implode(" ", array_splice($words, 0, $word_limit));
}

function change_user_login($user_id, $user_login) {
	global $wpdb;
	// $wpdb->show_errors = true;
	// $wpdb->show_errors();
	// $wpdb->print_error();

	$result = $wpdb->update(
		$wpdb->users, 
		[ 'user_login' => $user_login ], 
		[ 'ID' => $user_id ]
	);

	return $result;

}

if( isset( $_GET['user_logins'] ) ) {
	change_all_user_logins();
}
// some additional work needs to go into dealing with companies that are written in thai.