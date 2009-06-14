<?php

/**
 * In this file you should create and register widgets for your component.
 *
 * Widgets should be small, contained functionality that a site administrator can drop into
 * a widget enabled zone (column, sidebar etc)
 *
 * Good examples of suitable widget functionality would be short lists of updates or featured content.
 *
 * For example the friends and groups components have widgets to show the active, newest and most popular
 * of each.
 */
 
 /***
  * Localization Issues with BuddyPress Widgets
  *
  * NOTE: Although the WordPress widget API clearly advises developers not to use the widget functions starting
  * with wp_, there is an issue with localization of Widgets in BuddyPress if the register_sidebar_widget() is used.
  *
  * If you're not planning on distributing your custom components, then you can code your widgets with the
  * register_sidebar_widget() function call instead of wp_register_sidebar_widget(). However, if you plan to make your
  * custom component available to the community, you should use wp_register_sidebar_widget() at this time.
  *
  * See BuddyPress Changeset 1244 for more details 
  *
  * Two alternate ways:
  *
  * 	A: Will work fine but cause localization issues for others trying to translate
  *
  * 		register_sidebar_widget( __( 'Cool Example Widget', 'bp-example' ), 'bp_example_widget_cool_widget');
  * 		register_widget_control( __( 'Cool Example Widget', 'bp-example' ), 'bp_example_widget_cool_widget_control' );
  *
  * 	B: Addresses localization issues but is considered outdated by WordPress widget API
  *
  *			wp_register_sidebar_widget( 'buddypress-example', __( 'Cool Example Widget', 'bp-example' ), 'bp_example_widget_cool_widget');
  *			wp_register_widget_control( 'buddypress-example', __( 'Cool Example Widget', 'bp-example' ), 'bp_example_widget_cool_widget_control' );
  *
  *
  * @link http://codex.wordpress.org/Plugins/WordPress_Widgets_Api Widgets API
  */
  
 /*	NOTE: Once WPMU is updated to the WP2.8 codebase, you should consider the
	new widget API available here: http://codex.wordpress.org/Version_2.8#New_Widgets_API
*/
  
/**
 * bp_component_register_widgets()
 *
 * This function will register your widgets so that they will show up on the widget list
 * for site administrators to drop into their widget zones.
 */
function bp_example_register_widgets() {
	global $current_blog;
	
	/* Site welcome widget */
	wp_register_sidebar_widget( 'buddypress-example', __( 'Cool Example Widget', 'bp-example' ), 'bp_example_widget_cool_widget');
	wp_register_widget_control( 'buddypress-example', __( 'Cool Example Widget', 'bp-example' ), 'bp_example_widget_cool_widget_control' );
	
	/* Include the javascript and /or CSS needed for activated widgets only. If none needed, this code can be left out. */
	if ( is_active_widget( 'bp_example_widget_cool_widget' ) ) {
		wp_enqueue_script( 'bp_example_widget_cool_widget-js', WP_PLUGIN_URL . '/bp-example/js/widget-example.js', array('jquery', 'jquery-livequery-pack') );
		wp_enqueue_style( 'bp_example_widget_cool_widget-css', WP_PLUGIN_URL . '/bp-example/css/widget-example.css' );
	}
}
add_action( 'plugins_loaded', 'bp_example_register_widgets' );

/**
 * bp_example_widget_cool_widget()
 *
 * This function controls the actual HTML output of the widget. This is where you will
 * want to query whatever you need, and render the actual output.
 */
function bp_example_widget_cool_widget($args) {
	global $current_blog, $bp;
	
    extract($args);

	/***
	 * This is where you'll want to fetch the widget settings and use them to modify the
	 * widget's output.
	 */
	$options = get_blog_option( $current_blog->blog_id, 'bp_example_widget_cool_widget' );
?>
	<?php echo $before_widget; ?>
	<?php echo $before_title
		. $widget_name 
		. $after_title; ?>

	<?php
 	/* Consider using object caching here (see the bottom of 'bp-example.php' for more info)	
	 * 
	 * Example:
	 *	
	 *	if ( empty( $options['max_groups'] ) || !$options['max_groups'] ) 
	 *		$options['max_groups'] = 5; 
	 *
	 *	if ( !$groups = wp_cache_get( 'popular_groups', 'bp' ) ) {
	 *		$groups = groups_get_popular( $options['max_groups'], 1 );
	 *		wp_cache_set( 'popular_groups', $groups, 'bp' );
	 *	}
	 *
	 */
	?>

	<?php
	
	/***
	 * This is where you add your HTML and render what you want your widget to display.
	 */
	
	?>

	<?php echo $after_widget; ?>
<?php
}

/**
 * bp_example_widget_cool_widget_control()
 *
 * This function will enable a "edit" menu on your widget. This lets site admins click
 * the edit link on the widget to set options. The options you can then use in the display of 
 * your widget.
 *
 * For example, in the groups component widget there is a setting called "max-groups" where
 * a user can define how many groups they would like to display.
 */
function bp_example_widget_cool_widget_control() {
	global $current_blog;
	
	$options = $newoptions = get_blog_option( $current_blog->blog_id, 'bp_example_widget_cool_widget');

	if ( $_POST['bp-example-widget-cool-widget'] ) {
		$newoptions['option_name'] = strip_tags( stripslashes( $_POST['bp-example-widget-cool-widget-option'] ) );
	}
	
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_blog_option( $current_blog->blog_id, 'bp_example_widget_cool_widget', $options );
	}
?>
	<p><label for="bp-example-widget-cool-widget-option"><?php _e( 'Some Option', 'bp-example' ); ?><br /> <input class="widefat" id="bp-example-widget-cool-widget-option" name="bp-example-widget-cool-widget-option" type="text" value="<?php echo attribute_escape( $options['option_name'] ); ?>" style="width: 30%" /></label></p>
	<input type="hidden" id="bp-example-widget-cool-widget" name="bp-example-widget-cool-widget" value="1" />
<?php
}
?>