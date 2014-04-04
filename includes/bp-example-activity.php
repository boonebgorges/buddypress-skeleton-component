<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Having all functions that relates to activity component
 * in a specific file that is included only if the activity
 * component is active will avoid you to add various bp_is_active
 * checks throughout your component.
 * 
 * Then in this file we will use hooks to generate the activities :
 * - bp_example_accept_terms is hooked by bp_example_record_accepted_terms_activity()
 * - bp_example_reject_terms is hooked by bp_example_record_rejected_terms_activity()
 * - bp_example_send_high_five is hooked by bp_example_record_high_five_activity()
 */ 

/**
 * Hooking to bp_register_activity_actions will make your component's
 * actions available in the WP Admin Activity screen and will allow
 * you to define the format callback for the action displayed in the
 * activity screen. Using the format callback will make your strings
 * translatable as they will be generated at run time and not just retrieved
 * from the {$wpdb->base_prefix}bp_activity table
 */ 
function bp_example_register_activity_actions() {
	$bp = buddypress();

	bp_activity_set_action(
		$bp->example->id,
		'accepted_terms',
		__( 'Accepted terms', 'bp-example' ),
		'bp_example_format_accepted_terms_activity_action'
	);

	bp_activity_set_action(
		$bp->example->id,
		'rejected_terms',
		__( 'Rejected terms', 'bp-example' ),
		'bp_example_format_rejected_terms_activity_action'
	);

	bp_activity_set_action(
		$bp->example->id,
		'new_high_five',
		__( 'High fives', 'bp-example' ),
		'bp_example_format_high_fives_activity_action'
	);

	do_action( 'bp_example_register_activity_actions' );
}
add_action( 'bp_register_activity_actions', 'bp_example_register_activity_actions' );

/**
 * New since BuddyPress 2.0
 * 
 * @todo add some explanations about the improvements introduced by generating
 * actions using the format callback (translation concerns)
 */
function bp_example_format_accepted_terms_activity_action( $action, $activity ) {
	if ( empty( $action ) || empty( $activity ) )
		return false;

	$user_link = bp_core_get_userlink( $activity->user_id );

	$action  = sprintf( __( '%s accepted the really exciting terms and conditions!', 'bp-example' ), $user_link );

	return apply_filters( 'bp_example_format_rejected_terms_activity_action', $action, $activity );
}

function bp_example_format_rejected_terms_activity_action( $action, $activity ) {
	if ( empty( $action ) || empty( $activity ) )
		return false;

	$user_link = bp_core_get_userlink( $activity->user_id );

	$action  = sprintf( __( '%s rejected the really exciting terms and conditions.', 'bp-example' ), $user_link );

	return apply_filters( 'bp_example_format_rejected_terms_activity_action', $action, $activity );
}

function bp_example_format_high_fives_activity_action( $action, $activity ) {
	if ( empty( $action ) || empty( $activity ) )
		return false;

	$from_user_link = bp_core_get_userlink( $activity->user_id );

	// the activity item_id contains the "to user" id
	// see in bp_example_record_high_five_activity() the argument 'item_id'
	// used in bp_example_record_activity function
	$to_user_link = bp_core_get_userlink( $activity->item_id );

	$action  = sprintf( __( '%s high-fived %s!', 'bp-example' ), $from_user_link, $to_user_link );

	return apply_filters( 'bp_example_format_high_fives_activity_action', $action, $activity );
}


/**
 * Adds the available actions to activity filters.
 * 
 * As you registered your components action in the global
 * activity actions, let's use it to create the filter
 * options in Site Wide Activity directory & in members
 * activity stream.
 * 
 * @return string html output
 */
function bp_example_activity_options() {

	$bp_example_actions = buddypress()->activity->actions->example;

	foreach ( $bp_example_actions as $action ) {
		?>
		<option value="<?php echo esc_attr( $action['key'] ) ;?>"><?php echo esc_html( $action['value'] ) ;?></option>
		<?php
	}
}
add_action( 'bp_activity_filter_options',        'bp_example_activity_options' );
add_action( 'bp_member_activity_filter_options', 'bp_example_activity_options' );

/**
 * Hooking bp_example_accept_terms to record an activity
 */
function bp_example_record_accepted_terms_activity( $user_id = 0 ) {
	// Bail if user id is not defined
	if ( empty( $user_id ) )
		return;

	/***
	 * Here is a good example of where we can post something to a users activity stream.
	 * The user has excepted the terms on screen two, and now we want to post
	 * "Andy accepted the really exciting terms and conditions!" to the stream.
	 */
	$user_link = bp_core_get_userlink( $user_id );

	bp_example_record_activity( array(
		'type' => 'accepted_terms',
		'action' => apply_filters( 'bp_example_accepted_terms_activity_action', sprintf( __( '%s accepted the really exciting terms and conditions!', 'bp-example' ), $user_link ), $user_link ),
	) );

	/* See bp_example_record_rejected_terms_activity for an explanation of deleting activity items */
	bp_activity_delete( array( 'type' => 'rejected_terms', 'user_id' => $user_id ) );
}
add_action( 'bp_example_accept_terms', 'bp_example_record_accepted_terms_activity', 10, 1 );

