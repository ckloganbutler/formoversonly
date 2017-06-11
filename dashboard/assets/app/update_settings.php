<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/5/2017
 * Time: 3:43 PM
 */
session_start();
include 'init.php';
function mysql_loop($field, $value, $db, $where, $token) {
    mysql_query("UPDATE " . mysql_real_escape_string($db) . " SET `" . mysql_real_escape_string($field) . "`='" . mysql_real_escape_string($value) . "' WHERE $where='" . mysql_real_escape_string($token) . "'") or die(mysql_error());
}

if($_GET['update'] == 'event_laborers'){
    $field = $_POST['name'];
    $value = $_POST['value'];
    $pk    = $_POST['pk'];
    mysql_query("UPDATE fmo_locations_events_laborers SET ".mysql_real_escape_string($field)."='".mysql_real_escape_string($value)."' WHERE laborer_id='".mysql_real_escape_string($pk)."'") or die(mysql_error());
}
if($_GET['update'] == 'assets'){
    $field = $_POST['name'];
    $value = $_POST['value'];
    $pk    = $_POST['pk'];
    mysql_query("UPDATE fmo_locations_assets SET ".mysql_real_escape_string($field)."='".mysql_real_escape_string($value)."' WHERE asset_id='".mysql_real_escape_string($pk)."'");
}
if($_GET['update'] == 'usr_cs'){
    $field = $_POST['name'];
    $value = $_POST['value'];
    $pk    = $_POST['pk'];
    mysql_query("UPDATE fmo_users_employee_childsupports SET ".mysql_real_escape_string($field)."='".mysql_real_escape_string($value)."' WHERE childsupport_id='".mysql_real_escape_string($pk)."'");
}
if($_GET['update'] == 'time_clock'){
    $field = $_POST['name'];
    $value = $_POST['value'];
    $pk    = $_POST['pk'];
    mysql_query("UPDATE fmo_users_employee_timeclock SET ".mysql_real_escape_string($field)."='".mysql_real_escape_string($value)."' WHERE timeclock_id='".mysql_real_escape_string($pk)."'");
    // We need to re-calculate hours worked, as well (before returning success).
    $times = mysql_fetch_array(mysql_query("SELECT timeclock_clockin, timeclock_clockout FROM fmo_users_employee_timeclock WHERE timeclock_id='".mysql_real_escape_string($pk)."'"));
    $date_out = $times['timeclock_clockout'];
    $date_in  = $times['timeclock_clockin'];
    $date1 = new DateTime($date_in);
    $date2 = new DateTime($date_out);
    $diff = $date2->diff($date1);
    $hours   = $diff->h;
    $minutes += $diff->i;
    $seconds += $diff->s;
    $hours   = $hours + ($diff->days*24);
    $worked = decimalHours($hours.":".$minutes.":".$seconds);
    mysql_query("UPDATE fmo_users_employee_timeclock SET timeclock_hours='".mysql_real_escape_string($worked)."' WHERE timeclock_id='".mysql_real_escape_string($pk)."'");
}
if($_GET['update'] == 'usr_prf'){
    $field = $_POST['name'];
    $value = $_POST['value'];
    $pk    = $_POST['pk'];
    if($field == 'user_phone'){
        $value = preg_replace('/[^A-Za-z0-9\-]/', '', $value);
    }
    if($field == 'user_broadcast'){
        mysql_query("UPDATE fmo_users SET user_broadcast_timestamp='".date('Y/m/d')."' WHERE user_token='".mysql_real_escape_string($pk)."'");
    }
    mysql_query("UPDATE fmo_users SET ".mysql_real_escape_string($field)."='".mysql_real_escape_string($value)."' WHERE user_token='".mysql_real_escape_string($pk)."'");
    timeline_log($pk, $_SESSION['uuid'], "Profile updated", "Profile updated '<strong>".$field."</strong>' with new value: <strong>".$value."</strong>");
}
if($_GET['update'] == 'event_addy'){
    $field = $_POST['name'];
    $value = $_POST['value'];
    $pk    = $_POST['pk'];
    mysql_query("UPDATE fmo_locations_events_addresses SET ".mysql_real_escape_string($field)."='".mysql_real_escape_string($value)."' WHERE address_id='".mysql_real_escape_string($pk)."'");
}
if($_GET['update'] == 'event_fly'){
    $field = $_POST['name'];
    $value = $_POST['value'];
    $pk    = $_POST['pk'];
    mysql_query("UPDATE fmo_locations_events SET ".mysql_real_escape_string($field)."='".mysql_real_escape_string($value)."' WHERE event_token='".mysql_real_escape_string($pk)."'");
    timeline_event($pk, $_SESSION['uuid'], "Information update", "'".$field."' was changed to: ".$value);
}
if($_GET['update'] == 'change_type'){
    if(isset($_POST['type']) && $_POST['type'] == 'status'){
        $value = $_POST['value'];
        $token  = $_GET['ev'];
        mysql_query("UPDATE fmo_locations_events SET event_status='".mysql_real_escape_string($value)."' WHERE event_token='".mysql_real_escape_string($token)."'");
        timeline_event($token, $_SESSION['uuid'], "Status update", "Status was changed to _________");
        echo true;
    } elseif(isset($_POST['type']) && $_POST['type'] == 'eventtype'){
        $type = $_POST['value'];
        $token  = $_GET['ev'];
        mysql_query("UPDATE fmo_locations_events SET event_type='".mysql_real_escape_string($type)."' WHERE event_token='".mysql_real_escape_string($token)."'");
        timeline_event($token, $_SESSION['uuid'], "Type update", "Event type was changed to __________");
        echo true;
    } elseif(isset($_POST['type']) && $_POST['type'] == 'subtype'){
        $subtype = $_POST['value'];
        $token   = $_GET['ev'];
        mysql_query("UPDATE fmo_locations_events SET event_subtype='".mysql_real_escape_string($subtype)."' WHERE event_token='".mysql_real_escape_string($token)."'");
        timeline_event($token, $_SESSION['uuid'], "Subtype update", "Event subtype was changed to _________");
        echo true;
    }
}
if($_GET['update'] == 'event' && isset($_POST)){
    $pfield = array('event_status', 'event_date_start', 'event_date_end', 'event_time', 'event_name', 'event_type', 'event_subtype', 'event_email', 'event_phone', 'event_truckfee', 'event_laborrate', 'event_countyfee');
    $pvalue = array(1, date('Y-m-d', strtotime($_POST['startdate'])), date('Y-m-d', strtotime($_POST['enddate'])), $_POST['time'], $_POST['name'], $_POST['type'], $_POST['subtype'], $_POST['email'], $_POST['phone'], $_POST['truckfee'], $_POST['laborrate'], $_POST['countyfee']);
    $ptoken = $_GET['e'];
    for ($k = 0; $k<count($pfield); $k++) {
        if(!empty($pvalue[$k])){
            mysql_loop($pfield[$k], $pvalue[$k], "fmo_locations_events", "event_token", $ptoken);
        }
    }

    return false;
}
if($_GET['update'] == 'personal' && isset($_POST)){
    $pfield = array('user_fname', 'user_lname', 'user_phone', 'user_address', 'user_city', 'user_state', 'user_zip', 'user_pword', 'user_company_name', 'user_website');
    $pvalue = array($_POST['fname'], $_POST['lname'], preg_replace('/[^A-Za-z0-9\-]/', '', $_POST['phone']), $_POST['address'], $_POST['city'], $_POST['state'], $_POST['zip'], $_POST['pass'],$_POST['company'], $_POST['website']);
    $ptoken = $_SESSION['uuid'];
    for ($k = 0; $k<count($pfield); $k++) {
        if(!empty($pvalue[$k])){
            mysql_loop($pfield[$k], $pvalue[$k], "fmo_users", "user_token", $ptoken);
        }
    }

    return false;
}
if($_GET['update'] == 'password' && isset($_POST)){
    if(!empty($_POST['npassword'])){
        mysql_query("UPDATE fmo_users SET user_pword='".mysql_real_escape_string(md5($_POST['npassword']))."' WHERE user_token='".mysql_real_escape_string($_SESSION['uuid'])."'") or die(mysql_error());
    }
}
if($_GET['update'] == 'location_status' && isset($_POST)){
    if($_POST['status'] == 'false'){
        mysql_query("UPDATE fmo_locations SET location_status='".mysql_real_escape_string(0)."' WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'");
    } elseif($_POST['status'] == 'true') {
        mysql_query("UPDATE fmo_locations SET location_status='".mysql_real_escape_string(1)."' WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'");
    }
}
if($_GET['update'] == 'location' && isset($_POST)){
    $field = $_POST['name'];
    $value = $_POST['value'];
    $pk    = $_POST['pk'];
    mysql_query("UPDATE fmo_locations SET ".mysql_real_escape_string($field)."='".mysql_real_escape_string($value)."' WHERE location_token='".mysql_real_escape_string($pk)."'");

}
if($_GET['update'] == 'location_services'){
    $field = $_POST['name'];
    $value = $_POST['value'];
    $pk    = $_POST['pk'];
    mysql_query("UPDATE fmo_services SET ".mysql_real_escape_string($field)."='".mysql_real_escape_string($value)."' WHERE services_id='".mysql_real_escape_string($pk)."'");
}
if($_GET['update'] == 'location_storage'){
    $field = $_POST['name'];
    $value = $_POST['value'];
    $pk    = $_POST['pk'];
    mysql_query("UPDATE fmo_locations_storages SET ".mysql_real_escape_string($field)."='".mysql_real_escape_string($value)."' WHERE storage_id='".mysql_real_escape_string($pk)."'");
}
if($_GET['update'] == 'location_vendor'){
    $field = $_POST['name'];
    $value = $_POST['value'];
    $pk    = $_POST['pk'];
    if($field == 'vendor_phone'){
        $value = preg_replace('/[^A-Za-z0-9\-]/', '', $value);
    }
    mysql_query("UPDATE fmo_locations_vendors SET ".mysql_real_escape_string($field)."='".mysql_real_escape_string($value)."' WHERE vendor_id='".mysql_real_escape_string($pk)."'");
}
if($_GET['update'] == 'location_settings' && isset($_POST)){
    $pfield = array('location_minimum_hours', 'location_assumed_loadtime', 'location_assumed_unloadtime', 'location_sales_tax', 'location_service_tax', 'location_creditcard_fee');
    $pvalue = array($_POST['minimum_hours'], $_POST['assumed_loadtime'], $_POST['assumed_unloadtime'], $_POST['sales_tax'] / 100, $_POST['service_tax'] / 100, $_POST['creditcard_fee'] / 100);
    $ptoken = $_GET['luid'];
    for ($k = 0; $k<count($pfield); $k++) {
        if(!empty($pvalue[$k])){
            mysql_loop($pfield[$k], $pvalue[$k], "fmo_locations", "location_token", $ptoken);
        }
    }
}