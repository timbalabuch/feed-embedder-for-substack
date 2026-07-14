/**
 * Admin UI: tabs, color pickers, live preview and shortcode copy.
 *
 * @package Feed_Embedder_For_Substack
 */
( function ( $ ) {
	'use strict';

	// Style element holding the live-preview CSS. Created here (not printed in
	// markup) and updated on each preview, since the CSS is dynamic and
	// admin-only. Returns the element, creating it before the preview area.
	function fefsPreviewStyle() {
		var el = document.getElementById( 'fefs-preview-css' );
		if ( ! el ) {
			var preview = document.getElementById( 'fefs-preview' );
			el = document.createElement( 'style' );
			el.id = 'fefs-preview-css';
			preview.parentNode.insertBefore( el, preview );
		}
		return el;
	}

	$( function () {
		// Color pickers.
		$( '.fefs-color' ).wpColorPicker();

		// Tabs (pure show/hide so the Preview tab can read unsaved form values).
		$( '.fefs-nav .nav-tab' ).on( 'click', function ( e ) {
			e.preventDefault();

			var tab = $( this ).data( 'tab' );

			$( '.fefs-nav .nav-tab' ).removeClass( 'nav-tab-active' );
			$( this ).addClass( 'nav-tab-active' );

			$( '.fefs-tab-panel' ).hide();
			$( '#fefs-tab-' + tab ).show();
			$( '#fefs-submit-wrap' ).toggle( 'preview' !== tab );
		} );

		// Toggle the "view more" link-text row with its checkbox.
		function fefsToggleMoreText() {
			$( '.fefs-more-text-row' ).toggle( $( '#fefs-show-more' ).is( ':checked' ) );
		}
		$( '#fefs-show-more' ).on( 'change', fefsToggleMoreText );
		fefsToggleMoreText();

		// Live preview via AJAX, serializing the current (possibly unsaved) form.
		$( '#fefs-load-preview' ).on( 'click', function () {
			var $button  = $( this ),
				$spinner = $( '#fefs-preview-spinner' ),
				// Serialize only our own fields, not the Settings API hidden
				// inputs (option_page, action=update, _wpnonce, …).
				fields   = $( '#fefs-form :input[name^="fefs_settings"], #fefs-form :input[name^="fefs_design"]' ).serialize(),
				data     = fields +
					'&action=fefs_preview&nonce=' + encodeURIComponent( fefsAdmin.nonce );

			$button.prop( 'disabled', true );
			$spinner.addClass( 'is-active' );
			$( '#fefs-preview' ).html( '<p>' + fefsAdmin.loading + '</p>' );

			$.post( ajaxurl, data )
				.done( function ( response ) {
					if ( response && response.success ) {
						fefsPreviewStyle().textContent = response.data.css;
						$( '#fefs-preview' ).html( response.data.html );
					} else {
						var message = ( response && response.data && response.data.message ) ? response.data.message : fefsAdmin.error;
						$( '#fefs-preview' ).html( $( '<p class="fefs-error"></p>' ).text( message ) );
					}
				} )
				.fail( function () {
					$( '#fefs-preview' ).html( $( '<p class="fefs-error"></p>' ).text( fefsAdmin.error ) );
				} )
				.always( function () {
					$button.prop( 'disabled', false );
					$spinner.removeClass( 'is-active' );
				} );
		} );

		// Copy shortcode (button or one click on the code itself).
		function fefsCopyShortcode() {
			var text = $( '#fefs-shortcode' ).text();

			function showFeedback() {
				$( '#fefs-copied' ).fadeIn( 150 ).delay( 1200 ).fadeOut( 300 );
			}

			if ( navigator.clipboard && navigator.clipboard.writeText ) {
				navigator.clipboard.writeText( text ).then( showFeedback );
				return;
			}

			// Fallback for non-secure contexts.
			var $temp = $( '<textarea readonly>' ).val( text ).appendTo( 'body' );
			$temp[ 0 ].select();
			document.execCommand( 'copy' );
			$temp.remove();
			showFeedback();
		}

		$( '#fefs-copy, #fefs-shortcode' ).on( 'click', fefsCopyShortcode );
	} );
} )( jQuery );
