<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 5/10/2017
 * Time: 12:40 AM
 */
session_start();
include '../init.php';

if(isset($_GET['ty']) && $_GET['ty'] == 'ai'){
    $id    = $_POST['srv_id'];
    $ev    = $_POST['srv_ev'];
    $echo  = array();
    $rate = mysql_fetch_array(mysql_query("SELECT services_item, services_item_desc, services_saleprice, services_taxable, services_commissionable FROM fmo_services WHERE services_id='".mysql_real_escape_string($id)."'"));

    if(!empty($rate)){
        mysql_query("INSERT INTO fmo_locations_events_items (item_event_token, item_item, item_desc, item_cost, item_taxable, item_commission, item_adder) VALUES (
        '".mysql_real_escape_string($ev)."',
        '".mysql_real_escape_string($rate['services_item'])."',
        '".mysql_real_escape_string($rate['services_item_desc'])."',
        '".mysql_real_escape_string($rate['services_saleprice'])."',
        '".mysql_real_escape_string($rate['services_taxable'])."',
        '".mysql_real_escape_string($rate['services_commissionable'])."',
        '".mysql_real_escape_string($_SESSION['uuid'])."')");


        $echo['item'] = $rate['services_item'];
        $echo['cost'] = $rate['services_saleprice'];
        echo json_encode($echo);
    }
}