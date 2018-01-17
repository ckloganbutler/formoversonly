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
    $location = mysql_fetch_array(mysql_query("SELECT location_name, location_storage_creditcard_fee FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'"));
    ?>
    <div class="page-content">
        <h3 class="page-title">
            <strong>Storage</strong>
        </h3>
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <i class="fa fa-home"></i>
                    <a class="load_page" data-href="assets/pages/dashboard.php?luid=<?php echo $_GET['luid']; ?>"><?php echo $location['location_name']; ?></a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                    <a class="load_page" data-href="assets/pages/storage.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Storage">Storage</a>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light ">
                            <div class="portlet-title">
                                <div class="caption caption-md">
                                    <i class="fa fa-cubes theme-font bold"></i>
                                    <span class="caption-subject font-red bold uppercase"><?php echo $location['location_name']; ?></span> <span class="font-red">|</span>  <small>Storage</small>
                                </div>
                                <div class="actions btn-set">
                                    <strong>Show: &nbsp;</strong>
                                    <?php
                                    if(isset($_GET['sort'])){
                                        $sorting = explode('_', $_GET['sort']);
                                    }
                                    ?>
                                    <a class="btn btn-xs default load_page" data-page-title="Storage - Vacant Only" data-href="assets/pages/storage.php?luid=<?php echo $_GET['luid']; ?>"><?php if(!isset($_GET['sort'])){echo "<i class='fa fa-check'></i>";} ?> All</a>
                                    <a class="btn btn-xs default load_page" data-page-title="Storage - Vacant Only" data-href="assets/pages/storage.php?luid=<?php echo $_GET['luid']; ?>&sort=vacant"><?php if($_GET['sort'] == 'vacant'){echo "<i class='fa fa-check'></i>";} ?> Vacant</a>
                                    <a class="btn btn-xs green load_page" data-page-title="Storage - Occupied Only" data-href="assets/pages/storage.php?luid=<?php echo $_GET['luid']; ?>&sort=occupied"><?php if($_GET['sort'] == 'occupied'){echo "<i class='fa fa-check'></i>";} ?> Occupied</a>
                                    <a class="btn btn-xs red load_page" data-page-title="Storage - Delinquent Only" data-href="assets/pages/storage.php?luid=<?php echo $_GET['luid']; ?>&sort=delinquent"><?php if($_GET['sort'] == 'delinquent'){echo "<i class='fa fa-check'></i>";} ?> Delinquent</a>
                                    <a class="btn btn-xs yellow load_page" data-page-title="Storage - Auction Only" data-href="assets/pages/storage.php?luid=<?php echo $_GET['luid']; ?>&sort=auction"><?php if($_GET['sort'] == 'auction'){echo "<i class='fa fa-check'></i>";} ?> Auction</a>
                                    <a class="btn btn-xs blue load_page" data-page-title="Storage - Reserved Only" data-href="assets/pages/storage.php?luid=<?php echo $_GET['luid']; ?>&sort=reserved"><?php if($_GET['sort'] == 'reserved'){echo "<i class='fa fa-check'></i>";} ?> Reserved</a>
                                    <a class="btn btn-xs red-haze load_page" data-page-title="Storage - Damaged Only" data-href="assets/pages/storage.php?luid=<?php echo $_GET['luid']; ?>&sort=damaged"><?php if($_GET['sort'] == 'damaged'){echo "<i class='fa fa-check'></i>";} ?> Damaged</a>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="row margin-top-25">
                                    <div class="col-md-12">
                                        <div id="str_chart" class="chart" style="height: 170px;">
                                        </div>
                                    </div>
                                </div>
                                <hr/>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-container">
                                            <table class="table table-striped table-hover datatable">
                                                <thead>
                                                <tr role="row" class="heading">
                                                    <th>
                                                        Unit
                                                    </th>
                                                    <th>
                                                        Customer
                                                    </th>
                                                    <th>
                                                        Phone
                                                    </th>
                                                    <th>
                                                        Rates
                                                    </th>
                                                    <th>
                                                        Occupancy Days
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                if(isset($_GET['sort'])){
                                                    $extra = "AND storage_status LIKE '%".mysql_real_escape_string($_GET['sort'])."%'";
                                                } else { $extra = ""; }
                                                $findStorage = mysql_query("SELECT storage_id, storage_token, storage_available, storage_unit_name, storage_unit_lwh, storage_unit_desc, storage_price, storage_period, storage_occupant, storage_contract_token, storage_last_occupied, storage_status, storage_type_id FROM fmo_locations_storages WHERE storage_location_token='".mysql_real_escape_string($_GET['luid'])."' ".$extra." ORDER BY storage_id DESC") or die(mysql_error());
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
                                                            $days = secondsToTime($dif);
                                                            if($days > 0){
                                                                $d = $days;
                                                            } else { $d = 0; }
                                                        } else {
                                                            $name = "N/A";
                                                            $phone = "N/A";
                                                            $d = 0;
                                                        }


                                                        ?>
                                                        <tr style="cursor: pointer!important;" class="load_page"  data-href="<?php echo $link; ?>" data-page-title=" ">
                                                            <td><strong><?php echo $storage['storage_unit_name']; ?></strong> &nbsp; <span class="<?php echo $badge; ?>"><?php echo $msg; ?></span> &nbsp; Floor <?php echo $type['type_floor'].", ".$type['type_desc']; ?> - <?php echo $storage['storage_unit_lwh']; ?> [Climate: <?php echo $type['type_climate']; ?>]</td>
                                                            <td class="text-muted"><?php echo $name; ?></td>
                                                            <td class="text-muted"><?php echo $phone; ?></td>
                                                            <td class="text-success">$<strong><?php echo $storage['storage_price']."</strong>/".$storage['storage_period']; ?></td>
                                                            <td class="text-muted bold"><?php echo $d; ?></td>
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
                        </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function(){
            $('.datatable').dataTable({
                stateSave: true,
                "order": [[ 4, "desc" ]],
                "bFilter" : true,
                "bLengthChange": true,
                "bPaginate": true,
                "pageLength": 75,
                "info": true
            });
            $('.scroller').slimScroll({
               height: 500
            });
            $('.storage-src').on('input', function() {
                var search = $(this).val();
                $.ajax({
                    url: 'assets/app/api/search.php?e=vcn&luid=<?php echo $_GET['luid']; ?>',
                    type: 'POST',
                    data: {
                        search: search
                    },
                    success: function(data){
                        $('#results').html(data);
                    },
                    error: function(data){
                        toastr.error("<strong>Logan says:</strong><br/>I have encountered an error. Please try again later.");
                    }
                });
            });

            <?php
            $chart_calc['person']['invoiced']                              = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
            $chart_calc['person']['collected']                             = array(0,0,0,0,0,0,0,0,0,0,0,0,0);

            $findItems = mysql_query("SELECT item_total, item_taxable, item_taxable_amount, item_commission, item_added FROM fmo_locations_storages_contracts_items WHERE item_location_token='".mysql_real_escape_string($_GET['luid'])."' AND YEAR(item_added)='".mysql_real_escape_string(date('Y'))."'");
            $iTotalRecords = mysql_num_rows($findItems);

            $findPaid = mysql_query("SELECT payment_amount, payment_type, payment_timestamp FROM fmo_locations_storages_contracts_payments WHERE payment_location_token='".mysql_real_escape_string($_GET['luid'])."' AND YEAR(payment_timestamp)='".mysql_real_escape_string(date('Y'))."'");
            $bTotalRecords = mysql_num_rows($findPaid);

            if($iTotalRecords > 0){
                while($item = mysql_fetch_assoc($findItems)){
                    $chart_calc['person']['invoiced'][date('n', strtotime($item['item_added']))]         += number_format($item['item_total'], 2, '.', '');
                }
            }

            if($bTotalRecords > 0){
                while($paid = mysql_fetch_assoc($findPaid)){
                    $chart_calc['person']['collected'][date('n', strtotime($paid['payment_timestamp']))] += number_format($paid['payment_amount'], 2, '.', '');
                }
            }
            ?>

            var data = <?php echo json_encode($chart_calc) ?>;
            var pageviews = [
                [1, data.person['invoiced'][1]],
                [2, data.person['invoiced'][2]],
                [3, data.person['invoiced'][3]],
                [4, data.person['invoiced'][4]],
                [5, data.person['invoiced'][5]],
                [6, data.person['invoiced'][6]],
                [7, data.person['invoiced'][7]],
                [8, data.person['invoiced'][8]],
                [9, data.person['invoiced'][9]],
                [10, data.person['invoiced'][10]],
                [11, data.person['invoiced'][11]],
                [12, data.person['invoiced'][12]]
            ];
            var visitors = [
                [1, data.person['collected'][1]],
                [2, data.person['collected'][2]],
                [3, data.person['collected'][3]],
                [4, data.person['collected'][4]],
                [5, data.person['collected'][5]],
                [6, data.person['collected'][6]],
                [7, data.person['collected'][7]],
                [8, data.person['collected'][8]],
                [9, data.person['collected'][9]],
                [10, data.person['collected'][10]],
                [11, data.person['collected'][11]],
                [12, data.person['collected'][12]]
            ];


            var plot = $.plot($("#str_chart"), [{
                data: pageviews,
                label: "Invoiced",
                lines: {
                    lineWidth: 1,
                },
                shadowSize: 0
            }, {
                data: visitors,
                label: "Collected",
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
            $("#str_chart").bind("plothover", function(event, pos, item) {
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
            
        });
    </script>
    <?php
} else {
    header("Location: ../../../index.php?err=no_access");
}
?>
