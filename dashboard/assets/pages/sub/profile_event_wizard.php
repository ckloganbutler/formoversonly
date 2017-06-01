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
        $event = mysql_fetch_array(mysql_query("SELECT event_location_token, event_date_start, event_date_end, event_time, event_name, event_email, event_phone, event_type, event_subtype, event_truckfee, event_laborrate, event_countyfee, event_additions FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($_GET['conf'])."'"));
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
                                                                            <input type="text" class="form-control timepicker timepicker-no-seconds" name="time">
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
                                                                                    <option <?php if($type['eventtype_name'] == $event['event_type']){echo "selected";} ?> value="<?php echo $type['eventtype_name']; ?> ?>"><?php echo $type['eventtype_name']; ?></option>
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
                                                                        <select class="form-control" name="type">
                                                                            <?php
                                                                            $subtypes = mysql_query("SELECT subtype_id, subtype_name FROM fmo_locations_subtypes WHERE subtype_location_token='".mysql_real_escape_string($event['event_location_token'])."'");
                                                                            if(mysql_num_rows($types) > 0){
                                                                                while($subtype = mysql_fetch_assoc($subtypes)){
                                                                                    ?>
                                                                                    <option <?php if($subtype['subtype_name'] == $event['event_subtype']){echo "selected";} ?> value="<?php echo $subtype['subtype_name']; ?> ?>"><?php echo $subtype['subtype_name']; ?></option>
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
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label class="control-label col-md-3"><i class="fa fa-truck"></i> <span class="required">*</span></label>
                                                                    <div class="col-md-9">
                                                                        <div class="input-group">
                                                                            <input type="number" class="form-control doMath" name="event_truckfee" id="event_truckfee" value="<?php echo $event['event_truckfee']; ?>" data-a="#event_truckfee" data-b="#event_laborrate" data-c="#event_countyfee">
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
                                                                            <input type="number" class="form-control doMath" name="event_laborrate" id="event_laborrate" value="<?php echo $event['event_laborrate']; ?>" data-a="#event_truckfee" data-b="#event_laborrate" data-c="#event_countyfee">
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
                                                                            <input type="text" class="form-control doMath" name="event_countyfee" id="event_countyfee" value="<?php echo $event['event_countyfee']; ?>" data-a="#event_truckfee" data-b="#event_laborrate" data-c="#event_countyfee">
                                                                            <span class="input-group-btn">
                                                                                <button class="btn red" type="button" id="ev_CF" value="">$<span></span></button>
                                                                            </span>
                                                                        </div>
                                                                        <span class="help-block">
                                                                        # of counties traveling through. </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <label class="btn">
                                                                    <img src="assets/global/img/catcher/hottub.gif" alt="..." class="img-thumbnail img-check check" style="vertical-align: top;">
                                                                    <label style="padding-top: 5px;">Hot Tub <br/>$398<br/>$350 w/ move <br/> <small>click image <br/>to add</small> </label>
                                                                    <input type="checkbox" name="addition[]" id="hot_tub" value="hot_tub" class="hidden" autocomplete="off">
                                                                </label>
                                                                <label class="btn">
                                                                    <img src="assets/global/img/catcher/babygrand.gif" alt="..." class="img-thumbnail img-check check" style="vertical-align: top;">
                                                                    <label style="padding-top: 5px;">Piano <br/>$398<br/>$350 w/ move <br/> <small>click image <br/>to add</small> </label>
                                                                    <input type="checkbox" name="addition[]" id="piano" value="piano" class="hidden" autocomplete="off">
                                                                </label>
                                                                <label class="btn">
                                                                    <img src="assets/global/img/catcher/pooltable.gif" alt="..." class="img-thumbnail img-check check" style="vertical-align: top;">
                                                                    <label style="padding-top: 5px;">Pool Table <br/>$398<br/>$350 w/ move <br/> <small>click image <br/>to add</small> </label>
                                                                    <input type="checkbox" name="addition[]" id="pool_table" value="pool_table" class="hidden" autocomplete="off">
                                                                </label>
                                                                <label class="btn">
                                                                    <img src="assets/global/img/catcher/playset.gif" alt="..." class="img-thumbnail img-check check" style="vertical-align: top;">
                                                                    <label style="padding-top: 5px;">Play Set <br/>$378<br/>$300 w/ move <br/> <small>click image <br/>to add</small> </label>
                                                                    <input type="checkbox" name="addition[]" id="play_set" value="play_set" class="hidden" autocomplete="off">
                                                                </label>
                                                                <label class="btn">
                                                                    <img src="assets/global/img/catcher/safe.gif" alt="..." class="img-thumbnail img-check check" style="vertical-align: top;">
                                                                    <label style="padding-top: 5px;">Safe <br/>$298<br/>$200 w/ move <br/> <small>click image <br/>to add</small> </label>
                                                                    <input type="checkbox" name="addition[]" id="safe" value="safe" class="hidden" autocomplete="off">
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane" id="tab3">

                                                    </div>
                                                    <div class="tab-pane" id="tab4">

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
    <script type="text/javascript">
        jQuery(document).ready(function(){

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
                        url: 'assets/app/add_event.php?ev=pck&uuid=<?php echo $_GET['uuid']; ?>&luid=<?php echo $_GET['luid']; ?>&e=',
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
