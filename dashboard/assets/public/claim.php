<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<?php
require '../app/init.php';
$event = mysql_fetch_array(mysql_query("SELECT event_name FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($_GET['ev'])."'"));
?>
<html lang="en">
<!--<![endif]-->
<head>
    <meta charset="utf-8"/>
    <title>Claim form | <?php echo $event['event_name']; ?></title>
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
<div class="logo">
    <a href="">
        <img src="../admin/layout/img/logo-big.png" alt=""/>
    </a>
</div>
<div class="menu-toggler sidebar-toggler">
</div>
<div class="content">
    <div class="login-form">
        <h3 class="form-title"><strong>Claim</strong> form <span class="badge badge-danger"><?php echo $event['event_name']; ?></span></h3>
        <form class="claim-form" action="../app/login.php?t=aXn" method="POST">
            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">Item that was damaged</label>
                <div class="input-icon">
                    <i class="fa fa-chain-broken"></i>
                    <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Item that was damaged" name="item"/>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">Was the item padded?</label>
                <div class="input-icon">
                    <i class="fa fa-hospital-o"></i>
                    <select class="form-control placeholder-no-fix" name="padded">
                        <option disabled selected value="">Was the item padded?</option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">Approximate Weight (LBs)</label>
                <div class="input-icon">
                    <i class="fa fa-cube"></i>
                    <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Approximate Weight (Lbs)" name="weight"/>
                </div>
            </div>
            <div class="form-group">
                <textarea class="form-control placeholder-no-fix" style="height: 150px;border-left: 2px solid #c23f44!important" placeholder="Extra comments" name="comments"></textarea>
            </div>
            <div class="form-actions">
                <label class="checkbox">
                    <input type="checkbox" name="remember" value="1"/> Urgent claim </label>
                <button type="submit" class="btn red pull-right">
                    Submit <i class="m-icon-swapright m-icon-white"></i>
                </button>
            </div>
        </form>
    </div>
    <div class="login-form" id="success" style="display: none;">
        <center>
            <h3 class="form-title"><i class="fa fa-check" style="font-size: 25px;"></i><strong>Claim</strong> submitted.</h3>
            <small>You will be contacted by a customer service representative regarding this claim. <br/><br/> <span class="badge badge-success"><?php echo $event['event_name']; ?></span></small> <br/><br/><br/>
        </center>
    </div>
</div>
<!-- END LOGIN -->
<!-- BEGIN COPYRIGHT -->
<div class="copyright">
    2017 &copy; For Movers Only | <strong>Powered by Logan</strong> <br/>  <small><strong>This project is in beta. If you make an account, expect issues.</strong></small>
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

        $('.claim-form').validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                item: {
                    required: true
                },
                padded: {
                    required: true
                },
                weight: {
                    required: true
                }
            },

            messages: {
                item: {
                    required: "Please describe the item."
                },
                padded: {
                    required: "Please select yes or no."
                },
                weight: {
                    required: "Please enter item weight."
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
                    url: '../app/add_setting.php?setting=claim&ev=<?php echo $_GET['ev']; ?>',
                    type: 'POST',
                    data: $('.claim-form').serialize(),
                    success: function(c){
                        $('.login-form').hide();
                        $('#success').show();
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