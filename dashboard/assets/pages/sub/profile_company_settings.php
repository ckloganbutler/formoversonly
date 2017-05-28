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
                    <ul class="nav nav-tabs nav-justified">
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
                            <h3>Company Information</h3>
                            <div class="row static-info" style="margin-top: 20px;">
                                <div class="col-md-5 name">
                                    Name:
                                </div>
                                <div class="col-md-7 value">
                                    <a class="cs_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_company_name" data-pk="<?php echo $profile['user_token']; ?>" data-type="text" data-placement="right" data-title="Enter new company name.." data-url="assets/app/update_settings.php?update=usr_prf">
                                        <?php echo $profile['user_company_name']; ?>
                                    </a>
                                </div>
                            </div>
                            <div class="row static-info">
                                <div class="col-md-5 name">
                                    Website URL:
                                </div>
                                <div class="col-md-7 value">
                                    <a class="cs_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_website" data-pk="<?php echo $profile['user_token']; ?>" data-type="text" data-placement="right" data-title="Enter new website URL.." data-url="assets/app/update_settings.php?update=usr_prf">
                                        <?php echo $profile['user_website']; ?>
                                    </a>
                                </div>
                            </div>
                            <div class="row static-info">
                                <div class="col-md-5 name">
                                    Address:
                                </div>
                                <div class="col-md-7 value">
                                    <a class="cs_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_address" data-pk="<?php echo $profile['user_token']; ?>" data-type="text" data-placement="right" data-title="Enter new address.." data-url="assets/app/update_settings.php?update=usr_prf">
                                        <?php echo $profile['user_address']; ?>
                                    </a>,
                                    <a class="cs_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_city" data-pk="<?php echo $profile['user_token']; ?>" data-type="text" data-placement="right" data-title="Enter new city.." data-url="assets/app/update_settings.php?update=usr_prf">
                                        <?php echo $profile['user_city']; ?>
                                    </a>,
                                    <a class="cs_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_state" data-pk="<?php echo $profile['user_token']; ?>" data-type="text" data-placement="right" data-title="Select new state.." data-url="assets/app/update_settings.php?update=usr_prf">
                                        <?php echo $profile['user_state']; ?>
                                    </a>,
                                    <a class="cs_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_zip" data-pk="<?php echo $profile['user_token']; ?>" data-type="text" data-placement="right" data-title="Enter new zip code.." data-url="assets/app/update_settings.php?update=usr_prf">
                                        <?php echo $profile['user_zip']; ?>
                                    </a>
                                </div>
                            </div>
                            <hr/>
                            <a class="btn red edit" data-edit="cs_<?php echo $profile['user_token']; ?>" data-reload="">Edit </a>
                            <hr/>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="portlet">
                                        <div class="portlet-title tabbable-line" style="border-bottom: none;">
                                            <div class="caption caption-md">
                                                <i class="icon-tag theme-font bold"></i>
                                                Company Licenses
                                            </div>
                                            <div class="actions btn-set">
                                                <a class="load_page btn default red-stripe" data-href="assets/pages/create_location.php" data-page-title="Create Location"><i class="fa fa-plus"></i> Add license</a>
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
                                    </div>
                                </div>
                            </div>
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
                                                        <div class="col-md-5" style="padding-left: 0;">
                                                            <div class="portfolio-text">
                                                                <img src="assets/admin/pages/media/gallery/image3.jpg" alt="" height="81px" width="81px">
                                                                <div class="portfolio-text-info">
                                                                    <h4><?php echo $loc['location_name']; ?></h4>
                                                                    <p>
                                                                        <?php echo $loc['location_address'].", ".$loc['location_city'].", ".$loc['location_state']." - ".$loc['location_zip']; ?>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-5 portfolio-stat" style="margin-top: 8px;">
                                                            <div class="portfolio-info">
                                                                Hot Leads <span>0 </span>
                                                            </div>
                                                            <div class="portfolio-info">
                                                                New Customers <span>0 </span>
                                                            </div>
                                                            <div class="portfolio-info">
                                                                New Bookings <span>0 </span>
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
