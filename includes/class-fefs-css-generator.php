<?php
/**
 * Generates the front-end stylesheet from the Design settings.
 *
 * @package Feed_Embedder_For_Substack
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FEFS_CSS_Generator {

	/**
	 * Build the full stylesheet for the feed markup.
	 *
	 * Every value comes from the (already sanitized) design settings; values
	 * are passed through fefs_sanitize_css_value() again as defense in depth.
	 *
	 * @param array $design Design settings merged with defaults.
	 * @return string CSS.
	 */
	public static function generate( $design ) {
		$d = wp_parse_args( (array) $design, fefs_default_design() );

		$gap          = self::val( $d['gap'], '1rem' );
		$padding      = self::val( $d['card_padding'], '1rem' );
		$card_bg      = self::color( $d['card_bg'], '#f5f5f5' );
		$card_radius  = self::val( $d['card_radius'], '0' );
		$card_border  = self::val( $d['card_border'], 'none' );
		$columns      = max( 1, min( 3, absint( $d['columns'] ) ) );
		$img_width    = self::val( $d['img_width'], '180px' );
		$img_height   = self::val( $d['img_height'], '130px' );
		$img_height_v = self::val( $d['img_height_v'], 'auto' );
		$img_radius   = self::val( $d['img_radius'], '0' );
		$breakpoint   = max( 1, absint( $d['mobile_breakpoint'] ) );

		$css = '.fefs-wrap{margin:0 0 1em}';

		$css .= '.fefs-feed{margin:0;padding:0;list-style:none}';
		$css .= ".fefs-feed--horizontal{display:flex;flex-direction:column;gap:{$gap}}";
		$css .= ".fefs-feed--vertical{display:grid;grid-template-columns:repeat({$columns},1fr);gap:{$gap}}";

		$css .= ".fefs-card{background:{$card_bg};border-radius:{$card_radius};border:{$card_border};overflow:hidden;transition:background-color .2s ease,box-shadow .2s ease}";

		$hover_bg = self::color( $d['card_bg_hover'], '' );
		if ( '' !== $hover_bg ) {
			$css .= ".fefs-card:hover{background:{$hover_bg}}";
		}
		if ( ! empty( $d['card_shadow_hover'] ) ) {
			$css .= '.fefs-card:hover{box-shadow:0 4px 14px rgba(0,0,0,.12)}';
		}

		$css .= ".fefs-card-link{display:flex;gap:1rem;padding:{$padding};color:inherit;text-decoration:none}";
		$css .= '.fefs-card-body{min-width:0;flex:1}';

		// Horizontal: image beside the text.
		$css .= '.fefs-feed--horizontal .fefs-card-link{flex-direction:row;align-items:flex-start}';
		$css .= ".fefs-feed--horizontal .fefs-card-image{flex:0 0 {$img_width};width:{$img_width};height:{$img_height}}";

		// Vertical/grid: image on top.
		$css .= '.fefs-feed--vertical .fefs-card-link{flex-direction:column}';
		$css .= '.fefs-feed--vertical .fefs-card-image{width:100%;';
		if ( 'auto' === $img_height_v || '' === $img_height_v ) {
			$css .= 'aspect-ratio:16/9;height:auto}';
		} else {
			$css .= "height:{$img_height_v}}";
		}

		$css .= ".fefs-card-image img{display:block;width:100%;height:100%;object-fit:cover;border-radius:{$img_radius}}";

		// Typography.
		$title_color  = self::color( $d['title_color'], '#0a0a0a' );
		$title_size   = self::val( $d['title_size'], '1.2rem' );
		$title_weight = in_array( (string) $d['title_weight'], array( '400', '500', '600', '700' ), true ) ? $d['title_weight'] : '600';
		$title_lh     = self::val( $d['title_line_height'], '1.3' );
		$title_ls     = self::val( $d['title_spacing'], '0' );

		$css .= ".fefs-card-title{margin:0 0 .35em;color:{$title_color};font-size:{$title_size};font-weight:{$title_weight};line-height:{$title_lh};letter-spacing:{$title_ls};transition:color .2s ease}";

		$title_hover = self::color( $d['title_color_hover'], '' );
		if ( '' !== $title_hover ) {
			$css .= ".fefs-card:hover .fefs-card-title{color:{$title_hover}}";
		}

		$date_color = self::color( $d['date_color'], '#6b7280' );
		$date_size  = self::val( $d['date_size'], '0.8rem' );
		$css       .= ".fefs-card-date{margin:0 0 .5em;color:{$date_color};font-size:{$date_size}}";

		$css .= '.fefs-card-excerpt{margin:0}';
		$css .= '.fefs-more{display:inline-block;margin-top:.75em}';
		$css .= '.fefs-error{color:#b91c1c}';

		// Mobile.
		$mobile_title = self::val( $d['mobile_title_size'], '1rem' );
		$css         .= "@media (max-width:{$breakpoint}px){";
		$css         .= ".fefs-card-title{font-size:{$mobile_title}}";
		if ( ! empty( $d['mobile_stack'] ) ) {
			$css .= '.fefs-feed--vertical{grid-template-columns:1fr}';
			$css .= '.fefs-feed--horizontal .fefs-card-link{flex-direction:column}';
			$css .= '.fefs-feed--horizontal .fefs-card-image{flex:none;width:100%;height:auto;aspect-ratio:16/9}';
		}
		$css .= '}';

		return $css;
	}

	/**
	 * Sanitize a CSS value, falling back to a default when empty.
	 *
	 * @param string $value    Raw value.
	 * @param string $fallback Default.
	 * @return string
	 */
	private static function val( $value, $fallback ) {
		$value = fefs_sanitize_css_value( $value );
		return '' === $value ? $fallback : $value;
	}

	/**
	 * Sanitize a hex color, falling back to a default when invalid/empty.
	 *
	 * @param string $value    Raw value.
	 * @param string $fallback Default (may be '' meaning "skip rule").
	 * @return string
	 */
	private static function color( $value, $fallback ) {
		$color = sanitize_hex_color( (string) $value );
		return empty( $color ) ? $fallback : $color;
	}
}
