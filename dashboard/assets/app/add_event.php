<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 5/4/2017
 * Time: 10:38 PM
 */
include 'init.php';
if(isset($_GET['ev']) && $_GET['ev'] == 'plk'){
    $token      = struuid(true);
    $loc        = $_GET['luid'];
    $usr        = $_GET['uuid'];
    $name       = sentence_case($_POST['name']);
    $start      = date('Y-m-d', strtotime($_POST['startdate']));
    $end        = date('Y-m-d', strtotime($_POST['enddate']));
    $time       = $_POST['time'];
    $type       = $_POST['type'];
    $subtype    = $_POST['subtype'];
    $status     = 1;

    mysql_query("INSERT INTO fmo_locations_events (event_token, event_location_token, event_user_token, event_date_start, event_date_end, event_time, event_name, event_type, event_subtype, event_status) VALUES (
    '".mysql_real_escape_string($token)."',
    '".mysql_real_escape_string($loc)."',
    '".mysql_real_escape_string($usr)."',
    '".mysql_real_escape_string($start)."',
    '".mysql_real_escape_string($end)."',
    '".mysql_real_escape_string($time)."',
    '".mysql_real_escape_string($name)."',
    '".mysql_real_escape_string($type)."',
    '".mysql_real_escape_string($subtype)."',
    '".mysql_real_escape_string($status)."')");

}
if(isset($_GET['ev']) && $_GET['ev'] == 'pmk'){
    $token     = $_GET['e'];
    $type      = $_POST['type'];
    $address   = $_POST['address'];
    $address2  = $_POST['address2'];
    $suite     = $_POST['suite'];
    $city      = $_POST['city'];
    $state     = $_POST['state'];
    $zip       = $_POST['zip'];
    $intersec  = $_POST['closest_intersection'];
    $county    = $_POST['county'];
    $special   = $_POST['special'];
    $squareft  = $_POST['square_footage'];
    $bedrooms  = $_POST['bedrooms'];
    $garage    = $_POST['garage'];
    $stairs    = $_POST['stairs'];
    $distance  = $_POST['distance'];
    $comments  = $_POST['comments'];

    mysql_query("INSERT INTO fmo_locations_events_addresses (address_event_token, address_type, address_address, address_address2, address_suite, address_city, address_state, address_zip, address_closest_intersection, address_county, address_special, address_square_footage, address_bedrooms, address_garage, address_stairs, address_distance, address_comments) VALUES (
    '".mysql_real_escape_string($token)."',
    '".mysql_real_escape_string($type)."',
    '".mysql_real_escape_string($address)."',
    '".mysql_real_escape_string($address2)."',
    '".mysql_real_escape_string($suite)."',
    '".mysql_real_escape_string($city)."',
    '".mysql_real_escape_string($state)."',
    '".mysql_real_escape_string($zip)."',
    '".mysql_real_escape_string($intersec)."',
    '".mysql_real_escape_string($county)."',
    '".mysql_real_escape_string($special)."',
    '".mysql_real_escape_string($squareft)."',
    '".mysql_real_escape_string($bedrooms)."',
    '".mysql_real_escape_string($garage)."',
    '".mysql_real_escape_string($stairs)."',
    '".mysql_real_escape_string($distance)."',
    '".mysql_real_escape_string($comments)."')");
}