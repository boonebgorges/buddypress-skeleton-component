<?php



/********************************************************************************
 * Activity & Notification Functions
 *
 * These functions handle the recording, deleting and formatting of activity and
 * notifications for the user and for this specific component.
 */


/**
 * bp_example_screen_notification_settings()
 *
 * Adds notification settings for the component, so that a user can turn off email
 * notifications set on specific component actions.
 */
function bp_example_screen_notification_settings() {
	global $current_user;

	/**
	 * Under Settings > Notifications within a users profile page they will see
	 * settings to turn off notifications for each component.
	 *
	 * You can plug your custom notification settings into this page, so that when your
	 * component is active, the user will see options to turn off notifications that are
	 * specific to your component.
	 */

	 /**
	  * Each option is stored in a posted array notifications[SETTING_NAME]
	  * When saved, the SETTING_NAME is stored as usermeta for that user.
	  *
	  * For example, notifications[notification_friends_friendship_accepted] could be
	  * used like this:
	  *
	  * if ( 'no' == get_user_meta( $bp->displayed_user->id, 'notification_friends_friendship_accepted', true ) )
	  *		// don't send the email notification
	  *	else
	  *		// send the email notification.
      */

	?>
	<table class="notification-settings" id="bp-example-notification-settings">

		<thead>
		<tr>
			<th class="icon"></th>
			<th class="title"><?php _e( 'Example', 'bp-example' ) ?></th>
			<th class="yes"><?php _e( 'Yes', 'bp-example' ) ?></th>
			<th class="no"><?php _e( 'No', 'bp-example' )?></th>
		</tr>
		</thead>

		<tbody>
		<tr>
			<td></td>
			<td><?php _e( 'Action One', 'bp-example' ) ?></td>
			<td class="yes"><input type="radio" name="notifications[notification_example_action_one]" value="yes" <?php if ( !get_user_meta( $current_user->id, 'notification_example_action_one', true ) || 'yes' == get_user_meta( $current_user->id, 'notification_example_action_one', true ) ) { ?>checked="checked" <?php } ?>/></td>
			<td class="no"><input type="radio" name="notifications[notification_example_action_one]" value="no" <?php if ( get_user_meta( $current_user->id, 'notification_example_action_one') == 'no' ) { ?>checked="checked" <?php } ?>/></td>
		</tr>
		<tr>
			<td></td>
			<td><?php _e( 'Action Two', 'bp-example' ) ?></td>
			<td class="yes"><input type="radio" name="notifications[notification_example_action_two]" value="yes" <?php if ( !get_user_meta( $current_user->id, 'notification_example_action_two', true ) || 'yes' == get_user_meta( $current_user->id, 'notification_example_action_two', true ) ) { ?>checked="checked" <?php } ?>/></td>
			<td class="no"><input type="radio" name="notifications[notification_example_action_two]" value="no" <?php if ( 'no' == get_user_meta( $current_user->id, 'notification_example_action_two', true ) ) { ?>checked="checked" <?php } ?>/></td>
		</tr>

		<?php do_action( 'bp_example_notification_settings' ); ?>

		</tbody>
	</table>
<?php
}
add_action( 'bp_notification_settings', 'bp_example_screen_notification_settings' );


/**
 * bp_example_remove_screen_notifications()
 *
 * Remove a screen notification for a user.
 */
function bp_example_remove_screen_notifications() {
	global $bp;

	/**
	 * When clicking on a screen notification, we need to remove it from the menu.
	 * The following command will do so.
 	 */
	bp_core_delete_notifications_for_user_by_type( $bp->loggedin_user->id, $bp->example->slug, 'new_high_five' );
}
add_action( 'bp_example_screen_one', 'bp_example_remove_screen_notifications' );
add_action( 'xprofile_screen_display_profile', 'bp_example_remove_screen_notifications' );


