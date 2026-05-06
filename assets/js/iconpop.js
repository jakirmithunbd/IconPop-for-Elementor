/* IconPop for Elementor — popup interaction */
( function ( $ ) {
	'use strict';

	var overlay  = null;
	var panelWrap = null;

	/* ── Build the shared modal overlay once ── */
	function buildOverlay() {
		if ( overlay ) return;

		overlay = $( '<div class="iconpop-modal-overlay" role="dialog" aria-modal="true" tabindex="-1"></div>' );
		panelWrap = $( '<div class="iconpop-modal-panel-wrap"></div>' );
		overlay.append( panelWrap );
		$( 'body' ).append( overlay );

		/* Close on overlay background click */
		overlay.on( 'click', function ( e ) {
			if ( $( e.target ).is( overlay ) ) closeModal();
		} );

		/* Close on Escape key */
		$( document ).on( 'keydown.iconpop', function ( e ) {
			if ( e.key === 'Escape' ) closeModal();
		} );
	}

	/* ── Open modal with content from <template> ── */
	function openModal( $item ) {
		buildOverlay();

		var tpl = $item.find( '.iconpop-tpl' )[0];
		if ( ! tpl ) return;

		/* Clone the template content */
		var content = $( document.importNode( tpl.content, true ) );
		panelWrap.empty().append( content );

		/* Wire up the close button inside the freshly cloned panel */
		panelWrap.find( '.iconpop-modal-close' ).on( 'click', closeModal );

		overlay.addClass( 'is-open' );
		overlay.focus();
	}

	function closeModal() {
		if ( overlay ) {
			overlay.removeClass( 'is-open' );
		}
	}

	/* ── Init icon items ── */
	function initItems( context ) {
		$( '.iconpop-item', context ).each( function () {
			var $item = $( this );
			if ( $item.data( 'iconpop-init' ) ) return;
			$item.data( 'iconpop-init', true );

			/* Click / Enter to open popup */
			$item.on( 'click', function () {
				openModal( $item );
			} );

			$item.on( 'keydown', function ( e ) {
				if ( e.key === 'Enter' || e.key === ' ' ) {
					e.preventDefault();
					openModal( $item );
				}
			} );
		} );
	}

	/* ── Run on DOM ready ── */
	$( function () {
		initItems( document );
	} );

	/* ── Re-init after Elementor frontend re-renders ── */
	$( window ).on( 'elementor/frontend/init', function () {
		if ( window.elementorFrontend ) {
			elementorFrontend.hooks.addAction(
				'frontend/element_ready/iconpop_widget.default',
				function ( $scope ) {
					initItems( $scope[0] );
				}
			);
		}
	} );

} )( jQuery );
