<?php
/*
Plugin Name: BuddyPress Skeleton Component
Plugin URI: http://example.org/my/awesome/bp/component
Description: This BuddyPress component is the greatest thing since sliced bread.
Version: 1.5
Revision Date: MMMM DD, YYYY
Requires at least: What WP version, what BuddyPress version? ( Example: WP 3.2.1, BuddyPress 1.2.9 )
Tested up to: What WP version, what BuddyPress version?
License: (Example: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html)
Author: Dr. Jan Itor
Author URI: http://example.org/some/cool/developer
Site Wide Only: true
*/

/*************************************************************************************************************
 --- SKELETON COMPONENT V1.5 ---

 Contributors: apeatling, jeffsayre, boonebgorges

 This is a bare-bones component that should provide a good starting block to building your own custom BuddyPress
 component.

 It includes some of the functions that will make it easy to get your component registering activity stream
 items, posting notifications, setting up widgets, adding AJAX functionality and also structuring your
 component in a standardized way.

 It is by no means the letter of the law. You can go about writing your component in any style you like, that's
 one of the best (and worst!) features of a PHP based platform.

 I would recommend reading some of the comments littered throughout, as they will provide insight into how
 things tick within BuddyPress.

 You should replace all references to the word 'example' with something more suitable for your component.

 IMPORTANT: DO NOT configure your component so that it has to run in the /plugins/buddypress/ directory. If you
 do this, whenever the user auto-upgrades BuddyPress - your custom component will be deleted automatically. Design
 your component to run in the /wp-content/plugins/ directory
 *************************************************************************************************************/

/* Only load the component if BuddyPress is loaded and initialized. */
function bp_example_init() {
	require( dirname( __FILE__ ) . '/includes/bp-example-core.php' );
}
add_action( 'bp_include', 'bp_example_init' );

/* Put setup procedures to be run when the plugin is activated in the following function */
function bp_example_activate() {
	global $wpdb;

	if ( !empty($wpdb->charset) )
		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";

	/**
	 * If you want to create new tables you'll need to install them on
	 * activation.
	 *
	 * You should try your best to use existing tables if you can. The
	 * activity stream and meta tables are very flexible.
	 *
	 * Write your table definition below, you can define multiple
	 * tables by adding SQL to the $sql array.
	 */
	$sql[] = "CREATE TABLE {$wpdb->base_prefix}bp_example (
		  		id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  		field_1 bigint(20) NOT NULL,
		  		field_2 bigint(20) NOT NULL,
		  		field_3 bool DEFAULT 0,
			    KEY field_1 (field_1),
			    KEY field_2 (field_2)
		 	   ) {$charset_collate};";

	require_once( ABSPATH . 'wp-admin/upgrade-functions.php' );

	/**
	 * The dbDelta call is commented out so the example table is not installed.
	 * Once you define the SQL for your new table, uncomment this line to install
	 * the table. (Make sure you increment the BP_EXAMPLE_DB_VERSION constant though).
	 */
	// dbDelta($sql);

	update_site_option( 'bp-example-db-version', BP_EXAMPLE_DB_VERSION );
}
register_activation_hook( __FILE__, 'bp_example_activate' );

/* On deacativation, clean up anything your component has added. */
function bp_example_deactivate() {
	/* You might want to delete any options or tables that your component created. */
}
register_deactivation_hook( __FILE__, 'bp_example_deactivate' );
?>