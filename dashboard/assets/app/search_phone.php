<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/4/2017
 * Time: 5:19 AM
 */
include 'init.php';
$taken = true;
if(isset($_GET['phone'])) {
    $user_phone = $_GET['phone'];
    $query = mysql_query("SELECT user_phone FROM fmo_users WHERE user_phone ='" . mysql_real_escape_string($user_phone) . "'");
    $found = mysql_num_rows($query);

    header('content-type: application/json; charset=utf-8');
    if ($found > 0) {
        echo json_encode(false);
    } else {
        echo json_encode(true);
    }
}