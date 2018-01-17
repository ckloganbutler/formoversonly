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
    if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_customers") !== false){
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
                                    <a href="#customers_tab" data-toggle="tab">Customers</a>
                                </li>
                            </ul>
                        </div>
                        <div class="portlet-body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="customers_tab">
                                    <?php
                                    if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_customers_search") !== false){
                                        ?>
                                        <div class="row" style="margin-bottom: 80px;">
                                            <div class="col-md-12 page-404">
                                                <div class="number font-red">
                                                    <i style="font-size: 100px;" class="icon-globe"></i>
                                                </div>
                                                <div class="details">
                                                    <h3><strong>Global</strong> customer search</h3>
                                                    <p>
                                                        Search by phone to find customers for <?php echo $location['location_name']; ?>.<br>
                                                        <?php
                                                        if(isset($_GET['s']) && $_GET['s'] == 'only'){
                                                            ?>
                                                            <strong>See if customer already exists to continue storage move-in</strong>.
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <strong>If a match is found, you'll be taken to their profile</strong>.
                                                            <?php
                                                        }
                                                        ?>

                                                    </p>
                                                    <form action="#" id="search_locations_customers">
                                                        <div class="input-group input-medium">
                                                            <input type="text" class="form-control phune" placeholder="enter phone here.." name="number">
                                                            <span class="input-group-btn">
                                                        <button type="button" class="btn red search2">Search</button>
                                                    </span>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>

                                    <?php
                                    if((!isset($_GET['s']) && $_GET['s'] != 'only') && false){
                                        ?>
                                        <div class="row" >
                                            <div class="col-md-12">
                                                <div class="table-container">
                                                    <table class="table table-striped table-hover" id="customers">
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
        if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_customers_create") !== false){
            ?>
            <form method="POST" action="" role="form" id="create_customers">
                <div class="modal fade bs-modal-lg" id="create_customer" tabindex="-1" role="basic" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content box red">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                <h3 class="modal-title font-bold">Add new customer</h3>
                            </div>
                            <div class="modal-body">
                                <div class="form-body">
                                    <div class="form-group">
                                        <label class="control-label visible-ie8 visible-ie9">Full Name <span class="font-red">*</span></label>
                                        <div class="input-icon">
                                            <i class="fa fa-user"></i>
                                            <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Full Name" name="fullname"/>
                                            <span class="help-block">This will be used as reference for the customer.</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label class="control-label visible-ie8 visible-ie9">Phone Number<span class="font-red">*</span></label>
                                            <div class="input-icon">
                                                <i class="fa fa-phone"></i>
                                                <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Phone Number" id="phone" name="phone" value="<?php echo $_GET['p']; ?>"/>
                                                <span class="help-block">This will be the customer's mobile phone number.</span>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label visible-ie8 visible-ie9">Email Address</label>
                                            <div class="input-icon">
                                                <i class="fa fa-envelope"></i>
                                                <input class="form-control placeholder-no-fix" type="email" autocomplete="off" placeholder="Email Address" name="email"/>
                                                <span class="help-block">This will be unique, and cannot be taken by another customer.</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label class="control-label visible-ie8 visible-ie9">Company/Organization Name</label>
                                            <div class="input-icon">
                                                <i class="fa fa-location-arrow"></i>
                                                <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Company/Organization Name" name="company"/>
                                                <span class="help-block">This will only be required if the customer has their own company, and you'd like to make record of it.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn red pull-right">Add customer to system </button>
                                <button type="button" class="btn default pull-right" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <?php
        }
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function() {
                $('#search_locations_customers').on('keyup keypress', function(e) {
                    var keyCode = e.keyCode || e.which;
                    if (keyCode === 13) {
                        e.preventDefault();
                        return false;
                    }
                });
                $("#search_locations_customers").validate({
                    errorElement: 'span', //default input error message container
                    errorClass: 'font-red', // default input error message class
                    rules: {
                        number: {
                            required: true
                        }
                    }
                });
                $("input[name='number']").inputmask("mask", {
                    "mask": "(999) 999-9999"
                });
                $("#phone").inputmask("mask", {
                    "mask": "(999) 999-9999"
                });
                $('#create_customers').validate({
                    errorElement: 'span', //default input error message container
                    errorClass: 'help-block', // default input error message class
                    focusInvalid: false, // do not focus the last invalid input
                    ignore: "",
                    rules: {
                        fullname: {
                            required: true
                        },
                        phone: {
                            required: true,
                            remote: 'assets/app/search_phone.php'
                        },
                        email: {
                            required: false,
                            remote: 'assets/app/search_email.php'
                        }
                    },

                    messages: {
                        phone: {
                            remote: "Phone number is already taken"
                        },
                        email: {
                            remote: "Email address is already taken"
                        }
                    },

                    invalidHandler: function(event, validator) { //display error alert on form submit

                    },

                    highlight: function(element) { // hightlight error inputs
                        $(element)
                            .closest('.form-group').addClass('has-error'); // set error class to the control group
                    },

                    success: function(label) {
                        label.closest('.form-group').removeClass('has-error');
                    },


                    submitHandler: function(form) {
                        <?php
                        if(isset($_GET['s']) && $_GET['s'] == 'only'){
                        ?>
                        $.ajax({
                            url: 'assets/app/register.php?gr=3&c=<?php echo $_SESSION['cuid']; ?>&luid=<?php echo $_GET['luid']; ?>',
                            type: "POST",
                            data: $('#create_customers').serialize(),
                            success: function(data) {
                                toastr.success("<strong>Logan says</strong>:<br/>Nice! We've added your customer to the system, now you will be taken to the storage configuration wizard.");
                                $.ajax({
                                    url: 'assets/pages/profile.php?uuid='+data+'<?php if (isset($_GET['su'])){echo "&su=".$_GET['su'];} ?>',
                                    success: function(data) {
                                        $('#page_content').html(data);
                                        document.title = "Storage Wizard - www.FORMOVERSONLY.com";
                                    },
                                    error: function() {
                                        toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                                    }
                                });
                            },
                            error: function() {
                                toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                            }
                        });
                        <?php
                        } else {
                        ?>
                        $.ajax({
                            url: 'assets/app/register.php?gr=3&c=<?php echo $_SESSION['cuid']; ?>&luid=<?php echo $_GET['luid']; ?>',
                            type: "POST",
                            data: $('#create_customers').serialize(),
                            success: function(data) {
                                toastr.success("<strong>Logan says</strong>:<br/>Nice! We've added your customer to the system, you will now be redirected to their profile.");
                                $.ajax({
                                    url: 'assets/pages/profile.php?uuid='+data,
                                    success: function(data) {
                                        $('#page_content').html(data);
                                        document.title = "Profile - For Movers Only";
                                    },
                                    error: function() {
                                        toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                                    }
                                });
                            },
                            error: function() {
                                toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                            }
                        });
                        <?php
                        }
                        ?>

                    }
                });
                $('.search2').on('click', function(){
                    var that = $(this);
                    that.html('<i class="fa fa-spinner fa-spin"></i>');
                    var phone = $('input[name="number"]').val();
                    $('#phone').attr("value", phone);
                    if($("form#search_locations_customers").valid()){
                        $.ajax({
                            url: "assets/app/search_customers.php?luid=<?php echo $_GET['luid']; ?><?php if(isset($_GET['s']) && $_GET['s'] == 'only'){ echo "&su=".$_GET['su']; } ?>",
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
                    } else {
                        that.html('Search')
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
    }
    ?>

    <?php
} else {
    header("Location: ../../../index.php?err=no_access");
}
?>
