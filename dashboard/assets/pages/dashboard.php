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
    mysql_query("UPDATE fmo_users SET user_last_location='".mysql_real_escape_string(basename(__FILE__, '.php')).".php?".$_SERVER['QUERY_STRING']."' WHERE user_token='".mysql_real_escape_string($_SESSION['uuid'])."'");
    $location = mysql_fetch_array(mysql_query("SELECT location_name, location_manager FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'"));
    ?>
    <div class="page-content">
        <h3 class="page-title">
            <strong>Dashboard</strong>
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
            <div class="page-toolbar">
                <?php
                $ratings = 0; $rating_avg = 0; $rating_amt = 0;
                $events = mysql_query("SELECT event_token FROM fmo_locations_events WHERE event_location_token='".$_GET['luid']."'");
                if(mysql_num_rows($events) > 0){
                    $start = date('Y-m-d', strtotime("-365 days"));
                    $end   = date('Y-m-d');
                    while($evt = mysql_fetch_assoc($events)){
                        $reviews = mysql_query("SELECT review_rating FROM fmo_locations_events_reviews WHERE review_event_token='".$evt['event_token']."'");
                        if(mysql_num_rows($reviews) > 0){
                            $review  = mysql_fetch_array($reviews);
                            $ratings+= $review['review_rating'];
                            $rating_amt++;
                        }
                    }
                    $rating_avg = $ratings / $rating_amt;
                }
                ?>
                <div class="pull-right btn red btn-fit-height"><strong><?php echo number_format($rating_avg, 1); ?></strong> Average Rating</div>
                <div class="pull-right" data-toggle="modal" href="#avg_rating">
                    <div class="rateYoDash" data-rateyo-rating="<?php echo number_format($rating_avg, 1); ?>"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light">
                    <div class="portlet-title tabbable-line">
                        <div class="caption caption-md">
                            <i class="icon-home theme-font bold"></i>
                            <span class="caption-subject font-red bold uppercase"><?php echo $location['location_name']; ?></span> <span class="font-red">|</span> <small>Welcome back to <?php echo $location['location_name']; ?>'s dashboard, <strong><?php echo $_SESSION['fname']; ?></strong>. I collected information for you on today's current activity below.</small>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <div class="col-md-6">
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
        <?php
        $broadcast = getBroadcast($_SESSION['cuid']);
        if(!empty($broadcast['message']) && $broadcast['time'] > date('Y-m-d', strtotime($broadcast['time'].' + 2 days'))){
            ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-warning">
                        <marquee>
                            Company broadcast | <strong class="text-danger" style="font-size: 16px;">
                                <?php echo $broadcast['message']; ?>
                            </strong>
                        </marquee>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light">
                    <div class="portlet-title tabbable-line">
                        <div class="caption caption-md">
                            <i class="icon-home theme-font bold"></i>
                            <span class="caption-subject font-red bold uppercase"><?php echo $location['location_name']; ?></span> <span class="font-red">|</span> <small>All Events</small>
                        </div>
                        <div class="actions">
                            <div class="btn-group">
                                <button class="btn red"><i class="fa fa-external-link"></i> Month at a Glance</button>
                            </div>
                            <div class="btn-group">
                                <a id="dashboard-report-range" class="pull-right tooltips btn red" data-container="body" data-placement="bottom" data-original-title="Change dashboard date range">
                                    <i class="icon-calendar"></i>&nbsp;
                                    <span class="bold uppercase visible-lg-inline-block">
                                        <?php echo date('M d, Y'); ?> - <?php echo date('M d, Y'); ?>
                                    </span>&nbsp; <i class="fa fa-angle-down"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 style="margin-top: 0px;">Morning Jobs <small class="hidden-sm"><span class="text-danger">|</span> before noon</small></h3>
                                <div class="todo-tasklist">
                                    <?php
                                    $events = mysql_query("SELECT event_name, event_date_start, event_date_end, event_time, event_token, event_status, event_type, event_subtype, event_booking, event_truckfee, event_laborrate FROM fmo_locations_events WHERE event_location_token='".mysql_real_escape_string($_GET['luid'])."'");
                                    if(mysql_num_rows($events) > 0){
                                        $morningCount = mysql_num_rows($events);
                                        while($event = mysql_fetch_assoc($events)){
                                            if($event['event_status'] == 0){
                                                continue;
                                            }
                                            $times = explode("to", $event['event_time']);
                                            if(strtotime($times[0]) >= strtotime("12:00PM")){
                                                continue;
                                            }
                                            $start = mysql_fetch_array(mysql_query("SELECT address_address, address_city FROM fmo_locations_events_addresses WHERE address_event_token='".$event['event_token']."' AND address_type=1"));
                                            $end   = mysql_fetch_array(mysql_query("SELECT address_address, address_city FROM fmo_locations_events_addresses WHERE address_event_token='".$event['event_token']."' AND address_type=2"));
                                            ?>
                                            <div class="todo-tasklist-item todo-tasklist-item-border-red load_page col-md-12" data-href="assets/pages/event.php?ev=<?php echo $event['event_token']; ?>" data-page-title="<?php echo $event['event_name']; ?>">
                                                <div class="todo-tasklist-item-title">
                                                    <?php echo $event['event_name']; ?> <span class="text-danger">|</span> <small><i class="fa fa-truck"></i> Trucks: <?php echo $event['event_truckfee']; ?> + <i class="fa fa-users"></i> Crew size: <?php echo $event['event_laborrate']; ?></small>
                                                </div>
                                                <div class="todo-tasklist-item-text">
                                                    <?php
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
                                                    ?>
                                                </div>
                                                <div class="todo-tasklist-controls pull-left">
                                                    <span class="todo-tasklist-date"><i class="fa fa-calendar"></i> <?php echo date('d M Y', strtotime($event['event_date_start'])); ?> - <?php echo date('d M Y', strtotime($event['event_date_end'])); ?> @ <?php echo $event['event_time']; ?></span>
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
                                    } else {
                                        ?>
                                        <center>
                                            <h3>No events found for today at this location.</h3>
                                        </center>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h3 style="margin-top: 0px">Afternoon Jobs <small class="hidden-sm"><span class="text-danger">|</span> after noon</small></h3>
                                <div class="todo-tasklist">
                                    <?php
                                    $events = mysql_query("SELECT event_name, event_date_start, event_date_end, event_time, event_token, event_status, event_type, event_subtype, event_booking, event_truckfee, event_laborrate FROM fmo_locations_events WHERE event_location_token='".mysql_real_escape_string($_GET['luid'])."'");
                                    if(mysql_num_rows($events) > 0){
                                        $afternoonCount = mysql_num_rows($events);
                                        while($event = mysql_fetch_assoc($events)){
                                            if($event['event_status'] == 0){
                                                continue;
                                            }
                                            $times = explode("to", $event['event_time']);
                                            if(strtotime($times[0]) <= strtotime("12:00PM")){
                                                continue;
                                            }
                                            $start = mysql_fetch_array(mysql_query("SELECT address_address, address_city FROM fmo_locations_events_addresses WHERE address_event_token='".$event['event_token']."' AND address_type=1"));
                                            $end   = mysql_fetch_array(mysql_query("SELECT address_address, address_city FROM fmo_locations_events_addresses WHERE address_event_token='".$event['event_token']."' AND address_type=2"));
                                            ?>
                                            <div class="todo-tasklist-item todo-tasklist-item-border-red load_page col-md-12" data-href="assets/pages/event.php?ev=<?php echo $event['event_token']; ?>" data-page-title="<?php echo $event['event_name']; ?>">
                                                <div class="todo-tasklist-item-title">
                                                    <?php echo $event['event_name']; ?> <span class="text-danger">|</span> <small><i class="fa fa-truck"></i> Trucks: <?php echo $event['event_truckfee']; ?> + <i class="fa fa-users"></i> Crew size: <?php echo $event['event_laborrate']; ?></small>
                                                </div>
                                                <div class="todo-tasklist-item-text">
                                                    <?php
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
                                                    ?>
                                                </div>
                                                <div class="todo-tasklist-controls pull-left">
                                                    <span class="todo-tasklist-date"><i class="fa fa-calendar"></i> <?php echo date('d M Y', strtotime($event['event_date_start'])); ?> - <?php echo date('d M Y', strtotime($event['event_date_end'])); ?> @ <?php echo $event['event_time']; ?></span>
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
                                    } else {
                                        ?>
                                        <center>
                                            <h3>No events found for today at this location.</h3>
                                        </center>
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
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="portlet light">
                    <div class="portlet-title tabbable-line">
                        <div class="caption">
                            <i class="icon-globe font-green-sharp"></i>
                            <span class="caption-subject font-green-sharp bold uppercase">Feeds</span>
                        </div>
                        <ul class="nav nav-tabs">
                            <li class="">
                                <a href="#tab_1_1" data-toggle="tab" aria-expanded="false">
                                    Claims </a>
                            </li>
                            <li class="">
                                <a href="#a_r" data-toggle="tab" aria-expanded="false">
                                    Accounts Recievable </a>
                            </li>
                            <li class="active">
                                <a href="#tab_1_2" data-toggle="tab" aria-expanded="true">
                                    Location Activity </a>
                            </li>
                            <li class="">
                                <a href="#tab_1_3" data-toggle="tab" aria-expanded="false">
                                    Recent Reviews </a>
                            </li>
                        </ul>
                    </div>
                    <div class="portlet-body">
                        <!--BEGIN TABS-->
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab_1_1">
                                <div class="scroller" style="height: 339px;" data-always-visible="1" data-rail-visible="0">
                                    <ul class="feeds">
                                        <li>
                                            <div class="col1">
                                                <div class="cont">
                                                    <div class="cont-col1">
                                                        <div class="label label-sm label-success">
                                                            <i class="fa fa-bell-o"></i>
                                                        </div>
                                                    </div>
                                                    <div class="cont-col2">
                                                        <div class="desc">
                                                            You have 4 pending tasks. <span class="label label-sm label-info">
																Take action <i class="fa fa-share"></i>
																</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col2">
                                                <div class="date">
                                                    Just now
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <a href="javascript:;">
                                                <div class="col1">
                                                    <div class="cont">
                                                        <div class="cont-col1">
                                                            <div class="label label-sm label-success">
                                                                <i class="fa fa-bell-o"></i>
                                                            </div>
                                                        </div>
                                                        <div class="cont-col2">
                                                            <div class="desc">
                                                                New version v1.4 just lunched!
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col2">
                                                    <div class="date">
                                                        20 mins
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <div class="col1">
                                                <div class="cont">
                                                    <div class="cont-col1">
                                                        <div class="label label-sm label-danger">
                                                            <i class="fa fa-bolt"></i>
                                                        </div>
                                                    </div>
                                                    <div class="cont-col2">
                                                        <div class="desc">
                                                            Database server #12 overloaded. Please fix the issue.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col2">
                                                <div class="date">
                                                    24 mins
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="col1">
                                                <div class="cont">
                                                    <div class="cont-col1">
                                                        <div class="label label-sm label-info">
                                                            <i class="fa fa-bullhorn"></i>
                                                        </div>
                                                    </div>
                                                    <div class="cont-col2">
                                                        <div class="desc">
                                                            New order received. Please take care of it.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col2">
                                                <div class="date">
                                                    30 mins
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="col1">
                                                <div class="cont">
                                                    <div class="cont-col1">
                                                        <div class="label label-sm label-success">
                                                            <i class="fa fa-bullhorn"></i>
                                                        </div>
                                                    </div>
                                                    <div class="cont-col2">
                                                        <div class="desc">
                                                            New order received. Please take care of it.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col2">
                                                <div class="date">
                                                    40 mins
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="col1">
                                                <div class="cont">
                                                    <div class="cont-col1">
                                                        <div class="label label-sm label-warning">
                                                            <i class="fa fa-plus"></i>
                                                        </div>
                                                    </div>
                                                    <div class="cont-col2">
                                                        <div class="desc">
                                                            New user registered.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col2">
                                                <div class="date">
                                                    1.5 hours
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="col1">
                                                <div class="cont">
                                                    <div class="cont-col1">
                                                        <div class="label label-sm label-success">
                                                            <i class="fa fa-bell-o"></i>
                                                        </div>
                                                    </div>
                                                    <div class="cont-col2">
                                                        <div class="desc">
                                                            Web server hardware needs to be upgraded. <span class="label label-sm label-default ">
																Overdue </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col2">
                                                <div class="date">
                                                    2 hours
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="col1">
                                                <div class="cont">
                                                    <div class="cont-col1">
                                                        <div class="label label-sm label-default">
                                                            <i class="fa fa-bullhorn"></i>
                                                        </div>
                                                    </div>
                                                    <div class="cont-col2">
                                                        <div class="desc">
                                                            New order received. Please take care of it.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col2">
                                                <div class="date">
                                                    3 hours
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="col1">
                                                <div class="cont">
                                                    <div class="cont-col1">
                                                        <div class="label label-sm label-warning">
                                                            <i class="fa fa-bullhorn"></i>
                                                        </div>
                                                    </div>
                                                    <div class="cont-col2">
                                                        <div class="desc">
                                                            New order received. Please take care of it.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col2">
                                                <div class="date">
                                                    5 hours
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="col1">
                                                <div class="cont">
                                                    <div class="cont-col1">
                                                        <div class="label label-sm label-info">
                                                            <i class="fa fa-bullhorn"></i>
                                                        </div>
                                                    </div>
                                                    <div class="cont-col2">
                                                        <div class="desc">
                                                            New order received. Please take care of it.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col2">
                                                <div class="date">
                                                    18 hours
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="col1">
                                                <div class="cont">
                                                    <div class="cont-col1">
                                                        <div class="label label-sm label-default">
                                                            <i class="fa fa-bullhorn"></i>
                                                        </div>
                                                    </div>
                                                    <div class="cont-col2">
                                                        <div class="desc">
                                                            New order received. Please take care of it.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col2">
                                                <div class="date">
                                                    21 hours
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="col1">
                                                <div class="cont">
                                                    <div class="cont-col1">
                                                        <div class="label label-sm label-info">
                                                            <i class="fa fa-bullhorn"></i>
                                                        </div>
                                                    </div>
                                                    <div class="cont-col2">
                                                        <div class="desc">
                                                            New order received. Please take care of it.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col2">
                                                <div class="date">
                                                    22 hours
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="col1">
                                                <div class="cont">
                                                    <div class="cont-col1">
                                                        <div class="label label-sm label-default">
                                                            <i class="fa fa-bullhorn"></i>
                                                        </div>
                                                    </div>
                                                    <div class="cont-col2">
                                                        <div class="desc">
                                                            New order received. Please take care of it.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col2">
                                                <div class="date">
                                                    21 hours
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="col1">
                                                <div class="cont">
                                                    <div class="cont-col1">
                                                        <div class="label label-sm label-info">
                                                            <i class="fa fa-bullhorn"></i>
                                                        </div>
                                                    </div>
                                                    <div class="cont-col2">
                                                        <div class="desc">
                                                            New order received. Please take care of it.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col2">
                                                <div class="date">
                                                    22 hours
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="col1">
                                                <div class="cont">
                                                    <div class="cont-col1">
                                                        <div class="label label-sm label-default">
                                                            <i class="fa fa-bullhorn"></i>
                                                        </div>
                                                    </div>
                                                    <div class="cont-col2">
                                                        <div class="desc">
                                                            New order received. Please take care of it.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col2">
                                                <div class="date">
                                                    21 hours
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="col1">
                                                <div class="cont">
                                                    <div class="cont-col1">
                                                        <div class="label label-sm label-info">
                                                            <i class="fa fa-bullhorn"></i>
                                                        </div>
                                                    </div>
                                                    <div class="cont-col2">
                                                        <div class="desc">
                                                            New order received. Please take care of it.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col2">
                                                <div class="date">
                                                    22 hours
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="col1">
                                                <div class="cont">
                                                    <div class="cont-col1">
                                                        <div class="label label-sm label-default">
                                                            <i class="fa fa-bullhorn"></i>
                                                        </div>
                                                    </div>
                                                    <div class="cont-col2">
                                                        <div class="desc">
                                                            New order received. Please take care of it.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col2">
                                                <div class="date">
                                                    21 hours
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="col1">
                                                <div class="cont">
                                                    <div class="cont-col1">
                                                        <div class="label label-sm label-info">
                                                            <i class="fa fa-bullhorn"></i>
                                                        </div>
                                                    </div>
                                                    <div class="cont-col2">
                                                        <div class="desc">
                                                            New order received. Please take care of it.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col2">
                                                <div class="date">
                                                    22 hours
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="tab-pane" id="tab_1_2">
                                <div class="scroller" style="height: 290px;" data-always-visible="1" data-rail-visible1="1">
                                    <ul class="feeds">
                                        <li>
                                            <a href="javascript:;">
                                                <div class="col1">
                                                    <div class="cont">
                                                        <div class="cont-col1">
                                                            <div class="label label-sm label-success">
                                                                <i class="fa fa-bell-o"></i>
                                                            </div>
                                                        </div>
                                                        <div class="cont-col2">
                                                            <div class="desc">
                                                                New user registered
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col2">
                                                    <div class="date">
                                                        Just now
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;">
                                                <div class="col1">
                                                    <div class="cont">
                                                        <div class="cont-col1">
                                                            <div class="label label-sm label-success">
                                                                <i class="fa fa-bell-o"></i>
                                                            </div>
                                                        </div>
                                                        <div class="cont-col2">
                                                            <div class="desc">
                                                                New order received
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col2">
                                                    <div class="date">
                                                        10 mins
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <div class="col1">
                                                <div class="cont">
                                                    <div class="cont-col1">
                                                        <div class="label label-sm label-danger">
                                                            <i class="fa fa-bolt"></i>
                                                        </div>
                                                    </div>
                                                    <div class="cont-col2">
                                                        <div class="desc">
                                                            Order #24DOP4 has been rejected. <span class="label label-sm label-danger ">
																Take action <i class="fa fa-share"></i>
																</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col2">
                                                <div class="date">
                                                    24 mins
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <a href="javascript:;">
                                                <div class="col1">
                                                    <div class="cont">
                                                        <div class="cont-col1">
                                                            <div class="label label-sm label-success">
                                                                <i class="fa fa-bell-o"></i>
                                                            </div>
                                                        </div>
                                                        <div class="cont-col2">
                                                            <div class="desc">
                                                                New user registered
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col2">
                                                    <div class="date">
                                                        Just now
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;">
                                                <div class="col1">
                                                    <div class="cont">
                                                        <div class="cont-col1">
                                                            <div class="label label-sm label-success">
                                                                <i class="fa fa-bell-o"></i>
                                                            </div>
                                                        </div>
                                                        <div class="cont-col2">
                                                            <div class="desc">
                                                                New user registered
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col2">
                                                    <div class="date">
                                                        Just now
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;">
                                                <div class="col1">
                                                    <div class="cont">
                                                        <div class="cont-col1">
                                                            <div class="label label-sm label-success">
                                                                <i class="fa fa-bell-o"></i>
                                                            </div>
                                                        </div>
                                                        <div class="cont-col2">
                                                            <div class="desc">
                                                                New user registered
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col2">
                                                    <div class="date">
                                                        Just now
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;">
                                                <div class="col1">
                                                    <div class="cont">
                                                        <div class="cont-col1">
                                                            <div class="label label-sm label-success">
                                                                <i class="fa fa-bell-o"></i>
                                                            </div>
                                                        </div>
                                                        <div class="cont-col2">
                                                            <div class="desc">
                                                                New user registered
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col2">
                                                    <div class="date">
                                                        Just now
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;">
                                                <div class="col1">
                                                    <div class="cont">
                                                        <div class="cont-col1">
                                                            <div class="label label-sm label-success">
                                                                <i class="fa fa-bell-o"></i>
                                                            </div>
                                                        </div>
                                                        <div class="cont-col2">
                                                            <div class="desc">
                                                                New user registered
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col2">
                                                    <div class="date">
                                                        Just now
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;">
                                                <div class="col1">
                                                    <div class="cont">
                                                        <div class="cont-col1">
                                                            <div class="label label-sm label-success">
                                                                <i class="fa fa-bell-o"></i>
                                                            </div>
                                                        </div>
                                                        <div class="cont-col2">
                                                            <div class="desc">
                                                                New user registered
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col2">
                                                    <div class="date">
                                                        Just now
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;">
                                                <div class="col1">
                                                    <div class="cont">
                                                        <div class="cont-col1">
                                                            <div class="label label-sm label-success">
                                                                <i class="fa fa-bell-o"></i>
                                                            </div>
                                                        </div>
                                                        <div class="cont-col2">
                                                            <div class="desc">
                                                                New user registered
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col2">
                                                    <div class="date">
                                                        Just now
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="tab-pane" id="tab_1_3">
                                <div class="scroller" style="height: 290px;" data-always-visible="1" data-rail-visible1="1">
                                    <div class="row">
                                        <div class="col-md-6 user-info">
                                            <img alt="" src="assets/admin/layout/img/avatar.png" class="img-responsive"/>
                                            <div class="details">
                                                <div>
                                                    <a href="javascript:;">
                                                        Robert Nilson </a>
                                                    <span class="label label-sm label-success label-mini">
														Approved </span>
                                                </div>
                                                <div>
                                                    29 Jan 2013 10:45AM
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 user-info">
                                            <img alt="" src="assets/admin/layout/img/avatar.png" class="img-responsive"/>
                                            <div class="details">
                                                <div>
                                                    <a href="javascript:;">
                                                        Lisa Miller </a>
                                                    <span class="label label-sm label-info">
														Pending </span>
                                                </div>
                                                <div>
                                                    19 Jan 2013 10:45AM
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 user-info">
                                            <img alt="" src="assets/admin/layout/img/avatar.png" class="img-responsive"/>
                                            <div class="details">
                                                <div>
                                                    <a href="javascript:;">
                                                        Eric Kim </a>
                                                    <span class="label label-sm label-info">
														Pending </span>
                                                </div>
                                                <div>
                                                    19 Jan 2013 12:45PM
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 user-info">
                                            <img alt="" src="assets/admin/layout/img/avatar.png" class="img-responsive"/>
                                            <div class="details">
                                                <div>
                                                    <a href="javascript:;">
                                                        Lisa Miller </a>
                                                    <span class="label label-sm label-danger">
														In progress </span>
                                                </div>
                                                <div>
                                                    19 Jan 2013 11:55PM
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 user-info">
                                            <img alt="" src="assets/admin/layout/img/avatar.png" class="img-responsive"/>
                                            <div class="details">
                                                <div>
                                                    <a href="javascript:;">
                                                        Eric Kim </a>
                                                    <span class="label label-sm label-info">
														Pending </span>
                                                </div>
                                                <div>
                                                    19 Jan 2013 12:45PM
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 user-info">
                                            <img alt="" src="assets/admin/layout/img/avatar.png" class="img-responsive"/>
                                            <div class="details">
                                                <div>
                                                    <a href="javascript:;">
                                                        Lisa Miller </a>
                                                    <span class="label label-sm label-danger">
														In progress </span>
                                                </div>
                                                <div>
                                                    19 Jan 2013 11:55PM
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 user-info">
                                            <img alt="" src="assets/admin/layout/img/avatar.png" class="img-responsive"/>
                                            <div class="details">
                                                <div>
                                                    <a href="javascript:;">
                                                        Eric Kim </a>
                                                    <span class="label label-sm label-info">
														Pending </span>
                                                </div>
                                                <div>
                                                    19 Jan 2013 12:45PM
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 user-info">
                                            <img alt="" src="assets/admin/layout/img/avatar.png" class="img-responsive"/>
                                            <div class="details">
                                                <div>
                                                    <a href="javascript:;">
                                                        Lisa Miller </a>
                                                    <span class="label label-sm label-danger">
														In progress </span>
                                                </div>
                                                <div>
                                                    19 Jan 2013 11:55PM
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 user-info">
                                            <img alt="" src="assets/admin/layout/img/avatar.png" class="img-responsive"/>
                                            <div class="details">
                                                <div>
                                                    <a href="javascript:;">
                                                        Eric Kim </a>
                                                    <span class="label label-sm label-info">
														Pending </span>
                                                </div>
                                                <div>
                                                    19 Jan 2013 12:45PM
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 user-info">
                                            <img alt="" src="assets/admin/layout/img/avatar.png" class="img-responsive"/>
                                            <div class="details">
                                                <div>
                                                    <a href="javascript:;">
                                                        Lisa Miller </a>
                                                    <span class="label label-sm label-danger">
														In progress </span>
                                                </div>
                                                <div>
                                                    19 Jan 2013 11:55PM
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 user-info">
                                            <img alt="" src="assets/admin/layout/img/avatar.png" class="img-responsive"/>
                                            <div class="details">
                                                <div>
                                                    <a href="javascript:;">
                                                        Eric Kim </a>
                                                    <span class="label label-sm label-info">
														Pending </span>
                                                </div>
                                                <div>
                                                    19 Jan 2013 12:45PM
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 user-info">
                                            <img alt="" src="assets/admin/layout/img/avatar.png" class="img-responsive"/>
                                            <div class="details">
                                                <div>
                                                    <a href="javascript:;">
                                                        Lisa Miller </a>
                                                    <span class="label label-sm label-danger">
														In progress </span>
                                                </div>
                                                <div>
                                                    19 Jan 2013 11:55PM
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--END TABS-->
                    </div>
                </div>
            </div>
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
            </div>
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

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
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

            $('.scroller').slimScroll({
                height: 300
            });

            var pageviews = [
                [1, 2],
                [2, 2],
                [3, 2],
                [4, 3],
                [5, 5],
                [6, 10],
                [7, 15],
                [8, 20],
                [9, 25],
                [10, 30],
                [11, 35],
                [12, 25],
                [13, 15],
                [14, 20],
                [15, 45],
                [16, 50],
                [17, 65],
                [18, 70],
                [19, 85],
                [20, 80],
                [21, 75],
                [22, 80],
                [23, 75],
                [24, 70],
                [25, 65],
                [26, 75],
                [27, 80],
                [28, 85],
                [29, 90],
                [30, 95]
            ];
            var visitors = [
                [1, 2],
                [2, 2],
                [3, 2],
                [4, 6],
                [5, 5],
                [6, 20],
                [7, 25],
                [8, 36],
                [9, 26],
                [10, 38],
                [11, 39],
                [12, 50],
                [13, 51],
                [14, 12],
                [15, 13],
                [16, 14],
                [17, 15],
                [18, 15],
                [19, 16],
                [20, 17],
                [21, 18],
                [22, 19],
                [23, 20],
                [24, 21],
                [25, 14],
                [26, 24],
                [27, 25],
                [28, 26],
                [29, 27],
                [30, 31]
            ];

            var plot = $.plot($("#stats"), [{
                data: pageviews,
                label: "New Bookings",
                lines: {
                    lineWidth: 1,
                },
                shadowSize: 0

            }, {
                data: visitors,
                label: "New Customers",
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
                colors: ["#d12610", "#37b7f3", "#52e136"],
                xaxis: {
                    ticks: 11,
                    tickDecimals: 0,
                    tickColor: "#eee",
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

                        showTooltip(item.pageX, item.pageY, item.series.label + " of " + x + " = " + y);
                    }
                } else {
                    $("#tooltip").remove();
                    previousPoint = null;
                }
            });

            $('.ttm').click(function() {
               var uuid = "<?php echo $location['location_manager']; ?>";
               $.ajax({
                   url: 'assets/app/texting.php?txt=ttm&uuid='+uuid,
                   type: 'POST',
                   data: {
                       msg: $('#ttm_msg').val()
                   },
                   success: function(e){
                       toastr.success("<strong>Logan says:</strong><br/>Text message has been sent to <?php echo name($location['location_manager']); ?>");
                   },
                   error: function(e){
                       toastr.error("<strong>Logan says:</strong><br/>Something bad happened. You messed everything up. Just kidding, try that again.")
                   }
               })
            });
            $('#dashboard-report-range').daterangepicker({
                    opens: (Metronic.isRTL() ? 'right' : 'left'),
                    startDate: "<?php echo date('Y-m-d') ?>",
                    endDate: "<?php echo date('Y-m-d'); ?>",
                    showDropdowns: false,
                    showWeekNumbers: true,
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
                    $('#dashboard-report-range span').html(start.format('YYYY-DD-MM') + ' - ' + end.format('YYYY-DD-MM'));

                }
            );

            $('.rateYoDash').rateYo({
                halfStar: true,
                readOnly: true
            });
            $('#dashboard-report-range').show();
        });
    </script>
    <?php
} else {
    header("Location: ../../../index.php?err=no_access");
}
?>
