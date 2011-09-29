<?php

// Exit if accessed directly
// It's a good idea to include this in each of your plugin files, for increased security on
// improperly configured servers
if ( !defined( 'ABSPATH' ) ) exit;

/*
 * If you want the users of your component to be able to change the values of your other custom constants,
 * you can use this code to allow them to add new definitions to the wp-config.php file and set the value there.
 *
 *
 *	if ( !defined( 'BP_EXAMPLE_CONSTANT' ) )
 *		define ( 'BP_EXAMPLE_CONSTANT', 'some value' // or some value without quotes if integer );
 */

/**
 * You should try hard to support translation in your component. It's actually very easy.
 * Make sure you wrap any rendered text in __() or _e() and it will then be translatable.
 *
 * You must also provide a text domain, so translation files know which bits of text to translate.
 * Throughout this example the text domain used is 'bp-example', you can use whatever you want.
 * Put the text domain as the second parameter:
 *
 * __( 'This text will be translatable', 'bp-example' ); // Returns the first parameter value
 * _e( 'This text will be translatable', 'bp-example' ); // Echos the first parameter value
 */

if ( file_exists( dirname( __FILE__ ) . '/languages/' . get_locale() . '.mo' ) )
	load_textdomain( 'bp-example', dirname( __FILE__ ) . '/bp-example/languages/' . get_locale() . '.mo' );

/**
 * Implementation of BP_Component
 *
 * BP_Component is the base class that all BuddyPress components use to set up their basic
 * structure, including global data, navigation elements, and admin bar information. If there's
 * a particular aspect of this class that is not relevant to your plugin, just leave it out.
 *
 * @package BuddyPress_Skeleton_Component
 * @since 1.6
 */
class BP_Example_Component extends BP_Component {

	/**
	 * Constructor method
	 *
	 * You can do all sorts of stuff in your constructor, but it's recommended that, at the
	 * very least, you call the parent::start() function. This tells the parent BP_Component
	 * to begin its setup routine.
	 *
	 * BP_Component::start() takes three parameters:
	 *   (1) $id   - A unique identifier for the component. Letters, numbers, and underscores
	 *		 only.
	 *   (2) $name - This is a translatable name for your component, which will be used in
	 *               various places through the BuddyPress admin screens to identify it.
	 *   (3) $path - The path to your plugin directory. Primarily, this is used by
	 *		 BP_Component::includes(), to include your plugin's files. See loader.php
	 *		 to see how BP_EXAMPLE_PLUGIN_DIR was defined.
	 *
	 * @package BuddyPress_Skeleton_Component
	 * @since 1.6
	 */
	function __construct() {
		global $bp;

		parent::start(
			'example',
			__( 'Example', 'bp-example' ),
			BP_EXAMPLE_PLUGIN_DIR
		);

		/**
		 * BuddyPress-dependent plugins are loaded too late to depend on BP_Component's
		 * hooks, so we must call the function directly.
		 */
		 $this->includes();

		/**
		 * Put your component into the active components array, so that
		 *   bp_is_active( 'example' );
		 * returns true when appropriate. We have to do this manually, because non-core
		 * components are not saved as active components in the database.
		 */
		$bp->active_components[$this->id] = '1';

		/**
		 * Hook the register_post_types() method. If you're using custom post types to store
		 * data (which is recommended), you will need to hook your function manually to
		 * 'init'.
		 */
		add_action( 'init', array( &$this, 'register_post_types' ) );
	}

