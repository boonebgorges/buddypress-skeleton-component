<?php

/********************************************************************************
 * Screen Functions
 *
 * Screen functions are the controllers of BuddyPress. They will execute when their
 * specific URL is caught. They will first save or manipulate data using business
 * functions, then pass on the user to a template file.
 */

/**
 * Main Screen Class.
 *
 * @since BuddyPress Skeleton Component
 */
class BuddyPress_Skeleton_Screens {

	/**
	 * The constructor
	 *
	 * @package BuddyPress Skeleton Component
	 * @subpackage Screens
	 * @since 1.7.0
	 */
	public function __construct() {
		$this->setup_globals();
		$this->setup_filters();
	}

	public static function manage_screens() {
		$bp = buddypress();

		if ( empty( $bp->extend->skeleton->screens ) ) {
			$bp->extend->skeleton->screens = new self;
		}

		return $bp->extend->skeleton->screens;
	}

	/**
	 * Set some globals
	 *
	 * @package BuddyPress Skeleton Component
	 * @subpackage Screens
	 * @since 1.7.0
	 */
	public function setup_globals() {
		$bp = buddypress();

		$this->template     = '';
		$this->template_dir = $bp->extend->skeleton->includes_dir . 'templates';
	}

	/**
	 * Set filters
	 *
	 * @package BuddyPress Skeleton Component
	 * @subpackage Screens
	 * @since 1.7.0
	 */
	private function setup_filters() {
		if ( bp_is_current_component( 'example' ) ) {
			add_filter( 'bp_located_template',   array( $this, 'template_filter' ), 20, 2 );
			add_filter( 'bp_get_template_stack', array( $this, 'add_to_template_stack' ), 10, 1 );
		}
	}

	/**
	 * Filter the located template
	 *
	 * If a template was found, it means the theme added it to his folder
	 * If the current them uses theme compat, we need to return false so 
	 * that in the BuddyPress function bp_core_load_template() the action
	 * bp_setup_theme_compat is fired and allow Theme compatibility to
	 * manage templates.
	 * 
	 * @package BuddyPress Skeleton Component
	 * @subpackage Screens
	 * @since 1.7.0
	 */
	public function template_filter( $found_template = '', $templates = array() ) {
		$bp = buddypress();

		// Bail if theme has it's own template for content.
		if ( ! empty( $found_template ) )
			return $found_template;

		// Current theme do use theme compat, no need to carry on
		if ( $bp->theme_compat->use_with_current_theme )
			return false;

		return apply_filters( 'bp_example_load_template_filter', $found_template );
	}

	public function add_to_template_stack( $templates = array() ) {
		// Adding the plugin's provided template to the end of the stack
		// So that the theme can override it.
		return array_merge( $templates, array( buddypress()->extend->skeleton->includes_dir . 'templates' ) );
	}

	/**
	 * screen_one()
	 *
	 * Sets up and displays the screen output for the sub nav item "example/screen-one"
	 */
	public static function screen_one() {

		do_action( 'bp_example_screen_one' );

		self::load_template( 'example/screen-one', 'screen_one' );
	}

	/**
	 * screen_two()
	 *
	 * Sets up and displays the screen output for the sub nav item "example/screen-two"
	 */
	public static function screen_two() {

		do_action( 'bp_example_screen_two' );

		// We'll only use members/single/plugins
		self::load_template( '', 'screen_two' );
	}

	public static function load_template( $template = '', $screen = '' ) {
		$bp = buddypress();
		/****
		 * Displaying Content
		 */
		$bp->extend->skeleton->screens->template = $template;
		/****
		 * OPTION 1:
		 * You've got a few options for displaying content. Your first option is to bundle template files
		 * with your plugin that will be used to output content.
		 *
		 * In an earlier function bp_example_load_template_filter() we set up a filter on the core BP template
		 * loading function that will make it first look in the plugin directory for template files.
		 * If it doesn't find any matching templates it will look in the active theme directory.
		 *
		 * This example component comes bundled with a template for screen one, so we can load that
		 * template to display what we need. If you copied this template from the plugin into your theme
		 * then it would load that one instead. This allows users to override templates in their theme.
		 */
		if ( buddypress()->theme_compat->use_with_current_theme && ! empty( $template ) ) {
			add_filter( 'bp_get_template_part', array( __CLASS__, 'template_part' ), 10, 3 );
		} else {
			// You can only use this method for users profile pages
			if ( ! bp_is_directory() ) {
				/****
				 * OPTION 2:
				 * If your component is simple, and you just want to insert some HTML into the user's active theme
				 * then you can use the bundle plugin template.
				 *
				 * Or you can use this technique as a fallback if the theme does not support theme compat
				 *
				 * There are two actions you need to hook into. One for the title, and one for the content.
				 * The functions you hook these into should simply output the content you want to display on the
				 * page.
				 *
				 * The follow lines are commented out because we are not using this method for this screen.
				 * You'd want to remove the OPTION 1 parts above and uncomment these lines if you want to use
				 * this option instead.
				 *
				 * Generally, this method of adding content is preferred, as it makes your plugin
				 * work better with a wider variety of themes.
			 	 */
				$bp->extend->skeleton->screens->template = 'members/single/plugins';
				add_action( 'bp_template_title',   "bp_example_{$screen}_title"   );
				add_action( 'bp_template_content', "bp_example_{$screen}_content" );
			}
		}

		/* This is going to look in wp-content/plugins/[plugin-name]/includes/templates/ first */
		bp_core_load_template( apply_filters( "bp_example_template_{$screen}", $bp->extend->skeleton->screens->template ) );
	}

