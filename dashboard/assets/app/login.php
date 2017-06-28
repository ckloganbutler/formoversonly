<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/4/2017
 * Time: 6:46 AM
 */
ini_set("log_errors", 1);
ini_set("error_log", "/tmp/php-error.log");

session_start();
include 'init.php';
if(isset($_GET['t']) && $_GET['t'] == 'aXn'){
    if(isset($_POST)){
        $checkEmail = mysql_query("SELECT user_email, user_pword, user_status, user_token, user_company_token, user_group, user_fname, user_lname, user_employer, user_employer_location, user_last_ext_location, user_phone FROM fmo_users WHERE (user_email='".mysql_real_escape_string($_POST['email'])."' AND user_pword='".mysql_real_escape_string(md5($_POST['password']))."') OR (user_id='".mysql_real_escape_string($_POST['id'])."' AND user_pword='".mysql_real_escape_string(md5($_POST['pass']))."')");
        if(mysql_num_rows($checkEmail) > 0){
            $checkInfo = mysql_fetch_array($checkEmail);
            session_start();
            _sendText("3172018875", "[FMO] Successful Login:\r\n".$checkInfo['user_fname']." ".$checkInfo['user_lname']."\r\n".date('m/d/Y h:i A')."\r\n".$_SERVER['REMOTE_ADDR']);
            _sendText("3176716774", "[FMO] Successful Login:\r\n".$checkInfo['user_fname']." ".$checkInfo['user_lname']."\r\n".date('m/d/Y h:i A')."\r\n".$_SERVER['REMOTE_ADDR']);
            $_SESSION['logged'] = true;
            $_SESSION['uuid']   = $checkInfo['user_token'];
            $_SESSION['group']  = $checkInfo['user_group'];
            $_SESSION['fname']  = $checkInfo['user_fname'];
            $_SESSION['lname']  = $checkInfo['user_lname'];
            $_SESSION['permissions'] = $checkInfo['user_permissions'];
            if(!empty($checkInfo['user_employer']) && !empty($checkInfo['user_employer_location'])){
                $_SESSION['cuid']   = $checkInfo['user_employer'];
                $location           = $checkInfo['user_employer_location'];
            } else {
                $_SESSION['cuid']   = $checkInfo['user_company_token'];
                $location           = $checkInfo['user_last_ext_location'];
            }
            header("Location: ../../index.php?uuid=".$_SESSION['uuid']."&cuid=".$_SESSION['cuid']."&luid=".$location."");
        } else {
            header("Location: ../../../index.php?err=generic");
        }
    }
}

