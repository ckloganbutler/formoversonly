<?php
/**
 * Created by PhpStorm.
 * User: gameroom
 * Date: 7/6/2017
 * Time: 9:36 PM
 */
require_once('stripe-php-master/init.php');
//THIS WILL HOLD THE COMPANIES VALUES NOT FMO's
$stripe = array(
    "secret_key"      => "sk_test_1QhdZGvGH9Bh45QjG99AjINk",
    "publishable_key" => "pk_test_o9s6ScI3jBABd3V5pZM7kdYA"
);

\Stripe\Stripe::setApiKey($stripe['secret_key']);