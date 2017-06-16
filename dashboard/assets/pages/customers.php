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
            <strong>Customers</strong>
        </h3>
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <i class="fa fa-home"></i>
                    <a class="load_page" data-href="assets/pages/dashboard.php?luid=<?php echo $_GET['luid']; ?>"><?php echo $location['location_name']; ?></a>
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
                                <div class="row" style="margin-top: 80px;">
                                    <div class="col-md-12">
                                        <div class="table-container">
                                            <table class="table table-striped table-bordered table-hover" id="customers">
                                                <thead>
                                                <tr role="row" class="heading">
                                                    <th width="12%">
                                                        <input type="checkbox" class="group-checkable"> Customer ID
                                                    </th>
                                                    <th>
                                                        Customer Name
                                                    </th>
                                                    <th>
                                                        Customer Phone
                                                    </th>
                                                    <th>
                                                        Customer Email
                                                    </th>
                                                    <th width="12%">
                                                        View & edit
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
            var grid = new Datatable();

            grid.init({
                src: $("#customers"),
                onSuccess: function (grid) {
                    // execute some code after table records loaded
                },
                onError: function (grid) {
                    // execute some code on network or other general error
                },
                onDataLoad: function(grid) {

                },
                loadingMessage: 'Loading...',
                dataTable: {
                    "processing": true,
                    "serverSide": true,
                    "dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                    "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
                    "bPaginate": false,
                    "ajax": {
                        "url": "assets/app/api/customers.php?luid=<?php echo $_GET['luid']; ?>", // ajax source
                    },
                    "language": {
                        "aria": {
                            "sortAscending": ": activate to sort column ascending",
                            "sortDescending": ": activate to sort column descending"
                        },
                        "emptyTable": "No data available in table",
                        "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                        "infoEmpty": "No entries found",
                        "infoFiltered": "(filtered1 from _MAX_ total entries)",
                        "lengthMenu": "Show _MENU_ entries",
                        "search": "Search:",
                        "zeroRecords": "No matching records found"
                    },
                }
            });
        });
    </script>
    <?php
} else {
    header("Location: ../../../index.php?err=no_access");
}
?>
