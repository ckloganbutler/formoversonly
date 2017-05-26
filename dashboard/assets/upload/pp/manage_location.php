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
    mysql_query("UPDATE fmo_users SET user_last_location='".mysql_real_escape_string(basename(__FILE__, '.php')).".php' WHERE user_token='".mysql_real_escape_string($_SESSION['uuid'])."'");
    $location = mysql_fetch_array(mysql_query("SELECT location_name, location_phone, location_email, location_token, location_status, location_address, location_address2, location_city, location_state, location_zip, location_county, location_minimum_hours, location_assumed_loadtime, location_assumed_unloadtime, location_sales_tax, location_service_tax, location_creditcard_fee, location_storage_access FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'"));
    ?>
    <div class="page-content">
        <h3 class="page-title">
            <?php echo $location['location_name']; ?> <small>Manage</small>
        </h3>
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <i class="fa fa-home"></i>
                    <a class="load_page" data-href="assets/pages/dashboard.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Dashboard">Dashboard</a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                    <a class="load_page" data-href="assets/pages/manage_location.php?luid=<?php echo $location['location_token']; ?>" data-act="breadcrumb"><?php echo $location['location_name']; ?> </a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                    <a class="load_page" data-href="assets/pages/manage_location.php?luid=<?php echo $_GET['luid']; ?>" data-act="breadcrumb">Location Settings</a>
                </li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="portlet light">
                    <div class="portlet-title tabbable-line">
                        <div class="caption caption-md">
                            <i class="icon-globe theme-font hide"></i>
                            <span class="caption-subject font-red bold uppercase"><?php echo $location['location_name']; ?> </span> &nbsp; <small>Active: </small> &nbsp; <input type="checkbox" id="location_status" checked data-size="small" data-on-color="success" data-on-text="YES" data-off-color="default" data-off-text="NO" data-state="false">
                        </div>
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#tab_1_1" data-toggle="tab">Contact Details</a>
                            </li>
                            <li>
                                <a href="#tab_1_2" data-toggle="tab">Services & Rates</a>
                            </li>
                            <li>
                                <a href="#tab_1_3" data-toggle="tab">Promotional Codes</a>
                            </li>
                            <li>
                                <a href="#tab_1_4" data-toggle="tab">Inventory & Supplies</a>
                            </li>
                        </ul>
                    </div>
                    <div class="portlet-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab_1_1">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="note note-info">
                                            <p>
                                                NOTE: The software is fully customizable. You set your own settings, that are only used by your company.
                                            </p>
                                        </div>
                                        <div class="portlet">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-users"></i>Contact Details
                                                </div>
                                                <div class="actions">
                                                    <a class="btn default red-stripe">
                                                        <i class="fa fa-plus"></i>
                                                        <span class="hidden-480">Action</span>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                                <form id="update_location_contact" role="form" action="">
                                                    <div class="form-group">
                                                        <label class="control-label">Name (used for reference)</label>
                                                        <input type="text" placeholder="<?php echo $location['location_name']; ?>" class="form-control" name="name"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label">Location Phone</label>
                                                        <input type="number" placeholder="<?php echo $location['location_phone']; ?>" class="form-control" name="phone"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label">Location Email</label>
                                                        <input type="email" placeholder="<?php echo $location['location_email']; ?>" class="form-control" name="email"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label">Address Line 1</label>
                                                        <input type="text" placeholder="<?php echo $location['location_address']; ?>" class="form-control" name="address"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label">Address Line 2</label>
                                                        <input type="text" placeholder="<?php echo $location['location_address2']; ?>" class="form-control" name="address2"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label">City</label>
                                                        <input type="text" placeholder="<?php echo $location['location_city']; ?>" class="form-control" name="city"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label">State</label>
                                                        <select class="form-control" required="required" name="state" id="state">
                                                            <option <?php if($location['location_state'] == 'AL') {echo "selected";} ?> value="AL">Alabama</option>
                                                            <option <?php if($location['location_state'] == 'AK') {echo "selected";} ?> value="AK">Alaska</option>
                                                            <option <?php if($location['location_state'] == 'AZ') {echo "selected";} ?> value="AZ">Arizona</option>
                                                            <option <?php if($location['location_state'] == 'AR') {echo "selected";} ?> value="AR">Arkansas</option>
                                                            <option <?php if($location['location_state'] == 'CA') {echo "selected";} ?> value="CA">California</option>
                                                            <option <?php if($location['location_state'] == 'CO') {echo "selected";} ?> value="CO">Colorado</option>
                                                            <option <?php if($location['location_state'] == 'CT') {echo "selected";} ?> value="CT">Connecticut</option>
                                                            <option <?php if($location['location_state'] == 'DE') {echo "selected";} ?> value="DE">Delaware</option>
                                                            <option <?php if($location['location_state'] == 'DC') {echo "selected";} ?> value="DC">District Of Columbia</option>
                                                            <option <?php if($location['location_state'] == 'FL') {echo "selected";} ?> value="FL">Florida</option>
                                                            <option <?php if($location['location_state'] == 'GA') {echo "selected";} ?> value="GA">Georgia</option>
                                                            <option <?php if($location['location_state'] == 'HI') {echo "selected";} ?> value="HI">Hawaii</option>
                                                            <option <?php if($location['location_state'] == 'ID') {echo "selected";} ?> value="ID">Idaho</option>
                                                            <option <?php if($location['location_state'] == 'IL') {echo "selected";} ?> value="IL">Illinois</option>
                                                            <option <?php if($location['location_state'] == 'IN') {echo "selected";} ?> value="IN">Indiana</option>
                                                            <option <?php if($location['location_state'] == 'IA') {echo "selected";} ?> value="IA">Iowa</option>
                                                            <option <?php if($location['location_state'] == 'KS') {echo "selected";} ?> value="KS">Kansas</option>
                                                            <option <?php if($location['location_state'] == 'KY') {echo "selected";} ?> value="KY">Kentucky</option>
                                                            <option <?php if($location['location_state'] == 'LA') {echo "selected";} ?> value="LA">Louisiana</option>
                                                            <option <?php if($location['location_state'] == 'ME') {echo "selected";} ?> value="ME">Maine</option>
                                                            <option <?php if($location['location_state'] == 'MD') {echo "selected";} ?> value="MD">Maryland</option>
                                                            <option <?php if($location['location_state'] == 'MA') {echo "selected";} ?> value="MA">Massachusetts</option>
                                                            <option <?php if($location['location_state'] == 'MI') {echo "selected";} ?> value="MI">Michigan</option>
                                                            <option <?php if($location['location_state'] == 'MN') {echo "selected";} ?> value="MN">Minnesota</option>
                                                            <option <?php if($location['location_state'] == 'MS') {echo "selected";} ?> value="MS">Mississippi</option>
                                                            <option <?php if($location['location_state'] == 'MO') {echo "selected";} ?> value="MO">Missouri</option>
                                                            <option <?php if($location['location_state'] == 'MT') {echo "selected";} ?> value="MT">Montana</option>
                                                            <option <?php if($location['location_state'] == 'NE') {echo "selected";} ?> value="NE">Nebraska</option>
                                                            <option <?php if($location['location_state'] == 'NV') {echo "selected";} ?> value="NV">Nevada</option>
                                                            <option <?php if($location['location_state'] == 'NH') {echo "selected";} ?> value="NH">New Hampshire</option>
                                                            <option <?php if($location['location_state'] == 'NJ') {echo "selected";} ?> value="NJ">New Jersey</option>
                                                            <option <?php if($location['location_state'] == 'NM') {echo "selected";} ?> value="NM">New Mexico</option>
                                                            <option <?php if($location['location_state'] == 'NY') {echo "selected";} ?> value="NY">New York</option>
                                                            <option <?php if($location['location_state'] == 'NC') {echo "selected";} ?> value="NC">North Carolina</option>
                                                            <option <?php if($location['location_state'] == 'ND') {echo "selected";} ?> value="ND">North Dakota</option>
                                                            <option <?php if($location['location_state'] == 'OH') {echo "selected";} ?> value="OH">Ohio</option>
                                                            <option <?php if($location['location_state'] == 'OK') {echo "selected";} ?> value="OK">Oklahoma</option>
                                                            <option <?php if($location['location_state'] == 'OR') {echo "selected";} ?> value="OR">Oregon</option>
                                                            <option <?php if($location['location_state'] == 'PA') {echo "selected";} ?> value="PA">Pennsylvania</option>
                                                            <option <?php if($location['location_state'] == 'RI') {echo "selected";} ?> value="RI">Rhode Island</option>
                                                            <option <?php if($location['location_state'] == 'SC') {echo "selected";} ?> value="SC">South Carolina</option>
                                                            <option <?php if($location['location_state'] == 'SD') {echo "selected";} ?> value="SD">South Dakota</option>
                                                            <option <?php if($location['location_state'] == 'TN') {echo "selected";} ?> value="TN">Tennessee</option>
                                                            <option <?php if($location['location_state'] == 'TX') {echo "selected";} ?> value="TX">Texas</option>
                                                            <option <?php if($location['location_state'] == 'UT') {echo "selected";} ?> value="UT">Utah</option>
                                                            <option <?php if($location['location_state'] == 'VT') {echo "selected";} ?> value="VT">Vermont</option>
                                                            <option <?php if($location['location_state'] == 'VA') {echo "selected";} ?> value="VA">Virginia</option>
                                                            <option <?php if($location['location_state'] == 'WA') {echo "selected";} ?> value="WA">Washington</option>
                                                            <option <?php if($location['location_state'] == 'WV') {echo "selected";} ?> value="WV">West Virginia</option>
                                                            <option <?php if($location['location_state'] == 'WI') {echo "selected";} ?> value="WI">Wisconsin</option>
                                                            <option <?php if($location['location_state'] == 'WY') {echo "selected";} ?> value="WY">Wyoming</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label">Zip Code</label>
                                                        <input type="number" placeholder="<?php echo $location['location_zip']; ?>" class="form-control" name="zip"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label">County</label>
                                                        <input type="text" placeholder="<?php echo $location['location_county']; ?>" class="form-control" name="county"/>
                                                    </div>
                                                </form>
                                                <div class="margiv-top-10">
                                                    <a class="update_settings btn red" data-form="assets/app/update_settings.php?update=location_contact_details&luid=<?php echo $_GET['luid']; ?>" data-id="#update_location_contact" data-reload="assets/pages/manage_location.php?luid=<?php echo $_GET['luid']; ?>">Save Changes</a>
                                                    <a href="javascript:;" class="btn default">Cancel </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="tab-pane" id="tab_1_2">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="portlet">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-archive"></i> Static Information
                                                </div>
                                                <div class="actions">
                                                    <a class="btn default red-stripe update_settings" data-form="assets/app/update_settings.php?update=location_settings" data-id="#update_location_information" data-no-reload>
                                                        <i class="fa fa-download"></i>
                                                        <span class="hidden-480">Save Information</span>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                                <form id="update_location_information" role="form" action="">
                                                    <div class="form-group">
                                                        <label class="control-label">Minimum Hours</label>
                                                        <input type="number" placeholder="<?php echo $location['location_minimum_hours']; ?>hrs" class="form-control" name="minimum_hours"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label">Assumed Load Time (In hours)</label>
                                                        <input type="text" placeholder="<?php echo $location['location_assumed_loadtime']; ?>hrs" class="form-control" name="assumed_loadtime"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label">Assumed Unload Time (In hours)</label>
                                                        <input type="text" placeholder="<?php echo $location['location_assumed_unloadtime']; ?>hrs" class="form-control" name="assumed_unloadtime"/>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="portlet">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-tasks"></i>Static Rates
                                                </div>
                                                <div class="actions">
                                                    <a class="btn default red-stripe update_settings" data-form="assets/app/update_settings.php?update=location_settings" data-id="#update_location_rates" data-no-reload>
                                                        <i class="fa fa-download"></i>
                                                        <span class="hidden-480 disabled">Save Rates </span>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                                <form id="update_location_rates" role="form" action="">
                                                    <div class="form-group">
                                                        <label class="control-label">Sales Tax</label>
                                                        <input type="number" placeholder="%<?php echo $location['location_sales_tax'] * 100; ?>" class="form-control" name="sales_tax"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label">Service Tax</label>
                                                        <input type="number" placeholder="%<?php echo $location['location_service_tax'] * 100; ?>" class="form-control" name="service_tax"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label">Credit Card Fees</label>
                                                        <input type="number" placeholder="%<?php echo $location['location_creditcard_fee'] * 100; ?>" class="form-control" name="creditcard_fees"/>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-tags"></i>Services & Rates
                                                </div>
                                                <div class="actions">
                                                    <a class="btn default red-stripe" id="show_add_service">
                                                        <i class="fa fa-plus"></i>
                                                        <span class="hidden-480">New Service/Rate </span>
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
                                                        </select>
                                                        <button class="btn btn-sm red table-group-action-submit"><i class="fa fa-check"></i> Submit</button>
                                                    </div>
                                                    <form role="form" id="add_service_rate">
                                                        <table class="table table-striped table-bordered table-hover" id="service_rates">
                                                            <thead>
                                                                <tr role="row" class="heading">
                                                                    <th width="2%">
                                                                        <input type="checkbox" class="group-checkable">
                                                                    </th>
                                                                    <th width="7%">
                                                                        Service Name
                                                                    </th>
                                                                    <th width="18%">
                                                                        Service Description
                                                                    </th>
                                                                    <th width="8%">
                                                                        Saleprice
                                                                    </th>
                                                                    <th width="8%">
                                                                        Cost
                                                                    </th>
                                                                    <th width="8%">
                                                                        Taxable
                                                                    </th>
                                                                    <th width="8%">
                                                                        Commissionable
                                                                    </th>
                                                                    <th width="10%">
                                                                        Type
                                                                    </th>
                                                                    <th width="12%">
                                                                        Status
                                                                    </th>
                                                                </tr>
                                                                <tr role="row" class="filter hide" id="add_service_item">
                                                                    <td><i class="icon-control-forward"><br/></i>new</td>
                                                                    <td><input type="text" class="form-control form-filter input-sm" name="item"></td>
                                                                    <td></td>
                                                                    <td><input type="number" class="form-control form-filter input-sm" name="saleprice"></td>
                                                                    <td><input type="number" class="form-control form-filter input-sm" name="cost"></td>
                                                                    <td>
                                                                        <select name="taxable" class="form-control form-filter input-sm">
                                                                            <option value="">Select one..</option>
                                                                            <option value="1">Yes</option>
                                                                            <option value="0">No</option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <select name="commissionable" class="form-control form-filter input-sm">
                                                                            <option value="">Select one..</option>
                                                                            <option value="1">Yes</option>
                                                                            <option value="0">No</option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <select name="type" class="form-control form-filter input-sm">
                                                                            <option value="">Select...</option>
                                                                            <option value="Supplies">Supplies</option>
                                                                            <option value="Labor">Labor</option>
                                                                            <option value="Discount">Supplies</option>
                                                                            <option value="Extras">Extras</option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <div class="margin-bottom-5">
                                                                            <button type="button" class="btn btn-sm red margin-bottom add_service_rate"><i class="fa fa-download"></i> Save</button> <button class="btn btn-sm default filter-cancel"><i class="fa fa-times"></i> Reset</button>
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
                                        <!-- End: life time stats -->
                                    </div>
                                </div>
                            </div>


                            <div class="tab-pane" id="tab_1_3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="note note-info">
                                            <p>
                                                NOTE: The software is fully customizable. You set your own settings, that are only used by your company.
                                            </p>
                                        </div>
                                        <div class="portlet">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-tag"></i>Promotional Codes
                                                </div>
                                                <div class="actions">
                                                    <a class="btn default red-stripe">
                                                        <i class="fa fa-plus"></i>
                                                        <span class="hidden-480">Action</span>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                                TODO: add promotional codes stuff
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="tab_1_4">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="note note-info">
                                            <p>
                                                NOTE: The software is fully customizable. You set your own settings, that are only used by your company.
                                            </p>
                                        </div>
                                        <div class="portlet">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-tag"></i>Inventory & Supplies
                                                </div>
                                                <div class="actions">
                                                    <a class="btn default red-stripe">
                                                        <i class="fa fa-plus"></i>
                                                        <span class="hidden-480">Action</span>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                                TODO: add inventory and suppllies stuff
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
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            $(document).on('click', '.edit_line', function(){
                 var line = $(this).val();
                $('.'+line).editable();
                toastr.info("<strong>Ckai says</strong>:<br/>You can now edit that line. To edit, please click the blue underline under the value you'd like to update.")
            });
            $("#add_service_rate").validate({
                errorElement: 'span', //default input error message container
                errorClass: 'font-red', // default input error message class
                rules: {
                    item: {
                        required: true
                    },
                    saleprice: {
                        required: true
                    },
                    cost: {
                        required: true
                    },
                    taxable: {
                        required: true
                    },
                    commissionable: {
                        required: true
                    },
                    type: {
                        required: true
                    }
                }
            });

            var grid = new Datatable();

            grid.init({
                src: $("#service_rates"),
                onSuccess: function (grid) {
                    // execute some code after table records loaded
                },
                onError: function (grid) {
                    // execute some code on network or other general error
                },
                onDataLoad: function(grid) {
                    // execute some code on ajax data load
                },
                loadingMessage: 'Loading...',
                dataTable: { // here you can define a typical datatable settings from http://datatables.net/usage/options

                    // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
                    // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/scripts/datatable.js).
                    // So when dropdowns used the scrollable div should be removed.
                    //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",

                    "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.

                    "lengthMenu": [
                        [10, 20, 50, 100, 150, -1],
                        [10, 20, 50, 100, 150, "All"] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "ajax": {
                        "url": "assets/app/api/service_rates.php?luid=<?php echo $_GET['luid']; ?>", // ajax source
                    },
                    "order": [
                        [1, "asc"]
                    ]// set first column as a default sort by asc
                }
            });

            // handle group actionsubmit button click
            grid.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
                e.preventDefault();
                var action = $(".table-group-action-input", grid.getTableWrapper());
                if (action.val() != "" && grid.getSelectedRowsCount() > 0) {
                    grid.setAjaxParam("customActionType", "group_action");
                    grid.setAjaxParam("customActionName", action.val());
                    grid.setAjaxParam("id", grid.getSelectedRows());
                    grid.getDataTable().ajax.reload();
                    grid.clearAjaxParams();
                } else if (action.val() == "") {
                    Metronic.alert({
                        type: 'danger',
                        icon: 'warning',
                        message: 'Please select an action',
                        container: grid.getTableWrapper(),
                        place: 'prepend'
                    });
                } else if (grid.getSelectedRowsCount() === 0) {
                    Metronic.alert({
                        type: 'danger',
                        icon: 'warning',
                        message: 'No record selected',
                        container: grid.getTableWrapper(),
                        place: 'prepend'
                    });
                }
            });
            $('#show_add_service').on('click', function(){
                $('#add_service_item').removeClass('hide');
            });
            $('.add_service_rate').on('click', function(){
                if($("#add_service_rate").valid()){
                    $.ajax({
                        url: "assets/app/add_setting.php?setting=service_rates&luid=<?php echo $_GET['luid']; ?>",
                        type: "POST",
                        data: $('#add_service_rate').serialize(),
                        success: function(data) {
                            toastr.info('<strong>Ckai says</strong>:<br/>Your changes have been saved to the database. Changes wll take effect in a few moments...');
                            grid.getDataTable().ajax.reload();
                        },
                        error: function() {
                            toastr.error('<strong>Ckai says</strong>:<br/>An unexpected error has occurred. Please try again later.');
                        }
                    });
                }
            });
            $('#location_status').bootstrapSwitch({
                state: <?php echo $location['location_status']; ?>
            });
            $('#location_status').on('switchChange.bootstrapSwitch', function(event, state) {
                $.ajax({
                    url: "assets/app/update_settings.php?update=location_status&luid=<?php echo $_GET['luid']; ?>",
                    type: "POST",
                    data: {
                        status: state
                    },
                    success: function(data) {
                        toastr.info('<strong>Ckai says</strong>:<br/>Your changes have been saved to the database. Changes wll take effect in a few moments...');
                    },
                    error: function() {
                        toastr.error('<strong>Ckai says</strong>:<br/>An unexpected error has occurred. Please try again later.');
                    }
                });
            });
        });
    </script>
    <?php
} else {
    header("Location: ../../../index.php?err=no_access");
}
?>
