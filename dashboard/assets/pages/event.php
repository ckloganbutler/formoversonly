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
    $event    = mysql_fetch_array(mysql_query("SELECT event_id, event_token, event_location_token, event_user_token, event_name, event_date_start, event_date_end, event_time, event_truckfee, event_laborrate, event_countyfee, event_status, event_email, event_phone, event_type, event_subtype, event_additions FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($_GET['ev'])."'"));
    $location = mysql_fetch_array(mysql_query("SELECT location_name FROM fmo_locations WHERE location_token='".mysql_real_escape_string($event['event_location_token'])."'"));
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
            <?php echo $event['event_name']; ?> <small><strong>(EVENT ID #0<?php echo $event['event_id']; ?>)</strong></small>
        </h3>
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <i class="fa fa-home"></i>
                    <a><?php echo $location['location_name']; ?></a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                    <a class="load_page" data-href="assets/pages/dashboard.php?luid=<?php echo $_GET['luid']; ?>">Dashboard</a>
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
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <div class="actions btn-set">
                                    <a class="btn red dropdown-toggle" readonly href="javascript:;" data-toggle="dropdown">
                                        <i class="fa fa-users"></i> Crewmen:
                                        <strong>
                                            <?php echo $event['event_laborrate']; ?>
                                        </strong>
                                    </a>
                                    <a class="btn red dropdown-toggle" readonly href="javascript:;" data-toggle="dropdown">
                                        <i class="fa fa-truck"></i> Truck(s):
                                        <strong>
                                            <?php echo $event['event_truckfee']; ?>
                                        </strong>
                                    </a>
                                    <a class="btn default red-stripe hidden-sm dropdown-toggle" readonly href="javascript:;" data-toggle="dropdown">
                                        <i class="fa fa-clock-o"></i> Event Date/Time:
                                        <strong>
                                            <?php
                                            if($event['event_date_start'] == $event['event_date_end']){
                                                ?>
                                                <?php echo date('M d, Y', strtotime($event['event_date_start'])); ?> @ <?php echo $event['event_time']; ?>
                                                <?php
                                            } else {
                                                ?>
                                                <?php echo date('M d, Y', strtotime($event['event_date_start'])); ?> - <?php echo date('M d, Y', strtotime($event['event_date_end'])); ?> @ <?php echo $event['event_time']; ?>
                                                <?php
                                            }
                                            ?>
                                        </strong>
                                    </a>

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
                                        <a class="btn red dropdown-toggle hidden-sm" href="javascript:;" data-toggle="dropdown">
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
                                                        Event Name:
                                                    </div>
                                                    <div class="col-md-7 value">
                                                        <a class="ev" style="color:#333333" data-name="event_name" data-pk="<?php echo $event['event_id']; ?>" data-type="text" data-placement="right" data-title="Enter new event name.." data-url="assets/app/update_settings.php?update=event">
                                                            <?php echo $event['event_name']; ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="row static-info">
                                                    <div class="col-md-5 name">
                                                        Phone:
                                                    </div>
                                                    <div class="col-md-7 value">
                                                        <?php echo clean_phone($event['event_phone']);  ?>
                                                    </div>
                                                </div>
                                                <div class="row static-info">
                                                    <div class="col-md-5 name">
                                                        Email:
                                                    </div>
                                                    <div class="col-md-7 value">
                                                        <?php echo $event['event_email']; ?>
                                                    </div>
                                                </div>
                                                <div class="row static-info">
                                                    <div class="col-md-5 name">
                                                        Comments:
                                                    </div>
                                                    <div class="col-md-7 value">
                                                        <?php echo $event['event_comments']; ?>
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
                                                        Email:
                                                    </div>
                                                    <div class="col-md-7 value">
                                                        <?php echo secret_mail($user['user_email']); ?>
                                                    </div>
                                                </div>
                                                <div class="row static-info">
                                                    <div class="col-md-5 name">
                                                        Phone Number:
                                                    </div>
                                                    <div class="col-md-7 value">
                                                        <?php echo clean_phone($user['user_phone']); ?>
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
                                    <div class="portlet-title tabbable-line">
                                        <ul class="nav nav-tabs nav-justified">
                                            <li class="active">
                                                <a href="#additions" data-toggle="tab" aria-expanded="true" style="color: black;">
                                                   Additional Items & Rates</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="portlet-body">
                                        <div class="tab-content">
                                            <style type="text/css">
                                                .check {
                                                    opacity:0.5;
                                                    color:#996;
                                                }
                                            </style>
                                            <?php
                                            $additions = explode("|", $event['event_additions']);
                                            foreach($additions as $ck){
                                                $extra[$ck] = $ck;
                                            }
                                            ?>
                                            <!--
                                            <div class="tab-pane active" id="additions">
                                                <label class="btn <?php if(!empty($extra['hot_tub'])){ echo "red"; } ?>" style="height: 60px; width: 120px;">
                                                    <img src="assets/global/img/catcher/hottub.gif" alt="..." class="img-responsive img-check <?php if(empty($extra['hot_tub'])){ echo "checked"; } ?>" style="vertical-align: top;">
                                                    <label style="padding-top: 5px;">Hot Tub -$398 [+] $350 w/ move</label>
                                                    <input type="checkbox" name="addition[]" id="hot_tub" value="hot_tub" <?php if(!empty($extra['hot_tub'])){ echo "checked"; } ?> class="hidden" autocomplete="off">
                                                </label>
                                                <label class="btn <?php if(!empty($extra['piano'])){ echo "red"; } ?>">
                                                    <img src="assets/global/img/catcher/babygrand.gif" alt="..." class="img-thumbnail img-check <?php if(empty($extra['piano'])){ echo "checked"; } ?>" style="vertical-align: top;">
                                                    <label style="padding-top: 5px;">Piano <br/>$398<br/>$350 w/ move</label>
                                                    <input type="checkbox" name="addition[]" id="piano" value="piano" <?php if(!empty($extra['piano'])){ echo "checked"; } ?> class="hidden" autocomplete="off">
                                                </label>
                                                <label class="btn <?php if(!empty($extra['pool_table'])){ echo "red"; } ?>">
                                                    <img src="assets/global/img/catcher/pooltable.gif" alt="..." class="img-thumbnail img-check <?php if(empty($extra['pool_table'])){ echo "checked"; } ?>" style="vertical-align: top;">
                                                    <label style="padding-top: 5px;">Pool Table <br/>$398<br/>$350 w/ move</label>
                                                    <input type="checkbox" name="addition[]" id="pool_table" value="pool_table" <?php if(!empty($extra['pool_table'])){ echo "checked"; } ?> class="hidden" autocomplete="off">
                                                </label>
                                                <label class="btn <?php if(!empty($extra['play_set'])){ echo "red"; } ?>">
                                                    <img src="assets/global/img/catcher/playset.gif" alt="..." class="img-thumbnail img-check <?php if(empty($extra['play_set'])){ echo "checked"; } ?>" style="vertical-align: top;">
                                                    <label style="padding-top: 5px;">Play Set <br/>$378<br/>$300 w/ move</label>
                                                    <input type="checkbox" name="addition[]" id="play_set" value="play_set" <?php if(!empty($extra['play_set'])){ echo "checked"; } ?> class="hidden" autocomplete="off">
                                                </label>
                                                <label class="btn <?php if(!empty($extra['safe'])){ echo "red"; } ?>">
                                                    <img src="assets/global/img/catcher/safe.gif" alt="..." class="img-thumbnail img-check <?php if(empty($extra['safe'])){ echo "checked"; } ?>" style="vertical-align: top;">
                                                    <label style="padding-top: 5px;">Safe <br/>$298<br/>$200 w/ move</label>
                                                    <input type="checkbox" name="addition[]" id="safe" value="safe" <?php if(!empty($extra['safe'])){ echo "checked"; } ?> class="hidden" autocomplete="off">
                                                </label>
                                            </div>
                                            -->
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
                                        <li class="active">
                                            <a href="#documents" data-toggle="tab">Documents / Photos </a>
                                        </li>
                                        <li>
                                            <a href="#invoices" data-toggle="tab">Invoicing
                                                <span class="badge badge-danger"> 4 </span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#comments" data-toggle="tab"> Comments / Timeline </a>
                                        </li>
                                        <li>
                                            <a href="#labor" data-toggle="tab">Labor / Assets
                                                <span class="badge badge-danger"> 2 </span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#actions" data-toggle="tab">Actions </a>
                                        </li>
                                        <li>
                                            <a href="#estimates" data-toggle="tab">Estimates </a>
                                        </li>
                                        <li>
                                            <a href="#claims" data-toggle="tab">Claims </a>
                                        </li>
                                        <li>
                                            <a href="#reviews" data-toggle="tab">Reviews </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="documents">
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
                                                                <form role="form" id="add_service_rate">
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
                                                                            <td><input type="text" class="form-control form-filter input-sm" name="item"></td>
                                                                            <td></td>
                                                                            <td>
                                                                                <div class="margin-bottom-5">
                                                                                    <button type="button" class="btn btn-sm red margin-bottom add_service_rate"><i class="fa fa-download"></i> Save</button> <button class="btn btn-sm default filter-cancel"><i class="fa fa-times"></i> Reset</button>
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
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="portlet">
                                                        <div class="portlet-title">
                                                            <div class="caption">
                                                                <i class="fa fa-file"></i>Customer Invoice <small><span class="font-red">|</span> Preview for customer's invoice.</small>
                                                            </div>
                                                        </div>
                                                        <div class="portlet-body">
                                                            <div class="invoice">
                                                                <div class="row">
                                                                    <div class="col-xs-12">
                                                                        <div class="table-container">
                                                                            <form role="form" id="add_service_rate">
                                                                                <table class="table table-striped table-bordered table-hover datatable" id="sales" data-src="assets/app/api/event.php?type=sales&ev=<?php echo $event['event_token']; ?>">
                                                                                    <thead>
                                                                                    <tr role="row" class="heading">
                                                                                        <th>
                                                                                            Item
                                                                                        </th>
                                                                                        <th class="hidden-480">
                                                                                            Description
                                                                                        </th>
                                                                                        <th class="hidden-480">
                                                                                            Quantity
                                                                                        </th>
                                                                                        <th class="hidden-480">
                                                                                            Unit Cost
                                                                                        </th>
                                                                                        <th>
                                                                                            Total
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
                                                                                <strong>Loop, Inc.</strong><br>
                                                                                795 Park Ave, Suite 120<br>
                                                                                San Francisco, CA 94107<br>
                                                                                <abbr title="Phone">P:</abbr> (234) 145-1810 </address>
                                                                            <address>
                                                                                <strong>Full Name</strong><br>
                                                                                <a href="mailto:#">
                                                                                    first.last@email.com </a>
                                                                            </address>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-xs-8 invoice-block">
                                                                        <ul class="list-unstyled amounts">
                                                                            <li>
                                                                                <strong>Sub - Total amount:</strong> $9265
                                                                            </li>
                                                                            <li>
                                                                                <strong>Discount:</strong> 12.9%
                                                                            </li>
                                                                            <li>
                                                                                <strong>VAT:</strong> -----
                                                                            </li>
                                                                            <li>
                                                                                <strong>Grand Total:</strong> $12489
                                                                            </li>
                                                                        </ul>
                                                                        <br>
                                                                        <a class="btn btn-lg blue hidden-print margin-bottom-5" onclick="javascript:window.print();">
                                                                            Print <i class="fa fa-print"></i>
                                                                        </a>
                                                                        <a class="btn btn-lg green hidden-print margin-bottom-5">
                                                                            Submit Your Invoice <i class="fa fa-check"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="comments">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="portlet">
                                                        <div class="portlet-title">
                                                            <div class="caption">
                                                                <i class="fa fa-cogs"></i>Comments / Timeline <small><span class="font-red">|</span> This is the track record of the event--we save everything for easy reference.</small>
                                                            </div>
                                                            <div class="actions">
                                                                <a class="btn default red-stripe show_form" data-show="#new_comment">
                                                                    <i class="fa fa-plus"></i>
                                                                    <span class="hidden-480">Add new comment </span>
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
                                                                    <table class="table table-striped table-bordered table-hover datatable" data-src="assets/app/api/event.php?type=comments&ev=<?php echo $_GET['ev']; ?>">
                                                                        <thead>
                                                                        <tr role="row" class="heading">
                                                                            <th width="18%">
                                                                                Comment Date
                                                                            </th>
                                                                            <th >
                                                                                Comment
                                                                            </th>
                                                                            <th width="12%">
                                                                                Submitted by
                                                                            </th>
                                                                            <th width="8%">
                                                                                Actions
                                                                            </th>
                                                                        </tr>
                                                                        <tr role="row" class="filter" style="display: none;" id="new_comment">
                                                                            <td><i class="icon-control-forward"><br/></i>new</td>
                                                                            <td><input type="text" class="form-control form-filter input-sm" name="item"></td>
                                                                            <td></td>
                                                                            <td>
                                                                                <div class="margin-bottom-5">
                                                                                    <button type="button" class="btn btn-sm red margin-bottom add_service_rate"><i class="fa fa-download"></i> Save</button> <button class="btn btn-sm default filter-cancel"><i class="fa fa-times"></i> Reset</button>
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
                                        <div class="tab-pane" id="labor">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="portlet">
                                                        <div class="portlet-title">
                                                            <div class="caption">
                                                                <i class="fa fa-cogs"></i>Labor & Assets <small><span class="font-red">|</span> Currently tracked laborers and assets that have been assigned to this job.</small>
                                                            </div>
                                                            <div class="actions">
                                                                <a class="btn default red-stripe show_form" data-show="#new_labor">
                                                                    <i class="fa fa-plus"></i>
                                                                    <span class="hidden-480">Add new laborer/asset </span>
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
                                                                    <table class="table table-striped table-bordered table-hover datatable" data-src="assets/app/api/event.php?type=labor&ev=<?php echo $_GET['ev']; ?>">
                                                                        <thead>
                                                                        <tr role="row" class="heading">
                                                                            <th width="18%">
                                                                                Comment Date
                                                                            </th>
                                                                            <th >
                                                                                Comment
                                                                            </th>
                                                                            <th width="12%">
                                                                                Submitted by
                                                                            </th>
                                                                            <th width="8%">
                                                                                Actions
                                                                            </th>
                                                                        </tr>
                                                                        <tr role="row" class="filter" style="display: none;" id="new_labor">
                                                                            <td><i class="icon-control-forward"><br/></i>new</td>
                                                                            <td><input type="text" class="form-control form-filter input-sm" name="item"></td>
                                                                            <td></td>
                                                                            <td>
                                                                                <div class="margin-bottom-5">
                                                                                    <button type="button" class="btn btn-sm red margin-bottom add_service_rate"><i class="fa fa-download"></i> Save</button> <button class="btn btn-sm default filter-cancel"><i class="fa fa-times"></i> Reset</button>
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
                                        <div class="tab-pane" id="actions">
                                        </div>
                                        <div class="tab-pane" id="estimates">
                                        </div>
                                        <div class="tab-pane" id="claims">
                                        </div>
                                        <div class="tab-pane" id="reviews">
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
    <form method="POST" action="" role="form" id="new_location">
        <div class="modal fade bs-modal-lg" id="draggable" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content box red">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h3 class="modal-title font-bold">Add event location</h3>
                    </div>
                    <div class="modal-body">
                        <div class="row">
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
                                    <input type="text" class="form-control" name="address" placeholder="1220 Example Rd">
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
                                    <input type="text" class="form-control" name="city" placeholder="Sincity">
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
                                    <input type="text" class="form-control" name="zip">
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
            $('.show_form').on('click', function() {
               var show = $(this).attr('data-show');

               $(show).show();
            });

            $(".img-check").click(function(){
                $(this).toggleClass("check");
                $(this).parent().toggleClass("red");
            });

            var address = '<?php echo $pk_strt; ?>, <?php echo $pk_city; ?>, <?php echo $pk_state; ?>, <?php echo $pk_zip; ?>';

            var map = new google.maps.Map(document.getElementById('gmap_basic'), {
                mapTypeId: google.maps.MapTypeId.TERRAIN,
                zoom: 12
            });

            var geocoder = new google.maps.Geocoder();

            geocoder.geocode({
                'address': address
            },
            function(results, status) {
                if(status == google.maps.GeocoderStatus.OK) {
                    new google.maps.Marker({
                        position: results[0].geometry.location,
                        map: map
                    });
                    map.setCenter(results[0].geometry.location);
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
            $('.editable_inf').editable({

            });
    </script>
    <?php
} else {
    header("Location: ../../../index.php?err=no_access");
}
?>
