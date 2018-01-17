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
    $storage  = mysql_fetch_array(mysql_query("SELECT "))
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
                    <div class="portlet-body">
                        <div id="stats" class="chart" style="height: 170px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <!-- BEGIN TODO SIDEBAR -->
                <div class="todo-ui">
                    <!--
                    <div class="todo-sidebar">
                        <div class="portlet light ">
                            <div class="portlet-title">
                                <div class="caption" data-toggle="collapse" data-target=".todo-project-list-content-tags">
                                    <span class="caption-subject font-red bold uppercase">TAGS </span>
                                    <span class="caption-helper visible-sm-inline-block visible-xs-inline-block">click to view</span>
                                </div>
                            </div>
                            <div class="portlet-body todo-project-list-content todo-project-list-content-tags" style="height: auto;">
                                <div class="todo-project-list">
                                    <ul class="nav nav-pills nav-stacked">
                                        <li>
                                            <a href="javascript:;">
                                                <span class="badge badge-default"> 6 </span> Vacant </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;">
                                                <span class="badge badge-success"> 2 </span> Occupied </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;">
                                                <span class="badge badge-danger"> 14 </span> Deliquent </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;">
                                                <span class="badge badge-danger"> 6 </span> Damaged </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;">
                                                <span class="badge badge-info"> 2 </span> Reserved </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;">
                                                <span class="badge badge-warning"> 14 </span> Auction </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div-->
                    <!-- END TODO SIDEBAR -->
                    <!-- BEGIN TODO CONTENT -->
                    <div class="todo-content">
                        <div class="portlet light ">
                            <div class="portlet-body">
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
                                                    <th>
                                                        Current Balance
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                $findStorage = mysql_query("SELECT storage_id, storage_available, storage_unit_name, storage_unit_lwh, storage_unit_desc, storage_price, storage_period, storage_occupant, storage_contract_token, storage_last_occupied, storage_status, storage_type_id FROM fmo_locations_storages WHERE storage_location_token='".mysql_real_escape_string($_GET['luid'])."' ORDER BY storage_id DESC") or die(mysql_error());
                                                if(mysql_num_rows($findStorage)){
                                                    while($storage = mysql_fetch_assoc($findStorage)) {
                                                        switch($storage['storage_status']){
                                                            case "Reserved": $badge = 'badge badge-yellow badge-roundless'; $msg = $storage['storage_status']; break;
                                                            case "Vacant": $badge = 'badge badge-default badge-roundless'; $msg = $storage['storage_status']; break;
                                                            case "Occupied": $badge = 'badge badge-success badge-roundless'; $msg = $storage['storage_status']; break;
                                                            case "Damaged": $badge = 'badge badge-danger badge-roundless'; $msg = $storage['storage_status']; break;
                                                            case "Auction": $badge = 'badge badge-danger badge-roundless'; $msg = $storage['storage_status']; break;
                                                            case "Delinquent": $badge = 'badge badge-danger badge-roundless'; $msg = $storage['storage_status']; break;
                                                        }
                                                        $type = mysql_fetch_array(mysql_query("SELECT type_floor, type_desc, type_climate FROM fmo_locations_storages_types WHERE type_id='".mysql_real_escape_string($storage['storage_type_id'])."'"));
                                                        ?>
                                                        <tr style="cursor: pointer!important;" class="load_page"  data-href="assets/pages/contract.php?su=<?php echo $storage['storage_token']; ?>" data-page-title="Storage Unit <?php echo $storage['storage_unit_name']; ?>">
                                                            <td><span class="<?php echo $badge; ?>"><?php echo $msg; ?></span> Floor <?php echo $type['type_floor'].", ".$type['type_desc']; ?> - <?php echo $storage['storage_unit_lwh']; ?> - <strong><?php echo $storage['storage_unit_name']; ?></strong> [Climate: <?php echo $type['type_climate']; ?>]</td>
                                                            <td class="text-muted">Loganina Bootler</td>
                                                            <td class="text-muted">loganck@outlyehosting.com</td>
                                                            <td class="text-success"><?php echo $storage['storage_price']."/".$storage['storage_period']; ?></td>
                                                            <td class="text-muted bold">27</td>
                                                            <td class="text-danger bold">$100.00</td>
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
                    <!-- END TODO CONTENT -->
                </div>
            </div>
            <!-- END PAGE CONTENT-->
        </div>
    </div>
    <script>
        $(document).ready(function(){

        });
    </script>
    <?php
} else {
    header("Location: ../../../index.php?err=no_access");
}
?>
