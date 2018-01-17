<?php
/**
 * Created by PhpStorm.
 * User: gameroom
 * Date: 6/28/2017
 * Time: 8:09 PM
 */
session_start();
include '../init.php';

if(isset($_SESSION['uuid'])){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findCustomers = mysql_query("SELECT text_text, text_timestamp, text_by_user_token FROM fmo_locations_text_records WHERE text_location_token='".$_GET['luid']."' ORDER BY text_timestamp DESC LIMIT 30");
    $iTotalRecords = mysql_num_rows($findCustomers);

    $records = array();
    $records["data"] = array();

    while($cus = mysql_fetch_assoc($findCustomers)) {
        $records["data"][] = array(
            ''.$cus['text_timestamp'].'',
            ''.$cus['text_text'].'',
            ''.name($cus['text_by_user_token']).'',
        );
    }


    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;

    echo json_encode($records);
}
