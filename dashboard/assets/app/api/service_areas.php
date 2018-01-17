<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/8/2017
 * Time: 11:21 PM
 */
session_start();
include '../init.php';

if($_GET['type'] == 'county'){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findCounties = mysql_query("SELECT county_id, county_name, county_status FROM fmo_locations_counties WHERE county_location_token='".mysql_real_escape_string($_GET['luid'])."'");
    $iTotalRecords = mysql_num_rows($findCounties);

    $records = array();
    $records["data"] = array();

    while($counties = mysql_fetch_assoc($findCounties)) {
        if($counties['county_status'] == 0) {
            $status_tag = '<span class="label label-sm label-danger">DISABLED</span>';
        } else {
            $status_tag = '<span class="label label-sm label-success">ACTIVE</span>';
        }
        $records["data"][] = array(
            '<input type="checkbox" name="pk" value="'.$counties['county_id'].'"> '.$status_tag.'',
            '<a class="editable_county_item_'.$counties['county_id'].'" style="color:#333333" data-name="county_name" data-pk="'.$counties['county_id'].'" data-type="text" data-placement="right" data-title="Enter new county name.." data-url="assets/app/update_settings.php?setting=location_services_county">'.$counties['county_name'].'</a>',
            '<button type="button" value="editable_county_item_'.$counties['county_id'].'" class="btn default btn-xs red-stripe edit_line"><i class="fa fa-edit"></i> Edit</a>',
        );
    }
    if (isset($_REQUEST["customActionType"]) && $_REQUEST["customActionType"] == "group_action") {
        $checkRequest = explode("|", $_REQUEST['customActionName']);
        if($checkRequest[0] == 'copyTo'){
            foreach($_POST['id'] as $pk){
                mysql_query("INSERT INTO fmo_locations_counties (county_location_token, county_name, county_status) SELECT county_location_token, county_name, county_status FROM fmo_locations_counties WHERE county_id='".mysql_real_escape_string($pk)."'");
                mysql_query("UPDATE fmo_locations_counties SET county_location_token='".mysql_real_escape_string($checkRequest[1])."' WHERE county_id='".mysql_insert_id()."'");
                $i++;
            }
            $records["customActionMessage"] = $i." county(ies) were copied to the their new locations successfully.";
            $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
        } else {
            if ($_REQUEST['customActionName'] == 'Delete') {
                foreach ($_POST['id'] as $pk) {
                    mysql_query("DELETE FROM fmo_locations_counties WHERE county_id='" . mysql_real_escape_string($pk) . "'");
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

if($_GET['type'] == 'zipcodes'){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findZipcodes = mysql_query("SELECT zipcode_id, zipcode_code, zipcode_status FROM fmo_locations_zipcodes WHERE zipcode_location_token='".mysql_real_escape_string($_GET['luid'])."'");
    $iTotalRecords = mysql_num_rows($findZipcodes);

    $records = array();
    $records["data"] = array();

    while($zipcodes = mysql_fetch_assoc($findZipcodes)) {
        if($zipcodes['zipcode_status'] == 0) {
            $status_tag = '<span class="label label-sm label-danger">DISABLED</span>';
        } else {
            $status_tag = '<span class="label label-sm label-success">ACTIVE</span>';
        }
        $records["data"][] = array(
            '<input type="checkbox" name="pk" value="'.$zipcodes['zipcode_id'].'"> '.$status_tag.'',
            '<a class="editable_zipcode_item_'.$zipcodes['zipcode_id'].'" style="color:#333333" data-name="zipcode_code" data-pk="'.$zipcodes['zipcode_id'].'" data-type="number" data-placement="right" data-title="Enter new zip code.." data-url="assets/app/update_settings.php?setting=location_services_zipcode">'.$zipcodes['zipcode_code'].'</a>',
            '<button type="button" value="editable_zipcode_item_'.$zipcodes['zipcode_id'].'" class="btn default btn-xs red-stripe edit_line"><i class="fa fa-edit"></i> Edit</a>',
        );
    }
    if (isset($_REQUEST["customActionType"]) && $_REQUEST["customActionType"] == "group_action") {
        $checkRequest = explode("|", $_REQUEST['customActionName']);
        if($checkRequest[0] == 'copyTo'){
            foreach($_POST['id'] as $pk){
                mysql_query("INSERT INTO fmo_locations_zipcodes (zipcode_location_token, zipcode_code, zipcode_status) SELECT zipcode_location_token, zipcode_code, zipcode_status FROM fmo_locations_zipcodes WHERE zipcode_id='".mysql_real_escape_string($pk)."'");
                mysql_query("UPDATE fmo_locations_zipcodes SET zipcode_location_token='".mysql_real_escape_string($checkRequest[1])."' WHERE zipcode_id='".mysql_insert_id()."'");
                $i++;
            }
            $records["customActionMessage"] = $i." zipcode(s) were copied to the their new locations successfully.";
            $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
        } else {
            if ($_REQUEST['customActionName'] == 'Delete') {
                foreach ($_POST['id'] as $pk) {
                    mysql_query("DELETE FROM fmo_locations_zipcodes WHERE zipcode_id='" . mysql_real_escape_string($pk) . "'");
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
