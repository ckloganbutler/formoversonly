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
    $findEmployees = mysql_query("SELECT marketer_id, marketer_contact, marketer_phone, marketer_email, marketer_city, marketer_state, marketer_last_contacted, marketer_by_user_token, marketer_timestamp FROM fmo_locations_marketers WHERE marketer_location_token='".mysql_real_escape_string($_GET['luid'])."' ORDER BY marketer_last_contacted DESC");
    $iTotalRecords = mysql_num_rows($findEmployees);

    $records = array();
    $records["data"] = array();

    while($emp = mysql_fetch_assoc($findEmployees)) {
        $records["data"][] = array(
            '<input type="checkbox" name="pk" value="'.$emp['user_token'].'"> '.$status_tag.'',
            ''.$emp['user_lname'].', '.$emp['user_fname'].' '.$num.' '.$warning.'',
            ''.clean_phone($emp['user_phone']).'',
            ''.secret_mail($emp['user_email']).'',
            '<a class="btn default btn-xs red-stripe load_page" data-href="assets/pages/profile.php?uuid='.$emp['user_token'].'&luid='.$emp['user_employer_location'].'" data-page-title="'.$emp["user_fname"].' '.$profile["user_lname"].'"><i class="fa fa-edit"></i> View profile</a>'
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
