<?php
/**
 * Created by PhpStorm.
 * User: LoganCk
 * Date: 3/3/2017
 * Time: 3:55 AM
 */
ini_set("log_errors", 1);
ini_set("error_log", "/tmp/php-error.log");
session_start();
if(isset($_SESSION['logged'])){
    include '../app/init.php';
    mysql_query("UPDATE fmo_users SET user_last_location='".mysql_real_escape_string(basename(__FILE__, '.php')).".php?".$_SERVER['QUERY_STRING']."' WHERE user_token='".mysql_real_escape_string($_SESSION['uuid'])."'");
    $profile = mysql_fetch_array(mysql_query("SELECT user_status, user_setup, user_group, user_id, user_pin, user_last_login, user_creator_user, user_creation, user_company_name, user_company_token, user_employer_commission, user_pic, user_fname, user_lname, user_phone, user_ems_phone, user_email, user_website, user_token, user_group, user_employer, user_employer_location, user_employer_rate, user_dob, user_employer_salary, user_employer_hired, user_employer_dln, user_employer_dle, user_employer_dls, user_employer_dot_exp, user_address, user_state, user_zip, user_city, user_address2, user_state2, user_city2, user_zip2, user_repeatclient, user_repeatclient_terms, user_repeatclient_notes, user_last_ext_location, user_permissions, user_esc_permissions FROM fmo_users WHERE user_token='".mysql_real_escape_string($_GET['uuid'])."'"));
    if(!empty($profile['user_employer']) && !empty($profile['user_employer_location'])) {
        $employee = true;
        $location = mysql_fetch_array(mysql_query("SELECT location_name, location_state, location_token FROM fmo_locations WHERE location_token='".mysql_real_escape_string($profile['user_employer_location'])."'"));
    } else {
        $employee = false;
        $location = mysql_fetch_array(mysql_query("SELECT location_name, location_state, location_token FROM fmo_locations WHERE location_token='".mysql_real_escape_string($profile['user_last_ext_location'])."'"));
    }
    $uuidperm = mysql_fetch_array(mysql_query("SELECT user_esc_permissions FROM fmo_users WHERE user_token='".mysql_real_escape_string($_SESSION['uuid'])."'"));
    ?>
    <div class="page-content">
        <h3 class="page-title">
            <strong><?php echo $profile['user_fname']." ".$profile['user_lname']; ?></strong>
        </h3>
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <i class="fa fa-home"></i>
                    <a class="load_page" data-href="assets/pages/dashboard.php?luid=<?php echo $location['location_token']; ?>"><?php echo $location['location_name']; ?></a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                    <a class="load_page" data-href="assets/pages/profile.php?uuid=<?php echo $_GET['uuid']; ?>&luid=<?php echo $location['location_token']; ?>" data-page-title="<?php echo $profile['user_fname']." ".$profile['user_lname']; ?>"><?php echo $profile['user_fname']." ".$profile['user_lname']; ?></a>
                </li>
            </ul>
            <div class="page-toolbar">
                <div class="pull-right tooltips btn btn-fit-height default yellow-stripe">
                    Last Login:
                    <strong>
                        <?php
                        if(!empty($profile['user_last_login'])){
                            echo $profile['user_last_login'];
                        } else {
                            echo "Never logged in.";
                        }
                        ?>
                    </strong>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <!-- BEGIN PROFILE SIDEBAR -->
                <div class="profile-sidebar" style="width: 250px;">
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
                                                <img id="pp" src="<?php echo $profile['user_pic']; ?>" alt="1" style="width: 100%; height: 200px; display: block;"/>
                                                <?php
                                            } else {
                                                ?>
                                                <img id="pp" src="assets/admin/layout/img/default.png" alt="2" style="width: 100%; height: 200px; display: block;"/>
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
                                            <button class="btn red updt_pp fileinput-exists" data-uuid="<?php echo $profile['user_token']; ?>">Submit </button>
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
                                        <a class="load_profile_tab" data-href="assets/pages/sub/profile_event_wizard.php?luid=<?php echo $location['location_token']; ?>&uuid=<?php echo $profile['user_token']; ?>&n=nekotwen" data-page-title="Book new move"><i class="icon-plus"></i>Create event for <?php echo $profile['user_fname']; ?>  </a>
                                    </li>
                                    <?php
                                }
                                ?>
                                <li class="active">
                                    <a  class="load_profile_tab ps" data-href="assets/pages/sub/profile_personal_settings.php?uuid=<?php echo $_GET['uuid']; ?>&luid=<?php echo $location['location_token']; ?><?php if(isset($_GET['s'])){ echo "&s=true"; } ?>" data-page-title="<?php echo $profile['user_fname']." ".$profile['user_lname']; ?>"><i class="icon-user"></i>Personal Settings </a>
                                </li>
                                <?php
                                if($profile['user_group'] == 1 && $_SESSION['group'] == 1){
                                    ?>
                                    <li>
                                        <a class="load_profile_tab" data-href="assets/pages/sub/profile_company_settings.php?uuid=<?php echo $profile['user_token']; ?>" data-page-title="Company Settings"><i class="icon-settings"></i>Company Settings  </a>
                                    </li>
                                    <?php
                                }
                                ?>
                            </ul>
                            <?php
                            if(isset($_GET['su'])){
                                ?>
                                <a class="hidden load_profile_tab su" data-href="assets/pages/sub/profile_storage_wizard.php?luid=<?php echo $location['location_token']; ?>&uuid=<?php echo $profile['user_token']; ?>&su=<?php echo $_GET['su']; ?>" data-page-title="Storage Move-In"></a>
                                <?php
                            }
                            ?>
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
                                        <div class="uppercase profile-stat-title" style="font-size: 16px;">
                                            <?php echo $countLocations ?>
                                        </div>
                                        <div class="uppercase profile-stat-text" style="font-size: 9px;">
                                            Locations
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-6">
                                        <div class="uppercase profile-stat-title" style="font-size: 16px;">
                                            <?php echo $countEmployees ?>
                                        </div>
                                        <div class="uppercase profile-stat-text" style="font-size: 9px;">
                                            Employees
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-6">
                                        <div class="uppercase profile-stat-title" style="font-size: 16px;">
                                            <?php echo $countCustomers; ?>
                                        </div>
                                        <div class="uppercase profile-stat-text" style="font-size: 9px;">
                                            Customers
                                        </div>
                                    </div>
                                </div>
                                <!-- END STAT -->
                                <div>
                                    <h4 class="profile-desc-title">On the web <i class="fa fa-angle-right"></i></h4>
                                    <div class="margin-top-20 profile-desc-link">
                                        <i class="fa fa-globe"></i>
                                        <a target="_blank" href=""><?php echo $profile['user_website']; ?></a>
                                    </div>
                                </div>
                            </div>
                            <?php
                        } else {
                            ?>

                            <?php
                        }
                    ?>

                    <!-- END PORTLET MAIN -->
                </div>
                <!-- END BEGIN PROFILE SIDEBAR -->
                <!-- BEGIN PROFILE CONTENT -->
                <div class="profile-content" id="profile-content">

                </div>
                <!-- END PROFILE CONTENT -->
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            <?php
            if(isset($_GET['su'])){
                ?>
                $('.su').click();
                <?php
            } elseif(isset($_GET['wiz'])) {
                // Do nothing!
            } else {
                ?>
                $('.ps').click();
                <?php
            }
            ?>
        });
    </script>
    <?php
} else {
    header("Location: ../../../index.php?err=no_access");
}

