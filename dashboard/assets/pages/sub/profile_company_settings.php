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
    $profile = mysql_fetch_array(mysql_query("SELECT user_fname, user_lname, user_email, user_phone, user_company_name, user_website, user_pic, user_token, user_address, user_city, user_state, user_zip FROM fmo_users WHERE user_token='".mysql_real_escape_string($_GET['uuid'])."'"));
    if($_SESSION['uuid'] == $profile['user_token']) {
        $editable = true;
        $view     = 'editOnly';
    } else {$editable = false;$view='infoOnly';}
    ?>
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light">
                <div class="portlet-title tabbable-line">
                    <div class="caption caption-md">
                        <i class="icon-globe theme-font hide"></i>
                        <span class="caption-subject font-red bold uppercase">Company Settings</span>
                    </div>
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#settings" data-toggle="tab">Company Information</a>
                        </li>
                        <li>
                            <a href="#locations" data-toggle="tab">All Locations</a>
                        </li>
                    </ul>
                </div>
                <div class="portlet-body">
                    <div class="tab-content">
                        <!-- PERSONAL INFO TAB -->
                        <div class="tab-pane active" id="settings">
                            <?php
                            if($view == 'infoOnly'){
                                ?>

                                <?php
                            } elseif($view == 'editOnly'){
                                ?>
                                <form id="update_profile" role="form" action="">
                                    <div class="form-group">
                                        <label class="control-label">Address</label>
                                        <input type="text" placeholder="<?php echo $profile['user_address']; ?>" class="form-control" name="address"  <?php if($editable == false){echo "readonly";} ?>/>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">City</label>
                                        <input type="text" placeholder="<?php echo $profile['user_city']; ?>" class="form-control" name="city"  <?php if($editable == false){echo "readonly";} ?>/>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">State</label>
                                        <input type="text" placeholder="<?php echo $profile['user_state']; ?>" class="form-control" name="state"  <?php if($editable == false){echo "readonly";} ?>/>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Zip Code</label>
                                        <input type="text" placeholder="<?php echo $profile['user_zip']; ?>" class="form-control" name="zip"  <?php if($editable == false){echo "readonly";} ?>/>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Company Name</label>
                                        <input type="text" placeholder="<?php echo $profile['user_company_name']; ?>" class="form-control" name="company"  <?php if($editable == false){echo "readonly";} ?>/>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Website Url</label>
                                        <input type="text" placeholder="<?php echo $profile['user_website']; ?>" class="form-control" name="website"  <?php if($editable == false){echo "readonly";} ?>/>
                                    </div>
                                </form>
                                <div class="margiv-top-10">
                                    <a class="update_settings btn red" data-form="assets/app/update_settings.php?update=personal" data-id="#update_profile" data-reload="assets/pages/profile.php">Save Changes </a>
                                    <a href="javascript:;" class="btn default">Cancel </a>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <!-- END PERSONAL INFO TAB -->
                        <!-- CHANGE AVATAR TAB -->
                        <div class="tab-pane" id="locations">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="portlet light">
                                        <div class="portlet-title tabbable-line">
                                            <div class="caption caption-md">
                                                <i class="icon-pin theme-font bold"></i>
                                                <span class="font-red">|</span>  <small>You can edit/manage all your locations here.</small>
                                            </div>
                                            <div class="actions btn-set">
                                                <a class="load_page btn default red-stripe" data-href="assets/pages/create_location.php" data-page-title="Create Location"><i class="fa fa-plus"></i> Add location</a>
                                            </div>
                                        </div>
                                        <div class="portlet-body">
                                            <?php
                                            $locations = mysql_query("SELECT location_name, location_token, location_address, location_city, location_state, location_zip FROM fmo_locations WHERE location_owner_token='".mysql_real_escape_string($_SESSION['uuid'])."'");

                                            if(mysql_num_rows($locations) > 0){
                                                while($loc = mysql_fetch_assoc($locations)){
                                                    ?>
                                                    <div class="portfolio-block">
                                                        <div class="col-md-2" style="padding-left: 0;">
                                                            <div class="portfolio-text">
                                                                <img src="assets/admin/pages/media/gallery/image3.jpg" alt="" height="81px" width="81px">
                                                                <div class="portfolio-text-info">
                                                                    <h4><?php echo $loc['location_name']; ?></h4>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="sparkline-chart">
                                                                <div class="number sparkline_line"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 portfolio-stat" style="margin-top: 8px;">
                                                            <div class="portfolio-info">
                                                                Employees <span>0 </span>
                                                            </div>
                                                            <div class="portfolio-info">
                                                                Customers <span>0 </span>
                                                            </div>
                                                            <div class="portfolio-info">
                                                                Moves <span>0 </span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2" style="padding-right: 0;">
                                                            <div class="portfolio-btn">
                                                                <a class="btn bigicn-only load_page" data-href="assets/pages/manage_location.php?luid=<?php echo $loc['location_token']; ?>" data-page-title="<?php echo $loc['location_name']; ?>">
                                                                    <span>Manage </span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                            } else {
                                                ?>
                                                <h3 class="text-center">No locations found for your company yet. Would you like to <a class="load_page" data-href="assets/pages/create_location.php">create one</a>?</h3>
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
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            $(".sparkline_line").sparkline([9, 10, 9, 10, 10, 11, 12, 10, 10, 11, 11, 12, 11, 10, 12, 11, 10, 12], {
                type: 'line',
                width: '280',
                height: '55',
                lineColor: '#cb5a5e'
            });
        });
    </script>
    <?php
} else {
    header("Location: ../../../index.php?err=no_access");
}
?>
