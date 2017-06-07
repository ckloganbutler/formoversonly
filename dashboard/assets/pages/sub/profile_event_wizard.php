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
    if(isset($_GET['conf'])){
        $event = mysql_fetch_array(mysql_query("SELECT event_token, event_location_token, event_date_start, event_date_end, event_time, event_name, event_email, event_phone, event_type, event_subtype, event_truckfee, event_laborrate, event_countyfee, event_additions FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($_GET['conf'])."'"));
    }
    $profile = mysql_fetch_array(mysql_query("SELECT user_fname, user_lname, user_email, user_phone, user_company_name, user_website, user_pic, user_token FROM fmo_users WHERE user_token='".mysql_real_escape_string($_GET['uuid'])."'"));
    if($_GET['uuid'] == $profile['user_token']) {
        $editable = true;
        $view     = 'editOnly';
    } else {$editable = false;$view='infoOnly';}
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
                                    <form action="#" class="form-horizontal" id="submit_form" method="POST">
                                        <div class="form-wizard">
                                            <div class="form-body">
                                                <ul class="nav nav-pills nav-justified steps">
                                                    <li>
                                                        <a href="#tab1" data-toggle="tab" class="step">
                                                            <span class="number"> 1 </span>
                                                            <span class="desc"><i class="fa fa-check"></i> Customer Information </span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#tab2" data-toggle="tab" class="step">
                                                            <span class="number"> 2 </span>
                                                            <span class="desc"><i class="fa fa-check"></i> Services </span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#tab3" data-toggle="tab" class="step">
                                                            <span class="number">3 </span>
                                                            <span class="desc"><i class="fa fa-check"></i> Locations </span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#tab4" data-toggle="tab" class="step">
                                                            <span class="number">4 </span>
                                                            <span class="desc"><i class="fa fa-check"></i> Finalization </span>
                                                        </a>
                                                    </li>
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
                                                    <div class="tab-pane active" id="tab1">
                                                        <h3>Date & Time</h3> <br/>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="control-label col-md-3">Start/end dates <span class="required">*</span></label>
                                                                    <div class="col-md-9">
                                                                        <div class="input-group input-md date-picker input-daterange" data-date="10/11/2012" data-date-format="mm/dd/yyyy">
                                                                            <input type="text" class="form-control" name="startdate" value="<?php echo $event['event_date_start']; ?>">
                                                                            <span class="input-group-addon"> to </span>
                                                                            <input type="text" class="form-control" name="enddate" value="<?php echo $event['event_date_end']; ?>">
                                                                        </div>
                                                                        <!-- /input-group -->
                                                                        <span class="help-block" for="startdate enddate">Select date range of the event </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="control-label col-md-3">Time of move <span class="required">*</span></label>
                                                                    <div class="col-md-9">
                                                                        <div class="input-group">
                                                                            <select class="form-control" name="time">
                                                                                <option disabled selected value="">Select one..</option>
                                                                                <?php
                                                                                $timeOptions = mysql_query("SELECT time_start, time_end FROM fmo_locations_times WHERE time_location_token='".mysql_real_escape_string($event['event_location_token'])."'");
                                                                                if(mysql_num_rows($timeOptions) > 0){
                                                                                    while($t = mysql_fetch_assoc($timeOptions)){
                                                                                        if(empty($t['time_end'])){
                                                                                            $t['time_end'] = "finish";
                                                                                        }
                                                                                        ?>
                                                                                            <option value="<?php echo $t['time_start']; ?> to <?php echo $t['time_end']; ?>"><?php echo $t['time_start']; ?> to <?php echo $t['time_end']; ?></option>
                                                                                        <?php
                                                                                    }
                                                                                }
                                                                                ?>
                                                                            </select>
                                                                            <span class="input-group-btn">
                                                                              <button class="btn default" type="button"><i class="fa fa-clock-o"></i></button>
                                                                            </span>
                                                                        </div>
                                                                        <span class="help-block">Select time of customers event</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr/>
                                                        <h3>Information Gathering</h3> <br/>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="control-label col-md-3">Event Name <span class="required">*</span></label>
                                                                    <div class="col-md-9">
                                                                        <input type="text" class="form-control" name="name" value="<?php echo $event['event_name']; ?>'s move">
                                                                        <span class="help-block">
																        This should something like the homeowner's name, or their business name (if applicable) </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
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
                                                                        <span class="help-block">
                                                                        Most cases will use the option, "Local Move". </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
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
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="control-label col-md-3">Contact Email <span class="required">*</span></label>
                                                                    <div class="col-md-9">
                                                                        <input type="text" class="form-control" name="email" value="<?php echo $event['event_email']; ?>"/>
                                                                        <span class="help-block">
                                                                        example: something@somewhere.com </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="control-label col-md-3">Contact Phone <span class="required">*</span></label>
                                                                    <div class="col-md-9">
                                                                        <input type="text" class="form-control" id="mask_phone" name="phone" value="<?php echo $event['event_phone']; ?>"/>
                                                                        <span class="help-block">
                                                                        example: (999) 999-9999 </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane" id="tab2">
                                                        <div class="row" style="margin-top: 100px;">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label class="control-label col-md-3"><i class="fa fa-truck"></i> <span class="required">*</span></label>
                                                                    <div class="col-md-9">
                                                                        <div class="input-group">
                                                                            <input type="number" class="form-control doMath" name="truckfee" id="event_truckfee" value="<?php echo $event['event_truckfee']; ?>" data-a="#event_truckfee" data-b="#event_laborrate" data-c="#event_countyfee">
                                                                            <span class="input-group-btn">
                                                                                <button class="btn red" type="button" id="ev_TR" value="">$<span></span></button>
                                                                            </span>
                                                                        </div>
                                                                        <span class="help-block">
                                                                        # of trucks needed. </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label class="control-label col-md-3"><i class="fa fa-user"></i> <span class="required">*</span></label>
                                                                    <div class="col-md-9">
                                                                        <div class="input-group">
                                                                            <input type="number" class="form-control doMath" name="laborrate" id="event_laborrate" value="<?php echo $event['event_laborrate']; ?>" data-a="#event_truckfee" data-b="#event_laborrate" data-c="#event_countyfee">
                                                                            <span class="input-group-btn">
                                                                                <button class="btn red" type="button" id="ev_LR" value="">$<span></span></button>
                                                                            </span>
                                                                        </div>
                                                                        <span class="help-block">
                                                                        # of men needed. </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label class="control-label col-md-3"><i class="fa fa-location-arrow"></i> <span class="required">*</span></label>
                                                                    <div class="col-md-9">
                                                                        <div class="input-group">
                                                                            <input type="text" class="form-control doMath" name="countyfee" id="event_countyfee" value="<?php echo $event['event_countyfee']; ?>" data-a="#event_truckfee" data-b="#event_laborrate" data-c="#event_countyfee">
                                                                            <span class="input-group-btn">
                                                                                <button class="btn red" type="button" id="ev_CR" value="">$<span></span></button>
                                                                            </span>
                                                                        </div>
                                                                        <span class="help-block">
                                                                        # of counties traveling through. </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-8 col-md-offset-2 text-center">
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
                                                                <br/><br/><br/>
                                                                <label class="btn <?php if(!empty($extra['hot_tub'])){ echo "red"; } ?>">
                                                                    <img src="assets/global/img/catcher/hottub.gif" alt="..." class="img-thumbnail img-check <?php if(empty($extra['hot_tub'])){ echo "checked"; } ?>" style="vertical-align: top;">
                                                                    <label style="padding-top: 5px;">Hot Tub <br/>$398<br/>$350 w/ move <br/> <small>click image <br/>to add</small> </label>
                                                                    <input type="checkbox" name="addition[]" id="hot_tub" value="hot_tub" <?php if(!empty($extra['hot_tub'])){ echo "checked"; } ?> class="hidden" autocomplete="off">
                                                                </label>
                                                                <label class="btn <?php if(!empty($extra['piano'])){ echo "red"; } ?>">
                                                                    <img src="assets/global/img/catcher/babygrand.gif" alt="..." class="img-thumbnail img-check <?php if(empty($extra['piano'])){ echo "checked"; } ?>" style="vertical-align: top;">
                                                                    <label style="padding-top: 5px;">Piano <br/>$398<br/>$350 w/ move <br/> <small>click image <br/>to add</small> </label>
                                                                    <input type="checkbox" name="addition[]" id="piano" value="piano" <?php if(!empty($extra['piano'])){ echo "checked"; } ?> class="hidden" autocomplete="off">
                                                                </label>
                                                                <label class="btn <?php if(!empty($extra['pool_table'])){ echo "red"; } ?>">
                                                                    <img src="assets/global/img/catcher/pooltable.gif" alt="..." class="img-thumbnail img-check <?php if(empty($extra['pool_table'])){ echo "checked"; } ?>" style="vertical-align: top;">
                                                                    <label style="padding-top: 5px;">Pool Table <br/>$398<br/>$350 w/ move <br/> <small>click image <br/>to add</small> </label>
                                                                    <input type="checkbox" name="addition[]" id="pool_table" value="pool_table" <?php if(!empty($extra['pool_table'])){ echo "checked"; } ?> class="hidden" autocomplete="off">
                                                                </label> <br/><br/><br/>
                                                                <label class="btn <?php if(!empty($extra['play_set'])){ echo "red"; } ?>">
                                                                    <img src="assets/global/img/catcher/playset.gif" alt="..." class="img-thumbnail img-check <?php if(empty($extra['play_set'])){ echo "checked"; } ?>" style="vertical-align: top;">
                                                                    <label style="padding-top: 5px;">Play Set <br/>$378<br/>$300 w/ move <br/> <small>click image <br/>to add</small> </label>
                                                                    <input type="checkbox" name="addition[]" id="play_set" value="play_set" <?php if(!empty($extra['play_set'])){ echo "checked"; } ?> class="hidden" autocomplete="off">
                                                                </label>
                                                                <label class="btn <?php if(!empty($extra['safe'])){ echo "red"; } ?>">
                                                                    <img src="assets/global/img/catcher/safe.gif" alt="..." class="img-thumbnail img-check <?php if(empty($extra['safe'])){ echo "checked"; } ?>" style="vertical-align: top;">
                                                                    <label style="padding-top: 5px;">Safe <br/>$298<br/>$200 w/ move <br/> <small>click image <br/>to add</small> </label>
                                                                    <input type="checkbox" name="addition[]" id="safe" value="safe" <?php if(!empty($extra['safe'])){ echo "checked"; } ?> class="hidden" autocomplete="off">
                                                                </label>
                                                                <br/><br/><br/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane" id="tab3">
                                                        <div class="row">
                                                            <div class="col-md-5">
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
                                                            <div class="col-md-2 text-center">
                                                                <br/><br/>
                                                                <i class="fa fa-2x fa-info"></i> <br/>
                                                                <h3>Add locations</h3>
                                                            </div>
                                                            <div class="col-md-5">
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
                                                    <div class="tab-pane" id="tab4">
                                                        <div class="row">
                                                            <div class="col-md-12 text-center">
                                                                <button id="pay" class="btn btn-block btn-xl btn-success">Securely pay <strong>$3.00</strong> registration fee</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-actions">
                                                <div class="row">
                                                    <div class="col-md-offset-3 col-md-9">
                                                        <button href="javascript:;" class="btn default button-previous">
                                                            <i class="m-icon-swapleft"></i> Back </button>
                                                        <button href="javascript:;" class="btn blue button-next">
                                                            Continue <i class="m-icon-swapright m-icon-white"></i>
                                                        </button>
                                                        <button href="javascript:;" class="btn yellow button-save" name="status" value="0">
                                                            Save for later <i class="fa fa-download"></i>
                                                        </button>
                                                        <button href="javascript:;" class="btn green button-submit" name="status" value="1">
                                                            Submit <i class="m-icon-swapright m-icon-white"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
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
    <script>
        jQuery(document).ready(function(){

            var handler = StripeCheckout.configure({
                key: 'pk_test_o9s6ScI3jBABd3V5pZM7kdYA',
                image: 'https://stripe.com/img/documentation/checkout/marketplace.png',
                locale: 'auto',
                token: function(token) {
                    $.ajax({
                        url: 'assets/app/api/charge.php',
                        type: 'POST',
                        data: {
                            stripeToken: token.id,
                            stripeEmail: token.email,
                            stripeAmt: 300
                        },
                        success: function(data){

                        },
                        error: function(e){

                        }
                    })
                }
            });

            document.getElementById('pay').addEventListener('click', function(e) {
                // Open Checkout with further options:
                handler.open({
                    name: 'Booking Fee',
                    description: 'Allows customer to use card/check to pay later.',
                    amount: 300
                });
                e.preventDefault();
            });

            // Close Checkout on page navigation:
            window.addEventListener('popstate', function() {
                handler.close();
            });

            $.ajax({
                url: 'assets/app/api/catcher.php?luid=<?php echo $event['event_location_token']; ?>&p=jkv',
                type: 'POST',
                data: {
                    day: <?php echo date('N', strtotime($event['event_date_start'])); ?>
                },
                success: function(e){
                    var inf = JSON.parse(e);
                    $('#ev_TR > span').html(inf.truck_fee);
                    $('#ev_TR').attr('value', parseInt(inf.truck_fee));
                    $('#ev_LR > span').html(inf.total_labor_rate);
                    $('#ev_LR').val(inf.total_labor_rate);
                    $('#ev_CR > span').html(inf.county_fee);
                    $('#ev_CR').val(inf.county_fee);
                },
                error: function(e){

                }
            });

            $(".img-check").click(function(){
                $(this).toggleClass("check");
                $(this).parent().toggleClass("red");
            });

            $("#mask_phone").inputmask("mask", {
                "mask": "(999) 999-9999"
            });

            var date = $('.date-picker').datepicker({
                rtl: Metronic.isRTL(),
                orientation: "left",
                autoclose: true
            });
            $('.timepicker-no-seconds').timepicker({
                autoclose: true,
                minuteStep: 5
            });


            if (!jQuery().bootstrapWizard) {
                return;
            }

            function format(state) {
                if (!state.id) return state.text; // optgroup
                return "<img class='flag' src='../../assets/global/img/flags/" + state.id.toLowerCase() + ".png'/>&nbsp;&nbsp;" + state.text;
            }

            $("#country_list").select2({
                placeholder: "Select",
                allowClear: true,
                formatResult: format,
                formatSelection: format,
                escapeMarkup: function (m) {
                    return m;
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

                messages: {

                },

                errorPlacement: function (error, element) { // render error placement for each input type
                    if (element.attr("name") == "gender") { // for uniform radio buttons, insert the after the given container
                        error.insertAfter("#form_gender_error");
                    } else if (element.attr("name") == "payment[]") { // for uniform checkboxes, insert the after the given container
                        error.insertAfter("#form_payment_error");
                    } else {
                        error.insertAfter(element); // for other inputs, just perform default behavior
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
            $('#form_wizard_1 .button-submit').click(function () {
                Pace.track(function(){
                    $.ajax({
                        url: 'assets/app/update_settings.php?update=event&e=<?php echo $event['event_token']; ?>',
                        type: 'POST',
                        data: $('#submit_form').serialize(),
                        success: function(d) {
                            $.ajax({
                                url: 'assets/pages/profile.php?uuid=<?php echo $_GET['uuid']; ?>',
                                success: function(vat) {
                                    $('#page_content').html(vat);
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
                });
            }).hide();

            //apply validation on select2 dropdown value change, this only needed for chosen dropdown integration.
            $('#country_list', form).change(function () {
                form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
            });

            $('.doMath').on('change', function() {
                var a = $(this).attr('data-a');
                var b = $(this).attr('data-b');
                var c = $(this).attr('data-c');
                $.ajax({
                    url: 'assets/app/api/catcher.php?luid=<?php echo $event['event_location_token']; ?>&p=doMath',
                    type: 'POST',
                    data: {
                        day: <?php echo date('N', strtotime($event['event_date_start'])); ?>,
                        a: $(a).val(),
                        b: $(b).val(),
                        c: $(c).val()
                    },
                    success: function(d){
                        var e = JSON.parse(d);
                        $("#ev_TR > span").html(e.truck_fee);
                        $("#ev_TR").val(e.truck_fee);
                        $("#ev_LR > span").html(e.total_labor_rate);
                        $("#ev_LR").val(e.total_labor_rate);
                        $("#ev_CR > span").html(e.county_fee);
                        $('#ev_CR').val(e.county_fee);
                    },
                    error: function(e){

                    }
                })
            });
        });
    </script>
    <?php
} else {
    header("Location: ../../../index.php?err=no_access");
}
?>
