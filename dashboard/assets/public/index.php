<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 10/18/2017
 * Time: 12:02 PM
 */

include '../app/init.php';
if(isset($_GET['ev'])){
    $event      = mysql_fetch_array(mysql_query("SELECT event_id, event_company_token, event_name, event_status, event_phone, event_truckfee, event_laborrate, event_countyfee, event_comments, event_date_start, event_date_end, event_company_token, event_token, event_laborrate_rate, event_weekend_upcharge_rate, event_user_token FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($_GET['ev'])."'"));
    $user       = mysql_fetch_assoc(mysql_query("SELECT user_fname, user_lname, user_email, user_token, user_creator FROM fmo_users WHERE user_token='".mysql_real_escape_string($event['event_user_token'])."'"));
    $namer       = $event['event_name'];
    $name = companyName($user['user_creator']);
} elseif(isset($_GET['su'])){
    $user       = mysql_fetch_assoc(mysql_query("SELECT user_fname, user_lname, user_email, user_token, user_creator, user_last_ext_location FROM fmo_users WHERE user_token='".mysql_real_escape_string($_GET['uuid'])."'"));
    $namer       = name($user['user_token'])."'s rentals";
    $name = locationNickName($user['user_last_ext_location']);
}


?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8">
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
    <link href="../../assets/global/plugins/pace/themes/pace-theme-minimal.css" rel="stylesheet" type="text/css"/>
    <link href="../../assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="../../assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="../../assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
    <link href="../../assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>
    <link href="../../assets/global/plugins/xeditable/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet" />
    <link href="../../assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
    <link href="../../assets/global/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>
    <link href="../../assets/global/css/plugins.css" rel="stylesheet" type="text/css"/>
    <link href="../../assets/global/plugins/icheck/skins/all.css" rel="stylesheet"/>
    <link href="../../assets/global/plugins/bootstrap-toastr/toastr.min.css" rel="stylesheet" type="text/css"/>
    <link href="../../assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
    <link href="../../assets/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>
    <link href="../../assets/admin/pages/css/error.css" rel="stylesheet" type="text/css"/>
    <link href="../../assets/global/plugins/card-master/dist/card.css" rel="stylesheet" type="text/css"/>
    <link id="style_color" href="../../assets/admin/layout/css/themes/default.css" rel="stylesheet" type="text/css"/>
    <link href="../../assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>
    <link rel="shortcut icon" href="favicon.ico"/>
    <style type="text/css">
        .page-content-wrapper .page-content {
            margin-left: 0px !important;
            margin-top: 0px;
            min-height: 600px;
            padding: 25px 20px 10px 20px;
        }
    </style>
</head>
<body class="page-header-fixed page-quick-sidebar-over-content page-boxed page-sidebar-closed-hide-logo">
<!-- BEGIN HEADER -->
<div class="page-header -i navbar navbar-fixed-top">
    <!-- BEGIN HEADER INNER -->
    <div class="page-header-inner container">
        <!-- BEGIN LOGO -->
        <div class="page-logo" style="color: white; text-transform: uppercase; font-size: 23px; letter-spacing: .01em; word-spacing: 1px; width: auto; margin-top: 7px; font-weight: 300;">
            <?php
            if(!empty($name)){
                $cool = explode(" ", $name);
                $white = true; $red = false;
                foreach($cool as $word){
                    if($white == true){
                        $white = false;
                        $color = "#FFFFFF";
                        $red   = true;
                        echo "<span style='color: ".$color."'>".$word."</span>";
                    } elseif($red == true){
                        $red   = false;
                        $color = "#cb5a5e";
                        $white = true;
                        echo "<span style='color: ".$color."'>".$word."</span>";
                    }
                }
            }
            ?>
        </div>
        <div class="top-menu">
            <ul class="nav navbar-nav pull-right">
                <li class="dropdown dropdown-user dropdown-dark">
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        <img alt="" class="img-circle" src="<?php echo picture($user['user_token']); ?>"/>
                        <span class="username">
					        <?php echo $user['user_fname']." ".$user['user_lname']; ?>
                        </span>
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-default">
                        <li>
                            <a href="www.formoversonly.com/">
                                <i class="icon-key"></i> Log Out
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="clearfix">
</div>
<div class="container">
    <div class="page-container">
        <div class="page-content-wrapper">
            <div class="page-content">
                <h3 class="page-title">
                    <strong><?php echo $namer ?></strong>
                    <span class="hidden-xs">
                        <button class="pull-right btn default red-stripe print"><i class="fa fa-print"></i>&nbsp; Print this</button>
                    </span>
                </h3>
                <div class="row" id="page-content">

                </div>
            </div>
        </div>
    </div>
    <div class="page-footer">
        <div class="page-footer-inner">
            <strong>For Movers Only&trade;</strong> - Moving Management Software | &copy; 2016-2017 <a target="_blank" href="//www.captialkingdom.com">CK, Inc.</a> | <a target="_blank" href="https://www.fmcsa.dot.gov/protect-your-move"><strong>Your rights & responsibilities.</strong></a>
        </div>
        <div class="scroll-to-top">
            <i class="icon-arrow-up"></i>
        </div>
    </div>
    <script src="../../assets/global/plugins/respond.min.js"></script>
    <script src="../../assets/global/plugins/excanvas.min.js"></script>

    <script src="../../assets/global/plugins/jquery.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/bootstrap-daterangepicker/moment.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/datatables/media/js/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/printThis/printThis.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/card-master/dist/jquery.card.js"></script>
    <script src="../../assets/global/plugins/xeditable/bootstrap3-editable/js/bootstrap-editable.js"></script>
    <script src="../../assets/global/plugins/gmaps/gmaps.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/icheck/icheck.min.js"></script>
    <script src="../global/plugins/jsignature/src/jSignature.js"></script>
    <script src="../global/plugins/jsignature/src/plugins/jSignature.CompressorBase30.js"></script>
    <script src="../global/plugins/jsignature/src/plugins/jSignature.CompressorSVG.js"></script>
    <script src="../global/plugins/jsignature/src/plugins/jSignature.UndoButton.js"></script>
    <script async defer src="https://maps.google.com/maps/api/js?v=3.exp&key=AIzaSyBg2MfengOuhtRA-39qVbm8vA7n7pf5ES8&sensor=false" type="text/javascript"></script>
    <script src="../../assets/global/plugins/bootstrap-toastr/toastr.min.js"></script>
    <script src="../../assets/global/scripts/metronic.js" type="text/javascript"></script>
    <script src="../../assets/admin/layout/scripts/layout.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/pace/pace.min.js" type="text/javascript"></script>
    <script src="https://js.stripe.com/v2/" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            Metronic.init(); // init metronic core components
            Layout.init(); // init current layout
            $('.page-content').attr('style', 'margin-left: 0!important;');
            $(document).on('click', '.print', function(p){
                $('#page-content').printThis();
            });
            <?php
            if(isset($_GET['t']) && $_GET['t'] == 'MvP'){
                ?>
                $.ajax({
                    url: 'a/py.php?px=lp&ev=<?php echo $_GET['ev']; ?>',
                    success: function(data) {
                        $('#page-content').html(data);
                        document.title = "Please pay your bill.";
                    },
                    error: function() {
                        toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occurred. Please try again later.");
                    }
                });
                <?php
            } elseif(isset($_GET['e']) && $_GET['e'] == 'EmP'){
                ?>
                $.ajax({
                    url: 'a/es.php?px=lp&v=<?php echo $_GET['v']; ?>&ev=<?php echo $_GET['ev']; ?>&uuid=<?php echo $_GET['uuid']; ?>&n=<?php echo $_GET['n']; ?>',
                    success: function(data) {
                        $('#page-content').html(data);
                    },
                    error: function() {
                        toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occurred. Please try again later.");
                    }
                });
                <?php
            } elseif(isset($_GET['e']) && $_GET['e'] == 'QuT'){
                ?>
                $.ajax({
                    url: 'a/qt.php?px=lp&ev=<?php echo $_GET['ev']; ?>',
                    success: function(data) {
                        $('#page-content').html(data);
                        document.title = "Viewing your quote.";
                    },
                    error: function() {
                        toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occurred. Please try again later.");
                    }
                });
                <?php
            } elseif(isset($_GET['t']) && $_GET['t'] == 'sTr'){
                ?>
                $.ajax({
                    url: 'a/su.php?px=lp&luid=<?php echo $user['user_last_ext_location']; ?>',
                    type: 'POST',
                    data: {
                        uuid: '<?php echo $_GET['uuid']; ?>'
                    },
                    success: function(data) {
                        $('#page-content').html(data);
                        document.title = "Viewing your rentals.";
                    },
                    error: function() {
                        toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occurred. Please try again later.");
                    }
                });
                <?php
            }
            ?>
        });
    </script>
    <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>