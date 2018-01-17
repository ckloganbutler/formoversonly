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
        $checkEmail = mysql_query("SELECT user_email, user_pword, user_status, user_token, user_company_token, user_group, user_fname, user_lname, user_employer, user_employer_location, user_last_ext_location, user_phone, user_creator, user_permissions, user_esc_permissions FROM fmo_users WHERE (user_email='".mysql_real_escape_string($_POST['email'])."' AND user_pword='".mysql_real_escape_string(md5($_POST['password']))."') OR (user_id='".mysql_real_escape_string($_POST['email'])."' AND user_pword='".mysql_real_escape_string(md5($_POST['password']))."')");
        if(mysql_num_rows($checkEmail) > 0){
            if($checkInfo['user_status'] != 1){
                $checkInfo = mysql_fetch_array($checkEmail);
                session_start();
                if($checkInfo['user_group'] == 3){ $type = "- C"; } else { $type = "- E"; }
                _sendText("3172018875", "[FMO] Successful Login:\r\n".$checkInfo['user_fname']." ".$checkInfo['user_lname']." ".$type."\r\n".date('m/d/Y h:i A')."\r\n".$_SERVER['REMOTE_ADDR']);
                _sendText("3176716774", "[FMO] Successful Login:\r\n".$checkInfo['user_fname']." ".$checkInfo['user_lname']." ".$type."\r\n".date('m/d/Y h:i A')."\r\n".$_SERVER['REMOTE_ADDR']);
                $_SESSION['logged']      = true;
                $_SESSION['uuid']        = $checkInfo['user_token'];
                $_SESSION['group']       = $checkInfo['user_group'];
                $_SESSION['fname']       = $checkInfo['user_fname'];
                $_SESSION['lname']       = $checkInfo['user_lname'];
                $_SESSION['permissions'] = $checkInfo['user_permissions'];
                $_SESSION['esc_perms']   = $checkInfo['user_esc_permissions'];
                if($checkInfo['user_group'] == 1) {
                    $_SESSION['cuid']   = $checkInfo['user_company_token'];
                    $location           = $checkInfo['user_last_ext_location'];
                } elseif($checkInfo['user_group'] == 3){
                    $_SESSION['cuid']   = $checkInfo['user_creator'];
                    $location           = $checkInfo['user_last_ext_location'];
                } elseif(!empty($checkInfo['user_employer']) && !empty($checkInfo['user_employer_location']) && $user['user_group'] != 3){
                    $_SESSION['cuid']   = $checkInfo['user_employer'];
                    $location           = $checkInfo['user_employer_location'];
                    $_SESSION['luid']   = $checkInfo['user_employer_location'];
                }
                mysql_query("UPDATE fmo_users SET user_last_login='".date('m/d/Y H:i:s A')."', user_last_ext_date='".mysql_real_escape_string(date('Y-m-d'))."' WHERE user_token='".$checkInfo['user_token']."'");
                header("Location: ../../?luid=".$location."");
            } else {
                header("Location: ../../../?err=generic");
            }
        } else {
            header("Location: ../../../?err=generic");
        }
    }
}

