<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 10/18/2017
 * Time: 12:02 PM
 */

include '../app/init.php';

?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8">
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
    <link href="../../assets/global/plugins/pace/themes/pace-theme-minimal.css" rel="stylesheet" type="text/css"/>
    <link href="../../assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="../../assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="../../assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
    <link href="../../assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>
    <link href="../../assets/global/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>
    <link href="../../assets/global/css/plugins.css" rel="stylesheet" type="text/css"/>
    <link href="../../assets/global/plugins/bootstrap-toastr/toastr.min.css" rel="stylesheet" type="text/css"/>
    <link href="../../assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
    <link href="../../assets/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>
    <link id="style_color" href="../../assets/admin/layout/css/themes/default.css" rel="stylesheet" type="text/css"/>
    <link href="../../assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>
    <link rel="shortcut icon" href="favicon.ico"/>
    <style type="text/css">
        .page-content-wrapper .page-content {
            margin-left: 0px !important;
            margin-top: 0px;
            min-height: 600px;
            padding: 25px 20px 10px 20px;
        }
    </style>
</head>
<body class="page-header-fixed page-quick-sidebar-over-content page-boxed page-sidebar-closed-hide-logo">
<!-- BEGIN HEADER -->
<div class="page-header -i navbar navbar-fixed-top">
    <!-- BEGIN HEADER INNER -->
    <div class="page-header-inner container">
        <!-- BEGIN LOGO -->
        <div class="page-logo" style="color: white; text-transform: uppercase; font-size: 23px; letter-spacing: .01em; word-spacing: 1px; width: auto; margin-top: 7px; font-weight: 300;">
            <?php
            $name = companyName($_GET['cuid']);
            if(!empty($name)){
                $cool = explode(" ", $name);
                $white = true; $red = false;
                foreach($cool as $word){
                    if($white == true){
                        $white = false;
                        $color = "#FFFFFF";
                        $red   = true;
                        echo "<span style='color: ".$color."'>".$word."</span>";
                    } elseif($red == true){
                        $red   = false;
                        $color = "#cb5a5e";
                        $white = true;
                        echo "<span style='color: ".$color."'>".$word."</span>";
                    }
                }
            }
            ?>
        </div>
    </div>
