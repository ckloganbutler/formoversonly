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
if($_GET['setting'] == 'delete_item_str'){
    $item = explode("_", $_POST['del']);
    mysql_query("DELETE FROM fmo_locations_storages_contracts_items WHERE item_id='".mysql_real_escape_string($item[1])."'");
}
if($_GET['setting'] == 'del_alt'){
    mysql_query("DELETE FROM fmo_locations_storages_alts WHERE alt_id='".mysql_real_escape_string($_POST['id'])."'");
}
if($_GET['setting'] == 'delete_item_est'){
    $item = explode("_", $_POST['del']);
    mysql_query("DELETE FROM fmo_locations_events_estimates_items WHERE item_id='".mysql_real_escape_string($item[1])."'");
}
if($_GET['setting'] == 'delete_payment'){
    $old  = mysql_fetch_array(mysql_query("SELECT payment_type, payment_amount FROM fmo_locations_events_payments WHERE payment_id='".mysql_real_escape_string($_POST['del'])."'"));
    mysql_query("UPDATE fmo_locations_events_payments SET payment_type='".mysql_real_escape_string($old['payment_type'])." - VOIDED' WHERE payment_id='".mysql_real_escape_string($_POST['del'])."'");
    timeline_event($_POST['ev'], $_SESSION['uuid'], "Voided Payment", "Payment for <strong>$".number_format($old['payment_amount'], 2)."</strong> was <strong>voided</strong> from this event.");
}
if($_GET['setting'] == 'refund_payment'){
    $old  = mysql_fetch_array(mysql_query("SELECT payment_type, payment_amount FROM fmo_locations_events_payments WHERE payment_charge_token='".mysql_real_escape_string($_POST['del'])."'"));
    mysql_query("UPDATE fmo_locations_events_payments SET payment_type='".mysql_real_escape_string($old['payment_type'])." - VOIDED' WHERE payment_charge_token='".mysql_real_escape_string($_POST['del'])."'");
    timeline_event($_POST['ev'], $_SESSION['uuid'], "Refunded Payment", "Payment for <strong>$".number_format($old['payment_amount'], 2)."</strong> was <strong>refunded</strong> from this event. Token: <strong>".$_POST['del']."</strong>");
}
if($_GET['setting'] == 'redeem'){
    mysql_query("UPDATE fmo_locations_events_items SET item_desc=CONCAT(item_desc, ' - <strong class=\"badge badge-roundless badge-uuccess\">REDEEMED</strong>'), item_redeemable=2 WHERE item_id='".mysql_real_escape_string($_POST['item'])."'");
}
if($_GET['setting'] == 'delete_labor'){
    $laborer = explode("_", $_POST['del']);
    mysql_query("DELETE FROM fmo_locations_events_laborers WHERE laborer_id='".mysql_real_escape_string($laborer[1])."'");
}
if($_GET['setting'] == 'mv_out'){
    $unit  = $_POST['sid'];
    $u     = mysql_fetch_array(mysql_query("SELECT storage_unit_name, storage_occupant, storage_location_token FROM fmo_locations_storages WHERE storage_token='".mysql_real_escape_string($unit)."'"));
    mysql_query("UPDATE fmo_locations_storages SET storage_status='Vacant', storage_occupant=NULL, storage_contract_token=NULL, storage_last_occupied='0000-00-00' WHERE storage_token='".mysql_real_escape_string($unit)."'") or die(mysql_error());
    mysql_query("UPDATE fmo_locations_storages_contracts SET contract_status=0 WHERE contract_storage_token='".mysql_real_escape_string($unit)."'");
    $bal = json_decode(file_get_contents('https://www.formoversonly.com/dashboard/assets/app/api/storage.php?type=inv_c&luid='.$u['storage_location_token'].'&uuid='.$u['storage_occupant'].''), true);
    $old = number_format($bal['unpaid'], 2);
    if($old < 0){
        $due = "Credit";
        $old = number_format($bal['unpaid'] * -1, 2);
    } else {$due = "Due"; $old = number_format($bal['unpaid'], 2); }
    timeline_str($u['storage_occupant'], stlc($unit), $_SESSION['uuid'], "hiddenMoveout", "Unit <strong>#".$u['storage_unit_name']."</strong> has been vacated and needs <strong class='text-info'>checked</strong> by a manager.  <span class='pull-right'><strong>Balance: ".$due." ".$old."</strong> [Initiator: <strong>".name($_SESSION['uuid'])."</strong>]</span>");
    timeline_str($u['storage_occupant'], stlc($unit), $_SESSION['uuid'], "Moveout", "Unit <strong>#".$u['storage_unit_name']."</strong> has been vacated [".date('m/d/Y H:i:s')."]  <span class='pull-right'><strong>Balance: ".$due." ".$old."</strong> [Initiator: <strong>".name($_SESSION['uuid'])."</strong>]</span>");

    echo "Unit was successfully vacated.";
}
if($_GET['setting'] == 'closed'){
    $location = $_GET['luid'];

    mysql_query("UPDATE fmo_locations SET location_storage_last_closed='".date('Y-m-d', strtotime('today'))."' WHERE location_token='".mysql_real_escape_string($location)."'");
    mysql_query("UPDATE fmo_locations_storages_contracts_payments SET payment_closed=1 WHERE payment_location_token='".mysql_real_escape_string($location)."'");
}
if($_GET['setting'] == 'open'){
    $location = $_GET['luid'];

    mysql_query("UPDATE fmo_locations SET location_storage_last_opened='".date('Y-m-d', strtotime('today'))."' WHERE location_token='".mysql_real_escape_string($location)."'");
    mysql_query("UPDATE fmo_locations_storages_contracts_timelines SET timeline_opened=1 WHERE timeline_contract_token='".mysql_real_escape_string($location)."'");
}
if($_GET['setting'] == 'pymt'){
    $event  = $_GET['ev'];
    $loc    = $_GET['luid'];
    $user   = $_GET['uuid'];
    $by     = $_SESSION['uuid'];
    $type   = $_POST['type'];
    $amount = $_POST['amount'];
    $notes  = $_POST['notes'];
    $charge = $_POST['charge'];
    if(isset($_GET['ckpay']) && $_GET['ckpay'] == true){
        $type   = "Credit/Debt";
        $by     = $_GET['uuid'];
    }
    if($type == 'Credit/Debt'){
        $stripeCh     = ($_POST['amount'] * .029) + .30;
        $stripeChReal =  $_POST['amount'];
        $notes = "Approval: ".$charge." (Stripe charge: $".$stripeChReal." - 2.9% + .30¢ = $".number_format($stripeChReal - $stripeCh, 2)." deposited)";
    }

    mysql_query("INSERT INTO fmo_locations_events_payments (payment_event_token, payment_user_token, payment_company_token, payment_transaction_id, payment_type, payment_amount, payment_detail, payment_charge_token, payment_by_user_token) VALUES (
    '".mysql_real_escape_string($event)."',
    '".mysql_real_escape_string($user)."',
    '".mysql_real_escape_string($_SESSION['cuid'])."',
    '".mysql_real_escape_string(struuid(true))."',
    '".mysql_real_escape_string($type)."',
    '".mysql_real_escape_string($amount)."',
    '".mysql_real_escape_string($notes)."',
    '".mysql_real_escape_string($charge)."',
    '".mysql_real_escape_string($by)."')");

    timeline_event($event, $by, "Payment", "Payment of <strong>".$type."</strong> for <strong>$".number_format($amount, 2)."</strong> was <strong>added</strong> to this event. Notes: <strong>".$notes."</strong>");


}
if($_GET['setting'] == 'su_pymt'){
    $location = mysql_fetch_array(mysql_query("SELECT location_sales_tax, location_storage_creditcard_fee FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'"));
    $loc       = $_GET['luid'];
    $by        = $_SESSION['uuid'];
    $cuid      = $_SESSION['cuid'];
    $user      = $_GET['uuid'];
    $type      = $_POST['type'];
    $amount    = $_POST['amount'];
    $notes     = $_POST['notes'];
    $charge    = explode("|", $_POST['charge']);
    if(isset($_GET['ckpay']) && $_GET['ckpay'] == true){
        $type   = "Credit/Debt";
        $by     = $_GET['uuid'];
        $cuid   = $_GET['cuid'];
    }
    if($type = 'Credit/Debt'){
        $card   = "(Last 4 #: ".$charge[2].")";
    } else {$card = NULL;}

    $bal = json_decode(file_get_contents('https://www.formoversonly.com/dashboard/assets/app/api/storage.php?type=inv_c&luid='.$loc.'&uuid='.$user.''), true);
    $old = number_format($bal['unpaid'], 2);

    mysql_query("INSERT INTO fmo_locations_storages_contracts_payments (payment_user_token, payment_company_token, payment_location_token, payment_transaction_id, payment_type, payment_amount, payment_detail, payment_charge_token, payment_by_user_token) VALUES (
            '".mysql_real_escape_string($user)."',
            '".mysql_real_escape_string($cuid)."',
            '".mysql_real_escape_string($loc)."',
            '".mysql_real_escape_string(struuid(true))."',
            '".mysql_real_escape_string($type)."',
            '".mysql_real_escape_string($amount)."',
            '".mysql_real_escape_string($notes)."',
            '".mysql_real_escape_string($charge[0])."',
            '".mysql_real_escape_string($by)."')");

    $bal = json_decode(file_get_contents('https://www.formoversonly.com/dashboard/assets/app/api/storage.php?type=inv_c&luid='.$loc.'&uuid='.$user.''), true);
    $new = number_format($bal['unpaid'], 2);

    if(isset($_POST['auto']) && $_POST['auto'] == 1){
        mysql_query("UPDATE fmo_users SET user_autopay=1, user_autopay_token='".mysql_real_escape_string($charge[1])."' WHERE user_token='".mysql_real_escape_string($_GET['uuid'])."'");
    }

    if($bal['unpaid'] < 0){
        $due = "Credit";
        $new = number_format($bal['unpaid'] * -1, 2);
    } else {
        $due = "Due"; $new = number_format($bal['unpaid'], 2);
        if($new == 0.00){
            $del = mysql_query("SELECT storage_unit_name FROM fmo_locations_storages WHERE storage_occupant='".mysql_real_escape_string($user)."' AND storage_status='Delinquent'");
            if(mysql_num_rows($del) > 0){
                while($d = mysql_fetch_assoc($del)){
                    timeline_str($user, $loc, $by, "hiddenUnlock", "Unit <strong>#".$d['storage_unit_name']."</strong> is no longer delinquent and needs to be <strong class='text-success'>unlocked</strong> by a manager. <span class='pull-right'>[Initiator: <strong>".name($by)."</strong>]</span>");
                }
                mysql_query("UPDATE fmo_locations_storages SET storage_status='Occupied' WHERE storage_id='".mysql_real_escape_string(mysql_insert_id())."'");
            }
        }
    }

    timeline_str($user, $loc, $by, "Payment", " <strong>$".number_format($amount, 2)." ".$type."</strong> payment on <strong>".date('m/d/Y', strtotime('today'))."</strong> was added by <strong>".name($_SESSION['uuid'])."</strong> ".$card." <button style='width: 150px;' class='pull-right btn btn-xs default green-stripe'>".$due.": <strong>$".$new."</strong></button><button style='width: 150px;' class='pull-right btn btn-xs default green-stripe'>&nbsp;</button> <button style='width: 150px;' class='pull-right btn btn-xs default green-stripe'>Payment: <strong>$".number_format($amount, 2)."</strong></button>");

}
if($_GET['setting'] == 'add_str_item'){
    $ct   = $_GET['ct'];
    $uuid = $_GET['uuid'];
    $loc  = $_GET['luid'];

    $bal = json_decode(file_get_contents('https://www.formoversonly.com/dashboard/assets/app/api/storage.php?type=inv&luid='.$loc.'&ct='.$ct.''), true);
    $chr = number_format($bal['unpaid'], 2);

    $bal = json_decode(file_get_contents('https://www.formoversonly.com/dashboard/assets/app/api/storage.php?type=inv_c&luid='.$loc.'&uuid='.$uuid.''), true);
    $old = number_format($bal['unpaid'], 2);

    mysql_query("UPDATE fmo_locations_storages_contracts_items SET item_user_token='".mysql_real_escape_string($uuid)."' WHERE item_contract_token='".mysql_real_escape_string($ct)."'") or die(mysql_error());

    $bal = json_decode(file_get_contents('https://www.formoversonly.com/dashboard/assets/app/api/storage.php?type=inv_c&luid='.$loc.'&uuid='.$uuid.''), true);
    $new = number_format($bal['unpaid'], 2);

    if($bal['unpaid'] < 0){
        $due   = "Credit";
        $retil = "discounts given";
        $new = number_format($bal['unpaid'] * -1, 2);
    } else {$due = "Due"; $new = number_format($bal['unpaid'], 2); $retil = "retail sold";}

    timeline_str($uuid, $ct, $_SESSION['uuid'], "Charge", " <strong>$".number_format($chr, 2)."</strong> of ".$retil." on <strong>".date('m/d/Y', strtotime('today'))."</strong> by <strong>".name($_SESSION['uuid'])."</strong> <button style='width: 150px;' class='pull-right btn btn-xs default red-stripe'>".$due.": <strong>$".$new."</strong></button><button style='width: 150px;' class='pull-right btn btn-xs default red-stripe'>Charge: <strong>$".number_format($chr, 2)."</strong></button> <button style='width: 150px;' class='pull-right btn btn-xs default red-stripe'>&nbsp;</strong></button>");
}
if($_GET['setting'] == 'alts'){
    $field = $_POST['name'];
    $value = $_POST['value'];
    $pk    = $_POST['pk'];
    if($field == 'alt_phone'){
        $value = preg_replace('/\D+/', '', $value);
    }
    mysql_query("UPDATE fmo_locations_storages_alts SET ".mysql_real_escape_string($field)."='".mysql_real_escape_string($value)."' WHERE alt_id='".mysql_real_escape_string($pk)."'");
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
if($_GET['setting'] == 'c_items'){
    $field = $_POST['name'];
    $value = $_POST['value'];
    $pk    = $_POST['pk'];
    mysql_query("UPDATE fmo_locations_storages_contracts_items SET ".mysql_real_escape_string($field)."='".mysql_real_escape_string($value)."' WHERE item_id='".mysql_real_escape_string($pk)."'") or die(mysql_error());
    $item  = mysql_fetch_array(mysql_query("SELECT item_qty, item_cost FROM fmo_locations_storages_contracts_items WHERE item_id='".mysql_real_escape_string($pk)."'"));
    $total = $item['item_cost'] * $item['item_qty'];
    mysql_query("UPDATE fmo_locations_storages_contracts_items SET item_total='".mysql_real_escape_string($total)."' WHERE item_id='".mysql_real_escape_string($pk)."'");
}
if($_GET['setting'] == 'estimate_items'){
    $field = $_POST['name'];
    $value = $_POST['value'];
    $pk    = $_POST['pk'];
    mysql_query("UPDATE fmo_locations_events_estimates_items SET ".mysql_real_escape_string($field)."='".mysql_real_escape_string($value)."' WHERE item_id='".mysql_real_escape_string($pk)."'") or die(mysql_error());
    $item  = mysql_fetch_array(mysql_query("SELECT item_qty, item_cost FROM fmo_locations_events_estimates_items WHERE item_id='".mysql_real_escape_string($pk)."'"));
    $total = $item['item_cost'] * $item['item_qty'];
    mysql_query("UPDATE fmo_locations_events_estimates_items SET item_total='".mysql_real_escape_string($total)."' WHERE item_id='".mysql_real_escape_string($pk)."'");
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
        $value = preg_replace('/\D+/', '', $value);
    }
    if($field == 'user_company_phone'){
        $value = preg_replace('/\D+/', '', $value);
    }
    if($field == 'user_broadcast'){
        mysql_query("UPDATE fmo_users SET user_broadcast_timestamp='".date('Y/m/d')."' WHERE user_token='".mysql_real_escape_string($pk)."'");
    }
    if($field == 'user_employer_commission'){
        $value = $value / 100;
    }
    if($field == 'user_permissions'){
        $k     = 0;
        $value = NULL;
        foreach($_POST['value'] as $val){
            if($k == 0){
                $value .= $val;
            } else {
                $value .= ",".$val;
            }
            $k++;
        }
    }
    if($field == 'user_last_ext_date'){
        $date  = explode(" ", $value);
        if(!empty($date[1])){
            $value = date('Y-m-d', strtotime($date[1]." ".$date[2]." ".$date[3]));
        }
    }
    mysql_query("UPDATE fmo_users SET ".mysql_real_escape_string($field)."='".mysql_real_escape_string($value)."' WHERE user_token='".mysql_real_escape_string($pk)."'");
    if($field != 'user_last_ext_date'){

        if(strpos($value, ',') !== false){
            $parts = explode(",", $value);
            $value = "";
            $i     = 0;
            foreach($parts as $part){
                if ($i > 0){$comma = ', ';}
                $value .= "".locationName($part).$comma;
            }
        }

        $field = str_replace("user_", "", $field);
        $field = str_replace("_", " ", $field);

        timeline_log($pk, $_SESSION['uuid'], "Profile updated", "Updated <strong>".$field."</strong> with: <strong>".$value."</strong>");
    }
}
if($_GET['update'] == 'event_addy'){
    $field = $_POST['name'];
    $value = $_POST['value'];
    $pk    = $_POST['pk'];
    mysql_query("UPDATE fmo_locations_events_addresses SET ".mysql_real_escape_string($field)."='".mysql_real_escape_string($value)."' WHERE address_id='".mysql_real_escape_string($pk)."'");
}
if($_GET['update'] == 'subscr'){
    switch ($_POST['i']){
        case "STD":        $days = 30;  break;
        case "STDPLUS":    $days = 365; break;
        case "ENTPRI":     $days = 30;  break;
        case "ENTPRIPLUS": $days = 365; break;
    }
    $license = mysql_query("SELECT user_license_exp FROM fmo_users WHERE user_company_token='".mysql_real_escape_string($_SESSION['cuid'])."'");
    if(mysql_num_rows($license) > 0){
        $lic = mysql_fetch_array($license);
        $exp   = date('Y-m-d G:i:s', strtotime($lic['user_license_exp']));
        $today = date('Y-m-d G:i:s');
        if($today >= $exp){
            $newexp = date('Y-m-d G:i:s', strtotime($today." + ".$days." days"));
        } else {
            $newexp = date('Y-m-d G:i:s', strtotime($exp." + ".$days." days"));
        }
        mysql_query("UPDATE fmo_users SET user_license_exp='".mysql_real_escape_string($newexp)."' WHERE user_company_token='".mysql_real_escape_string($_SESSION['cuid'])."'");
    }

    echo $days." days have been added to your license.";
}
if($_GET['update'] == 'ticket' ){
    $field = $_POST['name'];
    $value = $_POST['value'];
    $pk    = $_POST['pk'];
    mysql_query("UPDATE fmo_locations_tickets SET ".mysql_real_escape_string($field)."='".mysql_real_escape_string($value)."' WHERE ticket_token='".mysql_real_escape_string($pk)."'");
}
if($_GET['update'] == 'event_fly'){
    $field = $_POST['name'];
    $value = $_POST['value'];
    $pk    = $_POST['pk'];
    if($field == 'event_phone'){
        $value = preg_replace('/[^A-Za-z0-9\-]/', '', $value);
    }
    if($field == 'event_date_touch'){
        $value = date('Y-m-d H:i:s');
        $luid = mysql_fetch_array(mysql_query("SELECT event_location_token FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($pk)."'"));
        echo $luid['event_location_token'];
    }
    if($_GET['era'] == 'pre'){
        $era = "Pre Booking";
    } elseif($_GET['era'] == 'post'){
        $era = "Post Booking";
    } elseif($_GET['era'] == 'self'){
        $era = "Self Booking";
    }
    if($field == 'event_booking'){
        if(isset($_SESSION['uuid']) && isset($_SESSION['cuid'])){
            $user = name($_SESSION['uuid']);
            $cuid = $_SESSION['cuid'];
            $uuid = $_SESSION['uuid'];
        } else {
            $user = name($_GET['uuid'])."/Customer";
            $cuid = $_GET['cuid'];
            $uuid = $_GET['uuid'];
        }
        mysql_query("INSERT INTO fmo_locations_events_items (item_event_token, item_item, item_desc, item_qty, item_cost, item_total, item_taxable, item_commission, item_redeemable, item_adder) VALUES (
        '".mysql_real_escape_string($pk)."',
        '".mysql_real_escape_string("Booking Fee")."',
        '".mysql_real_escape_string("")."',
        '".mysql_real_escape_string(1)."',
        '".mysql_real_escape_string(10.00)."',
        '".mysql_real_escape_string(10.00)."',
        '".mysql_real_escape_string(0)."',
        '".mysql_real_escape_string(0)."',
        '".mysql_real_escape_string(0)."',
        '".mysql_real_escape_string($uuid)."')");
        mysql_query("INSERT INTO fmo_locations_events_payments (payment_event_token, payment_user_token, payment_company_token, payment_transaction_id, payment_type, payment_amount, payment_era, payment_charge_token, payment_by_user_token) VALUES (
        '".mysql_real_escape_string($pk)."',
        '".mysql_real_escape_string($uuid)."',
        '".mysql_real_escape_string($cuid)."',
        '".mysql_real_escape_string(struuid(true))."',
        '".mysql_real_escape_string("Booking Fee")."',
        '".mysql_real_escape_string(10.00)."',
        '".mysql_real_escape_string($era)."',
        '".mysql_real_escape_string($_GET['tok'])."',
        '".mysql_real_escape_string($uuid)."')");
        timeline_event($pk, $uuid, "Booking Fee", "Paid <strong>$10.00</strong> booking fee: <strong>".$_GET['tok']."</strong>");
        _sendText("3172018875", "[".companyName($cuid)."]\r\nBooking fee paid ($10.00)\r\nBy ".$user);
        _sendText("3176716774", "[".companyName($cuid)."]\r\nBooking fee paid ($1.00)\r\nBy ".$user);
        $license = mysql_query("SELECT user_license_exp FROM fmo_users WHERE user_company_token='".mysql_real_escape_string($_SESSION['cuid'])."'");
        if(mysql_num_rows($license) > 0){
            $lic = mysql_fetch_array($license);
            $exp   = date('Y-m-d G:i:s', strtotime($lic['user_license_exp']));
            $today = date('Y-m-d G:i:s');
            if($today >= $exp){
                $newexp = date('Y-m-d G:i:s', strtotime($today." + 3 days"));
            } else {
                $newexp = date('Y-m-d G:i:s', strtotime($exp." + 3 days"));
            }
            mysql_query("UPDATE fmo_users SET user_license_exp='".mysql_real_escape_string($newexp)."' WHERE user_company_token='".mysql_real_escape_string($_SESSION['cuid'])."'");
        }
    }
    if($field == 'event_status'){
        if($value == 0){
            timeline_event($pk, $_SESSION['uuid'], "Event Cancelled", "Event was cancelled because: <strong>".$_POST['reasoning']."</strong>");
        }
        if($value == 9){
            $luid = mysql_fetch_array(mysql_query("SELECT event_location_token FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($pk)."'"));
            echo $luid['event_location_token'];
        }
    }
    if($field == 'event_cjr'){
        if($value == 1){
            timeline_event($pk, $_SESSION['uuid'], "Event CJR Approved", "Event was marked completed & approved by <strong>".name($_SESSION['uuid'])."</strong> in the completed jobs report.");
        }
    }
    mysql_query("UPDATE fmo_locations_events SET ".mysql_real_escape_string($field)."='".mysql_real_escape_string($value)."' WHERE event_token='".mysql_real_escape_string($pk)."'");
    if(isset($_GET['self'])){
        timeline_event($pk, $_GET['uuid'], "Customer Confirmed", "Event was self-confirmed by <strong>".name($_GET['uuid'])."/Outside</strong> using confirmation link.");
    }
}
if($_GET['update'] == 'review_stat'){
    $pk    = $_POST['pk'];
    $value = $_POST['value'];

    mysql_query("UPDATE fmo_locations_events_reviews SET review_status='".mysql_real_escape_string($value)."' WHERE review_id='".mysql_real_escape_string($pk)."'") or die(mysql_error());
}
if($_GET['setting'] == 'pw'){
    if(isset($_GET['uuid'])){
        $password = md5($_POST['password']);

        mysql_query("UPDATE fmo_users SET user_pword='".mysql_real_escape_string($password)."' WHERE user_token='".mysql_real_escape_string($_GET['uuid'])."'");
        timeline_log($_GET['uuid'], $_GET['uuid'], "Profile updated", "<strong>Password updated</strong> from password reset form sent by <strong>".name($_GET['b'])."</strong>");
        $email = mysql_fetch_array(mysql_query("SELECT user_email FROM fmo_users WHERE user_token='".mysql_real_escape_string($_GET['uuid'])."'"));
        ?>
        <center>
            <h3 class="form-title"><i class="fa fa-check" style="font-size: 17px;"></i><strong>Password</strong> updated.</h3>
            <small>
                You can now <a href="https://www.formoversonly.com/">login to the system</a> using your new password. <br/><br/> <strong>Friendly reminder:</strong> <br/>Your login email is something like this.. <br/> <strong><?php echo secret_mail($email['user_email']); ?></strong>
            </small>
            <br/> <br/>
        </center>
        <?php
    }
}
if($_GET['update'] == 'event_date'){
    $start     = $_POST['dateStart'];
    $endDate   = $_POST['dateEnd'];
    $token     = $_POST['ev'];
    $loc       = $_GET['luid'];

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

    mysql_query("UPDATE fmo_locations_events SET event_date_start='".mysql_real_escape_string($start)."', event_date_end='".mysql_real_escape_string($endDate)."', event_truckrate_rate='".mysql_real_escape_string($truckrate_rate)."', event_truckfee_rate='".mysql_real_escape_string($truckfee_rate)."', event_laborrate_rate='".mysql_real_escape_string($laborrate_rate)."', event_weekend_upcharge_rate='".mysql_real_escape_string($weekend_upcharge)."' WHERE event_token='".mysql_real_escape_string($token)."'") or die(mysql_error());
    timeline_event($token, $_SESSION['uuid'], "Date update", "Date range was changed to <strong>".date('m/d/Y', strtotime($start))."</strong> through <strong>".date('m/d/Y', strtotime($endDate))."</strong>");
    echo "<strong>Logan says:</strong><br/>I updated the event's date, and corrected the rates.";
}
if($_GET['update'] == 'ev_bol_comments'){
    $comment   = $_POST['comment'];
    $event     = $_POST['ev'];
    mysql_query("UPDATE fmo_locations_events SET event_comments='".mysql_real_escape_string($comment)."' WHERE event_token='".mysql_real_escape_string($event)."'");
}
if($_GET['update'] == 'est_comments'){
    $comment   = $_POST['comment'];
    $event     = $_POST['ev'];
    mysql_query("UPDATE fmo_locations_events_estimates SET estimate_comments='".mysql_real_escape_string($comment)."' WHERE estimate_token='".mysql_real_escape_string($event)."'");
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
if($_GET['update'] == 'user_perms'){
    $value = $_POST['value'];
    $perm  = $_POST['perm'];
    $perms = mysql_fetch_array(mysql_query("SELECT user_esc_permissions FROM fmo_users WHERE user_token='".$_POST['uuid']."'"));
    $p     = explode("|", $perms['user_esc_permissions']);
    if($value == "true"){
        // Give the user the permission.
        if(strpos($perms['user_esc_permissions'], $perm) !== false){
            // User has the permission already.
        } else {
            // Restructure array & submit
            if(!empty($perms['user_esc_permissions'])){
                $new_p = $perms['user_esc_permissions']."|".$perm;
            } else {
                $new_p = $perm;
            }
            mysql_query("UPDATE fmo_users SET user_esc_permissions='".mysql_real_escape_string($new_p)."' WHERE user_token='".mysql_real_escape_string($_POST['uuid'])."'");

        }
    } else {
        // Remove the user's permission.
        if(strpos($perms['user_esc_permissions'], $perm) !== false){
            // User has the permission, remove, restructure array & submit.
            if(strpos($perms['user_esc_permissions'], "|".$perm) !== false){
                $new_p = str_replace("|".$perm, "", $perms['user_esc_permissions']);
            } elseif(strpos($perms['user_esc_permissions'], $perm."|") !== false) {
                $new_p = str_replace($perm."|", "", $perms['user_esc_permissions']);
            } else {
                $new_p = str_replace($perm, "", $perms['user_esc_permissions']);
            }
            mysql_query("UPDATE fmo_users SET user_esc_permissions='".mysql_real_escape_string($new_p)."' WHERE user_token='".mysql_real_escape_string($_POST['uuid'])."'");
        } else {
            // User doesn't have the permission.
        }
    }
}
if($_GET['update'] == 'loc_qut'){
    $value = $_POST['value'];
    $perm  = $_POST['perm'];
    $perms = mysql_fetch_array(mysql_query("SELECT location_quote FROM fmo_locations WHERE location_token='".$_POST['uuid']."'"));
    $p     = explode("|", $perms['location_quote']);
    if($value == "true"){
        // Give the user the permission.
        if(strpos($perms['location_quote'], $perm) !== false){
            // User has the permission already.
        } else {
            // Restructure array & submit
            if(!empty($perms['location_quote'])){
                $new_p = $perms['location_quote']."|".$perm;
            } else {
                $new_p = $perm;
            }
            mysql_query("UPDATE fmo_locations SET location_quote='".mysql_real_escape_string($new_p)."' WHERE location_token='".mysql_real_escape_string($_POST['uuid'])."'");

        }
    } else {
        // Remove the user's permission.
        if(strpos($perms['location_quote'], $perm) !== false){
            // User has the permission, remove, restructure array & submit.
            if(strpos($perms['location_quote'], "|".$perm) !== false){
                $new_p = str_replace("|".$perm, "", $perms['location_quote']);
            } elseif(strpos($perms['location_quote'], $perm."|") !== false) {
                $new_p = str_replace($perm."|", "", $perms['location_quote']);
            } else {
                $new_p = str_replace($perm, "", $perms['location_quote']);
            }
            mysql_query("UPDATE fmo_locations SET location_quote='".mysql_real_escape_string($new_p)."' WHERE location_token='".mysql_real_escape_string($_POST['uuid'])."'");
        } else {
            // User doesn't have the permission.
        }
    }
}
if($_GET['update'] == 'change_type'){
    if(isset($_POST['type']) && $_POST['type'] == 'status'){
        $value = $_POST['value'];
        $token  = $_GET['ev'];
        switch($value){
            case 1: $status = "New Booking"; break;
            case 2: $status = "Confirmed";  break;
            case 3: $status = "Left Message";  break;
            case 4: $status = "On Hold"; break;
            case 5: $status = "Cancelled"; break;
            case 6: $status = "Customer Confirmed"; break;
            case 8: $status = "Completed";  break;
            case 9: $status = "Dead Hot Lead";  break;
            default: $status = "On Hold";  break;
        }
        if($value == 8 || $value == 5){
            // Let's send the customer a review link!
            $phone = mysql_fetch_array(mysql_query("SELECT event_phone FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($token)."'"));
            $review_link = $googer->shorten("https://www.formoversonly.com/dashboard/assets/public/review.php?ev=".$_GET['ev']);
            if($value == 8 ){
                $msg         = "[".companyName($_SESSION['cuid'])."]\r\nYou're all done! Leave a review about us here:\r\n".$review_link;
                timeline_event($token, $_SESSION['uuid'], "Link sent", "<Strong>Review link was sent</Strong> to the customer because the event was completed. ");
            } elseif($value == 5) {
                $msg         = "[".companyName($_SESSION['cuid'])."]\r\nSorry you had to cancel. Tell us your thoughts here:\r\n".$review_link;
                timeline_event($token, $_SESSION['uuid'], "Link sent", "<Strong>Review link was sent</Strong> to the customer because the event was canceled. ");
            }

            _sendText($phone['event_phone'], $msg);
        }
        mysql_query("UPDATE fmo_locations_events SET event_status='".mysql_real_escape_string($value)."' WHERE event_token='".mysql_real_escape_string($token)."'");
        if(isset($_POST['reasoning'])){
            $extra = ". Reasoning: <strong>".$_POST['reasoning']."</strong>";
        }else {$extra  = NULL;}
        timeline_event($token, $_SESSION['uuid'], "Status update", "Status was changed to <strong>".$status."</strong>".$extra);
        echo true;
    } elseif(isset($_POST['type']) && $_POST['type'] == 'eventtype'){
        $type = $_POST['value'];
        $token  = $_GET['ev'];
        mysql_query("UPDATE fmo_locations_events SET event_type='".mysql_real_escape_string($type)."' WHERE event_token='".mysql_real_escape_string($token)."'");
        timeline_event($token, $_SESSION['uuid'], "Type update", "Event type was changed to <strong>".$type."</strong>");
        echo true;
    } elseif(isset($_POST['type']) && $_POST['type'] == 'subtype'){
        $subtype = $_POST['value'];
        $token   = $_GET['ev'];
        $utoken = mysql_fetch_array(mysql_query("SELECT event_user_token FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($_GET['ev'])."'"));
        $name   = name($utoken['event_user_token']);
        mysql_query("UPDATE fmo_locations_events SET event_subtype='".mysql_real_escape_string($subtype)."', event_name='".mysql_real_escape_string($name."'s ".$subtype)."' WHERE event_token='".mysql_real_escape_string($token)."'");
        timeline_event($token, $_SESSION['uuid'], "Subtype update", "Event subtype was changed to <strong>".$subtype."</strong>");
        echo true;
    } elseif(isset($_POST['type']) && $_POST['type'] == 'eventtime'){
        $time    = $_POST['value'];
        $token   = $_GET['ev'];
        mysql_query("UPDATE fmo_locations_events SET event_time='".mysql_real_escape_string($time)."' WHERE event_token='".mysql_real_escape_string($token)."'");
        timeline_event($token, $_SESSION['uuid'], "Event time update", "Event time was changed to <strong>".$time."</strong>");
        echo true;
    } elseif(isset($_POST['type']) && $_POST['type'] == 'eventlocation'){
        $location = $_POST['value'];
        $token    = $_GET['ev'];
        mysql_query("UPDATE fmo_locations_events SET event_location_token='".mysql_real_escape_string($location)."' WHERE event_token='".mysql_real_escape_string($token)."'");
        timeline_event($token, $_SESSION['uuid'], "Event time update", "Event location was changed to <strong>".locationName($location)."</strong>");
        echo true;
    }
}
if($_GET['update'] == 'event' && isset($_POST)){
    $utoken = mysql_fetch_array(mysql_query("SELECT event_user_token FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($_GET['e'])."'"));
    $name   = name($utoken['event_user_token']);
    $pfield = array('event_status', 'event_date_start', 'event_date_end', 'event_time', 'event_name', 'event_type', 'event_subtype', 'event_email', 'event_phone', 'event_truckfee', 'event_laborrate', 'event_countyfee');
    $pvalue = array($_GET['s'], date('Y-m-d', strtotime($_POST['startdate'])), date('Y-m-d', strtotime($_POST['enddate'])), $_POST['time'], $name."'s ".$_POST['subtype'], $_POST['type'], $_POST['subtype'], $_POST['email'], $_POST['phone'], $_POST['truckfee'], $_POST['laborrate'], $_POST['countyfee']);
    $ptoken = $_GET['e'];
    for ($k = 0; $k<count($pfield); $k++) {
        if(!empty($pvalue[$k])){
            mysql_loop($pfield[$k], $pvalue[$k], "fmo_locations_events", "event_token", $ptoken);
        }
    }

    if($_GET['s'] == 1){
        if(isset($_SESSION['uuid'])){
            $user = name($_SESSION['uuid']);
        } else {
            $user = "Customer/Outside";
        }
        $confirm = $googer->shorten("https://www.formoversonly.com/dashboard/assets/public/conf.php?ty=confirm&ev=".$ptoken);
        _sendText(companyPhone2($_GET['cuid']), "[".companyName($_GET['cuid'])."]\r\nNew event: ".locationName($_GET['luid']).", ".date("m/d/Y", strtotime($_POST['startdate']))."\r\n".$name."'s ".$_POST['subtype'].", by ".$user."");
        _sendText(locationManagerPhone($_GET['luid']), "[".companyName($_GET['cuid'])."]\r\nNew event: ".locationName($_GET['luid']).", ".date("m/d/Y", strtotime($_POST['startdate']))."\r\n".$name."'s ".$_POST['subtype'].", by ".$user."");
        _sendText("3176716774", "[".companyName($_GET['cuid'])."]\r\nNew event: ".locationName($_GET['luid']).", ".date("m/d/Y", strtotime($_POST['startdate']))."\r\n".$name."'s ".$_POST['subtype'].", by ".$user."");
        _sendText($_POST['phone'], "[".companyName($_GET['cuid'])."]\r\nView & confirm your move here:\r\n".$confirm);

        try {
            $email = new PHPMailer(true);
            $email->isHTML(true);
            $email->From      = "no-reply@".str_replace(' ', '', strtolower(companyName($_SESSION['cuid']))).".com";
            $email->FromName  = "".name($_SESSION['uuid'])."";
            $email->Subject   = "Your move confirmation from ".companyName($_SESSION['cuid']).".";
            $email->Body      = "Hello, thanks for using our services! Please use the link below to view your confirmation. <br/> <br/> <a target='_blank' href='".$confirm."'>Confirm my move now!</a>";
            $email->AddAddress( $_POST['email'] );
            $email->Send();
        } catch (phpmailerException $e) {
            echo $e->errorMessage();
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        if(isset($_GET['STEALER']) ){
            $orig = mysql_fetch_array(mysql_query("SELECT event_by_user_token FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($ptoken)."'"));
            if($orig['event_by_user_token'] != $_GET['STEALER']){
                mysql_query("UPDATE fmo_locations_events SET event_by_user_token='".mysql_real_escape_string($_GET['STEALER'])."' WHERE event_token='".mysql_real_escape_string($ptoken)."'");
                timeline_event($ptoken, $_GET['STEALER'], "Booking Stolen! <i class='fa fa-user-secret faa-float animated'></i>", "<strong>".name($_GET['STEALER'])."</strong> stole this booking from <strong>".name($orig['event_by_user_token'])."</strong>.");
            }
        }


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
    } elseif($_GET['s'] == 0){
        if(!isset($_GET['no_txt']) && $_GET['no_txt'] == 'false'){
            $rates = $googer->shorten("https://www.formoversonly.com/dashboard/assets/public/index.php?e=QuT&ev=".$ptoken);
            _sendText($_POST['phone'], "[".companyName($_SESSION['cuid'])."]\r\nView your customized quote here:\r\n".$rates);
            try {
                $email = new PHPMailer(true);
                $email->isHTML(true);
                $email->From      = "no-reply@".str_replace(' ', '', strtolower(companyName($_SESSION['cuid']))).".com";
                $email->FromName  = "".name($_SESSION['uuid'])."";
                $email->Subject   = "Your move quote from ".companyName($_SESSION['cuid']).".";
                $email->Body      = "Hello, thanks for using our services! Please use the link below to view your quote. <br/> <br/> <a target='_blank' href='".$rates."'>Confirm my move now!</a>";
                $email->AddAddress( $_POST['email'] );
                $email->Send();
            } catch (phpmailerException $e) {
                echo $e->errorMessage();
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
    }

    return false;
}
if($_GET['update'] == 'estimate' && isset($_POST)){
    $pfield = array('estimate_status', 'estimate_customer_name', 'estimate_customer_email', 'estimate_customer_phone', 'estimate_pickup_date', 'estimate_dropoff_date', 'estimate_pickup_time', 'estimate_name', 'estimate_type', 'estimate_email', 'estimate_phone', 'estimate_comments', 'packing_comments', 'transport_comments', 'unload_comments', 'estimate_cus_sig', 'estimate_es_sig');
    $pvalue = array($_GET['s'], $_POST['customer_name'], $_POST['customer_email'], preg_replace('/[^A-Za-z0-9\-]/', '', $_POST['customer_phone']), date('Y-m-d', strtotime($_POST['startdate'])), date('Y-m-d', strtotime($_POST['enddate'])), $_POST['time'], $_POST['event_name'], $_POST['type'], $_POST['email'], preg_replace('/[^A-Za-z0-9\-]/', '', $_POST['phone']), $_POST['comments'], $_POST['packing_comments'], $_POST['transport_comments'], $_POST['unload_comments'], $_POST['cus_sig'], $_POST['es_sig']);
    $ptoken = $_GET['est'];
    $etoken = $_GET['ev'];
    for ($k = 0; $k<count($pfield); $k++) {
        if(!empty($pvalue[$k])){
            mysql_loop($pfield[$k], $pvalue[$k], "fmo_locations_events_estimates", "estimate_token", $ptoken);
        }
    }

    if($_GET['s'] == 1){
        if(!isset($_GET['no_txt']) || isset($_POST['txt'])){
            $confirm = $googer->shorten("https://www.formoversonly.com/dashboard/assets/public/index.php?e=EmP&ev=".$etoken."&v=v&n=".$ptoken);
            _sendText($_POST['customer_phone'], "[".companyName($_GET['cuid'])."]\r\nYour estimate is ready to view:\r\n".$confirm);
            try {
                $email = new PHPMailer(true);
                $email->isHTML(true);
                $email->From      = "no-reply@".str_replace(' ', '', strtolower(companyName($_SESSION['cuid']))).".com";
                $email->FromName  = "".name($_SESSION['uuid'])."";
                $email->Subject   = "Your move quote from ".companyName($_SESSION['cuid']).".";
                $email->Body      = "Hello, thanks for using our services! Your estimate is ready to view. <br/> <br/> <a target='_blank' href='".$confirm."'>View my estimate!</a>";
                $email->AddAddress( $_POST['email'] );
                $email->Send();
            } catch (phpmailerException $e) {
                echo $e->errorMessage();
            } catch (Exception $e) {
                echo $e->getMessage();
            }
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
    if(isset($_GET['p']) && $_GET['p'] == true){
        $value = $value / 100;
    }
    mysql_query("UPDATE fmo_locations SET ".mysql_real_escape_string($field)."='".mysql_real_escape_string($value)."' WHERE location_token='".mysql_real_escape_string($pk)."'");

}
if($_GET['update'] == 'location_services'){
    $field = $_POST['name'];
    $value = $_POST['value'];
    $pk    = $_POST['pk'];
    mysql_query("UPDATE fmo_services SET ".mysql_real_escape_string($field)."='".mysql_real_escape_string($value)."' WHERE services_id='".mysql_real_escape_string($pk)."'");
}
if($_GET['update'] == 'est_rates'){
    $field = $_POST['name'];
    $value = $_POST['value'];
    $pk    = $_POST['pk'];
    mysql_query("UPDATE fmo_locations_events_estimates SET ".mysql_real_escape_string($field)."='".mysql_real_escape_string($value)."' WHERE estimate_token='".mysql_real_escape_string($pk)."'");
}
if($_GET['update'] == 'deposit_reason'){
    $value = $_POST['reason'];
    $pk    = $_POST['id'];
    mysql_query("UPDATE fmo_locations_events_payments SET payment_payout_reason='".mysql_real_escape_string($value)."' WHERE payment_transaction_id='".mysql_real_escape_string($pk)."'");
}
if($_GET['update'] == 'deposit_amount'){
    $value = $_POST['amount'];
    $pk    = $_POST['id'];
    mysql_query("UPDATE fmo_locations_events_payments SET payment_payout_amount='".mysql_real_escape_string($value)."' WHERE payment_transaction_id='".mysql_real_escape_string($pk)."'");

    $payment = mysql_fetch_array(mysql_query("SELECT payment_amount, payment_payout_amount FROM fmo_locations_events_payments WHERE payment_transaction_id='".mysql_real_escape_string($pk)."'"));
    echo number_format($payment['payment_amount'] - $payment['payment_payout_amount'], 2);
}
if($_GET['update'] == 'location_time'){
    $field = $_POST['name'];
    $value = $_POST['value'];
    $pk    = $_POST['pk'];
    mysql_query("UPDATE fmo_locations_times SET ".mysql_real_escape_string($field)."='".mysql_real_escape_string($value)."' WHERE time_id='".mysql_real_escape_string($pk)."'");
}
if($_GET['update'] == 'estimate'){
    $field = $_POST['name'];
    $value = $_POST['value'];
    $pk    = $_POST['pk'];
    mysql_query("UPDATE fmo_locations_events_estimates SET ".mysql_real_escape_string($field)."='".mysql_real_escape_string($value)."' WHERE estimate_token='".mysql_real_escape_string($pk)."'");
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
if($_GET['update'] == 'location_storagetypes'){
    $field = $_POST['name'];
    $value = $_POST['value'];
    $pk    = $_POST['pk'];
    mysql_query("UPDATE fmo_locations_storages_types SET ".mysql_real_escape_string($field)."='".mysql_real_escape_string($value)."' WHERE type_id='".mysql_real_escape_string($pk)."'");
}
if($_GET['update'] == 'sendable'){
    $field = $_POST['name'];
    $value = $_POST['value'];
    $pk    = $_POST['pk'];
    mysql_query("UPDATE fmo_sendables SET ".mysql_real_escape_string($field)."='".mysql_real_escape_string($value)."' WHERE sendable_token='".mysql_real_escape_string($pk)."'");
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
if($_GET['adm'] == 'delete_sendable' && isset($_POST)){
    $token = $_POST['token'];
    mysql_query("DELETE FROM fmo_sendables WHERE sendable_token='".mysql_real_escape_string($token)."'");
    unlink('/var/www/dashboard/assets/upload/'.$token.'.pdf');
}
if($_GET['adm'] == 'delete_ev' && isset($_POST)){
    $event = $_POST['value'];
    $ev    = mysql_fetch_array(mysql_query("SELECT event_name, event_date_start FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($event)."'"));
    mysql_query("DELETE FROM fmo_locations_events_addresses WHERE address_event_token='".mysql_real_escape_string($event)."'");
    mysql_query("DELETE FROM fmo_locations_events_claims    WHERE claim_event_token='".mysql_real_escape_string($event)."'");
    mysql_query("DELETE FROM fmo_locations_events_comments  WHERE comment_event_token='".mysql_real_escape_string($event)."'");
    mysql_query("DELETE FROM fmo_locations_events_items     WHERE item_event_token='".mysql_real_escape_string($event)."'");
    mysql_query("DELETE FROM fmo_locations_events_payments  WHERE payment_event_token='".mysql_real_escape_string($event)."'");
    mysql_query("DELETE FROM fmo_locations_events_reviews   WHERE review_event_token='".mysql_real_escape_string($event)."'");
    mysql_query("DELETE FROM fmo_locations_events_timelines WHERE timeline_event_token='".mysql_real_escape_string($event)."'");
    mysql_query("DELETE FROM fmo_locations_events_laborers  WHERE labor_event_token='".mysql_real_escape_string($event)."'");
    mysql_query("DELETE FROM fmo_locations_events_estimates WHERE estimate_event_token='".mysql_real_escape_string($event)."'");
    mysql_query("DELETE FROM fmo_locations_events_documents WHERE document_event_token='".mysql_real_escape_string($event)."'");
    mysql_query("DELETE FROM fmo_locations_events           WHERE event_token='".mysql_real_escape_string($event)."'");

    //_sendText("3172018875", "[".companyName($_SESSION['cuid'])."]\r\n".name($_SESSION['uuid'])." deleted:\r\n".$ev['event_name']."\r\n".date('m-d-Y', strtotime($ev['event_date_start'])));
    _sendText("3176716774", "[".companyName($_SESSION['cuid'])."]\r\n".name($_SESSION['uuid'])." deleted:\r\n".$ev['event_name']."\r\n".date('m-d-Y', strtotime($ev['event_date_start'])));
}
if($_GET['adm'] == 'delete_tc'){
    $token = $_POST['token'];
    mysql_query("DELETE FROM fmo_users_employee_timeclock WHERE timeclock_id='".mysql_real_escape_string($token)."'");
}
if($_GET['adm'] == 'delete_p_doc'){
    $token = $_POST['token'];
    mysql_query("DELETE FROM fmo_users_employee_documents WHERE document_id='".mysql_real_escape_string($token)."'");
}
if($_GET['adm'] == 'daily_note'){
    $id    = $_POST['id'];
    mysql_query("DELETE FROM fmo_locations_activites WHERE activity_id='".mysql_real_escape_string($id)."'") or die(mysql_error());
}
if($_GET['adm'] == 'del_asset'){
    $id    = $_POST['id'];
    mysql_query("DELETE FROM fmo_locations_events_assets WHERE asset_id='".mysql_real_escape_string($id)."'");
}
if($_GET['adm'] == 'del_location'){
    $id    = $_POST['id'];
    mysql_query("DELETE FROM fmo_locations_events_addresses WHERE address_id='".mysql_real_escape_string($id)."'");
}
if($_GET['adm'] == 'del_usrlabor'){
    $id    = $_POST['id'];
    mysql_query("DELETE FROM fmo_locations_events_laborers WHERE laborer_id='".mysql_real_escape_string($id)."'");
}