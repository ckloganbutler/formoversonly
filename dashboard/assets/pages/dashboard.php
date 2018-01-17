<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/3/2017
 * Time: 3:52 AM
 */
session_start();
include '../app/init.php';

if(isset($_SESSION['logged'])){
    if($_SESSION['group'] >= 5.0 && $_SESSION['group'] <= 5.5){
        mysql_query("UPDATE fmo_users SET user_last_location='".mysql_real_escape_string(basename(__FILE__, '.php')).".php?".$_SERVER['QUERY_STRING']."' WHERE user_token='".mysql_real_escape_string($_SESSION['uuid'])."'");
        $location = mysql_fetch_array(mysql_query("SELECT location_name, location_manager FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'"));
        ?>
        <div class="page-content">
            <h3 class="page-title">
                <strong><?php echo $location['location_name']; ?>'s Dashboard</strong>
            </h3>
            <div class="page-bar">
                <ul class="page-breadcrumb">
                    <li>
                        <i class="fa fa-home"></i>
                        <a class="load_page" data-href="assets/pages/dashboard.php?luid=<?php echo $_GET['luid']; ?>"><?php echo $location['location_name']; ?></a>
                        <i class="fa fa-angle-right"></i>
                    </li>
                    <li>
                        <a class="load_page" data-href="assets/pages/dashboard.php?luid=<?php echo $_GET['luid']; ?>">Dashboard</a>
                    </li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light">
                        <?php


                        $broadcast = getBroadcast($_SESSION['cuid']);
                        $after = "".date('Y-m-d', strtotime($broadcast['time']." + 2 days"))."";
                        $time  = "".date('Y-m-d')."";
                        if(!empty($broadcast['message']) && $time < $after){
                            ?>
                            <div class="portlet-title">
                                <div class="caption caption-md col-md-12">
                                    <marquee>
                                        <i class="fa fa-bullhorn"></i> Company Broadcast | <strong class="text-danger" style="font-size: 16px;">
                                            <em><?php echo $broadcast['message']; ?></em>
                                        </strong>
                                    </marquee>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                        <div class="portlet-body">
                            <div class="row">
                                <?php
                                if(!empty($location['location_manager'])){
                                    ?>
                                    <div class="col-md-12">
                                        <div class="clearfix">
                                            <ul class="media-list">
                                                <li class="media">
                                                    <a class="pull-left hidden-xs" href="javascript:;">
                                                        <img class="media-object" src="<?php echo picture($location['location_manager']); ?>" alt="64x64" data-src="holder.js/64x64" style="width: 160px; height: 160px;">
                                                    </a>
                                                    <div class="media-body">
                                                        <textarea style="height: 110px;" class="form-control txt-message" id="ttm_msg" placeholder="Write <?php echo name($location['location_manager']); ?> a message here.."></textarea> <br/>
                                                        <h4 class="media-heading pull-left" style="margin-top: -8px"><strong><?php echo name($location['location_manager']); ?></strong><br/> <small><?php echo phone($location['location_manager']); ?> </small></h4>
                                                        <button type="button" class="btn red pull-right ttm" style="margin-top: -7px; margin-left: 15px;">Send message</button>
                                                        <small class="pull-right" style="margin-top: -6px;"><span class="txt-countdown hidden-xs"></span> <br/> <a class="hidden-xs" data-toggle="modal" href="#recent_texts"><i class="fa fa-external-link fa-1x"></i> view recent messages</a></small>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <?php
                                } else {
                                    ?>
                                    <div class="col-md-6 text-center">
                                        <br/>
                                        <h3><i class="fa fa-2x fa-question text-danger"></i><br/> No manager has been assigned to this location.</h3>
                                    </div>
                                    <?php
                                }
                                ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light">
                        <div class="portlet-title tabbable-line">
                            <div class="caption caption-md">
                                <i class="fa fa-tags theme-font bold"></i>
                                <span class="caption-subject font-red bold uppercase">Your jobs</span>
                            </div>
                            <div class="actions">
                                <div class="btn-group">
                                    <a id="dashboard-report-range" class="pull-right tooltips btn red" data-container="body" data-placement="bottom" data-original-title="Change dashboard date range">
                                        <i class="icon-calendar"></i>&nbsp;<span>Events for:</span>
                                        <span class="bold uppercase">
                                        <?php echo date('m-d-Y'); ?>
                                    </span>&nbsp; <i class="fa fa-angle-down"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="portlet-body" id="dashboard_events">
                            <div class="row">
                                <div class="col-md-6 col-xs-6 col-sm-6 col-lg-6">
                                    <?php
                                    $events = mysql_query("SELECT event_name, event_date_start, event_date_end, event_time, event_token, event_status, event_type, event_subtype, event_booking, event_truckfee, event_laborrate FROM fmo_locations_events WHERE event_location_token='".mysql_real_escape_string($_GET['luid'])."' AND (event_date_start<='".date('Y-m-d')."' AND event_date_end>='".date('Y-m-d')."') AND NOT event_status=0 AND NOT event_status=9 ORDER BY event_time+0 ASC");
                                    ?>
                                    <h3 style="margin-top: 0px;">Morning <small class="hidden-sm"><span class="text-danger">| <span id="morning"></span></span></small> </h3>
                                    <hr/>
                                    <div class="todo-tasklist">
                                        <?php
                                        if(mysql_num_rows($events) > 0){
                                            $eventsCount = 0;
                                            while($event = mysql_fetch_assoc($events)){
                                                $labor = mysql_query("SELECT laborer_id FROM fmo_locations_events_laborers WHERE laborer_event_token='".mysql_real_escape_string($event['event_token'])."' AND laborer_user_token='".mysql_real_escape_string($_SESSION['uuid'])."'") or die(mysql_error());
                                                if(mysql_num_rows($labor) == 0){
                                                    continue;
                                                }
                                                switch($event['event_status']){
                                                    case 1: $status = "New Booking"; $color = "blue"; $badge = "badge-info"; break;
                                                    case 2: $status = "Confirmed"; $color = "green"; $badge = "badge-success"; break;
                                                    case 3: $status = "Left Message"; $color = "red"; $badge = "badge-danger"; break;
                                                    case 4: $status = "On Hold"; $color = "red"; $badge = "badge-danger"; break;
                                                    case 5: $status = "Cancelled"; $color = "red"; $badge = "badge-danger"; break;
                                                    case 6: $status = "Customer Confirmed"; $color = "purple"; $badge = "badge-purple"; break;
                                                    case 8: $status = "Completed"; $color = "yellow"; $badge = "badge-warning"; break;
                                                    case 9: $status = "Dead Hot Lead"; $color = "red"; $badge = "badge-danger"; break;
                                                    default: $status = "On Hold"; $color = "red"; $badge = "badge-danger"; break;
                                                }
                                                $times = explode(" to ", $event['event_time']);
                                                if(strtotime($times[0]) >= strtotime("12:00PM")){
                                                    continue;
                                                }
                                                if(!empty($times[1])){
                                                    $times[1] = ' to '.$times[1];
                                                }
                                                $eventsCount++;
                                                $start = mysql_fetch_array(mysql_query("SELECT address_address, address_city FROM fmo_locations_events_addresses WHERE address_event_token='".$event['event_token']."' AND address_type=1"));
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
                                                ?>
                                                <div class="todo-tasklist-item todo-tasklist-item-border-<?php echo $color; if($_SESSION['group'] < 5.0 && $_SESSION['group'] != 3.0) { ?>load_page<?php } ?>  col-md-12" data-href="assets/pages/event.php?ev=<?php echo $event['event_token']; ?>" data-page-title="<?php echo $event['event_name']; ?>">
                                                    <div class="todo-tasklist-item-title">
                                                        <?php echo $event['event_name']; ?>
                                                        <span class="font-<?php echo $color; ?>">|</span>
                                                        <small>
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
                                                        </small>
                                                    </div>
                                                    <div class="todo-tasklist-item-text">
                                                        <i class="fa fa-truck"></i> Trucks: <?php echo $event['event_truckfee']; ?>
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
                                                        <i class="fa fa-users"></i> Crew size: <?php echo $event['event_laborrate']; ?>
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
                                                        <strong class="font-<?php echo $color; ?>" style="font-size: 15px;">|</strong>
                                                        <span class="badge badge-roundless <?php echo $badge; ?>"><?php echo $status; ?></span>
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
                                <div class="col-md-6 col-xs-6 col-sm-6 col-lg-6">
                                    <?php
                                    $events = mysql_query("SELECT event_name, event_date_start, event_date_end, event_time, event_token, event_status, event_type, event_subtype, event_booking, event_truckfee, event_laborrate FROM fmo_locations_events WHERE event_location_token='".mysql_real_escape_string($_GET['luid'])."' AND (event_date_start<='".date('Y-m-d')."' AND event_date_end>='".date('Y-m-d')."') AND NOT event_status=0 AND NOT event_status=9 ORDER BY event_time+0 ASC");
                                    ?>
                                    <h3 style="margin-top: 0px">Afternoon <small class="hidden-sm"><span class="text-danger">| <span id="afternoon"></span></span></small></h3>
                                    <hr/>
                                    <div class="todo-tasklist">
                                        <?php
                                        if(mysql_num_rows($events) > 0){
                                            $eventsCount = 0;
                                            while($event = mysql_fetch_assoc($events)){
                                                $labor = mysql_query("SELECT laborer_id FROM fmo_locations_events_laborers WHERE laborer_event_token='".mysql_real_escape_string($event['event_token'])."' AND laborer_user_token='".mysql_real_escape_string($_SESSION['uuid'])."'") or die(mysql_error());
                                                if(mysql_num_rows($labor) == 0){
                                                    continue;
                                                }
                                                switch($event['event_status']){
                                                    case 1: $status = "New Booking"; $color = "blue"; $badge = "badge-info"; break;
                                                    case 2: $status = "Confirmed"; $color = "green"; $badge = "badge-success"; break;
                                                    case 3: $status = "Left Message"; $color = "red"; $badge = "badge-danger"; break;
                                                    case 4: $status = "On Hold"; $color = "red"; $badge = "badge-danger"; break;
                                                    case 5: $status = "Cancelled"; $color = "red"; $badge = "badge-danger"; break;
                                                    case 6: $status = "Customer Confirmed"; $color = "purple"; $badge = "badge-purple"; break;
                                                    case 8: $status = "Completed"; $color = "yellow"; $badge = "badge-warning"; break;
                                                    case 9: $status = "Dead Hot Lead"; $color = "red"; $badge = "badge-danger"; break;
                                                    default: $status = "On Hold"; $color = "red"; $badge = "badge-danger"; break;
                                                }
                                                $times = explode("to", $event['event_time']);
                                                if(strtotime($times[0]) < strtotime("12:00PM")){
                                                    continue;
                                                }
                                                if(!empty($times[1])){
                                                    $times[1] = ' to '.$times[1];
                                                }
                                                $eventsCount++;
                                                $start = mysql_fetch_array(mysql_query("SELECT address_address, address_city FROM fmo_locations_events_addresses WHERE address_event_token='".$event['event_token']."' AND address_type=1"));
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
                                                <div class="todo-tasklist-item todo-tasklist-item-border-<?php echo $color; if($_SESSION['group'] < 5.0 && $_SESSION['group'] != 3.0) { ?>load_page<?php } ?>  col-md-12" data-href="assets/pages/event.php?ev=<?php echo $event['event_token']; ?>" data-page-title="<?php echo $event['event_name']; ?>">
                                                    <div class="todo-tasklist-item-title">
                                                        <?php echo $event['event_name']; ?>
                                                        <span class="font-<?php echo $color; ?>">|</span>
                                                        <small>
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
                                                        </small>
                                                    </div>
                                                    <div class="todo-tasklist-item-text">
                                                        <i class="fa fa-truck"></i> Trucks: <?php echo $event['event_truckfee']; ?>
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
                                                        <i class="fa fa-users"></i> Crew size: <?php echo $event['event_laborrate']; ?>
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
                                                        <strong class="font-<?php echo $color; ?>" style="font-size: 15px;">|</strong>
                                                        <span class="badge badge-roundless <?php echo $badge; ?>"><?php echo $status; ?></span>
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
                </div>
            </div>
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function(){


                $('.scroller').slimScroll({
                    height: 300
                });

                function updateCountdown() {
                    var remaining = 160 - $('.txt-message').val().length;
                    $('.txt-countdown').text(remaining + ' characters remaining.');
                }
                updateCountdown();
                $('.txt-message').change(updateCountdown);
                $('.txt-message').keyup(updateCountdown);

                $('.ttm').click(function() {
                    var uuid = "<?php echo $location['location_manager']; ?>";
                    if($('#ttm_msg').val().length > 0){
                        $.ajax({
                            url: 'assets/app/texting.php?txt=ttm&uuid='+uuid+'&luid=<?php echo $_GET['luid']; ?>',
                            type: 'POST',
                            data: {
                                msg: $('#ttm_msg').val()
                            },
                            success: function(e){
                                toastr.success("<strong>Logan says:</strong><br/>Text message has been sent to <?php echo name($location['location_manager']); ?>");
                                $('#ttm_msg').val('');
                                updateCountdown();
                            },
                            error: function(e){
                                toastr.error("<strong>Logan says:</strong><br/>Something bad happened. You messed everything up. Just kidding, try that again.")
                            }
                        })
                    } else {
                        toastr.error("<strong>Logan says:</strong><br/>You need to type up a message first, silly! I can't send <strong><i>nothing</i></strong>!")
                    }
                });



                $('#dashboard-report-range').daterangepicker({
                        opens: (Metronic.isRTL() ? 'right' : 'left'),
                        startDate: "<?php echo date('Y-m-d') ?>",
                        endDate: "<?php echo date('Y-m-d'); ?>",
                        showDropdowns: false,
                        showWeekNumbers: true,
                        singleDatePicker: true,
                        timePicker: false,
                        timePickerIncrement: 1,
                        timePicker12Hour: true,
                        buttonClasses: ['btn btn-sm'],
                        applyClass: ' blue',
                        cancelClass: 'default',
                        format: 'YYYY-MM-DD',
                        separator: ' to ',
                        locale: {
                            applyLabel: 'Apply',
                            fromLabel: 'From',
                            toLabel: 'To',
                            customRangeLabel: 'Custom Range',
                            daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                            monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                            firstDay: 1
                        }
                    },
                    function (start, end) {
                        $('#dashboard-report-range span').html(start.format('MM-DD-YYYY'));
                        $('.mag').attr('data-month', start.format('YYYY-MM-DD'));
                        $.ajax({
                            url: 'assets/pages/sub/dashboard_master.php?t=dash_evs&luid=<?php echo $_GET['luid']; ?>',
                            type: 'POST',
                            data: {
                                range: ''+start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD')+''
                            },
                            success: function(events){
                                $('#dashboard_events').html(events);
                                toastr.success("<strong>Logan says:</strong><br/>I updated the lists for you below.");
                            },
                            error: function(error){
                                toastr.error("<strong>Logan says:</strong><br/>Something didn't work correctly there. Try again.");
                            }
                        })
                    }
                );

                $('.datatable').each(function(){
                    var url = $(this).attr('data-src');
                    $(this).dataTable({
                        "processing": true,
                        "serverSide": true,
                        "order": [[ 0, "asc" ]],
                        "bFilter" : false,
                        "bLengthChange": false,
                        "bPaginate": false,
                        "info": true,
                        "ajax": {
                            "url": url // ajax source
                        }
                    });
                });

                $('.show_form').on('click', function() {
                    var show = $(this).attr('data-show');

                    $(show).show();
                });

                $('.rateYoDash').rateYo({
                    halfStar: true,
                    readOnly: true
                });
                $('#dashboard-report-range').show();
                $('#afternoon').html(document.getElementById("afternoonCount").innerText);
                $('#morning').html(document.getElementById("morningCount").innerText);


            });
        </script>
        <?php
    } elseif($_SESSION['group'] == 3.0){
        mysql_query("UPDATE fmo_users SET user_last_location='".mysql_real_escape_string(basename(__FILE__, '.php')).".php?".$_SERVER['QUERY_STRING']."' WHERE user_token='".mysql_real_escape_string($_SESSION['uuid'])."'");
        $location = mysql_fetch_array(mysql_query("SELECT location_name, location_manager FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'"));
        ?>
        <div class="page-content">
            <h3 class="page-title">
                <strong><?php echo $location['location_name']; ?>'s Dashboard</strong>
            </h3>
            <div class="page-bar">
                <ul class="page-breadcrumb">
                    <li>
                        <i class="fa fa-home"></i>
                        <a class="load_page" data-href="assets/pages/dashboard.php?luid=<?php echo $_GET['luid']; ?>"><?php echo $location['location_name']; ?></a>
                        <i class="fa fa-angle-right"></i>
                    </li>
                    <li>
                        <a class="load_page" data-href="assets/pages/dashboard.php?luid=<?php echo $_GET['luid']; ?>">Dashboard</a>
                    </li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light">
                        <div class="portlet-body">
                            <div class="row">
                                <?php
                                if(!empty($location['location_manager'])){
                                    ?>
                                    <div class="col-md-12">
                                        <div class="clearfix">
                                            <ul class="media-list">
                                                <li class="media">
                                                    <a class="pull-left hidden-xs" href="javascript:;">
                                                        <img class="media-object" src="<?php echo picture($location['location_manager']); ?>" alt="64x64" data-src="holder.js/64x64" style="width: 160px; height: 160px;">
                                                    </a>
                                                    <div class="media-body">
                                                        <textarea style="height: 110px;" class="form-control txt-message" id="ttm_msg" placeholder="Write <?php echo name($location['location_manager']); ?> a message here.."></textarea> <br/>
                                                        <h4 class="media-heading pull-left" style="margin-top: -8px"><strong><?php echo name($location['location_manager']); ?></strong><br/> <small><?php echo phone($location['location_manager']); ?> </small></h4>
                                                        <button type="button" class="btn red pull-right ttm" style="margin-top: -7px; margin-left: 15px;">Send message</button>
                                                        <small class="pull-right" style="margin-top: -6px;"><span class="txt-countdown hidden-xs"></span> <br/> <a class="hidden-xs" data-toggle="modal" href="#recent_texts"><i class="fa fa-external-link fa-1x"></i> view recent messages</a></small>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <?php
                                } else {
                                    ?>
                                    <div class="col-md-6 text-center">
                                        <br/>
                                        <h3><i class="fa fa-2x fa-question text-danger"></i><br/> No manager has been assigned to this location.</h3>
                                    </div>
                                    <?php
                                }
                                ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light">
                        <div class="portlet-title tabbable-line">
                            <div class="caption caption-md">
                                <i class="fa fa-tags theme-font bold"></i>
                                <span class="caption-subject font-red bold uppercase">Jobs booked with us</span>
                            </div>
                            <div class="actions">
                                <div class="btn-group">
                                    <a id="dashboard-report-range" class="pull-right tooltips btn red" data-container="body" data-placement="bottom" data-original-title="Change dashboard date range">
                                        <i class="icon-calendar"></i>&nbsp;<span>Events for:</span>
                                        <span class="bold uppercase">
                                        <?php echo date('m-d-Y'); ?>
                                    </span>&nbsp; <i class="fa fa-angle-down"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="portlet-body" id="dashboard_events">
                            <div class="row">
                                <div class="col-md-6 col-xs-6 col-sm-6 col-lg-6">
                                    <?php
                                    $events = mysql_query("SELECT event_name, event_date_start, event_date_end, event_time, event_token, event_status, event_type, event_subtype, event_booking, event_truckfee, event_laborrate FROM fmo_locations_events WHERE event_user_token='".mysql_real_escape_string($_SESSION['uuid'])."' AND (event_date_start<='".date('Y-m-d')."' AND event_date_end>='".date('Y-m-d')."') AND NOT event_status=0 AND NOT event_status=9 ORDER BY event_time+0 ASC");
                                    ?>
                                    <h3 style="margin-top: 0px;">Morning <small class="hidden-sm"><span class="text-danger">| <span id="morning"></span></span></small> </h3>
                                    <hr/>
                                    <div class="todo-tasklist">
                                        <?php
                                        if(mysql_num_rows($events) > 0){
                                            $eventsCount = 0;
                                            while($event = mysql_fetch_assoc($events)){
                                                switch($event['event_status']){
                                                    case 1: $status = "New Booking"; $color = "blue"; $badge = "badge-info"; break;
                                                    case 2: $status = "Confirmed"; $color = "green"; $badge = "badge-success"; break;
                                                    case 3: $status = "Left Message"; $color = "red"; $badge = "badge-danger"; break;
                                                    case 4: $status = "On Hold"; $color = "red"; $badge = "badge-danger"; break;
                                                    case 5: $status = "Cancelled"; $color = "red"; $badge = "badge-danger"; break;
                                                    case 6: $status = "Customer Confirmed"; $color = "purple"; $badge = "badge-purple"; break;
                                                    case 8: $status = "Completed"; $color = "yellow"; $badge = "badge-warning"; break;
                                                    case 9: $status = "Dead Hot Lead"; $color = "red"; $badge = "badge-danger"; break;
                                                    default: $status = "On Hold"; $color = "red"; $badge = "badge-danger"; break;
                                                }
                                                $times = explode(" to ", $event['event_time']);
                                                if(strtotime($times[0]) >= strtotime("12:00PM")){
                                                    continue;
                                                }
                                                if(!empty($times[1])){
                                                    $times[1] = ' to '.$times[1];
                                                }
                                                $eventsCount++;
                                                $start = mysql_fetch_array(mysql_query("SELECT address_address, address_city FROM fmo_locations_events_addresses WHERE address_event_token='".$event['event_token']."' AND address_type=1"));
                                                $end   = mysql_fetch_array(mysql_query("SELECT address_address, address_city FROM fmo_locations_events_addresses WHERE address_event_token='".$event['event_token']."' AND address_type=2"));
                                                ?>
                                                <div class="todo-tasklist-item todo-tasklist-item-border-<?php echo $color; ?> col-md-12">
                                                    <div class="todo-tasklist-item-title">
                                                        <?php echo $event['event_name']; ?> <span class="font-<?php echo $color; ?>">|</span> <small><i class="fa fa-truck"></i> <span class="hidden-xs">Trucks</span>: <?php echo $event['event_truckfee']; ?> + <i class="fa fa-users"></i> <span class="hidden-xs">Crew size</span>: <?php echo $event['event_laborrate']; ?></small> <span class="font-<?php echo $color; ?> hidden-xs">|</span> <span class="badge badge-roundless <?php echo $badge; ?>"><?php echo $status; ?></span>
                                                    </div>
                                                    <div class="todo-tasklist-item-text">
                                                        <?php
                                                        if($_SESSION['group'] >= 5.0 && $_SESSION['group'] != 3){
                                                            ?>
                                                            <strong>Be there to get ready by <?php echo date('g:i A', strtotime($times[0]." -45 minutes")); ?></strong> <?php echo $labors['laborer_id']; ?>
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
                                                        <span class="todo-tasklist-date"><i class="fa fa-calendar"></i> <strong><?php echo date('d M Y', strtotime($event['event_date_start'])); ?></strong> - <?php echo date('d M Y', strtotime($event['event_date_end'])); ?> @ <strong><?php echo $times[0]."</strong>".$times[1]; ?></span>
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
                                <div class="col-md-6 col-xs-6 col-sm-6 col-lg-6">
                                    <?php
                                    $events = mysql_query("SELECT event_name, event_date_start, event_date_end, event_time, event_token, event_status, event_type, event_subtype, event_booking, event_truckfee, event_laborrate FROM fmo_locations_events WHERE event_user_token='".mysql_real_escape_string($_SESSION['uuid'])."' AND (event_date_start<='".date('Y-m-d')."' AND event_date_end>='".date('Y-m-d')."') AND NOT event_status=0 AND NOT event_status=9 ORDER BY event_time+0 ASC");
                                    ?>
                                    <h3 style="margin-top: 0px">Afternoon <small class="hidden-sm"><span class="text-danger">| <span id="afternoon"></span></span></small></h3>
                                    <hr/>
                                    <div class="todo-tasklist">
                                        <?php
                                        if(mysql_num_rows($events) > 0){
                                            $eventsCount = 0;
                                            while($event = mysql_fetch_assoc($events)){
                                                switch($event['event_status']){
                                                    case 1: $status = "New Booking"; $color = "blue"; $badge = "badge-info"; break;
                                                    case 2: $status = "Confirmed"; $color = "green"; $badge = "badge-success"; break;
                                                    case 3: $status = "Left Message"; $color = "red"; $badge = "badge-danger"; break;
                                                    case 4: $status = "On Hold"; $color = "red"; $badge = "badge-danger"; break;
                                                    case 5: $status = "Cancelled"; $color = "red"; $badge = "badge-danger"; break;
                                                    case 6: $status = "Customer Confirmed"; $color = "purple"; $badge = "badge-purple"; break;
                                                    case 8: $status = "Completed"; $color = "yellow"; $badge = "badge-warning"; break;
                                                    case 9: $status = "Dead Hot Lead"; $color = "red"; $badge = "badge-danger"; break;
                                                    default: $status = "On Hold"; $color = "red"; $badge = "badge-danger"; break;
                                                }
                                                $times = explode("to", $event['event_time']);
                                                if(strtotime($times[0]) <= strtotime("12:00PM")){
                                                    continue;
                                                }
                                                if(!empty($times[1])){
                                                    $times[1] = ' to '.$times[1];
                                                }
                                                $eventsCount++;
                                                $start = mysql_fetch_array(mysql_query("SELECT address_address, address_city FROM fmo_locations_events_addresses WHERE address_event_token='".$event['event_token']."' AND address_type=1"));
                                                $end   = mysql_fetch_array(mysql_query("SELECT address_address, address_city FROM fmo_locations_events_addresses WHERE address_event_token='".$event['event_token']."' AND address_type=2"));
                                                ?>
                                                <div class="todo-tasklist-item todo-tasklist-item-border-<?php echo $color; ?> col-md-12">
                                                    <div class="todo-tasklist-item-title">
                                                        <?php echo $event['event_name']; ?> <span class="font-<?php echo $color; ?>">|</span> <small><i class="fa fa-truck"></i> <span class="hidden-xs">Trucks</span>: <?php echo $event['event_truckfee']; ?> + <i class="fa fa-users"></i> <span class="hidden-xs">Crew size</span>: <?php echo $event['event_laborrate']; ?></small> <span class="font-<?php echo $color; ?> hidden-xs">|</span> <span class="badge badge-roundless <?php echo $badge; ?>"><?php echo $status; ?></span>
                                                    </div>
                                                    <div class="todo-tasklist-item-text">
                                                        <?php
                                                        if($_SESSION['group'] >= 5.0 && $_SESSION['group'] != 3){
                                                            ?>
                                                            <strong>Be there to get ready by <?php echo date('g:i A', strtotime($times[0]." -45 minutes")); ?></strong> <?php echo $labors['laborer_id']; ?>
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
                                                        <span class="todo-tasklist-date"><i class="fa fa-calendar"></i> <strong><?php echo date('d M Y', strtotime($event['event_date_start'])); ?></strong> - <?php echo date('d M Y', strtotime($event['event_date_end'])); ?> @ <strong><?php echo $times[0]."</strong>".$times[1]; ?></span>
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
                </div>
            </div>
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function(){

                function updateCountdown() {
                    var remaining = 160 - $('.txt-message').val().length;
                    $('.txt-countdown').text(remaining + ' characters remaining.');
                }
                updateCountdown();
                $('.txt-message').change(updateCountdown);
                $('.txt-message').keyup(updateCountdown);

                $('.ttm').click(function() {
                    var uuid = "<?php echo $location['location_manager']; ?>";
                    if($('#ttm_msg').val().length > 0){
                        $.ajax({
                            url: 'assets/app/texting.php?txt=ttm&uuid='+uuid+'&luid=<?php echo $_GET['luid']; ?>',
                            type: 'POST',
                            data: {
                                msg: $('#ttm_msg').val()
                            },
                            success: function(e){
                                toastr.success("<strong>Logan says:</strong><br/>Text message has been sent to <?php echo name($location['location_manager']); ?>");
                                $('#ttm_msg').val('');
                                updateCountdown();
                            },
                            error: function(e){
                                toastr.error("<strong>Logan says:</strong><br/>Something bad happened. You messed everything up. Just kidding, try that again.")
                            }
                        })
                    } else {
                        toastr.error("<strong>Logan says:</strong><br/>You need to type up a message first, silly! I can't send <strong><i>nothing</i></strong>!")
                    }
                });



                $('#dashboard-report-range').daterangepicker({
                        opens: (Metronic.isRTL() ? 'right' : 'left'),
                        startDate: "<?php echo date('Y-m-d') ?>",
                        endDate: "<?php echo date('Y-m-d'); ?>",
                        showDropdowns: false,
                        showWeekNumbers: true,
                        singleDatePicker: true,
                        timePicker: false,
                        timePickerIncrement: 1,
                        timePicker12Hour: true,
                        buttonClasses: ['btn btn-sm'],
                        applyClass: ' blue',
                        cancelClass: 'default',
                        format: 'YYYY-MM-DD',
                        separator: ' to ',
                        locale: {
                            applyLabel: 'Apply',
                            fromLabel: 'From',
                            toLabel: 'To',
                            customRangeLabel: 'Custom Range',
                            daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                            monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                            firstDay: 1
                        }
                    },
                    function (start, end) {
                        $('#dashboard-report-range span').html(start.format('MM-DD-YYYY'));
                        $('.mag').attr('data-month', start.format('YYYY-MM-DD'));
                        $.ajax({
                            url: 'assets/pages/sub/dashboard_master.php?t=dash_evs&luid=<?php echo $_GET['luid']; ?>',
                            type: 'POST',
                            data: {
                                range: ''+start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD')+''
                            },
                            success: function(events){
                                $('#dashboard_events').html(events);
                                toastr.success("<strong>Logan says:</strong><br/>I updated the lists for you below.");
                            },
                            error: function(error){
                                toastr.error("<strong>Logan says:</strong><br/>Something didn't work correctly there. Try again.");
                            }
                        })
                    }
                );

                $('#dashboard-report-range').show();
                $('#afternoon').html(document.getElementById("afternoonCount").innerText);
                $('#morning').html(document.getElementById("morningCount").innerText);
            });
        </script>
        <?php
    } else {
        mysql_query("UPDATE fmo_users SET user_last_location='".mysql_real_escape_string(basename(__FILE__, '.php')).".php?".$_SERVER['QUERY_STRING']."' WHERE user_token='".mysql_real_escape_string($_SESSION['uuid'])."'");
        $location = mysql_fetch_array(mysql_query("SELECT location_name, location_manager, location_morning_restrict, location_afternoon_restrict FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'"));
        $uuidperm = mysql_fetch_array(mysql_query("SELECT user_esc_permissions, user_permissions, user_last_ext_date FROM fmo_users WHERE user_token='".mysql_real_escape_string($_SESSION['uuid'])."'"));

        $perms = explode(',', $user['user_permissions']);

        if($uuidperm['user_last_ext_date'] == '0000-00-00'){$uuidperm['user_last_ext_date'] = date('Y-m-d');}

        $morning      = mysql_query("SELECT activity_notes FROM fmo_locations_activites WHERE (activity_type=1 AND activity_date='".mysql_real_escape_string(date('Y-m-d', strtotime($uuidperm['user_last_ext_date'])))."') AND activity_location_token='".mysql_real_escape_string($_GET['luid'])."'");
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
        $afternoon    = mysql_query("SELECT activity_notes FROM fmo_locations_activites WHERE (activity_type=2 AND activity_date='".mysql_real_escape_string(date('Y-m-d', strtotime($uuidperm['user_last_ext_date'])))."') AND activity_location_token='".mysql_real_escape_string($_GET['luid'])."'");
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


        if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_dashboard") !== false){
            ?>
            <div class="page-content">
                <h3 class="page-title">
                    <?php
                    $assets = mysql_num_rows(mysql_query("SELECT asset_id FROM fmo_locations_assets WHERE asset_location_token='".$_GET['luid']."' AND asset_desc LIKE '%MT%' ORDER BY asset_desc ASC"));
                    ?>
                    <strong><?php echo $location['location_name']; ?>'s Dashboard <span class="pull-right bold text-muted"><i class="fa fa-truck" style="font-size: 30px;"></i> <?php echo $assets; ?></span></strong>
                </h3>
                <div class="page-bar hidden-sm hidden-xs">
                    <ul class="page-breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a class="load_page" data-href="assets/pages/dashboard.php?luid=<?php echo $_GET['luid']; ?>"><?php echo $location['location_name']; ?></a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a class="load_page refresh" data-href="assets/pages/dashboard.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Dashboard">Dashboard</a>
                        </li>
                    </ul>
                    <div class="page-toolbar">
                        <?php
                        $ratings = 0; $rating_avg = 0; $rating_amt = 0;
                        $reviews = mysql_query("SELECT review_rating FROM fmo_locations_events_reviews WHERE review_location_token='".$_GET['luid']."'");
                        if(mysql_num_rows($reviews) > 0) {
                            while ($review = mysql_fetch_assoc($reviews)) {
                                $ratings += $review['review_rating'];
                                $rating_amt++;
                            }
                            $rating_avg = $ratings / $rating_amt;
                        }
                        ?>
                        <div class="pull-right btn red btn-fit-height hidden-xs"><strong><?php echo number_format($rating_avg, 1); ?></strong> Average Rating (from <strong><?php echo $rating_amt; ?></strong> reviews in the last <strong>365</strong> days)</div>
                        <div class="pull-right hidden-xs" data-toggle="modal" href="#avg_rating">
                            <div class="rateYoDash" data-rateyo-rating="<?php echo number_format($rating_avg, 1); ?>"></div>
                        </div>
                    </div>
                </div>
                <div class="row hidden-xs">
                    <div class="col-md-12">
                        <div class="portlet light">
                            <?php


                            $broadcast = getBroadcast($_SESSION['cuid']);
                            $after = "".date('Y-m-d', strtotime($broadcast['time']." + 2 days"))."";
                            $time  = "".date('Y-m-d')."";
                            if(!empty($broadcast['message']) && $time < $after){
                                ?>
                                <div class="portlet-title">
                                    <div class="caption caption-md col-md-12">
                                        <marquee>
                                            <i class="fa fa-bullhorn"></i> Company Broadcast | <strong class="text-danger" style="font-size: 16px;">
                                                <em><?php echo $broadcast['message']; ?></em>
                                            </strong>
                                        </marquee>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                            <div class="portlet-body">
                                <div class="row">
                                    <div class="col-md-6" id="dah_notes">
                                        <div class="scrollerz">
                                            <h4 style="margin-top: 0px;">
                                                <strong>Notes</strong> for <strong id="notes_dtd"><?php echo date('m-d-Y', strtotime($uuidperm['user_last_ext_date'])); ?></strong></h4>
                                            <hr>
                                            <ul class="feeds" id="those_notes">
                                                <?php
                                                $daily_notes = mysql_query("SELECT activity_id, activity_notes, activity_by_user_token, activity_timestamp FROM fmo_locations_activites WHERE (activity_type<=0 AND activity_date='".date('Y-m-d', strtotime($uuidperm['user_last_ext_date']))."') AND activity_location_token='".mysql_real_escape_string($_GET['luid'])."' ORDER BY activity_timestamp DESC");
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
                                                    ?>
                                                    <li class="alert alert-warning">
                                                        <strong>No notes.</strong> Notes appear here after you've saved them.
                                                    </li>
                                                    <?php
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6" id="dah_graph">
                                        <div id="stats" class="chart" style="height: 170px;">
                                        </div>
                                    </div>
                                    <?php
                                    if(!empty($location['location_manager'])){
                                        ?>
                                        <div class="col-md-6">
                                            <div class="clearfix">
                                                <ul class="media-list">
                                                    <li class="media">
                                                        <a class="pull-left" href="javascript:;">
                                                            <img class="media-object" src="<?php echo picture($location['location_manager']); ?>" alt="64x64" data-src="holder.js/64x64" style="width: 160px; height: 160px;">
                                                        </a>
                                                        <div class="media-body">
                                                            <textarea style="height: 110px;" class="form-control txt-message" id="ttm_msg" placeholder="Write <?php echo name($location['location_manager']); ?> a message here.."></textarea> <br/>
                                                            <h4 class="media-heading pull-left" style="margin-top: -8px"><strong><?php echo name($location['location_manager']); ?></strong><br/> <small><?php echo phone($location['location_manager']); ?> </small></h4>
                                                            <button type="button" class="btn red pull-right ttm" style="margin-top: -7px; margin-left: 15px;">Send message</button>
                                                            <small class="pull-right" style="margin-top: -6px;"><span class="txt-countdown"></span> <br/> <a data-toggle="modal" href="#recent_texts"><i class="fa fa-external-link fa-1x"></i> view recent messages</a></small>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <?php
                                    } else {
                                        ?>
                                        <div class="col-md-6 text-center">
                                            <br/>
                                            <h3><i class="fa fa-2x fa-question text-danger"></i><br/> No manager has been assigned to this location.</h3>
                                        </div>
                                        <?php
                                    }
                                    ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet light">
                            <div class="portlet-title tabbable-line">
                                <div class="actions">
                                    <div class="btn-group-justified">
                                        <div class="btn-group">
                                            <a id="dashboard-report-range" class="pull-right tooltips btn red" data-container="body" data-placement="bottom" data-original-title="Change dashboard date range">
                                                <i class="icon-calendar"></i>&nbsp;<strong class="hidden-xs">Events for:</strong>
                                                <span class="bold uppercase">
                                            <?php echo date('m-d-Y', strtotime($uuidperm['user_last_ext_date'])); ?>
                                            </span>&nbsp; <i class="fa fa-angle-down"></i>
                                            </a>
                                        </div>
                                        <div class="btn-group">
                                            <button class="btn red-stripe add_daily_note pull-right" data-toggle="modal" href="#add_daily_note" data-date="<?php echo date('Y-m-d', strtotime($uuidperm['user_last_ext_date'])); ?>" data-date2="<?php echo date('m/d/Y', strtotime($uuidperm['user_last_ext_date'])); ?>"><i class="fa fa-plus-square-o"></i> Add note <span class="hidden-sm hidden-xs">for <strong id="note_date"><?php echo date('m-d-Y', strtotime($uuidperm['user_last_ext_date'])); ?></strong></span></button>
                                        </div>
                                        <div class="btn-group">
                                            <button class="btn red-stripe print hidden-sm hidden-xs" data-print="#dashboard_events"><i class="fa fa-print"></i> Print</button>
                                        </div>
                                        <div class="btn-group hidden-sm hidden-xs">
                                            <button class="btn red mag" data-month="<?php echo date('m-d-Y', strtotime($uuidperm['user_last_ext_date'])); ?>"><i class="fa fa-th"></i> Month at a Glance</button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="portlet-body" id="dashboard_events">
                                <div class="row" style="padding-left: 15px; padding-right: 15px; padding-top: 14px;">
                                    <div class="col-md-6 col-xs-6 col-sm-6 col-lg-6 <?php if($m_restricted == 'true'){echo "font-red alert-danger";} ?>" style="padding-top: 15px; margin-top: -30px;">
                                        <?php
                                        $events = mysql_query("SELECT event_name, event_date_start, event_date_end, event_time, event_token, event_status, event_type, event_subtype, event_booking, event_truckfee, event_laborrate FROM fmo_locations_events WHERE event_location_token='".mysql_real_escape_string($_GET['luid'])."' AND (event_date_start<='".date('Y-m-d', strtotime($uuidperm['user_last_ext_date']))."' AND event_date_end>='".date('Y-m-d', strtotime($uuidperm['user_last_ext_date']))."') AND NOT event_status=0 AND NOT event_status=9 ORDER BY event_time+0 ASC");
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
                                                            <input type="checkbox" class="activity_restrict" data-am-pm="morning" data-date="<?php echo date('Y-m-d', strtotime($uuidperm['user_last_ext_date'])); ?>" checked data-size="small" data-on-color="success" data-on-text="YES" data-off-color="default" data-off-text="NO">
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
                                    <div class="col-md-6 col-xs-6 col-sm-6 col-lg-6 <?php if($a_restricted == 'true'){echo "font-red alert-danger";} ?>" style="padding-top: 15px; margin-top: -30px;">
                                        <?php
                                        $events = mysql_query("SELECT event_name, event_date_start, event_date_end, event_time, event_token, event_status, event_type, event_subtype, event_booking, event_truckfee, event_laborrate FROM fmo_locations_events WHERE event_location_token='".mysql_real_escape_string($_GET['luid'])."' AND (event_date_start<='".date('Y-m-d', strtotime($uuidperm['user_last_ext_date']))."' AND event_date_end>='".date('Y-m-d', strtotime($uuidperm['user_last_ext_date']))."') AND NOT event_status=0 AND NOT event_status=9 ORDER BY event_time+0 ASC");
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
                                                             <input type="checkbox" class="activity_restrict" data-am-pm="afternoon" data-date="<?php echo date('Y-m-d', strtotime($uuidperm['user_last_ext_date'])); ?>" checked data-size="small" data-on-color="success" data-on-text="YES" data-off-color="default" data-off-text="NO">
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
                                                                            <span class="todo-tasklist-badge badge badge-roundless badge-success"><i class="fa fa-check"  style="margin-top: -6px"></i> Booking fee paid</span>
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
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="portlet light">
                            <div class="portlet-title tabbable-line">
                                <div class="caption">
                                    <i class="icon-globe theme-font bold"></i>
                                    <span class="caption-subject font-red bold uppercase">Activity</span>
                                </div>
                                <ul class="nav nav-tabs">
                                    <li class="active">
                                        <a href="#l_a" data-toggle="tab" aria-expanded="true">
                                            Hot Leads </a>
                                    </li>
                                    <?php
                                    if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_dashboard_customer_reviews") !== false){
                                        ?>
                                        <li class="">
                                            <a href="#c_r" data-toggle="tab" aria-expanded="false">
                                                Customer Reviews </a>
                                        </li>
                                        <?php
                                    }
                                    ?>

                                    <?php
                                    if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_dashboard_manager_tools")){
                                        ?>
                                        <li class="">
                                            <a href="#m_t" data-toggle="tab" aria-expanded="false">
                                                Manager Tools </a>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </div>
                            <div class="portlet-body">
                                <!--BEGIN TABS-->
                                <div class="tab-content">
                                    <div class="tab-pane active" id="l_a">
                                        <div class="scroller" style="height: 339px;" data-always-visible="1" data-rail-visible="0">
                                            <ul class="feeds">
                                                <?php
                                                $leads = mysql_query("SELECT event_name, event_token, event_user_token, event_status, event_type, event_subtype, event_phone, event_comments, event_date_touch, event_leadtype FROM fmo_locations_events WHERE event_location_token='".mysql_real_escape_string($_GET['luid'])."' AND event_status<=1 ORDER BY event_date_touch ASC");

                                                if(mysql_num_rows($leads) > 0){
                                                    $eventsCount = 0;
                                                    while($lead = mysql_fetch_assoc($leads)){
                                                        switch($lead['event_leadtype']){
                                                            default: $led = 'HOT LEAD'; $icon = '<i class="fa fa-fire"></i>'; $label = 'label-danger'; $color = 'text-danger'; $sv = 1; break;
                                                            case  2: $led = 'PURCHASED HOT LEAD'; $icon = '<i class="fa fa-dollar"></i>'; $label = 'label-success'; $color = 'text-success'; $sv = 1; break;
                                                            case  1: $led = 'WEB HOT LEAD'; $icon = '<i class="fa fa-desktop"></i>'; $label = 'label-info'; $color = 'text-info'; $sv = 1; break;
                                                            case  0: $led = 'PHONE HOT LEAD'; $icon = '<i class="fa fa-phone"></i>'; $label = 'label-warning'; $color = 'text-warning'; $sv = 1; break;
                                                        }
                                                        if($lead['event_status'] == 1){
                                                            $ess = mysql_query("SELECT estimate_id FROM fmo_locations_events_estimates WHERE estimate_event_token='".mysql_real_escape_string($lead['event_token'])."'");
                                                            if(mysql_num_rows($ess) > 0) {
                                                                $led = 'ESTIMATE LEAD'; $icon = '<i class="fa fa-book"></i>'; $label = 'label-primary'; $color = 'text-primary';  $sv = 2;
                                                            } else {
                                                                continue;
                                                            }
                                                        }
                                                        ?>
                                                        <li>
                                                            <div class="col1">
                                                                <div class="cont" style="width: 100% !important;">
                                                                    <div class="cont-col1">
                                                                        <div class="label label-sm <?php echo $label; ?>">
                                                                            <?php echo $icon; ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="cont-col2">
                                                                        <div class="desc">
                                                                            <strong><?php echo $lead['event_name']; ?></strong> <i class="fa fa-external-link"></i>
                                                                            | <strong class="<?php echo $color; ?>"><?php echo $led; ?></strong> for <strong class="<?php echo $color; ?>"><?php echo clean_phone($lead['event_phone']); ?></strong>
                                                                            <span class="<?php echo $color; ?>">
                                                                                <?php
                                                                                if(!empty($lead['event_type']) && strlen($lead['event_type']) >= 5){
                                                                                    echo " | ".$lead['event_type'];
                                                                                }
                                                                                if(!empty($lead['event_subtype']) && strlen($lead['event_subtype']) != 1){
                                                                                    echo " ".$lead['event_subtype'];
                                                                                }
                                                                                ?>
                                                                            </span>
                                                                            <?php if(!empty($lead['event_comments'])) { ?>| Comments: <strong><?php echo substr($lead['event_comments'], 0, 60)." ..."; ?></strong> <?php } ?>

                                                                            <a class="label label-sm label-info hotlead_remove pull-right" data-ev="<?php echo $lead['event_token']; ?>">
                                                                                Remove <i class="fa fa-user-times"></i>
                                                                            </a>
                                                                            <a class="label label-sm label-danger hotlead pull-right" style="margin-right: 10px;" data-uuid="<?php echo $lead['event_user_token']; ?>" data-ev="<?php echo $lead['event_token']; ?>" data-sv="<?php echo $sv; ?>">
                                                                                Continue <i class="fa fa-user-plus"></i>
                                                                            </a> <a class="label label-sm label-success touch pull-right" style="margin-right: 10px;" data-uuid="<?php echo $lead['event_user_token']; ?>" data-ev="<?php echo $lead['event_token']; ?>">
                                                                                Touch <i class="fa fa-phone"></i>
                                                                            </a>
                                                                            <?php
                                                                            if($lead['event_date_touch'] != '0000-00-00 00:00:00'){
                                                                                ?>
                                                                                <span class="pull-right text-muted" style="margin-right: 10px;">Last touched: <?php echo ago($lead['event_date_touch']); ?></span>
                                                                                <?php
                                                                            }
                                                                            ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <?php
                                                    }
                                                } else {

                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                    <?php
                                    if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_dashboard_customer_reviews")){
                                        ?>
                                        <div class="tab-pane" id="c_r">
                                            <div class="scroller" style="height: 290px;" data-always-visible="1" data-rail-visible1="1">
                                                <div class="todo-tasklist">
                                                    <?php
                                                    $ratings = 0; $rating_avg = 0; $rating_amt = 0;
                                                    $reviews = mysql_query("SELECT review_rating, review_id, review_comments, review_event_token, review_status, review_timestamp FROM fmo_locations_events_reviews WHERE review_location_token='".$_GET['luid']."' AND (review_status!=2 AND review_status!=1) ORDER BY review_timestamp DESC");
                                                    if(mysql_num_rows($reviews)) {
                                                        while ($review = mysql_fetch_assoc($reviews)) {
                                                            $event = mysql_fetch_array(mysql_query("SELECT event_user_token, event_name FROM fmo_locations_events WHERE event_token='" . mysql_real_escape_string($review['review_event_token']) . "'"));
                                                            ?>
                                                            <div class="portfolio-block review_<?php echo $review['review_id']; ?>">
                                                                <div class="col-md-3" style="padding-left: 0;">
                                                                    <div class="portfolio-text">
                                                                        <img src="<?php echo picture($event_review['even_user_token']); ?>"
                                                                             alt="" height="81px" width="81px">
                                                                        <div class="portfolio-text-info text-center">
                                                                            <div class="rateYoDash"
                                                                                 data-rateyo-rating="<?php echo $review['review_rating']; ?>" style="margin: auto!important;"></div>
                                                                            <h6 style="margin-top: 5px;"><strong>CUSTOMER:</strong> <?php echo name($event['event_user_token']); ?><br/>
                                                                                <strong>EVENT:</strong> <?php echo $event['event_name']; ?><br/>
                                                                                <strong>DATE:</strong> <?php echo date('m/d/Y', strtotime($review['review_timestamp'])); ?></h6>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-7 portfolio-stat"
                                                                     style="margin-top: 8px;">
                                                                    <div class="portfolio-info"
                                                                         style="text-transform: none !important;">
                                                                        <?php echo $review['review_comments']; ?>
                                                                    </div>
                                                                </div>
                                                                <?php
                                                                if (($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_dashboard_customer_reviews_judge")) && $review['review_status'] != 1) {
                                                                    ?>
                                                                    <div class="col-md-1" style="padding-right: 0;">
                                                                        <div class="portfolio-btn portfolio-btn-a review_btns_<?php echo $review['review_id']; ?>">
                                                                            <a class="btn bigicn-only red review_stat"
                                                                               data-stat="1"
                                                                               data-r="<?php echo $review['review_id']; ?>">
                                                                                <span>Approve </span>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-1" style="padding-right: 0;">
                                                                        <div class="portfolio-btn portfolio-btn-b info-btn review_btns_<?php echo $review['review_id']; ?>">
                                                                            <a class="btn bigicn-only blue review_stat"
                                                                               data-stat="2"
                                                                               data-r="<?php echo $review['review_id']; ?>">
                                                                                <span>Remove </span>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </div>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>

                                    <?php
                                    if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_dashboard_manager_tools")){
                                        ?>
                                        <div class="tab-pane" id="m_t">
                                            <div class="scroller" style="height: 290px;" data-always-visible="1" data-rail-visible1="1">
                                                <div class="tiles">
                                                    <div class="tile double-down bg-blue-hoki" data-toggle="modal" href="#expenses">
                                                        <div class="tile-body">
                                                            <i class="fa fa-money"></i>
                                                        </div>
                                                        <div class="tile-object">
                                                            <div class="name">
                                                                Add expense for this location
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="tile double-down bg-green-turquoise" data-toggle="modal" href="#send_doc">
                                                        <div class="tile-body">
                                                            <i class="fa fa-paperclip"></i>
                                                        </div>
                                                        <div class="tile-object">
                                                            <div class="name">
                                                                Send a document by email
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="tile double-down bg-red-intense" data-toggle="modal" href="#maintenance">
                                                        <div class="tile-body">
                                                            <i class="fa fa-truck"></i>
                                                        </div>
                                                        <div class="tile-object">
                                                            <div class="name">
                                                                Add maintenance record
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="tile double-down bg-blue-dark" data-toggle="modal" href="#writeup">
                                                        <div class="tile-body">
                                                            <i class="fa fa-user-times"></i>
                                                        </div>
                                                        <div class="tile-object">
                                                            <div class="name">
                                                                Add writeup for employee
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <!--END TABS-->
                            </div>
                        </div>
                    </div>
                    <!--
            <div class="col-md-6 col-sm-12">
                <div class="portlet light ">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="icon-bubble font-red-sunglo"></i>
                            <span class="caption-subject font-red-sunglo bold uppercase"><?php echo $location['location_name']; ?> Chatroom</span>
                        </div>
                        <div class="actions">
                            <div class="portlet-input input-inline">
                                <div class="input-icon right">
                                    <i class="icon-magnifier"></i>
                                    <input type="text" class="form-control input-circle" placeholder="search...">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="portlet-body" id="chats">
                        <div class="scroller" style="height: 341px;" data-always-visible="1" data-rail-visible1="1">
                            <ul class="chats">
                                <li class="in">
                                    <img class="avatar" alt="" src="assets/admin/layout/img/avatar1.jpg"/>
                                    <div class="message">
											<span class="arrow">
											</span>
                                        <a href="javascript:;" class="name">
                                            Bob Nilson </a>
                                        <span class="datetime">
											at 20:09 </span>
                                        <span class="body">
											Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. </span>
                                    </div>
                                </li>
                                <li class="out">
                                    <img class="avatar" alt="" src="assets/admin/layout/img/avatar2.jpg"/>
                                    <div class="message">
											<span class="arrow">
											</span>
                                        <a href="javascript:;" class="name">
                                            Lisa Wong </a>
                                        <span class="datetime">
											at 20:11 </span>
                                        <span class="body">
											Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. </span>
                                    </div>
                                </li>
                                <li class="in">
                                    <img class="avatar" alt="" src="assets/admin/layout/img/avatar1.jpg"/>
                                    <div class="message">
											<span class="arrow">
											</span>
                                        <a href="javascript:;" class="name">
                                            Bob Nilson </a>
                                        <span class="datetime">
											at 20:30 </span>
                                        <span class="body">
											Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. </span>
                                    </div>
                                </li>
                                <li class="out">
                                    <img class="avatar" alt="" src="assets/admin/layout/img/avatar3.jpg"/>
                                    <div class="message">
											<span class="arrow">
											</span>
                                        <a href="javascript:;" class="name">
                                            Richard Doe </a>
                                        <span class="datetime">
											at 20:33 </span>
                                        <span class="body">
											Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. </span>
                                    </div>
                                </li>
                                <li class="in">
                                    <img class="avatar" alt="" src="assets/admin/layout/img/avatar3.jpg"/>
                                    <div class="message">
											<span class="arrow">
											</span>
                                        <a href="javascript:;" class="name">
                                            Richard Doe </a>
                                        <span class="datetime">
											at 20:35 </span>
                                        <span class="body">
											Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. </span>
                                    </div>
                                </li>
                                <li class="out">
                                    <img class="avatar" alt="" src="assets/admin/layout/img/avatar1.jpg"/>
                                    <div class="message">
											<span class="arrow">
											</span>
                                        <a href="javascript:;" class="name">
                                            Bob Nilson </a>
                                        <span class="datetime">
											at 20:40 </span>
                                        <span class="body">
											Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. </span>
                                    </div>
                                </li>
                                <li class="in">
                                    <img class="avatar" alt="" src="assets/admin/layout/img/avatar3.jpg"/>
                                    <div class="message">
											<span class="arrow">
											</span>
                                        <a href="javascript:;" class="name">
                                            Richard Doe </a>
                                        <span class="datetime">
											at 20:40 </span>
                                        <span class="body">
											Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. </span>
                                    </div>
                                </li>
                                <li class="out">
                                    <img class="avatar" alt="" src="assets/admin/layout/img/avatar1.jpg"/>
                                    <div class="message">
											<span class="arrow">
											</span>
                                        <a href="javascript:;" class="name">
                                            Bob Nilson </a>
                                        <span class="datetime">
											at 20:54 </span>
                                        <span class="body">
											Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. sed diam nonummy nibh euismod tincidunt ut laoreet. </span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="chat-form">
                            <div class="input-cont">
                                <input class="form-control" type="text" placeholder="Type a message here...">
                            </div>
                            <div class="btn-cont">
									<span class="arrow">
									</span>
                                <a href="" class="btn blue icn-only">
                                    <i class="fa fa-check icon-white"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>-->
                </div>
            </div>
            <div class="modal fade bs-modal-lg" id="recent_texts" tabindex="-1" role="basic" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content box red">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                            <h3 class="modal-title font-bold">Recent texts to <?php echo name($location['location_manager']); ?> <small>(and maybe Joshua)</small></h3>
                        </div>
                        <div class="modal-body">
                            <div class="portlet">
                                <div class="portlet-body">
                                    <table class="table table-striped table-bordered table-hover datatable" data-src="assets/app/api/dashboard.php?type=ttm&luid=<?php echo $_GET['luid']; ?>">
                                        <thead>
                                        <tr role="row" class="heading">
                                            <th>
                                                Text Timestamp
                                            </th>
                                            <th>
                                                Text
                                            </th>
                                            <th>
                                                Sent by
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <form method="POST" action="" role="form" id="add_daily_notes">
                <div class="modal fade bs-modal-lg" id="add_daily_note" tabindex="-1" role="basic" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content box red">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                <h3 style="margin-top: 0" class="modal-title font-bold">Add note for <span class="daily_date"></span></h3>
                            </div>
                            <div class="modal-body">
                                <h3><strong>Daily note</strong> for <?php echo locationName($_GET['luid']); ?></h3>
                                <p>You're adding a note for <strong class="daily_date"></strong> in <strong><?php echo locationName($_GET['luid']); ?></strong>. This note will appear on the dashboard for the selected date, if today is the day the note was for.</p>
                                <hr/>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label><strong><?php echo name($_SESSION['uuid']); ?></strong> thinks..</label>
                                            <input type="text" class="form-control" name="daily_note" placeholder="Enter your thoughts here.">
                                            <input type="hidden" class="daily_date_val" name="date">
                                        </div>
                                    </div>
                                </div>
                                <br/>
                                <p><span class="text-danger">*</span> <strong>It's cool!</strong> If you don't see your note on the dashboard, it's because today is <strong class="text-danger">NOT</strong> the day the note is set for.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn default" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn red pull-right">Save daily note</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <form method="POST" action="" role="form" id="add_expense">
                <div class="modal fade bs-modal-lg" id="expenses" tabindex="-1" role="basic" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content box red">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                <h3 class="modal-title font-bold">Add an expense for <?php echo locationName($_GET['luid']); ?></h3>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Expense description (be brief)</label>
                                            <input type="text" class="form-control" name="description" placeholder="Description">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Job Name - Required if cash</label>
                                            <input type="text" class="form-control" name="name" placeholder="Job name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Date</label>
                                            <input type="text" class="form-control" name="date" id="expense_date">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Expense Type</label>
                                            <select name="type" class="form-control">
                                                <option disabled selected value="">Select one..</option>
                                                <option value="Credit/Debt Card">Credit/Debit Card</option>
                                                <option value="Credit/Debt Card">Fleet Card</option>
                                                <option value="Cash">Cash</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Expense Reason</label>
                                            <select name="reason" class="form-control">
                                                <option disabled selected value="">Select one..</option>
                                                <option value="Truck">Truck</option>
                                                <option value="Fuel">Fuel</option>
                                                <option value="Office">Office</option>
                                                <option value="Repair">Repair</option>
                                                <option value="Misc">Misc</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Expense Amount</label>
                                            <input type="number" step="any" class="form-control" name="amount" placeholder="$100.00">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn default" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn red">Save expense</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <form method="POST" action="" role="form" id="add_writeup">
                <div class="modal fade bs-modal-lg" id="writeup" tabindex="-1" role="basic" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content box red">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                <h3 class="modal-title font-bold">Add a writeup for <?php echo locationName($_GET['luid']); ?></h3>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Employee being written up</label>
                                            <select class="form-control input-sm writeups-avail" name="who" id="who">
                                                <option disabled selected value="">Select laborer..</option>
                                                <?php
                                                $laborers = mysql_query("SELECT user_fname, user_lname, user_employer_rate, user_token FROM fmo_users WHERE user_employer='".mysql_real_escape_string($_SESSION['cuid'])."' AND user_status=1 ORDER BY user_lname ASC");
                                                if(mysql_num_rows($laborers) > 0){
                                                    while($laborer = mysql_fetch_assoc($laborers)){
                                                        ?>
                                                        <option value="<?php echo $laborer['user_token']; ?>"><?php echo $laborer['user_lname'].", ".$laborer['user_fname']; ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Write up Reasoning</label>
                                            <input type="text" class="form-control" name="reasoning">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Action</label>
                                            <select class="form-control" name="action">
                                                <option disabled selected value="">Select action..</option>
                                                <option value="Warning">Warning</option>
                                                <option value="Suspended for day">Suspended for day</option>
                                                <option value="Suspended for week">Suspended for week</option>
                                                <option value="Pay Reduction">Pay Reduction</option>
                                                <option value="Termination">Termination</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn default" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn red">Save write up</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <form method="POST" action="" role="form" id="send_docs">
                <div class="modal fade bs-modal-lg" id="send_doc" tabindex="-1" role="basic" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content box red">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                <h3 class="modal-title font-bold">Send a document from <?php echo locationName($_GET['luid']); ?> to an email</h3>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Sending to email:</label>
                                            <input type="email" class="form-control" name="email" placeholder="someone@somewhere.net">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Send document:</label>
                                            <select class="form-control" name="doc">
                                                <option disabled selected value="">Select one..</option>
                                                <?php
                                                $sendable = mysql_query("SELECT sendable_token, sendable_name FROM fmo_sendables WHERE sendable_company_token='".mysql_real_escape_string($_SESSION['cuid'])."'");
                                                if(mysql_num_rows($sendable) > 0){
                                                    while($send = mysql_fetch_assoc($sendable)){
                                                        ?>
                                                        <option value="<?php echo $send['sendable_token']; ?>"><?php echo $send['sendable_name']; ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Message to be sent with document:</label>
                                            <textarea class="form-control" name="message" style="height: 150px;" placeholder="Some descriptive message about this document"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Document (PDF only)</label>
                                            <input type="file" class="form-control" name="document" accept="application/pdf">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn default" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn red">Send document</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <form method="POST" action="" role="form" id="add_maintenance">
                <div class="modal fade bs-modal-lg" id="maintenance" tabindex="-1" role="basic" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content box red">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                <h3 class="modal-title font-bold">Add a maintenance record for asset in <?php echo locationName($_GET['luid']); ?></h3>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Asset</label>
                                            <select name="asset" class="form-control">
                                                <option disabled selected value="">Select one..</option>
                                                <?php
                                                $assets = mysql_query("SELECT asset_id, asset_desc FROM fmo_locations_assets WHERE asset_location_token='".$_GET['luid']."' ORDER BY asset_desc ASC");
                                                if(mysql_num_rows($assets) > 0){
                                                    while($asset = mysql_fetch_assoc($assets)){
                                                        ?>
                                                        <option value="<?php echo $asset['asset_id']; ?>"><?php echo $asset['asset_desc']; ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Asset Maintenance Description (be brief)</label>
                                            <input type="text" class="form-control" name="description" placeholder="Description">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Asset Maintenance Type</label>
                                            <select name="type" class="form-control">
                                                <option disabled selected value="">Select one..</option>
                                                <option value="Basic Maintenance">Basic Maintenance</option>
                                                <option value="DOT Maintenance">DOT Maintenance</option>
                                                <option value="Accident Maintenance">Accident Maintenance</option>
                                                <option value="Major Repair Maintenance">Major Repair Maintenance</option>
                                                <option value="Other Maintenance">Other Maintenance</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Asset Maintenance By</label>
                                            <input type="text" class="form-control" name="by" placeholder="<?php echo name($_SESSION['uuid']); ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Maintenance Cost</label>
                                            <input type="number" step="any" class="form-control" name="cost" placeholder="$100.00">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>PO Number</label>
                                            <input type="number" step="any" class="form-control" name="po_number" placeholder="123456">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Asset Mileage</label>
                                            <input type="number" step="any" class="form-control" name="mileage" placeholder="126,129mi">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn default" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn red">Save maintenance record</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <script type="text/javascript">
                jQuery(document).ready(function(){


                    $(".writeups-avail").select2({
                        placeholder: 'Select laborer..'
                    }).on('change', function() {
                        $(this).valid();
                    });
                    $("#add_writeup").validate({
                        errorElement: 'span', //default input error message container
                        errorClass: 'font-red', // default input error message class
                        rules: {
                            who: {
                                required: true
                            },
                            reasoning: {
                                required: true
                            },
                            action: {
                                required: true
                            }
                        },
                        invalidHandler: function(event, validator) { //display error alert on form submit

                        },

                        highlight: function(element) { // hightlight error inputs
                            $(element)
                                .closest('.form-group').addClass('has-error'); // set error class to the control group
                        },

                        success: function(label) {
                            label.closest('.form-group').removeClass('has-error');
                            label.remove();
                        },


                        submitHandler: function(form) {
                            var uuid = $('#who option:selected').val();
                            $.ajax({
                                url: "assets/app/add_setting.php?setting=usr_writeup&uuid="+ uuid,
                                type: "POST",
                                data: $('#add_writeup').serialize(),
                                success: function(data) {
                                    $('#writeup').modal('hide');
                                    $('#add_writeup')[0].reset();
                                    toastr.info('<strong>Logan says</strong>:<br/>Write-up has been added to users write-up history.');
                                },
                                error: function() {
                                    toastr.error('<strong>Logan says</strong>:<br/>That page didnt respond correctly. Try again, or create a support ticket for help.');
                                }
                            });
                        }
                    });
                    $('.scroller').slimScroll({
                        height: 1000,
                        allowPageScroll: true
                    });
                    $('.scrollerz').slimScroll({
                        height: 170,
                        allowPageScroll: true
                    });

                        $.ajax({
                            url: 'assets/app/api/graph.php?ty=lt&luid=<?php echo $_GET['luid']; ?>',
                            type: 'POST',
                            data: {

                            },
                            success: function(graph){
                                var data = JSON.parse(graph);
                                var pageviews = [
                                    [1, data[<?php echo date('Y', strtotime('-1 year')); ?>][1]],
                                    [2, data[<?php echo date('Y', strtotime('-1 year')); ?>][2]],
                                    [3, data[<?php echo date('Y', strtotime('-1 year')); ?>][3]],
                                    [4, data[<?php echo date('Y', strtotime('-1 year')); ?>][4]],
                                    [5, data[<?php echo date('Y', strtotime('-1 year')); ?>][5]],
                                    [6, data[<?php echo date('Y', strtotime('-1 year')); ?>][6]],
                                    [7, data[<?php echo date('Y', strtotime('-1 year')); ?>][7]],
                                    [8, data[<?php echo date('Y', strtotime('-1 year')); ?>][8]],
                                    [9, data[<?php echo date('Y', strtotime('-1 year')); ?>][9]],
                                    [10, data[<?php echo date('Y', strtotime('-1 year')); ?>][10]],
                                    [11, data[<?php echo date('Y', strtotime('-1 year')); ?>][11]],
                                    [12, data[<?php echo date('Y', strtotime('-1 year')); ?>][12]]
                                ];
                                var visitors = [
                                    [1, data[<?php echo date('Y'); ?>][1]],
                                    [2, data[<?php echo date('Y'); ?>][2]],
                                    [3, data[<?php echo date('Y'); ?>][3]],
                                    [4, data[<?php echo date('Y'); ?>][4]],
                                    [5, data[<?php echo date('Y'); ?>][5]],
                                    [6, data[<?php echo date('Y'); ?>][6]],
                                    [7, data[<?php echo date('Y'); ?>][7]],
                                    [8, data[<?php echo date('Y'); ?>][8]],
                                    [9, data[<?php echo date('Y'); ?>][9]],
                                    [10, data[<?php echo date('Y'); ?>][10]],
                                    [11, data[<?php echo date('Y'); ?>][11]],
                                    [12, data[<?php echo date('Y'); ?>][12]]
                                ];


                                var plot = $.plot($("#stats"), [{
                                    data: pageviews,
                                    label: "<?php echo date('Y', strtotime("-1 year")); ?> NET Sales",
                                    lines: {
                                        lineWidth: 1,
                                    },
                                    shadowSize: 0
                                }, {
                                    data: visitors,
                                    label: "<?php echo date('Y'); ?> NET Sales",
                                    lines: {
                                        lineWidth: 1,
                                    },
                                    shadowSize: 0
                                }], {
                                    series: {
                                        lines: {
                                            show: true,
                                            lineWidth: 2,
                                            fill: true,
                                            fillColor: {
                                                colors: [{
                                                    opacity: 0.05
                                                }, {
                                                    opacity: 0.01
                                                }]
                                            }
                                        },
                                        points: {
                                            show: true,
                                            radius: 3,
                                            lineWidth: 1
                                        },
                                        shadowSize: 2
                                    },
                                    grid: {
                                        hoverable: true,
                                        clickable: true,
                                        tickColor: "#eee",
                                        borderColor: "#eee",
                                        borderWidth: 1
                                    },
                                    colors: ["#d12610", "#37b7f3", "#52e136", "#d4ad38"],
                                    xaxis: {
                                        ticks: [
                                            [1, "Jan"],
                                            [2, "Feb"],
                                            [3, "Mar"],
                                            [4, "Apr"],
                                            [5, "May"],
                                            [6, "Jun"],
                                            [7, "Jul"],
                                            [8, "Aug"],
                                            [9, "Sep"],
                                            [10, "Oct"],
                                            [11, "Nov"],
                                            [12, "Dec"]
                                        ]
                                    },
                                    yaxis: {
                                        ticks: 11,
                                        tickDecimals: 0,
                                        tickColor: "#eee",
                                    }
                                });

                                function showTooltip(x, y, contents) {
                                    $('<div id="tooltip">' + contents + '</div>').css({
                                        position: 'absolute',
                                        display: 'none',
                                        top: y + 5,
                                        left: x + 15,
                                        border: '1px solid #333',
                                        padding: '4px',
                                        color: '#fff',
                                        'border-radius': '3px',
                                        'background-color': '#333',
                                        opacity: 0.80
                                    }).appendTo("body").fadeIn(200);
                                }

                                var previousPoint = null;
                                $("#stats").bind("plothover", function(event, pos, item) {
                                    $("#x").text(pos.x.toFixed(2));
                                    $("#y").text(pos.y.toFixed(2));

                                    if (item) {
                                        if (previousPoint != item.dataIndex) {
                                            previousPoint = item.dataIndex;

                                            $("#tooltip").remove();
                                            var x = item.datapoint[0].toFixed(2),
                                                y = item.datapoint[1].toFixed(2);

                                            showTooltip(item.pageX, item.pageY, item.series.label + " for month: " + x + " made: " + y);
                                        }
                                    } else {
                                        $("#tooltip").remove();
                                        previousPoint = null;
                                    }
                                });
                            },
                            error: function(graph){
                                toastr.error("<strong>Logan says:</strong><br/>There was an error loading the income graph.");
                            }
                        });




                    $('#add_expense').validate({
                        errorElement: 'span', //default input error message container
                        errorClass: 'help-block', // default input error message class
                        focusInvalid: false, // do not focus the last invalid input
                        ignore: "",
                        rules: {
                            description: {
                                required: true
                            },
                            date: {
                                required: true
                            },
                            type: {
                                required: true
                            },
                            reason: {
                                required: true
                            },
                            amount: {
                                required: true,
                                number: true
                            }
                        },

                        invalidHandler: function(event, validator) { //display error alert on form submit

                        },

                        highlight: function(element) { // hightlight error inputs
                            $(element)
                                .closest('.form-group').addClass('has-error'); // set error class to the control group
                        },

                        success: function(label) {
                            label.closest('.form-group').removeClass('has-error');
                            label.remove();
                        },


                        submitHandler: function(form) {
                            $('#expenses').modal('hide');


                            $.ajax({
                                url: 'assets/app/add_setting.php?setting=expense&luid=<?php echo $_GET['luid']; ?>',
                                type: 'POST',
                                data: $('#add_expense').serialize(),
                                success: function (d) {
                                    $('#add_expense')[0].reset();
                                    toastr.success("<strong>Logan says</strong>:<br/>I have submitted that expense to the company records.");$('#add_expense')[0].reset();
                                },
                                error: function () {
                                    toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                                }
                            });
                        }
                    });

                    $('#send_docs').validate({
                        errorElement: 'span', //default input error message container
                        errorClass: 'help-block', // default input error message class
                        focusInvalid: false, // do not focus the last invalid input
                        ignore: "",
                        rules: {
                            email: {
                                required: true,
                                email: true
                            },
                            doc: {
                                required: true
                            },
                            message: {
                                required: true
                            },
                            document: {
                                required: false
                            }
                        },

                        invalidHandler: function(event, validator) { //display error alert on form submit

                        },

                        highlight: function(element) { // hightlight error inputs
                            $(element)
                                .closest('.form-group').addClass('has-error'); // set error class to the control group
                        },

                        success: function(label) {
                            label.closest('.form-group').removeClass('has-error');
                            label.remove();
                        },


                        submitHandler: function(form) {
                            $('#expenses').modal('hide');


                            $.ajax({
                                url: 'assets/app/api/actions.php?send_doc=from&luid=<?php echo $_GET['luid']; ?>',
                                type: 'POST',
                                data: new FormData($('#send_docs')[0]),
                                processData: false,
                                contentType: false,
                                success: function (d) {
                                    toastr.success("<strong>Logan says</strong>:<br/>I have sent that document to the email you gave me.");
                                    $('#send_doc').modal('hide');
                                    $('#send_docs')[0].reset();
                                },
                                error: function () {
                                    toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                                }
                            });
                        }
                    });

                    $('#add_daily_notes').validate({
                        errorElement: 'span', //default input error message container
                        errorClass: 'help-block', // default input error message class
                        focusInvalid: false, // do not focus the last invalid input
                        ignore: "",
                        rules: {
                            daily_note: {
                                required: true
                            }
                        },

                        invalidHandler: function(event, validator) { //display error alert on form submit

                        },

                        highlight: function(element) { // hightlight error inputs
                            $(element)
                                .closest('.form-group').addClass('has-error'); // set error class to the control group
                        },

                        success: function(label) {
                            label.closest('.form-group').removeClass('has-error');
                            label.remove();
                        },


                        submitHandler: function(form) {
                            $.ajax({
                                url: 'assets/app/add_setting.php?setting=daily_note&luid=<?php echo $_GET['luid']; ?>',
                                type: 'POST',
                                data: $('#add_daily_notes').serialize(),
                                success: function (d) {
                                    toastr.success("<strong>Logan says</strong>:<br/>I have saved that note for the date you selected.");
                                    $('#add_daily_note').modal('hide');
                                    $('#add_daily_notes')[0].reset();
                                    $('.refresh').click();
                                },
                                error: function () {
                                    toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                                }
                            });
                        }
                    });

                    $('#add_maintenance').validate({
                        errorElement: 'span', //default input error message container
                        errorClass: 'help-block', // default input error message class
                        focusInvalid: false, // do not focus the last invalid input
                        ignore: "",
                        rules: {
                            asset: {
                                required: true
                            },
                            description: {
                                required: true
                            },
                            type: {
                                required: true
                            },
                            by: {
                                required: true
                            },
                            cost: {
                                required: true
                            },
                            po_number: {
                                required: true
                            },
                            mileage: {
                                required: true
                            }
                        },

                        invalidHandler: function(event, validator) { //display error alert on form submit

                        },

                        highlight: function(element) { // hightlight error inputs
                            $(element)
                                .closest('.form-group').addClass('has-error'); // set error class to the control group
                        },

                        success: function(label) {
                            label.closest('.form-group').removeClass('has-error');
                            label.remove();
                        },


                        submitHandler: function(form) {
                            $('#expenses').modal('hide');


                            $.ajax({
                                url: 'assets/app/add_setting.php?setting=maintanence&luid=<?php echo $_GET['luid']; ?>',
                                type: 'POST',
                                data: $('#add_maintenance').serialize(),
                                success: function (d) {
                                    toastr.success("<strong>Logan says</strong>:<br/>I have submitted that maintenance record to the company records.");$('#add_maintenance')[0].reset();
                                },
                                error: function () {
                                    toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                                }
                            });
                        }
                    });

                    $('#expense_date').datepicker();




                    <?php
                    if(!empty($location['location_manager'])){
                    ?>
                    function updateCountdown() {
                        var remaining = 160 - $('.txt-message').val().length;
                        $('.txt-countdown').text(remaining + ' characters remaining.');
                    }
                    updateCountdown();
                    $('.txt-message').change(updateCountdown);
                    $('.txt-message').keyup(updateCountdown);

                    $('.ttm').click(function() {
                        var uuid = "<?php echo $location['location_manager']; ?>";
                        if($('#ttm_msg').val().length > 0){
                            $.ajax({
                                url: 'assets/app/texting.php?txt=ttm&uuid='+uuid+'&luid=<?php echo $_GET['luid']; ?>',
                                type: 'POST',
                                data: {
                                    msg: $('#ttm_msg').val()
                                },
                                success: function(e){
                                    toastr.success("<strong>Logan says:</strong><br/>Text message has been sent to <?php echo name($location['location_manager']); ?>");
                                    $('#ttm_msg').val('');
                                    updateCountdown();
                                },
                                error: function(e){
                                    toastr.error("<strong>Logan says:</strong><br/>Something bad happened. You messed everything up. Just kidding, try that again.")
                                }
                            })
                        } else {
                            toastr.error("<strong>Logan says:</strong><br/>You need to type up a message first, silly! I can't send <strong><i>nothing</i></strong>!")
                        }

                    });
                    <?php
                    }
                    if(mysql_num_rows($daily_notes) > 0){
                        ?>
                        $('#dah_notes').show();
                        $('#dah_graph').hide();
                       <?php
                    } else {
                        ?>
                        $('#dah_graph').show();
                        $('#dah_notes').hide();
                        <?php
                    }
                    ?>

                    $('#dashboard-report-range').daterangepicker({
                            startDate: "<?php echo date('Y-m-d', strtotime($uuidperm['user_last_ext_date'])); ?>",
                            endDate: "<?php echo date('Y-m-d', strtotime($uuidperm['user_last_ext_date'])); ?>",
                            showDropdowns: false,
                            showWeekNumbers: false,
                            singleDatePicker: true,
                            timePicker: false,
                            timePickerIncrement: 1,
                            timePicker12Hour: true,
                            buttonClasses: ['btn btn-sm'],
                            applyClass: ' blue',
                            cancelClass: 'default',
                            format: 'YYYY-MM-DD',
                            separator: ' to ',
                            locale: {
                                applyLabel: 'Apply',
                                fromLabel: 'From',
                                toLabel: 'To',
                                customRangeLabel: 'Custom Range',
                                daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                                monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                                firstDay:0
                            }
                        },
                        function (start, end) {
                            $('#dashboard-report-range span').html(start.format('MM-DD-YYYY'));
                            $('.mag').attr('data-month', start.format('YYYY-MM-DD'));
                            $('.add_daily_note').attr('data-date', start.format('YYYY-MM-DD'));
                            $('.add_daily_note').attr('data-date2', start.format('MM/DD/YYYY'));
                            $('#note_date').html(start.format('MM-DD-YYYY'));
                            $('#notes_dtd').html(start.format('MM-DD-YYYY'));
                            $.ajax({
                                url: 'assets/app/update_settings.php?update=usr_prf',
                                type: 'POST',
                                data: {
                                    name: 'user_last_ext_date',
                                    value: ''+ start.format('YYYY-MM-DD') +'',
                                    pk: '<?php echo $_SESSION['uuid']; ?>'
                                },
                                success: function(){
                                    $.ajax({
                                        url: 'assets/pages/sub/dashboard_master.php?t=dash_evs&luid=<?php echo $_GET['luid']; ?>',
                                        type: 'POST',
                                        data: {
                                            range: ''+start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD')+''
                                        },
                                        success: function(events){
                                            $('#dashboard_events').html(events);
                                            toastr.info("<strong>Logan says:</strong><br/>New events and notes loaded based on new date.");
                                        },
                                        error: function(error){
                                            toastr.error("<strong>Logan says:</strong><br/>Something didn't work correctly there. Try again.");
                                        }
                                    });
                                    $.ajax({
                                        url: 'assets/pages/sub/dashboard_master.php?t=dash_notes&luid=<?php echo $_GET['luid']; ?>',
                                        type: 'POST',
                                        data: {
                                            range: ''+start.format('YYYY-MM-DD') + ''
                                        },
                                        success: function(notes){
                                            if(notes == 'NOPE'){
                                                $('#dah_notes').hide();
                                                $('#dah_graph').show();
                                            } else {
                                                $('#dah_graph').hide();
                                                $('#dah_notes').show();
                                                $('#those_notes').html(notes);
                                            }

                                        },
                                        error: function(error){

                                        }
                                    });
                                }, error: function() {

                                }
                            });
                        }
                    );

                    <?php
                        if(isset($_POST['checkDay']) && $_POST['checkDay'] == true){
                            ?>
                            $.ajax({
                                url: 'assets/pages/sub/dashboard_master.php?t=dash_evs&luid=<?php echo $_GET['luid']; ?>',
                                type: 'POST',
                                data: {
                                    range: '<?php echo date('Y-m-d', strtotime($uuidperm['user_last_ext_date'])); ?> - <?php echo date('Y-m-d', strtotime($uuidperm['user_last_ext_date'])); ?>'
                                },
                                success: function(events){
                                    $('#dashboard_events').html(events);
                                    toastr.success("<strong>Logan says:</strong><br/>I updated the lists for you below.");
                                },
                                error: function(error){
                                    toastr.error("<strong>Logan says:</strong><br/>Something didn't work correctly there. Try again.");
                                }
                            });
                            <?php
                        }
                    ?>

                    $('.mag').click(function() {
                        var date = $(this).attr('data-month');
                        $.ajax({
                            url: 'assets/pages/sub/dashboard_master.php?t=dash_mag&luid=<?php echo $_GET['luid']; ?>',
                            type: 'POST',
                            data: {
                                month: date
                            },
                            success: function(events){
                                $('#dashboard_events').html(events);
                                toastr.success("<strong>Logan says:</strong><br/>Events for the month viewable below.");
                            },
                            error: function(){
                                toastr.error("<strong>Logan says:</strong><br/>Something didn't work correctly there. Try again.");
                            }
                        })
                    });

                    $('.datatable').each(function(){
                        var url = $(this).attr('data-src');
                        $(this).dataTable({
                            "processing": true,
                            "serverSide": true,
                            "order": [[ 0, "asc" ]],
                            "bFilter" : false,
                            "bLengthChange": false,
                            "bPaginate": false,
                            "info": true,
                            "ajax": {
                                "url": url // ajax source
                            }
                        });
                    });

                    $('.show_form').on('click', function() {
                        var show = $(this).attr('data-show');

                        $(show).show();
                    });
                    $('.add_daily_note').on('click', function() {
                        var date = $(this).attr('data-date');
                        var date2 = $(this).attr('data-date2');
                        $('.daily_date').html(date2);
                        $('.daily_date_val').val(date);
                    });

                    $('.rateYoDash').rateYo({
                        halfStar: true,
                        readOnly: true
                    });
                    $('#dashboard-report-range').show();
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
    }
} else {
    header("Location: ../../../index.php?err=no_access");
}
?>