</div>
<div class="clearfix">
</div>
<div class="container">
    <div class="page-container">
        <div class="page-content-wrapper">
            <div class="page-content"  style="margin-left: 0px !important;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet light">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="icon-paper-plane bold"></i>
                                    <span class="caption-subject bold font-red uppercase">
                                        Request information
                                    </span>
                                    <span class="caption-helper">right now, we just need a few details from you. A <strong>customer service representative</strong> will reach back out to you shortly.</span>
                                </div>
                            </div>
                            <div class="portlet-body" id="page">
                                <form id="lead" action="" method="POST" role="form">
                                    <div class="form-body">
                                        <h3 style="margin-top: 0px;"><strong>Personal</strong> details <small class="pull-right text-muted" style="margin-top: 7px;">name<span class="text-danger">*</span>, phone number<span class="text-danger">*</span></small></h3> <hr/>
                                        <div class="form-group">
                                            <label class="control-label">Your full name</label>
                                            <div class="input-icon">
                                                <i class="fa fa-user"></i>
                                                <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Name (first + last, common)" name="name"/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Phone Number</label>
                                            <div class="input-icon">
                                                <i class="fa fa-phone"></i>
                                                <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Phone Number" name="phone"/>
                                            </div>
                                        </div>
                                        <h3><strong>Move</strong> details <small class="pull-right text-muted" style="margin-top: 7px;">starting zip code<span class="text-danger">*</span>, move date<span class="text-danger">*</span>, # of bedrooms<span class="text-danger">*</span></small></h3> <hr/>
                                        <div class="form-group">
                                            <label class="control-label">Starting Zip Code</label>
                                            <div class="input-icon">
                                                <i class="fa fa-flag"></i>
                                                <input class="form-control placeholder-no-fix" type="number" autocomplete="off" placeholder="Zip Code" name="catcher_zipcode"/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Move Date</label>
                                            <div class="input-icon">
                                                <i class="fa fa-calendar-o"></i>
                                                <input class="form-control placeholder-no-fix" id="date" type="text" autocomplete="off" value="<?php echo date('m-d-Y', strtotime('today + 2 days')); ?>"/>
                                                <input type="hidden" name="date" value="<?php echo date('m-d-Y', strtotime('today + 2 days')); ?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label"># of bedrooms</label>
                                            <div class="input-icon">
                                                <i class="fa fa-bed"></i>
                                                <input class="form-control placeholder-no-fix" type="number" autocomplete="off" placeholder="Bedrooms" name="bedrooms"/>
                                            </div>
                                        </div>
                                        <h3><strong>Move</strong> pick-up details</h3><hr/>

                                        <h4>
                                            <a id="p_show" onclick="$('#p_wrapper').show(function(){$('#p_show').hide(); $('#p_hide').show();});"><i class="fa fa-plus-circle"></i> &nbsp; add pickup details <strong>(optional)</strong></a>
                                            <a id="p_hide" onclick="$('#p_wrapper').hide(function(){$('#p_hide').hide(); $('#p_show').show();})" style="display: none; margin-bottom: 20px;"><i class="fa fa-times"></i> &nbsp; cancel (it was <strong>optional</strong>) <span class="text-muted pull-right">address<span class="text-danger">*</span>, city<span class="text-danger">*</span>, state<span class="text-danger">*</span>, zip code<span class="text-danger">*</span></span></a>
                                        </h4>

                                        <div id="p_wrapper" style="display: none">
                                            <div class="form-group">
                                                <label class="control-label">Address</label>
                                                <div class="input-icon">
                                                    <i class="fa fa-building"></i>
                                                    <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Street Address" name="address"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">City</label>
                                                <div class="input-icon">
                                                    <i class="fa fa-building-o"></i>
                                                    <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="City" name="address_city"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">State</label>
                                                <div class="input-icon">
                                                    <i class="fa fa-location-arrow"></i>
                                                    <select class="form-control state" name="address_state" id="state">
                                                        <option disabled selected value="">State</option>
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
                                            <div class="form-group">
                                                <label class="control-label">Zip Code</label>
                                                <div class="input-icon">
                                                    <i class="fa fa-group"></i>
                                                    <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Zip Code" name="address_zip"/>
                                                </div>
                                            </div>
                                        </div>
                                        <h3><strong>Move</strong> destination details</h3><hr/>
                                        <h4>
                                            <a id="d_show" onclick="$('#d_wrapper').show(function(){$('#d_show').hide(); $('#d_hide').show();});"><i class="fa fa-plus-circle"></i> &nbsp; add destination details <strong>(optional)</strong></a>
                                            <a id="d_hide" onclick="$('#d_wrapper').hide(function(){$('#d_hide').hide(); $('#d_show').show();})" style="display: none; margin-bottom: 20px;"><i class="fa fa-times"></i> &nbsp; cancel (it was <strong>optional</strong>) <span class="text-muted pull-right">address<span class="text-danger">*</span>, city<span class="text-danger">*</span>, state<span class="text-danger">*</span>, zip code<span class="text-danger">*</span></span></a>
                                        </h4>

                                        <div id="d_wrapper" style="display: none">
                                            <div class="form-group">
                                                <label class="control-label">Address</label>
                                                <div class="input-icon">
                                                    <i class="fa fa-building"></i>
                                                    <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Street Address" name="address_2"/>
                                                 </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">City</label>
                                                <div class="input-icon">
                                                    <i class="fa fa-building-o"></i>
                                                    <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="City" name="address_city_2"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">State</label>
                                                <div class="input-icon">
                                                    <i class="fa fa-location-arrow"></i>
                                                    <select class="form-control state_2" name="address_state_2" id="state">
                                                        <option disabled selected value="">State</option>
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
                                            <div class="form-group">
                                                <label class="control-label">Zip Code</label>
                                                <div class="input-icon">
                                                    <i class="fa fa-group"></i>
                                                    <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Zip Code" name="address_zip_2"/>
                                                </div>
                                            </div>
                                        </div >
                                        <br/> <br/>
                                        <div class="form-group">
                                            <label class="control-label"><strong>Comments</strong> from you to us</label>
                                            <div class="input-icon">
                                                <i class="fa fa-location-arrow"></i>
                                                <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="type your thoughts here.." name="comments"/>
                                                <span class="help-block">You can include any extra details or opinions you have about your move.</span>
                                            </div>
                                        </div>

                                        <div class="alert alert-danger error" style="display: none;">
                                            <strong>Check your form for errors</strong>, then try again.
                                        </div>

                                        <button type="submit" class="btn btn-block red submitter">Request more information!</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-footer">
        <div class="page-footer-inner">
            <strong>For Movers Only&trade;</strong> - Moving Management Software | &copy; 2016-2017 <a target="_blank" href="//www.captialkingdom.com">CK, Inc.</a> | <a target="_blank" href="https://www.fmcsa.dot.gov/protect-your-move"><strong>Your rights & responsibilities.</strong></a>
        </div>
        <div class="scroll-to-top">
            <i class="icon-arrow-up"></i>
        </div>
    </div>
    <script src="../../assets/global/plugins/respond.min.js"></script>
    <script src="../../assets/global/plugins/excanvas.min.js"></script>

    <script src="../../assets/global/plugins/jquery.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/bootstrap-daterangepicker/moment.min.js" type="text/javascript" ></script>
    <script src="../../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js" type="text/javascript"></script>
    <script src="../../assets/global/scripts/metronic.js" type="text/javascript"></script>
    <script src="../../assets/admin/layout/scripts/layout.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/pace/pace.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            Metronic.init(); // init metronic core components
            Layout.init(); // init current layout

            $(function() {
                // IMPORTANT: Fill in your client key
                var clientKey = "js-InlLzUGLaGPQYhaSPQrQGnDmZH0HPvLyT6ks10ebG31Ekcxa3Y0KmE6ml73bDOJw";

                var cache = {};
                var container = $("#lead");

                /** Handle successful response */
                function handleResp(data) {
                    // Check for error
                    if (data.error_msg) toastr.error("<strong>Ckai says:</strong><br/>"+data.error_msg);
                    else if ("city" in data) {
                        // Set city and state
                        container.find("input[name='address_city']").val(data.city);
                        container.find("input[name='address_city_2']").val(data.city);
                        container.find("input[name='address_zip']").val($('input[name="catcher_zipcode"]').val());
                        container.find("input[name='address_zip_2']").val($('input[name="catcher_zipcode"]').val());
                        container.find('.state option[value="'+data.state+'"]').attr("selected", "selected");
                        container.find('.state_2 option[value="'+data.state+'"]').attr("selected", "selected");
                        console.log(data.state);
                    }
                }
                // Set up event handlers
                container.find("input[name='catcher_zipcode']").on("keyup change", function() {
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

            $('#date').daterangepicker({
                    opens: (Metronic.isRTL() ? 'right' : 'left'),
                    startDate: "<?php echo  date('m-d-Y', strtotime('today + 2 days')); ?>",
                    endDate: "<?php echo date('m-d-Y', strtotime('today + 2 days')); ?>",
                    minDate: "<?php echo date('m-d-Y', strtotime('today + 2 days')); ?>",
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
                    $('input[name="date"]').val(start.format('YYYY-MM-DD'));
                }
            );





            $('#lead').validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",
                rules: {
                    name: {
                        required: true
                    },
                    phone: {
                        required: true
                    },
                    catcher_zipcode: {
                        required: true,
                        minlength: 5,
                        maxlength: 5
                    },
                    date: {
                        required: true
                    },
                    bedrooms: {
                        required: true
                    }
                },

                invalidHandler: function(event, validator) { //display error alert on form submit
                    $('.error').show();
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
                    var name = $('input[name="name"').val();
                    var phone = $('input[name="phone"]').val().replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '');
                    var zipcode = $("input[name='catcher_zipcode']").val();
                    var data_form = $('#lead').serialize();

                    $('#page').html("").append("<h3 id='locating' class='text-center'></h3> <br/> <h3 id='registering' class='text-center' style='margin-top: -10px!important'> <br/><br/> <h3 id='done' class='text-center'></h3>");
                    $('#locating').html('Locating closest office... <i class="fa fa-spinner fa-pulse" style="font-size: 30px;"></i>');
                    $.ajax({
                        url: '../app/api/catcher.php?cuid=<?php echo $_GET['cuid']; ?>&p=jdk',
                        type: 'POST',
                        data: {
                            zipcode: zipcode
                        },
                        success: function (data) {
                            $('#locating').html('Office located. <i class="fa fa-check text-success" style="font-size: 30px;"></i>');
                            $('#registering').html('Registering you to system... <i class="fa fa-spinner fa-pulse" style="font-size: 30px;"></i>');
                            $.ajax({
                                url: '../app/register.php?gr=3&c=<?php echo $_GET['cuid']; ?>',
                                type: 'POST',
                                data: {
                                    fullname: name,
                                    phone: phone,
                                    luid: data
                                },
                                success: function (dat) {
                                    $('#registering').html('Registered to system <i class="fa fa-check text-success" style="font-size: 30px;"></i>');
                                    $('#done').html('Finishing up... <i class="fa fa-spinner fa-pulse" style="font-size: 30px;"></i>');
                                    $.ajax({
                                        url: '../app/add_event.php?ev=plk&uuid=' + dat + '&luid=' + data + '&cuid=<?php echo $_GET['cuid']; ?>&e=<?php echo struuid(true); ?>&hot=weblead&l=1',
                                        type: 'POST',
                                        data: data_form,
                                        success: function (ev) {
                                            $('#done').html('<strong class"text-success">All done!</strong> We will be in touch soon. <i class="fa fa-check text-success" style="font-size: 30px;"></i>');
                                        },
                                        error: function () {

                                        }
                                    });
                                }
                            });
                        }
                    });

                }
            });
            $("input[name='phone']").inputmask("mask", {
                "mask": "(999) 999-9999"
            });
        });
    </script>
    <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>