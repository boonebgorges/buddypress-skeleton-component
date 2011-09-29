<?php

/***
 * This file is used to add site administration menus to the WordPress backend.
 *
 * If you need to provide configuration options for your component that can only
 * be modified by a site administrator, this is the best place to do it.
 *
 * However, if your component has settings that need to be configured on a user
 * by user basis - it's best to hook into the front end "Settings" menu.
 */

/**
 * bp_example_add_admin_menu()
 *
 * This function will add a WordPress wp-admin admin menu for your component under the
 * "BuddyPress" menu.
 */
function bp_example_add_admin_menu() {
	global $bp;

	if ( !is_super_admin() )
		return false;

	add_submenu_page( 'bp-general-settings', __( 'Example Admin', 'bp-example' ), __( 'Example Admin', 'bp-example' ), 'manage_options', 'bp-example-settings', 'bp_example_admin' );
}
// The bp_core_admin_hook() function returns the correct hook (admin_menu or network_admin_menu),
// depending on how WordPress and BuddyPress are configured
add_action( bp_core_admin_hook(), 'bp_example_add_admin_menu' );

/**
 * bp_example_admin()
 *
 * Checks for form submission, saves component settings and outputs admin screen HTML.
 */
function bp_example_admin() {
	global $bp;

	/* If the form has been submitted and the admin referrer checks out, save the settings */
	if ( isset( $_POST['submit'] ) && check_admin_referer('example-settings') ) {
		update_option( 'example-setting-one', $_POST['example-setting-one'] );
		update_option( 'example-setting-two', $_POST['example-setting-two'] );

		$updated = true;
	}

	$setting_one = get_option( 'example-setting-one' );
	$setting_two = get_option( 'example-setting-two' );
?>
	<div class="wrap">
		<h2><?php _e( 'Example Admin', 'bp-example' ) ?></h2>
		<br />

		<?php if ( isset($updated) ) : ?><?php echo "<div id='message' class='updated fade'><p>" . __( 'Settings Updated.', 'bp-example' ) . "</p></div>" ?><?php endif; ?>

		<form action="<?php echo site_url() . '/wp-admin/admin.php?page=bp-example-settings' ?>" name="example-settings-form" id="example-settings-form" method="post">

			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="target_uri"><?php _e( 'Option One', 'bp-example' ) ?></label></th>
					<td>
						<input name="example-setting-one" type="text" id="example-setting-one" value="<?php echo esc_attr( $setting_one ); ?>" size="60" />
					</td>
				</tr>
					<th scope="row"><label for="target_uri"><?php _e( 'Option Two', 'bp-example' ) ?></label></th>
					<td>
						<input name="example-setting-two" type="text" id="example-setting-two" value="<?php echo esc_attr( $setting_two ); ?>" size="60" />
					</td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" name="submit" value="<?php _e( 'Save Settings', 'bp-example' ) ?>"/>
			</p>

			<?php
			/* This is very important, don't leave it out. */
			wp_nonce_field( 'example-settings' );
			?>
		</form>
	</div>
<?php
}

/**
 * Test to see if the necessary database tables are installed, and if not, install them
 *
 * You will only need a function like this if you need to install database tables. It is not
 * recommended that you do so if you can help it; it clutters up users' databases, and it creates
 * problems when attempting to interact with the rest of WordPress. You are highly encouraged
 * to use WordPress custom post types instead.
 *
 * Doing this check in the admin, instead of at activation time, adds a bit of overhead. But the
 * WordPress core developers have expressed a dislike for activation functions, so we do it this
 * way instead. Don't worry - dbDelta() is quite smart about not overwriting anything.
 *
 * @package BuddyPress_Skeleton_Component
 * @since 1.6
 */
function bp_example_install_tables() {
	global $wpdb;

	if ( !is_super_admin() )
		return;

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
	$sql = array();
	$sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->base_prefix}bp_example (
		  		id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  		high_fiver_id bigint(20) NOT NULL,
		  		recipient_id bigint(20) NOT NULL,
		  		date_notified datetime NOT NULL,
			    KEY high_fiver_id (high_fiver_id),
			    KEY recipient_id (recipient_id)
		 	   ) {$charset_collate};";

	//require_once( ABSPATH . 'wp-admin/upgrade.php' );

	/**
	 * The dbDelta call is commented out so the example table is not installed.
	 * Once you define the SQL for your new table, uncomment this line to install
	 * the table. (Make sure you increment the BP_EXAMPLE_DB_VERSION constant though).
	 */
	dbDelta($sql);

	update_site_option( 'bp-example-db-version', BP_EXAMPLE_DB_VERSION );
}
//add_action( 'admin_init', 'bp_example_install_tables' );
?>