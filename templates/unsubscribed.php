<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access allowed' );
} ?>
<p>
	<?php
	// translators: Phone number.
	printf( esc_html( 'The phone number %s has been successfully unsubscribed.', 'txtimpact' ), esc_html( $txtimpact_phone_number ) );
	?>
	&nbsp; <a href="<?php echo esc_url( home_url() ); ?>"><?php esc_html_e( 'Go home!', 'txtimpact' ); ?></a>
</p>