	/**
	 * Include your component's files
	 *
	 * BP_Component has a method called includes(), which will automatically load your plugin's
	 * files, as long as they are properly named and arranged. BP_Component::includes() loops
	 * through the $includes array, defined below, and for each $file in the array, it tries
	 * to load files in the following locations:
	 *   (1) $this->path . '/' . $file - For example, if your $includes array is defined as
	 *           $includes = array( 'notifications.php', 'filters.php' );
	 *       BP_Component::includes() will try to load these files (assuming a typical WP
	 *       setup):
	 *           /wp-content/plugins/bp-example/notifications.php
	 *           /wp-content/plugins/bp-example/filters.php
	 *       Our includes function, listed below, uses a variation on this method, by specifying
	 *       the 'includes' directory in our $includes array.
	 *   (2) $this->path . '/bp-' . $this->id . '/' . $file - Assuming the same $includes array
	 *       as above, BP will look for the following files:
	 *           /wp-content/plugins/bp-example/bp-example/notifications.php
	 *           /wp-content/plugins/bp-example/bp-example/filters.php
	 *   (3) $this->path . '/bp-' . $this->id . '/' . 'bp-' . $this->id . '-' . $file . '.php' -
	 *       This is the format that BuddyPress core components use to load their files. Given
	 *       an $includes array like
	 *           $includes = array( 'notifications', 'filters' );
	 *       BP looks for files at:
	 *           /wp-content/plugins/bp-example/bp-example/bp-example-notifications.php
	 *           /wp-content/plugins/bp-example/bp-example/bp-example-filters.php
	 *
	 * If you'd prefer not to use any of these naming or organizational schemas, you are not
	 * required to use parent::includes(); your own includes() method can require the files
	 * manually. For example:
	 *    require( $this->path . '/includes/notifications.php' );
	 *    require( $this->path . '/includes/filters.php' );
	 *
	 * Notice that this method is called directly in $this->__construct(). While this step is
	 * not necessary for BuddyPress core components, plugins are loaded later, and thus their
	 * includes() method must be invoked manually.
	 *
	 * Our example component includes a fairly large number of files. Your component may not
	 * need to have versions of all of these files. What follows is a short description of
	 * what each file does; for more details, open the file itself and see its inline docs.
	 *   - -actions.php       - Functions hooked to bp_actions, mainly used to catch action
	 *			    requests (save, delete, etc)
	 *   - -screens.php       - Functions hooked to bp_screens. These are the screen functions
	 *			    responsible for the display of your plugin's content.
	 *   - -filters.php	  - Functions that are hooked via apply_filters()
	 *   - -classes.php	  - Your plugin's classes. Depending on how you organize your
	 *			    plugin, this could mean: a database query class, a custom post
	 *			    type data schema, and so forth
	 *   - -activity.php      - Functions related to the BP Activity Component. This is where
	 *			    you put functions responsible for creating, deleting, and
	 *			    modifying activity items related to your component
	 *   - -template.php	  - Template tags. These are functions that are called from your
	 *			    templates, or from your screen functions. If your plugin
	 *			    contains its own version of the WordPress Loop (such as
	 *			    bp_example_has_items()), those functions should go in this file.
	 *   - -functions.php     - Miscellaneous utility functions required by your component.
	 *   - -notifications.php - Functions related to email notification, as well as the
	 *			    BuddyPress notifications that show up in the admin bar.
	 *   - -widgets.php       - If your plugin includes any sidebar widgets, define them in this
	 *			    file.
	 *   - -buddybar.php	  - Functions related to the BuddyBar.
	 *   - -adminbar.php      - Functions related to the WordPress Admin Bar.
	 *   - -cssjs.php	  - Here is where you set up and enqueue your CSS and JS.
	 *   - -ajax.php	  - Functions used in the process of AJAX requests.
	 *
	 * @package BuddyPress_Skeleton_Component
	 * @since 1.6
	 */
	function includes() {

		// Files to include
		$includes = array(
			'includes/bp-example-actions.php',
			'includes/bp-example-screens.php',
			'includes/bp-example-filters.php',
			'includes/bp-example-classes.php',
			'includes/bp-example-activity.php',
			'includes/bp-example-template.php',
			'includes/bp-example-functions.php',
			'includes/bp-example-notifications.php',
			'includes/bp-example-widgets.php',
			'includes/bp-example-cssjs.php',
			'includes/bp-example-ajax.php'
		);

		parent::includes( $includes );

		// As an example of how you might do it manually, let's include the functions used
		// on the WordPress Dashboard conditionally:
		if ( is_admin() || is_network_admin() ) {
			include( BP_EXAMPLE_PLUGIN_DIR . '/includes/bp-example-admin.php' );
		}
	}

