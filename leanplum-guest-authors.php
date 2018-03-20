<?php

/**
 * Plugin Name: Leanplum - Guest Authors
 * Description: Assign guest authors to a post.
 * Author: MentorMate
 * Author URI: http://mentormate.com/
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Define constants
 */
define( 'LP_GUEST_AUTHORS_PLUGIN_FILE', __FILE__ );
define( 'LP_GUEST_AUTHORS_PLUGIN_PATH', plugin_dir_path( LP_GUEST_AUTHORS_PLUGIN_FILE ) );
define( 'LP_GUEST_AUTHORS_PLUGIN_URL', plugin_dir_url( LP_GUEST_AUTHORS_PLUGIN_FILE ) );

/**
 * Load plugin translations
 *
 * @url http://geertdedeckere.be/article/loading-wordpress-language-files-the-right-way
 * @return void
 */
function lp_guest_authors_plugin_load_textdomain() {

	$domain = 'lp-co-authors';
	// The "plugin_locale" filter is also used in load_plugin_textdomain()
	$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
	$plugin_dirname = dirname( plugin_basename( LP_GUEST_AUTHORS_PLUGIN_FILE ) );

	load_textdomain( $domain, WP_LANG_DIR . $plugin_dirname . $domain . '-' . $locale . '.mo' );
	load_plugin_textdomain( $domain, FALSE, $plugin_dirname . '/lang/' );
}
add_action( 'plugins_loaded', 'lp_guest_authors_plugin_load_textdomain' );

/**
 * Include plugin classes
 */
require_once LP_GUEST_AUTHORS_PLUGIN_PATH . 'inc/class.LP_Plugin_Guest_Authors.php';
require_once LP_GUEST_AUTHORS_PLUGIN_PATH . 'inc/functions.various.php';
