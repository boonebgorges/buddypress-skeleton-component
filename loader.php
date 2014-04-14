<?php
/*
Plugin Name: BuddyPress Skeleton Component
Plugin URI: http://example.org/my/awesome/bp/component
Description: This BuddyPress component is the greatest thing since sliced bread.
Version: 1.7.0
Revision Date: MARC 30, 2014
Requires at least: What WP version, what BuddyPress version? ( Example: WP 3.2.1, BuddyPress 1.5 )
Tested up to: What WP version, what BuddyPress version?
License: (Example: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html)
Author: Dr. Jan Itor
Author URI: http://example.org/some/cool/developer
*/

/*************************************************************************************************************
 --- SKELETON COMPONENT V1.6.2 ---

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

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Main Skeleton Class.
 *
 * @since BuddyPress (1.7)
 */
class Skeleton {
	/**
	 * Instance of this class.
	 *
	 * @package BuddyPress Skeleton Component
	 * @since    1.7.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Required BuddyPress version for your plugin.
	 * 
	 * It can be interesting to check for user's current
	 * BuddyPress versions to prevent your plugin to temporarly
	 * break the website of the user. Especially when you are
	 * using functions that are not available in previous versions
	 * of BuddyPress.
	 *
	 * @package BuddyPress Skeleton Component
	 * @since    1.7.0
	 *
	 * @var      string
	 */
	public static $required_bp_version = '1.9';

	/**
	 * BuddyPress config.
	 * 
	 * It can be interesting to check for user's current
	 * BuddyPress config to be sure your plugin is actiated
	 * the same way.
	 *
	 * @package BuddyPress Skeleton Component
	 * @since    1.7.0
	 *
	 * @var      array
	 */
	public static $bp_config = array();

	/**
	 * Initialize the plugin
	 * 
	 * @package BuddyPress Skeleton Component
	 * @since 1.7.0
	 */
	private function __construct() {
		// First you will set your plugin's globals
		$this->setup_globals();
		// Then include the needed files
		$this->includes();
		// Then hook to BuddyPress actions & filters
		$this->setup_hooks();
	}

	/**
	 * Return an instance of this class.
	 *
	 * @package BuddyPress Skeleton Component
	 * @since 1.7.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function start() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Sets some globals for the plugin
	 * 
	 * @package BuddyPress Skeleton Component
	 * @since 1.7.0
	 */
	private function setup_globals() {

		/** Versions ********************************************/

		// Define a global that will hold the current version number of the component
		// This can be useful if you need to run update scripts or do compatibility checks in the future
		$this->version       = '1.7.0';

		/* Define a global that will hold the database version number that can be used for upgrading the DB
		 *
		 * NOTE: When table defintions change and you need to upgrade,
		 * make sure that you increment this global so that it runs the install function again.
		 *
		 * Also, if you have errors when testing the component for the first time, make sure that you check to
		 * see if the table(s) got created. If not, you'll most likely need to increment this global as
		 * buddypress()->extend->skeleton->db_version was written to the wp_usermeta table and the install function will not be
		 * triggered again unless you increment the version to a number higher than stored in the meta data.
		 */
		$this->db_version    = '1';
		
		// Define a global that can be checked to see if the component is installed or not.
		$this->is_installed  = '1';

		// Define a global to get the textdomain of your plugin.
		$this->domain        = 'bp-example';

		/** Paths ***********************************************/

		$this->file          = __FILE__;
		$this->basename      = plugin_basename( $this->file );

		// Define a global that we can use to construct file paths throughout the component
		$this->plugin_dir    = plugin_dir_path( $this->file );

		// Define a global that we can use to construct file paths starting from the includes directory
		$this->includes_dir  = trailingslashit( $this->plugin_dir . 'includes' );

		// Define a global that we can use to construct file paths starting from the includes directory
		$this->lang_dir      = trailingslashit( $this->plugin_dir . 'languages' );


		$this->plugin_url    = plugin_dir_url( $this->file );
		$this->includes_url  = trailingslashit( $this->plugin_url . 'includes' );

		// Define a global that we can use to construct url to the javascript scripts needed by the component
		$this->plugin_js     = trailingslashit( $this->includes_url . 'js' );

		// Define a global that we can use to construct url to the css needed by the component
		$this->plugin_css    = trailingslashit( $this->includes_url . 'css' );

		// Utility
		$this->debug = defined( 'BP_SKELETON_DEBUG' ) && BP_SKELETON_DEBUG ? true : false;
		$this->trace = array();
	}

