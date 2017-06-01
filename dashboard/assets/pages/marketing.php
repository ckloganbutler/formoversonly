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
            Marketing
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
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light">
                    <div class="portlet-title tabbable-line">
                        <div class="caption caption-md">
                            <i class="icon-earphones-alt theme-font bold"></i>
                            <span class="caption-subject font-red bold uppercase"><?php echo $location['location_name']; ?></span> <span class="font-red">|</span>  <small>Marketing</small>
                        </div>
                        <div class="actions btn-set">
                            <a class="btn default red-stripe" data-toggle="modal" href="#add_marketing">
                                <i class="fa fa-plus"></i> Add new marketer
                            </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="employees_tab">
                                <div class="table-container">
                                    <table class="table table-striped table-bordered table-hover" id="employees">
                                        <thead>
                                        <tr role="row" class="heading">
                                            <th width="18%">
                                                <input type="checkbox" class="group-checkable"> Marketer Contacted
                                            </th>
                                            <th>
                                                Marketer Name & ID
                                            </th>
                                            <th>
                                                Marketer Phone
                                            </th>
                                            <th>
                                                Marketer Email
                                            </th>
                                            <th width="10%">
                                                Contact or Edit
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
        </div>
    </div>
    <form method="POST" action="" role="form" id="new_marketers">
        <div class="modal fade bs-modal-lg" id="add_marketing" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content box red">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h3 class="modal-title font-bold">Add new asset</h3>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label class="control-label visible-ie8 visible-ie9">Select Marketer Type <span class="font-red">*</span></label>
                                <div class="input-icon">
                                    <i class="fa fa-tag"></i>
                                    <select class="form-control" name="type" id="type">
                                        <option disabled selected value="">Select type...</option>
                                        <option value="Realtor">Realtor</option>
                                        <option value="Storage Facility">Storage Facility</option>
                                        <option value="Apartment Community">Apartment Community</option>
                                        <option value="Retail">Retail</option>
                                        <option value="Broker">Broker</option>
                                        <option value="Senior Apartments">Senior Apartments</option>
                                        <option value="Lawyer">Lawyer</option>
                                        <option value="Other">Other</option>
                                    </select>
                                    <span class="help-block">Marketer type.</span>
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
                    </div>
                    <div class="modal-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="checkbox"><input type="checkbox"/> Add to newsletter</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn red pull-right">Save marketer </button>
                                <button type="button" class="btn default pull-right" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <?php
} else {
    header("Location: ../../../index.php?err=no_access");
}
?>
