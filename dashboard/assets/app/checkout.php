<?php
/**
 * Created by PhpStorm.
 * User: gameroom
 * Date: 7/6/2017
 * Time: 9:35 PM
 */

require_once ('obj/stripe.php');

$token  = $_POST['stripeToken'];
$amount = $_POST['stripeAmt'];
$email  = $_POST['stripeEmail'];

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
} catch (\Stripe\Error\ApiConnection $e) {
    // Network problem, perhaps try again.
} catch (\Stripe\Error\InvalidRequest $e) {
    // You screwed up in your programming. Shouldn't happen!
} catch (\Stripe\Error\Api $e) {
    // Stripe's servers are down!
} catch (\Stripe\Error\Card $e) {
    // Card was declined.
}