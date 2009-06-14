<?php

/**
 * NOTE: You should always use the wp_enqueue_script() and wp_enqueue_style() functions to include
 * javascript and css files.
 */

/**
 * bp_example_add_js()
 *
 * This function will enqueue the components javascript file, so that you can make
 * use of any javascript you bundle with your component within your interface screens.
 */
function bp_example_add_js() {
	global $bp;

	if ( $bp->current_component == $bp->example->slug )
		wp_enqueue_script( 'bp-example-js', WP_PLUGIN_URL . '/bp-example/js/general.js' );
}
add_action( 'template_redirect', 'bp_example_add_js', 1 );

/**
 * bp_example_add_structure_css()
 *
 * This function will enqueue structural CSS so that your component will retain interface
 * structure regardless of the theme currently in use. See the notes in the CSS file for more info.
 */
function bp_example_add_structure_css() {
	/* Enqueue the structure CSS file to give basic positional formatting for your component reglardless of the theme. */
	wp_enqueue_style( 'bp-example-structure', WP_PLUGIN_URL . '/bp-example/css/structure.css' );	
}
add_action( 'bp_styles', 'bp_example_add_structure_css' );

?>