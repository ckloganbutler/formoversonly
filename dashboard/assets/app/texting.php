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

        if(_sendText($phone, $msg)){
            echo "success";
        } else {
            echo "failed | ".$phone." | ".$msg;

        }
    }
}