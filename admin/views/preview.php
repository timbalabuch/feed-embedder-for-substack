<?php
/**
 * Preview tab.
 *
 * @package Feed_Embedder_For_Substack
 * @var array $settings Feed settings (provided by FEFS_Admin::render_page()).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php if ( '' === $settings['url'] ) : ?>
	<div class="notice notice-warning inline">
		<p><?php esc_html_e( 'No Substack URL configured yet. Enter one in the Feed tab (you can preview it before saving).', 'feed-embedder-for-substack' ); ?></p>
	</div>
<?php endif; ?>

<p>
	<button type="button" id="fefs-load-preview" class="button button-primary"><?php esc_html_e( 'Load Preview', 'feed-embedder-for-substack' ); ?></button>
	<span id="fefs-preview-spinner" class="spinner"></span>
</p>

<p class="fefs-shortcode-row">
	<strong><?php esc_html_e( 'Shortcode:', 'feed-embedder-for-substack' ); ?></strong>
	<code id="fefs-shortcode" title="<?php esc_attr_e( 'Click to copy', 'feed-embedder-for-substack' ); ?>">[fefs_feed]</code>
	<button type="button" id="fefs-copy" class="button"><?php esc_html_e( 'Copy', 'feed-embedder-for-substack' ); ?></button>
	<span id="fefs-copied" style="display:none"><?php esc_html_e( 'Copied!', 'feed-embedder-for-substack' ); ?></span>
</p>
<p class="description"><?php esc_html_e( 'Optional attributes: url, count, layout (horizontal | vertical). Example: [fefs_feed url="https://other.substack.com" count="4" layout="vertical"]', 'feed-embedder-for-substack' ); ?></p>

<div id="fefs-preview"></div>
