jQuery( function ( a ) {
	function g( a ) {
		var b, c = "";
		for ( b = 0; b < a; b ++ )c += "x";
		return c
	}

	function f() {
		c = new Object;
		c.blog_name = g( parseInt( a( "#txtimpact_blog_length .value" ).text() ) );
		c.blog_url = g( parseInt( a( "#txtimpact_blog_url_length .value" ).text() ) );
		if ( a( "#txtimpact_new_post_message" ).length ) {
			c.post_author = g( parseInt( a( "#txtimpact_post_author .value" ).text() ) );
			c.post_title = g( parseInt( a( "#txtimpact_post_title .value" ).text() ) );
			c.post_url = g( parseInt( a( "#txtimpact_post_url .value" ).text() ) )
		}
		b = a( "#txtimpact_counter" ).text();
		a( "#txtimpact_new_post_message, #txtimpact_message" ).each( e ).keyup( e )
	}

	function e() {
		var d, e;
		e = a( "#txtimpact_new_post_message, #txtimpact_message" ).val();
		e = e.replace( /{blog_name}/ig, c.blog_name ).replace( /{blog_url}/ig, c.blog_url );
		if ( a( "#txtimpact_new_post_message" ).length ) {
			e = e.replace( /{post_author}/ig, c.post_author ).replace( /{post_title}/ig, c.post_title ).replace( /{post_url}/ig, c.post_url )
		}
		d = e.length;
		a( "#txtimpact_counter" ).text( b.replace( "160", 160 - d ) )
	}

	function d() {
		a( ".column-cb input:checkbox" ).click( function () {
			var b = this.checked;
			a( ".txtimpact_subscribers_list input:checkbox" ).each( function () {
				this.checked = b
			} )
		} )
	}

	var b, c;
	a( document ).ready( function () {
		a( "#txtimpact_message_template span" ).click( function () {
			var b = document.getElementById( "txtimpact_new_post_message" );
			if ( a( document ).find( "#txtimpact_message" ).length > 0 ) {
				var b = document.getElementById( "txtimpact_message" )
			}
			if ( document.selection ) {
				b.focus();
				sel = document.selection.createRange();
				sel.text = a( this ).html();
				return
			}
			if ( b.selectionStart || b.selectionStart == "0" ) {
				var c = b.selectionStart;
				var d = b.selectionEnd;
				var f = b.value.substring( 0, c );
				var g = b.value.substring( d, b.value.length );
				b.value = f + a( this ).html() + g
			} else {
				b.value += a( this ).html()
			}
			e()
		} );

		if ( a( "#txtimpact_new_post_message, #txtimpact_message" ).length ) {
			f()
		}

		if ( a( ".column-cb input:checkbox" ).length ) {
			d()
		}

	} );
	a( ".txtimpact_messages_container .arrow-msg" ).click( function () {
		a( this ).parent().next().children().toggle( "fast" );
		if ( a( this ).is( ".arrow-closed-msg" ) ) a( this ).removeClass( "arrow-closed-msg" ).addClass( "arrow-open-msg" ); else a( this ).removeClass( "arrow-open-msg" ).addClass( "arrow-closed-msg" );
	} );
} );