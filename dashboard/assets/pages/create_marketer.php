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
    mysql_query("UPDATE fmo_users SET user_last_location='".mysql_real_escape_string(basename(__FILE__, '.php')).".php?".$_SERVER['QUERY_STRING']."' WHERE user_token='".mysql_real_escape_string($_SESSION['uuid'])."'");
    $location = mysql_fetch_array(mysql_query("SELECT location_name FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'"));
    ?>
    <div class="page-content">
        <h3 class="page-title">
            Create Marketer
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
                    <a class="load_page" data-href="assets/pages/marketing.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="marketers">Marketing</a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                    <a class="load_page" data-href="assets/pages/create_marketing.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Create marketer">Create Marketer</a>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-user-plus bold"></i>
                            <span class="caption-subject bold font-red uppercase">
								Create Marketer </span>
                            <span class="caption-helper">let's get your new marketer's information in the system. dont worry, once you've created this marketer, you'll have access to more options for them. we just need the basics for now.</span>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <form id="create_marketer" action="" method="POST" role="form">
                            <div class="form-body">
                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <label class="control-label visible-ie8 visible-ie9">Select marketer Role <span class="font-red">*</span></label>
                                        <div class="input-icon">
                                            <i class="fa fa-tag"></i>
                                            <select class="form-control" name="role" id="role">
                                                <option disabled selected value="">Select one...</option>
                                                <option value="2">Manager</option>
                                                <option value="5.1">Driver</option>
                                                <option value="5.2">Helper</option>
                                                <option value="5.3">Crewman/Other</option>
                                                <option value="4">Customer Service Represenative</option>
                                            </select>
                                            <span class="help-block">Otherwise known as their position.</span>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-9">
                                        <label class="control-label visible-ie8 visible-ie9">Full Name <span class="font-red">*</span></label>
                                        <div class="input-icon">
                                            <i class="fa fa-user"></i>
                                            <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Full Name" name="fullname"/>
                                            <span class="help-block">This will be used as reference for the marketer.</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label class="control-label visible-ie8 visible-ie9">Phone Number<span class="font-red">*</span></label>
                                        <div class="input-icon">
                                            <i class="fa fa-phone"></i>
                                            <input class="form-control placeholder-no-fix" type="number" autocomplete="off" placeholder="Phone Number" name="phone" value="<?php echo $_GET['p']; ?>"/>
                                            <span class="help-block">This will be the marketer's mobile phone number.</span>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="control-label visible-ie8 visible-ie9">Email Address</label>
                                        <div class="input-icon">
                                            <i class="fa fa-envelope"></i>
                                            <input class="form-control placeholder-no-fix" type="email" autocomplete="off" placeholder="Email Address" name="email"/>
                                            <span class="help-block">This will be unique, and cannot be taken by another marketer.</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label class="control-label visible-ie8 visible-ie9">Street Address 1 <span class="font-red">*</span></label>
                                        <div class="input-icon">
                                            <i class="fa fa-location-arrow"></i>
                                            <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Street Address" name="address"/>
                                            <span class="help-block">This will be the marketer's street address.</span>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label visible-ie8 visible-ie9">Street Address 2</label>
                                        <div class="input-icon">
                                            <i class="fa fa-location-arrow"></i>
                                            <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Street Address 2" name="address2"/>
                                            <span class="help-block">This isn't required, and isn't needed in most cases.</span>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label visible-ie8 visible-ie9">Apt/Suite</label>
                                        <div class="input-icon">
                                            <i class="fa fa-location-arrow"></i>
                                            <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Apt/Suite" name="apt"/>
                                            <span class="help-block">This could be the marketer's apartment number, or a business suite.</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label class="control-label visible-ie8 visible-ie9">City</label>
                                        <div class="input-icon">
                                            <i class="fa fa-location-arrow"></i>
                                            <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="City" name="city"/>
                                            <span class="help-block">This will be the marketer's billing city.</span>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label visible-ie8 visible-ie9">State</label>
                                        <div class="input-icon">
                                            <i class="fa fa-location-arrow"></i>
                                            <select class="form-control" name="state" id="state">
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
                                            <span class="help-block">This will be the marketer's billing state.</span>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label visible-ie8 visible-ie9">Zip Code</label>
                                        <div class="input-icon">
                                            <i class="fa fa-location-arrow"></i>
                                            <input class="form-control placeholder-no-fix" type="number" autocomplete="off" placeholder="Zip Code" name="zip"/>
                                            <span class="help-block">This will be the marketer's billing city.</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label class="control-label visible-ie8 visible-ie9">Company/Organization Name</label>
                                        <div class="input-icon">
                                            <i class="fa fa-location-arrow"></i>
                                            <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Company/Organization Name" name="company"/>
                                            <span class="help-block">This will only be required if the marketer has their own company, and you'd like to make record of it.</span>
                                        </div>
                                    </div>
                                </div>
                                <br/>
                                <button type="submit" class="btn red">Create marketer</button>
                                <a class="load_page btn default" data-href="assets/pages/dashboard.php" data-page-title="Dashboard">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            $('#create_marketer').validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",
                rules: {
                    role: {
                        required: true
                    },
                    fullname: {
                        required: true
                    },
                    phone: {
                        required: true,
                        remote: 'assets/app/search_phone.php'
                    },
                    email: {
                        required: false,
                        remote: 'assets/app/search_email.php'
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

                messages: {
                    phone: {
                        remote: "Phone number is already taken"
                    },
                    email: {
                        remote: "Email address is already taken"
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
                    var group = $('#role').val();
                    $.ajax({
                        url: 'assets/app/register.php?gr='+group+'&c=<?php echo $_SESSION['cuid']; ?>&luid=<?php echo $_GET['luid']; ?>',
                        type: "POST",
                        data: $('#create_marketer').serialize(),
                        success: function(data) {
                            toastr.success("<strong>Logan says</strong>:<br/>Nice! We've added your marketer to the system, you will now be redirected to their profile.");
                            $.ajax({
                                url: 'assets/pages/profile.php?uuid='+data,
                                success: function(data) {
                                    $('#page_content').html(data);
                                    document.title = "Profile - For Movers Only";
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
    <?php
} else {
    header("Location: ../../../index.php?err=no_access");
}
?>
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
    mysql_query("UPDATE fmo_users SET user_last_location='".mysql_real_escape_string(basename(__FILE__, '.php')).".php?".$_SERVER['QUERY_STRING']."' WHERE user_token='".mysql_real_escape_string($_SESSION['uuid'])."'");
    $location = mysql_fetch_array(mysql_query("SELECT location_name FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'"));
    ?>
    <div class="page-content">
        <h3 class="page-title">
            Create Marketer
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
                    <a class="load_page" data-href="assets/pages/marketing.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Marketing">Marketing</a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                    <a class="load_page" data-href="assets/pages/create_marketer.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Create marketer">Create marketer</a>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-user-plus bold"></i>
                            <span class="caption-subject bold font-red uppercase">
								Create marketer </span>
                            <span class="caption-helper">let's get your new marketer's information in the system.</span>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <form id="create_marketer" action="" method="POST" role="form">
                            <div class="form-body">
                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <label class="control-label visible-ie8 visible-ie9">Select Marketer Type <span class="font-red">*</span></label>
                                        <div class="input-icon">
                                            <i class="fa fa-tag"></i>
                                            <select class="form-control" name="type" id="type">
                                                <option disabled selected value="">Select marketer type...</option>
                                                <option value="Realtor">Realtor</option>
                                                <option value="Storage Facility">Storage Facility</option>
                                                <option value="Apartment Community">Apartment Community</option>
                                                <option value="Retail">Retail</option>
                                                <option value="Broker">Broker</option>
                                                <option value="Senior Apartments">Senior Apartments</option>
                                                <option value="Lawyer">Lawyer</option>
                                                <option value="Other">Other</option>
                                            </select>
                                            <span class="help-block">Just for your reference.</span>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-9">
                                        <label class="control-label visible-ie8 visible-ie9">Full Name of Contact <span class="font-red">*</span></label>
                                        <div class="input-icon">
                                            <i class="fa fa-user"></i>
                                            <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Full Name of Contact" name="fullname"/>
                                            <span class="help-block">This will be used as reference for the marketer.</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label class="control-label visible-ie8 visible-ie9">Phone Number<span class="font-red">*</span></label>
                                        <div class="input-icon">
                                            <i class="fa fa-phone"></i>
                                            <input class="form-control placeholder-no-fix" type="number" autocomplete="off" placeholder="Phone Number" name="phone" value="<?php echo $_GET['p']; ?>"/>
                                            <span class="help-block">This will be the marketer's mobile phone number.</span>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="control-label visible-ie8 visible-ie9">Email Address</label>
                                        <div class="input-icon">
                                            <i class="fa fa-envelope"></i>
                                            <input class="form-control placeholder-no-fix" type="email" autocomplete="off" placeholder="Email Address" name="email"/>
                                            <span class="help-block">This will be the marketer's email address.</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label class="control-label visible-ie8 visible-ie9">Street Address 1 <span class="font-red">*</span></label>
                                        <div class="input-icon">
                                            <i class="fa fa-location-arrow"></i>
                                            <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Street Address" name="address"/>
                                            <span class="help-block">This will be the marketer's street address.</span>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label visible-ie8 visible-ie9">Street Address 2</label>
                                        <div class="input-icon">
                                            <i class="fa fa-location-arrow"></i>
                                            <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Street Address 2" name="address2"/>
                                            <span class="help-block">This isn't required, and isn't needed in most cases.</span>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label visible-ie8 visible-ie9">Apt/Suite</label>
                                        <div class="input-icon">
                                            <i class="fa fa-location-arrow"></i>
                                            <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Apt/Suite" name="apt"/>
                                            <span class="help-block">This could be the marketer's apartment number, or a business suite.</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label class="control-label visible-ie8 visible-ie9">City</label>
                                        <div class="input-icon">
                                            <i class="fa fa-location-arrow"></i>
                                            <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="City" name="city"/>
                                            <span class="help-block">This will be the marketer's city.</span>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label visible-ie8 visible-ie9">State</label>
                                        <div class="input-icon">
                                            <i class="fa fa-location-arrow"></i>
                                            <select class="form-control" name="state" id="state">
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
                                            <span class="help-block">This will be the marketer's state.</span>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label visible-ie8 visible-ie9">Zip Code</label>
                                        <div class="input-icon">
                                            <i class="fa fa-location-arrow"></i>
                                            <input class="form-control placeholder-no-fix" type="number" autocomplete="off" placeholder="Zip Code" name="zip"/>
                                            <span class="help-block">This will be the marketer's city.</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label class="control-label visible-ie8 visible-ie9">Company/Organization Name</label>
                                        <div class="input-icon">
                                            <i class="fa fa-location-arrow"></i>
                                            <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Company/Organization Name" name="company"/>
                                            <span class="help-block">This will only be required if the marketer has their own company, and you'd like to make record of it.</span>
                                        </div>
                                    </div>
                                </div>
                                <br/>
                                <button type="submit" class="btn red">Create marketer</button>
                                <a class="load_page btn default" data-href="assets/pages/dashboard.php" data-page-title="Dashboard">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            $('#create_marketer').validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",
                rules: {
                    type: {
                        required: true
                    },
                    companyname: {
                        required: true
                    },
                    name: {
                        required: true
                    },
                    phone: {
                        required: true
                    },
                    email: {
                        required: false
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
                    var group = $('#role').val();
                    $.ajax({
                        url: 'assets/app/add_setting.php?setting=marketer',
                        type: "POST",
                        data: $('#create_marketer').serialize(),
                        success: function(data) {
                            toastr.success("<strong>Logan says</strong>:<br/>Nice! We've added your marketer to the system, you will now be redirected to the master list.");
                            $.ajax({
                                url: 'assets/pages/marketing.php?luid=<?php $_GET['luid']; ?>',
                                success: function(data) {
                                    $('#page_content').html(data);
                                    document.title = "Marketing";
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
    <?php
} else {
    header("Location: ../../../index.php?err=no_access");
}
?>
