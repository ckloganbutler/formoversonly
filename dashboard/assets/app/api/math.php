<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 11/24/2017
 * Time: 5:34 AM
 */
session_start();
include      '../init.php';

if(isset($_GET['e']) && $_GET['e'] == 'EmP'){
    $mr    = $_POST['mr'];
    $str   = mysql_fetch_array(mysql_query("SELECT storage_period FROM fmo_locations_storages WHERE storage_token='".mysql_real_escape_string($_GET['su'])."'"));
    $date1 = new DateTime(date('Y-m-d', strtotime($_POST['date1'])));
    $date2 = new DateTime(date('Y-m-d', strtotime($_POST['date2'])));

    if($str['storage_period'] == 'Weekly'){
        $dpm = 7;
    } else { $dpm = cal_days_in_month(CAL_GREGORIAN, date('m', strtotime($date1)), date('Y', strtotime($date1))); }

    $interval = $date1->diff($date2);
    $days     = $interval->days;
    // it's my job to do math :-)
    echo number_format(($mr / $dpm) * $days, 2)."/".$str['storage_period'];
}

if(isset($_GET['e']) && $_GET['e'] == 'MeP'){

}