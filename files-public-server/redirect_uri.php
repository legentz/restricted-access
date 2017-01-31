<?php

// Debug
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

session_start();

require_once 'google-api-php-client-2.1.1/vendor/autoload.php';

// MODIFY ONLY THIS VARIABLES
$CREDENTIALS = 'google-api-php-client-2.1.1/json/credentials.json';
$LOCAL_ADDRESS = 'http://192.168.1.107';
$LOCAL_CALLBACK_URI_PATH = '/whirlpool-blog/';
$QUERY_TOKEN = 'index.php?token=true&';
$SCOPE = 'email';
// END

$client = new Google_Client();
$client->setAuthConfig($CREDENTIALS);
$client->addScope($SCOPE);

// TODO: need to encrypt
// Callback received...
if (isset($_GET['code'])) {
    $client->authenticate($_GET['code']);

    $token = $client->getAccessToken();
    $token = isset($token) ? $token : array();

    $token_get_string = implode('&', array_map( 
    	function ($v, $k) { 
    		return sprintf("%s=%s", $k, $v);
    	}, 
    	$token, array_keys($token))
    );

    // var_dump($token);

    echo 'Click <a href="#" onclick="window.location.href=\'' . $LOCAL_ADDRESS . $LOCAL_CALLBACK_URI_PATH . $QUERY_TOKEN . $token_get_string . '\' ">here</a> to continue login process.';
}

// If no GET, show datetime 
if (count($_GET) == 0) {
    echo (new \DateTime())->format('Y-m-d H:i:s');
}