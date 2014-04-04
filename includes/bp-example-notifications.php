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
	$bp = buddypress();

	/**
	 * When clicking on a screen notification, we need to remove it from the menu.
	 * The following command will do so.
 	 */
	bp_notifications_mark_notifications_by_type( bp_loggedin_user_id(), $bp->example->id, 'new_high_five' );
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
function bp_example_format_notifications( $action, $item_id, $secondary_item_id, $total_items, $format = 'string' ) {
	$bp = buddypress();

	switch ( $action ) {
		case 'new_high_five':
			/* In this case, $item_id is the user ID of the user who sent the high five. */
			$user_url = trailingslashit( bp_core_get_user_domain( $item_id ) . $bp->profile->slug );
			$user_fullname = bp_core_get_user_displayname( $item_id, false );
			$title = $user_fullname .'\'s profile';

			/***
			 * We don't want a whole list of similar notifications in a users list, so we group them.
			 * If the user has more than one action from the same component, they are counted and the
			 * notification is rendered differently.
			 */
			if ( (int) $total_items > 1 ) {
				$user_url = trailingslashit( $bp->loggedin_user->domain . $bp->example->slug . '/screen-one' );
				$title = __( 'Multiple high-fives', 'bp-example' );
				$text = sprintf( __( '%d new high-fives, multi-five!', 'bp-example' ), (int) $total_items );
				$filter = 'bp_example_multiple_new_high_five_notification';
			} else {
				$text =  sprintf( __( '%s sent you a high-five!', 'bp-example' ), $user_fullname );
				$filter = 'bp_example_single_new_high_five_notification';
			}

		break;
	}

	if ( 'string' == $format ) {
		$return = apply_filters( $filter, '<a href="' . esc_url( $user_url ) . '" title="' . esc_attr( $title ) . '">' . esc_html( $text ) . '</a>', $user_url, (int) $total_items, $item_id, $secondary_item_id );
	} else {
		$return = apply_filters( $filter, array(
			'text' => $text,
			'link' => $user_url
		), $user_url, (int) $total_items, $item_id, $secondary_item_id );
	}

	do_action( 'bp_example_format_notifications', $action, $item_id, $secondary_item_id, $total_items );

	return $return;
}

/**
 * Notification functions are used to send email notifications to users on specific events
 * They will check to see the users notification settings first, if the user has the notifications
 * turned on, they will be sent a formatted email notification.
 *
 * You should use your own custom actions to determine when an email notification should be sent.
 */

function bp_example_send_high_five_notification( $to_user_id = 0, $from_user_id = 0 ) {
	// Bail if we don't have the needed inputs
	if ( empty( $to_user_id ) || empty( $from_user_id ) )
		return;

	$bp = buddypress();

	/***
	 * Post a screen notification to the user's notifications menu.
	 * Remember, like activity streams we need to tell the activity stream component how to format
	 * this notification in bp_example_format_notifications() using the 'new_high_five' action.
	 */
	bp_notifications_add_notification( array(
		'user_id'           => $to_user_id,
		'item_id'           => $from_user_id,
		'component_name'    => $bp->example->id,
		'component_action'  => 'new_high_five'
	) );

	/* Let's grab both user's names to use in the email. */
	$sender_name = bp_core_get_user_displayname( $from_user_id, false );
	$receiver_name = bp_core_get_user_displayname( $to_user_id, false );
	$receiver_email = bp_core_get_user_email( $to_user_id );

	/* We need to check to see if the recipient has opted not to recieve high-five emails */
	if ( 'no' == get_user_meta( (int)$to_user_id, 'notification_example_new_high_five', true ) )
		return false;

	/* Now we need to construct the URL's that we are going to use in the email */
	$sender_profile_link = trailingslashit( bp_core_get_user_domain( $from_user_id ) . $bp->profile->slug );
	$sender_highfive_link = trailingslashit( bp_core_get_user_domain( $from_user_id ) . $bp->example->slug . '/screen-one' );

	/* Set up and send the message */
	$to = $receiver_email;
	$subject = '[' . get_blog_option( 1, 'blogname' ) . '] ' . sprintf( __( '%s high-fived you!', 'bp-example' ), stripslashes( $sender_name ) );

	$message = sprintf( __(
'%s sent you a high-five! Why not send one back?

To see %s\'s profile: %s

To send %s a high five: %s

---------------------
', 'bp-example' ), $sender_name, $sender_name, $sender_profile_link, $sender_name, $sender_highfive_link );

	// Only add the link to email notifications settings if the component is active
	if ( bp_is_active( 'settings' ) ) {
		$receiver_settings_link = trailingslashit( bp_core_get_user_domain( $to_user_id ) . bp_get_settings_slug() . '/notifications' );
		$message .= sprintf( __( 'To disable these notifications please log in and go to: %s', 'bp-example' ), $receiver_settings_link );
	}

	// Send it!
	wp_mail( $to, $subject, $message );
}
add_action( 'bp_example_send_high_five', 'bp_example_send_high_five_notification', 10, 2 );

?>
