<?php
/**
 * Created by PhpStorm.
 * User: gameroom
 * Date: 5/17/2017
 * Time: 1:01 PM
 */
session_start();
include '../init.php';

if(isset($_GET) && $_GET['type'] == 'advance_amt'){
    $user     = $_GET['uuid'];
    $user_pay = mysql_fetch_array(mysql_query("SELECT user_employer_rate FROM fmo_users WHERE user_token='".mysql_real_escape_string($user)."'"));
    if($user_pay['user_employer_rate'] > 0){
        $refStart                = new DateTime('2017-01-02');
        $periodLength            = 14;
        $now                     = new DateTime();
        $cur                     = date('Y-m-d');
        $daysIntoCurrentPeriod   = (int)$now->diff($refStart)->format('%a') % $periodLength;
        $currentPeriodStart      = clone $now;
        $currentPeriodStart->sub(new DateInterval('P'.$daysIntoCurrentPeriod.'D'));
        $start                   = date("Y-m-d", strtotime($cur." -".$daysIntoCurrentPeriod." days"));
        $end                     = date('Y-m-d', strtotime($start." +13 days"));
        $hours = array();
        $prev  = mysql_query("
                            SELECT advance_requested FROM fmo_users_employee_advances
                            WHERE (DATE(advance_timestamp)>='".mysql_real_escape_string($start)."' AND DATE(advance_timestamp)<='".mysql_real_escape_string($end)."') AND advance_user_token='".mysql_real_escape_string($user)."'");

        $hours = mysql_query("
        SELECT timeclock_user, timeclock_hours FROM fmo_users_employee_timeclock 
        WHERE (DATE(timeclock_clockout)>='".mysql_real_escape_string($start)."' AND DATE(timeclock_clockout)<='".mysql_real_escape_string($end)."') AND timeclock_user='".mysql_real_escape_string($user)."'") or die(mysql_error());
        $misc_hours = mysql_query("SELECT laborer_hours_worked FROM fmo_locations_events_laborers WHERE (DATE(laborer_timestamp)>='".mysql_real_escape_string($start)."' AND DATE(laborer_timestamp)<='".mysql_real_escape_string($end)."') AND laborer_user_token='".mysql_real_escape_string($user)."'");
        $pay = array();
        if(mysql_num_rows($hours) > 0 || mysql_num_rows($misc_hours) > 0){
            while($work = mysql_fetch_assoc($hours)){
                $pay['hours']+=$work['timeclock_hours'];
            } while ($misc_work = mysql_fetch_assoc($misc_hours)){
                $pay['hours']+=$misc_work['laborer_hours_worked'];
            }
            if($pay['hours'] > 0){
                $pay['rate']      = $user_pay['user_employer_rate'];
                $pay['earned']    = $pay['hours'] * $user_pay['user_employer_rate'];
                if(mysql_num_rows($prev) > 0){
                    while($loans = mysql_fetch_assoc($prev)){
                        $pay['loans'] += $loans['advance_requested'];
                    }
                } else {$pay['loans'] = 0;}
                $pay['available'] = number_format(($pay['earned'] * .25) - $pay['loans'], 2);
                if($pay['available'] >= $_GET['requested']){
                    // user loan approved, they may continue with amount.
                    echo json_encode(true);
                } else {
                    echo json_encode(false);
                    // user is trying to take more than allowed
                }
            } else {
                // user has no hours/not enough hours.
                echo json_encode(false);
            }
        } else {
            // user has no clock records, therefore no hours.
            echo json_encode(false);
        }
    } else {
        // user makes no money, or has no salary.
        echo json_encode(false);
    }
}

if(isset($_GET) && $_GET['type'] == 'documents'){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findDocuments = mysql_query("SELECT document_id, document_link, document_type, document_desc, document_by_user_token, document_timestamp FROM fmo_users_employee_documents WHERE document_user_token='".mysql_real_escape_string($_GET['uuid'])."' ORDER BY document_id DESC");
    $iTotalRecords = mysql_num_rows($findDocuments);

    $records = array();
    $records["data"] = array();

    while($doc = mysql_fetch_assoc($findDocuments)) {
        $records["data"][] = array(
            '<embed height="200" width="100%" src="'.$doc['document_link'].'"/><br/><center>'.$doc['document_type'].'</center>',
            'File Type: <strong>'.$doc['document_type'].'</strong><br/> File Description: <strong>'.$doc['document_desc'].'</strong>  <br/> <a target="_blank" href="'.$doc['document_link'].'"><strong>Click here to view document</strong></a> <br/> File uploaded by: <strong>'.name($doc['document_by_user_token']).'</strong> <br/> File uploaded on: '.date('m/d/Y G:s A', strtotime($doc['document_timestamp'])),
            '<button type="button" class="btn btn-xs red del_p_doc" data-id="'.$doc['document_id'].'"><i class="fa fa-times"></i> Delete document</button>',
        );
    }


    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;

    echo json_encode($records);
}

if(isset($_GET) && $_GET['type'] == 'comments'){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findComments = mysql_query("SELECT comment_id, comment_comment, comment_by_user_token, comment_timestamp FROM fmo_users_employee_comments WHERE comment_user_token='".mysql_real_escape_string($_GET['uuid'])."' ORDER BY comment_timestamp DESC");
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

if(isset($_GET) && $_GET['type'] == 'writeups'){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findWriteups = mysql_query("SELECT writeup_id, writeup_reasoning, writeup_action, writeup_by_user_token, writeup_timestamp FROM fmo_users_employee_writeups WHERE writeup_user_token='".mysql_real_escape_string($_GET['uuid'])."' ORDER BY writeup_timestamp DESC");
    $iTotalRecords = mysql_num_rows($findWriteups);

    $records = array();
    $records["data"] = array();

    while($wu = mysql_fetch_assoc($findWriteups)) {
        $records["data"][] = array(
            ''.$wu['writeup_timestamp'].'',
            ''.$wu['writeup_reasoning'].'',
            ''.$wu['writeup_action'].'',
            ''.name($wu['writeup_by_user_token']).''
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
    $findTimeline = mysql_query("SELECT timeline_id, timeline_by_user_token, timeline_datatype, timeline_reasoning, timeline_timestamp FROM fmo_users_employee_timelines WHERE timeline_user_token='".mysql_real_escape_string($_GET['uuid'])."' ORDER BY timeline_timestamp DESC");
    $iTotalRecords = mysql_num_rows($findTimeline);

    $records = array();
    $records["data"] = array();

    while($time = mysql_fetch_assoc($findTimeline)) {
        $records["data"][] = array(
            ''.$time['timeline_timestamp'].'',
            ''.$time['timeline_datatype'].'',
            ''.$time['timeline_reasoning'].'',
            ''.name($time['timeline_by_user_token']).''
        );
    }


    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;

    echo json_encode($records);
}

if(isset($_GET) && $_GET['type'] == 'advances'){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findAdvances = mysql_query("SELECT advance_id, advance_timestamp, advance_requested, advance_available, advance_reason, advance_by_user_token FROM fmo_users_employee_advances WHERE advance_user_token='".mysql_real_escape_string($_GET['uuid'])."' ORDER BY advance_timestamp DESC");
    $iTotalRecords = mysql_num_rows($findAdvances);

    $records = array();
    $records["data"] = array();

    while($ad = mysql_fetch_assoc($findAdvances)) {
        $records["data"][] = array(
            ''.$ad['advance_timestamp'].'',
            ''.$ad['advance_available'].'',
            ''.$ad['advance_requested'].'',
            ''.$ad['advance_reason'].'',
            ''.name($ad['advance_by_user_token']).' <a class="btn btn-xs default red-stripe pull-right" href="//www.formoversonly.com/dashboard/assets/public/loan_auth.php?t=auth_tok&i='.$ad['advance_id'].'" target="_blank">Reprint Ticket</a>'
        );
    }


    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;

    echo json_encode($records);
}

if(isset($_GET) && $_GET['type'] == 'licenses'){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findLicenses = mysql_query("SELECT license_id, license_type, license_state, license_prefix, license_number, license_timestamp FROM fmo_users_licenses WHERE license_user_token='".mysql_real_escape_string($_GET['uuid'])."' ORDER BY license_timestamp DESC");
    $iTotalRecords = mysql_num_rows($findLicenses);

    $records = array();
    $records["data"] = array();

    while($lc = mysql_fetch_assoc($findLicenses)) {
        $records["data"][] = array(
            ''.$lc['license_type'].'',
            ''.$lc['license_state'].'',
            ''.$lc['license_prefix'].'',
            ''.$lc['license_number'].'',
            ''.$lc['license_timestamp'].''
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
    $findLabor = mysql_query("SELECT laborer_id, laborer_user_token, laborer_event_token, laborer_desc, laborer_hours_worked, laborer_timestamp, laborer_by_user_token, laborer_rate FROM fmo_locations_events_laborers WHERE laborer_event_token='".mysql_real_escape_string($_GET['uuid'])."'") or die(mysql_error());
    $iTotalRecords = mysql_num_rows($findLabor);

    $records = array();
    $records["data"] = array();

    while($lb = mysql_fetch_assoc($findLabor)) {
        $records["data"][] = array(
            ''.$lb['laborer_timestamp'].'',
            ''.$lb['laborer_desc'].'',
            '$'.$lb['laborer_rate'].'/hr',
            ''.$lb['laborer_hours_worked'].'hrs',
            ''.name($lb['laborer_by_user_token']).' <br/><a class="btn red btn-xs del_labor" data-delete="'.$lb['laborer_id'].'"><i class="fa fa-times"></i> Delete</a>'
        );
    }


    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;

    echo json_encode($records);
}

if(isset($_GET) && $_GET['type'] == 'sendables'){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findDocuments = mysql_query("SELECT sendable_id, sendable_path, sendable_name, sendable_message, sendable_token FROM fmo_sendables WHERE sendable_company_token='".mysql_real_escape_string($_SESSION['cuid'])."'");
    $iTotalRecords = mysql_num_rows($findDocuments);

    $records = array();
    $records["data"] = array();

    while($doc = mysql_fetch_assoc($findDocuments)) {
        if($_SESSION['group'] == 1 && !isset($_GET['std'])){
            $records["data"][] = array(
                '<embed height="200" width="300" src="assets/upload/sendables/'.$doc['sendable_token'].'.pdf"/><br/><center>'.$doc['sendable_name'].'</center>',
                'Document: <br/><strong class="doc_'.$doc['sendable_token'].'" style="color:#333333; cursor: pointer;" data-inputclass="form-control" data-name="sendable_name" data-pk="'.$doc['sendable_token'].'" data-type="text" data-placement="right" data-title="Enter new name.." data-url="assets/app/update_settings.php?setting=sendable">'.$doc['sendable_name'].'</strong> <br/> <a target="_blank" href="assets/upload/sendables/'.$doc['sendable_token'].'.pdf"><strong>Click here to view document</strong></a>',
                'Document Message: <br/><strong  class="doc_'.$doc['sendable_token'].'" style="color:#333333;  cursor: pointer;" data-inputclass="form-control" data-name="sendable_message" data-pk="'.$doc['sendable_token'].'" data-type="text" data-placement="right" data-title="Enter new message.." data-url="assets/app/update_settings.php?setting=sendable">'.$doc['sendable_message'].'</strong>',
                '<button class="btn default red-stripe del_sendable" data-delete="'.$doc['sendable_token'].'" type="button"><i class="fa fa-times"></i> Delete</button> <br/> <br/> <button class="btn default blue-stripe edit" data-edit="doc_'.$doc['sendable_token'].'" data-pk="'.$doc['sendable_token'].'" type="button"><i class="fa fa-pencil"></i> Edit</button>'
            );
        } else {
            $records["data"][] = array(
                '<embed height="200" width="300" src="assets/upload/sendables/'.$doc['sendable_token'].'.pdf"/><br/><center>'.$doc['sendable_name'].'</center>',
                'Document: <br/><strong class="doc_'.$doc['sendable_token'].'" style="color:#333333; cursor: pointer;" data-inputclass="form-control" data-name="sendable_name" data-pk="'.$doc['sendable_token'].'" data-type="text" data-placement="right" data-title="Enter new name.." data-url="assets/app/update_settings.php?setting=sendable">'.$doc['sendable_name'].'</strong> <br/> <a target="_blank" href="assets/upload/sendables/'.$doc['sendable_token'].'.pdf"><strong>Click here to view document</strong></a>',
                'Document Message: <br/><strong  class="doc_'.$doc['sendable_token'].'" style="color:#333333;  cursor: pointer;" data-inputclass="form-control" data-name="sendable_message" data-pk="'.$doc['sendable_token'].'" data-type="text" data-placement="right" data-title="Enter new message.." data-url="assets/app/update_settings.php?setting=sendable">'.$doc['sendable_message'].'</strong >',
            );
        }

    }


    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;

    echo json_encode($records);
}