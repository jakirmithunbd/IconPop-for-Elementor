/* IconPop for Elementor v1.5 */
( function () {
	'use strict';

	var modal    = null;
	var content  = null;
	var closeBtn = null;
	var closeTimer = null;

	/* Transition duration must match CSS (.is-closing transitions = 0.38s) */
	var CLOSE_MS = 400;

	function buildModal() {
		if ( modal ) return;

		modal = document.createElement( 'div' );
		modal.className = 'iconpop-modal';
		modal.setAttribute( 'role', 'dialog' );
		modal.setAttribute( 'aria-modal', 'true' );

		modal.innerHTML =
			'<div class="iconpop-modal-backdrop"></div>' +
			'<div class="iconpop-modal-panel">' +
				'<button class="iconpop-modal-close" type="button" aria-label="Close">&#x2715;</button>' +
				'<div class="iconpop-modal-content"></div>' +
			'</div>';

		modal.style.display = 'none';
		document.body.appendChild( modal );

		content  = modal.querySelector( '.iconpop-modal-content' );
		closeBtn = modal.querySelector( '.iconpop-modal-close' );

		modal.querySelector( '.iconpop-modal-backdrop' )
		     .addEventListener( 'click', closeModal );
		closeBtn.addEventListener( 'click', closeModal );

		document.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' && modal.style.display !== 'none' ) {
				closeModal();
			}
		} );
	}

	function openModal( section, idx ) {
		buildModal();

		/* Cancel any in-progress close */
		if ( closeTimer ) {
			clearTimeout( closeTimer );
			closeTimer = null;
			modal.classList.remove( 'is-closing' );
		}

		var store = section.querySelector(
			'.iconpop-popup-store[data-idx="' + idx + '"]'
		);
		if ( ! store ) {
			console.warn( 'IconPop: no popup store found for idx=' + idx );
			return;
		}

		content.innerHTML = store.innerHTML;
		modal.style.display = 'flex';
		document.body.style.overflow = 'hidden';

		/* Two rAFs: first makes element visible, second triggers transitions */
		requestAnimationFrame( function () {
			requestAnimationFrame( function () {
				modal.classList.add( 'is-open' );
				if ( closeBtn ) closeBtn.focus();
			} );
		} );
	}

	function closeModal() {
		if ( ! modal || modal.style.display === 'none' ) return;

		modal.classList.remove( 'is-open' );
		modal.classList.add( 'is-closing' );

		closeTimer = setTimeout( function () {
			modal.classList.remove( 'is-closing' );
			modal.style.display = 'none';
			document.body.style.overflow = '';
			closeTimer = null;
		}, CLOSE_MS );
	}

	/* ── Attach listeners to one section ──────────────────────── */

	function initSection( section ) {
		section.querySelectorAll( '.iconpop-item' ).forEach( function ( item ) {
			if ( item.dataset.iconpopBound ) return;
			item.dataset.iconpopBound = '1';

			item.addEventListener( 'click', function () {
				openModal( section, this.getAttribute( 'data-idx' ) );
			} );

			item.addEventListener( 'keydown', function ( e ) {
				if ( e.key === 'Enter' || e.key === ' ' ) {
					e.preventDefault();
					openModal( section, this.getAttribute( 'data-idx' ) );
				}
			} );
		} );
	}

	function initAll() {
		document.querySelectorAll( '.iconpop-section' ).forEach( initSection );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initAll );
	} else {
		initAll();
	}

	/* Elementor editor: re-init when widget re-renders */
	function attachElementorHook() {
		if ( ! window.elementorFrontend ) return;
		elementorFrontend.hooks.addAction(
			'frontend/element_ready/iconpop_widget.default',
			function ( $scope ) {
				var section = $scope[ 0 ].querySelector( '.iconpop-section' );
				if ( section ) initSection( section );
			}
		);
	}

	if ( window.elementorFrontend && window.elementorFrontend.isInit ) {
		attachElementorHook();
	} else {
		window.addEventListener( 'elementor/frontend/init', attachElementorHook );
	}

} )();
