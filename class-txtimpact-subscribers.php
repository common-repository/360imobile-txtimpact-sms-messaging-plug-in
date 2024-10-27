<?php
/**
 * @package    TXTImpact Texting SMS Notification plugin
 * @author     TXTImpact <support@txtimpact.com>
 * @copyright  2012 TXTImpact Texting https://www.txtimpact.com
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    1.0
 * @since      1.0
 */
/*  Copyright 2012 TXTImpactTexting
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

class TXTIMPACT_Subscribers {
	/**
	 * The current version of the class
	 */
	const VERSION = 1;

	/**
	 * Check for updating db structure
	 *
	 * @global $wpdb WordPress Database Object
	 * @return void
	 */
	public static function check_update() {
		global $wpdb;
		$version      = get_option( 'txtimpact-subscribers-version', 0 );
		$done_upgrade = false;
		if ( self::VERSION > $version ) {
			error_log( "TXTIMPACT: Upgrading subscribers DB table" ); // phpcs:ignore
			$charset_collate = txtimpact_db_charset_collate();
			$table           = $wpdb->prefix . 'txtimpact_subscribers';
			$sql             = " CREATE TABLE $table ( ";
			$sql             .= "    `ID` int(10) unsigned NOT NULL auto_increment, ";
			$sql             .= "    `phone_number` varchar(20) NOT NULL, ";
			$sql             .= "    `opt_out` smallint(1) unsigned NOT NULL default '0', ";
			$sql             .= "    `created` datetime NOT NULL, ";
			$sql             .= "    PRIMARY KEY  (`ID`)";
			$sql             .= ") $charset_collate ";
			$wpdb->query( $sql ); // phpcs:ignore
			$done_upgrade = true;
		}
		if ( $done_upgrade ) {
			error_log( "TXTIMPACT: Done upgrade" ); // phpcs:ignore
			update_option( 'txtimpact-subscribers-version', self::VERSION );
		}
	}

	/**
	 * Save phone number to subscribers table.Returns false if errors, or the number of rows
	 * affected if successful.
	 *
	 * @global       $wpdb WordPress Database Object
	 *
	 * @param string $phone_number number.
	 *
	 * @return count inserted row
	 */
	public static function save_number( $phone_number ) {
		global $wpdb;
		$table = $wpdb->prefix . 'txtimpact_subscribers';
		$data  = array(
			'phone_number' => $phone_number,
			'created'      => date( 'Y-m-d H:i:s' ),
		);

		return $wpdb->insert( $table, $data ); // phpcs:ignore
	}

	/**
	 * Get subscriber by phone number
	 *
	 * @global       $wpdb WordPress Database Object
	 *
	 * @param string $phone_number number.
	 *
	 * @return subscriber object
	 */
	public static function fetch_row_by_phone_number( $phone_number ) {
		global $wpdb;
		$table = $wpdb->prefix . 'txtimpact_subscribers';

		return $wpdb->get_row( $wpdb->prepare( " SELECT * FROM $table WHERE phone_number = %s ", $phone_number ) );
	}

	/**
	 * Get all subscribers
	 *
	 * @global        $wpdb WordPress Database Object
	 *
	 * @param int     $page - current page.
	 * @param int     $pageSize - subscribers count to get.
	 * @param boolean $opt_out - opt_out status.
	 *
	 * @return subscribers objects
	 */
	public static function fetch_all( $page = 1, $pageSize = 10, $opt_out = null ) {
		global $wpdb;
		$start = ( $page - 1 ) * $pageSize;
		$table = $wpdb->prefix . 'txtimpact_subscribers';
		$where = '';
		if ( $opt_out !== null ) {
			$where = ' AND `opt_out` = ' . (int) $opt_out . ' ';
		}
		$query = $wpdb->prepare( " SELECT * FROM $table WHERE 1=1 $where ORDER BY `created` DESC LIMIT %d, %d ", $start, $pageSize );

		return $wpdb->get_results( $query ); // phpcs:ignore
	}

	/**
	 * Get tottal count site subscribers
	 *
	 * @global $wpdb WordPress Database Object
	 * @return int
	 */
	public static function get_count() {
		global $wpdb;
		$table = $wpdb->prefix . 'txtimpact_subscribers';
		$query = "SELECT COUNT(*) as Count FROM $table";

		return $wpdb->get_var( $query ); // phpcs:ignore
	}

	/**
	 * Delete subscribers from subscribers table.Returns false if errors, or the number of rows
	 * affected if successful.
	 *
	 * @global          $wpdb WordPress Database Object
	 *
	 * @param int|array $subscriber_ids ids.
	 *
	 * @return int
	 */
	public static function delete( $subscriber_ids ) {
		global $wpdb;
		if ( ! is_array( $subscriber_ids ) ) {
			$subscriber_ids = (array) $subscriber_ids;
		}
		$table          = $wpdb->prefix . 'txtimpact_subscribers';
		$subscriber_ids = join( ', ', $subscriber_ids );
		$sql            = "DELETE FROM $table WHERE `ID` IN ( $subscriber_ids ) ";

		return (int) $deleted = $wpdb->query( $sql ); // phpcs:ignore
	}

	/**
	 * Opt out subscribers. Returns false if errors, or the number of rows
	 * affected if successful.
	 *
	 * @global          $wpdb WordPress Database Object
	 *
	 * @param int|array $subscriber_ids ids.
	 *
	 * @return <type>
	 */
	public static function opt_outs( $subscriber_ids ) {
		global $wpdb;
		if ( ! is_array( $subscriber_ids ) ) {
			$subscriber_ids = (array) $subscriber_ids;
		}
		$table          = $wpdb->prefix . 'txtimpact_subscribers';
		$subscriber_ids = join( ', ', $subscriber_ids );
		$sql            = "UPDATE $table SET `opt_out` = 1 WHERE `ID` IN ( $subscriber_ids ) ";

		return (int) $deleted = $wpdb->query( $sql ); // phpcs:ignore
	}

	/**
	 * Delete subscribers data. Drop the $wpdb->prefix . 'txtimpact_subscribers' table,
	 * clear delete txtimpact-subscribers-version option
	 *
	 * @global $wpdb WordPress Database Object
	 * @return void;
	 */
	public static function uninstall() {
		global $wpdb;
		$table = $wpdb->prefix . 'txtimpact_subscribers';
		$wpdb->query( "DROP TABLE $table" ); // phpcs:ignore
		delete_option( 'txtimpact-subscribers-version' );
	}
}
