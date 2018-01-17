<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<?php
require '../app/init.php';
$deposit = mysql_fetch_array(mysql_query("SELECT deposit_id, deposit_amount, deposit_token, deposit_teller, deposit_comments, deposit_company_token, deposit_by_user_token, deposit_timestamp FROM fmo_locations_deposits WHERE deposit_token='".mysql_real_escape_string($_GET['dpt'])."'"));
?>
<html lang="en">
<!--<![endif]-->
<head>
    <meta charset="utf-8"/>
    <title>Upload Teller Ticket</title>
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
<div class="logo" style="color: white; text-transform: uppercase; font-size: 23px; letter-spacing: .01em; word-spacing: 1px; width: auto; margin-top: 7px; font-weight: 300; margin-bottom: 0px;">
    <?php
    $name = companyName($deposit['deposit_company_token']);
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
    <?php
    if(!isset($_GET['dpt']) && empty($_GET['dpt'])){
        ?>
        <div class="login-form">
            <center>
                <h3><i class="fa fa-times"  style="font-size: 25px;"></i> <strong>You are not authorized to view this page.</strong></h3>
            </center>
        </div>
        <?php
    } elseif(!empty($deposit['deposit_teller'])){
        ?>
        <div class="login-form">
            <center>
                <h3 class="form-title"><i class="fa fa-check" style="font-size: 25px;"></i> <strong>Deposit</strong> updated.</h3>
                <p>
                    Thanks for submitting that ticket, <strong><?php echo name($deposit['deposit_by_user_token']); ?></strong>, here is a preview of what will be saved permanently.
                </p>
                <p><span class="text-warning">*</span> NOTE: The original record of this deposit was for <span class="text-warning">$<?php echo number_format($deposit['deposit_amount'], 2); ?></span></p>
                    <div class="well">
                        <img class="img-thumbnail" src="<?php echo $deposit['deposit_teller']; ?>"/>
                    </div>
                <p>
                    <strong>Comments: </strong> <br/>
                    <?php echo $deposit['deposit_comments']; ?>
                </p>
                <br/>
            </center>
        </div>
        <?php
    } else {
        ?>
        <div class="login-form">
            <h3 class="form-title"><strong>Deposit #<?php echo $deposit['deposit_id']; ?></strong><br/><span class="badge badge-danger">Upload teller ticket</span></h3>
            <form class="deposit-form">
                <h3>Whats this?</h3>
                <p>Recently, you <strong class="text-danger"><?php echo name($deposit['deposit_by_user_token']); ?></strong>, recorded that you made a deposit. We need to verify that you actually made this deposit by taking an image of the teller ticket that should've been given to you at the bank. <br/></p>
                <p><span class="text-warning">*</span> NOTE: The original record of this deposit was for <span class="text-warning">$<?php echo number_format($deposit['deposit_amount'], 2); ?></span></p>
                <div class="form-group">
                    <label class="control-label visible-ie8 visible-ie9">Image of teller ticket</label>
                    <div class="input-icon">
                        <i class="fa fa-paperclip"></i>
                        <input class="form-control placeholder-no-fix" type="file" autocomplete="off" placeholder="" name="ticket"/>
                    </div>
                </div>
                <div class="form-group">
                    <textarea class="form-control placeholder-no-fix" style="height: 150px;border-left: 2px solid #c23f44!important" placeholder="Extra comments" name="comments"></textarea>
                </div>
                <button type="submit" class="btn red pull-right smt_btn">
                    Submit <i class="m-icon-swapright m-icon-white"></i>
                </button>
                <br/><br/><br/>
                <p><span class="text-danger">*</span> NOTE:  <strong>If this is not you, please do not use this page. Your IP is being logged.</strong></p>
            </form>
        </div>
        <?php
    }
    ?>
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

        $('.deposit-form').validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                ticket: {
                    required: true
                }
            },

            messages: {
                ticket: {
                    required: "You need to select or take an image."
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
                $('#smt_btn').prop('disabled', true);
                $.ajax({
                    url: '../app/add_setting.php?setting=teller&dpt=<?php echo $deposit['deposit_token']; ?>',
                    type: 'POST',
                    data:  new FormData($('.deposit-form')[0]),
                    dataType: 'json',
					processData: false,
					contentType: false,
					encode: true,
                    success: function(c){
						console.log(c);
                        window.location.reload();
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