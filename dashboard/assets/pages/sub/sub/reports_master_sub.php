<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 9/24/2017
 * Time: 5:09 AM
 */
if(isset($_GET['ty'])) {
    session_start();
    include '../../../app/init.php';
    $uuidperm = mysql_fetch_array(mysql_query("SELECT user_esc_permissions FROM fmo_users WHERE user_token='".mysql_real_escape_string($_SESSION['uuid'])."'"));
    if ($_GET['ty'] == 'ftk') {
        /*
         *  Booking Fee Report
         */

        if($_SESSION['uuid'] == 'DH8I8KKVVXLZAJA5G' || $_SESSION['uuid'] == 'DJ5RELUMTA7QPHWJK'){
            $bf = array();
            $bookingfees = mysql_query("SELECT payment_event_token, payment_user_token, payment_company_token, payment_type, payment_charge_token, payment_amount, payment_era, payment_timestamp, payment_by_user_token FROM fmo_locations_events_payments WHERE payment_type REGEXP '[[:<:]]Booking Fee[[:>:]]' AND (YEAR(payment_timestamp)='".mysql_real_escape_string(date('Y'))."' OR YEAR(payment_timestamp)='".mysql_real_escape_string(date('Y', strtotime('-1 year')))."') ORDER BY payment_timestamp ASC");
            if(mysql_num_rows($bookingfees) > 0){
                $bkk_chart['person'][date('Y')]                                     = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
                $bkk_chart['person'][date('Y', strtotime('-1 year'))]         = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
                while($booking = mysql_fetch_assoc($bookingfees)){
                    $bf['records'][] = array(
                        ''.date('m-d-Y', strtotime($booking['payment_timestamp'])).'',
                        '<strong>'.companyName($booking['payment_company_token']).'</strong> - '.eventName($booking['payment_event_token']),
                        ''.$booking['payment_era'].'',
                        ''.$booking['payment_charge_token'].'',
                        '<strong>'.eventLocationName($booking['payment_event_token']).'</strong>',
                        '<strong>'.name($booking['payment_by_user_token']).'</strong>',
                    );
                    $bkk_chart['person'][date('Y', strtotime($booking['payment_timestamp']))][date('n', strtotime($booking['payment_timestamp']))] += 1;
                }
            }
        }
        ?>
        <style>
            @media print {
                .hide {display: none;}
            }
        </style>
        <div class="portlet">
            <div class="portlet-title" style="margin-bottom: 0px;">
                <h3>
                    <strong>Booking Fee Report</strong> <small class="text-muted">(not affected by date range)</small>
                    <button class="btn default blue-stripe pull-right btn-xs print printer" data-print="#bkk_print"><i class="fa fa-print" style="font-size: 16px;"></i> Print pretty report</button>
                </h3>
            </div>
            <div class="portlet-body">
                <div class="row margin-top-25">
                    <div class="col-md-12">
                        <div id="bkk_chart" class="chart" style="height: 170px;">
                        </div>
                    </div>
                </div>
                <hr/>
                <table class="table table-striped table-hover datatable" id="bkk_print">
                    <thead>
                    <tr>
                        <th>Hidden Date</th>
                        <th>Date</th>
                        <th>Company Name & Event</th>
                        <th>Taken Where</th>
                        <th>Transaction Token</th>
                        <th class="text-right">Location</th>
                        <th class="text-right">Taken By</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach($bf['records'] as $bfs){
                        ?>
                        <tr>
                            <td><?php echo date('Y-m-d', strtotime($bfs[0])); ?></td>
                            <td class="bold"><?php echo $bfs[0]; ?></td>
                            <td><?php echo $bfs[1]; ?></td>
                            <td><?php echo $bfs[2]; ?></td>
                            <td><?php echo $bfs[3]; ?></td>
                            <td><?php echo $bfs[4]; ?></td>
                            <td><?php echo $bfs[5]; ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
                <!--
                <div class="task-footer">
                    <div class="btn-arrow-link pull-right">
                        <a href="javascript:;">See All Records</a>
                        <i class="icon-arrow-right"></i>
                    </div>
                </div> -->
            </div>
        </div>
        <script>
            var bk = $('.datatable').dataTable({
                "bFilter" : true,
                "bLengthChange": true,
                "bPaginate": true,
                "info": true,
                "saveState": true,
                "columnDefs": [
                    {
                        "targets": [ 0 ],
                        "visible": false,
                        "searchable": false
                    }
                ],
                "order": [[ 0, "desc" ]],
            });
            $('.printer').unbind().on('click', function () {
                var settings = bk.fnSettings();
                settings._iDisplayLength = 50;
                bk.fnDraw();
            });
            var data = <?php echo json_encode($bkk_chart) ?>;
            var pageviews = [
                [1, data.person[<?php echo date('Y', strtotime('-1 year')); ?>][1]],
                [2, data.person[<?php echo date('Y', strtotime('-1 year')); ?>][2]],
                [3, data.person[<?php echo date('Y', strtotime('-1 year')); ?>][3]],
                [4, data.person[<?php echo date('Y', strtotime('-1 year')); ?>][4]],
                [5, data.person[<?php echo date('Y', strtotime('-1 year')); ?>][5]],
                [6, data.person[<?php echo date('Y', strtotime('-1 year')); ?>][6]],
                [7, data.person[<?php echo date('Y', strtotime('-1 year')); ?>][7]],
                [8, data.person[<?php echo date('Y', strtotime('-1 year')); ?>][8]],
                [9, data.person[<?php echo date('Y', strtotime('-1 year')); ?>][9]],
                [10, data.person[<?php echo date('Y', strtotime('-1 year')); ?>][10]],
                [11, data.person[<?php echo date('Y', strtotime('-1 year')); ?>][11]],
                [12, data.person[<?php echo date('Y', strtotime('-1 year')); ?>][12]]
            ];
            var visitors = [
                [1, data.person[<?php echo date('Y'); ?>][1]],
                [2, data.person[<?php echo date('Y'); ?>][2]],
                [3, data.person[<?php echo date('Y'); ?>][3]],
                [4, data.person[<?php echo date('Y'); ?>][4]],
                [5, data.person[<?php echo date('Y'); ?>][5]],
                [6, data.person[<?php echo date('Y'); ?>][6]],
                [7, data.person[<?php echo date('Y'); ?>][7]],
                [8, data.person[<?php echo date('Y'); ?>][8]],
                [9, data.person[<?php echo date('Y'); ?>][9]],
                [10, data.person[<?php echo date('Y'); ?>][10]],
                [11, data.person[<?php echo date('Y'); ?>][11]],
                [12, data.person[<?php echo date('Y'); ?>][12]]
            ];


            var plot = $.plot($("#bkk_chart"), [{
                data: pageviews,
                label: "<?php echo date('Y', strtotime("-1 year")); ?> Count Per Month",
                lines: {
                    lineWidth: 1,
                },
                shadowSize: 0
            }, {
                data: visitors,
                label: "<?php echo date('Y'); ?> Count Per Month",
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
                colors: ["#37b7f3", "#52e136", "#d12610", "#d4ad38"],
                xaxis: {
                    ticks: [
                        [1, "Jan"],
                        [2, "Feb"],
                        [3, "Mar"],
                        [4, "Apr"],
                        [5, "May"],
                        [6, "Jun"],
                        [7, "Jul"],
                        [8, "Aug"],
                        [9, "Sep"],
                        [10, "Oct"],
                        [11, "Nov"],
                        [12, "Dec"]
                    ]
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
            $("#bkk_chart").bind("plothover", function(event, pos, item) {
                $("#x").text(pos.x.toFixed(2));
                $("#y").text(pos.y.toFixed(2));

                if (item) {
                    if (previousPoint != item.dataIndex) {
                        previousPoint = item.dataIndex;

                        $("#tooltip").remove();
                        var x = item.datapoint[0],
                            y = item.datapoint[1].toFixed(2);

                        showTooltip(item.pageX, item.pageY, y);
                    }
                } else {
                    $("#tooltip").remove();
                    previousPoint = null;
                }
            });
        </script>
        <?php
        /*
         *  End Booking Fee Report
         */
    } elseif($_GET['ty'] == 'akr') {
        /*
         *  Accounts Rec. Report
         */

        $range = explode(" - ", $_POST['ext']);
        $ac['users'] = array();
        $events      = mysql_query("SELECT event_id, event_token, event_user_token, event_name, event_phone, event_date_start, event_location_token FROM fmo_locations_events WHERE event_location_token='".mysql_real_escape_string($_GET['luid'])."' AND (event_date_start>='".mysql_real_escape_string($range[0])."' AND event_date_end<='".mysql_real_escape_string($range[1])."') AND NOT event_status=0 AND NOT event_status=9 ORDER BY event_date_start ASC");
        if(mysql_num_rows($events) > 0) {
            $ac_chart['person'][date('Y')]                              = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
            $ac_chart['person'][date('Y', strtotime('-1 year'))]        = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
            while ($event = mysql_fetch_assoc($events)) {

                $location = mysql_fetch_array(mysql_query("SELECT location_sales_tax FROM fmo_locations WHERE location_token='".mysql_real_escape_string($event['event_location_token'])."'"));

                if(!empty($location['location_sales_tax'])){
                    $tax = $location['location_sales_tax'];
                } else {$tax = 0;}

                $findItems = mysql_query("SELECT item_total, item_taxable, item_commission FROM fmo_locations_events_items WHERE item_event_token='".mysql_real_escape_string($event['event_token'])."'");
                $iTotalRecords = mysql_num_rows($findItems);

                $findPaid = mysql_query("SELECT payment_amount, payment_type FROM fmo_locations_events_payments WHERE payment_event_token='".mysql_real_escape_string($event['event_token'])."'");
                $bTotalRecords = mysql_num_rows($findPaid);


                $total = array();
                $total['sub_total'] = 0.00;
                $total['tax']       = 0.00;
                $total['taxable']   = 0.00;
                $total['cc_fees']   = 0.00;
                $total['total']     = 0.00;
                $total['paid']      = 0.00;
                $total['unpaid']    = 0.00;
                if($iTotalRecords > 0){
                    while($item = mysql_fetch_assoc($findItems)){
                        $total['sub_total'] += $item['item_total'];
                        if($item['item_taxable'] == 1){
                            $total['tax']     += number_format($item['item_total'] * $tax, 2, '.', '');
                            $total['taxable'] += number_format($item['item_total'], 2, '.', '');
                        } else {
                            $total['tax']   += 0.00;
                        }
                        if($item['item_commission'] == 1){
                            $total['coms']  += number_format($item['item_total'], 2, '.', '');
                        } else {
                            $total['coms'] += 0.00;
                        }
                    }
                    $total['total'] = number_format($total['sub_total'] + $total['tax'], 2, '.', '');
                } else {
                    $total['total']     = 0.00;
                    $total['sub_total'] = 0.00;
                }

                if($bTotalRecords > 0){
                    while($paid = mysql_fetch_assoc($findPaid)){
                        $void = explode(" - ", $paid['payment_type']);
                        if($void[1] != 'VOIDED' && $void[0] != 'Invoice'){
                            $total['paid'] += $paid['payment_amount'];
                            if($void[0] == 'Credit/Debt' && $void[1] != 'Booking Fee'){
                                $total['total']   += ($paid['payment_amount'] / 1.03) * .03;
                                // alone i stand
                                $total['cc_fees'] += ($paid['payment_amount'] / 1.03) * .03;
                            }
                        }

                    }
                    $total['unpaid'] = number_format($total['total'] - $total['paid'], 2, '.', '');
                } else {
                    $total['unpaid'] = number_format($total['total'], 2, '.', '');
                    $total['paid']   = 0.00;
                }

                if($total['unpaid'] > 0 && $total['unpaid'] !== false){
                    $ac['users'][] = array(
                        ''.date('m-d-Y', strtotime($event['event_date_start'])).'',
                        '<strong>EVENT</strong>: '.$event['event_name'].' #'.$event['event_id'].' <span class="text-muted">[ <strong>CUSTOMER</strong>: '.name($event['event_user_token']).' <strong>/</strong> '.clean_phone($event['event_phone']).' ]</span>',
                        ''.number_format($total['paid'], 2).'',
                        ''.number_format($total['unpaid'], 2).'',
                        ''.$event['event_token'].''
                    );

                    $ac_chart['person'][date('Y')][date('n', strtotime($event['event_date_start']))] += $total['unpaid'];
                }
            }
        }
        ?>
        <style type="text/css">
            #acc_rece_wrapper {
                margin-top: -42px;
            }
        </style>
        <div class="portlet">
            <div class="portlet-title" style="margin-bottom: 0px;">
                <h3>
                    <strong>Accounts Receivable</strong>  <small class="text-muted">(date range <strong><?php echo date('m/d/Y', strtotime($range[0])); ?> - <?php echo date('m/d/Y', strtotime($range[1])); ?> </strong>)</small>
                    <button class="btn default blue-stripe pull-right btn-xs print printer" data-print="#acc_rece"><i class="fa fa-print" style="font-size: 16px;"></i> Print pretty report</button>
                </h3>
            </div>
            <div class="portlet-body">
                <div class="row margin-top-25">
                    <div class="col-md-12">
                        <div id="ac_chart" class="chart" style="height: 170px;">
                        </div>
                    </div>
                </div>
                <hr/>
                <h3>Table o' details</h3>
                <table class="table table-striped table-hover datatable" id="acc_rece">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Event & Customer</th>
                        <th class="text-right">Amount <em>Paid</em></th>
                        <th class="text-right">Amount <em>Still Due</em></th>
                        <th class="text-right">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach($ac['users'] as $items){
                        ?>
                        <tr>
                            <td><?php echo $items[0]; ?></td>
                            <td><?php echo $items[1]; ?></td>
                            <td class="text-right"><strong class="text-success">$<?php echo $items[2]; ?></strong></td>
                            <td class="text-right"><strong class="text-danger">$<?php echo $items[3]; ?></strong></td>
                            <td class="text-right">
                                <button class="btn btn-xs red popout" data-pop="event.php?ev=<?php echo $items[4]; ?>" data-page-title="Reviewing Event"><i class="fa fa-dollar"></i> Receive payment</button>
                                <button class="btn btn-xs purple "><i class="fa fa-user-secret"></i> Mark for Attorney</button>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
                <!--
                <div class="task-footer">
                    <div class="btn-arrow-link pull-right">
                        <a href="javascript:;">See All Records</a>
                        <i class="icon-arrow-right"></i>
                    </div>
                </div> -->
            </div>
        </div>
        <script>
            var bk = $('.datatable').dataTable({
                "order": [[ 0, "desc" ]],
                "bFilter" : true,
                "bLengthChange": false,
                "bPaginate": false,
                "info": true,
                "saveState": true
            });
            $('.printer').unbind().on('click', function () {
                var settings = bk.fnSettings();
                settings._iDisplayLength = 100;
                bk.fnDraw();
            });
            var data = <?php echo json_encode($ac_chart) ?>;
            var pageviews = [
                [1, data.person[<?php echo date('Y', strtotime('-1 year')); ?>][1]],
                [2, data.person[<?php echo date('Y', strtotime('-1 year')); ?>][2]],
                [3, data.person[<?php echo date('Y', strtotime('-1 year')); ?>][3]],
                [4, data.person[<?php echo date('Y', strtotime('-1 year')); ?>][4]],
                [5, data.person[<?php echo date('Y', strtotime('-1 year')); ?>][5]],
                [6, data.person[<?php echo date('Y', strtotime('-1 year')); ?>][6]],
                [7, data.person[<?php echo date('Y', strtotime('-1 year')); ?>][7]],
                [8, data.person[<?php echo date('Y', strtotime('-1 year')); ?>][8]],
                [9, data.person[<?php echo date('Y', strtotime('-1 year')); ?>][9]],
                [10, data.person[<?php echo date('Y', strtotime('-1 year')); ?>][10]],
                [11, data.person[<?php echo date('Y', strtotime('-1 year')); ?>][11]],
                [12, data.person[<?php echo date('Y', strtotime('-1 year')); ?>][12]]
            ];
            var visitors = [
                [1, data.person[<?php echo date('Y'); ?>][1]],
                [2, data.person[<?php echo date('Y'); ?>][2]],
                [3, data.person[<?php echo date('Y'); ?>][3]],
                [4, data.person[<?php echo date('Y'); ?>][4]],
                [5, data.person[<?php echo date('Y'); ?>][5]],
                [6, data.person[<?php echo date('Y'); ?>][6]],
                [7, data.person[<?php echo date('Y'); ?>][7]],
                [8, data.person[<?php echo date('Y'); ?>][8]],
                [9, data.person[<?php echo date('Y'); ?>][9]],
                [10, data.person[<?php echo date('Y'); ?>][10]],
                [11, data.person[<?php echo date('Y'); ?>][11]],
                [12, data.person[<?php echo date('Y'); ?>][12]]
            ];


            var plot = $.plot($("#ac_chart"), [{
                data: visitors,
                label: "<?php echo date('Y'); ?> Unpaid Per Month",
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
                colors: ["#d12610", "#37b7f3", "#52e136", "#d4ad38"],
                xaxis: {
                    ticks: [
                        [1, "Jan"],
                        [2, "Feb"],
                        [3, "Mar"],
                        [4, "Apr"],
                        [5, "May"],
                        [6, "Jun"],
                        [7, "Jul"],
                        [8, "Aug"],
                        [9, "Sep"],
                        [10, "Oct"],
                        [11, "Nov"],
                        [12, "Dec"]
                    ]
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
            $("#ac_chart").bind("plothover", function(event, pos, item) {
                $("#x").text(pos.x.toFixed(2));
                $("#y").text(pos.y.toFixed(2));

                if (item) {
                    if (previousPoint != item.dataIndex) {
                        previousPoint = item.dataIndex;

                        $("#tooltip").remove();
                        var x = item.datapoint[0].toFixed(2),
                            y = item.datapoint[1].toFixed(2);

                        showTooltip(item.pageX, item.pageY, item.series.label + " this month made: " + y);
                    }
                } else {
                    $("#tooltip").remove();
                    previousPoint = null;
                }
            });
        </script>
        <?php

        /*
         *  End Accounts Rec. Report
         */
    } elseif($_GET['ty'] == 'rdt') {
        /*
         *  Redemptions Report
         */

        $redeem = array();
        $events = mysql_query("SELECT event_token, event_user_token, event_name, event_date_start, event_location_token FROM fmo_locations_events WHERE event_location_token='".mysql_real_escape_string($_GET['luid'])."'");
        if(mysql_num_rows($events) > 0){
            $rdt_chart['person'][date('Y')] = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
            $rdt_chart['prepay'][date('Y')] = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
            while($event = mysql_fetch_assoc($events)){
                $items = mysql_query("SELECT item_id, item_item, item_desc, item_added, item_total, item_prepay, item_qty FROM fmo_locations_events_items WHERE item_event_token='".mysql_real_escape_string($event['event_token'])."' AND item_redeemable=1");
                if(mysql_num_rows($items) > 0){

                    while($item = mysql_fetch_assoc($items)){
                        $redeem['items'][] = array(
                            ''.$event['event_date_start'].'',
                            ''.$event['event_name'].' [ '.locationName($event['event_location_token']).' ]',
                            ''.$item['item_item'].'',
                            ''.$item['item_qty'].'',
                            ''.$item['item_desc'].'',
                            ''.$item['item_total'].'',
                            ''.$item['item_prepay'].'',
                            ''.$item['item_id'].''
                        );
                        $rdt_chart['person'][date('Y')][date('n', strtotime($event['event_date_start']))] += $item['item_total'];
                        $rdt_chart['prepay'][date('Y')][date('n', strtotime($event['event_date_start']))] += $item['item_prepay'];
                    }
                }
            }
        }
        ?>
        <style>
            @media print {
                .hide {display: none;}
            }
            #redempt_wrapper {
                margin-top: -42px;
            }
        </style>
        <div class="portlet">
            <div class="portlet-title" style="margin-bottom: 0px;">
                <h3>
                    <strong>Redemptions Report</strong> <small class="text-muted">(not affected by date range)</small>
                    <button class="btn default blue-stripe pull-right btn-xs print printer" data-print="#redempt"><i class="fa fa-print" style="font-size: 16px;"></i> Print pretty report</button>
                </h3>
            </div>
            <div class="portlet-body">
                <div class="row margin-top-25">
                    <div class="col-md-12">
                        <div id="rdt_chart" class="chart" style="height: 170px;">
                        </div>
                    </div>
                </div>
                <hr/>
                <h3>Table o' details</h3>
                <table class="table table-striped table-hover datatable" id="redempt">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Event Name</th>
                        <th>Item (Qty)</th>
                        <th>Description/Codes</th>
                        <th>Discount</th>
                        <th>Prepaid</th>
                        <th class="text-center">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach($redeem['items'] as $items){
                        if($items[3] > 0){ $txt = 'font-green'; } else { $txt = 'font-red'; }
                        ?>
                        <tr>
                            <td class="bold"><?php echo date('m/d/Y', strtotime($items[0])); ?></td>
                            <td><strong>EVENT:</strong> <?php echo $items[1]; ?></td>
                            <td class="<?php echo $txt; ?>"><?php echo $items[2]; ?> <span class="text-muted "><strong>(<?php echo number_format($items[3], 0); ?> qty)</strong></span></td>
                            <td><?php echo $items[4]; ?></td>
                            <td><?php echo $items[5]; ?></td>
                            <td><?php echo $items[6]; ?></td>
                            <td>
                                <?php
                                if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_sales_redemption_redeem") !== false){
                                    ?>
                                    <button class="btn default blue-stripe btn-xs redeem btn-block" data-redeem="<?php echo $items[7]; ?>"><i class="fa fa-check"></i> Mark as redeemed (currently not)</button>
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
                <!--
                <div class="task-footer">
                    <div class="btn-arrow-link pull-right">
                        <a href="javascript:;">See All Records</a>
                        <i class="icon-arrow-right"></i>
                    </div>
                </div> -->
            </div>
        </div>
        <script>
            var bk = $('.datatable').dataTable({
                "order": [[ 0, "desc" ]],
                "bFilter" : true,
                "bLengthChange": false,
                "bPaginate": false,
                "info": true,
                "saveState": true
            });
            $('.printer').unbind().on('click', function () {
                var settings = bk.fnSettings();
                settings._iDisplayLength = 50;
                bk.fnDraw();
            });
            $('.redeem').click(function() {
                var btn  = $(this);
                var item = $(this).data('redeem');
                $.ajax({
                    url: 'assets/app/update_settings.php?setting=redeem',
                    type: 'POST',
                    data: {
                        item: item
                    },
                    success: function(s){
                        $(btn).attr('disabled', true);
                        $(btn).removeClass('blue-stripe').addClass('green-stripe');
                        $(btn).html("Marked as redeemed <i class='fa fa-check'></i>");
                        toastr.info("<strong>Logan says:</strong><br/>Item ("+item+") has been redeemed.");
                    },
                    error: function(e){

                    }
                });
            });
            $('.printer').unbind().on('click', function () {
                var settings = bk.fnSettings();
                settings._iDisplayLength = 50;
                bk.fnDraw();
            });
            var data = <?php echo json_encode($rdt_chart) ?>;
            var pageviews = [
                [1, data.person[<?php echo date('Y'); ?>][1]],
                [2, data.person[<?php echo date('Y'); ?>][2]],
                [3, data.person[<?php echo date('Y'); ?>][3]],
                [4, data.person[<?php echo date('Y'); ?>][4]],
                [5, data.person[<?php echo date('Y'); ?>][5]],
                [6, data.person[<?php echo date('Y'); ?>][6]],
                [7, data.person[<?php echo date('Y'); ?>][7]],
                [8, data.person[<?php echo date('Y'); ?>][8]],
                [9, data.person[<?php echo date('Y'); ?>][9]],
                [10, data.person[<?php echo date('Y'); ?>][10]],
                [11, data.person[<?php echo date('Y'); ?>][11]],
                [12, data.person[<?php echo date('Y'); ?>][12]]
            ];
            var visitors = [
                [1, data.prepay[<?php echo date('Y'); ?>][1]],
                [2, data.prepay[<?php echo date('Y'); ?>][2]],
                [3, data.prepay[<?php echo date('Y'); ?>][3]],
                [4, data.prepay[<?php echo date('Y'); ?>][4]],
                [5, data.prepay[<?php echo date('Y'); ?>][5]],
                [6, data.prepay[<?php echo date('Y'); ?>][6]],
                [7, data.prepay[<?php echo date('Y'); ?>][7]],
                [8, data.prepay[<?php echo date('Y'); ?>][8]],
                [9, data.prepay[<?php echo date('Y'); ?>][9]],
                [10, data.prepay[<?php echo date('Y'); ?>][10]],
                [11, data.prepay[<?php echo date('Y'); ?>][11]],
                [12, data.prepay[<?php echo date('Y'); ?>][12]]
            ];


            var plot = $.plot($("#rdt_chart"), [{
                data: pageviews,
                label: "<?php echo date('Y'); ?> Discounts Per Month",
                lines: {
                    lineWidth: 1,
                },
                shadowSize: 0
            }, {
                data: visitors,
                label: "<?php echo date('Y'); ?> Prepaid Per Month",
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
                colors: ["#37b7f3", "#52e136", "#d12610", "#d4ad38"],
                xaxis: {
                    ticks: [
                        [1, "Jan"],
                        [2, "Feb"],
                        [3, "Mar"],
                        [4, "Apr"],
                        [5, "May"],
                        [6, "Jun"],
                        [7, "Jul"],
                        [8, "Aug"],
                        [9, "Sep"],
                        [10, "Oct"],
                        [11, "Nov"],
                        [12, "Dec"]
                    ]
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
            $("#rdt_chart").bind("plothover", function(event, pos, item) {
                $("#x").text(pos.x.toFixed(2));
                $("#y").text(pos.y.toFixed(2));

                if (item) {
                    if (previousPoint != item.dataIndex) {
                        previousPoint = item.dataIndex;

                        $("#tooltip").remove();
                        var x = item.datapoint[0].toFixed(2),
                            y = item.datapoint[1].toFixed(2);

                        showTooltip(item.pageX, item.pageY, item.series.label + " this month made: " + y);
                    }
                } else {
                    $("#tooltip").remove();
                    previousPoint = null;
                }
            });
        </script>
        <?php

        /*
         *  End Redemptions Report
         */
    } elseif($_GET['ty'] == 'srv') {
        /*
         *  Service Items Report
         */

        $range             = explode(" - ", $_POST['ext']);
        $services['types'] = array();
        $events            = mysql_query("SELECT event_id, event_token, event_user_token, event_name, event_date_start, event_location_token FROM fmo_locations_events WHERE event_location_token='".mysql_real_escape_string($_GET['luid'])."' AND (event_date_start>='".mysql_real_escape_string($range[0])."' AND event_date_end<='".mysql_real_escape_string($range[1])."') AND NOT event_status=0 AND NOT event_status=9 ORDER BY event_date_start ASC");
        if(mysql_num_rows($events) > 0) {
            $srv_chart['person'][date('Y')]    = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
            $srv_chart['discount'][date('Y')]  = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
            while ($event = mysql_fetch_assoc($events)) {
                $location = mysql_fetch_array(mysql_query("SELECT location_sales_tax FROM fmo_locations WHERE location_token='".mysql_real_escape_string($event['event_location_token'])."'"));

                if(!empty($location['location_sales_tax'])){
                    $tax = $location['location_sales_tax'];
                } else {$tax = 0;}

                $findItems = mysql_query("SELECT item_item, item_desc, item_qty, item_cost, item_total, item_taxable, item_commission, item_redeemable, item_prepay FROM fmo_locations_events_items WHERE item_event_token='".mysql_real_escape_string($event['event_token'])."'");
                $iTotalRecords = mysql_num_rows($findItems);

                if($iTotalRecords > 0){
                    while($item = mysql_fetch_assoc($findItems)){
                        if($item['item_item'] != 'Booking Fee'){
                            if(in_array($item['item_item'], $services['types'], true)){
                                $count = $services['types'][$item['item_item']]['count'];
                                $services['types'][$item['item_item']]['count'] += $count++;
                                $services['types'][$item['item_item']]['total_qty'] += $item['item_qty'];
                                if($item['item_taxable'] == 1){
                                    $services['types'][$item['item_item']]['sales'] += ($item['item_qty'] * $item['item_cost']) + ($item['item_qty'] * $item['item_cost']) * $tax;
                                } else {
                                    $services['types'][$item['item_item']]['sales'] += ($item['item_qty'] * $item['item_cost']);
                                } if($item['item_redeemable'] == 1){
                                    $services['types'][$item['item_item']]['prepay']   += $item['item_prepay'];
                                    $services['types'][$item['item_item']]['discount'] += $item['item_total'];
                                }
                                $services['types'][$item['item_item']]['sales']     += $item['item_qty'];
                            } else {
                                if(!empty($item['item_item'])){
                                    $services['types'][$item['item_item']]['count']    += 1;
                                    $services['types'][$item['item_item']]['name']      = $item['item_item'];
                                    $services['types'][$item['item_item']]['total_qty'] += $item['item_qty'];
                                    if($item['item_taxable'] == 1){
                                        $services['types'][$item['item_item']]['sales'] += ($item['item_qty'] * $item['item_cost']) + ($item['item_qty'] * $item['item_cost']) * $tax;
                                    } else {
                                        $services['types'][$item['item_item']]['sales'] += ($item['item_qty'] * $item['item_cost']);
                                    } if($item['item_redeemable'] == 1){
                                        $services['types'][$item['item_item']]['prepay']   += $item['item_prepay'];
                                        $services['types'][$item['item_item']]['discount'] += $item['item_total'];
                                    }
                                }
                            }
                            if($item['item_taxable'] == 1){
                                if($item['item_total'] > 0){
                                    $srv_chart['person'][date('Y')][date('n', strtotime($event['event_date_start']))] += ($item['item_qty'] * $item['item_cost']) + ($item['item_qty'] * $item['item_cost']) * $tax;
                                } elseif($item['item_total'] <= 0){
                                    $srv_chart['discount'][date('Y')][date('n', strtotime($event['event_date_start']))] += ($item['item_qty'] * $item['item_cost']);
                                }
                            } else {
                                if($item['item_total'] > 0){
                                    $srv_chart['person'][date('Y')][date('n', strtotime($event['event_date_start']))] += ($item['item_qty'] * $item['item_cost']);
                                } elseif($item['item_total'] <= 0){
                                    $srv_chart['discount'][date('Y')][date('n', strtotime($event['event_date_start']))] += ($item['item_qty'] * $item['item_cost']);
                                }
                            }
                        }
                    }
                }
            }
        }
        ?>
        <style type="text/css">
            #service_items_wrapper {
                margin-top: -42px;
            }
        </style>
        <div class="portlet">
            <div class="portlet-title" style="margin-bottom: 0px;">
                <h3>
                    <strong>Service Items Report</strong>  <small class="text-muted">(date range <strong><?php echo date('m/d/Y', strtotime($range[0])); ?> - <?php echo date('m/d/Y', strtotime($range[1])); ?> </strong>)</small>
                    <button class="btn default blue-stripe pull-right btn-xs print printer" data-print="#service_items"><i class="fa fa-print" style="font-size: 16px;"></i> Print pretty report</button>
                </h3>
            </div>
            <div class="portlet-body">
                <div class="row margin-top-25">
                    <div class="col-md-12">
                        <div id="srv_chart" class="chart" style="height: 170px;">
                        </div>
                    </div>
                </div>
                <hr/>
                <h3>Table o' details</h3>
                <table class="table table-striped table-hover datatable" id="service_items">
                    <thead>
                    <tr>
                        <th>Service Item</th>
                        <th class="text-right">Total # of uses</th>
                        <th class="text-right">Total Quantity</th>
                        <th class="text-right">Average</th>
                        <th class="text-right">Total Income</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach($services['types'] as $srv){
                        ?>
                        <tr>
                            <td><strong>ITEM:</strong> <?php echo $srv['name'];  ?></td>
                            <td class="font-green bold text-right"><?php echo $srv['count']; ?></td>
                            <td class="font-green bold text-right"><?php echo $srv['total_qty']; ?></td>
                            <td class="font-red bold text-right">$<?php echo number_format($srv['sales'] / $srv['total_qty'], 2); ?> average</td>
                            <td class="text-success bold text-right">$<?php echo number_format($srv['sales'], 2); ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
                <!--
                <div class="task-footer">
                    <div class="btn-arrow-link pull-right">
                        <a href="javascript:;">See All Records</a>
                        <i class="icon-arrow-right"></i>
                    </div>
                </div> -->

            </div>
        </div>
        <script>
            var bk = $('.datatable').dataTable( {
                "order": [[ 4, "desc" ]],
                "bFilter" : true,
                "bLengthChange": false,
                "bPaginate": false,
                "info": true,
                "saveState": true
            } );
            $('.printer').unbind().on('click', function () {
                var settings = bk.fnSettings();
                settings._iDisplayLength = 1000;
                bk.fnDraw();
            });
            var data = <?php echo json_encode($srv_chart) ?>;
            var pageviews = [
                [1, data.person[<?php echo date('Y'); ?>][1]],
                [2, data.person[<?php echo date('Y'); ?>][2]],
                [3, data.person[<?php echo date('Y'); ?>][3]],
                [4, data.person[<?php echo date('Y'); ?>][4]],
                [5, data.person[<?php echo date('Y'); ?>][5]],
                [6, data.person[<?php echo date('Y'); ?>][6]],
                [7, data.person[<?php echo date('Y'); ?>][7]],
                [8, data.person[<?php echo date('Y'); ?>][8]],
                [9, data.person[<?php echo date('Y'); ?>][9]],
                [10, data.person[<?php echo date('Y'); ?>][10]],
                [11, data.person[<?php echo date('Y'); ?>][11]],
                [12, data.person[<?php echo date('Y'); ?>][12]]
            ];
            var visitors = [
                [1, data.discount[<?php echo date('Y'); ?>][1]],
                [2, data.discount[<?php echo date('Y'); ?>][2]],
                [3, data.discount[<?php echo date('Y'); ?>][3]],
                [4, data.discount[<?php echo date('Y'); ?>][4]],
                [5, data.discount[<?php echo date('Y'); ?>][5]],
                [6, data.discount[<?php echo date('Y'); ?>][6]],
                [7, data.discount[<?php echo date('Y'); ?>][7]],
                [8, data.discount[<?php echo date('Y'); ?>][8]],
                [9, data.discount[<?php echo date('Y'); ?>][9]],
                [10, data.discount[<?php echo date('Y'); ?>][10]],
                [11, data.discount[<?php echo date('Y'); ?>][11]],
                [12, data.discount[<?php echo date('Y'); ?>][12]]
            ];


            var plot = $.plot($("#srv_chart"), [{
                data: pageviews,
                label: "<?php echo date('Y'); ?> Total Income Per Month",
                lines: {
                    lineWidth: 1,
                },
                shadowSize: 0
            }, {
                data: visitors,
                label: "<?php echo date('Y'); ?> Discounts Per Month",
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
                colors: ["#52e136", "#d12610", "#d4ad38"],
                xaxis: {
                    ticks: [
                        [1, "Jan"],
                        [2, "Feb"],
                        [3, "Mar"],
                        [4, "Apr"],
                        [5, "May"],
                        [6, "Jun"],
                        [7, "Jul"],
                        [8, "Aug"],
                        [9, "Sep"],
                        [10, "Oct"],
                        [11, "Nov"],
                        [12, "Dec"]
                    ]
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
            $("#srv_chart").bind("plothover", function(event, pos, item) {
                $("#x").text(pos.x.toFixed(2));
                $("#y").text(pos.y.toFixed(2));

                if (item) {
                    if (previousPoint != item.dataIndex) {
                        previousPoint = item.dataIndex;

                        $("#tooltip").remove();
                        var x = item.datapoint[0].toFixed(2),
                            y = item.datapoint[1].toFixed(2);

                        showTooltip(item.pageX, item.pageY, item.series.label + " this month made: " + y);
                    }
                } else {
                    $("#tooltip").remove();
                    previousPoint = null;
                }
            });
        </script>
        <?php

        /*
         *  End Service Items Report
         */
    } elseif($_GET['ty'] == 'cjr') {
        /*
         *  Completed Jobs Report
         */

        $range             = explode(" - ", $_POST['ext']);
        $services['types'] = array();
        $events            = mysql_query("SELECT event_id, event_token, event_user_token, event_name, event_date_start, event_location_token, event_status, event_cjr FROM fmo_locations_events WHERE event_location_token='".mysql_real_escape_string($_GET['luid'])."' AND (event_date_start>='".mysql_real_escape_string($range[0])."' AND event_date_end<='".mysql_real_escape_string($range[1])."') AND NOT event_status=0 AND NOT event_status=9 ORDER BY event_date_start ASC");
        if(mysql_num_rows($events) > 0) {
            while ($event = mysql_fetch_assoc($events)) {
                $payments = mysql_query("SELECT payment_user_token, payment_transaction_id, payment_type, payment_amount, payment_payout_reason, payment_payout_amount, payment_detail, payment_deposit_token, payment_timestamp, payment_by_user_token FROM fmo_locations_events_payments WHERE payment_event_token='".mysql_real_escape_string($event['event_token'])."'");
                if(mysql_num_rows($payments) > 0){
                    while($payment = mysql_fetch_assoc($payments)){
                        $void = explode(" - ", $payment['payment_type']);
                        $cjr_payment[$event['event_token']]['payments'][] = array(
                            '' . date('m-d-Y', strtotime($payment['payment_timestamp'])) . '',
                            '' . $payment['payment_type'] . '',
                            '' . name($payment['payment_by_user_token']) . '',
                            '$' . number_format($payment['payment_amount'], 2) . '',
                        );
                        if(empty($payment['payment_deposit_token'])) {
                            if($void[0] == 'Cash' || $void[0] == 'Check') {
                                $deposit_warn = true;
                            } else {
                                $deposit_warn = NULL;
                            }
                        } else {
                            $deposit_warn = NULL;
                        }
                    }
                } else {
                    $deposit_warn = NULL;
                }

                $findItems = mysql_query("SELECT item_item, item_qty, item_cost, item_total FROM fmo_locations_events_items WHERE item_event_token='".mysql_real_escape_string($event['event_token'])."'");
                $iTotalRecords = mysql_num_rows($findItems);


                if($iTotalRecords > 0){
                    while($item = mysql_fetch_assoc($findItems)){
                        $cjr_items[$event['event_token']]['items'][] = array(
                            ''.$item['item_item'].'',
                            ''.$item['item_qty'].'',
                            ''.$item['item_cost'].'',
                            ''.$item['item_total'].'',
                        );
                    }
                }
                if($event['event_cjr'] != 1 && $event['event_status'] != 9){
                    if(empty($deposit_warn)){
                        $deposit_done = true;
                    } else {
                        $deposit_done = false;
                    }
                    $findLabor = mysql_query("SELECT laborer_user_token, laborer_hours_worked, laborer_role, laborer_rate FROM fmo_locations_events_laborers WHERE laborer_event_token='".mysql_real_escape_string($event['event_token'])."'");
                    while($lb = mysql_fetch_assoc($findLabor)) {
                        $cjr_men[$event['event_token']]['men'][] = array(
                            '<span class="badge badge-info">'.$lb['laborer_role'].'</span> - '.name($lb['laborer_user_token']).'',
                            '$'.number_format($lb['laborer_rate'], 2).'/hr',
                            ''.$lb['laborer_hours_worked'].'hrs'
                        );
                    }
                    $cjr['records'][] = array(
                        ''.$event['event_name'].'',
                        ''.date('m-d-Y', strtotime($event['event_date_start'])).'',
                        ''.$event['event_status'].'',
                        ''.$deposit_done.'',
                        ''.$event['event_token'].'',
                    );
                }
            }
        }
        ?>
        <div class="portlet">
            <div class="portlet-title" style="margin-bottom: 0px;">
                <h3>
                    <strong>Completed Jobs Report</strong>  <small class="text-muted">(date range <strong><?php echo date('m/d/Y', strtotime($range[0])); ?> - <?php echo date('m/d/Y', strtotime($range[1])); ?> </strong>)</small>
                    <button class="btn default blue-stripe pull-right btn-xs print printer" data-print="#cjr_print"><i class="fa fa-print" style="font-size: 16px;"></i> Print pretty report</button>
                </h3>
            </div>
            <div class="portlet-body" id="cjr_print">
                <?php
                $pk = 0;
                if(!empty($cjr['records'])){
                    foreach($cjr['records'] as $exs){
                        $pk++;
                        switch($exs[2]){
                            case 1: $status = "New Booking"; $s_btn = "red-stripe"; $s_color = "font-red"; break;
                            case 2: $status = "Confirmed";  $s_btn = "red-stripe"; $s_color = "font-red";  break;
                            case 3: $status = "Left Message";  $s_btn = "red-stripe"; $s_color = "font-red"; break;
                            case 4: $status = "On Hold";  $s_btn = "red-stripe"; $s_color = "font-red";  break;
                            case 5: $status = "Cancelled";  $s_btn = "green-stripe"; $s_color = "font-green";  break;
                            case 9: $status = "Dead Hot Lead";  $s_btn = "red-stripe"; $s_color = "font-red"; break;
                            case 8: $status = "Completed"; $s_btn = "green-stripe"; $s_color = "font-green"; break;
                            default: $status = "On Hold";  $s_btn = "red-stripe"; $s_color = "font-red"; break;
                        }
                        if($exs[3] == true){
                            $color = "font-green";
                            $btns  = "green-stripe";
                            $btn   = "green";
                            $stat  = "Completed";
                            $dis   = NULL;
                        } else {
                            $color = "font-red";
                            $btns  = "red-stripe";
                            $btn   = "red";
                            $stat  = "Incomplete";
                            $dis   = "disabled";
                        }
                        if($exs[2] != 8 && $exs[2] != 5){
                            $dis     = "disabled";
                            $btn     = "red";
                            $s_color = "font-red";
                        }

                        ?>
                        <div id="cjr_h_<?php echo $pk; ?>" class="panel-group" <?php if($pk == 1){echo "style='margin-top: 10px;'";} ?>>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div class="actions pull-right" style="margin-top: -6px; margin-right: -9px">
                                        <a href="javascript:;" class="btn btn-default <?php echo $s_btn; ?> btn-sm">
                                            <i class="fa fa-tag"></i> <span class="hidden-sm hidden-xs">Status:</span> <strong class="<?php echo $s_color; ?>"><?php echo $status; ?></strong></a>
                                        <a href="javascript:;" class="btn btn-default <?php echo $btns; ?> btn-sm">
                                            <i class="fa fa-bank"></i> <span class="hidden-sm hidden-xs">Deposit: </span> <strong class="<?php echo $color; ?>"><?php echo $stat; ?></strong></a>
                                        <a href="javascript:;" class="btn btn-default btn-sm popout" data-pop="event.php?ev=<?php echo $exs[4]; ?>" data-page-title="Reviewing Event">
                                            <i class="fa fa-pencil-square"></i> <span class="hidden-sm hidden-xs">Review</span></a>
                                        <a href="javascript:;" class="btn <?php echo $btn; ?> btn-sm <?php echo $dis; ?> cjr_apr" data-ev="<?php echo $exs[4]; ?>">
                                            <i class="fa fa-external-link-square"></i> <span class="hidden-sm hidden-xs">Approve</span></a>
                                    </div>
                                    <div class="caption">
                                        <h4 class="panel-title">
                                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#cjr_h_<?php echo $pk; ?>" href="#cjr_<?php echo $pk; ?>" aria-expanded="false"><strong><?php echo $exs[1]; ?></strong> - <strong class="<?php echo $color; ?>"><?php echo $exs[0]; ?></strong></a>
                                        </h4>
                                    </div>
                                </div>
                                <div id="cjr_<?php echo $pk; ?>" class="panel-collapse collapse" aria-expanded="true" style="height: 0px;">
                                    <div class="panel-body">
                                        <div class="scroller">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="portlet">
                                                        <div class="portlet-title" style="margin-bottom: 0px;">
                                                            <h4 class="text-center">- <strong>Crewmen</strong> -</h4>
                                                        </div>
                                                        <div class="portlet-body">
                                                            <table class="table table-striped table-hover datatable">
                                                                <thead>
                                                                <tr>
                                                                    <th>Laborer</th>
                                                                    <th>Rate</th>
                                                                    <th class="text-right" style="padding-right: 5px">Hours</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <?php
                                                                foreach($cjr_men[$exs[4]]['men'] as $py){
                                                                    ?>
                                                                    <tr>
                                                                        <td class="bold"><?php echo $py[0]; ?></td>
                                                                        <td><?php echo $py[1]; ?></td>
                                                                        <td class="text-right bold"><?php echo $py[2] ?></td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                                ?>
                                                                </tbody>
                                                            </table>
                                                            <!--
                                                            <div class="task-footer">
                                                                <div class="btn-arrow-link pull-right">
                                                                    <a href="javascript:;">See All Records</a>
                                                                    <i class="icon-arrow-right"></i>
                                                                </div>
                                                            </div> -->
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="portlet">
                                                        <div class="portlet-title" style="margin-bottom: 0px;">
                                                            <h4 class="text-center">- <strong>Service Items</strong> -</h4>
                                                        </div>
                                                        <div class="portlet-body">
                                                            <table class="table table-striped table-hover datatable">
                                                                <thead>
                                                                <tr>
                                                                    <th>Item</th>
                                                                    <th>Quantity</th>
                                                                    <th>Cost</th>
                                                                    <th class="text-right" style="padding-right: 5px">Total</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <?php
                                                                foreach($cjr_items[$exs[4]]['items'] as $py){
                                                                    ?>
                                                                    <tr>
                                                                        <td class="bold"><?php echo $py[0]; ?></td>
                                                                        <td class="font-green bold"><?php echo $py[1]; ?></td>
                                                                        <td class="font-green bold">$<?php echo $py[2] ?></td>
                                                                        <td class="text-right font-green bold">$<?php echo $py[3] ?></td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                                ?>
                                                                </tbody>
                                                            </table>
                                                            <!--
                                                            <div class="task-footer">
                                                                <div class="btn-arrow-link pull-right">
                                                                    <a href="javascript:;">See All Records</a>
                                                                    <i class="icon-arrow-right"></i>
                                                                </div>
                                                            </div> -->
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="portlet">
                                                        <div class="portlet-title" style="margin-bottom: 0px;">
                                                            <h4 class="text-center">- <strong>Payments</strong> -</h4>
                                                        </div>
                                                        <div class="portlet-body">
                                                            <table class="table table-striped table-hover datatable">
                                                                <thead>
                                                                <tr>
                                                                    <th>Payment Date</th>
                                                                    <th>Type</th>
                                                                    <th>By</th>
                                                                    <th class="text-right" style="padding-right: 5px">Amount</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <?php
                                                                foreach($cjr_payment[$exs[4]]['payments'] as $py){
                                                                    ?>
                                                                    <tr>
                                                                        <td class="bold"><?php echo $py[0]; ?></td>
                                                                        <td class="font-green bold"><?php echo $py[1]; ?></td>
                                                                        <td><?php echo $py[2]; ?></td>
                                                                        <td class="text-right font-green bold"><?php echo $py[3] ?></td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                                ?>
                                                                </tbody>
                                                            </table>
                                                            <!--
                                                            <div class="task-footer">
                                                                <div class="btn-arrow-link pull-right">
                                                                    <a href="javascript:;">See All Records</a>
                                                                    <i class="icon-arrow-right"></i>
                                                                </div>
                                                            </div> -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
                        <strong>No jobs available to view!</strong> Check back later.
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        <script type="text/javascript">
            var bk = $('.datatable').dataTable( {
                "order": [[ 0, "desc" ]],
                "bFilter" : false,
                "bLengthChange": false,
                "bPaginate": false,
                "info": true,
                "saveState": true
            } );
            $('.cjr_apr').unbind('click').on('click', function() {
                var ev = $(this).attr('data-ev');
                $(this).closest('.panel-group').remove();
                $.ajax({
                    url: 'assets/app/update_settings.php?update=event_fly',
                    type: 'POST',
                    data: {
                        name: 'event_cjr',
                        value: 1,
                        pk: ev
                    },
                    success: function(s){
                        toastr.success("<strong>Logan says:</strong><br/>Event has been approved & record of you doing so has been recorded on event's timeline.");
                    },
                    error: function(s){
                        toastr.error("<strong>Logan says:</strong><br/>Something didn't work properly. Try again? (I know you want too).");
                    }
                });
            });
        </script>
        <?php

        /*
         *  End Completed Jobs Report
         */
    } elseif($_GET['ty'] == 'ext') {
        /*
         *  Expenses & Deposits Report
         */
        $range       = explode(" - ", $_POST['ext']);
        $undeposited = array();
        $deposits    = array();
        $totals      = array();
        $events      = mysql_query("SELECT event_id, event_token, event_user_token, event_name, event_date_start, event_location_token, event_cjr, event_status FROM fmo_locations_events WHERE event_location_token='".mysql_real_escape_string($_GET['luid'])."' AND (event_date_start>='".mysql_real_escape_string($range[0])."' AND event_date_end<='".mysql_real_escape_string($range[1])."') AND NOT event_status=0 AND NOT event_status=9 ORDER BY event_date_start ASC");
        if(mysql_num_rows($events) > 0){
            while($event = mysql_fetch_assoc($events)){
                $payments = mysql_query("SELECT payment_user_token, payment_transaction_id, payment_type, payment_amount, payment_payout_reason, payment_payout_amount, payment_detail, payment_deposit_token, payment_timestamp, payment_by_user_token FROM fmo_locations_events_payments WHERE payment_event_token='".mysql_real_escape_string($event['event_token'])."'");
                $total          = 0;
                if(mysql_num_rows($payments) > 0){
                    while($payment = mysql_fetch_assoc($payments)){
                        $void = explode(" - ", $payment['payment_type']);
                        if($void[1] != "VOIDED" && $void[1] != 'Booking Fee'){
                            if(empty($payment['payment_deposit_token'])) {
                                if($void[0] == 'Cash' || $void[0] == 'Check'){
                                    $undeposited['payment'][] = array(
                                        ''.date('m-d-Y', strtotime($payment['payment_timestamp'])).'',
                                        '<a class="load_page" data-href="assets/pages/event.php?ev='.$event['event_token'].'" data-page-title="'.$event['event_name'].'"><strong>'.$event['event_name'].'</strong> - <strong>ID #</strong>: '.$event['event_id'].'</a>',
                                        ''.$payment['payment_type'].'',
                                        '$'.number_format($payment['payment_amount'], 2).'',
                                        '<input type="number" class="form-control input-sm deposit-amount pull-right" data-id="'.$payment['payment_transaction_id'].'" placeholder="$0.00" value="'.$payment['payment_payout_amount'].'" style="width: 100px;">',
                                        '<input type="text" class="form-control input-sm deposit-reason pull-right" data-id="'.$payment['payment_transaction_id'].'" placeholder="Reason" value="'.$payment['payment_payout_reason'].'">',
                                        '<button class="btn btn-xs default red-stripe select-dpt" id="'.$payment['payment_transaction_id'].'" data-id="'.$payment['payment_transaction_id'].'" value="'.number_format($payment['payment_amount'] - $payment['payment_payout_amount'], 2, '.', '').'"">Select $<span class="amt">'.number_format($payment['payment_amount'] - $payment['payment_payout_amount'], 2).'</span> for deposit</button>'
                                    );
                                }
                            }
                        }
                    }
                }

            }
        }

        $ex = array();
        $expenses = mysql_query("SELECT expense_desc, expense_name, expense_date, expense_type, expense_reason, expense_amount, expense_by FROM fmo_locations_expenses WHERE expense_location_token='".mysql_real_escape_string($_GET['luid'])."' AND (expense_date>='".mysql_real_escape_string($range[0])."' AND expense_date<='".mysql_real_escape_string($range[1])."') ORDER BY expense_date ASC");
        if(mysql_num_rows($expenses) > 0){
            while($expense = mysql_fetch_assoc($expenses)){
                $ex['expenses'][] = array(
                    ''.date('m-d-Y', strtotime($expense['expense_date'])).'',
                    '<strong>'.$expense['expense_reason'].'</strong> - '.sentence_case($expense['expense_desc']).'',
                    ''.name($expense['expense_by']).'',
                    ''.$expense['expense_type'].'',
                    '$'.number_format($expense['expense_amount'], 2).'',
                );
            }
        }

        $de = array();
        $deposit = mysql_query("SELECT deposit_id, deposit_token, deposit_teller, deposit_comments, deposit_amount, deposit_by_user_token, deposit_timestamp FROM fmo_locations_deposits WHERE deposit_location_token='".mysql_real_escape_string($_GET['luid'])."' AND (deposit_timestamp>='".mysql_real_escape_string($range[0])."' AND deposit_timestamp<='".mysql_real_escape_string($range[1])."')");
        if(mysql_num_rows($deposit) > 0){
            while($dpts = mysql_fetch_assoc($deposit)){
                $payments    = mysql_query("SELECT payment_type, payment_payout_amount, payment_amount, payment_event_token, payment_deposit_token FROM fmo_locations_events_payments WHERE payment_deposit_token='".mysql_real_escape_string($dpts['deposit_token'])."'");
                if(mysql_num_rows($payments) > 0){
                    $totalPayout = 0;
                    while($payment = mysql_fetch_assoc($payments)){
                        $events  = mysql_query("SELECT event_name, event_id FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($payment['payment_event_token'])."'");
                        $totalPayout+=$payment['payment_payout_amount'];
                        if(mysql_num_rows($events) > 0){
                            while($event = mysql_fetch_assoc($events)){
                                $deposits[$payment['payment_deposit_token']]['payments'][] = array(
                                    '<strong>'.$event['event_name'].'</strong> - <strong>ID #</strong>: '.$event['event_id'],
                                    ''.$payment['payment_type'].'',
                                    '$'.number_format($payment['payment_payout_amount'], 2).'',
                                    '$'.number_format($payment['payment_amount'] - $payment['payment_payout_amount'], 2).''
                                );
                            }
                        }
                    }
                }

                $de['deposits'][] = array(
                    ''.date('m-d-Y', strtotime($dpts['deposit_timestamp'])).'',
                    '<strong>'.$dpts['deposit_id'].'</strong>',
                    ''.name($dpts['deposit_by_user_token']).'',
                    ''.$dpts['deposit_token'].'',
                    '$'.number_format($dpts['deposit_amount'], 2).'',
                    ''.$dpts['deposit_by_user_token'].'',
                    ''.$dpts['deposit_teller'].'',
                    ''.$dpts['deposit_comments'].'',
                    '$'.number_format($totalPayout, 2).'',
                );

            }
        }

        ?>
        <style type="text/css">
        </style>
        <div id="print_dp">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet">
                        <div class="portlet-title" style="margin-bottom: 0px;">
                            <h3 class="font-red">
                                <strong><i class="fa fa-exclamation-triangle"></i> UNDEPOSITED FUNDS</strong> - <?php echo locationName($_GET['luid']); ?>

                                <button class="btn default blue-stripe pull-right btn-xs print printer" data-print="#print_dp"><i class="fa fa-print" style="font-size: 16px;"></i> Print pretty report</button>
                            </h3>
                        </div>
                        <div class="portlet-body">
                            <table class="table table-striped table-hover datatable" id="undpt">
                                <thead>
                                <tr>
                                    <th>Event Date</th>
                                    <th>Event Name & ID</th>
                                    <th class="text-right" style="padding-right: 5px">Payment Type</th>
                                    <th class="text-right" style="padding-right: 5px">Total</th>
                                    <th class="text-right" style="padding-right: 5px">Payout Amount</th>
                                    <th class="text-right" style="padding-right: 5px">Payout Reason</th>
                                    <th class="text-right" style="padding-right: 5px">Select for Deposit</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach($undeposited['payment'] as $dpt){
                                    ?>
                                    <tr>
                                        <td><?php echo $dpt[0]; ?></td>
                                        <td><?php echo $dpt[1]; ?></td>
                                        <td class="text-right"><?php echo $dpt[2]; ?></td>
                                        <td class="text-right font-green"><strong><?php echo $dpt[3]; ?></strong></td>
                                        <td class="text-right"><?php echo $dpt[4]; ?></td>
                                        <td class="text-right"><?php echo $dpt[5]; ?></td>
                                        <td class="text-right font-red"><?php echo $dpt[6]; ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                            <!--
                            <div class="task-footer">
                                <div class="btn-arrow-link pull-right">
                                    <a href="javascript:;">See All Records</a>
                                    <i class="icon-arrow-right"></i>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet red box">
                        <div class="portlet-title">
                            <div class="caption">
                                <small>Make deposit(s):</small>
                            </div>
                        </div>
                        <div class="portlet-body" id="start_b_dp">
                            <center><h4>Please select an amount to be deposited for tools to appear here.</h4></center>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">
                <div class="portlet">
                    <div class="portlet-title" style="margin-bottom: 0px;">
                        <div class="caption">
                            <small><strong>Expenses</strong> - <?php echo locationName($_GET['luid']); ?></small>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <table class="table table-striped table-hover datatable" id="exp">
                            <thead>
                            <tr>
                                <th>Expense Date</th>
                                <th>Expense Reasoning</th>
                                <th class="text-right" style="padding-right: 5px">By</th>
                                <th class="text-right" style="padding-right: 5px">Type</th>
                                <th class="text-right" style="padding-right: 5px">Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach($ex['expenses'] as $exs){
                                ?>
                                <tr>
                                    <td><?php echo $exs[0]; ?></td>
                                    <td><?php echo substr($exs[1], 0, 55)." ..."; ?></td>
                                    <td class="text-right"><?php $name = explode(" ", $exs[2]); echo $name[0]." ".substr($name[1], 0, 1)."."; ?></td>
                                    <td class="text-right"><?php echo $exs[3]; ?></td>
                                    <td class="text-right font-red"><strong><?php echo $exs[4]; ?></strong></td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                        <!--
                        <div class="task-footer">
                            <div class="btn-arrow-link pull-right">
                                <a href="javascript:;">See All Records</a>
                                <i class="icon-arrow-right"></i>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">
                <div class="portlet">
                    <div class="portlet-title" style="margin-bottom: 0px;">
                        <div class="caption">
                            <small><strong>Deposits</strong> - <?php echo locationName($_GET['luid']); ?></small>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <?php
                        $pk = 0;
                        if(!empty($de['deposits'])){
                            foreach($de['deposits'] as $exs){
                                $pk++;
                                ?>
                                <div id="deposit_h_<?php echo $pk; ?>" class="panel-group" <?php if($pk == 1){echo "style='margin-top: 10px;'";} ?>>
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <div class="actions pull-right hidden" style="margin-top: -6px; margin-right: -9px">
                                                <a href="javascript:;" class="btn btn-default btn-sm edit" data-edit="cs_<?php echo $exs[1]; ?>">
                                                    <i class="fa fa-pencil"></i> <span class="hidden-sm hidden-md hidden-xs">Edit</span> </a>
                                            </div>
                                            <div class="caption">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#estimate_h_<?php echo $pk; ?>" href="#estimate_<?php echo $pk; ?>" aria-expanded="false"><strong>Deposit Number #<?php echo $exs[1]; ?></strong></a>
                                                </h4>
                                            </div>
                                        </div>
                                        <div id="estimate_<?php echo $pk; ?>" class="panel-collapse collapse" aria-expanded="true" style="height: 0px;">
                                            <div class="panel-body">
                                                <div class="scroller">
                                                    <div class="row">
                                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                                            <h4> (#<?php echo $exs[1]; ?>) <?php echo $exs[0]; ?> - <strong><?php $name = explode(" ", $exs[2]); echo $name[0]." ".substr($name[1], 0, 1)."."; ?></strong> deposited <strong class="text-success"><?php echo $exs[4]; ?></strong></h4>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4 col-sm-12 col-xs-12">
                                                            <div class="well" style="margin-top: 11px; margin-bottom: 8px;">
                                                                <?php
                                                                if(!empty($exs[6])){
                                                                    ?>
                                                                    <img src="<?php echo $exs[6]; ?>" class="img-thumbnail"/>
                                                                    <?php
                                                                } else {
                                                                    ?>
                                                                    <address>

                                                                        <h5 class="text-center" style="margin: 0;">Teller ticket will appear here once it has been uploaded.</h5>
                                                                    </address>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </div>
                                                            <?php
                                                            if(empty($exs[6])){
                                                                ?>
                                                                <button class="btn btn-block default red-stripe fire" data-fire="teller" data-uuid="<?php echo $exs[5]; ?>" data-id="<?php echo $exs[3]; ?>">Request Teller Ticket</button>
                                                                <?php
                                                            }
                                                            ?>
                                                        </div>
                                                        <div class="col-md-8 col-sm-12 col-xs-12">
                                                            <table class="table table-striped table-hover datatable" style="margin: 0 !important;">
                                                                <thead>
                                                                <tr role="row" class="heading">
                                                                    <th>
                                                                        Event
                                                                    </th>
                                                                    <th class="text-right" style="padding-right: 5px">
                                                                        Type
                                                                    </th>
                                                                    <th class="text-right" style="padding-right: 5px">
                                                                        Payout
                                                                    </th>
                                                                    <th class="text-right" style="padding-right: 5px">
                                                                        Amount
                                                                    </th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <?php
                                                                foreach($deposits[$exs[3]]['payments'] as $dp){
                                                                    ?>
                                                                    <tr>
                                                                        <td><?php echo $dp[0]; ?></td>
                                                                        <td class="text-right text-success"><?php echo $dp[1]; ?></td>
                                                                        <td class="text-right text-danger"><strong><?php echo $dp[2]; ?></strong></td>
                                                                        <td class="text-right text-success"><strong><?php echo $dp[3]; ?></strong></td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                                ?>
                                                                </tbody>
                                                            </table>
                                                            <?php
                                                            if(!empty($exs[7])){
                                                                ?>
                                                                <p><strong>Comments:</strong><br/><?php echo $exs[7]; ?></p>
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
                                <?php
                            }
                        } else {
                            ?>
                            <div class="alert alert-warning alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                                <strong>No estimates available to view!</strong> Add new estimates to see them appear here.
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            var bk = $('.datatable').dataTable( {
                "order": [[ 0, "desc" ]],
                "bFilter" : false,
                "bLengthChange": false,
                "bPaginate": false,
                "info": true,
                "saveState": true
            } );
            $('.printer').unbind().on('click', function () {
                var settings = bk.fnSettings();
                settings._iDisplayLength = 1000;
                bk.fnDraw();
            });
            $('.fire').click(function(f){
                var fire = $(this).attr('data-fire');
                var dpt  = $(this).attr('data-id');
                var uuid = $(this).attr('data-uuid');
                $.ajax({
                    url: 'assets/app/texting.php?txt='+fire,
                    type: 'POST',
                    data: {
                        dpt: dpt,
                        uuid: uuid
                    },
                    success: function(f){
                        toastr.success("<strong>Logan says:</strong><br/>Request was sent to the person who made the deposit. ");
                    },
                    error: function(f){
                        toastr.error("<strong>Logan says:</strong><br/>Oops.. that didnt work correctly.");
                    }
                })
            });
            $('.deposit-amount').on('change', function() {
                var id     = $(this).attr('data-id');
                var amount = $(this).val();
                $.ajax({
                    url: 'assets/app/update_settings.php?update=deposit_amount',
                    type: 'POST',
                    data: {
                        id: id,
                        amount: amount
                    },
                    success: function(s){
                        $('#'+id).html("Select $"+s+" for deposit");
                        $('#'+id).val(s.replace(/,/g, ''));
                        toastr.info("<strong>Logan says:</strong><br/>I updated that for you.");
                    },
                    error: function(e){

                    }
                });
            });
            $('.deposit-reason').on('change', function() {
                var id     = $(this).attr('data-id');
                var reason = $(this).val();
                $.ajax({
                    url: 'assets/app/update_settings.php?update=deposit_reason',
                    type: 'POST',
                    data: {
                        id: id,
                        reason: reason
                    },
                    success: function(s){

                        toastr.info("<strong>Logan says:</strong><br/>I updated that for you.");
                    },
                    error: function(e){

                    }
                });
            });

            var real_amt = 0;
            var deposits = 0;
            var payments = [];
            $('.select-dpt').click(function() {
                var thingy =   $(this).attr('data-id');
                var id     =   $(this).attr('data-id');
                var amt    =   $(this).val();
                real_amt   += +$(this).val();
                deposits++;
                payments.push(id);

                console.log(real_amt);

                $(this).closest('td').append("<button class='btn btn-xs default red-stripe remove-dpt' data-id='"+id+"' value='"+amt+"'><i class='fa fa-times'></i> Unselect</button>");
                $('.remove-dpt').unbind('click');
                $('.remove-dpt').click(function(){
                    var pid = $(this).attr('data-id');
                    payments.splice(pid, 1);
                    real_amt -= $(this).val();
                    deposits--;
                    $(this).remove();
                    $('#'+pid).addClass('red-stripe');
                    $('#'+pid).removeClass('green-stripe');
                    $('#'+pid).removeAttr('disabled');
                    $('#'+pid).html('Select '+$(this).val()+' for deposit.');

                    $('.bulk-deposit-amount').html(real_amt.toFixed(2));
                    $('.bulk-deposit-count').html(deposits);
                    $('.bulk-deposit-submit').val(payments);

                    console.log(real_amt);

                    if(real_amt <= 0){
                        $('#start_b_dp').html('<h4 class="text-center">Please select an amount to be deposited for tools to appear here.</h4>');
                    }

                });

                $(this).html("<i class='fa fa-check'></i> Selected $"+$(this).val());
                $(this).removeClass("red-stripe");
                $(this).addClass("green-stripe");
                $(this).prop('disabled', true);
                if($('#bulk-deposit').length){
                    $('.bulk-deposit-amount').html(real_amt.toFixed(2));
                    $('.bulk-deposit-count').html(deposits);
                    $('.bulk-deposit-submit').val(payments);
                } else {
                    $('#start_b_dp').html("");
                    $('#start_b_dp').append("<div id='bulk-deposit'> </div>");
                    $('#bulk-deposit').append("<h3 class='text-center'>Total amount selected: $<strong class='bulk-deposit-amount text-success'>"+real_amt.toFixed(2)+"</strong></h3>");
                    $('#bulk-deposit').append("<br/><button class='btn red btn-block btn-xl bulk-deposit-submit' value='"+payments+"'>Deposit $<strong class='bulk-deposit-amount'>"+real_amt.toFixed(2)+"</strong> total from <strong class='bulk-deposit-count'>"+deposits+"</strong> payments</button>")

                    $('.bulk-deposit-submit').click(function() {
                        $(this).prop('disabled', true);
                        var ids = $(this).val();
                        $.ajax({
                            url: 'assets/app/add_setting.php?setting=deposit&luid=<?php echo $_GET['luid']; ?>&cuid=<?php echo $_SESSION['cuid']; ?>',
                            type: 'POST',
                            data: {
                                d: '<?php echo struuid(true); ?>',
                                ids: ids
                            },
                            success: function(s){
                                $.ajax({
                                    url: 'assets/pages/sub/reports_master.php?luid=<?php echo $_GET['luid']; ?>&cuid=<?php echo $_SESSION['cuid']; ?>&show=exp_dp',
                                    type: 'POST',
                                    data: {
                                        type: 'sales',
                                        ext: "<?php echo date('Y-m-d', strtotime($range[0])); ?> - <?php echo date('Y-m-d', strtotime($range[1])); ?>"
                                    },
                                    success: function(data) {
                                        $(document).find('#reports-content').html(data);
                                    },
                                    error: function(e){

                                    }
                                });
                                toastr.info("<strong>Logan says:</strong><br/>I have added those payments to this locations deposits.");
                            },
                            error: function(e){

                            }
                        });
                    });
                }
            });
        </script>
        <?php

        /*
         *  End Expenses & Deposits Report
         */
    } elseif($_GET['ty'] == 'svk') {
        /*
         *  Sales Summary Report
         */
        $sales_chart['net'][date('Y')] = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        $sales_chart['deposits'][date('Y')] = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        $sales_chart['expss'][date('Y')] = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        $range       = explode(" - ", $_POST['ext']);
        $sales       = array();
        $totals      = array();
        $events      = mysql_query("SELECT event_id, event_token, event_user_token, event_name, event_date_start, event_location_token, event_cjr, event_status FROM fmo_locations_events WHERE event_location_token='".mysql_real_escape_string($_GET['luid'])."' AND (event_date_start>='".mysql_real_escape_string($range[0])."' AND event_date_end<='".mysql_real_escape_string($range[1])."') AND NOT event_status=0 AND NOT event_status=9 ORDER BY event_date_start ASC");
        if(mysql_num_rows($events) > 0){
            while($event = mysql_fetch_assoc($events)){
                $payments = mysql_query("SELECT payment_user_token, payment_transaction_id, payment_type, payment_amount, payment_payout_reason, payment_payout_amount, payment_detail, payment_deposit_token, payment_timestamp, payment_by_user_token FROM fmo_locations_events_payments WHERE payment_event_token='".mysql_real_escape_string($event['event_token'])."'");
                $total          = 0;
                if(mysql_num_rows($payments) > 0){
                    while($payment = mysql_fetch_assoc($payments)){
                        $void = explode(" - ", $payment['payment_type']);
                        if($void[1] != "VOIDED" && $void[1] != 'Booking Fee'){
                            if(empty($payment['payment_deposit_token'])) {
                                if($void[0] == 'Cash' || $void[0] == 'Check'){
                                    $deposit_warn = '<strong class="font-red"><i class="fa fa-warning"></i> UNDEPOSITED FUNDS</strong>';
                                } else {
                                    $deposit_warn = NULL;
                                }
                            } else {
                                $deposit_warn = NULL;
                            }
                        } else {
                            $deposit_warn = NULL;
                        }
                    }
                } else {
                    $deposit_warn = NULL;
                }

                $location = mysql_fetch_array(mysql_query("SELECT location_sales_tax FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'"));

                if(!empty($location['location_sales_tax'])){
                    $tax = $location['location_sales_tax'];
                } else {$tax = 0;}

                $findItems = mysql_query("SELECT item_item, item_total, item_taxable, item_redeemable, item_prepay, item_desc, item_commission FROM fmo_locations_events_items WHERE item_event_token='".mysql_real_escape_string($event['event_token'])."'");
                $iTotalRecords = mysql_num_rows($findItems);

                $findPaid = mysql_query("SELECT payment_amount, payment_type FROM fmo_locations_events_payments WHERE payment_event_token='".mysql_real_escape_string($event['event_token'])."'");
                $bTotalRecords = mysql_num_rows($findPaid);

                $total = array();
                $total['sub_total'] = 0.00;
                $total['tax']       = 0.00;
                $total['cc_fees']   = 0.00;
                $total['total']     = 0.00;
                $total['dis']       = 0.00;
                $total['ppay']      = 0.00;
                $total['paid']      = 0.00;
                $total['unpaid']    = 0.00;
                $totals['code']      = NULL;
                if($iTotalRecords > 0){
                    while($item = mysql_fetch_assoc($findItems)){
                        if($item['item_item'] != 'Booking Fee'){

                            if($item['item_total'] > 0){
                                $total['sub_total'] += $item['item_total'];
                            } else {
                                $total['fake_sub_total'] += $item['item_total'];
                            }

                            if($item['item_taxable'] == 1){
                                $total['tax']      += $item['item_total'] * $tax;
                                $totals['taxable']  += $item['item_total'];
                            } else {
                                $total['tax']   += 0.00;
                            }

                            if($item['item_redeemable'] > 0){
                                $totals['discounts'] += $item['item_total'];
                                $totals['prepaid']   += $item['item_prepay'];
                                $totals['code']      =  $item['item_desc'];
                                $total['dis']        += $item['item_total'];
                                $total['ppay']       += $item['item_prepay'];
                            }

                            if($item['item_commission'] > 0){
                                $totals['coms'] += $item['item_total'];
                            } else {
                                $totals['coms'] += 0.00;
                            }
                        } else {
                            $totals['bfs'] += $item['item_total'];
                        }
                    }
                    $total['total'] = number_format($total['sub_total'], 2, '.', '');
                } else {
                    $total['total']     = 0.00;
                    $total['sub_total'] = 0.00;
                }

                if($bTotalRecords > 0){
                    while($paid = mysql_fetch_assoc($findPaid)){
                        $void = explode(" - ", $paid['payment_type']);
                        if($void[1] != 'VOIDED' && $void[0] != 'Invoice'){
                            $total['paid'] += $paid['payment_amount'];
                            if($void[0] == 'Credit/Debt' && $void[1] != 'Booking Fee'){
                                $total['cc_fees'] += ($paid['payment_amount'] / 1.03) * .03;
                            }
                        }
                    }
                }


                if($event['event_status'] == 5){
                    $extra = "<span class='badge badge-danger badge-roundless'>Cancelled</span>";
                } else {$extra = NULL;}
                $sales['records'][] = array(
                    ''.date('m-d-Y', strtotime($event['event_date_start'])).'',
                    ''.$event['event_name'].' [ <span class="text-muted"><strong>ID #:</strong> '.$event['event_id'].'</span> ] '.$extra,
                    ''.$deposit_warn.'',
                    ''.$totals['code'].'',
                    '$'.number_format($total['total'] + $total['fake_sub_total'] + $total['cc_fees'], 2).'',
                    ''.$event['event_token'].''
                );

                $totals['gross']     += $total['total'] + $total['fake_sub_total'] + $total['cc_fees'];
                $totals['tax']       += $total['tax'];
                $sales_chart['net'][date('Y')][date('n', strtotime($event['event_date_start']))] += $total['total'] + $total['tax'] + $total['fake_sub_total'] + $total['cc_fees'] + $total['ppay'];
            }

        }

        $ex = array();
        $expenses = mysql_query("SELECT expense_desc, expense_name, expense_date, expense_type, expense_reason, expense_amount, expense_by FROM fmo_locations_expenses WHERE expense_location_token='".mysql_real_escape_string($_GET['luid'])."' AND (expense_date>='".mysql_real_escape_string($range[0])."' AND expense_date<='".mysql_real_escape_string($range[1])."') ORDER BY expense_date ASC");
        if(mysql_num_rows($expenses) > 0){
            while($expense = mysql_fetch_assoc($expenses)){
                $ex['expenses'][] = array(
                    ''.date('m-d-Y', strtotime($expense['expense_date'])).'',
                    '<strong>'.$expense['expense_reason'].'</strong> - '.sentence_case($expense['expense_desc']).'',
                    ''.name($expense['expense_by']).'',
                    ''.$expense['expense_type'].'',
                    '$'.number_format($expense['expense_amount'], 2).'',
                );
                $sales_chart['expss'][date('Y')][date('n', strtotime($expense['expense_date']))] += $expense['expense_amount'];
            }
        }

        $de = array();
        $deposit = mysql_query("SELECT deposit_id, deposit_token, deposit_teller, deposit_comments, deposit_amount, deposit_by_user_token, deposit_timestamp FROM fmo_locations_deposits WHERE deposit_location_token='".mysql_real_escape_string($_GET['luid'])."' AND (deposit_timestamp>='".mysql_real_escape_string($range[0])."' AND deposit_timestamp<='".mysql_real_escape_string($range[1])."')");
        if(mysql_num_rows($deposit) > 0){
            while($dpts = mysql_fetch_assoc($deposit)){
                $payments    = mysql_query("SELECT payment_payout_amount FROM fmo_locations_events_payments WHERE payment_deposit_token='".mysql_real_escape_string($dpts['deposit_token'])."'");
                if(mysql_num_rows($payments) > 0){
                    $totalPayout = 0;
                    while($payment = mysql_fetch_assoc($payments)){
                        $totalPayout+=$payment['payment_payout_amount'];
                    }
                }

                $de['deposits'][] = array(
                    ''.date('m-d-Y', strtotime($dpts['deposit_timestamp'])).'',
                    '<strong>'.$dpts['deposit_id'].'</strong>',
                    ''.name($dpts['deposit_by_user_token']).'',
                    ''.$dpts['deposit_token'].'',
                    '$'.number_format($dpts['deposit_amount'], 2).'',
                    ''.$dpts['deposit_by_user_token'].'',
                    ''.$dpts['deposit_teller'].'',
                    ''.$dpts['deposit_comments'].'',
                    '$'.number_format($totalPayout, 2).'',
                );

                $sales_chart['deposits'][date('Y')][date('n', strtotime($dpts['deposit_timestamp']))] += $dpts['deposit_amount'];
            }
        }
        ?>
        <style type="text/css">
            #sales_print_wrapper {
                margin-top: -42px;
            }
        </style>
        <div class="portlet">
            <div class="portlet-title" style="margin-bottom: 0px;">
                <h3>
                    <strong>Sales Report</strong>  <small class="text-muted">(date range <strong><?php echo date('m/d/Y', strtotime($range[0])); ?> - <?php echo date('m/d/Y', strtotime($range[1])); ?> </strong>)</small>
                    <button class="btn default blue-stripe pull-right btn-xs print printer" data-print="#sales_printer"><i class="fa fa-print" style="font-size: 16px;"></i> Print pretty report</button>
                </h3>
            </div>
            <div class="portlet-body" id="sales_printer">
                <div class="row">
                    <div class="col-md-12">
                        <div class="chart" id="sales_chart" style="height: 170px;">
                        </div>
                    </div>
                </div>
                <hr/>
                <h3>Table o' details</h3>
                <table class="table table-striped table-hover datatable" id="sales_print">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Event Name & ID</th>
                        <th></th>
                        <th class="text-right" style="padding-right: 5px">Promotion Code</th>
                        <th class="text-right" style="padding-right: 5px">Transaction Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach($sales['records'] as $items){
                        ?>
                        <tr style="cursor: pointer;" class="popout" data-pop="event.php?ev=<?php echo $items[5]; ?>" data-page-title="Reviewing Event">
                            <td class="bold"><?php echo $items[0]; ?></td>
                            <td><strong>EVENT:</strong> <?php echo $items[1]; ?></td>
                            <td class="text-right"><?php echo $items[2]; ?></td>
                            <td class="text-right"><?php echo $items[3]; ?></td>
                            <td class="text-right font-green"><strong><?php echo $items[4]; ?></strong></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>

                <div class="invoice">
                    <div class="row">
                        <div class="col-xs-4">
                            <small>
                                <br/><br/>
                                <strong class="text-danger">* NOTE:</strong><br/>
                                Total Discounts You Gave: <span style="display: inline" class="text-danger bold">$<?php echo number_format($totals['discounts'], 2); ?></span>
                                <br/>
                                <strong>Booking fees are paid to For Movers Only&trade;, they show on a customers receipt, but are deducted from your companies sales report.</strong>
                                <br/>
                                Total Booking Fees Collected:  <span style="display: inline" class="text-danger bold">$-<?php echo number_format($totals['bfs'], 2); ?></span>
                                <br/>

                            </small>

                        </div>
                        <div class="col-xs-8 invoice-block">
                            <ul class="list-unstyled amounts">
                                <li>
                                </li>
                                <li>
                                    <small class="bold">(<strong>$<?php echo number_format($totals['coms'], 2); ?></strong> commissionable)</small>  Sub Total: <h3 style="display: inline;" class="text-success bold">$<?php echo number_format($totals['gross'], 2); ?></h3>
                                </li>
                                <li>
                                    <small class="bold">(<strong>$<?php echo number_format($totals['taxable'], 2); ?></strong> taxable)</small> Taxes: <h3 style="display: inline;" class="text-success bold">$<?php echo number_format($totals['tax'], 2); ?></h3>
                                </li>
                                <li>
                                    Prepaid: <h3 style="display: inline" class="text-success bold">$<?php echo number_format($totals['prepaid'], 2); ?></h3>
                                </li>
                                <li>
                                    NET: <h3 style="display: inline" class="text-success bold">$<?php echo number_format(($totals['gross'] + $totals['tax']) + $totals['prepaid'], 2); ?></h3>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <table class="table table-striped table-hover datatable">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Expense Reasoning</th>
                        <th class="text-right" style="padding-right: 5px">Expense By</th>
                        <th class="text-right" style="padding-right: 5px">Expense Type</th>
                        <th class="text-right" style="padding-right: 5px">Expense Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach($ex['expenses'] as $exs){
                        ?>
                        <tr>
                            <td><?php echo $exs[0]; ?></td>
                            <td><?php echo $exs[1]; ?></td>
                            <td class="text-right"><?php echo $exs[2]; ?></td>
                            <td class="text-right"><?php echo $exs[3]; ?></td>
                            <td class="text-right font-red"><strong><?php echo $exs[4]; ?></strong></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>

                <table class="table table-striped table-hover datatable">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Deposit ID by Depositor</th>
                        <th class="text-right" style="padding-right: 5px">Deposit Payout</th>
                        <th class="text-right" style="padding-right: 5px">Deposit Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach($de['deposits'] as $exs){
                        ?>
                        <tr>
                            <td><?php echo $exs[0]; ?></td>
                            <td>#<strong><?php echo $exs[1]; ?></strong> by <?php echo $exs[2]; ?></td>
                            <td class="text-right"><?php echo $exs[8] ?></td>
                            <td class="text-right"><?php echo $exs[4]; ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
        <script type="text/javascript">
            var bk = $('.datatable').dataTable( {
                "order": [[ 0, "asc" ]],
                "bFilter" : true,
                "bLengthChange": false,
                "bPaginate": false,
                "info": true
            } );
            $('.printer').unbind().on('click', function () {
                var settings = bk.fnSettings();
                settings._iDisplayLength = 1000;
                bk.fnDraw();
            });
            var data = <?php echo json_encode($sales_chart) ?>;
            var pageviews = [
                [1, data.net[<?php echo date('Y'); ?>][1]],
                [2, data.net[<?php echo date('Y'); ?>][2]],
                [3, data.net[<?php echo date('Y'); ?>][3]],
                [4, data.net[<?php echo date('Y'); ?>][4]],
                [5, data.net[<?php echo date('Y'); ?>][5]],
                [6, data.net[<?php echo date('Y'); ?>][6]],
                [7, data.net[<?php echo date('Y'); ?>][7]],
                [8, data.net[<?php echo date('Y'); ?>][8]],
                [9, data.net[<?php echo date('Y'); ?>][9]],
                [10, data.net[<?php echo date('Y'); ?>][10]],
                [11, data.net[<?php echo date('Y'); ?>][11]],
                [12, data.net[<?php echo date('Y'); ?>][12]]
            ];
            var deposits = [
                [1, data.deposits[<?php echo date('Y'); ?>][1]],
                [2, data.deposits[<?php echo date('Y'); ?>][2]],
                [3, data.deposits[<?php echo date('Y'); ?>][3]],
                [4, data.deposits[<?php echo date('Y'); ?>][4]],
                [5, data.deposits[<?php echo date('Y'); ?>][5]],
                [6, data.deposits[<?php echo date('Y'); ?>][6]],
                [7, data.deposits[<?php echo date('Y'); ?>][7]],
                [8, data.deposits[<?php echo date('Y'); ?>][8]],
                [9, data.deposits[<?php echo date('Y'); ?>][9]],
                [10, data.deposits[<?php echo date('Y'); ?>][10]],
                [11, data.deposits[<?php echo date('Y'); ?>][11]],
                [12, data.deposits[<?php echo date('Y'); ?>][12]]
            ];
            var expenses = [
                [1, data.expss[<?php echo date('Y'); ?>][1]],
                [2, data.expss[<?php echo date('Y'); ?>][2]],
                [3, data.expss[<?php echo date('Y'); ?>][3]],
                [4, data.expss[<?php echo date('Y'); ?>][4]],
                [5, data.expss[<?php echo date('Y'); ?>][5]],
                [6, data.expss[<?php echo date('Y'); ?>][6]],
                [7, data.expss[<?php echo date('Y'); ?>][7]],
                [8, data.expss[<?php echo date('Y'); ?>][8]],
                [9, data.expss[<?php echo date('Y'); ?>][9]],
                [10, data.expss[<?php echo date('Y'); ?>][10]],
                [11, data.expss[<?php echo date('Y'); ?>][11]],
                [12, data.expss[<?php echo date('Y'); ?>][12]]
            ];


            var plot = $.plot($("#sales_chart"), [{
                data: pageviews,
                label: "<?php echo date('Y'); ?> NET Income Per Month",
                lines: {
                    lineWidth: 1,
                },
                shadowSize: 0
            }, {
                data: deposits,
                label: "<?php echo date('Y'); ?> Deposits Per Month",
                lines: {
                    lineWidth: 1,
                },
                shadowSize: 0
            }, {
                data: expenses,
                label: "<?php echo date('Y'); ?> Expenses Per Month",
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
                colors: ["#52e136", "#37b7f3", "#d12610", "#d4ad38"],
                xaxis: {
                    ticks: [
                        [1, "Jan"],
                        [2, "Feb"],
                        [3, "Mar"],
                        [4, "Apr"],
                        [5, "May"],
                        [6, "Jun"],
                        [7, "Jul"],
                        [8, "Aug"],
                        [9, "Sep"],
                        [10, "Oct"],
                        [11, "Nov"],
                        [12, "Dec"]
                    ]
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
            $("#sales_chart").bind("plothover", function(event, pos, item) {
                $("#x").text(pos.x.toFixed(2));
                $("#y").text(pos.y.toFixed(2));

                if (item) {
                    if (previousPoint != item.dataIndex) {
                        previousPoint = item.dataIndex;

                        $("#tooltip").remove();
                        var x = item.datapoint[0].toFixed(2),
                            y = item.datapoint[1].toFixed(2);

                        showTooltip(item.pageX, item.pageY, item.series.label + " this month made: " + y);
                    }
                } else {
                    $("#tooltip").remove();
                    previousPoint = null;
                }
            });
        </script>
        <?php

        /*
         *  End Sales Summary Report
         */
    } elseif($_GET['ty'] == 'mkt'){
        /*
         *  Marketing Report
         */

        $range       = explode(" - ", $_POST['ext']);
        $marketing   = array();
        $unique      = NULL;
        $events      = mysql_query("SELECT event_id, event_token, event_user_token, event_name, event_date_start, event_location_token, event_status, event_referer FROM fmo_locations_events WHERE event_location_token='".mysql_real_escape_string($_GET['luid'])."' AND (event_date_start>='".mysql_real_escape_string($range[0])."' AND event_date_end<='".mysql_real_escape_string($range[1])."') AND NOT event_status=0 AND NOT event_status=9 ORDER BY event_date_start ASC");
        if(mysql_num_rows($events) > 0){
            while($event = mysql_fetch_assoc($events)){
                if(in_array($event['event_referer'], $marketing['typer'], true)){
                    $count = $marketing['typer'][$event['event_referer']]['count'];
                    $marketing['typer'][$event['event_referer']]['count'] += $count++;
                } else {
                    if(!empty($event['event_referer'])){
                        $unique ++;
                        $marketing['typer'][$event['event_referer']]['count']    += 1;
                        $marketing['typer'][$event['event_referer']]['name']      = $event['event_referer'];
                    }
                }
            }
        }
        $unique = count($marketing['typer']);

        ?>
        <style>
            @media print {
                .hide {display: none;}
            }
            #marketing_print_wrapper {
                margin-top: -42px;
            }
        </style>
        <div class="portlet">
            <div class="portlet-title" style="margin-bottom: 0px;">
                <h3>
                    <strong>Marketing Report</strong> <small class="text-muted">(date range)</small>
                    <button class="btn default blue-stripe pull-right btn-xs print printer" data-print="#marketing_print"><i class="fa fa-print" style="font-size: 16px;"></i> Print pretty report</button>
                </h3>
            </div>
            <div class="portlet-body">
                <div class="row margin-top-25">
                    <div class="col-md-4 col-sm-12">
                        <div id="marketing_chart" class="chart" style="height: 365px; margin-top: 57px;">
                        </div>
                    </div>
                    <div class="col-md-8 col-sm-12">
                        <h3>Table o' details</h3>
                        <table class="table table-striped table-hover datatable" id="marketing_print">
                            <thead>
                            <tr>
                                <th class="bold">Marketing Type</th>
                                <th >Count</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach($marketing['typer'] as $bfs){
                                ?>
                                <tr>
                                    <td class="bold"><?php echo $bfs['name']; ?></td>
                                    <td ><?php echo $bfs['count']; ?></td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!--
                <div class="task-footer">
                    <div class="btn-arrow-link pull-right">
                        <a href="javascript:;">See All Records</a>
                        <i class="icon-arrow-right"></i>
                    </div>
                </div> -->
            </div>
        </div>
        <script>
            var bk = $('.datatable').dataTable({
                "order": [[ 1, "desc" ]],
                "bFilter" : true,
                "bLengthChange": false,
                "bPaginate": false,
                "info": true,
                "saveState": true
            });
            $('.printer').unbind().on('click', function () {
                var settings = bk.fnSettings();
                settings._iDisplayLength = 50;
                bk.fnDraw();
            });

            var dat  = [<?php echo json_encode($marketing["typer"]); ?>];
            var data = [];
            var series = <?php echo $unique ?>;
            series = series < 5 ? 5 : series;

            /*for (var i = 0; i < series; i++) {
                console.log(dat);
                data[i] = {
                    label: dat[i].name,
                    data: 5
                };
            }*/
            var i = 0;
            for (var key in dat) {
                if (dat.hasOwnProperty(key)) {
                    var obj = dat[key];
                    for (var prop in obj) {
                        if (obj.hasOwnProperty(prop)) {
                            data[i] = {
                                label: obj[prop]['name'],
                                data: obj[prop]['count']
                            };
                            i++;
                        }
                    }
                }
            }

            //var data = [];
            //var series = <?php echo $unique ?>;

            /*for (var i = 0; i < 11; i++) {
                data[i] = {
                    label: "Sup 1",
                    data: 50
                };
            }*/

            /*
            var i = 0;
            $.each(dat.typer,function(prop, obj) {
                alert("prop:"+prop + " , value: "+obj);
                data[i] = {
                    label: "Sup 1",
                    data: 50
                }; i++;
            });*/






            $.plot($("#marketing_chart"), data, {
                series: {
                    pie: {
                        show: true,
                        radius: 1,
                        label: {
                            show: true,
                            radius: 1,
                            formatter: function(label, nuts) {
                                return '<div style="font-size:8pt;text-align:center;padding:2px;color:white;">' + label + '<br/>' + Math.round(nuts.percent) + '%</div>';
                            },
                            background: {
                                opacity: 0.8
                            }
                        }
                    }
                },
                legend: {
                    show: true
                }
            });
        </script>
        <?php
        /*
         *  End Marketing Fee Report
         */
    } elseif($_GET['ty'] == 'snp'){
        /*
         *  Start Snapshot Report
         */


        $snapshot_chart['net'][date('Y')]                       = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        $snapshot_chart['net'][date('Y', strtotime('-1 year'))] = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        $range       = explode(" - ", $_POST['ext']);
        $cuid        = $_GET['cuid'];
        $sales       = array();
        $totals      = array();
        $eventsCurrent      = mysql_query("SELECT event_id, event_token, event_date_start, event_location_token FROM fmo_locations_events WHERE event_company_token='".mysql_real_escape_string($cuid)."' AND YEAR(event_date_start)='".mysql_real_escape_string(date('Y'))."' AND NOT event_status=0 AND NOT event_status=9 ORDER BY event_date_start ASC");
        if(mysql_num_rows($eventsCurrent) > 0){
            while($event = mysql_fetch_assoc($eventsCurrent)){
                $location = mysql_fetch_array(mysql_query("SELECT location_sales_tax FROM fmo_locations WHERE location_token='".mysql_real_escape_string($event['event_location_token'])."'"));

                if(!empty($location['location_sales_tax'])){
                    $tax = $location['location_sales_tax'];
                } else {$tax = 0;}

                $findItems = mysql_query("SELECT item_item, item_total, item_taxable, item_redeemable, item_prepay, item_desc, item_commission FROM fmo_locations_events_items WHERE item_event_token='".mysql_real_escape_string($event['event_token'])."'");
                $iTotalRecords = mysql_num_rows($findItems);

                $findPaid = mysql_query("SELECT payment_amount, payment_type FROM fmo_locations_events_payments WHERE payment_event_token='".mysql_real_escape_string($event['event_token'])."'");
                $bTotalRecords = mysql_num_rows($findPaid);

                $total = array();
                $total['sub_total'] = 0.00;
                $total['tax']       = 0.00;
                $total['cc_fees']   = 0.00;
                $total['total']     = 0.00;
                $total['dis']       = 0.00;
                $total['ppay']      = 0.00;
                $total['paid']      = 0.00;
                $total['unpaid']    = 0.00;
                $totals['code']      = NULL;
                if($iTotalRecords > 0){
                    while($item = mysql_fetch_assoc($findItems)){
                        if($item['item_item'] != 'Booking Fee'){

                            if($item['item_total'] > 0){
                                $total['sub_total'] += $item['item_total'];
                            } else {
                                $total['fake_sub_total'] += $item['item_total'];
                            }

                            if($item['item_taxable'] == 1){
                                $total['tax']      += $item['item_total'] * $tax;
                                $totals['taxable']  += $item['item_total'];
                            } else {
                                $total['tax']   += 0.00;
                            }

                            if($item['item_redeemable'] > 0){
                                $totals['discounts'] += $item['item_total'];
                                $totals['prepaid']   += $item['item_prepay'];
                                $totals['code']      =  $item['item_desc'];
                                $total['dis']        += $item['item_total'];
                                $total['ppay']       += $item['item_prepay'];
                            }

                            if($item['item_commission'] > 0){
                                $totals['coms'] += $item['item_total'];
                            } else {
                                $totals['coms'] += 0.00;
                            }
                        } else {
                            $totals['bfs'] += $item['item_total'];
                        }
                    }
                    $total['total'] = number_format($total['sub_total'], 2, '.', '');
                } else {
                    $total['total']     = 0.00;
                    $total['sub_total'] = 0.00;
                }
                $totals['gross']     += $total['total'] + $total['fake_sub_total'] + $total['cc_fees'];
                $totals['tax']       += $total['tax'];
                $snapshot_chart['net'][date('Y')][date('n', strtotime($event['event_date_start']))] += $total['total'] + $total['tax'] + $total['fake_sub_total'] + $total['cc_fees'] + $total['ppay'];
            }
        }
        $eventsLast         = mysql_query("SELECT event_id, event_token, event_date_start, event_location_token FROM fmo_locations_events WHERE event_company_token='".mysql_real_escape_string($cuid)."' AND YEAR(event_date_start)='".mysql_real_escape_string(date('Y', strtotime('-1 year')))."' AND NOT event_status=0 AND NOT event_status=9 ORDER BY event_date_start ASC");
        if(mysql_num_rows($eventsLast) > 0){
            while($event = mysql_fetch_assoc($eventsLast)){
                $location = mysql_fetch_array(mysql_query("SELECT location_sales_tax FROM fmo_locations WHERE location_token='".mysql_real_escape_string($event['event_location_token'])."'"));

                if(!empty($location['location_sales_tax'])){
                    $tax = $location['location_sales_tax'];
                } else {$tax = 0;}

                $findItems = mysql_query("SELECT item_item, item_total, item_taxable, item_redeemable, item_prepay, item_desc, item_commission FROM fmo_locations_events_items WHERE item_event_token='".mysql_real_escape_string($event['event_token'])."'");
                $iTotalRecords = mysql_num_rows($findItems);

                $findPaid = mysql_query("SELECT payment_amount, payment_type FROM fmo_locations_events_payments WHERE payment_event_token='".mysql_real_escape_string($event['event_token'])."'");
                $bTotalRecords = mysql_num_rows($findPaid);

                $total = array();
                $total['sub_total'] = 0.00;
                $total['tax']       = 0.00;
                $total['cc_fees']   = 0.00;
                $total['total']     = 0.00;
                $total['dis']       = 0.00;
                $total['ppay']      = 0.00;
                $total['paid']      = 0.00;
                $total['unpaid']    = 0.00;
                $totals['code']      = NULL;
                if($iTotalRecords > 0){
                    while($item = mysql_fetch_assoc($findItems)){
                        if($item['item_item'] != 'Booking Fee'){

                            if($item['item_total'] > 0){
                                $total['sub_total'] += $item['item_total'];
                            } else {
                                $total['fake_sub_total'] += $item['item_total'];
                            }

                            if($item['item_taxable'] == 1){
                                $total['tax']      += $item['item_total'] * $tax;
                                $totals['taxable']  += $item['item_total'];
                            } else {
                                $total['tax']   += 0.00;
                            }

                            if($item['item_redeemable'] > 0){
                                $totals['discounts'] += $item['item_total'];
                                $totals['prepaid']   += $item['item_prepay'];
                                $totals['code']      =  $item['item_desc'];
                                $total['dis']        += $item['item_total'];
                                $total['ppay']       += $item['item_prepay'];
                            }

                            if($item['item_commission'] > 0){
                                $totals['coms'] += $item['item_total'];
                            } else {
                                $totals['coms'] += 0.00;
                            }
                        } else {
                            $totals['bfs'] += $item['item_total'];
                        }
                    }
                    $total['total'] = number_format($total['sub_total'], 2, '.', '');
                } else {
                    $total['total']     = 0.00;
                    $total['sub_total'] = 0.00;
                }

                if($bTotalRecords > 0){
                    while($paid = mysql_fetch_assoc($findPaid)){
                        $void = explode(" - ", $paid['payment_type']);
                        if($void[1] != 'VOIDED' && $void[0] != 'Invoice'){
                            $total['paid'] += $paid['payment_amount'];
                            if($void[0] == 'Credit/Debt' && $void[1] != 'Booking Fee'){
                                $total['cc_fees'] += ($paid['payment_amount'] / 1.03) * .03;
                            }
                        }
                    }
                }

                $totals['gross']     += $total['total'] + $total['fake_sub_total'] + $total['cc_fees'];
                $totals['tax']       += $total['tax'];
                $snapshot_chart['net'][date('Y', strtotime('-1 year'))][date('n', strtotime($event['event_date_start']))] += $total['total'] + $total['tax'] + $total['fake_sub_total'] + $total['cc_fees'] + $total['ppay'];
            }
        }

        $marketing   = array();
        $unique      = NULL;
        $events      = mysql_query("SELECT event_id, event_token, event_user_token, event_name, event_date_start, event_location_token, event_status, event_referer FROM fmo_locations_events WHERE event_company_token='".mysql_real_escape_string($cuid)."' AND YEAR(event_date_start)='".mysql_real_escape_string(date('Y'))."' AND NOT event_status=0 AND NOT event_status=9 ORDER BY event_date_start ASC");
        if(mysql_num_rows($events) > 0){
            while($event = mysql_fetch_assoc($events)){
                if(in_array($event['event_referer'], $marketing['typer'], true)){
                    $count = $marketing['typer'][$event['event_referer']]['count'];
                    $marketing['typer'][$event['event_referer']]['count'] += $count++;
                } else {
                    if(!empty($event['event_referer'])){
                        $unique ++;
                        $marketing['typer'][$event['event_referer']]['count']    += 1;
                        $marketing['typer'][$event['event_referer']]['name']      = $event['event_referer'];
                    }
                }
            }
        }
        $unique = count($marketing['typer']);

        ?>
        <style>
            @media print {
                .hide {display: none;}
            }
            #marketing_print_wrapper {
                margin-top: -42px;
            }
        </style>
        <div class="portlet">
            <div class="portlet-title" style="margin-bottom: 0px;">
                <h3>
                    <strong>Company Snapshot</strong> <small class="text-muted">(<strong><?php echo date('M d, Y', strtotime($range[0])); ?></strong> to <strong><?php echo date('M d, Y', strtotime($range[1])); ?></strong>)</small>
                    <button class="btn default blue-stripe pull-right btn-xs print printer" data-print="#snapshot_print"><i class="fa fa-print" style="font-size: 16px;"></i> Print pretty report</button>
                </h3>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="snapshot_chart" class="chart" style="height: 170px;">
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top: 25px!important;">
                    <div class="col-md-4">
                        <div id="marketing_chart" class="chart" style="height: 400px;">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <?php
                        $locations = mysql_query("SELECT location_name, location_token, location_address, location_city, location_state, location_zip FROM fmo_locations WHERE location_owner_company_token='".mysql_real_escape_string($cuid)."' ORDER BY location_name ASC");

                        if(mysql_num_rows($locations) > 0){
                            while($loc = mysql_fetch_assoc($locations)){
                                $ratings = 0; $rating_avg = 0; $rating_amt = 0;
                                $reviews = mysql_query("SELECT review_rating FROM fmo_locations_events_reviews WHERE review_location_token='".$loc['location_token']."'");
                                if(mysql_num_rows($reviews) > 0) {
                                    while ($review = mysql_fetch_assoc($reviews)) {
                                        $ratings   += $review['review_rating'];
                                        $rating_amt++;
                                    }
                                    $rating_avg = $ratings / $rating_amt;
                                }
                                ?>
                                <div class="portfolio-block">
                                    <div class="col-md-3" style="padding-left: 0;">
                                        <div class="portfolio-text" style="margin-left: 10px!important;">
                                            <div class="portfolio-text-info">
                                                <h4 class="bold"><?php echo $loc['location_name']; ?></h4>
                                                <p>
                                                    <?php echo $loc['location_address']."<br/>".$loc['location_city'].", ".$loc['location_state']." - ".$loc['location_zip']; ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 portfolio-stat" style="margin-top: 8px;">
                                        <div class="portfolio-info">
                                            <?php
                                            $leads = mysql_num_rows(mysql_query("SELECT event_id FROM fmo_locations_events WHERE event_location_token='".mysql_real_escape_string($loc['location_token'])."' AND (event_date_start>='".mysql_real_escape_string($range[0])."' AND event_date_end<='".mysql_real_escape_string($range[1])."') AND event_status=0 ORDER BY event_date_touch ASC"));
                                            ?>
                                            Leads <span><?php echo $leads; ?> </span>
                                        </div>
                                        <div class="portfolio-info">
                                            <?php
                                            $bookings = mysql_num_rows(mysql_query("SELECT event_id FROM fmo_locations_events WHERE event_location_token='".mysql_real_escape_string($loc['location_token'])."' AND (event_date_start>='".mysql_real_escape_string($range[0])."' AND event_date_end<='".mysql_real_escape_string($range[1])."') AND NOT event_status=0 AND NOT event_status=9 ORDER BY event_date_start ASC"));
                                            ?>
                                            Bookings <span><?php echo $bookings; ?> </span>
                                        </div>
                                        <div class="portfolio-info">
                                            <?php
                                            $totals      = array();
                                            $events      = mysql_query("SELECT event_id, event_token, event_user_token, event_name, event_date_start, event_location_token, event_cjr, event_status FROM fmo_locations_events WHERE event_location_token='".mysql_real_escape_string($loc['location_token'])."' AND (event_date_start>='".mysql_real_escape_string($range[0])."' AND event_date_end<='".mysql_real_escape_string($range[1])."') AND NOT event_status=0 AND NOT event_status=9 ORDER BY event_date_start ASC");
                                            if(mysql_num_rows($events) > 0){
                                                while($event = mysql_fetch_assoc($events)){
                                                    $location = mysql_fetch_array(mysql_query("SELECT location_sales_tax FROM fmo_locations WHERE location_token='".mysql_real_escape_string($event['event_location_token'])."'"));

                                                    if(!empty($location['location_sales_tax'])){
                                                        $tax = $location['location_sales_tax'];
                                                    } else {$tax = 0;}

                                                    $findItems = mysql_query("SELECT item_item, item_total, item_taxable, item_redeemable, item_prepay, item_desc, item_commission FROM fmo_locations_events_items WHERE item_event_token='".mysql_real_escape_string($event['event_token'])."'");
                                                    $iTotalRecords = mysql_num_rows($findItems);
                                                    $findPaid = mysql_query("SELECT payment_amount, payment_type FROM fmo_locations_events_payments WHERE payment_event_token='".mysql_real_escape_string($event['event_token'])."'");
                                                    $bTotalRecords = mysql_num_rows($findPaid);

                                                    $total = array();
                                                    $total['sub_total'] = 0.00;
                                                    $total['tax']       = 0.00;
                                                    $total['cc_fees']   = 0.00;
                                                    $total['total']     = 0.00;
                                                    $total['dis']       = 0.00;
                                                    $total['ppay']      = 0.00;
                                                    $total['paid']      = 0.00;
                                                    $total['unpaid']    = 0.00;
                                                    $totals['code']      = NULL;
                                                    if($iTotalRecords > 0){
                                                        while($item = mysql_fetch_assoc($findItems)){
                                                            if($item['item_item'] != 'Booking Fee'){

                                                                if($item['item_total'] > 0){
                                                                    $total['sub_total'] += $item['item_total'];
                                                                } else {
                                                                    $total['fake_sub_total'] += $item['item_total'];
                                                                }

                                                                if($item['item_taxable'] == 1){
                                                                    $total['tax']      += $item['item_total'] * $tax;
                                                                    $totals['taxable']  += $item['item_total'];
                                                                } else {
                                                                    $total['tax']   += 0.00;
                                                                }

                                                                if($item['item_redeemable'] > 0){
                                                                    $totals['discounts'] += $item['item_total'];
                                                                    $totals['prepaid']   += $item['item_prepay'];
                                                                    $totals['code']      =  $item['item_desc'];
                                                                    $total['dis']        += $item['item_total'];
                                                                    $total['ppay']       += $item['item_prepay'];
                                                                }

                                                                if($item['item_commission'] > 0){
                                                                    $totals['coms'] += $item['item_total'];
                                                                } else {
                                                                    $totals['coms'] += 0.00;
                                                                }
                                                            } else {
                                                                $totals['bfs'] += $item['item_total'];
                                                            }
                                                        }
                                                        $total['total'] = number_format($total['sub_total'], 2, '.', '');
                                                    } else {
                                                        $total['total']     = 0.00;
                                                        $total['sub_total'] = 0.00;
                                                    }

                                                    if($bTotalRecords > 0){
                                                        while($paid = mysql_fetch_assoc($findPaid)){
                                                            $void = explode(" - ", $paid['payment_type']);
                                                            if($void[1] != 'VOIDED' && $void[0] != 'Invoice'){
                                                                $total['paid'] += $paid['payment_amount'];
                                                                if($void[0] == 'Credit/Debt' && $void[1] != 'Booking Fee'){
                                                                    $total['cc_fees'] += ($paid['payment_amount'] / 1.03) * .03;
                                                                }
                                                            }
                                                        }
                                                    }

                                                    $totals['gross']     += $total['total'] + $total['fake_sub_total'] + $total['cc_fees'];
                                                    $totals['tax']       += $total['tax'];
                                                }
                                            }
                                            ?>
                                            NET Sales <span>$<?php echo number_format(($totals['gross'] + $totals['tax']) + $totals['prepaid'], 2); ?> </span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="">
                                            <a class="btn bigicn-only" style="padding-top: 15px; padding-bottom: 15px;">
                                                <div class="rateYoDash" data-rateyo-rating="<?php echo number_format($rating_avg, 1); ?>"></div>
                                                Avg Rating (<strong><?php echo $rating_amt; ?></strong> reviews)
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
        <script>
            $('.rateYoDash').rateYo({
                halfStar: true,
                readOnly: true
            });
            var bk = $('.datatable').dataTable({
                "order": [[ 1, "desc" ]],
                "bFilter" : true,
                "bLengthChange": false,
                "bPaginate": false,
                "info": true,
                "saveState": true
            });
            $('.printer').unbind().on('click', function () {
                var settings = bk.fnSettings();
                settings._iDisplayLength = 50;
                bk.fnDraw();
            });
            var data = <?php echo json_encode($snapshot_chart) ?>;
            var twenties1 = [
                [1, data.net[<?php echo date('Y'); ?>][1]],
                [2, data.net[<?php echo date('Y'); ?>][2]],
                [3, data.net[<?php echo date('Y'); ?>][3]],
                [4, data.net[<?php echo date('Y'); ?>][4]],
                [5, data.net[<?php echo date('Y'); ?>][5]],
                [6, data.net[<?php echo date('Y'); ?>][6]],
                [7, data.net[<?php echo date('Y'); ?>][7]],
                [8, data.net[<?php echo date('Y'); ?>][8]],
                [9, data.net[<?php echo date('Y'); ?>][9]],
                [10, data.net[<?php echo date('Y'); ?>][10]],
                [11, data.net[<?php echo date('Y'); ?>][11]],
                [12, data.net[<?php echo date('Y'); ?>][12]]
            ];
            var twenties2 = [
                [1, data.net[<?php echo date('Y', strtotime('-1 year')); ?>][1]],
                [2, data.net[<?php echo date('Y', strtotime('-1 year')); ?>][2]],
                [3, data.net[<?php echo date('Y', strtotime('-1 year')); ?>][3]],
                [4, data.net[<?php echo date('Y', strtotime('-1 year')); ?>][4]],
                [5, data.net[<?php echo date('Y', strtotime('-1 year')); ?>][5]],
                [6, data.net[<?php echo date('Y', strtotime('-1 year')); ?>][6]],
                [7, data.net[<?php echo date('Y', strtotime('-1 year')); ?>][7]],
                [8, data.net[<?php echo date('Y', strtotime('-1 year')); ?>][8]],
                [9, data.net[<?php echo date('Y', strtotime('-1 year')); ?>][9]],
                [10, data.net[<?php echo date('Y', strtotime('-1 year')); ?>][10]],
                [11, data.net[<?php echo date('Y', strtotime('-1 year')); ?>][11]],
                [12, data.net[<?php echo date('Y', strtotime('-1 year')); ?>][12]]
            ];

            var plot = $.plot($("#snapshot_chart"), [{
                data: twenties1,
                label: "<?php echo date('Y') ?> NET Income By Month",
                lines: {
                    lineWidth: 1,
                },
                shadowSize: 0
            }, {
                data: twenties2,
                label: "<?php echo date('Y', strtotime('-1 year')) ?> NET Income By Month",
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
                colors: ["#52e136", "#37b7f3", "#d12610", "#d4ad38"],
                xaxis: {
                    ticks: [
                        [1, "Jan"],
                        [2, "Feb"],
                        [3, "Mar"],
                        [4, "Apr"],
                        [5, "May"],
                        [6, "Jun"],
                        [7, "Jul"],
                        [8, "Aug"],
                        [9, "Sep"],
                        [10, "Oct"],
                        [11, "Nov"],
                        [12, "Dec"]
                    ]
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
            $("#snapshot_chart").bind("plothover", function(event, pos, item) {
                $("#x").text(pos.x.toFixed(2));
                $("#y").text(pos.y.toFixed(2));

                if (item) {
                    if (previousPoint != item.dataIndex) {
                        previousPoint = item.dataIndex;

                        $("#tooltip").remove();
                        var x = item.datapoint[0].toFixed(2),
                            y = item.datapoint[1].toFixed(2);

                        showTooltip(item.pageX, item.pageY, item.series.label + " this month made: " + y);
                    }
                } else {
                    $("#tooltip").remove();
                    previousPoint = null;
                }
            });

            var dat  = [<?php echo json_encode($marketing["typer"]); ?>];
            var data = [];
            var series = <?php echo $unique ?>;
            series = series < 5 ? 5 : series;
            var i = 0;
            for (var key in dat) {
                if (dat.hasOwnProperty(key)) {
                    var obj = dat[key];
                    for (var prop in obj) {
                        if (obj.hasOwnProperty(prop)) {
                            data[i] = {
                                label: obj[prop]['name'],
                                data: obj[prop]['count']
                            };
                            i++;
                        }
                    }
                }
            }
            $.plot($("#marketing_chart"), data, {
                series: {
                    pie: {
                        show: true,
                        radius: 1,
                        label: {
                            show: true,
                            radius: 1,
                            formatter: function(label, nuts) {
                                return '<div style="font-size:8pt;text-align:center;padding:2px;color:white;">' + label + '<br/>' + Math.round(nuts.percent) + '%</div>';
                            },
                            background: {
                                opacity: 0.8
                            }
                        }
                    }
                },
                legend: {
                    show: true
                }
            });
        </script>
        <?php

        /*
         *  End Snapshot Report
         */
    } elseif($_GET['ty'] == 'str'){
        /*
         *  Start Storage Closing Report
         */
        $location = mysql_fetch_array(mysql_query("SELECT location_storage_last_closed FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'"));

        ?>
        <style>
            @media print {
                .hide {display: none;}
            }
        </style>
        <div class="portlet">
            <div class="portlet-title" style="margin-bottom: 0px;">
                <h3>
                    <strong>Closing</strong>
                    <button class="btn default blue-stripe pull-right btn-xs print printer" data-print="#closing2"><i class="fa fa-print" style="font-size: 16px;"></i> Print pretty report</button>
                </h3>
            </div>
            <div class="portlet-body">
                <div class="row margin-top-25">
                    <div class="col-md-12 col-sm-12" id="closing2">
                        <h3><strong>
                                <?php echo locationNickName($_GET['luid']); ?></strong>
                                | Last close: <strong>
                                <?php if ($location['location_storage_last_closed'] == "0000-00-00") {echo "Never";} else {
                                    echo date('m/d/Y', strtotime($location['location_storage_last_closed']));
                                  } ?>
                            </strong> | Today: <strong><?php echo date('m/d/Y', strtotime('today')); ?></strong>
                        </h3>
                        <table class="table table-striped table-hover datatable" id="closing">
                            <thead>
                            <tr>
                                <th class="bold">Tender</th>
                                <th class="bold">Amount</th>
                                <th>Customer</th>
                                <th>Notes</th>
                                <th class="bold text-right">Who Took</th>
                                <th class="bold text-right">Date</th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php
                                $payments = mysql_query("SELECT payment_user_token, payment_type, payment_amount, payment_detail, payment_by_user_token, payment_timestamp FROM fmo_locations_storages_contracts_payments WHERE payment_location_token='".mysql_real_escape_string($_GET['luid'])."' AND payment_closed!=1") or die(mysql_error());
                                if(mysql_num_rows($payments)){
                                    $types = array();
                                    while($py = mysql_fetch_assoc($payments)){
                                        $types[$py['payment_type']]['total'] += number_format($py['payment_amount'], 2);
                                        ?>
                                        <tr>
                                            <td class="bold text-success"><?php echo $py['payment_type']; ?></td>
                                            <td class="bold text-success">$<?php echo number_format($py['payment_amount'], 2); ?></td>
                                            <td><?php echo name($py['payment_user_token']); ?></td>
                                            <td><?php echo $py['payment_detail']; ?></td>
                                            <td class="bold text-right"><?php echo name($py['payment_by_user_token']); ?></td>
                                            <td class="bold text-right"><?php echo date('m/d/Y H:i:s', strtotime($py['payment_timestamp'])); ?></td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                        <div class="invoice">
                            <div class="row">
                                <div class="col-xs-12 invoice-block">
                                    <ul class="list-unstyled amounts">
                                        <li>
                                        </li>
                                        <li>
                                            Cash <h3 style="display: inline;" class="text-success bold">$<?php echo number_format($types['Cash']['total'], 2); ?></h3>
                                        </li>
                                        <li>
                                            Check: <h3 style="display: inline;" class="text-success bold">$<?php echo number_format($types['Check']['total'], 2); ?></h3>
                                        </li>
                                        <li>
                                            Credit/Debt: <h3 style="display: inline" class="text-success bold">$<?php echo number_format($types['Credit/Debt']['total'], 2); ?></h3>
                                        </li>
                                        <li>
                                            Write Offs: <h3 style="display: inline" class="text-success bold">$<?php echo number_format($types['Write Off']['total'], 2); ?></h3>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-md-4 col-lg-4 col-xs-4 col-sm-4 pull-right">
                                        <h5 class="pull-right">@ 1¢ USD</h5><br/> <hr style="margin-top: 10px;"/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 col-lg-4 col-xs-4 col-sm-4 pull-right">
                                        <h5 class="pull-right">@ 5¢ USD</h5><br/> <hr style="margin-top: 10px;"/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 col-lg-4 col-xs-4 col-sm-4 pull-right">
                                        <h5 class="pull-right">@ 10¢ USD</h5><br/> <hr style="margin-top: 10px;"/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 col-lg-4 col-xs-4 col-sm-4 pull-right">
                                        <h5 class="pull-right">@ 25¢ USD</h5><br/> <hr style="margin-top: 10px;"/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 col-lg-4 col-xs-4 col-sm-4 pull-right">
                                        <h5 class="pull-right">@ 1$ USD</h5><br/> <hr style="margin-top: 10px;"/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 col-lg-4 col-xs-4 col-sm-4 pull-right">
                                        <h5 class="pull-right">@ 5$ USD</h5><br/> <hr style="margin-top: 10px;"/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 col-lg-4 col-xs-4 col-sm-4 pull-right">
                                        <h5 class="pull-right">@ 10$ USD</h5><br/> <hr style="margin-top: 10px;"/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 col-lg-4 col-xs-4 col-sm-4 pull-right">
                                        <h5 class="pull-right">@ 20$ USD</h5><br/> <hr style="margin-top: 10px;"/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 col-lg-4 col-xs-4 col-sm-4 pull-right">
                                        <h5 class="pull-right">@ 50$ USD</h5><br/> <hr style="margin-top: 10px;"/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 col-lg-4 col-xs-4 col-sm-4 pull-right">
                                        <h5 class="pull-right">@ 100$ USD</h5><br/> <hr style="margin-top: 10px;"/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-lg-6 col-xs-6 col-sm-6 pull-right">
                                        <h5 class="pull-right">Subtotal</h5><br/> <hr style="margin-top: 10px;"/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-lg-6 col-xs-6 col-sm-6 pull-right">
                                        <h4 class="pull-right">Drawer Start (<strong>$200</strong>)</h4><br/> <hr style="margin-top: 10px;"/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-lg-6 col-xs-6 col-sm-6 pull-right">
                                        <h4 class="pull-right">Income</h4><br/> <hr style="margin-top: 10px;"/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-7 col-lg-7 col-xs-7 col-sm-7 pull-right">
                                        <h4 class="pull-left"><i class="fa fa-times text-danger" style="font-size: 18px;"></i></h4><h4 class="pull-right">Manager on Duty (<strong><?php echo name($_SESSION['uuid']); ?></strong>)</h4><br/> <hr style="margin-top: 10px;"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-block red disabled close-warn" disabled="disabled" style="height: 150px; font-size: 30px;">
                            You must at least try to print this before you can finalize & close. <i class="fa fa-times"></i>
                        </button>
                        <button class="btn btn-block red closer" style="display: none; height: 150px; font-size: 30px;">
                            Finalize Countdown & Close <i class="fa fa-external-link"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            var bk = $('.datatable').dataTable({
                "order": [[ 5, "asc" ]],
                "bFilter" : false,
                "bLengthChange": false,
                "bPaginate": false,
                "info": true,
                "saveState": true
            });
            $('.printer').unbind().on('click', function () {
                var settings = bk.fnSettings();
                settings._iDisplayLength = 50;
                bk.fnDraw();
            });
            $('.print').on('click', function(){
                window.onfocus=function(){
                    $('.closer').show();
                    $('.close-warn').hide();
                };
            });
            $('.closer').on('click', function() {
                $(this).html('<i class="fa fa-spin fa-spinner"></i>')
                $.ajax({
                    url: 'assets/app/update_settings.php?setting=closed&luid=<?php echo $_GET['luid']; ?>',
                    type: 'POST',
                    success: function() {
                        $('.closing').click();
                    }, error: function() {

                    }
                });
            }).hide();
        </script>
        <?php

        /*
         *  End Storage Rent Roll Report
         */
    } elseif($_GET['ty'] == 'str_o'){
        /*
         *  Start Storage Closing Report
         */
        $location = mysql_fetch_array(mysql_query("SELECT location_storage_last_opened FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'"));

        ?>
        <style>
            @media print {
                .hide {display: none;}
            }
        </style>
        <div class="portlet">
            <div class="portlet-title" style="margin-bottom: 0px;">
                <h3>
                    <strong>Opening</strong>
                    <button class="btn default blue-stripe pull-right btn-xs print printer" data-print="#opening2"><i class="fa fa-print" style="font-size: 16px;"></i> Print pretty report</button>
                </h3>
            </div>
            <div class="portlet-body">
                <div class="row margin-top-25">
                    <div class="col-md-12 col-sm-12" id="opening2">
                        <h3><strong><?php echo locationNickName($_GET['luid']); ?></strong> | Last open: <strong><?php echo date('m/d/Y', strtotime($location['location_storage_last_opened'])); ?></strong> | Today: <strong><?php echo date('m/d/Y', strtotime('today')); ?></strong></h3>
                        <hr/>
                        <ul class="feeds">
                            <?php
                            $findTimeline = mysql_query("SELECT timeline_id, timeline_by_user_token, timeline_type, timeline_reasoning, timeline_timestamp FROM fmo_locations_storages_contracts_timelines WHERE timeline_contract_token='".mysql_real_escape_string($_GET['luid'])."' AND timeline_type LIKE '%hidden%' AND NOT timeline_opened=1 ORDER BY timeline_id DESC");
                            $iTotalRecords = mysql_num_rows($findTimeline);

                            $records = array();
                            $records["data"] = array();

                            while($time = mysql_fetch_assoc($findTimeline)) {
                                switch($time['timeline_type']){
                                    default: break;
                                    case "hiddenLock": $label = "label-danger"; $icon = "lock"; $desc = "text-danger"; break;
                                    case "hiddenUnlock": $label = "label-success"; $icon = "unlock"; $desc = "text-success"; break;
                                    case "hiddenMoveout": $label = "label-info"; $icon = "home"; $desc = "text-info"; break;
                                }

                                ?>
                                <li>
                                    <div class="col1">
                                        <div class="cont" style="float: none; margin-right: 10px;">
                                            <div class="cont-col1">
                                                <div class="label label-sm <?php echo $label; ?>">
                                                    <i class="fa fa-<?php echo $icon; ?>"></i>
                                                </div>
                                            </div>
                                            <div class="cont-col2">
                                                <div class="desc <?php echo $desc; ?>">
                                                    <?php echo $time['timeline_reasoning']; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-block green disabled open-warn" disabled="disabled" style="height: 150px; font-size: 30px;">
                            You must at least try to print this before you can finalize & open. <i class="fa fa-times"></i>
                        </button>
                        <button class="btn btn-block green opener" style="display: none; height: 150px; font-size: 30px;">
                            Finalize & Open <i class="fa fa-external-link"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            var bk = $('.datatable').dataTable({
                "order": [[ 5, "asc" ]],
                "bFilter" : false,
                "bLengthChange": false,
                "bPaginate": false,
                "info": true,
                "saveState": true
            });
            $('.print').on('click', function(){
                window.onfocus=function(){
                    $('.opener').show();
                    $('.open-warn').hide();
                };
            });
            $('.opener').on('click', function() {
                $(this).html('<i class="fa fa-spin fa-spinner"></i>');
                $.ajax({
                    url: 'assets/app/update_settings.php?setting=open&luid=<?php echo $_GET['luid']; ?>',
                    type: 'POST',
                    success: function() {
                        $('.opening').click();
                    }, error: function() {

                    }
                });
            }).hide();
        </script>
        <?php

        /*
         *  End Storage Rent Roll Report
         */
    }  elseif($_GET['ty'] == 'str_r'){
        /*
         *  Start Storage Rent Roll Report
         */

        ?>
        <style>
            @media print {
                .hide {display: none;}
            }
            #marketing_print_wrapper {
                margin-top: -42px;
            }
        </style>
        <div class="portlet">
            <div class="portlet-title" style="margin-bottom: 0px;">
                <h3>
                    <strong>Rent Roll</strong> <small class="text-muted">(<?php echo date('m/d/Y', strtotime('today')); ?>)</small>
                    <button class="btn default blue-stripe pull-right btn-xs print printer" data-print="#rent_roll"><i class="fa fa-print" style="font-size: 16px;"></i> Print pretty report</button>
                </h3>
            </div>
            <div class="portlet-body">
                <div class="row margin-top-25">
                    <div class="col-md-12 col-sm-12">
                        <h3>Table o' details</h3>
                        <table class="table table-striped table-hover datatable" id="rent_roll">
                            <thead>
                            <tr>
                                <th class="bold">Unit</th>
                                <th class="bold">Customer</th>
                                <th>Phone</th>
                                <th>Rate</th>
                                <th>Due</th>
                                <th class="bold">Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $findStorage = mysql_query("SELECT storage_id, storage_token, storage_available, storage_unit_name, storage_unit_lwh, storage_unit_desc, storage_price, storage_period, storage_occupant, storage_contract_token, storage_last_occupied, storage_status, storage_type_id FROM fmo_locations_storages WHERE storage_location_token='".mysql_real_escape_string($_GET['luid'])."' ORDER BY storage_id DESC") or die(mysql_error());
                            if(mysql_num_rows($findStorage)){
                                while($storage = mysql_fetch_assoc($findStorage)) {
                                    switch($storage['storage_status']){
                                        case "Reserved": $badge = 'badge badge-yellow badge-roundless'; $msg = $storage['storage_status']; $link = ''; break;
                                        case "Vacant": $badge = 'badge badge-default badge-roundless'; $msg = $storage['storage_status']; $link = 'assets/pages/customers.php?luid='.$_GET['luid'].'&s=only&su='.$storage['storage_token']; break;
                                        case "Occupied": $badge = 'badge badge-success badge-roundless'; $msg = $storage['storage_status']; $link = 'assets/pages/profile.php?uuid='.$storage['storage_occupant'].'&s=true'; break;
                                        case "Damaged": $badge = 'badge badge-danger badge-roundless'; $msg = $storage['storage_status']; $link = ''; break;
                                        case "Auction": $badge = 'badge badge-danger badge-roundless'; $msg = $storage['storage_status']; $link = 'assets/pages/profile.php?uuid='.$storage['storage_occupant'].'&s=true'; break;
                                        case "Delinquent": $badge = 'badge badge-danger badge-roundless'; $msg = $storage['storage_status']; $link = ''; break;
                                    }
                                    $type = mysql_fetch_array(mysql_query("SELECT type_floor, type_desc, type_climate FROM fmo_locations_storages_types WHERE type_id='".mysql_real_escape_string($storage['storage_type_id'])."'"));

                                    if(!empty($storage['storage_occupant'])){
                                        $name  = name($storage['storage_occupant']);
                                        $phone = clean_phone(phone($storage['storage_occupant']));
                                        $ts1  = strtotime($storage['storage_last_occupied']);
                                        $ts2  = strtotime('today');
                                        $dif  = $ts2 - $ts1;
                                        $bal  = json_decode(file_get_contents('https://www.formoversonly.com/dashboard/assets/app/api/storage.php?type=inv_c&luid='.$_GET['luid'].'&uuid='.$storage['storage_occupant'].''), true);
                                        $days = secondsToTime($dif);
                                        if($days > 0){
                                            $d = $days;
                                        } else { $d = 0; }
                                    } else {
                                        $name = "N/A";
                                        $phone = "N/A";
                                        $bal['unpaid'] = 0.00;
                                        $d = 0;
                                    }


                                    ?>
                                    <tr style="cursor: pointer!important;" class="load_page"  data-href="<?php echo $link; ?>" data-page-title=" ">
                                        <td>Unit <strong>#<?php echo $storage['storage_unit_name']; ?></strong></td>
                                        <td class="text-muted"><?php echo $name; ?></td>
                                        <td class="text-muted"><?php echo $phone; ?></td>
                                        <td class="text-success">$<strong><?php echo $storage['storage_price']."</strong>/".$storage['storage_period']; ?></td>
                                        <td class="text-danger bold">$<?php echo number_format($bal['unpaid'], 2);; ?></td>
                                        <td class="text-muted bold"> <span class="<?php echo $badge; ?>"><?php echo $msg; ?></span></td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <script>
            var bk = $('.datatable').dataTable({
                "order": [[ 5, "asc" ]],
                "bFilter" : true,
                "bLengthChange": false,
                "bPaginate": false,
                "info": true,
                "saveState": true
            });
            $('.printer').unbind().on('click', function () {
                var settings = bk.fnSettings();
                settings._iDisplayLength = 50;
                bk.fnDraw();
            });
        </script>
        <?php

        /*
         *  End Storage Rent Roll Report
         */
    } elseif($_GET['ty'] == 'str_s'){
        /*
         *  Start Storage Sales Report
         */
        $range = explode(" - ", $_POST['ext']);
        $str_pie   = array();
        $unique      = NULL;
        $strs      = mysql_query("SELECT storage_status FROM fmo_locations_storages WHERE storage_location_token='".mysql_real_escape_string($_GET['luid'])."'");
        if(mysql_num_rows($strs) > 0){
            while($str = mysql_fetch_assoc($strs)){
                if(in_array($str['storage_status'], $str_pie['typer'], true)){
                    $count = $str_pie['typer'][$str['storage_status']]['count'];
                    $str_pie['typer'][$str['storage_status']]['count'] += $count++;
                } else {
                    if(!empty($str['storage_status'])){
                        $unique ++;
                        $str_pie['typer'][$str['storage_status']]['count']    += 1;
                        $str_pie['typer'][$str['storage_status']]['name']      = $str['storage_status'];
                    }
                }
            }
        }
        $unique = count($str_pie['typer']);
        ?>
        <style>
            @media print {
                .hide {display: none;}
            }
            #marketing_print_wrapper {
                margin-top: -42px;
            }
        </style>
        <div class="portlet">
            <div class="portlet-title" style="margin-bottom: 0px;">
                <h3>
                    <strong>Sales Report</strong> <small class="text-muted">(<?php echo date('m/d/Y', strtotime($range[0])); ?> to <?php echo date('m/d/Y', strtotime($range[1])) ?>)</small>
                    <button class="btn default blue-stripe pull-right btn-xs print printer" data-print="#sales_printer"><i class="fa fa-print" style="font-size: 16px;"></i> Print pretty report</button>
                </h3>
            </div>
            <div class="portlet-body" id="sales_printer">

                <div class="row margin-top-25">
                    <div class="col-md-4">
                        <h2 class="text-center">Today's percentages <small class="text-muted">(<?php echo date('m/d/Y'); ?>)</small></h2> <hr/>
                        <div id="str_pie_chart" class="chart" style="height: 365px;">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h2 class="text-center">Sales totals <small class="text-muted">(<?php echo date('m/d/Y', strtotime($range[0])); ?> to <?php echo date('m/d/Y', strtotime($range[1])) ?>)</small></h2> <hr/>
                        <div id="site_activities_loading">
                            <img src="assets/admin/layout/img/loading.gif" alt="loading"/>
                        </div>
                        <div id="site_activities_content" class="display-none">
                            <div id="site_activities" style="height: 228px;">
                            </div>
                        </div>
                        <div style="margin: 20px 0 10px 30px">
                            <div class="row">
                                <div class="col-md-3 col-sm-3 col-xs-6 text-stat">
										<span class="label label-sm label-success">
										Invoiced: </span>
                                    <h3>$13,234</h3>
                                </div>
                                <div class="col-md-3 col-sm-3 col-xs-6 text-stat">
										<span class="label label-sm label-info">
										Collected: </span>
                                    <h3>$134,900</h3>
                                </div>
                                <div class="col-md-3 col-sm-3 col-xs-6 text-stat">
										<span class="label label-sm label-danger">
										Still due: </span>
                                    <h3>$1,134</h3>
                                </div>
                                <div class="col-md-3 col-sm-3 col-xs-6 text-stat">
										<span class="label label-sm label-warning">
										Taxes Collected: </span>
                                    <h3>$235,500.00</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">

                </div>
            </div>
        </div>
        <script>
            var bk = $('.datatable').dataTable({
                "order": [[ 5, "asc" ]],
                "bFilter" : true,
                "bLengthChange": false,
                "bPaginate": false,
                "info": true,
                "saveState": true
            });
            $('.printer').unbind().on('click', function () {
                var settings = bk.fnSettings();
                settings._iDisplayLength = 50;
                bk.fnDraw();
            });

            var dat  = [<?php echo json_encode($str_pie["typer"]); ?>];
            var data = [];
            var series = <?php echo $unique ?>;
            series = series < 5 ? 5 : series;

            var i = 0;
            for (var key in dat) {
                if (dat.hasOwnProperty(key)) {
                    var obj = dat[key];
                    for (var prop in obj) {
                        if (obj.hasOwnProperty(prop)) {
                            data[i] = {
                                label: obj[prop]['name'],
                                data: obj[prop]['count']
                            };
                            i++;
                        }
                    }
                }
            }


            $.plot($("#str_pie_chart"), data, {
                series: {
                    pie: {
                        show: true,
                        radius: 1,
                        label: {
                            show: true,
                            radius: 1,
                            formatter: function(label, nuts) {
                                return '<div style="font-size:8pt;text-align:center;padding:2px;color:white;">' + label + '<br/>' + Math.round(nuts.percent) + '%</div>';
                            },
                            background: {
                                opacity: 0.8
                            }
                        }
                    }
                },
                legend: {
                    show: true
                }
            });

            //site activities
            var previousPoint2 = null;
            $('#site_activities_loading').hide();
            $('#site_activities_content').show();

            var data1 = [
                ['DEC', 300],
                ['JAN', 600],
                ['FEB', 1100],
                ['MAR', 1200],
                ['APR', 860],
                ['MAY', 1200],
                ['JUN', 1450],
                ['JUL', 1800],
                ['AUG', 1200],
                ['SEP', 600]
            ];


            var plot_statistics = $.plot($("#site_activities"),

                [{
                    data: data1,
                    lines: {
                        fill: 0.2,
                        lineWidth: 0,
                    },
                    color: ['#BAD9F5']
                }, {
                    data: data1,
                    points: {
                        show: true,
                        fill: true,
                        radius: 4,
                        fillColor: "#9ACAE6",
                        lineWidth: 2
                    },
                    color: '#9ACAE6',
                    shadowSize: 1
                }, {
                    data: data1,
                    lines: {
                        show: true,
                        fill: false,
                        lineWidth: 3
                    },
                    color: '#9ACAE6',
                    shadowSize: 0
                }],

                {

                    xaxis: {
                        tickLength: 0,
                        tickDecimals: 0,
                        mode: "categories",
                        min: 0,
                        font: {
                            lineHeight: 18,
                            style: "normal",
                            variant: "small-caps",
                            color: "#6F7B8A"
                        }
                    },
                    yaxis: {
                        ticks: 5,
                        tickDecimals: 0,
                        tickColor: "#eee",
                        font: {
                            lineHeight: 14,
                            style: "normal",
                            variant: "small-caps",
                            color: "#6F7B8A"
                        }
                    },
                    grid: {
                        hoverable: true,
                        clickable: true,
                        tickColor: "#eee",
                        borderColor: "#eee",
                        borderWidth: 1
                    },
                    options: {
                        scales: {
                            xAxes: [{
                                type: "time",
                                time: {
                                    min: <?php echo strtotime($range[0]); ?>,
                                    max: <?php echo strtotime($range[1]); ?>
                                }
                            }]
                        }
                    }
                });

            $("#site_activities").bind("plothover", function (event, pos, item) {
                $("#x").text(pos.x.toFixed(2));
                $("#y").text(pos.y.toFixed(2));
                if (item) {
                    if (previousPoint2 != item.dataIndex) {
                        previousPoint2 = item.dataIndex;
                        $("#tooltip").remove();
                        var x = item.datapoint[0].toFixed(2),
                            y = item.datapoint[1].toFixed(2);
                        showChartTooltip(item.pageX, item.pageY, item.datapoint[0], item.datapoint[1] + 'M$');
                    }
                }
            });

            $('#site_activities').bind("mouseleave", function () {
                $("#tooltip").remove();
            });
        </script>
        <?php

        /*
         *  End Storage Rent Roll Report
         */
    } elseif($_GET['ty'] == 'str_a'){
        /*
         *  Start Storage Auction Report
         */

        $auction = array();
        $storage = mysql_query("SELECT storage_id, storage_token, storage_available, storage_unit_name, storage_unit_lwh, storage_unit_desc, storage_price, storage_period, storage_occupant, storage_contract_token, storage_last_occupied, storage_status, storage_type_id FROM fmo_locations_storages WHERE storage_location_token='".mysql_real_escape_string($_GET['luid'])."' AND storage_status='Auction' ORDER BY storage_id, storage_occupant DESC");
        if(mysql_num_rows($storage) > 0){
            while($str = mysql_fetch_assoc($storage)) {
                if(!in_array($str['storage_occupant'], $auction['users'])){
                    $auction['users'][$str['storage_occupant']] = array();
                    $auction['users'][$str['storage_occupant']]['who'][] = array($str['storage_occupant'], ''.name($str['storage_occupant']).'', ''.clean_phone(phone($str['storage_occupant'])).'');
                    $auction['users'][$str['storage_occupant']]['units'][] = array(
                        ''.$str['storage_unit_name'].'',
                        ''.$str['storage_price'].'/'.$str['storage_period'],
                    );
                } else {
                    $auction['users'][$str['storage_occupant']]['units'][] = array(
                        ''.$str['storage_unit_name'].'',
                        ''.$str['storage_price'].'/'.$str['storage_period'],
                    );
                }
            }
        }
        ?>
        <style>
            @media print {
                .hide {display: none;}
            }
            #marketing_print_wrapper {
                margin-top: -42px;
            }
        </style>
        <div class="portlet">
            <div class="portlet-title" style="margin-bottom: 0px;">
                <h3>
                    <strong>Auction Summary</strong> <small class="text-muted"></small>
                    <button class="btn default blue-stripe pull-right btn-xs print printer" data-print="#rent_roll"><i class="fa fa-print" style="font-size: 16px;"></i> Print pretty report</button>
                </h3>
            </div>
            <div class="portlet-body">
                <div class="row margin-top-25">
                    <div class="col-md-12 col-sm-12">
                        <h3>Table o' details</h3>
                        <table class="table table-striped table-hover datatable" id="rent_roll">
                            <thead>
                            <tr>
                                <th class="bold">Unit</th>
                                <th class="bold">Customer</th>
                                <th>Phone</th>
                                <th>Rate</th>
                                <th>Due</th>
                                <th class="bold">Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach($auction['users'] as $u){
                                $total = 0; $runs  = 0;
                                foreach($u['units'] as $unit){
                                    if($runs = 0){$blah = ', ';}
                                    $total += $unit[1];
                                    $units .= $unit[0].$blah;
                                }
                                $bal  = json_decode(file_get_contents('https://www.formoversonly.com/dashboard/assets/app/api/storage.php?type=inv_c&luid='.$_GET['luid'].'&uuid='.$u['who'][0].''), true);
                                ?>
                                <tr>
                                    <td><?php echo $units; ?></td>
                                    <td class="bold"><?php echo $u['who'][1]; ?></td>
                                    <td><?php echo $u['who'][2]; ?></td>
                                    <td><?php ?></td>
                                    <td>$<?php echo number_format($bal['unpaid'], 2); ?></td>
                                    <td></td>
                                </tr>
                                <?php
                            }
                            ?>
                            <?php
                            /*
                            $findStorage = mysql_query("SELECT storage_id, storage_token, storage_available, storage_unit_name, storage_unit_lwh, storage_unit_desc, storage_price, storage_period, storage_occupant, storage_contract_token, storage_last_occupied, storage_status, storage_type_id FROM fmo_locations_storages WHERE storage_location_token='".mysql_real_escape_string($_GET['luid'])."' AND storage_status='Auction' ORDER BY storage_id DESC") or die(mysql_error());
                            if(mysql_num_rows($findStorage)){
                                while($storage = mysql_fetch_assoc($findStorage)) {
                                    switch($storage['storage_status']){
                                        case "Auction": $badge = 'badge badge-danger badge-roundless'; $msg = $storage['storage_status']; $link = 'assets/pages/profile.php?uuid='.$storage['storage_occupant'].'&s=true'; break;
                                    }
                                    $type = mysql_fetch_array(mysql_query("SELECT type_floor, type_desc, type_climate FROM fmo_locations_storages_types WHERE type_id='".mysql_real_escape_string($storage['storage_type_id'])."'"));

                                    if(!empty($storage['storage_occupant'])){
                                        $name  = name($storage['storage_occupant']);
                                        $phone = clean_phone(phone($storage['storage_occupant']));
                                        $ts1  = strtotime($storage['storage_last_occupied']);
                                        $ts2  = strtotime('today');
                                        $dif  = $ts2 - $ts1;
                                        $bal  = json_decode(file_get_contents('https://www.formoversonly.com/dashboard/assets/app/api/storage.php?type=inv_c&luid='.$_GET['luid'].'&uuid='.$storage['storage_occupant'].''), true);
                                        $days = secondsToTime($dif);
                                        if($days > 0){
                                            $d = $days;
                                        } else { $d = 0; }
                                    } else {
                                        $name = "N/A";
                                        $phone = "N/A";
                                        $bal['unpaid'] = 0.00;
                                        $d = 0;
                                    }


                                    ?>
                                    <tr style="cursor: pointer!important;" class="load_page"  data-href="<?php echo $link; ?>" data-page-title=" ">
                                        <td>Unit <strong>#<?php echo $storage['storage_unit_name']; ?></strong></td>
                                        <td class="text-muted"><?php echo $name; ?></td>
                                        <td class="text-muted"><?php echo $phone; ?></td>
                                        <td class="text-success">$<strong><?php echo $storage['storage_price']."</strong>/".$storage['storage_period']; ?></td>
                                        <td class="text-danger bold">$<?php echo number_format($bal['unpaid'], 2);; ?></td>
                                        <td class="text-muted bold"> <span class="<?php echo $badge; ?>"><?php echo $msg; ?></span></td>
                                    </tr>
                                    <?php
                                }
                            }*/
                            ?>
                            </tbody>

                        </table>
                        <pre>
<?php echo json_encode($auction['users']); ?>
                        </pre>

                    </div>
                </div>
            </div>
        </div>
        <script>
            var bk = $('.datatable').dataTable({
                "order": [[ 5, "asc" ]],
                "bFilter" : true,
                "bLengthChange": false,
                "bPaginate": false,
                "info": true,
                "saveState": true
            });
            $('.printer').unbind().on('click', function () {
                var settings = bk.fnSettings();
                settings._iDisplayLength = 50;
                bk.fnDraw();
            });
        </script>
        <?php

        /*
         *  End Storage Auction Report
         */
    }
}
