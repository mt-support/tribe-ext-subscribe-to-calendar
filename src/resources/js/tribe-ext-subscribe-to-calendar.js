jQuery( '.subscribe-to-calendar-dropdown-selector-button' ).on( 'click' , function() {
	jQuery( this ).toggleClass( 'subscribe-to-calendar-dropdown-selector-button-active' );
	jQuery( this ).next().toggle();
} );
