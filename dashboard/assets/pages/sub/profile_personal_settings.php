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
    $profile = mysql_fetch_array(mysql_query("SELECT user_id, user_company_name, user_company_token, user_pic, user_fname, user_lname, user_phone, user_email, user_website, user_token, user_group, user_employer, user_employer_location, user_employer_rate, user_employer_salary, user_employer_hired, user_employer_dln, user_employer_dle, user_employer_dls, user_employer_dot_exp, user_address, user_state, user_zip, user_city FROM fmo_users WHERE user_token='".mysql_real_escape_string($_GET['uuid'])."'"));
    if(!empty($profile['user_employer']) && !empty($profile['user_employer_location'])) {
        $employee = true;
    } else {$employee = false;}
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
                        if($employee == true){
                            ?>
                            <li>
                                <a href="#documents" data-toggle="tab">Documents</a>
                            </li>
                            <li>
                                <a href="#timeline" data-toggle="tab">Comments / Write-ups</a>
                            </li>
                            <li>
                                <a href="#advances" data-toggle="tab">Advances</a>
                            </li>
                            <li>
                                <a href="#childsupport" data-toggle="tab">Child Support</a>
                            </li>
                            <li>
                                <a href="#location_accs" data-toggle="tab">Location Access</a>
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
                                        <?php echo $profile['user_phone']; ?>
                                    </a>
                                </div>
                            </div>
                            <div class="row static-info">
                                <div class="col-md-5 name">
                                    Address:
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
                                        Role:
                                    </div>
                                    <div class="col-md-7 value">
                                        <a class="pu_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_group" data-pk="<?php echo $profile['user_token']; ?>" data-type="select" data-source="[{value: 1, text: 'Administrator'}, {value: 2, text: 'Manager'}, {value: 4, text: 'Customer Service'}, {value: 5.1, text: 'Driver'}, {value: 5.2, text: 'Helper'}, {value: 5.3, text: 'Crewman/Other'}]" data-placement="right" data-title="Enter new phone number.." data-url="assets/app/update_settings.php?update=usr_prf">
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
                                        Indianapolis (IN)
                                    </div>
                                </div>
                                <div class="row static-info">
                                    <div class="col-md-5 name">
                                        Drivers License Number, Expiration, & State:
                                    </div>
                                    <div class="col-md-7 value">
                                        <a class="pu_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_employer_dln" data-pk="<?php echo $profile['user_token']; ?>" data-type="number" data-inputclass="form-control" data-placement="right" data-title="Enter new drivers license number.." data-url="assets/app/update_settings.php?update=usr_prf">
                                            <?php echo $profile['user_employer_dln']; ?>
                                        </a> -
                                        <a class="pu_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_employer_dle" data-pk="<?php echo $profile['user_token']; ?>" data-type="date" data-format="mm/dd/yyyy" data-inputclass="form-control" data-placement="right" data-title="Select drivers license expiration date.." data-url="assets/app/update_settings.php?update=usr_prf">
                                            <?php echo $profile['user_employer_dle']; ?>
                                        </a> -
                                        <a class="pu_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_employer_dls" data-pk="<?php echo $profile['user_token']; ?>" data-type="select" data-source="[{value: 'AL', text: 'Alabama'},{value: 'AK', text: 'Alaska'},{value: 'AZ', text: 'Arizona'},{value: 'AR', text: 'Arkansas'},{value: 'CA', text: 'California'},{value: 'CO', text: 'Colorado'},{value: 'CT', text: 'Connecticut'},{value: 'DE', text: 'Delaware'},{value: 'DC', text: 'District Of Columbia'},{value: 'FL', text: 'Florida'},{value: 'GA', text: 'Georgia'},{value: 'HI', text: 'Hawaii'},{value: 'ID', text: 'Idaho'},{value: 'IL', text: 'Illinois'},{value: 'IN', text: 'Indiana'},{value: 'IA', text: 'Iowa'},{value: 'KS', text: 'Kansas'},{value: 'KY', text: 'Kentucky'},{value: 'LA', text: 'Louisiana'},{value: 'ME', text: 'Maine'},{value: 'MD', text: 'Maryland'},{value: 'MA', text: 'Massachusetts'},{value: 'MI', text: 'Michigan'},{value: 'MN', text: 'Minnesota'},{value: 'MS', text: 'Mississippi'},{value: 'MO', text: 'Missouri'},{value: 'MT', text: 'Montana'},{value: 'NE', text: 'Nebraska'},{value: 'NV', text: 'Nevada'},{value: 'NH', text: 'New Hampshire'},{value: 'NJ', text: 'New Jersey'},{value: 'NM', text: 'New Mexico'},{value: 'NY', text: 'New York'},{value: 'NC', text: 'North Carolina'},{value: 'ND', text: 'North Dakota'},{value: 'OH', text: 'Ohio'},{value: 'OK', text: 'Oklahoma'},{value: 'OR', text: 'Oregon'},{value: 'PW', text: 'Palau'},{value: 'PA', text: 'Pennsylvania'},{value: 'RI', text: 'Rhode Island'},{value: 'SC', text: 'South Carolina'},{value: 'SD', text: 'South Dakota'},{value: 'TN', text: 'Tennessee'},{value: 'TX', text: 'Texas'},{value: 'UT', text: 'Utah'},{value: 'VT', text: 'Vermont'},{value: 'VA', text: 'Virginia'},{value: 'WA', text: 'Washington'},{value: 'WV', text: 'West Virginia'},{value: 'WI', text: 'Wisconsin'},{value: 'WY', text: 'Wyoming'}]" data-inputclass="form-control" data-placement="right" data-title="Select drivers license state.." data-url="assets/app/update_settings.php?update=usr_prf">
                                            <?php echo $profile['user_employer_dls']; ?>
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
                                        </a>
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
                            <div class="tab-pane" id="timeline">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="portlet">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-comments"></i>Comments
                                                </div>
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
                                                                <th>
                                                                    Comment
                                                                </th>
                                                                <th>
                                                                    By
                                                                </th>
                                                                <th width="8%">
                                                                    Actions
                                                                </th>
                                                            </tr>
                                                            <tr role="row" class="filter" style="display: none;" id="add_comment">
                                                                <td><input type="text" class="form-control form-filter input-sm" name="comment"></td>
                                                                <td><?php echo $_SESSION['fname']." ".$_SESSION['lname']; ?></td>
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
                                        <!-- End: life time stats -->
                                    </div>
                                    <div class="col-md-6">
                                        <div class="portlet">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-times"></i>Write-ups
                                                </div>
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
                                                                <th>
                                                                    Write-up Reasoning
                                                                </th>
                                                                <th>
                                                                    Write-up Action
                                                                </th>
                                                                <th width="8%">
                                                                    Actions
                                                                </th>
                                                            </tr>
                                                            <tr role="row" class="filter" style="display: none;" id="add_writeup">
                                                                <td><input type="text" class="form-control form-filter input-sm" name="item"></td>
                                                                <td></td>
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
                                        <!-- End: life time stats -->
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-clock-o"></i>Employee Timeline<small><span class="font-red">|</span> These are un-editable records of the employee's history</small>
                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                                <div class="table-container">
                                                    <table class="table table-striped table-bordered table-hover datatable" data-src="assets/app/api/profile.php?type=timeline&uuid=<?php echo $profile['user_token']; ?>">
                                                        <thead>
                                                        <tr role="row" class="heading">
                                                            <th>
                                                                Date
                                                            </th>
                                                            <th>
                                                                Record & Details
                                                            </th>
                                                            <th width="8%">
                                                                Location
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
                            <div class="tab-pane" id="advances">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-external-link"></i>Advances <small><span class="font-red">|</span> Advances are tracked & monitored, and are only allowed if employee qualifies for one.</small>
                                                </div>
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
                                                                <th>
                                                                    Advance Amount Available
                                                                </th>
                                                                <th>
                                                                    Advance Amount
                                                                </th>
                                                                <th width="8%">
                                                                    Date
                                                                </th>
                                                            </tr>
                                                            <tr role="row" class="filter" style="display: none;" id="add_advance">
                                                                <td><input type="text" class="form-control form-filter input-sm" name="item"></td>
                                                                <td></td>
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
                                        </div>
                                        <!-- End: life time stats -->
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="childsupport">
                                Child Support
                            </div>
                            <div class="tab-pane" id="location_accs">
                                Location Access
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
} else {
    header("Location: ../../../index.php?err=no_access");
}
?>
