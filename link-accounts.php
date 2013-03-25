<?php
	$title = 'Link your accounts';

	require 'inc_header.php';
?>

	<?php $linked_accounts = false; ?>

	<div class="span8 offset2">

	<?php if ( $_SERVER['REQUEST_METHOD'] === 'POST' ): ?>
		<?php
			$user_data = get_user_data( $_POST['email'] );

			if ( $_POST['password'] == $user_data['password'] ) {
				add_paypal_id_to_user_account( $_POST['email'], $_POST['payer_id'] );
				set_user_logged_in( $user_data['given_name'], $user_data['email'] );
				$linked_accounts = true;
			}
		?>

		<?php if ( $linked_accounts ): ?>
			<div id="success">
				<h2>Thank you for linking your account!</h2>
				<p>Go to:</p>
				<ul>
					<li><a href="<?=BASE_URL ?>">Home page</a></li>
					<li><a href="<?=BASE_URL ?>cart.php">Shopping Cart</a></li>
				</ul>
			</div>

		<?php else: ?>
			<div id="failure">
				<h2>Failed to link accounts</h2>
				<p>Your password is incorrect.</p>
			</div>

		<?php endif; ?>

	<?php else: ?>
		<p class="plea">Link your <strong>PayPal</strong> and <strong><?=STORE_NAME ?></strong> accounts to streamline your shopping experience.</p>
		<!-- <p class="tiny">(so we don't get confused and think that you're two different people).</p> -->
		<form id="link_acct_form" class="form-standard" method="POST">
			<input type="hidden" name="payer_id" value="<?php echo $_GET['payer_id']; ?>">
			<fieldset id="linkAccountFields">
				<p><label for="email">Email</label>
					<input type="email" name="email" id="email" value="<?php echo $_GET['email'] ?>" required></p>
				<p><label for="password"><?=STORE_NAME ?> Password</label>
					<input type="password" name="password" id="password" required></p>
				<p><button type="submit" class="btn btn-primary">Link accounts</button>
					<a href="<?=BASE_URL ?>">Cancel</a></p>
			</fieldset>
		</form>

	<?php endif; ?>

	</div>

<?php require 'inc_footer.php'; ?>
