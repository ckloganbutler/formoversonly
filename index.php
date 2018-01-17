<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<head>
    <meta charset="utf-8"/>
    <title>Login [+] For Movers Only</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta content="For Movers Only UI Description" name="description">
    <meta content="For Movers Only Keywords" name="keywords">
    <meta content="loganck" name="author">
    <meta property="og:site_name" content="https://www.formoversonly.com">
    <meta property="og:title" content="For Movers Only">
    <meta property="og:description" content="Automated Fleet Software for Moving Companies">
    <meta property="og:type" content="website">
    <meta property="og:image" content="">
    <meta property="og:url" content="https://www.formoversonly.com">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
    <link href="dashboard/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="dashboard/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
    <link href="dashboard/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="dashboard/assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
    <link href="dashboard/assets/global/plugins/select2/select2.css" rel="stylesheet" type="text/css"/>
    <link href="dashboard/assets/admin/pages/css/login3.css" rel="stylesheet" type="text/css"/>
    <link href="dashboard/assets/global/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>
    <link href="dashboard/assets/global/css/plugins.css" rel="stylesheet" type="text/css"/>
    <link href="dashboard/assets/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>
    <link href="dashboard/assets/admin/layout/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/>
    <link href="dashboard/assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>
    <link rel="shortcut icon" href="dashboard/favicon.ico"/>
</head>
<body class="login">
<div class="logo">
    <a href="">
        <img src="dashboard/assets/admin/layout/img/logo-big.png" alt=""/>
    </a>
