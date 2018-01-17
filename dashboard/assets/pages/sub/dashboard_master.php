<?php
/**
 * Created by PhpStorm.
 * User: gameroom
 * Date: 6/21/2017
 * Time: 8:57 AM
 */
session_start();
include '../../app/init.php';

if(isset($_SESSION['logged'])) {
    if ($_GET['t'] == 'dash_evs') {
        if($_SESSION['group'] == 3){
            $range = explode(" - ", $_POST['range']);
            ?>
            <div class="row">
                <div class="col-md-6 col-xs-6 col-sm-6 col-lg-6">
                    <h3 style="margin-top: 0px;">
                        Morning
                        <small class="hidden-sm"><span class="text-danger">| <span id="morning"></span></span></small>
                    </h3>
                    <hr/>
                    <div class="todo-tasklist">
                        <?php
                        $events = mysql_query("SELECT event_name, event_date_start, event_date_end, event_time, event_token, event_status, event_type, event_subtype, event_booking, event_truckfee, event_laborrate FROM fmo_locations_events WHERE event_user_token='".mysql_real_escape_string($_SESSION['uuid'])."' AND (event_date_start>='".mysql_real_escape_string($range[0])."' AND event_date_end<='".mysql_real_escape_string($range[1])."') AND NOT event_status=0 AND NOT event_status=9 ORDER BY event_time+0 ASC");
                        if(mysql_num_rows($events) > 0){
                            $eventsCount = 0;
                            while($event = mysql_fetch_assoc($events)){
                                switch($event['event_status']){
                                    case 1: $status = "New Booking"; $color = "blue"; $badge = "badge-info"; break;
                                    case 2: $status = "Confirmed"; $color = "green"; $badge = "badge-success"; break;
                                    case 3: $status = "Left Message"; $color = "yellow"; $badge = "badge-warning"; break;
                                    case 4: $status = "On Hold"; $color = "yellow"; $badge = "badge-warning"; break;
                                    case 5: $status = "Canceled"; $color = "red"; $badge = "badge-danger"; break;
                                    case 6: $status = "Customer Confirmed"; $color = "purple"; $badge = "badge-purple"; break;
                                    case 8: $status = "Completed"; $color = "yellow"; $badge = "badge-warning"; break;
                                    case 9: $status = "Dead Hot Lead"; $color = "red"; $badge = "badge-danger"; break;
                                    default: $status = "On Hold"; $color = "yellow"; $badge = "badge-warning"; break;
                                }
                                $times = explode(" to ", $event['event_time']);
                                if(strtotime($times[0]) >= strtotime("12:00PM")){
                                    continue;
                                }
                                if(!empty($times[1])){
                                    $times[1] = ' to '.$times[1];
                                }
                                $eventsCount++;
                                $start = mysql_fetch_array(mysql_query("SELECT address_address, address_city, address_bedrooms FROM fmo_locations_events_addresses WHERE address_event_token='".$event['event_token']."' AND address_type=1"));
                                $end   = mysql_fetch_array(mysql_query("SELECT address_address, address_city FROM fmo_locations_events_addresses WHERE address_event_token='".$event['event_token']."' AND address_type=2"));
                                ?>
                                <div class="todo-tasklist-item todo-tasklist-item-border-<?php echo $color; ?> col-md-12">
                                    <div class="todo-tasklist-item-title">
                                        <?php echo $event['event_name']; ?> <span class="font-<?php echo $color; ?>">|</span> <small><i class="fa fa-truck"></i> Trucks: <?php echo $event['event_truckfee']; ?> + <i class="fa fa-users"></i> Crew size: <?php echo $event['event_laborrate']; ?> <?php if(!empty($start['address_bedrooms'])){ ?> + <i class="fa fa-home"></i> <?php echo $start['address_bedrooms']; } ?></small> <span class="font-<?php echo $color; ?>">|</span>
                                        <?php
                                        if(!empty($event['event_type'])){
                                            ?>
                                            <span class="todo-tasklist-badge badge badge-roundless badge-danger"><?php echo $event['event_type']; ?></span>
                                            <?php
                                        }
                                        if(!empty($event['event_subtype'])){
                                            ?>
                                            <span class="todo-tasklist-badge badge badge-roundless badge-info"><?php echo $event['event_subtype']; ?></span>
                                            <?php
                                        }
                                        ?>
                                        <span class="badge badge-roundless <?php echo $badge; ?>"><?php echo $status; ?></span></div>
                                    <div class="todo-tasklist-item-text">
                                        <?php
                                        if($_SESSION['group'] >= 5.0 && $_SESSION['group'] != 3){
                                            ?>
                                            <strong>Be there to get ready by <?php echo date('g:i A', strtotime($times[0]." -45 minutes")); ?></strong>
                                            <?php
                                        } else {
                                            if(!empty($start['address_address']) && !empty($end['address_address'])){
                                                ?>
                                                <strong>Start:</strong> <?php echo $start['address_address'].", ".$start['address_city']; ?> <i class="fa fa-map"></i> <strong>End:</strong> <?php echo $end['address_address'].", ".$end['address_city']; ?>
                                                <?php
                                            } elseif(!empty($event['event_comments'])) {
                                                ?>
                                                <strong>Comments:</strong> <?php echo $event['event_comments']; ?>
                                                <?php
                                            } else {
                                                ?>
                                                <strong>Click to view more details & manage</strong>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                    <div class="todo-tasklist-controls pull-left">
                                    <span class="todo-tasklist-date"><i class="fa fa-calendar"></i>
                                        <?php
                                        if(date('M d, Y', strtotime($event['event_date_start'])) == date('M d, Y', strtotime($event['event_date_end']))) {
                                            echo date('M d, Y', strtotime($event['event_date_start']));
                                        } else {
                                            echo date('M d, Y', strtotime($event['event_date_start'])); ?> - <?php echo date('M d, Y', strtotime($event['event_date_end']));
                                        }
                                        ?>
                                        @ <?php echo $times[0].$times[1]; ?></span>
                                        <?php
                                        if($event['event_booking'] == 1){
                                            ?>
                                            <span class="todo-tasklist-badge badge badge-roundless badge-success"><i class="fa fa-check"  style="margin-top: -6px"></i> Booking fee paid</span>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                            <span id="morningCount" class="hidden"><?php echo $eventsCount; ?></span>
                            <?php
                        } else {
                            ?>
                            <br/>
                            <div class="alert alert-warning alert-dismissable" style="margin-top: -20px;">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                                <strong>No events found</strong> for this morning at this location.
                                <span id="morningCount" class="hidden">0</span>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="col-md-6 col-xs-6 col-sm-6 col-lg-6">
                    <h3 style="margin-top: 0px">
                        Afternoon
                        <small class="hidden-sm"><span class="text-danger">| <span id="afternoon"></span></span></small>
                    </h3>
                    <hr/>
                    <div class="todo-tasklist">
                        <?php
                        $events = mysql_query("SELECT event_name, event_date_start, event_date_end, event_time, event_token, event_status, event_type, event_subtype, event_booking, event_truckfee, event_laborrate FROM fmo_locations_events WHERE event_user_token='".mysql_real_escape_string($_SESSION['uuid'])."' AND (event_date_start>='".mysql_real_escape_string($range[0])."' AND event_date_end<='".mysql_real_escape_string($range[1])."') AND NOT event_status=0 AND NOT event_status=9 ORDER BY event_time+0 ASC");
                        if(mysql_num_rows($events) > 0){
                            $eventsCount = 0;
                            while($event = mysql_fetch_assoc($events)){
                                switch($event['event_status']){
                                    case 1: $status = "New Booking"; $color = "blue"; $badge = "badge-info"; break;
                                    case 2: $status = "Confirmed"; $color = "green"; $badge = "badge-success"; break;
                                    case 3: $status = "Left Message"; $color = "yellow"; $badge = "badge-warning"; break;
                                    case 4: $status = "On Hold"; $color = "yellow"; $badge = "badge-warning"; break;
                                    case 5: $status = "Canceled"; $color = "red"; $badge = "badge-danger"; break;
                                    case 6: $status = "Customer Confirmed"; $color = "purple"; $badge = "badge-purple"; break;
                                    case 8: $status = "Completed"; $color = "yellow"; $badge = "badge-warning"; break;
                                    case 9: $status = "Dead Hot Lead"; $color = "red"; $badge = "badge-danger"; break;
                                    default: $status = "On Hold"; $color = "yellow"; $badge = "badge-warning"; break;
                                }
                                $times = explode(" to ", $event['event_time']);
                                if(strtotime($times[0]) < strtotime("12:00PM")){
                                    continue;
                                }
                                if(!empty($times[1])){
                                    $times[1] = ' to '.$times[1];
                                }
                                $eventsCount++;
                                $start = mysql_fetch_array(mysql_query("SELECT address_address, address_city, address_bedrooms FROM fmo_locations_events_addresses WHERE address_event_token='".$event['event_token']."' AND address_type=1"));
                                $end   = mysql_fetch_array(mysql_query("SELECT address_address, address_city FROM fmo_locations_events_addresses WHERE address_event_token='".$event['event_token']."' AND address_type=2"));
                                ?>
                                <div class="todo-tasklist-item todo-tasklist-item-border-<?php echo $color; ?> col-md-12">
                                    <div class="todo-tasklist-item-title">
                                        <?php echo $event['event_name']; ?> <span class="font-<?php echo $color; ?>">|</span> <small><i class="fa fa-truck"></i> Trucks: <?php echo $event['event_truckfee']; ?> + <i class="fa fa-users"></i> Crew size: <?php echo $event['event_laborrate']; ?> <?php if(!empty($start['address_bedrooms'])){ ?> + <i class="fa fa-home"></i> <?php echo $start['address_bedrooms']; } ?></small> <span class="font-<?php echo $color; ?>">|</span>
                                        <?php
                                        if(!empty($event['event_type'])){
                                            ?>
                                            <span class="todo-tasklist-badge badge badge-roundless badge-danger"><?php echo $event['event_type']; ?></span>
                                            <?php
                                        }
                                        if(!empty($event['event_subtype'])){
                                            ?>
                                            <span class="todo-tasklist-badge badge badge-roundless badge-info"><?php echo $event['event_subtype']; ?></span>
                                            <?php
                                        }
                                        ?>
                                        <span class="badge badge-roundless <?php echo $badge; ?>"><?php echo $status; ?></span></div>
                                    <div class="todo-tasklist-item-text">
                                        <?php
                                        if($_SESSION['group'] >= 5.0 && $_SESSION['group'] != 3){
                                            ?>
                                            <strong>Be there to get ready by <?php echo date('g:i A', strtotime($times[0]." -45 minutes")); ?></strong>
                                            <?php
                                        } else {
                                            if(!empty($start['address_address']) && !empty($end['address_address'])){
                                                ?>
                                                <strong>Start:</strong> <?php echo $start['address_address'].", ".$start['address_city']; ?> <i class="fa fa-map"></i> <strong>End:</strong> <?php echo $end['address_address'].", ".$end['address_city']; ?>
                                                <?php
                                            } elseif(!empty($event['event_comments'])) {
                                                ?>
                                                <strong>Comments:</strong> <?php echo $event['event_comments']; ?>
                                                <?php
                                            } else {
                                                ?>
                                                <strong>Click to view more details & manage</strong>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                    <div class="todo-tasklist-controls pull-left">
                                    <span class="todo-tasklist-date"><i class="fa fa-calendar"></i>
                                        <?php
                                        if(date('M d, Y', strtotime($event['event_date_start'])) == date('M d, Y', strtotime($event['event_date_end']))) {
                                            echo date('M d, Y', strtotime($event['event_date_start']));
                                        } else {
                                            echo date('M d, Y', strtotime($event['event_date_start'])); ?> - <?php echo date('M d, Y', strtotime($event['event_date_end']));
                                        }
                                        ?>
                                        @ <?php echo $times[0].$times[1]; ?></span>
                                        <?php
                                        if($event['event_booking'] == 1){
                                            ?>
                                            <span class="todo-tasklist-badge badge badge-roundless badge-success"><i class="fa fa-check" style="margin-top: -6px"></i> Booking fee paid</span>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                            <span id="afternoonCount" class="hidden"><?php echo $eventsCount; ?></span>
                            <?php
                        } else {
                            ?>
                            <br/>
                            <div class="alert alert-warning alert-dismissable" style="margin-top: -20px;">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                                <strong>No events found</strong> for this afternoon at this location.
                                <span id="afternoonCount" class="hidden">0</span>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
            <script>
                $(document).ready(function(){
                    $('#afternoon').html(document.getElementById("afternoonCount").innerText);
                    $('#morning').html(document.getElementById("morningCount").innerText);

                    $('.activity_restrict').each(function(){
                        var type = $(this).attr('data-am-pm');
                        if(type == 'morning'){
                            $(this).bootstrapSwitch({
                                state: true
                            });
                        } if (type == 'afternoon') {
                            $(this).bootstrapSwitch({
                                state: true
                            });
                        }
                    }).on('switchChange.bootstrapSwitch', function(event, state) {
                        var type = $(this).attr('data-am-pm');
                        if(type == 'morning'){
                            $.ajax({
                                url: "assets/app/api/actions.php?yt=ck&luid=<?php echo $_GET['luid']; ?>type=" + type,
                                type: "POST",
                                data: {
                                    state: state
                                },
                                success: function (data) {
                                    7
                                    if (state == true) {
                                        toastr.info('<strong>Logan says</strong>:<br/>Morning bookings have been opened for this location on this date.');
                                    } else {
                                        toastr.error('<strong>Logan says</strong>:<br/>Morning bookings have been closed for this location on this date.');
                                    }

                                },
                                error: function () {
                                    toastr.error('<strong>Logan says</strong>:<br/>That page didnt respond correctly. Try again, or create a support ticket for help.');
                                }
                            });
                        } if(type == 'afternoon'){
                            $.ajax({
                                url: "assets/app/api/actions.php?yt=ck&luid=<?php echo $_GET['luid']; ?>&type=" + type,
                                type: "POST",
                                data: {
                                    state: state
                                },
                                success: function (data) {
                                    if (state == true) {
                                        toastr.warning('<strong>Logan says</strong>:<br/>Afternoon bookings have been opened for this location on this date.');
                                    } else {
                                        toastr.error('<strong>Logan says</strong>:<br/>Afternoon bookings have been closed for this location on this date.');
                                    }

                                },
                                error: function () {
                                    toastr.error('<strong>Logan says</strong>:<br/>That page didnt respond correctly. Try again, or create a support ticket for help.');
                                }
                            });
                        }
                    });
                });
            </script>
            <?php
        } else {
            $range    = explode(" - ", $_POST['range']);
            $location = mysql_fetch_array(mysql_query("SELECT location_morning_restrict, location_afternoon_restrict FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'"));

            $morning      = mysql_query("SELECT activity_notes FROM fmo_locations_activites WHERE (activity_type=1 AND activity_date='".mysql_real_escape_string(date('Y-m-d', strtotime($range[0])))."') AND activity_location_token='".mysql_real_escape_string($_GET['luid'])."'");
            $m_restricted = 'false';
            if(mysql_num_rows($morning) > 0){
                $activity = mysql_fetch_array($morning);
                if($activity['activity_notes'] == 1){
                    $m_restricted = 'true';
                } elseif($activity['activity_notes'] == 0){
                    $m_restricted = 'false';
                }
            } else {
                $m_restricted = 'false';
            }
            $afternoon    = mysql_query("SELECT activity_notes FROM fmo_locations_activities WHERE (activity_type=2 AND activity_date='".mysql_real_escape_string(date('Y-m-d', strtotime($range[0])))."') AND activity_location_token='".mysql_real_escape_string($_GET['luid'])."'");
            $a_restricted = 'false';
            if(mysql_num_rows($afternoon) > 0){
                $activity = mysql_fetch_array($afternoon);
                if($activity['activity_notes'] == 1){
                    $a_restricted = 'true';
                } elseif($activity['activity_notes'] == 0){
                    $a_restricted = 'false';
                }
            } else {
                $a_restricted = 'false';
            }
            ?>
            <div class="row" style="padding-left: 15px; padding-right: 15px; padding-top: 14px;">
                <div class="col-md-6 col-xs-6 col-sm-6 col-lg-6 <?php if($m_restricted == 'true'){echo "font-red alert-danger";} ?>" style="padding-top: 15px; margin-top: -30px;">
                    <?php
                    $events = mysql_query("SELECT event_name, event_date_start, event_date_end, event_time, event_token, event_status, event_type, event_subtype, event_booking, event_truckfee, event_laborrate FROM fmo_locations_events WHERE event_location_token='".mysql_real_escape_string($_GET['luid'])."' AND (event_date_end>='".mysql_real_escape_string($range[0])."' AND event_date_start<='".mysql_real_escape_string($range[1])."') AND NOT event_status=0 AND NOT event_status=9 ORDER BY event_time+0 ASC");
                    ?>
                    <div class="portlet">
                        <div class="portlet-title">
                            <h3 style="margin-top: 0px;">
                                Morning
                                <?php
                                if($m_restricted == 'true'){
                                    ?>
                                    <strong>&nbsp;[ Restricted ]</strong>
                                    <?php
                                } ?>
                                <small class="hidden-sm"><span class="text-danger">| <span id="morning"></span></span></small>
                                <?php
                                if($_SESSION['group'] <= 2){
                                    ?>
                                    <span class="pull-right hidden-xs hidden-sm"><small>Restrict:</small>
                                        <input type="checkbox" class="activity_restrict" data-am-pm="morning" data-date="<?php echo date('Y-m-d', strtotime($range[0])); ?>" checked data-size="small" data-on-color="success" data-on-text="YES" data-off-color="default" data-off-text="NO">
                                    </span>
                                    <?php
                                }
                                ?>
                            </h3>
                        </div>
                        <div class="portlet-body" style="min-height: 100%!important;">
                            <div class="todo-tasklist">
                                <?php
                                if(mysql_num_rows($events) > 0){
                                    $eventsCount = 0;
                                    while($event = mysql_fetch_assoc($events)){
                                        switch($event['event_status']){
                                            case 1: $status = "New Booking"; $color = "blue"; $badge = "badge-info"; break;
                                            case 2: $status = "Confirmed"; $color = "green"; $badge = "badge-success"; break;
                                            case 3: $status = "Left Message"; $color = "yellow"; $badge = "badge-warning"; break;
                                            case 4: $status = "On Hold"; $color = "yellow"; $badge = "badge-warning"; break;
                                            case 5: $status = "Canceled"; $color = "red"; $badge = "badge-danger"; break;
                                            case 6: $status = "Customer Confirmed"; $color = "purple"; $badge = "badge-purple"; break;
                                            case 8: $status = "Completed"; $color = "yellow"; $badge = "badge-warning"; break;
                                            case 9: $status = "Dead Hot Lead"; $color = "red"; $badge = "badge-danger"; break;
                                            default: $status = "On Hold"; $color = "yellow"; $badge = "badge-warning"; break;
                                        }
                                        $times = explode(" to ", $event['event_time']);
                                        if(strtotime($times[0]) >= strtotime("12:00PM")){
                                            continue;
                                        }
                                        if(!empty($times[1])){
                                            $times[1] = ' to '.$times[1];
                                        }
                                        $eventsCount++;
                                        $start = mysql_fetch_array(mysql_query("SELECT address_address, address_city, address_bedrooms FROM fmo_locations_events_addresses WHERE address_event_token='".$event['event_token']."' AND address_type=1"));
                                        $end   = mysql_fetch_array(mysql_query("SELECT address_address, address_city FROM fmo_locations_events_addresses WHERE address_event_token='".$event['event_token']."' AND address_type=2"));
                                        $assets = mysql_query("SELECT asset_name FROM fmo_locations_events_assets WHERE asset_event_token='".mysql_real_escape_string($event['event_token'])."'") or die(mysql_error());
                                        $mans   = mysql_query("SELECT laborer_user_token FROM fmo_locations_events_laborers WHERE laborer_event_token='".mysql_real_escape_string($event['event_token'])."' ORDER BY laborer_role ASC") or die(mysql_error());
                                        $eventAssets = array();
                                        if(mysql_num_rows($assets) > 0){
                                            while($asset = mysql_fetch_assoc($assets)){
                                                $eventAssets[$event['event_token']]['assets'][] = array(''.$asset['asset_name'].'');
                                            }
                                        }
                                        $eventMen = array();
                                        if(mysql_num_rows($mans) > 0){
                                            while($men = mysql_fetch_assoc($mans)){
                                                $name   = abbrName($men['laborer_user_token']);
                                                $eventMen[$event['event_token']]['men'][] = array(''.$name.'');
                                            }
                                        }
                                        ?>
                                        <div class="todo-tasklist-item todo-tasklist-item-border-<?php echo $color; if($_SESSION['group'] < 5.0 && $_SESSION['group'] != 3.0) { ?> popout<?php } ?> col-md-12" data-pop="event.php?ev=<?php echo $event['event_token']; ?>" data-page-title="<?php echo $event['event_name']; ?>">
                                            <div class="todo-tasklist-item-title">
                                                <span class="badge badge-roundless <?php echo $badge; ?>"><?php echo $status; ?></span> &nbsp;
                                                <?php echo $event['event_name']; ?>
                                                <span class="font-<?php echo $color; ?>">|</span>
                                                <small>
                                                    <i class="fa fa-truck"></i> <?php echo $event['event_truckfee']; ?>
                                                    <?php
                                                    if(mysql_num_rows($assets) > 0){
                                                        $ass = 0;
                                                        ?> ( <?php
                                                        foreach($eventAssets[$event['event_token']]['assets'] as $asset){
                                                            if($ass > 0){
                                                                echo ', ';
                                                            }
                                                            echo "<strong>".$asset[0]."</strong>";
                                                            $ass++;
                                                        }
                                                        ?>
                                                        ) <?php
                                                    }
                                                    ?>+
                                                    <i class="fa fa-users"></i> <?php echo $event['event_laborrate']; ?>
                                                    <?php
                                                    if(mysql_num_rows($mans) > 0){
                                                        $dude = 0;
                                                        ?> ( <?php
                                                        foreach($eventMen[$event['event_token']]['men'] as $man){
                                                            if($dude > 0){
                                                                echo ", ";
                                                            }
                                                            echo "<strong>".$man[0]."</strong>";
                                                            $dude++;
                                                        }
                                                        ?>
                                                        ) <?php
                                                    }
                                                    if(!empty($start['address_bedrooms'])){
                                                        ?> +
                                                        <i class="fa fa-home"></i>
                                                        <?php echo $start['address_bedrooms'];
                                                    }
                                                    ?>
                                                </small>

                                                <small>
                                                    <?php
                                                    if($_SESSION['group'] >= 5.0 && $_SESSION['group'] != 3){
                                                        ?>
                                                        <br/><strong>Be there to get ready by <?php echo date('g:i A', strtotime($times[0]." -45 minutes")); ?></strong>
                                                        <?php
                                                    } else {
                                                        if(!empty($start['address_address']) && !empty($end['address_address'])){
                                                            ?>
                                                            <br/><strong>Start:</strong> <?php echo $start['address_address'].", ".$start['address_city']; ?> <i class="fa fa-map"></i> <strong>End:</strong> <?php echo $end['address_address'].", ".$end['address_city']; ?>
                                                            <?php
                                                        } elseif(!empty($event['event_comments'])) {
                                                            ?>
                                                            <br/><strong>Comments:</strong> <?php echo $event['event_comments']; ?>
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <br/><strong>Click to view more details & manage</strong>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </small>
                                            </div>
                                            <div class="todo-tasklist-item-text">

                                            </div>
                                            <div class="todo-tasklist-controls pull-left">
                                                <?php
                                                if(!empty($event['event_type'])){
                                                    ?>
                                                    <span class="todo-tasklist-badge badge badge-roundless <?php echo $badge; ?>"><?php echo $event['event_type']; ?></span>
                                                    <?php
                                                }
                                                if(!empty($event['event_subtype'])){
                                                    ?>
                                                    <span class="todo-tasklist-badge badge badge-roundless badge-info"><?php echo $event['event_subtype']; ?></span>
                                                    <?php
                                                }
                                                if($event['event_booking'] == 1){
                                                    ?>
                                                    <span class="todo-tasklist-badge badge badge-roundless badge-success"><i class="fa fa-check"  style="margin-top: -6px"></i> Booking fee paid</span>
                                                    <?php
                                                }
                                                ?>
                                                <strong class="font-<?php echo $color; ?>" style="font-size: 15px;">|</strong>
                                                <span class="todo-tasklist-date"><i class="fa fa-calendar"></i>
                                                    <?php
                                                    if(date('M d, Y', strtotime($event['event_date_start'])) == date('M d, Y', strtotime($event['event_date_end']))) {
                                                        echo date('M dS, Y', strtotime($event['event_date_start']));
                                                    } else {
                                                        echo date('M dS, Y', strtotime($event['event_date_start'])); ?> - <?php echo date('M dS, Y', strtotime($event['event_date_end']));
                                                    }
                                                    ?>
                                                    @ <?php echo $times[0].$times[1]; ?></span>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                    <span id="morningCount" class="hidden"><?php echo number_format($eventsCount, 0); ?></span>
                                    <?php
                                } else {
                                    ?>
                                    <Br/>
                                    <div class="alert alert-warning alert-dismissable">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                                        <strong>No events found</strong> for this morning at this location.
                                        <span id="morningCount" class="hidden">0</span>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xs-6 col-sm-6 col-lg-6 <?php if($a_restricted == 'true'){echo "font-red alert-danger";} ?>" style="padding-top: 15px; margin-top: -30px;"">
                    <?php
                    $events = mysql_query("SELECT event_name, event_date_start, event_date_end, event_time, event_token, event_status, event_type, event_subtype, event_booking, event_truckfee, event_laborrate FROM fmo_locations_events WHERE event_location_token='".mysql_real_escape_string($_GET['luid'])."' AND (event_date_end>='".mysql_real_escape_string($range[0])."' AND event_date_start<='".mysql_real_escape_string($range[1])."') AND NOT event_status=0 AND NOT event_status=9 ORDER BY event_time+0 ASC");
                    ?>
                    <div class="portlet">
                        <div class="portlet-title">
                            <h3 style="margin-top: 0px">
                                Afternoon
                                <?php
                                if($a_restricted == 'true'){
                                    ?>
                                    <strong>&nbsp;[ Restricted ]</strong>
                                    <?php
                                } ?>
                                <small class="hidden-sm"><span class="text-danger">| <span id="afternoon"></span></span></small>
                                <?php
                                if($_SESSION['group'] <= 2){
                                    ?>
                                    <span class="pull-right hidden-xs hidden-sm"><small>Restrict:</small>
                                         <input type="checkbox" class="activity_restrict" data-am-pm="afternoon" data-date="<?php echo date('Y-m-d', strtotime($range[0])); ?>" checked data-size="small" data-on-color="success" data-on-text="YES" data-off-color="default" data-off-text="NO">
                                    </span>
                                    <?php
                                }
                                ?>
                            </h3>
                        </div>
                        <div class="portlet-body" style="min-height: 100%!important;">
                            <div class="todo-tasklist">
                                <?php
                                if(mysql_num_rows($events) > 0){
                                    $eventsCount = 0;
                                    while($event = mysql_fetch_assoc($events)){
                                        switch($event['event_status']){
                                            case 1: $status = "New Booking"; $color = "blue"; $badge = "badge-info"; break;
                                            case 2: $status = "Confirmed"; $color = "green"; $badge = "badge-success"; break;
                                            case 3: $status = "Left Message"; $color = "yellow"; $badge = "badge-warning"; break;
                                            case 4: $status = "On Hold"; $color = "yellow"; $badge = "badge-warning"; break;
                                            case 5: $status = "Canceled"; $color = "red"; $badge = "badge-danger"; break;
                                            case 6: $status = "Customer Confirmed"; $color = "purple"; $badge = "badge-purple"; break;
                                            case 8: $status = "Completed"; $color = "yellow"; $badge = "badge-warning"; break;
                                            case 9: $status = "Dead Hot Lead"; $color = "red"; $badge = "badge-danger"; break;
                                            default: $status = "On Hold"; $color = "yellow"; $badge = "badge-warning"; break;
                                        }
                                        $times = explode("to", $event['event_time']);
                                        if(strtotime($times[0]) < strtotime("12:00PM")){
                                            continue;
                                        }
                                        if(!empty($times[1])){
                                            $times[1] = ' to '.$times[1];
                                        }
                                        $eventsCount++;
                                        $start = mysql_fetch_array(mysql_query("SELECT address_address, address_city, address_bedrooms FROM fmo_locations_events_addresses WHERE address_event_token='".$event['event_token']."' AND address_type=1"));
                                        $end   = mysql_fetch_array(mysql_query("SELECT address_address, address_city FROM fmo_locations_events_addresses WHERE address_event_token='".$event['event_token']."' AND address_type=2"));
                                        $assets = mysql_query("SELECT asset_name FROM fmo_locations_events_assets WHERE asset_event_token='".mysql_real_escape_string($event['event_token'])."'") or die(mysql_error());
                                        $mans   = mysql_query("SELECT laborer_user_token FROM fmo_locations_events_laborers WHERE laborer_event_token='".mysql_real_escape_string($event['event_token'])."' ORDER BY laborer_role ASC") or die(mysql_error());
                                        $eventAssets = array();
                                        if(mysql_num_rows($assets) > 0){
                                            while($asset = mysql_fetch_assoc($assets)){
                                                $eventAssets[$event['event_token']]['assets'][] = array(''.$asset['asset_name'].'');
                                            }
                                        }
                                        $eventMen = array();
                                        if(mysql_num_rows($mans) > 0){
                                            while($men = mysql_fetch_assoc($mans)){
                                                $name   = abbrName($men['laborer_user_token']);
                                                $eventMen[$event['event_token']]['men'][] = array(''.$name.'');
                                            }
                                        }
                                        ?>
                                        <div class="todo-tasklist-item todo-tasklist-item-border-<?php echo $color; if($_SESSION['group'] < 5.0 && $_SESSION['group'] != 3.0) { ?> popout<?php } ?> col-md-12" data-pop="event.php?ev=<?php echo $event['event_token']; ?>" data-page-title="<?php echo $event['event_name']; ?>">
                                            <div class="todo-tasklist-item-title">
                                                <span class="badge badge-roundless <?php echo $badge; ?>"><?php echo $status; ?></span> &nbsp;
                                                <?php echo $event['event_name']; ?>
                                                <span class="font-<?php echo $color; ?>">|</span>
                                                <small>
                                                    <i class="fa fa-truck"></i> <?php echo $event['event_truckfee']; ?>
                                                    <?php
                                                    if(mysql_num_rows($assets) > 0){
                                                        $ass = 0;
                                                        ?> ( <?php
                                                        foreach($eventAssets[$event['event_token']]['assets'] as $asset){
                                                            if($ass > 0){
                                                                echo ', ';
                                                            }
                                                            echo "<strong>".$asset[0]."</strong>";
                                                            $ass++;
                                                        }
                                                        ?>
                                                        ) <?php
                                                    }
                                                    ?>+
                                                    <i class="fa fa-users"></i> <?php echo $event['event_laborrate']; ?>
                                                    <?php
                                                    if(mysql_num_rows($mans) > 0){
                                                        $dude = 0;
                                                        ?> ( <?php
                                                        foreach($eventMen[$event['event_token']]['men'] as $man){
                                                            if($dude > 0){
                                                                echo ", ";
                                                            }
                                                            echo "<strong>".$man[0]."</strong>";
                                                            $dude++;
                                                        }
                                                        ?>
                                                        ) <?php
                                                    }
                                                    if(!empty($start['address_bedrooms'])){
                                                        ?> +
                                                        <i class="fa fa-home"></i>
                                                        <?php echo $start['address_bedrooms'];
                                                    }
                                                    ?>
                                                </small>
                                                <small>
                                                    <?php
                                                    if($_SESSION['group'] >= 5.0 && $_SESSION['group'] != 3){
                                                        ?>
                                                        <br/><strong>Be there to get ready by <?php echo date('g:i A', strtotime($times[0]." -45 minutes")); ?></strong>
                                                        <?php
                                                    } else {
                                                        if(!empty($start['address_address']) && !empty($end['address_address'])){
                                                            ?>
                                                            <br/><strong>Start:</strong> <?php echo $start['address_address'].", ".$start['address_city']; ?> <i class="fa fa-map"></i> <strong>End:</strong> <?php echo $end['address_address'].", ".$end['address_city']; ?>
                                                            <?php
                                                        } elseif(!empty($event['event_comments'])) {
                                                            ?>
                                                            <br/><strong>Comments:</strong> <?php echo $event['event_comments']; ?>
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <br/><strong>Click to view more details & manage</strong>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </small>
                                            </div>
                                            <div class="todo-tasklist-item-text">

                                            </div>
                                            <div class="todo-tasklist-controls pull-left">
                                                                    <span class="todo-tasklist-date">

                                                                        <?php
                                                                        if(!empty($event['event_type'])){
                                                                            ?>
                                                                            <span class="todo-tasklist-badge badge badge-roundless <?php echo $badge; ?>"><?php echo $event['event_type']; ?></span>
                                                                            <?php
                                                                        }
                                                                        if(!empty($event['event_subtype'])){
                                                                            ?>
                                                                            <span class="todo-tasklist-badge badge badge-roundless badge-info"><?php echo $event['event_subtype']; ?></span>
                                                                            <?php
                                                                        }
                                                                        if($event['event_booking'] == 1){
                                                                            ?>
                                                                            <span class="todo-tasklist-badge badge badge-roundless badge-success"><i class="fa fa-check" style="margin-top: -6px; color: #fff!important;"></i> Booking fee paid</span>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                        <strong class="font-<?php echo $color; ?>" style="font-size: 15px;">|</strong>
                                                                        <i class="fa fa-calendar"></i>
                                                                        <?php
                                                                        if(date('M d, Y', strtotime($event['event_date_start'])) == date('M d, Y', strtotime($event['event_date_end']))) {
                                                                            echo date('M dS, Y', strtotime($event['event_date_start']));
                                                                        } else {
                                                                            echo date('M dS, Y', strtotime($event['event_date_start'])); ?> - <?php echo date('M dS, Y', strtotime($event['event_date_end']));
                                                                        }
                                                                        ?>
                                                                        @ <?php echo $times[0].$times[1]; ?>
                                                                    </span>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                    <span id="afternoonCount" class="hidden"><?php echo number_format($eventsCount, 0); ?></span>
                                    <?php
                                } else {
                                    ?>
                                    <br/>
                                    <div class="alert alert-warning alert-dismissable">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                                        <strong>No events found</strong> for this afternoon at this location.
                                        <span id="afternoonCount" class="hidden">0</span>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                $(document).ready(function(){
                    $('#afternoon').html(document.getElementById("afternoonCount").innerText);
                    $('#morning').html(document.getElementById("morningCount").innerText);

                    $('.activity_restrict').each(function(){
                        var type = $(this).attr('data-am-pm');
                        if(type == 'morning'){
                            $(this).bootstrapSwitch({
                                state: <?php echo $m_restricted; ?>
                            });
                        } if (type == 'afternoon') {
                            $(this).bootstrapSwitch({
                                state: <?php echo $a_restricted; ?>
                            });
                        }
                    }).on('switchChange.bootstrapSwitch', function(event, state) {
                        var type = $(this).attr('data-am-pm');
                        var date = $(this).attr('data-date');
                        $.ajax({
                            url: "assets/app/api/actions.php?yt=ck&luid=<?php echo $_GET['luid']; ?>&type=" + type,
                            type: "POST",
                            data: {
                                state: state,
                                date: date
                            },
                            success: function (data) {
                                if (state == false) {


                                    toastr.info('<strong>Logan says</strong>:<br/> bookings have been opened for this location on this date.');
                                } else {


                                    toastr.error('<strong>Logan says</strong>:<br/>Morning bookings have been closed for this location on this date.');
                                }
                            },
                            error: function () {
                                toastr.error('<strong>Logan says</strong>:<br/>That page didnt respond correctly. Try again, or create a support ticket for help.');
                            }
                        });
                    });
                });
            </script>
            <?php
        }
    } elseif($_GET['t'] == 'dash_mag'){
        ?>
        <div class="row">
            <div class="col-sm-12">
                <div id="mag" class="has-toolbar">
                </div>
            </div>
        </div>
        <script>
            $(document).ready(function() {

                var date = new Date(Date.parse("<?php echo date('Y-m-d'); ?>"));
                var d = date.getDate();
                var m = date.getMonth();
                var y = date.getFullYear();

                console.log(m + ' ' + y + ' ' + d);

                var h = {};

                $('#mag').fullCalendar('destroy'); // destroy the calendar
                $('#mag').fullCalendar({ //re-initialize the calendar
                    header: {
                        left:   'title',
                        center: '',
                        right:  'prev next'
                    },
                    defaultView: 'month', // change default view with available options from http://arshaw.com/fullcalendar/docs/views/Available_Views/
                    slotMinutes: 15,
                    events: function(start, end, timezone, callback) {
                        jQuery.ajax({
                            url: 'assets/app/api/calendar.php?trl=ext&luid=<?php echo $_GET['luid']; ?>',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                start: start.format(),
                                end: end.format()
                            },
                            success: function(doc) {
                                var events = [];
                                if(!!doc){
                                    $.map( doc.data, function( r ) {
                                        events.push({
                                            id: r.id,
                                            title: r.nm,
                                            start: new Date(Date.parse(r.st)),
                                            ev: r.ev,
                                            textColor: r.cl,
                                            color: r.bg
                                        });
                                    });
                                }
                                callback(events);
                            }
                        });
                    },
                    viewRender: function (view, element) {

                    },
                    eventAfterAllRender: function(view) {
                        $('.fc-left h2').append("<small class='bold font-xs text-center' style='margin-left: 20px!important;'> <i class='fa fa-stop' style=' color: #cb5a5e'></i> = Canceled | <i class='fa fa-stop' style='color: #337ab7'></i> = Out of State Move</small>");
                        console.log("something");
                    },
                    eventClick: function(event) {
                        Pace.track(function(){
                            $.ajax({
                                url: "assets/pages/event.php?ev=" + event.ev,
                                success: function(data) {
                                    $('#page_content').html(data);
                                    document.title = event.title+" - For Movers Only";
                                },
                                error: function() {
                                    toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                                }
                            });
                        });
                    }
                });


                $('#mag').fullCalendar('gotoDate', '<?php echo date('Y-m-d'); ?>');

            });
        </script>
        <?php
    } elseif($_GET['t'] == 'dash_notes'){
        $range = $_POST['range'];
        $daily_notes = mysql_query("SELECT activity_id, activity_notes, activity_by_user_token, activity_timestamp FROM fmo_locations_activites WHERE activity_date='".$range."' AND activity_location_token='".mysql_real_escape_string($_GET['luid'])."' ORDER BY activity_timestamp DESC");
        if(mysql_num_rows($daily_notes) > 0){
            while($note = mysql_fetch_assoc($daily_notes)){
                ?>
                <li>
                    <div class="col1">
                        <div class="cont" style="width: 100% !important;">
                            <div class="cont-col1">
                                <div class="label label-sm label-danger">
                                    <i class="fa fa-newspaper-o"></i>
                                </div>
                            </div>
                            <div class="cont-col2">
                                <div class="desc">
                                    <strong><?php echo $note['activity_notes']; ?></strong>
                                    <span class="text-muted pull-right">
                                        by <strong><?php echo name($note['activity_by_user_token']); ?></strong> -
                                                <a class="del_daily_note" data-delete="<?php echo $note['activity_id']; ?>">
                                                    <i class="fa fa-times"></i>
                                                </a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <?php
            }
        } else {
            ?>NOPE<?php
        }
    }
}