<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/12/2017
 * Time: 8:45 PM
 */
session_start();
include 'init.php';

if(isset($_POST)){
    $user_token          = struuid();
    $user_owner_token    = $_SESSION['uuid'];
    $user_owner_company  = $_SESSION['cuid'];
    $user_name           = $_POST['name'];
    $user_address        = $_POST['address'];
    $user_city           = $_POST['city'];
    $user_state          = $_POST['state'];
    $user_zip            = $_POST['zip'];
    mysql_query("INSERT INTO fmo_users (user_token, user_owner_token, user_fname, user_address, user_city, user_state, user_zip) VALUES (
    '".mysql_real_escape_string($user_token)."',
    '".mysql_real_escape_string($user_owner_company)."',
    '".mysql_real_escape_string($user_name)."',
    '".mysql_real_escape_string($user_address)."',
    '".mysql_real_escape_string($user_city)."',
    '".mysql_real_escape_string($user_state)."',
    '".mysql_real_escape_string($user_zip)."')");
} else {die();}