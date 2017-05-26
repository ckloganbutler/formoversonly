<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/4/2017
 * Time: 5:19 AM
 */
session_start();
include 'init.php';
$taken = true;
if(isset($_SESSION['uuid'])){
    $user_token = $_SESSION['uuid'];
    $query = mysql_query("SELECT user_id FROM fmo_users WHERE user_token ='".mysql_real_escape_string($user_token)."' AND user_pword='".mysql_real_escape_string($_GET['c'])."'");
    $found = mysql_num_rows($query);


    header('content-type: application/json; charset=utf-8');
    if ($found > 0) {
        echo json_encode(false);
    } else {
        echo json_encode(true);
    }

}