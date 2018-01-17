<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 5/10/2017
 * Time: 12:40 AM
 */
session_start();
include      '../init.php';

if(isset($_GET['ty']) && $_GET['ty'] == 'ai'){
    $id    = $_POST['srv_id'];
    $ev    = $_POST['srv_ev'];
    $echo  = array();
    $rate = mysql_fetch_array(mysql_query("SELECT services_item, services_item_desc, services_saleprice, services_taxable, services_commissionable, services_redeemable, services_redeemable_back, services_type, services_percentage FROM fmo_services WHERE services_id='".mysql_real_escape_string($id)."'"));

    $location = mysql_fetch_array(mysql_query("SELECT location_sales_tax FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'"));
    if(!empty($location['location_sales_tax'])){
        $tax = $location['location_sales_tax'];
    } else {$tax = 0;}

    if(!empty($rate)){

        $qty = $_POST['srv_qt'];
        if($qty > 0){
            $price = $qty * $rate['services_saleprice'];
        } else {
            $price = 0;
        }
        if($rate['services_type'] == 'Discount'){
            $discount = 1;
        } else { $discount = 0; }

        if($rate['services_percentage'] == 1){
            $percent = 1;
        } else { $percent = 0; }

        if(!isset($_GET['st_c'])){
            mysql_query("INSERT INTO fmo_locations_events_items (item_event_token, item_item, item_desc, item_qty, item_cost, item_total, item_taxable, item_commission, item_redeemable, item_discount, item_percent, item_prepay, item_adder) VALUES (
            '".mysql_real_escape_string($ev)."',
            '".mysql_real_escape_string($rate['services_item'])."',
            '".mysql_real_escape_string($rate['services_item_desc'])."',
            '".mysql_real_escape_string($qty)."',
            '".mysql_real_escape_string($rate['services_saleprice'])."',
            '".mysql_real_escape_string($price)."',
            '".mysql_real_escape_string($rate['services_taxable'])."',
            '".mysql_real_escape_string($rate['services_commissionable'])."',
            '".mysql_real_escape_string($rate['services_redeemable'])."',
            '".mysql_real_escape_string($discount)."',
            '".mysql_real_escape_string($percent)."',
            '".mysql_real_escape_string($rate['services_redeemable_back'])."',
            '".mysql_real_escape_string($_SESSION['uuid'])."')") or die(mysql_error());
        } elseif(isset($_GET['st_c'])){
            mysql_query("INSERT INTO fmo_locations_storages_contracts_items (item_contract_token, item_company_token, item_location_token, item_item, item_desc, item_qty, item_cost, item_total, item_taxable, item_taxable_amount, item_commission, item_redeemable, item_discount, item_percent, item_prepay, item_adder) VALUES (
            '".mysql_real_escape_string($_GET['st_c'])."',
            '".mysql_real_escape_string($_SESSION['cuid'])."',
            '".mysql_real_escape_string($_GET['luid'])."',
            '".mysql_real_escape_string($rate['services_item'])."',
            '".mysql_real_escape_string($rate['services_item_desc'])."',
            '".mysql_real_escape_string($qty)."',
            '".mysql_real_escape_string($rate['services_saleprice'])."',
            '".mysql_real_escape_string($price)."',
            '".mysql_real_escape_string($rate['services_taxable'])."',
            '".mysql_real_escape_string($tax)."',
            '".mysql_real_escape_string($rate['services_commissionable'])."',
            '".mysql_real_escape_string($rate['services_redeemable'])."',
            '".mysql_real_escape_string($discount)."',
            '".mysql_real_escape_string($percent)."',
            '".mysql_real_escape_string($rate['services_redeemable_back'])."',
            '".mysql_real_escape_string($_SESSION['uuid'])."')") or die(mysql_error());
        }

        $echo['item'] = $rate['services_item'];
        $echo['cost'] = $rate['services_saleprice'];
        echo json_encode($echo);
    }
}
if(isset($_GET['ty']) && $_GET['ty'] == 'ei'){
    $id    = $_POST['srv_id'];
    $est   = $_POST['srv_est'];
    $echo  = array();
    $rate = mysql_fetch_array(mysql_query("SELECT services_item, services_item_desc, services_saleprice, services_taxable, services_commissionable, services_redeemable, services_redeemable_back FROM fmo_services WHERE services_id='".mysql_real_escape_string($id)."'"));

    if(!empty($rate)){
        mysql_query("INSERT INTO fmo_locations_events_estimates_items (item_estimate_token, item_item, item_desc, item_cost, item_taxable, item_commission, item_redeemable, item_prepay, item_adder) VALUES (
        '".mysql_real_escape_string($est)."',
        '".mysql_real_escape_string($rate['services_item'])."',
        '".mysql_real_escape_string($rate['services_item_desc'])."',
        '".mysql_real_escape_string($rate['services_saleprice'])."',
        '".mysql_real_escape_string($rate['services_taxable'])."',
        '".mysql_real_escape_string($rate['services_commissionable'])."',
        '".mysql_real_escape_string($rate['services_redeemable'])."',
        '".mysql_real_escape_string($rate['services_redeemable_back'])."',
        '".mysql_real_escape_string($_SESSION['uuid'])."')");


        $echo['item'] = $rate['services_item'];
        $echo['cost'] = $rate['services_saleprice'];
        echo json_encode($echo);
    }
}

