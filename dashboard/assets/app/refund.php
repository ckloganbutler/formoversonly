<?php
/**
 * Created by PhpStorm.
 * User: gameroom
 * Date: 7/6/2017
 * Time: 9:35 PM
 */

include 'init.php';
require_once('obj/stripe-php-master/init.php');

$companyinf = mysql_fetch_array(mysql_query("SELECT user_stripe_pk, user_stripe_sk FROM fmo_users WHERE user_company_token='".mysql_real_escape_string($_GET['cuid'])."'"));

$stripe = array(
    "secret_key"      => "".$companyinf['user_stripe_sk']."",
    "publishable_key" => "".$companyinf['user_stripe_pk'].""
);

\Stripe\Stripe::setApiKey($stripe['secret_key']);

$token  = $_POST['ch'];

try {

    \Stripe\Refund::create(array(
        "charge" => $token
    ));

    echo "error-0";
} catch (\Stripe\Error\ApiConnection $e) {
    // Network problem, perhaps try again.
    echo "error-1";
} catch (\Stripe\Error\InvalidRequest $e) {
    // You screwed up in your programming. Shouldn't happen!
    echo "error-2";
} catch (\Stripe\Error\Api $e) {
    // Stripe's servers are down!
    echo "error-3";
}