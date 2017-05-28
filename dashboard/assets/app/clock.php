<?php
/**
 * Created by PhpStorm.
 * User: gameroom
 * Date: 5/24/2017
 * Time: 12:46 PM
 */
session_start();
include 'init.php';

if(isset($_GET['clock'])){
    $user       = $_SESSION['uuid'];
    $company    = $_SESSION['cuid'];
    $ip         = $_SERVER['REMOTE_ADDR'];
    $location   = $_GET['luid'];
    if($_GET['clock'] == 'in'){
        $in = mysql_query("SELECT timeclock_id FROM fmo_users_employee_timeclock WHERE timeclock_user='".mysql_real_escape_string($user)."' AND timeclock_complete=0");
        if(mysql_num_rows($in) == 0){
            $date_in = date('Y-m-d H:i:s');
            mysql_query("INSERT INTO fmo_users_employee_timeclock (timeclock_user, timeclock_company_token, timeclock_location_token, timeclock_ip, timeclock_clockin, timeclock_complete) VALUES (
            '".mysql_real_escape_string($user)."',
            '".mysql_real_escape_string($company)."',
            '".mysql_real_escape_string($location)."',
            '".mysql_real_escape_string($ip)."',
            '".mysql_real_escape_string($date_in)."',
            '".mysql_real_escape_string(0)."')");
        } else {
            // user already clocked in, needs to clock-out before clocking in again.
            header("HTTP/1.0 500 Internal Server Error");
        }
    }elseif($_GET['clock'] == 'out'){
        $out = mysql_query("SELECT timeclock_id, timeclock_clockin FROM fmo_users_employee_timeclock WHERE timeclock_user='".mysql_real_escape_string($user)."' AND timeclock_complete=0") or die(mysql_error());
        if(mysql_num_rows($out) > 0){
            $clock    = mysql_fetch_array($out);
            $date_out = date('Y-m-d H:i:s');
            $date_in  = $clock['timeclock_clockin'];
            $date1 = new DateTime($date_in);
            $date2 = new DateTime($date_out);
            $diff = $date2->diff($date1);
            $hours   = $diff->h;
            $minutes += $diff->i;
            $seconds += $diff->s;
            $hours   = $hours + ($diff->days*24);
            $worked = decimalHours($hours.":".$minutes.":".$seconds);
            mysql_query("UPDATE fmo_users_employee_timeclock SET timeclock_clockout='".mysql_real_escape_string($date_out)."', timeclock_hours='".mysql_real_escape_string($worked)."', timeclock_complete=1 WHERE timeclock_id='".mysql_real_escape_string($clock['timeclock_id'])."'") or die(mysql_error());
            echo $worked;
        } else {
            // user already clocked out. Clock-in to continue using the clock-in system.
            header("HTTP/1.0 500 Internal Server Error");
        }
    }
}