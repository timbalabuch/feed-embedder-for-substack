<?php
/**
 * Standalone pure-logic test harness for Feed Embedder for Substack.
 * Run: php tests/harness.php
 */
error_reporting( E_ALL );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}
if ( ! defined( 'MINUTE_IN_SECONDS' ) ) {
	define( 'MINUTE_IN_SECONDS', 60 );
}

function plugin_dir_path( $file ) { return trailingslashit( dirname( $file ) ); }
function plugin_dir_url( $file ) { return 'https://example.test/wp-content/plugins/' . basename( dirname( $file ) ) . '/'; }
function trailingslashit( $value ) { return rtrim( (string) $value, '/\\' ) . '/'; }
function untrailingslashit( $value ) { return rtrim( (string) $value, '/\\' ); }
function add_action() {}
function add_shortcode() {}
function wp_register_style() {}
function is_admin() { return false; }
function get_option( $key, $default = false ) { return $default; }
function wp_parse_args( $args, $defaults = array() ) { return array_merge( (array) $defaults, (array) $args ); }
function sanitize_text_field( $value ) { return trim( preg_replace( '/[\r\n\t ]+/', ' ', strip_tags( (string) $value ) ) ); }
function esc_url_raw( $url, $protocols = null ) { return trim( (string) $url ); }
function wp_http_validate_url( $url ) { return false !== filter_var( $url, FILTER_VALIDATE_URL ) && 0 === strpos( $url, 'https://' ); }
function wp_parse_url( $url, $component = -1 ) { return -1 === $component ? parse_url( $url ) : parse_url( $url, $component ); }
function absint( $value ) { return abs( (int) $value ); }
function sanitize_hex_color( $color ) { return preg_match( '/^#[0-9a-fA-F]{3}([0-9a-fA-F]{3})?$/', (string) $color ) ? $color : ''; }

require __DIR__ . '/../feed-embedder-for-substack.php';

$GLOBALS['fefs_fail'] = 0;
function fefs_ok( $label, $condition ) {
	echo ( $condition ? 'PASS  ' : 'FAIL  ' ) . $label . "\n";
	if ( ! $condition ) {
		$GLOBALS['fefs_fail']++;
	}
}

fefs_ok( 'derive bare Substack URL appends /feed', 'https://example.substack.com/feed' === FEFS_Feed_Fetcher::derive_feed_url( 'https://example.substack.com' ) );
fefs_ok( 'derive feed URL with path stays unchanged', 'https://example.substack.com/feed' === FEFS_Feed_Fetcher::derive_feed_url( 'https://example.substack.com/feed' ) );
fefs_ok( 'derive rejects http URLs', false === FEFS_Feed_Fetcher::derive_feed_url( 'http://example.substack.com' ) );

fefs_ok( 'CSS sanitizer strips declaration-breaking characters', '1px solid red' === fefs_sanitize_css_value( '1px; solid {red}' ) );

if ( function_exists( 'fefs_review_url' ) ) {
	fefs_ok( 'review URL points directly to WordPress.org review form', 'https://wordpress.org/support/plugin/feed-embedder-for-substack/reviews/#new-post' === fefs_review_url() );
} else {
	fefs_ok( 'review URL helper exists', false );
}

if ( function_exists( 'fefs_truncate_text' ) ) {
	fefs_ok( 'truncate decodes entities before limiting text', 'Tom & Je...' === fefs_truncate_text( 'Tom &amp; Jerry', 8 ) );
	fefs_ok( 'truncate returns full text when under limit', 'Short text' === fefs_truncate_text( 'Short text', 50 ) );
	fefs_ok( 'truncate length below one disables limit', 'Long text' === fefs_truncate_text( 'Long text', 0 ) );
} else {
	fefs_ok( 'truncate helper exists', false );
}

echo "\n" . ( $GLOBALS['fefs_fail'] ? "{$GLOBALS['fefs_fail']} FAILURE(S)\n" : "ALL TESTS PASSED\n" );
exit( $GLOBALS['fefs_fail'] ? 1 : 0 );
