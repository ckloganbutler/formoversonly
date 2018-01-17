<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/8/2017
 * Time: 11:21 PM
 */
session_start();
include '../init.php';

if(isset($_GET['p'])){
    if($_GET['p'] == 'xEx'){
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);
        $findStorage = mysql_query("SELECT storage_id, storage_available, storage_unit_name, storage_unit_lwh, storage_unit_desc, storage_price, storage_period, storage_occupant, storage_contract_token, storage_last_occupied, storage_status, storage_type_id FROM fmo_locations_storages WHERE storage_location_token='".mysql_real_escape_string($_GET['luid'])."' ORDER BY storage_unit_name ASC") or die(mysql_error());
        $iTotalRecords = mysql_num_rows($findStorage);

        $records = array();
        $records["data"] = array();

        while($storage = mysql_fetch_assoc($findStorage)) {
            switch($storage['storage_status']){
                case "Reserved": $badge = 'badge badge-yellow badge-roundless'; $msg = $storage['storage_status']; break;
                case "Vacant": $badge = 'badge badge-default badge-roundless'; $msg = $storage['storage_status']; break;
                case "Occupied": $badge = 'badge badge-success badge-roundless'; $msg = $storage['storage_status']; break;
                case "Damaged": $badge = 'badge badge-danger badge-roundless'; $msg = $storage['storage_status']; break;
                case "Auction": $badge = 'badge badge-danger badge-roundless'; $msg = $storage['storage_status']; break;
                case "Delinquent": $badge = 'badge badge-danger badge-roundless'; $msg = $storage['storage_status']; break;
            }
            $type = mysql_fetch_array(mysql_query("SELECT type_floor, type_desc, type_climate FROM fmo_locations_storages_types WHERE type_id='".mysql_real_escape_string($storage['storage_type_id'])."'"));
            $records["data"][] = array(
                '<input type="checkbox" name="pk" value="'.$storage['storage_id'].'"> <strong><a class="editable_item_'.$storage['storage_id'].'" style="color:#333333" data-name="storage_unit_name" data-pk="'.$storage['storage_id'].'" data-type="text" data-placement="right" data-title="Enter new unit name.." data-url="assets/app/update_settings.php?update=location_storage">'.$storage['storage_unit_name'].'</a></strong> &nbsp; <span class="'.$badge.'"><a class="editable_item_'.$storage['storage_id'].'" style="color:white" data-name="storage_status" data-pk="'.$storage['storage_id'].'" data-type="select" data-source="[{value: \'Damaged\', text: \'Damaged\'}, {value: \'Vacant\', text: \'Vacant\'},  {value: \'Occupied\', text: \'Occupied\'},  {value: \'Delinquent\', text: \'Delinquent\'},  {value: \'Auction\', text: \'Auction\'},  {value: \'Reserved\', text: \'Reserved\'}]" data-placement="right" data-title="Select new status.." data-url="assets/app/update_settings.php?update=location_storage">'.$msg.'</a></span> &nbsp; Floor '.$type['type_floor'].", ".$type['type_desc'].' [Climate: '.$type['type_climate'].']',
                '<a class="editable_item_'.$storage['storage_id'].'" style="color:#333333" data-name="storage_unit_lwh" data-pk="'.$storage['storage_id'].'" data-type="text" data-placement="right" data-title="Enter new unit LxWxH.." data-url="assets/app/update_settings.php?update=location_storage">'.$storage['storage_unit_lwh'].'</a>',
                '<a class="editable_item_'.$storage['storage_id'].'" style="color:#333333" data-name="storage_price" data-pk="'.$storage['storage_id'].'" data-type="text" data-placement="right" data-title="Enter new unit price.." data-url="assets/app/update_settings.php?update=location_storage">'.$storage['storage_price'].'</a>/<a class="editable_item_'.$storage['storage_id'].'" data-name="storage_period" data-pk="'.$storage['storage_id'].'" data-type="select" data-source="[{value: \'Monthly\', text: \'Monthly\'}, {value: \'Weekly\', text: \'Weekly\'}]" data-placement="right" data-title="Select new period.." data-url="assets/app/update_settings.php?update=location_storage">'.$storage['storage_period'].'</a>',
                '<a class="editable_item_'.$storage['storage_id'].'" style="color:#333333" data-name="storage_unit_desc" data-pk="'.$storage['storage_id'].'" data-type="text" data-placement="right" data-title="Enter new unit description.." data-url="assets/app/update_settings.php?update=location_storage">'.$storage['storage_unit_desc'].'</a>',
                '<button type="button" value="editable_item_'.$storage['storage_id'].'" class="btn default btn-xs red-stripe edit_line btn-block"><i class="fa fa-edit"></i> Edit</a>'
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
    } elseif($_GET['p'] == 'ExE'){
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);
        $findStorage = mysql_query("SELECT type_id, type_floor, type_desc, type_lwh, type_rent, type_climate FROM fmo_locations_storages_types WHERE type_location_token='".mysql_real_escape_string($_GET['luid'])."' ORDER BY type_id DESC") or die(mysql_error());
        $iTotalRecords = mysql_num_rows($findStorage);

        $records = array();
        $records["data"] = array();

        while($storage = mysql_fetch_assoc($findStorage)) {
            $sqft = explode("x", $storage['type_lwh']);
            $rsqf = $sqft[0] * $sqft[1];
            if($storage['storage_available'] == 0) {
                $available = '<span class="label label-sm label-danger">NO</span>';
            } else {
                $available = '<span class="label label-sm label-success">YES</span>';
            }
            $records["data"][] = array(
                '<input type="checkbox" name="pk" value="'.$storage['type_id'].'"> <strong><a class="editable1_item_'.$storage['type_id'].'" style="color:#333333" data-name="type_lwh" data-pk="'.$storage['type_id'].'" data-type="text" data-placement="right" data-title="Enter new unit LxWxH.." data-url="assets/app/update_settings.php?update=location_storagetypes">'.$storage['type_lwh'].'</a></strong> ('.mysql_real_escape_string($rsqf).' square feet)',
                'Floor <a class="editable1_item_'.$storage['type_id'].'" style="color:#333333" data-name="type_floor" data-pk="'.$storage['type_id'].'" data-type="text" data-placement="right" data-title="Enter new unit name.." data-url="assets/app/update_settings.php?update=location_storagetypes">'.$storage['type_floor'].'</a> - <a class="editable1_item_'.$storage['type_id'].'" style="color:#333333" data-name="type_desc" data-pk="'.$storage['type_id'].'" data-type="text" data-placement="right" data-title="Enter new unit type description.." data-url="assets/app/update_settings.php?update=location_storagetypes">'.$storage['type_desc'].'</a>',
                '<a class="editable1_item_'.$storage['type_id'].'" style="color:#333333" data-name="type_rent" data-pk="'.$storage['type_id'].'" data-type="text" data-placement="right" data-title="Enter new unit price.." data-url="assets/app/update_settings.php?update=location_storagetypes">'.$storage['type_rent'].'</a>/Monthly',
                '<a class="editable1_item_'.$storage['type_id'].'" style="color:#333333" data-name="type_climate" data-pk="'.$storage['type_id'].'" data-type="text" data-placement="right" data-title="Enter new unit period.." data-url="assets/app/update_settings.php?update=location_storagetypes">'.$storage['type_climate'].'</a>',
                '<button type="button" value="editable1_item_'.$storage['type_id'].'" class="btn default btn-xs pull-right red-stripe edit_line btn-block"><i class="fa fa-edit"></i> Edit</button>'
            );
        }
        if (isset($_REQUEST["customActionType"]) && $_REQUEST["customActionType"] == "group_action") {
            $checkRequest = explode("|", $_REQUEST['customActionName']);
            if($checkRequest[0] == 'copyTo'){
                foreach($_POST['id'] as $pk){
                    mysql_query("INSERT INTO fmo_locations_storages_types (type_location_token, type_floor, type_desc, type_lwh, type_rent, type_climate) SELECT type_location_token, type_floor, type_desc, type_lwh, type_rent, type_climate FROM fmo_locations_storages_types WHERE type_id='".mysql_real_escape_string($pk)."'");
                    mysql_query("UPDATE fmo_locations_storages SET type_location_token='".mysql_real_escape_string($checkRequest[1])."' WHERE storage_id='".mysql_insert_id()."'");
                    $i++;
                }
                $records["customActionMessage"] = $i." storage unit types(s) were copied to the their new locations successfully.";
                $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
            } else {
                if ($_REQUEST['customActionName'] == 'delete') {
                    foreach ($_POST['id'] as $pk) {
                        mysql_query("DELETE FROM fmo_locations_storages_types WHERE type_id='" . mysql_real_escape_string($pk) . "'");
                        $i++;
                    }
                    $records["customActionMessage"] = $i . " unit type(s) were deleted successfully.";
                }
                $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
            }
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }
}