	/**
	 * Include the component's loader.
	 *
	 * @package BuddyPress Skeleton Component
	 * @since 1.7.0
	 */
	private function includes() {
		if ( self::bail() )
			return;

		require( $this->includes_dir . 'bp-example-loader.php' );
	}

	/**
	 * Sets the key hooks to add an action or a filter to
	 * 
	 * @package BuddyPress Skeleton Component
	 * @since 1.7.0
	 */
	private function setup_hooks() {

		if ( ! self::bail() ) {
			// Load the component
			add_action( 'bp_loaded', 'bp_example_load_core_component' );

			// Filter to make the BP Default theme be available
			//add_filter( 'bp_do_register_theme_directory', '__return_true' );

			// Enqueue the needed script and css files
			add_action( 'bp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// loads the languages..
			add_action( 'bp_init', array( $this, 'load_textdomain' ), 6 );

			// It can be helpfull to easily see if the component is behaving the right
			// way while developing it.
			if ( $this->debug ) {

				if ( ! defined( 'WP_DEBUG' ) && ! WP_DEBUG )
					define( 'WP_DEBUG', true );

				// Infos will be displayed in footer
				add_action( 'wp_footer', array( $this, 'debug' ) );
			}
		} else {
			// Display a warning message in network admin or admin
			add_action( self::$bp_config['network_active'] ? 'network_admin_notices' : 'admin_notices', array( $this, 'warning' ) );
		}
		
	}

	/**
	 * Display a warning message to admin
	 * 
	 * @package BuddyPress Skeleton Component
	 * @since 1.7.0
	 */
	public function warning() {
		$warnings = array();

		if( ! self::version_check() ) {
			$warnings[] = sprintf( __( 'BP Example requires at least version %s of BuddyPress.', 'bp-example' ), self::$required_bp_version );
		}

		if ( ! empty( self::$bp_config ) ) {
			$config = self::$bp_config;
		} else {
			$config = self::config_check();
		}
		
		if ( ! bp_core_do_network_admin() && ! $config['blog_status'] ) {
			$warnings[] = __( 'BP Example requires to be activated on the blog where BuddyPress is activated.', 'bp-example' );
		}

		if ( bp_core_do_network_admin() && ! $config['network_status'] ) {
			$warnings[] = __( 'BP Examples and BuddyPress need to share the same network configuration.', 'bp-example' );
		}

		if ( ! empty( $warnings ) ) :
		?>
		<div id="message" class="error">
			<?php foreach ( $warnings as $warning ) : ?>
				<p><?php echo esc_html( $warning ) ; ?></p>
			<?php endforeach ; ?>
		</div>
		<?php
		endif;
	}

	/**
	 * Enqueue scripts if your component is loaded
	 * 
	 * @package BuddyPress Skeleton Component
	 * @since 1.7.0
	 */
	public function enqueue_scripts() {
		if ( ! bp_is_current_component( 'example' ) )
			return;

		wp_enqueue_script( 'bp-example-js', $this->includes_url . 'js/general.js', array( 'jquery' ), $this->version, true );
	}

	/** Utilities *****************************************************************************/

	/**
	 * Debug utility
	 * 
	 * Define BP_SKELETON_DEBUG to true to use it.
	 * 
	 * @package BuddyPress Skeleton Component
	 * @since 1.7.0
	 */
	public function debug() {
		$this->trace['main_class'] = $this;
		?>
		<div id="buddypress-skeleton-component-debug-tool">
			<pre><?php var_dump( $this->trace ); ?></pre>
		</div>
		<?php
	}

	/**
	 * BuddyPress can be (most frequently) :
	 * - activated on a regular config (single blog)
	 * - network activated on a multisite config
	 * - not activated on the network but on a specific blog.
	 * 
	 * As a BuddyPress plugin, it can be interesting to check
	 * BuddyPress config and guide the user to apply it to your
	 * plugin.
	 * 
	 * Using static methods can help you benefit from them
	 * while running the activation and deactivation process
	 */

	/**
	 * Checks BuddyPress version
	 * 
	 * @package BuddyPress Skeleton Component
	 * @since 1.7.0
	 */
	public static function version_check() {
		// taking no risk
		if ( ! defined( 'BP_VERSION' ) )
			return false;

		return version_compare( BP_VERSION, self::$required_bp_version, '>=' );
	}

	/**
	 * Checks if your plugin's config is similar to BuddyPress
	 * 
	 * @package BuddyPress Skeleton Component
	 * @since 1.7.0
	 */
	public static function config_check() {
		/**
		 * blog_status    : true if your plugin is activated on the same blog
		 * network_active : true when your plugin is activated on the network
		 * network_status : BuddyPress & your plugin share the same network status
		 */
		self::$bp_config = array(
			'blog_status'    => false, 
			'network_active' => false, 
			'network_status' => true 
		);

		if ( get_current_blog_id() == bp_get_root_blog_id() ) {
			self::$bp_config['blog_status'] = true;
		}
		
		$network_plugins = get_site_option( 'active_sitewide_plugins', array() );

		// No Network plugins
		if ( empty( $network_plugins ) )
			return self::$bp_config;

		$plugin_basename = plugin_basename( __FILE__ );

		// Looking for BuddyPress and your plugin
		$check = array( buddypress()->basename, $plugin_basename );

		// Are they active on the network ?
		$network_active = array_diff( $check, array_keys( $network_plugins ) );
		
		// If result is 1, your plugin is network activated
		// and not BuddyPress or vice & versa. Config is not ok
		if ( count( $network_active ) == 1 )
			self::$bp_config['network_status'] = false;
		
		// We need to know if the plugin is network activated to choose the right
		// notice ( admin or network_admin ) to display the warning message.
		self::$bp_config['network_active'] = isset( $network_plugins[ $plugin_basename ] );

		return self::$bp_config;
	}

	/**
	 * Bail if BuddyPress config is different than this plugin
	 *
	 * @package BuddyPress Skeleton Component
	 * @since 1.7.0
	 */
	public static function bail() {
		$retval = false;

		$config = self::config_check();

		if ( ! self::version_check() || ! $config['blog_status'] || ! $config['network_status'] )
			$retval = true;

		return $retval;
	}

	/**
	 * Loads the translation files
	 * 
	 * You should try hard to support translation in your component. It's actually very easy.
 	 * Make sure you wrap any rendered text in __() or _e() and it will then be translatable.
 	 *
 	 * You must also provide a text domain, so translation files know which bits of text to translate.
 	 * Throughout this example the text domain used is 'bp-example', you can use whatever you want.
 	 * Put the text domain as the second parameter:
 	 *
 	 * __( 'This text will be translatable', 'bp-example' ); // Returns the first parameter value
 	 * _e( 'This text will be translatable', 'bp-example' ); // Echos the first parameter value
	 *
	 * @package BuddyPress Skeleton Component
	 * @since 1.7.0
	 * 
	 * @uses get_locale() to get the language of WordPress config
	 * @uses load_texdomain() to load the translation if any is available for the language
	 */
	public function load_textdomain() {
		// Traditional WordPress plugin locale filter
		$locale        = apply_filters( 'plugin_locale', get_locale(), $this->domain );
		$mofile        = sprintf( '%1$s-%2$s.mo', $this->domain, $locale );

		// Setup paths to current locale file
		$mofile_local  = $this->lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/buddypress-skeleton-component/' . $mofile;

		// Look in global /wp-content/languages/buddyplug folder
		load_textdomain( $this->domain, $mofile_global );

		// Look in local /wp-content/plugins/buddyplug/languages/ folder
		load_textdomain( $this->domain, $mofile_local );
	}
}

// BuddyPress is loaded and initialized, let's start !
function buddypress_skeleton_component() {
	$bp = buddypress();

	/**
	 * You could simply return Skeleton::start()
	 * and access to your global using 
	 * buddypress_skeleton_component()->{the_neede_global}
	 */
	if ( empty( $bp->extend ) ) {
		$bp->extend = new StdClass();
	}
	/**
	 * Setup your plugin globals
	 * 
	 * Throughout your plugin, you'll be able to 
	 * access to your globals using :
	 * buddypress()->extend->skeleton->{the_needed_global}
	 * 
	 * You will also be able to use this name space to temporarly
	 * store vars in the globals.
	 */
	$bp->extend->skeleton = Skeleton::start();
}
add_action( 'bp_include', 'buddypress_skeleton_component' );


/* Put setup procedures to be run when the plugin is activated in the following function */
/*function bp_example_activate() {
	if ( ! Skeleton::bail() )
		// process activation routine
}
register_activation_hook( __FILE__, 'bp_example_activate' );

/* On deacativation, clean up anything your component has added. */
/*function bp_example_deactivate() {
	/* You might want to delete any options or tables that your component created. */
/*}
register_deactivation_hook( __FILE__, 'bp_example_deactivate' );*/
