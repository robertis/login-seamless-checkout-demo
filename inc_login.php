<?php logout_or_login(); ?>
<?php $cartSlug = render_cart_count(); ?>

	<?php if ( is_logged_in() ): ?>
		<p class="login-out">Welcome, <?php echo get_username() ?> <a href="?logout=true&nonce=<?=$_SESSION['nonce'] ?>" class="tiny">(log out)</a> <?=$cartSlug ?></p>

	<?php else: ?>
		<p class="login-out"><a href="#myModal" data-toggle="modal" class="login-trigger">Log in <span class="tiny">(or create account)</span></a> <?=$cartSlug ?></p>

		<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-body login-choices">
				<div class="store-login">
					<form method="POST">
						<input type="hidden" name="nonce" value ="<?=$_SESSION['nonce'] ?>" />
						<legend>Log in with <?=STORE_NAME ?></legend>
						<p><label for="email">Email:</label>
							<input type="email" name="email" id="email" required></p>
						<p><label for="password">Password:</label>
							<input type="password" name="password" id="password" required></p>
						<p><button type="submit" class="btn">Log In</button></p>
					</form>
					<p class="tiny"><a href="<?=BASE_URL ?>create-account.php">Create a <?=STORE_NAME ?> account</a></p>
				</div>

				<div class="or">or</div>

				<div class="paypal-login">
					<p id="pp_login_button"></p>
					<p class="tiny"><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_registration-run">Create a PayPal account</a></p>
				</div>
			</div>

			<div class="modal-footer">
				<a data-dismiss="modal" aria-hidden="true" class="close-modal">Close</a>
			</div>
		</div>

		<script src="<?=PPI_BUTTON_JS ?>"></script>
		<script>
		paypal.use(["login"], function(login) {
			login.render ({
				"appid": "<?=PP_SELLER_APP_ID ?>",
				"returnurl": "<?=BASE_URL ?>return.php",
				"scopes": "profile email address phone https://uri.paypal.com/services/paypalattributes https://uri.paypal.com/services/expresscheckout",
				"containerid": "pp_login_button",
				"authend": "sandbox"
			});
		});
		</script>
	<?php endif; ?>
