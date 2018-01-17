<?php
/**
 * Created by PhpStorm.
 * User: gameroom
 * Date: 6/16/2017
 * Time: 6:43 AM
 */

session_start();
include('init.php');
require_once('obj/stripe-php-master/init.php');

if(isset($_GET['e']) && $_GET['e'] == 'LOL'){
    $stripe = array(
        "secret_key"      => "sk_live_jXTRX2yoTyaqxB197myHgjoB",
        "publishable_key" => "pk_live_ftqBPIkJ6eBemXHToHiU8Eqa"
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

        $last4 = $customer->sources->data[0]->last4;

        echo $charge->id;
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
} elseif(isset($_GET['e']) && $_GET['e'] == 'subscr'){
    $stripe = array(
        "secret_key"      => "sk_live_jXTRX2yoTyaqxB197myHgjoB",
        "publishable_key" => "pk_live_ftqBPIkJ6eBemXHToHiU8Eqa"
    );

    \Stripe\Stripe::setApiKey($stripe['secret_key']);

    $token  = $_POST['token'];
    switch ($_POST['i']){
        case "STD":        $amount = 9999;   break;
        case "STDPLUS":    $amount = 99999;  break;
        case "ENTPRI":     $amount = 14999;  break;
        case "ENTPRIPLUS": $amount = 149999; break;
    }
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

        $last4 = $customer->sources->data[0]->last4;

        echo $charge->id;
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
    $stripe = array(
        "secret_key"      => "sk_live_jXTRX2yoTyaqxB197myHgjoB",
        "publishable_key" => "pk_live_ftqBPIkJ6eBemXHToHiU8Eqa"
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

        $last4 = $customer->sources->data[0]->last4;

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