	/**
	 * Set up your plugin's globals
	 *
	 * Use the parent::setup_globals() method to set up the key global data for your plugin:
	 *   - 'slug'			- This is the string used to create URLs when your component
	 *				  adds navigation underneath profile URLs. For example,
	 *				  in the URL http://testbp.com/members/boone/example, the
	 *				  'example' portion of the URL is formed by the 'slug'.
	 *				  Site admins can customize this value by defining
	 *				  BP_EXAMPLE_SLUG in their wp-config.php or bp-custom.php
	 *				  files.
	 *   - 'root_slug'		- This is the string used to create URLs when your component
	 *				  adds navigation to the root of the site. In other words,
	 *				  you only need to define root_slug if your component is a
	 *				  "root component". Eg, in:
	 *				    http://testbp.com/example/test
	 *				  'example' is a root slug. This should always be defined
	 *				  in terms of $bp->pages; see the example below. Site admins
	 *				  can customize this value by changing the permalink of the
	 *				  corresponding WP page in the Dashboard. NOTE:
	 *				  'root_slug' requires that 'has_directory' is true.
	 *   - 'has_directory'		- Set this to true if your component requires a top-level
	 *				  directory, such as http://testbp.com/example. When
	 *				  'has_directory' is true, BP will require that site admins
	 *				  associate a WordPress page with your component. NOTE:
	 *				  When 'has_directory' is true, you must also define your
	 *				  component's 'root_slug'; see previous item. Defaults to
	 *				  false.
	 *   - 'notification_callback'  - The name of the function that is used to format BP
	 *				  admin bar notifications for your component.
	 *   - 'search_string'		- If your component is a root component (has_directory),
	 *				  you can provide a custom string that will be used as the
	 *				  default text in the directory search box.
	 *   - 'global_tables'		- If your component creates custom database tables, store
	 *				  the names of the tables in a $global_tables array, so that
	 *				  they are available to other BP functions.
	 *
	 * You can also use this function to put data directly into the $bp global.
	 *
	 * @package BuddyPress_Skeleton_Component
	 * @since 1.6
	 *
	 * @global obj $bp BuddyPress's global object
	 */
	function setup_globals() {
		global $bp;

		// Defining the slug in this way makes it possible for site admins to override it
		if ( !defined( 'BP_EXAMPLE_SLUG' ) )
			define( 'BP_EXAMPLE_SLUG', $this->id );

		// Global tables for the example component. Build your table names using
		// $bp->table_prefix (instead of hardcoding 'wp_') to ensure that your component
		// works with $wpdb, multisite, and custom table prefixes.
		$global_tables = array(
			'table_name'      => $bp->table_prefix . 'bp_example'
		);

		// Set up the $globals array to be passed along to parent::setup_globals()
		$globals = array(
			'slug'                  => BP_EXAMPLE_SLUG,
			'root_slug'             => isset( $bp->pages->{$this->id}->slug ) ? $bp->pages->{$this->id}->slug : BP_EXAMPLE_SLUG,
			'has_directory'         => true, // Set to false if not required
			'notification_callback' => 'bp_example_format_notifications',
			'search_string'         => __( 'Search Examples...', 'buddypress' ),
			'global_tables'         => $global_tables
		);

		// Let BP_Component::setup_globals() do its work.
		parent::setup_globals( $globals );

		// If your component requires any other data in the $bp global, put it there now.
		$bp->{$this->id}->misc_data = '123';
	}