/**
 * Hooking bp_example_reject_terms to record an activity
 */
function bp_example_record_rejected_terms_activity( $user_id = 0 ) {
	// Bail if user id is not defined
	if ( empty( $user_id ) )
		return;

	/***
	 * In this example component, the user can reject the terms even after they have
	 * previously accepted them.
	 *
	 * If a user has accepted the terms previously, then this will be in their activity
	 * stream. We don't want both 'accepted' and 'rejected' in the activity stream, so
	 * we should remove references to the user accepting from all activity streams.
	 * A real world example of this would be a user deleting a published blog post.
	 */

	$user_link = bp_core_get_userlink( $user_id );

	/* Now record the new 'rejected' activity item */
	bp_example_record_activity( array(
		'type' => 'rejected_terms',
		'action' => apply_filters( 'bp_example_rejected_terms_activity_action', sprintf( __( '%s rejected the really exciting terms and conditions.', 'bp-example' ), $user_link ), $user_link ),
	) );

	/* Delete any accepted_terms activity items for the user */
	bp_activity_delete( array( 'type' => 'accepted_terms', 'user_id' => $user_id ) );
}
add_action( 'bp_example_reject_terms', 'bp_example_record_rejected_terms_activity', 1, 1 );

/**
 * Hooking bp_example_send_high_five to record an activity
 */
function bp_example_record_high_five_activity( $to_user_id = 0, $from_user_id = 0 ) {
	// Bail if we don't have the needed inputs
	if ( empty( $to_user_id ) || empty( $from_user_id ) )
		return;

	/* Now record the new 'new_high_five' activity item */
	$to_user_link = bp_core_get_userlink( $to_user_id );
	$from_user_link = bp_core_get_userlink( $from_user_id );

	bp_example_record_activity( array(
		'type' => 'new_high_five',
		'action' => apply_filters( 'bp_example_new_high_five_activity_action', sprintf( __( '%s high-fived %s!', 'bp-example' ), $from_user_link, $to_user_link ), $from_user_link, $to_user_link ),
		'item_id' => $to_user_id,
	) );
}
// 9 priority is used to fire before the notifications
add_action( 'bp_example_send_high_five', 'bp_example_record_high_five_activity', 9, 2 );

/**
 * bp_example_record_activity()
 *
 * If the activity stream component is installed, this function will record activity items for your
 * component.
 *
 * You must pass the function an associated array of arguments:
 *
 *     $args = array(
 *	 	 REQUIRED PARAMS
 *		 'action' => For example: "Andy high-fived John", "Andy posted a new update".
 *       'type' => The type of action being carried out, for example 'new_friendship', 'joined_group'. This should be unique within your component.
 *
 *		 OPTIONAL PARAMS
 *		 'id' => The ID of an existing activity item that you want to update.
 * 		 'content' => The content of your activity, if it has any, for example a photo, update content or blog post excerpt.
 *       'component' => The slug of the component.
 *		 'primary_link' => The link for the title of the item when appearing in RSS feeds (defaults to the activity permalink)
 *       'item_id' => The ID of the main piece of data being recorded, for example a group_id, user_id, forum_post_id - useful for filtering and deleting later on.
 *		 'user_id' => The ID of the user that this activity is being recorded for. Pass false if it's not for a user.
 *		 'recorded_time' => (optional) The time you want to set as when the activity was carried out (defaults to now)
 *		 'hide_sitewide' => Should this activity item appear on the site wide stream?
 *		 'secondary_item_id' => (optional) If the activity is more complex you may need a second ID. For example a group forum post may need the group_id AND the forum_post_id.
 *     )
 *
 * Example usage would be:
 *
 *   bp_example_record_activity( array( 'type' => 'new_highfive', 'action' => 'Andy high-fived John', 'user_id' => $bp->loggedin_user->id, 'item_id' => $bp->displayed_user->id ) );
 *
 */
function bp_example_record_activity( $args = '' ) {
	$bp = buddypress();

	$defaults = array(
		'id' => false,
		'user_id' => $bp->loggedin_user->id,
		'action' => '',
		'content' => '',
		'primary_link' => '',
		'component' => $bp->example->id,
		'type' => false,
		'item_id' => false,
		'secondary_item_id' => false,
		'recorded_time' => gmdate( "Y-m-d H:i:s" ),
		'hide_sitewide' => false
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r );

	return bp_activity_add( array( 'id' => $id, 'user_id' => $user_id, 'action' => $action, 'content' => $content, 'primary_link' => $primary_link, 'component' => $component, 'type' => $type, 'item_id' => $item_id, 'secondary_item_id' => $secondary_item_id, 'recorded_time' => $recorded_time, 'hide_sitewide' => $hide_sitewide ) );
}
