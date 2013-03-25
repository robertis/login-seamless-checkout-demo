<?php
	require_once 'inc_functions.php';

	// if we've chosen a payment type from traditional flow
	// OR set a preference for the streamlined flow in Admin
	// THEN get the PP Identity Access Token and redirect to PP Express Checkout
	if ( isset( $_POST['paymentType'] ) || ( isset( $_SESSION['fastFlow'] ) && $_SESSION['fastFlow'] === true ) ):

		if ( verify_nonce() ) {
			try {
				$amount = 0.0;
				$cartItems = $_SESSION['cartItems'];

				foreach ( $cartItems as $itm ) {
					$amount += $storeItems[$itm]['price'];
				}

				$params = array(
					'method'        => 'SetExpressCheckout',
					'itemamt'       => $amount,
					'amt'           => $amount,
					'currencycode'  => 'USD',
					'paymentaction' => 'Sale',
					'returnUrl'     => BASE_URL . 'confirm-purchase.php?result=success',
					'cancelUrl'     => BASE_URL . 'confirm-purchase.php?result=cancelled',
					'version'       => PP_VERSION,
					'user'          => PP_USER,
					'pwd'           => PP_PWD,
					'signature'     => PP_SIGNATURE,
					'IDENTITYACCESSTOKEN' => get_access_token()
					);

				$checkoutResponse = post_checkout( $params );
				$parsedCheckoutResponse = parse_url_encoded( $checkoutResponse );

				// NOTE: for header() to work properly, there must be NO whitespace, NO echo, etc. before this point
				header( 'Location:' . PP_CHECKOUT_URL . '&token=' . $parsedCheckoutResponse['TOKEN'] );

			} catch( Exception $e ) {
				echo $e->getMessage();
			}
		}
?>

	<?php else: // ...else set the checkout preferences to traditional and present the form: ?>
		<?php $_SESSION['fastFlow'] = false; ?>

		<?php
			$title = 'Checkout';

			require 'inc_header.php';
		?>

		<div class="span4 offset4">
			<form method="post">
				<h3>Payment method</h3>
				<label class="radio">
					<input type="radio" name="paymentType" value="paypal" checked>
					<img src="https://www.paypal.com/en_US/i/logo/PayPal_mark_37x23.gif" align="left" style="margin-right:7px;"><span style="font-size:11px; font-family: Arial, Verdana;">The safer, easier way to pay.</span>
				</label>
				<label class="radio">
					<input type="radio" name="paymentType" value="creditcard">
					credit card
				</label>

				<?php if ( is_logged_in() ): ?>
					<h3>Billing address</h3>
					<address>
						<?php
							echo '<strong>' . $_SESSION['user']['given_name'] . ' ' . $_SESSION['user']['family_name'] . '</strong><br>' .
								$_SESSION['user']['street_address'] . '<br>' .
								$_SESSION['user']['locality'] . ', ' . $_SESSION['user']['region'] . '<br/>'.
								$_SESSION['user']['postal_code'] . '<br/>';
						?>
					</address>
					<h3>Shipping address</h3>
					<address>
						<?php
							echo '<strong>' . $_SESSION['user']['given_name'] . ' ' . $_SESSION['user']['family_name'] . '</strong><br>' .
								$_SESSION['user']['street_address'] . '<br>' .
								$_SESSION['user']['locality'] . ', ' . $_SESSION['user']['region'] . '<br/>'.
								$_SESSION['user']['postal_code'] . '<br/>';
						?>
					</address>					
				<?php endif; ?>

 				<p><button type="submit" class="btn btn-success">Continue checkout</button></p>
			</form>
		</div>

		<?php require 'inc_footer.php'; ?>

	<?php endif; ?>