<?php

/**
 *
 * @package BuddyPress_Skeleton_Component
 * @since 1.6
 */

?>

<?php do_action( 'bp_before_example_loop' ); ?>

<?php if ( bp_example_has_items( bp_ajax_querystring( 'example' ) ) ) : ?>
<?php // global $items_template; var_dump( $items_template ) ?>
	<div id="pag-top" class="pagination">

		<div class="pag-count" id="example-dir-count-top">

			<?php bp_example_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="example-dir-pag-top">

			<?php bp_example_item_pagination(); ?>

		</div>

	</div>

	<?php do_action( 'bp_before_directory_example_list' ); ?>

	<ul id="example-list" class="item-list" role="main">

	<?php while ( bp_example_has_items() ) : bp_example_the_item(); ?>

		<li>
			<div class="item-avatar">
				<?php bp_example_high_fiver_avatar( 'type=thumb&width=50&height=50' ); ?>
			</div>

			<div class="item">
				<div class="item-title"><?php bp_example_high_five_title() ?></div>

				<?php do_action( 'bp_directory_example_item' ); ?>

			</div>

			<div class="clear"></div>
		</li>

	<?php endwhile; ?>

	</ul>

	<?php do_action( 'bp_after_directory_example_list' ); ?>

	<div id="pag-bottom" class="pagination">

		<div class="pag-count" id="example-dir-count-bottom">

			<?php bp_example_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="example-dir-pag-bottom">

			<?php bp_example_item_pagination(); ?>

		</div>

	</div>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'There were no high-fives found.', 'buddypress' ); ?></p>
	</div>

<?php endif; ?>

<?php do_action( 'bp_after_example_loop' ); ?>
