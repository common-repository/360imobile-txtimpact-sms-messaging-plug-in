<?php
/**
 * @package    TXTImpact Texting SMS Notification plugin
 * @author     Ksouri <contact@pro.tn>
 * @copyright  2012 TXTImpact Texting https://www.txtimpact.com
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    1.0
 * @since      1.0
 */

/**
 * Utility: provides the URL to something in this plugin dir.
 *
 * @param string $path The path within this plugin dir.
 *
 * @return string The absolute URL
 **/
function txtimpact_url( $path ) {
	$folder = rtrim( basename( dirname( __FILE__ ) ), '/' );
	$dir    = trailingslashit( WP_PLUGIN_DIR ) . $folder;
	$url    = plugins_url( $folder );

	return $url . $path;
}

/**
 * Generate paginator html controll
 *
 * @param integer $current_page
 * @param integer $page_size
 * @param integer $total_items
 * @param integer $total_page
 *
 * @return string of paginator
 */
function txtimpact_build_pager_controll( $current_page, $page_size = 10, $total_items, $total_page, $type = 'subscriber' ) {
	$base_link = txtimpact_current_page_url();
	if ( 'message' === $type ) {
		$pager_content = __( 'Messages', 'txtimpact' ) . '&nbsp;:&nbsp;';
	} else {
		$pager_content = __( 'Subscribers', 'txtimpact' ) . '&nbsp;:&nbsp;';
	}
	$pager_content .= ( ( ( $current_page ) * $page_size ) - $page_size ) + 1 . ' - ' . ( ( $current_page ) * $page_size );
	$pager_content .= '&nbsp;' . __( 'of', 'txtimpact' ) . '&nbsp;' . $total_items;
	$base_link      = remove_query_arg( array( 'p', 'pageSize' ), $base_link );

	// build first link.
	if ( 1 === $current_page ) {
		$first_link = '<span class="txtimpact-first disabled">' . __( 'First', 'txtimpact' ) . '</span>';
	} else {
		$first_link = sprintf( '<a href="%s" class="%s">%s</a>', esc_url( $base_link ), 'txtimpact-first', __( 'First', 'txtimpact' ) );
	}
	$pager_content .= '&nbsp;|&nbsp;' . $first_link;

	// build previous link.
	if ( 1 === $current_page ) {
		$previous_link = '<span class="txtimpact-previous disabled">' . __( 'Previous', 'txtimpact' ) . '</span>';
	} else {
		$previous_link = sprintf( '<a href="%s" class="%s">%s</a>', esc_url( add_query_arg( 'p', ( $current_page - 1 ), $base_link ) ), 'txtimpact-previous', __( 'Previous', 'txtimpact' ) );
	}
	$pager_content .= '&nbsp;|&nbsp;' . $previous_link;

	// build next link.
	if ( intval( $total_page ) === $current_page ) {
		$next_page = '<span class="txtimpact-next disabled">' . __( 'Next', 'txtimpact' ) . '</span>';
	} else {
		$next_page = sprintf( '<a href="%s" class="%s">%s</a>', esc_url( add_query_arg( 'p', ( $current_page + 1 ), $base_link ) ), 'txtimpact-next', __( 'Next', 'txtimpact' ) );
	}
	$pager_content .= '&nbsp;|&nbsp;' . $next_page;

	// build last link.
	if ( intval( $total_page ) === $current_page ) {
		$last_link = '<span class="txtimpact-last disabled">' . __( 'Last', 'txtimpact' ) . '</span>';
	} else {
		$last_link = sprintf( '<a href="%s" class="%s">%s</a>', esc_url( add_query_arg( 'p', $total_page, $base_link ) ), 'txtimpact-last', __( 'Last', 'txtimpact' ) );
	}

	$pager_content  .= '&nbsp;|&nbsp;' . $last_link;
	$pager_container = "<div class='txtimpact-pager'>$pager_content</div>";

	return $pager_container;
}

/**
 * Get current page url
 *
 * @return string url
 */
function txtimpact_current_page_url() {
	return ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/**
 * The database character collate.
 *
 * @return string The database character collate.
 **/
function txtimpact_db_charset_collate() {
	global $wpdb;
	$charset_collate = '';
	if ( ! empty( $wpdb->charset ) ) {
		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
	}
	if ( ! empty( $wpdb->collate ) ) {
		$charset_collate .= " COLLATE $wpdb->collate";
	}

	return $charset_collate;
}