if(isset($_GET['yt']) && $_GET['yt'] == 'ck'){
    if(isset($_GET['type']) && isset($_GET['luid'])){
        $type  = $_GET['type'];
        $luid  = $_GET['luid'];
        $data  = date('Y-m-d', strtotime($_POST['date']));
        $state = $_POST['state'];
        if($state == 'true'){$state = 1;} elseif($state == 'false'){$state = 0;}
        if($type == 'morning'){$type = 1;} elseif($type == 'afternoon'){$type = 2;}

        $date = mysql_query("SELECT activity_date FROM fmo_locations_activites WHERE (activity_type='".mysql_real_escape_string($type)."' AND activity_date='".mysql_real_escape_string($data)."') AND activity_location_token='".mysql_real_escape_string($luid)."'");
        if(mysql_num_rows($date) > 0){
            // Update
            mysql_query("UPDATE fmo_locations_activites SET activity_notes='".mysql_real_escape_string($state)."' WHERE (activity_type='".mysql_real_escape_string($type)."' AND activity_date='".mysql_real_escape_string($data)."') AND activity_location_token='".mysql_real_escape_string($luid)."'") or die(mysql_error());
            echo "success21";
        } else {
            // Create
            mysql_query("INSERT INTO fmo_locations_activites (activity_location_token, activity_type, activity_date, activity_notes, activity_by_user_token) VALUES (
            '".mysql_real_escape_string($luid)."',
            '".mysql_real_escape_string($type)."',
            '".mysql_real_escape_string($data)."',
            '".mysql_real_escape_string($state)."',
            '".mysql_real_escape_string($_SESSION['uuid'])."')") or die(mysql_error());
            echo "success";
        }
    }
}

if(isset($_GET['send_doc']) && $_GET['send_doc'] == 'from'){
    $document = mysql_fetch_array(mysql_query("SELECT sendable_token FROM fmo_sendables WHERE sendable_token='".$_POST['doc']."'"));
    try {

        $fname = explode(" ", name($_SESSION['uuid']));

        $email = new PHPMailer(true);
        $email->From      = "".strtolower($fname[0])."@".str_replace(' ', '', strtolower(companyName($_SESSION['cuid']))).".com";
        $email->FromName  = "".name($_SESSION['uuid'])."";
        $email->Subject   = "".name($_SESSION['uuid'])." from ".companyName($_SESSION['cuid'])." sent you a document.";
        $email->Body      = "".$_POST['message']."";
        $email->AddAddress( $_POST['email'] );

        $email->AddAttachment( "/var/www/dashboard/assets/upload/sendables/".$document['sendable_token'].".pdf" , struuid(true).'.pdf' );

        $email->Send();

    } catch (phpmailerException $e) {
        echo $e->errorMessage(); //error messages from PHPMailer
    } catch (Exception $e) {
        echo $e->getMessage();
    }

}

if(isset($_GET['ty']) && $_GET['ty'] == 'aco'){
    $out = mysql_query("SELECT timeclock_id, timeclock_clockin FROM fmo_users_employee_timeclock WHERE timeclock_user='".mysql_real_escape_string($_GET['uuid'])."' AND timeclock_complete=0") or die(mysql_error());
    if(mysql_num_rows($out) > 0){
        $clock    = mysql_fetch_array($out);
        $date_in  = $clock['timeclock_clockin'];
        $date_out = date('Y-m-d H:i:s');
        $date1 = new DateTime($date_in);
        $date2 = new DateTime($date_out);
        $diff = $date1->diff($date2);
        $hours   = $diff->h;
        $minutes = $diff->i;
        $seconds = $diff->s;
        $worked = decimalHours($hours.":".$minutes.":".$seconds);
        mysql_query("UPDATE fmo_users_employee_timeclock SET timeclock_clockout='".mysql_real_escape_string($date_out)."', timeclock_hours='".mysql_real_escape_string($worked)."', timeclock_ip_out='Admin Clocked Out', timeclock_complete=1 WHERE timeclock_id='".mysql_real_escape_string($clock['timeclock_id'])."'") or die(mysql_error());
        echo $worked;
    }
}
