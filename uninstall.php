<?php
/**
 * Uninstall handler for Feed Embedder for Substack.
 *
 * Removes all plugin options and cached feed transients.
 *
 * @package Feed_Embedder_For_Substack
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'fefs_settings' );
delete_option( 'fefs_design' );

global $wpdb;

// Delete every cached feed transient. There is no core API to remove
// transients by prefix, so a direct query is required; caching is not
// applicable to a one-off DELETE that runs only on uninstall.
$wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- prefixed transient cleanup on uninstall.
	$wpdb->prepare(
		"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
		$wpdb->esc_like( '_transient_fefs_feed_' ) . '%',
		$wpdb->esc_like( '_transient_timeout_fefs_feed_' ) . '%'
	)
);
