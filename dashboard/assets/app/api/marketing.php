<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/8/2017
 * Time: 11:21 PM
 */
session_start();
include '../init.php';

if(isset($_SESSION['uuid'])){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findMarketers = mysql_query("SELECT marketer_id, marketer_company, marketer_contact, marketer_phone, marketer_email, marketer_city, marketer_state, marketer_last_contacted, marketer_by_user_token, marketer_timestamp FROM fmo_locations_marketers WHERE marketer_location_token='".mysql_real_escape_string($_GET['luid'])."' ORDER BY marketer_last_contacted DESC");
    $iTotalRecords = mysql_num_rows($findMarketers);

    $records = array();
    $records["data"] = array();

    while($mk = mysql_fetch_assoc($findMarketers)) {
        $records["data"][] = array(
            '<span class="label label-warning">'.$mk['marketer_company'].'</span> &nbsp; '.$mk['marketer_contact'].'',
            ''.$mk['marketer_phone'].'',
            ''.$mk['marketer_email'].'',
            ''.$mk['marketer_city'].', '.$mk['marketer_state'].'',
            '',
            ''.name($mk['marketer_by_user_token']).''
        );
    }
    if (isset($_REQUEST["customActionType"]) && $_REQUEST["customActionType"] == "group_action") {
        $checkRequest = explode("|", $_REQUEST['customActionName']);
        if($checkRequest[0] == 'changeStatus'){
            foreach($_POST['id'] as $pk){
                mysql_query("UPDATE fmo_users SET user_status='".$checkRequest[1]."' WHERE user_token='".mysql_real_escape_string($pk)."'");
                $i++;
            }
            $records["customActionMessage"] = $i." user(s) statuses changed successfully. .";
            $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
        }
    }

    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;

    echo json_encode($records);
}
