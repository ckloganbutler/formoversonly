<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/19/2017
 * Time: 7:58 PM
 */
session_start();
include '../init.php';

if(isset($_SESSION['uuid'])){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findRates = mysql_query("SELECT time_id, time_start, time_end FROM fmo_locations_times WHERE time_location_token='".mysql_real_escape_string($_GET['luid'])."'");
    $iTotalRecords = mysql_num_rows($findRates);

    $records = array();
    $records["data"] = array();

    while($time = mysql_fetch_assoc($findRates)) {
        $status_tag = '<span class="label label-sm label-success">ACTIVE</span>';
        $records["data"][] = array(
            '<input type="checkbox" name="pk" value="'.$time['time_id'].'"> '.$status_tag.'',
            '<a class="editable_item_'.$time['time_id'].'" style="color:#333333" data-name="time_start" data-pk="'.$time['time_id'].'" data-type="text" data-placement="right" data-title="Enter new start time.." data-url="assets/app/update_settings.php?update=location_time">'.$time['time_start'].'</a> to <a class="editable_item_'.$time['time_id'].'" style="color:#333333" data-name="time_end" data-pk="'.$time['time_id'].'" data-type="text" data-placement="right" data-title="Enter new end time.." data-url="assets/app/update_settings.php?update=location_time">'.$time['time_end'].'</a> ',
            '<button type="button" value="editable_item_'.$time['time_id'].'" class="btn default btn-xs red-stripe edit_line"><i class="fa fa-edit"></i> Edit</a>',
        );
    }
    if (isset($_REQUEST["customActionType"]) && $_REQUEST["customActionType"] == "group_action") {
        $checkRequest = explode("|", $_REQUEST['customActionName']);
        if($checkRequest[0] == 'copyTo'){
            foreach($_POST['id'] as $pk){
                mysql_query("INSERT INTO fmo_locations_times (time_location_token, time_start, time_end) SELECT time_location_token, time_start, time_end FROM fmo_locations_times WHERE time_id='".mysql_real_escape_string($pk)."'");
                mysql_query("UPDATE fmo_locations_times SET time_location_token='".mysql_real_escape_string($checkRequest[1])."' WHERE time_id='".mysql_insert_id()."'");
                $i++;
            }
            $records["customActionMessage"] = $i." zipcode(s) were copied to the their new locations successfully.";
            $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
        } else {
            if ($_REQUEST['customActionName'] == 'Delete') {
                foreach ($_POST['id'] as $pk) {
                    mysql_query("DELETE FROM fmo_locations_times WHERE time_id='" . mysql_real_escape_string($pk) . "'");
                    $i++;
                }
                $records["customActionMessage"] = $i . " record(s) were deleted successfully.";
            }
            $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
        }
    }

    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;

    echo json_encode($records);
}