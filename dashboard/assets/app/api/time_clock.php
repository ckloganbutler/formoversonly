<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/8/2017
 * Time: 11:21 PM
 */
session_start();
include '../init.php';

if(isset($_GET['admin']) && $_GET['admin'] == 'trl'){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findEmployees = mysql_query("SELECT timeclock_id, timeclock_clockin, timeclock_clockout, timeclock_hours, timeclock_timestamp, timeclock_ip FROM fmo_users_employee_timeclock WHERE timeclock_user='".mysql_real_escape_string($_GET['uuid'])."' ORDER BY timeclock_timestamp DESC");
    $iTotalRecords = mysql_num_rows($findEmployees);

    $records = array();
    $records["data"] = array();

    while($emp = mysql_fetch_assoc($findEmployees)) {
        $location = mysql_fetch_array(mysql_query("SELECT location_name FROM fmo_locations WHERE location_token='".$emp['user_employer_location']."'"));
        if($emp['timeclock_clockout'] == '0000-00-00 00:00:00'){
            $emp['timeclock_clockout'] = 'N/A (Clocked in)';
        }
        $records["data"][] = array(
            ''.date('M, d, Y', strtotime($emp['timeclock_timestamp'])).'',
            '<a class="editable_time_'.$emp['timeclock_id'].'" style="color:#333333" data-name="timeclock_clockin" data-pk="'.$emp['timeclock_id'].'" data-type="datetime" data-format="yyyy-mm-dd hh:ii:ss" data-placement="right" data-title="Select clock-in date & time.." data-url="assets/app/update_settings.php?update=time_clock">'.$emp['timeclock_clockin'].'</a>',
            ''.$emp['timeclock_ip'].'',
            '<a class="editable_time_'.$emp['timeclock_id'].'" style="color:#333333" data-name="timeclock_clockout" data-pk="'.$emp['timeclock_id'].'" data-type="datetime" data-format="yyyy-mm-dd hh:ii:ss" data-placement="right" data-title="Select clock-out date & time.." data-url="assets/app/update_settings.php?update=time_clock">'.$emp['timeclock_clockout'].'</a>',
            ''.$emp['timeclock_hours'].'',
            '<button type="button" class="btn default btn-xs red-stripe edit" data-edit="editable_time_'.$emp['timeclock_id'].'" data-reload="#timeclock_admin"><i class="fa fa-edit"></i> Edit</button> <button class="btn red btn-xs delete_tc" data-id="'.$emp['timeclock_id'].'"><i class="fa fa-times"></i> Delete</button>'
        );
    }

    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;

    echo json_encode($records);
}

if(isset($_GET['t']) && $_GET['t'] == 'lp'){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findEmployees = mysql_query("SELECT timeclock_id, timeclock_clockin, timeclock_clockout, timeclock_hours, timeclock_timestamp, timeclock_ip, timeclock_ip_out FROM fmo_users_employee_timeclock WHERE timeclock_user='".mysql_real_escape_string($_SESSION['uuid'])."' ORDER BY timeclock_timestamp DESC LIMIT 25");
    $iTotalRecords = mysql_num_rows($findEmployees);

    $records = array();
    $records["data"] = array();

    while($emp = mysql_fetch_assoc($findEmployees)) {
        $location = mysql_fetch_array(mysql_query("SELECT location_name FROM fmo_locations WHERE location_token='".$emp['user_employer_location']."'"));
        if($emp['timeclock_clockout'] == '0000-00-00 00:00:00'){
            $emp['timeclock_clockout'] = 'N/A (Clocked in)';
        }
        $records["data"][] = array(
            ''.date('M, d, Y', strtotime($emp['timeclock_timestamp'])).'',
            '<strong>'.$emp['timeclock_clockin'].'</strong> ( '.$emp['timeclock_ip'].' )',
            '<strong>'.$emp['timeclock_clockout'].'</strong> ( '.$emp['timeclock_ip_out'].' )',
            '<strong>'.$emp['timeclock_hours'].'</strong>'
        );
    }

    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;

    echo json_encode($records);
}

if(isset($_GET['t']) && $_GET['t'] == 'pl'){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findEmployees = mysql_query("SELECT timeclock_id, timeclock_clockin, timeclock_timestamp, timeclock_ip, timeclock_user FROM fmo_users_employee_timeclock WHERE timeclock_complete=0 AND timeclock_company_token='".mysql_real_escape_string($_GET['cuid'])."' ORDER BY timeclock_timestamp DESC");
    $iTotalRecords = mysql_num_rows($findEmployees);

    $records = array();
    $records["data"] = array();

    while($emp = mysql_fetch_assoc($findEmployees)) {
        $date_in  = $emp['timeclock_clockin'];
        $date_out = date('Y-m-d H:i:s');
        $date1 = new DateTime($date_in);
        $date2 = new DateTime($date_out);
        $diff = $date1->diff($date2);
        $hours   = $diff->h;
        $minutes = $diff->i;
        $seconds = $diff->s;

        $worked = decimalHours($hours.":".$minutes.":".$seconds);
        $records["data"][] = array(
            '<strong>ON THE CLOCK:</strong> '.name($emp['timeclock_user']).'',
            '<strong>'.$emp['timeclock_clockin'].'</strong> ( '.$emp['timeclock_ip'].' )',
            ''.number_format($worked, 2).' hrs',
            '<button class="btn btn-xs red-stripe aco" data-uuid="'.$emp['timeclock_user'].'"><i class="fa fa-user-times"></i> Clock Employee Out</button>',
        );
    }

    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;

    echo json_encode($records);
}
