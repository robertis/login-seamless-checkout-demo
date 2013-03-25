<?php
	$title = 'Confirm payment details';

	require 'inc_header.php';
?>

	<?php
		try {
			$token 	= $_GET['token'];
			$status = $_GET['result'];

			$params = array(
				'method'    => 'GetExpressCheckoutDetails',
				'token'     => $token,
				'version'   => PP_VERSION,
				'user'      => PP_USER,
				'pwd'       => PP_PWD,
				'signature' => PP_SIGNATURE
				);

			$response = post_checkout( $params );
			$checkoutDetails = parse_url_encoded( $response );

			$_SESSION['user']['token'] 	  = $checkoutDetails['TOKEN'];
			$_SESSION['user']['payer_id'] = $checkoutDetails['PAYERID'];
			$_SESSION['user']['amt']      = $checkoutDetails['AMT'];

			$invoice = array(
				'Amount'      => $checkoutDetails['AMT'],
				'Name'        => $checkoutDetails['SHIPTONAME'],
				'Street'      => $checkoutDetails['SHIPTOSTREET'],
				'City'        => $checkoutDetails['SHIPTOCITY'],
				'State'       => $checkoutDetails['SHIPTOSTATE'],
				'Postal Code' => $checkoutDetails['SHIPTOZIP'],
				'Country'     => $checkoutDetails['SHIPTOCOUNTRYNAME']
				);

			$_SESSION['invoice'] = $invoice;

		} catch( Exception $e ) {
			$error = $e->getMessage();
		}
	?>

	<?php if( isset($_GET['result']) && $_GET['result'] == 'cancelled' ): ?>
		<h2>Transaction Cancelled</h2>
		<p>Your transaction has been cancelled.</p>

	<?php elseif ( isset( $error ) ): ?>
		<h2>Error</h2>
		<p class="error">Oops, sorry. (The developers might understand this message: <code><?=$error ?></code>)</p>

	<?php else: ?>
		<?php include 'inc_invoice.php'; ?>

	<?php endif; ?>

<?php require 'inc_footer.php'; ?>
