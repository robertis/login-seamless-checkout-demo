<?php require 'inc_functions.php'; ?>

<?php
	$targetUrl = 'index.php';

	if ( isset($_GET['code']) ) {
		try {
			$access_token = acquire_access_token( $_GET['code'] );

			if ( !isset($access_token) ) {
				throw new Exception( 'Failed to get access token' );
			}

			// For use with "Log In and Checkout", when we just want the access token and not a full user account
			$_SESSION['access_token'] = $access_token;

			$profile = acquire_paypal_user_profile( $access_token );

			if ( !isset($profile) ) {
				throw new Exception( 'Failed to get user profile' );
			}

			$_SESSION['username'] = $profile->given_name;
			$_SESSION['user'] = array (
				"email"          => $profile->email,
				"given_name"     => $profile->given_name,
				"family_name"    => $profile->family_name,
				"language"       => $profile->language,
				"phone_number"   => $profile->phone_number,
				"street_address" => $profile->address->street_address,
				"locality"       => $profile->address->locality,
				"region"         => $profile->address->region,
				"postal_code"    => $profile->address->postal_code,
				"country"        => $profile->address->country,
				"payer_id"       => $profile->payer_id,
				"access_token"	 => $access_token
			);			

			if ( does_user_have_account($profile->email) ) {
				set_user_logged_in( $profile->given_name, $profile->email );

				store_access_token( $profile->email, $access_token );

				if ( !does_user_have_paypal_id($profile->email) ) {
					$targetUrl = 'link-accounts.php?email=' . urlencode( $profile->email ) . '&payer_id=' . $profile->payer_id;
				}

			} else {
				$targetUrl = 'create-account.php';
			}

		} catch( Exception $e ) {
			throw_error_in_console( $e->getMessage() );
		}
	}
?>

<script>
	var endpoint = ( sessionStorage.intent ) ? "<?=BASE_URL ?>" + sessionStorage.intent : "<?=$targetUrl ?>";

	window.opener.location.href = endpoint;

	window.close();
</script>
