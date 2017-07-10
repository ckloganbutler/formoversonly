<?php
/**
 * Created by PhpStorm.
 * User: gameroom
 * Date: 7/10/2017
 * Time: 5:24 PM
 */
include '../../app/init.php';
$event = mysql_fetch_array(mysql_query("SELECT event_status FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($_GET['ev'])."'"));
if(!empty($event)){
    if($event['event_status'] == 1){
        // New booking. Needs confirmed!
        ?>
        <div class="col-md-12">
            <p>NEW BOOKING. NEEDS CONFIRMED</p>
        </div>
        <?php
    } elseif($event['event_status'] >= 2){
        // Confirmed. Show more info!
        ?>
        <div class="col-md-12">
            <p>CONFIRMED. SHOW MORE INFO.</p>
        </div>
        <?php
    }
}