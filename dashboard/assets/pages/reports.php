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
    $location = mysql_fetch_array(mysql_query("SELECT location_name FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'"));

    $refStart                = new DateTime('2017-01-02');
    $periodLength            = 14;
    $now                     = new DateTime();
    $cur                     = date('Y-m-d');
    $daysIntoCurrentPeriod   = (int)$now->diff($refStart)->format('%a') % $periodLength;
    $currentPeriodStart      = clone $now;
    $currentPeriodStart->sub(new DateInterval('P'.$daysIntoCurrentPeriod.'D'));
    $start                   = date("Y-m-d", strtotime($cur." -".$daysIntoCurrentPeriod." days"));
    $end                     = date('Y-m-d', strtotime($start." +13 days"));

    ?>
    <div class="page-content">
        <h3 class="page-title">
            <strong>Reports</strong>
        </h3>
        <style>
            @media print {
                .no_print {
                    display: none;
                }
            }
        </style>
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <i class="fa fa-home"></i>
                    <a class="load_page" data-href="assets/pages/dashboard.php?luid=<?php echo $_GET['luid']; ?>"><?php echo $location['location_name']; ?></a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                    <a class="load_page" data-href="assets/pages/reports.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Reports">Reports</a>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light">
                    <div class="portlet-title tabbable-line">
                        <div class="caption caption-md">
                            <i class="icon-layers theme-font bold"></i>
                            <span class="caption-subject font-red bold uppercase"><?php echo $location['location_name']; ?></span> <span class="font-red">|</span>  <small>Reports</small>
                        </div>
                        <div class="actions">
                            <div class="btn-group">
                                <a id="report-range" class="pull-right tooltips btn red" data-container="body" data-placement="bottom" data-original-title="Change dashboard date range">
                                    <i class="icon-calendar"></i>&nbsp;
                                    <span class="bold uppercase visible-lg-inline-block">
                                        <?php echo date('m-d-Y', strtotime($start)); ?> - <?php echo date('m-d-Y', strtotime($end)); ?>
                                    </span>&nbsp; <i class="fa fa-angle-down"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                <div class="dashboard-stat blue-madison">
                                    <div class="visual">
                                        <i class="fa fa-money"></i>
                                    </div>
                                    <div class="details">
                                        <div class="number">
                                            Sales
                                        </div>
                                        <div class="desc">
                                            <span class="range">
                                                <?php echo date('m-d-Y', strtotime($start)); ?> - <?php echo date('m-d-Y', strtotime($end)); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <a class="more" href="javascript:;">
                                        View report <i class="m-icon-swapright m-icon-white"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                <div class="dashboard-stat red-intense">
                                    <div class="visual">
                                        <i class="fa fa-bar-chart-o"></i>
                                    </div>
                                    <div class="details">
                                        <div class="number">
                                            Payroll
                                        </div>
                                        <div class="desc">
                                            <span class="range">
                                                <?php echo date('m-d-Y', strtotime($start)); ?> - <?php echo date('m-d-Y', strtotime($end)); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <a class="more load_reports_pull" data-type="pryl" data-href="assets/pages/sub/reports_master.php?luid=<?php echo $_GET['luid']; ?>&cuid=<?php echo $_SESSION['cuid']; ?>" data-page-title="Payroll Report" data-ext="<?php echo date('Y-m-d', strtotime($start)); ?> - <?php echo date('Y-m-d', strtotime($end)); ?>">
                                        View report <i class="m-icon-swapright m-icon-white"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                <div class="dashboard-stat green-haze">
                                    <div class="visual">
                                        <i class="fa fa-shopping-cart"></i>
                                    </div>
                                    <div class="details">
                                        <div class="number">
                                            Marketing
                                        </div>
                                        <div class="desc">
                                            <span class="range">
                                                <?php echo date('m-d-Y', strtotime($start)); ?> - <?php echo date('m-d-Y', strtotime($end)); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <a class="more" href="javascript:;">
                                        View report <i class="m-icon-swapright m-icon-white"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                <div class="dashboard-stat purple-plum">
                                    <div class="visual">
                                        <i class="fa fa-globe"></i>
                                    </div>
                                    <div class="details">
                                        <div class="number">
                                            Other Reports
                                        </div>
                                        <div class="desc">
                                            <span class="range">
                                                <?php echo date('m-d-Y', strtotime($start)); ?> - <?php echo date('m-d-Y', strtotime($end)); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <a class="more load_reports_pull" date-type="" data-href="assets/pages/sub/reports_master.php?luid=<?php echo $_GET['luid']; ?>&cuid=<?php $_SESSION['cuid']; ?>">
                                        View report <i class="m-icon-swapright m-icon-white"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="row" id="reports-content">
                            <?php
                            if($_SESSION['group'] == 1.0 || $_SESSION['group'] == 2.0){
                                ?>
                                <div class="col-md-12">
                                    <div class="portlet">
                                        <div class="portlet-title tabbable-line">
                                            <div class="caption caption-md">
                                                <i class="fa fa-users theme-font"></i>
                                                <span class="caption-subject font-red bold uppercase">COMPANY SNAPSHOT</span> <span class="font-red">|</span> <button class="btn btn-xs red-stripe print" data-print="#reportsStuff"><i class="fa fa-print"></i> Print</button>
                                            </div>
                                            <ul class="nav nav-tabs">

                                            </ul>
                                        </div>
                                        <div class="portlet-body" id="reportsStuff">
                                            <center>
                                                <h3>
                                                    <i class="fa fa-users"></i> Company Snapshot | <strong class="range"><?php echo date('m-d-Y', strtotime($start)); ?> - <?php echo date('m-d-Y', strtotime($end)); ?></strong>
                                                </h3><br/>
                                            </center>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div id="stats" class="chart" style="height: 170px;">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" style="margin-left: 10px; margin-right: 10px; margin-top: 30px;">
                                                <?php
                                                $locations = mysql_query("SELECT location_name, location_token, location_address, location_city, location_state, location_zip FROM fmo_locations WHERE location_owner_token='".mysql_real_escape_string($_SESSION['uuid'])."'");

                                                if(mysql_num_rows($locations) > 0){
                                                    while($loc = mysql_fetch_assoc($locations)){
                                                        ?>
                                                        <div class="portfolio-block">
                                                            <div class="col-md-5" style="padding-left: 0;">
                                                                <div class="portfolio-text">
                                                                    <img src="assets/admin/pages/media/gallery/image3.jpg" alt="" height="81px" width="81px">
                                                                    <div class="portfolio-text-info">
                                                                        <h4><?php echo $loc['location_name']; ?></h4>
                                                                        <p>
                                                                            <?php echo $loc['location_address'].", ".$loc['location_city'].", ".$loc['location_state']." - ".$loc['location_zip']; ?>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-5 portfolio-stat" style="margin-top: 8px;">
                                                                <div class="portfolio-info">
                                                                    New Bookings <span>0 </span>
                                                                </div>
                                                                <div class="portfolio-info">
                                                                    Booking Fees Paid <span>0 </span>
                                                                </div>
                                                                <div class="portfolio-info">
                                                                    NET Sales <span>0 </span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2 no-print" style="padding-right: 0;">
                                                                <div class="portfolio-btn">
                                                                    <a class="btn bigicn-only load_page" data-href="assets/pages/manage_location.php?luid=<?php echo $loc['location_token']; ?>" data-page-title="<?php echo $loc['location_name']; ?>">
                                                                        <span>Manage </span>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    }
                                                } else {
                                                    ?>
                                                    <h3 class="text-center">No locations found for your company yet. Would you like to <a class="load_page" data-href="assets/pages/create_location.php">create one</a>?</h3>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            } else {
                                ?>
                                <div class="col-md-12 text-center" style="margin-top: 80px; margin-bottom: 80px;">
                                    <h1 style="margin-top: 0;">Please select a report to view.</h1>
                                    <small>A report based on the date range <strong class="range"> <?php echo date('m-d-Y', strtotime($start)); ?> - <?php echo date('m-d-Y', strtotime($end)); ?></strong> will be generated here. (payroll only working)</small>
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
    <script>
        $(document).ready(function(){
            $('#report-range').daterangepicker({
                    opens: (Metronic.isRTL() ? 'right' : 'left'),
                    startDate: "<?php echo date('m-d-Y', strtotime($start)); ?>",
                    endDate: "<?php echo date('m-d-Y', strtotime($end)); ?>",
                    showDropdowns: false,
                    showWeekNumbers: true,
                    timePicker: false,
                    timePickerIncrement: 1,
                    timePicker12Hour: true,
                    buttonClasses: ['btn btn-sm'],
                    applyClass: ' blue',
                    cancelClass: 'default',
                    format: 'MM-DD-YYYY',
                    separator: ' - ',
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
                    $('#report-range span').html(start.format('MM-DD-YYYY') + ' - ' + end.format('MM-DD-YYYY'));
                    $('.range').html(start.format('MM-DD-YYYY') + ' - ' + end.format('MM-DD-YYYY'));
                    $('.load_reports_pull').attr('data-ext', start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
                }
            );

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

            $('#dashboard-report-range').show();

        });
    </script>
    <?php
} else {
    header("Location: ../../../index.php?err=no_access");
}
?>
