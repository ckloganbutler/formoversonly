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
    $location = mysql_fetch_array(mysql_query("SELECT location_name, location_owner_company_token FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'"));
    ?>
    <div class="page-content">
        <h3 class="page-title">
            Vendors
        </h3>
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <i class="fa fa-home"></i>
                    <a class="load_page" data-href="assets/pages/dashboard.php?luid=<?php echo $_GET['luid']; ?>"><?php echo $location['location_name']; ?></a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                    <a class="load_page" data-href="assets/pages/vendors.php?luid=<?php echo $_GET['luid']; ?>">Vendors</a>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption caption-md">
                            <i class="icon-call-out theme-font bold"></i>
                            <span class="caption-subject font-red bold uppercase"><?php echo $location['location_name']; ?></span> <span class="font-red">|</span> <small>Vendors</small>
                        </div>
                        <div class="actions">
                            <a class="btn default red-stripe" id="show_add_vendor">
                                <i class="fa fa-plus"></i>
                                <span class="hidden-480">New Vendor </span>
                            </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="table-container">
                            <div class="table-actions-wrapper">
                                <span>

                                </span>
                                <select class="table-group-action-input form-control input-inline input-small input-sm">
                                    <option value="">Select...</option>
                                    <option value="Delete">Delete</option>
                                    <?php
                                    $copyToLocations = mysql_query("SELECT location_name, location_token FROM fmo_locations WHERE location_owner_company_token='".mysql_real_escape_string($location['location_owner_company_token'])."'");
                                    if(mysql_num_rows($copyToLocations)>0){
                                        while($copyTo = mysql_fetch_assoc($copyToLocations)){
                                            ?>
                                            <option value="copyTo|<?php echo $copyTo['location_token']; ?>|<?php echo $_GET['luid']; ?>">Copy to <?php echo $copyTo['location_name']; ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <button class="btn btn-sm red table-group-action-submit"><i class="fa fa-check"></i> Submit</button>
                            </div>
                            <form role="form" id="add_vendor_contact">
                                <table class="table table-striped table-bordered table-hover" id="vendors">
                                    <thead>
                                    <tr role="row" class="heading">
                                        <th width="4.7%">
                                            <input type="checkbox" class="group-checkable"> Active
                                        </th>
                                        <th width="9%">
                                            Vendor Name
                                        </th>
                                        <th width="9%">
                                            Type
                                        </th>
                                        <th width="9%">
                                            Phone
                                        </th>
                                        <th width="9%">
                                            Contact Name
                                        </th>
                                        <th width="9%">
                                            Account #
                                        </th>
                                        <th width="9%">
                                            Extra Information
                                        </th>
                                        <th width="9%">
                                            In-line Edit
                                        </th>
                                    </tr>
                                    <tr role="row" class="filter hide" id="add_vendor_item">
                                        <td>
                                            <select name="active" class="form-control form-filter input-sm">
                                                <option value="">Select one..</option>
                                                <option value="1">Yes</option>
                                                <option value="0">No</option>
                                            </select>
                                        </td>
                                        <td><input type="text" class="form-control form-filter input-sm" name="name"></td>
                                        <td>
                                            <select name="type" class="form-control form-filter input-sm">
                                                <option value="">Select one..</option>
                                                <option value="Towing">Towing</option>
                                                <option value="Mechanic">Mechanic</option>
                                                <option value="Roadside">Roadside</option>
                                                <option value="Trucks">Trucks</option>
                                                <option value="Fuel">Fuel</option>
                                                <option value="Rentals">Rentals</option>
                                                <option value="Day">Day</option>
                                                <option value="Furniture Repair"></option>
                                                <option value="Labor">Labor</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </td>
                                        <td><input type="number" class="form-control form-filter input-sm" name="phone"></td>
                                        <td><input type="text" class="form-control form-filter input-sm" name="contact"></td>
                                        <td><input type="text" class="form-control form-filter input-sm" name="account_ref"></td>
                                        <td><input type="text" class="form-control form-filter input-sm" name="extra_ref"></td>
                                        <td>
                                            <div class="margin-bottom-5">
                                                <button type="button" class="btn btn-sm red margin-bottom add_vendor_contact"><i class="fa fa-download"></i> Save</button> <button class="btn btn-sm default filter-cancel"><i class="fa fa-times"></i> Reset</button>
                                            </div>
                                        </td>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function(){
            $(document).on('click', '.edit_line', function(){
                var line = $(this).val();
                $('.'+line).editable();
                $('.'+line+'_taxable').editable({
                    source: [
                        {value: 0, text: 'Yes'},
                        {value: 1, text: 'No'}
                    ]
                });
                $('.'+line+'_commissionable').editable({
                    source: [
                        {value: 0, text: 'Yes'},
                        {value: 1, text: 'No'}
                    ]
                });
                $('.'+line+'_type').editable({
                    source: [
                        {value: 'Supplies', text: 'Supplies'},
                        {value: 'Labor', text: 'Labor'},
                        {value: 'Discount', text: 'Discount'},
                        {value: 'Extras', text: 'Extras'},
                        {value: 'Other', text: 'Other'}
                    ]
                });
                toastr.info("<strong>Logan says</strong>:<br/>You can now edit that line. To edit, please click the blue underline under the value you'd like to update.")
            });
            $("#add_vendor_contact").validate({
                errorElement: 'span', //default input error message container
                errorClass: 'font-red', // default input error message class
                rules: {
                    active: {
                        required: true
                    },
                    name: {
                        required: true
                    },
                    type: {
                        required: true
                    },
                    phone: {
                        required: true
                    },
                    contact: {
                        required: true
                    },
                    account_ref: {
                        required: false
                    },
                    extra_ref: {
                        required: false
                    }
                }
            });
            var vendors = new Datatable();

            vendors.init({
                src: $("#vendors"),
                onSuccess: function (vendors) {
                    // execute some code after table records loaded
                },
                onError: function (vendors) {
                    // execute some code on network or other general error
                },
                onDataLoad: function(vendors) {
                    // execute some code on ajax data load
                },
                loadingMessage: 'Loading...',
                dataTable: {
                    "processing": true,
                    "serverSide": true,
                    //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                    "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
                    "bPaginate": false,
                    "ajax": {
                        "url": "assets/app/api/vendors.php?luid=<?php echo $_GET['luid']; ?>", // ajax source
                    },
                    "lengthMenu": [
                        [10, 20, 50, 100, 150, -1],
                        [10, 20, 50, 100, 150, "All"] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "order": [
                        [1, "asc"]
                    ]// set first column as a default sort by asc
                }
            });

            // handle group actionsubmit button click
            vendors.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
                e.preventDefault();
                var action = $(".table-group-action-input", vendors.getTableWrapper());
                if (action.val() != "" && vendors.getSelectedRowsCount() > 0) {
                    vendors.setAjaxParam("customActionType", "group_action");
                    vendors.setAjaxParam("customActionName", action.val());
                    vendors.setAjaxParam("id", vendors.getSelectedRows());
                    vendors.getDataTable().ajax.reload();
                    vendors.clearAjaxParams();
                } else if (action.val() == "") {
                    Metronic.alert({
                        type: 'danger',
                        icon: 'warning',
                        message: 'Please select an action',
                        container: vendors.getTableWrapper(),
                        place: 'prepend'
                    });
                } else if (vendors.getSelectedRowsCount() === 0) {
                    Metronic.alert({
                        type: 'danger',
                        icon: 'warning',
                        message: 'No record selected',
                        container: vendors.getTableWrapper(),
                        place: 'prepend'
                    });
                }
            });
            $('#show_add_vendor').on('click', function(){
                $('#add_vendor_item').removeClass('hide');
            });
            $('.add_vendor_contact').on('click', function(){
                if($("#add_vendor_contact").valid()){
                    $.ajax({
                        url: "assets/app/add_setting.php?setting=vendor&luid=<?php echo $_GET['luid']; ?>",
                        type: "POST",
                        data: $('#add_vendor_contact').serialize(),
                        success: function(data) {
                            $('#add_vendor_contact')[0].reset();
                            toastr.info('<strong>Logan says</strong>:<br/>'+data+' has been added to your list of vendors for this location.');
                            vendors.getDataTable().ajax.reload();
                        },
                        error: function() {
                            toastr.error('<strong>Logan says</strong>:<br/>That page didnt respond correctly. Try again, or create a support ticket for help.');
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
