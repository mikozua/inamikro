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
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'db_coba' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         'Gkq]93}EcX?AKL/i~6DR^sc@BrH@s+e9+;}Snr)B.2!>do{~9+H~Yacf3akPl/%A' );
define( 'SECURE_AUTH_KEY',  '^IGXS/`rnUp+Cc4iD=OWr2u/$^h;=]|obYa{7g8IOybeC]ZLh#&`t{RWq,gcU_]:' );
define( 'LOGGED_IN_KEY',    '@cZV1cUM:#-w57?#ksKfMY>H!)sT@LK1#ex;nORCv]3u~M}k<KywV~4V{w^Qpu]y' );
define( 'NONCE_KEY',        '~tgBAFAPQh|(Mx3Gh_y`po_ Sm.]clpTOL-j*22IV$y%a;af6^q `~}JUN0c%.-u' );
define( 'AUTH_SALT',        'uoh*5K:E-$FDqFR@`aO_L|OF03u3 WEGhxpSgoP5rdUiAzSD1L4`iM3W#<7|(M^x' );
define( 'SECURE_AUTH_SALT', 'DFi[F{?{dW45A9BnvW|6ejz)~,^!ZE|.2HBdJ/VB&Yw<cSe1Px=@W_Jl~Uf`KTVU' );
define( 'LOGGED_IN_SALT',   'C/+5m}S s>,)Zb:c8G>aIXgjfj/WT?=m4B1vV!q+L2J*x0T-hnaEIhsu_UL> 7Xq' );
define( 'NONCE_SALT',       'IPbt#6{vU3Va.;-/T{ego^7Et+IIJLm=2)uNcuOP|36$}YAlY7$)&X-MXB]H33;_' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
