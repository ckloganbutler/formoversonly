<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/3/2017
 * Time: 3:55 AM
 */
ini_set("log_errors", 1);
ini_set("error_log", "/tmp/php-error.log");
session_start();
if(isset($_SESSION['logged'])){
    include '../app/init.php';
    mysql_query("UPDATE fmo_users SET user_last_location='".mysql_real_escape_string(basename(__FILE__, '.php')).".php?".$_SERVER['QUERY_STRING']."' WHERE user_token='".mysql_real_escape_string($_SESSION['uuid'])."'");
    $profile = mysql_fetch_array(mysql_query("SELECT user_status, user_id, user_company_name, user_company_token, user_pic, user_fname, user_lname, user_phone, user_ems_phone, user_email, user_website, user_token, user_group, user_employer, user_employer_location, user_employer_rate, user_dob, user_employer_salary, user_employer_hired, user_employer_dln, user_employer_dle, user_employer_dls, user_employer_dot_exp, user_address, user_state, user_zip, user_city, user_address2, user_state2, user_city2, user_zip2 FROM fmo_users WHERE user_token='".mysql_real_escape_string($_GET['uuid'])."'"));
    if(!empty($profile['user_employer']) && !empty($profile['user_employer_location'])) {
        $employee = true;
        $location = mysql_fetch_array(mysql_query("SELECT location_name, location_state FROM fmo_locations WHERE location_token='".mysql_real_escape_string($profile['user_employer_location'])."'"));
    } else {$employee = false;$location = mysql_fetch_array(mysql_query("SELECT location_name, location_state FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'"));}
    ?>
    <div class="page-content">
        <h3 class="page-title">
            <?php echo $profile['user_fname']." ".$profile['user_lname']; ?>
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
                    <a class="load_page" data-href="assets/pages/profile.php?uuid=<?php echo $_GET['uuid']; ?>" data-page-title="<?php echo $profile['user_fname']." ".$profile['user_lname']; ?>"><?php echo $profile['user_fname']." ".$profile['user_lname']; ?></a>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="col-md-12">
                <!-- BEGIN PROFILE SIDEBAR -->
                <div class="profile-sidebar">
                    <!-- PORTLET MAIN -->
                    <div class="portlet light profile-sidebar-portlet" style="padding-top: 0!important;">

                        <!-- SIDEBAR USERPIC -->
                            <div class="form-group text-center" >
                                <form id="pp_upload" action="" method="POST" role="form">
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="fileinput-new thumbnail">
                                            <?php
                                            if(!empty($profile['user_pic'])){
                                                ?>
                                                <img id="pp" src="<?php echo $profile['user_pic']; ?>" alt="" style="width: 100%; height: 200px; display: block;"/>
                                                <?php
                                            } else {
                                                ?>
                                                <img id="pp" src="assets/admin/layout/img/default.png" alt="" style="width: 100%; height: 200px; display: block;"/>
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
                                            <button class="btn red updt_pp fileinput-exists">Submit </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <!-- END SIDEBAR USERPIC -->
                        <!-- SIDEBAR USER TITLE -->
                        <div class="profile-usertitle" style="margin-top: -15px;">
                            <div class="profile-usertitle-name">
                                <?php echo $profile['user_fname']." ".$profile['user_lname']; ?>
                            </div>
                            <div class="profile-usertitle-job font-red">
                                <?php echo $profile['user_company_name']; ?>
                            </div>
                        </div>
                        <!-- END SIDEBAR USER TITLE -->
                        <!-- SIDEBAR BUTTONS -->
                        <div class="profile-userbuttons">
                            <?php
                            if($profile['user_group'] == 3.0){
                                ?>
                                <button type="button" class="btn btn-circle btn-info btn-sm">CUSTOMER</button>
                                <?php
                            } elseif($profile['user_group'] == 1.0){
                                ?>
                                <button type="button" class="btn btn-circle btn-danger btn-sm">ADMINISTRATOR</button>
                                <?php
                            } elseif($profile['user_group'] == 2.0){
                                ?>
                                <button type="button" class="btn btn-circle btn-success btn-sm">MANAGER</button>
                                <?php
                            } elseif($profile['user_group'] == 4.0){
                                ?>
                                <button type="button" class="btn btn-circle btn-info btn-sm">CUSTOMER SERVICE</button>
                                <?php
                            } elseif($profile['user_group'] == 5.1){
                                ?>
                                <button type="button" class="btn btn-circle btn-warning btn-sm">DRIVER</button>
                                <?php
                            } elseif($profile['user_group'] == 5.2){
                                ?>
                                <button type="button" class="btn btn-circle btn-warning2 btn-sm">HELPER</button>
                                <?php
                            } elseif($profile['user_group'] == 5.3){
                                ?>
                                <button type="button" class="btn btn-circle btn-default btn-sm">CREWMAN/OTHER</button>
                                <?php
                            }
                            ?>
                        </div>
                        <!-- END SIDEBAR BUTTONS -->
                        <!-- SIDEBAR MENU -->
                        <div class="profile-usermenu">
                            <ul class="nav">
                                <?php
                                if($profile['user_group'] == 3){
                                   ?>
                                    <li>
                                        <a class="load_profile_tab" data-href="assets/pages/sub/profile_create_event.php?luid=<?php echo $_GET['luid']; ?>&uuid=<?php echo $profile['user_token']; ?>" data-page-title="Book new move"><i class="icon-plus"></i>Create event for <?php echo $profile['user_fname']; ?>  </a>
                                    </li>
                                    <?php
                                }
                                ?>
                                <li class="active">
                                    <a class="load_profile_tab" data-href="assets/pages/sub/profile_personal_settings.php?uuid=<?php echo $profile['user_token']; ?>" data-page-title="Personal Settings"><i class="icon-user"></i>Personal Settings </a>
                                </li>
                                <?php
                                if($profile['user_group'] == 1){
                                    ?>
                                    <li>
                                        <a class="load_profile_tab" data-href="assets/pages/sub/profile_company_settings.php?uuid=<?php echo $profile['user_token']; ?>" data-page-title="Company Settings"><i class="icon-settings"></i>Company Settings  </a>
                                    </li>
                                    <?php
                                }
                                ?>
                            </ul>
                        </div>
                        <!-- END MENU -->
                    </div>
                    <!-- END PORTLET MAIN -->
                    <!-- PORTLET MAIN -->
                    <?php
                        if($profile['user_group'] == 1){
                            ?>
                            <div class="portlet light">
                                <!-- LETS DO SOME STATS!!! YOU CANT SEE IT -->
                                <?php
                                    $countLocations = mysql_num_rows(mysql_query("SELECT location_id FROM fmo_locations WHERE location_owner_token='".$profile['user_token']."'"));
                                    $countEmployees = mysql_num_rows(mysql_query("SELECT user_id FROM fmo_users WHERE user_employer='".$profile['user_company_token']."'"));
                                    $countCustomers = mysql_num_rows(mysql_query("SELECT user_id FROM fmo_users WHERE user_creator='".$profile['user_company_token']."' AND user_group=3"));
                                ?>
                                <!-- END STAT GATHERING -->
                                <div class="row list-separated profile-stat">
                                    <div class="col-md-4 col-sm-4 col-xs-6">
                                        <div class="uppercase profile-stat-title">
                                            <?php echo $countLocations ?>
                                        </div>
                                        <div class="uppercase profile-stat-text">
                                            Locations
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-6">
                                        <div class="uppercase profile-stat-title">
                                            <?php echo $countEmployees ?>
                                        </div>
                                        <div class="uppercase profile-stat-text">
                                            Employees
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-6">
                                        <div class="uppercase profile-stat-title">
                                            <?php echo $countCustomers; ?>
                                        </div>
                                        <div class="uppercase profile-stat-text">
                                            Customers
                                        </div>
                                    </div>
                                </div>
                                <!-- END STAT -->
                                <div>
                                    <h4 class="profile-desc-title">About <i class="fa fa-angle-right"></i></h4>
                                    <span class="profile-desc-text"> Joshua is an outstanding boss and great innovative thinker. </span>
                                    <div class="margin-top-20 profile-desc-link">
                                        <i class="fa fa-globe"></i>
                                        <a target="_blank" href="http://www.heretotheremovers.com">www.heretotheremovers.com</a>
                                    </div>
                                </div>
                            </div>
                            <?php
                        } elseif($employee == true){
                            ?>
                            <div class="portlet light">
                                <!-- LETS DO SOME STATS!!! YOU CANT SEE IT -->
                                <?php

                                ?>
                                <!-- END STAT GATHERING -->
                                <div class="row list-separated profile-stat">
                                    <div class="col-md-6 col-sm-6 col-xs-6">
                                        <div class="uppercase profile-stat-title">
                                            20
                                        </div>
                                        <div class="uppercase profile-stat-text">
                                            Hours Worked <br/>(this period)
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-6">
                                        <div class="uppercase profile-stat-title">
                                            8
                                        </div>
                                        <div class="uppercase profile-stat-text">
                                            Moves Completed <br/>(this period)
                                        </div>
                                    </div>
                                </div>
                                <!-- END STAT -->
                                <div>
                                    <h4 class="profile-desc-title">About <i class="fa fa-angle-right"></i></h4>
                                    <span class="profile-desc-text"> We will show basic information on your current employment statistics, for your reference. </span>
                                    <div class="margin-top-20 profile-desc-link">
                                        <i class="fa fa-globe"></i>
                                        <a target="_blank" href="http://www.heretotheremovers.com">www.heretotheremovers.com</a>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    ?>

                    <!-- END PORTLET MAIN -->
                </div>
                <!-- END BEGIN PROFILE SIDEBAR -->
                <!-- BEGIN PROFILE CONTENT -->
                <div class="profile-content" id="profile-content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="portlet light">
                                <div class="portlet-title tabbable-line">
                                    <ul class="nav nav-tabs nav-justified">
                                        <li class="active">
                                            <a href="#about" data-toggle="tab">About <?php echo $profile['user_fname']; ?></a>
                                        </li>
                                        <?php
                                        if($profile['user_group'] == 3){
                                            ?>
                                            <li>
                                                <a href="#bookings" data-toggle="tab">Bookings</a>
                                            </li>
                                            <?php
                                        }
                                        ?>
                                        <?php
                                        if($employee == true){
                                            ?>
                                            <li>
                                                <a href="#documents" data-toggle="tab">Documents</a>
                                            </li>
                                            <li>
                                                <a href="#timeline" data-toggle="tab">Timeline</a>
                                            </li>
                                            <li>
                                                <a href="#administration" data-toggle="tab">Administration</a>
                                            </li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                </div>
                                <div class="portlet-body">
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="about">
                                            <h3>Personal Information</h3>
                                            <div class="row static-info" style="margin-top: 20px;">
                                                <div class="col-md-5 name">
                                                    Name:
                                                </div>
                                                <div class="col-md-7 value">
                                                    <a class="pu_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_fname" data-pk="<?php echo $profile['user_token']; ?>" data-type="text" data-placement="right" data-title="Enter new first name.." data-url="assets/app/update_settings.php?update=usr_prf">
                                                        <?php echo $profile['user_fname']; ?>
                                                    </a>
                                                    <a class="pu_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_lname" data-pk="<?php echo $profile['user_token']; ?>" data-type="text" data-placement="right" data-title="Enter new last name.." data-url="assets/app/update_settings.php?update=usr_prf">
                                                        <?php echo $profile['user_lname']; ?>
                                                    </a>(#<?php echo $profile['user_id']; ?>)
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    Email:
                                                </div>
                                                <div class="col-md-7 value">
                                                    <a class="pu_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_email" data-pk="<?php echo $profile['user_token']; ?>" data-type="text" data-placement="right" data-title="Enter new email.." data-url="assets/app/update_settings.php?update=usr_prf">
                                                        <?php echo $profile['user_email']; ?>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    Phone Number:
                                                </div>
                                                <div class="col-md-7 value">
                                                    <a class="pu_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_phone" data-pk="<?php echo $profile['user_token']; ?>" data-type="text" data-placement="right" data-title="Enter new phone number.." data-url="assets/app/update_settings.php?update=usr_prf">
                                                        <?php echo clean_phone($profile['user_phone']); ?>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    Emergency Contact Number:
                                                </div>
                                                <div class="col-md-7 value">
                                                    <a class="pu_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_ems_phone" data-pk="<?php echo $profile['user_token']; ?>" data-type="text" data-placement="right" data-title="Enter new emergency contact number.." data-url="assets/app/update_settings.php?update=usr_prf">
                                                        <?php
                                                        if(!empty($profile['user_ems_phone'])){
                                                            echo clean_phone($profile['user_ems_phone']);
                                                        }
                                                        ?>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    Date of Birth:
                                                </div>
                                                <div class="col-md-7 value">
                                                    <a class="pu_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_dob" data-pk="<?php echo $profile['user_token']; ?>" data-type="date" data-format="mm/dd/yyyy" data-placement="right" data-title="Select new date.." data-url="assets/app/update_settings.php?update=usr_prf">
                                                        <?php
                                                        echo $profile['user_dob'];
                                                        ?>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    Primary Address:
                                                </div>
                                                <div class="col-md-7 value">
                                                    <a class="pu_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_address" data-pk="<?php echo $profile['user_token']; ?>" data-type="text" data-placement="right" data-title="Enter new street address.." data-url="assets/app/update_settings.php?update=usr_prf">
                                                        <?php echo $profile['user_address']; ?>
                                                    </a>,
                                                    <a class="pu_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_city" data-pk="<?php echo $profile['user_token']; ?>" data-type="text" data-placement="right" data-title="Enter new city.." data-url="assets/app/update_settings.php?update=usr_prf">
                                                        <?php echo $profile['user_city']; ?>
                                                    </a>,
                                                    <a class="pu_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_state" data-pk="<?php echo $profile['user_token']; ?>" data-type="select" data-source="[{value: 'AL', text: 'Alabama'},{value: 'AK', text: 'Alaska'},{value: 'AZ', text: 'Arizona'},{value: 'AR', text: 'Arkansas'},{value: 'CA', text: 'California'},{value: 'CO', text: 'Colorado'},{value: 'CT', text: 'Connecticut'},{value: 'DE', text: 'Delaware'},{value: 'DC', text: 'District Of Columbia'},{value: 'FL', text: 'Florida'},{value: 'GA', text: 'Georgia'},{value: 'HI', text: 'Hawaii'},{value: 'ID', text: 'Idaho'},{value: 'IL', text: 'Illinois'},{value: 'IN', text: 'Indiana'},{value: 'IA', text: 'Iowa'},{value: 'KS', text: 'Kansas'},{value: 'KY', text: 'Kentucky'},{value: 'LA', text: 'Louisiana'},{value: 'ME', text: 'Maine'},{value: 'MD', text: 'Maryland'},{value: 'MA', text: 'Massachusetts'},{value: 'MI', text: 'Michigan'},{value: 'MN', text: 'Minnesota'},{value: 'MS', text: 'Mississippi'},{value: 'MO', text: 'Missouri'},{value: 'MT', text: 'Montana'},{value: 'NE', text: 'Nebraska'},{value: 'NV', text: 'Nevada'},{value: 'NH', text: 'New Hampshire'},{value: 'NJ', text: 'New Jersey'},{value: 'NM', text: 'New Mexico'},{value: 'NY', text: 'New York'},{value: 'NC', text: 'North Carolina'},{value: 'ND', text: 'North Dakota'},{value: 'OH', text: 'Ohio'},{value: 'OK', text: 'Oklahoma'},{value: 'OR', text: 'Oregon'},{value: 'PW', text: 'Palau'},{value: 'PA', text: 'Pennsylvania'},{value: 'RI', text: 'Rhode Island'},{value: 'SC', text: 'South Carolina'},{value: 'SD', text: 'South Dakota'},{value: 'TN', text: 'Tennessee'},{value: 'TX', text: 'Texas'},{value: 'UT', text: 'Utah'},{value: 'VT', text: 'Vermont'},{value: 'VA', text: 'Virginia'},{value: 'WA', text: 'Washington'},{value: 'WV', text: 'West Virginia'},{value: 'WI', text: 'Wisconsin'},{value: 'WY', text: 'Wyoming'}]" data-inputclass="form-control" data-placement="right" data-title="Select new state.." data-url="assets/app/update_settings.php?update=usr_prf">
                                                        <?php echo $profile['user_state']; ?>
                                                    </a>,
                                                    <a class="pu_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_zip" data-pk="<?php echo $profile['user_token']; ?>" data-type="text" data-placement="right" data-title="Enter new zip code.." data-url="assets/app/update_settings.php?update=usr_prf">
                                                        <?php echo $profile['user_zip']; ?>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    Secondary Address:
                                                </div>
                                                <div class="col-md-7 value">
                                                    <a class="pu_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_address2" data-pk="<?php echo $profile['user_token']; ?>" data-type="text" data-placement="right" data-title="Enter new street address.." data-url="assets/app/update_settings.php?update=usr_prf">
                                                        <?php echo $profile['user_address2']; ?>
                                                    </a>,
                                                    <a class="pu_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_city2" data-pk="<?php echo $profile['user_token']; ?>" data-type="text" data-placement="right" data-title="Enter new city.." data-url="assets/app/update_settings.php?update=usr_prf">
                                                        <?php echo $profile['user_city2']; ?>
                                                    </a>,
                                                    <a class="pu_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_state2" data-pk="<?php echo $profile['user_token']; ?>" data-type="select" data-source="[{value: 'AL', text: 'Alabama'},{value: 'AK', text: 'Alaska'},{value: 'AZ', text: 'Arizona'},{value: 'AR', text: 'Arkansas'},{value: 'CA', text: 'California'},{value: 'CO', text: 'Colorado'},{value: 'CT', text: 'Connecticut'},{value: 'DE', text: 'Delaware'},{value: 'DC', text: 'District Of Columbia'},{value: 'FL', text: 'Florida'},{value: 'GA', text: 'Georgia'},{value: 'HI', text: 'Hawaii'},{value: 'ID', text: 'Idaho'},{value: 'IL', text: 'Illinois'},{value: 'IN', text: 'Indiana'},{value: 'IA', text: 'Iowa'},{value: 'KS', text: 'Kansas'},{value: 'KY', text: 'Kentucky'},{value: 'LA', text: 'Louisiana'},{value: 'ME', text: 'Maine'},{value: 'MD', text: 'Maryland'},{value: 'MA', text: 'Massachusetts'},{value: 'MI', text: 'Michigan'},{value: 'MN', text: 'Minnesota'},{value: 'MS', text: 'Mississippi'},{value: 'MO', text: 'Missouri'},{value: 'MT', text: 'Montana'},{value: 'NE', text: 'Nebraska'},{value: 'NV', text: 'Nevada'},{value: 'NH', text: 'New Hampshire'},{value: 'NJ', text: 'New Jersey'},{value: 'NM', text: 'New Mexico'},{value: 'NY', text: 'New York'},{value: 'NC', text: 'North Carolina'},{value: 'ND', text: 'North Dakota'},{value: 'OH', text: 'Ohio'},{value: 'OK', text: 'Oklahoma'},{value: 'OR', text: 'Oregon'},{value: 'PW', text: 'Palau'},{value: 'PA', text: 'Pennsylvania'},{value: 'RI', text: 'Rhode Island'},{value: 'SC', text: 'South Carolina'},{value: 'SD', text: 'South Dakota'},{value: 'TN', text: 'Tennessee'},{value: 'TX', text: 'Texas'},{value: 'UT', text: 'Utah'},{value: 'VT', text: 'Vermont'},{value: 'VA', text: 'Virginia'},{value: 'WA', text: 'Washington'},{value: 'WV', text: 'West Virginia'},{value: 'WI', text: 'Wisconsin'},{value: 'WY', text: 'Wyoming'}]" data-inputclass="form-control" data-placement="right" data-title="Select new state.." data-url="assets/app/update_settings.php?update=usr_prf">
                                                        <?php echo $profile['user_state2']; ?>
                                                    </a>,
                                                    <a class="pu_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_zip2" data-pk="<?php echo $profile['user_token']; ?>" data-type="text" data-placement="right" data-title="Enter new zip code.." data-url="assets/app/update_settings.php?update=usr_prf">
                                                        <?php echo $profile['user_zip2']; ?>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="row static-info">
                                                <div class="col-md-5 name">
                                                    Password:
                                                </div>
                                                <div class="col-md-7 value">
                                                    <a>Send password reset to <?php echo clean_phone($profile['user_phone']); ?></a>
                                                </div>
                                            </div>
                                            <?php
                                                if($employee == true){
                                                    ?>
                                                    <hr/>
                                                    <h3>Employee Information</h3>
                                                    <div class="row static-info">
                                                        <div class="col-md-5 name">
                                                            Role & Status:
                                                        </div>
                                                        <div class="col-md-7 value">
                                                            <a class="pu_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_group" data-pk="<?php echo $profile['user_token']; ?>" data-type="select" data-source="[<?php if($_SESSION['group'] == 1){ ?>{value: 1, text: 'Administrator'},<?php } ?>{value: 2, text: 'Manager'}, {value: 4, text: 'Customer Service'}, {value: 5.1, text: 'Driver'}, {value: 5.2, text: 'Helper'}, {value: 5.3, text: 'Crewman/Other'}]" data-placement="right" data-title="Enter new phone number.." data-url="assets/app/update_settings.php?update=usr_prf">
                                                                <?php
                                                                    if($profile['user_group'] == 1){
                                                                        echo "Administrator";
                                                                    } elseif($profile['user_group'] == 2){
                                                                        echo "Manager";
                                                                    } elseif($profile['user_group'] == 4){
                                                                        echo "Customer Service";
                                                                    } elseif($profile['user_group'] == 5.1){
                                                                        echo "Driver";
                                                                    } elseif($profile['user_group'] == 5.2){
                                                                        echo "Helper";
                                                                    } elseif($profile['user_group'] == 5.3){
                                                                        echo "Crewman/Other";
                                                                    }
                                                                ?>
                                                            </a> -
                                                            <a class="pu_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_status" data-pk="<?php echo $profile['user_token']; ?>" data-type="select" data-source="[{value: 0, text: 'Inactive'}, {value: 1, text: 'Active'}, {value: 2, text: 'Terminated'}]" data-placement="right" data-title="Select new status.." data-url="assets/app/update_settings.php?update=usr_prf">
                                                                <?php
                                                                if($profile['user_status'] == 0){
                                                                    echo "Inactive";
                                                                } elseif($profile['user_status'] == 1){
                                                                    echo "Active";
                                                                } elseif($profile['user_status'] == 2){
                                                                    echo "Terminated";
                                                                }
                                                                ?>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="row static-info">
                                                        <div class="col-md-5 name">
                                                            Pay Rate:
                                                        </div>
                                                        <div class="col-md-7 value">
                                                            $<a class="pu_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_employer_rate" data-pk="<?php echo $profile['user_token']; ?>" data-type="number" data-inputclass="form-control" data-placement="right" data-title="Enter new rate.." data-url="assets/app/update_settings.php?update=usr_prf">
                                                                <?php echo $profile['user_employer_rate']; ?>
                                                            </a>-
                                                            <a class="pu_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_employer_salary" data-pk="<?php echo $profile['user_token']; ?>" data-type="select" data-source="[{value: 1, text: 'Hourly'},{value: 2, text: 'Weekly'}]" data-placement="right" data-title="Select new salary type.." data-url="assets/app/update_settings.php?update=usr_prf">
                                                                <?php
                                                                if($profile['user_employer_salary'] == 1){
                                                                    echo "Hourly";
                                                                } elseif($profile['user_employer_salary'] == 2){
                                                                    echo "Weekly";
                                                                }
                                                                ?>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="row static-info">
                                                        <div class="col-md-5 name">
                                                            Created/Hired By:
                                                        </div>
                                                        <div class="col-md-7 value">
                                                            Joshua Baxter
                                                        </div>
                                                    </div>
                                                    <div class="row static-info">
                                                        <div class="col-md-5 name">
                                                            Date Hired:
                                                        </div>
                                                        <div class="col-md-7 value">
                                                            <a class="pu_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_employer_hired" data-pk="<?php echo $profile['user_token']; ?>" data-type="date" data-format="mm/dd/yyyy" data-placement="right" data-title="Select hire date.." data-url="assets/app/update_settings.php?update=usr_prf">
                                                                <?php echo $profile['user_employer_hired']; ?>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="row static-info">
                                                        <div class="col-md-5 name">
                                                            Dispatch From:
                                                        </div>
                                                        <div class="col-md-7 value">
                                                            <?php
                                                            $findLocations = mysql_query("SELECT location_name, location_token, location_state FROM fmo_locations WHERE location_owner_company_token='".$_SESSION['cuid']."' ORDER BY location_name ASC");
                                                            if(mysql_num_rows($findLocations) > 0){
                                                                $selectData = NULL;
                                                                while($loc = mysql_fetch_assoc($findLocations)){
                                                                    $selectData .= "{value: '".$loc['location_token']."', text: '".$loc['location_name']." (".$loc['location_state'].")'},";
                                                                }
                                                            }
                                                            ?>
                                                            <a class="pu_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_employer_location" data-pk="<?php echo $profile['user_token']; ?>" data-type="select" data-source="[<?php echo $selectData ?>]" data-placement="right" data-title="Select new dispatch location type.." data-url="assets/app/update_settings.php?update=usr_prf">
                                                                <?php
                                                                     echo $location['location_name']." (".$location['location_state'].")";
                                                                ?>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="row static-info">
                                                        <div class="col-md-5 name">
                                                            Drivers License Number, Expiration, & State:
                                                        </div>
                                                        <div class="col-md-7 value">
                                                            <a class="pu_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_employer_dln" data-pk="<?php echo $profile['user_token']; ?>" data-type="text" data-inputclass="form-control" data-placement="right" data-title="Enter new drivers license number.." data-url="assets/app/update_settings.php?update=usr_prf">
                                                                <?php echo $profile['user_employer_dln']; ?>
                                                            </a> -
                                                            <a class="pu_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_employer_dle" data-pk="<?php echo $profile['user_token']; ?>" data-type="date" data-format="mm/dd/yyyy" data-inputclass="form-control" data-placement="right" data-title="Select drivers license expiration date.." data-url="assets/app/update_settings.php?update=usr_prf">
                                                                <?php echo $profile['user_employer_dle']; ?>
                                                            </a> -
                                                            <a class="pu_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_employer_dls" data-pk="<?php echo $profile['user_token']; ?>" data-type="select" data-source="[{value: 'AL', text: 'Alabama'},{value: 'AK', text: 'Alaska'},{value: 'AZ', text: 'Arizona'},{value: 'AR', text: 'Arkansas'},{value: 'CA', text: 'California'},{value: 'CO', text: 'Colorado'},{value: 'CT', text: 'Connecticut'},{value: 'DE', text: 'Delaware'},{value: 'DC', text: 'District Of Columbia'},{value: 'FL', text: 'Florida'},{value: 'GA', text: 'Georgia'},{value: 'HI', text: 'Hawaii'},{value: 'ID', text: 'Idaho'},{value: 'IL', text: 'Illinois'},{value: 'IN', text: 'Indiana'},{value: 'IA', text: 'Iowa'},{value: 'KS', text: 'Kansas'},{value: 'KY', text: 'Kentucky'},{value: 'LA', text: 'Louisiana'},{value: 'ME', text: 'Maine'},{value: 'MD', text: 'Maryland'},{value: 'MA', text: 'Massachusetts'},{value: 'MI', text: 'Michigan'},{value: 'MN', text: 'Minnesota'},{value: 'MS', text: 'Mississippi'},{value: 'MO', text: 'Missouri'},{value: 'MT', text: 'Montana'},{value: 'NE', text: 'Nebraska'},{value: 'NV', text: 'Nevada'},{value: 'NH', text: 'New Hampshire'},{value: 'NJ', text: 'New Jersey'},{value: 'NM', text: 'New Mexico'},{value: 'NY', text: 'New York'},{value: 'NC', text: 'North Carolina'},{value: 'ND', text: 'North Dakota'},{value: 'OH', text: 'Ohio'},{value: 'OK', text: 'Oklahoma'},{value: 'OR', text: 'Oregon'},{value: 'PW', text: 'Palau'},{value: 'PA', text: 'Pennsylvania'},{value: 'RI', text: 'Rhode Island'},{value: 'SC', text: 'South Carolina'},{value: 'SD', text: 'South Dakota'},{value: 'TN', text: 'Tennessee'},{value: 'TX', text: 'Texas'},{value: 'UT', text: 'Utah'},{value: 'VT', text: 'Vermont'},{value: 'VA', text: 'Virginia'},{value: 'WA', text: 'Washington'},{value: 'WV', text: 'West Virginia'},{value: 'WI', text: 'Wisconsin'},{value: 'WY', text: 'Wyoming'}]" data-inputclass="form-control" data-placement="right" data-title="Select drivers license state.." data-url="assets/app/update_settings.php?update=usr_prf">
                                                                <?php echo $profile['user_employer_dls']; ?>
                                                            </a> -
                                                            <?php
                                                            $startdate2 = $profile['user_employer_dle'];
                                                            $expire2 = strtotime($startdate2. ' - 30 days');
                                                            $today2 = strtotime("today midnight");

                                                            if($today2 >= $expire2){
                                                                echo "<span class='text-danger'>Expiring soon</span>";
                                                            } else {
                                                                echo "<span class='text-success'>Valid</span>";
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                    <div class="row static-info">
                                                        <div class="col-md-5 name">
                                                            DOT Exiration:
                                                        </div>
                                                        <div class="col-md-7 value">
                                                            <a class="pu_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_employer_dot_exp" data-pk="<?php echo $profile['user_token']; ?>" data-type="date" data-format="mm/dd/yyyy" data-inputclass="form-control" data-placement="right" data-title="Select DOT expiration date.." data-url="assets/app/update_settings.php?update=usr_prf">
                                                                <?php echo $profile['user_employer_dot_exp']; ?>
                                                            </a> -
                                                            <?php
                                                            $startdate = $profile['user_employer_dot_exp'];
                                                            $expire = strtotime($startdate. ' - 30 days');
                                                            $today = strtotime("today midnight");

                                                            if($today >= $expire){
                                                                echo "<span class='text-danger'>Expiring soon</span>";
                                                            } else {
                                                                echo "<span class='text-success'>Valid</span>";
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>

                                                    <?php
                                                }
                                            ?>
                                            <hr/>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <a href="javascript:;" class="btn text-center red btn-sm edit" data-edit="pu_<?php echo $profile['user_token']; ?>"> <i class="fa fa-pencil"></i> <span class="hidden-sm hidden-md " >Edit</span></a>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        if($profile['user_group'] == 3){
                                            ?>
                                            <div class="tab-pane" id="bookings">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="todo-tasklist">
                                                            <?php
                                                            $events = mysql_query("SELECT event_name, event_date_start, event_date_end, event_time, event_token FROM fmo_locations_events WHERE event_user_token='".mysql_real_escape_string($profile['user_token'])."'");
                                                            if(mysql_num_rows($events) > 0){
                                                                while($event = mysql_fetch_assoc($events)){
                                                                    ?>
                                                                    <div class="todo-tasklist-item todo-tasklist-item-border-red load_page" data-href="assets/pages/event.php?ev=<?php echo $event['event_token']; ?>" data-page-title="<?php echo $event['event_name']; ?>">
                                                                        <div class="todo-tasklist-item-title">
                                                                            <?php echo $event['event_name']; ?>
                                                                        </div>
                                                                        <div class="todo-tasklist-item-text">
                                                                            Lorem ipsum dolor sit amet, consectetuer dolore dolor sit amet.
                                                                        </div>
                                                                        <div class="todo-tasklist-controls pull-left">
                                                                            <span class="todo-tasklist-date"><i class="fa fa-calendar"></i> <?php echo date('d M Y', strtotime($event['event_date_start'])); ?> - <?php echo date('d M Y', strtotime($event['event_date_end'])); ?> </span>
                                                                            <span class="todo-tasklist-badge badge badge-roundless">Local Move</span>
                                                                        </div>
                                                                    </div>
                                                                    <?php
                                                                }
                                                            } else {
                                                                ?>
                                                                <div class="note note-warning">
                                                                    <h4 class="block"><strong>Hmm..</strong> <?php echo strtolower($profile['user_fname']); ?>'s profile doesn't seem to have any event's scheduled.</h4>
                                                                    <p>
                                                                        You can schedule a booking/event to the left, under the users profile. After creating a new booking/event, you will have access to that event's dashboard through this section.
                                                                    </p>
                                                                </div>
                                                                <?php
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }


                                        if($employee == true){
                                            ?>
                                            <div class="tab-pane" id="documents">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="portlet">
                                                            <div class="portlet-title">
                                                                <div class="caption">
                                                                    <i class="fa fa-file-o"></i>Documents <small><span class="font-red">|</span> Missing files: <span class="font-red">Copy of ID, Handbook 19, State Tax Form, Federal Tax Form</span>.</small>
                                                                </div>
                                                                <div class="actions">
                                                                    <a class="btn default red-stripe show_form" data-show="#add_document">
                                                                        <i class="fa fa-plus"></i>
                                                                        <span class="hidden-480">Upload new document</span>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <div class="portlet-body">
                                                                <div class="table-container">
                                                                    <form role="form" id="add_service_rate">
                                                                        <table class="table table-striped table-bordered table-hover datatable" data-src="assets/app/api/profile.php?type=documents&uuid=<?php echo $profile['user_token']; ?>">
                                                                            <thead>
                                                                            <tr role="row" class="heading">
                                                                                <th width="18%">
                                                                                    File Thumbnail
                                                                                </th>
                                                                                <th>
                                                                                    File Type & Description
                                                                                </th>
                                                                                <th width="8%">
                                                                                    Actions
                                                                                </th>
                                                                            </tr>
                                                                            <tr role="row" class="filter" style="display: none;" id="add_document">
                                                                                <td><input type="text" class="form-control form-filter input-sm" name="item"></td>
                                                                                <td></td>
                                                                                <td>
                                                                                    <div class="margin-bottom-5">
                                                                                        <button type="button" class="btn btn-sm red margin-bottom add_service_rate"><i class="fa fa-download"></i> Save</button>
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
                                            <div class="tab-pane" id="childsupport">
                                                Child Support
                                            </div>
                                            <div class="tab-pane" id="administration">
                                                <div class="portlet light">
                                                    <div class="portlet-title tabbable-line">
                                                        <div class="caption">
                                                            <i class="fa fa-file-o"></i>Administration <small><span class="font-red">|</span> These are special settings only for administration purposes.</small>
                                                        </div>
                                                    </div>
                                                    <div class="portlet-body">
                                                        <div class="tab-content">
                                                            <div class="tab-pane active" id="tab">
                                                                <div class="table-container">
                                                                    <table class="table table-striped table-bordered table-hover datatable" id="timeclock_admin" data-src="assets/app/api/time_clock.php?admin=trl&uuid=<?php echo $profile['user_token']; ?>">
                                                                        <thead>
                                                                        <tr role="row" class="heading">
                                                                            <th>
                                                                                Date Worked
                                                                            </th>
                                                                            <th>
                                                                                Clock-in Date & Time
                                                                            </th>
                                                                            <th>
                                                                                Clock-out Date & Time
                                                                            </th>
                                                                            <th>
                                                                                Hours worked
                                                                            </th>
                                                                            <th>
                                                                                Edit
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
                                            <div class="tab-pane" id="timeline">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="portlet">
                                                            <div class="portlet-title">
                                                                <div class="caption">
                                                                    <i class="fa fa-clock-o"></i> Timeline
                                                                </div>
                                                                <div class="actions">
                                                                    <button class="btn btn-xs default red-stripe pull-right" data-toggle="modal" href="#child_support_only" style="margin-left: 5px;"><i class="fa fa-child"></i> View <strong>child support</strong></button>
                                                                    <button class="btn btn-xs default red-stripe pull-right" data-toggle="modal" href="#advances_only"><i class="fa fa-money"></i> View <strong>advances</strong></button>
                                                                    <button class="btn btn-xs default red-stripe pull-right" data-toggle="modal" href="#write_ups_only"><i class="fa fa-pencil"></i> View <strong>write-ups</strong></button>
                                                                    <button class="btn btn-xs default red-stripe pull-right" data-toggle="modal" href="#comments_only"><i class="fa fa-comments-o"></i> View <strong>comments</strong></button>
                                                                </div>
                                                            </div>
                                                            <div class="portlet-body">
                                                                <div class="table-container">
                                                                    <table class="table table-striped table-bordered table-hover datatable" data-src="assets/app/api/profile.php?type=timeline&uuid=<?php echo $profile['user_token']; ?>">
                                                                        <thead>
                                                                        <tr role="row" class="heading">
                                                                            <th width="12%">
                                                                                Record Timestamp
                                                                            </th>
                                                                            <th>
                                                                                Record Type
                                                                            </th>
                                                                            <th>
                                                                                Record Details
                                                                            </th>
                                                                            <th width="12%">
                                                                                Record Creator
                                                                            </th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>

                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- End: life time stats -->
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
                <!-- END PROFILE CONTENT -->
            </div>
        </div>
    </div>
    <?php
    if($employee == true){
        ?>
        <div class="modal fade bs-modal-lg" id="comments_only" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content box red">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h3 class="modal-title font-bold">Comments for <?php echo $profile['user_fname']; ?></h3>
                    </div>
                    <div class="modal-body">
                        <div class="portlet">
                            <div class="portlet-title" style="border-bottom: none;">
                                <div class="actions">
                                    <a class="btn default red-stripe show_form" data-show="#add_comment">
                                        <i class="fa fa-plus"></i>
                                        <span class="hidden-480">Add new comment</span>
                                    </a>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="table-container">
                                    <form role="form" id="add_comt">
                                        <table class="table table-striped table-bordered table-hover datatable" data-src="assets/app/api/profile.php?type=comments&uuid=<?php echo $profile['user_token']; ?>">
                                            <thead>
                                            <tr role="row" class="heading">
                                                <th width="12%">
                                                    Comment Timestamp
                                                </th>
                                                <th>
                                                    Comment Content
                                                </th>
                                                <th width="12%">
                                                    Comment Creator
                                                </th>
                                            </tr>
                                            <tr role="row" class="filter" style="display: none;" id="add_comment">
                                                <td></td>
                                                <td><input type="text" class="form-control form-filter input-sm" name="comment"></td>
                                                <td>
                                                    <div class="margin-bottom-5">
                                                        <button type="button" class="btn btn-sm red margin-bottom add_comment"><i class="fa fa-download"></i> Save</button>
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
                    <div class="modal-footer">
                        <button type="button" class="btn default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade bs-modal-lg" id="write_ups_only" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content box red">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h3 class="modal-title font-bold">Write ups for <?php echo $profile['user_fname']; ?></h3>
                    </div>
                    <div class="modal-body">
                        <div class="portlet">
                            <div class="portlet-title" style="border-bottom: none;">
                                <div class="actions">
                                    <a class="btn default red-stripe show_form" data-show="#add_writeup">
                                        <i class="fa fa-plus"></i>
                                        <span class="hidden-480">Add new write-up</span>
                                    </a>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="table-container">
                                    <form role="form" id="add_writeups">
                                        <table class="table table-striped table-bordered table-hover datatable" data-src="assets/app/api/profile.php?type=writeups&uuid=<?php echo $profile['user_token']; ?>">
                                            <thead>
                                            <tr role="row" class="heading">
                                                <th width="12%">
                                                    Write-up Timestamp
                                                </th>
                                                <th>
                                                    Write-up Reasoning
                                                </th>
                                                <th>
                                                    Write-up Action
                                                </th>
                                                <th width="12%">
                                                    Write-up Creator
                                                </th>
                                            </tr>
                                            <tr role="row" class="filter" style="display: none;" id="add_writeup">
                                                <td></td>
                                                <td><input type="text" class="form-control form-filter input-sm" name="reasoning"></td>
                                                <td>
                                                    <select class="form-control input-sm" name="action">
                                                        <option disabled selected value="">Select action..</option>
                                                        <option value="Warning">Warning</option>
                                                        <option value="Suspended for day">Suspended for day</option>
                                                        <option value="Suspended for week">Suspended for week</option>
                                                        <option value="Pay Reduction">Pay Reduction</option>
                                                        <option value="Termination">Termination</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <div class="margin-bottom-5">
                                                        <button type="button" class="btn btn-sm red margin-bottom add_writeup"><i class="fa fa-download"></i> Save</button>
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
                    <div class="modal-footer">
                        <button type="button" class="btn default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade bs-modal-lg" id="advances_only" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content box red">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h3 class="modal-title font-bold">Advances for <?php echo $profile['user_fname']; ?></h3>
                    </div>
                    <div class="modal-body">
                        <?php
                        $user_pay = mysql_fetch_array(mysql_query("SELECT user_employer_rate FROM fmo_users WHERE user_token='".mysql_real_escape_string($profile['user_token'])."'"));
                        if($user_pay['user_employer_rate'] > 0){
                            $refStart                = new DateTime('2017-01-02');
                            $periodLength            = 14;
                            $now                     = new DateTime();
                            $cur                     = date('Y-m-d');
                            $daysIntoCurrentPeriod   = (int)$now->diff($refStart)->format('%a') % $periodLength;
                            $currentPeriodStart      = clone $now;
                            $currentPeriodStart->sub(new DateInterval('P'.$daysIntoCurrentPeriod.'D'));
                            $start                   = date("Y-m-d", strtotime($cur." -".$daysIntoCurrentPeriod." days"));
                            $end                     = date('Y-m-d', strtotime($start." +14 days"));
                            $hours = array();
                            $prev  = mysql_query("
                            SELECT advance_requested FROM fmo_users_employee_advances
                            WHERE (advance_timestamp>='".mysql_real_escape_string($start)."' AND advance_timestamp<'".mysql_real_escape_string($end)."') AND advance_user_token='".mysql_real_escape_string($profile['user_token'])."'");
                            $hours = mysql_query("
                            SELECT timeclock_user, timeclock_hours FROM fmo_users_employee_timeclock 
                            WHERE (timeclock_clockout>='".mysql_real_escape_string($start)."' AND timeclock_clockout<'".mysql_real_escape_string($end)."') AND timeclock_user='".mysql_real_escape_string($profile['user_token'])."'") or die(mysql_error());
                            $pay = array();
                            if(mysql_num_rows($hours) > 0){
                                while($work = mysql_fetch_assoc($hours)){
                                    $pay['hours']+=$work['timeclock_hours'];
                                }
                                if($pay['hours'] > 0){
                                    $pay['rate']      = $user_pay['user_employer_rate'];
                                    $pay['earned']    = $pay['hours'] * $user_pay['user_employer_rate'];
                                    if(mysql_num_rows($prev) > 0){
                                        while($loans = mysql_fetch_assoc($prev)){
                                            $pay['loans'] += $loans['advance_requested'];
                                        }
                                    } else {$pay['loans'] = 0;}
                                    $pay['available'] = ($pay['earned'] - $pay['loans']) * .25;
                                } else {
                                    $pay['available'] = 0;
                                    $pay['hours']     = 0;
                                    $pay['earned']    = 0;
                                }
                            } else {
                                $pay['available'] = 0;
                                $pay['hours']     = 0;
                                $pay['earned']    = 0;
                            }
                        } else {
                            $pay['available'] = 0;
                            $pay['hours']     = 0;
                            $pay['earned']    = 0;
                        }
                        ?>
                        <div class="portlet">
                            <div class="portlet-title" style="border-bottom: none;">
                                <div class="actions">
                                    <a class="btn default red-stripe show_form" data-show="#add_advance">
                                        <i class="fa fa-plus"></i>
                                        <span class="hidden-480">Add new advance</span>
                                    </a>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="table-container">
                                    <form role="form" id="add_advances">
                                        <table class="table table-striped table-bordered table-hover datatable" data-src="assets/app/api/profile.php?type=advances&uuid=<?php echo $profile['user_token']; ?>">
                                            <thead>
                                            <tr role="row" class="heading">
                                                <th width="12%">
                                                    Advance Timestamp
                                                </th>
                                                <th>
                                                    Advance Requested
                                                </th>
                                                <th>
                                                    Advance Available
                                                </th>
                                                <th>
                                                    Advance Reasoning
                                                </th>
                                                <th width="12%">
                                                    Advance Authorization
                                                </th>
                                            </tr>
                                            <tr role="row" class="filter" style="display: none;" id="add_advance">
                                                <td></td>
                                                <td><input type="number" class="form-control form-filter input-sm" name="requested"></td>
                                                <td><input type="number" class="form-control form-filter input-sm" name="available" readonly value="<?php echo number_format($pay['available'], 2); ?>"></td>
                                                <td><input type="text" class="form-control form-filter input-sm" name="reasoning"></td>
                                                <td>
                                                    <div class="margin-bottom-5">
                                                        <button type="button" class="btn btn-sm red margin-bottom add_advance"><i class="fa fa-download"></i> Save</button>
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
                            <div class="portlet-footer">
                                <strong><?php echo $profile['user_fname']; ?>'s advance information for this period (<?php echo $start." - ".$end; ?>): </strong><br/>
                                <strong id="ad_hrs"><?php echo $pay['hours']; ?></strong> hours @ $<strong><?php echo $user_pay['user_employer_rate']; ?></strong>/hour, with $<strong id="ad_earned"><?php echo number_format($pay['earned'], 2); ?></strong> earned (<span class="text-danger">-$<strong id="ad_loans"><?php echo number_format($pay['loans'], 2); ?></strong></span> from previous loans), making $<strong id="ad_avail"><?php echo number_format($pay['available'], 2); ?></strong> available.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade bs-modal-lg" id="child_support_only" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content box red">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h3 class="modal-title font-bold">Child Support for <?php echo $profile['user_fname']; ?></h3>
                    </div>
                    <div class="modal-body">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
    <script type="text/javascript">
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
            $('.show_form').on('click', function() {
                var show = $(this).attr('data-show');

                $(show).show();
            });
            $("#add_comt").validate({
                errorElement: 'span', //default input error message container
                errorClass: 'font-red', // default input error message class
                rules: {
                    comment: {
                        required: true
                    }
                }
            });
            $("#add_writeups").validate({
                errorElement: 'span', //default input error message container
                errorClass: 'font-red', // default input error message class
                rules: {
                    reasoning: {
                        required: true
                    },
                    action: {
                        required: true
                    }
                }
            });
            $("#add_advances").validate({
                errorElement: 'span', //default input error message container
                errorClass: 'font-red', // default input error message class
                rules: {
                    requested: {
                        required: true,
                        remote: 'assets/app/api/profile.php?type=advance_amt&uuid=<?php echo $profile['user_token']; ?>'
                    },
                    reasoning: {
                        required: true
                    }
                },
                messages: {
                    requested: {
                        remote: 'Limit exceeded <i class="fa fa-arrow-right"></i>'
                    }
                }
            });
            $('.add_comment').on('click', function(){
                if($("#add_comt").valid()){
                    $.ajax({
                        url: "assets/app/add_setting.php?setting=usr_cmt&uuid=<?php echo $profile['user_token']; ?>",
                        type: "POST",
                        data: $('#add_comt').serialize(),
                        success: function(data) {
                            toastr.info('<strong>Logan says</strong>:<br/>Comment has been added to users comment history.');
                        },
                        error: function() {
                            toastr.error('<strong>Logan says</strong>:<br/>That page didnt respond correctly. Try again, or create a support ticket for help.');
                        }
                    });
                }
            });
            $('.add_writeup').on('click', function(){
                if($("#add_writeups").valid()){
                    $.ajax({
                        url: "assets/app/add_setting.php?setting=usr_writeup&uuid=<?php echo $profile['user_token']; ?>",
                        type: "POST",
                        data: $('#add_writeups').serialize(),
                        success: function(data) {
                            toastr.info('<strong>Logan says</strong>:<br/>Write-up has been added to users write-up history.');
                        },
                        error: function() {
                            toastr.error('<strong>Logan says</strong>:<br/>That page didnt respond correctly. Try again, or create a support ticket for help.');
                        }
                    });
                }
            });
            $('.add_advance').on('click', function(){
                if($("#add_advances").valid()){
                    $.ajax({
                        url: "assets/app/add_setting.php?setting=usr_advance&uuid=<?php echo $profile['user_token']; ?>",
                        type: "POST",
                        data: $('#add_advances').serialize(),
                        success: function(data) {
                            var inf = JSON.parse(data);
                            $('#ad_hrs').html(inf.hours);
                            $('#ad_earned').html(inf.earned);
                            $('#ad_loans').html(inf.loans);
                            $('#ad_avail').html(inf.available);
                            $('#add_advances')[0].reset();
                            $('input[name="available"]').val(inf.available);
                            toastr.info('<strong>Logan says</strong>:<br/>Advance has been added to users advance history.');
                        },
                        error: function() {
                            toastr.error('<strong>Logan says</strong>:<br/>That page didnt respond correctly. Try again, or create a support ticket for help.');
                        }
                    });
                }
            });
            $(document).on('click', '.load_profile_tab', function(){
                var act = $(this).attr('data-act');
                var url = $(this).attr('data-href');
                var tit = $(this).attr('data-page-title');
                $(".active").removeClass("active");
                $(this).parent().addClass("active");
                Pace.track(function(){
                    $.ajax({
                        url: url,
                        success: function(data) {
                            $('#profile-content').html(data);

                        },
                        error: function() {
                            toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                        }
                    });
                });
            });
            $(document).on('click', '.updt_pp', function () {
                var formData = new FormData($('form#pp_upload')[0]);
                $.ajax({
                    type: 'POST',
                    url: 'assets/app/upload_image.php?uuid=<?php echo $profile['user_token']; ?>',
                    data: formData,
                    async: false,
                    success: function(data) {
                        toastr.success("<strong>Logan says</strong>:<br/>Your new profile picture has been uploaded. Refreshing the dashboard to show your new image..");
                        $('.pp').attr("src", data);
                    },
                    error: function() {
                        toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
                return false;
            })
        });
    </script>
    <?php
} else {
    header("Location: ../../../index.php?err=no_access");
}

