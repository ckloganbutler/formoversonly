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
    $location = mysql_fetch_array(mysql_query("SELECT location_manager, location_owner_company_token, location_name, location_phone, location_email, location_token, location_status, location_address, location_address2, location_city, location_state, location_zip, location_county, location_minimum_hours, location_assumed_loadtime, location_assumed_unloadtime, location_sales_tax, location_service_tax, location_creditcard_fee, location_storage_access FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'"));
    ?>
    <div class="page-content">
        <h3 class="page-title">
            <?php echo $location['location_name']; ?> <small>Settings</small>
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
                    <a class="load_page" data-href="assets/pages/manage_location.php?luid=<?php echo $_GET['luid']; ?>" data-act="breadcrumb">Settings</a>
                </li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="portlet light">
                    <div class="portlet-title tabbable-line">
                        <div class="caption caption-md">
                            <i class="icon-settings theme-font bold"></i>
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
                                <a href="#tab_1_3" data-toggle="tab">Storage Units</a>
                            </li>
                            <li>
                                <a href="#tab_1_4" data-toggle="tab">Call Catcher</a>
                            </li>
                            <li>
                                <a href="#service_areas" data-toggle="tab">Misc</a>
                            </li>
                        </ul>
                    </div>
                    <div class="portlet-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab_1_1">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-cogs"></i>Location Details for <?php echo $location['location_name']; ?>
                                                </div>
                                                <div class="actions">
                                                    <a class="btn default red-stripe edit" data-edit="loc" data-reload="">
                                                        <i class="fa fa-pencil"></i>
                                                        <span class="hidden-480">Edit</span>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                                <div class="row static-info">
                                                    <div class="col-md-5 name">
                                                        Manager:
                                                    </div>
                                                    <div class="col-md-7 value">
                                                        <?php
                                                        $managers = mysql_query("SELECT user_token, user_lname, user_fname FROM fmo_users WHERE user_group=".mysql_real_escape_string(2.0)." AND user_employer='".mysql_real_escape_string($_SESSION['cuid'])."'");
                                                        if(mysql_num_rows($managers) > 0){
                                                            while($manager = mysql_fetch_assoc($managers)){
                                                                $source .= "{value: '".$manager['user_token']."', text: '".$manager['user_fname']." ".$manager['user_lname']."'},";
                                                            }
                                                        } else {
                                                            $source = "{value: '', text: 'No managers have been added in this location'}";
                                                        }
                                                        ?>
                                                        <a class="loc" style="color:#333333" data-name="location_manager" data-pk="<?php echo $location['location_token']; ?>" data-type="select" data-source="[<?php echo $source; ?>]" data-placement="right" data-title="Enter new location name.." data-url="assets/app/update_settings.php?update=location">
                                                            <?php
                                                            if(!empty($location['location_manager'])){
                                                                echo name($location['location_manager']);
                                                            } else {
                                                                echo "Nobody selected";
                                                            }
                                                            ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="row static-info">
                                                    <div class="col-md-5 name">
                                                        Name:
                                                    </div>
                                                    <div class="col-md-7 value">
                                                        <a class="loc" style="color:#333333" data-name="location_name" data-pk="<?php echo $location['location_token']; ?>" data-type="text" data-placement="right" data-title="Enter new location name.." data-url="assets/app/update_settings.php?update=location">
                                                            <?php echo $location['location_name']; ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="row static-info">
                                                    <div class="col-md-5 name">
                                                        Phone:
                                                    </div>
                                                    <div class="col-md-7 value">
                                                        <a class="loc" style="color:#333333" data-name="location_phone" data-pk="<?php echo $location['location_token']; ?>" data-type="text" data-placement="right" data-title="Enter new location phone.." data-url="assets/app/update_settings.php?update=location">
                                                            <?php echo clean_phone($location['location_phone']); ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="row static-info">
                                                    <div class="col-md-5 name">
                                                        Email:
                                                    </div>
                                                    <div class="col-md-7 value">
                                                        <a class="loc" style="color:#333333" data-name="location_email" data-pk="<?php echo $location['location_token']; ?>" data-type="email" data-placement="right" data-title="Enter new location email.." data-url="assets/app/update_settings.php?update=location">
                                                            <?php echo $location['location_email']; ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="row static-info">
                                                    <div class="col-md-5 name">
                                                        Address Line 1:
                                                    </div>
                                                    <div class="col-md-7 value">
                                                        <a class="loc" style="color:#333333" data-name="location_address" data-pk="<?php echo $location['location_token']; ?>" data-type="text" data-placement="right" data-title="Enter new location email.." data-url="assets/app/update_settings.php?update=location">
                                                            <?php echo $location['location_address']; ?>
                                                        </a>,
                                                        <a class="loc" style="color:#333333" data-name="location_city" data-pk="<?php echo $location['location_token']; ?>" data-type="text" data-placement="right" data-title="Enter new location email.." data-url="assets/app/update_settings.php?update=location">
                                                            <?php echo $location['location_city']; ?>
                                                        </a>,
                                                        <a class="loc" style="color:#333333" data-name="location_state" data-pk="<?php echo $location['location_token']; ?>" data-type="text" data-placement="right" data-title="Enter new location email.." data-url="assets/app/update_settings.php?update=location">
                                                            <?php echo $location['location_state']; ?>
                                                        </a>,
                                                        <a class="loc" style="color:#333333" data-name="location_zip" data-pk="<?php echo $location['location_token']; ?>" data-type="text" data-placement="right" data-title="Enter new location email.." data-url="assets/app/update_settings.php?update=location">
                                                            <?php echo $location['location_zip']; ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="row static-info">
                                                    <div class="col-md-5 name">
                                                        Address Line 2:
                                                    </div>
                                                    <div class="col-md-7 value">
                                                        <a class="loc" style="color:#333333" data-name="location_address2" data-pk="<?php echo $location['location_token']; ?>" data-type="text" data-placement="right" data-title="Enter new location email.." data-url="assets/app/update_settings.php?update=location">
                                                            <?php echo $location['location_address2']; ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="row static-info">
                                                    <div class="col-md-5 name">
                                                        County:
                                                    </div>
                                                    <div class="col-md-7 value">
                                                        <a class="loc" style="color:#333333" data-name="location_county" data-pk="<?php echo $location['location_token']; ?>" data-type="text" data-placement="right" data-title="Enter new location email.." data-url="assets/app/update_settings.php?update=location">
                                                            <?php echo $location['location_county']; ?>
                                                        </a>
                                                    </div>
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
                                                    <a class="btn default red-stripe update_settings" data-form="assets/app/update_settings.php?update=location_settings&luid=<?php echo $_GET['luid']; ?>" data-id="#update_location_information" data-no-reload>
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
                                                        <input type="number" placeholder="<?php echo $location['location_assumed_loadtime']; ?>hrs" class="form-control" name="assumed_loadtime"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label">Assumed Unload Time (In hours)</label>
                                                        <input type="number" placeholder="<?php echo $location['location_assumed_unloadtime']; ?>hrs" class="form-control" name="assumed_unloadtime"/>
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
                                                    <a class="btn default red-stripe update_settings" data-form="assets/app/update_settings.php?update=location_settings&luid=<?php echo $_GET['luid']; ?>" data-id="#update_location_rates" data-no-reload>
                                                        <i class="fa fa-download"></i>
                                                        <span class="hidden-480 disabled">Save Rates </span>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                                <form id="update_location_rates" role="form" action="">
                                                    <div class="form-group">
                                                        <label class="control-label">Sales Tax</label>
                                                        <input type="number" placeholder="<?php echo $location['location_sales_tax'] * 100; ?>%" class="form-control" name="sales_tax"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label">Service Tax</label>
                                                        <input type="number" placeholder="<?php echo $location['location_service_tax'] * 100; ?>%" class="form-control" name="service_tax"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label">Credit Card Fees</label>
                                                        <input type="number" step="percent" placeholder="<?php echo $location['location_creditcard_fee'] * 100; ?>%" class="form-control" name="creditcard_fee"/>
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
                                                    <i class="fa fa-cogs"></i>Custom Services & Rates <small><span class="font-red">|</span> Add your own services/rates to the system. This helps <strong>Logan</strong> adapt to your company.</small>
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
                                                            <option value="Delete" class="font-red">Delete</option>
                                                        </select>
                                                        <button class="btn btn-sm red table-group-action-submit"><i class="fa fa-check"></i> Submit</button>
                                                    </div>
                                                    <form role="form" id="add_service_rate">
                                                        <table class="table table-striped table-bordered table-hover" id="service_rates">
                                                            <thead>
                                                                <tr role="row" class="heading">
                                                                    <th width="4.7%">
                                                                        <input type="checkbox" class="group-checkable"> Status
                                                                    </th>
                                                                    <th width="9%">
                                                                        Service Name
                                                                    </th>
                                                                    <th width="18%">
                                                                        Service Description
                                                                    </th>
                                                                    <th width="9%">
                                                                        Saleprice
                                                                    </th>
                                                                    <th width="9%">
                                                                        Cost
                                                                    </th>
                                                                    <th width="9%">
                                                                        Taxable
                                                                    </th>
                                                                    <th width="9%">
                                                                        Commissionable
                                                                    </th>
                                                                    <th width="10%">
                                                                        Type
                                                                    </th>
                                                                    <th width="2.5%">
                                                                        In-line edit
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
                                                                            <option value="Discount">Discount</option>
                                                                            <option value="Extras">Extras</option>
                                                                            <option value="Other">Other</option>
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

                            <div class="tab-pane" id="service_areas">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="portlet">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-cogs"></i>Counties <small><span class="font-red">|</span> These are the names of counties you service.</small>
                                                </div>
                                                <div class="actions">
                                                    <a class="btn default red-stripe" id="show_add_county">
                                                        <i class="fa fa-plus"></i>
                                                        <span class="hidden-480">Add County </span>
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
                                                            <option value="Delete" class="font-red">Delete</option>
                                                        </select>
                                                        <button class="btn btn-sm red table-group-action-submit"><i class="fa fa-check"></i> Submit</button>
                                                    </div>
                                                    <form role="form" id="add_county_form">
                                                        <table class="table table-striped table-bordered table-hover" id="service_counties">
                                                            <thead>
                                                            <tr role="row" class="heading">
                                                                <th width="20%">
                                                                    <input type="checkbox" class="group-checkable"> Status
                                                                </th>
                                                                <th width="50%">
                                                                    County
                                                                </th>
                                                                <th width="20%">
                                                                    In-line edit
                                                                </th>
                                                            </tr>
                                                            <tr role="row" class="filter hide" id="add_county">
                                                                <td></td>
                                                                <td><input type="text" class="form-control form-filter input-sm" name="county" id="county"></td>
                                                                <td>
                                                                    <div class="margin-bottom-5">
                                                                        <button type="button" class="btn btn-sm red margin-bottom submit_county_form"><i class="fa fa-download"></i> Save</button> <button class="btn btn-sm default filter-cancel"><i class="fa fa-times"></i> Reset</button>
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
                                        <div class="portlet">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-cogs"></i>Zip Codes <small><span class="font-red">|</span> These are the zip codes of areas you service.</small>
                                                </div>
                                                <div class="actions">
                                                    <a class="btn default red-stripe" id="show_add_zipcode">
                                                        <i class="fa fa-plus"></i>
                                                        <span class="hidden-480">Add Zip Code </span>
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
                                                            <option value="Delete" class="font-red">Delete</option>
                                                        </select>
                                                        <button class="btn btn-sm red table-group-action-submit"><i class="fa fa-check"></i> Submit</button>
                                                    </div>
                                                    <form role="form" id="add_zipcodes_form">
                                                        <table class="table table-striped table-bordered table-hover" id="service_zipcodes">
                                                            <thead>
                                                            <tr role="row" class="heading">
                                                                <th width="20%">
                                                                    <input type="checkbox" class="group-checkable"> Status
                                                                </th>
                                                                <th width="50%">
                                                                    Zip Code
                                                                </th>
                                                                <th width="20%">
                                                                    In-line edit
                                                                </th>
                                                            </tr>
                                                            <tr role="row" class="filter hide" id="add_zipcode">
                                                                <td></td>
                                                                <td><input type="number" class="form-control form-filter input-sm" name="code"></td>
                                                                <td>
                                                                    <div class="margin-bottom-5">
                                                                        <button type="button" class="btn btn-sm red margin-bottom submit_zipcodes_form"><i class="fa fa-download"></i> Save</button> <button class="btn btn-sm default filter-cancel"><i class="fa fa-times"></i> Reset</button>
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
                                        <div class="portlet">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-cogs"></i>Event Times <small><span class="font-red">|</span> These are times in which events can be made (or reserved).</small>
                                                </div>
                                                <div class="actions">
                                                    <a class="btn default red-stripe" id="show_add_times">
                                                        <i class="fa fa-plus"></i>
                                                        <span class="hidden-480">Add Time </span>
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
                                                            <option value="Delete" class="font-red">Delete</option>
                                                        </select>
                                                        <button class="btn btn-sm red table-group-action-submit"><i class="fa fa-check"></i> Submit</button>
                                                    </div>
                                                    <form role="form" id="add_times_form">
                                                        <table class="table table-striped table-bordered table-hover" id="times">
                                                            <thead>
                                                            <tr role="row" class="heading">
                                                                <th width="20%">
                                                                    <input type="checkbox" class="group-checkable"> Status
                                                                </th>
                                                                <th width="50%">
                                                                    Zip Code
                                                                </th>
                                                                <th width="20%">
                                                                    In-line edit
                                                                </th>
                                                            </tr>
                                                            <tr role="row" class="filter hide" id="add_times">
                                                                <td></td>
                                                                <td>
                                                                    <div class="input-group input-sm">
                                                                        <input type="text" class="form-control timepicker timepicker-no-seconds" name="starttime">
                                                                        <span class="input-group-addon"> to </span>
                                                                        <input type="text" class="form-control timepicker timepicker-no-seconds" name="endtime">
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="margin-bottom-5">
                                                                        <button type="button" class="btn btn-sm red margin-bottom submit_times_form"><i class="fa fa-download"></i> Save</button> <button class="btn btn-sm default filter-cancel"><i class="fa fa-times"></i> Reset</button>
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
                                    <div class="col-md-6">
                                        <div class="portlet">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-cogs"></i>How Hear <small><span class="font-red">|</span> How people hear about you (powers survey).</small>
                                                </div>
                                                <div class="actions">
                                                    <a class="btn default red-stripe" id="show_add_hear">
                                                        <i class="fa fa-plus"></i>
                                                        <span class="hidden-480">Add How Hear </span>
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
                                                            <option value="Delete" class="font-red">Delete</option>
                                                        </select>
                                                        <button class="btn btn-sm red table-group-action-submit"><i class="fa fa-check"></i> Submit</button>
                                                    </div>
                                                    <form role="form" id="add_hear_form">
                                                        <table class="table table-striped table-bordered table-hover" id="howhear">
                                                            <thead>
                                                            <tr role="row" class="heading">
                                                                <th width="20%">
                                                                    <input type="checkbox" class="group-checkable"> Status
                                                                </th>
                                                                <th width="50%">
                                                                    Reference
                                                                </th>
                                                                <th width="20%">
                                                                    In-line edit
                                                                </th>
                                                            </tr>
                                                            <tr role="row" class="filter hide" id="add_hear">
                                                                <td></td>
                                                                <td><input type="text" class="form-control form-filter input-sm" name="hear" id="hear"></td>
                                                                <td>
                                                                    <div class="margin-bottom-5">
                                                                        <button type="button" class="btn btn-sm red margin-bottom submit_hear_form"><i class="fa fa-download"></i> Save</button> <button class="btn btn-sm default filter-cancel"><i class="fa fa-times"></i> Reset</button>
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
                                        <div class="portlet">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-cogs"></i>Event Types <small><span class="font-red">|</span> Different types of events that your company partakes in.</small>
                                                </div>
                                                <div class="actions">
                                                    <a class="btn default red-stripe" id="show_add_eventtype">
                                                        <i class="fa fa-plus"></i>
                                                        <span class="hidden-480">Add Type </span>
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
                                                            <option value="Delete" class="font-red">Delete</option>
                                                        </select>
                                                        <button class="btn btn-sm red table-group-action-submit"><i class="fa fa-check"></i> Submit</button>
                                                    </div>
                                                    <form role="form" id="add_eventtype_form">
                                                        <table class="table table-striped table-bordered table-hover" id="eventtype">
                                                            <thead>
                                                            <tr role="row" class="heading">
                                                                <th width="20%">
                                                                    <input type="checkbox" class="group-checkable"> Status
                                                                </th>
                                                                <th width="50%">
                                                                    Reference
                                                                </th>
                                                                <th width="20%">
                                                                    In-line edit
                                                                </th>
                                                            </tr>
                                                            <tr role="row" class="filter hide" id="add_eventtype">
                                                                <td></td>
                                                                <td><input type="text" class="form-control form-filter input-sm" name="eventtype" id="eventtype"></td>
                                                                <td>
                                                                    <div class="margin-bottom-5">
                                                                        <button type="button" class="btn btn-sm red margin-bottom submit_eventtype_form"><i class="fa fa-download"></i> Save</button> <button class="btn btn-sm default filter-cancel"><i class="fa fa-times"></i> Reset</button>
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
                                        <div class="portlet">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-cogs"></i>Event Sub Types <small><span class="font-red">|</span> How people hear about you (powers survey).</small>
                                                </div>
                                                <div class="actions">
                                                    <a class="btn default red-stripe" id="show_add_subtype">
                                                        <i class="fa fa-plus"></i>
                                                        <span class="hidden-480">Add Subtype </span>
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
                                                            <option value="Delete" class="font-red">Delete</option>
                                                        </select>
                                                        <button class="btn btn-sm red table-group-action-submit"><i class="fa fa-check"></i> Submit</button>
                                                    </div>
                                                    <form role="form" id="add_subtype_form">
                                                        <table class="table table-striped table-bordered table-hover" id="subtype">
                                                            <thead>
                                                            <tr role="row" class="heading">
                                                                <th width="20%">
                                                                    <input type="checkbox" class="group-checkable"> Status
                                                                </th>
                                                                <th width="50%">
                                                                    Reference
                                                                </th>
                                                                <th width="20%">
                                                                    In-line edit
                                                                </th>
                                                            </tr>
                                                            <tr role="row" class="filter hide" id="add_subtype">
                                                                <td></td>
                                                                <td><input type="text" class="form-control form-filter input-sm" name="subtype" id="subtype"></td>
                                                                <td>
                                                                    <div class="margin-bottom-5">
                                                                        <button type="button" class="btn btn-sm red margin-bottom submit_subtype_form"><i class="fa fa-download"></i> Save</button> <button class="btn btn-sm default filter-cancel"><i class="fa fa-times"></i> Reset</button>
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


                            <div class="tab-pane" id="tab_1_3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="note note-info">
                                            <p>
                                                NOTE: This is an experimental feature.
                                            </p>
                                        </div>
                                        <div class="portlet">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-cubes"></i>Storage Units  <small><span class="font-red">|</span> If your location has storage, you can add their specifcations here.</small>
                                                </div>
                                                <div class="actions">
                                                    <a class="btn default red-stripe">
                                                        <i class="fa fa-cube"></i>
                                                        <span class="hidden-480 disabled" id="show_add_storage">Add Unit Option</span>
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
                                                            <option value="changeAvailabilityAvailable">Make Available</option>
                                                            <option value="changeAvailabilityUnAvailable">Make Un-Available</option>
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
                                                            <option value="delete" class="font-red">Delete</option>
                                                        </select>
                                                        <button class="btn btn-sm red table-group-action-submit"><i class="fa fa-check"></i> Submit</button>
                                                    </div>
                                                    <form role="form" id="add_storage_form">
                                                        <table class="table table-striped table-bordered table-hover" id="storage">
                                                            <thead>
                                                            <tr role="row" class="heading">
                                                                <th width="20%">
                                                                    <input type="checkbox" class="group-checkable"> Available
                                                                </th>
                                                                <th width="20%">
                                                                    Unit Specifications (LxWxH)
                                                                </th>
                                                                <th width="20">
                                                                    Price/Period ($10/Month)
                                                                </th>
                                                                <th width="20%">
                                                                    In-line edit
                                                                </th>
                                                            </tr>
                                                            <tr role="row" class="filter hide" id="add_storage">
                                                                <td>
                                                                    <select name="available" class="form-control form-filter input-sm">
                                                                        <option value="">Select one..</option>
                                                                        <option value="1">Yes</option>
                                                                        <option value="0">No</option>
                                                                    </select>
                                                                </td>
                                                                <td><input type="text" class="form-control form-filter input-sm" name="unit" id="unit"></td>
                                                                <td><input type="text" class="form-control form-filter input-sm" name="priceperiod"></td>
                                                                <td>
                                                                    <div class="margin-bottom-5">
                                                                        <button type="button" class="btn btn-sm red margin-bottom submit_storage_form"><i class="fa fa-download"></i> Save</button> <button class="btn btn-sm default filter-cancel"><i class="fa fa-times"></i> Reset</button>
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

                            <div class="tab-pane" id="tab_1_4">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-phone"></i>Call Catcher Weekly Rates
                                                </div>
                                                <div class="actions">
                                                    <a class="btn default red-stripe">
                                                        <i class="fa fa-download"></i>
                                                        <span class="hidden-480">Save Changes</span>
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
                                                            <option value="changeAvailabilityAvailable">Make Available</option>
                                                            <option value="changeAvailabilityUnAvailable">Make Un-Available</option>
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
                                                            <option value="delete" class="font-red">Delete</option>
                                                        </select>
                                                        <button class="btn btn-sm red table-group-action-submit"><i class="fa fa-check"></i> Submit</button>
                                                    </div>
                                                    <?php

                                                    $item = array();
                                                    $days = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
                                                    foreach($days as $day){
                                                        $query = mysql_query("SELECT ".mysql_real_escape_string($day)."_truck_fee, ".mysql_real_escape_string($day)."_labor_rate, ".mysql_real_escape_string($day)."_truck_rate, ".mysql_real_escape_string($day)."_upcharge FROM fmo_locations_rates_".mysql_real_escape_string($day)." WHERE ".mysql_real_escape_string($day)."_location_token='".mysql_real_escape_string($_GET['luid'])."'") or die(mysql_error());
                                                        if(mysql_num_rows($query) > 0){
                                                            $inf = mysql_fetch_array($query);
                                                            $item[$day]["_truck_fee"]   = $inf[$day.'_truck_fee'];
                                                            $item[$day]["_labor_rate"]  = $inf[$day.'_labor_rate'];
                                                            $item[$day]["_truck_rate"]  = $inf[$day.'_truck_rate'];
                                                            $item[$day]["_upcharge"]    = $inf[$day.'_upcharge'];
                                                        }
                                                    }

                                                    ?>
                                                    <form role="form" id="call_catcher_setup">
                                                        <table class="table table-striped table-bordered table-hover" id="call_catcher_table">
                                                            <thead>
                                                            <tr role="row" class="heading text-center">
                                                                <th>
                                                                    Sunday
                                                                </th>
                                                                <th>
                                                                    Monday
                                                                </th>
                                                                <th>
                                                                    Tuesday
                                                                </th>
                                                                <th>
                                                                    Wednesday
                                                                </th>
                                                                <th>
                                                                    Thursday
                                                                </th>
                                                                <th>
                                                                    Friday
                                                                </th>
                                                                <th>
                                                                    Saturday
                                                                </th>
                                                            </tr>
                                                            <tr role="row" class="filter" id="add_storage">
                                                                <td>
                                                                    <div class="form-group">
                                                                        <label>Truck Fee</label>
                                                                        <div class="input-icon">
                                                                            <i class="fa fa-truck"></i>
                                                                            <input type="number" class="form-control catcher_item" name="sunday_truck_fee" id="sunday_truck_fee" value="<?php echo $item['sunday']['_truck_fee']; ?>">
                                                                        </div>
                                                                    </div>
                                                                    <hr/>
                                                                    <div class="form-group">
                                                                        <label>Labor Rate</label>
                                                                        <div class="input-icon">
                                                                            <i class="fa fa-child"></i>
                                                                            <input type="text" class="form-control catcher_item" name="sunday_labor_rate" id="sunday_labor_rate" value="<?php echo $item['sunday']['_labor_rate']; ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Truck Rate</label>
                                                                        <div class="input-icon">
                                                                            <i class="fa fa-truck"></i>
                                                                            <input type="text" class="form-control catcher_item" name="sunday_truck_rate" id="sunday_truck_rate" value="<?php echo $item['sunday']['_truck_rate']; ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Weekend Upcharge</label>
                                                                        <div class="input-icon">
                                                                            <i class="fa fa-child"></i>
                                                                            <input type="text" class="form-control catcher_item" name="sunday_upcharge" id="sunday_upcharge" value="<?php echo $item['sunday']['_upcharge']; ?>">
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <label>Truck Fee</label>
                                                                        <div class="input-icon">
                                                                            <i class="fa fa-truck"></i>
                                                                            <input type="text" class="form-control catcher_item" name="monday_truck_fee" id="monday_truck_fee" value="<?php echo $item['monday']['_truck_fee']; ?>">
                                                                        </div>
                                                                    </div>
                                                                    <hr/>
                                                                    <div class="form-group">
                                                                        <label>Labor Rate</label>
                                                                        <div class="input-icon">
                                                                            <i class="fa fa-child"></i>
                                                                            <input type="text" class="form-control catcher_item" name="monday_labor_rate" id="monday_labor_rate" value="<?php echo $item['monday']['_labor_rate']; ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Truck Rate</label>
                                                                        <div class="input-icon">
                                                                            <i class="fa fa-truck"></i>
                                                                            <input type="text" class="form-control catcher_item" name="monday_truck_rate" id="monday_truck_rate" value="<?php echo $item['monday']['_truck_rate']; ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Weekend Upcharge</label>
                                                                        <div class="input-icon">
                                                                            <i class="fa fa-child"></i>
                                                                            <input type="text" class="form-control catcher_item" name="monday_upcharge" id="monday_upcharge" value="<?php echo $item['monday']['_upcharge']; ?>">
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <label>Truck Fee</label>
                                                                        <div class="input-icon">
                                                                            <i class="fa fa-truck"></i>
                                                                            <input type="text" class="form-control catcher_item" name="tuesday_truck_fee" id="tuesday_truck_fee" value="<?php echo $item['tuesday']['_truck_fee']; ?>">
                                                                        </div>
                                                                    </div>
                                                                    <hr/>
                                                                    <div class="form-group">
                                                                        <label>Labor Rate</label>
                                                                        <div class="input-icon">
                                                                            <i class="fa fa-child"></i>
                                                                            <input type="text" class="form-control catcher_item" name="tuesday_labor_rate" id="tuesday_labor_rate" value="<?php echo $item['tuesday']['_labor_rate']; ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Truck Rate</label>
                                                                        <div class="input-icon">
                                                                            <i class="fa fa-truck"></i>
                                                                            <input type="text" class="form-control catcher_item" name="tuesday_truck_rate" id="tuesday_truck_rate" value="<?php echo $item['tuesday']['_truck_rate']; ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Weekend Upcharge</label>
                                                                        <div class="input-icon">
                                                                            <i class="fa fa-child"></i>
                                                                            <input type="text" class="form-control catcher_item" name="tuesday_upcharge" id="tuesday_upcharge" value="<?php echo $item['tuesday']['_upcharge']; ?>">
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <label>Truck Fee</label>
                                                                        <div class="input-icon">
                                                                            <i class="fa fa-truck"></i>
                                                                            <input type="text" class="form-control catcher_item" name="wednesday_truck_fee" id="wednesday_truck_fee" value="<?php echo $item['wednesday']['_truck_fee']; ?>">
                                                                        </div>
                                                                    </div>
                                                                    <hr/>
                                                                    <div class="form-group">
                                                                        <label>Labor Rate</label>
                                                                        <div class="input-icon">
                                                                            <i class="fa fa-child"></i>
                                                                            <input type="text" class="form-control catcher_item" name="wednesday_labor_rate" id="wednesday_labor_rate" value="<?php echo $item['wednesday']['_labor_rate']; ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Truck Rate</label>
                                                                        <div class="input-icon">
                                                                            <i class="fa fa-truck"></i>
                                                                            <input type="text" class="form-control catcher_item" name="wednesday_truck_rate" id="wednesday_truck_rate" value="<?php echo $item['wednesday']['_truck_rate']; ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Weekend Upcharge</label>
                                                                        <div class="input-icon">
                                                                            <i class="fa fa-child"></i>
                                                                            <input type="text" class="form-control catcher_item" name="wednesday_upcharge" id="wednesday_upcharge" value="<?php echo $item['wednesday']['_upcharge']; ?>">
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <label>Truck Fee</label>
                                                                        <div class="input-icon">
                                                                            <i class="fa fa-truck"></i>
                                                                            <input type="text" class="form-control catcher_item" name="thursday_truck_fee" id="thursday_truck_fee" value="<?php echo $item['thursday']['_truck_fee']; ?>">
                                                                        </div>
                                                                    </div>
                                                                    <hr/>
                                                                    <div class="form-group">
                                                                        <label>Labor Rate</label>
                                                                        <div class="input-icon">
                                                                            <i class="fa fa-child"></i>
                                                                            <input type="text" class="form-control catcher_item" name="thursday_labor_rate" id="thursday_labor_rate" value="<?php echo $item['thursday']['_labor_rate']; ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Truck Rate</label>
                                                                        <div class="input-icon">
                                                                            <i class="fa fa-truck"></i>
                                                                            <input type="text" class="form-control catcher_item" name="thursday_truck_rate" id="thursday_truck_rate" value="<?php echo $item['thursday']['_truck_rate']; ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Weekend Upcharge</label>
                                                                        <div class="input-icon">
                                                                            <i class="fa fa-child"></i>
                                                                            <input type="text" class="form-control catcher_item" name="thursday_upcharge" id="thursday_upcharge" value="<?php echo $item['thursday']['_upcharge']; ?>">
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <label>Truck Fee</label>
                                                                        <div class="input-icon">
                                                                            <i class="fa fa-truck"></i>
                                                                            <input type="text" class="form-control catcher_item" name="friday_truck_fee" id="friday_truck_fee" value="<?php echo $item['friday']['_truck_fee']; ?>">
                                                                        </div>
                                                                    </div>
                                                                    <hr/>
                                                                    <div class="form-group">
                                                                        <label>Labor Rate</label>
                                                                        <div class="input-icon">
                                                                            <i class="fa fa-child"></i>
                                                                            <input type="text" class="form-control catcher_item" name="friday_labor_rate" id="friday_labor_rate"  value="<?php echo $item['friday']['_labor_rate']; ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Truck Rate</label>
                                                                        <div class="input-icon">
                                                                            <i class="fa fa-truck"></i>
                                                                            <input type="text" class="form-control catcher_item" name="friday_truck_rate" id="friday_truck_rate"  value="<?php echo $item['friday']['_truck_rate']; ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Weekend Upcharge</label>
                                                                        <div class="input-icon">
                                                                            <i class="fa fa-child"></i>
                                                                            <input type="text" class="form-control catcher_item"  name="friday_upcharge" id="friday_upcharge"  value="<?php echo $item['friday']['_upcharge']; ?>">
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <label>Truck Fee</label>
                                                                        <div class="input-icon">
                                                                            <i class="fa fa-truck"></i>
                                                                            <input type="text" class="form-control catcher_item" name="saturday_truck_fee" id="saturday_truck_fee"  value="<?php echo $item['saturday']['_truck_fee']; ?>">
                                                                        </div>
                                                                    </div>
                                                                    <hr/>
                                                                    <div class="form-group">
                                                                        <label>Labor Rate</label>
                                                                        <div class="input-icon">
                                                                            <i class="fa fa-child"></i>
                                                                            <input type="text" class="form-control catcher_item" name="saturday_labor_rate" id="saturday_labor_rate" value="<?php echo $item['saturday']['_labor_rate']; ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Truck Rate</label>
                                                                        <div class="input-icon">
                                                                            <i class="fa fa-truck"></i>
                                                                            <input type="text" class="form-control catcher_item" name="saturday_truck_rate" id="saturday_truck_rate" value="<?php echo $item['saturday']['_truck_rate']; ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Weekend Upcharge</label>
                                                                        <div class="input-icon">
                                                                            <i class="fa fa-child"></i>
                                                                            <input type="text" class="form-control catcher_item" name="saturday_upcharge" id="saturday_upcharge" value="<?php echo $item['saturday']['_upcharge']; ?>">
                                                                        </div>
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

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            var date = new Date();
            date.setDate(date.getDate()-1);
            $('.date-picker').datepicker({
                startDate: date
            });
            $('.timepicker-no-seconds').timepicker({
                autoclose: true
            });
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
            $(document).on('focusout', '.catcher_item', function(){
                $.ajax({
                    url: 'assets/app/api/catcher.php?p=jvk',
                    type: 'POST',
                    data: {
                        f: $(this).attr('name'),
                        v: $(this).val(),
                        l: '<?php echo $_GET['luid']; ?>'
                    },
                    success: function(e){
                        toastr.info("<strong>Logan says</strong>:<br/>Information has been saved to the database successfully.")
                    },
                    error: function(e){

                    }
                })
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
            $("#add_county_form").validate({
                errorElement: 'span', //default input error message container
                errorClass: 'font-red', // default input error message class
                rules: {
                    county: {
                        required: true
                    }
                }
            });
            $("#add_hear_form").validate({
                errorElement: 'span', //default input error message container
                errorClass: 'font-red', // default input error message class
                rules: {
                    hear: {
                        required: true
                    }
                }
            });
            $("#add_eventtype_form").validate({
                errorElement: 'span', //default input error message container
                errorClass: 'font-red', // default input error message class
                rules: {
                    eventtype: {
                        required: true
                    }
                }
            });
            $("#add_subtype_form").validate({
                errorElement: 'span', //default input error message container
                errorClass: 'font-red', // default input error message class
                rules: {
                    subtype: {
                        required: true
                    }
                }
            });
            $("#add_storage_form").validate({
                errorElement: 'span', //default input error message container
                errorClass: 'font-red', // default input error message class
                rules: {
                    available: {
                        required: true
                    },
                    unit: {
                        required: true
                    },
                    priceperiod: {
                        required: true
                    }
                }
            });
            $("#add_zipcode_form").validate({
                errorElement: 'span', //default input error message container
                errorClass: 'font-red', // default input error message class
                rules: {
                    code: {
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
                dataTable: {
                    "processing": true,
                    "serverSide": true,
                    //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                    "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
                    "bPaginate": false,
                    "ajax": {
                        "url": "assets/app/api/service_rates.php?luid=<?php echo $_GET['luid']; ?>", // ajax source
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

            var counties = new Datatable();

            counties.init({
                src: $("#service_counties"),
                onSuccess: function (counties) {
                    // execute some code after table records loaded
                },
                onError: function (countiesv) {
                    // execute some code on network or other general error
                },
                onDataLoad: function(counties) {
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
                        "url": "assets/app/api/service_areas.php?type=county&luid=<?php echo $_GET['luid']; ?>", // ajax source
                    },
                }
            });

            // handle group actionsubmit button click
            counties.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
                e.preventDefault();
                var action = $(".table-group-action-input", counties.getTableWrapper());
                if (action.val() != "" && grid.getSelectedRowsCount() > 0) {
                    counties.setAjaxParam("customActionType", "group_action");
                    counties.setAjaxParam("customActionName", action.val());
                    counties.setAjaxParam("id", counties.getSelectedRows());
                    counties.getDataTable().ajax.reload();
                    counties.clearAjaxParams();
                } else if (action.val() == "") {
                    Metronic.alert({
                        type: 'danger',
                        icon: 'warning',
                        message: 'Please select an action',
                        container: counties.getTableWrapper(),
                        place: 'prepend'
                    });
                } else if (counties.getSelectedRowsCount() === 0) {
                    Metronic.alert({
                        type: 'danger',
                        icon: 'warning',
                        message: 'No record selected',
                        container: counties.getTableWrapper(),
                        place: 'prepend'
                    });
                }
            });

            var zipcodes = new Datatable();

            zipcodes.init({
                src: $("#service_zipcodes"),
                onSuccess: function (zipcodes) {
                },
                onError: function (zipcodes) {
                },
                onDataLoad: function(zipcodesd) {
                },
                loadingMessage: 'Loading...',
                dataTable: {
                    "processing": true,
                    "serverSide": true,
                    //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                    "bStateSave": true,
                    "bPaginate": false,
                    "ajax": {
                        "url": "assets/app/api/service_areas.php?type=zipcodes&luid=<?php echo $_GET['luid']; ?>",
                    },
                }
            });

            zipcodes.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
                e.preventDefault();
                var action = $(".table-group-action-input", zipcodes.getTableWrapper());
                if (action.val() != "" && zipcodes.getSelectedRowsCount() > 0) {
                    zipcodes.setAjaxParam("customActionType", "group_action");
                    zipcodes.setAjaxParam("customActionName", action.val());
                    zipcodes.setAjaxParam("id", zipcodes.getSelectedRows());
                    zipcodes.getDataTable().ajax.reload();
                    zipcodes.clearAjaxParams();
                } else if (action.val() == "") {
                    Metronic.alert({
                        type: 'danger',
                        icon: 'warning',
                        message: 'Please select an action',
                        container: zipcodes.getTableWrapper(),
                        place: 'prepend'
                    });
                } else if (zipcodes.getSelectedRowsCount() === 0) {
                    Metronic.alert({
                        type: 'danger',
                        icon: 'warning',
                        message: 'No record selected',
                        container: zipcodes.getTableWrapper(),
                        place: 'prepend'
                    });
                }
            });
            var storage = new Datatable();

            storage.init({
                src: $("#storage"),
                onSuccess: function (storage) {
                },
                onError: function (storage) {
                },
                onDataLoad: function() {
                },
                loadingMessage: 'Loading...',
                dataTable: {
                    "processing": true,
                    "serverSide": true,
                    //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                    "bStateSave": true,
                    "bPaginate": false,
                    "ajax": {
                        "url": "assets/app/api/service_storage.php?luid=<?php echo $_GET['luid']; ?>",
                    },
                }
            });
            storage.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
                e.preventDefault();
                var action = $(".table-group-action-input", storage.getTableWrapper());
                if (action.val() != "" && storage.getSelectedRowsCount() > 0) {
                    storage.setAjaxParam("customActionType", "group_action");
                    storage.setAjaxParam("customActionName", action.val());
                    storage.setAjaxParam("id", storage.getSelectedRows());
                    storage.getDataTable().ajax.reload();
                    storage.clearAjaxParams();
                } else if (action.val() == "") {
                    Metronic.alert({
                        type: 'danger',
                        icon: 'warning',
                        message: 'Please select an action',
                        container: storage.getTableWrapper(),
                        place: 'prepend'
                    });
                } else if (storage.getSelectedRowsCount() === 0) {
                    Metronic.alert({
                        type: 'danger',
                        icon: 'warning',
                        message: 'No record selected',
                        container: storage.getTableWrapper(),
                        place: 'prepend'
                    });
                }
            });
            var howhear = new Datatable();

            howhear.init({
                src: $("#howhear"),
                onSuccess: function (askOut) {
                },
                onError: function (howhear) {
                },
                onDataLoad: function() {
                },
                loadingMessage: 'Loading...',
                dataTable: {
                    "processing": true,
                    "serverSide": true,
                    //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                    "bStateSave": true,
                    "bPaginate": false,
                    "ajax": {
                        "url": "assets/app/api/howhear.php?luid=<?php echo $_GET['luid']; ?>",
                    },
                }
            });
            howhear.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
                e.preventDefault();
                var action = $(".table-group-action-input", howhear.getTableWrapper());
                if (action.val() != "" && howhear.getSelectedRowsCount() > 0) {
                    howhear.setAjaxParam("customActionType", "group_action");
                    howhear.setAjaxParam("customActionName", action.val());
                    howhear.setAjaxParam("id", howhear.getSelectedRows());
                    howhear.getDataTable().ajax.reload();
                    howhear.clearAjaxParams();
                } else if (action.val() == "") {
                    Metronic.alert({
                        type: 'danger',
                        icon: 'warning',
                        message: 'Please select an action',
                        container: howhear.getTableWrapper(),
                        place: 'prepend'
                    });
                } else if (howhear.getSelectedRowsCount() === 0) {
                    Metronic.alert({
                        type: 'danger',
                        icon: 'warning',
                        message: 'No record selected',
                        container: howhear.getTableWrapper(),
                        place: 'prepend'
                    });
                }
            });
            var subtype = new Datatable();

            subtype.init({
                src: $("#subtype"),
                onSuccess: function (askOut) {
                },
                onError: function (subtype) {
                },
                onDataLoad: function() {
                },
                loadingMessage: 'Loading...',
                dataTable: {
                    "processing": true,
                    "serverSide": true,
                    //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                    "bStateSave": true,
                    "bPaginate": false,
                    "ajax": {
                        "url": "assets/app/api/subtype.php?luid=<?php echo $_GET['luid']; ?>",
                    },
                }
            });
            subtype.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
                e.preventDefault();
                var action = $(".table-group-action-input", subtype.getTableWrapper());
                if (action.val() != "" && subtype.getSelectedRowsCount() > 0) {
                    subtype.setAjaxParam("customActionType", "group_action");
                    subtype.setAjaxParam("customActionName", action.val());
                    subtype.setAjaxParam("id", subtype.getSelectedRows());
                    subtype.getDataTable().ajax.reload();
                    subtype.clearAjaxParams();
                } else if (action.val() == "") {
                    Metronic.alert({
                        type: 'danger',
                        icon: 'warning',
                        message: 'Please select an action',
                        container: subtype.getTableWrapper(),
                        place: 'prepend'
                    });
                } else if (subtype.getSelectedRowsCount() === 0) {
                    Metronic.alert({
                        type: 'danger',
                        icon: 'warning',
                        message: 'No record selected',
                        container: subtype.getTableWrapper(),
                        place: 'prepend'
                    });
                }
            });
            var eventtype = new Datatable();

            eventtype.init({
                src: $("#eventtype"),
                onSuccess: function (askOut) {
                },
                onError: function (eventtype) {
                },
                onDataLoad: function() {
                },
                loadingMessage: 'Loading...',
                dataTable: {
                    "processing": true,
                    "serverSide": true,
                    //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                    "bStateSave": true,
                    "bPaginate": false,
                    "ajax": {
                        "url": "assets/app/api/eventtype.php?luid=<?php echo $_GET['luid']; ?>",
                    },
                }
            });
            eventtype.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
                e.preventDefault();
                var action = $(".table-group-action-input", eventtype.getTableWrapper());
                if (action.val() != "" && eventtype.getSelectedRowsCount() > 0) {
                    eventtype.setAjaxParam("customActionType", "group_action");
                    eventtype.setAjaxParam("customActionName", action.val());
                    eventtype.setAjaxParam("id", eventtype.getSelectedRows());
                    eventtype.getDataTable().ajax.reload();
                    eventtype.clearAjaxParams();
                } else if (action.val() == "") {
                    Metronic.alert({
                        type: 'danger',
                        icon: 'warning',
                        message: 'Please select an action',
                        container: eventtype.getTableWrapper(),
                        place: 'prepend'
                    });
                } else if (eventtype.getSelectedRowsCount() === 0) {
                    Metronic.alert({
                        type: 'danger',
                        icon: 'warning',
                        message: 'No record selected',
                        container: eventtype.getTableWrapper(),
                        place: 'prepend'
                    });
                }
            });
            var times = new Datatable();

            times.init({
                src: $("#times"),
                onSuccess: function (askOut) {
                },
                onError: function (times) {
                },
                onDataLoad: function() {
                },
                loadingMessage: 'Loading...',
                dataTable: {
                    "processing": true,
                    "serverSide": true,
                    //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                    "bStateSave": true,
                    "bPaginate": false,
                    "ajax": {
                        "url": "assets/app/api/times.php?luid=<?php echo $_GET['luid']; ?>",
                    },
                }
            });
            times.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
                e.preventDefault();
                var action = $(".table-group-action-input", times.getTableWrapper());
                if (action.val() != "" && times.getSelectedRowsCount() > 0) {
                    times.setAjaxParam("customActionType", "group_action");
                    times.setAjaxParam("customActionName", action.val());
                    times.setAjaxParam("id", times.getSelectedRows());
                    times.getDataTable().ajax.reload();
                    times.clearAjaxParams();
                } else if (action.val() == "") {
                    Metronic.alert({
                        type: 'danger',
                        icon: 'warning',
                        message: 'Please select an action',
                        container: times.getTableWrapper(),
                        place: 'prepend'
                    });
                } else if (times.getSelectedRowsCount() === 0) {
                    Metronic.alert({
                        type: 'danger',
                        icon: 'warning',
                        message: 'No record selected',
                        container: times.getTableWrapper(),
                        place: 'prepend'
                    });
                }
            });
            $('#show_add_times').on('click', function(){
                $('#add_times').removeClass('hide');
            });
            if($("#add_times_form").valid()){
                $('.submit_times_form').on('click', function(){
                    $.ajax({
                        url: "assets/app/add_setting.php?setting=times&luid=<?php echo $_GET['luid']; ?>",
                        type: "POST",
                        data: $('#add_times_form').serialize(),
                        success: function(data) {
                            toastr.info('<strong>Logan says</strong>:<br/>'+data+' has been added to your list of times for this location.');
                            times.getDataTable().ajax.reload();
                        },
                        error: function() {
                            toastr.error('<strong>Logan says</strong>:<br/>That page didnt respond correctly. Try again, or create a support ticket for help.');
                        }
                    });
                });
            }
            $('#show_add_eventtype').on('click', function(){
                $('#add_eventtype').removeClass('hide');
            });
            if($("#add_eventtype_form").valid()){
                $('.submit_eventtype_form').on('click', function(){
                    $.ajax({
                        url: "assets/app/add_setting.php?setting=eventtype&luid=<?php echo $_GET['luid']; ?>",
                        type: "POST",
                        data: $('#add_eventtype_form').serialize(),
                        success: function(data) {
                            toastr.info('<strong>Logan says</strong>:<br/>'+data+' has been added to your list of eventtypes counties for this location.');
                            eventtype.getDataTable().ajax.reload();
                        },
                        error: function() {
                            toastr.error('<strong>Logan says</strong>:<br/>That page didnt respond correctly. Try again, or create a support ticket for help.');
                        }
                    });
                });
            }
            $('#show_add_subtype').on('click', function(){
                $('#add_subtype').removeClass('hide');
            });
            if($("#add_subtype_form").valid()){
                $('.submit_subtype_form').on('click', function(){
                    $.ajax({
                        url: "assets/app/add_setting.php?setting=subtype&luid=<?php echo $_GET['luid']; ?>",
                        type: "POST",
                        data: $('#add_subtype_form').serialize(),
                        success: function(data) {
                            toastr.info('<strong>Logan says</strong>:<br/>'+data+' has been added to your list of subtypes for this location.');
                            subtype.getDataTable().ajax.reload();
                        },
                        error: function() {
                            toastr.error('<strong>Logan says</strong>:<br/>That page didnt respond correctly. Try again, or create a support ticket for help.');
                        }
                    });
                });
            }
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
                            toastr.info('<strong>Logan says</strong>:<br/>'+data+' has been added to your list of services and rates for this location.');
                            grid.getDataTable().ajax.reload();
                        },
                        error: function() {
                            toastr.error('<strong>Logan says</strong>:<br/>That page didnt respond correctly. Try again, or create a support ticket for help.');
                        }
                    });
                }
            });
            $('#show_add_county').on('click', function(){
                $('#add_county').removeClass('hide');
            });
            if($("#add_county_form").valid()){
                $('.submit_county_form').on('click', function(){
                  $.ajax({
                        url: "assets/app/add_setting.php?setting=service_county&luid=<?php echo $_GET['luid']; ?>",
                        type: "POST",
                        data: $('#add_county_form').serialize(),
                        success: function(data) {
                            toastr.info('<strong>Logan says</strong>:<br/>'+data+' has been added to your list of serviceable counties for this location.');
                            counties.getDataTable().ajax.reload();
                        },
                        error: function() {
                            toastr.error('<strong>Logan says</strong>:<br/>That page didnt respond correctly. Try again, or create a support ticket for help.');
                        }
                    });
                });
            }
            $('#show_add_hear').on('click', function(){
                $('#add_hear').removeClass('hide');
            });
            if($("#add_hear_form").valid()){
                $('.submit_hear_form').on('click', function(){
                    $.ajax({
                        url: "assets/app/add_setting.php?setting=howhear&luid=<?php echo $_GET['luid']; ?>",
                        type: "POST",
                        data: $('#add_hear_form').serialize(),
                        success: function(data) {
                            toastr.info('<strong>Logan says</strong>:<br/>'+data+' has been added to your list of references for this location.');
                            howhear.getDataTable().ajax.reload();
                        },
                        error: function() {
                            toastr.error('<strong>Logan says</strong>:<br/>That page didnt respond correctly. Try again, or create a support ticket for help.');
                        }
                    });
                });
            }
            $('#show_add_storage').on('click', function(){
                $('#add_storage').removeClass('hide');
            });
            if($("#add_storage_form").valid()){
                $('.submit_storage_form').on('click', function(){
                    $.ajax({
                        url: "assets/app/add_setting.php?setting=service_storage&luid=<?php echo $_GET['luid']; ?>",
                        type: "POST",
                        data: $('#add_storage_form').serialize(),
                        success: function(data) {
                            toastr.info('<strong>Logan says</strong>:<br/>'+data+' has been added to your list of available storage units for this location.');
                            storage.getDataTable().ajax.reload();
                        },
                        error: function() {
                            toastr.error('<strong>Logan says</strong>:<br/>That page didnt respond correctly. Try again, or create a support ticket for help.');
                        }
                    });
                });
            }
            $('#show_add_zipcode').on('click', function(){
                $('#add_zipcode').removeClass('hide');
            });
            $('.submit_zipcodes_form').on('click', function(){
                if($("#add_zipcodes_form").valid()){
                    $.ajax({
                        url: "assets/app/add_setting.php?setting=service_zipcode&luid=<?php echo $_GET['luid']; ?>",
                        type: "POST",
                        data: $('#add_zipcodes_form').serialize(),
                        success: function(data) {
                            toastr.info('<strong>Logan says</strong>:<br/>'+data+' has been added to your list of serviceable zipccodes for this location.');
                            zipcodes.getDataTable().ajax.reload();
                        },
                        error: function() {
                            toastr.error('<strong>Logan says</strong>:<br/>That page didnt respond correctly. Try again, or create a support ticket for help.');
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
                        toastr.info('<strong>Logan says</strong>:<br/>Your changes have been saved to the database. Changes wll take effect in a few moments...');
                    },
                    error: function() {
                        toastr.error('<strong>Logan says</strong>:<br/>That page didnt respond correctly. Try again, or create a support ticket for help.');
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
