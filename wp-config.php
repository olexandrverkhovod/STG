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
define( 'DB_NAME', '' );

/** MySQL database username */
define( 'DB_USER', '' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

define( 'WP_HOME', 'http://upw-stg.dev.boosthost.net' );
define( 'WP_SITEURL', 'http://upw-stg.dev.boosthost.net' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'Iz~LvrX]4OibhFz[Ao_ZGYxz31ya2-0dbt*.@3XR1(QAS=N-`sgGUb0g7$Wq*-[b' );
define( 'SECURE_AUTH_KEY',  '{} 8xW=GOMG`$:( D6P<4/{xEnj{*I19,h<iEz(F;^u!2|)t=6:Iu1<9ROj[,ajH' );
define( 'LOGGED_IN_KEY',    '%{NMFp.C1RZ]%D53 k~# MC)n{F9:[ag4<F,uiqi5U4DrfE;L*X@~R4JYW>L^7@!' );
define( 'NONCE_KEY',        'e/g>DnaVX(qcnk`MN9%naV,IM~BMJJ-d@$B{(o9]PRm;k`}cZ<Bk*8#]z[<4G%%[' );
define( 'AUTH_SALT',        '7ba5rs__E/MLi~OBnav.X)cbZ#3rr`k5O={dm$H7u0Ga`pON9-4|F==:f/COx=#c' );
define( 'SECURE_AUTH_SALT', 'Gu[|.x_af/%-o sE/J7@XCH!HH>@voDf<H%)2Wjv^oSp^Z-aCQh ;TNY0.`3w!M@' );
define( 'LOGGED_IN_SALT',   'Iz8FawVnFYGQ,SMdw6mD3?wj#T[yla!!kxf2.OnIj9yP_.&1Pa%x3c?qR`9i-lD!' );
define( 'NONCE_SALT',       'VZ:*WMyq?Y@<SMLw:3#@i`p,mT`M<l?R7HE0k<*`/.+$rBu/HpNWZ_wGui fZ[@/' );

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
