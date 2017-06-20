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
                            <div class="col-md-12 text-center" style="margin-top: 80px; margin-bottom: 80px;">
                                <h1 style="margin-top: 0;">Please select a report to view.</h1>
                                <small>A report based on the date range <strong class="range"> <?php echo date('m-d-Y', strtotime($start)); ?> - <?php echo date('m-d-Y', strtotime($end)); ?></strong> will be generated here. (payroll only working)</small>
                            </div>
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

            $('#dashboard-report-range').show();

        });
    </script>
    <?php
} else {
    header("Location: ../../../index.php?err=no_access");
}
?>
