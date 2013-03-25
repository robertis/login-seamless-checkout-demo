function toggleHide( selector ) {
	$( selector ).toggleClass( "hider" );
}

function toggle( triggerElem, hideElem, intent ) {
	var $el = $( triggerElem );

	$el.each( function( idx, elem ) {
		elem.on( "click", function( ev ) {
			ev.preventDefault();

			// toggle show/hide for an array of elements
			if ( $.isArray( hideElem ) ) {
				$.each( hideElem, toggleHide );

			// ...or a single element
			} else if ( typeof hideElem === "string" ) {
				toggleHide( hideElem );
			}

			registerIntent( intent );
		});
	});
}

function registerIntent( intent ) {
	// save intended return filename in browser storage
	if ( intent ) {
		sessionStorage.intent = intent;

	// or delete browser storage
	} else {
		sessionStorage.removeItem( "intent" );
	}
}

function setFocus( selector ) {
	document.querySelector( selector ).focus();
}

function goBack( ev ) {
	ev.preventDefault();

	history.back(-1);
}


jQuery( function() {
	$( ".cart .login-trigger" ).on( "click", function( ev ) {
		ev.preventDefault();

		registerIntent( "checkout.php" );
	});

	$( ".auth" ).find( ".login-trigger, .close-modal" ).on( "click", function() {
		registerIntent();
	});

	toggle( ".review-details", ".checkout-details" );

	$( ".cancel" ).on( "click", goBack );

	$( ".checkout-flow" ).button();
});