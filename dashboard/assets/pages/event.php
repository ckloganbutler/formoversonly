<?php
/**
 * Created by PhpStorm.
 * User: LoganCk
 * Date: 3/3/2017
 * Time: 3:52 AM
 */
session_start();
include '../app/init.php';

if(isset($_SESSION['logged'])){
    mysql_query("UPDATE fmo_users SET user_last_location='".mysql_real_escape_string(basename(__FILE__, '.php')).".php?".$_SERVER['QUERY_STRING']."' WHERE user_token='".mysql_real_escape_string($_SESSION['uuid'])."'");
    $event    = mysql_fetch_array(mysql_query("SELECT event_company_token, event_id, event_token, event_location_token, event_booking, event_user_token, event_name, event_date_start, event_date_end, event_time, event_zip, event_truckfee, event_laborrate, event_countyfee, event_status, event_email, event_phone, event_type, event_subtype, event_additions, event_comments, event_by_user_token, event_adjustment FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($_GET['ev'])."'"));
    $location = mysql_fetch_array(mysql_query("SELECT location_name, location_token, location_max_trucks, location_max_men, location_max_counties, location_creditcard_fee FROM fmo_locations WHERE location_token='".mysql_real_escape_string($event['event_location_token'])."'"));
    $user     = mysql_fetch_array(mysql_query("SELECT user_id, user_fname, user_lname, user_email, user_phone, user_token FROM fmo_users WHERE user_token='".mysql_real_escape_string($event['event_user_token'])."'"));
    $uuidperm = mysql_fetch_array(mysql_query("SELECT user_esc_permissions FROM fmo_users WHERE user_token='".mysql_real_escape_string($_SESSION['uuid'])."'"));

    switch($event['event_status']){
        case 0: $status = "Hot Lead"; break;
        case 1: $status = "New Booking"; break;
        case 2: $status = "Confirmed"; break;
        case 3: $status = "Left Message"; break;
        case 4: $status = "On Hold"; break;
        case 5: $status = "Canceled"; break;
        case 6: $status = "Customer Confirmed"; break;
        case 8: $status = "Completed"; break;
        default: $status = "On Hold"; break;
    }
    ?>
    <style>
        @media print {
            .plain_bol {display: block;}
        }
    </style>
    <div class="page-content">
        <h3 class="page-title">
            <strong><?php echo $event['event_name']; ?></strong> | <small class="hidden-xs">EVENT ID <strong>#<?php echo $event['event_id']; ?></strong> BY <strong><?php echo strtoupper(name($event['event_by_user_token'])); ?></strong></small>

            <?php
            if($_SESSION['group'] == 1){
                ?>
                <div class="btn-group pull-right">
                    <a class="btn red dropdown-toggle" href="javascript:;" data-toggle="dropdown">
                        <i class="fa fa-location-arrow"></i> <span class="hidden-xs">Location: <img alt="" src="assets/global/img/flags/us.png"><strong><?php echo $location['location_name']; ?></strong> <i class="fa fa-angle-down"></i></span>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <?php
                        $findLocations = mysql_query("SELECT location_name, location_token, location_state FROM fmo_locations WHERE location_owner_company_token='".$_SESSION['cuid']."' ORDER BY location_name ASC");
                        if(mysql_num_rows($findLocations) > 0){
                            while($loc = mysql_fetch_assoc($findLocations)){
                                if($location['location_token'] == $loc['location_token']){
                                    continue;
                                }
                                ?>
                                <li>
                                    <a class="change_type" data-id="<?php echo $loc['location_token']; ?>" data-type="eventlocation"><img alt="" src="assets/global/img/flags/us.png"> <?php echo $loc['location_name']; ?> (<?php echo $loc['location_state']; ?>) </a>
                                </li>
                                <?php
                            }
                        }
                        ?>
                    </ul>
                </div>
                <?php
            }
            ?>

            <div class="btn-group pull-right">
                <a class="btn red dropdown-toggle" href="javascript:;" data-toggle="dropdown">
                    <?php
                    $times = explode(" to ", $event['event_time']);
                    if(!empty($times[1])){
                       $times[1] = ' to '.$times[1];
                    }
                    ?>
                    <i class="fa fa-clock-o"></i> <span class="hidden-xs">Start Time: <strong><?php echo $times[0].$times[1]; ?></strong> <i class="fa fa-angle-down"></i></span>
                </a>
                <ul class="dropdown-menu pull-right">
                    <?php
                    $timeOptions = mysql_query("SELECT time_start, time_end FROM fmo_locations_times WHERE time_location_token='".mysql_real_escape_string($event['event_location_token'])."'");
                    if(mysql_num_rows($timeOptions) > 0){
                        while($t = mysql_fetch_assoc($timeOptions)){
                            ?>
                            <li>
                                <a class="change_type" data-id="<?php echo $t['time_start']." to ".$t['time_end']; ?>" data-type="eventtime"><?php echo $t['time_start']." - ".$t['time_end']; ?></a>
                            </li>
                            <?php
                        }
                    }
                    ?>
                </ul>
            </div>
            <a id="dashboard-report-range" class="pull-right tooltips btn red" data-container="body" data-placement="bottom" data-original-title="Change dashboard date range">
                <i class="icon-calendar"></i>&nbsp;
                <span class="bold uppercase hidden-xs">
                    <?php
                    if(date('M d, Y', strtotime($event['event_date_start'])) == date('M d, Y', strtotime($event['event_date_end']))) {
                        echo date('M d, Y', strtotime($event['event_date_start']));
                    } else {
                        echo date('M d, Y', strtotime($event['event_date_start'])); ?> - <?php echo date('M d, Y', strtotime($event['event_date_end']));
                    }
                    ?>
                </span>&nbsp; <i class="fa fa-angle-down"></i>
            </a>
        </h3>
        <div class="page-bar hidden-xs">
            <ul class="page-breadcrumb">
                <li>
                    <i class="fa fa-home"></i>
                    <a class="load_page" data-href="assets/pages/dashboard.php?luid=<?php echo $event['event_location_token']; ?>"><?php echo $location['location_name']; ?></a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li class="hidden-xs">
                    <a class="load_page" data-href="assets/pages/profile.php?uuid=<?php echo $user['user_token']; ?>&luid=<?php echo $event['event_location_token']; ?>" data-page-title="<?php echo $user['user_fname']." ".$user['user_lname']; ?>"><?php echo $user['user_fname']." ".$user['user_lname']; ?></a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                    <a class="load_page" data-href="assets/pages/event.php?ev=<?php echo $_GET['ev']; ?>&luid=<?php echo $_GET['luid']; ?>" data-page-title="<?php echo $event['event_name']; ?>"><?php echo $event['event_name']; ?></a>
                </li>
            </ul>
            <div class="page-toolbar">

                <?php
                if($event['event_booking'] == 1){
                    ?>
                    <div class="pull-right tooltips btn btn-fit-height default green-stripe">
                        <i class="fa fa-credit-card"></i>&nbsp; <span class="thin uppercase">BOOKING FEE PAID <i class="fa fa-arrow-right"></i></span>
                    </div>
                    <?php
                } elseif ($event['event_booking'] == 0){
                    ?>
                    <div class="pull-right tooltips btn btn-fit-height default red-stripe"  data-toggle="modal" href="#booking_fee_modal">
                        <i class="fa fa-credit-card text-danger"></i>&nbsp; <span class="thin uppercase text-danger">BOOKING FEE UNPAID <i class="fa fa-arrow-right"></i> CLICK TO PAY</span>
                    </div>
                    <?php
                }
                ?>
                <div class="pull-right tooltips btn btn-fit-height default yellow-stripe font-red" id="owe_alert" style="display: none;">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <div class="actions btn-set btn-group-justified hidden-xs">
                                    <div class="btn-group ">
                                        <a class="btn default red-stripe dropdown-toggle" href="javascript:;" data-toggle="dropdown">
                                            <i class="fa fa-truck"></i> Trucks: <strong id="truckfee" class="event_truckfee_out edits" data-a="#truckfee" data-b="#laborrate" data-c="#countyfee"><?php echo $event['event_truckfee']; ?></strong> for $<strong id="TF"></strong> <i class="fa fa-angle-down"></i>
                                        </a>
                                        <ul class="dropdown-menu pull-right">
                                            <?php
                                            for($i = 0; $i <= $location['location_max_trucks']; $i++){
                                                ?>
                                                <li>
                                                    <a class="rate_changer" data-value="<?php echo $i; ?>" data-name="event_truckfee"><i class="fa fa-truck font-red"></i> <?php echo $i; ?></a>
                                                </li>
                                                <?php
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                    <div class="btn-group">
                                        <a class="btn default red-stripe dropdown-toggle" href="javascript:;" data-toggle="dropdown">
                                            <i class="fa fa-users"></i> Crewmen: <strong id="laborrate" class="event_laborrate_out edits" data-a="#truckfee" data-b="#laborrate" data-c="#countyfee"><?php echo $event['event_laborrate']; ?></strong> for $<strong id="LR"></strong> <i class="fa fa-angle-down"></i>
                                        </a>
                                        <ul class="dropdown-menu pull-left">
                                            <?php
                                            for($i = 0; $i <= $location['location_max_men']; $i++){
                                                ?>
                                                <li>
                                                    <a class="rate_changer" data-value="<?php echo $i; ?>" data-name="event_laborrate"><i class="fa fa-users font-red"></i> <?php echo $i; ?></a>
                                                </li>
                                                <?php
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                    <div class="btn-group">
                                        <a class="btn default red-stripe dropdown-toggle" href="javascript:;" data-toggle="dropdown">
                                            <i class="fa fa-location-arrow"></i> Counties: <strong id="countyfee" class="event_countyfee_out edits" data-a="#truckfee" data-b="#laborrate" data-c="#countyfee"><?php echo $event['event_countyfee']; ?></strong> for $<strong id="CF"></strong> <i class="fa fa-angle-down"></i>
                                        </a>
                                        <ul class="dropdown-menu pull-left">
                                            <?php
                                            for($i = 0; $i <= $location['location_max_counties']; $i++){
                                                ?>
                                                <li>
                                                    <a class="rate_changer" data-value="<?php echo $i; ?>" data-name="event_countyfee"><i class="fa fa-location-arrow font-red"></i> <?php echo $i; ?></a>
                                                </li>
                                                <?php
                                            }
                                            ?>
                                        </ul>
                                    </div>

                                    <div class="btn-group hidden-xs hidden-sm">
                                        <button class="btn red mbol" data-toggle="modal" href="#print_bol" data-event="<?php echo $event['event_token']; ?>">
                                            <i class="fa fa-print"></i> Print BOL</span>
                                        </button>
                                    </div>

                                    <div class="btn-group">
                                        <a class="btn default red-stripe dropdown-toggle" href="javascript:;" data-toggle="dropdown">
                                            <i class="fa fa-tag"></i> Event Type: <strong><?php echo $event['event_type']; ?></strong> <i class="fa fa-angle-down"></i>
                                        </a>
                                        <ul class="dropdown-menu pull-right">
                                            <?php
                                            $types = mysql_query("SELECT eventtype_name FROM fmo_locations_eventtypes WHERE eventtype_location_token='".mysql_real_escape_string($event['event_location_token'])."'");
                                            if(mysql_num_rows($types) > 0){
                                                while($type = mysql_fetch_assoc($types)){
                                                    ?>
                                                    <li>
                                                        <a class="change_type" data-id="<?php echo $type['eventtype_name']; ?>" data-type="eventtype"><?php echo $type['eventtype_name']; ?></a>
                                                    </li>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </ul>
                                    </div>

                                    <div class="btn-group">
                                        <a class="btn default red-stripe dropdown-toggle" href="javascript:;" data-toggle="dropdown">
                                            <i class="fa fa-tags"></i> Sub Type: <strong><?php echo $event['event_subtype']; ?></strong> <i class="fa fa-angle-down"></i>
                                        </a>
                                        <ul class="dropdown-menu pull-right">
                                            <?php
                                            $subtypes = mysql_query("SELECT subtype_id, subtype_name FROM fmo_locations_subtypes WHERE subtype_location_token='".mysql_real_escape_string($event['event_location_token'])."'");
                                            if(mysql_num_rows($types) > 0){
                                                while($subtype = mysql_fetch_assoc($subtypes)){
                                                    ?>
                                                    <li>
                                                        <a class="change_type" data-id="<?php echo $subtype['subtype_name']; ?>" data-type="subtype"><?php echo $subtype['subtype_name']; ?></a>
                                                    </li>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </ul>
                                    </div>

                                    <div class="btn-group">
                                        <a class="btn default red-stripe dropdown-toggle hidden-sm" href="javascript:;" data-toggle="dropdown">
                                            <i class="fa fa-info-o"></i> Status: <strong><?php echo $status; ?></strong> <i class="fa fa-angle-down"></i>
                                        </a>
                                        <ul class="dropdown-menu pull-right">
                                            <?php
                                            if($event['event_status'] != 1){
                                                ?>
                                                <li>
                                                    <a class="change_type" data-id="1" data-type="status">Change to New Booking</a>
                                                </li>
                                                <?php
                                            }
                                            if($event['event_status'] != 2){
                                                ?>
                                                <li>
                                                    <a class="change_type" data-id="2" data-type="status">Change to Confirmed</a>
                                                </li>
                                                <?php
                                            }
                                            if($event['event_status'] != 3){
                                                ?>
                                                <li>
                                                    <a class="change_type" data-id="3" data-type="status">Change to Left Message</a>
                                                </li>
                                                <?php
                                            }
                                            if($event['event_status'] != 4){
                                                ?>
                                                <li>
                                                    <a class="change_type" data-id="4" data-type="status">Change to On Hold</a>
                                                </li>
                                                <?php
                                            }
                                            if($event['event_status'] != 5){
                                                ?>
                                                <li>
                                                    <a data-toggle="modal" href="#cancel_event">Change to Cancelled</a>
                                                </li>
                                                <?php
                                            }
                                            if($event['event_status'] != 8){
                                                ?>
                                                <li>
                                                    <a class="change_type" data-id="8" data-type="status">Change to Completed</a>
                                                </li>
                                                <?php
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="portlet-body" style="padding-top: 0px;">
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="portlet">
                                    <div class="portlet-title tabbable-line">
                                        <ul class="nav nav-tabs nav-justified">
                                            <li class="active">
                                                <a href="#event" data-toggle="tab" aria-expanded="true" style="color: black;">
                                                    Event Information</a>
                                            </li>
                                            <li class="">
                                                <a href="#customer" data-toggle="tab" aria-expanded="false" style="color: black;">
                                                    Customer Information</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="portlet-body">
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="event">
                                                <div class="row static-info">
                                                    <div class="col-md-5 name">
                                                        Event Name & ID:
                                                    </div>
                                                    <div class="col-md-7 value">
                                                        <a class="ev" style="color:#333333" data-name="event_name" data-pk="<?php echo $event['event_token']; ?>" data-type="text" data-placement="right" data-title="Enter new event name.." data-url="assets/app/update_settings.php?update=event_fly">
                                                            <?php echo $event['event_name']; ?>
                                                        </a> (#0<?php echo $event['event_id']; ?>)
                                                    </div>
                                                </div>
                                                <div class="row static-info">
                                                    <div class="col-md-5 name">
                                                        Phone:
                                                    </div>
                                                    <div class="col-md-7 value">
                                                        <a class="ev" style="color:#333333" data-name="event_phone" data-pk="<?php echo $event['event_token']; ?>" data-type="text" data-placement="right" data-title="Enter new event phone number.." data-url="assets/app/update_settings.php?update=event_fly">
                                                            <?php echo clean_phone($event['event_phone']);  ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="row static-info">
                                                    <div class="col-md-5 name">
                                                        Email:
                                                    </div>
                                                    <div class="col-md-7 value">
                                                        <a class="ev" style="color:#333333" data-name="event_email" data-pk="<?php echo $event['event_token']; ?>" data-type="email" data-placement="right" data-title="Enter new event email.." data-url="assets/app/update_settings.php?update=event_fly">
                                                            <?php echo $event['event_email']; ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <hr/>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <a href="javascript:;" class="btn red edit" data-edit="ev" data-reload="">
                                                            <i class="fa fa-pencil"></i> <span class="hidden-sm hidden-md">Edit</span> </a>
                                                        <div class="btn-group pull-right">
                                                            <button class="btn <?php if(!empty($extra['packing'])){ echo "green"; }else{echo "green-stripe";} ?> pull-right addition">
                                                                <i class="fa <?php if(!empty($extra['packing'])){ echo "fa-check"; }else{echo "fa-times";} ?>"></i>
                                                                Packing
                                                                <input type="checkbox" name="addition[]" id="packing" value="packing" <?php if(!empty($extra['packing'])){ echo "checked"; } ?> class="hidden hidden-checkbox" autocomplete="off">
                                                            </button>
                                                        </div>
                                                        <div class="btn-group pull-right">
                                                            <button class="btn <?php if(!empty($extra['materials'])){ echo "green"; }else{echo "green-stripe";} ?> pull-right addition">
                                                                <i class="fa <?php if(!empty($extra['materials'])){ echo "fa-check"; }else{echo "fa-times";} ?>"></i>
                                                                Materials
                                                                <input type="checkbox" name="addition[]" id="materials" value="materials" <?php if(!empty($extra['materials'])){ echo "checked"; } ?> class="hidden hidden-checkbox" autocomplete="off">
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="customer">
                                                <div class="row static-info">
                                                    <div class="col-md-5 name">
                                                        Customer Name & ID:
                                                    </div>
                                                    <div class="col-md-7 value">
                                                        <?php echo $user['user_fname']." ".$user['user_lname']; ?> (#<?php echo $user['user_id']; ?>)
                                                    </div>
                                                </div>
                                                <div class="row static-info">
                                                    <div class="col-md-5 name">
                                                        Phone:
                                                    </div>
                                                    <div class="col-md-7 value">
                                                        <?php echo clean_phone($user['user_phone']); ?>
                                                    </div>
                                                </div>
                                                <div class="row static-info">
                                                    <div class="col-md-5 name">
                                                        Email:
                                                    </div>
                                                    <div class="col-md-7 value">
                                                        <?php echo $user['user_email']; ?>
                                                    </div>
                                                </div>
                                                <hr/>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <a href="javascript:;" class="btn red edit" data-edit="ci" data-reload="">
                                                            <i class="fa fa-pencil"></i> <span class="hidden-sm hidden-md">Edit</span> </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="portlet">
                                    <?php
                                    $additions = explode("|", $event['event_additions']);
                                    $add       = 0;
                                    foreach($additions as $ck){
                                        $add++;
                                        $extra[$ck] = $ck;
                                    }
                                    ?>
                                    <div class="portlet-title tabbable-line">
                                        <ul class="nav nav-tabs nav-justified">
                                            <li class="active">
                                                <a href="#additions" data-toggle="tab" aria-expanded="true" style="color: black;">
                                                  BOL Comments & Additional Items
                                                    <span class="badge badge-danger"> <?php echo $add - 1; ?> </span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="portlet-body">
                                        <div class="tab-content">
                                            <textarea placeholder="BOL comments (psst! the comment you're about to type will automatically save when you're done typing." class="form-control bol_comments" style="height: 80px;"><?php echo $event['event_comments']; ?></textarea>
                                            <span style="margin-top: -23px; margin-right: 10px;" class="bol_countdown pull-right"></span>
                                            <style type="text/css">
                                                .check {
                                                    opacity:0.5;
                                                    color:#996;
                                                }
                                            </style>
                                            <hr/>
                                            <div class="tab-pane active" id="additions">
                                                <div class="btn-group-justified">
                                                    <div class="btn-group">
                                                        <button class="btn <?php if(!empty($extra['safe'])){ echo "green"; }else{echo "green-stripe";} ?> pull-right addition">
                                                            <i class="fa <?php if(!empty($extra['safe'])){ echo "fa-check"; }else{echo "fa-times";} ?>"></i>
                                                            Safe
                                                            <input type="checkbox" name="addition[]" id="safe" value="safe" <?php if(!empty($extra['safe'])){ echo "checked"; } ?> class="hidden hidden-checkbox" autocomplete="off">
                                                        </button>
                                                    </div>
                                                    <div class="btn-group">
                                                        <button class="btn <?php if(!empty($extra['play_set'])){ echo "green"; }else{echo "green-stripe";} ?> pull-right addition">
                                                            <i class="fa <?php if(!empty($extra['play_set'])){ echo "fa-check"; }else{echo "fa-times";} ?>"></i>
                                                            Play Set
                                                            <input type="checkbox" name="addition[]" id="play_set" value="play_set" <?php if(!empty($extra['play_set'])){ echo "checked"; } ?> class="hidden hidden-checkbox" autocomplete="off">
                                                        </button>
                                                    </div>
                                                    <div class="btn-group">
                                                        <button class="btn <?php if(!empty($extra['pool_table'])){ echo "green"; }else{echo "green-stripe";} ?> pull-right addition">
                                                            <i class="fa <?php if(!empty($extra['pool_table'])){ echo "fa-check"; }else{echo "fa-times";} ?>"></i>
                                                            Pool Table
                                                            <input type="checkbox" name="addition[]" id="pool_table" value="pool_table" <?php if(!empty($extra['pool_table'])){ echo "checked"; } ?> class="hidden hidden-checkbox" autocomplete="off">
                                                        </button>
                                                    </div>
                                                    <div class="btn-group">
                                                        <button class="btn <?php if(!empty($extra['piano'])){ echo "green"; } else {echo "green-stripe";} ?> pull-right addition">
                                                            <i class="fa <?php if(!empty($extra['piano'])){ echo "fa-check"; } else {echo "fa-times";} ?>"></i>
                                                            Piano
                                                            <input type="checkbox" name="addition[]" id="piano" value="piano" <?php if(!empty($extra['piano'])){ echo "checked"; } ?> class="hidden hidden-checkbox" autocomplete="off">
                                                        </button>
                                                    </div>
                                                    <div class="btn-group">
                                                        <button class="btn <?php if(!empty($extra['hot_tub'])){echo "green";} else {echo "green-stripe";} ?> pull-right addition">
                                                            <i class="fa <?php if(!empty($extra['hot_tub'])){echo "fa-check";}else{echo "fa-times";} ?>"></i>
                                                            Hot Tub
                                                            <input type="checkbox" name="addition[]" id="hot_tub" value="hot_tub" <?php if(!empty($extra['hot_tub'])){ echo "checked"; } ?> class="hidden hidden-checkbox" autocomplete="off">
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-lg-12 col-md-12 col-sm-12">
                                <div class="btn-group btn-group-justified">
                                    <a class="btn default red-stripe" data-toggle="modal" href="#comments_only">
                                        <i class="fa fa-comments"></i>
                                        <span class="hidden-480"><strong>Comments</strong> (<?php echo mysql_num_rows(mysql_query("SELECT comment_id FROM fmo_locations_events_comments WHERE comment_event_token='".mysql_real_escape_string($event['event_token'])."'")); ?>)</span>
                                    </a>
                                    <a class="btn default red-stripe" data-toggle="modal" href="#estimates_only">
                                        <i class="fa fa-usd"></i>
                                        <span class="hidden-480"><strong>Estimates (<?php echo mysql_num_rows(mysql_query("SELECT estimate_id, estimate_name, estimate_type FROM fmo_locations_events_estimates WHERE estimate_event_token='".mysql_real_escape_string($event['event_token'])."'")); ?>)</strong></span>
                                    </a>
                                    <a class="btn default red" data-toggle="modal" href="#tools_only">
                                        <i class="fa fa-wrench"></i>
                                        <span class="hidden-480"><strong>Event tools</strong> </span>
                                    </a>
                                    <a class="btn default red-stripe" data-toggle="modal" href="#claims_only">
                                        <i class="fa fa-exclamation-triangle"></i>
                                        <span class="hidden-480"><strong>Customer claims</strong> (<?php echo mysql_num_rows(mysql_query("SELECT ticket_id FROM fmo_locations_tickets WHERE ticket_event_token='".mysql_real_escape_string($event['event_token'])."'")); ?>)</span>
                                    </a>
                                    <a class="btn default red-stripe ratings" data-toggle="modal" href="#reviews_only">
                                        <i class="fa fa-book"></i>
                                        <span class="hidden-480"><strong>Customer reviews</strong> (<?php echo mysql_num_rows(mysql_query("SELECT review_id FROM fmo_locations_events_reviews WHERE review_event_token='".mysql_real_escape_string($event['event_token'])."'")); ?>)</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="portlet light">
                    <div class="portlet-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div id="gmap_basic" class="gmaps" style="height: 450px;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="scroller" style="height: 450px;" data-always-visible="1" data-rail-visible1="1">
                                    <div class="portlet">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <strong>Pick up</strong> location(s)
                                            </div>
                                            <div class="actions">
                                                <a class="btn default red-stripe" data-toggle="modal" href="#draggable" data-location-type="1">
                                                    <i class="fa fa-plus"></i>
                                                    <span class="hidden-480">Add pickup</span>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="portlet-body">
                                            <?php
                                            $pickups = mysql_query("SELECT address_id, address_address, address_suite, address_city, address_state, address_zip, address_closest_intersection, address_county, address_stairs, address_distance, address_comments, address_bedrooms, address_garage, address_special, address_square_footage FROM fmo_locations_events_addresses WHERE address_event_token='".mysql_real_escape_string($event['event_token'])."' AND address_type=1");
                                            if(mysql_num_rows($pickups) > 0){
                                                $pk = 0;
                                                while($pickup = mysql_fetch_assoc($pickups)){
                                                    $pk++
                                                    ?>
                                                    <div id="pickup_h_<?php echo $pickup['address_id']; ?>" class="panel-group r_l_<?php echo $pickup['address_id']; ?>">
                                                        <div class="panel panel-default">
                                                            <div class="panel-heading">
                                                                <div class="actions pull-right" style="margin-top: -6px; margin-right: -9px">
                                                                    <a href="javascript:;" class="btn btn-default btn-sm edit" data-edit="pu_<?php echo $pickup['address_id']; ?>" data-reload="">
                                                                        <i class="fa fa-pencil"></i> <span class="hidden-sm hidden-md hidden-xs">Edit</span> </a>
                                                                    <a href="javascript:;" class="btn btn-default red-stripe btn-sm del_location" data-id="<?php echo $pickup['address_id']; ?>">
                                                                        <i class="fa fa-times"></i> <span class="hidden-sm hidden-md hidden-xs">Delete</span> </a>
                                                                </div>
                                                                <div class="caption">
                                                                    <h4 class="panel-title">
                                                                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#pickup_h_<?php echo $pickup['address_id']; ?>" href="#pickup_<?php echo $pickup['address_id']; ?>" aria-expanded="false"><strong><?php echo $pickup['address_address']; ?>, <?php echo $pickup['address_city']; ?>, <?php echo $pickup['address_state']; ?> <?php echo $pickup['address_zip']; ?>, Suite: <?php echo $pickup['address_suite']; ?></strong></a>
                                                                    </h4>
                                                                </div>
                                                            </div>
                                                            <div id="pickup_<?php echo $pickup['address_id']; ?>" class="panel-collapse collapse" aria-expanded="true" style="height: 0px;">
                                                                <div class="panel-body">
                                                                    <address>
                                                                        <strong>Physical Address</strong><br>
                                                                        <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_address" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new street address.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                            <?php echo $pickup['address_address']; ?>
                                                                        </a><br/>
                                                                        <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_city" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new city.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                            <?php echo $pickup['address_city']; ?>
                                                                        </a>,
                                                                        <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_state" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Select new state.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                            <?php echo $pickup['address_state']; ?>
                                                                        </a>
                                                                        <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_zip" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new zipcode.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                            <?php echo $pickup['address_zip']; ?>
                                                                        </a><br/>
                                                                        <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_county" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new county.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                            <?php echo $pickup['address_county']; ?>
                                                                        </a><br/>
                                                                        Apt: <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_suite" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new suite.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                            <?php echo $pickup['address_suite']; ?>
                                                                        </a>
                                                                    </address>
                                                                    <address>
                                                                        Closest intersection:
                                                                        <strong>
                                                                            <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_closest_intersection" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new closest intersection.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                                <?php echo $pickup['address_closest_intersection']; ?>
                                                                            </a><br/>
                                                                        </strong>

                                                                        Stairs:
                                                                        <strong>
                                                                            <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_stairs" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new closest intersection.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                                <?php echo $pickup['address_stairs']; ?>
                                                                            </a><br/>
                                                                        </strong>

                                                                        Parking Distance:
                                                                        <strong>
                                                                            <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_distance" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new distance.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                                <?php echo $pickup['address_distance']; ?>
                                                                            </a><br/>
                                                                        </strong>

                                                                        Bedrooms:
                                                                        <strong>
                                                                            <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_bedrooms" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new distance.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                                <?php echo $pickup['address_bedrooms']; ?>
                                                                            </a><br/>
                                                                        </strong>

                                                                        Garage:
                                                                        <strong>
                                                                            <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_garage" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new distance.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                                <?php echo $pickup['address_garage']; ?>
                                                                            </a><br/>
                                                                        </strong>

                                                                        Special Item(s):
                                                                        <strong>
                                                                            <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_special" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new distance.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                                <?php echo $pickup['address_special']; ?>
                                                                            </a><br/>
                                                                        </strong>

                                                                        Square Footage:
                                                                        <strong>
                                                                            <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_square_footage" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new distance.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                                <?php echo $pickup['address_square_footage']; ?>
                                                                            </a><br/>
                                                                        </strong>
                                                                    </address>
                                                                    <address>
                                                                        Comments: <br/>
                                                                        <strong>
                                                                            <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_comments" data-pk="<?php echo $pickup['address_id']; ?>" data-type="textarea" data-placement="right" data-title="Enter new comments.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                                <?php echo $pickup['address_comments']; ?>
                                                                            </a>
                                                                        </strong>
                                                                    </address>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                    $pk_strt  = $pickup['address_address'];
                                                    $pk_state = $pickup['address_state'];
                                                    $pk_city  = $pickup['address_city'];
                                                    $pk_zip   = $pickup['address_zip'];
                                                }
                                            } else {
                                                ?>
                                                <div class="alert alert-warning alert-dismissable">
                                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                                                    <strong>No pickup locations!</strong> Add a new location to see them appear here.
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="portlet">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <strong>Destination</strong> location(s)
                                            </div>
                                            <div class="actions">
                                                <a class="btn default red-stripe" data-toggle="modal" href="#draggable" data-location-type="2">
                                                    <i class="fa fa-plus"></i>
                                                    <span class="hidden-480">Add destination</span>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="portlet-body">
                                            <?php
                                            $dests = mysql_query("SELECT address_id, address_address, address_suite, address_city, address_state, address_zip, address_closest_intersection, address_county, address_stairs, address_distance, address_comments FROM fmo_locations_events_addresses WHERE address_event_token='".mysql_real_escape_string($event['event_token'])."' AND address_type=2");
                                            if(mysql_num_rows($dests) > 0){
                                                $pk = 0;
                                                while($dest = mysql_fetch_assoc($dests)){
                                                    $pk++
                                                    ?>
                                                    <div id="dest_h_<?php echo $dest['address_id']; ?>" class="panel-group r_l_<?php echo $dest['address_id']; ?>">
                                                        <div class="panel panel-default">
                                                            <div class="panel-heading">
                                                                <div class="actions pull-right" style="margin-top: -6px; margin-right: -9px">
                                                                    <a href="javascript:;" class="btn btn-default btn-sm edit" data-edit="ds_<?php echo $dest['address_id']; ?>" data-reload="">
                                                                        <i class="fa fa-pencil"></i> <span class="hidden-sm hidden-md hidden-xs">Edit</span> </a>
                                                                    <a href="javascript:;" class="btn btn-default red-stripe btn-sm del_location" data-id="<?php echo $dest['address_id']; ?>">
                                                                        <i class="fa fa-times"></i> <span class="hidden-sm hidden-md hidden-xs">Delete</span> </a>
                                                                </div>
                                                                <div class="caption">
                                                                    <h4 class="panel-title">
                                                                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#dest_h_<?php echo $dest['address_id']; ?>" href="#dest_<?php echo $dest['address_id']; ?>" aria-expanded="false"><strong><?php echo $dest['address_address']; ?>, <?php echo $dest['address_city']; ?>, <?php echo $dest['address_state']; ?> <?php echo $dest['address_zip']; ?>, Suite: <?php echo $dest['address_suite']; ?></strong></a>
                                                                    </h4>
                                                                </div>
                                                            </div>
                                                            <div id="dest_<?php echo $dest['address_id']; ?>" class="panel-collapse collapse" aria-expanded="true" style="height: 0px;">
                                                                <div class="panel-body">
                                                                    <address>
                                                                        <strong>Physical Address</strong><br>
                                                                        <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_address" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new street address.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                            <?php echo $dest['address_address']; ?>
                                                                        </a><br/>
                                                                        <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_city" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new city.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                            <?php echo $dest['address_city']; ?>
                                                                        </a>,
                                                                        <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_state" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Select new state.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                            <?php echo $dest['address_state']; ?>
                                                                        </a>
                                                                        <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_zip" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new zipcode.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                            <?php echo $dest['address_zip']; ?>
                                                                        </a><br/>
                                                                        <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_county" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new county.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                            <?php echo $dest['address_county']; ?>
                                                                        </a><br/>
                                                                        Apt:  <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_suite" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new county.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                            <?php echo $dest['address_suite']; ?>
                                                                        </a>
                                                                    </address>
                                                                    <address>
                                                                        Closest intersection:
                                                                        <strong>
                                                                            <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_closest_intersection" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new closest intersection.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                                <?php echo $dest['address_closest_intersection']; ?>
                                                                            </a><br/>
                                                                        </strong>

                                                                        Stairs:
                                                                        <strong>
                                                                            <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_stairs" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new closest intersection.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                                <?php echo $dest['address_stairs']; ?>
                                                                            </a><br/>
                                                                        </strong>

                                                                        Parking Distance:
                                                                        <strong>
                                                                            <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_distance" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new distance.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                                <?php echo $dest['address_distance']; ?>
                                                                            </a><br/>
                                                                        </strong>
                                                                    </address>
                                                                    <address>
                                                                        Comments: <br/>
                                                                        <strong>
                                                                            <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_comments" data-pk="<?php echo $dest['address_id']; ?>" data-type="textarea" data-placement="right" data-title="Enter new comments.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                                <?php echo $dest['address_comments']; ?>
                                                                            </a>
                                                                        </strong>
                                                                    </address>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                    $ds_strt  = $dest['address_address'];
                                                    $ds_state = $dest['address_state'];
                                                    $ds_city  = $dest['address_city'];
                                                    $ds_zip   = $dest['address_zip'];
                                                }
                                            } else {
                                                ?>
                                                <div class="alert alert-warning alert-dismissable">
                                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                                                    <strong>No destination locations!</strong> Add a new location to see them appear here.
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="portlet">
                                        <div class="portlet-body">
                                            <div id="results-map-panel">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="portlet light">
                    <div class="portlet-body">
                        <div class="row" style="margin-top: 15px;">
                            <div class="col-md-12">
                                <div class="tabbable-custom nav-justified">
                                    <ul class="nav nav-tabs nav-justified">
                                        <?php
                                        $timelines = mysql_num_rows(mysql_query("SELECT timeline_id FROM fmo_locations_events_timelines WHERE timeline_event_token='".mysql_real_escape_string($event['event_token'])."'"));
                                        $laborers = mysql_num_rows(mysql_query("SELECT laborer_id FROM fmo_locations_events_laborers WHERE laborer_event_token='".mysql_real_escape_string($event['event_token'])."'"));
                                        $documents = mysql_num_rows(mysql_query("SELECT document_id FROM fmo_locations_events_documents WHERE document_event_token='".mysql_real_escape_string($event['event_token'])."'"));
                                        $items = mysql_num_rows(mysql_query("SELECT item_id FROM fmo_locations_events_items WHERE item_event_token='".mysql_real_escape_string($event['event_token'])."'"));
                                        $assets = mysql_num_rows(mysql_query("SELECT asset_id FROM fmo_locations_events_assets WHERE asset_event_token='".mysql_real_escape_string($event['event_token'])."'"));
                                        ?>
                                        <li class="active">
                                            <a href="#comments" data-toggle="tab"> Timeline
                                                <?php
                                                if($timelines > 0){
                                                    ?>
                                                    <span class="badge badge-danger"> <?php echo $timelines; ?> </span>
                                                    <?php
                                                }
                                                ?>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#documents" data-toggle="tab">Documents
                                                <?php
                                                if($documents > 0){
                                                    ?>
                                                    <span class="badge badge-danger"> <?php echo $documents; ?> </span>
                                                    <?php
                                                }
                                                ?>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#labor" data-toggle="tab">
                                                Laborers
                                                <?php
                                                    if($laborers > 0){
                                                        ?>
                                                        <span class="badge badge-danger"> <?php echo $laborers; ?> </span>
                                                        <?php
                                                    }
                                                ?> /
                                                Assets
                                                <?php
                                                if($assets > 0){
                                                    ?>
                                                    <span class="badge badge-danger"> <?php echo $laborers; ?> </span>
                                                    <?php
                                                }
                                                ?>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#invoices" data-toggle="tab">Invoicing
                                                <?php
                                                if($items > 0){
                                                    ?>
                                                    <span class="badge badge-danger"> <?php echo $items; ?> </span>
                                                    <?php
                                                }
                                                ?>
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="comments">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="scroller2" style="height: 290px;" data-always-visible="1" data-rail-visible1="1">
                                                        <div class="portlet">
                                                            <div class="portlet-title">
                                                                <div class="caption">
                                                                    <i class="fa fa-cogs"></i>Timeline <small><span class="font-red">|</span>Records and tools for this event.</small>
                                                                </div>
                                                                <div class="actions">

                                                                </div>
                                                            </div>
                                                            <div class="portlet-body">
                                                                <div class="table-container">
                                                                    <div class="table-actions-wrapper">
                                                                    <span>

                                                                    </span>
                                                                        <select class="table-group-action-input form-control input-inline input-small input-sm">
                                                                            <option value="">Select...</option>
                                                                            <option value="Delete" class="font-red">Delete</option>
                                                                        </select>
                                                                        <button class="btn btn-sm red table-group-action-submit"><i class="fa fa-check"></i> Submit</button>
                                                                    </div>
                                                                    <form role="form" id="add_service_rate">
                                                                        <table class="table table-striped table-hover datatable" data-src="assets/app/api/event.php?type=timeline&ev=<?php echo $_GET['ev']; ?>">
                                                                            <thead>
                                                                            <tr role="row" class="heading">
                                                                                <th width="12%">
                                                                                    Record Timestamp
                                                                                </th>
                                                                                <th width="14%">
                                                                                    Record Type
                                                                                </th>
                                                                                <th>
                                                                                    Record Details
                                                                                </th>
                                                                                <th width="12%">
                                                                                    Record Creator
                                                                                </th>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody>

                                                                            </tbody>
                                                                        </table>
                                                                    </form>
                                                                </div>
                                                                <p><span class="text-info"><i class="fa fa-info"></i></span> | Click on table headers to refresh the information within the table.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- End: life time stats -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="documents">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="scroller2" style="height: 290px;" data-always-visible="1" data-rail-visible1="1">
                                                        <div class="portlet">
                                                            <div class="portlet-title">
                                                                <div class="caption">
                                                                    <i class="fa fa-cogs"></i>Documents & Photos<small><span class="font-red">|</span> Upload files that regard the event here.</small>
                                                                </div>
                                                                <div class="actions">
                                                                    <a class="btn default red-stripe show_form" data-show="#add_document">
                                                                        <i class="fa fa-plus"></i>
                                                                        <span class="hidden-480">Upload new document or photo </span>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <div class="portlet-body">
                                                                <div class="table-container">
                                                                    <form role="form" id="add_documents">
                                                                        <table class="table table-striped table-hover datatable" id="docs" data-src="assets/app/api/event.php?type=documents&ev=<?php echo $event['event_token']; ?>">
                                                                            <thead>
                                                                            <tr role="row" class="heading">
                                                                                <th width="18%">
                                                                                    File Thumbnail
                                                                                </th>
                                                                                <th>
                                                                                    File Description
                                                                                </th>
                                                                                <th width="8%">
                                                                                    Actions
                                                                                </th>
                                                                            </tr>
                                                                            <tr role="row" class="filter" style="display: none;" id="add_document">
                                                                                <td><input type="file" class="form-control form-filter input-sm" name="file"></td>
                                                                                <td>
                                                                                    <div class="form-group">
                                                                                        <div class="col-md-12">
                                                                                            <input type="text" class="form-control form-filter input-sm" name="file_desc">
                                                                                        </div>
                                                                                    </div>
                                                                                </td>
                                                                                <td>
                                                                                    <button type="button" class="btn btn-sm red margin-bottom add_document"><i class="fa fa-download"></i> Save</button>
                                                                                </td>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody>

                                                                            </tbody>
                                                                        </table>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- End: life time stats -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="labor">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="scroller2" style="height: 290px;" data-always-visible="1" data-rail-visible1="1">
                                                        <div class="portlet">
                                                            <div class="portlet-title">
                                                                <div class="caption">
                                                                    <i class="fa fa-cogs"></i>Laborers <small><span class="font-red">|</span> Currently tracked laborers that have been assigned to this job.</small>
                                                                </div>
                                                                <div class="actions">
                                                                    <a class="btn default red-stripe show_form" data-show="#new_labor">
                                                                        <i class="fa fa-plus"></i>
                                                                        <span class="hidden-480">Add new laborer</span>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <div class="portlet-body">
                                                                <div class="table-container">
                                                                    <form role="form" id="add_laborer">
                                                                        <table class="table table-striped table-hover datatable" id="laborers" data-src="assets/app/api/event.php?type=labor&ev=<?php echo $_GET['ev']; ?>">
                                                                            <thead>
                                                                            <tr role="row" class="heading">
                                                                                <th width="12%">
                                                                                    Role
                                                                                </th>
                                                                                <th width="25%">
                                                                                    Laborer Name
                                                                                </th>
                                                                                <th>
                                                                                    Laborer Wage
                                                                                </th>
                                                                                <th>
                                                                                    Paid Hours
                                                                                </th>
                                                                                <th>
                                                                                    Tips/Other Pay
                                                                                </th>
                                                                                <th width="12%">
                                                                                    Added By
                                                                                </th>
                                                                                <th width="12%">
                                                                                    Actions
                                                                                </th>
                                                                            </tr>
                                                                            <tr role="row" class="filter" style="display: none;" id="new_labor">
                                                                                <td>
                                                                                    <select class="form-control input-sm" name="role">
                                                                                        <option disabled selected value="">Select one..</option>
                                                                                        <option value="CREW LEADER">Crew Leader</option>
                                                                                        <option value="CREWMAN">Crewman</option>
                                                                                    </select>
                                                                                    <input type="text" class="hidden" value="<?php echo $event['event_date_start']; ?>" name="date">
                                                                                    <input type="text" class="hidden" value="<?php echo $event['event_time']; ?>" name="time">
                                                                                </td>
                                                                                <td>
                                                                                    <select class="form-control input-sm laborers" name="laborer">
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
                                                                                </td>
                                                                                <td><input type="text" class="form-control input-sm" readonly value="$__.__"></td>
                                                                                <td><input type="number" class="form-control input-sm" name="hp"></td>
                                                                                <td><input type="text" class="form-control input-sm" name="tip"></td>
                                                                                <td><input readonly class="form-control input-sm" value="<?php echo name($_SESSION['uuid']); ?>"</td>
                                                                                <td>
                                                                                    <div class="margin-bottom-5">
                                                                                        <button type="button" class="btn btn-sm red margin-bottom add_laborer"><i class="fa fa-download"></i> Save</button> <button type="button" class="btn btn-sm default filter-cancel"><i class="fa fa-times"></i> Reset</button>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody>

                                                                            </tbody>
                                                                        </table>
                                                                    </form>
                                                                    <p class="pull-right bold" id="commie_fees"></p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="scroller2" style="height: 290px;" data-always-visible="1" data-rail-visible1="1">
                                                        <div class="portlet">
                                                            <div class="portlet-title">
                                                                <div class="caption">
                                                                    <i class="fa fa-truck"></i> Assets
                                                                </div>
                                                                <div class="actions">
                                                                    <a class="btn default red-stripe show_form" data-show="#add_asset">
                                                                        <i class="fa fa-plus"></i>
                                                                        <span class="hidden-480">Add new asset</span>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <div class="portlet-body">
                                                                <div class="table-container">
                                                                    <form role="form" id="add_assets">
                                                                        <table class="table table-striped table-hover datatable" id="assets" data-src="assets/app/api/event.php?type=assets&ev=<?php echo $_GET['ev']; ?>">
                                                                            <thead>
                                                                            <tr role="row" class="heading">
                                                                                <th>
                                                                                    Asset Name
                                                                                </th>
                                                                                <th>
                                                                                    Added By
                                                                                </th>
                                                                                <th>
                                                                                    Actions
                                                                                </th>
                                                                            </tr>
                                                                            <tr role="row" class="filter" style="display: none;" id="add_asset">
                                                                                <td>
                                                                                    <select class="form-control input-sm" name="asset">
                                                                                        <option disabled selected value="">Select one..</option>
                                                                                        <?php
                                                                                        $assets = mysql_query("SELECT asset_desc FROM fmo_locations_assets WHERE asset_location_token='".mysql_real_escape_string($event['event_location_token'])."' ORDER BY asset_desc ASC");
                                                                                        if(mysql_num_rows($assets) > 0){
                                                                                            while($asset = mysql_fetch_assoc($assets)){
                                                                                                ?>
                                                                                                <option value="<?php echo $asset['asset_desc']; ?>"><?php echo $asset['asset_desc']; ?></option>
                                                                                                <?php
                                                                                            }
                                                                                        }
                                                                                        ?>
                                                                                    </select>
                                                                                </td>
                                                                                <td><input readonly class="form-control input-sm" value="<?php echo name($_SESSION['uuid']); ?>" name="who"></td>
                                                                                <td>
                                                                                    <div class="margin-bottom-5">
                                                                                        <button type="button" class="btn btn-sm red margin-bottom add_asset"><i class="fa fa-download"></i> Save</button> <button type="button" class="btn btn-sm default filter-cancel"><i class="fa fa-times"></i> Reset</button>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody>

                                                                            </tbody>
                                                                        </table>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- End: life time stats -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="invoices">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="scroller2" style="height: 290px;" data-always-visible="1" data-rail-visible1="1">
                                                        <div class="portlet">
                                                            <div class="portlet-title">
                                                                <div class="caption">
                                                                    <i class="fa fa-tags"></i> Items for sale(s) <small><span class="font-red">|</span> Available items for invoicing. <i class="fa fa-arrow-right"></i></small>
                                                                </div>
                                                            </div>
                                                            <div class="portlet-body">
                                                                <div class="table-container">
                                                                    <form role="form" id="add_service_rate">
                                                                        <table class="table table-striped table-hover datatable" data-src="assets/app/api/event.php?type=rates&luid=<?php echo $event['event_location_token']; ?>&ev=<?php echo $event['event_token']; ?>">
                                                                            <thead>
                                                                            <tr role="row" class="heading">
                                                                                <th>
                                                                                    Service Name
                                                                                </th>
                                                                                <th width="12%" class="text-center">
                                                                                    Invoice item <i class="fa fa-arrow-right"></i>
                                                                                </th>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody>

                                                                            </tbody>
                                                                        </table>
                                                                    </form>
                                                                </div>
                                                                <small class="bold">(<i class="fa fa-check text-danger light"></i> = Taxable | <i class="fa fa-check text-success light"></i> = Commissionable)</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="scroller2" style="height: 290px;" data-always-visible="1" data-rail-visible1="1">
                                                        <div class="portlet">
                                                            <div class="portlet-title">
                                                                <div class="caption">
                                                                    <i class="fa fa-file"></i>Invoice <small><span class="font-red">|</span></small> <button class="btn btn-xs red-stripe print" data-print="#plain_pap"><i class="fa fa-print"></i> Print</button>
                                                                </div>
                                                            </div>
                                                            <div class="portlet-body" id="invoice">
                                                                <div class="invoice">
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="table-container">
                                                                                <form role="form" id="add_service_rate">
                                                                                    <table class="table table-striped table-hover datatable sales" data-src="assets/app/api/event.php?type=sales&ev=<?php echo $event['event_token']; ?>&luid=<?php echo $event['event_location_token']; ?>">
                                                                                        <thead>
                                                                                        <tr role="row" class="heading">
                                                                                            <th>
                                                                                                Item
                                                                                                <span class="pull-right no_print">
                                                                                                Options
                                                                                            </span>
                                                                                            </th>
                                                                                            <th>
                                                                                                Description
                                                                                            </th>
                                                                                            <th>
                                                                                                Quantity
                                                                                            </th>
                                                                                            <th>
                                                                                                Unit Cost
                                                                                            </th>
                                                                                            <th>
                                                                                                <span class="pull-right">Total</span>
                                                                                            </th>
                                                                                        </tr>
                                                                                        </thead>
                                                                                        <tbody>

                                                                                        </tbody>
                                                                                    </table>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-xs-6">
                                                                            <div class="well">
                                                                                <address>
                                                                                    <strong><?php echo $event['event_name']; ?></strong><br>
                                                                                    <?php echo $pk_strt; ?>, <br/>
                                                                                    <?php echo $pk_city; ?>, <?php echo $pk_state; ?> <?php echo $pk_zip; ?> <br/>
                                                                                    <abbr title="Phone">P:</abbr> <?php echo clean_phone($event['event_phone']); ?> </address>
                                                                                <address>
                                                                                    <strong><?php echo $user['user_fname']." ".$user['user_lname']; ?></strong><br>
                                                                                    <a href="mailto:#">
                                                                                        <?php echo$user['user_email']; ?>
                                                                                    </a>
                                                                                </address>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-xs-6 invoice-block">
                                                                            <ul class="list-unstyled amounts" style="margin-bottom: 0;">
                                                                                <li>
                                                                                    Sub Total: <h3 style="display: inline" class="text-danger bold">$<span id="owe_sub_total"></span></h3>
                                                                                </li>
                                                                                <li>
                                                                                    <small class="bold" id="taxable_fees"></small> Taxes Due:  <h3 style="display: inline;" class="text-danger bold">$<span id="owe_tax"></span></h3>
                                                                                </li>
                                                                                <li id="cc_fees">
                                                                                    Credit Card Fees: <h3 style="display: inline;" class="text-danger bold">$<span id="owe_cc_fees"></span></h3>
                                                                                </li>
                                                                                <li>
                                                                                    Grand Total: <h3 style="display: inline;" class="text-danger bold">$<span id="owe_total"></span></h3>
                                                                                </li>
                                                                            </ul>
                                                                            <br/>
                                                                            <div class="btn-group-justified">
                                                                                <div class="btn-group">
                                                                                    <a class="btn btn-lg btn-block green hidden-print load_payments margin-top-15"  data-type="py" data-href="assets/pages/sub/event_master.php?ev=<?php echo $event['event_token']; ?>&uuid=<?php echo $event['event_user_token']; ?>&luid=<?php echo $event['event_location_token']; ?>" data-page-title="Taking payment">
                                                                                        Take Payment <i class="fa fa-money"></i>
                                                                                    </a>
                                                                                </div>
                                                                                <div class="btn-group">
                                                                                    <a class="btn btn-lg btn-block default red-stripe hidden-print fire margin-top-15" data-fire="ckpay">
                                                                                        Send mPay&trade; <i class="fa fa-external-link"></i>
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row" id="payments-content">

                                                                    </div>
                                                                    <div class="row" id="payments-maked">
                                                                        <div class="col-md-12">
                                                                            <div class="table-container">
                                                                                <table class="table table-striped table-hover datatable" id="paid" data-src="assets/app/api/event.php?type=payments&ev=<?php echo $event['event_token']; ?>&luid=<?php echo $event['event_location_token']; ?>">
                                                                                    <thead>
                                                                                    <tr role="row" class="heading">
                                                                                        <th>
                                                                                            Tender Type
                                                                                            <span class="pull-right no_print">
                                                                                            Options
                                                                                        </span>
                                                                                        </th>
                                                                                        <th>
                                                                                            Notes
                                                                                        </th>
                                                                                        <th>Taken By</th>
                                                                                        <th class="text-right">
                                                                                            Tender Amount
                                                                                        </th>
                                                                                    </tr>
                                                                                    </thead>
                                                                                    <tbody>

                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-xs-4">

                                                                        </div>
                                                                        <div class="col-xs-8 invoice-block">
                                                                            <ul class="list-unstyled amounts">
                                                                                <li>
                                                                                    Paid: <h3 style="display: inline;" class="text-success bold">$<span id="owe_paid"></span></h3>
                                                                                </li>
                                                                                <li>
                                                                                    Amount Due: <h3 style="display: inline" class="text-danger bold">$<span id="owe_total_unpaid"></span></h3>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row plain_bol" style="display: none!important;">
                            <div class="col-md-12">
                                <div id="plain_pap">
                                    <table class="table table-striped table-hover table-bordered">
                                        <thead>
                                        <tr>
                                            <th width="50%" class="text-center">
                                                <img class="img-responsive" src="assets/global/img/htt.png" style="display: block; margin: 0 auto!important;"/>
                                            </th>
                                            <th width="50%" class="text-center">
                                                <?php echo companyName($event['event_company_token']); ?> <br/>
                                                <?php echo companyAddress($event['event_company_token']); ?> <br/>
                                                <?php echo clean_phone(locationPhone($event['event_location_token'])); ?> - <?php echo clean_phone(companyPhone3($event['event_company_token'])); ?> <br/>
                                                <?php echo companyLicenses($event['event_company_token']); ?> <br/>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="2" class="text-center uppercasel" style="font-size: 20px;">BILL OF LADING / ORDER FOR SERVICE - Event #<?php echo $event['event_id']; ?> (<?php echo $event['event_type']." ".$event['event_subtype']; ?>)</th>
                                        </tr>
                                        <tr>
                                            <th width="50%">
                                                <i class="fa fa-user"></i> <strong><?php echo name($event['event_user_token']); ?></strong> / <i class="fa fa-tag"></i> <strong><?php echo $event['event_name']; ?></strong>, <?php echo $event['event_type']; ?> <?php echo $event['event_subtype']; ?> <br/>
                                                <i class="fa fa-phone"></i> <strong><?php echo clean_phone($event['event_phone']); ?></strong> or <strong><?php echo clean_phone($user['user_phone']); ?></strong>
                                                <hr style="width: 25%; margin-top: 5px; margin-bottom: 8px;"/>
                                                Pick up location(s): <br/>
                                                <?php
                                                $pickups = mysql_query("SELECT address_id, address_address, address_suite, address_city, address_state, address_zip, address_suite, address_closest_intersection, address_county, address_bedrooms, address_stairs, address_distance, address_comments FROM fmo_locations_events_addresses WHERE address_event_token='".mysql_real_escape_string($event['event_token'])."' AND address_type=1");
                                                if(mysql_num_rows($pickups) > 0){
                                                    $pk = 0;
                                                    while($pickup = mysql_fetch_assoc($pickups)){
                                                        $pk++;
                                                        ?>
                                                        <strong>
                                                            <?php echo $pickup['address_address']; ?>,
                                                            <?php echo $pickup['address_city']; ?>,
                                                            <?php echo $pickup['address_state']; ?>,
                                                            <?php echo $pickup['address_zip']; ?> <?php echo $pickup['address_suite']; ?></strong> <br/>
                                                        <?php
                                                        $extt = 0;
                                                        if(!empty($pickup['address_stairs'])){
                                                            echo "<strong>Stairs</strong>: ".$pickup['address_stairs']." ";
                                                            $extt++;
                                                        }
                                                        if(!empty($pickup['address_bedrooms'])){
                                                            echo "<strong>Bedrooms</strong>: ".$pickup['address_bedrooms']." ";
                                                            $extt++;
                                                        }
                                                        if(!empty($pickup['address_distance'])){
                                                            echo "<strong>Distance</strong>: ".$pickup['address_distance']." ";
                                                            $extt++;
                                                        }
                                                        if($extt > 0){
                                                            echo "<br/>";
                                                        }
                                                    }
                                                } else {
                                                    ?>
                                                    (no pickup locations have been added)
                                                    <?php
                                                }
                                                ?>
                                            </th>
                                            <th width="50%">
                                                Destination location(s): <br/>
                                                <?php
                                                $dests = mysql_query("SELECT address_address, address_suite, address_city, address_state, address_zip, address_suite, address_closest_intersection, address_county, address_stairs, address_distance, address_comments FROM fmo_locations_events_addresses WHERE address_event_token='".mysql_real_escape_string($event['event_token'])."' AND address_type=2");
                                                if(mysql_num_rows($dests) > 0){
                                                    $pk = 0;
                                                    while($dest = mysql_fetch_assoc($dests)){
                                                        $pk++
                                                        ?>
                                                        <strong>
                                                            <?php echo $dest['address_address']; ?>,
                                                            <?php echo $dest['address_city']; ?>,
                                                            <?php echo $dest['address_state']; ?>,
                                                            <?php echo $dest['address_zip']; ?> <?php echo $dest['address_suite']; ?></strong> <br/>
                                                        <?php
                                                        $extt = 0;
                                                        if(!empty($dest['address_stairs'])){
                                                            echo "<strong>Stairs</strong>: ".$dest['address_stairs']." ";
                                                            $extt++;
                                                        }
                                                        if(!empty($dest['address_distance'])){
                                                            echo "<strong>Distance</strong>: ".$dest['address_distance']." ";
                                                            $extt++;
                                                        }
                                                        if($extt > 0){
                                                            echo "<br/>";
                                                        }
                                                    }
                                                } else {
                                                    ?>
                                                    (no destination locations have been added)
                                                    <?php
                                                }
                                                ?>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th width="50%" class="text-center" style="font-size: 15px;">
                                                <strong>Agreed Start</strong>: <?php echo date('d-m-Y', strtotime($event['event_date_start'])); ?> | <span class="text-danger">Actual Start: <strong>_______________</strong> </span>
                                            </th>
                                            <th width="50%" class="text-center" style="font-size: 15px;">
                                                <strong>Agreed Finish</strong>: <?php echo date('d-m-Y', strtotime($event['event_date_end'])); ?> | <span class="text-danger">Actual Finish: <strong>_______________</strong> </span>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="2"><strong>Comments:</strong> <?php echo $event['event_comments']; ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td colspan="2">
                                                <table class="table table-striped table-hover table-bordered">
                                                    <thead>
                                                    <tr>
                                                        <th>Item</th>
                                                        <th>Description</th>
                                                        <th>Unit Cost</th>
                                                        <th>Quantity</th>
                                                        <th class="text-right">Line Total</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                    $findItems = mysql_query("SELECT item_id, item_item, item_desc, item_qty, item_cost, item_total, item_redeemable FROM fmo_locations_events_items WHERE item_event_token='".mysql_real_escape_string($event['event_token'])."'");
                                                    $iTotalRecords = mysql_num_rows($findItems);

                                                    $records = array();
                                                    $records["data"] = array();

                                                    while($items = mysql_fetch_assoc($findItems)) {
                                                        if($items['item_redeemable'] == 2){
                                                            $records["data"][] = array(
                                                                '<span class="text-success">'.$items['item_item'].'</span>',
                                                                '<span class="text-success">'.$items['item_desc'].'</span>',
                                                                '<span class="text-success">'.$items['item_qty'].'</span>',
                                                                '<span class="text-success">'.$items['item_cost'].'</span>',
                                                                '<strong class="text-danger pull-right">'.$items['item_total'].'</strong>'
                                                            );
                                                        } else {
                                                            $records["data"][] = array(
                                                                ''.$items['item_item'].'',
                                                                '<a>'.$items['item_desc'].'</a>',
                                                                '<a>'.$items['item_qty'].'</a>',
                                                                '<a>'.$items['item_cost'].'</a>',
                                                                '<strong class="text-danger pull-right">'.$items['item_total'].'</strong>'
                                                            );
                                                        }
                                                    }
                                                    $i = 0;
                                                    foreach($records['data'] as $data){
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $data[0]; ?></td>
                                                            <td><?php echo $data[1]; ?></td>
                                                            <td><?php echo $data[2]; ?></td>
                                                            <td><?php echo $data[3]; ?></td>
                                                            <td><?php echo $data[4]; ?></td>
                                                        </tr>
                                                        <?php
                                                        $i++;
                                                    }
                                                    for($b = 0; $b <= 8 - $i; $i++){
                                                        ?>
                                                        <tr>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    ?>
                                                    </tbody>
                                                </table>
                                                <table class="table table-striped table-hover table-bordered">
                                                    <tr>
                                                        <th style="width: 14%">Name</th>
                                                        <th style="width: 6%" class="text-center">On</th>
                                                        <th style="width: 6%" class="text-center">Lunch</th>
                                                        <th style="width: 6%" class="text-center">Off</th>
                                                        <th style="width: 6%" class="text-center">Total</th>
                                                        <th rowspan="5" class="text-center" style="width: 33%">
                                                            <br/><br/>
                                                            <strong>CC # _________-_________-_________-_________</strong><br/><br/>
                                                            <strong>Expiration Date _____/_____ CVC ________</strong><br/><br/>
                                                            <strong>Name On Card ___________________________</strong>
                                                        </th>
                                                        <th rowspan="2" colspan="2"  style="width: 20%">
                                                            Sub Total: <span class="pull-right bold font-red">$<span id="PLPAP_SUBTOTAL"></span></span> <br/>
                                                            Taxes: <span class="pull-right bold font-red">$<span id="PLPAP_TAXES"></span></span> <br/>
                                                            Total: <span class="pull-right bold font-red">$<span id="PLPAP_TOTAL"></span></span>
                                                        </th>
                                                    </tr>
                                                    <?php
                                                    $findLabor = mysql_query("SELECT laborer_user_token FROM fmo_locations_events_laborers WHERE laborer_event_token='".mysql_real_escape_string($event['event_token'])."'");
                                                    $iTotalRecords = mysql_num_rows($findLabor);

                                                    $records = array();
                                                    $records["data"] = array();

                                                    while($lb = mysql_fetch_assoc($findLabor)) {
                                                        $records["data"][] = array(''.name($lb['laborer_user_token']).'');
                                                    }
                                                    $i = 0;
                                                    foreach($records['data'] as $crew){
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $crew[0]; ?></td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <?php
                                                            if($i == 1){
                                                                ?>
                                                                <td rowspan="3" colspan="2" class="text-center">I authorize Here To There Movers to charge me for the charges listed above.</td>
                                                                <?php
                                                            } $i ++;
                                                            ?>
                                                        </tr>
                                                        <?php
                                                    }
                                                    for($c = 0; $c <= 4 - $i; $c++){
                                                        ?>
                                                        <tr>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <?php
                                                            if($i == 1){
                                                                ?>
                                                                <td rowspan="3" colspan="2" class="text-center">I authorize Here To There Movers to charge me for the charges listed above.</td>
                                                                <?php
                                                            } $i ++;
                                                            ?>
                                                        </tr>
                                                        <?php
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td colspan="3" style="font-size: 10px;" class="text-muted text-center">Consumer Must Personally Initial Choice</td>
                                                        <td colspan="4"  style="font-size: 10px;" class="text-muted text-center">This contract is subject to all terms and conditions, rates, and disclaimers contained here and within the tarrif filed with the State.</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="7" style="font-size: 12px;">
                                                            _______ <strong class="font-red">I agree to minimal reimbursement for lost or damaged goods. I understand and accept that I will be reimbursed for lost or damaged goods at a minimal amount not exceeding sixty cents per pound per article.</strong>

                                                            <hr/>

                                                            _______ <strong class="font-red">I accept reimbursement equal to the replacement cost of lost or damaged goods. I declare a total replacement value of $ ______________ or a minimum of six dollars per pound times the weight of the shipment, whichever is greater. I understand that total reimbursement for lost or damaged goods shall not exceed this declared value. I understand that failure to disclose any article valued at greater than one hundred dollars per pound may limit the carrier's reimbursement liability to this maximum per article.</strong>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="5">
                                                            Lost or Damaged Items:

                                                        </td>
                                                        <td rowspan="2" colspan="2">
                                                            <br/><br/>
                                                            <strong style="font-size: 20px;">X</strong> <span class="text-muted" style="font-size: 8px;">signature</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="5" class="text-center">
                                                            www.HERETOTHEREMOVERS.com
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade bs-modal-lg" id="print_bol" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content box red">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h3 class="modal-title font-bold">Printing BOL for <strong><?php echo $event['event_name']; ?></strong></h3>
                </div>
                <div class="modal-body">
                    <div class="portlet">
                        <div class="portlet-body" id="bol">
                            <?php

                            $time = explode(" to ", $event['event_time']);
                            if($time[1] == 'finish'){
                                $event['event_time'] = $time[0];
                            } else {
                                $event['event_time'] = $time[0]." to ".$time[1];
                            }

                            ?>
                            <h5 style="text-decoration: underline; font-size: 16px;">Bill of Lading / Order for Service | Job # <?php echo $event['event_id']; ?></h5>
                            <i class="fa fa-user"></i> <strong><?php echo $event['event_name']; ?></strong>, <?php echo $event['event_type']; ?> <?php echo $event['event_subtype']; ?> <br/>
                            <i class="fa fa-calendar"></i> <strong><?php echo date('m-d-Y', strtotime($event['event_date_start'])); ?></strong> | <strong><?php echo $event['event_time']; ?></strong> <br/>
                            <i class="fa fa-phone"></i> <strong><?php echo clean_phone($event['event_phone']); ?></strong> or <strong><?php echo clean_phone($user['user_phone']); ?></strong>
                            <hr style="width: 25%; margin-top: 5px; margin-bottom: 8px;"/>
                            Pick up location(s): <br/>
                            <?php
                            $pickups = mysql_query("SELECT address_id, address_address, address_suite, address_city, address_state, address_zip, address_suite, address_closest_intersection, address_county, address_bedrooms, address_stairs, address_distance, address_comments FROM fmo_locations_events_addresses WHERE address_event_token='".mysql_real_escape_string($event['event_token'])."' AND address_type=1");
                            if(mysql_num_rows($pickups) > 0){
                                $pk = 0;
                                while($pickup = mysql_fetch_assoc($pickups)){
                                    $pk++;
                                    ?>
                                    <strong>
                                    <?php echo $pickup['address_address']; ?>,
                                    <?php echo $pickup['address_city']; ?>,
                                    <?php echo $pickup['address_state']; ?>,
                                    <?php echo $pickup['address_zip']; ?> / <?php echo $pickup['address_suite']; ?></strong> <br/>
                                    <?php
                                    $extt = 0;
                                    if(!empty($pickup['address_stairs'])){
                                        echo "<strong>Stairs</strong>: ".$pickup['address_stairs']." ";
                                        $extt++;
                                    }
                                    if(!empty($pickup['address_bedrooms'])){
                                        echo "<strong>Bedrooms</strong>: ".$pickup['address_bedrooms']." ";
                                        $extt++;
                                    }
                                    if(!empty($pickup['address_distance'])){
                                        echo "<strong>Distance</strong>: ".$pickup['address_distance']." ";
                                        $extt++;
                                    }
                                    if($extt > 0){
                                        echo "<br/>";
                                    }
                                }
                            } else {
                                ?>
                                (no pickup locations have been added)
                                <?php
                            }
                            ?>
                            <hr style="width: 25%; margin-top: 5px; margin-bottom: 8px;"/>
                            Destination location(s): <br/>
                            <?php
                            $dests = mysql_query("SELECT address_address, address_suite, address_city, address_state, address_zip, address_suite, address_closest_intersection, address_county, address_stairs, address_distance, address_comments FROM fmo_locations_events_addresses WHERE address_event_token='".mysql_real_escape_string($event['event_token'])."' AND address_type=2");
                            if(mysql_num_rows($dests) > 0){
                                $pk = 0;
                                while($dest = mysql_fetch_assoc($dests)){
                                    $pk++
                                    ?>
                                    <strong>
                                    <?php echo $dest['address_address']; ?>,
                                    <?php echo $dest['address_city']; ?>,
                                    <?php echo $dest['address_state']; ?>,
                                    <?php echo $dest['address_zip']; ?> / <?php echo $dest['address_suite']; ?></strong> <br/>
                                    <?php
                                    $extt = 0;
                                    if(!empty($dest['address_stairs'])){
                                        echo "<strong>Stairs</strong>: ".$dest['address_stairs']." ";
                                        $extt++;
                                    }
                                    if(!empty($dest['address_distance'])){
                                        echo "<strong>Distance</strong>: ".$dest['address_distance']." ";
                                        $extt++;
                                    }
                                    if($extt > 0){
                                        echo "<br/>";
                                    }
                                }
                            } else {
                                ?>
                                (no destination locations have been added)
                                <?php
                            }
                            ?>
                            <hr style="width: 25%; margin-top: 5px; margin-bottom: 8px;"/>
                            <?php
                            if($event['event_date_start'] < $event['event_date_end']){
                                ?>
                                Unload Date: <?php echo $event['event_date_end']; ?> | Actual unload date: <strong>____________</strong>
                                <?php
                            }
                            ?>

                            <h5 style="text-decoration: underline; font-size: 16px;">Job Comments</h5>
                            <p style="width: 50%"><strong><?php echo $event['event_comments']; ?></strong></p>
                            <h5 style="text-decoration: underline; font-size: 16px;">Agreed Pricing for this job</h5>
                            <strong>$<span id="bol_TF"></span></strong> truck fee (<strong id="bol_TR_qty"></strong> trucks). <br/>
                            <strong>$<span id="bol_LR"></span></strong>/<strong>hour</strong> labor rate, billed to the closest 1/4<br/>hour from arrival to departure. (<strong id="bol_LR_qty"></strong> men)<br/>
                            <strong>2 hour minimum</strong> - After <strong>8:00PM</strong> rate is <strong>x1.5</strong> regular rate.<br/>
                            <strong>$<span id="bol_CF"></span></strong> county fees for travel outside our service area. (<strong id="bol_CR_qty"></strong> counties).<br/>
                            <?php
                            $n=0;
                            if(!empty($extra['safe'])){ echo "<strong>Safes</strong>, "; $n++;}
                            if(!empty($extra['play_set'])){ echo "<strong>Play Sets</strong>, "; $n++;}
                            if(!empty($extra['pool_table'])){ echo "<strong>Pool Tables</strong>, "; $n++;}
                            if(!empty($extra['piano'])){ echo "<strong>Pianos</strong>, "; $n++;}
                            if(!empty($extra['hot_tub'])){ echo "<strong>Hot Tubs</strong>, "; $n++;}
                            if(!empty($extra['packing'])){ echo "<strong>Packing</strong>, "; $n++;}
                            if(!empty($extra['materials'])){ echo "<strong>Materials</strong>, "; $n++;}
                            if($n > 0){
                                echo "have special terms and fees, please ask for details.";
                            }
                            ?>
                            <h5 style="text-decoration: underline; font-size: 16px;">Disclaimers</h5>
                            <?php
                            if($event['event_booking'] != 1){
                                ?>
                                Accepted payment types: <br/><strong class="text-success">Cash or Certified Funds - Driver must collect before unload.</strong><br/>
                                <?php
                            } else {
                                ?>
                                Accepted payment types: <br><strong class="text-success">Cash, Check, Credit/Debit Card<br/>%<?php echo number_format($location['location_creditcard_fee'] * 100, 1); ?> added to payments via Credit/Debt Card.</strong><br/>
                                <?php
                            }
                            ?>

                            <strong>All damage claims are subject to a $100 deductible.</strong>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn green pull-right print" data-print="#bol">Print BOL (Normal)</button>
                    <button type="button" class="btn red pull-right">Print BOL (Plain-Paper)</button>
                </div>
            </div>
        </div>
    </div>
    <?php
    if($event['event_booking'] != 1){
        ?>
        <div class="modal fade bs-modal-lg" id="booking_fee_modal" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content box red">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h3 class="modal-title font-bold">Booking fee for <?php echo $event['event_name']; ?></h3>
                    </div>
                    <div class="modal-body">
                        <div class="portlet">
                            <div class="portlet-body">
                                <h3 class="text-danger">Read this:</h3>
                                <p>
                                    Ok, now that we have you all set up... We ask that you secure your appointment with a debit or credit card.  We will charge a non-refundable $10 booking fee that does 2 things.  It provides us peace of mind that you have a good form of payment and earns you the option to pay any way you want at the end of the job.
                                    <br/>
                                    You have the option to book this move without a card on file, but you would be required to pay cash for your services prior to the unload.... Would you like to put a card on file, please?
                                </p>
                                <div id="bookingfeereason_h" class="panel-group">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <div class="caption">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#bookingfeereason_h" href="#bookingfeereason" aria-expanded="false"><strong>Why Booking Fees?</strong> (sometimes referred to and treated as deposits)</a>
                                                </h4>
                                            </div>
                                        </div>
                                        <div id="bookingfeereason" class="panel-collapse collapse" aria-expanded="true" style="height: 0px;">
                                            <div class="panel-body">
                                                 By utilizing ForMoversOnly.com, you agree to our terms regarding booking fees charged to your customers.  While it is NOT required that your customer provide a booking fee through our software, any booking fee paid is the sole income of ForMoversOnly.com and NON-REFUNDABLE for any reason. Booking fees are $10 as of Feb. 1st, 2015. We are the sole decision maker to set the pricing for these fees and may change the amount charged at any time.  Booking fees are not actually deposits and we recommend that you do not apply this fee as a credit to your customer’s event payments.  We have no obligation to provide you with any card processing details after the fact.  Booking fees do have great benefits to you.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <form id="booking_fee_form">
                                    <div class="form-inline margin-bottom-25 text-center">
                                        <div class="form-group form-md-line-input">
                                            <div class="input-icon">
                                                <input type="text" size="20" data-stripe="name" class="form-control input-sm card_name" value="<?php echo name($user['user_token']); ?>">
                                                <div class="form-control-focus">
                                                </div>
                                                <span class="help-block">Card Holder Name</span>
                                                <i class="fa fa-user"></i>
                                            </div>
                                        </div>
                                        <div class="form-group form-md-line-input">
                                            <div class="input-icon">
                                                <input type="text" size="20" data-stripe="number" class="form-control input-sm card_num">
                                                <div class="form-control-focus">
                                                </div>
                                                <span class="help-block">Card number</span>
                                                <i class="fa fa-credit-card"></i>
                                            </div>
                                        </div>
                                        <div class="form-group form-md-line-input">
                                            <div class="input-icon">
                                                <input type="text" size="2" data-stripe="exp" class="form-control input-sm exp_date" style="width: 80px!important;">
                                                <div class="form-control-focus">
                                                </div>
                                                <span class="help-block">Expiration</span>
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                        </div>
                                        <div class="form-group form-md-line-input">
                                            <div class="input-icon">
                                                <input type="text" size="4" data-stripe="cvc" class="form-control input-sm cvc_num">
                                                <div class="form-control-focus">
                                                </div>
                                                <span class="help-block">CVC</span>
                                                <i class="fa fa-sort-numeric-asc"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="text" name="notes" id="booking_notes2" class="hidden"/>
                                    <button id="booking_fee" class="btn btn-block red" type="button"><span class="error-handler">Pay $10.00 booking fee</span> <i class="fa fa-credit-card"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
    <div class="modal fade bs-modal-lg" id="comments_only" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content box red">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h3 class="modal-title font-bold">Comments for <strong><?php echo $event['event_name']; ?></strong></h3>
                </div>
                <div class="modal-body">
                    <h3>Comments</h3>
                    <p>You can leave comments on this event at any time to leave a quick note or thought about the event. These comments will be saved here, and also logged in the event's timeline.</p>
                    <hr/>
                    <div class="portlet">
                        <div class="portlet-title" style="border-bottom: none;">
                            <div class="actions">
                                <a class="btn default red-stripe show_form" data-show="#add_comment">
                                    <i class="fa fa-plus"></i>
                                    <span class="hidden-480">Add new comment</span>
                                </a>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="table-container">
                                <form role="form" id="add_comt">
                                    <table class="table table-striped table-bordered table-hover datatable" data-src="assets/app/api/event.php?type=comments&ev=<?php echo $event['event_token']; ?>">
                                        <thead>
                                        <tr role="row" class="heading">
                                            <th width="12%">
                                                Comment Timestamp
                                            </th>
                                            <th>
                                                Comment
                                            </th>
                                            <th width="12%">
                                                Comment Creator
                                            </th>
                                        </tr>
                                        <tr role="row" class="filter" style="display: none;" id="add_comment">
                                            <td></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="comment"></td>
                                            <td>
                                                <div class="margin-bottom-5">
                                                    <button type="button" class="btn btn-sm red margin-bottom add_comment"><i class="fa fa-download"></i> Save</button>
                                                </div>
                                            </td>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade bs-modal-lg" id="cancel_event" tabindex="-1" role="basic">
        <div class="modal-dialog modal-lg">
            <div class="modal-content box red">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h3 class="modal-title font-bold">Cancel <strong><?php echo $event['event_name']; ?></strong></h3>
                </div>
                <div class="modal-body">
                    <div class="portlet">
                        <div class="portlet-body">
                            <h4 class="text-danger"> Please be sure!</h4>
                            <p>
                                Below, please provide the reason as to why you are cancelling this event. Please note, this action will be logged & reported to administration for further review. If you choose to proceed, this event will be reverted to <strong>its previous status of: Hot Lead</strong>.
                                <br/>
                                <strong>Please provide your reasoning here</strong>:
                            </p>
                            <br/>
                            <textarea placeholder="Your reasoning.." class="form-control cancel_reason" style="height: 200px;"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default pull-left" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn red pull-right submit_cancel" data-dismiss="modal">Submit cancellation reason</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade bs-modal-lg" id="estimates_only" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content box red">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h3 class="modal-title font-bold">Send the <strong>estimate tool</strong> too:
                        <select class="form-control input-sm estimators" style="width: 200px !important;">
                            <option value="">Select someone..</option>
                            <?php
                            $estimators = mysql_query("SELECT user_fname, user_lname, user_phone, user_employer_rate, user_token FROM fmo_users WHERE user_employer='".mysql_real_escape_string($_SESSION['cuid'])."' ORDER BY user_lname ASC");
                            if(mysql_num_rows($estimators) > 0){
                                while($estimator = mysql_fetch_assoc($estimators)){
                                    if(empty($estimator['user_phone'])){
                                        continue;
                                    }
                                    ?>
                                    <option value="<?php echo $estimator['user_token']; ?>" data-who="<?php echo name($estimator['user_token']); ?>" data-pho="<?php echo $estimator['user_token']; ?>"><?php echo $estimator['user_lname'].", ".$estimator['user_fname']; ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                        <button type="button" class="btn default red-stripe btn-sm send_txt" disabled>Send <span id="es_to"></span></button>
                    </h3>
                </div>
                <div class="modal-body">
                    <div class="portlet">
                        <div class="portlet-body">
                            <h3 style="margin-top: 10px;">Estimates</h3>
                            <p>Below are a list of estimates for this event. You can use the tool above to send a new estimate tool too an estimators phone. It will find their on-file number, and ask them to complete it. They will have access to this estimate only. <strong>Otherwise, you can hit the Create New Estimate button below, and do one yourself.</strong></p>
                            <hr/>
                            <?php
                            $ess = mysql_query("SELECT estimate_id, estimate_token, estimate_location_token, estimate_name, estimate_type, estimate_estimator FROM fmo_locations_events_estimates WHERE estimate_event_token='".mysql_real_escape_string($event['event_token'])."'");
                            if(mysql_num_rows($ess) > 0){
                                $pk = 0;
                                while($es = mysql_fetch_assoc($ess)){
                                    ?>
                                    <div id="estimate_h_<?php echo $es['estimate_id']; ?>" class="panel-group">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <span class="pull-right text-muted"><small>expand for more options <i class="fa fa-arrow-down"></i></small></span>
                                                <div class="caption">
                                                    <h4 class="panel-title">
                                                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#estimate_h_<?php echo $es['estimate_id']; ?>" href="#estimate_<?php echo $es['estimate_id']; ?>" aria-expanded="false"><strong>Estimate #<?php echo $es['estimate_id']; ?>: <?php echo $es['estimate_name']; ?>, <?php echo $es['estimate_type']; ?></strong></a>
                                                    </h4>
                                                </div>
                                            </div>
                                            <div id="estimate_<?php echo $es['estimate_id']; ?>" class="panel-collapse collapse" aria-expanded="true" style="height: 0px;">
                                                <div class="panel-body">
                                                   <div class="btn-group-justified">
                                                       <div class="btn-group">
                                                           <a class="btn btn-default btn-sm blue-stripe" target="_blank" href="https://www.formoversonly.com/dashboard/assets/public/index.php?e=EmP&ev=<?php echo $event['event_token']; ?>&v=e&n=<?php echo $es['estimate_token']; ?>" >
                                                               <i class="fa fa-pencil"></i> <span class="hidden-sm hidden-md hidden-xs">Edit</span>
                                                           </a>
                                                       </div>
                                                       <div class="btn-group">
                                                           <a class="btn btn-default btn-sm red-stripe" target="_blank" href="https://www.formoversonly.com/dashboard/assets/public/index.php?e=EmP&ev=<?php echo $event['event_token']; ?>&v=v&n=<?php echo $es['estimate_token']; ?>">
                                                               <i class="fa fa-external-link"></i> <span class="hidden-sm hidden-md hidden-xs">View</span>
                                                           </a>
                                                       </div>
                                                   </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    $pk++;
                                }
                            } else {
                                ?>
                                <div class="alert alert-warning alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                                    <strong>No estimates available to view!</strong> Add new estimates to see them appear here.
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn red pull-right new_estimate" data-dismiss="modal">Create New Estimate</button>
                    <button type="button" class="btn default pull-left" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade bs-modal-lg" id="e_conf" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content box red">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h3 class="modal-title font-bold">Confirmation for <?php echo $event['event_name']; ?></h3>
                </div>
                <div class="modal-body">
                    <div class="portlet">
                        <div class="portlet-body text-center">
                            <h3>Ok, thank you <strong><?php echo name($event['event_user_token']); ?></strong></h3>
                            Your confirmation number is: <strong># <?php echo $event['event_id']; ?></strong> <br/>
                            Date(s): <strong><?php echo date('m-d-Y', strtotime($event['event_date_start'])); ?></strong> - <strong><?php echo date('m-d-Y', strtotime($event['event_date_end'])); ?></strong> <br/>
                            Start Time(s): <strong><?php echo $event['event_time']; ?></strong> <br/>
                            Crew(s): <strong><?php echo $event['event_truckfee']; ?> Truck(s)</strong> / <strong><?php echo $event['event_laborrate']; ?> Crewmen</strong> <br/>
                            <?php
                            if(!empty($pk_strt)){
                                ?>
                                First Location: <strong><?php echo $pk_strt; ?>, <?php echo $pk_city; ?>, <?php echo $pk_state; ?>, <?php echo $pk_zip; ?></strong> <br/>
                                <?php
                            }
                            ?>
                            <br/><br/>
                            <strong class="text-danger">You will get an email AND text confirmation of this appointment. Please let us know if anything looks wrong!</strong>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade bs-modal-lg" id="claims_only" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content box red">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h3 class="modal-title font-bold">Claim for <?php echo $event['event_name']; ?></h3>
                </div>
                <div class="modal-body">
                    <table class="table table-striped table-hover dataaa">
                        <thead>
                        <tr role="row" class="heading">
                            <th width="8%">
                                Ticket Number
                            </th>
                            <th>
                                Department
                            </th>
                            <th>
                                Customer / Event
                            </th>
                            <th>
                                Priority
                            </th>
                            <th>
                                Status
                            </th>
                            <th width="10%">
                                Last Message By
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $tickets = mysql_query("SELECT ticket_id, ticket_token, ticket_user_token, ticket_event_token, ticket_department, ticket_priority, ticket_status, ticket_last_contacted_by, ticket_timestamp FROM fmo_locations_tickets WHERE ticket_event_token='".mysql_real_escape_string($event['event_token'])."' ORDER BY ticket_priority ASC");
                        if(mysql_num_rows($tickets) > 0){
                            while($ticket = mysql_fetch_assoc($tickets)){
                                /* Statuses
                                 * 0 = Open - new
                                 * 1 = Open - waiting for staff reply
                                 * 2 = Open - waiting for user reply
                                 * 3 = Closed - Solved
                                 */
                                switch($ticket['ticket_status']){
                                    case  0: $badge = "<span class='badge badge-info badge-roundless'><strong>Open</strong> - New ticket</span>"; break;
                                    case  1: $badge = "<span class='badge badge-warning badge-roundless'><strong>Open</strong> - waiting for staff reply</span>"; break;
                                    case  2: $badge = "<span class='badge badge-warning badge-roundless'><strong>Open</strong> - waiting for user reply</span>"; break;
                                    case  3: $badge = "<span class='badge badge-success badge-roundless'><strong>Open</strong> - Solved</span>"; break;
                                    default: $badge = "<span class='badge badge-danger badge-roundless'><strong>Open</strong> - waiting for action</span>"; break;
                                }
                                ?>
                                <tr style="cursor: pointer" class="popout" data-pop="tickets.php?tk=<?php echo $ticket['ticket_token']; ?>" data-page-title="Support Ticket <?php echo $ticket['ticket_id']; ?>">
                                    <td><span class="badge badge-danger badge-roundless"><strong>#<?php echo $ticket['ticket_id']; ?></strong></span></td>
                                    <td><strong><?php echo $ticket['ticket_department']; ?></strong></td>
                                    <td>
                                        <strong>USER:</strong> <?php echo name($ticket['ticket_user_token']); ?>
                                        <?php
                                        if(!empty($ticket['ticket_event_token'])){
                                            ?>
                                            / <strong>EVENT</strong>: <?php echo eventName($ticket['ticket_event_token']); ?>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                    <td><span class="badge badge-danger badge-roundless"><strong><?php echo $ticket['ticket_priority']; ?></strong></span></td>
                                    <td><?php echo $badge; ?></td>
                                    <td><strong><?php echo name($ticket['ticket_last_contacted_by']); ?></strong></td>
                                </tr>
                                <?php
                            }
                        }

                        ?>

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade bs-modal-lg" id="reviews_only" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content box red">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h3 class="modal-title font-bold">Review for <?php echo $event['event_name']; ?></h3>
                </div>
                <div class="modal-body">
                    <div class="portlet">
                        <div class="portlet-body">
                            <?php
                            $review = mysql_query("SELECT review_rating, review_comments FROM fmo_locations_events_reviews WHERE review_event_token='".mysql_real_escape_string($event['event_token'])."'");
                            if(mysql_num_rows($review) > 0){
                                $review = mysql_fetch_array($review);
                                ?>
                                <center>
                                    <br/><br/>
                                    <div class="rateYo" data-rateyo-rating="<?php echo $review['review_rating']; ?>"></div><br/><br/>
                                    <p style="font-size: 24px"><?php echo $review['review_comments']; ?></p>
                                    <small>by <strong><?php echo name($event['event_user_token']); ?></strong></small>
                                </center>
                                <?php
                            } else {
                                ?>
                                <div class="alert alert-warning">
                                    <strong>There is no review associated with this event.</strong>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php
    if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_event_rate_adj") !== false){
        ?>
        <div class="modal fade bs-modal-lg" id="rate_adj" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content box red">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h3 class="modal-title font-bold"><strong>Creating adjustment</strong> for <?php echo $event['event_name']; ?></h3>
                    </div>
                    <div class="modal-body">
                        <div class="portlet">
                            <div class="portlet-body">
                                <h3 class="text-warning text-center">Warning, this will be shown to customer & logged! <br/> <small class="text-muted"><a class="edit" data-edit="adj" data-reload="">Click here to unlock</a> editing the rate for this event.</small></h3>
                                <h1 class="text-center">$<a class="adj" style="color:#333333" data-name="event_adjustment" data-pk="<?php echo $event['event_token']; ?>" data-type="text" data-placement="right" data-title="Enter new event phone number.." data-url="assets/app/update_settings.php?update=event_fly"><?php echo $event['event_adjustment']; ?></a><small>/hr</small></h1>
                                <h5 class="text-center" style="margin-top: 10px; padding: 20px;">The amount entered above will be deducted from the total cost of labor calculated by FORMOVERSONLY&trade;. For example, if you have a total cost of <strong>$99.00/hr</strong> and an adjusment of <strong class="text-danger">-$10.00/hr</strong>, the new shown cost of labor will be <strong>$89.00/hr</strong>. This new rate will also be shown to the customer & administrators.</h5>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn red adj_conf" data-dismiss="modal">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
    
    <div class="modal fade bs-modal-lg" id="tools_only" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content box red">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h3 class="modal-title font-bold">Miscellaneous tools for <?php echo $event['event_name']; ?></h3>
                </div>
                <div class="modal-body">
                    <div class="portlet">
                        <div class="portlet-body">
                            <h4><strong>General Tools</strong> for this event</h4><hr/>
                            <div class="tiles">
                                <div class="tile bg-red-flamingo open_ticket">
                                    <div class="tile-body">
                                        <i class="fa fa-tag"></i>
                                    </div>
                                    <div class="tile-object">
                                        <div class="name">
                                            Open <strong>Support Ticket</strong>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_event_rate_adj") !== false){
                                    ?>
                                    <div class="tile bg-blue-steel" data-toggle="modal" href="#rate_adj" onclick="$('#tools_only').modal('hide');">
                                        <div class="tile-body">
                                            <i class="fa fa-tag"></i>
                                        </div>
                                        <div class="tile-object">
                                            <div class="name">
                                                Adjust <strong>Rates</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                                
                                <?php
                                if($_SESSION['group'] == 1) {
                                ?>
                                <div class="tile bg-red" id="del_ev" data-ev="<?php echo $event['event_token']; ?>">
                                    <div class="tile-body">
                                        <i class="fa fa-exclamation-triangle"></i>
                                    </div>
                                    <div class="tile-object">
                                        <div class="name">
                                            Delete Event
                                        </div>
                                        <div class="number">

                                        </div>
                                    </div>
                                </div><?php
                                }
                                ?>
                            </div>

                            <h4><strong>Texting Tools</strong> for this event</h4><hr/>
                            <div class="tiles">
                                <div class="tile bg-purple-wisteria fire" data-fire="rates_link">
                                    <div class="tile-body">
                                        <i class="fa fa-dollar"></i>
                                    </div>
                                    <div class="tile-object">
                                        <div class="name">
                                            Send <strong>Rates</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="tile bg-yellow-gold fire" data-fire="confirm_link">
                                    <div class="tile-body">
                                        <i class="fa fa-check"></i>
                                    </div>
                                    <div class="tile-object">
                                        <div class="name">
                                            Send <strong>Confirmation</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="tile bg-blue-steel fire" data-fire="receipt_link">
                                    <div class="tile-body">
                                        <i class="fa fa-file-text-o"></i>
                                    </div>
                                    <div class="tile-object">
                                        <div class="name">
                                            Send <strong>Receipt</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="tile bg-yellow-lemon fire" data-fire="review_link">
                                    <div class="tile-body">
                                        <i class="fa fa-star"></i>
                                    </div>
                                    <div class="tile-object">
                                        <div class="name">
                                            Send <strong>Review</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="tile bg-green-haze fire" data-fire="claim_link">
                                    <div class="tile-body">
                                        <i class="fa fa-exclamation-triangle"></i>
                                    </div>
                                    <div class="tile-object">
                                        <div class="name">
                                            Send <strong>Claim</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--
                            <button class="btn red-stripe fire" data-fire="review_link"><i class="fa fa-star"></i> Send event review link (text)</button> <br/> <br/>
                            <button class="btn blue-stripe fire" data-fire="claim_link"><i class="fa fa-exclamation-triangle"></i> Send claim form link (text)</button> <br/> <br/>
                            <button class="btn green-stripe fire" data-fire="rates_link"><i class="fa fa-dollar"></i> Re-Send quote/estimate link (text)</button> <br/> <br/>
                            <button class="btn yellow-stripe fire" data-fire="confirm_link"><i class="fa fa-dollar"></i> Re-Send confirmation link (text)</button> <br/> <br/>
                            <button class="btn blue-stripe fire" data-fire="receipt_link"><i class="fa fa-dollar"></i> Re-Send receipt link (text)</button> <br/> <br/>
                            <button class="btn red-stripe open_ticket"><i class="fa fa-info"></i> Open support ticket for this user/event.</button> <br/> <br/>-->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <form method="POST" action="" role="form" id="new_location">
        <div class="modal fade bs-modal-lg" id="draggable" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content box red">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h3 class="modal-title font-bold">Add event location</h3>
                    </div>
                    <div class="modal-body">
                        <div class="row hidden">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Location Type</label>
                                    <select name="type" class="form-control" id="type">
                                        <option value="1">Pick up</option>
                                        <option value="2">Destination</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Street Address</label>
                                    <input type="text" class="form-control" name="address" placeholder="Street">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Zip Code</label>
                                    <input type="number" class="form-control" name="zip" id="zip_auto">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" class="form-control" name="city" placeholder="City">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>State</label>
                                    <select name="state" class="form-control state">
                                        <option value="" selected disabled>Select one..</option>
                                        <option value="AL">Alabama</option>
                                        <option value="AK">Alaska</option>
                                        <option value="AZ">Arizona</option>
                                        <option value="AR">Arkansas</option>
                                        <option value="CA">California</option>
                                        <option value="CO">Colorado</option>
                                        <option value="CT">Connecticut</option>
                                        <option value="DE">Delaware</option>
                                        <option value="DC">District Of Columbia</option>
                                        <option value="FL">Florida</option>
                                        <option value="GA">Georgia</option>
                                        <option value="HI">Hawaii</option>
                                        <option value="ID">Idaho</option>
                                        <option value="IL">Illinois</option>
                                        <option value="IN">Indiana</option>
                                        <option value="IA">Iowa</option>
                                        <option value="KS">Kansas</option>
                                        <option value="KY">Kentucky</option>
                                        <option value="LA">Louisiana</option>
                                        <option value="ME">Maine</option>
                                        <option value="MD">Maryland</option>
                                        <option value="MA">Massachusetts</option>
                                        <option value="MI">Michigan</option>
                                        <option value="MN">Minnesota</option>
                                        <option value="MS">Mississippi</option>
                                        <option value="MO">Missouri</option>
                                        <option value="MT">Montana</option>
                                        <option value="NE">Nebraska</option>
                                        <option value="NV">Nevada</option>
                                        <option value="NH">New Hampshire</option>
                                        <option value="NJ">New Jersey</option>
                                        <option value="NM">New Mexico</option>
                                        <option value="NY">New York</option>
                                        <option value="NC">North Carolina</option>
                                        <option value="ND">North Dakota</option>
                                        <option value="OH">Ohio</option>
                                        <option value="OK">Oklahoma</option>
                                        <option value="OR">Oregon</option>
                                        <option value="PA">Pennsylvania</option>
                                        <option value="RI">Rhode Island</option>
                                        <option value="SC">South Carolina</option>
                                        <option value="SD">South Dakota</option>
                                        <option value="TN">Tennessee</option>
                                        <option value="TX">Texas</option>
                                        <option value="UT">Utah</option>
                                        <option value="VT">Vermont</option>
                                        <option value="VA">Virginia</option>
                                        <option value="WA">Washington</option>
                                        <option value="WV">West Virginia</option>
                                        <option value="WI">Wisconsin</option>
                                        <option value="WY">Wyoming</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Street Address 2 (Optional)</label>
                                    <input type="text" class="form-control" name="address2" placeholder="Complex Name / Second Address">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Suite/Apt</label>
                                    <input type="text" class="form-control" name="suite" placeholder="Apt/Suite #">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Closest Intersection</label>
                                    <input type="text" class="form-control" name="closest_intersection" placeholder="Intersection">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>County</label>
                                    <input type="text" class="form-control" name="county" placeholder="County name">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Stairs</label>
                                    <select name="stairs" class="form-control">
                                        <option disabled selected value="">Select one..</option>
                                        <option value="No stairs">No stairs</option>
                                        <option value="1 flight">1 flight</option>
                                        <option value="2 flights">2 flights</option>
                                        <option value="Elevator">Elevator</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Parking Distance</label>
                                    <select name="distance" class="form-control">
                                        <option disabled selected value="">Select one..</option>
                                        <option value="Less than 50">Less than 50</option>
                                        <option value="More than 50">More than 50</option>
                                        <option value="More than 100">More than 100</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row extra-forms">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Bedrooms</label>
                                    <select class="form-control" name="bedrooms">
                                        <option disabled selected value="">Select one..</option>
                                        <option value="Miscellaneous Items">Miscellaneous Items</option>
                                        <option value="1 Bedroom">1 Bedroom</option>
                                        <option value="2 Bedrooms">2 Bedrooms</option>
                                        <option value="3 Bedrooms">3 Bedrooms</option>
                                        <option value="4 Bedrooms">4 Bedrooms</option>
                                        <option value="5 Bedrooms">5 Bedrooms</option>
                                        <option value="6+ Bedroom">6+ Bedrooms</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Garage</label>
                                    <select class="form-control" name="garage">
                                        <option disabled selected value="">Select one..</option>
                                        <option value="No garage">No garage</option>
                                        <option value="1 Car">1 Car</option>
                                        <option value="2 Cars">2 Cars</option>
                                        <option value="3 Cars">3 Cars</option>
                                        <option value="4+ Cars">4+ Cars</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Square Footage</label>
                                    <select name="sqft" class="form-control">
                                        <option disabled selected value="">Select one..</option>
                                        <option value="Less than 1000sqft">Less than 1000sqft</option>
                                        <option value="Less than 1500sqft">Less than 1500sqft</option>
                                        <option value="Less than 2000sqft">Less than 2000sqft</option>
                                        <option value="Less than 2500sqft">Less than 2500sqft</option>
                                        <option value="Less than 3000sqft">Less than 3000sqft</option>
                                        <option value="Less than 3500sqft">Less than 3500sqft</option>
                                        <option value="Less than 4000sqft">Less than 4000sqft</option>
                                        <option value="Less than 4500sqft">Less than 4500sqft</option>
                                        <option value="More than 4500sqft+">More than 4500sqft+</option>
                                        <option value="More than 5000sqft+">More than 5000sqft+</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Special Item(s)</label>
                                    <input type="text" class="form-control" name="special" placeholder="Special Item(s)">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Comments</label>
                                    <input type="text" class="form-control" name="comments">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn red">Save location</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <script type="text/javascript">
        $(document).ready(function() {
            <?php if(isset($_GET['conf']) && $_GET['conf'] == true) { ?> $('#e_conf').modal('show'); <?php } ?>

            $('.dataaa').dataTable({
                "order": [[ 0, "asc" ]],
                "bFilter" : true,
                "bLengthChange": true,
                "bPaginate": true,
                "info": true
            });
            $('.open_ticket').unbind().on('click', function() {
                $.ajax({
                    url: 'assets/app/add_setting.php?setting=ticket&luid=<?php echo $event['event_location_token']; ?>&uuid=<?php echo $event['event_user_token']; ?>&ev=<?php echo $event['event_token']; ?>&cuid=<?php echo $event['event_company_token']; ?>&m_token=<?php echo $_SESSION['uuid']; ?>',
                    type: "POST",
                    data: {
                        department: "General Support",
                        priority:   "High",
                        message:    "I've opened a support ticket for you, <?php $n = explode(" ", name($event['event_user_token'])); echo $n[0]; ?>. You can reply with images and messages below."
                    },
                    success: function (dat) {
                        var data = JSON.parse(dat);
                        toastr.success("<strong>Logan says</strong>:<br/>Magic. I'm taking you to your newly created support ticket now..");
                        $.ajax({
                            url: 'assets/pages/tickets.php?tk='+ data.tk,
                            type: "POST",
                            success: function (data) {
                                $('#page_content').html(data);
                            },
                            error: function () {
                                toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later. B");
                            }
                        });
                    },
                    error: function () {
                        toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later. C");
                    }
                });
            });
            $(function() {
                // IMPORTANT: Fill in your client key
                var clientKey = "js-InlLzUGLaGPQYhaSPQrQGnDmZH0HPvLyT6ks10ebG31Ekcxa3Y0KmE6ml73bDOJw";

                var cache = {};
                var container = $("#new_location");

                /** Handle successful response */
                function handleResp(data) {
                    // Check for error
                    if (data.error_msg) toastr.error("<strong>Logan says:</strong><br/>"+data.error_msg);
                    else if ("city" in data) {
                        // Set city and state
                        container.find("input[name='city']").val(data.city);
                        container.find('.state option[value="'+data.state+'"]').attr("selected", "selected");
                    }
                }
                // Set up event handlers
                container.find("input[name='zip']").on("keyup change", function() {
                    // Get zip code
                    var zipcode = $(this).val().substring(0, 5);
                    if (zipcode.length == 5 && /^[0-9]+$/.test(zipcode)) {
                        // Check cache
                        if (zipcode in cache) {
                            handleResp(cache[zipcode]);
                        } else {
                            // Build url
                            var url = "https://www.zipcodeapi.com/rest/"+clientKey+"/info.json/" + zipcode + "/radians";
                            // Make AJAX request
                            $.ajax({
                                "url": url,
                                "dataType": "json"
                            }).done(function(data) {
                                handleResp(data);

                                // Store in cache
                                cache[zipcode] = data;
                            }).fail(function(data) {
                                if (data.responseText && (json = $.parseJSON(data.responseText))) {
                                    // Store in cache
                                    cache[zipcode] = json;

                                    // Check for error
                                    if (json.error_msg) toastr.error("<strong>Logan says:</strong><br/>"+json.error_msg);
                                } else toastr.error("<strong>Logan says:</strong><br/>Unknown error. You really f**ked up!");
                            });
                        }
                    }
                }).trigger("change");
            });

            function initMap() {
                var directionsService = new google.maps.DirectionsService;
                var directionsDisplay = new google.maps.DirectionsRenderer;
                var map = new google.maps.Map(document.getElementById('gmap_basic'), {
                    zoom: 6,
                    center: {lat: 41.85, lng: -87.65}
                });
                directionsDisplay.setMap(map);
                calculateAndDisplayRoute(directionsService, directionsDisplay);
            }

            function calculateAndDisplayRoute(directionsService, directionsDisplay) {
                var waypts = [];
                waypts.push({
                    location: "<?php echo $pk_strt; ?>, <?php echo $pk_city; ?>, <?php echo $pk_state; ?>, <?php echo $pk_zip; ?>",
                    stopover: true
                });

                directionsService.route({
                    origin: "<?php echo locationAddress($event['event_location_token']); ?>",
                    destination: "<?php echo $ds_strt; ?>, <?php echo $ds_city; ?>, <?php echo $ds_state; ?>, <?php echo $ds_zip; ?>",
                    waypoints: waypts,
                    optimizeWaypoints: true,
                    travelMode: 'DRIVING'
                }, function(response, status) {
                    if (status === 'OK') {
                        directionsDisplay.setDirections(response);
                        var route = response.routes[0];
                        var total = 0;
                        var summaryPanel = document.getElementById('results-map-panel');
                        summaryPanel.innerHTML = '';
                        // For each route, display summary information.
                        for (var i = 0; i < route.legs.length; i++) {
                            var routeSegment = i + 1;
                            var mi           = route.legs[i].distance.text.replace(/[^\d.-]/g, '');
                             if(routeSegment == 1){
                                 summaryPanel.innerHTML += 'From <strong>dispatch</strong> to <strong>first location</strong> (' + route.legs[i].distance.text + '): ' + routeSegment +
                                     '<br>';
                             } else {
                                 summaryPanel.innerHTML += 'From <strong>pickup</strong> to <strong>destination</strong> (' + route.legs[i].distance.text + '): ' + routeSegment +
                                     '<br>';
                             }
                             total += +mi;
                            summaryPanel.innerHTML += route.legs[i].start_address + ' <strong>to</strong>  ';
                            summaryPanel.innerHTML += route.legs[i].end_address + ' <br/><br/>';
                        }
                        summaryPanel.innerHTML += '<strong>Total milage</strong>: ' + total.toFixed(2); + ' mi';
                    } else {
                        toastr.error("<strong>Logan says:</strong><br/>There is not enough information to route the trip. Please add at least 1 pickup and destination location.")
                    }
                });
            }

            initMap();
            $(".laborers").select2({
                placeholder: 'Select laborer..'
            }).on('change', function() {
                $(this).valid();
            });
            $(".estimators").select2({
                placeholder: 'Select estimator..'
            }).on('change', function() {
                var who = $(this).find(":selected").data("who");
                var pho = $(this).find(":selected").data("pho");
                $('.send_txt').removeAttrs('disabled');
                $('#es_to').html('to '+ who);$('.send_txt').attr('data-txt', pho);
            });
            $('.send_txt').on('click', function(){
                var uuid = $(this).attr('data-txt');
                $.ajax({
                    url: 'assets/app/texting.php?txt=estimate_link',
                    type: 'POST',
                    data: {
                        uuid: uuid,
                        ev: "<?php echo $event['event_token']; ?>"
                    },
                    success: function(f){
                        toastr.success("<strong>Logan says:</strong><br/>Message was sent to the phone number associated with them. They should recieve it momentarily. ");
                    },
                    error: function(f){

                    }
                })
            });
            $('.new_estimate').on('click', function(){
                $.ajax({
                    url: 'assets/pages/event.php?ev=<?php echo $_GET['ev']; ?>&luid=<?php echo $_GET['luid']; ?>',
                    success: function(data) {
                        $('#page_content').html(data);
                    },
                    error: function() {
                        toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                    }
                });
                window.open("https://www.formoversonly.com/dashboard/assets/public/index.php?e=EmP&ev=<?php echo $event['event_token']; ?>&v=c&n=<?php echo struuid(true); ?>&uuid=<?php echo $_SESSION['uuid']; ?>");
            });
            $('.scroller').slimScroll({
                height: 450,
                allowPageScroll: true
            });
            $('.scroller2').slimScroll({
                height: 700,
                allowPageScroll: true
            });
            $('.add_item').off('click');
            $('.datatable').each(function(){
                var url = $(this).attr('data-src');
                $(this).DataTable({
                    "processing": true,
                    "serverSide": true,
                    //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                    "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
                    "bPaginate": false,
                    "bFilter": false,
                    "info": false,
                    "ajax": {
                        "url": url, // ajax source
                    },
                    "lengthMenu": [
                        [10, 20, 50, 100, 150, -1],
                        [10, 20, 50, 100, 150, "All"] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "order": [
                        [1, "asc"]
                    ]// set first column as a default sort by asc
                });
            });


            function updateI(){
                $.ajax({
                    url: 'assets/app/api/event.php?type=inv&luid=<?php echo $event['event_location_token']; ?>',
                    type: 'POST',
                    data: {
                        event: '<?php echo $event['event_token']; ?>'
                    },
                    success: function(m){
                        var owe = JSON.parse(m);
                        $(document).find('#owe_sub_total').html(parseFloat(owe.sub_total).toFixed(2));
                        $(document).find('#owe_tax').html(parseFloat(owe.tax).toFixed(2));
                        $(document).find('#owe_total').html(parseFloat(owe.total).toFixed(2));
                        $(document).find('#PLPAP_SUBTOTAL').html(parseFloat(owe.sub_total).toFixed(2));
                        $(document).find('#PLPAP_TAXES').html(parseFloat(owe.tax).toFixed(2));
                        $(document).find('#PLPAP_TOTAL').html(parseFloat(owe.total).toFixed(2));
                        $(document).find('#owe_total_unpaid').html(parseFloat(owe.unpaid).toFixed(2));
                        $(document).find('#owe_paid').html(parseFloat(owe.paid).toFixed(2));
                        if(parseFloat(owe.unpaid).toFixed(2) > 0){
                            $(document).find('#owe_alert').show();
                            $(document).find('#owe_alert').html("<i class='fa fa-exclamation-triangle'></i> UNPAID - $" + parseFloat(owe.unpaid).toFixed(2));
                        } else {
                            $(document).find('#owe_alert').hide();
                            $(document).find('#owe_alert').html("");
                        }
                        if(parseFloat(owe.cc_fees).toFixed(2) > 0){
                            $(document).find("#cc_fees").show();
                            $(document).find("#owe_cc_fees").html(parseFloat(owe.cc_fees).toFixed(2));
                            $(document).find(".load_payments").removeClass("margin-top-15");
                        } else {
                            $(document).find("#cc_fees").hide();
                            $(document).find("#owe_cc_fees").html("");
                            $(document).find(".load_payments").addClass("margin-top-15");
                        }
                        if(parseFloat(owe.taxable).toFixed(2) > 0){
                            $(document).find("#taxable_fees").show();
                            $(document).find("#commie_fees").show();
                            $(document).find("#taxable_fees").html("($"+ parseFloat(owe.taxable).toFixed(2) +" taxable)");
                            $(document).find("#commie_fees").html("($"+ parseFloat(owe.coms).toFixed(2) +" commissionable)");
                        } else {
                            $(document).find("#taxable_fees").hide();
                            $(document).find("#commie_fees").hide();
                        }
                    },
                    error: function(e){

                    }
                });
            }
            updateI();
            $('#add_laborer').validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",
                rules: {
                    role: {
                        required: true
                    },
                    laborer: {
                        required: true
                    }
                }
            });
            $('.add_laborer').on('click', function(){
                if($("#add_laborer").valid()){
                    $.ajax({
                        url: "assets/app/add_setting.php?setting=laborer&ev=<?php echo $event['event_token']; ?>&luid=<?php echo $location['location_token']; ?>",
                        type: "POST",
                        data: $('#add_laborer').serialize(),
                        success: function(data) {
                            toastr.info('<strong>Logan says</strong>:<br/>'+data+' has been added to your list of laborers for this event.');
                        },
                        error: function() {
                            toastr.error('<strong>Logan says</strong>:<br/>That page didnt respond correctly. Try again, or create a support ticket for help.');
                        }
                    });
                }
            });
            $('#add_assets').validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",
                rules: {
                    asset: {
                        required: true
                    }
                }
            });
            $('.add_asset').on('click', function(){
                if($("#add_assets").valid()){
                    $.ajax({
                        url: "assets/app/add_setting.php?setting=evasset&ev=<?php echo $event['event_token']; ?>",
                        type: "POST",
                        data: $('#add_assets').serialize(),
                        success: function(data) {
                            toastr.info('<strong>Logan says</strong>:<br/>'+data+' has been added to this events asset tracker.');
                        },
                        error: function() {
                            toastr.error('<strong>Logan says</strong>:<br/>That page didnt respond correctly. Try again, or create a support ticket for help.');
                        }
                    });
                }
            });
            $(document).on('click', '.del_asset', function(){
               var id = $(this).attr('data-id');
               $(this).closest('tr').remove();
                $.ajax({
                    url: 'assets/app/update_settings.php?adm=del_asset',
                    type: 'POST',
                    data: {
                        id: id
                    },
                    success: function (d) {
                        toastr.success("<strong>Logan says</strong>:<br/>I deleted that asset from this event.");

                    },
                    error: function () {
                        toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                    }
                });
            });
            $('.show_form').on('click', function() {
                var show = $(this).attr('data-show');

                $(show).show();
            });

            function reload() {
                $.ajax({
                    url: 'assets/pages/event.php?ev=<?php echo $_GET['ev']; ?>&luid=<?php echo $_GET['luid']; ?>',
                    success: function(data) {
                        $('#page_content').html(data);
                    },
                    error: function() {
                        toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                    }
                });
            }

            $('.adj_conf').on('click', function() {
                reload();
            });

            $('.addition').unbind().on('click', function() {
                var value = $(this).find('.hidden-checkbox').val();
                $(this).toggleClass("green");
                $(this).toggleClass("green-stripe");
                $(this).find('.fa').toggleClass('fa-check');
                $(this).find('.fa').toggleClass('fa-times');
                $(this).find('.hidden-checkbox').prop("checked", !$(this).find('.hidden-checkbox').prop("checked"));
                if(!$(this).find('.hidden-checkbox').attr('checked')){
                    $.ajax({
                        url: 'assets/app/update_settings.php?update=ev_additions&t=r',
                        type: 'POST',
                        data: {
                            value: value,
                            ev: '<?php echo $event['event_token']; ?>'
                        },
                        success: function(){
                            toastr.info(value+" removed.");
                        },
                        error: function(){

                        }
                    });
                } else {
                    $.ajax({
                        url: 'assets/app/update_settings.php?update=ev_additions&t=a',
                        type: 'POST',
                        data: {
                            value: value,
                            ev: '<?php echo $event['event_token']; ?>'
                        },
                        success: function(){
                            toastr.info(value+" added.");
                        },
                        error: function(){

                        }
                    });
                }
            });

            Stripe.setPublishableKey('pk_live_ftqBPIkJ6eBemXHToHiU8Eqa');

            function stripeResponseHandler(status, response) {
                // Grab the form:
                var $form = $('#booking_fee_form');

                if (response.error) { // Problem!

                    // Show the errors on the form:
                    toastr.error("<strong>Logan says:</strong><br/>" + response.error.message);
                    $form.find('#booking_fee').prop('disabled', false); // Re-enable submission
                    $('#booking_fee').html("Check your fields. Try again? <i class='fa fa-credit-card'></i>");

                } else { // Token was created!

                    // Get the token ID:
                    var token = response.id;

                    // Insert the token ID into the form so it gets submitted to the server:
                    //$form.append($('<input type="hidden" name="auth">').val(token));

                    $.ajax({
                        url: 'assets/app/charge.php?ev=<?php echo $event['event_token']; ?>',
                        type: 'post',
                        data: {
                            token: token,
                            amount: 1000,
                            email: "<?php echo $event['event_email']; ?>"
                        },
                        success: function(data) {
                            if (data.length > 8) {
                                toastr.info("<strong>Logan says:</strong><br/>Card was charged successfully, here's the confirmation token: " + data);
                                $('#booking_notes').removeAttr('disabled');
                                $('#booking_notes').attr('value', data);
                                $.ajax({
                                    url: 'assets/app/update_settings.php?update=event_fly&tok='+data+'&uuid=<?php echo $event['event_user_token']; ?>&era=post',
                                    type: 'POST',
                                    data: {
                                        name: 'event_booking',
                                        value: 1,
                                        pk: '<?php echo $event['event_token']; ?>'
                                    },
                                    success: function(s){
                                        $.ajax({
                                            url: 'assets/pages/event.php?ev=<?php echo $_GET['ev']; ?>&luid=<?php echo $_GET['luid']; ?>',
                                            success: function(data) {
                                                $('#page_content').html(data);
                                            },
                                            error: function() {
                                                toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                                            }
                                        });
                                        toastr.success("<strong>Logan says:</strong><br/>Booking fee paid, 10$ has been charged to the card you provided.");
                                    },
                                    error: function(s){

                                    }
                                });
                            }
                            if (data == 'error-4'){
                                $('#booking_fee').html("Card declined. Try again? <i class='fa fa-credit-card'></i>");
                                toastr.error("<strong>Logan says:</strong><br/>Card was declined. Please try again using a different card, or review your card information for mistakes.");
                                $form.find('#booking_fee').prop('disabled', false); // Re-enable submission
                            }

                            if (data == 'error-2'){
                                $('#booking_fee').html("Error. Try again? <i class='fa fa-credit-card'></i>")
                                toastr.error("<strong>Logan says:</strong><br/>A programatic error has occured, and the card has not been charged.");
                                $form.find('#booking_fee').prop('disabled', false);
                            }
                        },
                        error: function(data) {
                            console.log("Ajax Error!");
                            console.log(data);
                        }
                    });
                }
            };

            $('#booking_fee').unbind().click(function(ee) {
                var $form  = $('#booking_fee_form');
                // Disable the submit button to prevent repeated clicks:
                $('#booking_fee').attr("disabled","disabled");
                $('#booking_fee').html("<i class='fa fa-spinner fa-spin'></i>");
                console.log("disabled!");

                // Request a token from Stripe:
                Stripe.card.createToken($form, stripeResponseHandler);

                // Prevent the form from being submitted:
                return false;
            });

            $('.card_num').inputmask("mask", {
                "mask": "9999 9999 9999 9999",
                "removeMaskOnSubmit": false,
                "placeholder": ""
            });
            $('.exp_date').inputmask("mask", {
                "mask": "99/99",
                "removeMaskOnSubmit": false,
                "placeholder": ""
            });
            $('.cvc_num').inputmask("mask", {
                "mask": "9999",
                "placeholder": ""
            });



            $('#new_location').validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",
                rules: {
                    type: {
                        required: true
                    },
                    address: {
                        required: true
                    },
                    city: {
                        required: true
                    },
                    state: {
                        required: true
                    },
                    zip: {
                        required: true,
                        number: true,
                        maxlength: 5,
                        minlength: 5
                    },
                    comments: {
                        maxlength: 100
                    }
                },

                messages: {
                    zip: {
                        min: "Please enter a 5 digit zipcode.",
                        max: "Please enter a 5 digit zipcode."
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
                        url: 'assets/app/add_event.php?ev=pmk&e=<?php echo $event['event_token']; ?>',
                        type: "POST",
                        data: $('#new_location').serialize(),
                        success: function(data) {
                            $('#draggable').modal('hide')
                            $('#new_location')[0].reset();
                            toastr.success("<strong>Logan says</strong>:<br/>That location has been added to this events record. Let me refresh the event for you, so you can see the changes.");
                            $.ajax({
                                url: 'assets/pages/event.php?ev=<?php echo $_GET['ev']; ?>&luid=<?php echo $_GET['luid']; ?>',
                                success: function(data) {
                                    $('#page_content').html(data);
                                },
                                error: function() {
                                    toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                                }
                            });
                        },
                        error: function() {
                            toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                        }
                    });
                }
            });
            $("#add_comt").validate({
                errorElement: 'span', //default input error message container
                errorClass: 'font-red', // default input error message class
                rules: {
                    comment: {
                        required: true
                    }
                }
            });
            $('.add_comment').on('click', function(){
                if($("#add_comt").valid()){
                    $.ajax({
                        url: "assets/app/add_setting.php?setting=ev_cmt&ev=<?php echo $event['event_token']; ?>",
                        type: "POST",
                        data: $('#add_comt').serialize(),
                        success: function(data) {
                            toastr.info('<strong>Logan says</strong>:<br/>Comment has been added to events comment history.');
                        },
                        error: function() {
                            toastr.error('<strong>Logan says</strong>:<br/>That page didnt respond correctly. Try again, or create a support ticket for help.');
                        }
                    });
                }
            });
            $('.add_document').on('click', function(){
                if($("#add_documents").valid()){
                    $.ajax({
                        url: "assets/app/add_setting.php?setting=ev_document&ev=<?php echo $event['event_token']; ?>",
                        type: "POST",
                        data: new FormData($('#add_documents')[0]),
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                        encode: true,
                        success: function(data) {
                            toastr.info('<strong>Logan says</strong>:<br/>Document has been added to users documents table.');
                            $("#add_documents")[0].reset();
                            $('#docs').DataTable().ajax.reload();
                        },
                        error: function() {
                            toastr.error('<strong>Logan says</strong>:<br/>That page didnt respond correctly. Try again, or create a support ticket for help.');
                        }
                    });
                }
            });
            $('.change_type').on('click', function(){
                var type   = $(this).attr('data-type');
                var id     = $(this).attr('data-id');
                $.ajax({
                    url: 'assets/app/update_settings.php?update=change_type&ev=<?php echo $event['event_token']; ?>',
                    type: 'POST',
                    data: {
                        type: type,
                        value: id
                    },
                    success: function(d){
                        $.ajax({
                            url: 'assets/pages/event.php?ev=<?php echo $_GET['ev']; ?>&luid=<?php echo $_GET['luid']; ?>',
                            success: function(data) {
                                $('#page_content').html(data);
                            },
                            error: function() {
                                toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                            }
                        });
                    },
                    error: function(e){
                        toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                    }
                });
            });

            function updateCountdown2() {
                var remaining = 500 - $('.bol_comments').val().length;
                $('.bol_countdown').html('('+ remaining + ' characters remaining)');
            }

            updateCountdown2();

            $('.bol_comments').change(updateCountdown2);
            $('.bol_comments').keyup(updateCountdown2);
            $('.bol_comments').on('change', function(){
                var comment = $(this).val();
                updateCountdown2();
                $.ajax({
                    url: 'assets/app/update_settings.php?update=ev_bol_comments',
                    type: 'POST',
                    data: {
                        comment: comment,
                        ev: '<?php echo $event['event_token']; ?>'
                    },
                    success: function(bol_cmts){
                        toastr.success("<strong>Logan says:</strong><br/> BOL comments saved (see? told you). ");
                    },
                    error: function(){

                    }
                }) ;
            });

            var a = $('#truckfee').attr('data-a');
            var b = $('#laborrate').attr('data-b');
            var c = $('#countyfee').attr('data-c');
            $.ajax({
                url: 'assets/app/api/event.php?type=math&ev=<?php echo $event['event_token']; ?>',
                type: 'POST',
                data: {
                    a: $(a).text(),
                    b: $(b).text(),
                    c: $(c).text()
                },
                success: function(d){
                    var e = JSON.parse(d);
                    $("#TF").html(e.truck_fee);
                    $("#LR").html(e.total_labor_rate);
                    $("#CF").html(e.county_fee);
                    $("#bol_TF").html(e.truck_fee);
                    $("#bol_LR").html(e.total_labor_rate);
                    $("#bol_CF").html(e.county_fee);
                    $("#bol_TR_qty").html($(a).text());
                    $("#bol_LR_qty").html($(b).text());
                    $("#bol_CR_qty").html($(c).text());
                },
                error: function(e){

                }
            });

            $('.rate_changer').on('click', function(){
                var name   = $(this).attr('data-name');
                var value  = $(this).attr('data-value');
                $.ajax({
                    url: 'assets/app/update_settings.php?update=event_fly',
                    type: 'POST',
                    data: {
                        name: name,
                        value: value,
                        pk: "<?php echo $event['event_token']; ?>"
                    },
                    success: function(d){
                        $('.'+name+'_out').html(value);
                        var a = $('#truckfee').attr('data-a');
                        var b = $('#laborrate').attr('data-b');
                        var c = $('#countyfee').attr('data-c');
                        $.ajax({
                            url: 'assets/app/api/event.php?type=math&ev=<?php echo $event['event_token']; ?>',
                            type: 'POST',
                            data: {
                                a: $(a).text(),
                                b: $(b).text(),
                                c: $(c).text()
                            },
                            success: function(d){
                                var e = JSON.parse(d);
                                $("#TF").html(e.truck_fee);
                                $("#LR").html(e.total_labor_rate);
                                $("#CF").html(e.county_fee);
                            },
                            error: function(e){

                            }
                        })
                    },
                    error: function(e){
                        toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                    }
                });
            });


            var start    = new Date("<?php echo date('Y/m/d', strtotime($event['event_date_start'])); ?>");
            var start_dd = start.getDate();
            var start_mm = start.getMonth()+1;
            var start_yy = start.getFullYear();
            if(start_dd<10) {
                start_dd = '0'+start_dd
            }

            if(start_mm<10) {
                start_mm = '0'+start_mm
            }
            var end   = new Date("<?php echo date('Y/m/d', strtotime($event['event_date_end'])); ?>");
            var end_dd = end.getDate();
            var end_mm = end.getMonth()+1;
            var end_yy = end.getFullYear();
            if(end_dd<10) {
                end_dd = '0'+end_dd
            }

            if(end_mm<10) {
                end_mm = '0'+end_mm
            }

            $('#dashboard-report-range').daterangepicker({
                    opens: (Metronic.isRTL() ? 'right' : 'left'),
                    startDate: start_yy + '-' + start_mm + '-' + start_dd,
                    endDate: end_yy + '-' + end_mm + '-' + end_dd,
                    showDropdowns: false,
                    showWeekNumbers: false,
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
                        firstDay: 0
                    }
                },
                function (start, end) {
                    $('#dashboard-report-range span').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
                    $.ajax({
                        type: "POST",
                        url: "assets/app/update_settings.php?update=event_date&luid=<?php echo $event['event_location_token']; ?>",
                        data: {
                            dateStart: start.format('YYYY-MM-DD'),
                            dateEnd: end.format('YYYY-MM-DD'),
                            ev: '<?php echo $event['event_token']; ?>'
                        },
                        success: function(result) {
                            toastr.info(""+result+"");
                        }
                    });
                }
            );

            console.log(start_yy + '-' + start_mm + '-' + start_dd);
            console.log(end_yy + '-' + end_mm + '-' + end_dd);

            $(document).on('click', '#payments .button-cancel', function () {
                Pace.track(function(){
                    $('#paid').DataTable().ajax.reload();
                    $('#payments-maked').show();
                    $('#payments-content').html("");
                    $('.load_payments').html("Take another payment?");
                    $('.load_payments').removeClass("red");
                    $('.load_payments').addClass("green");
                });
            });

            $('#dashboard-report-range').show();

            $('.fire').click(function(f){
                var fire = $(this).attr('data-fire');
                $.ajax({
                    url: 'assets/app/texting.php?txt='+fire,
                    type: 'POST',
                    data: {
                        ev: '<?php echo $event['event_token']; ?>'
                    },
                    success: function(f){
                        toastr.success("<strong>Logan says:</strong><br/>Message was sent to the phone number associated with the event. They should recieve it momentarily. ");
                    },
                    error: function(f){

                    }
                })
            });

            $('.submit_cancel').click(function(){
                $.ajax({
                    url: 'assets/app/update_settings.php?update=change_type&ev=<?php echo $event['event_token']; ?>',
                    type: 'POST',
                    data: {
                        type: 'status',
                        value: 5,
                        reasoning: $('.cancel_reason').val()
                    },
                    success: function(s){
                        $.ajax({
                            url: 'assets/pages/dashboard.php?luid=<?php echo $event['event_location_token']; ?>',
                            success: function(data) {
                                $('#page_content').html(data);
                            },
                            error: function() {
                                toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                            }
                        });
                        toastr.success("<strong>Logan says:</strong><br/>This event was only cancelled, not deleted.");
                    },
                    error: function(s){

                    }
                });
            });

            $('.del_location').on('click', function(){
               var id = $(this).attr('data-id');
                $.ajax({
                    url: 'assets/app/update_settings.php?adm=del_location',
                    type: 'POST',
                    data: {
                        id: id
                    },
                    success: function (d) {
                        toastr.success("<strong>Logan says</strong>:<br/>I deleted that location from this event.");
                        $('.r_l_'+id).remove();
                    },
                    error: function () {
                        toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                    }
                });
            });

            $(".rateYo").rateYo({
                readOnly: true,
                halfStar: true
            });

            <?php
            if($_SESSION['group'] == 1){
                ?>
                $('#del_ev').click(function(E) {
                    $('#tools_only').modal('hide');
                    swal({
                        title: 'Are you sure?',
                        text: 'You will not be able to recover this event!',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'No, keep it'
                    }).then(function() {

                        $.ajax({
                            url: 'assets/app/update_settings.php?adm=delete_ev',
                            type: 'POST',
                            data: {
                                name: 'del',
                                value: '<?php echo $event['event_token']; ?>'
                            },
                            success: function(s){
                                $.ajax({
                                    url: 'assets/pages/dashboard.php?luid=<?php echo $event['event_location_token']; ?>',
                                    success: function(data) {
                                        $('#page_content').html(data);
                                    },
                                    error: function() {
                                        toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                                    }
                                });
                                toastr.success("<strong>Logan says:</strong><br/>Event deleted. Thanks for cleaning up, boss!");
                            },
                            error: function(s){

                            }
                        });

                        swal(
                            'Deleted!',
                            'The event has been deleted.',
                            'success'
                        );

                    }, function(dismiss) {
                        // dismiss can be 'overlay', 'cancel', 'close', 'esc', 'timer'
                        if (dismiss === 'cancel') {
                            swal(
                                'Cancelled',
                                'Your record is safe :)',
                                'error'
                            )
                        }
                    });

                });
                <?php
            }
            ?>

            $('#draggable').on('show.bs.modal', function(e) {

                //get data-id attribute of the clicked element
                var type = $(e.relatedTarget).data('location-type');
                var zip  = "<?php echo $event['event_zip']; ?>";

                if(type == 1){
                    $('.extra-forms').show();
                } else {
                    $('.extra-forms').hide();
                }

                //populate the textbox
                $('#zip_auto').val(zip).trigger("change");
                $('#type option[value="'+type+'"]').attr("selected", "selected");
            });
        });
    </script>
    <?php
} else {
    header("Location: ../../../index.php?err=no_access");
}
?>
