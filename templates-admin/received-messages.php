<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access allowed' );
}
?>
<div class="wrap">
	<h2><?php esc_html_e( 'Received Messages', 'txtimpact' ); ?></h2>
	<form id="txtimpact_message_list_form" method="post" action="">
		<?php wp_nonce_field( 'txtimpact-received-message-action', '_txtimpact_nonce' ); ?>
		<div class="txtimpact_messages_container">
			<div class="tablenav top">
				<div class="alignleft actions">
					<select name="action">
						<option value="-1" selected="selected"><?php esc_html_e( 'Bulk Actions', 'txtimpact' ); ?></option>
						<option value="delete"><?php esc_html_e( 'Delete', 'txtimpact' ); ?></option>
					</select> <input type="submit" name="submit" class="button-secondary action" value="Apply">
				</div>
				<?php echo txtimpact_build_pager_controll( $current_page, $page_size, $total_items, $total_page, "message" ); ?>
			</div>
			<table class="wp-list-table widefat fixed" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<th scope="col" id="cb" class="manage-column check-column colum-checker"><input type="checkbox"/></th>
						<th scope="col" class="manage-column column-phonenumber"><span><?php esc_html_e( 'Phone Number', 'txtimpact' ); ?></span></th>
						<th scope="col" class="manage-column column-message"><span><?php esc_html_e( 'Message', 'txtimpact' ); ?></span></th>
						<th scope="col" class="manage-column column-mrdt"><span><?php esc_html_e( 'Message received date & time', 'txtimpact' ); ?></span></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th scope="col" class="manage-column check-column colum-checker"><input type="checkbox"/></th>
						<th scope="col" class="manage-column column-phonenumber"><span><?php esc_html_e( 'Phone Number', 'txtimpact' ); ?></span></th>
						<th scope="col" class="manage-column column-message"><span><?php esc_html_e( 'Message', 'txtimpact' ); ?></span></th>
						<th scope="col" class="manage-column column-mrdt"><span><?php esc_html_e( 'Message received date & time', 'txtimpact' ); ?></span></th>
					</tr>
				</tfoot>
				<tbody id="the-list">
				<?php foreach ( $messages as $message ) : ?>
					<tr class="alternate <?php echo ( $message->opt_out ) ? 'txtimpact_opt_out' : ''; ?>">
						<th scope="row" class="check-column colum-checker"><input type="checkbox" name="message_ids[]" value="<?php echo esc_html( $message->ID ); ?>"/></th>
						<td><?php echo esc_html( $message->phone_number ); ?></td>
						<td><?php echo esc_html( $message->message ); ?></td>
						<td><?php echo esc_html( $message->rcvd ); ?></td>
					</tr>
				<?php endforeach ?>
				</tbody>
			</table>
			<div class="tablenav bottom">
				<div class="alignleft actions">
					<select name="action2">
						<option value="-1" selected="selected"><?php esc_html_e( 'Bulk Actions', 'txtimpact' ); ?></option>
						<option value="delete"><?php esc_html_e( 'Delete', 'txtimpact' ); ?></option>
					</select> <input type="submit" name="submit" class="button-secondary action" value="Apply">
				</div>
				<?php echo txtimpact_build_pager_controll( $current_page, $page_size, $total_items, $total_page, "message" ); ?>
			</div>
		</div>
	</form>
</div>