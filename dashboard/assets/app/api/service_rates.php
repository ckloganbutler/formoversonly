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
    $findRates = mysql_query("SELECT services_id, services_item, services_item_desc, services_saleprice, services_cost, services_taxable, services_commissionable, services_redeemable, services_status, services_type FROM fmo_services WHERE services_location_token='".mysql_real_escape_string($_GET['luid'])."'");
    $iTotalRecords = mysql_num_rows($findRates);

    $records = array();
    $records["data"] = array();

    while($services = mysql_fetch_assoc($findRates)) {
        if($services['services_taxable'] == 0) {
            $taxable_tag = '<span class="label label-sm label-danger">NO</span>';
        } else {
            $taxable_tag = '<span class="label label-sm label-success">YES</span>';
        }
        if($services['services_commissionable'] == 0) {
            $commissionable_tag = '<span class="label label-sm label-danger">NO</span>';
        }  else  {
            $commissionable_tag = '<span class="label label-sm label-success">YES</span>';
        }
        if($services['services_redeemable'] == 0){
            $redeemable_tag = '<span class="label label-sm label-danger">NO</span>';
        }  else  {
            $redeemable_tag = '<span class="label label-sm label-success">YES</span>';
        }
        if($services['services_status'] == 0) {
            $status_tag = '<span class="label label-sm label-danger">DISABLED</span>';
        } else {
            $status_tag = '<span class="label label-sm label-success">ACTIVE</span>';
        }
        $records["data"][] = array(
            '<input type="checkbox" name="pk" value="'.$services['services_id'].'"> '.$status_tag.'',
            '<a class="editable_item_'.$services['services_id'].'" style="color:#333333" data-name="services_item" data-pk="'.$services['services_id'].'" data-type="text" data-placement="right" data-title="Enter new service name.." data-url="assets/app/update_settings.php?update=location_services">'.$services['services_item'].'</a>',
            '<a class="editable_item_'.$services['services_id'].'" style="color:#333333" data-name="services_item_desc" data-pk="'.$services['services_id'].'" data-type="text" data-placement="right" data-title="Enter new service description.." data-url="assets/app/update_settings.php?update=location_services">'.$services['services_item_desc'].'</a>',
            '<a class="editable_item_'.$services['services_id'].'" style="color:#333333" data-name="services_saleprice" data-pk="'.$services['services_id'].'" data-type="number" data-placement="right" data-title="Enter new service saleprice.." data-url="assets/app/update_settings.php?update=location_services">'.$services['services_saleprice'].'</a>',
            '<a class="editable_item_'.$services['services_id'].'" style="color:#333333" data-name="services_cost" data-pk="'.$services['services_id'].'" data-type="number" data-placement="right" data-title="Enter new service cost.." data-url="assets/app/update_settings.php?update=location_services">'.$services['services_cost'].'</a>',
            '<a class="editable_item_'.$services['services_id'].'_taxable" style="color:#333333" data-name="services_taxable" data-pk="'.$services['services_id'].'" data-type="select" data-placement="right" data-title="Select new taxable status.." data-url="assets/app/update_settings.php?update=location_services">'.$taxable_tag.'</a>',
            '<a class="editable_item_'.$services['services_id'].'_commissionable" style="color:#333333" data-name="services_commissionable" data-pk="'.$services['services_id'].'" data-type="select" data-placement="right" data-title="Select new commissionable status.." data-url="assets/app/update_settings.php?update=location_services">'.$commissionable_tag.'</a>',
            '<a class="editable_item_'.$services['services_id'].'_redeemable" style="color:#333333" data-name="services_redeemable" data-pk="'.$services['services_id'].'" data-type="select" data-placement="right" data-title="Select new redeemable status.." data-url="assets/app/update_settings.php?update=location_services">'.$redeemable_tag.'</a>',
            '<a class="editable_item_'.$services['services_id'].'_type" style="color:#333333" data-name="services_type" data-pk="'.$services['services_id'].'" data-type="select" data-placement="right" data-title="Select new type.." data-url="assets/app/update_settings.php?update=location_services">'.$services['services_type'].'</a>',
            '<button type="button" value="editable_item_'.$services['services_id'].'" class="btn default btn-xs red-stripe edit_line"><i class="fa fa-edit"></i> Edit</a>',
        );
    }
    if (isset($_REQUEST["customActionType"]) && $_REQUEST["customActionType"] == "group_action") {
        $checkRequest = explode("|", $_REQUEST['customActionName']);
        if($checkRequest[0] == 'copyTo'){
            foreach($_POST['id'] as $pk){
                mysql_query("INSERT INTO fmo_services (services_location_token, services_item, services_item_desc, services_saleprice, services_cost, services_taxable, services_commissionable, services_type, services_status) SELECT services_location_token, services_item, services_item_desc, services_saleprice, services_cost, services_taxable, services_commissionable, services_type, services_status FROM fmo_services WHERE service_id='".mysql_real_escape_string($pk)."'");
                mysql_query("UPDATE fmo_services SET services_location_token='".mysql_real_escape_string($checkRequest[1])."' WHERE service_id='".mysql_insert_id()."'");
                $i++;
            }
            $records["customActionMessage"] = $i." zipcode(s) were copied to the their new locations successfully.";
            $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
        } else {
            if ($_REQUEST['customActionName'] == 'Delete') {
                foreach ($_POST['id'] as $pk) {
                    mysql_query("DELETE FROM fmo_services WHERE service_id='" . mysql_real_escape_string($pk) . "'");
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
