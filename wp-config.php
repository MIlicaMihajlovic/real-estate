<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'QkOpYH/LZ0uVOG[MgYL,+Xgj$:f(?C9/_T ,v6xUa VFi)55kNHodSEN(-{nj^8,' );
define( 'SECURE_AUTH_KEY',  '{,w{JyX7(e| :vd(KSeTnKV$eC;dbuQ}F4w~SVlI9<[s0w3Oz@VAPROuMnxVC71b' );
define( 'LOGGED_IN_KEY',    'EAd{VU.Z7J8K;EP{#DF5p?IfbJWmdm^|7p7jqnDz$T214ez/yHQ0^_yJkn,PYmp#' );
define( 'NONCE_KEY',        'rrPOCJ pTbxYsw ud82BUw5Xh|1)!_kb5O<qVO%,)tRbKhb_$~ZTZfjV<~{m_9CH' );
define( 'AUTH_SALT',        '(1!O/L8xNfet^/&NC{SqiS_:P%Fw^IWe?C?8G1QX;yYaI8dB{e7C*KHH{@=U!#K4' );
define( 'SECURE_AUTH_SALT', '0<{t;IdNK:=!3bU>jB. %J?v#YeuMC0j9][oy1vWAh!3G[fMNNCyR:EM_r Qg89{' );
define( 'LOGGED_IN_SALT',   'P&Z!MivbjkG~#eR1;tQh~L~k)I$dm^B4yR<r#q*Do-_];!FGxS(;2t1foDsz,ARt' );
define( 'NONCE_SALT',       'UDO.}Vs@nvU|b]g~AQmsc[f^#H7j?Jq;h[]vf.orXQ$(lT[oTSnLFz3IY87}BE2C' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );

define('FS_METHOD','direct');
