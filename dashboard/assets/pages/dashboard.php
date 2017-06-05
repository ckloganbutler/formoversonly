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
    $location = mysql_fetch_array(mysql_query("SELECT location_name, location_manager FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'"));
    ?>
    <div class="page-content">
        <h3 class="page-title">
            Dashboard
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
            </ul>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light">
                    <div class="portlet-title tabbable-line">
                        <div class="caption caption-md">
                            <i class="icon-home theme-font bold"></i>
                            <span class="caption-subject font-red bold uppercase"><?php echo $location['location_name']; ?></span> <span class="font-red">|</span> <small>Welcome back to <?php echo $location['location_name']; ?>'s dashboard, <strong><?php echo $_SESSION['fname']; ?></strong>. I collected information for you on today's current activity below.</small>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div id="stats" class="chart" style="height: 170px;">
                                </div>
                            </div>
                            <?php
                            if(!empty($location['location_manager'])){
                                ?>
                                <div class="col-md-6">
                                    <div class="clearfix">
                                        <ul class="media-list">
                                            <li class="media">
                                                <a class="pull-left" href="javascript:;">
                                                    <img class="media-object" src="<?php echo picture($location['location_manager']); ?>" alt="64x64" data-src="holder.js/64x64" style="width: 160px; height: 160px;">
                                                </a>
                                                <div class="media-body">
                                                    <textarea style="height: 110px;" class="form-control" id="ttm_msg" placeholder="Send <?php echo name($location['location_manager']); ?> a message.."></textarea> <br/>
                                                    <h4 class="media-heading pull-left"><strong><?php echo name($location['location_manager']); ?></strong> - <?php echo phone($location['location_manager']); ?></h4>
                                                    <button type="button" class="btn red pull-right ttm" style="margin-top: -7px;">Send text message</button>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <?php
                            } else {
                                ?>
                                <div class="col-md-6 text-center">
                                    <br/>
                                    <h3><i class="fa fa-2x fa-question text-danger"></i><br/> No manager has been assigned to this location.</h3>
                                </div>
                                <?php
                            }
                            ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $broadcast = getBroadcast($_SESSION['cuid']);
        if(!empty($broadcast['message']) && $broadcast['time'] < strtotime('+ 1 days')){
            ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-warning">
                        <marquee>
                            <?php echo $broadcast['time']; ?> | <strong class="text-danger" style="font-size: 16px;">
                                <?php echo $broadcast['message']; ?>
                            </strong>
                        </marquee>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light">
                    <div class="portlet-title tabbable-line">
                        <div class="caption caption-md">
                            <i class="icon-home theme-font bold"></i>
                            <span class="caption-subject font-red bold uppercase"><?php echo $location['location_name']; ?></span> <span class="font-red">|</span> <small>All Events</small>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="todo-tasklist">
                                    <?php
                                    $events = mysql_query("SELECT event_name, event_date_start, event_date_end, event_time, event_token, event_status, event_type, event_subtype FROM fmo_locations_events WHERE event_location_token='".mysql_real_escape_string($_GET['luid'])."'");
                                    if(mysql_num_rows($events) > 0){
                                        while($event = mysql_fetch_assoc($events)){
                                            if($event['event_status'] == 0){
                                                continue;
                                            }
                                            ?>
                                            <div class="todo-tasklist-item todo-tasklist-item-border-red load_page col-md-6 col-xs-12" data-href="assets/pages/event.php?ev=<?php echo $event['event_token']; ?>" data-page-title="<?php echo $event['event_name']; ?>">
                                                <div class="todo-tasklist-item-title">
                                                    <?php echo $event['event_name']; ?>
                                                </div>
                                                <div class="todo-tasklist-item-text">
                                                    <strong>Start:</strong> Indianapolis <i class="fa fa-map"></i> <strong>End:</strong> Fishers
                                                </div>
                                                <div class="todo-tasklist-controls pull-left">
                                                    <span class="todo-tasklist-date"><i class="fa fa-calendar"></i> <?php echo date('d M Y', strtotime($event['event_date_start'])); ?> - <?php echo date('d M Y', strtotime($event['event_date_end'])); ?> @ <?php echo $event['event_time']; ?></span>
                                                    <?php
                                                    if(!empty($event['event_type'])){
                                                        ?>
                                                        <span class="todo-tasklist-badge badge badge-roundless"><?php echo $event['event_type']; ?></span>
                                                        <?php
                                                    }
                                                    if(!empty($event['event_subtype'])){
                                                        ?>
                                                        <span class="todo-tasklist-badge badge badge-roundless"><?php echo $event['event_subtype']; ?></span>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <center>
                                            <h3>No events found for today at this location.</h3>
                                        </center>
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
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function(){
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

            $('.ttm').click(function() {
               var uuid = "<?php echo $location['location_manager']; ?>";
               $.ajax({
                   url: 'assets/app/texting.php?txt=ttm&uuid='+uuid,
                   type: 'POST',
                   data: {
                       msg: $('#ttm_msg').val()
                   },
                   success: function(e){
                       toastr.success("<strong>Logan says:</strong><br/>Text message has been sent to <?php echo name($location['location_manager']); ?>");
                   },
                   error: function(e){
                       toastr.error("<strong>Logan says:</strong><br/>Something bad happened. You messed everything up. Just kidding, try that again.")
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
