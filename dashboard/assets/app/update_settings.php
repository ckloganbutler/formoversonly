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

$key = 'AIzaSyDxLua1UKdf-637NvG5NgBuhb0DYVQ77cg';
$googer = new GoogleUrlApi($key);
if($_GET['setting'] == 'delete_item'){
    $item = explode("_", $_POST['del']);
    mysql_query("DELETE FROM fmo_locations_events_items WHERE item_id='".mysql_real_escape_string($item[1])."'");
}
if($_GET['setting'] == 'redeem'){
    mysql_query("UPDATE fmo_locations_events_items SET item_desc='REDEEMED', item_redeemable=2 WHERE item_id='".mysql_real_escape_string($_POST['item'])."'");
}
if($_GET['setting'] == 'delete_labor'){
    $laborer = explode("_", $_POST['del']);
    mysql_query("DELETE FROM fmo_locations_events_laborers WHERE laborer_id='".mysql_real_escape_string($laborer[1])."'");
}
if($_GET['setting'] == 'pymt'){
    $event  = $_GET['ev'];
    $loc    = $_GET['luid'];
    $user   = $_GET['uuid'];
    $by     = $_SESSION['uuid'];
    $type   = $_POST['type'];
    $amount = $_POST['amount'];
    $notes  = $_POST['notes'];

    mysql_query("INSERT INTO fmo_locations_events_payments (payment_event_token, payment_user_token, payment_company_token, payment_transaction_id, payment_type, payment_amount, payment_detail, payment_by_user_token) VALUES (
    '".mysql_real_escape_string($event)."',
    '".mysql_real_escape_string($user)."',
    '".mysql_real_escape_string($_SESSION['cuid'])."',
    '".mysql_real_escape_string(struuid(true))."',
    '".mysql_real_escape_string($type)."',
    '".mysql_real_escape_string($amount)."',
    '".mysql_real_escape_string($notes)."',
    '".mysql_real_escape_string($by)."')");
}
if($_GET['setting'] == 'event_laborers'){
    $field = $_POST['name'];
    $value = $_POST['value'];
    $pk    = $_POST['pk'];
    mysql_query("UPDATE fmo_locations_events_laborers SET ".mysql_real_escape_string($field)."='".mysql_real_escape_string($value)."' WHERE laborer_id='".mysql_real_escape_string($pk)."'") or die(mysql_error());
}
if($_GET['setting'] == 'event_items'){
    $field = $_POST['name'];
    $value = $_POST['value'];
    $pk    = $_POST['pk'];
    mysql_query("UPDATE fmo_locations_events_items SET ".mysql_real_escape_string($field)."='".mysql_real_escape_string($value)."' WHERE item_id='".mysql_real_escape_string($pk)."'") or die(mysql_error());
    $item  = mysql_fetch_array(mysql_query("SELECT item_qty, item_cost FROM fmo_locations_events_items WHERE item_id='".mysql_real_escape_string($pk)."'"));
    $total = $item['item_cost'] * $item['item_qty'];
    mysql_query("UPDATE fmo_locations_events_items SET item_total='".mysql_real_escape_string($total)."' WHERE item_id='".mysql_real_escape_string($pk)."'");
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
    if($field == 'user_employer_commission'){
        $value = $value / 100;
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
    if($field == 'event_phone'){
        $value = preg_replace('/[^A-Za-z0-9\-]/', '', $value);
    }
    mysql_query("UPDATE fmo_locations_events SET ".mysql_real_escape_string($field)."='".mysql_real_escape_string($value)."' WHERE event_token='".mysql_real_escape_string($pk)."'");
}
if($_GET['update'] == 'event_date'){
    $startDate = $_POST['dateStart'];
    $endDate   = $_POST['dateEnd'];
    $token     = $_POST['ev'];

    mysql_query("UPDATE fmo_locations_events SET event_date_start='".mysql_real_escape_string($startDate)."', event_date_end='".mysql_real_escape_string($endDate)."' WHERE event_token='".mysql_real_escape_string($token)."'");
    timeline_event($token, $_SESSION['uuid'], "Date update", "Date range was changed to ".$startDate." through ".$endDate."");
}
if($_GET['update'] == 'ev_bol_comments'){
    $comment   = $_POST['comment'];
    $event     = $_POST['ev'];
    mysql_query("UPDATE fmo_locations_events SET event_comments='".mysql_real_escape_string($comment)."' WHERE event_token='".mysql_real_escape_string($event)."'");
}
if($_GET['update'] == 'ev_additions'){
    $value     = $_POST['value'];
    $action    = $_GET['t'];
    $event     = $_POST['ev'];
    if($action == 'a'){
        $additions = array(); $extra = "";
        $items = mysql_query("SELECT event_additions FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($event)."'");
        if(mysql_num_rows($items)){
            $item_list  = mysql_fetch_array($items);
            $item_array = explode("|", $item_list['event_additions']);
            foreach($item_array as $item){
                $additions[] = $item;
            }
            if(!in_array($value, $additions)){
                $additions[] = $value;
                foreach($additions as $addition){
                    if(!empty($addition)){
                        $extra .= $addition."|";
                    }
                } echo $extra;
                mysql_query("UPDATE fmo_locations_events SET event_additions='".mysql_real_escape_string($extra)."' WHERE event_token='".mysql_real_escape_string($event)."'");
            }
        }
    } elseif($action == 'r'){
        $additions = array(); $extra = "";
        $items = mysql_query("SELECT event_additions FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($event)."'");
        if(mysql_num_rows($items)){
            $item_list  = mysql_fetch_array($items);
            $item_array = explode("|", $item_list['event_additions']);
            foreach($item_array as $item){
                $additions[] = $item;
            }
            if(($key = array_search($value, $additions)) !== false){
                unset($additions[$key]);
                foreach($additions as $addition){
                    if(!empty($addition)){
                        $extra .= $addition."|";
                    }
                }
                mysql_query("UPDATE fmo_locations_events SET event_additions='".mysql_real_escape_string($extra)."' WHERE event_token='".mysql_real_escape_string($event)."'");
            }
        }
    }
}
if($_GET['update'] == 'change_type'){
    if(isset($_POST['type']) && $_POST['type'] == 'status'){
        $value = $_POST['value'];
        $token  = $_GET['ev'];
        if($value == 1){ $status = 'New Booking'; }
        if($value == 2){ $status = 'Confirmed'; }
        if($value == 3){ $status = 'Left Message';}
        if($value == 4){ $status = 'On Hold';}
        if($value == 5){ $status = 'Cancelled';}
        mysql_query("UPDATE fmo_locations_events SET event_status='".mysql_real_escape_string($value)."' WHERE event_token='".mysql_real_escape_string($token)."'");
        timeline_event($token, $_SESSION['uuid'], "Status update", "Status was changed to <strong>".$status."</strong>");
        echo true;
    } elseif(isset($_POST['type']) && $_POST['type'] == 'eventtype'){
        $type = $_POST['value'];
        $token  = $_GET['ev'];
        mysql_query("UPDATE fmo_locations_events SET event_type='".mysql_real_escape_string($type)."' WHERE event_token='".mysql_real_escape_string($token)."'");
        timeline_event($token, $_SESSION['uuid'], "Type update", "Event type was changed to <strong>".$value."</strong>");
        echo true;
    } elseif(isset($_POST['type']) && $_POST['type'] == 'subtype'){
        $subtype = $_POST['value'];
        $token   = $_GET['ev'];
        mysql_query("UPDATE fmo_locations_events SET event_subtype='".mysql_real_escape_string($subtype)."' WHERE event_token='".mysql_real_escape_string($token)."'");
        timeline_event($token, $_SESSION['uuid'], "Subtype update", "Event subtype was changed to <strong>".$value."</strong>");
        echo true;
    } elseif(isset($_POST['type']) && $_POST['type'] == 'eventtime'){
        $time    = $_POST['value'];
        $token   = $_GET['ev'];
        mysql_query("UPDATE fmo_locations_events SET event_time='".mysql_real_escape_string($time)."' WHERE event_token='".mysql_real_escape_string($token)."'");
        timeline_event($token, $_SESSION['uuid'], "Event time update", "Event time was changed to <strong>".$time."</strong>");
        echo true;
    }
}
if($_GET['update'] == 'event' && isset($_POST)){
    $pfield = array('event_status', 'event_date_start', 'event_date_end', 'event_time', 'event_name', 'event_type', 'event_subtype', 'event_email', 'event_phone', 'event_truckfee', 'event_laborrate', 'event_countyfee');
    $pvalue = array($_GET['s'], date('Y-m-d', strtotime($_POST['startdate'])), date('Y-m-d', strtotime($_POST['enddate'])), $_POST['time'], $_POST['name'], $_POST['type'], $_POST['subtype'], $_POST['email'], $_POST['phone'], $_POST['truckfee'], $_POST['laborrate'], $_POST['countyfee']);
    $ptoken = $_GET['e'];
    for ($k = 0; $k<count($pfield); $k++) {
        if(!empty($pvalue[$k])){
            mysql_loop($pfield[$k], $pvalue[$k], "fmo_locations_events", "event_token", $ptoken);
        }
    }

    if($_GET['s'] == 1){
        $confirm = $googer->shorten("https://www.formoversonly.com/dashboard/assets/public/conf.php?ev=".$_POST['ev']);
        _sendText(companyPhone($_SESSION['cuid']), "New ".locationName($_GET['luid'])." event on ".date("m/d/Y", strtotime($_POST['startdate']))." by ".$_POST['name']." / ".name($_SESSION['uuid'])."");
        _sendText(locationManagerPhone($_GET['luid']), "New ".locationName($_GET['luid'])." event on ".date("m/d/Y", strtotime($_POST['startdate']))." by ".$_POST['name']." / ".name($_SESSION['uuid'])."");
        _sendText($_POST['phone'], "Ahoy from ".companyName($_SESSION['cuid'])."! Track your booking using handly link!\r\n".$confirm);

        // Now, we must also put the default items into the invoice.
        /*
         *  Logan, what the heck goes into the invoice automatically?
         *
         *  - Booking Fee,
         *  - Truck Fee,
         *  - Crew Size,
         *  - Counties Traveled
         *  - Other items added
         *
         */


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
if($_GET['update'] == 'location_eventtype'){
    $field = $_POST['name'];
    $value = $_POST['value'];
    $pk    = $_POST['pk'];
    mysql_query("UPDATE fmo_locations_eventtypes SET ".mysql_real_escape_string($field)."='".mysql_real_escape_string($value)."' WHERE eventtype_id='".mysql_real_escape_string($pk)."'");
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