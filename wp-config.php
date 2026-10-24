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
define( 'DB_NAME', 'misitio_db' );

/** Database username */
define( 'DB_USER', 'admin' );

/** Database password */
define( 'DB_PASSWORD', 'secreto123' );

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
define( 'AUTH_KEY',          'f<w624PXMOz@wu Udrk/s/TF0L{#~_}hkuyBDqpObujD)r>bFbRYa0btLB!oL0h3' );
define( 'SECURE_AUTH_KEY',   '6E=V8{JM22Z-coW1`cZi#g&>Ef3;7T5LSw)~s};^A3q1Y2}b0dG> N|U#53@5{`^' );
define( 'LOGGED_IN_KEY',     '-=x61_s1{-H85hFo7s<%7^f2Rahq7NfjGLv8(EThQzppY21<)rv$giSun<8-*,xv' );
define( 'NONCE_KEY',         '&RS.ua.BaXI=Rkx/5^x`~OnP<(g@>t=!o[$F(=V4wiy{qMe`1X69zj}{(,yvba8N' );
define( 'AUTH_SALT',         'qXmDWQKklW /Ah*YR l_va9+0%g=+hoE0*{Ft?_}yejWjEZtt<8DsfBArN5R+Gm~' );
define( 'SECURE_AUTH_SALT',  'IS[pT=:A5!}Lh1bf0xu/+=hUQuxZsdIrpW63@=fQ w9:;WF$oc74~lq$dxItF?e%' );
define( 'LOGGED_IN_SALT',    '(|4;34&%e%e`qPO.3F:<e9T0Lm$RbOi**~6hx>]T>PJI,sBe0x3b+U8XE6W2hTFf' );
define( 'NONCE_SALT',        '8|DfWmT}n2hs2q<:F?A=1 {o7sgO062r,,lVNG&/;G6^r_YH`79Xg=trksrhD78x' );
define( 'WP_CACHE_KEY_SALT', 'px#R6wSdt:o{Dml)EKvTeC-|*Oxb=D*n)G_qVldD3CaW$/tM}X3g}<*beSqVJddP' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


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

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
