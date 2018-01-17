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
require_once('obj/mailer-php-master/class.phpmailer.php');
include     ('obj/mailer-php-master/class.smtp.php');

function secondsToTime($seconds) {
    $dtF = new \DateTime('@0');
    $dtT = new \DateTime("@$seconds");
    if($dtF->diff($dtT)->format('%a') > 0){
        return $dtF->diff($dtT)->format('%a day(s)');
    } else { return 0; }
}
function curl_get_contents($url)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);

    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}

function stlc ($st){
    $luid = mysql_fetch_array(mysql_query("SELECT storage_location_token FROM fmo_locations_storages WHERE storage_token='".mysql_real_escape_string($st)."'"));
    return $luid['storage_location_token'];
}
function timeline_log($who, $by, $type, $reasoning){
    mysql_query("INSERT INTO fmo_users_employee_timelines (timeline_user_token, timeline_datatype, timeline_reasoning, timeline_by_user_token) VALUES (
    '".mysql_real_escape_string($who)."',
    '".mysql_real_escape_string($type)."',
    '".mysql_real_escape_string($reasoning)."',
    '".mysql_real_escape_string($by)."')");
}

function timeline_str($who, $who2, $by, $type, $reasoning){
    mysql_query("INSERT INTO fmo_locations_storages_contracts_timelines (timeline_user_token, timeline_contract_token, timeline_type, timeline_reasoning, timeline_by_user_token) VALUES (
    '".mysql_real_escape_string($who)."',
    '".mysql_real_escape_string($who2)."',
    '".mysql_real_escape_string($type)."',
    '".mysql_real_escape_string($reasoning)."',
    '".mysql_real_escape_string($by)."')") or die(mysql_error());
}

function timeline_event($event, $by, $type, $reasoning){
    mysql_query("INSERT INTO fmo_locations_events_timelines (timeline_event_token, timeline_type, timeline_reasoning, timeline_by_user_token) VALUES (
    '".mysql_real_escape_string($event)."',
    '".mysql_real_escape_string($type)."',
    '".mysql_real_escape_string($reasoning)."',
    '".mysql_real_escape_string($by)."')");
}

function image_fix_orientation(&$image, $filename) {
    $image = imagerotate($image, array_values([0, 0, 0, 180, 0, 0, -90, 0, 90])[@exif_read_data($filename)['Orientation'] ?: 0], 0);
}

function resample($jpgFile, $thumbFile, $width, $orientation) {
    // Get new dimensions
    list($width_orig, $height_orig) = getimagesize($jpgFile);
    $height = (int) (($width / $width_orig) * $height_orig);
    // Resample
    $image_p = imagecreatetruecolor($width, $height);
    $image   = imagecreatefromjpeg($jpgFile);
    imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
    // Fix Orientation
    switch($orientation) {
        case 3:
            $image_p = imagerotate($image_p, 180, 0);
            break;
        case 6:
            $image_p = imagerotate($image_p, -90, 0);
            break;
        case 8:
            $image_p = imagerotate($image_p, 90, 0);
            break;
    }
    // Output
    imagejpeg($image_p, $thumbFile, 90);
}

function abbrName($token){
    $name = name($token);

    list ($first_name, $second_names) = explode(' ', $name, 2);

    $second_names = explode(' ', $second_names);

    foreach ($second_names as $key => $value) {
        $second_names[$key] = $value[0] . '';
    }

    return $first_name . ' ' . implode(' ', $second_names);
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
    return ($hms[0] + ($hms[1]/60) + ($hms[2]/3600));
}

function convert($hours, $minutes) {
    return $hours + round($minutes / 60, 2);
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
function phone2($token){
    $phone = mysql_fetch_array(mysql_query("SELECT user_phone FROM fmo_users WHERE user_token='".mysql_real_escape_string($token)."'"));

    if(empty($phone['user_phone'])){
        return NULL;
    } else {
        return $phone['user_phone'];
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

function companyPhone2($cuid){
    $phone = mysql_fetch_array(mysql_query("SELECT user_phone FROM fmo_users WHERE user_company_token='".mysql_real_escape_string($cuid)."'"));
    if(!empty($phone['user_phone'])){
        return $phone['user_phone'];
    } else {
        return NULL;
    }
}
function companyPhone3($cuid){
    $phone = mysql_fetch_array(mysql_query("SELECT user_company_phone FROM fmo_users WHERE user_company_token='".mysql_real_escape_string($cuid)."'"));
    if(!empty($phone['user_company_phone'])){
        return $phone['user_company_phone'];
    } else {
        return NULL;
    }
}

function companyName($cuid){
    $name = mysql_fetch_array(mysql_query("SELECT user_company_name FROM fmo_users WHERE user_company_token='".mysql_real_escape_string($cuid)."'"));
    if(!empty($name['user_company_name'])){
        return $name['user_company_name'];
    } else {
        return NULL;
    }
}
function companyAddress($cuid){
    $address = mysql_fetch_array(mysql_query("SELECT user_company_address, user_company_city, user_company_state, user_company_zip FROM fmo_users WHERE user_company_token='".mysql_real_escape_string($cuid)."'"));
    if(!empty($address['user_company_address'])){
        echo $address['user_company_address'].", ".$address['user_company_city'].", ".$address['user_company_state'].", ".$address['user_company_zip'];
    } else {
        return NULL;
    }
}
function companyLicenses($cuid){
    $findLicenses = mysql_query("SELECT license_prefix, license_number FROM fmo_users_licenses WHERE license_company_token='".mysql_real_escape_string($cuid)."' ORDER BY license_timestamp DESC");
    $licenses = null;
    while($lc = mysql_fetch_assoc($findLicenses)) {
       $licenses .= ''.$lc['license_prefix'].' #'.$lc['license_number'].' [+] ';
    }
    return $licenses;
}

function eventName($token){
    $name = mysql_fetch_array(mysql_query("SELECT event_name FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($token)."'"));
    if(!empty($name['event_name'])){
        return $name['event_name'];
    } else {
        return NULL;
    }
}

function eventLocationName($token){
    $name = mysql_fetch_array(mysql_query("SELECT event_location_token FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($token)."'"));
    $location_name = mysql_fetch_array(mysql_query("SELECT location_name FROM fmo_locations WHERE location_token='".mysql_real_escape_string($name['event_location_token'])."'"));
    return $location_name['location_name'];
}

function locationName($token){
    $name = mysql_fetch_array(mysql_query("SELECT location_name FROM fmo_locations WHERE location_token='".mysql_real_escape_string($token)."'"));
    if(!empty($name['location_name'])){
        return "".$name['location_name']."";
    } else {
        return NULL;
    }
}
function locationNickName($token){
    $name = mysql_fetch_array(mysql_query("SELECT location_name, location_nickname FROM fmo_locations WHERE location_token='".mysql_real_escape_string($token)."'"));
    if(!empty($name['location_nickname'])){
        return "".$name['location_nickname']."";
    } else {
        return "".$name['location_name']."";
    }
}
function locationPhone($token){
    $phone = mysql_fetch_array(mysql_query("SELECT location_phone FROM fmo_locations WHERE location_token='".mysql_real_escape_string($token)."'"));
    if(!empty($phone['location_phone'])){
        return $phone['location_phone'];
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
            return $phone['user_phone'];
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

function ago($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}