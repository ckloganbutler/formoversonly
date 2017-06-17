<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/8/2017
 * Time: 11:21 PM
 */
session_start();
include '../init.php';

if(isset($_GET) && $_GET['type'] == 'comments'){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findComments = mysql_query("SELECT comment_id, comment_comment, comment_by_user_token, comment_timestamp FROM fmo_locations_events_comments WHERE comment_event_token='".mysql_real_escape_string($_GET['ev'])."' ORDER BY comment_timestamp DESC");
    $iTotalRecords = mysql_num_rows($findComments);

    $records = array();
    $records["data"] = array();

    while($comt = mysql_fetch_assoc($findComments)) {
        $records["data"][] = array(
            ''.$comt['comment_timestamp'].'',
            ''.$comt['comment_comment'].'',
            ''.name($comt['comment_by_user_token']).'',
        );
    }


    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;

    echo json_encode($records);
}

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

if(isset($_GET) && $_GET['type'] == 'reviews'){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findReviews = mysql_query("SELECT review_rating, review_comments, review_name, review_timestamp FROM fmo_locations_events_reviews WHERE review_event_token='".mysql_real_escape_string($_GET['ev'])."'");
    $iTotalRecords = mysql_num_rows($findReviews);

    $records = array();
    $records["data"] = array();

    while($rv = mysql_fetch_assoc($findReviews)) {
        $records["data"][] = array(
            ''.$rv['review_timestamp'].'',
            ''.$rv['review_rating'].' stars',
            ''.$rv['review_comments'].'',
            ''.$rv['review_name'].'',
            '<a class="btn default btn-xs red-stripe"><i class="fa fa-times"></i> Delete</a>'
        );
    }


    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;

    echo json_encode($records);
}

if(isset($_GET) && $_GET['type'] == 'claims'){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findClaims = mysql_query("SELECT claim_timestamp, claim_item, claim_padded, claim_weight, claim_comments FROM fmo_locations_events_claims WHERE claim_event_token='".mysql_real_escape_string($_GET['ev'])."'");
    $iTotalRecords = mysql_num_rows($findClaims);

    $records = array();
    $records["data"] = array();

    while($cl = mysql_fetch_assoc($findClaims)) {
        if($cl['claim_padded'] == 'No'){
            $label = "label-danger";
        } else {
            $label = "label-success";
        }
        $records["data"][] = array(
            ''.$cl['claim_timestamp'].'',
            ''.$cl['claim_item'].'',
            '<span class="label '.$label.'">'.$cl['claim_padded'].'</span>',
            ''.$cl['claim_weight'].'',
            ''.$cl['claim_comments'].'',
            '<a class="btn default btn-xs red-stripe"><i class="fa fa-times"></i> Delete</a>'
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
        $records["data"][] = array(
            '<span class="label label-sm label-success text-center"><a class="lb_'.$lb['laborer_id'].'" style="color:white" data-name="laborer_role" data-pk="'.$lb['laborer_id'].'" data-type="select" data-source="[{value: \'CREW LEADER\', text: \'Crew Leader\'}, {value: \'CREWMAN\', text: \'Crewman\'}]" data-placement="right" data-title="Select new role.." data-url="assets/app/update_settings.php?setting=event_laborers">'.$lb['laborer_role'].'</a></span>',
            ''.name($lb['laborer_user_token']).'',
            '$'.number_format($lb['laborer_rate'], 2).'/hr',
            '<a class="lb_'.$lb['laborer_id'].'" style="color:#333333" data-inputclass="form-control" data-name="laborer_hours_worked" data-pk="'.$lb['laborer_id'].'" data-type="number" data-placement="right" data-title="Enter new paid hours.." data-url="assets/app/update_settings.php?setting=event_laborers">'.number_format($lb['laborer_hours_worked'], 2).'</a>',
            '$<a class="lb_'.$lb['laborer_id'].'" style="color:#333333" data-inputclass="form-control" data-name="laborer_tip" data-pk="'.$lb['laborer_id'].'" data-type="number" data-placement="right" data-title="Enter new tip/other pay.." data-url="assets/app/update_settings.php?setting=event_laborers">'.number_format($lb['laborer_tip'], 2).'</a>',
            ''.name($lb['laborer_by_user_token']).'',
            '<a class="btn default btn-xs red-stripe edit" data-edit="lb_'.$lb['laborer_id'].'" data-reload=""><i class="fa fa-edit"></i> Edit</a> <a class="btn default btn-xs red delete" data-delete="lb_'.$lb['laborer_id'].'" data-event="'.$_GET['ev'].'"><i class="fa fa-times"></i> Delete</a>',
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


