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
    $findStorage = mysql_query("SELECT storage_id, storage_available, storage_unit, storage_priceperiod FROM fmo_locations_storages WHERE storage_location_token='".mysql_real_escape_string($_GET['luid'])."'");
    $iTotalRecords = mysql_num_rows($findStorage);

    $records = array();
    $records["data"] = array();

    while($storage = mysql_fetch_assoc($findStorage)) {
        if($storage['storage_available'] == 0) {
            $available = '<span class="label label-sm label-danger">NO</span>';
        } else {
            $available = '<span class="label label-sm label-success">YES</span>';
        }
        $records["data"][] = array(
            '<input type="checkbox" name="pk" value="'.$storage['storage_id'].'"> '.$available.'',
            '<a class="editable_item_'.$storage['storage_id'].'" style="color:#333333" data-name="storage_unit" data-pk="'.$storage['storage_id'].'" data-type="text" data-placement="right" data-title="Enter new unit specifications.." data-url="assets/app/update_settings.php?update=location_storage">'.$storage['storage_unit'].'</a>',
            '<a class="editable_item_'.$storage['storage_id'].'" style="color:#333333" data-name="storage_priceperiod" data-pk="'.$storage['storage_id'].'" data-type="text" data-placement="right" data-title="Enter new unit priceperiod.." data-url="assets/app/update_settings.php?update=location_storage">'.$storage['storage_priceperiod'].'</a>',
            '<button type="button" value="editable_item_'.$storage['storage_id'].'" class="btn default btn-xs red-stripe edit_line"><i class="fa fa-edit"></i> Edit</a>'
        );
    }
    if (isset($_REQUEST["customActionType"]) && $_REQUEST["customActionType"] == "group_action") {
        $checkRequest = explode("|", $_REQUEST['customActionName']);
        if($checkRequest[0] == 'copyTo'){
            foreach($_POST['id'] as $pk){
                mysql_query("INSERT INTO fmo_locations_storages (storage_location_token, storage_available, storage_unit, storage_priceperiod) SELECT storage_location_token, storage_available, storage_unit, storage_priceperiod FROM fmo_locations_storages WHERE storage_id='".mysql_real_escape_string($pk)."'");
                mysql_query("UPDATE fmo_locations_storages SET storage_location_token='".mysql_real_escape_string($checkRequest[1])."' WHERE storage_id='".mysql_insert_id()."'");
                $i++;
            }
            $records["customActionMessage"] = $i." storage unit(s) were copied to the their new locations successfully.";
            $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
        } else {
            if ($_REQUEST['customActionName'] == 'changeAvailabilityAvailable') {
                foreach ($_POST['id'] as $pk) {
                    mysql_query("UPDATE fmo_locations_storages SET storage_available='1' WHERE storage_id='" . mysql_real_escape_string($pk) . "'");
                    $i++;
                }
                $records["customActionMessage"] = $i . " units were made available.";
            }
            if ($_REQUEST['customActionName'] == 'changeAvailabilityUnAvailable') {
                foreach ($_POST['id'] as $pk) {
                    mysql_query("UPDATE fmo_locations_storages SET storage_available='0' WHERE storage_id='" . mysql_real_escape_string($pk) . "'");
                    $i++;
                }
                $records["customActionMessage"] = $i . " units were made un-available.";
            }
            if ($_REQUEST['customActionName'] == 'delete') {
                foreach ($_POST['id'] as $pk) {
                    mysql_query("DELETE FROM fmo_locations_storages WHERE storage_id='" . mysql_real_escape_string($pk) . "'");
                    $i++;
                }
                $records["customActionMessage"] = $i . " units were deleted successfully.";
            }
            $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
        }
    }

    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;

    echo json_encode($records);
}
