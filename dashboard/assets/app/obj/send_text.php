<?php
// Require the bundled autoload file - the path may need to change
// based on where you downloaded and unzipped the SDK
include_once 'twilio-php-master/Twilio/autoload.php';

// Use the REST API Client to make requests to the Twilio REST API
use Twilio\Rest\Client;

ini_set("log_errors", 1);
ini_set("error_log", "/tmp/php-error.log");

function _sendText($who, $text){
    $sid = 'SKa7d45c9e7536fc3ca713b8ea27a3955d';
    $token = 'BuLDsz7ElhS9Xnnt2XdE8PYYtbVjK8JL';
    $client = new Client($sid, $token);

    $choose = array('3177932337');
    $rand   = rand(0, 0);
    $chosen = $choose[$rand];

    try {
        $client->messages->create(
            '+1'.$who,
            array(
                'from' => '+13177932337',
                'body' => $text
            )
        );
        return true;
    } catch(Exception $e) {
        return false;
    }
}