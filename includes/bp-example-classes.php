<?php

/**
 * This function should include all classes and functions that access the database.
 * In most BuddyPress components the database access classes are treated like a model,
 * where each table has a class that can be used to create an object populated with a row
 * from the corresponding database table.
 *
 * By doing this you can easily save, update and delete records using the class, you're also
 * abstracting database access.
 */

class BP_Example_Highfive {
	var $id;
	var $high_fiver_id;
	var $recipient_id;
	var $date;

	/**
	 * bp_example_tablename()
	 *
	 * This is the constructor, it is auto run when the class is instantiated.
	 * It will either create a new empty object if no ID is set, or fill the object
	 * with a row from the table if an ID is provided.
	 */
	function __construct( $args = array() ) {
		// Set some defaults
		$defaults = array(
			'id'		=> 0,
			'high_fiver_id' => 0,
			'recipient_id'  => 0,
			'date' 		=> date( 'Y-m-d H:i:s' )
		);

		// Parse the defaults with the arguments passed
		$r = wp_parse_args( $args, $defaults );
		extract( $r );

		if ( $id ) {
			$this->id = $id;
			$this->populate( $this->id );
		} else {
			foreach( $r as $key => $value ) {
				$this->{$key} = $value;
			}
		}
	}

	/**
	 * populate()
	 *
	 * This method will populate the object with a row from the database, based on the
	 * ID passed to the constructor.
	 */
	function populate() {
		global $wpdb, $bp, $creds;

		if ( $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bp->example->table_name} WHERE id = %d", $this->id ) ) ) {
			$this->high_fiver_id = $row->high_fiver_id;
			$this->recipient_id  = $row->recipient_id;
			$this->date 	     = $row->date;
		}
	}

	/**
	 * save()
	 *
	 * This method will save an object to the database. It will dynamically switch between
	 * INSERT and UPDATE depending on whether or not the object already exists in the database.
	 */

	function save() {
		global $wpdb, $bp;

		/***
		 * In this save() method, you should add pre-save filters to all the values you are
		 * saving to the database. This helps with two things -
		 *
		 * 1. Blanket filtering of values by plugins (for example if a plugin wanted to
		 * force a specific value for all saves)
		 *
		 * 2. Security - attaching a wp_filter_kses() call to all filters, so you are not
		 * saving potentially dangerous values to the database.
		 *
		 * It's very important that for number 2 above, you add a call like this for each
		 * filter to 'bp-example-filters.php'
		 *
		 *   add_filter( 'example_data_fieldname1_before_save', 'wp_filter_kses' );
		 */

		$this->high_fiver_id = apply_filters( 'bp_example_data_high_fiver_id_before_save', $this->high_fiver_id, $this->id );
		$this->recipient_id  = apply_filters( 'bp_example_data_recipient_id_before_save', $this->recipient_id, $this->id );
		$this->date	     = apply_filters( 'bp_example_data_date_before_save', $this->date, $this->id );

		// Call a before save action here
		do_action( 'bp_example_data_before_save', $this );

		if ( $this->id ) {
			// Update
			$result = $wpdb->query( $wpdb->prepare(
					"UPDATE {$bp->example->table_name} SET
						high_fiver_id = %d,
						recipient_id = %d,
						date = %s
					WHERE id = %d",
						$this->high_fiver_id,
						$this->recipient_id,
						$this->date,
						$this->id
					) );
		} else {
			// Save
			$result = $wpdb->query( $wpdb->prepare(
					"INSERT INTO {$bp->example->table_name} (
						high_fiver_id,
						recipient_id,
						date
					) VALUES (
						%d, %d, %s
					)",
						$this->high_fiver_id,
						$this->recipient_id,
						$this->date
					) );
		}

		if ( !$result )
			return false;

		if ( !$this->id ) {
			$this->id = $wpdb->insert_id;
		}

		/* Add an after save action here */
		do_action( 'bp_example_data_after_save', $this );

		return $result;
	}

	/**
	 * delete()
	 *
	 * This method will delete the corresponding row for an object from the database.
	 */
	function delete() {
		global $wpdb, $bp;

		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->example->table_name} WHERE id = %d", $this->id ) );
	}

	/* Static Functions */

	/**
	 * Static functions can be used to bulk delete items in a table, or do something that
	 * doesn't necessarily warrant the instantiation of the class.
	 *
	 * Look at bp-core-classes.php for examples of mass delete.
	 */

	function delete_all() {

	}

	function delete_by_user_id() {

	}
}

?>