	public static function template_part( $templates, $slug, $name ) {
		if ( $slug != 'members/single/plugins' ) {
	        return $templates;
		}
	    return array( buddypress()->extend->skeleton->screens->template . '.php' );
	}
	
}
add_action( 'bp_init', array( 'BuddyPress_Skeleton_Screens', 'manage_screens' ) );

/**
 * If your component uses a top-level directory, this function will catch the requests and load
 * the index page.
 *
 * @package BuddyPress_Template_Pack
 * @since 1.6
 */
function bp_example_directory_setup() {
	if ( bp_is_example_component() && !bp_current_action() && !bp_current_item() ) {
		// This wrapper function sets the $bp->is_directory flag to true, which help other
		// content to display content properly on your directory.
		bp_update_is_directory( true, 'example' );

		// Add an action so that plugins can add content or modify behavior
		do_action( 'bp_example_directory_setup' );

		bp_core_load_template( apply_filters( 'example_directory_template', 'example/index' ) );
	}
}
add_action( 'bp_screens', 'bp_example_directory_setup' );


/***
 * The second argument of each of the above add_action() calls is a function that will
 * display the corresponding information. The functions are presented below:
 */
function bp_example_screen_one_title() {
	_e( 'Screen One', 'bp-example' );
}

function bp_example_screen_one_content() {
	global $bp;

	$high_fives = bp_example_get_highfives_for_user( $bp->displayed_user->id );

	/**
	 * For security reasons, we MUST use the wp_nonce_url() function on any actions.
	 * This will stop naughty people from tricking users into performing actions without their
	 * knowledge or intent.
	 */
	$send_link = wp_nonce_url( $bp->displayed_user->domain . $bp->current_component . '/screen-one/send-h5', 'bp_example_send_high_five' );
?>
	<h4><?php _e( 'Welcome to Screen One', 'bp-example' ) ?></h4>
	<p><?php printf( __( 'Send %s a <a href="%s" title="Send high-five!">high-five!</a>', 'bp-example' ), $bp->displayed_user->fullname, $send_link ) ?></p>

	<?php if ( $high_fives ) : ?>
		<h4><?php _e( 'Received High Fives!', 'bp-example' ) ?></h4>

		<table id="high-fives">
			<?php foreach ( $high_fives as $user_id ) : ?>
			<tr>
				<td width="1%"><?php echo bp_core_fetch_avatar( array( 'item_id' => $user_id, 'width' => 25, 'height' => 25 ) ) ?></td>
				<td>&nbsp; <?php echo bp_core_get_userlink( $user_id ) ?></td>
 			</tr>
			<?php endforeach; ?>
		</table>
	<?php endif; ?>
<?php
}

function bp_example_screen_two_title() {
	_e( 'Screen Two', 'bp-example' );
}

function bp_example_screen_two_content() {
	global $bp; ?>

	<h4><?php _e( 'Welcome to Screen Two', 'bp-example' ) ?></h4>

	<?php
		$accept_link = '<a href="' . wp_nonce_url( $bp->loggedin_user->domain . $bp->example->slug . '/screen-two/accept', 'bp_example_accept_terms' ) . '">' . __( 'Accept', 'bp-example' ) . '</a>';
		$reject_link = '<a href="' . wp_nonce_url( $bp->loggedin_user->domain . $bp->example->slug . '/screen-two/reject', 'bp_example_reject_terms' ) . '">' . __( 'Reject', 'bp-example' ) . '</a>';
	?>

	<p><?php printf( __( 'You must %s or %s the terms of use policy.', 'bp-example' ), $accept_link, $reject_link ) ?></p>
<?php
}

/**
 * The following screen functions are called when the Settings subpanel for this component is viewed
 */
function bp_example_screen_settings_menu() {
	global $bp, $current_user, $bp_settings_updated, $pass_error;

	if ( isset( $_POST['submit'] ) ) {
		/* Check the nonce */
		check_admin_referer('bp-example-admin');

		$bp_settings_updated = true;

		/**
		 * This is when the user has hit the save button on their settings.
		 * The best place to store these settings is in wp_usermeta.
		 */
		update_user_meta( $bp->loggedin_user->id, 'bp-example-option-one', attribute_escape( $_POST['bp-example-option-one'] ) );
	}

	add_action( 'bp_template_content_header', 'bp_example_screen_settings_menu_header' );
	add_action( 'bp_template_title', 'bp_example_screen_settings_menu_title' );
	add_action( 'bp_template_content', 'bp_example_screen_settings_menu_content' );

	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

	function bp_example_screen_settings_menu_header() {
		_e( 'Example Settings Header', 'bp-example' );
	}

	function bp_example_screen_settings_menu_title() {
		_e( 'Example Settings', 'bp-example' );
	}

	function bp_example_screen_settings_menu_content() {
		global $bp, $bp_settings_updated; ?>

		<?php if ( $bp_settings_updated ) { ?>
			<div id="message" class="updated fade">
				<p><?php _e( 'Changes Saved.', 'bp-example' ) ?></p>
			</div>
		<?php } ?>

		<form action="<?php echo $bp->loggedin_user->domain . 'settings/example-admin'; ?>" name="bp-example-admin-form" id="account-delete-form" class="bp-example-admin-form" method="post">

			<input type="checkbox" name="bp-example-option-one" id="bp-example-option-one" value="1"<?php if ( '1' == get_user_meta( $bp->loggedin_user->id, 'bp-example-option-one', true ) ) : ?> checked="checked"<?php endif; ?> /> <?php _e( 'Do you love clicking checkboxes?', 'bp-example' ); ?>
			<p class="submit">
				<input type="submit" value="<?php _e( 'Save Settings', 'bp-example' ) ?> &raquo;" id="submit" name="submit" />
			</p>

			<?php
			/* This is very important, don't leave it out. */
			wp_nonce_field( 'bp-example-admin' );
			?>

		</form>
	<?php
	}
?>