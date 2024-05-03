<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'ljsherlock-vinestovino' );

/** Database username */
define( 'DB_USER', 'ljsherlock-vinestovino' );

/** Database password */
define( 'DB_PASSWORD', '7TQaOetMmyWJafjdCq2Z' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          'rW_87ol;Cl?EifpA-0MvoHXDUjX{G<,Q Y[NOd2A?qE=[M6q):%*vc?|*LU^|QX<' );
define( 'SECURE_AUTH_KEY',   'jVX6ckgE=Qy5@#R4(l,a;w_vC6]7q;Uw4p2pSWTZ[<1LV4zzk!J6?HHbL=,w,oN;' );
define( 'LOGGED_IN_KEY',     'V$VK!t6@</t*?ae?[wjSWtI(#]5V}1Z>Wd2!8:7qaE(c)D](KXP<rU!ljIA_eLU.' );
define( 'NONCE_KEY',         '*K%Xer)=gtIzjq#$TYI,0&Sob8X6o^8?Lop5R%v0UG[x&wk:^|0L!~[yt~t@W00N' );
define( 'AUTH_SALT',         'LM?Q/,CXH TQXCY_`vnQ+inCIRFt7LM%mM6=Sm]&b>N`H<k-GhzcSMqw6,9O+KlK' );
define( 'SECURE_AUTH_SALT',  '))z T/JaFSF_X!%ZWUxys.{4{I}@&MH27.>:)hCt0JN/?`Dvcq8B]1l!9g%}%q03' );
define( 'LOGGED_IN_SALT',    'bghy)[E[00<1J[o^27,rm14HtPN8qCJE<-v6RbZ;[az<oDQN1B1$Wz#T|F&wPPl ' );
define( 'NONCE_SALT',        'eXR-MKJeM3v|4=m}lld@%8KqLji;aGS&K^`6 =Fsgxm`#=Bl^Zer9.cs8%WIGgkH' );
define( 'WP_CACHE_KEY_SALT', '[I~<Poea9y)W/>;tb_ zAJv$-@?A)O>-*(=ap/VL&2tN1De)6W27Usog@;)vbGfT' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'EDz_';


/* Add any custom values between this line and the "stop editing" line. */



/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
