<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 5/28/2017
 * Time: 6:50 AM
 */
session_start();
include 'init.php';

ini_set("log_errors", 1);
ini_set("error_log", "/tmp/php-error.log");

if(isset($_GET['txt'])){
    $key = 'AIzaSyDxLua1UKdf-637NvG5NgBuhb0DYVQ77cg';
    $googer = new GoogleUrlApi($key);
    if($_GET['txt'] == 'upr'){
        $who = $_POST['p'];
        $msg = "Your new password on FMO is: ".struuid();

        _sendText($who, $msg);
    }
    if($_GET['txt'] == 'ttm'){
        $manager = mysql_query("SELECT user_phone FROM fmo_users WHERE user_token='".mysql_real_escape_string($_GET['uuid'])."'");
        if(mysql_num_rows($manager) > 0){
            $number = mysql_fetch_array($manager);
            $phone  = $number['user_phone'];
        }
        $msg = $_POST['msg'];

        _sendText($phone, $msg);
        
    }
    if($_GET['txt'] == 'claim_link'){
        $event = mysql_fetch_array(mysql_query("SELECT event_phone FROM fmo_locations_events WHERE event_token='".$_POST['ev']."'"));
        if(!empty($event['event_phone'])){
            $claim_link = $googer->shorten("https://www.formoversonly.com/dashboard/assets/public/claim.php?ev=".$_POST['ev']);
            $msg        = "File your claim using the link below:\r\n".$claim_link;
            _sendText($event['event_phone'], $msg);
        }
    }
    if($_GET['txt'] == 'review_link'){
        $event = mysql_fetch_array(mysql_query("SELECT event_phone FROM fmo_locations_events WHERE event_token='".$_POST['ev']."'"));
        if(!empty($event['event_phone'])){
            $review_link = $googer->shorten("https://www.formoversonly.com/dashboard/assets/public/review.php?ev=".$_POST['ev']);
            $msg        = "File your review using the link below:\r\n".$review_link;
            _sendText($event['event_phone'], $msg);
        }
    }
}