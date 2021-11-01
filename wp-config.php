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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'omier_db' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         '(Gp`#:6B`k{dE-_ui){c#7J=b5v9RVX2j`BhN?uQo)QShWR)Y-J E#<q(p#xeDWD' );
define( 'SECURE_AUTH_KEY',  'NM=OzeIlwlyS|mLQ-v84#<cAAn+AUOrd}3ZB+lS`:-2$_Jseh1~=2~KXI|} j?Xj' );
define( 'LOGGED_IN_KEY',    'H4s#=efNtXk7t9{482zyRFtD%5Yk/F!Fobj`e<WfG->4o}7AYp7V#!N96j/!-(aR' );
define( 'NONCE_KEY',        'W`<1qoQIMQ$qY.l5O2gHC~OTeOE:>XsePF /%&}j2^yDaMQzi/8O&e?[SG2Ra:wb' );
define( 'AUTH_SALT',        '{=szakzPh)yQany2>23hb{{3?<?UmiT*Yc4L)?KCTaisHw>S^hpx|C*xSSJTc6yG' );
define( 'SECURE_AUTH_SALT', 'BmnCVyYgkt7[M8O@I}t#H;6wl5A|Ht*[wNz&ZjA&GC1As:V<?aWq3WdRDCyimix`' );
define( 'LOGGED_IN_SALT',   '2B>x%Gv ^=n&dW}]{,{Yu8Bf|`X|4PmhKBJ|$G1)1G0r|w9|6xH:.J`ed]XOFu}2' );
define( 'NONCE_SALT',       '_r08!G5tf!t^Sp<MkjoFHH<Z-<Dp3V9H-5rAmvnUj~]7di3]){I ~YhOaSF1ppT4' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'omier_';

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
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
