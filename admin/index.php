<?php
	$title = 'Admin';

	require '../inc_header.php';
?>

	<p class="alert alert-pop"><strong>Note:</strong> Please create a buyer account on <a href="https://developer.paypal.com/">PayPal Sandbox</a> in order to log in and check out with PayPal.</p>

	<form class="form well" method="post">
		<input type="hidden" name="nonce" value ="<?=$_SESSION['nonce'] ?>" />
		<h4>Settings</h4>
		<fieldset>
			<h5>Checkout flow</h5>
			<p>The <strong>traditional flow</strong> includes a payments options section in the checkout flow and is the default setting. The <strong>streamlined flow</strong> jumps directly from login to PayPal checkout.</p>
			<div class="btn-group checkout-flow" data-toggle="buttons-radio">
				<?php
					if ( verify_nonce() && isset($_POST['fastFlow']) ) {
						if ( $_POST['fastFlow'] === '1' ) {
							$_SESSION['fastFlow'] =  true;
							$fastFlow = 1;

						} else {
							$_SESSION['fastFlow'] = false;
							$fastFlow = 0;
						}

					} elseif ( isset( $_SESSION['fastFlow'] ) && $_SESSION['fastFlow'] === true ) {
						$fastFlow = 1;

					} else {
						$_SESSION['fastFlow'] = false;
						$fastFlow = 0;
					}

					$buttons = array(
						'traditional',
						'streamlined'
						);

					$html = '';

					foreach ( $buttons as $i => $text ) {
						$active = ( $i === $fastFlow ) ? 'active' : '';
						$html .= '<button type="submit" class="btn ' . $active . '" name="fastFlow" value="' . $i . '">' . $text . ' flow</button>';
					}

					echo $html;
				?>
			</div>
		</fieldset>

		<fieldset>
			<h5>Debug mode</h5>
			<p>Enabling <strong>debug mode</strong> will show the back-end HTTP calls made to PayPal.</p>
			<div class="btn-group checkout-flow" data-toggle="buttons-radio">
				<?php
					if ( verify_nonce() && isset($_POST['debug_mode']) ) {
						if ( $_POST['debug_mode'] === '1' ) {
							$_SESSION['debug_mode'] =  true;
							$debug_mode = 1;

						} else {
							$_SESSION['debug_mode'] = false;
							$debug_mode = 0;
						}

					} elseif ( isset( $_SESSION['debug_mode'] ) && $_SESSION['debug_mode'] === true ) {
						$debug_mode = 1;

					} else {
						$_SESSION['debug_mode'] = false;
						$debug_mode = 0;
					}

					$buttons = array(
						'normal',
						'debug mode'
						);

					$html = '';

					foreach ( $buttons as $i => $text ) {
						$active = ( $i === $debug_mode ) ? 'active' : '';
						$html .= '<button type="submit" class="btn ' . $active . '" name="debug_mode" value="' . $i . '">' . $text . '</button>';
					}

					echo $html;
				?>
			</div>
		</fieldset>
	</form>

	<div class="user-accts">
		<table class="table table-hover table-condensed">
			<caption>User Accounts</caption>
			<tr>
				<th>email</th>
				<th>password</th>
				<th>first name</th>
				<th>last name</th>
				<th>action</th>
			</tr>
			<?php
				try {
					$dbColumns = array(
						'email',
						'password',
						'given_name',
						'family_name'
						);

					$query = 'SELECT id, ';
					foreach( $dbColumns as $colname ) {
						$query .= 'AES_DECRYPT(' . $colname . ',"' . AES_KEY . '") as ' . $colname . ',';
					}
					$query = substr( $query, 0, -1 );
					$query .= ' FROM users where session_key=AES_ENCRYPT("' . $_COOKIE['session_key'] . '","' . AES_KEY . '")';

					$result = $pdo->query( $query );

					foreach( $result as $row ) {
			        	echo '<tr>';

			        	foreach( $dbColumns as $key ) {
							echo '<td>' . @$row[$key] . '</td>';
						}

						echo '<td><a href="delete-user.php?id=' . $row['id'] . '">delete</a></td>';
						echo '</tr>';
			        }

				} catch( PDOException $e ) {
					echo $e->getMessage();
				}
			?>
		</table>
	</div>

<?php require '../inc_footer.php'; ?>
