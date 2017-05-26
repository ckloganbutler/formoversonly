<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/24/2017
 * Time: 1:27 AM
 */
session_start();
include '../init.php';

if(isset($_SESSION['uuid'])){
    if($_GET['p'] == 'jvr'){

    }
    if($_GET['p'] == 'jre'){
        $customers = mysql_query("SELECT user_token, user_phone, user_lname, user_fname FROM fmo_users WHERE user_phone='".mysql_real_escape_string(preg_replace('/[^A-Za-z0-9]/', '', $_POST['phone']))."' LIMIT 1");
        if(mysql_num_rows($customers) > 0){
            $customer = mysql_fetch_array($customers); echo $customer['user_token'];
        } else {
            header("HTTP/1.0 500 Internal Server Error");
        }
    }
    if($_GET['p'] == 'jdk'){
        $code = substr($_POST['zipcode'], 0, 3);
        $location_tokens = mysql_query("SELECT location_token FROM fmo_locations WHERE location_owner_company_token='".mysql_real_escape_string($_SESSION['cuid'])."'");

        if(mysql_num_rows($location_tokens) > 0){
            while($location = mysql_fetch_assoc($location_tokens)){
                $location_zipcode_check = mysql_query("SELECT zipcode_location_token FROM fmo_locations_zipcodes WHERE zipcode_location_token='".mysql_real_escape_string($location['location_token'])."' AND zipcode_code='".mysql_real_escape_string($code)."'");
                if(mysql_num_rows($location_zipcode_check) > 0){
                    echo $location['location_token'];
                }
            }
        }
    }
    if($_GET['p'] == 'jvk'){
        $field = $_POST['f'];
        $day   = explode("_", $field, 15);
        $col   = "fmo_locations_rates_".$day[0];
        $tok   = $day[0]."_location_token";
        $value = $_POST['v'];
        $loc   = $_POST['l'];

        $check_fees = mysql_query("SELECT ".mysql_real_escape_string($field)." FROM ".mysql_real_escape_string($col)." WHERE ".mysql_real_escape_string($tok)."='".mysql_real_escape_string($loc)."'") or die(mysql_error());
        if(mysql_num_rows($check_fees) > 0){
            mysql_query("UPDATE ".mysql_real_escape_string($col)." SET ".mysql_real_escape_string($field)."=".mysql_real_escape_string($value)." WHERE ".mysql_real_escape_string($tok)."='".mysql_real_escape_string($loc)."'") or die(mysql_error());
            echo json_encode(false);
        } else {
            mysql_query("INSERT INTO fmo_locations_rates_".mysql_real_escape_string($day[0])." (".mysql_real_escape_string($field).", $day[0]_location_token) VALUES ('".mysql_real_escape_string($value)."', '".mysql_real_escape_string($loc)."')");
            echo json_encode(true);
        }
    }
    if($_GET['p'] == 'jkv'){
        $days = array(0 => "sunday", 1 => "monday", 2 => "tuesday", 3 => "wednesday", 4 => "thursday", 5 => "friday", 6 => "saturday");
        $col   = "fmo_locations_rates_".$days[$_POST['day']];
        $tok   = $days[$_POST['day']]."_location_token";
        $loc   = $_GET['luid'];
        $math  = array();

        $find_fees = mysql_query("SELECT ".mysql_real_escape_string($days[$_POST['day']])."_truck_fee, ".mysql_real_escape_string($days[$_POST['day']])."_labor_rate, ".mysql_real_escape_string($days[$_POST['day']])."_truck_rate, ".mysql_real_escape_string($days[$_POST['day']])."_upcharge FROM fmo_locations_rates_".mysql_real_escape_string($days[$_POST['day']])." WHERE ".mysql_real_escape_string($days[$_POST['day']])."_location_token='".mysql_real_escape_string($loc)."'");
        if(mysql_num_rows($find_fees) > 0){
            $fees = mysql_fetch_array($find_fees);
            $math['truck_fee']        = $fees[$days[$_POST['day']].'_truck_fee'];
            $math['labor_rate']       = $fees[$days[$_POST['day']].'_labor_rate'];
            $math['truck_rate']       = $fees[$days[$_POST['day']].'_truck_rate'];
            $math['upcharge']         = $fees[$days[$_POST['day']].'_upcharge'];
            if($fees[$days[$_POST['day']].'_upcharge'] > 0){
                $math['total_labor_rate'] = ($fees[$days[$_POST['day']].'_labor_rate'] * 2) + ($fees[$days[$_POST['day']].'_truck_rate'] * 1) + $fees[$days[$_POST['day']].'_upcharge'];
            } else {
                $math['total_labor_rate'] = ($fees[$days[$_POST['day']].'_labor_rate'] * 2) + ($fees[$days[$_POST['day']].'_truck_rate'] * 1);
            }
            $math['county_fee']       = ($math['total_labor_rate'] * .5) * 0;
            echo json_encode($math);
        }
    }
    if($_GET['p'] == 'doMath'){
        $days = array(0 => "sunday", 1 => "monday", 2 => "tuesday", 3 => "wednesday", 4 => "thursday", 5 => "friday", 6 => "saturday");
        $col   = "fmo_locations_rates_".$days[$_POST['day']];
        $tok   = $days[$_POST['day']]."_location_token";
        $loc   = $_GET['luid'];
        $math  = array();
        $a = $_POST['a'];
        $b = $_POST['b'];
        $c = $_POST['c'];

        $find_fees = mysql_query("SELECT ".mysql_real_escape_string($days[$_POST['day']])."_truck_fee, ".mysql_real_escape_string($days[$_POST['day']])."_labor_rate, ".mysql_real_escape_string($days[$_POST['day']])."_truck_rate, ".mysql_real_escape_string($days[$_POST['day']])."_upcharge FROM fmo_locations_rates_".mysql_real_escape_string($days[$_POST['day']])." WHERE ".mysql_real_escape_string($days[$_POST['day']])."_location_token='".mysql_real_escape_string($loc)."'");
        if(mysql_num_rows($find_fees) > 0){
            $fees = mysql_fetch_array($find_fees);
            $math['truck_fee']        = $fees[$days[$_POST['day']].'_truck_fee'] * $a;
            $math['labor_rate']       = $fees[$days[$_POST['day']].'_labor_rate'];
            $math['truck_rate']       = $fees[$days[$_POST['day']].'_truck_rate'];

            $math['upcharge']         = $fees[$days[$_POST['day']].'_upcharge'];
            if($fees[$days[$_POST['day']].'_upcharge'] > 0){
                $math['total_labor_rate'] = ($fees[$days[$_POST['day']].'_labor_rate'] * $b) + ($fees[$days[$_POST['day']].'_truck_rate'] * $a) + $fees[$days[$_POST['day']].'_upcharge'];
            } else {
                $math['total_labor_rate'] = ($fees[$days[$_POST['day']].'_labor_rate'] * $b) + ($fees[$days[$_POST['day']].'_truck_rate'] * $a);
            }
            $math['county_fee']       = ($math['total_labor_rate'] * .5) * $c;
            echo json_encode($math);
        }
    }
}

