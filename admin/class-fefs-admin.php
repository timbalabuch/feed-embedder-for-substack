<?php
/**
 * Admin settings page (Settings → Feed for Substack) and AJAX preview.
 *
 * @package Feed_Embedder_For_Substack
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FEFS_Admin {

	/**
	 * Hook everything up.
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_action( 'wp_ajax_fefs_preview', array( __CLASS__, 'ajax_preview' ) );
	}

	/**
	 * Register the options page.
	 */
	public static function add_menu() {
		add_options_page(
			__( 'Feed Embedder for Substack', 'feed-embedder-for-substack' ),
			__( 'Feed for Substack', 'feed-embedder-for-substack' ),
			'manage_options',
			'feed-embedder-for-substack',
			array( __CLASS__, 'render_page' )
		);
	}

	/**
	 * Register both options in a single group so one form saves both.
	 */
	public static function register_settings() {
		register_setting(
			'fefs_options',
			'fefs_settings',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitize_settings' ),
				'default'           => fefs_default_settings(),
			)
		);

		register_setting(
			'fefs_options',
			'fefs_design',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitize_design' ),
				'default'           => fefs_default_design(),
			)
		);
	}

	/**
	 * Sanitize the Feed settings.
	 *
	 * @param mixed $input Raw option value.
	 * @return array
	 */
	public static function sanitize_settings( $input ) {
		$input = is_array( $input ) ? $input : array();

		return array(
			'url'           => isset( $input['url'] ) ? esc_url_raw( trim( $input['url'] ), array( 'https' ) ) : '',
			'count'         => isset( $input['count'] ) ? max( 1, min( 20, absint( $input['count'] ) ) ) : 6,
			'cache_minutes' => isset( $input['cache_minutes'] ) ? max( 0, min( 1440, absint( $input['cache_minutes'] ) ) ) : 60,
			'show_more'     => empty( $input['show_more'] ) ? 0 : 1,
			'more_text'     => isset( $input['more_text'] ) ? sanitize_text_field( $input['more_text'] ) : '',
		);
	}

	/**
	 * Sanitize the Design settings.
	 *
	 * @param mixed $input Raw option value.
	 * @return array
	 */
	public static function sanitize_design( $input ) {
		$input    = is_array( $input ) ? $input : array();
		$defaults = fefs_default_design();

		$css_fields = array(
			'gap',
			'card_padding',
			'card_radius',
			'card_border',
			'img_width',
			'img_height',
			'img_height_v',
			'img_radius',
			'title_size',
			'title_line_height',
			'title_spacing',
			'date_size',
			'mobile_title_size',
		);

		$color_fields = array( 'card_bg', 'card_bg_hover', 'title_color', 'title_color_hover', 'date_color' );

		$checkbox_fields = array( 'card_shadow_hover', 'show_image', 'show_date', 'show_excerpt', 'mobile_stack' );

		$out = array();

		foreach ( $css_fields as $field ) {
			$out[ $field ] = isset( $input[ $field ] ) ? fefs_sanitize_css_value( $input[ $field ] ) : $defaults[ $field ];
		}

		foreach ( $color_fields as $field ) {
			$color         = isset( $input[ $field ] ) ? sanitize_hex_color( $input[ $field ] ) : '';
			$out[ $field ] = empty( $color ) ? '' : $color;
		}

		foreach ( $checkbox_fields as $field ) {
			$out[ $field ] = empty( $input[ $field ] ) ? 0 : 1;
		}

		$out['orientation'] = ( isset( $input['orientation'] ) && 'vertical' === $input['orientation'] ) ? 'vertical' : 'horizontal';
		$out['columns']     = isset( $input['columns'] ) ? max( 1, min( 3, absint( $input['columns'] ) ) ) : 3;

		$out['title_weight'] = ( isset( $input['title_weight'] ) && in_array( (string) $input['title_weight'], array( '400', '500', '600', '700' ), true ) )
			? (string) $input['title_weight']
			: '600';

		$out['date_format']       = isset( $input['date_format'] ) ? sanitize_text_field( $input['date_format'] ) : $defaults['date_format'];
		$out['excerpt_length']    = isset( $input['excerpt_length'] ) ? max( 1, min( 1000, absint( $input['excerpt_length'] ) ) ) : 120;
		$out['mobile_breakpoint'] = isset( $input['mobile_breakpoint'] ) ? max( 1, min( 3000, absint( $input['mobile_breakpoint'] ) ) ) : 640;

		return $out;
	}

	/**
	 * Enqueue admin assets only on our settings page.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public static function enqueue_assets( $hook ) {
		if ( 'settings_page_feed-embedder-for-substack' !== $hook ) {
			return;
		}

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'fefs-admin', FEFS_PLUGIN_URL . 'assets/css/admin.css', array(), FEFS_VERSION );

		wp_enqueue_script(
			'fefs-admin',
			FEFS_PLUGIN_URL . 'assets/js/admin.js',
			array( 'jquery', 'wp-color-picker' ),
			FEFS_VERSION,
			true
		);

		wp_localize_script(
			'fefs-admin',
			'fefsAdmin',
			array(
				'nonce'   => wp_create_nonce( 'fefs_preview' ),
				'copied'  => __( 'Copied!', 'feed-embedder-for-substack' ),
				'error'   => __( 'Could not load the preview. Please check the feed URL.', 'feed-embedder-for-substack' ),
				'loading' => __( 'Loading…', 'feed-embedder-for-substack' ),
			)
		);
	}

	/**
	 * Render the settings page with its three tabs.
	 */
	public static function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$settings = fefs_get_settings();
		$design   = fefs_get_design();
		?>
		<div class="wrap fefs-admin">
			<h1><?php esc_html_e( 'Feed Embedder for Substack', 'feed-embedder-for-substack' ); ?></h1>

			<h2 class="nav-tab-wrapper fefs-nav">
				<a href="#feed" class="nav-tab nav-tab-active" data-tab="feed"><?php esc_html_e( 'Feed', 'feed-embedder-for-substack' ); ?></a>
				<a href="#design" class="nav-tab" data-tab="design"><?php esc_html_e( 'Design', 'feed-embedder-for-substack' ); ?></a>
				<a href="#preview" class="nav-tab" data-tab="preview"><?php esc_html_e( 'Preview', 'feed-embedder-for-substack' ); ?></a>
			</h2>

			<form id="fefs-form" method="post" action="options.php">
				<?php settings_fields( 'fefs_options' ); ?>

				<div class="fefs-tab-panel" id="fefs-tab-feed">
					<?php require FEFS_PLUGIN_DIR . 'admin/views/settings.php'; ?>
				</div>

				<div class="fefs-tab-panel" id="fefs-tab-design" style="display:none">
					<?php require FEFS_PLUGIN_DIR . 'admin/views/design.php'; ?>
				</div>

				<div id="fefs-submit-wrap">
					<?php submit_button(); ?>
				</div>
			</form>

			<div class="fefs-tab-panel" id="fefs-tab-preview" style="display:none">
				<?php require FEFS_PLUGIN_DIR . 'admin/views/preview.php'; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * AJAX handler: render the feed with the (possibly unsaved) form values.
	 */
	public static function ajax_preview() {
		check_ajax_referer( 'fefs_preview', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'You are not allowed to do this.', 'feed-embedder-for-substack' ) ), 403 );
		}

		$raw_settings = isset( $_POST['fefs_settings'] ) && is_array( $_POST['fefs_settings'] ) ? wp_unslash( $_POST['fefs_settings'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized below.
		$raw_design   = isset( $_POST['fefs_design'] ) && is_array( $_POST['fefs_design'] ) ? wp_unslash( $_POST['fefs_design'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized below.

		$settings = wp_parse_args( self::sanitize_settings( $raw_settings ), fefs_default_settings() );
		$design   = wp_parse_args( self::sanitize_design( $raw_design ), fefs_default_design() );

		if ( '' === $settings['url'] ) {
			wp_send_json_error( array( 'message' => __( 'Please enter a Substack URL in the Feed tab first.', 'feed-embedder-for-substack' ) ) );
		}

		wp_send_json_success(
			array(
				'html' => FEFS_Shortcode::render_feed( $settings, $design ),
				'css'  => FEFS_CSS_Generator::generate( $design ),
			)
		);
	}
}
