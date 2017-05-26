<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/4/2017
 * Time: 8:38 AM
 */
session_start();
include 'init.php';

if(isset($_FILES['image'])){
    $token = $_GET['uuid'];
    $fileName  = struuid();
    $file_ext = substr($_FILES['image']['name'], strripos($_FILES['image']['name'], '.'));
    $uploaddir = '../upload/pp/';
    $uploadfile = $uploaddir . $fileName;

    move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile . $file_ext);
    $teaserLink = "//www.formoversonly.com/dashboard/assets/upload/pp/". $fileName . $file_ext;

    $check   = mysql_query("SELECT user_pic FROM fmo_users WHERE user_token='".mysql_real_escape_string($token)."'");
    $checked = mysql_num_rows($check);

    if($checked > 0){
        mysql_query("UPDATE fmo_users SET user_pic='".mysql_real_escape_string($teaserLink)."' WHERE user_token='".mysql_real_escape_string($token)."'");
        echo $teaserLink;
    }
}
