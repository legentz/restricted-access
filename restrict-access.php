<?php

// Ugly code.
// :(

// Debug
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// PHP MUST BE >= 5.4
// echo phpversion(); 

session_start();

require_once 'google-api-php-client-2.1.1/vendor/autoload.php';

$credentials = 'google-api-php-client-2.1.1/json/client_secret_862237647527-q28bkscruu70dfrb6ek85fmd3g6afrft.apps.googleusercontent.com.json';

$client = new Google_Client();
$client->setAuthConfig($credentials);
$client->addScope('email');
$ALLOWED_DOMAIN = 'whirlpool.com';
$errorMessage = '';

// logout: destroy token
if (isset($_GET['logout'])) { 
    unset($_SESSION['token']);

    $errorMessage = 'Logged out.';

    // Show login page with errorMessage
    require_once 'gapps-login.php';

	die;
}

// We received the positive auth callback, get the token and store it in session
if (isset($_GET['code'])) { 
    $client->authenticate($_GET['code']);
    $_SESSION['token'] = $client->getAccessToken();
}

// extract token from session and configure client
if (isset($_SESSION['token'])) {
    $token = $_SESSION['token'];
    $client->setAccessToken($token);

    // Get user's info
    $oauth2 = new \Google_Service_Oauth2($client);
	$userInfo = $oauth2->userinfo->get();
	
	if (isset($userInfo) && !empty($userInfo)) {
		$hd = $userInfo['hd'];

		// Check G-Apps domain. If it's not allowed...
		if (strcasecmp($ALLOWED_DOMAIN, $hd) != 0 || empty($hd)) {
			$errorMessage = 'You\'re not allowed. Domain not recognized.';
			
			// Unset token so user can try again
			unset($_SESSION['token']);

			// Auth HREF
			$authUrl = $client->createAuthUrl();

			// Show login page with errorMessage
			require_once 'gapps-login.php';
			
			die;
		}
	}
}

// No token? Login
if (!$client->getAccessToken()) {
    $authUrl = $client->createAuthUrl();

    // Show login page with authUrl
    require_once 'gapps-login.php';

    die;
}


