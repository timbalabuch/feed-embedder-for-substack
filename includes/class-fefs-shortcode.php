<?php
/**
 * The [fefs_feed] shortcode.
 *
 * @package Feed_Embedder_For_Substack
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FEFS_Shortcode {

	/**
	 * Register the shortcode.
	 */
	public static function register() {
		add_shortcode( 'fefs_feed', array( __CLASS__, 'render' ) );
	}

	/**
	 * Shortcode callback.
	 *
	 * @param array|string $atts Shortcode attributes.
	 * @return string HTML.
	 */
	public static function render( $atts ) {
		$settings = fefs_get_settings();
		$design   = fefs_get_design();

		$atts = shortcode_atts(
			array(
				'url'    => '',
				'count'  => 0,
				'layout' => '',
			),
			$atts,
			'fefs_feed'
		);

		// Per-instance overrides.
		if ( '' !== $atts['url'] ) {
			$settings['url'] = esc_url_raw( $atts['url'], array( 'https' ) );
		}
		if ( absint( $atts['count'] ) > 0 ) {
			$settings['count'] = max( 1, min( 20, absint( $atts['count'] ) ) );
		}
		if ( in_array( $atts['layout'], array( 'horizontal', 'vertical' ), true ) ) {
			$design['orientation'] = $atts['layout'];
		}

		wp_enqueue_style( 'fefs-feed' );

		// The generated CSS depends only on the (global) design option, so
		// emit it once even if several shortcodes appear on the same page.
		static $css_added = false;
		if ( ! $css_added ) {
			wp_add_inline_style( 'fefs-feed', FEFS_CSS_Generator::generate( $design ) );
			$css_added = true;
		}

		return self::render_feed( $settings, $design );
	}

	/**
	 * Render the feed markup. Shared by the shortcode and the admin preview.
	 *
	 * @param array $settings Feed settings (merged with defaults).
	 * @param array $design   Design settings (merged with defaults).
	 * @return string HTML.
	 */
	public static function render_feed( $settings, $design ) {
		if ( empty( $settings['url'] ) ) {
			return '<p class="fefs-error">' . esc_html__( 'No Substack URL configured.', 'feed-embedder-for-substack' ) . '</p>';
		}

		$items = FEFS_Feed_Fetcher::fetch( $settings['url'], $settings['count'], $settings['cache_minutes'] );

		if ( is_wp_error( $items ) ) {
			return '<p class="fefs-error">' . esc_html__( 'Could not load feed.', 'feed-embedder-for-substack' ) . '</p>';
		}

		$orientation = ( 'vertical' === $design['orientation'] ) ? 'vertical' : 'horizontal';

		$html  = '<div class="fefs-wrap">';
		$html .= '<div class="fefs-feed fefs-feed--' . esc_attr( $orientation ) . '">';

		foreach ( $items as $item ) {
			$html .= self::render_card( $item, $design );
		}

		$html .= '</div>';

		if ( ! empty( $settings['show_more'] ) ) {
			$more_text = trim( (string) $settings['more_text'] );
			if ( '' === $more_text ) {
				$more_text = __( 'Read more', 'feed-embedder-for-substack' );
			}
			$html .= '<a class="fefs-more" href="' . esc_url( $settings['url'] ) . '" target="_blank" rel="noopener noreferrer">'
				. esc_html( $more_text ) . '</a>';
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Render a single post card.
	 *
	 * @param array $item   Feed item (title, link, date, excerpt, image).
	 * @param array $design Design settings.
	 * @return string HTML.
	 */
	private static function render_card( $item, $design ) {
		$html  = '<article class="fefs-card">';
		$html .= '<a class="fefs-card-link" href="' . esc_url( $item['link'] ) . '" target="_blank" rel="noopener noreferrer">';

		if ( ! empty( $design['show_image'] ) && '' !== $item['image'] ) {
			$html .= '<div class="fefs-card-image">';
			$html .= '<img src="' . esc_url( $item['image'] ) . '" alt="' . esc_attr( $item['title'] ) . '" loading="lazy" />';
			$html .= '</div>';
		}

		$html .= '<div class="fefs-card-body">';
		$html .= '<h3 class="fefs-card-title">' . esc_html( $item['title'] ) . '</h3>';

		if ( ! empty( $design['show_date'] ) && $item['date'] > 0 ) {
			$format = '' !== trim( (string) $design['date_format'] ) ? $design['date_format'] : 'd M Y';
			$html  .= '<div class="fefs-card-date">' . esc_html( date_i18n( $format, $item['date'] ) ) . '</div>';
		}

		if ( ! empty( $design['show_excerpt'] ) && '' !== trim( $item['excerpt'] ) ) {
			$html .= '<p class="fefs-card-excerpt">' . esc_html( self::truncate( $item['excerpt'], absint( $design['excerpt_length'] ) ) ) . '</p>';
		}

		$html .= '</div></a></article>';

		return $html;
	}

	/**
	 * Multibyte-safe excerpt truncation.
	 *
	 * @param string $text   Plain text.
	 * @param int    $length Max characters (0 = no limit).
	 * @return string
	 */
	private static function truncate( $text, $length ) {
		$text = trim( html_entity_decode( $text, ENT_QUOTES, 'UTF-8' ) );

		if ( $length < 1 || mb_strlen( $text ) <= $length ) {
			return $text;
		}

		return rtrim( mb_substr( $text, 0, $length ) ) . '…';
	}
}
