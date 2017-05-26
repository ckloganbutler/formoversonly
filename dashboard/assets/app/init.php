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

function name($token){
    $name = mysql_fetch_array(mysql_query("SELECT user_fname, user_lname FROM fmo_users WHERE user_token='".mysql_real_escape_string($token)."'"));
    return $name['user_fname']." ".$name['user_lname'];
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