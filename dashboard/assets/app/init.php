<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/4/2017
 * Time: 5:19 AM
 */
mysql_connect("localhost", "root", "lolyouhaha65") or die(mysql_error());
mysql_select_db("fmo") or die(mysql_error());

require 'obj/misc.php';
require 'obj/send_email.php';
require 'obj/send_text.php';

function timeline_log($who, $by, $type, $reasoning){
    mysql_query("INSERT INTO fmo_users_employee_timelines (timeline_user_token, timeline_datatype, timeline_reasoning, timeline_by_user_token) VALUES (
    '".mysql_real_escape_string($who)."',
    '".mysql_real_escape_string($type)."',
    '".mysql_real_escape_string($reasoning)."',
    '".mysql_real_escape_string($by)."')");
}

function timeline_event($event, $by, $type, $reasoning){
    mysql_query("INSERT INTO fmo_locations_events_timelines (timeline_event_token, timeline_type, timeline_reasoning, timeline_by_user_token) VALUES (
    '".mysql_real_escape_string($event)."',
    '".mysql_real_escape_string($type)."',
    '".mysql_real_escape_string($reasoning)."',
    '".mysql_real_escape_string($by)."')");
}

function name($token){
    $name = mysql_fetch_array(mysql_query("SELECT user_fname, user_lname FROM fmo_users WHERE user_token='".mysql_real_escape_string($token)."'"));
    return $name['user_fname']." ".$name['user_lname'];
}
function nameByLast($token){
    $name = mysql_fetch_array(mysql_query("SELECT user_fname, user_lname FROM fmo_users WHERE user_token='".mysql_real_escape_string($token)."'"));
    return $name['user_lname'].", ".$name['user_fname'];
}

function decimalHours($time){
    $hms = explode(":", $time);
    return number_format(($hms[0] + ($hms[1]/60) + ($hms[2]/3600)), 2);
}

function hasPermission($action){
    $permissions = explode("|", $_SESSION['permissions']);

    if(array_search($action, $permissions)){
        return true;
    } else { return false; }
}

function getBroadcast($company) {
    $cast = mysql_fetch_array(mysql_query("SELECT user_broadcast, user_broadcast_timestamp FROM fmo_users WHERE user_company_token='".$company."'"));

    $broadcast = array();
    $broadcast['message'] = $cast['user_broadcast'];
    $broadcast['time']    = $cast['user_broadcast_timestamp'];

    return $broadcast;
}

function picture($token){
    $pic = mysql_fetch_array(mysql_query("SELECT user_pic FROM fmo_users WHERE user_token='".mysql_real_escape_string($token)."'"));

    if(empty($pic['user_pic'])){
        return 'assets/admin/layout/img/default.png';
    } else {
        return $pic['user_pic'];
    }

}
function phone($token){
    $phone = mysql_fetch_array(mysql_query("SELECT user_phone FROM fmo_users WHERE user_token='".mysql_real_escape_string($token)."'"));

    if(empty($phone['user_phone'])){
        return 'N/A';
    } else {
        return clean_phone($phone['user_phone']);
    }
}

function companyPhone($cuid){
    $phone = mysql_fetch_array(mysql_query("SELECT user_phone FROM fmo_users WHERE user_company_token='".mysql_real_escape_string($cuid)."'"));
    if(!empty($phone['user_phone'])){
        echo $phone['user_phone'];
    } else {
        return NULL;
    }
}

function companyName($cuid){
    $name = mysql_fetch_array(mysql_query("SELECT user_company_name FROM fmo_users WHERE user_company_token='".mysql_real_escape_string($cuid)."'"));
    if(!empty($name['user_company_name'])){
        echo $name['user_company_name'];
    } else {
        return NULL;
    }
}

function locationName($token){
    $name = mysql_fetch_array(mysql_query("SELECT location_name FROM fmo_locations WHERE location_token='".mysql_real_escape_string($token)."'"));
    if(!empty($name['location_name'])){
        echo $name['location_name'];
    } else {
        return NULL;
    }
}
function locationName2($token){
    $name = mysql_fetch_array(mysql_query("SELECT location_name FROM fmo_locations WHERE location_token='".mysql_real_escape_string($token)."'"));
    if(!empty($name['location_name'])){
        return $name['location_name'];
    } else {
        return NULL;
    }
}

function locationAddress($token){
    $address = mysql_fetch_array(mysql_query("SELECT location_address, location_city, location_state, location_zip FROM fmo_locations WHERE location_token='".mysql_real_escape_string($token)."'"));
    if(!empty($address['location_address'])){
        echo $address['location_address'].", ".$address['location_city'].", ".$address['location_state'].", ".$address['location_zip'];
    } else {
        return NULL;
    }
}

function locationManagerPhone($token){
    $manager = mysql_fetch_array(mysql_query("SELECT location_manager FROM fmo_locations WHERE location_token='".mysql_real_escape_string($token)."'"));
    if(!empty($manager['location_manager'])){
        $phone = mysql_fetch_array(mysql_query("SELECT user_phone FROM fmo_users WHERE user_token='".mysql_real_escape_string($manager['location_manager'])."'"));
        if(!empty($phone['user_phone'])){
            echo $phone['user_phone'];
        } else {
            return NULL;
        }
    } else {
        return NULL;
    }
}

function nameFromEvent($token){
    $name = mysql_fetch_array(mysql_query("SELECT user_fname, user_lname FROM fmo_users WHERE user_token='".mysql_real_escape_string($token)."'"));
    return $name['user_fname']." ".$name['user_lname'];
}