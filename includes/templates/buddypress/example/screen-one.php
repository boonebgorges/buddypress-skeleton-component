<?php do_action( 'bp_before_member_' . bp_current_action() . '_content' ); ?>

<h4><?php _e( 'Welcome to Screen One', 'bp-example' ); ?></h4>
<p><?php printf( __( 'Send %1$s a <a href="%2$s" title="Send high-five!">high-five!</a>', 'bp-example' ), bp_get_displayed_user_fullname(), wp_nonce_url( bp_displayed_user_domain() . bp_current_component() . '/screen-one/send-h5/', 'bp_example_send_high_five' ) ); ?></p>

<?php if ( $high_fives = bp_example_get_highfives_for_user( bp_displayed_user_id() ) ) : ?>
	<h4><?php _e( 'Received High Fives!', 'bp-example' ); ?></h4>

	<table id="high-fives">
		<?php foreach ( $high_fives as $user_id ) : ?>
		<tr>
			<td width="1%">
			<?php
			echo bp_core_fetch_avatar(
				array(
					'item_id' => $user_id,
					'width'   => 25,
					'height'  => 25,
				)
			);
			?>
							</td>
			<td>&nbsp; <?php echo bp_core_get_userlink( $user_id ); ?></td>
		</tr>
		<?php endforeach; ?>
	</table>
<?php endif; ?>

<?php do_action( 'bp_after_member_' . bp_current_action() . '_content' ); ?>
