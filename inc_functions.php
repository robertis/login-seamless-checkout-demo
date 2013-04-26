<?php
	session_start();

	// Sanitize inputs
	foreach( $_GET as $key => $value ) {
		$_GET[$key] = filter_input( INPUT_GET, $key, FILTER_SANITIZE_STRING );
	}

	foreach( $_POST as $key => $value ) {
		$_POST[$key] = filter_input( INPUT_POST, $key, FILTER_SANITIZE_STRING );
	}

	foreach( $_COOKIE as $key => $value ) {
		$_COOKIE[$key] = filter_input( INPUT_COOKIE, $key, FILTER_SANITIZE_STRING );
	}


	require 'inc_config.php';

	// Set session nonce and key
	if ( !isset($_SESSION['nonce']) ) {
		$_SESSION['nonce'] = uniqid();
	}

	if ( !isset($_COOKIE['session_key']) ) {
		$session_key = uniqid();
		setcookie( 'session_key', $session_key, time() + 60*60*24*30, '/' );
		$_COOKIE['session_key'] = $session_key;
	}	

	/**
	 * intialize database
	 */
	try {
		$pdo = new PDO( 'mysql:host=localhost;dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD );
		$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

	} catch ( Exception $e ) {
		try {
			// create new database
			$pdo = new PDO( 'mysql:host=localhost', DB_USERNAME, DB_PASSWORD );
			$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$pdo->exec( 'CREATE database ' . DB_NAME );
			$pdo = null;

			// create new columns in database
			$pdo = new PDO( 'mysql:host=localhost;dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD );
			$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

			$pdo->exec(
				'CREATE TABLE users (
					id INT NOT NULL AUTO_INCREMENT,
					PRIMARY KEY(id),
					email VARCHAR(128),
					password VARCHAR(128),
					given_name VARCHAR(128),
					family_name VARCHAR(128),
					language VARCHAR(128),
					phone_number VARCHAR(128),
					street_address VARCHAR(128),
					locality VARCHAR(128),
					region VARCHAR(128),
					postal_code VARCHAR(128),
					country VARCHAR(128),
					paypal_id VARCHAR(128),
					access_token VARCHAR(128),
					date_created DATETIME,
					session_key VARCHAR(128)
				)'
			);

		} catch ( Exception $e2 ) {
			echo '<h2 class=error><strong>Error:</strong> ' . $e2->getMessage() . '</h2>';
		}
	}

	/**
	 * Reload current page without the querystring.
	 * Using JavaScript to avoid header issues.
	 * @return
	 */
	function refresh_and_clear_querystring() {
		echo '<script>window.location.href=window.location.pathname;</script>';
	}

	/**
	 * Display error silently in browser console.
	 * @return
	 */
	function throw_error_in_console( $errorMsg ) {
		echo '<script>throw new Error("PHP: ' . $errorMsg . '");</script>';
		error_log( $errorMsg );
	}

	/**
	 * is user logged in (has username set in session data)
	 * @return boolean status of the given name session object
	 */
	function is_logged_in() {
		return isset( $_SESSION['username'] );
	}

	/**
	 * get user name
	 * @return string user name
	 */
	function get_username() {
		return ( is_logged_in() ) ? $_SESSION['username'] : '';
	}

	/**
	 * Prevents Cross-Site Scripting Forgery
	 * @return boolean
	 */
	function verify_nonce() {
	    return true;
	    /*
		if( isset($_GET['nonce']) && $_GET['nonce'] == $_SESSION['nonce'] ) {
			return true;
		}

		if( isset($_POST['nonce']) && $_POST['nonce'] == $_SESSION['nonce'] ) {
			return true;
		}

		return false;
		*/
	}

	/**
	 * log user in or out
	 * @return
	 */
	function logout_or_login() {
		if( !verify_nonce() ) {
			return;
		}

		// logout
		if ( isset( $_GET['logout'] ) ) {
			session_unset();

			echo '<script type="text/javascript">document.location.href="index.php";</script>';
		}

		// login
		if ( isset( $_POST['email'] ) && isset( $_POST['password'] ) ) {
			global $pdo;

			$email = $_POST['email'];
			$password = $_POST['password'];

			$query = "SELECT * FROM users WHERE email=AES_ENCRYPT(:email,'" . AES_KEY . "') AND password=AES_ENCRYPT(:password,'" . AES_KEY . "')";

			$sql = $pdo->prepare( $query );
			$sql->bindParam( ':email', $email, PDO::PARAM_STR );
			$sql->bindParam( ':password', $password, PDO::PARAM_STR );
			$sql->execute();

			if ( $userData = $sql->fetch( PDO::FETCH_ASSOC ) ) {
				set_user_logged_in( $userData['given_name'], $userData['email'] );
			}
		}

		echo '<script>sessionStorage.removeItem("intent");</script>';
	}

	/**
	 * get user data
	 * @param  string $email address
	 * @return object        user data
	 */
	function get_user_data( $email ) {
		try {
			global $pdo;

			$dbColumns = array(
				'email',
				'password',
				'given_name',
				'family_name',
				'language',
				'phone_number',
				'street_address',
				'locality',
				'region',
				'postal_code',
				'country',
				'paypal_id',
				'access_token',
				'session_key'
			);

			$query = 'SELECT id, date_created, ';
			foreach( $dbColumns as $colname ) {
				$query .= 'AES_DECRYPT(' . $colname . ',"' . AES_KEY . '") as ' . $colname . ',';
			}
			$query = substr( $query, 0, -1 );
			$query .= " FROM users WHERE email=AES_ENCRYPT(:email,'" . AES_KEY . "')";

			$sql = $pdo->prepare( $query );
		
			$sql->bindParam( ':email', $email, PDO::PARAM_STR );
			$sql->execute();

		} catch( PDOException $e ) {
			throw_error_in_console( $e->getMessage() );
		}

		if ( $sql->rowCount() ) {
			return $sql->fetch( PDO::FETCH_ASSOC );

		} else {
			return null;
		}
	}

	/**
	 * Sets user's session data
	 */
	function set_user_logged_in( $given_name, $email ) {
		$_SESSION['username'] = $given_name;
		$_SESSION['email'] = $email;
	}

	/**
	 * does user's email exist in the database
	 * @param  string $email address
	 * @return boolean
	 */
	function does_user_have_account( $email ) {
		$userData = get_user_data( $email );

		return isset( $userData );
	}

	/**
	 * does user exist and has PayPal ID?
	 * @param  string $email address
	 * @return boolean
	 */
	function does_user_have_paypal_id( $email ) {
		if ( !isset( $email ) ) {
			return false;
		}

		$userData = get_user_data( $email );

		if ( !isset( $userData ) ) {
			return false;
		}

		if ( isset( $userData['paypal_id'] ) && strlen( $userData['paypal_id'] ) > 0 ) {
			return true;
		}

		return false;
	}

	/**
	 * add PayPal ID to user account
	 * @param string $email    address
	 * @param string $payer_id PayPal payer ID
	 * @return boolean successfully added to database
	 */
	function add_paypal_id_to_user_account( $email, $payer_id ) {
		global $pdo;

		$success = false;

		try {
			$query = "UPDATE users SET paypal_id=AES_ENCRYPT(:payer_id,'" . AES_KEY ."') WHERE email=AES_ENCRYPT(:email,'" . AES_KEY . "')";
			
			$sql = $pdo->prepare( $query );
			$sql->bindParam( ':payer_id', $payer_id, PDO::PARAM_STR );
			$sql->bindParam( ':email', $email, PDO::PARAM_STR );
			$sql->execute();

			if ( $sql->rowCount() ) {
				$success = true;
			}

			return $success;

		} catch ( PDOException $e ) {
			throw_error_in_console( $e->getMessage() );
		}
	}

	/**
	 * store HTTP call details to session var for printout in debug mode 
	 * @param  curl object $ch 
	 */
	function log_http_call( $ch, $response, $postvals ) {
		if ( !isset($_SESSION['debug_mode']) || !$_SESSION['debug_mode'] ) {
			return;
		}

		if ( !isset($_SESSION['http_log']) ) {
			$_SESSION['http_log'] = array();
		}

		$url_data = curl_getinfo( $ch );
		if ( isset($postvals) ) {
			$url_data['parameters'] = $postvals;
		}
		$url_data['response'] = $response;

		array_push( $_SESSION['http_log'], $url_data );
	}

	/**
	 * get PayPal access token
	 * @param  string $code ?
	 * @return string       access token
	 */
	function acquire_access_token( $code ) {
		$accessToken = null;

		try {
			$postvals = sprintf(
				"client_id=%s&client_secret=%s&grant_type=authorization_code&code=%s",
				PP_SELLER_APP_ID,
				PP_SELLER_APP_SECRET,
				$code
			);

			$ch = curl_init( PPI_TOKEN_SERVICE_URL );

			$options = array(
				CURLOPT_POST           => 1,
				CURLOPT_VERBOSE        => 1,
				CURLOPT_POSTFIELDS     => $postvals,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_SSL_VERIFYPEER => FALSE
			);

			curl_setopt_array( $ch, $options );

			$response = curl_exec( $ch );
			$error = curl_error( $ch );

			log_http_call( $ch, $response, $postvals );

			curl_close( $ch );

			if ( !$response ) {
				throw new Exception( "Error retrieving access token: " . curl_error( $ch ) );
			}

			$jsonResponse = json_decode( $response );

			if ( isset( $jsonResponse->access_token ) ) {
				$accessToken = $jsonResponse->access_token;
			}

		} catch( Exception $e ) {
			throw_error_in_console( $e->getMessage() );
		}

		return $accessToken;
	}

	/**
	 * get the PayPal user profile, decoded
	 * @param  string $accessToken
	 * @return object
	 */
	function acquire_paypal_user_profile( $accessToken ) {
		try {
			$url = PPI_USER_INFO_URL . '?schema=openid&access_token=' . $accessToken;
			$ch = curl_init( $url );

			$options = array(
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_SSL_VERIFYPEER => FALSE
			);

			curl_setopt_array( $ch, $options );

			$response = curl_exec( $ch );
			$error = curl_error( $ch );
			log_http_call( $ch, $response, null );
			curl_close( $ch );

			if ( !$response ) {
				return false;
			}

			return json_decode( $response );

		} catch( Exception $e ) {
			return false;
		}
	}

	/**
	 * store access token for user
	 * @return boolean success
	 */
	function store_access_token( $email, $access_token ) {
		try {
			global $pdo;

			$sql = $pdo->prepare( "UPDATE users SET access_token=:access_token" );
			$sql->bindParam( ':access_token', $access_token, PDO::PARAM_STR );
			$sql->execute();

		} catch( PDOException $e ) {
			return false;
		}

		return true;
	}

	/**
	 * get access token for user
	 * @return string access_token
	 */
	function get_access_token( $email = null ) {
		if ( !isset( $email ) ) {
			$email = $_SESSION['email'];
		}

		$user_data = get_user_data( $email );

		if ( isset( $user_data['access_token'] ) ) {
			return $user_data['access_token'];
		}

		if ( isset( $_SESSION['access_token'] ) ) {
			return $_SESSION['access_token'];
		}

		return '';
	}

	/**
	 * remove older accounts
	 */
	function cull_accounts() {
		try {
			global $pdo;

			// Delete accounts older than one month
			$sql = $pdo->prepare( 'DELETE FROM users WHERE date_created < DATE_SUB(NOW(), INTERVAL 1 MONTH)' );
			$sql->execute();

			// Get number of accounts
			$num_accounts = 0;
			$sql = $pdo->prepare( 'SELECT COUNT(*) FROM users' );
			$sql->execute();

			if ( $sql->fetchColumn() > MYSQL_MAX_USERS ) {
				$sql = $pdo->prepare( 'DELETE FROM users WHERE id NOT IN (SELECT * FROM (SELECT ID FROM users ORDER BY date_created DESC LIMIT ' . MYSQL_MAX_USERS . ') AS t)' );
				$sql->execute();
			}

		} catch( PDOException $e ) {
			return 'Error culling accounts data: ' . $e->getMessage();
		}
	}

	/**
	 * create merchant account
	 * @return string error
	 */
	function create_account() {
		if ( !verify_nonce() ) {
			return "Cross-site scripting detection error";
		}

		if ( !isset( $_POST['email'] ) || strlen( $_POST['email'] ) == 0 ) {
			return "Email address not found.";
		}

		if ( does_user_have_account($_POST['email']) ) {
			return "Email account already exists.";
		}

		cull_accounts();

		try {
			global $pdo;

			$query = (
				"INSERT INTO users VALUES(
					0,
					AES_ENCRYPT(:email,':aes_key'),
					AES_ENCRYPT(:password,':aes_key'),
					AES_ENCRYPT(:given_name,':aes_key'),
					AES_ENCRYPT(:family_name,':aes_key'),
					AES_ENCRYPT(:language,':aes_key'),
					AES_ENCRYPT(:phone_number,':aes_key'),
					AES_ENCRYPT(:street_address,':aes_key'),
					AES_ENCRYPT(:locality,':aes_key'),
					AES_ENCRYPT(:region,':aes_key'),
					AES_ENCRYPT(:postal_code,':aes_key'),
					AES_ENCRYPT(:country,':aes_key'),
					AES_ENCRYPT(:payer_id,':aes_key'),
					AES_ENCRYPT(:access_token,':aes_key'),
					NOW(),
					AES_ENCRYPT(:session_key,':aes_key')
					)"
				);

			$query = str_replace( ":aes_key", AES_KEY, $query );

			$sql = $pdo->prepare( $query );

			$sql->bindParam( ':email', $_POST['email'], PDO::PARAM_STR );
			$sql->bindParam( ':password', $_POST['password'], PDO::PARAM_STR );
			$sql->bindParam( ':given_name', $_POST['given_name'], PDO::PARAM_STR );
			$sql->bindParam( ':family_name', $_POST['family_name'], PDO::PARAM_STR );
			$sql->bindParam( ':language', $_POST['language'], PDO::PARAM_STR );
			$sql->bindParam( ':phone_number', $_POST['phone_number'], PDO::PARAM_STR );
			$sql->bindParam( ':street_address', $_POST['street_address'], PDO::PARAM_STR );
			$sql->bindParam( ':locality', $_POST['locality'], PDO::PARAM_STR );
			$sql->bindParam( ':region', $_POST['region'], PDO::PARAM_STR );
			$sql->bindParam( ':postal_code', $_POST['postal_code'], PDO::PARAM_STR );
			$sql->bindParam( ':country', $_POST['country'], PDO::PARAM_STR );
			$sql->bindParam( ':payer_id', $_POST['payer_id'], PDO::PARAM_STR );
			$sql->bindParam( ':access_token', $_POST['access_token'], PDO::PARAM_STR );
			$sql->bindParam( ':session_key', $_COOKIE['session_key'], PDO::PARAM_STR );

			$sql->execute();

			set_user_logged_in( $_POST['given_name'], $_POST['email'] );

		} catch( Exception $e ) {
			echo 'Foo' . $e->getMessage();
			return 'Error creating data: ' . $e->getMessage();
		}

		return null;
	}

	/**
	 * parse url encoded parameter string
	 * @param  string $string url encoded string
	 * @return array
	 */
	function parse_url_encoded( $string ) {
		//$retval = [];
		$retval = array();

		$kvps = explode( '&', $string );

		foreach( $kvps as $kvp ) {
			$vals = explode( '=', $kvp );
			$retval[ $vals[0] ] = urldecode( $vals[1] );
		}

		return $retval;
	}

	/**
	 * render the cart count in HTML
	 * @return string HTML
	 */
	function render_cart_count() {
		if ( $cartCount = count( @$_GET['item'] ) + count( @$_SESSION['cartItems'] ) ) {
			return '&bull; <a href="' . BASE_URL . 'cart.php" title="Your cart has ' . $cartCount . ' items in it">Cart (' . $cartCount . ')</a>';
		}
	}

	/**
	 * Removes all items from cart
	 */
	function empty_cart() {
		unset( $_SESSION['cartItems'] );
		$_SESSION['cartItems'] = array();
	}

	/**
	 * Add items, delete items, or delete whole cart
	 */
	function manage_cart_items() {
		// make sure we have our session
		if ( !isset( $_SESSION['cartItems'] ) || !is_array( $_SESSION['cartItems'] ) ) {
			$_SESSION['cartItems'] = array();
		}

		// DELETE CART ITEM
		if ( isset( $_GET['delete-item'] ) && is_numeric( $_GET['delete-item'] ) ) {
			$cartItems = $_SESSION['cartItems'];

			if ( ( $key = array_search( $_GET['delete-item'], $cartItems ) ) !== false ) {
				unset( $cartItems[$key] );
			}

			$_SESSION['cartItems'] = $cartItems;

			refresh_and_clear_querystring();

		// DELETE CART
		} elseif ( isset( $_GET['delete-cart'] ) ) {
			empty_cart();
			refresh_and_clear_querystring();

		// ADD TO CART
		} elseif ( isset( $_GET['item']) && is_numeric( $_GET['item'] ) ) {
			$newItem = $_GET['item'];

			if ( !in_array( $newItem, $_SESSION['cartItems'] ) ) {
				array_push( $_SESSION['cartItems'], $newItem );

			} else {
				refresh_and_clear_querystring();
			}
		}
	}

	/**
	 * post checkout details
	 * @param  array $params items to post
	 * @return string        checkout response
	 */
	function post_checkout( $params ) {
		$ch = curl_init( PP_CHECKOUT_NVP_URL );
		$postvals = http_build_query( $params );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $postvals );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );

		$response = curl_exec( $ch );
		log_http_call( $ch, $response, $postvals );

		curl_close( $ch );

		return $response;
	}
?>
