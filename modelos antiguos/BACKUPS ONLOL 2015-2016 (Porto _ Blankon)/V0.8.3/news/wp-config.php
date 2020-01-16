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
define('WP_CACHE', true); //Added by WP-Cache Manager
define( 'WPCACHEHOME', '/home/sczhmszf/public_html/news/wp-content/plugins/wp-super-cache/' ); //Added by WP-Cache Manager
define('DB_NAME', 'sczhmszf_news');

/** MySQL database username */
define('DB_USER', 'sczhmszf_root');

/** MySQL database password */
define('DB_PASSWORD', '?t!uQ?Nx^TJZ');

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
define('AUTH_KEY',         '{i}3QI0is-W:0K+),BHjMU~%wjt?AYB-xkz-fql]o]7wn+#{{yz?bH*R%A!gBwaC');
define('SECURE_AUTH_KEY',  ',dig+CQReo|BD=p&Z,<, @Q{r(e`mNy``QWJ&7`786(L13G67=C.{.8_z@Q?9~]A');
define('LOGGED_IN_KEY',    '=|i>|:[=9H/[-t!`fA`s,s%LFMrq<FRK,{VLr~]8yj6QUH1/k)IXs5-Vh@h.77A5');
define('NONCE_KEY',        '!TpM:)Lx`6b~2|YCY/[ Pw&_#MYyu5d0F&>E#x7!q&hsqJf><5EOcR:N::U|B,#c');
define('AUTH_SALT',        '@*+2A$3j?|0r}(lkWmt/[{UQW1K(hne}dsZW+|Xt&(#m#=kAObCu8-q.**mn]*0;');
define('SECURE_AUTH_SALT', ',tmpw@F+o|^4^+|]Bvfa0 ,|]&=g*{O[JuT5e>lhU90)&MK.q*8 n$woz6.EVUhQ');
define('LOGGED_IN_SALT',   ')t_)MlS|62_7z2}XE{x2j$W=WMY>G}6Dg{0@7NqA>Xbge3qRo)*3nYha;I.aU0d@');
define('NONCE_SALT',       'u1KD9H3no|v@7M#mI2,*9Rs,b+RMr$F1@RIj|q6Ds7mnHkH=ZX%pa6{.K{V1){1g');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'news_';

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
