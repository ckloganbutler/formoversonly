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
    $findRates = mysql_query("SELECT subtype_id, subtype_name FROM fmo_locations_subtypes WHERE subtype_location_token='".mysql_real_escape_string($_GET['luid'])."'");
    $iTotalRecords = mysql_num_rows($findRates);

    $records = array();
    $records["data"] = array();

    while($subtype = mysql_fetch_assoc($findRates)) {
        $status_tag = '<span class="label label-sm label-success">ACTIVE</span>';
        $records["data"][] = array(
            '<input type="checkbox" name="pk" value="'.$subtype['subtype_id'].'"> '.$status_tag.'',
            '<a class="editable_item_'.$subtype['subtype_id'].'" style="color:#333333" data-name="subtype_name" data-pk="'.$subtype['subtype_id'].'" data-type="text" data-placement="right" data-title="Enter new subtype name.." data-url="assets/app/update_settings.php?update=location_subtype">'.$subtype['subtype_name'].'</a>',
            '<button type="button" value="editable_item_'.$subtype['subtype_id'].'" class="btn default btn-xs red-stripe edit_line"><i class="fa fa-edit"></i> Edit</a>',
        );
    }
    if (isset($_REQUEST["customActionType"]) && $_REQUEST["customActionType"] == "group_action") {
        $checkRequest = explode("|", $_REQUEST['customActionName']);
        if($checkRequest[0] == 'copyTo'){
            foreach($_POST['id'] as $pk){
                mysql_query("INSERT INTO fmo_locations_subtypes (subtype_location_token, subtype_name) SELECT subtype_location_token, subtype_name FROM fmo_locations_subtypes WHERE subtype_id='".mysql_real_escape_string($pk)."'");
                mysql_query("UPDATE fmo_locations_subtypes SET subtype_location_token='".mysql_real_escape_string($checkRequest[1])."' WHERE subtype_id='".mysql_insert_id()."'");
                $i++;
            }
            $records["customActionMessage"] = $i." subtype(s) were copied to the their new location successfully.";
            $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
        } else {
            if ($_REQUEST['customActionName'] == 'Delete') {
                foreach ($_POST['id'] as $pk) {
                    mysql_query("DELETE FROM fmo_locations_subtypes WHERE subtype_id='" . mysql_real_escape_string($pk) . "'");
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