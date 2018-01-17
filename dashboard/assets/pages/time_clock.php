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
    $uuidperm = mysql_fetch_array(mysql_query("SELECT user_esc_permissions FROM fmo_users WHERE user_token='".mysql_real_escape_string($_SESSION['uuid'])."'"));
    if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_timeclock") !== false){
        ?>
        <div class="page-content">
            <h3 class="page-title">
                <strong>Time Clock</strong>
            </h3>
            <div class="page-bar">
                <ul class="page-breadcrumb">
                    <li>
                        <i class="fa fa-home"></i>
                        <a class="load_page" data-href="assets/pages/dashboard.php?luid=<?php echo $_GET['luid']; ?>"><?php echo $location['location_name']; ?></a>
                        <i class="fa fa-angle-right"></i>
                    </li>
                    <li>
                        <a class="load_page" data-href="assets/pages/time_clock.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Time Clock">Time Clock</a>
                    </li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light">
                        <div class="portlet-title tabbable-line">
                            <div class="caption caption-md">
                                <i class="icon-clock theme-font bold"></i>
                                <span class="caption-subject font-red bold uppercase"><?php echo $location['location_name']; ?></span> <span class="font-red">|</span>  <small>Time Clock</small>
                            </div>
                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a href="#tc" data-toggle="tab">Time Clock</a>
                                </li>
                                <?php
                                if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_timeclock_current") !== false){
                                    ?>
                                    <li class="">
                                        <a href="#tc_adm" data-toggle="tab">Time Clock Administration</a>
                                    </li>
                                    <?php
                                }
                                ?>

                            </ul>
                        </div>
                        <div class="portlet-body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="tc">
                                    <?php
                                    if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_timeclock_clock") !== false){
                                        ?>
                                        <div class="row">
                                            <?php

                                            $check = mysql_query("SELECT timeclock_id, timeclock_clockin FROM fmo_users_employee_timeclock WHERE timeclock_user='".mysql_real_escape_string($_SESSION['uuid'])."' AND timeclock_complete=0");
                                            if(mysql_num_rows($check) > 0){
                                                $clock = mysql_fetch_array($check);
                                                $font  = "font-green";
                                                $in    = "display: none;";
                                                $inout = "out";
                                                $not   = "";
                                                $date_in  = $clock['timeclock_clockin'];
                                                $date_out = date('Y-m-d H:i:s');
                                                $date1 = new DateTime($date_in);
                                                $date2 = new DateTime($date_out);
                                                $diff = $date1->diff($date2);

                                                $hours   = $diff->h;
                                                $minutes = $diff->i;
                                                $seconds = $diff->s;
                                                //$hours   = $hours + ($diff->days*24);
                                                $worked = decimalHours($hours.":".$minutes.":".$seconds);
                                            } else {
                                                $font  = "font-red";
                                                $out   = "display: none;";
                                                $inout = "in";
                                                $not   = "not";
                                            }
                                            ?>
                                            <div class="col-md-12 page-404">
                                                <div class="number <?php echo $font; ?> clock">
                                                    <i style="font-size: 100px;" class="icon-clock"></i>
                                                </div>
                                                <div class="details">
                                                    <h3><?php echo $location['location_name']; ?> clock in/out</h3>
                                                    <p>
                                                        You <strong class="<?php echo $font; ?> clock">are <span class="not"><?php echo $not; ?></span></strong> currently clocked in at <?php echo $location['location_name']; ?>.<br>
                                                        <strong>To clock in/out, please click the button below</strong>.
                                                    </p>
                                                    <div class="input-group input-medium">
                                                        <button type="button" class="btn red clock-in" style="<?php echo $in; ?>"><i class="fa fa-sign-in"></i> Clock in</button>
                                                        <button type="button" class="btn green clock-out" style="<?php echo $out; ?> margin-left: 0;"><i class="fa fa-sign-in"></i> Clock out</button>
                                                        <span style="margin-left: 5px;"><strong><?php echo number_format($worked, 2); ?></strong> hours so far.</span>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                    <div class="row" style="margin-top: 80px;">
                                        <div class="col-md-12">
                                            <div class="table-container">
                                                <table class="table table-striped table-hover datatable" data-src="assets/app/api/time_clock.php?t=lp" id="timeclock">
                                                    <thead>
                                                    <tr role="row" class="heading">
                                                        <th>
                                                            Date Worked
                                                        </th>
                                                        <th>
                                                            Clock-in Date & Time ( + In IP address )
                                                        </th>
                                                        <th>
                                                            Clock-out Date & Time ( + Out IP Address )
                                                        </th>
                                                        <th>
                                                            Hours worked
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_timeclock_current") !== false){
                                    ?>
                                    <div class="tab-pane" id="tc_adm">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="portlet">
                                                    <div class="portlet-title">
                                                        <div class="caption">
                                                            <i class="fa fa-user-plus"></i> Currently Clocked In Employees
                                                        </div>
                                                    </div>
                                                    <div class="portlet-body">
                                                        <div class="table-container">
                                                            <table class="table table-striped table-hover datatable" data-src="assets/app/api/time_clock.php?t=pl&cuid=<?php echo $_SESSION['cuid']; ?>" id="current">
                                                                <thead>
                                                                    <tr role="row" class="heading">
                                                                        <th>
                                                                            Employee
                                                                        </th>
                                                                        <th>
                                                                            Clock-in Date & Time ( + In IP address )
                                                                        </th>
                                                                        <th>
                                                                            Hours worked so far
                                                                        </th>
                                                                        <th>
                                                                            Actions
                                                                        </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>

                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
            $(document).ready(function() {
                $('.datatable').each(function(){
                    var url = $(this).attr('data-src');
                    $(this).DataTable({
                        "processing": true,
                        "serverSide": true,
                        //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                        "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
                        "bPaginate": false,
                        "bFilter": false,
                        "info": false,
                        "ajax": {
                            "url": url, // ajax source
                        },
                        "lengthMenu": [
                            [10, 20, 50, 100, 150, -1],
                            [10, 20, 50, 100, 150, "All"] // change per page values here
                        ],
                        "pageLength": 10, // default record count per page
                        "order": [
                            [1, "asc"]
                        ]// set first column as a default sort by asc
                    });
                });
                $('.clock-in').click(function(){
                    $.ajax({
                        url: 'assets/app/clock.php?clock=in&luid=<?php echo $_GET['luid']; ?>',
                        type: 'POST',
                        success: function(d){
                            $('.clock-in').hide();
                            $('.clock-out').show();
                            $('.not').html("");
                            $('.clock').removeClass('font-red').addClass('font-green');
                            toastr.success("<strong>Logan says</strong>:<br/>You have been clocked in.");
                            $('#timeclock').DataTable().ajax.reload();
                        },
                        error: function(d){
                            toastr.error("<strong>Logan says</strong>:<br/>You are already clocked in.")
                        }
                    });
                });
                $('.clock-out').click(function(){
                    $.ajax({
                        url: 'assets/app/clock.php?clock=out&luid=<?php echo $_GET['luid']; ?>',
                        type: 'POST',
                        success: function(d){
                            $('.clock-out').hide();
                            $('.clock-in').show();
                            $('.not').html("not");
                            $('.clock').removeClass('font-green').addClass('font-red');
                            toastr.success("<strong>Logan says</strong>:<br/>You have been clocked out.");
                            $('#timeclock').DataTable().ajax.reload();
                        },
                        error: function(d){
                            toastr.error("<strong>Logan says</strong>:<br/>You have already been clocked out.")
                        }
                    });
                });
                $(document).on('click', '.aco', function() {
                    var uuid = $(this).attr('data-uuid');
                    var that = $(this);
                    $.ajax({
                        url: 'assets/app/api/actions.php?ty=aco&uuid=' + uuid,
                        type: 'POST',
                        success: function(d){
                            $(that).closest('tr').remove();
                            toastr.success("<strong>Logan says</strong>:<br/>You have clocked that user out.");
                        },
                        error: function(d){
                            toastr.error("<strong>Logan says</strong>:<br/>You have already been clocked out.")
                        }
                    });
                });
            });
        </script>
        <?php
    }
} else {
    header("Location: ../../../index.php?err=no_access");
}
?>
