<?php
	$title = 'Create new account';

	require 'inc_header.php';
?>

	<?php
		$attempted_account_creation = false;

		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			$attempted_account_creation = true;
			$account_creation_error = create_account();
		}
	?>

	<?php if ( $attempted_account_creation ): ?>
		<div class="span8 offset2">
			<?php if ( isset($account_creation_error) ): ?>
				<h2>Account creation error</h2>
				<p><?= $account_creation_error ?></p>
				<p><a href="<?=BASE_URL ?>create-account.php">Return to account creation page</a></p>

			<?php else: ?>
				<?php if ( !does_user_have_paypal_id($_SESSION['email']) ): ?>
					<script>
						alert( "Email confirmation step: <?=STORE_NAME ?> would send a verification email which would include a link to this page." );
					</script>
				<?php endif; ?>

				<h2>Account created</h2>
				<p>Thank you for creating your <?=STORE_NAME ?> account!</p>
				<ul>
					<li><a href="<?=BASE_URL ?>index.php">Go to Storefront</a></li>
					<li><a href="<?=BASE_URL ?>cart.php">Go to Shopping Cart</a></li>
				</ul>

			<?php endif; ?>
		</div>

		<?php //unset( $_SESSION['user'] ); ?>

	<?php else: ?>
		<?php $user = @$_SESSION['user']; ?>

		<div class="alert alert-pop">
			<p>Create your account faster with <strong>PayPal</strong>!</p>
			<p id="pp_login_button_linkage"></p>
			<script>
			paypal.use(["login"], function(login) {
				login.render ({
				"appid": "<?=PP_SELLER_APP_ID ?>",
				"returnurl": "<?=BASE_URL ?>return.php",
					"scopes": "profile email address phone https://uri.paypal.com/services/paypalattributes https://uri.paypal.com/services/expresscheckout",
					"containerid": "pp_login_button_linkage",
					"authend": "sandbox"
				});
			});
			</script>
		</div>

		<form id="create_account" class="row-fluid form-standard" method="POST">
			<input type="hidden" name="nonce" value="<?=$_SESSION['nonce'] ?>" />
			<?php if ( isset( $user['payer_id'] ) && !isset( $_GET['edit'] ) ): ?>
				<div class="span8 offset2">
					<h2>Link PayPal account with <?=STORE_NAME ?></h2>
					<p>Would you like to create an account with us?</p>
					<p>Just confirm the following information so we can provide a better experience.</p>
				</div>
				<fieldset class="span4 offset4">
					<table class="table table-hover">
						<tr>
							<th>first name</th>
							<td>
								<?=$user['given_name'] ?><input type="hidden" name="given_name" value="<?=$user['given_name'] ?>">
							</td>
						</tr>
						<tr>
							<th>last name</th>
							<td>
								<?=$user['family_name'] ?><input type="hidden" name="family_name" value="<?=$user['family_name'] ?>">
							</td>
						</tr>
						<tr>
							<th>email</th>
							<td>
								<?=$user['email'] ?><input type="hidden" name="email" value="<?=$user['email'] ?>">
							</td>
						</tr>
						<tr>
							<th>phone</th>
							<td><?=$user['phone_number'] ?><input type="hidden" name="phone_number" value="<?=$user['phone_number'] ?>"></td>
						</tr>
						<tr>
							<th>address</th>
							<td>
								<?=$user['street_address'] ?><br>
								<?=$user['locality'] ?>, <?=$user['region'] ?> <?=$user['postal_code'] ?><br>
								<?=$user['country'] ?>
								<input type="hidden" name="street_address" value="<?=$user['street_address'] ?>">
								<input type="hidden" name="locality" value="<?=$user['locality'] ?>">
								<input type="hidden" name="region" value="<?=$user['region'] ?>">
								<input type="hidden" name="postal_code" value="<?=$user['postal_code'] ?>">
								<input type="hidden" name="country" value="<?=$user['country'] ?>">
							</td>
						</tr>
						<tr class="summary-row">
							<th>&nbsp;</th>
							<td>
								<a href="create-account.php?edit=true" class="btn btn-mini">edit information</a>
							</td>
						</tr>
					</table>
					<p><input type="checkbox" name="newsletter" id="newsletter">
						<label for="newsletter">Receive our <?=STORE_NAME ?> newsletter.</label></p>
					<p><button type="submit" class="btn btn-primary">Link accounts</button>
						<a href="#" class="cancel">cancel</a></p>
				</fieldset>

			<?php else: ?>
				<fieldset class="span4 create-identity">
					<p><label for="given_name">First Name</label>
						<input type="text" name="given_name" id="given_name" value="<?=@$user['given_name'] ?>" required></p>
					<p><label for="family_name">Last Name</label>
						<input type="text" name="family_name" id="family_name" value="<?=@$user['family_name'] ?>"></p>
					<p><label for="email">Email</label>
						<input type="email" name="email" id="email" value="<?=@$user['email'] ?>" required></p>
					<p><label type="tel" for="phone_number">Phone Number</label>
						<input type="text" name="phone_number" id="phone_number" value="<?=@$user['phone_number'] ?>"></p>
				</fieldset>
				<fieldset class="span4 create-address">
					<p><label for="street_address">Street Address</label>
						<input type="text" name="street_address"id="street_address" value="<?=@$user['street_address'] ?>"></p>
					<p><label for="locality">City</label>
						<input type="text" name="locality" id="locality" value="<?=@$user['locality'] ?>"></p>
					<p><label for="region">State</label>
						<select name="region">
							<option value="">-- State --</option>
							<?php foreach ( $regionArray as $abbr => $state ): ?>
								<option value="<?=$abbr ?>" <?php if ( $abbr == @$user['region'] ) { echo 'selected'; } ?>><?=$state ?></option>
							<?php endforeach; ?>
						</select></p>
					<p><label for="postal_code">Zip Code</label>
						<input type="text" name="postal_code"id="postal_code" value="<?=@$user['postal_code'] ?>"></p>
					<p><label for="country">Country</label>
						<select name="country">
							<option value="">-- Country --</option>
							<?php foreach ( $countryArray as $abbr => $country ): ?>
								<option value="<?=$abbr ?>" <?php if ( $abbr == @$user['country'] ) { echo 'selected'; } ?>><?=$country ?></option>
							<?php endforeach; ?>
						</select></p>
				</fieldset>
				<fieldset class="span4 create-password">
					<?php if ( !isset( $user['email']) ): // don't show password fields for PayPal linking ?>
					<p><label for="password">Password</label>
						<input type="password" name="password" id="password" required></p>
					<p><label for="confirmPassword">Confirm Password</label>
						<input type="password" name="confirmPassword" id="confirmPassword" required></p>
					<?php endif; ?>
					<p><input type="checkbox" name="newsletter" id="newsletter">
						<label for="newsletter">Receive our <?=STORE_NAME ?> newsletter.</label></p>
					<hr>

					<?php if ( isset( $user['email'] ) ): ?>
						<p><button type="submit" class="btn btn-primary">Link accounts</button>
							<a href="#" class="cancel">cancel</a></p>

					<?php else: ?>
						<p><button type="submit" class="btn btn-primary">Create account</button>
							<a href="#" class="cancel">cancel</a></p>

					<?php endif; ?>
				</fieldset>

			<?php endif; ?>

			<input type="hidden" name="language" value="en_US">
			<input type="hidden" name="payer_id" value="<?=@$user['payer_id'] ?>">
			<input type="hidden" name="access_token" value="<?=@$user['access_token'] ?>">
		</form>

	<?php endif; ?>

<?php require 'inc_footer.php'; ?>
