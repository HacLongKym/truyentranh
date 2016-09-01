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
define('DB_NAME', 'wp_comic');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '+ M/[*>|q(4&A|=JW5*}8x7a:tM?lN^iHT y)`w3g9&9<Sg^~mhDN2r&<qdvDJXU');
define('SECURE_AUTH_KEY',  '}zHNd~f_>YUC_?8Ug@6~gt7doQ#0y$P+Kp{kV78uTl^yM~7B,^3Gz*-7=7p_cHbp');
define('LOGGED_IN_KEY',    '<,mn+J)JU3T(:6[kEPukY;yfHBw/cq/0%(U$ol0TRyJkE^?&?k]~[ud*&Pvx}*jP');
define('NONCE_KEY',        ':]@M@:I^?*..Clh0*:b)`S ~?J7xyN4~Z#Lb{k0Wcc=E8 TMu<}TI`nb#+`)3obs');
define('AUTH_SALT',        '_{IHWk7y8/7JPEw-1{+-oi~rQN,E:s/cti>8PU:PZPtc&y%~%(1Zjj$V25.K>=@n');
define('SECURE_AUTH_SALT', ' =$(%%u ME5[,[koIey69Cw~?}D@72-vbJxmeBp#xCzVqA.;^Em!)45Dk[{T-qmP');
define('LOGGED_IN_SALT',   'Q^qu !z2@# ^?A$j![GLb7?xJEigwEPJ;9}lOhXXcT=h&was2<KR;]v_dj%nxEHN');
define('NONCE_SALT',       ',sh=VSmf4h9|twcev3K<qWREd._}iuC+W)1(gx~h9 O -?J9RK#Hv,qK_.tWt&`8');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'cm_';

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
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
