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
            '<button type="button" class="btn default btn-xs red-stripe edit" data-edit="editable_time_'.$emp['timeclock_id'].'" data-reload="#timeclock_admin"><i class="fa fa-edit"></i> Edit</a>'
        );
    }

    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;

    echo json_encode($records);
}elseif(isset($_SESSION['uuid'])){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findEmployees = mysql_query("SELECT timeclock_id, timeclock_clockin, timeclock_clockout, timeclock_hours, timeclock_timestamp, timeclock_ip FROM fmo_users_employee_timeclock WHERE timeclock_user='".mysql_real_escape_string($_SESSION['uuid'])."' ORDER BY timeclock_timestamp DESC");
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
            ''.$emp['timeclock_clockin'].'',
            ''.$emp['timeclock_ip'].'',
            ''.$emp['timeclock_clockout'].'',
            ''.$emp['timeclock_hours'].''
        );
    }

    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;

    echo json_encode($records);
}
