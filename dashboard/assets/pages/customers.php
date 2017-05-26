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
    ?>
    <div class="page-content">
        <h3 class="page-title">
            Customers
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
                <li>
                    <a class="load_page" data-href="assets/pages/customers.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Customers">Customers</a>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light">
                    <div class="portlet-title tabbable-line">
                        <div class="caption caption-md">
                            <i class="icon-users theme-font bold"></i>
                            <span class="caption-subject font-red bold uppercase"><?php echo $location['location_name']; ?></span> <span class="font-red">|</span>  <small>Customers</small>
                        </div>
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#customers_tab" data-toggle="tab">Customers Search</a>
                            </li>
                            <li class="">
                                <a href="#customers_list" data-toggle="tab">Customers List</a>
                            </li>
                        </ul>
                    </div>
                    <div class="portlet-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="customers_tab">
                                <div class="row">
                                    <div class="col-md-12 page-404">
                                        <div class="number font-red">
                                            <i style="font-size: 100px;" class="icon-magnifier"></i>
                                        </div>
                                        <div class="details">
                                            <h3><?php echo $location['location_name']; ?> customer search</h3>
                                            <p>
                                                Search by phone to find customers for <?php echo $location['location_name']; ?>.<br>
                                                <strong>If a match is found, you'll be taken to their profile</strong>.
                                            </p>
                                            <form action="#" id="search_locations_customers">
                                                <div class="input-group input-medium">
                                                    <input type="number" class="form-control" placeholder="phone.." name="number">
                                                    <span class="input-group-btn">
                                                        <button type="button" class="btn red search">Search</button>
                                                    </span>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <br/><br/><br/>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="customers_list">
                                <div class="row">
                                    <div class="col-md-12">
                                        <br/><br/><br/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            $("#search_locations_customers").validate({
                errorElement: 'span', //default input error message container
                errorClass: 'font-red', // default input error message class
                rules: {
                    number: {
                        required: true
                    }
                }
            });
            $('.search').on('click', function(){
                if($("form#search_locations_customers").valid()){
                    $.ajax({
                        url: "assets/app/search_customers.php?luid=<?php echo $_GET['luid']; ?>",
                        type: "POST",
                        data: $('#search_locations_customers').serialize(),
                        success: function(data) {
                            $('#customers_tab').html(data);
                            toastr.info('<strong>Logan says</strong>:<br/>Please wait while I search the database for you.');
                        },
                        error: function() {
                            toastr.error('<strong>Logan says</strong>:<br/>An unexpected error has occurred. Please try again later.');
                        }
                    });
                }
            });
        });
    </script>
    <?php
} else {
    header("Location: ../../../index.php?err=no_access");
}
?>
