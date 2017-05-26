<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/9/2017
 * Time: 2:46 AM
 */
session_start();
include 'init.php';

if(isset($_GET['setting'])){
    if($_GET['setting'] == 'usr_advance'){
        $token      = $_GET['uuid'];
        $advance    = $_POST['requested'];
        $available  = $_POST['available'];
        $reasoning  = $_POST['reasoning'];
        $by         = $_SESSION['uuid'];

        mysql_query("INSERT INTO fmo_users_employee_advances (advance_user_token, advance_requested, advance_available, advance_reason, advance_by_user_token) VALUES (
        '".mysql_real_escape_string($token)."',
        '".mysql_real_escape_string($advance)."',
        '".mysql_real_escape_string($available)."',
        '".mysql_real_escape_string($reasoning)."',
        '".mysql_real_escape_string($by)."')");
        timeline_log($token, $by, "Advance", name($by)." authorized loan for: $<strong>".$advance."</strong>/$<strong>".$available."</strong>");

        $user_pay = mysql_fetch_array(mysql_query("SELECT user_employer_rate FROM fmo_users WHERE user_token='".mysql_real_escape_string($token)."'"));
        if($user_pay['user_employer_rate'] > 0){
            $refStart                = new DateTime('2017-01-02');
            $periodLength            = 14;
            $now                     = new DateTime();
            $cur                     = date('Y-m-d');
            $daysIntoCurrentPeriod   = (int)$now->diff($refStart)->format('%a') % $periodLength;
            $currentPeriodStart      = clone $now;
            $currentPeriodStart->sub(new DateInterval('P'.$daysIntoCurrentPeriod.'D'));
            $start                   = date("Y-m-d", strtotime($cur." -".$daysIntoCurrentPeriod." days"));
            $end                     = date('Y-m-d', strtotime($start." +14 days"));
            $hours = array();
            $prev  = mysql_query("
                            SELECT advance_requested FROM fmo_users_employee_advances
                            WHERE (advance_timestamp>='".mysql_real_escape_string($start)."' AND advance_timestamp<'".mysql_real_escape_string($end)."') AND advance_user_token='".mysql_real_escape_string($token)."'");
            $hours = mysql_query("
                            SELECT timeclock_user, timeclock_hours FROM fmo_users_employee_timeclock 
                            WHERE (timeclock_clockout>='".mysql_real_escape_string($start)."' AND timeclock_clockout<'".mysql_real_escape_string($end)."') AND timeclock_user='".mysql_real_escape_string($token)."'") or die(mysql_error());
            $pay = array();
            if(mysql_num_rows($hours) > 0){
                while($work = mysql_fetch_assoc($hours)){
                    $pay['hours']+=$work['timeclock_hours'];
                }
                if($pay['hours'] > 0){
                    $pay['rate']      = $user_pay['user_employer_rate'];
                    $pay['earned']    = $pay['hours'] * $user_pay['user_employer_rate'];
                    if(mysql_num_rows($prev) > 0){
                        while($loans = mysql_fetch_assoc($prev)){
                            $pay['loans'] += $loans['advance_requested'];
                        }
                    } else {$pay['loans'] = 0;}
                    $pay['available'] = ($pay['earned'] - $pay['loans']) * .25;
                } else {
                    $pay['available'] = 0;
                    $pay['hours']     = 0;
                    $pay['earned']    = 0;
                }
            } else {
                $pay['available'] = 0;
                $pay['hours']     = 0;
                $pay['earned']    = 0;
            }
        } else {
            $pay['available'] = 0;
            $pay['hours']     = 0;
            $pay['earned']    = 0;
        }
        echo json_encode($pay);
    }
    if($_GET['setting'] == 'usr_writeup'){
        $token      = $_GET['uuid'];
        $writeup    = $_POST['reasoning'];
        $action     = $_POST['action'];
        $by         = $_SESSION['uuid'];

        mysql_query("INSERT INTO fmo_users_employee_writeups (writeup_user_token, writeup_reasoning, writeup_action, writeup_by_user_token) VALUES (
        '".mysql_real_escape_string($token)."',
        '".mysql_real_escape_string($writeup)."',
        '".mysql_real_escape_string($action)."',
        '".mysql_real_escape_string($by)."')");
        timeline_log($token, $by, "Write-up", name($by)." wrote up ".name($token).": <strong>".$writeup."</strong> (Action taken: <strong>".$action."</strong>)");
    }
    if($_GET['setting'] == 'usr_cmt'){
        $token      = $_GET['uuid'];
        $comment    = $_POST['comment'];
        $by         = $_SESSION['uuid'];

        mysql_query("INSERT INTO fmo_users_employee_comments (comment_user_token, comment_comment, comment_by_user_token) VALUES (
        '".mysql_real_escape_string($token)."',
        '".mysql_real_escape_string($comment)."',
        '".mysql_real_escape_string($by)."')");
        timeline_log($token, $by, "Comment", name($by)." left a comment on ".name($token)."'s profile: <strong>".$comment."</strong>");
    }
    if($_GET['setting'] == 'service_rates'){
        $location_token = $_GET['luid'];
        $item           = sentence_case($_POST['item']);
        $saleprice      = $_POST['saleprice'];
        $cost           = $_POST['cost'];
        $taxable        = $_POST['taxable'];
        $commissionable = $_POST['commissionable'];
        $type           = $_POST['type'];

        mysql_query("INSERT INTO fmo_services (services_location_token, services_item, services_saleprice, services_cost, services_taxable, services_commissionable, services_type) VALUES (
        '".mysql_real_escape_string($location_token)."',
        '".mysql_real_escape_string($item)."',
        '".mysql_real_escape_string($saleprice)."',
        '".mysql_real_escape_string($cost)."',
        '".mysql_real_escape_string($taxable)."',
        '".mysql_real_escape_string($commissionable)."',
        '".mysql_real_escape_string($type)."')");
        echo $item;
    }
    if($_GET['setting'] == 'vendor'){
        $location_token = $_GET['luid'];
        $active         = $_POST['active'];
        $name           = sentence_case($_POST['name']);
        $type           = $_POST['type'];
        $phone          = $_POST['phone'];
        $contact        = $_POST['contact'];
        $account_ref    = $_POST['account_ref'];
        $extra_ref      = $_POST['extra_ref'];

        mysql_query("INSERT INTO fmo_locations_vendors (vendor_location_token, vendor_active, vendor_name, vendor_type, vendor_phone, vendor_contact, vendor_account_ref, vendor_extra_ref) VALUES (
        '".mysql_real_escape_string($location_token)."',
        '".mysql_real_escape_string($active)."',
        '".mysql_real_escape_string($name)."',
        '".mysql_real_escape_string($type)."',
        '".mysql_real_escape_string($phone)."',
        '".mysql_real_escape_string($contact)."',
        '".mysql_real_escape_string($account_ref)."',
        '".mysql_real_escape_string($extra_ref)."')");
        echo $name;
    }
    if($_GET['setting'] == 'service_storage'){
        $location_token = $_GET['luid'];
        $available      = $_POST['available'];
        $unit           = $_POST['unit'];
        $priceperiod    = $_POST['priceperiod'];

        mysql_query("INSERT INTO fmo_locations_storages (storage_location_token, storage_available, storage_unit, storage_priceperiod) VALUES (
        '".mysql_real_escape_string($location_token)."',
        '".mysql_real_escape_string($available)."',
        '".mysql_real_escape_string($unit)."',
        '".mysql_real_escape_string($priceperiod)."')");
        echo $unit;
    }
    if($_GET['setting'] == 'service_county'){
        $location_token = $_GET['luid'];
        $name           = sentence_case($_POST['county']);

        mysql_query("INSERT INTO fmo_locations_counties (county_location_token, county_name) VALUES (
        '".mysql_real_escape_string($location_token)."',
        '".mysql_real_escape_string($name)."')");
        echo $name;
    }
    if($_GET['setting'] == 'times'){
        $location_token = $_GET['luid'];
        $start           = $_POST['starttime'];
        $end             = $_POST['endtime'];

        mysql_query("INSERT INTO fmo_locations_times (time_location_token, time_start, time_end) VALUES (
        '".mysql_real_escape_string($location_token)."',
        '".mysql_real_escape_string($start)."',
        '".mysql_real_escape_string($end)."')");
        echo $start." to ".$end;
    }
    if($_GET['setting'] == 'eventtype'){
        $location_token = $_GET['luid'];
        $name           = sentence_case($_POST['eventtype']);

        mysql_query("INSERT INTO fmo_locations_eventtypes (eventtype_location_token, eventtype_name) VALUES (
        '".mysql_real_escape_string($location_token)."',
        '".mysql_real_escape_string($name)."')");
        echo $name;
    }
    if($_GET['setting'] == 'subtype'){
        $location_token = $_GET['luid'];
        $name           = sentence_case($_POST['subtype']);

        mysql_query("INSERT INTO fmo_locations_subtypes (subtype_location_token, subtype_name) VALUES (
        '".mysql_real_escape_string($location_token)."',
        '".mysql_real_escape_string($name)."')");
        echo $name;
    }
    if($_GET['setting'] == 'howhear'){
        $location_token = $_GET['luid'];
        $hear           = sentence_case($_POST['hear']);

        mysql_query("INSERT INTO fmo_locations_howhears (howhear_location_token, howhear_name) VALUES (
        '".mysql_real_escape_string($location_token)."',
        '".mysql_real_escape_string($hear)."')");
        echo $hear;
    }
    if($_GET['setting'] == 'service_zipcode'){
        $location_token = $_GET['luid'];
        $code           = $_POST['code'];

        mysql_query("INSERT INTO fmo_locations_zipcodes (zipcode_location_token, zipcode_code) VALUES (
        '".mysql_real_escape_string($location_token)."',
        '".mysql_real_escape_string($code)."')");
        echo $code;
    }
}