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
		wp_enqueue_script( 'bp-example-js', plugins_url( '/bp-example/js/general.js' ) );
}
add_action( 'template_redirect', 'bp_example_add_js', 1 );

function bp_example_add_icon_css() { ?>
	<style type="text/css">
		li a#user-example, li a#my-example { background: url( <?php echo plugins_url( '/bp-example/images/heart_bullet.png' ) ?> ) no-repeat 88% 52%; padding: 0.55em 3em 0.55em 0 !important; margin-right: 0.85em !important; }
		li#afilter-example a { background: url( <?php echo plugins_url( '/bp-example/images/heart_bullet.png' ) ?> ) no-repeat; }
	</style>
<?php	
}
add_action( 'wp_footer', 'bp_example_add_icon_css' );

?>