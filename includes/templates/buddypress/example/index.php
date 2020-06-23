<?php

/**
 * BuddyPress - Example Directory
 *
 * @package BuddyPress_Skeleton_Component
 */

?>

<?php get_header( 'buddypress' ); ?>

	<?php do_action( 'bp_before_directory_example_page' ); ?>

	<div id="content">
		<div class="padder">

		<?php do_action( 'bp_before_directory_example' ); ?>

		<form action="" method="post" id="example-directory-form" class="dir-form">

			<h3><?php _e( 'High Fives Directory', 'bp-example' ); ?></h3>

			<?php do_action( 'bp_before_directory_example_content' ); ?>

			<?php do_action( 'template_notices' ); ?>

			<div class="item-list-tabs no-ajax" role="navigation">
				<ul>
					<li class="selected" id="groups-all"><a href="<?php echo trailingslashit( bp_get_root_domain() . '/' . bp_get_example_root_slug() ); ?>"><?php printf( __( 'All High Fives <span>%s</span>', 'buddypress' ), bp_example_get_total_high_five_count() ); ?></a></li>

					<?php do_action( 'bp_example_directory_example_filter' ); ?>

				</ul>
			</div><!-- .item-list-tabs -->

			<div id="example-dir-list" class="example dir-list">

				<?php bp_core_load_template( 'example/example-loop' ); ?>

			</div><!-- #examples-dir-list -->

			<?php do_action( 'bp_directory_example_content' ); ?>

			<?php wp_nonce_field( 'directory_example', '_wpnonce-example-filter' ); ?>

			<?php do_action( 'bp_after_directory_example_content' ); ?>

		</form><!-- #example-directory-form -->

		<?php do_action( 'bp_after_directory_example' ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php do_action( 'bp_after_directory_example_page' ); ?>

<?php get_sidebar( 'buddypress' ); ?>
<?php get_footer( 'buddypress' ); ?>

