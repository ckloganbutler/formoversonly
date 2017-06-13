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
    $findCustomers = mysql_query("SELECT user_id, user_fname, user_lname, user_token, user_group, user_phone, user_email, user_last_ext_location, user_employer_location, user_status FROM fmo_users WHERE user_group=3 AND user_last_ext_location='".$_GET['luid']."' ORDER BY user_lname ASC");
    $iTotalRecords = mysql_num_rows($findCustomers);

    $records = array();
    $records["data"] = array();

    while($cus = mysql_fetch_assoc($findCustomers)) {
        if($cus['user_fname'] == 'Logan'){
            $warning = '<img src="assets/admin/layout/img/warning.png" alt="TOO MANY HOURS" height="16px" width="16px"/>';
        } else {$warning = NULL;}
        if($cus['user_group'] == 1) {
            $status_tag = '<span class="label label-sm label-danger">ADMINISTRATOR</span>';
            $num        = '<span class="label label-sm label-danger"><strong>#'.$cus['user_id'].'</strong></span>';
        } elseif($cus['user_group'] == 2) {
            $status_tag = '<span class="label label-sm label-success"> MANAGER</span>';
            $num        = '<span class="label label-sm label-success"><strong>#'.$cus['user_id'].'</strong></span>';
        } elseif($cus['user_group'] == 3){

            $status_tag = '<span class="label label-sm label-info"> CUSTOMER</span>';
            $num        = '<span class="label label-sm label-info"><strong>#'.$cus['user_id'].'</strong></span>';
        } elseif($cus['user_group'] == 4) {
            $status_tag = '<span class="label label-sm label-info">CUSTOMER SERVICE</span>';
            $num        = '<span class="label label-sm label-info"><strong>#'.$cus['user_id'].'</strong></span>';
        } elseif($cus['user_group'] == 5.1) {
            $status_tag = '<span class="label label-sm label-warning">DRIVER</span>';
            $num        = '<span class="label label-sm label-warning"><strong>#'.$cus['user_id'].'</strong></span>';
        } elseif($cus['user_group'] == 5.2) {
            $status_tag = '<span class="label label-sm label-warning2">HELPER</span>';
            $num        = '<span class="label label-sm label-warning2"><strong>#'.$cus['user_id'].'</strong></span>';
        } elseif($cus['user_group'] == 5.3) {
            $status_tag = '<span class="label label-sm label-default">CREWMAN/OTHER</span>';
            $num        = '<span class="label label-sm label-default"><strong>#'.$cus['user_id'].'</strong></span>';
        }
        if($cus['user_status'] == 0){
            $status     = '<span class="label label-sm label-warning">INACTIVE</span>';
        } elseif($cus['user_status'] == 1){
            $status     = '<span class="label label-sm label-success">ACTIVE</span>';
        } elseif($cus['user_status'] == 2){
            $status     = '<span class="label label-sm label-danger">TERMINATED</span>';
        }
        $records["data"][] = array(
            '<input type="checkbox" name="pk" value="'.$cus['user_token'].'"> '.$num.'',
            ''.$cus['user_lname'].', '.$cus['user_fname'].'',
            ''.clean_phone($cus['user_phone']).'',
            ''.secret_mail($cus['user_email']).'',
            '<a class="btn default btn-xs red-stripe load_page" data-href="assets/pages/profile.php?uuid='.$cus['user_token'].'&luid='.$cus['user_last_ext_location'].'" data-page-title="'.$cus["user_fname"].' '.$cus["user_lname"].'"><i class="fa fa-edit"></i> View profile</a>'
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
