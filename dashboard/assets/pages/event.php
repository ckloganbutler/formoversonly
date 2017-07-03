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
    $event    = mysql_fetch_array(mysql_query("SELECT event_id, event_token, event_location_token, event_booking, event_user_token, event_name, event_date_start, event_date_end, event_time, event_truckfee, event_laborrate, event_countyfee, event_status, event_email, event_phone, event_type, event_subtype, event_additions, event_comments FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($_GET['ev'])."'"));
    $location = mysql_fetch_array(mysql_query("SELECT location_name, location_token FROM fmo_locations WHERE location_token='".mysql_real_escape_string($event['event_location_token'])."'"));
    $user     = mysql_fetch_array(mysql_query("SELECT user_id, user_fname, user_lname, user_email, user_phone, user_token FROM fmo_users WHERE user_token='".mysql_real_escape_string($event['event_user_token'])."'"));
    switch($event['event_status']){
        case 1: $status = "New Booking"; break;
        case 2: $status = "Confirmed"; break;
        case 3: $status = "Left Message"; break;
        case 4: $status = "On Hold"; break;
        case 5: $status = "Cancelled"; break;
        default: $status = "On Hold"; break;
    }
    ?>
    <div class="page-content">
        <h3 class="page-title">
            <strong><?php echo $event['event_name']; ?></strong> | <small>(EVENT ID #0<?php echo $event['event_id']; ?>)</small>

            <div class="btn-group pull-right">
                <a class="btn red dropdown-toggle" href="javascript:;" data-toggle="dropdown">
                    <i class="fa fa-clock-o"></i> Event Time: <strong><?php echo $event['event_time']; ?></strong> <i class="fa fa-angle-down"></i>
                </a>
                <ul class="dropdown-menu pull-right">
                    <?php
                    $timeOptions = mysql_query("SELECT time_start, time_end FROM fmo_locations_times WHERE time_location_token='".mysql_real_escape_string($event['event_location_token'])."'");
                    if(mysql_num_rows($timeOptions) > 0){
                        while($t = mysql_fetch_assoc($timeOptions)){
                            if(empty($t['time_end'])){
                                $t['time_end'] = "finish";
                            }
                            ?>
                            <li>
                                <a class="change_type" data-id="<?php echo $t['time_start']; ?> to <?php echo $t['time_end']; ?>" data-type="eventtime"><?php echo $t['time_start']; ?> to <?php echo $t['time_end']; ?></a>
                            </li>
                            <?php
                        }
                    }
                    ?>
                </ul>
            </div>
            <a id="dashboard-report-range" class="pull-right tooltips btn red" data-container="body" data-placement="bottom" data-original-title="Change dashboard date range">
                <i class="icon-calendar"></i>&nbsp;
                <span class="bold uppercase visible-lg-inline-block">
                    <?php echo date('M d, Y', strtotime($event['event_date_start'])); ?> - <?php echo date('M d, Y', strtotime($event['event_date_end'])); ?>
                </span>&nbsp; <i class="fa fa-angle-down"></i>
            </a>
        </h3>
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <i class="fa fa-home"></i>
                    <a class="load_page" data-href="assets/pages/dashboard.php?luid=<?php echo $_GET['luid']; ?>"><?php echo $location['location_name']; ?></a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
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
                    <div class="pull-right tooltips btn btn-fit-height green" data-toggle="modal" href="#booking_fee">
                        <i class="fa fa-credit-card"></i>&nbsp; <span class="thin uppercase visible-lg-inline-block">BOOKING FEE PAID <i class="fa fa-arrow-right"></i> CLICK TO VIEW</span>
                    </div>
                    <?php
                } elseif ($event['event_booking'] == 0){
                    ?>
                    <div class="pull-right tooltips btn btn-fit-height grey-salt" id="pay">
                        <i class="fa fa-credit-card text-danger"></i>&nbsp; <span class="thin uppercase visible-lg-inline-block text-danger">BOOKING FEE UNPAID <i class="fa fa-arrow-right"></i> CLICK TO PAY</span>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <div class="actions btn-set">
                                    <div class="btn-group hidden-xs hidden-sm">
                                        <button class="btn red edit_inf" data-edit="truckfee">
                                            <i class="fa fa-truck"></i> Truck(s):
                                            <strong>
                                                <a id="truckfee" class="edits" data-a="#truckfee" data-b="#laborrate" data-c="#countyfee" style="color: white; text-decoration: none !important;" data-mode="popup" data-name="event_truckfee" data-pk="<?php echo $event['event_token']; ?>" data-type="number" data-placement="bottom" data-title="Enter new amount of truck(s)." data-inputclass="input-sm col-md-1" data-url="assets/app/update_settings.php?update=event_fly"><?php echo $event['event_truckfee']; ?></a>
                                            </strong> for <span id="TF">80</span>$
                                        </button>
                                    </div>
                                    <div class="btn-group hidden-xs hidden-sm">
                                        <button class="btn red edit_inf" data-edit="laborrate">
                                            <i class="fa fa-users"></i> Crewmen:
                                            <strong>
                                                <a id="laborrate" class="edits" data-a="#truckfee" data-b="#laborrate" data-c="#countyfee" style="color: white; text-decoration: none !important;" data-mode="popup" data-name="event_laborrate" data-pk="<?php echo $event['event_token']; ?>" data-type="number" data-placement="bottom" data-title="Enter new amount of crewmen." data-inputclass="input-sm col-md-1" data-url="assets/app/update_settings.php?update=event_fly"><?php echo $event['event_laborrate']; ?></a>
                                            </strong> for <span id="LR">90</span>$/hr
                                        </button>
                                    </div>
                                    <div class="btn-group hidden-xs hidden-sm">
                                        <button class="btn red edit_inf" data-edit="countyfee">
                                            <i class="fa fa-location-arrow"></i> Counties:
                                            <strong>
                                                <a id="countyfee" class="edits" data-a="#truckfee" data-b="#laborrate" data-c="#countyfee" style="color: white; text-decoration: none !important;" data-mode="popup" data-name="event_countyfee" data-pk="<?php echo $event['event_token']; ?>" data-type="number" data-placement="bottom" data-title="Enter new amount of counties." data-inputclass="input-sm col-md-1" data-url="assets/app/update_settings.php?update=event_fly"><?php echo $event['event_countyfee']; ?></a>
                                            </strong> for <span id="CF">0</span>$
                                        </button>
                                    </div>

                                    <div class="btn-group">
                                        <button class="btn blue mbol" data-toggle="modal" href="#print_bol" data-event="<?php echo $event['event_token']; ?>">
                                            <i class="fa fa-print"></i> Print BOL
                                        </button>
                                    </div>

                                    <div class="btn-group hidden-xs hidden-sm">
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

                                    <div class="btn-group hidden-xs hidden-sm">
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
                                            <i class="fa fa-cogs"></i> Status: <strong><?php echo $status; ?></strong> <i class="fa fa-angle-down"></i>
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
                                                    <a class="change_type" data-id="5" data-type="status">Change to Cancelled</a>
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
                                                  Comments & Additional Items
                                                    <span class="badge badge-danger"> <?php echo $add - 1; ?> </span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="portlet-body">
                                        <div class="tab-content">
                                            <textarea placeholder="BOL comments (psst! the comment you're about to type will automatically save when you're done typing." class="form-control bol_comments" style="height: 80px;"><?php echo $event['event_comments']; ?></textarea>
                                            <style type="text/css">
                                                .check {
                                                    opacity:0.5;
                                                    color:#996;
                                                }
                                            </style>
                                            <hr/>
                                            <div class="tab-pane active" id="additions">
                                                <label class="btn <?php if(!empty($extra['safe'])){ echo "red"; } ?> pull-right img-check" style="height: 34px; width: 130px; margin-left: 5px;">
                                                    <label style="padding-top: 1px;"><i class="fa <?php if(!empty($extra['safe'])){ echo "fa-check"; } ?>"></i> Safe</label>
                                                    <input type="checkbox" name="addition[]" id="safe" value="safe" <?php if(!empty($extra['safe'])){ echo "checked"; } ?> class="hidden hidden-checkbox" autocomplete="off">
                                                </label>
                                                <label class="btn <?php if(!empty($extra['play_set'])){ echo "red"; } ?> pull-right img-check" style="height: 34px; width: 130px;">
                                                    <label style="padding-top: 1px;"><i class="fa <?php if(!empty($extra['play_set'])){ echo "fa-check"; } ?>"></i> Play Set</label>
                                                    <input type="checkbox" name="addition[]" id="play_set" value="play_set" <?php if(!empty($extra['play_set'])){ echo "checked"; } ?> class="hidden hidden-checkbox" autocomplete="off">
                                                </label>
                                                <label class="btn <?php if(!empty($extra['pool_table'])){ echo "red"; } ?> pull-right img-check" style="height: 34px; width: 130px;">
                                                    <label style="padding-top: 1px;"><i class="fa <?php if(!empty($extra['pool_table'])){ echo "fa-check"; } ?> "></i> Pool Table</label>
                                                    <input type="checkbox" name="addition[]" id="pool_table" value="pool_table" <?php if(!empty($extra['pool_table'])){ echo "checked"; } ?> class="hidden hidden-checkbox" autocomplete="off">
                                                </label>
                                                <label class="btn <?php if(!empty($extra['piano'])){ echo "red"; } ?> pull-right img-check" style="height: 34px; width: 130px;">
                                                    <label style="padding-top: 1px;"><i class="fa <?php if(!empty($extra['piano'])){ echo "fa-check"; } ?>"></i> Piano</label>
                                                    <input type="checkbox" name="addition[]" id="piano" value="piano" <?php if(!empty($extra['piano'])){ echo "checked"; } ?> class="hidden hidden-checkbox" autocomplete="off">
                                                </label>
                                                <label class="btn <?php if(!empty($extra['hot_tub'])){ echo "red"; } ?> pull-right img-check" style="height: 34px; width: 130px;">
                                                    <label style="padding-top: 1px;"><i class="fa <?php if(!empty($extra['hot_tub'])){ echo "fa-check"; } ?>"></i> Hot Tub</label>
                                                    <input type="checkbox" name="addition[]" id="hot_tub" value="hot_tub" <?php if(!empty($extra['hot_tub'])){ echo "checked"; } ?> class="hidden hidden-checkbox" autocomplete="off">
                                                </label>
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
                        <div class="row">
                            <div class="col-md-4">
                                <div class="portlet">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            Pick up location(s)
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
                                        $pickups = mysql_query("SELECT address_id, address_address, address_suite, address_city, address_state, address_zip, address_closest_intersection, address_county, address_special, address_square_footage, address_bedrooms, address_garage, address_stairs, address_distance, address_comments FROM fmo_locations_events_addresses WHERE address_event_token='".mysql_real_escape_string($event['event_token'])."' AND address_type=1");
                                        if(mysql_num_rows($pickups) > 0){
                                            $pk = 0;
                                            while($pickup = mysql_fetch_assoc($pickups)){
                                                $pk++
                                                ?>
                                                <div id="pickup_h_<?php echo $pk; ?>" class="panel-group">
                                                    <div class="panel panel-default">
                                                        <div class="panel-heading">
                                                            <div class="actions pull-right" style="margin-top: -6px; margin-right: -9px">
                                                                <a href="javascript:;" class="btn btn-default btn-sm edit" data-edit="pu_<?php echo $pickup['address_id']; ?>">
                                                                    <i class="fa fa-pencil"></i> <span class="hidden-sm hidden-md hidden-xs">Edit</span> </a>
                                                            </div>
                                                            <div class="caption">
                                                                <h4 class="panel-title">
                                                                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#pickup_h_<?php echo $pk; ?>" href="#pickup_<?php echo $pk; ?>" aria-expanded="false"><strong><?php echo $pickup['address_address']; ?>, <?php echo $pickup['address_city']; ?>, <?php echo $pickup['address_state']; ?> <?php echo $pickup['address_zip']; ?></strong></a>
                                                                </h4>
                                                            </div>
                                                        </div>
                                                        <div id="pickup_<?php echo $pk; ?>" class="panel-collapse collapse" aria-expanded="true" style="height: 0px;">
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
                                                                    </a>
                                                                </address>
                                                                <address>
                                                                    Closest intersection:
                                                                    <strong>
                                                                        <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_closest_intersection" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new closest intersection.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                            <?php echo $pickup['address_closest_intersection']; ?>
                                                                        </a><br/>
                                                                    </strong>

                                                                    Square Footage:
                                                                    <strong>
                                                                        <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_square_footage" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new closest intersection.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                            <?php echo $pickup['address_square_footage']; ?>
                                                                        </a><br/>
                                                                    </strong>

                                                                    Bedrooms:
                                                                    <strong>
                                                                        <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_bedrooms" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new closest intersection.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                            <?php echo $pickup['address_bedrooms']; ?>
                                                                        </a><br/>
                                                                    </strong>

                                                                    Garage:
                                                                    <strong>
                                                                        <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_garage" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new closest intersection.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                            <?php echo $pickup['address_garage']; ?>
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
                            </div>
                            <div class="col-md-4">
                                <div id="gmap_basic" class="gmaps" style="height: 450px;">
                                </div>
                                <div class="" id="results-map-panel"></div>
                            </div>
                            <div class="col-md-4">
                                <div class="portlet">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            Destination location(s)
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
                                        $dests = mysql_query("SELECT address_address, address_suite, address_city, address_state, address_zip, address_closest_intersection, address_county, address_special, address_square_footage, address_bedrooms, address_garage, address_stairs, address_distance, address_comments FROM fmo_locations_events_addresses WHERE address_event_token='".mysql_real_escape_string($event['event_token'])."' AND address_type=2");
                                        if(mysql_num_rows($dests) > 0){
                                            $pk = 0;
                                            while($dest = mysql_fetch_assoc($dests)){
                                                $pk++
                                                ?>
                                                <div id="dest_h_<?php echo $pk; ?>" class="panel-group">
                                                    <div class="panel panel-default">
                                                        <div class="panel-heading">
                                                            <div class="actions pull-right" style="margin-top: -6px; margin-right: -9px">
                                                                <a href="javascript:;" class="btn btn-default btn-sm edit" data-edit="ds_<?php echo $dest['address_id']; ?>">
                                                                    <i class="fa fa-pencil"></i> <span class="hidden-sm hidden-md hidden-xs">Edit</span> </a>
                                                            </div>
                                                            <div class="caption">
                                                                <h4 class="panel-title">
                                                                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#dest_h_<?php echo $pk; ?>" href="#dest_<?php echo $pk; ?>" aria-expanded="false"><strong><?php echo $dest['address_address']; ?>, <?php echo $dest['address_city']; ?>, <?php echo $dest['address_state']; ?> <?php echo $dest['address_zip']; ?></strong></a>
                                                                </h4>
                                                            </div>
                                                        </div>
                                                        <div id="dest_<?php echo $pk; ?>" class="panel-collapse collapse" aria-expanded="true" style="height: 0px;">
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
                                                                    </a>
                                                                </address>
                                                                <address>
                                                                    Closest intersection:
                                                                    <strong>
                                                                        <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_closest_intersection" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new closest intersection.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                            <?php echo $dest['address_closest_intersection']; ?>
                                                                        </a><br/>
                                                                    </strong>

                                                                    Square Footage:
                                                                    <strong>
                                                                        <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_square_footage" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new closest intersection.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                            <?php echo $dest['address_square_footage']; ?>
                                                                        </a><br/>
                                                                    </strong>

                                                                    Bedrooms:
                                                                    <strong>
                                                                        <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_bedrooms" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new closest intersection.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                            <?php echo $dest['address_bedrooms']; ?>
                                                                        </a><br/>
                                                                    </strong>

                                                                    Garage:
                                                                    <strong>
                                                                        <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_garage" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new closest intersection.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                            <?php echo $dest['address_garage']; ?>
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
                                                    <div class="portlet">
                                                        <div class="portlet-title">
                                                            <div class="caption">
                                                                <i class="fa fa-cogs"></i>Timeline <small><span class="font-red">|</span>Records and tools for this event.</small>
                                                            </div>
                                                            <div class="actions">
                                                                <a class="btn default red-stripe" data-toggle="modal" href="#comments_only">
                                                                    <i class="fa fa-comments"></i>
                                                                    <span class="hidden-480">View <strong>comments</strong> (<?php echo mysql_num_rows(mysql_query("SELECT comment_id FROM fmo_locations_events_comments WHERE comment_event_token='".mysql_real_escape_string($event['event_token'])."'")); ?>)</span>
                                                                </a>
                                                                <a class="btn default red-stripe" data-toggle="modal" href="#estimates_only">
                                                                    <i class="fa fa-usd"></i>
                                                                    <span class="hidden-480">View <strong>estimates</strong></span>
                                                                </a>
                                                                <a class="btn default red-stripe" data-toggle="modal" href="#claims_only">
                                                                    <i class="fa fa-exclamation-triangle"></i>
                                                                    <span class="hidden-480">View <strong>customer claims</strong> (<?php echo mysql_num_rows(mysql_query("SELECT claim_id FROM fmo_locations_events_claims WHERE claim_event_token='".mysql_real_escape_string($event['event_token'])."'")); ?>)</span>
                                                                </a>
                                                                <a class="btn default red-stripe ratings" data-toggle="modal" href="#reviews_only">
                                                                    <i class="fa fa-book"></i>
                                                                    <span class="hidden-480">View <strong>customer reviews</strong> (<?php echo mysql_num_rows(mysql_query("SELECT review_id FROM fmo_locations_events_reviews WHERE review_event_token='".mysql_real_escape_string($event['event_token'])."'")); ?>)</span>
                                                                </a>
                                                                <a class="btn default red-stripe" data-toggle="modal" href="#tools_only">
                                                                    <i class="fa fa-external-link"></i>
                                                                    <span class="hidden-480">View <strong><span class="hidden-480">miscellaneous</span> tools</strong> </span>
                                                                </a>
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
                                                                    <table class="table table-striped table-bordered table-hover datatable" data-src="assets/app/api/event.php?type=timeline&ev=<?php echo $_GET['ev']; ?>">
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
                                                        </div>
                                                    </div>
                                                    <!-- End: life time stats -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="documents">
                                            <div class="row">
                                                <div class="col-md-12">
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
                                                                    <table class="table table-striped table-bordered table-hover datatable" data-src="assets/app/api/event.php?type=documents&ev=<?php echo $event['event_token']; ?>">
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
                                                    <!-- End: life time stats -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="labor">
                                            <div class="row">
                                                <div class="col-md-12">
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
                                                                    <table class="table table-striped table-bordered table-hover datatable" data-src="assets/app/api/event.php?type=labor&ev=<?php echo $_GET['ev']; ?>">
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
                                                                                    $laborers = mysql_query("SELECT user_fname, user_lname, user_employer_rate, user_token FROM fmo_users WHERE user_employer='".mysql_real_escape_string($_SESSION['cuid'])."' ORDER BY user_lname ASC");
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
                                                                                    <button type="button" class="btn btn-sm red margin-bottom add_laborer"><i class="fa fa-download"></i> Save</button> <button class="btn btn-sm default filter-cancel"><i class="fa fa-times"></i> Reset</button>
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
                                                    <!-- End: life time stats -->
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane" id="invoices">
                                            <div class="row">
                                                <!--
                                                <div class="col-md-6">
                                                    <div class="portlet">
                                                        <div class="portlet-title">
                                                            <div class="caption">
                                                                <i class="fa fa-tags"></i> Items for sale(s) <small><span class="font-red">|</span> Available items for invoicing. <i class="fa fa-arrow-right"></i></small>
                                                            </div>
                                                        </div>
                                                        <div class="portlet-body">
                                                            <div class="table-container">
                                                                <form role="form" id="add_service_rate">
                                                                    <table class="table table-striped table-bordered table-hover datatable" data-src="assets/app/api/event.php?type=rates&luid=<?php echo $event['event_location_token']; ?>&ev=<?php echo $event['event_token']; ?>">
                                                                        <thead>
                                                                        <tr role="row" class="heading">
                                                                            <th>
                                                                                Service Name
                                                                            </th>
                                                                            <th>
                                                                                Service Description
                                                                            </th>
                                                                            <th>
                                                                                Taxable
                                                                            </th>
                                                                            <th>
                                                                                Commissionable
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
                                                        </div>
                                                    </div>
                                                </div>
                                                -->
                                                <div class="col-md-12">
                                                    <div class="portlet">
                                                        <div class="portlet-title">
                                                            <div class="caption">
                                                                <i class="fa fa-file"></i>Invoice <small><span class="font-red">|</span></small> <button class="btn btn-xs red-stripe print" data-print="#invoice"><i class="fa fa-print"></i> Print</button>
                                                            </div>
                                                        </div>
                                                        <div class="portlet-body" id="invoice">
                                                            <div class="invoice" id="payments-content">
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="table-container">
                                                                            <form role="form" id="add_service_rate">
                                                                                <table class="table table-striped table-hover datatable" id="sales" data-src="assets/app/api/event.php?type=sales&ev=<?php echo $event['event_token']; ?>">
                                                                                    <thead>
                                                                                    <tr role="row" class="heading">
                                                                                        <th>
                                                                                            Item
                                                                                            <span class="pull-right no_print">
                                                                                                Options for you
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
                                                                    <div class="col-xs-4">
                                                                        <div class="well">
                                                                            <address>
                                                                                <strong><?php echo $event['event_name']; ?></strong><br>
                                                                                <?php echo $pk_strt; ?>, <br/>
                                                                                <?php echo $pk_city; ?>, <?php echo $pk_state; ?> <?php echo $pk_zip; ?> <br/>
                                                                                <abbr title="Phone">P:</abbr> <?php echo clean_phone($event['event_phone']); ?> </address>
                                                                            <address>
                                                                                <strong><?php echo $user['user_fname']." ".$user['user_lname']; ?></strong><br>
                                                                                <a href="mailto:#">
                                                                                    <?php echo secret_mail($user['user_email']); ?> </a>
                                                                            </address>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-xs-8 invoice-block">
                                                                        <ul class="list-unstyled amounts">
                                                                            <li>
                                                                                Sub total: <strong>$2,265</strong>
                                                                            </li>
                                                                            <li>
                                                                                Tax (<strong>X</strong> items) 7%:  <strong>$100</strong>
                                                                            </li>
                                                                            <li>
                                                                                Grand Total: <strong class="text-danger">$2,365</strong>
                                                                            </li>
                                                                        </ul>
                                                                        <br>
                                                                        <a class="btn btn-lg green hidden-print margin-bottom-5 load_payments"  data-type="py" data-href="assets/pages/sub/event_master.php?ev=<?php echo $event['event_token']; ?>" data-page-title="Taking payment">
                                                                            Take Payment <i class="fa fa-check"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                <div class="row" id="payments-content">
                                                                    <div class="col-md-12">
                                                                        <h3>Payments made</h3>
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
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade bs-modal-lg" id="print_bol" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content box red">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h3 class="modal-title font-bold">Bill of Lading confirmation for <?php echo $event['event_name']; ?></h3>
                </div>
                <div class="modal-body">
                    <div class="portlet">
                        <div class="portlet-body" id="bol_inf">
                            // todo: generate preview & confirm button
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn red">Print BOL</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade bs-modal-lg" id="booking_fee" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content box red">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h3 class="modal-title font-bold">Booking fee information for <?php echo $event['event_name']; ?></h3>
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
    <div class="modal fade bs-modal-lg" id="comments_only" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content box red">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h3 class="modal-title font-bold">Comments for <?php echo $event['event_name']; ?></h3>
                </div>
                <div class="modal-body">
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
    <div class="modal fade bs-modal-lg" id="estimates_only" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content box red">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h3 class="modal-title font-bold">Estimates for <?php echo $event['event_name']; ?></h3>
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
    <div class="modal fade bs-modal-lg" id="claims_only" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content box red">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h3 class="modal-title font-bold">Claim for <?php echo $event['event_name']; ?></h3>
                </div>
                <div class="modal-body">
                    <div class="portlet">
                        <div class="portlet-body">
                            <?php
                            $claims = mysql_query("SELECT claim_id, claim_item, claim_padded, claim_weight, claim_comments FROM fmo_locations_events_claims WHERE claim_event_token='".mysql_real_escape_string($event['event_token'])."'");
                            if(mysql_num_rows($claims) > 0){
                                $claim = mysql_fetch_array($claims);
                                ?>
                                <div class="alert alert-danger">
                                    <h4><strong><?php echo $claim['claim_item']; ?></strong></h4>
                                    <p>
                                        Padded: <strong><?php echo $claim['claim_padded']; ?></strong> | Weight (Lbs): <strong><?php echo $claim['claim_weight']; ?></strong> <br/>
                                        Comments: <strong><?php if(empty($claim['claim_comments'])){echo "N/A";} else {echo $claim['claim_comments'];} ?></strong>
                                    </p>
                                </div>
                                <h6>Images for <strong><?php echo $claim['claim_item']; ?></strong></h6>
                                <div class="well">
                                    <div class="row">
                                        <?php
                                        $claimimgs = mysql_query("SELECT image_link FROM fmo_locations_events_claims_images WHERE image_event_token='".mysql_real_escape_string($event['event_token'])."'");
                                        if(mysql_num_rows($claimimgs) > 0){
                                            while($img = mysql_fetch_assoc($claimimgs)){
                                                ?>
                                                <img class="col-md-4 img-responsive" src="<?php echo $img['image_link']; ?>" alt="claim image"/>
                                                <?php
                                            }
                                        } else {
                                            ?>
                                            <center>
                                                <strong>No images are associated with this claim yet.</strong>
                                            </center>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                                <?php
                            } else {
                                ?>
                                <div class="alert alert-warning">
                                    <strong>There is no claim associated with this event.</strong>
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
                            <h4>Outbound texts to customer</h4><hr/>
                            <button class="btn red-stripe fire" data-fire="review_link"><i class="fa fa-star"></i> Send event review link</button>
                            <button class="btn blue-stripe fire" data-fire="claim_link"><i class="fa fa-exclamation-triangle"></i> Send claim form link</button>
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" class="form-control" name="city" placeholder="City">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>State</label>
                                    <select name="state" class="form-control">
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
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Zip Code</label>
                                    <input type="number" class="form-control" name="zip">
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
                                    <label>Special Item(s)</label>
                                    <input type="text" class="form-control" name="special" placeholder="Valueables/priceless items">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Square Footage</label>
                                    <select name="square_footage" class="form-control">
                                        <option disabled selected value="">Select one..</option>
                                        <option value="Miscellaneous Items">Less than 1000ft</option>
                                        <option value="Less than 1500ft">Less than 1500ft</option>
                                        <option value="Less than 2000ft">Less than 2000ft</option>
                                        <option value="Less than 2500ft">Less than 2500ft</option>
                                        <option value="Less than 3000ft">Less than 3000ft</option>
                                        <option value="Less than 3500ft">Less than 3500ft</option>
                                        <option value="Less than 4000ft">Less than 4000ft</option>
                                        <option value="Less than 4500ft">Less than 4500ft</option>
                                        <option value="More than 4500ft+">More than 4500ft+</option>
                                        <option value="More than 5000ft+">More than 5000ft+</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Bedrooms</label>
                                    <select name="bedrooms" class="form-control">
                                        <option disabled selected value="">Select one..</option>
                                        <option value="Miscellaneous Items">Miscellaneous Items</option>
                                        <option value="1 Bedroom">1 Bedroom</option>
                                        <option value="2 Bedroom">2 Bedroom</option>
                                        <option value="3 Bedroom">3 Bedroom</option>
                                        <option value="4 Bedroom">4 Bedroom</option>
                                        <option value="5 Bedroom">5 Bedroom</option>
                                        <option value="6 Bedroom">6 Bedroom</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Garage</label>
                                    <select name="garage" class="form-control">
                                        <option disabled selected value="">Select one..</option>
                                        <option value="No garage">No garage</option>
                                        <option value="1 Car">1 Car</option>
                                        <option value="2 Cars">2 Cars</option>
                                        <option value="3 Cars">3 Cars</option>
                                        <option value="4 Cars">4 Cars</option>
                                        <option value="5 Cars">5 Cars</option>
                                        <option value="6 Cars">6 Cars</option>
                                    </select>
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

            $('#payments .button-cancel').click(function () {
                Pace.track(function(){
                    $.ajax({
                        url: 'assets/pages/event.php?ev=<?php echo $_GET['ev']; ?>&luid=<?php echo $_GET['luid']; ?>',
                        success: function(data) {
                            $('#page_content').html(data);
                        },
                        error: function() {
                            toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                        }
                    });
                });
            });

            var handler = StripeCheckout.configure({
                key: 'pk_test_o9s6ScI3jBABd3V5pZM7kdYA',
                image: 'https://stripe.com/img/documentation/checkout/marketplace.png',
                locale: 'auto',
                token: function(token) {
                    $.ajax({
                        url: 'assets/app/charge.php',
                        type: 'POST',
                        data: {
                            stripeToken: token.id,
                            stripeEmail: token.email,
                            stripeAmt: 1000
                        },
                        success: function(data){
                            $.ajax({
                                url: 'assets/app/update_settings.php?update=event_fly',
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
                        },
                        error: function(e){

                        }
                    })
                }
            });

            $('#pay').click(function(e) {
                // Open Checkout with further options:
                handler.open({
                    name: 'Booking Fee',
                    description: 'Allows customer to use card/check to pay later.',
                    amount: 1000,
                    <?php
                    if(!empty($event['event_email'])){
                    ?>
                    email: '<?php echo $event['event_email']; ?>'
                    <?php
                    }
                    ?>

                });
                e.preventDefault();
            });

            // Close Checkout on page navigation:
            window.addEventListener('popstate', function() {
                handler.close();
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
                        var summaryPanel = document.getElementById('results-map-panel');
                        summaryPanel.innerHTML = '';
                        // For each route, display summary information.
                        for (var i = 0; i < route.legs.length; i++) {
                            /*var routeSegment = i + 1;
                             if(routeSegment == 1){
                                 summaryPanel.innerHTML += 'From <strong>dispatch</strong> to <strong>there</strong> (' + route.legs[i].distance.text + '): ' + routeSegment +
                                     '<br>';
                             } else {
                                 summaryPanel.innerHTML += 'From <strong>here</strong> to <strong>there</strong> (' + route.legs[i].distance.text + '): ' + routeSegment +
                                     '<br>';
                             }
                            summaryPanel.innerHTML += route.legs[i].start_address + ' <strong>to</strong>  ';
                            summaryPanel.innerHTML += route.legs[i].end_address + ' <br/><br/>';*/
                        }
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
                            $('.datatable').getDataTable().ajax.reload();
                        },
                        error: function() {
                            toastr.error('<strong>Logan says</strong>:<br/>That page didnt respond correctly. Try again, or create a support ticket for help.');
                        }
                    });
                }
            });
            $('.show_form').on('click', function() {
                var show = $(this).attr('data-show');

                $(show).show();
            });

            $(".img-check").click(function(){
                $(this).toggleClass("red");
                $(this).find('.fa').toggleClass("fa-check");
                $(this).find('.hidden-checkbox').prop("checked", !$(this).find('.hidden-checkbox').prop("checked"));
                var value = $(this).find('.hidden-checkbox').val();
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
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            toastr.info('<strong>Logan says</strong>:<br/>Document has been added to users documents table.');
                            $("#add_documents")[0].reset();
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

            $('.bol_comments').on('change', function(){
                var comment = $(this).val();
                $.ajax({
                    url: 'assets/app/update_settings.php?update=ev_bol_comments',
                    type: 'POST',
                    data: {
                        comment: comment,
                        ev: '<?php echo $event['event_token']; ?>'
                    },
                    success: function(bol_cmts){
                        toastr.success("<strong>Logan says:</strong><br/> BOL comments saved (see? told you). ")
                    },
                    error: function(){

                    }
                }) ;
            });

            $('.edits').bind("DOMSubtreeModified",function() {
                var a = $('#truckfee').attr('data-a');
                var b = $('#laborrate').attr('data-b');
                var c = $('#countyfee').attr('data-c');
                $.ajax({
                    url: 'assets/app/api/catcher.php?luid=<?php echo $event['event_location_token']; ?>&p=doMath',
                    type: 'POST',
                    data: {
                        day: <?php echo date('N', strtotime($event['event_date_start'])); ?>,
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
            });

            var a = $('#truckfee').attr('data-a');
            var b = $('#laborrate').attr('data-b');
            var c = $('#countyfee').attr('data-c');
            $.ajax({
                url: 'assets/app/api/catcher.php?luid=<?php echo $event['event_location_token']; ?>&p=doMath',
                type: 'POST',
                data: {
                    day: <?php echo date('N', strtotime($event['event_date_start'])); ?>,
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
            });

            $('#dashboard-report-range').daterangepicker({
                    opens: (Metronic.isRTL() ? 'right' : 'left'),
                    startDate: <?php echo $event['event_date_start']; ?>,
                    endDate: <?php echo $event['event_date_end']; ?>,
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
                    $.ajax({
                        type: "POST",
                        url: "assets/app/update_settings.php?update=event_date",
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

            $(".rateYo").rateYo({
                readOnly: true,
                halfStar: true
            });

            $('#draggable').on('show.bs.modal', function(e) {

                //get data-id attribute of the clicked element
                var type = $(e.relatedTarget).data('location-type');

                //populate the textbox
                $('#type option[value="'+type+'"]').attr("selected", "selected");
            });
        });
    </script>
    <?php
} else {
    header("Location: ../../../index.php?err=no_access");
}
?>
