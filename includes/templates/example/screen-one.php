<?php get_header() ?>

	<div id="content">
		<div class="padder">

			<div id="item-header">
				<?php locate_template( array( 'members/single/member-header.php' ), true ) ?>
			</div>

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav">
					<ul>
						<?php bp_get_displayed_user_nav() ?>
					</ul>
				</div>
			</div>

			<div id="item-body">

				<div class="item-list-tabs no-ajax" id="subnav">
					<ul>
						<?php bp_get_options_nav() ?>
					</ul>
				</div>

				<h4><?php _e( 'Welcome to Screen One', 'bp-example' ) ?></h4>
				<p><?php printf( __( 'Send %s a <a href="%s" title="Send high-five!">high-five!</a>', 'bp-example' ), bp_get_displayed_user_fullname(), wp_nonce_url( bp_displayed_user_domain() . bp_current_component() . '/screen-one/send-h5/', 'bp_example_send_high_five' ) ) ?></p>

				<?php if ( $high_fives = bp_example_get_highfives_for_user( bp_displayed_user_id() ) ) : ?>
					<h4><?php _e( 'Received High Fives!', 'bp-example' ) ?></h4>

					<table id="high-fives">
						<?php foreach ( $high_fives as $user_id ) : ?>
						<tr>
							<td width="1%"><?php echo bp_core_fetch_avatar( array( 'item_id' => $user_id, 'width' => 25, 'height' => 25 ) ) ?></td>
							<td>&nbsp; <?php echo bp_core_get_userlink( $user_id ) ?></td>
			 			</tr>
						<?php endforeach; ?>
					</table>
				<?php endif; ?>

			</div><!-- #item-body -->

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>