	/**
	 * Set up your component's navigation.
	 *
	 * The navigation elements created here are responsible for the main site navigation (eg
	 * Profile > Activity > Mentions), as well as the navigation in the BuddyBar. WP Admin Bar
	 * navigation is broken out into a separate method; see
	 * BP_Example_Component::setup_admin_bar().
	 *
	 * @global obj $bp
	 */
	function setup_nav() {
		// Add 'Example' to the main navigation
		$main_nav = array(
			'name' 		      => __( 'Example', 'bp-example' ),
			'slug' 		      => bp_get_example_slug(),
			'position' 	      => 80,
			'screen_function'     => 'bp_example_screen_one',
			'default_subnav_slug' => 'screen-one'
		);

		$example_link = trailingslashit( bp_loggedin_user_domain() . bp_get_example_slug() );

		// Add a few subnav items under the main Example tab
		$sub_nav[] = array(
			'name'            =>  __( 'Screen One', 'bp-example' ),
			'slug'            => 'screen-one',
			'parent_url'      => $example_link,
			'parent_slug'     => bp_get_example_slug(),
			'screen_function' => 'bp_example_screen_one',
			'position'        => 10
		);

		// Add the subnav items to the friends nav item
		$sub_nav[] = array(
			'name'            =>  __( 'Screen Two', 'bp-example' ),
			'slug'            => 'screen-two',
			'parent_url'      => $example_link,
			'parent_slug'     => bp_get_example_slug(),
			'screen_function' => 'bp_example_screen_two',
			'position'        => 20
		);

		parent::setup_nav( $main_nav, $sub_nav );

		// If your component needs additional navigation menus that are not handled by
		// BP_Component::setup_nav(), you can register them manually here. For example,
		// if your component needs a subsection under a user's Settings menu, add
		// it like this. See bp_example_screen_settings_menu() for more info
		bp_core_new_subnav_item( array(
			'name' 		  => __( 'Example', 'bp-example' ),
			'slug' 		  => 'example-admin',
			'parent_slug'     => bp_get_settings_slug(),
			'parent_url' 	  => trailingslashit( bp_loggedin_user_domain() . bp_get_settings_slug() ),
			'screen_function' => 'bp_example_screen_settings_menu',
			'position' 	  => 40,
			'user_has_access' => bp_is_my_profile() // Only the logged in user can access this on his/her profile
		) );
	}

	/**
	 * If your component needs to store data, it is highly recommended that you use WordPress
	 * custom post types for that data, instead of creating custom database tables.
	 *
	 * In the future, BuddyPress will have its own bp_register_post_types hook. For the moment,
	 * hook to init. See BP_Example_Component::__construct().
	 *
	 * @package BuddyPress_Skeleton_Component
	 * @since 1.6
	 * @see http://codex.wordpress.org/Function_Reference/register_post_type
	 */
	function register_post_types() {
		// Set up some labels for the post type
		$labels = array(
			'name'	   => __( 'High Fives', 'bp-example' ),
			'singular' => __( 'High Five', 'bp-example' )
		);

		// Set up the argument array for register_post_type()
		$args = array(
			'label'	   => __( 'High Fives', 'bp-example' ),
			'labels'   => $labels,
			'public'   => false,
			'show_ui'  => true,
			'supports' => array( 'title' )
		);

		// Register the post type.
		// Here we are using $this->id ('example') as the name of the post type. You may
		// choose to use a different name for the post type; if you register more than one,
		// you will have to declare more names.
		register_post_type( $this->id, $args );

		parent::register_post_types();
	}

	function register_taxonomies() {

	}

}

/**
 * Loads your component into the $bp global
 *
 * This function loads your component into the $bp global. By hooking to bp_loaded, we ensure that
 * BP_Example_Component is loaded after BuddyPress's core components. This is a good thing because
 * it gives us access to those components' functions and data, should our component interact with
 * them.
 *
 * Keep in mind that, when this function is launched, your component has only started its setup
 * routine. Using print_r( $bp->example ) or var_dump( $bp->example ) at the end of this function
 * will therefore only give you a partial picture of your component. If you need to dump the content
 * of your component for troubleshooting, try doing it at bp_init, ie
 *   function bp_example_var_dump() {
 *   	  global $bp;
 *	  var_dump( $bp->example );
 *   }
 *   add_action( 'bp_init', 'bp_example_var_dump' );
 * It goes without saying that you should not do this on a production site!
 *
 * @package BuddyPress_Skeleton_Component
 * @since 1.6
 */
function bp_example_load_core_component() {
	global $bp;

	$bp->example = new BP_Example_Component;
}
add_action( 'bp_loaded', 'bp_example_load_core_component' );


?>