<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/3/2017
 * Time: 3:52 AM
 */
session_start();
include '../../app/init.php';

if(isset($_SESSION['logged'])){
    $profile = mysql_fetch_array(mysql_query("SELECT user_status, user_id, user_company_name, user_company_token, user_pic, user_fname, user_lname, user_phone, user_email, user_website, user_token, user_group, user_employer, user_employer_location, user_employer_rate, user_employer_salary, user_employer_hired, user_employer_dln, user_employer_dle, user_employer_dls, user_employer_dot_exp, user_address, user_state, user_zip, user_city, user_address2, user_state2, user_city2, user_zip2 FROM fmo_users WHERE user_token='".mysql_real_escape_string($_GET['uuid'])."'"));
    if(!empty($profile['user_employer']) && !empty($profile['user_employer_location'])) {
        $employee = true;
        $location = mysql_fetch_array(mysql_query("SELECT location_name, location_state FROM fmo_locations WHERE location_token='".mysql_real_escape_string($profile['user_employer_location'])."'"));
    } else {$employee = false;$location = mysql_fetch_array(mysql_query("SELECT location_name, location_state FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'"));}

    ?>
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
                        if($employee == true && $_SESSION['group'] <= 2){
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
                            if($employee == true && $_SESSION['group'] <= 2){
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
                                            $events = mysql_query("SELECT event_name, event_date_start, event_date_end, event_time, event_token, event_status FROM fmo_locations_events WHERE event_user_token='".mysql_real_escape_string($profile['user_token'])."'");
                                            if(mysql_num_rows($events) > 0){
                                                while($event = mysql_fetch_assoc($events)){
                                                    ?>
                                                    <div class="todo-tasklist-item todo-tasklist-item-border-red <?php if($event['event_status'] != 0){echo "load_page";} else {echo "load_profile_tab";} ?>"
                                                        <?php
                                                        if($event['event_status'] == 0){
                                                            ?>
                                                            data-href="assets/pages/sub/profile_event_wizard.php?conf=<?php echo $event['event_token']; ?>"
                                                            data-page-title="Configure <?php echo $event['event_name']; ?>"
                                                            <?php
                                                        } else {
                                                            ?>
                                                            data-href="assets/pages/event.php?ev=<?php echo $event['event_token']; ?>"
                                                            data-page-title="<?php echo $event['event_name']; ?>"
                                                            <?php
                                                        }
                                                        ?>
                                                        >
                                                        <div class="todo-tasklist-item-title">
                                                            <?php echo $event['event_name']; ?>
                                                            <?php
                                                            if($event['event_status'] == 0){
                                                                ?>
                                                                <span class="todo-tasklist-badge badge badge-roundless badge-danger">HOT LEAD</span>
                                                                <?php
                                                            }
                                                            ?>
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
                                                    <form role="form" id="add_documents">
                                                        <table class="table table-striped table-bordered table-hover datatable-2" data-src="assets/app/api/profile.php?type=documents&uuid=<?php echo $profile['user_token']; ?>">
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
                                                                <td><input type="file" class="form-control form-filter input-sm" name="file"></td>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <div class="col-md-3">
                                                                            <select class="form-control input-sm" name="file_type">
                                                                                <option disabled selected value="">Select one..</option>
                                                                                <option value="Copy of ID">Copy of ID</option>
                                                                                <option value="Handbook">Handbook</option>
                                                                                <option value="I9">I9</option>
                                                                                <option value="State Tax Form">State Tax Form</option>
                                                                                <option value="Federal Tax Form">Federal Tax Form</option>
                                                                                <option value="Application">Application</option>
                                                                                <option value="Driver Questionare">Driver Questionare</option>
                                                                                <option value="Previous Employer Check Authorization">Previous Employer Check Authorization</option>
                                                                                <option value="Manager Road Test">Manager Road Test</option>
                                                                                <option value="5 Years MVR">5 Years MVR</option>
                                                                                <option value="Scan of DOT Physical">Scan of DOT Physical</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-9">
                                                                            <input type="text" class="form-control form-filter input-sm" name="file_desc">
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-sm red margin-bottom add_document"><i class="fa fa-download"></i> Save</button>
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
                                                    <table class="table table-striped table-bordered table-hover datatable-2" id="timeclock_admin" data-src="assets/app/api/time_clock.php?admin=trl&uuid=<?php echo $profile['user_token']; ?>">
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
                                                    <table class="table table-striped table-bordered table-hover datatable-2" data-src="assets/app/api/profile.php?type=timeline&uuid=<?php echo $profile['user_token']; ?>">
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
    <script>
        $(document).ready(function(){
            $('.datatable-2').each(function(){
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
            $("#add_documents").validate({
                errorElement: 'span', //default input error message container
                errorClass: 'font-red', // default input error message class
                rules: {
                    file: {
                        required: true
                    },
                    file_type: {
                        required: true
                    },
                    file_desc: {
                        required: true
                    }
                }
            });
            $('.add_document').on('click', function(){
                if($("#add_documents").valid()){
                    $.ajax({
                        url: "assets/app/add_setting.php?setting=document&uuid=<?php echo $profile['user_token']; ?>",
                        type: "POST",
                        data: new FormData($('#add_documents')[0]),
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            toastr.info('<strong>Logan says</strong>:<br/>Document has been added to users documents table.');
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
