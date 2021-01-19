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
define( 'DB_NAME', 'zigecek.github.io_db' );

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
define( 'AUTH_KEY',         '9/.N9#b?E<Y.vMzzG0Zh66B6TfO.vgWI|R4P/Icnw>(-B5]2#f&4QK}:f <0T7{_' );
define( 'SECURE_AUTH_KEY',  'jVfmWQ1#Tj#?pPWGUaUZ 3s8SSwn8k>Ra r.#Iu+cfpk]RozR8aT6qMC|F,@q]VP' );
define( 'LOGGED_IN_KEY',    'Gb.T|B#l_NW%TDCV2C0>%rBEN*p;!~`*]2)$b6MHU~6eAJgt:_x5E^Sh8qOxD(TF' );
define( 'NONCE_KEY',        '6B|-9m1n* }E5<i{p7i~EMDDI-*{yk9BL~N`fA,Bxn3?L%a(ix(]MpD7BTUmh``6' );
define( 'AUTH_SALT',        'M*-V[&i2Hhv` Mi2Y#{Ub_kArG^S@OL4ow@.Im/z*lCRH_9E3Gogpl+;5s;8)7fm' );
define( 'SECURE_AUTH_SALT', '/Q8y)QE[8FyE!DCu2z=h`o-kAaP(,SUte{[l=%&`t} h&Yw{@O*p?(<3eT:]A*sP' );
define( 'LOGGED_IN_SALT',   'pOVe>2x0#^<=jUx~*NN[mJ%6?]*XO;<:<_H,/R[ MZ&J-} 7tRE^[1IT+b434Y*S' );
define( 'NONCE_SALT',       '+v[Jhbk4~sf!;/~l`a$R.(q{Wp:k;a,e^d7*-A=$K|)WENV c@[UiVC4b#=84-xR' );

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
