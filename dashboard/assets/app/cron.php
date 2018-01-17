<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 12/3/2017
 * Time: 2:30 PM
 */

include 'init.php';

$key = 'AIzaSyDxLua1UKdf-637NvG5NgBuhb0DYVQ77cg';
$googer = new GoogleUrlApi($key);

_sendText("3172018875", "[FMO]\r\nDaily CRON has begun.");
_sendText("3176716774", "[FMO]\r\nDaily CRON has begun.");

$storage   = mysql_query("SELECT storage_occupant, storage_contract_token, storage_location_token, storage_price, storage_unit_name FROM fmo_locations_storages WHERE storage_status='Occupied' OR storage_status='Delinquent'");
if(mysql_num_rows($storage) > 0){
    while($str = mysql_fetch_assoc($storage)){
        $luid     = $str['storage_location_token'];
        $location = mysql_fetch_array(mysql_query("SELECT location_storage_late_fee, location_storage_days_late, location_storage_days_auction, location_storage_auction_fee, location_token, location_owner_company_token FROM fmo_locations WHERE location_token='".mysql_real_escape_string($luid)."'"));
        if(!empty($str['storage_occupant']) && !empty($str['storage_contract_token'])){
            $contract = mysql_fetch_array(mysql_query("SELECT contract_rate_adj, contract_next_due, contract_last_due FROM fmo_locations_storages_contracts WHERE contract_token='".mysql_real_escape_string($str['storage_contract_token'])."'"));

            if(date('Y-m-d', strtotime($contract['contract_next_due'])) == date('Y-m-d', strtotime('today'))){
                // Charge for rent. Change next due date. Update last due.
                $cost    = number_format($str['storage_price'] + $contract['contract_rate_adj'], 2);
                $bal = json_decode(file_get_contents('https://www.formoversonly.com/dashboard/assets/app/api/storage.php?type=inv_c&luid='.$luid.'&uuid='.$str['storage_occupant'].''), true);
                $old = number_format($bal['unpaid'], 2);
                mysql_query("INSERT INTO fmo_locations_storages_contracts_items (item_contract_token, item_user_token, item_location_token, item_company_token, item_item, item_desc, item_qty, item_cost, item_total, item_taxable, item_commission, item_redeemable, item_discount, item_percent, item_prepay, item_adder) VALUES (
                '".mysql_real_escape_string($str['storage_contract_token'])."',
                '".mysql_real_escape_string($str['storage_occupant'])."',
                '".mysql_real_escape_string($str['storage_location_token'])."',
                '".mysql_real_escape_string($location['location_owner_company_token'])."',
                '".mysql_real_escape_string("Rent")."',
                '".mysql_real_escape_string("Standard rent charge")."',
                '".mysql_real_escape_string(1)."',
                '".mysql_real_escape_string($cost)."',
                '".mysql_real_escape_string($cost)."',
                '".mysql_real_escape_string(0)."',
                '".mysql_real_escape_string(0)."',
                '".mysql_real_escape_string(0)."',
                '".mysql_real_escape_string(0)."',
                '".mysql_real_escape_string(0)."',
                '".mysql_real_escape_string(0)."',
                '".mysql_real_escape_string('SYSTEM')."')") or die(mysql_error());
                $bal = json_decode(file_get_contents('https://www.formoversonly.com/dashboard/assets/app/api/storage.php?type=inv_c&luid='.$luid.'&uuid='.$str['storage_occupant'].''), true);
                $new = number_format($bal['unpaid'], 2);
                if($bal['unpaid'] < 0){
                    $due = "Credit";
                    $new = number_format($bal['unpaid'] * -1);
                } else {$due = "Due"; $new = number_format($bal['unpaid'], 2); }
                $review_link = $googer->shorten("https://www.formoversonly.com/dashboard/assets/public/index.php?t=sTr&su=true&uuid=".$str['storage_occupant']);
                $msg        = "[".locationNickName($str['storage_location_token'])."]\r\nFriendly reminder; rental payment due:\r\n".$review_link;
                _sendText(phone2($str['storage_occupant']), $msg);
                timeline_str($str['storage_occupant'], $str['storage_contract_token'], "SYSTEM", "Charge", "<strong>$".number_format($cost, 2)." rent charge</strong> for <strong>".date('m/d/Y', strtotime('today'))."</strong> was systematically added. <strong>(Unit #".$str['storage_unit_name'].")</strong> [By: <strong>System</strong>]  <button style='width: 150px;' class='pull-right btn btn-xs default red-stripe'>".$due.": <strong>$".$new."</strong></button><button style='width: 150px;' class='pull-right btn btn-xs default red-stripe'>Charge: <strong>$".number_format($cost, 2)."</strong></button> <button style='width: 150px;' class='pull-right btn btn-xs default red-stripe'>&nbsp;</button>");
                if($storage['storage_period'] == 'Weekly'){
                    $dpm = 7;
                } else {$dpm = cal_days_in_month(CAL_GREGORIAN, date('m', strtotime($contract['contract_next_due'])), date('Y', strtotime($contract['contract_next_due'])));}
                mysql_query("UPDATE fmo_locations_storages_contracts SET contract_last_due='".$contract['contract_next_due']."', contract_next_due='".date('Y-m-d', strtotime($contract['contract_next_due']." + ".$dpm." days"))."' WHERE contract_token='".mysql_real_escape_string($str['storage_contract_token'])."'");
                echo "[CRON] ".name($str['storage_occupant'])." was charged ".number_format($cost, 2)." just now \n";

            }

            if(date('Y-m-d', strtotime('today')) == date('Y-m-d', strtotime("+".($location['location_storage_days_late'] + 1)." days", strtotime($contract['contract_last_due'])))){
                // Today > last due + 5 days...if they have a balance due, they're late!
                $bal = json_decode(file_get_contents('https://www.formoversonly.com/dashboard/assets/app/api/storage.php?type=inv_c&luid='.$luid.'&uuid='.$str['storage_occupant'].''), true);
                $old = number_format($bal['unpaid'], 2);

                if($old > 0){
                    $fee = number_format($old + $location['location_storage_late_fee'], 2);
                    mysql_query("INSERT INTO fmo_locations_storages_contracts_items (item_contract_token, item_user_token, item_item, item_desc, item_qty, item_cost, item_total, item_taxable, item_commission, item_redeemable, item_discount, item_percent, item_prepay, item_adder) VALUES (
                    '".mysql_real_escape_string($str['storage_contract_token'])."',
                    '".mysql_real_escape_string($str['storage_occupant'])."',
                    '".mysql_real_escape_string("Late Fee")."',
                    '".mysql_real_escape_string("Standard late fee charge")."',
                    '".mysql_real_escape_string(1)."',
                    '".mysql_real_escape_string($location['location_storage_late_fee'])."',
                    '".mysql_real_escape_string($location['location_storage_late_fee'])."',
                    '".mysql_real_escape_string(0)."',
                    '".mysql_real_escape_string(0)."',
                    '".mysql_real_escape_string(0)."',
                    '".mysql_real_escape_string(0)."',
                    '".mysql_real_escape_string(0)."',
                    '".mysql_real_escape_string(0)."',
                    '".mysql_real_escape_string('SYSTEM')."')") or die(mysql_error());

                    $bal = json_decode(file_get_contents('https://www.formoversonly.com/dashboard/assets/app/api/storage.php?type=inv_c&luid='.$luid.'&uuid='.$str['storage_occupant'].''), true);
                    $new = number_format($bal['unpaid'], 2);

                    if($bal['unpaid'] < 0){
                        $due = "Credit";
                        $new = number_format($bal['unpaid'] * -1);
                    } else {$due = "Due"; $new = number_format($bal['unpaid'], 2); }

                    $review_link = $googer->shorten("https://www.formoversonly.com/dashboard/assets/public/index.php?t=sTr&su=true&uuid=".$str['storage_occupant']);
                    $msg        = "[".locationNickName($str['storage_location_token'])."]\r\nPayment is late, late fee applied:\r\n".$review_link;
                    _sendText(phone2($str['storage_occupant']), $msg);
                    mysql_query("UPDATE fmo_locations_storages SET storage_status='Delinquent' WHERE storage_token='".mysql_real_escape_string($ct['contract_storage_token'])."'");
                    timeline_str($str['storage_occupant'], $str['storage_contract_token'], "SYSTEM", "Late Fee", "<strong>$".number_format($location['location_storage_late_fee'], 2)." late fee charge</strong> on <strong>".date('m/d/Y', strtotime('today'))."</strong> was systematically added. <strong>(Unit #".$str['storage_unit_name'].")</strong> [By: <strong>System</strong>]  <button style='width: 150px;' class='pull-right btn btn-xs default yellow-stripe'>".$due.": <strong>$".$new."</strong></button><button style='width: 150px;' class='pull-right btn btn-xs default yellow-stripe'>Charge: <strong>$".number_format($location['location_storage_late_fee'], 2)."</strong></button> <button style='width: 150px;' class='pull-right btn btn-xs default yellow-stripe'>&nbsp;</button>");
                    timeline_str($str['storage_occupant'], $luid,                          "SYSTEM", "Lock", "Unit <strong>#".$storage['storage_unit_name']."</strong> has gone delinquent and needs to be <strong class='text-danger'>locked</strong> by a manager. <span class='pull-right'>[Initiator: <strong>System</strong>]</span>");
                    echo "[CRON] ".name($str['storage_occupant'])." was charged a late fee \n";
                }
            }

            /*
            $payments = mysql_num_rows(mysql_query("SELECT payment_id FROM fmo_locations_storages_payments WHERE payment_user_token='".mysql_real_escape_string($str['storage_occupant'])."' AND DATE(payment_timestamp)>='".date('Y-m-d', strtotime('today - '.$location['location_storage_days_auction'].' days'))."'"));
            if($payments > 0){
                // payments found. let's check if they're zero'd out?
                if(true){
                    $bal = json_decode(file_get_contents('https://www.formoversonly.com/dashboard/assets/app/api/storage.php?type=inv_c&luid='.$location['location_token'].'&uuid='.$str['storage_occupant'].''), true);
                    $old = number_format($bal['unpaid'], 2);

                    if($old > 0){
                        // needs to be set to auction status.
                        timeline_str($str['storage_occupant'], $ct['contract_token'], "SYSTEM", "Auction Fee", "<strong>Auction status set</strong> on <strong>".date('m/d/Y', strtotime('today'))."</strong>. <strong>(Unit #".$str['storage_unit_name'].")</strong> [By: <strong>System</strong>]  <button style='width: 450px;' class='pull-right btn btn-xs default purple-stripe'>".$due.": <strong>$".$new."</strong></button>");
                        mysql_query("UPDATE fmo_locations_storages SET storage_status='Auction' WHERE storage_token='".mysql_real_escape_string($str['storage_token'])."'");
                        mysql_query("INSERT INTO fmo_locations_storages_contracts_items (item_contract_token, item_user_token, item_company_token, item_location_token, item_item, item_desc, item_qty, item_cost, item_total, item_taxable, item_commission, item_redeemable, item_discount, item_percent, item_prepay, item_adder) VALUES (
                    '".mysql_real_escape_string($ct['contract_token'])."',
                    '".mysql_real_escape_string($str['storage_occupant'])."',
                    '".mysql_real_escape_string($str['storage_location_token'])."',
                    '".mysql_real_escape_string($location['location_owner_company_token'])."',
                    '".mysql_real_escape_string("Late Fee")."',
                    '".mysql_real_escape_string("Standard late fee charge")."',
                    '".mysql_real_escape_string(1)."',
                    '".mysql_real_escape_string($location['location_storage_late_fee'])."',
                    '".mysql_real_escape_string($location['location_storage_late_fee'])."',
                    '".mysql_real_escape_string(0)."',
                    '".mysql_real_escape_string(0)."',
                    '".mysql_real_escape_string(0)."',
                    '".mysql_real_escape_string(0)."',
                    '".mysql_real_escape_string(0)."',
                    '".mysql_real_escape_string(0)."',
                    '".mysql_real_escape_string('SYSTEM')."')") or die(mysql_error());

                        $bal = json_decode(file_get_contents('https://www.formoversonly.com/dashboard/assets/app/api/storage.php?type=inv_c&luid='.$location['location_token'].'&uuid='.$str['storage_occupant'].''), true);
                        $new = number_format($bal['unpaid'], 2);

                        if($bal['unpaid'] < 0){
                            $due = "Credit";
                            $new = number_format($bal['unpaid'] * -1);
                        } else {$due = "Due"; $new = number_format($bal['unpaid'], 2); }

                        timeline_str($str['storage_occupant'], $ct['contract_token'], "SYSTEM", "Auction Fee", "<strong>$".number_format($location['location_storage_auction_fee'], 2)." auction fee charge</strong> on <strong>".date('m/d/Y', strtotime('today'))."</strong> was systematically added. <strong>(Unit #".$str['storage_unit_name'].")</strong> [By: <strong>System</strong>]  <button style='width: 150px;' class='pull-right btn btn-xs default yellow-stripe'>".$due.": <strong>$".$new."</strong></button><button style='width: 150px;' class='pull-right btn btn-xs default yellow-stripe'>Charge: <strong>$".number_format($location['location_storage_auction_fee'], 2)."</strong></button> <button style='width: 150px;' class='pull-right btn btn-xs default yellow-stripe'>&nbsp;</button>");
                    } else {
                        // payments...but nothing due? All good.
                    }
                }
            } else {
                // no payments found for last 90 days. pittyful.
                if(true){
                    $bal = json_decode(file_get_contents('https://www.formoversonly.com/dashboard/assets/app/api/storage.php?type=inv_c&luid='.$location['location_token'].'&uuid='.$str['storage_occupant'].''), true);
                    $old = number_format($bal['unpaid'], 2);

                    if($old > 0){
                        // needs to be set to auction status.
                        timeline_str($str['storage_occupant'], $ct['contract_token'], "SYSTEM", "Auction Fee", "<strong>Auction status set</strong> on <strong>".date('m/d/Y', strtotime('today'))."</strong>. <strong>(Unit #".$str['storage_unit_name'].")</strong> [By: <strong>System</strong>]  <button style='width: 450px;' class='pull-right btn btn-xs default purple-stripe'>".$due.": <strong>$".$new."</strong></button>");
                        mysql_query("UPDATE fmo_locations_storages SET storage_status='Auction' WHERE storage_token='".mysql_real_escape_string($str['storage_token'])."'");
                        mysql_query("INSERT INTO fmo_locations_storages_contracts_items (item_contract_token, item_user_token, item_location_token, item_company_token, item_item, item_desc, item_qty, item_cost, item_total, item_taxable, item_commission, item_redeemable, item_discount, item_percent, item_prepay, item_adder) VALUES (
                        '".mysql_real_escape_string($ct['contract_token'])."',
                        '".mysql_real_escape_string($str['storage_occupant'])."',
                        '".mysql_real_escape_string($str['storage_location_token'])."',
                        '".mysql_real_escape_string($location['location_owner_company_token'])."',
                        '".mysql_real_escape_string("Auction Fee")."',
                        '".mysql_real_escape_string("Standard auction fee charge")."',
                        '".mysql_real_escape_string(1)."',
                        '".mysql_real_escape_string($location['location_storage_auction_fee'])."',
                        '".mysql_real_escape_string($location['location_storage_auction_fee'])."',
                        '".mysql_real_escape_string(0)."',
                        '".mysql_real_escape_string(0)."',
                        '".mysql_real_escape_string(0)."',
                        '".mysql_real_escape_string(0)."',
                        '".mysql_real_escape_string(0)."',
                        '".mysql_real_escape_string(0)."',
                        '".mysql_real_escape_string('SYSTEM')."')") or die(mysql_error());
                        $bal = json_decode(file_get_contents('https://www.formoversonly.com/dashboard/assets/app/api/storage.php?type=inv_c&luid='.$location['location_token'].'&uuid='.$str['storage_occupant'].''), true);
                        $new = number_format($bal['unpaid'], 2);

                        if($bal['unpaid'] < 0){
                            $due = "Credit";
                            $new = number_format($bal['unpaid'] * -1);
                        } else {$due = "Due"; $new = number_format($bal['unpaid'], 2); }

                        timeline_str($str['storage_occupant'], $ct['contract_token'], "SYSTEM", "Auction Fee", "<strong>$".number_format($location['location_storage_auction_fee'], 2)." auction fee charge</strong> on <strong>".date('m/d/Y', strtotime('today'))."</strong> was systematically added. <strong>(Unit #".$str['storage_unit_name'].")</strong> [By: <strong>System</strong>]  <button style='width: 150px;' class='pull-right btn btn-xs default yellow-stripe'>".$due.": <strong>$".$new."</strong></button><button style='width: 150px;' class='pull-right btn btn-xs default yellow-stripe'>Charge: <strong>$".number_format($location['location_storage_auction_fee'], 2)."</strong></button> <button style='width: 150px;' class='pull-right btn btn-xs default yellow-stripe'>&nbsp;</button>");
                    } else {
                        // nothing due, no payments is normal.
                    }
                }
            }*/
        }
    }
}

_sendText("3172018875", "[FMO]\r\nDaily CRON has finished.");
_sendText("3176716774", "[FMO]\r\nDaily CRON has finished.");
