<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 10/29/2017
 * Time: 11:33 PM
 */
require '../../app/init.php';
if($_GET['px'] == 'lp'){
    $event    = mysql_fetch_array(mysql_query("SELECT event_id, event_token, event_location_token, event_company_token, event_booking, event_user_token, event_name, event_date_start, event_date_end, event_time, event_zip, event_truckfee, event_laborrate, event_countyfee, event_status, event_email, event_phone, event_type, event_subtype, event_additions, event_comments, event_company_token FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($_GET['ev'])."'"));
    $estimates = mysql_query("SELECT estimate_id, estimate_token, estimate_location_token, estimate_customer_name, estimate_customer_email, estimate_customer_phone, estimate_pickup_date, estimate_dropoff_date, estimate_pickup_time, estimate_name, estimate_email, estimate_phone, estimate_type, estimate_comments, estimate_truckfee, estimate_laborrate, estimate_countyfee, estimate_packing, estimate_transport, estimate_unload, estimate_cus_sig, estimate_es_sig, estimate_estimator FROM fmo_locations_events_estimates WHERE estimate_token='".mysql_real_escape_string($_GET['n'])."'");
    if($_GET['v'] == 'c') {
        if(mysql_num_rows($estimates) > 0) {
            $estimate = mysql_fetch_array($estimates);
        } else {
            $new_token      = $_GET['n'];
            $new_location   = $event['event_location_token'];
            $new_company    = $event['event_company_token'];
            $new_user_token = $_GET['uuid'];
            $new_customer_n = name($event['event_user_token']);
            $new_customer_e = $event['event_email'];
            $new_customer_p = $event['event_phone'];
            $new_pickup     = $event['event_date_start'];
            $new_dropoff    = $event['event_date_end'];
            $new_time       = $event['event_time'];
            $new_name       = $event['event_name'];
            $new_type       = $event['event_type'];
            $new_email      = $event['event_email'];
            $new_phone      = $event['event_phone'];
            $new_event      = $event['event_token'];
            $new_comments   = $event['event_comments'];
            $new_truckfee   = $event['event_truckfee'];
            $new_laborrate  = $event['event_laborrate'];
            $new_countyfee  = $event['event_countyfee'];

            $days  = array(0 => "sunday", 1 => "monday", 2 => "tuesday", 3 => "wednesday", 4 => "thursday", 5 => "friday", 6 => "saturday");
            $col   = "fmo_locations_rates_".$days[date('w', strtotime($event['event_date_start']))];
            $tok   = $days[date('w', strtotime($event['event_date_start']))]."_location_token";
            $find_fees = mysql_query("SELECT ".mysql_real_escape_string($days[date('w', strtotime($event['event_date_start']))])."_truck_fee, ".mysql_real_escape_string($days[date('w', strtotime($event['event_date_start']))])."_labor_rate, ".mysql_real_escape_string($days[date('w', strtotime($event['event_date_start']))])."_truck_rate, ".mysql_real_escape_string($days[date('w', strtotime($event['event_date_start']))])."_upcharge FROM fmo_locations_rates_".mysql_real_escape_string($days[date('w', strtotime($event['event_date_start']))])." WHERE ".mysql_real_escape_string($days[date('w', strtotime($event['event_date_start']))])."_location_token='".mysql_real_escape_string($new_location)."'");
            if(mysql_num_rows($find_fees) > 0){
                $fees = mysql_fetch_array($find_fees);
                $truckfee_rate = $fees[$days[date('w', strtotime($event['event_date_start']))]."_truck_fee"];
                $laborrate_rate = $fees[$days[date('w', strtotime($event['event_date_start']))]."_labor_rate"];
                $truckrate_rate = $fees[$days[date('w', strtotime($event['event_date_start']))]."_truck_rate"];
                $weekend_upcharge = $fees[$days[date('w', strtotime($event['event_date_start']))]."_upcharge"];
            }
            mysql_query("INSERT INTO fmo_locations_events_estimates (estimate_token, estimate_location_token, estimate_company_token, estimate_estimator, estimate_event_token, estimate_customer_name, estimate_customer_email, estimate_customer_phone, estimate_pickup_date, estimate_dropoff_date, estimate_pickup_time, estimate_name, estimate_type, estimate_email, estimate_phone, estimate_comments, estimate_truckrate_rate, estimate_truckfee, estimate_truckfee_rate, estimate_laborrate, estimate_laborrate_rate, estimate_countyfee) VALUES (
                '".mysql_real_escape_string($new_token)."',
                '".mysql_real_escape_string($new_location)."',
                '".mysql_real_escape_string($new_company)."',
                '".mysql_real_escape_string($new_user_token)."',
                '".mysql_real_escape_string($new_event)."',
                '".mysql_real_escape_string($new_customer_n)."',
                '".mysql_real_escape_string($new_customer_e)."',
                '".mysql_real_escape_string($new_customer_p)."',
                '".mysql_real_escape_string($new_pickup)."',
                '".mysql_real_escape_string($new_dropoff)."',
                '".mysql_real_escape_string($new_time)."',
                '".mysql_real_escape_string($new_name)."',
                '".mysql_real_escape_string($new_type)."',
                '".mysql_real_escape_string($new_email)."',
                '".mysql_real_escape_string($new_phone)."',
                '".mysql_real_escape_string($new_comments)."',
                '".mysql_real_escape_string($truckrate_rate)."',
                '".mysql_real_escape_string($new_truckfee)."',
                '".mysql_real_escape_string($truckfee_rate)."',
                '".mysql_real_escape_string($new_laborrate)."',
                '".mysql_real_escape_string($laborrate_rate)."',
                '".mysql_real_escape_string($new_countyfee)."')") or die(mysql_error());
            $estimate = mysql_fetch_array(mysql_query("SELECT estimate_id, estimate_token, estimate_location_token, estimate_customer_name, estimate_customer_email, estimate_customer_phone, estimate_pickup_date, estimate_dropoff_date, estimate_pickup_time, estimate_name, estimate_email, estimate_phone, estimate_type, estimate_comments, estimate_truckfee, estimate_laborrate, estimate_countyfee, estimate_packing, estimate_transport, estimate_unload, estimate_cus_sig, estimate_es_sig FROM fmo_locations_events_estimates WHERE estimate_token='".mysql_real_escape_string($new_token)."'")) or die(mysql_error());
            timeline_event($new_token, $_GET['uuid'], "Estimate Created", name($_GET['uuid'])." was given the task to estimate this event.");
        }
        ?>
        <div class="col-md-12" style="background-color: white;">
            <div class="portlet">
                <div class="portlet-body">
                    <div class="portlet">
                        <div class="portlet-body" id="invoice">
                            <h3 class="text-center" style="margin-bottom: 50px;"><small>Creating</small><br/><span id="typ" class="bold">LOCAL</span>, <span id="bind">NON-</span>BINDING ESTIMATE</h3>
                            <form id="estimate_it" method="POST">
                                <div class="row">
                                    <div class="form-group col-md-4 col-sm-12 col-xs-4">
                                        <label class="control-label">Customer Name <span class="required">*</span></label>
                                        <div class="input-icon" style="border-left: 2px solid #c23f44 !important">
                                            <i class="fa fa-user"></i>
                                            <input type="text" class="form-control placeholder-no-fix" name="customer_name" value="<?php echo $estimate['estimate_customer_name']; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4 col-sm-12 col-xs-4">
                                        <label class="control-label">Customer Email <span class="required">*</span></label>
                                        <div class="input-icon" style="border-left: 2px solid #c23f44 !important">
                                            <i class="fa fa-envelope"></i>
                                            <input type="text" class="form-control placeholder-no-fix" name="customer_email" value="<?php echo $estimate['estimate_customer_email']; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4 col-sm-12 col-xs-4">
                                        <label class="control-label">Customer Phone <span class="required">*</span></label>
                                        <div class="input-icon" style="border-left: 2px solid #c23f44 !important">
                                            <i class="fa fa-phone"></i>
                                            <input type="text" class="form-control placeholder-no-fix mask-phone" name="customer_phone" value="<?php echo clean_phone($estimate['estimate_customer_phone']); ?>">
                                        </div>
                                    </div>
                                </div>
                                <hr/>
                                <div class="row">
                                    <div class="form-group col-md-6 col-sm-12 col-xs-6">
                                        <label class="control-label"><strong>Estimated</strong> Pickup/Dropoff Dates <span class="required">*</span></label>
                                        <div class="" style="border-left: 2px solid #c23f44 !important">
                                            <div class="input-group input-md input-daterange" data-date="10/11/2012" data-date-format="mm/dd/yyyy" style="width: 100% !important;">
                                                <input type="text" class="form-control date-picker" name="startdate" value="<?php echo date("m/d/Y", strtotime($event['event_date_start'])); ?>">
                                                <span class="input-group-addon"> to </span>
                                                <input type="text" class="form-control date-picker" name="enddate" value="<?php echo date("m/d/Y", strtotime($event['event_date_end'])); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6 col-sm-12 col-xs-6">
                                        <label class="control-label"><strong>Estimated</strong> Pickup Time <span class="required">*</span></label>
                                        <select class="form-control" name="time" id="time_select" style="border-left: 2px solid #c23f44 !important">
                                            <option disabled selected value="">Select a start time..</option>
                                            <?php
                                            $timeOptions = mysql_query("SELECT time_start, time_end FROM fmo_locations_times WHERE time_location_token='".mysql_real_escape_string($event['event_location_token'])."'");
                                            if(mysql_num_rows($timeOptions) > 0){
                                                while($t = mysql_fetch_assoc($timeOptions)){
                                                    if(empty($t['time_end'])){
                                                        continue;
                                                    }
                                                    ?>
                                                    <option <?php if($t['time_start']." to ".$t['time_end'] == $estimate['estimate_pickup_time']){echo "selected";} ?> value="<?php echo $t['time_start']; ?> to <?php echo $t['time_end']; ?>"><?php echo $t['time_start']; ?> - <?php echo $t['time_end']; ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group  col-md-3 col-sm-12 col-xs-3">
                                        <label class="control-label"><strong>Event</strong> Name <span class="required">*</span></label>
                                        <div class="input-icon" style="border-left: 2px solid #c23f44 !important">
                                            <i class="fa fa-user"></i>
                                            <input type="text" class="form-control placeholder-no-fix" name="event_name" value="<?php echo $estimate['estimate_name']; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3 col-sm-12 col-xs-3">
                                        <label class="control-label"><strong>Event</strong> Type <span class="required">*</span></label>
                                        <div class="input-icon" style="border-left: 2px solid #c23f44 !important">
                                            <i class="fa fa-tag"></i>
                                            <select class="form-control placeholder-no-fix" name="type" id="binder">
                                                <option disabled selected value="">Select a type..</option>
                                                <?php
                                                $types = mysql_query("SELECT eventtype_name FROM fmo_locations_eventtypes WHERE eventtype_location_token='".mysql_real_escape_string($event['event_location_token'])."'");
                                                if(mysql_num_rows($types) > 0){
                                                    while($type = mysql_fetch_assoc($types)){
                                                        ?>
                                                        <option <?php if($type['eventtype_name'] == $estimate['estimate_type']){echo "selected";} ?> value="<?php echo $type['eventtype_name']; ?>"><?php echo $type['eventtype_name']; ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3 col-sm-12 col-xs-3">
                                        <label class="control-label"><strong>Event</strong> Email <span class="required">*</span></label>
                                        <div class="input-icon" style="border-left: 2px solid #c23f44 !important">
                                            <i class="fa fa-envelope"></i>
                                            <input type="text" class="form-control placeholder-no-fix" name="email" value="<?php echo $estimate['estimate_email']; ?>"/>
                                        </div>
                                    </div>
                                    <div class="form-group  col-md-3 col-sm-12 col-xs-3">
                                        <label class="control-label"><strong>Event</strong> Phone <span class="required">*</span></label>
                                        <div class="input-icon" style="border-left: 2px solid #c23f44 !important">
                                            <i class="fa fa-phone"></i>
                                            <input type="text" class="form-control placeholder-no-fix mask-phone" name="phone" value="<?php echo clean_phone($estimate['estimate_phone']); ?>"/>
                                        </div>
                                    </div>
                                </div>
                                <hr/>
                                <div class="row">
                                    <div class="col-md-6 col-xs-6">
                                        <div class="scrollz">
                                            <div class="portlet">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        Pick up location(s)
                                                    </div>
                                                    <div class="actions">
                                                        <a class="btn default red-stripe" data-toggle="modal" href="#draggable" data-location-type="1">
                                                            <i class="fa fa-plus"></i>
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
                                                            <div id="pickup_h_<?php echo $pk; ?>" class="panel-group">
                                                                <div class="panel panel-default">
                                                                    <div class="panel-heading">
                                                                        <div class="actions pull-right" style="margin-top: -6px; margin-right: -9px">
                                                                            <a href="javascript:;" class="btn btn-default btn-sm edit" data-edit="pu_<?php echo $pickup['address_id']; ?>">
                                                                                <i class="fa fa-pencil"></i> <span class="hidden-sm hidden-md hidden-xs">Edit</span> </a>
                                                                        </div>
                                                                        <div class="caption">
                                                                            <h4 class="panel-title">
                                                                                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#pickup_h_<?php echo $pk; ?>" href="#pickup_<?php echo $pk; ?>" aria-expanded="false"><strong><?php echo $pickup['address_address']; ?>, <?php echo $pickup['address_city']; ?>, <?php echo $pickup['address_state']; ?>, <?php echo $pickup['address_zip'];  ?></strong></a>
                                                                            </h4>
                                                                        </div>
                                                                    </div>
                                                                    <div id="pickup_<?php echo $pk; ?>" class="panel-collapse collapse" aria-expanded="true" style="height: 0px;">
                                                                        <div class="panel-body">
                                                                            <address>
                                                                                <strong>Physical Address</strong><br>
                                                                                <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_address" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new street address.." data-url="../app/update_settings.php?update=event_addy">
                                                                                    <?php echo $pickup['address_address']; ?>
                                                                                </a><br/>
                                                                                <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_city" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new city.." data-url="../app/update_settings.php?update=event_addy">
                                                                                    <?php echo $pickup['address_city']; ?>
                                                                                </a>,
                                                                                <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_state" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Select new state.." data-url="../app/update_settings.php?update=event_addy">
                                                                                    <?php echo $pickup['address_state']; ?>
                                                                                </a>
                                                                                <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_zip" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new zipcode.." data-url="../app/update_settings.php?update=event_addy">
                                                                                    <?php echo $pickup['address_zip']; ?>
                                                                                </a><br/>
                                                                                <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_county" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new county.." data-url="../app/update_settings.php?update=event_addy">
                                                                                    <?php echo $pickup['address_county']; ?>
                                                                                </a>
                                                                            </address>
                                                                            <address>
                                                                                Closest intersection:
                                                                                <strong>
                                                                                    <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_closest_intersection" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new closest intersection.." data-url="../app/update_settings.php?update=event_addy">
                                                                                        <?php echo $pickup['address_closest_intersection']; ?>
                                                                                    </a><br/>
                                                                                </strong>

                                                                                Stairs:
                                                                                <strong>
                                                                                    <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_stairs" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new closest intersection.." data-url="../app/update_settings.php?update=event_addy">
                                                                                        <?php echo $pickup['address_stairs']; ?>
                                                                                    </a><br/>
                                                                                </strong>

                                                                                Parking Distance:
                                                                                <strong>
                                                                                    <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_distance" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new distance.." data-url="../app/update_settings.php?update=event_addy">
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
                                                                                    <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_comments" data-pk="<?php echo $pickup['address_id']; ?>" data-type="textarea" data-placement="right" data-title="Enter new comments.." data-url="../app/update_settings.php?update=event_addy">
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
                                                        Destination location(s)
                                                    </div>
                                                    <div class="actions">
                                                        <a class="btn default red-stripe" data-toggle="modal" href="#draggable" data-location-type="2">
                                                            <i class="fa fa-plus"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="portlet-body">
                                                    <?php
                                                    $dests = mysql_query("SELECT address_address, address_suite, address_city, address_state, address_zip, address_closest_intersection, address_county, address_stairs, address_distance, address_comments FROM fmo_locations_events_addresses WHERE address_event_token='".mysql_real_escape_string($event['event_token'])."' AND address_type=2");
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
                                                                                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#dest_h_<?php echo $pk; ?>" href="#dest_<?php echo $pk; ?>" aria-expanded="false"><strong><?php echo $dest['address_address']; ?>, <?php echo $dest['address_city']; ?>, <?php echo $dest['address_state']; ?>, <?php echo $dest['address_zip']; ?></strong></a>
                                                                            </h4>
                                                                        </div>
                                                                    </div>
                                                                    <div id="dest_<?php echo $pk; ?>" class="panel-collapse collapse" aria-expanded="true" style="height: 0px;">
                                                                        <div class="panel-body">
                                                                            <address>
                                                                                <strong>Physical Address</strong><br>
                                                                                <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_address" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new street address.." data-url="../app/update_settings.php?update=event_addy">
                                                                                    <?php echo $dest['address_address']; ?>
                                                                                </a><br/>
                                                                                <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_city" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new city.." data-url="../app/update_settings.php?update=event_addy">
                                                                                    <?php echo $dest['address_city']; ?>
                                                                                </a>,
                                                                                <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_state" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Select new state.." data-url="../app/update_settings.php?update=event_addy">
                                                                                    <?php echo $dest['address_state']; ?>
                                                                                </a>
                                                                                <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_zip" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new zipcode.." data-url="../app/update_settings.php?update=event_addy">
                                                                                    <?php echo $dest['address_zip']; ?>
                                                                                </a><br/>
                                                                                <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_county" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new county.." data-url="../app/update_settings.php?update=event_addy">
                                                                                    <?php echo $dest['address_county']; ?>
                                                                                </a>
                                                                            </address>
                                                                            <address>
                                                                                Closest intersection:
                                                                                <strong>
                                                                                    <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_closest_intersection" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new closest intersection.." data-url="../app/update_settings.php?update=event_addy">
                                                                                        <?php echo $dest['address_closest_intersection']; ?>
                                                                                    </a><br/>
                                                                                </strong>

                                                                                Stairs:
                                                                                <strong>
                                                                                    <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_stairs" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new closest intersection.." data-url="../app/update_settings.php?update=event_addy">
                                                                                        <?php echo $dest['address_stairs']; ?>
                                                                                    </a><br/>
                                                                                </strong>

                                                                                Parking Distance:
                                                                                <strong>
                                                                                    <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_distance" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new distance.." data-url="../app/update_settings.php?update=event_addy">
                                                                                        <?php echo $dest['address_distance']; ?>
                                                                                    </a><br/>
                                                                                </strong>
                                                                            </address>
                                                                            <address>
                                                                                Comments: <br/>
                                                                                <strong>
                                                                                    <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_comments" data-pk="<?php echo $dest['address_id']; ?>" data-type="textarea" data-placement="right" data-title="Enter new comments.." data-url="../app/update_settings.php?update=event_addy">
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
                                            <div class="form-group">
                                                <label class="control-label"><strong>Customer / Event / Location</strong> Comments / Exclusions </label>
                                                <textarea placeholder="" class="form-control bol_comments" style="height: 165px;"><?php echo $estimate['estimate_comments']; ?></textarea>
                                                <span style="margin-top: -23px; margin-right: 10px;" class="bol_countdown pull-right"></span>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-6 col-xs-6">
                                        <div id="gmap_basic" class="gmaps" style="height: 475px;">
                                        </div>
                                    </div>
                                </div>
                                <hr/>
                                <h4><strong>Items for sale(s)</strong> use this tool to estimate all items/costs.</h4>
                                <div class="row no-print">
                                    <div class="col-md-12">
                                        <div class="scroller2" style="height: 290px;" data-always-visible="1" data-rail-visible1="1">
                                            <div class="portlet">
                                                <div class="portlet-body">
                                                    <div class="table-container">
                                                        <table class="table table-striped table-hover datatable" data-src="../app/api/estimate.php?type=rates&est=<?php echo $estimate['estimate_token']; ?>&luid=<?php echo $estimate['estimate_location_token']; ?>">
                                                            <thead>
                                                            <tr role="row" class="heading">
                                                                <th>
                                                                    Service Name
                                                                </th>
                                                                <th width="12%" class="text-center">
                                                                    Estimate item <i class="fa fa-arrow-right"></i>
                                                                </th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> <br/>
                                <h4><strong>Estimated cost(s)</strong> from what you've added to the estimate.</h4>

                                <div class="row">
                                    <div class="col-md-12">
                                       <div class="table-container">
                                            <table class="table table-striped table-hover datatable" id="sales" data-src="../app/api/estimate.php?type=sales&est=<?php echo $estimate['estimate_token']; ?>&luid=<?php echo $estimate['estimate_location_token']; ?>">
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
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 invoice-block text-right">
                                        <ul class="list-unstyled amounts" style="margin-bottom: 0;">
                                            <li>
                                                Sub total: <h3 style="display: inline; color: #F3565D!important" class="text-danger bold">$<span id="owe_sub_total"></span></h3>
                                            </li>
                                            <li>
                                                Taxes due:  <h3 style="display: inline; color: #F3565D!important" class="text-danger bold">$<span id="owe_tax"></span></h3>
                                            </li>
                                            <li>
                                                Grand Total: <h3 style="display: inline; color: #F3565D!important" class="text-danger bold">$<span id="owe_total"></span></h3>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <hr/>
                                <div class="row" id="estimator-sig">
                                    <div class="col-md-12">
                                        <div class="portlet">
                                            <div class="portlet-title">
                                                <span class="pull-left">
                                                    <h4 style="font-size: 21px; margin-top: 4px;"><strong >Estimator Signature</strong><small><span class="font-red">*</span> USE YOUR REAL SIGNATURE. CUSTOMER WILL SEE THIS.</small></h4>
                                                </span>
                                                <button type="button" class="btn red pull-right" id="es-clear"  style="margin-left: 10px; display: none;" onclick="$('#es-signature').jSignature('clear')">Clear</button>
                                                <button type="button" class="btn blue pull-right" id="es-save_sig">Save</button>
                                            </div>
                                            <div class="portlet-body" style="border: 1px solid #b83a3e">
                                                <div id="es-signature"></div>
                                                <input type="hidden" id="es-hiddenSigData" name="es_sig" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr/>
                                <button type="button" class="btn red button-submit btn-block" style="height: 130px" id="finisher">
                                    Send confirmation & signature request to customer (via <strong>SMS</strong>) <i class="m-icon-swapright m-icon-white"></i>
                                </button>
                            </form>
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
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Street Address</label>
                                        <input type="text" class="form-control" name="address" placeholder="Street Address">
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
        <script>
            jQuery(document).ready(function() {

                document.title = "Creating an estimate.";
                $('#binder').on('change', function() {
                    var type = $(this).val();
                    if(type.toUpperCase() == 'LOCAL'){
                        $('#typ').html(type.toUpperCase());
                        $('#bind').html("NON-");
                    } else {
                        $('#typ').html("OUT OF STATE");
                        $('#bind').html("");
                    }
                    if(type.toUpperCase() == 'OUT OF STATE'){
                        $('#typ').html(type.toUpperCase());
                        $('#bind').html("");
                    } else {
                        $('#typ').html("LOCAL");
                        $('#bind').html("NON-")
                    }
                });

                $.ajax({
                    url: '../app/api/estimate.php?type=inv&luid=<?php echo $estimate['estimate_location_token']; ?>',
                    type: 'POST',
                    data: {
                        estimate: '<?php echo $estimate['estimate_token']; ?>'
                    },
                    success: function(m){
                        var owe = JSON.parse(m);
                        $(document).find('#owe_sub_total').html(parseFloat(owe.sub_total).toFixed(2));
                        $(document).find('#owe_tax').html(parseFloat(owe.tax).toFixed(2));
                        $(document).find('#owe_total').html(parseFloat(owe.total).toFixed(2));
                        $(document).find('#TOTAL').html(parseFloat(owe.total).toFixed(2));
                        $(document).find('#owe_total_unpaid').html(parseFloat(owe.unpaid).toFixed(2));
                    },
                    error: function(e){

                    }
                });

                function updateInv(estimate, luid){
                    $.ajax({
                        url: '../app/api/estimate.php?type=inv&luid='+luid,
                        type: 'POST',
                        data: {
                            estimate: '' + estimate + ''
                        },
                        success: function(m){
                            var owe = JSON.parse(m);
                            $(document).find('#owe_sub_total').html(parseFloat(owe.sub_total).toFixed(2));
                            $(document).find('#owe_tax').html(parseFloat(owe.tax).toFixed(2));
                            $(document).find('#owe_total').html(parseFloat(owe.total).toFixed(2));
                            $(document).find('#TOTAL').html(parseFloat(owe.total).toFixed(2));
                            $(document).find('#owe_total_unpaid').html(parseFloat(owe.unpaid).toFixed(2));
                        },
                        error: function(e){

                        }
                    });
                }

                $(document).on('click', '.add_item', function(){
                    var estimate = $(this).attr('data-est');
                    var luid     = $(this).attr('data-luid');
                    $.ajax({
                        url: '../app/api/actions.php?ty=ei',
                        type: 'POST',
                        data: {
                            srv_id: $(this).attr('data-id'),
                            srv_est: estimate
                        },
                        success: function(d){
                            var inf = JSON.parse(d);
                            toastr.success("<strong>Logan says</strong>:<br/> "+inf.item+" added to <?php echo $user['user_fname']; ?>'s invoice for "+inf.cost);
                            $('#sales').DataTable().ajax.reload();
                            updateInv(estimate, luid);
                        },
                        error: function(e){
                            toastr.error("<strong>Logan says</strong>:<br/> An unexpected error has occurred. Please try again later.")
                        }
                    });
                });

                $(document).on('click', '.delete_item',  function() {
                    var del    = $(this).attr('data-delete');
                    var est    = $(this).attr('data-estimate');
                    var luid   = '<?php echo $estimate['estimate_location_token']; ?>';
                    $(this).closest('tr').remove();
                    $.ajax({
                        url: '../app/update_settings.php?setting=delete_item_est',
                        type: 'POST',
                        data: {
                            del: del,
                            est: est
                        },
                        success: function(s){
                            updateInv(est, luid);
                        },
                        error: function(e){
                            updateInv(est, luid);
                        }
                    });
                });

                $(document).on('click', '.edit', function(){
                    var line   = $(this).attr('data-edit');
                    var reload = $(this).attr('data-reload');
                    var est    = $(this).attr('data-estimate');
                    var luid   = $(this).attr('data-luid');
                    var selec  = $(this).attr('data-selec');
                    $('.'+line).editable({
                        step: 'any',
                        success: function(e) {
                            if(reload == "est"){
                                updateInv(est, luid);
                            } if(reload){
                                $('.datatable').DataTable().ajax.reload();
                            }
                        }
                    }).on("shown", function(e, editable) {
                        if(selec == 'autoselect'){
                            editable.input.$input.get(0).select();
                        }
                        //console.log(type + " is the type!");
                    });
                    toastr.info("<strong>Logan says</strong>:<br/>Editable information has been underlined with blue dots.")
                });


                var form = $('#estimate_it');

                form.validate({
                    doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                    errorElement: 'span', //default input error message container
                    errorClass: 'help-block', // default input error message class
                    focusInvalid: false, // do not focus the last invalid input
                    rules: {
                        customer_name: {
                            required: true
                        },
                        customer_email: {
                            required: true
                        },
                        customer_phone: {
                            required: true
                        },
                        name: {
                            required: true
                        },
                        type: {
                            required: true
                        },
                        phone: {
                            required: true
                        },
                        email: {
                            required: true
                        },
                        startdate: {
                            required: true
                        },
                        enddate: {
                            required: true
                        },
                        time: {
                            required: true
                        }
                    },


                    invalidHandler: function (event, validator) { //display error alert on form submit

                    },

                    highlight: function (element) { // hightlight error inputs
                        $(element)
                            .closest('.form-group').removeClass('has-success').addClass('has-error'); // set error class to the control group
                    },

                    unhighlight: function (element) { // revert the change done by hightlight
                        $(element)
                            .closest('.form-group').removeClass('has-error'); // set error class to the control group
                    },

                    success: function (label) {
                        if (label.attr("for") == "gender" || label.attr("for") == "payment[]") { // for checkboxes and radio buttons, no need to show OK icon
                            label
                                .closest('.form-group').removeClass('has-error').addClass('has-success');
                            label.remove(); // remove error label here
                        } else { // display success icon for other inputs
                            label
                                .addClass('valid') // mark the current input as valid and display OK icon
                                .closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
                        }
                    }

                });

                var start    = new Date("<?php echo date('m-d-Y', strtotime($event['event_date_start'])); ?>");
                var start_dd = start.getDate();
                var start_mm = start.getMonth()+1;
                var start_yy = start.getFullYear();
                if(start_dd<10) {
                    start_dd = '0'+start_dd
                }

                if(start_mm<10) {
                    start_mm = '0'+start_mm
                }
                var end   = new Date("<?php echo date('m-d-Y', strtotime($event['event_date_end'])); ?>");
                var end_dd = end.getDate();
                var end_mm = end.getMonth()+1;
                var end_yy = end.getFullYear();
                if(end_dd<10) {
                    end_dd = '0'+end_dd
                }

                if(end_mm<10) {
                    end_mm = '0'+end_mm
                }

                $('.date-picker').daterangepicker({
                        opens: (Metronic.isRTL() ? 'right' : 'left'),
                        startDate: start_mm + '-' + start_dd + '-' + start_yy,
                        endDate: end_mm + '-' + end_dd + '-' + end_yy,
                        showDropdowns: false,
                        showWeekNumbers: false,
                        timePicker: false,
                        timePickerIncrement: 1,
                        timePicker12Hour: true,
                        singleDatePicker: true,
                        buttonClasses: ['btn btn-sm'],
                        applyClass: ' blue',
                        cancelClass: 'default',
                        format: 'MM-DD-YYYY',
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

                    }
                );

                $('#finisher').on('click', function () {
                    $.ajax({
                        url: '../app/update_settings.php?update=estimate&ev=<?php echo $event['event_token']; ?>&est=<?php echo $estimate['estimate_token']; ?>&s=1&cuid=<?php echo $event['event_company_token']; ?>',
                        type: 'POST',
                        data: $('#estimate_it').serialize(),
                        success: function(d) {
                            $('#page-content').html("<br/> <br/> <br/> <div class=\"row\" style=\"margin-bottom: 50px;\">\n" +
                                "                                                                    <div class=\"col-md-12 page-404\">\n" +
                                "                                                                        <div class=\"number font-green\" style=\"top: 12px !important;\">\n" +
                                "                                                                            <i style=\"font-size: 100px;\" class=\"icon-check\"></i>\n" +
                                "                                                                        </div>\n" +
                                "                                                                        <div class=\"details\">\n" +
                                "                                                                            <h3>Estimate completed.</h3>\n" +
                                "                                                                            <p>\n" +
                                "                                                                                A text-message & email has been sent to the customer.<br/>\n" +
                                "                                                                                <strong>Yeah, we know it was you,</strong> <?php echo name($estimate['estimate_estimator']); ?>\n"+
                                "                                                                            </p>\n" +
                                "                                                                        </div>\n" +
                                "                                                                    </div>\n" +
                                "                                                                </div><br/>")
                        },
                        error: function() {
                            toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                        }
                    });
                }).hide();

                $("#estimate_it").validate().element("#time_select");

                $('.bol_comments').on('change', function(){
                    var comment = $(this).val();
                    $.ajax({
                        url: '../app/update_settings.php?update=est_comments',
                        type: 'POST',
                        data: {
                            comment: comment,
                            ev: '<?php echo $estimate['estimate_token']; ?>'
                        },
                        success: function(bol_cmts){
                            toastr.success("<strong>Logan says:</strong><br/> Comments saved (wow thats cool). ");
                        },
                        error: function(){

                        }
                    }) ;
                });

                $("#es-signature").jSignature();
                <?php
                if(!empty($estimate['estimate_es_sig'])){
                ?>
                $("#es-signature").jSignature("importData", 'data:'+"<?php echo $estimate['estimate_es_sig']; ?>");
                <?php
                }
                ?>

                $('#es-save_sig').click(function(){
                    var sigData = $('#es-signature').jSignature('getData','base30');
                    $('#es-hiddenSigData').val(sigData);

                    $('#es-save_sig').html("Saved!");
                    $('#es-clear').show();

                    $('#finisher').show();
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
                });

                $(".mask-phone").inputmask("mask", {
                    "mask": "(999) 999-9999"
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
                            /* var summaryPanel = document.getElementById('results-map-panel');
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
                            summaryPanel.innerHTML += '<strong>Total milage</strong>: ' + total.toFixed(2); + ' mi';*/
                        } else {
                            toastr.error("<strong>Logan says:</strong><br/>There is not enough information to route the trip. Please add at least 1 pickup and destination location.")
                        }
                    });
                }

                initMap();
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
                $('.scroller2').slimScroll({
                    height: 225
                }); $('.scrollz').slimScroll({
                    height: 475
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
                            minlength: 5,
                            maxlength: 5
                        },
                        comments: {
                            maxlength: 100
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
                            url: '../app/add_event.php?ev=pmk&e=<?php echo $event['event_token']; ?>',
                            type: "POST",
                            data: $('#new_location').serialize(),
                            success: function(data) {
                                $('#draggable').modal('hide');
                                $('#new_location')[0].reset();
                                toastr.success("<strong>Logan says</strong>:<br/>That location has been added to this events record. Let me refresh the event for you, so you can see the changes.");
                                $.ajax({
                                    url: '../app/update_settings.php?update=estimate&ev=<?php echo $event['event_token']; ?>&est=<?php echo $estimate['estimate_token']; ?>&s=0',
                                    type: 'POST',
                                    data: $('#estimate_it').serialize(),
                                    success: function(d) {
                                        location.reload();
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
    } elseif($_GET['v'] == 'e') {
        if(mysql_num_rows($estimates) > 0) {
            $estimate = mysql_fetch_array($estimates);
        }
        ?>
        <div class="col-md-12" style="background-color: white;">
            <div class="portlet">
                <div class="portlet-body">
                    <div class="portlet">
                        <div class="portlet-body" id="invoice">
                            <h3 class="text-center" style="margin-bottom: 50px;">LOCAL, NON-BINDING ESTIMATE <br/><small>Editing</small></h3>
                            <form id="estimate_it" method="POST">
                                <div class="row">
                                    <div class="form-group col-md-4 col-sm-12 col-xs-4">
                                        <label class="control-label">Customer Name <span class="required">*</span></label>
                                        <div class="input-icon" style="border-left: 2px solid #c23f44 !important">
                                            <i class="fa fa-user"></i>
                                            <input type="text" class="form-control placeholder-no-fix" name="customer_name" value="<?php echo $estimate['estimate_customer_name']; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4 col-sm-12 col-xs-4">
                                        <label class="control-label">Customer Email <span class="required">*</span></label>
                                        <div class="input-icon" style="border-left: 2px solid #c23f44 !important">
                                            <i class="fa fa-envelope"></i>
                                            <input type="text" class="form-control placeholder-no-fix" name="customer_email" value="<?php echo $estimate['estimate_customer_email']; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4 col-sm-12 col-xs-4">
                                        <label class="control-label">Customer Phone <span class="required">*</span></label>
                                        <div class="input-icon" style="border-left: 2px solid #c23f44 !important">
                                            <i class="fa fa-phone"></i>
                                            <input type="text" class="form-control placeholder-no-fix mask-phone" name="customer_phone" value="<?php echo clean_phone($estimate['estimate_customer_phone']); ?>">
                                        </div>
                                    </div>
                                </div>
                                <hr/>
                                <div class="row">
                                    <div class="form-group col-md-6 col-sm-12 col-xs-6">
                                        <label class="control-label"><strong>Estimated</strong> Pickup/Dropoff Dates <span class="required">*</span></label>
                                        <div class="" style="border-left: 2px solid #c23f44 !important">
                                            <div class="input-group input-md input-daterange" data-date="10/11/2012" data-date-format="mm/dd/yyyy" style="width: 100% !important;">
                                                <input type="text" class="form-control date-picker" name="startdate" value="<?php echo date("m/d/Y", strtotime($event['event_date_start'])); ?>">
                                                <span class="input-group-addon"> to </span>
                                                <input type="text" class="form-control date-picker" name="enddate" value="<?php echo date("m/d/Y", strtotime($event['event_date_end'])); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6 col-sm-12 col-xs-6">
                                        <label class="control-label"><strong>Estimated</strong> Pickup Time <span class="required">*</span></label>
                                        <select class="form-control" name="time" id="time_select" style="border-left: 2px solid #c23f44 !important">
                                            <option disabled selected value="">Select a start time..</option>
                                            <?php
                                            $timeOptions = mysql_query("SELECT time_start, time_end FROM fmo_locations_times WHERE time_location_token='".mysql_real_escape_string($event['event_location_token'])."'");
                                            if(mysql_num_rows($timeOptions) > 0){
                                                while($t = mysql_fetch_assoc($timeOptions)){
                                                    if(empty($t['time_end'])){
                                                        continue;
                                                    }
                                                    ?>
                                                    <option <?php if($t['time_start']." to ".$t['time_end'] == $estimate['estimate_pickup_time']){echo "selected";} ?> value="<?php echo $t['time_start']; ?> to <?php echo $t['time_end']; ?>"><?php echo $t['time_start']; ?> - <?php echo $t['time_end']; ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group  col-md-3 col-sm-12 col-xs-3">
                                        <label class="control-label"><strong>Event</strong> Name <span class="required">*</span></label>
                                        <div class="input-icon" style="border-left: 2px solid #c23f44 !important">
                                            <i class="fa fa-user"></i>
                                            <input type="text" class="form-control placeholder-no-fix" name="event_name" value="<?php echo $estimate['estimate_name']; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3 col-sm-12 col-xs-3">
                                        <label class="control-label"><strong>Event</strong> Type <span class="required">*</span></label>
                                        <div class="input-icon" style="border-left: 2px solid #c23f44 !important">
                                            <i class="fa fa-tag"></i>
                                            <select class="form-control placeholder-no-fix" name="type" id="binder">
                                                <option disabled selected value="">Select a type..</option>
                                                <?php
                                                $types = mysql_query("SELECT eventtype_name FROM fmo_locations_eventtypes WHERE eventtype_location_token='".mysql_real_escape_string($event['event_location_token'])."'");
                                                if(mysql_num_rows($types) > 0){
                                                    while($type = mysql_fetch_assoc($types)){
                                                        ?>
                                                        <option <?php if($type['eventtype_name'] == $estimate['estimate_type']){echo "selected";} ?> value="<?php echo $type['eventtype_name']; ?>"><?php echo $type['eventtype_name']; ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3 col-sm-12 col-xs-3">
                                        <label class="control-label"><strong>Event</strong> Email <span class="required">*</span></label>
                                        <div class="input-icon" style="border-left: 2px solid #c23f44 !important">
                                            <i class="fa fa-envelope"></i>
                                            <input type="text" class="form-control placeholder-no-fix" name="email" value="<?php echo $estimate['estimate_email']; ?>"/>
                                        </div>
                                    </div>
                                    <div class="form-group  col-md-3 col-sm-12 col-xs-3">
                                        <label class="control-label"><strong>Event</strong> Phone <span class="required">*</span></label>
                                        <div class="input-icon" style="border-left: 2px solid #c23f44 !important">
                                            <i class="fa fa-phone"></i>
                                            <input type="text" class="form-control placeholder-no-fix mask-phone" name="phone" value="<?php echo clean_phone($estimate['estimate_phone']); ?>"/>
                                        </div>
                                    </div>
                                </div>
                                <hr/>
                                <div class="row">
                                    <div class="col-md-6 col-xs-6">
                                        <div class="scrollz">
                                            <div class="portlet">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        Pick up location(s)
                                                    </div>
                                                    <div class="actions">
                                                        <a class="btn default red-stripe" data-toggle="modal" href="#draggable" data-location-type="1">
                                                            <i class="fa fa-plus"></i>
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
                                                            <div id="pickup_h_<?php echo $pk; ?>" class="panel-group">
                                                                <div class="panel panel-default">
                                                                    <div class="panel-heading">
                                                                        <div class="actions pull-right" style="margin-top: -6px; margin-right: -9px">
                                                                            <a href="javascript:;" class="btn btn-default btn-sm edit" data-edit="pu_<?php echo $pickup['address_id']; ?>">
                                                                                <i class="fa fa-pencil"></i> <span class="hidden-sm hidden-md hidden-xs">Edit</span> </a>
                                                                        </div>
                                                                        <div class="caption">
                                                                            <h4 class="panel-title">
                                                                                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#pickup_h_<?php echo $pk; ?>" href="#pickup_<?php echo $pk; ?>" aria-expanded="false"><strong><?php echo $pickup['address_address']; ?>, <?php echo $pickup['address_city']; ?>, <?php echo $pickup['address_state']; ?>, <?php echo $pickup['address_zip'];  ?></strong></a>
                                                                            </h4>
                                                                        </div>
                                                                    </div>
                                                                    <div id="pickup_<?php echo $pk; ?>" class="panel-collapse collapse" aria-expanded="true" style="height: 0px;">
                                                                        <div class="panel-body">
                                                                            <address>
                                                                                <strong>Physical Address</strong><br>
                                                                                <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_address" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new street address.." data-url="../app/update_settings.php?update=event_addy">
                                                                                    <?php echo $pickup['address_address']; ?>
                                                                                </a><br/>
                                                                                <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_city" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new city.." data-url="../app/update_settings.php?update=event_addy">
                                                                                    <?php echo $pickup['address_city']; ?>
                                                                                </a>,
                                                                                <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_state" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Select new state.." data-url="../app/update_settings.php?update=event_addy">
                                                                                    <?php echo $pickup['address_state']; ?>
                                                                                </a>
                                                                                <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_zip" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new zipcode.." data-url="../app/update_settings.php?update=event_addy">
                                                                                    <?php echo $pickup['address_zip']; ?>
                                                                                </a><br/>
                                                                                <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_county" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new county.." data-url="../app/update_settings.php?update=event_addy">
                                                                                    <?php echo $pickup['address_county']; ?>
                                                                                </a>
                                                                            </address>
                                                                            <address>
                                                                                Closest intersection:
                                                                                <strong>
                                                                                    <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_closest_intersection" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new closest intersection.." data-url="../app/update_settings.php?update=event_addy">
                                                                                        <?php echo $pickup['address_closest_intersection']; ?>
                                                                                    </a><br/>
                                                                                </strong>

                                                                                Stairs:
                                                                                <strong>
                                                                                    <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_stairs" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new closest intersection.." data-url="../app/update_settings.php?update=event_addy">
                                                                                        <?php echo $pickup['address_stairs']; ?>
                                                                                    </a><br/>
                                                                                </strong>

                                                                                Parking Distance:
                                                                                <strong>
                                                                                    <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_distance" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new distance.." data-url="../app/update_settings.php?update=event_addy">
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
                                                                                    <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_comments" data-pk="<?php echo $pickup['address_id']; ?>" data-type="textarea" data-placement="right" data-title="Enter new comments.." data-url="../app/update_settings.php?update=event_addy">
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
                                                        Destination location(s)
                                                    </div>
                                                    <div class="actions">
                                                        <a class="btn default red-stripe" data-toggle="modal" href="#draggable" data-location-type="2">
                                                            <i class="fa fa-plus"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="portlet-body">
                                                    <?php
                                                    $dests = mysql_query("SELECT address_address, address_suite, address_city, address_state, address_zip, address_closest_intersection, address_county, address_stairs, address_distance, address_comments FROM fmo_locations_events_addresses WHERE address_event_token='".mysql_real_escape_string($event['event_token'])."' AND address_type=2");
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
                                                                                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#dest_h_<?php echo $pk; ?>" href="#dest_<?php echo $pk; ?>" aria-expanded="false"><strong><?php echo $dest['address_address']; ?>, <?php echo $dest['address_city']; ?>, <?php echo $dest['address_state']; ?>, <?php echo $dest['address_zip']; ?></strong></a>
                                                                            </h4>
                                                                        </div>
                                                                    </div>
                                                                    <div id="dest_<?php echo $pk; ?>" class="panel-collapse collapse" aria-expanded="true" style="height: 0px;">
                                                                        <div class="panel-body">
                                                                            <address>
                                                                                <strong>Physical Address</strong><br>
                                                                                <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_address" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new street address.." data-url="../app/update_settings.php?update=event_addy">
                                                                                    <?php echo $dest['address_address']; ?>
                                                                                </a><br/>
                                                                                <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_city" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new city.." data-url="../app/update_settings.php?update=event_addy">
                                                                                    <?php echo $dest['address_city']; ?>
                                                                                </a>,
                                                                                <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_state" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Select new state.." data-url="../app/update_settings.php?update=event_addy">
                                                                                    <?php echo $dest['address_state']; ?>
                                                                                </a>
                                                                                <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_zip" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new zipcode.." data-url="../app/update_settings.php?update=event_addy">
                                                                                    <?php echo $dest['address_zip']; ?>
                                                                                </a><br/>
                                                                                <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_county" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new county.." data-url="../app/update_settings.php?update=event_addy">
                                                                                    <?php echo $dest['address_county']; ?>
                                                                                </a>
                                                                            </address>
                                                                            <address>
                                                                                Closest intersection:
                                                                                <strong>
                                                                                    <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_closest_intersection" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new closest intersection.." data-url="../app/update_settings.php?update=event_addy">
                                                                                        <?php echo $dest['address_closest_intersection']; ?>
                                                                                    </a><br/>
                                                                                </strong>

                                                                                Stairs:
                                                                                <strong>
                                                                                    <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_stairs" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new closest intersection.." data-url="../app/update_settings.php?update=event_addy">
                                                                                        <?php echo $dest['address_stairs']; ?>
                                                                                    </a><br/>
                                                                                </strong>

                                                                                Parking Distance:
                                                                                <strong>
                                                                                    <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_distance" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new distance.." data-url="../app/update_settings.php?update=event_addy">
                                                                                        <?php echo $dest['address_distance']; ?>
                                                                                    </a><br/>
                                                                                </strong>
                                                                            </address>
                                                                            <address>
                                                                                Comments: <br/>
                                                                                <strong>
                                                                                    <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_comments" data-pk="<?php echo $dest['address_id']; ?>" data-type="textarea" data-placement="right" data-title="Enter new comments.." data-url="../app/update_settings.php?update=event_addy">
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
                                            <div class="form-group">
                                                <label class="control-label"><strong>Customer / Event / Location</strong> Comments / Exclusions </label>
                                                <textarea placeholder="" class="form-control bol_comments" style="height: 165px;"><?php echo $estimate['estimate_comments']; ?></textarea>
                                                <span style="margin-top: -23px; margin-right: 10px;" class="bol_countdown pull-right"></span>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-6 col-xs-6">
                                        <div id="gmap_basic" class="gmaps" style="height: 475px;">
                                        </div>
                                    </div>
                                </div>
                                <hr/>
                                <h4><strong>Items for sale(s)</strong> use this tool to estimate all items/costs.</h4>
                                <div class="row no-print">
                                    <div class="col-md-12">
                                        <div class="scroller2" style="height: 290px;" data-always-visible="1" data-rail-visible1="1">
                                            <div class="portlet">
                                                <div class="portlet-body">
                                                    <div class="table-container">
                                                        <table class="table table-striped table-hover datatable" data-src="../app/api/estimate.php?type=rates&est=<?php echo $estimate['estimate_token']; ?>&luid=<?php echo $estimate['estimate_location_token']; ?>">
                                                            <thead>
                                                            <tr role="row" class="heading">
                                                                <th>
                                                                    Service Name
                                                                </th>
                                                                <th width="12%" class="text-center">
                                                                    Estimate item <i class="fa fa-arrow-right"></i>
                                                                </th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> <br/>
                                <h4><strong>Estimated cost(s)</strong> from what you've added to the estimate.</h4>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-container">
                                            <table class="table table-striped table-hover datatable" id="sales" data-src="../app/api/estimate.php?type=sales&est=<?php echo $estimate['estimate_token']; ?>&luid=<?php echo $estimate['estimate_location_token']; ?>">
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
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 invoice-block text-right">
                                        <ul class="list-unstyled amounts" style="margin-bottom: 0;">
                                            <li>
                                                Sub total: <h3 style="display: inline; color: #F3565D!important" class="text-danger bold">$<span id="owe_sub_total"></span></h3>
                                            </li>
                                            <li>
                                                Taxes due:  <h3 style="display: inline; color: #F3565D!important" class="text-danger bold">$<span id="owe_tax"></span></h3>
                                            </li>
                                            <li>
                                                Grand Total: <h3 style="display: inline; color: #F3565D!important" class="text-danger bold">$<span id="owe_total"></span></h3>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="row" style="margin-top: 20px;">
                                    <div class="col-md-12">
                                        <div class="alert alert-warning text-center">
                                            <h3 style="margin-top: 10px;"><strong>Estimated total</strong> for all services listed above: <strong class="font-red">$<span id="TOTAL"></span></strong></h3>
                                            <br/>
                                            <h3><strong>Optional Valuation Premium</strong>: <strong class="font-red">$<span id="VALUATION">300.00</span></strong></h3>
                                        </div>
                                    </div>
                                </div>
                                <hr/>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="portlet">
                                            <div class="portlet-title tabbable-line">
                                                <ul class="nav nav-tabs nav-justified">
                                                    <li class="active">
                                                        <a href="#free" data-toggle="tab" aria-expanded="true" style="color: black;">
                                                            Free Valuation</a>
                                                    </li>
                                                    <li class="">
                                                        <a href="#premium" data-toggle="tab" aria-expanded="false" style="color: black;">
                                                            Premium Valuation</a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="portlet-body">
                                                <div class="tab-content">
                                                    <div class="tab-pane active" id="free">
                                                        <strong class="font-red">Re-Imbursement for Lost or Damaged Goods - Customer must personally initial cargo liability choice</strong>
                                                        <br/> <br/>
                                                        <input type="checkbox" name="val" value="0"/> <strong class="font-red">I agree to minimal reimbursement for lost or damaged goods. I understand and accept that I will be reimbursed for lost or damaged goods at a minimal amount not exceeding sixty cents per pound per article.</strong>
                                                    </div>
                                                    <div class="tab-pane" id="premium">
                                                        <strong class="font-red">Re-Imbursement for Lost or Damaged Goods - Customer must personally initial cargo liability choice</strong>
                                                        <br/><br/>
                                                        <input type="checkbox" name="val" value="1"/> <strong class="font-red">I accept reimbursement equal to the replacement cost of lost or damaged goods. I declare a total replacement value of.. <br/>
                                                            $ <input type="number" class="input-sm" name="val"/><br/> or a minimum of six dollars per pound times the weight of the shipment, whichever is greater. I understand that total reimbursement for lost or damaged goods shall not exceed this declared value. I understand that failure to disclose any article valued at greater than one hundred dollars per pound may limit the carrier's reimbursement liability to this maximum per article.</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h5><span class="text-danger">*</span> Payments at delivery must be <strong>CASH</strong> or <strong>Certified Funds</strong>.</h5>
                                <h5><span class="text-danger">*</span> All other types must be approved by carrier <strong>in advance</strong>.</h5> <br/>
                                <h5><span class="text-danger">*</span> Other disclaimers:</h5>
                                <h5><strong>THIS IS A BINDING ESTIMATE! IF THIS ESTIMATE IS ACCEPTED, I WILL BE BOUND BY ALL LAWFUL TERMS AND CONDITIONS PROVIDED BY THE CARRIER.</strong></h5>
                                <h5>THIS ESTIMATE WAS BY <strong><?php echo name($estimate['estimate_estimator']); ?></strong>, FROM <strong>HERE TO THERE MOVERS</strong>. (317) 547-6683, indianapolis@heretotheremovers.com</h5>
                                <h5>Company License Numbers: DOT # 1062160 / MC 777343 / PUCO No. 506822 / C 4009 / C - 2612 / IM 2792 </h5>
                                <h5>Please see required notices and disclaimers with your estimate, as well as other services and fees.</h5>
                                <hr/>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet">
                                            <div class="portlet-title">
                                                <span class="pull-left">
                                                    <h4 style="font-size: 21px; margin-top: 4px;"><strong >Customer Signature</strong> <small>so we know you verify above information.</small></h4>
                                                </span>
                                                <button type="button" class="btn red pull-right" id="clear" style="margin-left: 10px; display: none;" onclick="$('#signature').jSignature('clear')">Clear</button>
                                                <button type="button" class="btn blue pull-right" id="save_sig">Save</button>
                                            </div>
                                            <div class="portlet-body" style="border: 1px solid #b83a3e">
                                                <div id="signature"></div>
                                                <input type="hidden" id="hiddenSigData" name="cus_sig" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="estimator-sig">
                                    <div class="col-md-12">
                                        <div class="portlet">
                                            <div class="portlet-title">
                                                <span class="pull-left">
                                                    <h4 style="font-size: 21px; margin-top: 4px;"><strong >Estimator Signature</strong></h4>
                                                </span>
                                                <button type="button" class="btn red pull-right" id="es-clear"  style="margin-left: 10px; display: none;" onclick="$('#es-signature').jSignature('clear')">Clear</button>
                                                <button type="button" class="btn blue pull-right" id="es-save_sig">Save</button>
                                            </div>
                                            <div class="portlet-body" style="border: 1px solid #b83a3e">
                                                <div id="es-signature"></div>
                                                <input type="hidden" id="es-hiddenSigData" name="es_sig" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr/>
                                <label><input type="checkbox" name="txt" value="1"> <Strong>Re-send estimate to customer</Strong></label>
                                <button type="button" class="btn red button-submit btn-block" id="finisher">
                                   Save & exit  <i class="m-icon-swapright m-icon-white"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            jQuery(document).ready(function() {

                $('#finisher').on('click', function () {
                    $(this).html("<i class='fa fa-spinner fa-spin'></i>");
                    $.ajax({
                        url: '../app/update_settings.php?update=estimate&ev=<?php echo $event['event_token']; ?>&est=<?php echo $estimate['estimate_token']; ?>&s=1&cuid=<?php echo $event['event_company_token']; ?>',
                        type: 'POST',
                        data: $('#estimate_it').serialize(),
                        success: function(d) {
                            $('#page-content').html("<br/> <br/> <br/> <div class=\"row\" style=\"margin-bottom: 50px;\">\n" +
                                "                                                                    <div class=\"col-md-12 page-404\">\n" +
                                "                                                                        <div class=\"number font-green\" style=\"top: 12px !important;\">\n" +
                                "                                                                            <i style=\"font-size: 100px;\" class=\"icon-check\"></i>\n" +
                                "                                                                        </div>\n" +
                                "                                                                        <div class=\"details\">\n" +
                                "                                                                            <h3>Your changes have been saved!</h3>\n" +
                                "                                                                            <p>\n" +
                                "                                                                                A text-message & email has been re-sent to customer (if checked)!<br/>\n" +
                                "                                                                                <strong>We know it was you,</strong> <?php echo name($estimate['estimate_estimator']); ?>\n"+
                                "                                                                            </p>\n" +
                                "                                                                        </div>\n" +
                                "                                                                    </div>\n" +
                                "                                                                </div><br/>")
                        },
                        error: function() {
                            toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                        }
                    });
                });

                function updateInv(estimate, luid){
                    $.ajax({
                        url: '../app/api/estimate.php?type=inv&luid='+luid,
                        type: 'POST',
                        data: {
                            estimate: '' + estimate + ''
                        },
                        success: function(m){
                            var owe = JSON.parse(m);
                            $(document).find('#owe_sub_total').html(parseFloat(owe.sub_total).toFixed(2));
                            $(document).find('#owe_tax').html(parseFloat(owe.tax).toFixed(2));
                            $(document).find('#owe_total').html(parseFloat(owe.total).toFixed(2));
                            $(document).find('#TOTAL').html(parseFloat(owe.total).toFixed(2));
                            $(document).find('#owe_total_unpaid').html(parseFloat(owe.unpaid).toFixed(2));
                        },
                        error: function(e){

                        }
                    });
                }

                $(document).on('click', '.add_item', function(){
                    var estimate = $(this).attr('data-est');
                    var luid     = $(this).attr('data-luid');
                    $.ajax({
                        url: '../app/api/actions.php?ty=ei',
                        type: 'POST',
                        data: {
                            srv_id: $(this).attr('data-id'),
                            srv_est: estimate
                        },
                        success: function(d){
                            var inf = JSON.parse(d);
                            toastr.success("<strong>Logan says</strong>:<br/> "+inf.item+" added to <?php echo $user['user_fname']; ?>'s invoice for "+inf.cost);
                            $('#sales').DataTable().ajax.reload();
                            updateInv(estimate, luid);
                        },
                        error: function(e){
                            toastr.error("<strong>Logan says</strong>:<br/> An unexpected error has occurred. Please try again later.")
                        }
                    });
                });

                $(document).on('click', '.delete_item',  function() {
                    var del    = $(this).attr('data-delete');
                    var est    = $(this).attr('data-estimate');
                    var luid   = '<?php echo $estimate['estimate_location_token']; ?>';
                    $(this).closest('tr').remove();
                    $.ajax({
                        url: '../app/update_settings.php?setting=delete_item_est',
                        type: 'POST',
                        data: {
                            del: del,
                            est: est
                        },
                        success: function(s){
                            updateInv(est, luid);
                        },
                        error: function(e){
                            updateInv(est, luid);
                        }
                    });
                });

                $(document).on('click', '.edit', function(){
                    var line   = $(this).attr('data-edit');
                    var reload = $(this).attr('data-reload');
                    var est    = $(this).attr('data-estimate');
                    var luid   = $(this).attr('data-luid');
                    var selec  = $(this).attr('data-selec');
                    $('.'+line).editable({
                        step: 'any',
                        success: function(e) {
                            if(reload == "est"){
                                updateInv(est, luid);
                            } if(reload){
                                $('.datatable').DataTable().ajax.reload();
                            }
                        }
                    }).on("shown", function(e, editable) {
                        if(selec == 'autoselect'){
                            editable.input.$input.get(0).select();
                        }
                        //console.log(type + " is the type!");
                    });
                    toastr.info("<strong>Logan says</strong>:<br/>Editable information has been underlined with blue dots.")
                });

                $.ajax({
                    url: '../app/api/estimate.php?type=inv&luid=<?php echo $estimate['estimate_location_token']; ?>',
                    type: 'POST',
                    data: {
                        estimate: '<?php echo $estimate['estimate_token']; ?>'
                    },
                    success: function(m){
                        var owe = JSON.parse(m);
                        $(document).find('#owe_sub_total').html(parseFloat(owe.sub_total).toFixed(2));
                        $(document).find('#owe_tax').html(parseFloat(owe.tax).toFixed(2));
                        $(document).find('#owe_total').html(parseFloat(owe.total).toFixed(2));
                        $(document).find('#TOTAL').html(parseFloat(owe.total).toFixed(2));
                        $(document).find('#owe_total_unpaid').html(parseFloat(owe.unpaid).toFixed(2));
                    },
                    error: function(e){

                    }
                });

                $('input[type="checkbox"]').iCheck({
                    checkboxClass: 'icheckbox_minimal',
                    radioClass: 'iradio_minimal'
                });

                $('.bol_comments').on('change', function(){
                    var comment = $(this).val();
                    $.ajax({
                        url: '../app/update_settings.php?update=est_comments',
                        type: 'POST',
                        data: {
                            comment: comment,
                            ev: '<?php echo $estimate['estimate_token']; ?>'
                        },
                        success: function(bol_cmts){
                            toastr.success("<strong>Logan says:</strong><br/> Comments saved (wow thats cool). ");
                        },
                        error: function(){

                        }
                    }) ;
                });

                $("#signature").jSignature();
                $("#es-signature").jSignature();

                <?php
                if(!empty($estimate['estimate_es_sig'])){
                ?>
                $("#es-signature").jSignature("importData", 'data:'+"<?php echo $estimate['estimate_es_sig']; ?>");
                <?php
                }
                if(!empty($estimate['estimate_cus_sig'])){
                ?>
                $("#signature").jSignature("importData", 'data:'+"<?php echo $estimate['estimate_cus_sig']; ?>");
                <?php
                }
                ?>

                $('#save_sig').click(function(){
                    var sigData = $('#signature').jSignature('getData','base30');
                    $('#hiddenSigData').val(sigData);

                    $('#save_sig').html("Saved!");
                    $('#clear').show();

                    $('#estimator-sig').show();
                });


                $('#es-save_sig').click(function(){
                    var sigData = $('#es-signature').jSignature('getData','base30');
                    $('#es-hiddenSigData').val(sigData);

                    $('#es-save_sig').html("Saved!");
                    $('#es-clear').show();

                    $('#finisher').show();
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
                });

                $(".mask-phone").inputmask("mask", {
                    "mask": "(999) 999-9999"
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
                            /* var summaryPanel = document.getElementById('results-map-panel');
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
                            summaryPanel.innerHTML += '<strong>Total milage</strong>: ' + total.toFixed(2); + ' mi';*/
                        } else {
                            toastr.error("<strong>Logan says:</strong><br/>There is not enough information to route the trip. Please add at least 1 pickup and destination location.")
                        }
                    });
                }

                initMap();
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
                $('.scroller2').slimScroll({
                    height: 225
                }); $('.scrollz').slimScroll({
                    height: 475
                });
                document.title = "Editing an estimate.";
            });
        </script>
        <?php
    } elseif($_GET['v'] == 'v'){
        if(mysql_num_rows($estimates) > 0) {
            $estimate = mysql_fetch_array($estimates);
        }
        ?>
        <div class="col-md-12" style="background-color: white;" id="conf">
            <div class="portlet">
                <div class="portlet-body">
                    <div class="portlet">
                        <div class="portlet-body" id="invoice">
                            <h3 class="text-center" style="margin-bottom: 50px;"><small>Viewing your</small><br/>LOCAL, NON-BINDING ESTIMATE </h3>
                            <form id="estimate_it" method="POST">
                                <div class="row">
                                    <div class="form-group col-md-4 col-sm-12">
                                        <label class="control-label">Customer Name </label>
                                        <div class="input-icon" style="border-left: 2px solid #c23f44 !important">
                                            <i class="fa fa-user"></i>
                                            <input readonly type="text" class="form-control placeholder-no-fix" value="<?php echo $estimate['estimate_customer_name']; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4 col-sm-12">
                                        <label class="control-label">Customer Email</label>
                                        <div class="input-icon" style="border-left: 2px solid #c23f44 !important">
                                            <i class="fa fa-envelope"></i>
                                            <input readonly type="text" class="form-control placeholder-no-fix" value="<?php echo $estimate['estimate_customer_email']; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4 col-sm-12">
                                        <label class="control-label">Customer Phone</label>
                                        <div class="input-icon" style="border-left: 2px solid #c23f44 !important">
                                            <i class="fa fa-phone"></i>
                                            <input readonly type="text" class="form-control placeholder-no-fix mask-phone" value="<?php echo clean_phone($estimate['estimate_customer_phone']); ?>">
                                        </div>
                                    </div>
                                </div>
                                <hr/>
                                <div class="row">
                                    <div class="form-group col-md-6 col-sm-12">
                                        <label class="control-label"><strong>Estimated</strong> Pickup/Dropoff Dates</label>
                                        <div class="" style="border-left: 2px solid #c23f44 !important">
                                            <div class="input-group input-md" style="width: 100% !important;">
                                                <input readonly type="text" class="form-control date-picker" value="<?php echo date("m/d/Y", strtotime($event['event_date_start'])); ?>">
                                                <span class="input-group-addon"> to </span>
                                                <input readonly type="text" class="form-control date-picker" value="<?php echo date("m/d/Y", strtotime($event['event_date_end'])); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6 col-sm-12">
                                        <label class="control-label"><strong>Estimated</strong> Pickup Time </label>
                                        <select class="form-control" readonly style="border-left: 2px solid #c23f44 !important">
                                            <?php
                                            $timeOptions = mysql_query("SELECT time_start, time_end FROM fmo_locations_times WHERE time_location_token='".mysql_real_escape_string($event['event_location_token'])."'");
                                            if(mysql_num_rows($timeOptions) > 0){
                                                while($t = mysql_fetch_assoc($timeOptions)){
                                                    if(empty($t['time_end'])){
                                                        continue;
                                                    }
                                                    ?>
                                                    <option <?php if($t['time_start']." to ".$t['time_end'] == $estimate['estimate_pickup_time']){echo "selected";} ?> value="<?php echo $t['time_start']; ?> to <?php echo $t['time_end']; ?>"><?php echo $t['time_start']; ?> - <?php echo $t['time_end']; ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group  col-md-3 col-sm-12">
                                        <label class="control-label"><strong>Event</strong> Name</label>
                                        <div class="input-icon" style="border-left: 2px solid #c23f44 !important">
                                            <i class="fa fa-user"></i>
                                            <input readonly type="text" class="form-control placeholder-no-fix" value="<?php echo $estimate['estimate_name']; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3 col-sm-12">
                                        <label class="control-label"><strong>Event</strong> Type </label>
                                        <div class="input-icon" style="border-left: 2px solid #c23f44 !important">
                                            <i class="fa fa-tag"></i>
                                            <select readonly class="form-control placeholder-no-fix">
                                                <?php
                                                $types = mysql_query("SELECT eventtype_name FROM fmo_locations_eventtypes WHERE eventtype_location_token='".mysql_real_escape_string($event['event_location_token'])."'");
                                                if(mysql_num_rows($types) > 0){
                                                    while($type = mysql_fetch_assoc($types)){
                                                        ?>
                                                        <option <?php if($type['eventtype_name'] == $estimate['estimate_type']){echo "selected";} ?> value="<?php echo $type['eventtype_name']; ?>"><?php echo $type['eventtype_name']; ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3 col-sm-12">
                                        <label class="control-label"><strong>Event</strong> Email <span class="required">*</span></label>
                                        <div class="input-icon" style="border-left: 2px solid #c23f44 !important">
                                            <i class="fa fa-envelope"></i>
                                            <input readonly type="text" class="form-control placeholder-no-fix"  value="<?php echo $estimate['estimate_email']; ?>"/>
                                        </div>
                                    </div>
                                    <div class="form-group  col-md-3 col-sm-12">
                                        <label class="control-label"><strong>Event</strong> Phone <span class="required">*</span></label>
                                        <div class="input-icon" style="border-left: 2px solid #c23f44 !important">
                                            <i class="fa fa-phone"></i>
                                            <input readonly type="text" class="form-control placeholder-no-fix mask-phone" value="<?php echo clean_phone($estimate['estimate_phone']); ?>"/>
                                        </div>
                                    </div>
                                </div>
                                <hr/>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="portlet">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    Pick up location(s)
                                                </div>
                                                <div class="actions">
                                                    <a class="btn default red-stripe" data-toggle="modal" href="#draggable" data-location-type="1">
                                                        <i class="fa fa-plus"></i>
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
                                                        <div id="pickup_h_<?php echo $pk; ?>" class="panel-group">
                                                            <div class="panel panel-default">
                                                                <div class="panel-heading">
                                                                    <div class="actions pull-right" style="margin-top: -6px; margin-right: -9px">
                                                                        <a href="javascript:;" class="btn btn-default btn-sm edit" data-edit="pu_<?php echo $pickup['address_id']; ?>">
                                                                            <i class="fa fa-pencil"></i> <span class="hidden-sm hidden-md hidden-xs">Edit</span> </a>
                                                                    </div>
                                                                    <div class="caption">
                                                                        <h4 class="panel-title">
                                                                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#pickup_h_<?php echo $pk; ?>" href="#pickup_<?php echo $pk; ?>" aria-expanded="false"><strong><?php echo $pickup['address_address']; ?>, <?php echo $pickup['address_city']; ?>, <?php echo $pickup['address_state']; ?>, <?php echo $pickup['address_zip'];  ?></strong></a>
                                                                        </h4>
                                                                    </div>
                                                                </div>
                                                                <div id="pickup_<?php echo $pk; ?>" class="panel-collapse collapse" aria-expanded="true" style="height: 0px;">
                                                                    <div class="panel-body">
                                                                        <address>
                                                                            <strong>Physical Address</strong><br>
                                                                            <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_address" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new street address.." data-url="../app/update_settings.php?update=event_addy">
                                                                                <?php echo $pickup['address_address']; ?>
                                                                            </a><br/>
                                                                            <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_city" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new city.." data-url="../app/update_settings.php?update=event_addy">
                                                                                <?php echo $pickup['address_city']; ?>
                                                                            </a>,
                                                                            <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_state" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Select new state.." data-url="../app/update_settings.php?update=event_addy">
                                                                                <?php echo $pickup['address_state']; ?>
                                                                            </a>
                                                                            <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_zip" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new zipcode.." data-url="../app/update_settings.php?update=event_addy">
                                                                                <?php echo $pickup['address_zip']; ?>
                                                                            </a><br/>
                                                                            <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_county" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new county.." data-url="../app/update_settings.php?update=event_addy">
                                                                                <?php echo $pickup['address_county']; ?>
                                                                            </a>
                                                                        </address>
                                                                        <address>
                                                                            Closest intersection:
                                                                            <strong>
                                                                                <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_closest_intersection" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new closest intersection.." data-url="../app/update_settings.php?update=event_addy">
                                                                                    <?php echo $pickup['address_closest_intersection']; ?>
                                                                                </a><br/>
                                                                            </strong>

                                                                            Stairs:
                                                                            <strong>
                                                                                <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_stairs" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new closest intersection.." data-url="../app/update_settings.php?update=event_addy">
                                                                                    <?php echo $pickup['address_stairs']; ?>
                                                                                </a><br/>
                                                                            </strong>

                                                                            Parking Distance:
                                                                            <strong>
                                                                                <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_distance" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new distance.." data-url="../app/update_settings.php?update=event_addy">
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
                                                                                <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_comments" data-pk="<?php echo $pickup['address_id']; ?>" data-type="textarea" data-placement="right" data-title="Enter new comments.." data-url="../app/update_settings.php?update=event_addy">
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
                                                    Destination location(s)
                                                </div>
                                                <div class="actions">
                                                    <a class="btn default red-stripe" data-toggle="modal" href="#draggable" data-location-type="2">
                                                        <i class="fa fa-plus"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                                <?php
                                                $dests = mysql_query("SELECT address_address, address_suite, address_city, address_state, address_zip, address_closest_intersection, address_county, address_stairs, address_distance, address_comments FROM fmo_locations_events_addresses WHERE address_event_token='".mysql_real_escape_string($event['event_token'])."' AND address_type=2");
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
                                                                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#dest_h_<?php echo $pk; ?>" href="#dest_<?php echo $pk; ?>" aria-expanded="false"><strong><?php echo $dest['address_address']; ?>, <?php echo $dest['address_city']; ?>, <?php echo $dest['address_state']; ?>, <?php echo $dest['address_zip']; ?></strong></a>
                                                                        </h4>
                                                                    </div>
                                                                </div>
                                                                <div id="dest_<?php echo $pk; ?>" class="panel-collapse collapse" aria-expanded="true" style="height: 0px;">
                                                                    <div class="panel-body">
                                                                        <address>
                                                                            <strong>Physical Address</strong><br>
                                                                            <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_address" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new street address.." data-url="../app/update_settings.php?update=event_addy">
                                                                                <?php echo $dest['address_address']; ?>
                                                                            </a><br/>
                                                                            <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_city" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new city.." data-url="../app/update_settings.php?update=event_addy">
                                                                                <?php echo $dest['address_city']; ?>
                                                                            </a>,
                                                                            <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_state" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Select new state.." data-url="../app/update_settings.php?update=event_addy">
                                                                                <?php echo $dest['address_state']; ?>
                                                                            </a>
                                                                            <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_zip" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new zipcode.." data-url="../app/update_settings.php?update=event_addy">
                                                                                <?php echo $dest['address_zip']; ?>
                                                                            </a><br/>
                                                                            <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_county" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new county.." data-url="../app/update_settings.php?update=event_addy">
                                                                                <?php echo $dest['address_county']; ?>
                                                                            </a>
                                                                        </address>
                                                                        <address>
                                                                            Closest intersection:
                                                                            <strong>
                                                                                <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_closest_intersection" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new closest intersection.." data-url="../app/update_settings.php?update=event_addy">
                                                                                    <?php echo $dest['address_closest_intersection']; ?>
                                                                                </a><br/>
                                                                            </strong>

                                                                            Stairs:
                                                                            <strong>
                                                                                <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_stairs" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new closest intersection.." data-url="../app/update_settings.php?update=event_addy">
                                                                                    <?php echo $dest['address_stairs']; ?>
                                                                                </a><br/>
                                                                            </strong>

                                                                            Parking Distance:
                                                                            <strong>
                                                                                <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_distance" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new distance.." data-url="../app/update_settings.php?update=event_addy">
                                                                                    <?php echo $dest['address_distance']; ?>
                                                                                </a><br/>
                                                                            </strong>
                                                                        </address>
                                                                        <address>
                                                                            Comments: <br/>
                                                                            <strong>
                                                                                <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_comments" data-pk="<?php echo $dest['address_id']; ?>" data-type="textarea" data-placement="right" data-title="Enter new comments.." data-url="../app/update_settings.php?update=event_addy">
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
                                        <div class="form-group">
                                            <label class="control-label"><strong>Customer / Event / Location</strong> Comments / Exclusions </label>
                                            <textarea placeholder="" class="form-control bol_comments" style="height: 165px;"><?php echo $estimate['estimate_comments']; ?></textarea>
                                            <span style="margin-top: -23px; margin-right: 10px;" class="bol_countdown pull-right"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div id="gmap_basic" class="gmaps" style="height: 475px;">
                                        </div>
                                    </div>
                                </div>
                                <hr/>
                                <h4><strong>Estimated cost(s)</strong> from what you've added to the estimate.</h4>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-container">
                                            <table class="table table-striped table-hover datatable" id="sales" data-src="../app/api/estimate.php?type=sales&est=<?php echo $estimate['estimate_token']; ?>&luid=<?php echo $estimate['estimate_location_token']; ?>&e=f">
                                                <thead>
                                                <tr role="row" class="heading">
                                                    <th>
                                                        Item
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
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 invoice-block text-right">
                                        <ul class="list-unstyled amounts" style="margin-bottom: 0;">
                                            <li>
                                                Sub total: <h3 style="display: inline; color: #F3565D!important" class="text-danger bold">$<span id="owe_sub_total"></span></h3>
                                            </li>
                                            <li>
                                                Taxes due:  <h3 style="display: inline; color: #F3565D!important" class="text-danger bold">$<span id="owe_tax"></span></h3>
                                            </li>
                                            <li>
                                                Grand Total: <h3 style="display: inline; color: #F3565D!important" class="text-danger bold">$<span id="owe_total"></span></h3>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="row" style="margin-top: 20px;">
                                    <div class="col-md-12">
                                        <div class="alert alert-warning text-center">
                                            <h3 style="margin-top: 10px;"><strong>Estimated total</strong> for all services listed above: <strong class="font-red">$<span id="TOTAL"></span></strong></h3>
                                            <br/>
                                            <h3><strong>Optional Valuation Premium</strong>: <strong class="font-red">$<span id="VALUATION">300.00</span></strong></h3>
                                        </div>
                                    </div>
                                </div>
                                <hr/>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="portlet">
                                            <div class="portlet-title tabbable-line">
                                                <ul class="nav nav-tabs nav-justified">
                                                    <li class="active">
                                                        <a href="#free" data-toggle="tab" aria-expanded="true" style="color: black;">
                                                            Free Valuation</a>
                                                    </li>
                                                    <li class="">
                                                        <a href="#premium" data-toggle="tab" aria-expanded="false" style="color: black;">
                                                            Premium Valuation</a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="portlet-body">
                                                <div class="tab-content">
                                                    <div class="tab-pane active" id="free">
                                                        <strong class="font-red">Re-Imbursement for Lost or Damaged Goods - Customer must personally initial cargo liability choice</strong>
                                                        <br/> <br/>
                                                        <input type="checkbox" name="val" value="0"/> <strong class="font-red">I agree to minimal reimbursement for lost or damaged goods. I understand and accept that I will be reimbursed for lost or damaged goods at a minimal amount not exceeding sixty cents per pound per article.</strong>
                                                    </div>
                                                    <div class="tab-pane" id="premium">
                                                        <strong class="font-red">Re-Imbursement for Lost or Damaged Goods - Customer must personally initial cargo liability choice</strong>
                                                        <br/><br/>
                                                        <input type="checkbox" name="val" value="1"/> <strong class="font-red">I accept reimbursement equal to the replacement cost of lost or damaged goods. I declare a total replacement value of.. <br/>
                                                            $ <input type="number" class="input-sm" name="val"/><br/> or a minimum of six dollars per pound times the weight of the shipment, whichever is greater. I understand that total reimbursement for lost or damaged goods shall not exceed this declared value. I understand that failure to disclose any article valued at greater than one hundred dollars per pound may limit the carrier's reimbursement liability to this maximum per article.</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h5><span class="text-danger">*</span> Payments at delivery must be <strong>CASH</strong> or <strong>Certified Funds</strong>.</h5>
                                <h5><span class="text-danger">*</span> All other types must be approved by carrier <strong>in advance</strong>.</h5> <br/>
                                <h5><span class="text-danger">*</span> Other disclaimers:</h5>
                                <h5><strong>THIS IS A BINDING ESTIMATE! IF THIS ESTIMATE IS ACCEPTED, I WILL BE BOUND BY ALL LAWFUL TERMS AND CONDITIONS PROVIDED BY THE CARRIER.</strong></h5>
                                <h5>THIS ESTIMATE WAS BY <strong><?php echo name($estimate['estimate_estimator']); ?></strong>, FROM <strong>HERE TO THERE MOVERS</strong>. (317) 547-6683, indianapolis@heretotheremovers.com</h5>
                                <h5>Company License Numbers: DOT # 1062160 / MC 777343 / PUCO No. 506822 / C 4009 / C - 2612 / IM 2792 </h5>
                                <h5>Please see required notices and disclaimers with your estimate, as well as other services and fees.</h5>
                                <hr/>
                                <div class="row" id="estimator-sig">
                                    <div class="col-md-12">
                                        <div class="portlet">
                                            <div class="portlet-title">
                                                <span class="pull-left">
                                                    <h4 style="font-size: 21px; margin-top: 4px;"><strong >Estimator Signature</strong></h4>
                                                </span>
                                            </div>
                                            <div class="portlet-body" style="border: 1px solid #b83a3e">
                                                <div id="es-signature"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr/>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet">
                                            <div class="portlet-title">
                                        <span class="pull-left">
                                            <h4 style="font-size: 21px; margin-top: 4px;"><strong >Customer Signature</strong> <small>so we know you verify above information.</small></h4>
                                        </span>
                                            </div>
                                            <div class="portlet-body" style="border: 1px solid #b83a3e">
                                                <div id="signature"></div>
                                                <input type="hidden" id="hiddenSigData" name="cus_sig" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="btn-group-justified">
                                    <div class="btn-group"><button type="button" class="btn btn-block blue pull-right" id="save_sig" onclick="$('#finisher').show();">Save</button></div>
                                    <div class="btn-group"><button type="button" class="btn btn-block red pull-right " id="clear" style="margin-left: 10px; display: none;" onclick="$('#signature').jSignature('clear');$('#save_sig').html('Save');$('#finisher').hide();">Clear</button></div>
                                </div>
                                <hr/>
                                <button type="button" class="btn red button-submit btn-block" id="finisher" style="height: 130px; display: none;" >
                                    Confirm estimate <i class="m-icon-swapright m-icon-white"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            $(document).ready(function() {
                document.title = "Viewing an estimate.";

                $('#finisher').on('click', function () {
                    $(this).html("<i class='fa fa-spinner fa-spin'></i>");
                    $.ajax({
                        url: '../app/update_settings.php?update=estimate&ev=<?php echo $event['event_token']; ?>&est=<?php echo $estimate['estimate_token']; ?>&s=1&cuid=<?php echo $event['event_company_token']; ?>&no_txt=true',
                        type: 'POST',
                        data: $('#estimate_it').serialize(),
                        success: function(d) {
                            $('#page-content').html("<br/> <br/> <br/> <div class=\"row\" style=\"margin-bottom: 50px;\">\n" +
                                "                                                                    <div class=\"col-md-12 page-404\">\n" +
                                "                                                                        <div class=\"number font-green\" style=\"top: 12px !important;\">\n" +
                                "                                                                            <i style=\"font-size: 100px;\" class=\"icon-check\"></i>\n" +
                                "                                                                        </div>\n" +
                                "                                                                        <div class=\"details\">\n" +
                                "                                                                            <h3>Thanks for confirming!</h3>\n" +
                                "                                                                            <p>\n" +
                                "                                                                                Now just wait for your estimator to get back in touch with you!<br/>\n" +
                                "                                                                                <strong>Your estimator</strong>: <?php echo name($estimate['estimate_estimator']); ?>\n"+
                                "                                                                            </p>\n" +
                                "                                                                        </div>\n" +
                                "                                                                    </div>\n" +
                                "                                                                </div><br/>")
                        },
                        error: function() {
                            toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                        }
                    });
                }).hide();

                $.ajax({
                    url: '../app/api/estimate.php?type=inv&luid=<?php echo $estimate['estimate_location_token']; ?>',
                    type: 'POST',
                    data: {
                        estimate: '<?php echo $estimate['estimate_token']; ?>'
                    },
                    success: function(m){
                        var owe = JSON.parse(m);
                        $(document).find('#owe_sub_total').html(parseFloat(owe.sub_total).toFixed(2));
                        $(document).find('#owe_tax').html(parseFloat(owe.tax).toFixed(2));
                        $(document).find('#owe_total').html(parseFloat(owe.total).toFixed(2));
                        $(document).find('#TOTAL').html(parseFloat(owe.total).toFixed(2));
                        $(document).find('#owe_total_unpaid').html(parseFloat(owe.unpaid).toFixed(2));
                    },
                    error: function(e){

                    }
                });

                $('input[type="checkbox"]').iCheck({
                    checkboxClass: 'icheckbox_minimal',
                    radioClass: 'iradio_minimal'
                });

                $("#signature").jSignature();
                $("#es-signature").jSignature();

                <?php
                if(!empty($estimate['estimate_es_sig'])){
                ?>
                $("#es-signature").jSignature("importData", 'data:'+"<?php echo $estimate['estimate_es_sig']; ?>");
                <?php
                }
                if(!empty($estimate['estimate_cus_sig'])){
                ?>
                $("#signature").jSignature("importData", 'data:'+"<?php echo $estimate['estimate_cus_sig']; ?>");
                <?php
                }
                ?>

                $('#save_sig').click(function(){
                    var sigData = $('#signature').jSignature('getData','base30');
                    $('#hiddenSigData').val(sigData);

                    $('#save_sig').html("Saved!");
                    $('#clear').show();
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
                            /* var summaryPanel = document.getElementById('results-map-panel');
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
                            summaryPanel.innerHTML += '<strong>Total milage</strong>: ' + total.toFixed(2); + ' mi';*/
                        } else {
                            toastr.error("<strong>Logan says:</strong><br/>There is not enough information to route the trip. Please add at least 1 pickup and destination location.")
                        }
                    });
                }

                initMap();
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
                $('.scroller2').slimScroll({
                    height: 225
                }); $('.scrollz').slimScroll({
                    height: 475
                });
            });

        </script>
        <?php
    }
}
