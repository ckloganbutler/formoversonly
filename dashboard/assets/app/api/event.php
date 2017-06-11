<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/8/2017
 * Time: 11:21 PM
 */
session_start();
include '../init.php';

if(isset($_GET) && $_GET['type'] == 'documents'){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findRates = mysql_query("SELECT services_id FROM fmo_services WHERE services_location_token='".mysql_real_escape_string($_GET['luid'])."'");
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
        if($services['services_status'] == 0) {
            $status_tag = '<span class="label label-sm label-danger">DISABLED</span>';
        } else {
            $status_tag = '<span class="label label-sm label-success">ACTIVE</span>';
        }
        $records["data"][] = array(
            '<input type="checkbox" name="pk" value="'.$services['services_id'].'"> '.$status_tag.'',
            'desc',
            '<button type="button" value="editable_item_'.$services['services_id'].'" class="btn default btn-xs red-stripe edit_line"><i class="fa fa-edit"></i> Edit</a>',
        );
    }


    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;

    echo json_encode($records);
}

if(isset($_GET) && $_GET['type'] == 'timeline'){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findTimelines = mysql_query("SELECT timeline_id, timeline_by_user_token, timeline_type, timeline_reasoning, timeline_timestamp  FROM fmo_locations_events_timelines WHERE timeline_event_token='".mysql_real_escape_string($_GET['ev'])."' ORDER BY timeline_timestamp DESC");
    $iTotalRecords = mysql_num_rows($findTimelines);

    $records = array();
    $records["data"] = array();

    while($time = mysql_fetch_assoc($findTimelines)) {
        $records["data"][] = array(
            ''.$time['timeline_timestamp'].'',
            ''.$time['timeline_type'].'',
            ''.$time['timeline_reasoning'].'',
            ''.name($time['timeline_by_user_token']).''
        );
    }


    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;

    echo json_encode($records);
}

if(isset($_GET) && $_GET['type'] == 'labor'){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findLabor = mysql_query("SELECT laborer_id, laborer_user_token, laborer_hours_worked, laborer_role, laborer_timestamp, laborer_by_user_token, laborer_tip, laborer_rate FROM fmo_locations_events_laborers WHERE laborer_event_token='".mysql_real_escape_string($_GET['ev'])."'");
    $iTotalRecords = mysql_num_rows($findLabor);

    $records = array();
    $records["data"] = array();

    while($lb = mysql_fetch_assoc($findLabor)) {
        if($lb['laborer_role'] == 0) {
            $role = '<span class="label label-sm label-danger text-center">CREWMAN</span>';
        } else {
            $role = '<span class="label label-sm label-success text-center">CREW LEADER</span>';
        }
        $records["data"][] = array(
            ''.$role.'',
            ''.name($lb['laborer_user_token']).'',
            '$'.number_format($lb['laborer_rate'], 2).'/hr',
            ''.number_format($lb['laborer_hours_worked'], 2).'',
            '$'.number_format($lb['laborer_tip'], 2).'',
            ''.name($lb['laborer_by_user_token']).'',
            '<button type="button" value="editable_item_'.$lb['laborer_id'].'" class="btn default btn-xs red-stripe edit_line"><i class="fa fa-edit"></i> Edit</a>',
        );
    }


    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;

    echo json_encode($records);
}

if(isset($_GET['type']) && $_GET['type'] == 'rates'){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findRates = mysql_query("SELECT services_id, services_item, services_item_desc, services_taxable, services_commissionable FROM fmo_services WHERE services_location_token='".mysql_real_escape_string($_GET['luid'])."'");
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
        $records["data"][] = array(
            '<a>'.$services['services_item'].'</a>',
            '<a>'.$services['services_item_desc'].'</a>',
            '<a>'.$taxable_tag.'</a>',
            '<a>'.$commissionable_tag.'</a>',
            '<button type="button" data-id="'.$services['services_id'].'" data-ev="'.$_GET['ev'].'" class="btn default btn-xs blue-stripe add_item"><i class="fa fa-plus"></i> Add to invoice</a>',
        );
    }

    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;

    echo json_encode($records);
}

if(isset($_GET['type']) && $_GET['type'] == 'sales'){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findItems = mysql_query("SELECT item_item, item_desc, item_qty, item_cost, item_total FROM fmo_locations_events_items WHERE item_event_token='".mysql_real_escape_string($_GET['ev'])."'");
    $iTotalRecords = mysql_num_rows($findItems);

    $records = array();
    $records["data"] = array();

    while($items = mysql_fetch_assoc($findItems)) {
        $records["data"][] = array(
            '<a>'.$items['item_item'].'</a>',
            '<a>'.$items['item_desc'].'</a>',
            '<a>'.$items['item_qty'].'</a>',
            '<a>'.$items['item_cost'].'</a>',
            '<a>'.$items['item_total'].'</a>'
        );
    }

    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;

    echo json_encode($records);
}


