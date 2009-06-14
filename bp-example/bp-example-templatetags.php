<?php

/**
 * In this file you should define template tag functions that end users can add to their template files.
 * Each template tag function should echo the final data so that it will output the required information
 * just by calling the function name.
 */

/**
 * If you want to go a step further, you can create your own custom WordPress loop for your component.
 * By doing this you could output a number of items within a loop, just as you would output a number
 * of blog posts within a standard WordPress loop.
 *
 * The example template class below would allow you do the following in the template file:
 *
 * 	<?php if ( bp_get_example_has_items() ) : ?>
 *
 *		<?php while ( bp_get_example_items() ) : bp_get_example_the_item(); ?>
 *
 *			<p><?php bp_get_example_item_name() ?></p>
 *
 *		<?php endwhile; ?>
 *		
 *	<?php else : ?>
 *
 *		<p class="error">No items!</p>
 *		
 *	<?php endif; ?>
 *
 * Obviously, you'd want to be more specific than the word 'item'.
 *
 */

class BP_Example_Template {
	var $current_item = -1;
	var $item_count;
	var $items;
	var $item;
	
	var $in_the_loop;
	
	var $pag_page;
	var $pag_num;
	var $pag_links;
	
	function bp_example_template( $user_id, $per_page, $max ) {
		global $bp;
		
		if ( !$user_id )
			$user_id = $bp->displayed_user->id;
				
		/***
		 * If you want to make parameters that can be passed, then append a
		 * character or two to "page" like this: $_REQUEST['xpage']
		 * You can add more than a single letter.
		 *
		 * The "x" in "xpage" should be changed to something unique so as not to conflict with
		 * BuddyPress core components which use the unique characters "b", "g", "u", "w",
		 * "ac", "fr", "gr", "ml", "mr" with "page".
		 */
		
		$this->pag_page = isset( $_REQUEST['xpage'] ) ? intval( $_REQUEST['xpage'] ) : 1;
		$this->pag_num = isset( $_GET['num'] ) ? intval( $_GET['num'] ) : $per_page;
		$this->user_id = $user_id;
		
		/***
		 * Consider using WordPress Object Caching to limit your calls to the database.
		 * For more information and doc links, see the bottom of 'bp-example.php'
		 *
	 	 * Example:
	 	 *	
		 *		if ( !$this->items = wp_cache_get( 'bp_get_example_items_' . $user_id, 'bp' ) ) {
		 *			$this->items = bp_example_get_items_for_user( $user_id );
		 *			wp_cache_set( 'bp_get_example_items_' . $user_id, $this->items, 'bp' );
		 *		}
		 *
		 */
		
		// Item Requests
		/*
		if ( !$max || $max >= (int)$this->items['total'] )
			$this->total_item_count = (int)$this->items['total'];
		else
			$this->total_item_count = (int)$max;
		
		$this->items = $this->items['items'];
				
		if ( $max ) {
			if ( $max >= count($this->items) )
				$this->item_count = count($this->items);
			else
				$this->item_count = (int)$max;
		} else {
			$this->item_count = count($this->items);
		}
		*/
		
		/* Remember to change the "x" in "xpage" to match whatever character(s) you're using above */
		$this->pag_links = paginate_links( array(
			'base' => add_query_arg( 'xpage', '%#%' ),
			'format' => '',
			'total' => ceil( (int) $this->total_item_count / (int) $this->pag_num ),
			'current' => (int) $this->pag_page,
			'prev_text' => '&laquo;',
			'next_text' => '&raquo;',
			'mid_size' => 1			
		));
	}
	
	function has_items() {
		if ( $this->item_count )
			return true;
		
		return false;
	}
	
	function next_item() {
		$this->current_item++;
		$this->item = $this->items[$this->current_item];
		
		return $this->item;
	}
	
	function rewind_items() {
		$this->current_item = -1;
		if ( $this->item_count > 0 ) {
			$this->item = $this->items[0];
		}
	}
	
	function user_items() { 
		if ( $this->current_item + 1 < $this->item_count ) {
			return true;
		} elseif ( $this->current_item + 1 == $this->item_count ) {
			do_action('loop_end');
			// Do some cleaning up after the loop
			$this->rewind_items();
		}

		$this->in_the_loop = false;
		return false;
	}
	
	function the_item() {
		global $item, $bp;

		$this->in_the_loop = true;
		$this->item = $this->next_item();
				
		if ( 0 == $this->current_item ) // loop has just started
			do_action('loop_start');
	}
}

function bp_get_example_has_items( $args = '' ) {
	global $bp, $items_template;
	
	/***
	 * This function should accept arguments passes as a string, just the same
	 * way a 'query_posts()' call accepts parameters.
	 * At a minimum you should accept 'per_page' and 'max' parameters to determine
	 * the number of items to show per page, and the total number to return.
	 * 
	 * e.g. bp_get_example_has_items( 'per_page=10&max=50' );
	 */
	
	
	/***
	 * Set the defaults for the parameters you are accepting via the "bp_get_example_has_items()"
	 * function call 
	 */
	$defaults = array(
		'per_page' => 10,
		'max' => false,
		'type' => 'all'
	);

	/***
	 * This function will extract all the parameters passed in the string, and turn them into
	 * proper variables you can use in the code - $per_page, $max
	 */
	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$items_template = new BP_Example_Template( $per_page, $max, $type );
		
	return $items_template->has_items();
}

function bp_get_example_the_item() {
	global $items_template;
	return $items_template->the_item();
}

function bp_get_example_items() {
	global $items_template;
	return $items_template->user_items();
}

function bp_get_example_item_name() {
	global $items_template;
	
	echo ''; // Example: $items_template->item->name;
}

function bp_get_example_item_pagination() {
	global $items_template;
	
	echo $items_template->pag_links;
}

?>