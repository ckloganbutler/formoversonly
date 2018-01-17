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
    mysql_query("UPDATE fmo_users SET user_last_location='".mysql_real_escape_string(basename(__FILE__, 'new.php'))."new.php?".$_SERVER['QUERY_STRING']."' WHERE user_token='".mysql_real_escape_string($_SESSION['uuid'])."'");
    ?>
    <div class="page-content">
        <h3 class="page-title">
            <strong><?php echo companyName($_SESSION['cuid']); ?> |</strong> <small>Create Location</small>
        </h3>
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <i class="fa fa-home"></i>
                    <a class="load_page" data-href="assets/pages/dashboard.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Dashboard">Dashboard</a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                    <a class="load_page" data-href="assets/pages/create_location.php" data-page-title="Create Location">Create Location</a>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="icon-paper-plane bold"></i>
                            <span class="caption-subject bold font-red uppercase">
								Create Location </span>
                            <span class="caption-helper">let's get your new location's information in the system. dont worry, once you've created your location, you'll have access to more configuration options. we just need the basics for now.</span>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <form id="create_location" action="" method="POST" role="form">
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="control-label visible-ie8 visible-ie9">Name</label>
                                    <div class="input-icon">
                                        <i class="fa fa-tag"></i>
                                        <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Name (e.g: city name, common name)" name="name"/>
                                        <span class="help-block">This will be used as reference for the location</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label visible-ie8 visible-ie9">Address</label>
                                    <div class="input-icon">
                                        <i class="fa fa-location-arrow"></i>
                                        <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Address" name="address"/>
                                        <span class="help-block">This will be the line address of the location</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label visible-ie8 visible-ie9">City</label>
                                    <div class="input-icon">
                                        <i class="fa fa-location-arrow"></i>
                                        <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="City" name="city"/>
                                        <span class="help-block">This will be the city of the location</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label visible-ie8 visible-ie9">State</label>
                                    <div class="input-icon">
                                        <i class="fa fa-location-arrow"></i>
                                        <select class="form-control" required="required" name="state" id="state">
                                            <option disabled selected value="">State</option>
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
                                        <span class="help-block">This will be the state of the location</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label visible-ie8 visible-ie9">Zip Code</label>
                                    <div class="input-icon">
                                        <i class="fa fa-location-arrow"></i>
                                        <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Zip Code" name="zip"/>
                                        <span class="help-block">This will be the zip/postal code of the location</span>
                                    </div>
                                </div>
                                <br/>
                                <button type="submit" class="btn red">Add Location</button>
                                <a class="load_page btn default" data-href="assets/pages/location_admin.php" data-page-title="Location Administration">Cancel</a>
                            </div>
                        </form>
                        <script type="text/javascript">
                            $(document).ready(function() {
                                $('#create_location').validate({
                                    errorElement: 'span', //default input error message container
                                    errorClass: 'help-block', // default input error message class
                                    focusInvalid: false, // do not focus the last invalid input
                                    ignore: "",
                                    rules: {
                                        name: {
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
                                            url: 'assets/app/create_location.php',
                                            type: "POST",
                                            data: $('#create_location').serialize(),
                                            success: function(data) {
                                                toastr.success("<strong>Logan says</strong>:<br/>Nice! We've added your location to the system. Now, we will redirect you back to the Location Administration.");
                                                $.ajax({
                                                    url: 'assets/pages/manage_location.php?luid=' + data,
                                                    success: function(data) {
                                                        $('#page_content').html(data);
                                                        document.title = "Location Administration - For Movers Only";
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
                            });
                        </script>
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
