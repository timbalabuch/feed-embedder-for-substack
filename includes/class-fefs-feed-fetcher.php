<?php
/**
 * Fetches and caches a Substack (or any) RSS feed.
 *
 * @package Feed_Embedder_For_Substack
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FEFS_Feed_Fetcher {

	/**
	 * Fetch feed items, using a transient cache.
	 *
	 * @param string $url           Site or feed URL (https only).
	 * @param int    $count         Number of items to return (1-20).
	 * @param int    $cache_minutes Cache lifetime in minutes; 0 disables caching.
	 * @return array|WP_Error Array of items on success.
	 */
	public static function fetch( $url, $count, $cache_minutes = 60 ) {
		$feed_url = self::derive_feed_url( $url );

		if ( ! $feed_url ) {
			return new WP_Error( 'fefs_invalid_url', __( 'Invalid feed URL. Only https:// URLs are supported.', 'feed-embedder-for-substack' ) );
		}

		$count         = max( 1, min( 20, absint( $count ) ) );
		$cache_minutes = max( 0, min( 1440, absint( $cache_minutes ) ) );
		$cache_key     = 'fefs_feed_' . md5( $feed_url ) . '_' . $count;

		if ( $cache_minutes > 0 ) {
			$cached = get_transient( $cache_key );
			// A cached result may be the parsed items (array) or a cached
			// failure (WP_Error) — both short-circuit further requests.
			if ( is_array( $cached ) || is_wp_error( $cached ) ) {
				return $cached;
			}
		}

		$result = self::request_and_parse( $feed_url, $count );

		if ( $cache_minutes > 0 ) {
			// Cache failures too, for a shorter window, so a broken or slow
			// feed is not re-fetched on every page load.
			$ttl = is_wp_error( $result )
				? min( $cache_minutes, 5 ) * MINUTE_IN_SECONDS
				: $cache_minutes * MINUTE_IN_SECONDS;

			set_transient( $cache_key, $result, $ttl );
		}

		return $result;
	}

	/**
	 * Perform the HTTP request and parse the body.
	 *
	 * @param string $feed_url Feed URL.
	 * @param int    $count    Max items.
	 * @return array|WP_Error
	 */
	private static function request_and_parse( $feed_url, $count ) {
		$response = wp_remote_get(
			$feed_url,
			array(
				'timeout'            => 5,
				'redirection'        => 3,
				'reject_unsafe_urls' => true,
				'user-agent'         => 'Mozilla/5.0 (compatible; FeedEmbedderForSubstack/' . FEFS_VERSION . '; +' . home_url( '/' ) . ')',
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== (int) $code ) {
			return new WP_Error( 'fefs_http_error', __( 'The feed could not be fetched.', 'feed-embedder-for-substack' ) );
		}

		return self::parse( wp_remote_retrieve_body( $response ), $count );
	}

	/**
	 * Normalize a Substack site URL into its RSS feed URL.
	 *
	 * Accepts https://example.substack.com and appends /feed automatically.
	 * Any other valid https URL is used as-is (assumed to already be a feed).
	 *
	 * @param string $url Raw URL.
	 * @return string|false Feed URL or false when invalid.
	 */
	public static function derive_feed_url( $url ) {
		$url = esc_url_raw( trim( (string) $url ), array( 'https' ) );

		if ( '' === $url || ! wp_http_validate_url( $url ) ) {
			return false;
		}

		$path = (string) wp_parse_url( $url, PHP_URL_PATH );
		$path = untrailingslashit( $path );

		// Bare site URL (no path): assume Substack-style /feed endpoint.
		if ( '' === $path ) {
			return untrailingslashit( $url ) . '/feed';
		}

		return $url;
	}

	/**
	 * Parse an RSS XML body into an array of items.
	 *
	 * @param string $body  Raw XML.
	 * @param int    $count Max items.
	 * @return array|WP_Error
	 */
	private static function parse( $body, $count ) {
		if ( '' === trim( (string) $body ) ) {
			return new WP_Error( 'fefs_empty_feed', __( 'The feed response was empty.', 'feed-embedder-for-substack' ) );
		}

		$previous = libxml_use_internal_errors( true );
		$xml      = simplexml_load_string( $body, 'SimpleXMLElement', LIBXML_NOCDATA );
		libxml_clear_errors();
		libxml_use_internal_errors( $previous );

		if ( false === $xml || ! isset( $xml->channel->item ) ) {
			return new WP_Error( 'fefs_parse_error', __( 'The feed could not be parsed.', 'feed-embedder-for-substack' ) );
		}

		$items = array();

		foreach ( $xml->channel->item as $item ) {
			if ( count( $items ) >= $count ) {
				break;
			}

			$link = esc_url_raw( trim( (string) $item->link ), array( 'https', 'http' ) );
			if ( '' === $link ) {
				continue;
			}

			$content = $item->children( 'content', true );

			$items[] = array(
				'title'   => wp_strip_all_tags( (string) $item->title ),
				'link'    => $link,
				'date'    => (int) strtotime( (string) $item->pubDate ),
				'excerpt' => wp_strip_all_tags( (string) $item->description ),
				'image'   => self::extract_image( $item, isset( $content->encoded ) ? (string) $content->encoded : '' ),
			);
		}

		if ( empty( $items ) ) {
			return new WP_Error( 'fefs_no_items', __( 'The feed contains no posts.', 'feed-embedder-for-substack' ) );
		}

		return $items;
	}

	/**
	 * Extract the post image from an item.
	 *
	 * Substack feeds expose it as <enclosure url="..."/>; falls back to the
	 * first <img> inside content:encoded.
	 *
	 * @param SimpleXMLElement $item    Feed item.
	 * @param string           $content content:encoded HTML.
	 * @return string Image URL or empty string.
	 */
	private static function extract_image( $item, $content ) {
		if ( isset( $item->enclosure ) ) {
			$type = (string) $item->enclosure['type'];
			$src  = esc_url_raw( (string) $item->enclosure['url'], array( 'https', 'http' ) );
			if ( '' !== $src && ( '' === $type || 0 === strpos( $type, 'image/' ) ) ) {
				return $src;
			}
		}

		if ( '' !== $content && preg_match( '/<img[^>]+src=["\']([^"\']+)["\']/i', $content, $matches ) ) {
			return esc_url_raw( $matches[1], array( 'https', 'http' ) );
		}

		return '';
	}
}
