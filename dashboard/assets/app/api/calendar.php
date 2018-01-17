<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 5/31/2017
 * Time: 2:45 AM
 */
session_start();
include '../init.php';

if(isset($_GET['trl']) && $_GET['trl'] == 'ext'){
    $events = mysql_query("SELECT event_id, event_name, event_time, event_date_start, event_token, event_status, event_type FROM fmo_locations_events WHERE (event_date_start>='".mysql_real_escape_string(date('Y-m-d', strtotime($_POST['start'])))."' AND event_date_end<='".mysql_real_escape_string(date('Y-m-d', strtotime($_POST['end'])))."') AND (event_location_token='".mysql_real_escape_string($_GET['luid'])."' AND NOT event_status=0 AND NOT event_status=9)") or die(mysql_error());

    $results = array();
    if(mysql_num_rows($events)>0){
        while($event = mysql_fetch_assoc($events)){
            switch($event['event_status']){
                case 5: $status = "Canceled"; $color = "#ffffff"; $background = '#cb5a5e'; break;
                default: $status = "Active"; $color = "#000000"; $background = '#ffffff'; break;
            }
            $times = explode("to", $event['event_time']);
            if(!empty($times[1])){
                $times[1] = ' to '.$times[1];
            }
            if(strtolower($event['event_type']) == 'out of state') {
                $color      = "#ffffff";
                $background = "#337ab7";
            }
            $results['data'][] = array(
                "id" => "".$event['event_id']."",
                "nm" => "".$event['event_name']."",
                "ev" => "".$event['event_token']."",
                "st" => "".$event['event_date_start']." ".date('G:i:s', strtotime($times[0]))."",
                "cl" => "".$color."",
                "bg" => "".$background."",
            );
        }
        echo json_encode($results);
    }
}

?>