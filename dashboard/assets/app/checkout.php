<?php
/**
 * Created by PhpStorm.
 * User: gameroom
 * Date: 7/6/2017
 * Time: 9:35 PM
 */

include 'init.php';
require_once('obj/stripe-php-master/init.php');


if(isset($_GET['e']) && $_GET['e'] == 'LOL'){
    $companyinf = mysql_fetch_array(mysql_query("SELECT location_storage_stripe_secret, location_storage_stripe_public FROM fmo_locations WHERE location_owner_company_token='".mysql_real_escape_string($_GET['cuid'])."'"));

    $stripe = array(
        "secret_key"      => "".$companyinf['location_storage_stripe_secret']."",
        "publishable_key" => "".$companyinf['location_storage_stripe_public'].""
    );

    \Stripe\Stripe::setApiKey($stripe['secret_key']);

    $token  = $_POST['token'];
    $amount = $_POST['amount'];
    $email  = $_POST['email'];
    $auto   = $_POST['auto'];

    try {

        $customer = \Stripe\Customer::create(array(
            'email' => $email,
            'source'  => $token
        ));

        $charge = \Stripe\Charge::create(array(
            'customer' => $customer->id,
            'amount'   => $amount,
            'currency' => 'usd'
        ));

        echo $charge->id."|".$customer->id;
        timeline_log($_GET['uuid'], $_SESSION['uuid'], "Card informer", "Last payment used card # <strong>".$last4."</strong>");
    } catch (\Stripe\Error\ApiConnection $e) {
        // Network problem, perhaps try again.
        echo "error-1";
    } catch (\Stripe\Error\InvalidRequest $e) {
        // You screwed up in your programming. Shouldn't happen!
        echo "error-2";
    } catch (\Stripe\Error\Api $e) {
        // Stripe's servers are down!
        echo "error-3";
    } catch (\Stripe\Error\Card $e) {
        // Card was declined.
        echo "error-4";
    }
} else {
    $companyinf = mysql_fetch_array(mysql_query("SELECT user_stripe_pk, user_stripe_sk FROM fmo_users WHERE user_company_token='".mysql_real_escape_string($_GET['cuid'])."'"));

    $stripe = array(
        "secret_key"      => "".$companyinf['user_stripe_sk']."",
        "publishable_key" => "".$companyinf['user_stripe_pk'].""
    );

    \Stripe\Stripe::setApiKey($stripe['secret_key']);

    $token  = $_POST['token'];
    $amount = $_POST['amount'];
    $email  = $_POST['email'];

    try {

        $customer = \Stripe\Customer::create(array(
            'email' => $email,
            'source'  => $token
        ));

        $charge = \Stripe\Charge::create(array(
            'customer' => $customer->id,
            'amount'   => $amount,
            'currency' => 'usd'
        ));

        echo $charge->id;
        timeline_event($_GET['ev'], $_SESSION['uuid'], "Card informer", "Last payment used card # <strong>".$last4."</strong>");
    } catch (\Stripe\Error\ApiConnection $e) {
        // Network problem, perhaps try again.
        echo "error-1";
    } catch (\Stripe\Error\InvalidRequest $e) {
        // You screwed up in your programming. Shouldn't happen!
        echo "error-2";
    } catch (\Stripe\Error\Api $e) {
        // Stripe's servers are down!
        echo "error-3";
    } catch (\Stripe\Error\Card $e) {
        // Card was declined.
        echo "error-4";
        timeline_event($_GET['ev'], $_SESSION['uuid'], "Card declined", "Card was declined for <strong>$10.00</strong> booking fee. Reference: ".$last4);
    }
}
