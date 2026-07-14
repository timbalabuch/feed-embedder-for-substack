<?php
/**
 * Feed tab.
 *
 * @package Feed_Embedder_For_Substack
 * @var array $settings Feed settings (provided by FEFS_Admin::render_page()).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<table class="form-table" role="presentation">
	<tr>
		<th scope="row">
			<label for="fefs-url"><?php esc_html_e( 'Substack URL', 'feed-embedder-for-substack' ); ?></label>
		</th>
		<td>
			<input type="url" id="fefs-url" name="fefs_settings[url]" class="regular-text"
				value="<?php echo esc_attr( $settings['url'] ); ?>"
				placeholder="https://example.substack.com" />
			<p class="description"><?php esc_html_e( 'Your public Substack address. The /feed path is added automatically.', 'feed-embedder-for-substack' ); ?></p>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="fefs-count"><?php esc_html_e( 'Posts to display', 'feed-embedder-for-substack' ); ?></label>
		</th>
		<td>
			<input type="number" id="fefs-count" name="fefs_settings[count]" class="small-text"
				min="1" max="20" value="<?php echo esc_attr( $settings['count'] ); ?>" />
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="fefs-cache"><?php esc_html_e( 'Cache (minutes)', 'feed-embedder-for-substack' ); ?></label>
		</th>
		<td>
			<input type="number" id="fefs-cache" name="fefs_settings[cache_minutes]" class="small-text"
				min="0" max="1440" value="<?php echo esc_attr( $settings['cache_minutes'] ); ?>" />
			<p class="description"><?php esc_html_e( 'How long fetched posts are cached. Use 0 to disable caching.', 'feed-embedder-for-substack' ); ?></p>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php esc_html_e( '"View more" link', 'feed-embedder-for-substack' ); ?></th>
		<td>
			<label>
				<input type="checkbox" id="fefs-show-more" name="fefs_settings[show_more]" value="1" <?php checked( ! empty( $settings['show_more'] ) ); ?> />
				<?php esc_html_e( 'Show a link to your Substack below the feed', 'feed-embedder-for-substack' ); ?>
			</label>
		</td>
	</tr>
	<tr class="fefs-more-text-row">
		<th scope="row">
			<label for="fefs-more-text"><?php esc_html_e( 'Link text', 'feed-embedder-for-substack' ); ?></label>
		</th>
		<td>
			<input type="text" id="fefs-more-text" name="fefs_settings[more_text]" class="regular-text"
				value="<?php echo esc_attr( $settings['more_text'] ); ?>"
				placeholder="<?php esc_attr_e( 'Read more', 'feed-embedder-for-substack' ); ?>" />
			<p class="description"><?php esc_html_e( 'Text for the link. Leave empty to use the default ("Read more").', 'feed-embedder-for-substack' ); ?></p>
		</td>
	</tr>
</table>
