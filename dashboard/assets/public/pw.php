<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<?php
require '../app/init.php';
$user = mysql_fetch_array(mysql_query("SELECT user_token, user_creator FROM fmo_users WHERE user_token='".mysql_real_escape_string($_GET['uuid'])."'"));
?>
<html lang="en">
<!--<![endif]-->
<head>
    <meta charset="utf-8"/>
    <title>Password Reset | <?php echo name($user['user_token']); ?></title>
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
    <link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="../global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
    <link href="../global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css">
    <link href="../global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
    <link href="../global/plugins/select2/select2.css" rel="stylesheet" type="text/css"/>
    <link href="../admin/pages/css/login3.css" rel="stylesheet" type="text/css"/>
    <link href="../global/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>
    <link href="../global/css/plugins.css" rel="stylesheet" type="text/css"/>
    <link href="../admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>
    <link href="../admin/layout/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/>
    <link href="../admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>

    <link rel="shortcut icon" href="../../favicon.ico"/>
</head>
<body class="login">
<div class="logo" style="color: white; text-transform: uppercase; font-size: 23px; letter-spacing: .01em; word-spacing: 1px; width: auto; margin-top: 7px; font-weight: 300; margin-bottom: 0px;">
    <?php
    $name = companyName($user['user_creator']);
    if(!empty($name)){
        $cool = explode(" ", $name);
        $white = true; $red = false;
        foreach($cool as $word){
            if($white == true){
                echo "<span style='color: #FFFFFF'>".$word."</span>";
                $white = false;
                $red   = true;
            } elseif($red == true){
                echo "<span style='color: #cb5a5e'>".$word."</span>";
                $red   = false;
                $white = true;
            }
        }
    } else {

    }
    ?>
</div>
<div class="menu-toggler sidebar-toggler">
</div>
<div class="content">
    <div class="login-form">
        <h3 class="form-title text-center"><strong><?php echo name($user['user_token']); ?></strong> <br/> <span class="badge badge-danger">Password Reset</span></h3>
        <form class="password-form" method="POST">
            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">New password</label>
                <div class="input-icon">
                    <i class="fa fa-user-secret"></i>
                    <input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="New password" name="password" id="password"/>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">New password confirmation</label>
                <div class="input-icon">
                    <i class="fa fa-exclamation-circle"></i>
                    <input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Confirm new password." name="passwordConf"/>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn red pull-right">
                    Submit <i class="m-icon-swapright m-icon-white"></i>
                </button><br/><br/>
            </div>

        </form>
        <div class="forget-password">
            <h4><strong>Wait</strong>, what is this?</h4>
            <p>
                <strong><?php echo name($_GET['b']); ?></strong> sent you a password reset. Here, you will make a new password then <a>login</a>.
            </p>
        </div>
    </div>
</div>
<!-- END LOGIN -->
<!-- BEGIN COPYRIGHT -->
<div class="copyright">
    2017 &copy; For Movers Only | <strong>Powered by Logan</strong> <br/>  <a target="_blank" href="https://www.fmcsa.dot.gov/protect-your-move"><strong>Your rights & responsibilities.</strong></a>
</div>
<!--[if lt IE 9]>
<script src="../global/plugins/respond.min.js"></script>
<script src="../global/plugins/excanvas.min.js"></script>
<![endif]-->
<script src="../global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="../global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
<script src="../global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="../global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script src="../global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="../global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../global/plugins/select2/select2.min.js"></script>
<script src="../global/scripts/metronic.js" type="text/javascript"></script>
<script src="../admin/layout/scripts/layout.js" type="text/javascript"></script>
<script src="../admin/pages/scripts/login.js" type="text/javascript"></script>
<script>
    jQuery(document).ready(function() {
        Metronic.init();
        Layout.init();
        Login.init();

        $('.password-form').validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                password: {
                    required: true
                },
                passwordConf: {
                    equalTo: "#password"
                }
            },

            messages: {
                password: {
                    required: "Empty passwords are not secure."
                },
                passwordConf: {
                    equalTo: "Passwords do not match."
                }
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
                $.ajax({
                    url: '../app/update_settings.php?setting=pw&uuid=<?php echo $_GET['uuid']; ?>&b=<?php echo $_GET['b']; ?>',
                    type: 'POST',
                    data: $('.password-form').serialize(),
                    success: function(c){
                        $('.password-form').html(c);
                    },
                    error: function(c){

                    }
                })
            }
        });
    });
</script>
</body>
</html>