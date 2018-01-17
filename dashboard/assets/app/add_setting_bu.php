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
    if($_GET['setting'] == 'su'){
        $contract     = $_GET['c'];
        $storage      = $_GET['su'];
        $user         = $_GET['uuid'];
        $start        = date('Y-m-d', strtotime($_POST['startdate']));
        $nextDue      = date('Y-m-d', strtotime($_POST['nextDue']));
        $deposit      = $_POST['deposit'];
        $rate_adj     = $_POST['rate_adj'];
        $address      = $_POST['address'];
        $city         = $_POST['city'];
        $state        = $_POST['state'];
        $zip          = $_POST['zip'];
        $email        = $_POST['email'];
        $phone        = $_POST['phone'];
        $tax_number   = $_POST['taxn'];
        $gate_code    = $_POST['gate'];
        $old  = number_format(0, 2);
        $bal  = number_format(0, 2);
        $unit = mysql_fetch_array(mysql_query("SELECT storage_unit_name FROM fmo_locations_storages WHERE storage_token='".mysql_real_escape_string($storage)."'"));

        foreach($_POST['alt'] as $alt){
            $alt = explode("|", $alt);
            mysql_query("INSERT INTO fmo_locations_storages_alts (alt_user_token, alt_name, alt_address, alt_phone, alt_added_by) VALUES (
            '".mysql_real_escape_string($user)."',
            '".mysql_real_escape_string($alt[0])."',
            '".mysql_real_escape_string($alt[1])."',
            '".mysql_real_escape_string(preg_replace("/[^A-Za-z0-9]/", '', $alt[2]))."',
            '".mysql_real_escape_string($_SESSION['uuid'])."')");
        }
        mysql_query("INSERT INTO fmo_locations_storages_contracts_items (item_contract_token, item_user_token, item_item, item_desc, item_qty, item_cost, item_total, item_taxable, item_commission, item_redeemable, item_discount, item_percent, item_prepay, item_adder) VALUES (
            '".mysql_real_escape_string($contract)."',
            '".mysql_real_escape_string($user)."',
            '".mysql_real_escape_string("Deposit")."',
            '".mysql_real_escape_string("Security deposit.")."',
            '".mysql_real_escape_string(1)."',
            '".mysql_real_escape_string($_POST['deposit'])."',
            '".mysql_real_escape_string($_POST['deposit'])."',
            '".mysql_real_escape_string(0)."',
            '".mysql_real_escape_string(0)."',
            '".mysql_real_escape_string(0)."',
            '".mysql_real_escape_string(0)."',
            '".mysql_real_escape_string(0)."',
            '".mysql_real_escape_string(0)."',
            '".mysql_real_escape_string($_SESSION['uuid'])."')") or die(mysql_error());
        $ba  = json_decode(file_get_contents('https://www.formoversonly.com/dashboard/assets/app/api/storage.php?type=inv_c&luid='.stlc($storage).'&uuid='.$user.''), true);
        $ol  = number_format($ba['unpaid'], 2);
        $bal = number_format($bal + $_POST['deposit'], 2);
        if($ol < 0){
            $due = "Credit";
            $new = number_format($ol * -1);
        } else {$due = "Due"; $new = number_format($ol, 2); }
        timeline_str($user, $contract, $_SESSION['uuid'], "Charge", "<strong>$".number_format($_POST['deposit'], 2)." deposit charge</strong> for <strong>".date('m/d/Y', strtotime('today'))."</strong> was systematically added. <strong>(Unit #".$unit['storage_unit_name'].")</strong> [By: <strong>System</strong>] <button style='width: 150px;' class='pull-right btn btn-xs default red-stripe'>".$due.": <strong>$".$new."</strong></button><button style='width: 150px;' class='pull-right btn btn-xs default red-stripe'>Charge: <strong>$".number_format($_POST['deposit'], 2)."</strong></button> <button style='width: 150px;' class='pull-right btn btn-xs default red-stripe'>&nbsp;</button>");


        mysql_query("INSERT INTO fmo_locations_storages_contracts_items (item_contract_token, item_user_token, item_item, item_desc, item_qty, item_cost, item_total, item_taxable, item_commission, item_redeemable, item_discount, item_percent, item_prepay, item_adder) VALUES (
            '".mysql_real_escape_string($contract)."',
            '".mysql_real_escape_string($user)."',
            '".mysql_real_escape_string("Rent")."',
            '".mysql_real_escape_string("Standard rent charge")."',
            '".mysql_real_escape_string(1)."',
            '".mysql_real_escape_string($_POST['rent'])."',
            '".mysql_real_escape_string($_POST['rent'])."',
            '".mysql_real_escape_string(0)."',
            '".mysql_real_escape_string(0)."',
            '".mysql_real_escape_string(0)."',
            '".mysql_real_escape_string(0)."',
            '".mysql_real_escape_string(0)."',
            '".mysql_real_escape_string(0)."',
            '".mysql_real_escape_string($_SESSION['uuid'])."')") or die(mysql_error());
        $ba  = json_decode(file_get_contents('https://www.formoversonly.com/dashboard/assets/app/api/storage.php?type=inv_c&luid='.stlc($storage).'&uuid='.$user.''), true);
        $ol  = number_format($ba['unpaid'], 2);
        $bal = number_format($bal + $_POST['rent'], 2);
        if($ol < 0){
            $due = "Credit";
            $new = number_format($ol * -1);
        } else {$due = "Due"; $new = number_format($ol, 2); }
        timeline_str($user, $contract, $_SESSION['uuid'], "Charge", " <strong>$".number_format($_POST['rent'], 2)." rent charge</strong> for <strong>".date('m/d/Y', strtotime('today'))."</strong> was added systematically. <strong>(Unit #".$unit['storage_unit_name'].")</strong> [By: <strong>System</strong>] <button style='width: 150px;' class='pull-right btn btn-xs default red-stripe'>".$due.": <strong>$".$new."</strong></button><button style='width: 150px;' class='pull-right btn btn-xs default red-stripe'>Charge: <strong>$".number_format($_POST['rent'], 2)."</strong></button> <button style='width: 150px;' class='pull-right btn btn-xs default red-stripe'>&nbsp;</button>");

        if($_POST['amount'] > 0){
            $loc       = $_GET['luid'];
            $by        = $_SESSION['uuid'];
            $cuid      = $_SESSION['cuid'];
            $type      = $_POST['type'];
            $amount    = $_POST['amount'];
            $notes     = $_POST['notes'];
            $charge    = $_POST['charge'];
            if(isset($_GET['ckpay']) && $_GET['ckpay'] == true){
                $type   = "Credit/Debt";
                $by     = $_GET['uuid'];
                $cuid   = $_GET['cuid'];
            }
            if($type == 'Credit/Debt'){
                $fee     = ($_POST['amt_b4'] * $location['location_creditcard_fee']);
                $old  = number_format($bal);
                $bal  = number_format($retail + $fee, 2);
                if($bal < 0){
                    $due = "Credit";
                    $new = number_format($bal * -1, 2);
                } else {$due = "Due"; $new = number_format($bal, 2); }
                timeline_str($user, $contract, $by, "Charge", " <strong>$".number_format($fee, 2)." (%".number_format($location['location_creditcard_fee'] * 100, 0).") ".$type."</strong> fee applied on <strong>".date('m/d/Y', strtotime('today'))."</strong> was added by <strong>".name($_SESSION['uuid'])."</strong> <button style='width: 150px;' class='pull-right btn btn-xs default red-stripe'>".$due.": <strong>$".$new."</strong></button><button style='width: 150px;' class='pull-right btn btn-xs default red-stripe'>Charge: <strong>$".number_format($fee, 2)."</strong></button> <button style='width: 150px;' class='pull-right btn btn-xs default red-stripe'>&nbsp;</button>");
            }

            mysql_query("INSERT INTO fmo_locations_storages_contracts_payments (payment_user_token, payment_company_token, payment_transaction_id, payment_type, payment_amount, payment_detail, payment_charge_token, payment_by_user_token) VALUES (
            '".mysql_real_escape_string($user)."',
            '".mysql_real_escape_string($cuid)."',
            '".mysql_real_escape_string(struuid(true))."',
            '".mysql_real_escape_string($type)."',
            '".mysql_real_escape_string($amount)."',
            '".mysql_real_escape_string($notes)."',
            '".mysql_real_escape_string($charge)."',
            '".mysql_real_escape_string($by)."')");

            $old  = number_format($bal);
            $bal  = number_format($bal - $amount, 2);

            if($bal < 0){
                $due = "Credit";
                $new = number_format($bal * -1);
            } else {$due = "Due"; $new = number_format($bal, 2); }

            timeline_str($user, $contract, $by, "Payment", "<strong>$".number_format($amount, 2)." ".$type."</strong> payment for <strong>".date('m/d/Y', strtotime('today'))."</strong> was added by <strong>".name($_SESSION['uuid'])."</strong>  <button style='width: 150px;' class='pull-right btn btn-xs default green-stripe'>".$due.": <strong>$".$new."</strong></button><button style='width: 150px;' class='pull-right btn btn-xs default green-stripe'>&nbsp;</button> <button style='width: 150px;' class='pull-right btn btn-xs default green-stripe'>Payment: <strong>$".number_format($amount, 2)."</strong></button>");

        }


        if(!empty($storage) && !empty($user)){
            mysql_query("INSERT INTO fmo_locations_storages_contracts (contract_token, contract_storage_token, contract_user_token, contract_start, contract_next_due, contract_deposit, contract_rate_adj, contract_address, contract_city, contract_state, contract_zip, contract_email, contract_phone, contract_tax_number, contract_gate_code, contract_by) VALUES (
            '".mysql_real_escape_string($contract)."',
            '".mysql_real_escape_string($storage)."',
            '".mysql_real_escape_string($user)."',
            '".mysql_real_escape_string($start)."',
            '".mysql_real_escape_string($nextDue)."',
            '".mysql_real_escape_string($deposit)."',
            '".mysql_real_escape_string($rate_adj)."',
            '".mysql_real_escape_string($address)."',
            '".mysql_real_escape_string($city)."',
            '".mysql_real_escape_string($state)."',
            '".mysql_real_escape_string($zip)."',
            '".mysql_real_escape_string($email)."',
            '".mysql_real_escape_string($phone)."',
            '".mysql_real_escape_string($tax_number)."',
            '".mysql_real_escape_string($gate_code)."',
            '".mysql_real_escape_string($_SESSION['uuid'])."')") or die(mysql_error());
            mysql_query("UPDATE fmo_locations_storages SET storage_occupant='".mysql_real_escape_string($user)."', storage_contract_token='".mysql_real_escape_string($contract)."', storage_last_occupied='".mysql_real_escape_string($start)."', storage_status='Occupied' WHERE storage_token='".mysql_real_escape_string($storage)."'");
        }
    }
    if($_GET['setting'] == 'daily_note'){
        $note      = $_POST['daily_note'];
        $date      = date('Y-m-d', strtotime($_POST['date']));
        $location  = $_GET['luid'];

        mysql_query("INSERT INTO fmo_locations_activites (activity_location_token, activity_type, activity_date, activity_notes, activity_by_user_token) VALUES (
        '".mysql_real_escape_string($location)."',
        '".mysql_real_escape_string(0)."',
        '".mysql_real_escape_string($date)."',
        '".mysql_real_escape_string($note)."',
        '".mysql_real_escape_string($_SESSION['uuid'])."')") or die(mysql_error());
    }
    if($_GET['setting'] == 'ticket'){
        $uuid          = $_GET['uuid'];
        $ev            = $_GET['ev'];
        $company       = $_GET['cuid'];
        $location      = $_GET['luid'];
        $department    = $_POST['department'];
        $priority      = $_POST['priority'];
        if(isset($_POST['item'])){
            $message   = "Item: ".$_POST['item'].", Padded: ".$_POST['padded'].", Weight: ".$_POST['weight']."&#13;&#10;Message: ".$_POST['message'];
        } else {
            $message = $_POST['message'];
        }
        $status        = 0;
        /*
         * 0 = Open - new
         * 1 = Open - waiting for staff reply
         * 2 = Open - waiting for customer reply
         * 3 = Closed - Solved
         */

        $check = mysql_query("SELECT ticket_token, ticket_event_token FROM fmo_locations_tickets WHERE ticket_event_token='".$ev."' AND NOT ticket_event_token=''");
        $checked = mysql_fetch_array($check);
        if(mysql_num_rows($check) > 0 || (!empty($checked['ticket_event_token']) || $checked['ticket_event_token'] != '')){
            $token         = $checked['ticket_token'];
            $data['tk']    = $checked['ticket_token'];
            $data['status'] = 'no';
        } else {
            $token         = struuid(true);
            mysql_query("INSERT INTO fmo_locations_tickets (ticket_token, ticket_user_token, ticket_event_token, ticket_company_token, ticket_location_token, ticket_department, ticket_priority, ticket_status, ticket_last_contacted_by) VALUES (
            '".mysql_real_escape_string($token)."',
            '".mysql_real_escape_string($uuid)."',
            '".mysql_real_escape_string($ev)."',
            '".mysql_real_escape_string($company)."',
            '".mysql_real_escape_string($location)."',
            '".mysql_real_escape_string($department)."',
            '".mysql_real_escape_string($priority)."',
            '".mysql_real_escape_string($status)."',
            '".mysql_real_escape_string($uuid)."')");
            $data['tk']    = $token;
            $data['status'] = 'yes';
        }


        /*
         * Now, we add our first message:
         * cOoL :-)
         */
        $msgTk        = struuid(true);
        if(isset($_GET['m_token'])){
            $m_token = $_GET['m_token'];
        } else {
            $m_token = $uuid;
        }
        mysql_query("INSERT INTO fmo_locations_tickets_messages (message_token, message_ticket_token, message_user_token, message_message) VALUES (
        '".mysql_real_escape_string($msgTk)."',
        '".mysql_real_escape_string($token)."',
        '".mysql_real_escape_string($m_token)."',
        '".mysql_real_escape_string($message)."')");

        if($department == 'Software Issues'){
            $admin = array('DH8I8KKVVXLZAJA5G', 'DJ5RELUMTA7QPHWJK');
            mysql_query("INSERT INTO fmo_locations_tickets_messages (message_token, message_ticket_token, message_user_token, message_message) VALUES (
            '".mysql_real_escape_string($msgTk)."',
            '".mysql_real_escape_string($token)."',
            '".mysql_real_escape_string($admin[rand(0, 1)])."',
            '".mysql_real_escape_string("Thanks for getting in touch! This is an automatic message to let you know we have been notified by text--please allow up to 12 hours before spamming us (which is okay!).")."')");
        }

        if(is_uploaded_file($_FILES['file']['tmp_name'])){
            $fileName  = struuid();
            $file_ext = strtolower(substr($_FILES['file']['name'], strripos($_FILES['file']['name'], '.')));
            $uploaddir = '../upload/tickets/';
            $uploadfile = $uploaddir . $fileName;

            if(move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile . $file_ext)){
                $image = imagecreatefromjpeg($uploadfile . $file_ext);
                $exif = exif_read_data($uploadfile . $file_ext);
                $ort   = $exif['Orientation'];
                $width = $exif['COMPUTED']['Width'];
                resample($uploadfile . $file_ext, $uploadfile . $file_ext, $width, $ort);

                $link = "//www.formoversonly.com/dashboard/assets/upload/tickets/". $fileName . $file_ext;

                mysql_query("INSERT INTO fmo_locations_tickets_messages_documents (document_message_token, document_link, document_by_user_token) VALUES (
                '".mysql_real_escape_string($msgTk)."',
                '".mysql_real_escape_string($link)."',
                '".mysql_real_escape_string($uuid)."')") or die(mysql_error());
            }
        }

        $key = 'AIzaSyDxLua1UKdf-637NvG5NgBuhb0DYVQ77cg';
        $googer = new GoogleUrlApi($key);
        $company = mysql_fetch_array(mysql_query("SELECT ticket_id, ticket_company_token FROM fmo_locations_tickets WHERE ticket_token='".mysql_real_escape_string($token)."'"));
        $claim_link = $googer->shorten("https://www.formoversonly.com/dashboard/assets/public/ticket.php?ev=".$company['ticket_event_token']."&tk=".$token."&uuid=".$uuid."&cuid=".$company['ticket_company_token']);
        $msg        = "[".companyName($company['ticket_company_token'])."]\r\nSupport Ticket #".$company['ticket_id']." created for you:\r\n".$claim_link;
        _sendText(phone2($uuid), $msg);
        _sendText("3172018875", "[FMO] Software Issue:\r\n".name($uuid)."\r\n".$googer->shorten("https://www.formoversonly.com/dashboard/assets/public/ticket.php?ev=".$company['ticket_event_token']."&tk=".$token."&uuid=DH8I8KKVVXLZAJA5G&cuid=".$company['ticket_company_token'].""));
        _sendText("3176716774", "[FMO] Software Issue:\r\n".name($uuid)."\r\n".$googer->shorten("https://www.formoversonly.com/dashboard/assets/public/ticket.php?ev=".$company['ticket_event_token']."&tk=".$token."&uuid=DJ5RELUMTA7QPHWJK&cuid=".$company['ticket_company_token'].""));
        echo json_encode($data);

    }
    if($_GET['setting'] == 'ticket_reply'){

        /*
         * Now, we add more messages:
         * cOoL :-)
         */
        $msgTk        = struuid(true);
        mysql_query("INSERT INTO fmo_locations_tickets_messages (message_token, message_ticket_token, message_user_token, message_message) VALUES (
        '".mysql_real_escape_string($msgTk)."',
        '".mysql_real_escape_string($_GET['tk'])."',
        '".mysql_real_escape_string($_GET['uuid'])."',
        '".mysql_real_escape_string($_POST['message'])."')");

        if(is_uploaded_file($_FILES['file']['tmp_name'])){
            $fileName  = struuid();
            $file_ext = strtolower(substr($_FILES['file']['name'], strripos($_FILES['file']['name'], '.')));
            $uploaddir = '../upload/tickets/';
            $uploadfile = $uploaddir . $fileName;

            if(move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile . $file_ext)){
                $image = imagecreatefromjpeg($uploadfile . $file_ext);
                $exif = exif_read_data($uploadfile . $file_ext);
                $ort   = $exif['Orientation'];
                $width = $exif['COMPUTED']['Width'];
                resample($uploadfile . $file_ext, $uploadfile . $file_ext, $width, $ort);

                $link = "//www.formoversonly.com/dashboard/assets/upload/tickets/". $fileName . $file_ext;

                mysql_query("INSERT INTO fmo_locations_tickets_messages_documents (document_message_token, document_link, document_by_user_token) VALUES (
                '".mysql_real_escape_string($msgTk)."',
                '".mysql_real_escape_string($link)."',
                '".mysql_real_escape_string($_GET['uuid'])."')") or die(mysql_error());
            }
        }
        $data['tk'] = $_GET['tk'];

        $u['parti'] = array();
        $company = mysql_fetch_array(mysql_query("SELECT ticket_id, ticket_company_token FROM fmo_locations_tickets WHERE ticket_token='".mysql_real_escape_string($_GET['tk'])."'"));
        $party   = mysql_query("SELECT message_user_token FROM fmo_locations_tickets_messages WHERE message_ticket_token='".mysql_real_escape_string($_GET['tk'])."' ORDER BY message_user_token ASC");
        if(mysql_num_rows($party) > 0){
            $key = 'AIzaSyDxLua1UKdf-637NvG5NgBuhb0DYVQ77cg';
            $googer = new GoogleUrlApi($key);
            while($parti = mysql_fetch_assoc($party)){
                if(!in_array($parti['message_user_token'], $u['parti'])){
                    $u['parti'][] = $parti['message_user_token'];
                }
            }
            foreach($u['parti'] as $notify){
                if($_GET['uuid'] != $notify){
                    $claim_link = $googer->shorten("https://www.formoversonly.com/dashboard/assets/public/ticket.php?ev=".$company['ticket_event_token']."&tk=".$_GET['tk']."&uuid=".$notify."cuid=".$company['ticket_company_token']);
                    $msg        = "[".companyName($company['ticket_company_token'])."]\r\nSupport Ticket #".$company['ticket_id']." has new replies:\r\n".$claim_link;
                    _sendText(phone2($notify), $msg);
                }
            }
        }
        mysql_query("UPDATE fmo_locations_tickets SET ticket_status='".mysql_real_escape_string($_GET['s'])."', ticket_last_contacted_by='".mysql_real_escape_string($_GET['uuid'])."' WHERE ticket_token='".mysql_real_escape_string($_GET['tk'])."'");

        echo json_encode($data);
    }
    if($_GET['setting'] == 'accident'){
        $datetime  = $_POST['datetime'];
        $asset     = $_POST['asset'];
        $address   = $_POST['address'];
        $city      = $_POST['city'];
        $state     = $_POST['state'];
        $deaths    = $_POST['deaths'];
        $nfi       = $_POST['nfi'];
        $hazmat    = $_POST['hazmat'];
        $driver    = $_POST['driver'];
        $copies    = $_POST['copies'];

        mysql_query("INSERT INTO fmo_locations_accidents (accident_location_token, accident_asset, accident_timestamp, accident_address, accident_city, accident_state, accident_deaths, accident_nfi, accident_hazmat, accident_driver, accident_insurance_report, accident_by_user_token) VALUES (
        '".mysql_real_escape_string($_GET['luid'])."',
        '".mysql_real_escape_string($asset)."',
        '".mysql_real_escape_string($datetime)."',
        '".mysql_real_escape_string($address)."',
        '".mysql_real_escape_string($city)."',
        '".mysql_real_escape_string($state)."',
        '".mysql_real_escape_string($deaths)."',
        '".mysql_real_escape_string($nfi)."',
        '".mysql_real_escape_string($hazmat)."',
        '".mysql_real_escape_string($driver)."',
        '".mysql_real_escape_string($copies)."',
        '".mysql_real_escape_string($_SESSION['uuid'])."')");
    }
    if($_GET['setting'] == 'maintanence'){
        $asset  = $_POST['asset'];
        $desc   = $_POST['description'];
        $type   = $_POST['type'];
        $date   = date('Y-m-d');
        $by     = $_POST['by'];
        $cost   = $_POST['cost'];
        $po     = $_POST['po_number'];
        $miles  = $_POST['mileage'];
        $luid   = $_GET['luid'];
        $cuid   = $_SESSION['cuid'];

        mysql_query("INSERT INTO fmo_locations_assets_records (record_asset_id, record_desc, record_type, record_by, record_cost, record_po_number, record_mileage, record_by_user_token) VALUES (
        '".mysql_real_escape_string($asset)."',
        '".mysql_real_escape_string($desc)."',
        '".mysql_real_escape_string($type)."',
        '".mysql_real_escape_string($by)."',
        '".mysql_real_escape_string($cost)."',
        '".mysql_real_escape_string($po)."',
        '".mysql_real_escape_string($miles)."',
        '".mysql_real_escape_string($_SESSION['uuid'])."')") or die(mysql_error());
    }
    if($_GET['setting'] == 'teller') {
        $fileName = struuid();
        $file_ext = strtolower(substr($_FILES['ticket']['name'], strripos($_FILES['ticket']['name'], '.')));
        $uploaddir = '../upload/tellers/';
        $uploadfile = $uploaddir . $fileName;

        if(move_uploaded_file($_FILES['ticket']['tmp_name'], $uploadfile . $file_ext)){
            $image = imagecreatefromjpeg($uploadfile . $file_ext);
            $exif = exif_read_data($uploadfile . $file_ext);
            $ort   = $exif['Orientation'];
            $width = $exif['COMPUTED']['Width'];
            resample($uploadfile . $file_ext, $uploadfile . $file_ext, $width, $ort);

            $link = "//www.formoversonly.com/dashboard/assets/upload/tellers/" . $fileName . $file_ext;

            mysql_query("UPDATE fmo_locations_deposits SET deposit_teller='".mysql_real_escape_string($link)."', deposit_comments='".mysql_real_escape_string($_POST['comments'])."' WHERE deposit_token='".mysql_real_escape_string($_GET['dpt'])."'") or die(mysql_error());
       
		} 
    }
    if($_GET['setting'] == 'deposit'){
        $deposit = $_POST['d'];
        $luid    = $_GET['luid'];
        $cuid    = $_GET['cuid'];
        $ids     = explode(',', $_POST['ids']);
        $key = 'AIzaSyDxLua1UKdf-637NvG5NgBuhb0DYVQ77cg';
        $googer = new GoogleUrlApi($key);
        $confirm = $googer->shorten("https://www.formoversonly.com/dashboard/assets/public/deposits.php?dpt=".$deposit);
        $amount = 0;
        foreach($ids as $id){
            mysql_query("UPDATE fmo_locations_events_payments SET payment_deposit_token='".mysql_real_escape_string($deposit)."' WHERE payment_transaction_id='".mysql_real_escape_string($id)."'");
            $payment = mysql_fetch_array(mysql_query("SELECT payment_amount, payment_payout_amount FROM fmo_locations_events_payments WHERE payment_transaction_id='".mysql_real_escape_string($id)."'"));
            $amount  += number_format($payment['payment_amount'] - $payment['payment_payout_amount'], 2, '.', '');
        }

        mysql_query("INSERT INTO fmo_locations_deposits (deposit_token, deposit_location_token, deposit_company_token, deposit_amount, deposit_by_user_token) VALUES (
        '".mysql_real_escape_string($deposit)."',
        '".mysql_real_escape_string($luid)."',
        '".mysql_real_escape_string($cuid)."',
        '".mysql_real_escape_string($amount)."',
        '".mysql_real_escape_string($_SESSION['uuid'])."')");

        _sendText(phone2($_SESSION['uuid']), "[".companyName($_SESSION['cuid'])."]\r\nFinish verifying your deposit here:\r\n".$confirm);
    }
    if($_GET['setting'] == 'claimImage'){
        $fileName  = struuid();
        $file_ext = strtolower(substr($_FILES['file']['name'], strripos($_FILES['file']['name'], '.')));
        $uploaddir = '../upload/claims/';
        $uploadfile = $uploaddir . $fileName;

        if(move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile . $file_ext)){
            $image = imagecreatefromjpeg($uploadfile . $file_ext);
            $exif = exif_read_data($uploadfile . $file_ext);
            $ort   = $exif['Orientation'];
            $width = $exif['COMPUTED']['Width'];
            resample($uploadfile . $file_ext, $uploadfile . $file_ext, $width, $ort);

            $link = "//www.formoversonly.com/dashboard/assets/upload/claims/". $fileName . $file_ext;

            mysql_query("INSERT INTO fmo_locations_events_claims_images (
            image_event_token, 
            image_link
            ) VALUES (
            '" . mysql_real_escape_string($_GET['ev']) . "',
            '" . mysql_real_escape_string($link) . "')") or die(mysql_error());
        }


    }
    if($_GET['setting'] == 'expense'){
        $desc   = $_POST['description'];
        $name   = $_POST['name'];
        $date   = date('Y-m-d', strtotime($_POST['date']));
        $type   = $_POST['type'];
        $reason = $_POST['reason'];
        $amount = $_POST['amount'];
        $luid   = $_GET['luid'];
        $cuid   = $_SESSION['cuid'];

        mysql_query("INSERT INTO fmo_locations_expenses (expense_location_token, expense_company_token, expense_desc, expense_name, expense_date, expense_type, expense_reason, expense_amount, expense_by) VALUES (
        '".mysql_real_escape_string($luid)."',
        '".mysql_real_escape_string($cuid)."',
        '".mysql_real_escape_string($desc)."',
        '".mysql_real_escape_string($name)."',
        '".mysql_real_escape_string($date)."',
        '".mysql_real_escape_string($type)."',
        '".mysql_real_escape_string($reason)."',
        '".mysql_real_escape_string($amount)."',
        '".mysql_real_escape_string($_SESSION['uuid'])."')");
    }
    if($_GET['setting'] == 'review'){
        $token          = $_GET['ev'];
        $ctoken         = $_GET['cuid'];
        $ltoken         = $_GET['luid'];
        $rating         = $_POST['rating'];
        $comments       = $_POST['comments'];
        $name           = $_POST['name'];
        $anonymous      = $_POST['anonymous'];

        $event_user = mysql_fetch_array(mysql_query("SELECT event_user_token FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($_GET['ev'])."'"));

        mysql_query("INSERT INTO fmo_locations_events_reviews (review_event_token, review_company_token, review_location_token, review_rating, review_comments, review_name, review_anonymous) VALUES (
        '".mysql_real_escape_string($token)."',
        '".mysql_real_escape_string($ctoken)."',
        '".mysql_real_escape_string($ltoken)."',
        '".mysql_real_escape_string($rating)."',
        '".mysql_real_escape_string($comments)."',
        '".mysql_real_escape_string($name)."',
        '".mysql_real_escape_string($anonymous)."')");
        timeline_event($token, $event_user['event_user_token'], "Review", "<strong>".name($event_user['event_user_token'])."</strong> submitted a new review.");
    }
    if($_GET['setting'] == 'claim'){
        $token          = $_GET['ev'];
        $item           = $_POST['item'];
        $padded         = $_POST['padded'];
        $weight         = $_POST['weight'];
        $comments       = $_POST['comments'];
        $remote_ip      = $_SERVER['REMOTE_ADDR'];

        $event_user = mysql_fetch_array(mysql_query("SELECT event_user_token FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($_GET['ev'])."'"));

        mysql_query("INSERT INTO fmo_locations_events_claims (claim_event_token, claim_item, claim_padded, claim_weight, claim_comments, claim_remote_addr) VALUES (
        '".mysql_real_escape_string($token)."',
        '".mysql_real_escape_string($item)."',
        '".mysql_real_escape_string($padded)."',
        '".mysql_real_escape_string($weight)."',
        '".mysql_real_escape_string($comments)."',
        '".mysql_real_escape_string($remote_ip)."')");
        timeline_event($token, $event_user['event_user_token'], "Claim", "<strong>".name($event_user['event_user_token'])."</strong> submitted a new claim.");
    }
    if($_GET['setting'] == 'laborer'){
        $token          = $_GET['ev'];
        $role           = $_POST['role'];
        $laborer        = $_POST['laborer'];
        $wage           = mysql_fetch_array(mysql_query("SELECT user_employer_rate, user_employer_commission, user_phone FROM fmo_users WHERE user_token='".mysql_real_escape_string($laborer)."'"));
        $desc           = $_POST['desc'];
        $hp             = $_POST['hp'];
        $tip            = $_POST['tip'];
        $event          = mysql_fetch_array(mysql_query("SELECT event_date_start, event_time FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($token)."'"));
        $time           = explode("to", $event['event_time']);
        $date           = $event['event_date_start'];
        $location       = mysql_fetch_array(mysql_query("SELECT location_name FROM fmo_locations WHERE location_token='".$_GET['luid']."'"));
        if(isset($_POST['dtd'])){
            $date           = date('Y-m-d g:i:s', strtotime($_POST['dtd']));
            mysql_query("INSERT INTO fmo_locations_events_laborers (laborer_timestamp, laborer_event_token, laborer_user_token, laborer_rate, laborer_commission, laborer_role, laborer_desc, laborer_hours_worked, laborer_tip, laborer_by_user_token) VALUES (
            '".mysql_real_escape_string($date)."',
            '".mysql_real_escape_string($token)."',
            '".mysql_real_escape_string($laborer)."',
            '".mysql_real_escape_string($wage['user_employer_rate'])."',
            '".mysql_real_escape_string($wage['user_employer_commission'])."',
            '".mysql_real_escape_string($role)."',
            '".mysql_real_escape_string($desc)."',
            '".mysql_real_escape_string($hp)."',
            '".mysql_real_escape_string($tip)."',
            '".mysql_real_escape_string($_SESSION['uuid'])."')");
        } else {
            mysql_query("INSERT INTO fmo_locations_events_laborers (laborer_event_token, laborer_user_token, laborer_rate, laborer_commission, laborer_role, laborer_desc, laborer_hours_worked, laborer_tip, laborer_by_user_token) VALUES (
            '".mysql_real_escape_string($token)."',
            '".mysql_real_escape_string($laborer)."',
            '".mysql_real_escape_string($wage['user_employer_rate'])."',
            '".mysql_real_escape_string($wage['user_employer_commission'])."',
            '".mysql_real_escape_string($role)."',
            '".mysql_real_escape_string($desc)."',
            '".mysql_real_escape_string($hp)."',
            '".mysql_real_escape_string($tip)."',
            '".mysql_real_escape_string($_SESSION['uuid'])."')");
            if($event['event_date_start'] >= date('Y-m-d')){
                _sendText($wage['user_phone'], "[".companyName($_SESSION['cuid'])."]:\r\nJob Assignment - ".date('g:i A', strtotime($time[0]." - 1 hour"))."\r\n".$location['location_name']." on ".date("M d, Y", strtotime($date))."");
            }
        }
    }
    if($_GET['setting'] == 'usr_lic'){
        $token          = $_GET['uuid'];
        $type           = $_POST['type'];
        $state          = $_POST['state'];
        $prefix         = $_POST['prefix'];
        $number         = $_POST['number'];

        mysql_query("INSERT INTO fmo_users_licenses (license_user_token, license_company_token, license_type, license_state, license_prefix, license_number) VALUES (
        '".mysql_real_escape_string($token)."',
        '".mysql_real_escape_string($_SESSION['cuid'])."',
        '".mysql_real_escape_string($type)."',
        '".mysql_real_escape_string($state)."',
        '".mysql_real_escape_string($prefix)."',
        '".mysql_real_escape_string($number)."')");
    }
    if($_GET['setting'] == 'marketer'){
        $location       = $_GET['luid'];
        $type           = $_POST['type'];
        $contact        = $_POST['fullname'];
        $phone          = $_POST['phone'];
        $email          = $_POST['email'];
        $address        = $_POST['address'];
        $address2       = $_POST['address2'];
        $city           = $_POST['city'];
        $state          = $_POST['state'];
        $apt            = $_POST['apt'];
        $zip            = $_POST['zip'];
        $company        = $_POST['company'];

        mysql_query("INSERT INTO fmo_locations_marketers (marketer_location_token, marketer_type, marketer_contact, marketer_phone, marketer_email, marketer_address, marketer_address2, marketer_city, marketer_state, marketer_apt, marketer_zip, marketer_company, marketer_by_user_token) VALUES (
        '".mysql_real_escape_string($location)."',
        '".mysql_real_escape_string($type)."',
        '".mysql_real_escape_string($contact)."',
        '".mysql_real_escape_string($phone)."',
        '".mysql_real_escape_string($email)."',
        '".mysql_real_escape_string($address)."',
        '".mysql_real_escape_string($address2)."',
        '".mysql_real_escape_string($city)."',
        '".mysql_real_escape_string($state)."',
        '".mysql_real_escape_string($apt)."',
        '".mysql_real_escape_string($zip)."',
        '".mysql_real_escape_string($company)."',
        '".mysql_real_escape_string($_SESSION['uuid'])."')") or die(mysql_error());
    }
    if($_GET['setting'] == 'asset'){
        $token          = $_GET['luid'];
        $ctoken         = $_GET['cuid'];
        $type           = $_POST['type'];
        $vin            = $_POST['vin'];
        $year           = $_POST['year'];
        $make           = $_POST['make'];
        $model          = $_POST['model'];
        $color          = $_POST['color'];
        $dop            = $_POST['date_of_purchase'];
        $price          = $_POST['price'];
        $tire_size      = $_POST['tire_size'];
        $agent          = $_POST['agent'];
        $plate          = $_POST['plate'];
        $comments       = $_POST['comments'];
        $last_dot_inspec= $_POST['last_dot_inspec'];
        $by             = $_SESSION['uuid'];

        $type_query   = mysql_query("SELECT asset_id FROM fmo_locations_assets WHERE asset_company_token='".mysql_real_escape_string($ctoken)."' AND asset_type='".mysql_real_escape_string($type)."'");
        $unit_id      = mysql_num_rows($type_query) + 1;
        if($type == 'Moving Truck'){
            $unit_name = 'MT';
        }elseif($type == 'Office Car'){
            $unit_name = 'OV';
        }elseif($type == 'Trailer'){
            $unit_name = 'TR';
        }elseif($type == 'Other'){
            $unit_name = 'O';
        }
        $unit_number = $unit_name.$unit_id;

        mysql_query("INSERT INTO fmo_locations_assets (asset_location_token, asset_company_token, asset_type, asset_desc, asset_vin, asset_year, asset_make, asset_model, asset_color, asset_dop, asset_price, asset_tire_size, asset_agent, asset_plate, asset_comments, asset_last_dot_inspec, asset_by_user_token) VALUES (
        '".mysql_real_escape_string($token)."',
        '".mysql_real_escape_string($ctoken)."',
        '".mysql_real_escape_string($type)."',
        '".mysql_real_escape_string($unit_number)."',
        '".mysql_real_escape_string($vin)."',
        '".mysql_real_escape_string($year)."',
        '".mysql_real_escape_string($make)."',
        '".mysql_real_escape_string($model)."',
        '".mysql_real_escape_string($color)."',
        '".mysql_real_escape_string($dop)."',
        '".mysql_real_escape_string($price)."',
        '".mysql_real_escape_string($tire_size)."',
        '".mysql_real_escape_string($agent)."',
        '".mysql_real_escape_string($plate)."',
        '".mysql_real_escape_string($comments)."',
        '".mysql_real_escape_string($last_dot_inspec)."',
        '".mysql_real_escape_string($by)."')") or die(mysql_error());
    }
    if($_GET['setting'] == 'ev_document'){
        $token = $_GET['ev'];
        $type  = $_POST['file_type'];
        $desc  = $_POST['file_desc'];
        $by    = $_SESSION['uuid'];
        $fileName  = struuid();
        $file_ext = strtolower(substr($_FILES['file']['name'], strripos($_FILES['file']['name'], '.')));
        $uploaddir = '../upload/ev_documents/';
        $uploadfile = $uploaddir . $fileName;

        if(move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile . $file_ext)){
            $image = imagecreatefromjpeg($uploadfile . $file_ext);
            $exif = exif_read_data($uploadfile . $file_ext);
            $ort   = $exif['Orientation'];
            $width = $exif['COMPUTED']['Width'];
            resample($uploadfile . $file_ext, $uploadfile . $file_ext, $width, $ort);

            $link = "//www.formoversonly.com/dashboard/assets/upload/ev_documents/". $fileName . $file_ext;

            mysql_query("INSERT INTO fmo_locations_events_documents (document_event_token, document_desc, document_link, document_by_user_token) VALUES (
            '".mysql_real_escape_string($token)."',
            '".mysql_real_escape_string($desc)."',
            '".mysql_real_escape_string($link)."',
            '".mysql_real_escape_string($by)."')") or die(mysql_error());
        }

    }
    if($_GET['setting'] == 'document'){
        $token = $_GET['uuid'];
        $type  = $_POST['file_type'];
        $desc  = $_POST['file_desc'];
        $by    = $_SESSION['uuid'];
		$parts     = explode(".", $_FILES['file']['name']);
		$new_name  = struuid().".".strtolower($parts[1]);
        $new_dir   = '../upload/documents/'.$new_name;
		
		
        if(move_uploaded_file($_FILES['file']['tmp_name'], '../upload/documents/'.$new_name)){
            $image = imagecreatefromjpeg($new_dir);
            $exif  = exif_read_data($new_dir);
            $ort   = $exif['Orientation'];
            $width = $exif['COMPUTED']['Width'];

            resample($new_dir, $new_dir, $width, $ort);
            $link = "//www.formoversonly.com/dashboard/assets/upload/documents/". $new_name;

            mysql_query("INSERT INTO fmo_users_employee_documents (document_user_token, document_type, document_desc, document_link, document_by_user_token) VALUES (
            '".mysql_real_escape_string($token)."',
            '".mysql_real_escape_string($type)."',
            '".mysql_real_escape_string($desc)."',
            '".mysql_real_escape_string($link)."',
            '".mysql_real_escape_string($by)."')");
			echo "success ".$new_dir;
        } else {
			echo "fuck ".$new_dir;
		}

    }
    if($_GET['setting'] == 'evasset'){
        mysql_query("INSERT INTO fmo_locations_events_assets (asset_name, asset_event_token, asset_by_user_token) VALUES (
        '".mysql_real_escape_string($_POST['asset'])."',
        '".mysql_real_escape_string($_GET['ev'])."',
        '".mysql_real_escape_string($_SESSION['uuid'])."')");
        echo $_POST['asset'];
    }
    if($_GET['setting'] == 'asset_doc'){
        $id    = $_GET['id'];
        $type  = $_POST['file_type'];
        $desc  = $_POST['file_desc'];
        $by    = $_SESSION['uuid'];
        $fileName  = struuid();
        $file_ext = strtolower(substr($_FILES['file']['name'], strripos($_FILES['file']['name'], '.')));
        $uploaddir = '../upload/asset_docs/';
        $uploadfile = $uploaddir . $fileName;

        if(move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile . $file_ext)){
            $image = imagecreatefromjpeg($uploadfile . $file_ext);
            $exif = exif_read_data($uploadfile . $file_ext);
            $ort   = $exif['Orientation'];
            $width = $exif['COMPUTED']['Width'];
            resample($uploadfile . $file_ext, $uploadfile . $file_ext, $width, $ort);
            $link = "//www.formoversonly.com/dashboard/assets/upload/asset_docs/". $fileName . $file_ext;

            mysql_query("INSERT INTO fmo_locations_assets_documents (document_asset_id, document_type, document_desc, document_link, document_by_user_token) VALUES (
            '".mysql_real_escape_string($id)."',
            '".mysql_real_escape_string($type)."',
            '".mysql_real_escape_string($desc)."',
            '".mysql_real_escape_string($link)."',
            '".mysql_real_escape_string($by)."')") or die(mysql_error());
        }


    }
    if($_GET['setting'] == 'childsupport'){
        $token          = $_GET['uuid'];
        $case_name      = $_POST['case_name'];
        $case_number    = $_POST['case_number'];
        $amount         = $_POST['amount'];
        $address        = $_POST['address'];
        $address2       = $_POST['address2'];
        $city           = $_POST['city'];
        $state          = $_POST['state'];
        $zip            = $_POST['zip'];
        $pay_allowed    = $_POST['pay_allowed'];
        $pay_period     = $_POST['pay_period'];
        $comments       = $_POST['comments'];
        $by             = $_SESSION['uuid'];

        mysql_query("INSERT INTO fmo_users_employee_childsupports (childsupport_user_token, childsupport_case_name, childsupport_case_number, childsupport_amount, childsupport_address, childsupport_address2, childsupport_city, childsupport_state, childsupport_zip, childsupport_pay_allowed, childsupport_pay_period, childsupport_comments, childsupport_by_user_token) VALUES (
        '".mysql_real_escape_string($token)."',
        '".mysql_real_escape_string($case_name)."',
        '".mysql_real_escape_string($case_number)."',
        '".mysql_real_escape_string($amount)."',
        '".mysql_real_escape_string($address)."',
        '".mysql_real_escape_string($address2)."',
        '".mysql_real_escape_string($city)."',
        '".mysql_real_escape_string($state)."',
        '".mysql_real_escape_string($zip)."',
        '".mysql_real_escape_string($pay_allowed)."',
        '".mysql_real_escape_string($pay_period)."',
        '".mysql_real_escape_string($comments)."',
        '".mysql_real_escape_string($by)."')") or die(mysql_error());
        timeline_log($token, $by, "Child Support Case", "Case <strong>$case_name</strong> created. Comments: <strong>$comments</strong>");
    }
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
        $id = mysql_insert_id();
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
            $end                     = date('Y-m-d', strtotime($start." +13 days"));
            $hours = array();
            $prev  = mysql_query("
                            SELECT advance_requested FROM fmo_users_employee_advances
                            WHERE (advance_timestamp>='".mysql_real_escape_string($start)."' AND advance_timestamp<'".mysql_real_escape_string($end)."') AND advance_user_token='".mysql_real_escape_string($token)."'");
            $hours = mysql_query("
                            SELECT timeclock_user, timeclock_hours FROM fmo_users_employee_timeclock 
                            WHERE (timeclock_clockout>='".mysql_real_escape_string($start)."' AND timeclock_clockout<'".mysql_real_escape_string($end)."') AND timeclock_user='".mysql_real_escape_string($token)."'") or die(mysql_error());
            $misc_hours = mysql_query("SELECT laborer_hours_worked FROM fmo_locations_events_laborers WHERE (laborer_timestamp>='".mysql_real_escape_string($start)."' AND laborer_timestamp<'".mysql_real_escape_string($end)."') AND laborer_user_token='".mysql_real_escape_string($token)."'");
            $pay = array();
            if(mysql_num_rows($hours) > 0 || mysql_num_rows($misc_hours) > 0){
                while($work = mysql_fetch_assoc($hours)){
                    $pay['hours']+=$work['timeclock_hours'];
                } while ($misc_work = mysql_fetch_assoc($misc_hours)){
                    $pay['hours']+=$misc_work['laborer_hours_worked'];
                }
                if($pay['hours'] > 0){
                    $pay['rate']      = $user_pay['user_employer_rate'];
                    $pay['earned']    = $pay['hours'] * $user_pay['user_employer_rate'];
                    if(mysql_num_rows($prev) > 0){
                        while($loans = mysql_fetch_assoc($prev)){
                            $pay['loans'] += $loans['advance_requested'];
                        }
                    } else {$pay['loans'] = 0;}
                    $pay['available'] = number_format(($pay['earned'] * .25) - $pay['loans'], 2);
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
        $pay['id'] = $id;
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
        timeline_log($token, $by, "Comment", "<strong>".name($by)."</strong> commented: <strong>".$comment."</strong>");
    }
    if($_GET['setting'] == 'usr_sendable'){
        $token      = $_SESSION['cuid'];
        $name       = $_POST['name'];
        $message    = $_POST['message'];

        $fileName  = struuid(true);
        $file_ext = strtolower(substr($_FILES['document']['name'], strripos($_FILES['document']['name'], '.')));
        $uploaddir = '../upload/sendables/';
        $uploadfile = $uploaddir . $fileName;

        if(move_uploaded_file($_FILES['document']['tmp_name'], $uploadfile . $file_ext)){
            $image = imagecreatefromjpeg($uploadfile . $file_ext);
            $exif = exif_read_data($uploadfile . $file_ext);
            $ort   = $exif['Orientation'];
            $width = $exif['COMPUTED']['Width'];
            resample($uploadfile . $file_ext, $uploadfile . $file_ext, $width, $ort);
            $link = "//www.formoversonly.com/dashboard/assets/upload/sendables/". $fileName . $file_ext;

            mysql_query("INSERT INTO fmo_sendables (sendable_company_token, sendable_path, sendable_name, sendable_message, sendable_token) VALUES (
            '".mysql_real_escape_string($token)."',
            '".mysql_real_escape_string($uploadfile.$file_ext)."',
            '".mysql_real_escape_string($name)."',
            '".mysql_real_escape_string($message)."',
            '".mysql_real_escape_string($fileName)."')");
        }

    }
    if($_GET['setting'] == 'ev_cmt'){
        $token      = $_GET['ev'];
        $comment    = $_POST['comment'];
        $by         = $_SESSION['uuid'];

        mysql_query("INSERT INTO fmo_locations_events_comments (comment_event_token, comment_comment, comment_by_user_token) VALUES (
        '".mysql_real_escape_string($token)."',
        '".mysql_real_escape_string($comment)."',
        '".mysql_real_escape_string($by)."')");
        timeline_event($token, $by, "Comment", "<strong>".name($by)."</strong> commented: <strong>".$comment."</strong>");
    }
    if($_GET['setting'] == 'service_rates'){
        $location_token = $_GET['luid'];
        $item           = sentence_case($_POST['item']);
        $desc           = sentence_case($_POST['desc']);
        $saleprice      = $_POST['saleprice'];
        $cost           = $_POST['cost'];
        $taxable        = $_POST['taxable'];
        $commissionable = $_POST['commissionable'];
        $redeemable     = $_POST['redeemable'];
        $type           = $_POST['type'];

        mysql_query("INSERT INTO fmo_services (services_location_token, services_item, services_item_desc, services_saleprice, services_cost, services_taxable, services_commissionable, services_redeemable, services_type) VALUES (
        '".mysql_real_escape_string($location_token)."',
        '".mysql_real_escape_string($item)."',
        '".mysql_real_escape_string($desc)."',
        '".mysql_real_escape_string($saleprice)."',
        '".mysql_real_escape_string($cost)."',
        '".mysql_real_escape_string($taxable)."',
        '".mysql_real_escape_string($commissionable)."',
        '".mysql_real_escape_string($redeemable)."',
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
    if($_GET['setting'] == 'service_storagetypes'){
        $location_token = $_GET['luid'];
        $floor          = $_POST['floor'];
        $desc           = $_POST['desc'];
        $lwh            = $_POST['l']."x".$_POST['w']."x".$_POST['h'];
        $rent           = $_POST['rent'];
        $climate        = $_POST['climate'];

        mysql_query("INSERT INTO fmo_locations_storages_types (type_location_token, type_floor, type_desc, type_lwh, type_rent, type_climate) VALUES (
        '".mysql_real_escape_string($location_token)."',
        '".mysql_real_escape_string($floor)."',
        '".mysql_real_escape_string($desc)."',
        '".mysql_real_escape_string($lwh)."',
        '".mysql_real_escape_string($rent)."',
        '".mysql_real_escape_string($climate)."')");
        echo $desc." (".$lwh.")";
    }
    if($_GET['setting'] == 'service_storage'){
        $location_token = $_GET['luid'];
        $type           = $_POST['type'];
        $qa             = $_POST['qa'];
        $status         = $_POST['status'];
        $desc           = $_POST['desc'];
        $findType = mysql_query("SELECT type_lwh, type_rent FROM fmo_locations_storages_types WHERE type_id='".mysql_real_escape_string($type)."'");
        if(mysql_num_rows($findType)){
            $types = mysql_fetch_array($findType);
            for($i = 0; $i < $qa; $i++){
                $token          = struuid(true);
                mysql_query("INSERT INTO fmo_locations_storages (storage_location_token, storage_token, storage_type_id, storage_available, storage_unit_name, storage_unit_lwh, storage_unit_desc, storage_price, storage_status, storage_period) VALUES (
                '".mysql_real_escape_string($location_token)."',
                '".mysql_real_escape_string($token)."',
                '".mysql_real_escape_string($type)."',
                '".mysql_real_escape_string(1)."',
                '".mysql_real_escape_string(0000)."',
                '".mysql_real_escape_string($types['type_lwh'])."',
                '".mysql_real_escape_string($desc)."',
                '".mysql_real_escape_string($types['type_rent'])."',
                '".mysql_real_escape_string($status)."',
                '".mysql_real_escape_string("Monthly")."')");
            }
        }
        echo $qa." unit(s)";
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