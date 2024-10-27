jQuery( function ( $ ) {
	$( document ).ready( function () {
		$( ".txtimpactsubscribe_form" ).each( function () {
			var b = $( this );
			var c = b.children( "input[name=action]" ).val();
			var d = b.children( "input[name=successInfo]" ).val();
			b.submit( function ( e ) {
				var f = b.children( ".txtimpactsubscribe-phoneNumber" ).children( "input[name=phone_number]" ).val();
				$.ajax( {
					        type: "POST", dataType: "json", url: b.attr( "action" ), data: {action: c, phone_number: f}, success: function ( c ) {
						if ( c.success ) {
							b.html( d );
							return true
						}
						if ( ! c.success ) {
							$( ".phoneNumber-error" ).html( c.messages ).css( "color", "red" )
						}
					}
				        } );
				return false
			} )
		} )
	} )
} );