</div>
<div class="menu-toggler sidebar-toggler">
</div>
<div class="content" style="width: 390px!important;">
    <div class="login-form">
        <div id="alert-msg">
            <?php
            if(isset($_GET['err'])){
                if($_GET['err'] == 'verify'){
                    ?>
                    <div class="alert alert-danger">
                        <button class="close" data-close="alert"></button>
                        <span>You need to validate your account before you can login. </span>
                    </div>
                    <?php
                }
                if($_GET['err'] == 'generic'){
                    ?>
                    <div class="alert alert-danger">
                        <button class="close" data-close="alert"></button>
                        <span>Incorrect username/password combination. </span>
                    </div>
                    <?php
                }
                if($_GET['err'] == 'no_access'){
                    ?>
                    <div class="alert alert-danger">
                        <button class="close" data-close="alert"></button>
                        <span>You need to be logged in to access that page. </span>
                    </div>
                    <?php
                }
                if($_GET['err'] == 'goodbye'){
                    ?>
                    <div class="alert alert-success">
                        <button class="close" data-close="alert"></button>
                        <span>You have been successfully logged out, see you again soon! </span>
                    </div>
                    <?php
                }
                if($_GET['err'] == 'lock_uuid'){
                    ?>
                    <div class="alert alert-danger">
                        <button class="close" data-close="alert"></button>
                        <span><strong>Don't do that! </strong>You have been warned.</span>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div class="alert alert-danger display-hide">
                    <button class="close" data-close="alert"></button>
                    <span>You must enter a combination. </span>
                </div>
                <?php
            }
            ?>

        </div>
        <h3 class="form-title"><strong>System</strong> login</h3>
        <form class="admin-form" action="dashboard/assets/app/login.php?t=aXn" method="POST">
            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">Email</label>
                <div class="input-icon">
                    <i class="fa fa-envelope"></i>
                    <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Email" name="email"/>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">Password</label>
                <div class="input-icon">
                    <i class="fa fa-lock"></i>
                    <input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Password" name="password"/>
                </div>
            </div>
            <div class="form-actions">
                <label class="checkbox">
                    <input type="checkbox" name="remember" value="1"/> Remember me </label>
                <button type="submit" class="btn red pull-right">
                    Login <i class="m-icon-swapright m-icon-white"></i>
                </button>
            </div>
        </form>

        <div class="forget-password">
            <h4>Forgot your password ?</h4>
            <p>
                no worries, click <a href="javascript:;" id="forget-password">
                    here </a>
                to reset your password.
            </p>
        </div>
        <div class="create-account">
            <p>
                Don't have an account yet ?&nbsp;

                <a href="javascript:;" id="register-btn">
                    Create an account </a>
            </p>
        </div>
    </div>
    <!-- END LOGIN FORM -->
    <!-- BEGIN FORGOT PASSWORD FORM -->
    <form class="forget-form forgot-container" action="" method="post">
        <h3>Forget Password ?</h3>
        <p>
            Enter your phone number below to reset your password.
        </p>
        <div class="form-group">
            <div class="input-icon">
                <i class="fa fa-phone"></i>
                <input class="form-control placeholder-no-fix" id="forgot_phone" type="text" autocomplete="off" placeholder="phone" name="phone"/>
            </div>
        </div>
        <div class="form-actions">
            <button type="button" id="back-btn" class="btn">
                <i class="m-icon-swapleft"></i> Back </button>
            <button type="button" class="btn red pull-right urs">
                Submit <i class="m-icon-swapright m-icon-white"></i>
            </button>
        </div>
    </form>
    <!-- END FORGOT PASSWORD FORM -->
    <!-- BEGIN REGISTRATION FORM -->
    <form class="register-form" action="" method="post">
        <h3>Sign Up</h3>
        <p>
            First, we'll need some personal information.
        </p>
        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9">Full Name</label>
            <div class="input-icon">
                <i class="fa fa-font"></i>
                <input class="form-control placeholder-no-fix" type="text" placeholder="Full Name" name="fullname"/>
            </div>
        </div>
        <div class="form-group">
            <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
            <label class="control-label visible-ie8 visible-ie9">Email</label>
            <div class="input-icon">
                <i class="fa fa-envelope"></i>
                <input class="form-control placeholder-no-fix" type="text" placeholder="Email" name="email"/>
            </div>
        </div>
        <div class="form-group">
            <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
            <label class="control-label visible-ie8 visible-ie9">Phone</label>
            <div class="input-icon">
                <i class="fa fa-mobile-phone"></i>
                <input class="form-control placeholder-no-fix" type="number" placeholder="Phone" name="phone"/>
            </div>
        </div>
        <p>
            Next, make a strong password.
        </p>
        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9">Password</label>
            <div class="input-icon">
                <i class="fa fa-lock"></i>
                <input class="form-control placeholder-no-fix" type="password" autocomplete="off" id="register_password" placeholder="Password" name="password"/>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9">Re-type Your Password</label>
            <div class="controls">
                <div class="input-icon">
                    <i class="fa fa-check"></i>
                    <input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Re-type Your Password" name="rpassword"/>
                </div>
            </div>
        </div>
        <p>Finally, we need your companies mailing address. </p>
        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9">Company Name</label>
            <div class="input-icon">
                <i class="fa fa-tag"></i>
                <input class="form-control placeholder-no-fix" type="text" placeholder="Company Name" name="company"/>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9">Address</label>
            <div class="input-icon">
                <i class="fa fa-check"></i>
                <input class="form-control placeholder-no-fix" type="text" placeholder="Address" name="address"/>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9">City/Town</label>
            <div class="input-icon">
                <i class="fa fa-location-arrow"></i>
                <input class="form-control placeholder-no-fix" type="text" placeholder="City/Town" name="city"/>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9">Zip Code</label>
            <div class="input-icon">
                <i class="fa fa-location-arrow"></i>
                <input class="form-control placeholder-no-fix" type="text" placeholder="Zip Code" name="zip" id="zip"/>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9">State</label>
            <select name="state" class="form-control" style="border-left: 2px solid #44B6AE !important">
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
            </select>
        </div>
        <div class="form-group">
            <label>
                <input type="checkbox" name="tnc"/> I agree to the <a href="javascript:;">
                    Terms of Service </a>
                and <a href="javascript:;">
                    Privacy Policy </a>
            </label>
            <div id="register_tnc_error">
            </div>
        </div>
        <div class="form-actions">
            <button id="register-back-btn" type="button" class="btn">
                <i class="m-icon-swapleft"></i> Back </button>
            <button type="submit" id="register-submit-btn" class="btn red pull-right">
                Sign Up <i class="m-icon-swapright m-icon-white"></i>
            </button>
        </div>
    </form>
    <!-- END REGISTRATION FORM -->
</div>
<!-- END LOGIN -->
<!-- BEGIN COPYRIGHT -->
<div class="copyright">
    <strong>For Movers Only&trade;</strong> - Moving Management Software <br/> &copy; 2016-2017 <a target="_blank" href="//www.captialkingdom.com">CK, Inc.</a> <br/>  <small><strong><a href="tos.php">Terms of Service</a> &nbsp; [+] &nbsp; <a href="pp.php">Privacy Policy</a></strong></small>
</div>
<!--[if lt IE 9]>
<script src="dashboard/assets/global/plugins/respond.min.js"></script>
<script src="dashboard/assets/global/plugins/excanvas.min.js"></script>
<![endif]-->
<script src="dashboard/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="dashboard/assets/global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
<script src="dashboard/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="dashboard/assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="dashboard/assets/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script src="dashboard/assets/global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="dashboard/assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script type="text/javascript" src="dashboard/assets/global/plugins/select2/select2.min.js"></script>
<script src="dashboard/assets/global/scripts/metronic.js" type="text/javascript"></script>
<script src="dashboard/assets/admin/layout/scripts/layout.js" type="text/javascript"></script>
<script src="dashboard/assets/admin/pages/scripts/login.js" type="text/javascript"></script>
<script>
    jQuery(document).ready(function() {
        Metronic.init();
        Layout.init();
        Login.init();

        $('.urs').on('click', function() {
            $.ajax({
                url: 'dashboard/assets/app/texting.php?txt=urs',
                type: 'POST',
                data: {
                    p: $('#forgot_phone').val()
                },
                success: function(data) {
                    $('.forgot-container').html(data);
                },
                error: function() {
                    toastr.error("<strong>Logan says:</strong><br/>Oops..that didnt work properly. Try again?");
                }
            })
        });

        $('.admin-form').validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                email: {
                    required: true,
                    email: true
                },
                password: {
                    required: true
                },
                remember: {
                    required: false
                }
            },

            messages: {
                email: {
                    required: "Email is required."
                },
                password: {
                    required: "Password is required."
                }
            },

            invalidHandler: function(event, validator) { //display error alert on form submit
                $('.alert-danger', $('.login-form')).show();
            },

            highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            success: function(label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },

            errorPlacement: function(error, element) {
                error.insertAfter(element.closest('.input-icon'));
            },

            submitHandler: function(form) {
                form.submit(); // form validation success, call ajax form submit
            }
        });

        $('.admin-form input').keypress(function(e) {
            if (e.which == 13) {
                if ($('.admin-form').validate().form()) {
                    $('.admin-form').submit(); //form validation success, call ajax form submit
                }
                return false;
            }
        });
    });
</script>
</body>
</html>