<?php
/**
 * Created by PhpStorm.
 * User: LoganCk
 * Date: 3/3/2017
 * Time: 3:52 AM
 */
session_start();
include '../app/init.php';

if(isset($_SESSION['logged'])){
    mysql_query("UPDATE fmo_users SET user_last_location='".mysql_real_escape_string(basename(__FILE__, '.php')).".php?".$_SERVER['QUERY_STRING']."' WHERE user_token='".mysql_real_escape_string($_SESSION['uuid'])."'");
    $location = mysql_fetch_array(mysql_query("SELECT location_manager, location_owner_company_token, location_storage_stripe_secret, location_storage_stripe_public, location_storage_tax, location_storage_creditcard_fee, location_nickname, location_storage_deposit, location_storage_late_fee, location_storage_auction_fee, location_storage_days_late, location_storage_days_auction, location_quote, location_pic, location_name, location_phone, location_email, location_token, location_status, location_booking_fee_disclaimer, location_address, location_address2, location_city, location_state, location_zip, location_county, location_minimum_hours, location_assumed_loadtime, location_assumed_unloadtime, location_sales_tax, location_service_tax, location_creditcard_fee, location_max_trucks, location_max_men, location_max_counties, location_storage_access, location_disclaimers, location_callcatcher_osb, location_quote_extra, location_quote_cancel, location_quote_overtime_time, location_quote_overtime_rate, location_quote_oversized_safe, location_quote_oversized_playset, location_quote_oversized_pooltable, location_quote_oversized_piano, location_quote_oversized_hottub, location_quote_packing_small, location_quote_packing_medium, location_quote_packing_large, location_quote_packing_dishpack,  location_quote_packing_wardrobe, location_quote_packing_paper, location_quote_packing_tape,  location_quote_packing_shrinkwrap FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'"));
    $quote = $location['location_quote'];
    ?>
    <div class="page-content">
        <h3 class="page-title">
            <strong><?php echo $location['location_name']; ?> </strong> | <small>Settings</small>
        </h3>
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <i class="fa fa-home"></i>
                    <a class="load_page" data-href="assets/pages/dashboard.php?luid=<?php echo $_GET['luid']; ?>"><?php echo $location['location_name']; ?></a>
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
                                <a href="#disclaimers" data-toggle="tab">Disclaimers</a>
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
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group text-center" >
                                                            <form id="ll_upload" action="" method="POST" role="form">
                                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                                    <div class="fileinput-new thumbnail">
                                                                        <?php
                                                                        if(!empty($location['location_pic'])){
                                                                            ?>
                                                                            <img id="pp" src="<?php echo $location['location_pic']; ?>" alt="1" style="width: 100%; height: 200px; display: block;"/>
                                                                            <?php
                                                                        } else {
                                                                            ?>
                                                                            <img id="pp" src="assets/admin/layout/img/default-location.jpg" alt="2" style="width: 100%; height: 200px; display: block;"/>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </div>
                                                                    <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"></div>
                                                                    <div>
                                                                        <span class="btn default blue-stripe btn-file" style="margin-top:-45px;">
                                                                            <span class="fileinput-new">Upload new photo </span>
                                                                            <span class="fileinput-exists">Change </span>
                                                                            <input type="file" name="image">
                                                                        </span>
                                                                        <button class="btn red updt_pp fileinput-exists" data-luid="<?php echo $location['location_token']; ?>">Submit </button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="row static-info">
                                                            <div class="col-md-5 name">
                                                                Manager:
                                                            </div>
                                                            <div class="col-md-7 value">
                                                                <?php
                                                                $managers = mysql_query("SELECT user_token, user_lname, user_fname FROM fmo_users WHERE  (user_group=".mysql_real_escape_string(1)." AND user_company_token='".mysql_real_escape_string($_SESSION['cuid'])."') OR (user_group=".mysql_real_escape_string(2.0)." AND user_employer='".mysql_real_escape_string($_SESSION['cuid'])."')");
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
                                                                Location Nickname:
                                                            </div>
                                                            <div class="col-md-7 value">
                                                                <a class="loc" style="color:#333333" data-name="location_nickname" data-pk="<?php echo $location['location_token']; ?>" data-type="text" data-placement="right" data-title="Enter new location name.." data-url="assets/app/update_settings.php?update=location">
                                                                    <?php echo $location['location_nickname']; ?>
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
                                                                <a class="loc" style="color:#333333" data-name="location_state" data-pk="<?php echo $location['location_token']; ?>" data-type="select" data-source="[{value: 'AL', text: 'Alabama'},{value: 'AK', text: 'Alaska'},{value: 'AZ', text: 'Arizona'},{value: 'AR', text: 'Arkansas'},{value: 'CA', text: 'California'},{value: 'CO', text: 'Colorado'},{value: 'CT', text: 'Connecticut'},{value: 'DE', text: 'Delaware'},{value: 'DC', text: 'District Of Columbia'},{value: 'FL', text: 'Florida'},{value: 'GA', text: 'Georgia'},{value: 'HI', text: 'Hawaii'},{value: 'ID', text: 'Idaho'},{value: 'IL', text: 'Illinois'},{value: 'IN', text: 'Indiana'},{value: 'IA', text: 'Iowa'},{value: 'KS', text: 'Kansas'},{value: 'KY', text: 'Kentucky'},{value: 'LA', text: 'Louisiana'},{value: 'ME', text: 'Maine'},{value: 'MD', text: 'Maryland'},{value: 'MA', text: 'Massachusetts'},{value: 'MI', text: 'Michigan'},{value: 'MN', text: 'Minnesota'},{value: 'MS', text: 'Mississippi'},{value: 'MO', text: 'Missouri'},{value: 'MT', text: 'Montana'},{value: 'NE', text: 'Nebraska'},{value: 'NV', text: 'Nevada'},{value: 'NH', text: 'New Hampshire'},{value: 'NJ', text: 'New Jersey'},{value: 'NM', text: 'New Mexico'},{value: 'NY', text: 'New York'},{value: 'NC', text: 'North Carolina'},{value: 'ND', text: 'North Dakota'},{value: 'OH', text: 'Ohio'},{value: 'OK', text: 'Oklahoma'},{value: 'OR', text: 'Oregon'},{value: 'PW', text: 'Palau'},{value: 'PA', text: 'Pennsylvania'},{value: 'RI', text: 'Rhode Island'},{value: 'SC', text: 'South Carolina'},{value: 'SD', text: 'South Dakota'},{value: 'TN', text: 'Tennessee'},{value: 'TX', text: 'Texas'},{value: 'UT', text: 'Utah'},{value: 'VT', text: 'Vermont'},{value: 'VA', text: 'Virginia'},{value: 'WA', text: 'Washington'},{value: 'WV', text: 'West Virginia'},{value: 'WI', text: 'Wisconsin'},{value: 'WY', text: 'Wyoming'}]" data-inputclass="form-control" data-placement="right" data-title="Select new state.." data-url="assets/app/update_settings.php?update=location">
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
                                </div>
                            </div>


                            <div class="tab-pane" id="tab_1_2">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="portlet">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-archive"></i> Static Information
                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                                <div class="row static-info">
                                                    <div class="col-md-5 name">
                                                        Min. Hours
                                                    </div>
                                                    <div class="col-md-7 value text-right">
                                                        <a class="loc" style="color:#333333" data-name="location_minimum_hours" data-pk="<?php echo $location['location_token']; ?>" data-inputclass="form-control" data-type="number" data-placement="right" data-title="Enter new min. hours.." data-url="assets/app/update_settings.php?update=location">
                                                            <?php echo $location['location_minimum_hours']; ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="row static-info">
                                                    <div class="col-md-5 name">
                                                        Assumed Load Time (Hrs)
                                                    </div>
                                                    <div class="col-md-7 value text-right">
                                                        <a class="loc" style="color:#333333" data-name="location_assumed_loadtime" data-pk="<?php echo $location['location_token']; ?>" data-inputclass="form-control" data-type="number" data-placement="right" data-title="Enter new assumed load time.." data-url="assets/app/update_settings.php?update=location">
                                                            <?php echo $location['location_assumed_loadtime']; ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="row static-info">
                                                    <div class="col-md-5 name">
                                                        Assumed Unload Time (Hrs)
                                                    </div>
                                                    <div class="col-md-7 value text-right">
                                                        <a class="loc" style="color:#333333" data-name="location_assumed_unloadtime" data-pk="<?php echo $location['location_token']; ?>" data-inputclass="form-control" data-type="number" data-placement="right" data-title="Enter new assumed unload time.." data-url="assets/app/update_settings.php?update=location">
                                                            <?php echo $location['location_assumed_unloadtime']; ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <hr/>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <a href="javascript:;" class="btn red edit" data-edit="loc" data-reload="">
                                                            <i class="fa fa-pencil"></i> <span class="hidden-sm hidden-md">Edit</span> </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-4">
                                        <div class="portlet">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-tasks"></i>Static Rates
                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                                <div class="row static-info">
                                                    <div class="col-md-5 name">
                                                        Sales Tax
                                                    </div>
                                                    <div class="col-md-7 value text-right">
                                                        <a class="loc" style="color:#333333" data-name="location_sales_tax" data-pk="<?php echo $location['location_token']; ?>" data-inputclass="form-control" data-type="number" data-step="any" data-placement="right" data-title="Enter new sales tax percentage.." data-url="assets/app/update_settings.php?update=location&p=true">
                                                            <?php echo $location['location_sales_tax'] * 100; ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="row static-info">
                                                    <div class="col-md-5 name">
                                                        Service Tax
                                                    </div>
                                                    <div class="col-md-7 value text-right">
                                                        <a class="loc" style="color:#333333" data-name="location_service_tax" data-pk="<?php echo $location['location_token']; ?>" data-inputclass="form-control" data-type="number" data-step="any" data-placement="right" data-title="Enter new service tax percentage.." data-url="assets/app/update_settings.php?update=location&p=true">
                                                            <?php echo $location['location_service_tax'] * 100; ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="row static-info">
                                                    <div class="col-md-5 name">
                                                        Credit Card Fees
                                                    </div>
                                                    <div class="col-md-7 value text-right">
                                                        <a class="loc" style="color:#333333" data-name="location_creditcard_fee" data-pk="<?php echo $location['location_token']; ?>" data-inputclass="form-control" data-type="number" data-step="any" data-placement="right" data-title="Enter new credit card percentage.." data-url="assets/app/update_settings.php?update=location&p=true">
                                                            <?php echo $location['location_creditcard_fee'] * 100; ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <hr/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="portlet">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-tasks"></i> Static Rates (Cont.)
                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                                <div class="row static-info">
                                                    <div class="col-md-5 name">
                                                        Max. Trucks per Event
                                                    </div>
                                                    <div class="col-md-7 value text-right">
                                                        <a class="loc" style="color:#333333" data-name="location_max_trucks" data-pk="<?php echo $location['location_token']; ?>" data-inputclass="form-control" data-type="number" data-placement="left" data-title="Enter new max. trucks.." data-url="assets/app/update_settings.php?update=location">
                                                            <?php echo $location['location_max_trucks']; ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="row static-info">
                                                    <div class="col-md-5 name">
                                                        Max. Men per Event
                                                    </div>
                                                    <div class="col-md-7 value text-right">
                                                        <a class="loc" style="color:#333333" data-name="location_max_men" data-pk="<?php echo $location['location_token']; ?>" data-inputclass="form-control" data-type="number" data-placement="left" data-title="Enter new max. men.." data-url="assets/app/update_settings.php?update=location">
                                                            <?php echo $location['location_max_men']; ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="row static-info">
                                                    <div class="col-md-5 name">
                                                       Max. Counties per Event
                                                    </div>
                                                    <div class="col-md-7 value text-right">
                                                        <a class="loc" style="color:#333333" data-name="location_max_counties" data-pk="<?php echo $location['location_token']; ?>" data-inputclass="form-control" data-type="number" data-placement="left" data-title="Enter new max. counties.." data-url="assets/app/update_settings.php?update=location">
                                                            <?php echo $location['location_max_counties']; ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <hr/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-cogs"></i>Custom Services & Rates <small><span class="font-red">|</span> Add your own services/rates to the system. This helps <strong>FORMOVERSONLY&trade;</strong> adapt to your company.</small>
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
                                                                    <th>
                                                                        <input type="checkbox" class="group-checkable"
                                                                        Service Name
                                                                    </th>
                                                                    <th>
                                                                        Description
                                                                    </th>
                                                                    <th>
                                                                        Saleprice
                                                                    </th>
                                                                    <th>
                                                                        Cost
                                                                    </th>
                                                                    <th>
                                                                        Percent?
                                                                    </th>
                                                                    <th>
                                                                        Taxable?
                                                                    </th>
                                                                    <th>
                                                                        Commissionable?
                                                                    </th>
                                                                    <th>
                                                                        Redeemable?
                                                                        <span class="pull-right">
                                                                            Prepaid
                                                                        </span>
                                                                    </th>
                                                                    <th>
                                                                        Type
                                                                    </th>
                                                                    <th>
                                                                        In-line edit
                                                                    </th>
                                                                </tr>
                                                                <tr role="row" class="filter hide" id="add_service_item">
                                                                    <td><input type="text" class="form-control form-filter input-sm" name="item"></td>
                                                                    <td><input type="text" class="form-control form-filter input-sm" name="desc"></td>
                                                                    <td><input type="number" class="form-control form-filter input-sm" name="saleprice"></td>
                                                                    <td><input type="number" class="form-control form-filter input-sm" name="cost"></td>
                                                                    <td>
                                                                        <select name="percentage" class="form-control form-filter input-sm">
                                                                            <option value="">Select one..</option>
                                                                            <option value="1">Yes</option>
                                                                            <option value="0">No</option>
                                                                        </select>
                                                                    </td>
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
                                                                        <select name="redeemable" class="form-control form-filter input-sm">
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
                                                                            <option value="Storage">Storage</option>
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
                                                    <i class="fa fa-cogs"></i>Zip Codes <small><span class="font-red">|</span> These are the zip codes of areas you service. (First three digits only)</small>
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
                                    <div class="col-md-8">
                                        <div class="portlet">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-cubes"></i>Storage Unit <strong>Types</strong> & <strong>Options</strong>
                                                </div>
                                                <div class="actions">
                                                    <a class="btn default red-stripe">
                                                        <i class="fa fa-cube"></i>
                                                        <span class="hidden-480 disabled" id="show_add_storagetypes">Add Unit Types</span>
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
                                                                    <option value="delete" class="font-red">Delete</option>
                                                                </select>
                                                                <button class="btn btn-sm red table-group-action-submit"><i class="fa fa-check"></i> Submit</button>
                                                            </div>
                                                            <form role="form" id="add_storagetypes_form">
                                                                <table class="table table-striped table-hover" id="storagetypes">
                                                                    <thead>
                                                                    <tr role="row" class="heading">
                                                                        <th>
                                                                            <input type="checkbox" class="group-checkable"> Unit Specifications
                                                                        </th>
                                                                        <th>
                                                                            Floor / Room Description
                                                                        </th>
                                                                        <th>
                                                                            Standard Rent
                                                                        </th>
                                                                        <th>
                                                                            Climate/Electric
                                                                        </th>
                                                                        <th>

                                                                        </th>
                                                                    </tr>
                                                                    <tr role="row" class="filter hide" id="add_storagetypes">
                                                                        <td>
                                                                            <div class="form-group row">
                                                                                <div class="col-md-4">
                                                                                    <input type="number" step="any" class="form-control form-filter input-sm" name="l" style="width: 80px;" placeholder="Length">
                                                                                </div>
                                                                                <div class="col-md-4">
                                                                                    <input type="number" step="any" class="form-control form-filter input-sm" name="w" style="width: 80px;" placeholder="Width">
                                                                                </div>
                                                                                <div class="col-md-4">
                                                                                    <input type="number" step="any" class="form-control form-filter input-sm" name="h" style="width: 80px;" placeholder="Height">
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="form-group row">
                                                                                <div class="col-md-6">
                                                                                    <select name="floor" class="form-control form-filter input-sm">
                                                                                        <option value="1" selected>1</option>
                                                                                        <option value="2">2</option>
                                                                                        <option value="3">3</option>
                                                                                        <option value="4">4</option>
                                                                                        <option value="5">5</option>
                                                                                        <option value="6">6</option>
                                                                                        <option value="7">7</option>
                                                                                    </select>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <select name="desc" class="form-control form-filter input-sm">
                                                                                        <option value="">Select one..</option>
                                                                                        <option value="Flex Unit">Flex Unit</option>
                                                                                        <option value="Inside Unit">Inside Unit</option>
                                                                                        <option value="Outside Unit">Outside Unit</option>
                                                                                        <option value="Vault Unit">Vault Unit</option>
                                                                                        <option value="Office Unit">Office Unit</option>
                                                                                        <option value="Parking Unit">Parking Unit</option>
                                                                                        <option value="Pod Unit">Pod Unit</option>
                                                                                        <option value="Wineslot Unit">Wineslot Unit</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <input type="number" placeholder="999.99" class="form-control form-filter input-sm" name="rent" id="rent">
                                                                        </td>
                                                                        <td>
                                                                            <select name="climate" class="form-control form-filter input-sm">
                                                                                <option value="">Select one..</option>
                                                                                <option value="N/A">N/A</option>
                                                                                <option value="Heat">Heat</option>
                                                                                <option value="Air">Air</option>
                                                                                <option value="Heat/Air">Heat/Air</option>
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <div class="margin-bottom-5">
                                                                                <button type="submit" class="btn btn-sm red margin-bottom submit_storage_form"><i class="fa fa-download"></i> Save</button> <button class="btn btn-sm default filter-cancel"><i class="fa fa-times"></i> Reset</button>
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
                                    <div class="col-md-4">
                                        <div class="portlet">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-tasks"></i>Storage Rates Configuration
                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                                <div class="row static-info">
                                                    <div class="col-md-8 name">
                                                        Storage Tax Rate (%)
                                                    </div>
                                                    <div class="col-md-4 value text-right">
                                                        %<a class="loc_su" style="color:#333333" data-name="location_storage_tax" data-pk="<?php echo $location['location_token']; ?>" data-inputclass="form-control" data-type="number" data-step="any" data-placement="right" data-title="Enter new sales tax percentage.." data-url="assets/app/update_settings.php?update=location&p=true">
                                                            <?php echo number_format($location['location_storage_tax'] * 100, 2); ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="row static-info">
                                                    <div class="col-md-8 name">
                                                        Storage Credit Card Fee (%)
                                                    </div>
                                                    <div class="col-md-4 value text-right">
                                                        %<a class="loc_su" style="color:#333333" data-name="location_storage_tax" data-pk="<?php echo $location['location_token']; ?>" data-inputclass="form-control" data-type="number" data-step="any" data-placement="right" data-title="Enter new credit card percentage.." data-url="assets/app/update_settings.php?update=location&p=true">
                                                            <?php echo number_format($location['location_storage_creditcard_fee'] * 100, 2); ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="row static-info">
                                                    <div class="col-md-8 name">
                                                        Deposit Amount ($)
                                                    </div>
                                                    <div class="col-md-4 value text-right">
                                                        $<a class="loc_su" style="color:#333333" data-name="location_storage_deposit" data-pk="<?php echo $location['location_token']; ?>" data-inputclass="form-control" data-type="number" data-step="any" data-placement="right" data-title="Enter new deposit amount.." data-url="assets/app/update_settings.php?update=location">
                                                            <?php echo number_format($location['location_storage_deposit'], 2); ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="row static-info">
                                                    <div class="col-md-8 name">
                                                        Late Fee Amount ($)
                                                    </div>
                                                    <div class="col-md-4 value text-right">
                                                        $<a class="loc_su" style="color:#333333" data-name="location_storage_late_fee" data-pk="<?php echo $location['location_token']; ?>" data-inputclass="form-control" data-type="number" data-step="any" data-placement="right" data-title="Enter new late fee amount.." data-url="assets/app/update_settings.php?update=location">
                                                            <?php echo number_format($location['location_storage_late_fee'], 2); ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="row static-info">
                                                    <div class="col-md-8 name">
                                                        Auction Setup Fee Amount ($)
                                                    </div>
                                                    <div class="col-md-4 value text-right">
                                                        $<a class="loc_su" style="color:#333333" data-name="location_storage_auction_fee" data-pk="<?php echo $location['location_token']; ?>" data-inputclass="form-control" data-type="number" data-step="any" data-placement="right" data-title="Enter new auction setup fee.." data-url="assets/app/update_settings.php?update=location">
                                                            <?php echo number_format($location['location_storage_auction_fee'], 2); ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="row static-info">
                                                    <div class="col-md-8 name">
                                                        # of days before late (#)
                                                    </div>
                                                    <div class="col-md-4 value text-right">
                                                        <a class="loc_su" style="color:#333333" data-name="location_storage_days_late" data-pk="<?php echo $location['location_token']; ?>" data-inputclass="form-control" data-type="number" data-step="any" data-placement="right" data-title="Enter new days.." data-url="assets/app/update_settings.php?update=location">
                                                            <?php echo $location['location_storage_days_late']; ?>
                                                        </a> days
                                                    </div>
                                                </div>
                                                <div class="row static-info">
                                                    <div class="col-md-8 name">
                                                        # of days before put in auction (#)
                                                    </div>
                                                    <div class="col-md-4 value text-right">
                                                        <a class="loc_su" style="color:#333333" data-name="location_storage_days_auction" data-pk="<?php echo $location['location_token']; ?>" data-inputclass="form-control" data-type="number" data-step="any" data-placement="right" data-title="Enter new days.." data-url="assets/app/update_settings.php?update=location">
                                                            <?php echo $location['location_storage_days_auction']; ?>
                                                        </a> days
                                                    </div>
                                                </div>
                                                <hr/>
                                                <h6><a target="_blank" href="https://dashboard.stripe.com/register">Stripe</a> tokens (for credit card payments).</h6>
                                                <div class="row static-info">
                                                    <div class="col-md-4 name">
                                                        Publishable
                                                    </div>
                                                    <div class="col-md-8 value text-right">
                                                        <a class="loc_su" style="color:#333333" data-name="location_storage_stripe_public" data-pk="<?php echo $location['location_token']; ?>" data-inputclass="form-control" data-type="text" data-step="any" data-placement="right" data-title="Enter new publishable key.." data-url="assets/app/update_settings.php?update=location">
                                                            <?php echo $location['location_storage_stripe_public']; ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="row static-info">
                                                    <div class="col-md-4 name">
                                                        Secret
                                                    </div>
                                                    <div class="col-md-8 value text-right">
                                                        <a class="loc_su" style="color:#333333" data-name="location_storage_stripe_secret" data-pk="<?php echo $location['location_token']; ?>" data-inputclass="form-control" data-type="text" data-step="any" data-placement="right" data-title="Enter new secret key.." data-url="assets/app/update_settings.php?update=location">
                                                            <?php echo $location['location_storage_stripe_secret']; ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <hr/>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <a href="javascript:;" class="btn red edit" data-edit="loc_su" data-reload="">
                                                            <i class="fa fa-pencil"></i> <span class="hidden-sm hidden-md">Edit</span> </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-cubes"></i>Storage Units  <small><span class="font-red">|</span> If your location has storage, you can add their specifcations here.</small>
                                                </div>
                                                <div class="actions">
                                                    <a class="btn default red-stripe">
                                                        <i class="fa fa-cube"></i>
                                                        <span class="hidden-480 disabled" id="show_add_storage">Add Units</span>
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
                                                                <table class="table table-striped table-hover" id="storage">
                                                                    <thead>
                                                                    <tr role="row" class="heading">
                                                                        <th>
                                                                            <input type="checkbox" class="group-checkable"> Unit Status & Number/Name
                                                                        </th>
                                                                        <th>
                                                                            Unit Size/Sqft
                                                                        </th>
                                                                        <th>

                                                                        </th>
                                                                        <th>

                                                                        </th>
                                                                        <th>

                                                                        </th>
                                                                    </tr>
                                                                    <tr role="row" class="filter hide" id="add_storage">
                                                                        <td>
                                                                            <select name="type" class="form-control form-filter input-sm">
                                                                                <option value="">Select one..</option>
                                                                                <?php
                                                                                $findStorage = mysql_query("SELECT type_id, type_floor, type_desc, type_lwh, type_rent, type_climate FROM fmo_locations_storages_types WHERE type_location_token='".mysql_real_escape_string($_GET['luid'])."' ORDER BY type_id DESC") or die(mysql_error());
                                                                                if(mysql_num_rows($findStorage) > 0){
                                                                                    while($types = mysql_fetch_assoc($findStorage)){
                                                                                        ?>
                                                                                        <option value="<?php echo $types['type_id']; ?>"><?php echo $types['type_lwh']; ?> - Floor <?php echo $types['type_floor']; ?>, <?php echo $types['type_desc']; ?> @ <?php echo $types['type_rent']; ?>/Monthly [Climate: <?php echo $types['type_climate']; ?>]</option>
                                                                                        <?php
                                                                                    }
                                                                                }
                                                                                ?>
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <select name="status" class="form-control form-filter input-sm">
                                                                                <option value="Damaged">Damaged</option>
                                                                                <option value="Vacant" selected>Vacant</option>
                                                                                <option value="Occupied">Occupied</option>
                                                                                <option value="Delinquent">Delinquent</option>
                                                                                <option value="Auction">Auction</option>
                                                                                <option value="Reserved">Reserved</option>
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" placeholder="Some example notes.." class="form-control form-filter input-sm" name="desc" id="name">
                                                                        </td>
                                                                        <td>
                                                                            <input type="number" placeholder="# of duplicates" class="form-control form-filter input-sm" name="qa" id="qa">
                                                                        </td>
                                                                        <td>
                                                                            <div class="margin-bottom-5">
                                                                                <button type="submit" class="btn btn-sm red margin-bottom submit_storage_form"><i class="fa fa-download"></i> Save</button> <button class="btn btn-sm default filter-cancel"><i class="fa fa-times"></i> Reset</button>
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
                            <div class="tab-pane" id="disclaimers">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-cubes"></i> Location Disclaimers
                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                                <div class="col-md-12">
                                                    <span class="dc" style="color: #333333; width: 100%;" data-mode="inline" data-placement="bottom" data-inputclass="form-control col-md-12" data-name="location_disclaimers" data-pk="<?php echo $location['location_token']; ?>" data-type="wysihtml5" data-title="Enter new location disclaimers.." data-url="assets/app/update_settings.php?update=location">
                                                        <?php
                                                        if(empty($location['location_disclaimers'])){
                                                            ?>
                                                            <h3>You can edit this disclaimer!</h3>
                                                            <p>Its easy, just hit edit & start editing away! You can even add cool colors like this: <span class="text-danger">WOW</span> <span class="text-warning">BLAM</span> <span class="text-info">SLAM</span></p>
                                                            <?php
                                                        } else {
                                                            echo $location['location_disclaimers'];
                                                        }
                                                        ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br/><br/>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-credit-card"></i> Booking Fee Disclaimers
                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                                <div class="col-md-12">
                                                    <a class="dc" style="color: #333333;" data-mode="inline" data-placement="bottom" data-inputclass="form-control" data-name="location_booking_fee_disclaimer" data-pk="<?php echo $location['location_token']; ?>" data-type="wysihtml5" data-title="Enter new location booking fee disclaimer.." data-url="assets/app/update_settings.php?update=location">
                                                        <?php
                                                        if(empty($location['location_booking_fee_disclaimer'])){
                                                            ?>
                                                            <h3>You can edit this disclaimer!</h3>
                                                            <p>Its easy, just hit edit & start editing away! You can even add cool colors like this: <span class="text-danger">WOW</span> <span class="text-warning">BLAM</span> <span class="text-info">SLAM</span></p>
                                                            <?php
                                                        } else {
                                                            echo $location['location_booking_fee_disclaimer'];
                                                        }
                                                        ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr/>
                                <div class="row">
                                    <div class="col-md-12">
                                        <a class="btn text-center red btn-sm edit" data-edit="dc" data-reload=""> <i class="fa fa-pencil"></i> <span class="hidden-sm hidden-md " >Edit</span></a>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="tab_1_4">
                                <h3><strong>Call Catcher/Quote</strong> <small>variables for the <em>call catcher</em>, <em>customer quote</em>, and <em>reports</em>.</small>
                                    <a href="javascript:;" class="btn btn-sm red edit pull-right" data-edit="loc" data-reload="">
                                        <i class="fa fa-pencil"></i> <span class="hidden-sm hidden-md">Edit</span> </a></h3>
                                <hr/>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet">
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
                                                                        <label>Adjustment</label>
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
                                                                        <label>Adjustment</label>
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
                                                                        <label>Adjustment</label>
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
                                                                        <label>Adjustment</label>
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
                                                                        <label>Adjustment</label>
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
                                                                        <label>Adjustment</label>
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
                                                                        <label>Adjustment</label>
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
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4><strong>Custom Quote</strong> <small>variables for quote message to customer</small></h4>
                                        <hr/>
                                        <div class="row form-group" style="margin-top: 20px;">
                                            <label class="col-md-4 control-label">Other Possible Fees</label>
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <div class="icheck-inline">
                                                        <label>
                                                            <input type="checkbox" class="icheck" <?php if(strpos($quote, "view_quote_other") !== false){echo "checked";} ?> data-perm="view_quote_other"> Enabled </label>
                                                        <label id="edit_view_quote_other">
                                                            <i class="icon-home"></i>
                                                            <a class="perms" data-toggle="modal" href="#quote" data-type="quote_other" data-title="Other Possible Fees">
                                                                Edit pricing options
                                                            </a>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row form-group" style="margin-top: 20px;">
                                            <label class="col-md-4 control-label">Oversized Items</label>
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <div class="icheck-inline">
                                                        <label>
                                                            <input type="checkbox" class="icheck" <?php if(strpos($quote, "view_quote_oversized") !== false){echo "checked";} ?> data-perm="view_quote_oversized"> Enabled </label>
                                                        <label id="edit_view_quote_oversized">
                                                            <i class="icon-home"></i>
                                                            <a class="perms" data-toggle="modal" href="#quote" data-type="quote_oversized" data-title="Oversized Items">
                                                                Edit pricing options
                                                            </a>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row form-group" style="margin-top: 20px;">
                                            <label class="col-md-4 control-label">Packing Materials</label>
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <div class="icheck-inline">
                                                        <label>
                                                            <input type="checkbox" class="icheck" <?php if(strpos($quote, "view_quote_packing") !== false){echo "checked";} ?> data-perm="view_quote_packing"> Enabled </label>
                                                        <label id="edit_view_quote_packing">
                                                            <i class="icon-home"></i>
                                                            <a class="perms" data-toggle="modal" href="#quote" data-type="quote_packing" data-title="Packing Materials">
                                                                Edit pricing options
                                                            </a>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h4><strong>Custom Quote</strong> <small>variables for out of state bidding</small></h4>
                                        <hr/>
                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                                Out of State Bidding:
                                            </div>
                                            <div class="col-md-5 value">
                                                Take
                                                <a class="loc" style="color:#333333" data-name="location_callcatcher_osb" data-pk="<?php echo $location['location_token']; ?>" data-inputclass="form-control" data-type="number" data-placement="right" data-title="Enter new amount" data-url="assets/app/update_settings.php?update=location">
                                                    <?php echo number_format($location['location_callcatcher_osb'], 1); ?>
                                                </a> and multiply it by the loaded mileage.
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
    <div class="modal fade bs-modal-lg" id="quote" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content box red">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h3 class="modal-title font-bold"><strong id="perms_title"></strong> pricing options</h3>
                </div>
                <div class="modal-body">
                    <div class="edit_perms" id="edit_perms_quote_other">
                        <h4><strong class="text-success">Available</strong> options</h4>
                        <hr/>
                        <div class="row form-group" style="margin-top: 20px;">
                            <label class="col-md-5 control-label">Extra Man - <strong>$<a class="edit_qut" data-inputclass="form-control" data-name="location_quote_extra" data-pk="<?php echo $location['location_token']; ?>" data-type="number" data-placement="right" data-title="Enter new extra man amount in $.." data-url="assets/app/update_settings.php?update=location"><?php echo number_format($location['location_quote_extra'], 2); ?></a> (each)</strong></label>
                            <div class="col-md-7">
                                <div class="input-group">
                                    <div class="icheck-inline">
                                        <label>
                                            <input type="checkbox" class="icheck" <?php if(strpos($quote, "view_quote_other_extra") !== false){echo "checked";} ?> data-perm="view_quote_other_extra"> Shown in message
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-md-5 control-label">Cancel Charge - <strong>$<a class="edit_qut" data-inputclass="form-control" data-name="location_quote_cancel" data-pk="<?php echo $location['location_token']; ?>" data-type="number" data-placement="right" data-title="Enter new cancel charge amount in $.." data-url="assets/app/update_settings.php?update=location"><?php echo number_format($location['location_quote_cancel'], 2); ?></a></strong></label>
                            <div class="col-md-7">
                                <div class="input-group">
                                    <div class="icheck-inline">
                                        <label>
                                            <input type="checkbox" class="icheck" <?php if(strpos($quote, "view_quote_other_cancel") !== false){echo "checked";} ?> data-perm="view_quote_other_cancel"> Shown in message
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-md-5 control-label">Overtime Rate - <strong>After <a class="edit_qut" data-inputclass="form-control" data-name="location_quote_overtime_time" data-pk="<?php echo $location['location_token']; ?>" data-type="number" data-placement="right" data-title="Enter new overtime start.." data-url="assets/app/update_settings.php?update=location"><?php echo $location['location_quote_overtime_time']; ?></a>pm => <a class="edit_qut" data-name="location_quote_overtime_rate" data-pk="<?php echo $location['location_token']; ?>" data-type="text" data-placement="right" data-title="Enter new overtime rate.." data-url="assets/app/update_settings.php?update=location"><?php echo number_format($location['location_quote_overtime_rate'], 2); ?></a>x current rate</strong></label>
                            <div class="col-md-7">
                                <div class="input-group">
                                    <div class="icheck-inline">
                                        <label>
                                            <input type="checkbox" class="icheck" <?php if(strpos($quote, "view_quote_other_overtime") !== false){echo "checked";} ?> data-perm="view_quote_other_overtime"> Shown in message
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="edit_perms" id="edit_perms_quote_oversized">
                        <h4><strong class="text-success">Available</strong> options</h4>
                        <hr/>
                        <div class="row form-group" style="margin-top: 20px;">
                            <label class="col-md-5 control-label">Safe - <strong>$<a class="edit_qut" data-inputclass="form-control" data-name="location_quote_oversized_safe" data-pk="<?php echo $location['location_token']; ?>" data-type="number" data-placement="right" data-title="Enter new safe amount in $.." data-url="assets/app/update_settings.php?update=location"><?php echo number_format($location['location_quote_oversized_safe'], 2); ?></a></strong></label>
                            <div class="col-md-7">
                                <div class="input-group">
                                    <div class="icheck-inline">
                                        <label>
                                            <input type="checkbox" class="icheck" <?php if(strpos($quote, "view_quote_oversized_safe") !== false){echo "checked";} ?> data-perm="view_quote_oversized_safe"> Shown in message & enabled in call-catcher/event dashboard
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-md-5 control-label">Play Set - <strong>$<a class="edit_qut" data-inputclass="form-control" data-name="location_quote_oversized_playset" data-pk="<?php echo $location['location_token']; ?>" data-type="number" data-placement="right" data-title="Enter new play set amount in $.." data-url="assets/app/update_settings.php?update=location"><?php echo number_format($location['location_quote_oversized_playset'], 2); ?></a></strong> w/ move $0.00 w/o</label>
                            <div class="col-md-7">
                                <div class="input-group">
                                    <div class="icheck-inline">
                                        <label>
                                            <input type="checkbox" class="icheck" <?php if(strpos($quote, "view_quote_oversized_playset") !== false){echo "checked";} ?> data-perm="view_quote_oversized_playset"> Shown in message & enabled in call-catcher/event dashboard
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-md-5 control-label">Pool Table - <strong>$<a class="edit_qut" data-inputclass="form-control" data-name="location_quote_oversized_pooltable" data-pk="<?php echo $location['location_token']; ?>" data-type="number" data-placement="right" data-title="Enter new pool table amount in $.." data-url="assets/app/update_settings.php?update=location"><?php echo number_format($location['location_quote_oversized_pooltable'], 2); ?></a></strong> w/ move $0.00 w/o</label>
                            <div class="col-md-7">
                                <div class="input-group">
                                    <div class="icheck-inline">
                                        <label>
                                            <input type="checkbox" class="icheck" <?php if(strpos($quote, "view_quote_oversized_pooltable") !== false){echo "checked";} ?> data-perm="view_quote_oversized_pooltable"> Shown in message & enabled in call-catcher/event dashboard
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-md-5 control-label">Piano - <strong>$<a class="edit_qut" data-inputclass="form-control" data-name="location_quote_oversized_piano" data-pk="<?php echo $location['location_token']; ?>" data-type="number" data-placement="right" data-title="Enter new piano amount in $.." data-url="assets/app/update_settings.php?update=location"><?php echo number_format($location['location_quote_oversized_piano'], 2); ?></a></strong> w/ move $0.00 w/o</label>
                            <div class="col-md-7">
                                <div class="input-group">
                                    <div class="icheck-inline">
                                        <label>
                                            <input type="checkbox" class="icheck" <?php if(strpos($quote, "view_quote_oversized_piano") !== false){echo "checked";} ?> data-perm="view_quote_oversized_piano"> Shown in message & enabled in call-catcher/event dashboard
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-md-5 control-label">Hot Tub - <strong>$<a class="edit_qut" data-inputclass="form-control" data-name="location_quote_oversized_hottub" data-pk="<?php echo $location['location_token']; ?>" data-type="number" data-placement="right" data-title="Enter new extra man amount in $.." data-url="assets/app/update_settings.php?update=location"><?php echo number_format($location['location_quote_oversized_hottub'], 2); ?></a></strong> w/ move $0.00 w/o</label>
                            <div class="col-md-7">
                                <div class="input-group">
                                    <div class="icheck-inline">
                                        <label>
                                            <input type="checkbox" class="icheck" <?php if(strpos($quote, "view_quote_oversized_hottub") !== false){echo "checked";} ?> data-perm="view_quote_oversized_hottub"> Shown in message & enabled in call-catcher/event dashboard
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="edit_perms" id="edit_perms_quote_packing">
                        <h4><strong class="text-success">Available</strong> options</h4>
                        <hr/>
                        <div class="row form-group" style="margin-top: 20px;">
                            <label class="col-md-5 control-label">Small Box - <strong>$<a class="edit_qut" data-inputclass="form-control" data-name="location_quote_packing_small" data-pk="<?php echo $location['location_token']; ?>" data-type="number" data-placement="right" data-title="Enter new small amount in $.." data-url="assets/app/update_settings.php?update=location"><?php echo number_format($location['location_quote_packing_small'], 2); ?></a></strong></label>
                            <div class="col-md-7">
                                <div class="input-group">
                                    <div class="icheck-inline">
                                        <label>
                                            <input type="checkbox" class="icheck" <?php if(strpos($quote, "view_quote_packing_small") !== false){echo "checked";} ?> data-perm="view_quote_packing_small"> Shown in message
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-md-5 control-label">Medium Box - <strong>$<a class="edit_qut" data-inputclass="form-control" data-name="location_quote_packing_medium" data-pk="<?php echo $location['location_token']; ?>" data-type="number" data-placement="right" data-title="Enter new medium amount in $.." data-url="assets/app/update_settings.php?update=location"><?php echo number_format($location['location_quote_packing_medium'], 2); ?></a></strong></label>
                            <div class="col-md-7">
                                <div class="input-group">
                                    <div class="icheck-inline">
                                        <label>
                                            <input type="checkbox" class="icheck" <?php if(strpos($quote, "view_quote_packing_medium") !== false){echo "checked";} ?> data-perm="view_quote_packing_medium"> Shown in message
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-md-5 control-label">Large Box - <strong>$<a class="edit_qut" data-inputclass="form-control" data-name="location_quote_packing_large" data-pk="<?php echo $location['location_token']; ?>" data-type="number" data-placement="right" data-title="Enter new large amount in $.." data-url="assets/app/update_settings.php?update=location"><?php echo number_format($location['location_quote_packing_large'], 2); ?></a></strong></label>
                            <div class="col-md-7">
                                <div class="input-group">
                                    <div class="icheck-inline">
                                        <label>
                                            <input type="checkbox" class="icheck" <?php if(strpos($quote, "view_quote_packing_large") !== false){echo "checked";} ?> data-perm="view_quote_packing_large"> Shown in message
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-md-5 control-label">Dishpack - <strong>$<a class="edit_qut" data-inputclass="form-control" data-name="location_quote_packing_dishpack" data-pk="<?php echo $location['location_token']; ?>" data-type="number" data-placement="right" data-title="Enter new dishpack amount in $.." data-url="assets/app/update_settings.php?update=location"><?php echo number_format($location['location_quote_packing_dishpack'], 2); ?></a></strong></label>
                            <div class="col-md-7">
                                <div class="input-group">
                                    <div class="icheck-inline">
                                        <label>
                                            <input type="checkbox" class="icheck" <?php if(strpos($quote, "view_quote_packing_dishpack") !== false){echo "checked";} ?> data-perm="view_quote_packing_dishpack"> Shown in message
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-md-5 control-label">Wardrobe - <strong>$<a class="edit_qut" data-inputclass="form-control" data-name="location_quote_packing_wardrobe" data-pk="<?php echo $location['location_token']; ?>" data-type="number" data-placement="right" data-title="Enter new wardrobe amount in $.." data-url="assets/app/update_settings.php?update=location"><?php echo number_format($location['location_quote_packing_wardrobe'], 2); ?></a></strong></label>
                            <div class="col-md-7">
                                <div class="input-group">
                                    <div class="icheck-inline">
                                        <label>
                                            <input type="checkbox" class="icheck" <?php if(strpos($quote, "view_quote_packing_wardrobe") !== false){echo "checked";} ?> data-perm="view_quote_packing_wardrobe"> Shown in message
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-md-5 control-label">Packing Paper - <strong>$<a class="edit_qut" data-inputclass="form-control" data-name="location_quote_packing_paper" data-pk="<?php echo $location['location_token']; ?>" data-type="number" data-placement="right" data-title="Enter new paper amount in $.." data-url="assets/app/update_settings.php?update=location"><?php echo number_format($location['location_quote_packing_paper'], 2); ?></a></strong></label>
                            <div class="col-md-7">
                                <div class="input-group">
                                    <div class="icheck-inline">
                                        <label>
                                            <input type="checkbox" class="icheck" <?php if(strpos($quote, "view_quote_packing_paper") !== false){echo "checked";} ?> data-perm="view_quote_packing_paper"> Shown in message
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-md-5 control-label">Tape - <strong>$<a class="edit_qut" data-inputclass="form-control" data-name="location_quote_packing_tape" data-pk="<?php echo $location['location_token']; ?>" data-type="number" data-placement="right" data-title="Enter new tape amount in $.." data-url="assets/app/update_settings.php?update=location"><?php echo number_format($location['location_quote_packing_tape'], 2); ?></a></strong></label>
                            <div class="col-md-7">
                                <div class="input-group">
                                    <div class="icheck-inline">
                                        <label>
                                            <input type="checkbox" class="icheck" <?php if(strpos($quote, "view_quote_packing_tape") !== false){echo "checked";} ?> data-perm="view_quote_packing_tape"> Shown in message
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-md-5 control-label">Shrinkwrap - <strong>$<a class="edit_qut" data-inputclass="form-control" data-name="location_quote_packing_shrinkwrap" data-pk="<?php echo $location['location_token']; ?>" data-type="number" data-placement="right" data-title="Enter new shrink wrap amount in $.." data-url="assets/app/update_settings.php?update=location"><?php echo number_format($location['location_quote_packing_shrinkwrap'], 2); ?></a></strong></label>
                            <div class="col-md-7">
                                <div class="input-group">
                                    <div class="icheck-inline">
                                        <label>
                                            <input type="checkbox" class="icheck" <?php if(strpos($quote, "view_quote_packing_shrinkwrap") !== false){echo "checked";} ?> data-perm="view_quote_packing_shrinkwrap"> Shown in message
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
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
            $('.catcher_item').unbind().on('focusout', function(){
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
            $('.icheck').each(function(){
                var perm = $(this).attr('data-perm');
                var chec = $(this).attr('checked');
                if(chec){
                    $('#edit_'+perm).show()
                } else {
                    $('#edit_'+perm).hide();
                }
                $(this).iCheck({
                    checkboxClass: 'icheckbox_minimal',
                    radioClass: 'iradio_minimal'
                }).on("ifChanged", function(){
                    var checked = $(this).iCheck('update')[0].checked;
                    var perm    = $(this).attr('data-perm');
                    if(checked){
                        $.ajax({
                            url: 'assets/app/update_settings.php?update=loc_qut',
                            type: 'POST',
                            data: {
                                value: checked,
                                perm: perm,
                                uuid: "<?php echo $location['location_token']; ?>"
                            },
                            success:function() {
                                toastr.info("<strong>Logan says:</strong><Br/>I have added that to this locations quote.");
                            },
                            error:function(){

                            }
                        });
                        $('#edit_'+perm).show();
                    } else {
                        $.ajax({
                            url: 'assets/app/update_settings.php?update=loc_qut',
                            type: 'POST',
                            data: {
                                value: checked,
                                perm: perm,
                                uuid: "<?php echo $location['location_token']; ?>"
                            },
                            success:function() {
                                toastr.error("<strong>Logan says:</strong><Br/>I have removed that from this locations quote.");
                            },
                            error:function(){

                            }
                        });
                        $('#edit_'+perm).hide();
                    }
                });
            });
            $('.perms').on('click', function(e) {
                var type  = $(this).attr("data-type");
                var title = $(this).attr("data-title");

                $('.edit_qut').editable({
                    step: 'any'
                })

                $('#perms_title').html(title);
                $('.edit_perms').hide();
                $('#edit_perms_'+type).show();
            });
            $("#add_service_rate").validate({
                errorElement: 'span', //default input error message container
                errorClass: 'font-red', // default input error message class
                rules: {
                    item: {
                        required: true
                    },
                    desc: {
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
                    redeemable: {
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
            $('#add_storagetypes_form').validate({
                errorElement: 'span', //default input error message container
                errorClass: 'font-red', // default input error message class
                ignore: "",
                rules: {
                    desc: {
                        required: true
                    },
                    l: {
                        required: true
                    },
                    w: {
                        required: true
                    },
                    h: {
                        required: true
                    },
                    price: {
                        required: true
                    },
                    climate: {
                        required: true
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
                    label.remove();
                },


                submitHandler: function(form) {
                    $.ajax({
                        url: "assets/app/add_setting.php?setting=service_storagetypes&luid=<?php echo $_GET['luid']; ?>",
                        type: "POST",
                        data: $('#add_storagetypes_form').serialize(),
                        success: function(data) {
                            toastr.info('<strong>Logan says</strong>:<br/>'+data+' has been added to your list of available storage unit types for this location.');
                            storagetypes.getDataTable().ajax.reload();
                            $('#add_storagetypes_form')[0].reset();
                        },
                        error: function() {
                            toastr.error('<strong>Logan says</strong>:<br/>That page didnt respond correctly. Try again, or create a support ticket for help.');
                        }
                    });
                }
            });
            $('#add_storage_form').validate({
                errorElement: 'span', //default input error message container
                errorClass: 'font-red', // default input error message class
                ignore: "",
                rules: {
                    type: {
                        required: true
                    },
                    qa: {
                        required: true,
                        min: 1
                    },
                    status: {
                        required: true
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
                    label.remove();
                },


                submitHandler: function(form) {
                    $.ajax({
                        url: "assets/app/add_setting.php?setting=service_storage&luid=<?php echo $_GET['luid']; ?>",
                        type: "POST",
                        data: $('#add_storage_form').serialize(),
                        success: function(data) {
                            toastr.info('<strong>Logan says</strong>:<br/>'+data+' has been added to your list of available storage units for this location.');
                            storage.getDataTable().ajax.reload();
                            $('#add_storage_form')[0].reset();
                        },
                        error: function() {
                            toastr.error('<strong>Logan says</strong>:<br/>That page didnt respond correctly. Try again, or create a support ticket for help.');
                        }
                    });
                }
            });
            $("#add_zipcodes_form").validate({
                errorElement: 'span', //default input error message container
                errorClass: 'font-red', // default input error message class
                rules: {
                    code: {
                        required: true,
                        maxlength: 3,
                        minlength: 3
                    }
                },
                messages: {
                    code: {
                        maxlength: 'Please enter no more than 3 numbers.',
                        minlength: 'Please enter no less than 3 numbers.'
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
                        "url": "assets/app/api/service_storage.php?p=xEx&luid=<?php echo $_GET['luid']; ?>",
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
            var storagetypes = new Datatable();

            storagetypes.init({
                src: $("#storagetypes"),
                onSuccess: function (storagetypes) {
                },
                onError: function (storagetypes) {
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
                        "url": "assets/app/api/service_storage.php?p=ExE&luid=<?php echo $_GET['luid']; ?>",
                    },
                }
            });
            storagetypes.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
                e.preventDefault();
                var action = $(".table-group-action-input", storagetypes.getTableWrapper());
                if (action.val() != "" && storagetypes.getSelectedRowsCount() > 0) {
                    storagetypes.setAjaxParam("customActionType", "group_action");
                    storagetypes.setAjaxParam("customActionName", action.val());
                    storagetypes.setAjaxParam("id", storagetypes.getSelectedRows());
                    storagetypes.getDataTable().ajax.reload();
                    storagetypes.clearAjaxParams();
                } else if (action.val() == "") {
                    Metronic.alert({
                        type: 'danger',
                        icon: 'warning',
                        message: 'Please select an action',
                        container: storagetypes.getTableWrapper(),
                        place: 'prepend'
                    });
                } else if (storagetypes.getSelectedRowsCount() === 0) {
                    Metronic.alert({
                        type: 'danger',
                        icon: 'warning',
                        message: 'No record selected',
                        container: storagetypes.getTableWrapper(),
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
            $('#show_add_storagetypes').on('click', function(){
                $('#add_storagetypes').removeClass('hide');
            });
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
