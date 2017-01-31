<?php

// Ugly code.
// :(

// Debug
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// PHP MUST BE >= 5.4
// echo phpversion();
error_reporting(E_ALL ^ E_NOTICE);  // Avoid notice
ini_set('max_execution_time', 100);

session_start();

require_once 'google-api-php-client-2.1.1/vendor/autoload.php';

// MODIFY ONLY THIS VARIABLES
$CREDENTIALS = 'google-api-php-client-2.1.1/json/credentials.json';
$ALLOWED_DOMAIN = 'whirlpool.com';
$SCOPE = 'email';
$LOGOUT_MESSAGE = 'Logged out.';
$NOT_ALLOWED_MESSAGE = 'You\'re not allowed. Domain not recognized.';
$TOKEN_ERROR = 'Something went wrong contacting Google.';
$EXPIRED_SESSION = 'Expired session';
// END

// Init
$client = new Google_Client();
$client->setAuthConfig($CREDENTIALS);
$client->addScope($SCOPE);

// Empty error message
$ERROR_MESSAGE = '';
$AUTH_URL = '';

// Show login page
function showLogin ($message = null) {
	global $AUTH_URL, $ERROR_MESSAGE, $client;

	unset($_SESSION['token']);

	$ERROR_MESSAGE = isset($message) ? $message : '';
    $AUTH_URL = $client->createAuthUrl();

    require_once 'gapps-login.php';
	die;
}

function tokenExpired ($token) {
	if (!isset($token)) {
		return true;
	}

	$created = $token['created'];
	$expires_in = $token['expires_in'];

	// echo time();
	// echo '<br>';
	// echo $created;
	// echo '<br>';
	// echo $expires_in;
	// echo '<br>';
	// echo time() - ($created + $expires_in);
	// echo '<br>';
	// echo (time() - ($created + $expires_in)) >= 0;
	// echo '<br>';

	return (time() - ($created + $expires_in)) >= 0;
}

function checkTokenHealth ($token) {
	if (!isset($token)) {
		return false;
	}

	foreach (array_values($token) as $v) {
		if (!isset($v) || empty($v)) {
			return false;
		}
	}

	return true;
}

// logout: destroy token
if (isset($_GET['logout'])) {
	showLogin($LOGOUT_MESSAGE);
}

// TODO: clean GET variables in URL!
if (isset($_GET['token']) || isset($_SESSION['token'])) {

	if (isset($_GET['token'])) {
	
		$token = array(
	    	'access_token' => $_GET['access_token'],
	    	'token_type' => $_GET['token_type'],
	    	'expires_in' => intval($_GET['expires_in']),
	    	'id_token' => $_GET['id_token'],
	    	'created' => intval($_GET['created'])
	    );

	    $_SESSION['token'] = $token;
	
	} else {

		$token = $_SESSION['token'];

	}

	if (tokenExpired($token)) {
		showLogin($EXPIRED_SESSION);
	}

	// Check if $token, last time...
	if (!isset($token) || !checkTokenHealth($token)) {
		showLogin($TOKEN_ERROR);
	}

    $client->setAccessToken($token);

    // Get user's info
    $oauth2 = new \Google_Service_Oauth2($client);
	$userInfo = $oauth2->userinfo->get();

	if (isset($userInfo) && !empty($userInfo)) {
		$hd = $userInfo['hd'];

		// Check G-Apps domain. If it's not allowed...
		if (strcasecmp($ALLOWED_DOMAIN, $hd) != 0 || empty($hd)) {
			showLogin($NOT_ALLOWED_MESSAGE);
		}
	}

} else {
    showLogin();
}
