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
            Create Customer
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
                    <a class="load_page" data-href="assets/pages/customers.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Customers">Customers</a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                    <a class="load_page" data-href="assets/pages/create_customer.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Add Customer">Create Customer</a>
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
								Create Customer </span>
                            <span class="caption-helper">let's get your new customer's information in the system. dont worry, once you've created this customer, you'll have access to more options for them. we just need the basics for now.</span>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <form id="create_customer" action="" method="POST" role="form">
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="control-label visible-ie8 visible-ie9">Full Name <span class="font-red">*</span></label>
                                    <div class="input-icon">
                                        <i class="fa fa-user"></i>
                                        <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Full Name" name="fullname"/>
                                        <span class="help-block">This will be used as reference for the customer.</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label class="control-label visible-ie8 visible-ie9">Phone Number<span class="font-red">*</span></label>
                                        <div class="input-icon">
                                            <i class="fa fa-phone"></i>
                                            <input class="form-control placeholder-no-fix" type="number" autocomplete="off" placeholder="Phone Number" name="phone" value="<?php echo $_GET['p']; ?>"/>
                                            <span class="help-block">This will be the customer's mobile phone number.</span>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="control-label visible-ie8 visible-ie9">Email Address</label>
                                        <div class="input-icon">
                                            <i class="fa fa-envelope"></i>
                                            <input class="form-control placeholder-no-fix" type="email" autocomplete="off" placeholder="Email Address" name="email"/>
                                            <span class="help-block">This will be unique, and cannot be taken by another customer.</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label class="control-label visible-ie8 visible-ie9">Billing Address 1 <span class="font-red">*</span></label>
                                        <div class="input-icon">
                                            <i class="fa fa-location-arrow"></i>
                                            <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Billing Address" name="address"/>
                                            <span class="help-block">This will be the customer's billing address.</span>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label visible-ie8 visible-ie9">Billing Address 2</label>
                                        <div class="input-icon">
                                            <i class="fa fa-location-arrow"></i>
                                            <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Billing Address 2" name="address2"/>
                                            <span class="help-block">This isn't required, and isn't needed in most cases.</span>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label visible-ie8 visible-ie9">Apt/Suite</label>
                                        <div class="input-icon">
                                            <i class="fa fa-location-arrow"></i>
                                            <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Apt/Suite" name="apt"/>
                                            <span class="help-block">This could be the customers apartment number, or a business suite.</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label class="control-label visible-ie8 visible-ie9">City</label>
                                        <div class="input-icon">
                                            <i class="fa fa-location-arrow"></i>
                                            <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="City" name="city"/>
                                            <span class="help-block">This will be the customers billing city.</span>
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
                                            <span class="help-block">This will be the customer's billing state.</span>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label visible-ie8 visible-ie9">Zip Code</label>
                                        <div class="input-icon">
                                            <i class="fa fa-location-arrow"></i>
                                            <input class="form-control placeholder-no-fix" type="number" autocomplete="off" placeholder="Zip Code" name="zip"/>
                                            <span class="help-block">This will be the customers billing city.</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label class="control-label visible-ie8 visible-ie9">Company/Organization Name</label>
                                        <div class="input-icon">
                                            <i class="fa fa-location-arrow"></i>
                                            <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Company/Organization Name" name="company"/>
                                            <span class="help-block">This will only be required if the customer has their own company, and you'd like to make record of it.</span>
                                        </div>
                                    </div>
                                </div>
                                <br/>
                                <button type="submit" class="btn red">Create Customer</button>
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
            $('#create_customer').validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",
                rules: {
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
                    $.ajax({
                        url: 'assets/app/register.php?gr=3&c=<?php echo $_SESSION['cuid']; ?>',
                        type: "POST",
                        data: $('#create_customer').serialize(),
                        success: function(data) {
                            toastr.success("<strong>Logan says</strong>:<br/>Nice! We've added your customer to the system, you will now be redirected to their profile.");
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
