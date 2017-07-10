<?php
include '../app/init.php';
$event    = mysql_fetch_array(mysql_query("SELECT event_id, event_token, event_location_token, event_company_token, event_booking, event_user_token, event_name, event_date_start, event_date_end, event_time, event_truckfee, event_laborrate, event_countyfee, event_status, event_email, event_phone, event_type, event_subtype, event_additions, event_comments FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($_GET['ev'])."'"));
$location = mysql_fetch_array(mysql_query("SELECT location_name, location_token FROM fmo_locations WHERE location_token='".mysql_real_escape_string($event['event_location_token'])."'"));
$user     = mysql_fetch_array(mysql_query("SELECT user_id, user_fname, user_lname, user_email, user_phone, user_token FROM fmo_users WHERE user_token='".mysql_real_escape_string($event['event_user_token'])."'"));

?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title><?php echo $event['event_name']; ?> | Tracker</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8">
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
    <script src="../../assets/global/plugins/pace/pace.min.js" type="text/javascript"></script>
    <link href="../../assets/global/plugins/pace/themes/pace-theme-minimal.css" rel="stylesheet" type="text/css"/>
    <link href="../../assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="../../assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
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
            $name = companyName($event['event_company_token']);
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
        <div class="top-menu">
            <ul class="nav navbar-nav pull-right">
                <li class="dropdown dropdown-extended dropdown-inbox dropdown-dark" id="header_inbox_bar">
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        <i class="icon-envelope-open"></i>
                        <span class="badge badge-default">
					1 </span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="external">
                            <h3>You have <span class="bold">1 new</span> message</h3>
                            <a href="">view all</a>
                        </li>
                        <li>
                            <ul class="dropdown-menu-list scroller" style="height: 275px;" data-handle-color="#637283">
                                <li>
                                    <a href="">
                                        <span class="photo">
                                            <img src="../admin/layout/img/avatar2.jpg" class="img-circle" alt="">
                                        </span>
                                        <span class="subject">
                                            <span class="from">Lisa Wong </span>
                                            <span class="time">Just Now </span>
                                        </span>
                                        <span class="message">
                                            Thanks for booking your move with us! If you have any ques...
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li class="dropdown dropdown-user dropdown-dark">
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        <img alt="" class="img-circle" src="//www.formoversonly.com/dashboard/assets/admin/layout/img/default.png"/>
                        <span class="username username-hide-on-mobile">
					        <?php echo $user['user_fname']." ".$user['user_lname']; ?>
                        </span>
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-default">
                        <li>
                            <a href="">
                                <i class="icon-key"></i> Log Out
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- END USER LOGIN DROPDOWN -->
                <!-- BEGIN QUICK SIDEBAR TOGGLER -->
                <!--
                <li class="dropdown dropdown-quick-sidebar-toggler">
                    <a href="javascript:;" class="dropdown-toggle">
                        <i class="icon-logout"></i>
                    </a>
                </li>
                <!-- END QUICK SIDEBAR TOGGLER -->
            </ul>
        </div>
    </div>
</div>
<div class="clearfix">
</div>
<div class="container">
    <div class="page-container">
        <div class="page-content-wrapper">
            <div class="page-content" style="margin-left: 0px !important;">
                <h3 class="page-title">
                    <strong><?php echo $event['event_name']; ?></strong> | <small>(EVENT ID #<?php echo $event['event_id']; ?>)</small>

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
                            <a href="#"><?php echo $location['location_name']; ?></a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="#"><?php echo $user['user_fname']." ".$user['user_lname']; ?></a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="#"><?php echo $event['event_name']; ?></a>
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
                <div class="row" id="tracker-content">

                </div>
            </div>
        </div>
    </div>
    <div class="page-footer">
        <div class="page-footer-inner">
            2017 &copy; For Movers Only | Powered by <strong>CkAI 5.0</strong>
        </div>
        <div class="scroll-to-top">
            <i class="icon-arrow-up"></i>
        </div>
    </div>
    <!--[if lt IE 9]>
    <script src="../../assets/global/plugins/respond.min.js"></script>
    <script src="../../assets/global/plugins/excanvas.min.js"></script>
    <![endif]-->
    <script src="../../assets/global/plugins/jquery.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
    <script src="../../assets/global/scripts/metronic.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/bootstrap-daterangepicker/moment.min.js" type="text/javascript" ></script>
    <script src="../../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js" type="text/javascript"></script>
    <script src="../../assets/admin/layout/scripts/layout.js" type="text/javascript"></script>
    <script src="../../assets/admin/layout/scripts/quick-sidebar.js" type="text/javascript"></script>
    <script src="../../assets/admin/layout/scripts/demo.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/bootstrap-toastr/toastr.min.js"></script>
    <script>
        jQuery(document).ready(function() {
            Metronic.init(); // init metronic core components
            Layout.init(); // init current layout
            $.ajax({
                url: 'tracker/dashboard.php?ev=<?php echo $_GET['ev']; ?>',
                success: function(data) {
                    $('#tracker-content').html(data);
                },
                error: function() {
                    toastr.error("<strong>CkAI says</strong>:<br/>An unexpected error has occured. Please try again later.");
                }
            });
            $(document).on('click', '.load_page', function(){
                var url = $(this).attr('data-href');
                var tit = $(this).attr('data-page-title');
                Pace.track(function(){
                    $.ajax({
                        url: url,
                        success: function(data) {
                            $('#tracker-content').html(data);
                            document.title = tit+" - For Movers Only";
                        },
                        error: function() {
                            toastr.error("<strong>CkAI says</strong>:<br/>An unexpected error has occured. Please try again later.");
                        }
                    });
                });
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
        });
    </script>
    <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>