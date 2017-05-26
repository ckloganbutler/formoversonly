<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/5/2017
 * Time: 5:23 PM
 */
session_start();
include 'init.php';

if(isset($_POST)){
    $location_token          = struuid();
    $location_owner_token    = $_SESSION['uuid'];
    $location_owner_company  = $_SESSION['cuid'];
    $location_name           = $_POST['name'];
    $location_address        = $_POST['address'];
    $location_city           = $_POST['city'];
    $location_state          = $_POST['state'];
    $location_zip            = $_POST['zip'];
    mysql_query("INSERT INTO fmo_locations (location_token, location_owner_token, location_owner_company_token, location_name, location_address, location_city, location_state, location_zip) VALUES (
    '".mysql_real_escape_string($location_token)."',
    '".mysql_real_escape_string($location_owner_token)."',
    '".mysql_real_escape_string($location_owner_company)."',
    '".mysql_real_escape_string($location_name)."',
    '".mysql_real_escape_string($location_address)."',
    '".mysql_real_escape_string($location_city)."',
    '".mysql_real_escape_string($location_state)."',
    '".mysql_real_escape_string($location_zip)."')");
} else {die();}