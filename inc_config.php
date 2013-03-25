<?php
	define( 'STORE_NAME', 'BigBox' );

	// MySQL
	define( 'DB_NAME', 'bigbox_demo' );
	define( 'DB_USERNAME', 'root' );
	$pw = ( $_SERVER['SERVER_NAME'] == 'sb.dev' ) ? 'root' : '';
	define( 'DB_PASSWORD', $pw );
	define( 'MYSQL_MAX_USERS', '1000' );
	define( 'AES_KEY', 'F[O/4-sL5_;PNOE2a;<|4w' );

	// PayPal identity
	define( 'PPI_BUTTON_JS', 'https://www.paypalobjects.com/js/external/api.js' );
	define( 'PPI_TOKEN_SERVICE_URL', 'https://www.sandbox.paypal.com/webapps/auth/protocol/openidconnect/v1/tokenservice' );
	define( 'PPI_USER_INFO_URL', 'https://www.sandbox.paypal.com/webapps/auth/protocol/openidconnect/v1/userinfo' );

	// PayPal checkout
	define( 'PP_SELLER_APP_ID', 'simplebutton_sb_client_dev' );
	define( 'PP_SELLER_APP_SECRET' , 'simplebutton_sb_client_dev' );
	define( 'PP_VERSION', '92.0' );
	define( 'PP_USER', 'seller_1357834706_biz_api1.paypal.com' );
	define( 'PP_PWD', '1357834725' );
	define( 'PP_SIGNATURE', 'AFcWxV21C7fd0v3bYYYRCpSSRl31AdYAf2hyMbhVdgvRiPEYQJhaAJZn' );
	define( 'PP_CHECKOUT_URL', 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout' );
	define( 'PP_CHECKOUT_NVP_URL', 'https://api-3t.sandbox.paypal.com/nvp' );

	/**
	 * BUILD THE BASE_URL
	 */
	// Is the user using HTTPS?
	$base_url = ( @$_SERVER['HTTPS'] == 'on' ) ? 'https://' : 'http://';
	// Complete the URL
	$base_url .= $_SERVER['HTTP_HOST'] . dirname( $_SERVER['PHP_SELF'] );
	// Strip the admin directory
	$base_url = preg_replace( '/admin/', '', $base_url );
	// Make sure we end with a slash
	define( 'BASE_URL', preg_replace( '/([^\/])$/', '$1/', $base_url ) );


	/**
	 * DEFINE THE STORE ITEMS
	 */
	$storeItems = array();
	$storeItems[0]['title'] = 'Umbrella';
	$storeItems[0]['image'] = '&#x2602';
	$storeItems[0]['price'] = '12.50';
	$storeItems[1]['title'] = 'Horse';
	$storeItems[1]['image'] = '&#x265e';
	$storeItems[1]['price'] = '38.64';
	$storeItems[2]['title'] = 'Telephone';
	$storeItems[2]['image'] = '&#x260e';
	$storeItems[2]['price'] = '12.00';
	$storeItems[3]['title'] = 'Crown';
	$storeItems[3]['image'] = '&#x265b';
	$storeItems[3]['price'] = '21.30';
	$storeItems[4]['title'] = 'Wheel';
	$storeItems[4]['image'] = '&#x2638';
	$storeItems[4]['price'] = '16.50';

	/**
	 * DEFINE STATES AND COUNTRIES
	 */
	$regionArray = array(
		"AL" => "Alabama",
		"AK" => "Alaska",
		"AZ" => "Arizona",
		"AR" => "Arkansas",
		"CA" => "California",
		"CO" => "Colorado",
		"CT" => "Connecticut",
		"DE" => "Delaware",
		"DC" => "District of Columbia",
		"FL" => "Florida",
		"GA" => "Georgia",
		"HI" => "Hawaii",
		"ID" => "Idaho",
		"IL" => "Illinois",
		"IN" => "Indiana",
		"IA" => "Iowa",
		"KS" => "Kansas",
		"KY" => "Kentucky",
		"LA" => "Louisiana",
		"ME" => "Maine",
		"MD" => "Maryland",
		"MA" => "Massachusetts",
		"MI" => "Michigan",
		"MN" => "Minnesota",
		"MS" => "Mississippi",
		"MO" => "Missouri",
		"MT" => "Montana",
		"NE" => "Nebraska",
		"NV" => "Nevada",
		"NH" => "New Hampshire",
		"NJ" => "New Jersey",
		"NM" => "New Mexico",
		"NY" => "New York",
		"NC" => "North Carolina",
		"ND" => "North Dakota",
		"OH" => "Ohio",
		"OK" => "Oklahoma",
		"OR" => "Oregon",
		"PA" => "Pennsylvania",
		"RI" => "Rhode Island",
		"SC" => "South Carolina",
		"SD" => "South Dakota",
		"TN" => "Tennessee",
		"TX" => "Texas",
		"UT" => "Utah",
		"VT" => "Vermont",
		"VA" => "Virginia",
		"WA" => "Washington",
		"WV" => "West Virginia",
		"WI" => "Wisconsin",
		"WY" => "Wyoming"
		);

	$countryArray = array(
		"Australia" => "Australia",
		"Brazil" => "Brazil",
		"Canada" => "Canada",
		"China" => "China",
		"Denmark" => "China",
		"France" => "France",
		"Germany" => "Germany",
		"Indonesia" => "Indonesia",
		"Israel" => "Israel",
		"Italy" => "Italy",
		"Japan" => "Japan",
		"Mexico" => "Mexico",
		"Netherlands" => "Netherlands",
		"Norway" => "Norway",
		"Poland" => "Poland",
		"Portugal" => "Portugal",
		"Russia" => "Russia",
		"Spain" => "Spain",
		"Sweden" => "Sweden",
		"Taiwan" => "Taiwan",
		"Thailand" => "Thailand",
		"Turkey" => "Turkey",
		"UK" => "United Kingdom",
		"US" => "United States"
		);

?>
