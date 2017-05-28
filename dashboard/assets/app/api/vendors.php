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
    $findVendors = mysql_query("SELECT vendor_id, vendor_active, vendor_name, vendor_type, vendor_phone, vendor_contact, vendor_account_ref, vendor_extra_ref FROM fmo_locations_vendors WHERE vendor_location_token='".mysql_real_escape_string($_GET['luid'])."'");
    $iTotalRecords = mysql_num_rows($findVendors);

    $records = array();
    $records["data"] = array();

    while($vendor = mysql_fetch_assoc($findVendors)) {
        if($vendor['vendor_active'] == 0) {
            $active = '<span class="label label-sm label-danger">NO</span>';
        } else {
            $active = '<span class="label label-sm label-success">YES</span>';
        }
        $types = "{value: 'Towing', text: 'Towing'},{value: 'Mechanic', text: 'Mechanic'},{value: 'Roadside', text: 'Roadside'},{value: 'Trucks', text: 'Trucks'},{value: 'Fuel', text: 'Fuel'},{value: 'Rentals', text: 'Rentals'},{value: 'Day', text: 'labor'},{value: 'Furniture Repair', text: 'Furniture Repair'},{value: 'Other', text: 'Other'}";
        $records["data"][] = array(
            '<input type="checkbox" name="pk" value="'.$vendor['vendor_id'].'"> '.$active.'',
            '<a class="editable_item_'.$vendor['vendor_id'].'" style="color:#333333" data-name="vendor_name" data-pk="'.$vendor['vendor_id'].'" data-type="text" data-placement="right" data-title="Enter new vendor name.." data-url="assets/app/update_settings.php?update=location_vendor">'.$vendor['vendor_name'].'</a>',
            '<a class="editable_item_'.$vendor['vendor_id'].'" style="color:#333333" data-name="vendor_type" data-pk="'.$vendor['vendor_id'].'" data-type="select" data-source="['.$types.']" data-placement="right" data-title="Select new vendor type.." data-url="assets/app/update_settings.php?update=location_vendor">'.$vendor['vendor_type'].'</a>',
            '<a class="editable_item_'.$vendor['vendor_id'].'" style="color:#333333" data-name="vendor_phone" data-pk="'.$vendor['vendor_id'].'" data-type="text" data-placement="right" data-title="Enter new vendor phone.." data-url="assets/app/update_settings.php?update=location_vendor">'.clean_phone($vendor['vendor_phone']).'</a>',
            '<a class="editable_item_'.$vendor['vendor_id'].'" style="color:#333333" data-name="vendor_contact" data-pk="'.$vendor['vendor_id'].'" data-type="text" data-placement="right" data-title="Enter new vendor contact.." data-url="assets/app/update_settings.php?update=location_vendor">'.$vendor['vendor_contact'].'</a>',
            '<a class="editable_item_'.$vendor['vendor_id'].'" style="color:#333333" data-name="vendor_account_ref" data-pk="'.$vendor['vendor_id'].'" data-type="text" data-placement="right" data-title="Enter new account #.." data-url="assets/app/update_settings.php?update=location_vendor">'.$vendor['vendor_account_ref'].'</a>',
            '<a class="editable_item_'.$vendor['vendor_id'].'" style="color:#333333" data-name="vendor_extra_ref" data-pk="'.$vendor['vendor_id'].'" data-type="text" data-placement="right" data-title="Enter new extra reference.." data-url="assets/app/update_settings.php?update=location_vendor">'.$vendor['vendor_extra_ref'].'</a>',
            '<button type="button" value="editable_item_'.$vendor['vendor_id'].'" class="btn default btn-xs red-stripe edit_line"><i class="fa fa-edit"></i> Edit</a>',
        );
    }
    if (isset($_REQUEST["customActionType"]) && $_REQUEST["customActionType"] == "group_action") {
        $checkRequest = explode("|", $_REQUEST['customActionName']);
        if($checkRequest[0] == 'copyTo'){
            foreach($_POST['id'] as $pk){
                mysql_query("INSERT INTO fmo_locations_vendors (vendor_location_token, vendor_active, vendor_name, vendor_type, vendor_phone, vendor_contact, vendor_account_ref, vendor_extra_ref) SELECT vendor_location_token, vendor_active, vendor_name, vendor_type, vendor_phone, vendor_contact, vendor_account_ref, vendor_extra_ref FROM fmo_locations_vendors WHERE vendor_id='".mysql_real_escape_string($pk)."'");
                mysql_query("UPDATE fmo_locations_vendors SET vendor_location_token='".mysql_real_escape_string($checkRequest[1])."' WHERE vendor_id='".mysql_insert_id()."'");
                $i++;
            }
            $records["customActionMessage"] = $i." vendor(s) were copied to the their locations successfully.";
            $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
        } else {
            if($_REQUEST['customActionName'] == 'Delete'){
                foreach($_POST['id'] as $pk){
                    mysql_query("DELETE FROM fmo_locations_vendors WHERE vendor_id='".mysql_real_escape_string($pk)."'");
                    $i++;
                }
                $records["customActionMessage"] = $i." record(s) were deleted successfully.";
            }$records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
        }

    }

    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;

    echo json_encode($records);
}
