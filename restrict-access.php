<?php

// Debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// PHP MUST BE >= 5.4
// echo phpversion(); 

session_start();

require_once 'google-api-php-client-2.1.1/vendor/autoload.php';

$credentials = 'google-api-php-client-2.1.1/json/client_secret_862237647527-q28bkscruu70dfrb6ek85fmd3g6afrft.apps.googleusercontent.com.json';

$client = new Google_Client();
$client->setAuthConfig($credentials);
$client->addScope('email');
$ALLOWED_DOMAIN = 'leandrogentili.me';
$errorMessage = '';

// Pay attention to Redirect URL
// $client->setRedirectUri($scriptUri);

if (isset($_GET['logout'])) { // logout: destroy token
    unset($_SESSION['token']);

    $errorMessage = 'Logged out.';
    echo file_get_contents('gapps-login.php');

	die;
}

if (isset($_GET['code'])) { // we received the positive auth callback, get the token and store it in session
    $client->authenticate($_GET['code']);
    $_SESSION['token'] = $client->getAccessToken();
}

if (isset($_SESSION['token'])) { // extract token from session and configure client
    $token = $_SESSION['token'];
    $client->setAccessToken($token);

    $oauth2 = new \Google_Service_Oauth2($client);
	$userInfo = $oauth2->userinfo->get();
	
	if (isset($userInfo) && !empty($userInfo)) {
		$hd = $userInfo['hd'];

		if (strcasecmp($ALLOWED_DOMAIN, $hd) != 0 || empty($hd)) {
			$errorMessage = 'You\'re not allowed. Domain not recognized.';
			
			unset($_SESSION['token']);
			$authUrl = $client->createAuthUrl();

			require_once 'gapps-login.php';
			
			die;
		}
	}
}

if (!$client->getAccessToken()) {
    $authUrl = $client->createAuthUrl();

    require_once 'gapps-login.php';

    // header("Location: ".$authUrl);
    die;
}


