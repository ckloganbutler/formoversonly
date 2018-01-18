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
    $profile = mysql_fetch_array(mysql_query("SELECT user_status, user_setup, user_military, user_autopay, user_autopay_token, user_autopay_last4, user_group, user_id, user_pin, user_last_login, user_creator_user, user_creation, user_company_name, user_company_token, user_employer_commission, user_pic, user_fname, user_lname, user_phone, user_ems_phone, user_email, user_website, user_token, user_group, user_employer, user_employer_location, user_employer_rate, user_dob, user_employer_salary, user_employer_hired, user_employer_dln, user_employer_dle, user_employer_dls, user_employer_dot_exp, user_address, user_state, user_zip, user_city, user_address2, user_state2, user_city2, user_zip2, user_repeatclient, user_repeatclient_terms, user_repeatclient_notes, user_last_ext_location, user_permissions, user_esc_permissions FROM fmo_users WHERE user_token='".mysql_real_escape_string($_GET['uuid'])."'"));
    if(!empty($profile['user_employer']) && !empty($profile['user_employer_location'])) {
        $employee = true;
        $location = mysql_fetch_array(mysql_query("SELECT location_name, location_nickname, location_state, location_token FROM fmo_locations WHERE location_token='".mysql_real_escape_string($profile['user_employer_location'])."'"));
    } else {
        $employee = false;
        $location = mysql_fetch_array(mysql_query("SELECT location_name, location_nickname, location_state, location_token FROM fmo_locations WHERE location_token='".mysql_real_escape_string($profile['user_last_ext_location'])."'"));
    }
    $uuidperm = mysql_fetch_array(mysql_query("SELECT user_esc_permissions FROM fmo_users WHERE user_token='".mysql_real_escape_string($_SESSION['uuid'])."'"));
    ?>
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light">
                <div class="portlet-title tabbable-line">
                    <?php
                    if(($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_employees_view_information") !== false) && $profile['user_group'] != 3){
                        ?>
                        <div class="btn-group-justified" style="margin-bottom: 25px !important;">
                            <div class="btn-group">
                                <button class="btn default red-stripe" data-toggle="modal" href="#child_support_only"><i class="fa fa-child"></i> <strong>Child support</strong></button>
                            </div>
                            <div class="btn-group">
                                <button class="btn default red-stripe" data-toggle="modal" href="#advances_only"><i class="fa fa-money"></i> <strong>Advances</strong></button>
                            </div>
                            <div class="btn-group">
                                <button class="btn default red-stripe" data-toggle="modal" href="#labor_only"><i class="fa fa-area-chart"></i> <strong>Labor</strong></button>
                            </div>
                            <div class="btn-group">
                                <button class="btn default red-stripe" data-toggle="modal" href="#write_ups_only"><i class="fa fa-pencil"></i> <strong>Write-ups</strong></button>
                            </div>
                            <div class="btn-group">
                                <button class="btn default red-stripe" data-toggle="modal" href="#comments_only"><i class="fa fa-comments-o"></i> <strong>Comments</strong></button>
                            </div>
                        </div>
                        <?php
                    }
                    ?>

                    <ul class="nav nav-tabs nav-justified margin-top-25">
                        <li <?php if($profile['user_group'] != 3){ ?>class="active"<?php } ?>>
                            <a href="#about" data-toggle="tab">About <?php echo $profile['user_fname']; ?></a>
                        </li>
                        <li>
                            <a href="#timeline" data-toggle="tab">Timeline</a>
                        </li>
                        <li>
                            <a href="#documents" data-toggle="tab">Documents</a>
                        </li>
                        <?php
                        if($profile['user_group'] == 3){
                            ?>

                            <li class="active">
                                <a href="#bookings" data-toggle="tab">Events</a>
                            </li>
                            <li class="">
                                <a href="#storage" id="storage_tab" data-toggle="tab">Storage</a>
                            </li>
                            <?php
                        }
                        ?>

                        <?php
                        if(($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_employees_view_information") !== false) && $profile['user_group'] != 3){
                            if($_SESSION['group'] == 1 || $_SESSION['uuid'] == 'DJ5RELUMTA7QPHWJK'){
                                ?>
                                <li>
                                    <a href="#administration" data-toggle="tab">Administration</a>
                                </li>
                                <?php
                            }
                        }
                        ?>
                    </ul>
                </div>
                <div class="portlet-body">
                    <div class="tab-content">
                        <div class="tab-pane <?php if($profile['user_group'] != 3){ ?>active<?php } ?>" id="about">
                            <?php
                            if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_employee_view_profile") !== false || strpos($uuidperm['user_esc_permissions'], "view_customers_search_view_profile") !== false){
                                ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3 style="margin-top: 10px;"><strong>Personal information</strong> for <?php echo $profile['user_fname']; ?>
                                        </h3>
                                        <hr/>
                                    </div>
                                </div>
                                <div class="row static-info" style="margin-top: 20px;">
                                    <div class="col-md-5 name">
                                        Name (ID):
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
                                        $startdate2 = date('Y-m-d', strtotime($profile['user_employer_dle']));
                                        $expire2 = date('Y-m-d', strtotime($startdate2. ' - 30 days'));
                                        $today2 = date('Y-m-d', strtotime("today midnight"));

                                        if($today2 > $startdate2){
                                            echo "<span class='text-danger'>Expired</span>";
                                        } elseif($today2 > $expire2) {
                                            echo "<span class='text-danger'>Expiring soon</span>";
                                        } else {
                                            echo "<span class='text-success'>Valid</span>";
                                        }
                                        ?>
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
                                        <a class="upr">Send password reset to <?php echo clean_phone($profile['user_phone']); ?></a>
                                    </div>
                                </div>
                                <?php
                            }
                            if($profile['user_group'] == 3 &&  strpos($uuidperm['user_esc_permissions'], "view_customers_search_view_profile") !== false){
                                ?>
                                <hr/>
                                <h3><strong>Customer</strong> Information</h3>
                                <div class="row static-info" style="margin-top: 20px;">
                                    <div class="col-md-5 name">
                                        Repeat Client:
                                    </div>
                                    <div class="col-md-7 value">
                                        <a class="pu_<?php echo $profile['user_token']; ?>" data-name="user_repeatclient" data-pk="<?php echo $profile['user_token']; ?>" data-type="select" data-source="[{value: '1', text: 'Yes'},{value: '0', text: 'No'}]" data-inputclass="form-control" data-placement="right" data-title="Select new value.." data-url="assets/app/update_settings.php?update=usr_prf">
                                            <?php
                                            if($profile['user_repeatclient'] == 1){
                                                echo "Yes";
                                            } else {
                                                echo "No";
                                            }
                                            ?>
                                        </a>
                                        <br/><em>Payment Terms</em>:
                                        <a class="pu_<?php echo $profile['user_token']; ?>"  data-name="user_repeatclient_terms" data-pk="<?php echo $profile['user_token']; ?>" data-type="select" data-source="[{value: 'COD', text: 'COD'},{value: 'NET15', text: 'NET15'},{value: 'NET30', text: 'NET30'}]" data-inputclass="form-control" data-placement="right" data-title="Select new terms.." data-url="assets/app/update_settings.php?update=usr_prf">
                                            <?php echo $profile['user_repeatclient_terms']; ?>
                                        </a> <br/> <em>Agreed Terms</em>:
                                        <a class="pu_<?php echo $profile['user_token']; ?>"  data-name="user_repeatclient_notes" data-pk="<?php echo $profile['user_token']; ?>" data-type="text" data-inputclass="form-control" data-placement="right" data-title="Enter new notes.." data-url="assets/app/update_settings.php?update=usr_prf">
                                            <?php echo $profile['user_repeatclient_notes']; ?>
                                        </a>
                                    </div>
                                </div>
                                <div class="row static-info" style="margin-top: 20px;">
                                    <div class="col-md-5 name">
                                        <strong>Active Duty</strong> Military:
                                    </div>
                                    <div class="col-md-7 value">
                                        <a class="pu_<?php echo $profile['user_token']; ?>" data-name="user_military" data-pk="<?php echo $profile['user_token']; ?>" data-type="select" data-source="[{value: '1', text: 'Yes'},{value: '0', text: 'No'}]" data-inputclass="form-control" data-placement="right" data-title="Select new value.." data-url="assets/app/update_settings.php?update=usr_prf">
                                            <?php
                                            if($profile['user_military'] == 1){
                                                echo "Yes";
                                            } else {
                                                echo "No";
                                            }
                                            ?>
                                        </a>
                                    </div>
                                </div>
                                <div class="row static-info" style="margin-top: 20px;">
                                    <div class="col-md-5 name">
                                        <strong>Auto Pay&trade;</strong> Enrolled:
                                    </div>
                                    <div class="col-md-7 value">
                                        <a class="pu_<?php echo $profile['user_token']; ?>" data-name="user_autopay" data-pk="<?php echo $profile['user_token']; ?>" data-type="select" data-source="[{value: '1', text: 'Yes'},{value: '0', text: 'No'}]" data-inputclass="form-control" data-placement="right" data-title="Select new value.." data-url="assets/app/update_settings.php?update=usr_prf">
                                            <?php
                                            if($profile['user_autopay'] == 1){
                                                echo "Yes";
                                            } else {
                                                echo "No";
                                            }
                                            ?>
                                        </a>
                                        <br/>
                                        Customer Token: <strong><?php echo $profile['user_autopay_token']; ?></strong> <br/>
                                        Customer Credit/Debt Last 4 #: <strong><?php echo $profile['user_autopay_last4']; ?></strong> <br/>
                                        <strong><span class="text-danger">*</span> if Auto Pay&trade; is set to yes, the next/last card used will be the card automatically charged when permitted.</strong>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>

                            <?php
                            if(($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_employees_view_information") !== false) && $profile['user_group'] != 3){
                                ?>
                                <hr/>
                                <h3><strong>Employee</strong> Information</h3>
                                <div class="row static-info" style="margin-top: 20px;">
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
                                        </a>- ($<?php echo number_format($profile['user_employer_rate'] * 2080, 2); ?> / year)
                                    </div>
                                </div>
                                <div class="row static-info">
                                    <div class="col-md-5 name">
                                        Commission Rate:
                                    </div>
                                    <div class="col-md-7 value">
                                        %<a class="pu_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_employer_commission" data-pk="<?php echo $profile['user_token']; ?>" data-type="number" data-inputclass="form-control" data-placement="right" data-title="Enter new commission rate (whole number percentage).." data-url="assets/app/update_settings.php?update=usr_prf">
                                            <?php echo number_format($profile['user_employer_commission'] * 100, 2); ?>
                                        </a>
                                    </div>
                                </div>
                                <div class="row static-info">
                                    <div class="col-md-5 name">
                                        Driver PIN:
                                    </div>
                                    <div class="col-md-7 value">
                                        <a class="pu_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_pin" data-pk="<?php echo $profile['user_token']; ?>" data-type="number" data-inputclass="form-control" data-placement="right" data-title="Enter new PIN.." data-url="assets/app/update_settings.php?update=usr_prf">
                                            <?php echo $profile['user_pin']; ?>
                                        </a>
                                    </div>
                                </div>
                                <div class="row static-info">
                                    <div class="col-md-5 name">
                                        Created/Hired By:
                                    </div>
                                    <div class="col-md-7 value">
                                        <?php echo name($profile['user_creator_user']); ?>
                                    </div>
                                </div>
                                <div class="row static-info">
                                    <div class="col-md-5 name">
                                        Date Hired:
                                    </div>
                                    <div class="col-md-7 value">
                                        <a class="pu_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_employer_hired" data-pk="<?php echo $profile['user_token']; ?>" data-type="date" data-format="mm/dd/yyyy" data-placement="right" data-title="Select hire date.." data-url="assets/app/update_settings.php?update=usr_prf">
                                            <?php
                                            if(!empty($profile['user_employer_hired'])){
                                                echo $profile['user_employer_hired'];
                                            } else {
                                                echo date('m-d-Y', strtotime($profile['user_creation']));
                                            }

                                            ?>
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
                                        DOT Exiration:
                                    </div>
                                    <div class="col-md-7 value">
                                        <a class="pu_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_employer_dot_exp" data-pk="<?php echo $profile['user_token']; ?>" data-type="date" data-format="mm/dd/yyyy" data-inputclass="form-control" data-placement="right" data-title="Select DOT expiration date.." data-url="assets/app/update_settings.php?update=usr_prf">
                                            <?php echo $profile['user_employer_dot_exp']; ?>
                                        </a> -
                                        <?php
                                        $startdate = date('Y-m-d', strtotime($profile['user_employer_dot_exp']));
                                        $expire = date('Y-m-d', strtotime($startdate. ' - 30 days'));
                                        $today = date('Y-m-d', strtotime("today midnight"));

                                        if($today > $startdate){
                                            echo "<span class='text-danger'>Expired</span>";
                                        } elseif($today > $expire) {
                                            echo "<span class='text-danger'>Expiring soon</span>";
                                        } else {
                                            echo "<span class='text-success'>Valid</span>";
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="row static-info">
                                    <div class="col-md-5 name">
                                        Payroll Enrolled:

                                    </div>
                                    <div class="col-md-7 value">
                                        <a class="pu_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_setup" data-pk="<?php echo $profile['user_token']; ?>" data-type="select" data-source="[{value: 0, text: 'No'}, {value: 1, text: 'Yes'}]" data-placement="right" data-title="Has employee been put into SurePayRoll?" data-url="assets/app/update_settings.php?update=usr_prf">
                                            <?php
                                            if($profile['user_setup'] == 0){
                                                echo "No (new hire, needs to be put into your payroll system)";
                                            } elseif($profile['user_setup'] == 1) {
                                                echo "Yes";
                                            }
                                            ?>
                                        </a> <br/>
                                        <small class="text-muted"><span class="text-danger">*</span> While we may track employee hours through events, clock in/out tools, and other methods--you may want to ensure consistency by using a system like SurePayRoll</small>

                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                            <hr/>
                            <?php
                            if($_SESSION['group'] <= 2 || $_SESSION['group'] == 4.0){
                                ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <a href="javascript:;" class="btn text-center red btn-sm edit" data-edit="pu_<?php echo $profile['user_token']; ?>" data-reload="tables"> <i class="fa fa-pencil"></i> <span class="hidden-sm hidden-md " >Edit</span></a>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>

                        </div>
                        <div class="tab-pane" id="timeline">
                            <div class="row">
                                <div class="col-md-12">
                                    <h3 style="margin-top: 10px;"><strong>Timeline</strong> for <?php echo $profile['user_fname']; ?>
                                    </h3>
                                    <hr/>
                                </div>
                            </div>
                            <!--
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="timeline">
                                        <!-- TIMELINE ITEM
                                        <div class="timeline-item">
                                            <div class="timeline-badge">
                                                <img class="timeline-badge-userpic" src="<?php echo picture($_SESSION['uuid']); ?>">
                                            </div>
                                            <div class="timeline-body">
                                                <div class="timeline-body-arrow">
                                                </div>
                                                <div class="timeline-body-head">
                                                    <div class="timeline-body-head-caption">
                                                        <a href="javascript:;" class="timeline-body-title font-blue-madison">Logan Butler</a>
                                                        <span class="timeline-body-time font-grey-cascade">Payment at 7:14pm</span>
                                                    </div>
                                                    <div class="timeline-body-head-actions">
                                                        <div class="btn-group">
                                                            <button class="btn btn-circle green-haze btn-sm dropdown-toggle" type="button" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                                                Actions <i class="fa fa-angle-down"></i>
                                                            </button>
                                                            <ul class="dropdown-menu pull-right" role="menu">
                                                                <li>
                                                                    <a href="javascript:;">Action </a>
                                                                </li>
                                                                <li>
                                                                    <a href="javascript:;">Another action </a>
                                                                </li>
                                                                <li>
                                                                    <a href="javascript:;">Something else here </a>
                                                                </li>
                                                                <li class="divider">
                                                                </li>
                                                                <li>
                                                                    <a href="javascript:;">Separated link </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="timeline-body-content">
                                                    <span class="font-grey-cascade">
                                                        Payment of Credit/Debt for $66.95 was added to storage contract #12345678. Notes: Approval: ch_1BUgzmC1O7q0QhbW9eRg9TLo (Stripe charge: $66.95 - 2.9% + .30¢ = $64.71 deposited)
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="portlet">
                                        <div class="portlet-body">
                                            <div class="table-container">
                                                <table class="table table-striped table-bordered table-hover datatable" data-src="assets/app/api/profile.php?type=timeline&uuid=<?php echo $profile['user_token']; ?>">
                                                    <thead>
                                                    <tr role="row" class="heading">
                                                        <th>
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
                        <div class="tab-pane" id="documents">
                            <div class="row">
                                <div class="col-md-12">
                                    <h3 style="margin-top: 10px;"><strong>Documents</strong> for <?php echo $profile['user_fname']; ?>
                                        <a class="btn default red-stripe show_form pull-right btn-sm" data-show="#add_document">
                                            <i class="fa fa-plus"></i>
                                            <span class="hidden-480">Upload new document</span>
                                        </a>
                                    </h3>
                                    <hr/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="portlet">
                                        <div class="portlet-body">
                                            <div class="table-container">
                                                <form role="form" id="add_documents">
                                                    <table class="table table-striped table-bordered table-hover datatable" id="p_docs" data-src="assets/app/api/profile.php?type=documents&uuid=<?php echo $profile['user_token']; ?>">
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
                                                                            <option value="Other (explain)">Other (explain)</option>
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
                        <?php
                        if($profile['user_group'] == 3){
                            ?>
                            <div class="tab-pane active" id="bookings">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3 style="margin-top: 10px;"><strong>Events</strong> for <?php echo $profile['user_fname']; ?> <small class="pull-right text-muted"><span class="text-danger bold">2</span> events</small></h3><hr/><br/>
                                    </div>
                                </div>
                               <div class="row">
                                    <div class="col-md-12">
                                        <div class="todo-tasklist">
                                            <?php
                                            $events = mysql_query("SELECT event_name, event_date_start, event_date_end, event_time, event_token, event_status, event_type, event_subtype, event_booking, event_truckfee, event_laborrate, event_comments FROM fmo_locations_events WHERE event_user_token='".mysql_real_escape_string($profile['user_token'])."' ORDER BY event_date_start DESC, event_time+0 DESC");
                                            if(mysql_num_rows($events) > 0){
                                            while($event = mysql_fetch_assoc($events)){
                                                switch($event['event_status']){
                                                    case 0: $status = "Hot Lead"; $color = "red"; $badge = "badge-danger"; break;
                                                    case 1: $status = "New Booking"; $color = "blue"; $badge = "badge-info"; break;
                                                    case 2: $status = "Confirmed"; $color = "green"; $badge = "badge-success"; break;
                                                    case 3: $status = "Left Message"; $color = "red"; $badge = "badge-danger"; break;
                                                    case 4: $status = "On Hold"; $color = "red"; $badge = "badge-danger"; break;
                                                    case 5: $status = "Cancelled"; $color = "red"; $badge = "badge-danger"; break;
                                                    case 6: $status = "Customer Self Booking"; $color = "purple"; $badge = "badge-purple"; break;
                                                    case 8: $status = "Completed"; $color = "yellow"; $badge = "badge-warning"; break;
                                                    case 9: $status = "Dead Hot Lead"; $color = "red"; $badge = "badge-danger"; break;
                                                    default: $status = "On Hold"; $color = "red"; $badge = "badge-danger"; break;
                                                }
                                                $times = explode("to", $event['event_time']);
                                                $start = mysql_fetch_array(mysql_query("SELECT address_address, address_city FROM fmo_locations_events_addresses WHERE address_event_token='".$event['event_token']."' AND address_type=1"));
                                                $end   = mysql_fetch_array(mysql_query("SELECT address_address, address_city FROM fmo_locations_events_addresses WHERE address_event_token='".$event['event_token']."' AND address_type=2"));
                                                ?>
                                                <div class="todo-tasklist-item todo-tasklist-item-border-red
                                                                        <?php
                                                if($_SESSION['group'] != 3){
                                                    if($event['event_status'] != 0 && $event['event_status'] != 9){echo "load_page";} else {echo "load_profile_tab";}
                                                } else {
                                                    if($event['event_status'] == 0 && $event['event_status'] == 9){
                                                        echo "load_profile_tab";
                                                    }
                                                }
                                                ?>"

                                                    <?php
                                                    if($_SESSION['group'] != 3){
                                                        if($event['event_status'] == 0 || $event['event_status'] == 9){
                                                            ?>
                                                            data-href="assets/pages/sub/profile_event_wizard.php?conf=<?php echo $event['event_token']; ?>&uuid=<?php echo $profile['user_token']; ?>"
                                                            data-page-title="Configure <?php echo $event['event_name']; ?>"
                                                            <?php
                                                        } else {
                                                            ?>
                                                            data-href="assets/pages/event.php?ev=<?php echo $event['event_token']; ?>"
                                                            data-page-title="<?php echo $event['event_name']; ?>"
                                                            <?php
                                                        }
                                                    } else {
                                                        if($event['event_status'] == 0 || $event['event_status'] == 9){
                                                            ?>
                                                            data-href="assets/pages/sub/profile_event_wizard.php?conf=<?php echo $event['event_token']; ?>&uuid=<?php echo $profile['user_token']; ?>"
                                                            data-page-title="Configure <?php echo $event['event_name']; ?>"
                                                            <?php
                                                        }
                                                    }

                                                    ?>>
                                                    <div class="row">
                                                        <div class="col-md-7">
                                                            <div class="todo-tasklist-item-title">
                                                                <span class="badge badge-roundless <?php echo $badge; ?>"><?php echo $status; ?></span> &nbsp;
                                                                <?php echo $event['event_name']; ?>
                                                                <span class="font-<?php echo $color; ?>">|</span>
                                                                <small>
                                                                    <i class="fa fa-truck"></i> Trucks: <?php echo $event['event_truckfee']; ?> +
                                                                    <i class="fa fa-users"></i> Crew size: <?php echo $event['event_laborrate']; ?>
                                                                </small>
                                                            </div>
                                                            <div class="todo-tasklist-item-text">
                                                                <?php
                                                                if(!empty($start['address_address']) && !empty($end['address_address'])){
                                                                    ?>
                                                                    <strong>Start:</strong> <?php echo $start['address_address'].", ".$start['address_city']; ?> <i class="fa fa-map"></i> <strong>End:</strong> <?php echo $end['address_address'].", ".$end['address_city']; ?>
                                                                    <?php
                                                                } elseif(!empty($event['event_comments']) || $event['event_status'] == 0) {
                                                                    ?>
                                                                    <strong>Comments:</strong> <?php echo $event['event_comments']; ?>
                                                                    <?php
                                                                } else {
                                                                    ?>
                                                                    <strong>Click to view more details & manage</strong>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </div>
                                                            <div class="todo-tasklist-controls pull-left">
                                                                <?php
                                                                if(!empty($event['event_type'])){
                                                                    ?>
                                                                    <span class="todo-tasklist-badge badge badge-roundless badge-danger"><?php echo $event['event_type']; ?></span>
                                                                    <?php
                                                                }
                                                                if(!empty($event['event_subtype'])){
                                                                    ?>
                                                                    <span class="todo-tasklist-badge badge badge-roundless badge-info"><?php echo $event['event_subtype']; ?></span>
                                                                    <?php
                                                                }
                                                                if($event['event_booking'] == 1){
                                                                    ?>
                                                                    <span class="todo-tasklist-badge badge badge-roundless badge-success"><i class="fa fa-check"  style="margin-top: -6px"></i> Booking fee paid</span>
                                                                    <?php
                                                                }
                                                                ?>
                                                                <span class="todo-tasklist-date"><i class="fa fa-calendar"></i> <strong><?php echo date('M dS, Y', strtotime($event['event_date_start'])); ?></strong> - <?php echo date('M dS, Y', strtotime($event['event_date_end'])); ?> @ <strong><?php echo $times[0]; ?></strong> to <?php echo $times[1]; ?></span>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-5">
                                                            <div class="pull-right">
                                                                <h3>
                                                                    <small><span class="text-success">*</span> Total</small> <span class="text-success bold" id="owe_total_<?php echo $event['event_token']; ?>"></span> |
                                                                    <small><span class="text-danger">*</span> Due</small> <span class="text-danger bold" id="owe_unpaid_<?php echo $event['event_token']; ?>"></span></h3>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <script>
                                                    $.ajax({
                                                        url: 'assets/app/api/event.php?type=inv&luid=<?php echo $_GET['luid']; ?>',
                                                        type: 'POST',
                                                        data: {
                                                            event: '<?php echo $event['event_token']; ?>'
                                                        },
                                                        success: function(m){
                                                            var owe = JSON.parse(m);
                                                            $(document).find('#owe_total_<?php echo $event['event_token']; ?>').html("$" + parseFloat(owe.total).toFixed(2));
                                                            $(document).find('#owe_unpaid_<?php echo $event['event_token']; ?>').html("$" + parseFloat(owe.unpaid).toFixed(2));
                                                        },
                                                        error: function(e){

                                                        }
                                                    });
                                                </script>
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
                            <div class="tab-pane" id="storage">
                                <?php
                                if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_storage_contracts") !== false){
                                    ?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h3 style="margin-top: 10px;"><strong>Storage Units</strong> for <?php echo $profile['user_fname']; ?> &nbsp; &nbsp;
                                                <?php
                                                if($profile['user_military'] == 1){
                                                    ?>
                                                    <small><i class="fa fa-check text-success"></i> <strong>Active Duty Military</strong></small>
                                                    <?php
                                                }
                                                if($profile['user_autopay'] == 1){
                                                    ?>
                                                    <small><i class="fa fa-check text-success"></i> <strong>Auto Pay</strong></small>
                                                    <?php
                                                }
                                                ?>
                                                <a data-toggle="modal" href="#create_comment" class="btn btn-md default green-stripe pull-right">Add new comment <i class="fa fa-comment"></i> </a>
                                                <a data-toggle="modal" href="#create_alt" class="btn btn-md default green-stripe pull-right">Add new authorized contact <i class="fa fa-user-plus"></i> </a>
                                                <a data-toggle="modal" href="#new_unit" class="btn btn-md default green-stripe pull-right">Add new unit <i class="fa fa-cubes"></i></a>
                                            </h3>
                                            <hr/>
                                        </div>
                                    </div>
                                    <div class="str-content">

                                    </div>
                                    <?php
                                } else {
                                    ?>
                                    <div class="alert alert-danger" style="margin-top: 20px;">
                                        <strong>You do not have permission to view/edit storage contract information.</strong>
                                    </div>
                                    <?php
                                }
                                ?>

                            </div>
                            <?php
                        }
                        if($employee == true && $_SESSION['group'] == 1){
                            if($_SESSION['group'] == 1 || $_SESSION['uuid'] == 'DJ5RELUMTA7QPHWJK'){
                                $perms = $profile['user_esc_permissions'];
                                ?>
                                <div class="tab-pane" id="administration">
                                    <div class="portlet">
                                        <div class="portlet-title tabbable-line">
                                            <div class="caption">
                                                <i class="fa fa-file-o"></i>Permissions <small><span class="font-red">|</span> Edit this employees permissions on the system.</small>
                                            </div>
                                        </div>
                                        <div class="portlet-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h4><strong>Access Options</strong> for locations</h4>
                                                    <hr/>
                                                    <div class="row static-info" style="margin-top: 20px;">
                                                        <div class="col-md-12 name">
                                                            <a class="perm_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_permissions" data-value="<?php echo $profile['user_permissions']; ?>" data-pk="<?php echo $profile['user_token']; ?>" data-type="checklist" data-source="[<?php echo $selectData; ?>]" data-placement="right" data-title="Select locations to give access too.." data-url="assets/app/update_settings.php?update=usr_prf">
                                                                <?php
                                                                $perms2 = explode(',', $profile['user_permissions']);
                                                                foreach($perms2 as $p){
                                                                    echo locationName2($p)." (US)<br/>";
                                                                }
                                                                ?>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <h4><strong>Navigation Options</strong> for the navbar</h4>
                                                    <hr/>
                                                    <div class="row form-group" style="margin-top: 20px;">
                                                        <label class="col-md-4 control-label">Dashboard</label>
                                                        <div class="col-md-8">
                                                            <div class="input-group">
                                                                <div class="icheck-inline">
                                                                    <label>
                                                                        <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_dashboard") !== false){echo "checked";} ?> data-perm="view_dashboard"> Can view </label>
                                                                    <label id="edit_view_dashboard">
                                                                        <i class="icon-home"></i>
                                                                        <a class="perms" data-toggle="modal" href="#perms" data-type="dashboard" data-title="Dashboard">
                                                                            Edit page permissions
                                                                        </a>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <label class="col-md-4 control-label">Event</label>
                                                        <div class="col-md-8">
                                                            <div class="input-group">
                                                                <div class="icheck-inline">
                                                                    <label>
                                                                        <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_event") !== false){echo "checked";} ?> data-perm="view_event"> Can view </label>
                                                                    <label id="edit_view_event">
                                                                        <i class="icon-home"></i>
                                                                        <a class="perms" data-toggle="modal" href="#perms" data-type="event" data-title="Event">
                                                                            Edit page permissions
                                                                        </a>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row form-group" style="margin-top: 0px;">
                                                        <label class="col-md-4 control-label">Time Clock</label>
                                                        <div class="col-md-8">
                                                            <div class="input-group">
                                                                <div class="icheck-inline">
                                                                    <label>
                                                                        <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_timeclock") !== false){echo "checked";} ?> data-perm="view_timeclock"> Can view </label>
                                                                    <label id="edit_view_timeclock">
                                                                        <i class="icon-clock"></i>
                                                                        <a class="perms" data-toggle="modal" href="#perms" data-type="timeclock" data-title="Time Clock">
                                                                            Edit page permissions
                                                                        </a>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <label class="col-md-4 control-label">Customers</label>
                                                        <div class="col-md-8">
                                                            <div class="input-group">
                                                                <div class="icheck-inline">
                                                                    <label>
                                                                        <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_customers") !== false){echo "checked";} ?> data-perm="view_customers"> Can view </label>
                                                                    <label id="edit_view_customers">
                                                                        <i class="icon-users"></i>
                                                                        <a class="perms" data-toggle="modal" href="#perms" data-type="customers" data-title="Customers">
                                                                            Edit page permissions
                                                                        </a>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <label class="col-md-4 control-label">Support Tickets</label>
                                                        <div class="col-md-8">
                                                            <div class="input-group">
                                                                <div class="icheck-inline">
                                                                    <label>
                                                                        <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_tickets") !== false){echo "checked";} ?> data-perm="view_tickets"> Can view </label>
                                                                    <label id="edit_view_tickets">
                                                                        <i class="icon-users"></i>
                                                                        <a class="perms" data-toggle="modal" href="#perms" data-type="tickets" data-title="Support Tickets">
                                                                            Edit page permissions
                                                                        </a>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <label class="col-md-4 control-label">Marketing</label>
                                                        <div class="col-md-8">
                                                            <div class="input-group">
                                                                <div class="icheck-inline">
                                                                    <label >
                                                                        <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_marketing") !== false){echo "checked";} ?> data-perm="view_marketing"> Can view </label>
                                                                    <label id="edit_view_marketing">
                                                                        <i class="icon-graph"></i>
                                                                        <a class="perms" data-toggle="modal" href="#perms" data-type="marketing" data-title="Marketing">
                                                                            Edit page permissions
                                                                        </a>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <label class="col-md-4 control-label">Employees</label>
                                                        <div class="col-md-8">
                                                            <div class="input-group">
                                                                <div class="icheck-inline">
                                                                    <label>
                                                                        <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_employees") !== false){echo "checked";} ?> data-perm="view_employees"> Can view </label>
                                                                    <label id="edit_view_employees">
                                                                        <i class="icon-earphones-alt"></i>
                                                                        <a class="perms" data-toggle="modal" href="#perms" data-type="employees" data-title="Employees">
                                                                            Edit page permissions
                                                                        </a>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <label class="col-md-4 control-label">Reports</label>
                                                        <div class="col-md-8">
                                                            <div class="input-group">
                                                                <div class="icheck-inline">
                                                                    <label>
                                                                        <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_reports") !== false){echo "checked";} ?> data-perm="view_reports"> Can view </label>
                                                                    <label id="edit_view_reports">
                                                                        <i class="fa fa-external-link"></i>
                                                                        <a class="perms" data-toggle="modal" href="#perms" data-type="reports" data-title="Reports">
                                                                            Edit page permissions
                                                                        </a>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <label class="col-md-4 control-label">Assets</label>
                                                        <div class="col-md-8">
                                                            <div class="input-group">
                                                                <div class="icheck-inline">
                                                                    <label>
                                                                        <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_assets") !== false){echo "checked";} ?> data-perm="view_assets"> Can view </label>
                                                                    <label id="edit_view_assets">
                                                                        <i class="fa fa-external-link"></i>
                                                                        <a class="perms" data-toggle="modal" href="#perms" data-type="assets" data-title="Assets">
                                                                            Edit page permissions
                                                                        </a>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <label class="col-md-4 control-label">Vendors</label>
                                                        <div class="col-md-8">
                                                            <div class="input-group">
                                                                <div class="icheck-inline">
                                                                    <label>
                                                                        <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_vendors") !== false){echo "checked";} ?> data-perm="view_vendors"> Can view </label>
                                                                    <label id="edit_view_vendors">
                                                                        <i class="fa fa-external-link"></i>
                                                                        <a class="perms" data-toggle="modal" href="#perms" data-type="vendors" data-title="Vendors">
                                                                            Edit page permissions
                                                                        </a>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <label class="col-md-4 control-label">Storage</label>
                                                        <div class="col-md-8">
                                                            <div class="input-group">
                                                                <div class="icheck-inline">
                                                                    <label>
                                                                        <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_storage") !== false){echo "checked";} ?> data-perm="view_storage"> Can view </label>
                                                                    <label id="edit_view_storage">
                                                                        <i class="fa fa-external-link"></i>
                                                                        <a class="perms" data-toggle="modal" href="#perms" data-type="storage" data-title="Storage">
                                                                            Edit page permissions
                                                                        </a>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <label class="col-md-4 control-label">Resource Library</label>
                                                        <div class="col-md-8">
                                                            <div class="input-group">
                                                                <div class="icheck-inline">
                                                                    <label>
                                                                        <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_library") !== false){echo "checked";} ?> data-perm="view_library"> Can view </label>
                                                                    <label id="edit_view_library">
                                                                        <i class="fa fa-external-link"></i>
                                                                        <a class="perms" data-toggle="modal" href="#perms" data-type="library" data-title="Resource Library">
                                                                            Edit page permissions
                                                                        </a>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row form-group" style="margin-top: 0; margin-bottom: 0;">
                                                        <label class="col-md-4 control-label">Location Settings</label>
                                                        <div class="col-md-8">
                                                            <div class="input-group">
                                                                <div class="icheck-inline">
                                                                    <label>
                                                                        <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_locationsettings") !== false){echo "checked";} ?> data-perm="view_locationsettings"> Can view </label>
                                                                    <label id="edit_view_locationsettings">
                                                                        <i class="fa fa-external-link"></i>
                                                                        <a class="perms" data-toggle="modal" href="#perms" data-type="locationsettings" data-title="Location Settings">
                                                                            Edit page permissions
                                                                        </a>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" style="margin-top: 50px;">
                                                <div class="col-md-12">
                                                    <a href="javascript:;" class="btn text-center red btn-sm edit" data-edit="perm_<?php echo $profile['user_token']; ?>" data-reload=""> <i class="fa fa-pencil"></i> <span class="hidden-sm hidden-md " >Edit</span></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="portlet">
                                        <div class="portlet-title tabbable-line">
                                            <div class="caption">
                                                <i class="fa fa-file-o"></i>Time Clock <small><span class="font-red">|</span> Edit this employees time clock records.</small>
                                            </div>
                                        </div>
                                        <div class="portlet-body">
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
                                                            Clock IP Address
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
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    if($employee == true || $_SESSION['group'] == 1){
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
        <?php
        if($_SESSION['group'] == 1 || $_SESSION['uuid'] == 'DJ5RELUMTA7QPHWJK'){
            ?>
            <div class="modal fade bs-modal-lg" id="perms" tabindex="-1" role="basic" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content box red">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                            <h3 class="modal-title font-bold"><strong id="perms_title"></strong> permissions for <?php echo name($profile['user_token']); ?></h3>
                        </div>
                        <div class="modal-body">
                            <div class="edit_perms" id="edit_perms_dashboard">
                                <h4><strong class="text-success">Available</strong> permissions</h4>
                                <hr/>
                                <div class="row form-group" style="margin-top: 20px;">
                                    <label class="col-md-4 control-label">Dashboard - <strong>Manager Tools</strong></label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_dashboard_manager_tools") !== false){echo "checked";} ?> data-perm="view_dashboard_manager_tools"> Can view
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group" style="margin-top: 20px;">
                                    <label class="col-md-4 control-label">Dashboard - <strong>Customer Reviews</strong></label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_dashboard_customer_reviews") !== false){echo "checked";} ?> data-perm="view_dashboard_customer_reviews"> Can view
                                                </label>
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_dashboard_customer_reviews_judge") !== false){echo "checked";} ?> data-perm="view_dashboard_customer_reviews_judge"> Can approve/deny
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="edit_perms" id="edit_perms_event">
                                <h4><strong class="text-success">Available</strong> permissions</h4>
                                <hr/>
                                <div class="row form-group" style="margin-top: 20px;">
                                    <label class="col-md-4 control-label">Event - <strong>Rate Adjustment</strong></label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_event_rate_adj") !== false){echo "checked";} ?> data-perm="view_event_rate_adj"> Can adjust rates
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="edit_perms" id="edit_perms_timeclock">
                                <h4><strong class="text-success">Available</strong> permissions</h4>
                                <hr/>
                                <div class="row form-group" style="margin-top: 20px;">
                                    <label class="col-md-4 control-label">Time Clock - <strong>Clock In/Out</strong></label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_timeclock_clock") !== false){echo "checked";} ?> data-perm="view_timeclock_clock"> Can view
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group" style="margin-top: 20px;">
                                    <label class="col-md-4 control-label">Time Clock - <strong>Current Staff Clocked In</strong></label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_timeclock_current") !== false){echo "checked";} ?> data-perm="view_timeclock_current"> Can view
                                                </label>
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_timeclock_current_clockout") !== false){echo "checked";} ?> data-perm="view_timeclock_current_clockout"> Can clock staff out
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="edit_perms" id="edit_perms_customers">
                                <h4><strong class="text-success">Available</strong> permissions</h4>
                                <hr/>
                                <div class="row form-group" style="margin-top: 20px;">
                                    <label class="col-md-4 control-label">Customers - <strong>Create New</strong></label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_customers_create") !== false){echo "checked";} ?> data-perm="view_customers_create"> Can create
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group" style="margin-top: 20px;">
                                    <label class="col-md-4 control-label">Customers - <strong>Customer Search</strong></label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_customers_search") !== false){echo "checked";} ?> data-perm="view_customers_search"> Can search
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group" style="margin-top: 20px;">
                                    <label class="col-md-4 control-label">Customers - <strong>Customer Profile</strong></label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_customers_search_view_profile") !== false){echo "checked";} ?> data-perm="view_customers_search_view_profile"> Can view
                                                </label>
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_customers_search_edit_profile") !== false){echo "checked";} ?> data-perm="view_customers_search_edit_profile"> Can edit
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="edit_perms" id="edit_perms_tickets">
                                <h4><strong class="text-success">Available</strong> permissions</h4>
                                <hr/>
                                <div class="row form-group" style="margin-top: 20px;">
                                    <label class="col-md-4 control-label">Support Tickets - <strong>Reply/Manage</strong></label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_tickets_manage") !== false){echo "checked";} ?> data-perm="view_tickets_manage"> Can reply/manage
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="edit_perms" id="edit_perms_marketing">
                                <h4><strong class="text-success">Available</strong> permissions</h4>
                                <hr/>
                                <div class="row form-group" style="margin-top: 20px;">
                                    <label class="col-md-4 control-label">Marketing - <strong>Create New</strong></label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_marketing_create") !== false){echo "checked";} ?> data-perm="view_marketing_create"> Can create
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="edit_perms" id="edit_perms_employees">
                                <h4><strong class="text-success">Available</strong> permissions</h4>
                                <hr/>
                                <div class="row form-group" style="margin-top: 20px;">
                                    <label class="col-md-4 control-label">Employees - <strong>Create New</strong></label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_employees_create") !== false){echo "checked";} ?> data-perm="view_employees_create"> Can create
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group" style="margin-top: 20px;">
                                    <label class="col-md-4 control-label">Employees - <strong>Employee Profile</strong></label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_employees_view_profile") !== false){echo "checked";} ?> data-perm="view_employees_view_profile"> Can view
                                                </label>
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_employees_edit_profile") !== false){echo "checked";} ?> data-perm="view_employees_edit_profile"> Can edit
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group" style="margin-top: 20px;">
                                    <label class="col-md-4 control-label">Employees - <strong>Employee Information</strong></label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_employees_view_information") !== false){echo "checked";} ?> data-perm="view_employees_view_information"> Can view
                                                </label>
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_employees_edit_information") !== false){echo "checked";} ?> data-perm="view_employees_edit_information"> Can edit
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="edit_perms" id="edit_perms_reports">
                                <h4><strong class="text-success">Available</strong> permissions</h4>
                                <hr/>
                                <div class="row form-group" style="margin-top: 20px;">
                                    <label class="col-md-4 control-label">Reports - <strong>Sales Report</strong></label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_reports_sales") !== false){echo "checked";} ?> data-perm="view_reports_sales"> Can view
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-md-4 control-label">Reports - <strong>Sales</strong> - Sales Summary</label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_reports_sales_summary") !== false){echo "checked";} ?> data-perm="view_reports_sales_summary"> Can view
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-md-4 control-label">Reports - <strong>Sales</strong> - Expenses & Deposits</label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_reports_sales_expdpt") !== false){echo "checked";} ?> data-perm="view_reports_sales_expdpt"> Can view
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-md-4 control-label">Reports - <strong>Sales</strong> - Completed Jobs Report</label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_reports_sales_cjr") !== false){echo "checked";} ?> data-perm="view_reports_sales_cjr"> Can view
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-md-4 control-label">Reports - <strong>Sales</strong> - Service Items</label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_reports_sales_serviceitems") !== false){echo "checked";} ?> data-perm="view_reports_sales_serviceitems"> Can view
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-md-4 control-label">Reports - <strong>Sales</strong> - Redemptions</label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_reports_sales_redemption") !== false){echo "checked";} ?> data-perm="view_reports_sales_redemption"> Can view
                                                </label>
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_reports_sales_redemption_redeem") !== false){echo "checked";} ?> data-perm="view_reports_sales_redemption_redeem"> Can redeem
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-md-4 control-label">Reports - <strong>Sales</strong> - Accounts Recieveable</label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_reports_sales_accr") !== false){echo "checked";} ?> data-perm="view_reports_sales_accr"> Can view
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr/>
                                <div class="row form-group" style="margin-top: 20px;">
                                    <label class="col-md-6 control-label">Marketing - <strong>Marketing Report</strong></label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_reports_marketing") !== false){echo "checked";} ?> data-perm="view_reports_marketing"> Can view
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr/>
                                <div class="row form-group" style="margin-top: 20px;">
                                    <label class="col-md-6 control-label">Reports - <strong>Payroll Report</strong></label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_reports_payroll") !== false){echo "checked";} ?> data-perm="view_reports_payroll"> Can view
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-md-6 control-label">Reports - <strong>Payroll Report</strong> - Company Summary</label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_reports_payroll_company_summary") !== false){echo "checked";} ?> data-perm="view_reports_payroll_company_summary"> Can view
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-md-6 control-label">Reports - <strong>Payroll Report</strong> - Location Summary</label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_reports_payroll_location_summary") !== false){echo "checked";} ?> data-perm="view_reports_payroll_location_summary"> Can view
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr/>
                                <div class="row form-group" style="margin-top: 20px;">
                                    <label class="col-md-6 control-label">Reports - <strong>Storage Report</strong></label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_reports_storage") !== false){echo "checked";} ?> data-perm="view_reports_storage"> Can view
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-md-6 control-label">Reports - <strong>Storage Report</strong> - Closing Summary</label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_reports_storage_closings") !== false){echo "checked";} ?> data-perm="view_reports_storage_closings"> Can view
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-md-6 control-label">Reports - <strong>Storage Report</strong> - Sales Summary</label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_reports_storage_sales") !== false){echo "checked";} ?> data-perm="view_reports_storage_sales"> Can view
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-md-6 control-label">Reports - <strong>Storage Report</strong> - Lock Action Summary</label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_reports_storage_lockactions") !== false){echo "checked";} ?> data-perm="view_reports_storage_lockactions"> Can view
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-md-6 control-label">Reports - <strong>Storage Report</strong> - Rent Roll Summary</label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_reports_storage_rentroll") !== false){echo "checked";} ?> data-perm="view_reports_storage_rentroll"> Can view
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr/>
                                <div class="row form-group">
                                    <label class="col-md-6 control-label">Reports - <strong>Other Reports</strong></label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_reports_other") !== false){echo "checked";} ?> data-perm="view_reports_other"> Can view
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-md-6 control-label">Reports - <strong>Other Reports</strong> - Score Card</label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_reports_other_scorecard") !== false){echo "checked";} ?> data-perm="view_reports_other_scorecard"> Can view
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="edit_perms" id="edit_perms_storage">
                                <h4><strong class="text-success">Available</strong> permissions</h4>
                                <hr/>
                                <div class="row form-group" style="margin-top: 20px;">
                                    <label class="col-md-6 control-label">Storage - <strong>Contracts</strong></label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_storage_create_contracts") !== false){echo "checked";} ?> data-perm="view_storage_create_contracts"> Can create
                                                </label>
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_storage_contracts") !== false){echo "checked";} ?> data-perm="view_storage_contracts"> Can view
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group" style="margin-top: 20px;">
                                    <label class="col-md-6 control-label">Storage - <strong>Contract Rate Adjustment (on create)</strong></label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_storage_create_contracts_adj") !== false){echo "checked";} ?> data-perm="view_storage_create_contracts_adj"> Can create
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group" style="margin-top: 20px;">
                                    <label class="col-md-6 control-label">Storage - <strong>Contract Deposit Adjustment (on create)</strong></label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_storage_create_contracts_dpt") !== false){echo "checked";} ?> data-perm="view_storage_create_contracts_dpt"> Can create
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group" style="margin-top: 20px;">
                                    <label class="col-md-6 control-label">Storage - <strong>Write Off</strong></label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label>
                                                    <input type="checkbox" class="icheck" <?php if(strpos($perms, "view_storage_create_writeoff") !== false){echo "checked";} ?> data-perm="view_storage_create_writeoff"> Can create
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
            <?php
        }
        ?>

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
        <div class="modal fade bs-modal-lg" id="labor_only" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content box red">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h3 class="modal-title font-bold">Misc labor for <?php echo $profile['user_fname']; ?></h3>
                    </div>
                    <div class="modal-body">
                        <div class="portlet">
                            <div class="portlet-title" style="border-bottom: none;">
                                <div class="actions">
                                    <a class="btn default red-stripe show_form" data-show="#add_labor">
                                        <i class="fa fa-plus"></i>
                                        <span class="hidden-480">Add new labor record</span>
                                    </a>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="table-container">
                                    <form role="form" id="add_laborer">
                                        <table class="table table-striped table-bordered table-hover datatable" data-src="assets/app/api/profile.php?type=labor&uuid=<?php echo $profile['user_token']; ?>">
                                            <thead>
                                            <tr role="row" class="heading">
                                                <th>
                                                    Timestamp
                                                </th>
                                                <th width="35%">
                                                    Labor Description
                                                </th>
                                                <th>
                                                    Labor Rate
                                                </th>
                                                <th>
                                                    Hours Paid
                                                </th>
                                                <th width="18%">
                                                    Added By
                                                </th>
                                            </tr>
                                            <tr role="row" class="filter" style="display: none;" id="add_labor">
                                                <td><input type="text" class="hidden" readonly name="laborer" value="<?php echo $profile['user_token']; ?>">
                                                    <input class="datepicker" class="form-control input-sm" name="dtd">
                                                </td>
                                                <td><input type="text" class="form-control form-filter input-sm" name="desc"></td>
                                                <td>
                                                    <input type="text" class="form-control input-sm" readonly value="$__.__">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control input-sm" name="hp">
                                                </td>
                                                <td>
                                                    <div class="margin-bottom-5">
                                                        <button type="button" class="btn btn-sm red margin-bottom add_labor"><i class="fa fa-download"></i> Save</button>
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
                            $end                     = date('Y-m-d', strtotime($start." +13 days"));
                            $hours = array();
                            $prev  = mysql_query("
                            SELECT advance_requested FROM fmo_users_employee_advances
                            WHERE (DATE(advance_timestamp)>='".mysql_real_escape_string($start)."' AND DATE(advance_timestamp)<='".mysql_real_escape_string($end)."') AND advance_user_token='".mysql_real_escape_string($profile['user_token'])."'");
                            $hours = mysql_query("
                            SELECT timeclock_user, timeclock_hours FROM fmo_users_employee_timeclock 
                            WHERE (DATE(timeclock_clockout)>='".mysql_real_escape_string($start)."' AND DATE(timeclock_clockout)<='".mysql_real_escape_string($end)."') AND timeclock_user='".mysql_real_escape_string($profile['user_token'])."'") or die(mysql_error());
                            $misc_hours = mysql_query("SELECT laborer_hours_worked FROM fmo_locations_events_laborers WHERE (DATE(laborer_timestamp)>='".mysql_real_escape_string($start)."' AND DATE(laborer_timestamp)<='".mysql_real_escape_string($end)."') AND laborer_user_token='".mysql_real_escape_string($profile['user_token'])."'");
                            $pay = array();
                            if(mysql_num_rows($hours) > 0 || mysql_num_rows($misc_hours) > 0){
                                while($work = mysql_fetch_assoc($hours)){
                                    $pay['hours']+=$work['timeclock_hours'];
                                } while ($misc_work = mysql_fetch_assoc($misc_hours)){
                                    $pay['hours']+=$misc_work['laborer_hours_worked'];
                                }
                                if($pay['hours'] > 0){
                                    $pay['rate']      = $user_pay['user_employer_rate'];
                                    $pay['earned']    = $pay['hours'] * $user_pay['user_employer_rate'];
                                    if(mysql_num_rows($prev) > 0){
                                        while($loans = mysql_fetch_assoc($prev)){
                                            $pay['loans'] += $loans['advance_requested'];
                                        }
                                    } else {$pay['loans'] = 0;}
                                    $pay['available'] = number_format(($pay['earned'] * .25) - $pay['loans'], 2);
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
                                                    Advance Available
                                                </th>
                                                <th>
                                                    Advance Requested
                                                </th>
                                                <th>
                                                    Advance Reasoning
                                                </th>
                                                <th>
                                                    Advance Authorization
                                                </th>
                                            </tr>
                                            <tr role="row" class="filter" style="display: none;" id="add_advance">
                                                <td></td>
                                                <td><input type="number" class="form-control form-filter input-sm" name="available" readonly value="<?php echo number_format($pay['available'], 2); ?>"></td>
                                                <td><input type="number" class="form-control form-filter input-sm" name="requested"></td>
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
                                <strong id="ad_hrs"><?php echo $pay['hours']; ?></strong> hours @ $<strong><?php echo $user_pay['user_employer_rate']; ?></strong>/hour, with $<strong id="ad_earned"><?php echo number_format($pay['earned'], 2); ?></strong> earned, with <span class="text-danger">-$<strong id="ad_loans"><?php echo number_format($pay['loans'], 2); ?></strong></span> from previous loans, making $<strong id="ad_avail"><?php echo number_format($pay['available'], 2); ?></strong> available.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <form method="POST" action="" role="form" id="new_case">
            <div class="modal fade bs-modal-lg" id="child_support_only" tabindex="-1" role="basic" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content box red">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                            <h3 class="modal-title font-bold">Child Support for <?php echo $profile['user_fname']; ?></h3>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <?php
                                    $childsupport = mysql_query("SELECT childsupport_id, childsupport_case_name, childsupport_case_number, childsupport_amount, childsupport_address, childsupport_address2, childsupport_city, childsupport_state, childsupport_zip, childsupport_pay_allowed, childsupport_pay_period, childsupport_comments, childsupport_by_user_token FROM fmo_users_employee_childsupports WHERE childsupport_user_token='".mysql_real_escape_string($profile['user_token'])."'");
                                    if(mysql_num_rows($childsupport) > 0){
                                        $pk = 0;
                                        while($cs = mysql_fetch_assoc($childsupport)){
                                            $pk++
                                            ?>
                                            <div id="childsupport_h_<?php echo $pk; ?>" class="panel-group">
                                                <div class="panel panel-default">
                                                    <div class="panel-heading">
                                                        <div class="actions pull-right" style="margin-top: -6px; margin-right: -9px">
                                                            <a href="javascript:;" class="btn btn-default btn-sm edit" data-edit="cs_<?php echo $cs['childsupport_id']; ?>">
                                                                <i class="fa fa-pencil"></i> <span class="hidden-sm hidden-md hidden-xs">Edit</span> </a>
                                                        </div>
                                                        <div class="caption">
                                                            <h4 class="panel-title">
                                                                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#childsupport_h_<?php echo $pk; ?>" href="#childsupport_<?php echo $pk; ?>" aria-expanded="false"><strong><?php echo $cs['childsupport_case_name']; ?></strong></a>
                                                            </h4>
                                                        </div>
                                                    </div>
                                                    <div id="childsupport_<?php echo $pk; ?>" class="panel-collapse collapse" aria-expanded="true" style="height: 0px;">
                                                        <div class="panel-body">
                                                            <address>
                                                                Case Name:
                                                                <strong>
                                                                    <a class="cs_<?php echo $cs['childsupport_id']; ?>" style="color:#333333" data-name="childsupport_case_name" data-pk="<?php echo $cs['childsupport_id']; ?>" data-type="text" data-placement="right" data-title="Enter new case name.." data-url="assets/app/update_settings.php?update=usr_cs">
                                                                        <?php echo $cs['childsupport_case_name']; ?>
                                                                    </a><br/>
                                                                </strong>

                                                                Case Number:
                                                                <strong>
                                                                    <a class="cs_<?php echo $cs['childsupport_id']; ?>" style="color:#333333" data-name="childsupport_case_number" data-pk="<?php echo $cs['childsupport_id']; ?>" data-type="text" data-placement="right" data-title="Enter new case number.." data-url="assets/app/update_settings.php?update=usr_cs">
                                                                        <?php echo $cs['childsupport_case_number']; ?>
                                                                    </a><br/>
                                                                </strong>

                                                                Garnishment Amount:
                                                                <strong>
                                                                    <a class="cs_<?php echo $cs['childsupport_id']; ?>" style="color:#333333" data-name="childsupport_amount" data-pk="<?php echo $cs['childsupport_id']; ?>" data-type="number" data-placement="right" data-title="Enter new amount.." data-url="assets/app/update_settings.php?update=usr_cs">
                                                                        <?php echo $cs['childsupport_amount']; ?>
                                                                    </a><br/>
                                                                </strong>

                                                                Address:
                                                                <strong>
                                                                    <a class="cs_<?php echo $cs['childsupport_id']; ?>" style="color:#333333" data-name="childsupport_address" data-pk="<?php echo $cs['childsupport_id']; ?>" data-type="text" data-placement="right" data-title="Enter new address line.." data-url="assets/app/update_settings.php?update=usr_cs">
                                                                        <?php echo $cs['childsupport_address']; ?>
                                                                    </a>,
                                                                    <a class="cs_<?php echo $cs['childsupport_id']; ?>" style="color:#333333" data-name="childsupport_city" data-pk="<?php echo $cs['childsupport_id']; ?>" data-type="text" data-placement="right" data-title="Enter new city.." data-url="assets/app/update_settings.php?update=usr_cs">
                                                                        <?php echo $cs['childsupport_city']; ?>
                                                                    </a>,
                                                                    <a class="cs_<?php echo $cs['childsupport_id']; ?>" style="color:#333333" data-name="childsupport_state" data-pk="<?php echo $cs['childsupport_id']; ?>" data-type="select" data-source="[{value: 'AL', text: 'Alabama'},{value: 'AK', text: 'Alaska'},{value: 'AZ', text: 'Arizona'},{value: 'AR', text: 'Arkansas'},{value: 'CA', text: 'California'},{value: 'CO', text: 'Colorado'},{value: 'CT', text: 'Connecticut'},{value: 'DE', text: 'Delaware'},{value: 'DC', text: 'District Of Columbia'},{value: 'FL', text: 'Florida'},{value: 'GA', text: 'Georgia'},{value: 'HI', text: 'Hawaii'},{value: 'ID', text: 'Idaho'},{value: 'IL', text: 'Illinois'},{value: 'IN', text: 'Indiana'},{value: 'IA', text: 'Iowa'},{value: 'KS', text: 'Kansas'},{value: 'KY', text: 'Kentucky'},{value: 'LA', text: 'Louisiana'},{value: 'ME', text: 'Maine'},{value: 'MD', text: 'Maryland'},{value: 'MA', text: 'Massachusetts'},{value: 'MI', text: 'Michigan'},{value: 'MN', text: 'Minnesota'},{value: 'MS', text: 'Mississippi'},{value: 'MO', text: 'Missouri'},{value: 'MT', text: 'Montana'},{value: 'NE', text: 'Nebraska'},{value: 'NV', text: 'Nevada'},{value: 'NH', text: 'New Hampshire'},{value: 'NJ', text: 'New Jersey'},{value: 'NM', text: 'New Mexico'},{value: 'NY', text: 'New York'},{value: 'NC', text: 'North Carolina'},{value: 'ND', text: 'North Dakota'},{value: 'OH', text: 'Ohio'},{value: 'OK', text: 'Oklahoma'},{value: 'OR', text: 'Oregon'},{value: 'PW', text: 'Palau'},{value: 'PA', text: 'Pennsylvania'},{value: 'RI', text: 'Rhode Island'},{value: 'SC', text: 'South Carolina'},{value: 'SD', text: 'South Dakota'},{value: 'TN', text: 'Tennessee'},{value: 'TX', text: 'Texas'},{value: 'UT', text: 'Utah'},{value: 'VT', text: 'Vermont'},{value: 'VA', text: 'Virginia'},{value: 'WA', text: 'Washington'},{value: 'WV', text: 'West Virginia'},{value: 'WI', text: 'Wisconsin'},{value: 'WY', text: 'Wyoming'}]" data-inputclass="form-control" data-placement="right" data-title="Select new state.." data-url="assets/app/update_settings.php?update=usr_cs">
                                                                        <?php echo $cs['childsupport_state']; ?>
                                                                    </a>,
                                                                    <a class="cs_<?php echo $cs['childsupport_id']; ?>" style="color:#333333" data-name="childsupport_zip" data-pk="<?php echo $cs['childsupport_id']; ?>" data-type="number" data-placement="right" data-title="Enter new zip code.." data-url="assets/app/update_settings.php?update=usr_cs">
                                                                        <?php echo $cs['childsupport_zip']; ?>
                                                                    </a><br/>
                                                                </strong>

                                                                Pay Allowed:
                                                                <strong>
                                                                    <a class="cs_<?php echo $cs['childsupport_id']; ?>" style="color:#333333" data-name="childsupport_pay_allowed" data-pk="<?php echo $cs['childsupport_id']; ?>" data-type="number" data-placement="right" data-title="Enter new pay allowed.." data-url="assets/app/update_settings.php?update=usr_cs">
                                                                        <?php echo $cs['childsupport_pay_allowed']; ?>
                                                                    </a><br/>
                                                                </strong>

                                                                Pay Period:
                                                                <strong>
                                                                    <a class="cs_<?php echo $cs['childsupport_id']; ?>" style="color:#333333" data-name="childsupport_pay_period" data-pk="<?php echo $cs['childsupport_id']; ?>" data-type="date" data-format="mm/dd/yyy" data-placement="right" data-title="Select new pay period.." data-url="assets/app/update_settings.php?update=usr_cs">
                                                                        <?php echo $cs['childsupport_pay_period']; ?>
                                                                    </a><br/>
                                                                </strong>

                                                                Comments:
                                                                <strong>
                                                                    <a class="cs_<?php echo $cs['childsupport_id']; ?>" style="color:#333333" data-name="childsupport_comments" data-pk="<?php echo $cs['childsupport_id']; ?>" data-type="text" data-placement="right" data-title="Enter new comments.." data-url="assets/app/update_settings.php?update=usr_cs">
                                                                        <?php echo $cs['childsupport_comments']; ?>
                                                                    </a>
                                                                </strong>
                                                            </address>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <div class="alert alert-warning alert-dismissable">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                                            <strong>No child support cases!</strong> Add new cases below to see them appear here.
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <hr/>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Case Name</label>
                                        <input type="text" class="form-control" name="case_name" placeholder="Child # x or Parent Name">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Case Number</label>
                                        <input type="text" class="form-control" name="case_number" placeholder="93432-19-123">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Garnishment Amount</label>
                                        <input type="text" class="form-control" name="amount" placeholder="100">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Street Address</label>
                                        <input type="text" class="form-control" name="address" placeholder="123 Example Rd">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Street Address 2 (Optional)</label>
                                        <input type="text" class="form-control" name="address2" placeholder="Complex Name / Second Address">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>City</label>
                                        <input type="text" class="form-control" name="city" placeholder="Sincity">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>State</label>
                                        <select name="state" class="form-control">
                                            <option value="" selected disabled>Select one..</option>
                                            <option value="AL">Alabama</option>
                                            <option value="AK">Alaska</option>
                                            <option value="AZ">Arizona</option>
                                            <option value="AR">Arkansas</option>
                                            <option value="CA">California</option>
                                            <option value="CO">Colorado</option>
                                            <option value="CT">Connecticut</option>
                                            <option value="DE">Delaware</option>
                                            <option value="DC">District Of Columbia</option>
                                            <option value="FL">Florida</option>
                                            <option value="GA">Georgia</option>
                                            <option value="HI">Hawaii</option>
                                            <option value="ID">Idaho</option>
                                            <option value="IL">Illinois</option>
                                            <option value="IN">Indiana</option>
                                            <option value="IA">Iowa</option>
                                            <option value="KS">Kansas</option>
                                            <option value="KY">Kentucky</option>
                                            <option value="LA">Louisiana</option>
                                            <option value="ME">Maine</option>
                                            <option value="MD">Maryland</option>
                                            <option value="MA">Massachusetts</option>
                                            <option value="MI">Michigan</option>
                                            <option value="MN">Minnesota</option>
                                            <option value="MS">Mississippi</option>
                                            <option value="MO">Missouri</option>
                                            <option value="MT">Montana</option>
                                            <option value="NE">Nebraska</option>
                                            <option value="NV">Nevada</option>
                                            <option value="NH">New Hampshire</option>
                                            <option value="NJ">New Jersey</option>
                                            <option value="NM">New Mexico</option>
                                            <option value="NY">New York</option>
                                            <option value="NC">North Carolina</option>
                                            <option value="ND">North Dakota</option>
                                            <option value="OH">Ohio</option>
                                            <option value="OK">Oklahoma</option>
                                            <option value="OR">Oregon</option>
                                            <option value="PA">Pennsylvania</option>
                                            <option value="RI">Rhode Island</option>
                                            <option value="SC">South Carolina</option>
                                            <option value="SD">South Dakota</option>
                                            <option value="TN">Tennessee</option>
                                            <option value="TX">Texas</option>
                                            <option value="UT">Utah</option>
                                            <option value="VT">Vermont</option>
                                            <option value="VA">Virginia</option>
                                            <option value="WA">Washington</option>
                                            <option value="WV">West Virginia</option>
                                            <option value="WI">Wisconsin</option>
                                            <option value="WY">Wyoming</option>
                                        </select></div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Zip Code</label>
                                        <input type="text" class="form-control" name="zip" placeholder="46219">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Pay Allowed</label>
                                        <input type="text" class="form-control" name="pay_allowed" placeholder="">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Pay Period</label>
                                        <input type="text" class="form-control" name="pay_period" placeholder="">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Comments</label>
                                        <input type="text" class="form-control" name="comments" placeholder="nice feller, has beautiful kids..too many probably">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn red">Save child support case</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php
    }
    ?>
    <script>
        $(document).ready(function() {


            $('#storage_tab').on('click',function(){
                $('.str-content').html("<h3 class='text-center'><i class='fa fa-spin fa-spinner'></i> Loading... </h3>");
                $.ajax({
                    url: 'assets/pages/sub/sub/storage_py.php?e=mgr&luid=<?php echo $_GET['luid']; ?>',
                    type: 'POST',
                    data: {
                        uuid: '<?php echo $profile['user_token']; ?>'
                    }, success: function(data){
                        $('.str-content').html(data)
                    }, error: function(){
                        toastr.error("<strong>Logan says:</strong><br/>Hmm..something didn't work right. Refresh your browser.")
                    }
                });
            });

            <?php if(isset($_GET['s'])){ ?> $('#storage_tab').click(); <?php } ?>

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
            $('.new_unit').on('click', function() {
                $('.new_unit').hide();
                $('.')
            });
            $('.datepicker').datepicker();
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
                            url: 'assets/app/update_settings.php?update=user_perms',
                            type: 'POST',
                            data: {
                                value: checked,
                                perm: perm,
                                uuid: "<?php echo $profile['user_token']; ?>"
                            },
                            success:function() {
                                toastr.info("<strong>Logan says:</strong><Br/>I have added that to <?php echo $profile['user_fname']; ?>'s permissions");
                            },
                            error:function(){

                            }
                        });
                        $('#edit_'+perm).show();
                    } else {
                        $.ajax({
                            url: 'assets/app/update_settings.php?update=user_perms',
                            type: 'POST',
                            data: {
                                value: checked,
                                perm: perm,
                                uuid: "<?php echo $profile['user_token']; ?>"
                            },
                            success:function() {
                                toastr.error("<strong>Logan says:</strong><Br/>I have removed that from <?php echo $profile['user_fname']; ?>'s permissions");
                            },
                            error:function(){

                            }
                        });
                        $('#edit_'+perm).hide();
                    }
                });
            });


            $('.show_form').on('click', function() {

                var show = $(this).attr('data-show');

                $(show).show();
            });
            $('.upr').on('click', function() {
                $.ajax({
                    url: 'assets/app/texting.php?txt=upr',
                    type: 'POST',
                    data: {
                        p: <?php echo $profile['user_phone']; ?>,
                        uuid: "<?php echo $profile['user_token']; ?>",
                        b: "<?php echo $_SESSION['uuid']; ?>"
                    },
                    success: function() {
                        toastr.success("<strong>Logan says:</strong><br/>Password reset was sent to <?php echo clean_phone($profile['user_phone']); ?>, they should receive it momentarily.");
                    },
                    error: function() {
                        toastr.error("<strong>Logan says:</strong><br/>Oops..that didnt work properly. Try again?");
                    }
                })
            });
            $('#add_laborer').validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",
                rules: {
                    dtd: {
                        required: true
                    },
                    desc: {
                        required: true
                    },
                    hp: {
                        required: true
                    }
                }
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
            $("#new_case").validate({
                errorElement: 'span', //default input error message container
                errorClass: 'font-red', // default input error message class
                rules: {
                    case_name: {
                        required: true
                    },
                    case_number: {
                        required: true
                    },
                    amount: {
                        required: true
                    },
                    address: {
                        required: true
                    },
                    city: {
                        required: true
                    },
                    state: {
                        required: true
                    },
                    zip: {
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
                        url: 'assets/app/add_setting.php?setting=childsupport&uuid=<?php echo $profile['user_token']; ?>',
                        type: "POST",
                        data: $('#new_case').serialize(),
                        success: function(data) {
                            $('#childsupport').modal('hide');
                            $('#new_case')[0].reset();
                            toastr.success("<strong>Logan says</strong>:<br/>That case has been added to this users records. I had to refresh the page for you, so you can see the new record.");
                            $.ajax({
                                url: 'assets/pages/profile.php?uuid=<?php echo $profile['user_token']; ?>&luid=<?php echo $_GET['luid']; ?>',
                                success: function(data) {
                                    $('#page_content').html(data);
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
                }
            });
            $('.add_labor').on('click', function(){
                if($("#add_laborer").valid()){
                    $.ajax({
                        url: "assets/app/add_setting.php?setting=laborer&ev=<?php echo $profile['user_token']; ?>",
                        type: "POST",
                        data: $('#add_laborer').serialize(),
                        success: function(data) {
                            toastr.info('<strong>Logan says</strong>:<br/>'+data+' has been added to this users labor record.');
                            $('.datatable').getDataTable().ajax.reload();
                        },
                        error: function() {
                            toastr.error('<strong>Logan says</strong>:<br/>That page didnt respond correctly. Try again, or create a support ticket for help.');
                        }
                    });
                }
            });
            $('.add_document').on('click', function(){
                $(this).html('<i class="fa fa-spinner fa-spin"></i>');
                $(this).attr('disabled', true);
                if($("#add_documents").valid()){
                    $.ajax({
                        url: "assets/app/add_setting.php?setting=document&uuid=<?php echo $profile['user_token']; ?>",
                        type: "POST",
                        data: new FormData($('#add_documents')[0]),
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            $('#add_documents')[0].reset();
                            toastr.info('<strong>Logan says</strong>:<br/>Document has been added to users documents table.');
                            $('#p_docs').DataTable().ajax.reload();
                            $('.add_document').html('<i class="fa fa-download"></i> Save');
                            $('.add_document').attr('disabled', false);
                        },
                        error: function() {
                            toastr.error('<strong>Logan says</strong>:<br/>That page didnt respond correctly. Try again, or create a support ticket for help.');
                        }
                    });
                } else {
                    $(this).html('<i class="fa fa-download"></i> Save');
                    $(this).attr('disabled', false);
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
                            window.open('assets/public/loan_auth.php?t=auth_tok&i='+inf.id,'_blank');
                            toastr.info('<strong>Logan says</strong>:<br/>Advance has been added to users advance history.');
                        },
                        error: function() {
                            toastr.error('<strong>Logan says</strong>:<br/>That page didnt respond correctly. Try again, or create a support ticket for help.');
                        }
                    });
                }
            });


            $('.perms').on('click', function(e) {
                var type  = $(this).attr("data-type");
                var title = $(this).attr("data-title");

                $('#perms_title').html(title);
                $('.edit_perms').hide();
                $('#edit_perms_'+type).show();
            });



        });
    </script>
    <?php
} else {
    header("Location: ../../../index.php?err=no_access");
}
?>