/**
 * bp_example_format_notifications()
 *
 * The format notification function will take DB entries for notifications and format them
 * so that they can be displayed and read on the screen.
 *
 * Notifications are "screen" notifications, that is, they appear on the notifications menu
 * in the site wide navigation bar. They are not for email notifications.
 *
 *
 * The recording is done by using bp_core_add_notification() which you can search for in this file for
 * examples of usage.
 */
function bp_example_format_notifications( $action, $item_id, $secondary_item_id, $total_items ) {
	global $bp;

	switch ( $action ) {
		case 'new_high_five':
			/* In this case, $item_id is the user ID of the user who sent the high five. */

			/***
			 * We don't want a whole list of similar notifications in a users list, so we group them.
			 * If the user has more than one action from the same component, they are counted and the
			 * notification is rendered differently.
			 */
			if ( (int)$total_items > 1 ) {
				return apply_filters( 'bp_example_multiple_new_high_five_notification', '<a href="' . $bp->loggedin_user->domain . $bp->example->slug . '/screen-one/" title="' . __( 'Multiple high-fives', 'bp-example' ) . '">' . sprintf( __( '%d new high-fives, multi-five!', 'bp-example' ), (int)$total_items ) . '</a>', $total_items );
			} else {
				$user_fullname = bp_core_get_user_displayname( $item_id, false );
				$user_url = bp_core_get_user_domain( $item_id );
				return apply_filters( 'bp_example_single_new_high_five_notification', '<a href="' . $user_url . '?new" title="' . $user_fullname .'\'s profile">' . sprintf( __( '%s sent you a high-five!', 'bp-example' ), $user_fullname ) . '</a>', $user_fullname );
			}
		break;
	}

	do_action( 'bp_example_format_notifications', $action, $item_id, $secondary_item_id, $total_items );

	return false;
}

/**
 * Notification functions are used to send email notifications to users on specific events
 * They will check to see the users notification settings first, if the user has the notifications
 * turned on, they will be sent a formatted email notification.
 *
 * You should use your own custom actions to determine when an email notification should be sent.
 */

function bp_example_send_high_five_notification( $to_user_id, $from_user_id ) {
	global $bp;

	/* Let's grab both user's names to use in the email. */
	$sender_name = bp_core_get_user_displayname( $from_user_id, false );
	$reciever_name = bp_core_get_user_displayname( $to_user_id, false );

	/* We need to check to see if the recipient has opted not to recieve high-five emails */
	if ( 'no' == get_user_meta( (int)$to_user_id, 'notification_example_new_high_five', true ) )
		return false;

	/* Get the userdata for the reciever and sender, this will include usernames and emails that we need. */
	$reciever_ud = get_userdata( $to_user_id );
	$sender_ud = get_userdata( $from_user_id );

	/* Now we need to construct the URL's that we are going to use in the email */
	$sender_profile_link = site_url( BP_MEMBERS_SLUG . '/' . $sender_ud->user_login . '/' . $bp->profile->slug );
	$sender_highfive_link = site_url( BP_MEMBERS_SLUG . '/' . $sender_ud->user_login . '/' . $bp->example->slug . '/screen-one' );
	$reciever_settings_link = site_url( BP_MEMBERS_SLUG . '/' . $reciever_ud->user_login . '/settings/notifications' );

	/* Set up and send the message */
	$to = $reciever_ud->user_email;
	$subject = '[' . get_blog_option( 1, 'blogname' ) . '] ' . sprintf( __( '%s high-fived you!', 'bp-example' ), stripslashes($sender_name) );

	$message = sprintf( __(
'%s sent you a high-five! Why not send one back?

To see %s\'s profile: %s

To send %s a high five: %s

---------------------
', 'bp-example' ), $sender_name, $sender_name, $sender_profile_link, $sender_name, $sender_highfive_link );

	$message .= sprintf( __( 'To disable these notifications please log in and go to: %s', 'bp-example' ), $reciever_settings_link );

	// Send it!
	wp_mail( $to, $subject, $message );
}
add_action( 'bp_example_send_high_five', 'bp_example_send_high_five_notification', 10, 2 );

?>
