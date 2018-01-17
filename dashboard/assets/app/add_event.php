<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 5/4/2017
 * Time: 10:38 PM
 */
session_start();
include 'init.php';

$key = 'AIzaSyDxLua1UKdf-637NvG5NgBuhb0DYVQ77cg';
$googer = new GoogleUrlApi($key);
if(isset($_GET['ev']) && $_GET['ev'] == 'plk'){
    $token      = struuid(true);
    $loc        = (empty($_GET['luid'])) ? $_POST['location'] : $_GET['luid'];
    $cuid       = (empty($_GET['cuid'])) ? $_SESSION['cuid']  : $_GET['cuid'];
    $usr        = $_GET['uuid'];
    $phone      = preg_replace('/[^A-Za-z0-9\-]/', '', $_POST['phone']);
    $email      = strtolower($_POST['email']);
    $name       = sentence_case($_POST['name'])."'s move";
    $start      = date('Y-m-d', strtotime($_POST['date']));
    $end        = date('Y-m-d', strtotime($_POST['date']));
    $time       = $_POST['time'];
    $zip        = $_POST['catcher_zipcode'];
    $truckfee   = (empty($_POST['truckfee'])) ? 1 : $_POST['truckfee'];
    $laborrate  = (empty($_POST['laborrate'])) ? 2 : $_POST['laborrate'];
    $countyfee  = $_POST['countyfee'];
    $type       = (empty($_POST['type'])) ? $_POST['type'] : "Local";
    $subtype    = (empty($_POST['subtype'])) ? $_POST['subtype'] : "Move";
    $referer    = (empty($_POST['referer'])) ? "Web Lead" : $_POST['referer'];
    $comments   = $_POST['comments'];
    $by_user    = $_SESSION['uuid'];
    $days = array(0 => "sunday", 1 => "monday", 2 => "tuesday", 3 => "wednesday", 4 => "thursday", 5 => "friday", 6 => "saturday");
    $col   = "fmo_locations_rates_".$days[date('w', strtotime($start))];
    $tok   = $days[date('w', strtotime($start))]."_location_token";
    $find_fees = mysql_query("SELECT ".mysql_real_escape_string($days[date('w', strtotime($start))])."_truck_fee, ".mysql_real_escape_string($days[date('w', strtotime($start))])."_labor_rate, ".mysql_real_escape_string($days[date('w', strtotime($start))])."_truck_rate, ".mysql_real_escape_string($days[date('w', strtotime($start))])."_upcharge FROM fmo_locations_rates_".mysql_real_escape_string($days[date('w', strtotime($start))])." WHERE ".mysql_real_escape_string($days[date('w', strtotime($start))])."_location_token='".mysql_real_escape_string($loc)."'");
    if(mysql_num_rows($find_fees) > 0){
        $fees = mysql_fetch_array($find_fees);
        $truckfee_rate = $fees[$days[date('w', strtotime($start))]."_truck_fee"];
        $laborrate_rate = $fees[$days[date('w', strtotime($start))]."_labor_rate"];
        $truckrate_rate = $fees[$days[date('w', strtotime($start))]."_truck_rate"];
        $weekend_upcharge = $fees[$days[date('w', strtotime($start))]."_upcharge"];
    }
    foreach($_POST['addition'] as $ck){
        $additions .= "".$ck."|";
    }
    mysql_query("INSERT INTO fmo_locations_events (event_token, event_by_user_token, event_location_token, event_user_token, event_company_token, event_date_start, event_date_end, event_time, event_zip, event_name, event_phone, event_email, event_type, event_subtype, event_truckrate_rate, event_truckfee, event_truckfee_rate, event_laborrate, event_laborrate_rate, event_countyfee, event_weekend_upcharge_rate, event_referer, event_comments, event_additions, event_status, event_leadtype) VALUES (
    '".mysql_real_escape_string($token)."',
    '".mysql_real_escape_string($by_user)."',
    '".mysql_real_escape_string($loc)."',
    '".mysql_real_escape_string($usr)."',
    '".mysql_real_escape_string($cuid)."',
    '".mysql_real_escape_string($start)."',
    '".mysql_real_escape_string($end)."',
    '".mysql_real_escape_string($time)."',
    '".mysql_real_escape_string($zip)."',
    '".mysql_real_escape_string($name)."',
    '".mysql_real_escape_string($phone)."',
    '".mysql_real_escape_string($email)."',
    '".mysql_real_escape_string($type)."',
    '".mysql_real_escape_string($subtype)."',
    '".mysql_real_escape_string($truckrate_rate)."',
    '".mysql_real_escape_string($truckfee)."',
    '".mysql_real_escape_string($truckfee_rate)."',
    '".mysql_real_escape_string($laborrate)."',
    '".mysql_real_escape_string($laborrate_rate)."',
    '".mysql_real_escape_string($countyfee)."',
    '".mysql_real_escape_string($weekend_upcharge)."',
    '".mysql_real_escape_string($referer)."',
    '".mysql_real_escape_string($comments)."',
    '".mysql_real_escape_string($additions)."',
    '".mysql_real_escape_string(0)."',
    '".mysql_real_escape_string($_GET['l'])."')") or die(mysql_error());

    if($_GET['hot'] == 'lead' || $_GET['hot'] == 'weblead'){
        $rates = $googer->shorten("https://www.formoversonly.com/dashboard/assets/public/index.php?e=QuT&ev=".$token);
        _sendText($phone, "[".companyName($cuid)."]\r\nView your customized quote here:\r\n".$rates);
    }


    $math['truck_fee']        = $truckfee_rate * $truckfee;
    if($weekend_upcharge > 0){
        $math['total_labor_rate'] = ($laborrate_rate * $laborrate) + ($truckrate_rate * $truckfee) + $weekend_upcharge;
    } else {
        $math['total_labor_rate'] = ($laborrate_rate * $laborrate) + ($truckrate_rate * $truckfee);
    }


    if($_GET['hot'] == 'weblead'){
        timeline_event($token, $uuid, "Creation", name($uuid)." created this event, estimated to need <strong>".$truckfee."</strong> truck(s) (for <strong>$".number_format($math['truck_fee'], 2)."</strong>), and <strong>".$laborrate."</strong> crewmen (for <strong>$".number_format($math['total_labor_rate'], 2)."/hr</strong>) on <strong>".date('m-d-Y', strtotime($start))."</strong> through <strong>".date('m-d-Y', strtotime($end))."</strong> in <strong>".locationName2($loc)."</strong>.");

    } else {
        timeline_event($token, $by_user, "Creation", name($by_user)." created this event, estimated to need <strong>".$truckfee."</strong> truck(s) (for <strong>$".number_format($math['truck_fee'], 2)."</strong>), and <strong>".$laborrate."</strong> crewmen (for <strong>$".number_format($math['total_labor_rate'], 2)."/hr</strong>) on <strong>".date('m-d-Y', strtotime($start))."</strong> through <strong>".date('m-d-Y', strtotime($end))."</strong> in <strong>".locationName2($loc)."</strong>.");

    }
    echo $token;
}
if(isset($_GET['ev']) && $_GET['ev'] == 'pmk'){
    $token     = $_GET['e'];
    $type      = $_POST['type'];
    $address   = sentence_case($_POST['address']);
    $address2  = sentence_case($_POST['address2']);
    $suite     = $_POST['suite'];
    $city      = sentence_case($_POST['city']);
    $state     = $_POST['state'];
    $zip       = $_POST['zip'];
    $intersec  = $_POST['closest_intersection'];
    $county    = sentence_case($_POST['county']);
    $stairs    = $_POST['stairs'];
    $distance  = $_POST['distance'];
    $bedrooms  = $_POST['bedrooms'];
    $garage    = $_POST['garage'];
    $sqft      = $_POST['sqft'];
    $special   = $_POST['special'];
    $comments  = sentence_case($_POST['comments']);

    mysql_query("INSERT INTO fmo_locations_events_addresses (address_event_token, address_type, address_address, address_address2, address_suite, address_city, address_state, address_zip, address_closest_intersection, address_county, address_stairs, address_distance, address_bedrooms, address_garage, address_square_footage, address_special, address_comments) VALUES (
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
    '".mysql_real_escape_string($stairs)."',
    '".mysql_real_escape_string($distance)."',
    '".mysql_real_escape_string($bedrooms)."',
    '".mysql_real_escape_string($garage)."',
    '".mysql_real_escape_string($sqft)."',
    '".mysql_real_escape_string($special)."',
    '".mysql_real_escape_string($comments)."')");
}