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
    $profile = mysql_fetch_array(mysql_query("SELECT user_fname, user_lname, user_email, user_phone, user_company_name, user_website, user_pic, user_token, user_address, user_city, user_state, user_zip, user_broadcast FROM fmo_users WHERE user_token='".mysql_real_escape_string($_GET['uuid'])."'"));
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
                            <div class="row static-info">
                                <div class="col-md-5 name">
                                    Broadcast Message:
                                </div>
                                <div class="col-md-7 value">
                                    <a class="cs_<?php echo $profile['user_token']; ?>" style="color:#333333" data-name="user_broadcast" data-pk="<?php echo $profile['user_token']; ?>" data-type="textarea" data-placement="right" data-title="Enter new broadcast message.." data-url="assets/app/update_settings.php?update=usr_prf">
                                        <?php echo $profile['user_broadcast']; ?>
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
                                                <a class="btn default red-stripe show_form" data-show="#add_license">
                                                    <i class="fa fa-plus"></i>
                                                    <span class="hidden-480">Upload new document</span>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="portlet-body">
                                            <div class="table-container">
                                                <form role="form" id="add_licenses">
                                                    <table class="table table-striped table-bordered table-hover" id="licenses">
                                                        <thead>
                                                        <tr role="row" class="heading">
                                                            <th width="12%">
                                                                License Type
                                                            </th>
                                                            <th>
                                                                License State
                                                            </th>
                                                            <th>
                                                                License Prefix
                                                            </th>
                                                            <th>
                                                                License Number
                                                            </th>
                                                            <th>
                                                                License Timestamp
                                                            </th>
                                                        </tr>
                                                        <tr role="row" class="filter" style="display: none;" id="add_license">
                                                            <td><select class="form-control input-sm" name="type">
                                                                    <option disabled selected value="">Select one..</option>
                                                                    <option value="Federal">Federal</option>
                                                                    <option value="State">State</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select class="form-control input-sm" name="state">
                                                                    <option disabled selected value="">Select one..</option>
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
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control form-filter input-sm" name="prefix">
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control input-sm" name="number">
                                                            </td>
                                                            <td>
                                                                <div class="margin-bottom-5">
                                                                    <button type="button" class="btn btn-sm red margin-bottom add_license"><i class="fa fa-download"></i> Save</button>
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
            var grid = new Datatable();

            grid.init({
                src: $("#licenses"),
                onSuccess: function (grid) {
                    // execute some code after table records loaded
                },
                onError: function (grid) {
                    // execute some code on network or other general error
                },
                onDataLoad: function(grid) {

                },
                loadingMessage: 'Loading...',
                dataTable: {
                    "processing": true,
                    "serverSide": true,
                    //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                    "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
                    "bPaginate": false,
                    "ajax": {
                        "url": "assets/app/api/profile.php?type=licenses&uuid=<?php echo $_GET['uuid']; ?>", // ajax source
                    },
                    "language": {
                        "aria": {
                            "sortAscending": ": activate to sort column ascending",
                            "sortDescending": ": activate to sort column descending"
                        },
                        "emptyTable": "No data available in table",
                        "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                        "infoEmpty": "No entries found",
                        "infoFiltered": "(filtered1 from _MAX_ total entries)",
                        "lengthMenu": "Show _MENU_ entries",
                        "search": "Search:",
                        "zeroRecords": "No matching records found"
                    },
                }
            });
            $('.show_form').on('click', function() {
                var show = $(this).attr('data-show');

                $(show).show();
            });
            $("#add_licenses").validate({
                errorElement: 'span', //default input error message container
                errorClass: 'font-red', // default input error message class
                rules: {
                    type: {
                        required: true
                    },
                    state: {
                        required: true
                    },
                    prefix: {
                        required: true
                    },
                    number: {
                        required: true
                    }
                }
            });
            $('.add_license').on('click', function(){
                if($("#add_licenses").valid()){
                    $.ajax({
                        url: "assets/app/add_setting.php?setting=usr_lic&uuid=<?php echo $profile['user_token']; ?>",
                        type: "POST",
                        data: $('#add_licenses').serialize(),
                        success: function(data) {
                            toastr.info('<strong>Logan says</strong>:<br/>License has been added to your companies license records.');
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
