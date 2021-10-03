jQuery( '[data-js="tribe-events-dropdown-selector-button"]' ).on( 'click' , function() {
	jQuery( this ).toggleClass( 'tribe-events-c-dropdown-selector__button--active' );
	jQuery( this ).next().toggle();
} );
