<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/3/2017
 * Time: 3:52 AM
 */
session_start();
include '../../app/init.php';

if(isset($_SESSION['logged'])){
    $profile = mysql_fetch_array(mysql_query("SELECT user_fname, user_lname, user_email, user_phone, user_company_name, user_website, user_pic, user_token, user_repeatclient, user_repeatclient_terms, user_repeatclient_notes FROM fmo_users WHERE user_token='".mysql_real_escape_string($_GET['uuid'])."'"));
    $exp      = mysql_fetch_array(mysql_query("SELECT user_license_exp FROM fmo_users WHERE user_company_token='".mysql_real_escape_string($_SESSION['cuid'])."'"));

    if(date('Y-m-d G:i:s') <= $exp['user_license_exp']){
        $expired = false;
    } else {
        $expired = true;
    }

    if($_GET['uuid'] == $profile['user_token']) {
        $editable = true;
        $view     = 'editOnly';
    } else {$editable = false;$view='infoOnly';}

    if(isset($_GET['conf'])){
        $event    = mysql_fetch_array(mysql_query("SELECT event_token, event_location_token, event_company_token, event_user_token, event_date_start, event_date_end, event_time, event_zip, event_name, event_email, event_phone, event_type, event_subtype, event_truckfee, event_laborrate, event_countyfee, event_additions, event_comments, event_booking FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($_GET['conf'])."'"));
        $location = mysql_fetch_array(mysql_query("SELECT location_booking_fee_disclaimer, location_disclaimers, location_max_trucks, location_max_men, location_max_counties FROM fmo_locations WHERE location_token='".$event['event_location_token']."'"));
    } elseif(isset($_GET['n']) && $_GET['n'] == 'nekotwen'){
        $new_token      = struuid(true);
        $new_location   = $_GET['luid'];
        $new_user_token = $profile['user_token'];
        $new_start      = date('Y-m-d');
        $new_end        = date('Y-m-d');
        $new_email      = strtolower($profile['user_email']);
        $new_phone      = $profile['user_phone'];
        $new_status     = 0;
        $new_by_user    = $_SESSION['uuid'];
        if($_SESSION['group'] != 3){
            $new_name       = $profile['user_fname']." ".$profile['user_lname']."'s move";
        } else {
            $new_name       = NULL;
        }
        $days  = array(0 => "sunday", 1 => "monday", 2 => "tuesday", 3 => "wednesday", 4 => "thursday", 5 => "friday", 6 => "saturday");
        $col   = "fmo_locations_rates_".$days[date('w', strtotime($new_start))];
        $tok   = $days[date('w', strtotime($new_start))]."_location_token";
        $find_fees = mysql_query("SELECT ".mysql_real_escape_string($days[date('w', strtotime($new_start))])."_truck_fee, ".mysql_real_escape_string($days[date('w', strtotime($new_start))])."_labor_rate, ".mysql_real_escape_string($days[date('w', strtotime($new_start))])."_truck_rate, ".mysql_real_escape_string($days[date('w', strtotime($new_start))])."_upcharge FROM fmo_locations_rates_".mysql_real_escape_string($days[date('w', strtotime($new_start))])." WHERE ".mysql_real_escape_string($days[date('w', strtotime($new_start))])."_location_token='".mysql_real_escape_string($new_location)."'");
        if(mysql_num_rows($find_fees) > 0){
            $fees = mysql_fetch_array($find_fees);
            $truckfee_rate = $fees[$days[date('w', strtotime($new_start))]."_truck_fee"];
            $laborrate_rate = $fees[$days[date('w', strtotime($new_start))]."_labor_rate"];
            $truckrate_rate = $fees[$days[date('w', strtotime($new_start))]."_truck_rate"];
            $weekend_upcharge = $fees[$days[date('w', strtotime($new_start))]."_upcharge"];
        }
        mysql_query("INSERT INTO fmo_locations_events (event_token, event_by_user_token, event_location_token, event_user_token, event_company_token, event_date_start, event_date_end, event_name, event_email, event_phone, event_truckfee_rate, event_laborrate_rate, event_truckrate_rate, event_weekend_upcharge_rate, event_status) VALUES (
        '".mysql_real_escape_string($new_token)."',
        '".mysql_real_escape_string($new_by_user)."',
        '".mysql_real_escape_string($new_location)."',
        '".mysql_real_escape_string($new_user_token)."',
        '".mysql_real_escape_string($_SESSION['cuid'])."',
        '".mysql_real_escape_string($new_start)."',
        '".mysql_real_escape_string($new_end)."',
        '".mysql_real_escape_string($new_name)."',
        '".mysql_real_escape_string($new_email)."',
        '".mysql_real_escape_string($new_phone)."',
        '".mysql_real_escape_string($truckfee_rate)."',
        '".mysql_real_escape_string($laborrate_rate)."',
        '".mysql_real_escape_string($truckrate_rate)."',
        '".mysql_real_escape_string($weekend_upcharge)."',
        '".mysql_real_escape_string($new_status)."')") or die(mysql_error());
        $math['truck_fee']        = $truckfee_rate * 1;
        if($weekend_upcharge > 0){
            $math['total_labor_rate'] = ($laborrate_rate * 2) + ($truckrate_rate * 1) + $weekend_upcharge;
        } else {
            $math['total_labor_rate'] = ($laborrate_rate * 2) + ($truckrate_rate * 1);
        }

        timeline_event($new_token, $_SESSION['uuid'], "Creation", name($_SESSION['uuid'])." created this event, estimated to need <strong>1</strong> truck(s) (for <strong>$".number_format($math['truck_fee'], 2)."</strong>), and <strong>2</strong> crewmen (for <strong>$".number_format($math['total_labor_rate'], 2)."/hr</strong>) on <strong>".date('m-d-Y', strtotime($new_start))."</strong> through <strong>".date('m-d-Y', strtotime($new_end))."</strong> in <strong>".locationName2($loc)."</strong>");

        $event    = mysql_fetch_array(mysql_query("SELECT event_token, event_location_token, event_company_token, event_user_token, event_date_start, event_date_end, event_time, event_name, event_email, event_phone, event_type, event_subtype, event_truckfee, event_laborrate, event_countyfee, event_additions, event_comments, event_booking FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($new_token)."'"));
        $location = mysql_fetch_array(mysql_query("SELECT location_booking_fee_disclaimer, location_disclaimers, location_max_trucks, location_max_men, location_max_counties FROM fmo_locations WHERE location_token='".$event['event_location_token']."'"));
    }
    ?>
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light">
                <div class="portlet-title tabbable-line">
                    <div class="caption caption-md">
                        <i class="icon-notebook theme-font"></i>
                        <span class="caption-subject font-red bold uppercase">Create Event</span>
                    </div>
                    <ul class="nav nav-tabs">
                         <li class="active">
                            <a href="#tab_1_1" data-toggle="tab">Create Event</a>
                        </li>
                    </ul>
                </div>
                <div class="portlet-body">
                    <div class="tab-content">
                        <!-- PERSONAL INFO TAB -->
                        <div class="tab-pane active" id="tab_1_1">
                            <div class="portlet" id="form_wizard_1">
                                <div class="portlet-body form">
                                    <div class="form-wizard">
                                        <div class="form-body">
                                            <ul class="nav nav-pills nav-justified steps hidden">
                                                <li>
                                                    <a href="#tab1" data-toggle="tab" class="step">
                                                        <span class="number"> 1 </span>
                                                        <span class="desc"><i class="fa fa-check"></i> Event Information </span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#tab3" data-toggle="tab" class="step">
                                                        <span class="number">3 </span>
                                                        <span class="desc"><i class="fa fa-check"></i> Event Locations </span>
                                                    </a>
                                                </li>
                                                <?php
                                                if($profile['user_repeatclient'] != 1){
                                                    ?>
                                                    <li>
                                                        <a href="#tab4" data-toggle="tab" class="step">
                                                            <span class="number">4 </span>
                                                            <span class="desc"><i class="fa fa-check"></i> Finalization </span>
                                                        </a>
                                                    </li>
                                                    <?php
                                                }
                                                ?>
                                            </ul>
                                            <div id="bar" class="progress progress-striped" role="progressbar">
                                                <div class="progress-bar progress-bar-success">
                                                </div>
                                            </div>
                                            <div class="tab-content">
                                                <div class="alert alert-danger display-none">
                                                    <button class="close" data-dismiss="alert"></button>
                                                    You have some form errors. Please check below.
                                                </div>
                                                <div class="tab-pane" id="tab1">
                                                    <form action="#" class="form-horizontal" id="submit_form" method="POST" editable-form name="textBtnForm">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <h3 style="margin-top: 0;">Event Information</h3><small>Below, we will need basic information about the event. Once you've completed this section, you will be able to move onto the next. All fields marked with a <strong>red star</strong> ( <span class="text-danger">*</span> ) are <strong>required</strong>!</small>
                                                                <hr/>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6 col-sm-12">
                                                                <div class="form-group">
                                                                    <label class="control-label col-md-3">Event Name <span class="required">*</span></label>
                                                                    <div class="col-md-9">
                                                                        <input type="text" class="form-control" name="name" value="<?php echo $event['event_name']; ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="control-label col-md-3">Start/end dates <span class="required">*</span></label>
                                                                    <div class="col-md-9">
                                                                        <div class="input-group input-md date-picker input-daterange" data-date="10/11/2012" data-date-format="mm/dd/yyyy" style="width: 100% !important;">
                                                                            <input type="text" class="form-control" name="startdate" value="<?php echo date("m/d/Y", strtotime($event['event_date_start'])); ?>">
                                                                            <span class="input-group-addon"> to </span>
                                                                            <input type="text" class="form-control" name="enddate" value="<?php echo date("m/d/Y", strtotime($event['event_date_end'])); ?>">
                                                                        </div>
                                                                        <strong class="help-block"><i class="fa fa-arrow-up faa-vertical animated"></i> <span class="font-xs">STARTING DATE</span> <span class="pull-right font-xs">ENDING DATE <i class="fa fa-arrow-up faa-vertical animated"></i></span></strong>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="control-label col-md-3">Time of move <span class="required">*</span></label>
                                                                    <div class="col-md-9">
                                                                        <select class="form-control" name="time" id="time_select">
                                                                            <option disabled selected value="">Select one..</option>
                                                                            <?php
                                                                            $timeOptions = mysql_query("SELECT time_start, time_end FROM fmo_locations_times WHERE time_location_token='".mysql_real_escape_string($event['event_location_token'])."'");
                                                                            if(mysql_num_rows($timeOptions) > 0){
                                                                                while($t = mysql_fetch_assoc($timeOptions)){
                                                                                    ?>
                                                                                    <option <?php if($t['time_start']." to ".$t['time_end'] == $event['event_time']){echo "selected";} ?> value="<?php echo $t['time_start']; ?> to <?php echo $t['time_end']; ?>"><?php echo $t['time_start']; ?> - <?php echo $t['time_end']; ?></option>
                                                                                    <?php
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                        <strong class="help-block">Please select the event's start time.</strong>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="control-label col-md-3">Type <span class="required">*</span></label>
                                                                    <div class="col-md-9">
                                                                        <select class="form-control" name="type">
                                                                            <?php
                                                                            $types = mysql_query("SELECT eventtype_name FROM fmo_locations_eventtypes WHERE eventtype_location_token='".mysql_real_escape_string($event['event_location_token'])."'");
                                                                            if(mysql_num_rows($types) > 0){
                                                                                while($type = mysql_fetch_assoc($types)){
                                                                                    ?>
                                                                                    <option <?php if($type['eventtype_name'] == $event['event_type']){echo "selected";} ?> value="<?php echo $type['eventtype_name']; ?>"><?php echo $type['eventtype_name']; ?></option>
                                                                                    <?php
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="control-label col-md-3">Sub-Type <span class="required">*</span></label>
                                                                    <div class="col-md-9">
                                                                        <select class="form-control" name="subtype">
                                                                            <?php
                                                                            $subtypes = mysql_query("SELECT subtype_id, subtype_name FROM fmo_locations_subtypes WHERE subtype_location_token='".mysql_real_escape_string($event['event_location_token'])."'");
                                                                            if(mysql_num_rows($types) > 0){
                                                                                while($subtype = mysql_fetch_assoc($subtypes)){
                                                                                    ?>
                                                                                    <option <?php if($subtype['subtype_name'] == $event['event_subtype']){echo "selected";} ?> value="<?php echo $subtype['subtype_name']; ?>"><?php echo $subtype['subtype_name']; ?></option>
                                                                                    <?php
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="control-label col-md-3">Contact Email <span class="required">*</span></label>
                                                                    <div class="col-md-9">
                                                                        <input type="text" class="form-control" name="email" value="<?php echo $event['event_email']; ?>"/>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="control-label col-md-3">Contact Phone <span class="required">*</span></label>
                                                                    <div class="col-md-9">
                                                                        <input type="text" class="form-control" id="mask_phone" name="phone" value="<?php echo $event['event_phone']; ?>"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-sm-12">
                                                                <div class="form-group">
                                                                    <div class="col-md-12 text-left btn-group-justified">
                                                                        <div class="btn-group">
                                                                            <a class="btn default red-stripe dropdown-toggle hidden-sm" href="javascript:;" data-toggle="dropdown">
                                                                                <i class="fa fa-truck"></i> Trucks: <strong id="truckfee" class="event_truckfee_out edits" data-a="#truckfee" data-b="#laborrate" data-c="#countyfee"><?php echo $event['event_truckfee']; ?></strong> for $<span id="TF"></span> <i class="fa fa-angle-down"></i>
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
                                                                            <a class="btn default red-stripe dropdown-toggle hidden-sm" href="javascript:;" data-toggle="dropdown">
                                                                                <i class="fa fa-users"></i> Crewmen: <strong id="laborrate" class="event_laborrate_out edits" data-a="#truckfee" data-b="#laborrate" data-c="#countyfee"><?php echo $event['event_laborrate']; ?></strong> for $<span id="LR"></span> <i class="fa fa-angle-down"></i>
                                                                            </a>
                                                                            <ul class="dropdown-menu pull-right">
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
                                                                            <a class="btn default red-stripe dropdown-toggle hidden-sm" href="javascript:;" data-toggle="dropdown">
                                                                                <i class="fa fa-location-arrow"></i> Counties: <strong id="countyfee" class="event_countyfee_out edits" data-a="#truckfee" data-b="#laborrate" data-c="#countyfee"><?php echo $event['event_countyfee']; ?></strong> for $<span id="CF"></span> <i class="fa fa-angle-down"></i>
                                                                            </a>
                                                                            <ul class="dropdown-menu pull-right">
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
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <div class="col-md-12">
                                                                        <textarea class="form-control bol_comments" style="height: 173px;" placeholder="BOL comments"><?php echo $event['event_comments']; ?></textarea>
                                                                        <?php
                                                                        if($profile['user_repeatclient'] == 1){
                                                                            ?>
                                                                            <p style="margin-top: 20px;">
                                                                                <span class="text-danger">*</span> <strong>YOU ARE A REPEAT CLIENT, SPECIAL PRICING APPLIES</strong><br/>
                                                                                <span class="text-info">*</span> PAYMENT TERMS: <strong class="text-info"><?php echo $profile['user_repeatclient_terms']; ?></strong><br/>
                                                                                <span class="text-info">*</span> AGREED TERMS: <strong class="text-info"><?php echo $profile['user_repeatclient_notes']; ?></strong><br/>
                                                                            </p>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </div>
                                                                </div>
                                                                <hr/>
                                                                <div class="form-group">
                                                                    <div class="col-md-12 btn-group-justified">
                                                                        <?php
                                                                        $additions = explode("|", $event['event_additions']);
                                                                        $add       = 0;
                                                                        foreach($additions as $ck){
                                                                            $add++;
                                                                            $extra[$ck] = $ck;
                                                                        }
                                                                        ?>
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
                                                    </form>
                                                </div>
                                                <div class="tab-pane" id="tab3">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <h3 style="margin-top: 0;">Location Information</h3><small>Now, you have the option to add events to this location for the customer. <br/> <strong>Note</strong>: <i>you don't have to add locations right now, but it is recommended.</i></small>
                                                            <hr/>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6 text-center">
                                                            <div id="gmap_basic" class="gmaps" style="height: 450px;">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
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
                                                                                                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#pickup_h_<?php echo $pk; ?>" href="#pickup_<?php echo $pk; ?>" aria-expanded="false"><strong><?php echo $pickup['address_address']; ?>, <?php echo $pickup['address_city']; ?>, <?php echo $pickup['address_state']; ?> <?php echo $pickup['address_zip']; ?>, Suite: <?php echo $pickup['address_suite']; ?></strong></a>
                                                                                            </h4>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div id="pickup_<?php echo $pk; ?>" class="panel-collapse collapse" aria-expanded="true" style="height: 0px;">
                                                                                        <div class="panel-body">
                                                                                            <address>
                                                                                                <strong>Physical Address</strong><br>
                                                                                                <a class="pu_<?php echo $pickup['address_id']; ?>" data-reload="" style="color:#333333" data-name="address_address" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new street address.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                                                    <?php echo $pickup['address_address']; ?>
                                                                                                </a><br/>
                                                                                                <a class="pu_<?php echo $pickup['address_id']; ?>" data-reload="" style="color:#333333" data-name="address_city" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new city.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                                                    <?php echo $pickup['address_city']; ?>
                                                                                                </a>,
                                                                                                <a class="pu_<?php echo $pickup['address_id']; ?>" data-reload="" style="color:#333333" data-name="address_state" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Select new state.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                                                    <?php echo $pickup['address_state']; ?>
                                                                                                </a>
                                                                                                <a class="pu_<?php echo $pickup['address_id']; ?>" data-reload="" style="color:#333333" data-name="address_zip" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new zipcode.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                                                    <?php echo $pickup['address_zip']; ?>
                                                                                                </a><br/>
                                                                                                <a class="pu_<?php echo $pickup['address_id']; ?>" data-reload="" style="color:#333333" data-name="address_county" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new county.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                                                    <?php echo $pickup['address_county']; ?>
                                                                                                </a>
                                                                                            </address>
                                                                                            <address>
                                                                                                Closest intersection:
                                                                                                <strong>
                                                                                                    <a class="pu_<?php echo $pickup['address_id']; ?>" data-reload=""  style="color:#333333" data-name="address_closest_intersection" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new closest intersection.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                                                        <?php echo $pickup['address_closest_intersection']; ?>
                                                                                                    </a><br/>
                                                                                                </strong>

                                                                                                Stairs:
                                                                                                <strong>
                                                                                                    <a class="pu_<?php echo $pickup['address_id']; ?>" data-reload="" style="color:#333333" data-name="address_stairs" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new closest intersection.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                                                        <?php echo $pickup['address_stairs']; ?>
                                                                                                    </a><br/>
                                                                                                </strong>

                                                                                                Parking Distance:
                                                                                                <strong>
                                                                                                    <a class="pu_<?php echo $pickup['address_id']; ?>" data-reload="" style="color:#333333" data-name="address_distance" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new distance.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                                                        <?php echo $pickup['address_distance']; ?>
                                                                                                    </a><br/>
                                                                                                </strong>

                                                                                                Bedrooms:
                                                                                                <strong>
                                                                                                    <a class="pu_<?php echo $pickup['address_id']; ?>" data-reload="" style="color:#333333" data-name="address_bedrooms" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new distance.." data-url="assets/app/update_settings.php?update=event_addy">
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
                                                                                                    <a class="pu_<?php echo $pickup['address_id']; ?>" data-reload="" style="color:#333333" data-name="address_special" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new distance.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                                                        <?php echo $pickup['address_special']; ?>
                                                                                                    </a><br/>
                                                                                                </strong>

                                                                                                Square Footage:
                                                                                                <strong>
                                                                                                    <a class="pu_<?php echo $pickup['address_id']; ?>" data-reload="" style="color:#333333" data-name="address_square_footage" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new distance.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                                                        <?php echo $pickup['address_square_footage']; ?>
                                                                                                    </a><br/>
                                                                                                </strong>
                                                                                            </address>
                                                                                            <address>
                                                                                                Comments: <br/>
                                                                                                <strong>
                                                                                                    <a class="pu_<?php echo $pickup['address_id']; ?>" data-reload="" style="color:#333333" data-name="address_comments" data-pk="<?php echo $pickup['address_id']; ?>" data-type="textarea" data-placement="right" data-title="Enter new comments.." data-url="assets/app/update_settings.php?update=event_addy">
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
                                                                            <span class="hidden-480">Add destination</span>
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
                                                                                                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#dest_h_<?php echo $pk; ?>" href="#dest_<?php echo $pk; ?>" aria-expanded="false"><strong><?php echo $dest['address_address']; ?>, <?php echo $dest['address_city']; ?>, <?php echo $dest['address_state']; ?> <?php echo $dest['address_zip']; ?>, Suite: <?php echo $dest['address_suite']; ?></strong></a>
                                                                                            </h4>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div id="dest_<?php echo $pk; ?>" class="panel-collapse collapse" aria-expanded="true" style="height: 0px;">
                                                                                        <div class="panel-body">
                                                                                            <address>
                                                                                                <strong>Physical Address</strong><br>
                                                                                                <a class="ds_<?php echo $dest['address_id']; ?>" data-reload="" style="color:#333333" data-name="address_address" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new street address.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                                                    <?php echo $dest['address_address']; ?>
                                                                                                </a><br/>
                                                                                                <a class="ds_<?php echo $dest['address_id']; ?>" data-reload="" style="color:#333333" data-name="address_city" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new city.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                                                    <?php echo $dest['address_city']; ?>
                                                                                                </a>,
                                                                                                <a class="ds_<?php echo $dest['address_id']; ?>" data-reload="" style="color:#333333" data-name="address_state" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Select new state.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                                                    <?php echo $dest['address_state']; ?>
                                                                                                </a>
                                                                                                <a class="ds_<?php echo $dest['address_id']; ?>" data-reload="" style="color:#333333" data-name="address_zip" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new zipcode.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                                                    <?php echo $dest['address_zip']; ?>
                                                                                                </a><br/>
                                                                                                <a class="ds_<?php echo $dest['address_id']; ?>" data-reload="" style="color:#333333" data-name="address_county" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new county.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                                                    <?php echo $dest['address_county']; ?>
                                                                                                </a>
                                                                                            </address>
                                                                                            <address>
                                                                                                Closest intersection:
                                                                                                <strong>
                                                                                                    <a class="ds_<?php echo $dest['address_id']; ?>" data-reload="" style="color:#333333" data-name="address_closest_intersection" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new closest intersection.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                                                        <?php echo $dest['address_closest_intersection']; ?>
                                                                                                    </a><br/>
                                                                                                </strong>

                                                                                                Stairs:
                                                                                                <strong>
                                                                                                    <a class="ds_<?php echo $dest['address_id']; ?>" data-reload="" style="color:#333333" data-name="address_stairs" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new closest intersection.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                                                        <?php echo $dest['address_stairs']; ?>
                                                                                                    </a><br/>
                                                                                                </strong>

                                                                                                Parking Distance:
                                                                                                <strong>
                                                                                                    <a class="ds_<?php echo $dest['address_id']; ?>" data-reload="" style="color:#333333" data-name="address_distance" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new distance.." data-url="assets/app/update_settings.php?update=event_addy">
                                                                                                        <?php echo $dest['address_distance']; ?>
                                                                                                    </a><br/>
                                                                                                </strong>
                                                                                            </address>
                                                                                            <address>
                                                                                                Comments: <br/>
                                                                                                <strong>
                                                                                                    <a class="ds_<?php echo $dest['address_id']; ?>" data-reload="" style="color:#333333" data-name="address_comments" data-pk="<?php echo $dest['address_id']; ?>" data-type="textarea" data-placement="right" data-title="Enter new comments.." data-url="assets/app/update_settings.php?update=event_addy">
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
                                                <?php
                                                if($profile['user_repeatclient'] != 1){
                                                    ?>
                                                    <div class="tab-pane" id="tab4">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <h3 style="margin-top: 0;">Finalization</h3>
                                                                <?php
                                                                if($profile['user_repeatclient'] != 1){
                                                                    ?>
                                                                    <small>
                                                                        Finally, we need to benefit our security and ask the customer to give us validation that they have good form of payment.
                                                                        <br/> If we <strong class="text-danger">DO NOT</strong> collect a booking fee, the customer/client will be <strong>required</strong> to use <strong>CASH</strong> as their form of payment.
                                                                        <br/> If we <strong class="text-success">DO</strong> collect a booking fee, the customer/client will be <strong>free to choose</strong> their form of payment.
                                                                    </small>
                                                                    <hr/>
                                                                    <?php
                                                                } else {
                                                                    ?>
                                                                    <small>
                                                                        <span class="text-danger">*</span> By submitting this work order to our live scheduling system, you agree that you are authorized to act on behalf of your company and to be bound by our terms of service and any agreed rate structure listed above. See your local office manager if you have any questions.
                                                                    </small>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                        <?php
                                                        if($profile['user_repeatclient'] != 1){
                                                            if($event['event_booking'] != 1){
                                                                ?>
                                                                <div>
                                                                    <div class="row">
                                                                        <div class="col-md-12" style="font-size: 17px!important;">
                                                                            <?php
                                                                            echo $location['location_booking_fee_disclaimer'];
                                                                            ?>
                                                                            <br/>
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
                                                                        </div>
                                                                    </div>
                                                                    <div class="row" id="booking_fee_success_maybe">
                                                                        <div class="col-md-12 text-center">
                                                                            <div class="well">
                                                                                <button class="btn btn-block btn-xl red" data-toggle="modal" href="#booking_fee_modal">Securely pay <strong>$10.00</strong> booking fee</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-12" style="font-size: 17px!important;">
                                                                            <?php
                                                                            echo $location['location_disclaimers'];
                                                                            ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <?php
                                                            } else {
                                                                ?>
                                                                <div class="row" style="margin-bottom: 50px;">
                                                                    <div class="col-md-12 page-404">
                                                                        <div class="number font-green" style="top: 20px !important;">
                                                                            <i style="font-size: 100px;" class="icon-check"></i>
                                                                        </div>
                                                                        <div class="details">
                                                                            <h3>Booking fee paid</h3>
                                                                            <p>
                                                                                Customer can pay bill however they choose to.<br>
                                                                                <strong>stripe token? who authorized? idk</strong>.
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <?php
                                                                        echo $location['location_disclaimers'];
                                                                        ?>
                                                                    </div>
                                                                </div>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                    <?php
                                                }
                                                ?>

                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <button href="javascript:;" class="btn default button-previous pull-left">
                                                        <i class="m-icon-swapleft"></i> Back </button>
                                                    <button href="javascript:;" class="btn yellow button-save-for-later pull-left" type="submit" name="status" value="0">
                                                        Save for later <i class="fa fa-download"></i>
                                                    </button>
                                                    <button href="javascript:;" class="btn blue button-next pull-right">
                                                        Continue <i class="m-icon-swapright m-icon-white"></i>
                                                    </button>
                                                    <?php
                                                    if($expired == false){
                                                        ?>
                                                        <button href="javascript:;" class="btn green button-submit pull-right" type="submit" name="status" value="1">
                                                            Submit <i class="m-icon-swapright m-icon-white"></i>
                                                        </button>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <button href="javascript:;" class="btn default red-stripe button-submit pull-right" type="submit" name="status" value="0" style="display: none!important;">
                                                            Your software license is expired. Please take a booking fee, or renew your license to continue with this booking.
                                                        </button>
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
                        <!-- END PERSONAL INFO TAB -->
                        <!-- CHANGE AVATAR TAB -->
                        <div class="tab-pane" id="tab_1_2">

                        </div>
                        <!-- END CHANGE AVATAR TAB -->
                        <!-- CHANGE PASSWORD TAB -->
                        <div class="tab-pane" id="tab_1_3">

                        </div>
                        <!-- END CHANGE PASSWORD TAB -->
                        <!-- PRIVACY SETTINGS TAB -->
                        <div class="tab-pane" id="tab_1_4">

                        </div>
                        <!-- END PRIVACY SETTINGS TAB -->
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
                            <h4 class="text-danger"> Booking Fees (sometimes referred to and treated as deposits)</h4>
                            <p>
                                By utilizing ForMoversOnly.com, you agree to our terms regarding booking fees charged to your customers.  While it is NOT required that your customer provide a booking fee through our software, any booking fee paid is the sole income of ForMoversOnly.com and NON-REFUNDABLE for any reason. Booking fees are $10 as of Feb. 1st, 2015. We are the sole decision maker to set the pricing for these fees and may change the amount charged at any time.  Booking fees are not actually deposits and we recommend that you do not apply this fee as a credit to your customer’s event payments.  We have no obligation to provide you with any card processing details after the fact.  Booking fees do have great benefits to you.
                            </p>
                            <br/><br/>
                            <form id="booking_fee_form">
                                <div class="form-inline margin-bottom-25 text-center">
                                    <div class="form-group form-md-line-input">
                                        <div class="input-icon">
                                            <input type="text" size="20" data-stripe="name" class="form-control input-sm card_name" value="<?php echo name($profile['user_token']); ?>">
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
                                <input type="text" name="notes" id="booking_notes" class="hidden"/>
                                <button id="booking_fee" class="btn btn-block red" type="button"><span class="error-handler">Pay $10.00 booking fee</span> <i class="fa fa-credit-card"></i></button>
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    }
    ?>
    <script>
        $(document).ready(function(){

            $(function() {
                // IMPORTANT: Fill in your client key
                var clientKey = "js-InlLzUGLaGPQYhaSPQrQGnDmZH0HPvLyT6ks10ebG31Ekcxa3Y0KmE6ml73bDOJw";

                var cache = {};
                var container = $("#new_location");

                /** Handle successful response */
                function handleResp(data) {
                    // Check for error
                    if (data.error_msg) toastr.error("<strong>Ckai says:</strong><br/>"+data.error_msg);
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
                                    if (json.error_msg) toastr.error("<strong>Ckai says:</strong><br/>"+json.error_msg);
                                } else toastr.error("<strong>Ckai says:</strong><br/>Unknown error. You really f**ked up!");
                            });
                        }
                    }
                });
            });

            function initMap() {
                var directionsService = new google.maps.DirectionsService;
                var directionsDisplay = new google.maps.DirectionsRenderer;
                var map = new google.maps.Map(document.getElementById('gmap_basic'), {
                    zoom: 5,
                    center: {lat: 40.2672, lng: 86.1349}
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
                        summaryPanel.innerHTML += '<strong>Total milage</strong>: ' + total + ' mi';
                    } else {
                        toastr.error("<strong>Logan says:</strong><br/>There is not enough information to route the trip. Please add at least 1 pickup and destination location.")
                    }
                });
            }

            initMap();

            $("#mask_phone").inputmask("mask", {
                "mask": "(999) 999-9999"
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

            var date = $('.date-picker').datepicker({
                rtl: Metronic.isRTL(),
                orientation: "left",
                autoclose: true
            });


            if (!jQuery().bootstrapWizard) {
                return;
            }


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
                        url: 'assets/app/add_event.php?ev=pmk&e=<?php echo $event['event_token']; ?>',
                        type: "POST",
                        data: $('#new_location').serialize(),
                        success: function(data) {
                            $('#draggable').modal('hide');
                            $('#new_location')[0].reset();
                            toastr.success("<strong>Logan says</strong>:<br/>That location has been added to this events record. Let me refresh the event for you, so you can see the changes.");
                            $.ajax({
                                url: 'assets/app/update_settings.php?update=event&e=<?php echo $event['event_token']; ?>&s=0&no_txt=true',
                                type: 'POST',
                                data: $('#submit_form').serialize(),
                                success: function(d) {
                                    $.ajax({
                                        url: 'assets/pages/sub/profile_event_wizard.php?conf=<?php echo $event['event_token']; ?>&loc_added=true&uuid=<?php echo $_GET['uuid']; ?>',
                                        success: function(data) {
                                            $('#profile-content').html(data);
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
                        },
                        error: function() {
                            toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                        }
                    });
                }
            });



            var form = $('#submit_form');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);

            form.validate({
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    name: {
                        required: true
                    },
                    move_type: {
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
                    },
                    event_truckfee: {
                        required: true
                    },
                    event_laborrate: {
                        required: true
                    },
                    event_countyfee: {
                        required: true
                    }
                },


                invalidHandler: function (event, validator) { //display error alert on form submit
                    success.hide();
                    error.show();
                    Metronic.scrollTo(error, -200);
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
                },

                submitHandler: function (form) {
                    success.show();
                    error.hide();
                    //add here some ajax code to submit your form or just call form.submit() if you want to submit the form without ajax
                }

            });

            $("#submit_form").validate().element("#time_select");

            var displayConfirm = function() {
                $('#tab4 .form-control-static', form).each(function(){
                    var input = $('[name="'+$(this).attr("data-display")+'"]', form);
                    if (input.is(":radio")) {
                        input = $('[name="'+$(this).attr("data-display")+'"]:checked', form);
                    }
                    if (input.is(":text") || input.is("textarea")) {
                        $(this).html(input.val());
                    } else if (input.is("select")) {
                        $(this).html(input.find('option:selected').text());
                    } else if (input.is(":radio") && input.is(":checked")) {
                        $(this).html(input.attr("data-title"));
                    } else if ($(this).attr("data-display") == 'payment[]') {
                        var payment = [];
                        $('[name="payment[]"]:checked', form).each(function(){
                            payment.push($(this).attr('data-title'));
                        });
                        $(this).html(payment.join("<br>"));
                    }
                });
            }

            var handleTitle = function(tab, navigation, index) {
                var total = navigation.find('li').length;
                var current = index + 1;
                // set wizard title
                $('.step-title', $('#form_wizard_1')).text('Step ' + (index + 1) + ' of ' + total);
                // set done steps
                jQuery('li', $('#form_wizard_1')).removeClass("done");
                var li_list = navigation.find('li');
                for (var i = 0; i < index; i++) {
                    jQuery(li_list[i]).addClass("done");
                }

                if (current == 1) {
                    $('#form_wizard_1').find('.button-previous').hide();
                } else {
                    $('#form_wizard_1').find('.button-previous').show();
                }

                if (current >= total) {
                    $('#form_wizard_1').find('.button-next').hide();
                    $('#form_wizard_1').find('.button-submit').show();
                    displayConfirm();
                } else {
                    $('#form_wizard_1').find('.button-next').show();
                    $('#form_wizard_1').find('.button-submit').hide();
                }
                Metronic.scrollTo($('.page-title'));
            }

            // default form wizard
            $('#form_wizard_1').bootstrapWizard({
                'nextSelector': '.button-next',
                'previousSelector': '.button-previous',
                onTabClick: function (tab, navigation, index, clickedIndex) {
                    return false;
                    /*
                     success.hide();
                     error.hide();
                     if (form.valid() == false) {
                     return false;
                     }
                     handleTitle(tab, navigation, clickedIndex);
                     */
                },
                onNext: function (tab, navigation, index) {
                    success.hide();
                    error.hide();

                    if (form.valid() == false) {
                        return false;
                    }

                    handleTitle(tab, navigation, index);
                },
                onPrevious: function (tab, navigation, index) {
                    success.hide();
                    error.hide();

                    handleTitle(tab, navigation, index);
                },
                onTabShow: function (tab, navigation, index) {
                    var total = navigation.find('li').length;
                    var current = index + 1;
                    var $percent = (current / total) * 100;
                    $('#form_wizard_1').find('.progress-bar').css({
                        width: $percent + '%'
                    });
                }
            });

            $('#form_wizard_1').find('.button-previous').hide();
            <?php
            if($expired == false){
                ?>
                $('#form_wizard_1 .button-submit').click(function () {
                    Pace.track(function(){
                        $.ajax({
                            url: 'assets/app/update_settings.php?update=event&e=<?php echo $event['event_token']; ?>&luid=<?php echo $event['event_location_token']; ?>&s=1&cuid=<?php echo $event['event_company_token']; ?>&STEALER=<?php echo $_SESSION['uuid']; ?>',
                            type: 'POST',
                            data: $('#submit_form').serialize(),
                            success: function(d) {
                                <?php
                                if($_SESSION['group'] == 3){
                                ?>
                                $.ajax({
                                    url: 'assets/pages/dashboard.php?luid=<?php echo $event['event_location_token']; ?>',
                                    success: function(vat) {
                                        $('#page_content').html(vat);
                                    },
                                    error: function() {
                                        toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                                    }
                                });
                                <?php
                                } else {
                                ?>
                                $.ajax({
                                    url: 'assets/pages/event.php?ev=<?php echo $event['event_token']; ?>&conf=true',
                                    success: function(vat) {
                                        $('#page_content').html(vat);
                                    },
                                    error: function() {
                                        toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                                    }
                                });
                                <?php
                                }
                                ?>

                            },
                            error: function() {
                                toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                            }
                        });
                    });
                }).hide();
                <?php
            }
            ?>
            $('#form_wizard_1 .button-save-for-later').click(function () {
                Pace.track(function(){
                    $.ajax({
                        url: 'assets/app/update_settings.php?update=event&e=<?php echo $event['event_token']; ?>&luid=<?php echo $event['event_location_token']; ?>&s=0&cuid=<?php echo $event['event_company_token']; ?>',
                        type: 'POST',
                        data: $('#submit_form').serialize(),
                        success: function(d) {
                            $.ajax({
                                url: 'assets/pages/profile.php?uuid=<?php echo $event['event_user_token']; ?>&luid=<?php echo $event['event_location_token']; ?>',
                                success: function(vat) {
                                    $('#page_content').html(vat);
                                    toastr.success("<strong>Ckai says</strong><br/>I saved all that information for later. It's been returned to a hot lead.");
                                },
                                error: function() {
                                    toastr.error("<strong>CkAI says</strong>:<br/>An unexpected error has occured. Please try again later.");
                                }
                            });
                        },
                        error: function() {
                            toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                        }
                    });
                });
            });

            <?php
            if(isset($_GET['loc_added'])){
                ?>
                $('.button-next').trigger('click');
                <?php
            } elseif(isset($_GET['booking_added'])) {
                ?>
                $('.button-next').trigger('click');
                $('.button-next').trigger('click');
                <?php
            }
            ?>

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
                                    url: 'assets/app/update_settings.php?update=event_fly&tok='+data+'&uuid=<?php echo $event['event_user_token']; ?>&era=pre',
                                    type: 'POST',
                                    data: {
                                        name: 'event_booking',
                                        value: 1,
                                        pk: '<?php echo $event['event_token']; ?>'
                                    },
                                    success: function(s){
                                        $.ajax({
                                            url: 'assets/app/update_settings.php?update=event&e=<?php echo $event['event_token']; ?>&s=0&cuid=<?php echo $event['event_company_token']; ?>&no_text=true',
                                            type: 'POST',
                                            data: $('#submit_form').serialize(),
                                            success: function(d) {
                                                $.ajax({
                                                    url: 'assets/pages/sub/profile_event_wizard.php?conf=<?php echo $event['event_token']; ?>&booking_added=true&uuid=<?php echo $_GET['uuid']; ?>',
                                                    success: function(data) {
                                                        $('#profile-content').html(data);
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
