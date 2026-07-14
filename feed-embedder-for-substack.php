<?php
/**
 * Plugin Name:       Feed Embedder for Substack
 * Plugin URI:        https://wordpress.org/plugins/feed-embedder-for-substack/
 * Description:       Embed your Substack posts feed anywhere with a shortcode. Fully customizable layout and design.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            timbalabuch
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       feed-embedder-for-substack
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FEFS_VERSION', '1.0.0' );
define( 'FEFS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'FEFS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once FEFS_PLUGIN_DIR . 'includes/class-fefs-feed-fetcher.php';
require_once FEFS_PLUGIN_DIR . 'includes/class-fefs-css-generator.php';
require_once FEFS_PLUGIN_DIR . 'includes/class-fefs-shortcode.php';

/**
 * Default values for the Feed settings option.
 *
 * @return array
 */
function fefs_default_settings() {
	return array(
		'url'           => '',
		'count'         => 6,
		'cache_minutes' => 60,
		'show_more'     => 0,
		'more_text'     => '',
	);
}

/**
 * Default values for the Design settings option.
 *
 * @return array
 */
function fefs_default_design() {
	return array(
		// Layout.
		'orientation'       => 'horizontal',
		'columns'           => 3,
		'gap'               => '1rem',
		'card_padding'      => '1rem',
		// Card.
		'card_bg'           => '#f5f5f5',
		'card_bg_hover'     => '',
		'card_radius'       => '0',
		'card_border'       => 'none',
		'card_shadow_hover' => 0,
		// Image.
		'show_image'        => 1,
		'img_width'         => '180px',
		'img_height'        => '130px',
		'img_height_v'      => 'auto',
		'img_radius'        => '0',
		// Typography.
		'title_color'       => '#0a0a0a',
		'title_color_hover' => '',
		'title_size'        => '1.2rem',
		'title_weight'      => '600',
		'title_line_height' => '1.3',
		'title_spacing'     => '0',
		'show_date'         => 1,
		'date_color'        => '#6b7280',
		'date_size'         => '0.8rem',
		'date_format'       => 'd M Y',
		'show_excerpt'      => 1,
		'excerpt_length'    => 120,
		// Mobile.
		'mobile_breakpoint' => 640,
		'mobile_stack'      => 1,
		'mobile_title_size' => '1rem',
	);
}

/**
 * Get saved Feed settings merged with defaults.
 *
 * @return array
 */
function fefs_get_settings() {
	$saved = get_option( 'fefs_settings', array() );
	return wp_parse_args( is_array( $saved ) ? $saved : array(), fefs_default_settings() );
}

/**
 * Get saved Design settings merged with defaults.
 *
 * @return array
 */
function fefs_get_design() {
	$saved = get_option( 'fefs_design', array() );
	return wp_parse_args( is_array( $saved ) ? $saved : array(), fefs_default_design() );
}

/**
 * Sanitize a free-text CSS value (lengths, borders, shorthand values).
 *
 * Strips characters that could break out of a declaration block.
 *
 * @param string $value Raw value.
 * @return string
 */
function fefs_sanitize_css_value( $value ) {
	$value = sanitize_text_field( (string) $value );
	return trim( str_replace( array( ';', '{', '}', '<', '>', '"', "'", '\\', '&' ), '', $value ) );
}

/**
 * Bootstrap the public-facing side.
 */
function fefs_init() {
	FEFS_Shortcode::register();
}
add_action( 'init', 'fefs_init' );

/**
 * Register a src-less style handle used to attach the dynamically
 * generated CSS via wp_add_inline_style().
 */
function fefs_register_style() {
	wp_register_style( 'fefs-feed', false, array(), FEFS_VERSION );
}
add_action( 'wp_enqueue_scripts', 'fefs_register_style' );

if ( is_admin() ) {
	require_once FEFS_PLUGIN_DIR . 'admin/class-fefs-admin.php';
	FEFS_Admin::init();
}
