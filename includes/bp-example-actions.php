<?php

/**
 * Displays a message when terms are accepted or rejected for screen two
 *
 * @package BuddyPress_Skeleton_Component
 * @since 1.7.0
 */
function bp_example_screen_two_save_terms() {

	if ( bp_is_example_component() && bp_is_current_action( 'screen-two' ) && bp_is_action_variable( 'accept', 0 ) ) {
		if ( bp_example_accept_terms() ) {
			/* Add a success message, that will be displayed in the template on the next page load */
			bp_core_add_message( __( 'Terms were accepted!', 'bp-example' ) );
		} else {
			/* Add a failure message if there was a problem */
			bp_core_add_message( __( 'Terms could not be accepted.', 'bp-example' ), 'error' );
		}

		/**
		 * Now redirect back to the page without any actions set, so the user can't carry out actions multiple times
		 * just by refreshing the browser.
		 */
		bp_core_redirect( bp_loggedin_user_domain() . bp_get_example_slug() );
	}

	if ( bp_is_example_component() && bp_is_current_action( 'screen-two' ) && bp_is_action_variable( 'reject', 0 ) ) {
		if ( bp_example_reject_terms() ) {
			/* Add a success message, that will be displayed in the template on the next page load */
			bp_core_add_message( __( 'Terms were rejected!', 'bp-example' ) );
		} else {
			/* Add a failure message if there was a problem */
			bp_core_add_message( __( 'Terms could not be rejected.', 'bp-example' ), 'error' );
		}

		/**
		 * Now redirect back to the page without any actions set, so the user can't carry out actions multiple times
		 * just by refreshing the browser.
		 */
		bp_core_redirect( bp_loggedin_user_domain() . bp_get_example_slug() );
	}
}
add_action( 'bp_example_screen_two', 'bp_example_screen_two_save_terms' );

/**
 * Check to see if a high five is being given, and if so, save it.
 *
 * Hooked to bp_actions, this function will fire before the screen function. We use our function
 * bp_is_example_component(), along with the bp_is_current_action() and bp_is_action_variable()
 * functions, to detect (based on the requested URL) whether the user has clicked on "send high
 * five". If so, we do a bit of simple logic to see what should happen next.
 *
 * @package BuddyPress_Skeleton_Component
 * @since 1.6
 */
function bp_example_high_five_save() {

	if ( bp_is_example_component() && bp_is_current_action( 'screen-one' ) && bp_is_action_variable( 'send-h5', 0 ) ) {
		// The logged in user has clicked on the 'send high five' link

		if ( bp_is_my_profile() ) {
			// Don't let users high five themselves
			bp_core_add_message( __( 'No self-fives! :)', 'bp-example' ), 'error' );
		} else {
			if ( bp_example_send_highfive( bp_displayed_user_id(), bp_loggedin_user_id() ) )
				bp_core_add_message( __( 'High-five sent!', 'bp-example' ) );
			else
				bp_core_add_message( __( 'High-five could not be sent.', 'bp-example' ), 'error' );
		}

		bp_core_redirect( bp_displayed_user_domain() . bp_get_example_slug() . '/screen-one' );
	}
}
add_action( 'bp_actions', 'bp_example_high_five_save' );

?>