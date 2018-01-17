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
        $who  = $_POST['p'];
        $uuid = $_POST['uuid'];
        $b    = $_POST['b'];
        $msg = "Your new password on FMO is: ".struuid();

        $rates = $googer->shorten("https://www.formoversonly.com/dashboard/assets/public/pw.php?t=reset&uuid=".$uuid."&b=".$b);
        _sendText($who, "[".companyName($_SESSION['cuid'])."]\r\nReset your password here:\r\n".$rates);

    }
    if($_GET['txt'] == 'urs'){
        $who  = preg_replace('/[^A-Za-z0-9\-]/', '', $_POST['p']);
        $uuid = mysql_query("SELECT user_token FROM fmo_users WHERE user_phone='".mysql_real_escape_string($who)."' AND NOT user_phone='' LIMIT 1");
        if(mysql_num_rows($uuid)){
            $b = mysql_fetch_array($uuid);
            $uuid = $b['user_token'];
            $rates = $googer->shorten("https://www.formoversonly.com/dashboard/assets/public/pw.php?t=reset&uuid=".$uuid['user_token']."&b=".$uuid['user_token']);
            _sendText($who, "[".companyName($_SESSION['cuid'])."]\r\nReset your password here:\r\n".$rates);
            ?>
            <h4 class="text-center">You should receive your text message shortly, if we have your phone in our records. </h4>
            <?php
        } else {
            ?>
            <h4 class="text-center">Didn't recognize that phone. Please contact your administrator for help. </h4>
            <?php
        }
    }
    if($_GET['txt'] == 'ttm'){
        $owner   = mysql_fetch_array(mysql_query("SELECT user_phone FROM fmo_users WHERE user_company_token='".mysql_real_escape_string($_SESSION['cuid'])."'"));
        $manager = mysql_query("SELECT user_phone FROM fmo_users WHERE user_token='".mysql_real_escape_string($_GET['uuid'])."'");
        if(mysql_num_rows($manager) > 0){
            $number = mysql_fetch_array($manager);
            $phone  = $number['user_phone'];
        }
        $name = explode (" ", name($_SESSION['uuid']));
        $msg = $name[0]." said: ".$_POST['msg'];

        _sendText($phone, $msg);
        _sendText($owner['user_phone'], $msg);
        mysql_query("INSERT INTO fmo_locations_text_records (text_location_token, text_text, text_by_user_token) VALUES (
        '".mysql_real_escape_string($_GET['luid'])."',
        '".mysql_real_escape_string($msg)."',
        '".mysql_real_escape_string($_SESSION['uuid'])."')");
    }
    if($_GET['txt'] == 'claim_link'){
        $event = mysql_fetch_array(mysql_query("SELECT event_phone, event_user_token FROM fmo_locations_events WHERE event_token='".$_POST['ev']."'"));
        if(!empty($event['event_phone'])){
            $claim_link = $googer->shorten("https://www.formoversonly.com/dashboard/assets/public/ticket.php?ev=".$_POST['ev']."&uuid=".$event['event_user_token']);
            $msg        = "[".companyName($_SESSION['cuid'])."]\r\nFile your claim using the link below:\r\n".$claim_link;
            _sendText($event['event_phone'], $msg);
        }
    }
    if($_GET['txt'] == 'teller'){
        $user = mysql_fetch_array(mysql_query("SELECT user_phone FROM fmo_users WHERE user_token='".$_POST['uuid']."'"));
        if(!empty($user['user_phone'])){
            $deposit_link = $googer->shorten("https://www.formoversonly.com/dashboard/assets/public/deposits.php?dpt=".$_POST['dpt']);
            $msg        = "[".companyName($_SESSION['cuid'])."]\r\nFinish verifying your deposit here:\r\n".$deposit_link;
            _sendText($user['user_phone'], $msg);
        }
    }
    if($_GET['txt'] == 'review_link'){
        $event = mysql_fetch_array(mysql_query("SELECT event_phone FROM fmo_locations_events WHERE event_token='".$_POST['ev']."'"));
        if(!empty($event['event_phone'])){
            $review_link = $googer->shorten("https://www.formoversonly.com/dashboard/assets/public/review.php?ev=".$_POST['ev']);
            $msg        = "[".companyName($_SESSION['cuid'])."]\r\nFile your review using the link below:\r\n".$review_link;
            _sendText($event['event_phone'], $msg);
            timeline_event($_POST['ev'], $_SESSION['uuid'], "Misc tool used", "<strong>Review link</strong> was sent to this events phone number.");
        }
    }
    if($_GET['txt'] == 'confirm_link'){
        $event = mysql_fetch_array(mysql_query("SELECT event_phone FROM fmo_locations_events WHERE event_token='".$_POST['ev']."'"));
        if(!empty($event['event_phone'])){
            $confirm = $googer->shorten("https://www.formoversonly.com/dashboard/assets/public/conf.php?ty=confirm&ev=".$_POST['ev']);
            _sendText($event['event_phone'], "[".companyName($_SESSION['cuid'])."]\r\nView & confirm your move here:\r\n".$confirm);
            timeline_event($_POST['ev'], $_SESSION['uuid'], "Misc tool used", "<strong>Confirmation link</strong> was sent to this events phone number.");
        }
    }
    if($_GET['txt'] == 'receipt_link'){
        $event = mysql_fetch_array(mysql_query("SELECT event_phone FROM fmo_locations_events WHERE event_token='".$_POST['ev']."'"));
        if(!empty($event['event_phone'])){
            $confirm = $googer->shorten("https://www.formoversonly.com/dashboard/assets/public/receipt.php?ev=".$_POST['ev']);
            _sendText($event['event_phone'], "[".companyName($_SESSION['cuid'])."]\r\nView your receipt here:\r\n".$confirm);
            timeline_event($_POST['ev'], $_SESSION['uuid'], "Receipt sent", "<strong>Receipt link</strong> was sent to this events phone number.");
        }
    }
    if($_GET['txt'] == 'rates_link'){
        $event = mysql_fetch_array(mysql_query("SELECT event_phone FROM fmo_locations_events WHERE event_token='".$_POST['ev']."'"));
        if(!empty($event['event_phone'])){
            $rates = $googer->shorten("https://www.formoversonly.com/dashboard/assets/public/index.php?e=QuT&ev=".$_POST['ev']);
            _sendText($event['event_phone'], "[".companyName($_SESSION['cuid'])."]\r\nView your customized quote here:\r\n".$rates);
            timeline_event($_POST['ev'], $_SESSION['uuid'], "Misc tool used", "<strong>Rates/Quote</strong> was sent to this events phone number.");
        }
    }
    if($_GET['txt'] == 'estimate_link'){
        $user = mysql_fetch_array(mysql_query("SELECT user_phone FROM fmo_users WHERE user_token='".$_POST['uuid']."'"));
        if(!empty($user['user_phone'])){
            $review_link = $googer->shorten("https://www.formoversonly.com/dashboard/assets/public/estimate.php?jb=emosewa&uuid=".$_POST['uuid']."&n=".struuid(true)."&ev=".$_POST['ev']);
            $msg        = "[".companyName($_SESSION['cuid'])."]\r\nPlease complete this estimate:\r\n".$review_link;
            _sendText($user['user_phone'], $msg);
            timeline_event($_POST['ev'], $_SESSION['uuid'], "Estimate tool used", "<strong>Estimate tool</strong> was sent to <strong>".name($_POST['uuid'])."</strong> to complete for this event.");
        }
    }
    if($_GET['txt'] == 'ckpay'){
        $user = mysql_fetch_array(mysql_query("SELECT event_phone FROM fmo_locations_events WHERE event_token='".$_POST['ev']."'"));
        if(!empty($user['event_phone'])){
            $review_link = $googer->shorten("https://www.formoversonly.com/dashboard/assets/public/index.php?t=MvP&ev=".$_POST['ev']);
            $msg        = "[".companyName($_SESSION['cuid'])."]\r\nPlease pay your bill:\r\n".$review_link;
            _sendText($user['event_phone'], $msg);
            timeline_event($_POST['ev'], $_SESSION['uuid'], "Payment tool used", "<strong>mPay</strong> was sent to this events phone number to request payment of bill.");
        }
    }
}