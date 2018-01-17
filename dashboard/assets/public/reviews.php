<?php
/**
 * Created by PhpStorm.
 * User: gameroom
 * Date: 1/17/2018
 * Time: 4:00 AM
 */
include '../app/init.php';

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
    <link href="../../assets/global/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>
    <link href="../../assets/global/css/plugins.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css">
    <link href="../../assets/global/plugins/bootstrap-toastr/toastr.min.css" rel="stylesheet" type="text/css"/>
    <link href="../../assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
    <link href="../../assets/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>
    <link id="style_color" href="../../assets/admin/layout/css/themes/default.css" rel="stylesheet" type="text/css"/>
    <link href="../../assets/admin/pages/css/todo.css" rel="stylesheet" type="text/css">
    <link href="../../assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>
    <link href="../../assets/admin/pages/css/portfolio.css" rel="stylesheet" type="text/css">
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
            $name = companyName($_GET['cuid']);
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
    </div>
</div>
<div class="clearfix">
</div>
<div class="container">
    <div class="page-container">
        <div class="page-content-wrapper">
            <div class="page-content"  style="margin-left: 0px !important;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet light">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="icon-paper-plane bold"></i>
                                    <span class="caption-subject bold font-red uppercase">
                                        Reviews from customers
                                    </span>
                                    <span class="caption-helper">from real people just like you.</span>
                                </div>
                            </div>
                            <div class="portlet-body" id="page">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3 class="text-center">Average: <span id="avg"></span><br/>

                                        </h3>
                                        <br/>
                                        <div class="rateTheDash" style="margin: auto!important;">

                                        </div>
                                    </div>
                                </div>
                                <br/>
                                <hr/>
                                <div class="todo-tasklist">
                                <?php
                                $ratings = 0; $rating_avg = 0; $rating_amt = 0;
                                $reviews = mysql_query("SELECT review_rating, review_id, review_comments, review_event_token, review_location_token, review_status, review_timestamp FROM fmo_locations_events_reviews WHERE review_company_token='".$_GET['cuid']."' AND (review_status=1) ORDER BY review_timestamp DESC");
                                if(mysql_num_rows($reviews)) {
                                    while ($review = mysql_fetch_assoc($reviews)) {
                                    $ratings += $review['review_rating'];
                                    $rating_amt++;
                                    $event = mysql_fetch_array(mysql_query("SELECT event_user_token, event_name FROM fmo_locations_events WHERE event_token='" . mysql_real_escape_string($review['review_event_token']) . "'"));
                                    ?>
                                    <div class="review_<?php echo $review['review_id']; ?> row">
                                        <div class="col-md-4" style="padding-left: 0;">
                                            <div class="portfolio-text">
                                                <img src="<?php echo picture($event_review['even_user_token']); ?>"
                                                     alt="" height="81px" width="81px">
                                                <div class="portfolio-text-info text-center">
                                                    <div class="rateYoDash"
                                                         data-rateyo-rating="<?php echo $review['review_rating']; ?>" style="margin: auto!important;"></div>
                                                    <h6 style="margin-top: 5px;"><strong>CUSTOMER:</strong> <?php echo name($event['event_user_token']); ?><br/>
                                                        <strong>LOCATION:</strong> <?php echo locationName($review['review_location_token']); ?><br/>
                                                        <strong>DATE:</strong> <?php echo date('m/d/Y', strtotime($review['review_timestamp'])); ?></h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-8 portfolio-stat"
                                             style="margin-top: 8px;">
                                            <div class="portfolio-info"
                                                 style="text-transform: none !important;">
                                                <?php echo $review['review_comments']; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    }
                                    $rating_avg = $ratings / $rating_amt;
                                }
                                ?>
                            </div>
                        </div>
                    </div>
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
    <script src="../../assets/global/plugins/bootstrap-daterangepicker/moment.min.js" type="text/javascript" ></script>
    <script src="../../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js" type="text/javascript"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>
    <script src="../../assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js" type="text/javascript"></script>
    <script src="../../assets/global/scripts/metronic.js" type="text/javascript"></script>
    <script src="../../assets/admin/layout/scripts/layout.js" type="text/javascript"></script>
    <script src="../../assets/global/plugins/pace/pace.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            Metronic.init(); // init metronic core components
            Layout.init(); // init current layout
            $('.rateYoDash').rateYo({
                halfStar: true,
                readOnly: true
            });
            $('.rateTheDash').rateYo({
                halfStar: true,
                readOnly: true,
                rating: <?php echo $rating_avg; ?>
            });
            $('#avg').html('<?php echo number_format($rating_avg, 1); ?>');
        });
    </script>
    <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>