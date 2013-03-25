<?php
	$title = 'Your receipt';

	require 'inc_header.php';
?>

	<?php
		if( verify_nonce() ) {
			try {
				$token 	 = $_SESSION['user'][ 'token' ];
				$payerId = $_SESSION['user'][ 'payer_id' ];
				$amount	 = $_SESSION['user'][ 'amt' ];

				$_SESSION['user']['token']    = null;
				$_SESSION['user']['payer_id'] = null;
				$_SESSION['user']['amt']      = null;

				$params = array(
					'method'       => 'DoExpressCheckoutPayment',
					'token'        => $token,
					'payerid'      => $payerId,
					'itemamt'      => $amount,
					'amt'          => $amount,
					'currencycode' => 'USD',
					'paymentaction'=> 'Sale',
					'version'      => PP_VERSION,
					'user'         => PP_USER,
					'pwd'          => PP_PWD,
					'signature'    => PP_SIGNATURE
					);

				$response = post_checkout( $params );
				$checkoutDetails = parse_url_encoded( $response );

				if ( $checkoutDetails['ACK'] != 'Success' ) {
					$error = $checkoutDetails['L_LONGMESSAGE0'];
				}

			} catch( Exception $e ) {
				$error = $e->getMessage();
			}
		}
	?>

	<?php if ( isset( $error ) ): ?>
		<h2>Error</h2>
		<p class="error">Oops, sorry. (The developers might understand this message: <code><?=$error ?></code>)</p>

	<?php else: ?>
		<?php 
			include 'inc_invoice.php'; 

			empty_cart();
		?>

		<h2 class="thanks">Thank you for your purchase!</h2>

	<?php endif; ?>

<?php require 'inc_footer.php'; ?>
