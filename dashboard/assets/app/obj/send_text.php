<?php
// Require the bundled autoload file - the path may need to change
// based on where you downloaded and unzipped the SDK
require __DIR__ .'/twilio-php-master/Twilio/autoload.php';
require 'GoogleUrlApi.php';

// Use the REST API Client to make requests to the Twilio REST API
use Twilio\Rest\Client;

ini_set("log_errors", 1);
ini_set("error_log", "/tmp/php-error.log");

function _sendText($who, $text){
    $sid = 'AC0bf571148b89cecccce17421c0d1a0e4';
    $token = '21dc5570a44d4887a9c38a59bf0f00ea';
    $client = new Client($sid, $token);

    $choose = array('3176891082', '3176891102', '3175880075');
    $rand   = rand(0, 2);
    $chosen = $choose[$rand];

    try {
        $client->messages->create(
            '+1'. preg_replace('/[^A-Za-z0-9\-]/', '', $who),
            array(
                'from' => '+1'.$chosen,
                'body' => $text
            )
        );
    } catch(Exception $e) {

    }
}