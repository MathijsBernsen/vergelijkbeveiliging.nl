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
define( 'DB_NAME', 'u61335p65614_wp1' );

/** MySQL database username */
define( 'DB_USER', 'u61335p65614_wp1' );

/** MySQL database password */
define( 'DB_PASSWORD', 'F#Eyl^8Sip1bFAvQ@V~48&&3' );

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
define('AUTH_KEY',         'M1gcOgwhusd7igwvGCww9tQcjxQeW1uetd44YdZ56Ui0r4cLPGeqYhLA3Cyo97si');
define('SECURE_AUTH_KEY',  'xpVRP6VZV9Ct3SKTcpxlaQdgP0r0skQf9qGE5zgKxQw78lxIiBhL4h03UqPdfWJt');
define('LOGGED_IN_KEY',    'ccgovtA2hMLr89SRFp9pvXfbjaRuh26TeD8TovSI2E3PUPCmjbW4GjnJrjulL2Hj');
define('NONCE_KEY',        'ostkMs5ROE8oxriNHmtJey6vV2Xj5t8aBGufPATcGp0mhiX0BihfYiM7pNno6dQn');
define('AUTH_SALT',        'iFeItUWaRKnPNyR5GQlgUQZEXyHilasI8zFCP9dRR9FmwkLWRZGg0iHbgPNNJPCA');
define('SECURE_AUTH_SALT', 'BTFnjNjDVEeiYHV9C7cFqXxuvC0kCppHChXNfev9BmK29ZUMjfLK88VMFpI3cCu7');
define('LOGGED_IN_SALT',   'GacPNI0UhQBfIkYXLseey5O9I9TSCOsLAenz73ARHUqL1pjfoCEqUwd8mhxIjUZ1');
define('NONCE_SALT',       'gkUWuWvjJWvN3URaWukQvTFSvhxVXshIs8zfCygvbJkzwV5NO9Yhvh3r1i9arizO');

/**
 * Other customizations.
 */
define('FS_METHOD','direct');
define('FS_CHMOD_DIR',0755);
define('FS_CHMOD_FILE',0644);
define('WP_TEMP_DIR',dirname(__FILE__).'/wp-content/uploads');

/**
 * Turn off automatic updates since these are managed externally by Installatron.
 * If you remove this define() to re-enable WordPress's automatic background updating
 * then it's advised to disable auto-updating in Installatron.
 */
define('AUTOMATIC_UPDATER_DISABLED', true);


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
define( 'WP_DEBUG', true );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
