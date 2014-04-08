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
 * bp_example_register_settings()
 * 
 * If your plugin has few options, it can be a good idea to add a new section to BuddyPress settings
 * @see http://codex.buddypress.org/plugindev/taking-benefits-from-buddypress-settings-to-add-your-plugins-options/
 */
function bp_example_register_settings() {

	$plugin_options = array(
		array(
			'option_name'       => 'example-setting-one',
			'option_title'      => __( 'Option One', 'bp-example' ),
			'display_function'  => 'bp_example_setting_one_field_callback',
			'settings_section'  => 'bp_main', // this one will be in BuddyPress main section
			'validate_function' => 'bp_example_validate_setting',
		),
		array(
			'option_name'       => 'example-setting-two',
			'option_title'      => __( 'Option Two', 'bp-example' ),
			'display_function'  => 'bp_example_setting_two_field_callback',
			'settings_section'  => 'bp_xprofile', // this one will be in xProfile section
			'validate_function' => 'bp_example_validate_setting',
		),	
	);

	foreach ( $plugin_options as $option ) {
		add_settings_field(
			/* the option name you want to use for your plugin */
			$option['option_name'],

	        /* The title for your setting */
	        $option['option_title'],

	        /* Display function */
	        $option['display_function'],

	        /* BuddyPress settings */
	        'buddypress',

	        /* BuddyPress setting section
	        Here you are adding a field to 'bp_main' section.
	        As shown on the image, other available sections are :
	        - if xprofile component is active : 'bp_xprofile',
	        - if groups component is active : 'bp_groups',
	        - if legacy forums component is active : 'bp_forums',
	        - if activity component is active : 'bp_activity'
	        */
	        $option['settings_section']
	    );

	    /* This is where you add your setting to BuddyPress ones */
	    register_setting(
	        /* BuddyPress settings */
	        'buddypress',
	 
	        /* the option name you want to use for your plugin */
	        $option['option_name'],
	 
	        /* the validatation function you use before saving your option to the database */
	        $option['validate_function']
	    );
	}
}
 
add_action( 'bp_register_admin_settings', 'bp_example_register_settings' );


/**
 * bp_example_setting_one_field_callback()
 * 
 * This is the display function for option one
 */
function bp_example_setting_one_field_callback() {
    /* if you use bp_get_option(), then you are sure to get the option for the blog BuddyPress is activated on */
    $plugin_option_value = bp_get_option( 'example-setting-one' );
 
    ?>
    <input id="example-setting-one" name="example-setting-one" type="text" value="<?php echo esc_attr( $plugin_option_value ); ?>" />
    <?php
}

/**
 * bp_example_setting_two_field_callback()
 * 
 * This is the display function for option two
 */
function bp_example_setting_two_field_callback() {
    /* if you use bp_get_option(), then you are sure to get the option for the blog BuddyPress is activated on */
    $plugin_option_value = bp_get_option( 'example-setting-two' );
 
    ?>
    <input id="example-setting-two" name="example-setting-two" type="text" value="<?php echo esc_attr( $plugin_option_value ); ?>" />
    <?php
}
 
/**
 * bp_example_validate_setting()
 * 
 * This is validation function for your options
 */
function bp_example_validate_setting( $option = '' ) {
    /* you could directly use 'sanitize_text_field' as your the 3rd argument of the register_setting() function in this case..
       For the purpose of this example, this specific function illustrates a custom validation function */
    return sanitize_text_field( $option );
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