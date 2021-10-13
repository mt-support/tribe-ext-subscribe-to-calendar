jQuery( 'body' ).on( 'click' , '.subscribe-to-calendar-dropdown-selector-button',  function() {
	jQuery( this ).toggleClass( 'subscribe-to-calendar-dropdown-selector-button-active' );
	jQuery( this ).next().toggle();
} );
