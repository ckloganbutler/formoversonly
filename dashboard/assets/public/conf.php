<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<?php
require '../app/init.php';
$event = mysql_fetch_array(mysql_query("SELECT event_location_token, event_user_token, event_name, event_status, event_truckfee, event_laborrate, event_countyfee, event_comments, event_date_start, event_date_end, event_company_token, event_token, event_laborrate_rate, event_weekend_upcharge_rate, event_by_user_token FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($_GET['ev'])."'"));
$location = mysql_fetch_array(mysql_query("SELECT location_quote, location_quote_extra, location_quote_cancel, location_quote_overtime_time, location_quote_overtime_rate, location_quote_oversized_safe, location_quote_oversized_playset, location_quote_oversized_pooltable, location_quote_oversized_piano, location_quote_oversized_hottub, location_quote_packing_small, location_quote_packing_medium, location_quote_packing_large, location_quote_packing_dishpack,  location_quote_packing_wardrobe, location_quote_packing_paper, location_quote_packing_tape,  location_quote_packing_shrinkwrap FROM fmo_locations WHERE location_token='".mysql_real_escape_string($event['event_location_token'])."'"));

?>
<html lang="en">
<!--<![endif]-->
<head>
    <meta charset="utf-8"/>
    <title><?php echo companyName($event['event_company_token']); ?> | <?php echo $event['event_name']; ?></title>
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
    <link href="../global/plugins/dropzone/css/dropzone.css" rel="stylesheet"/>
    <link href="../global/plugins/bootstrap-toastr/toastr.min.css" rel="stylesheet" type="text/css"/>
    <link href="../global/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="../global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css"/>
    <link rel="stylesheet" type="text/css" href="../global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"/>
    <link rel="stylesheet" type="text/css" href="../global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
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
    $name = companyName($event['event_company_token']);
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
    if($_GET['ty'] == 'confirm'){
        if($event['event_status'] == 6 || $event['event_status'] == 2){
            ?>
            <div class="login-form">
                <center>
                    <h3 class="form-title"><i class="fa fa-check" style="font-size: 25px;"></i><strong>Confirmation</strong> submitted.</h3>
                    <small>
                        Thank you for confirming your event, our crews & staff have been notified of your confirmation.
                        <br/><br/>
                        <span class="badge badge-danger"><?php echo $event['event_name']; ?></span> <br/> <br/>
                    </small>
                    <br/>
                </center>
            </div>
            <?php
        } else {
            ?>
            <div class="login-form">
                <h3 class="form-title text-center"><strong><?php echo $event['event_name']; ?></strong><br/><span class="badge badge-danger">Confirmation</span></h3>
                <h5>Date: <strong class="text-danger pull-right"><?php
                        if(date('Y-m-d', strtotime($event['event_date_start'])) == date('Y-m-d', strtotime($event['event_date_end']))) {
                            echo date('M d, Y', strtotime($event['event_date_start']));
                        } else {
                            echo date('M d, Y', strtotime($event['event_date_start'])); ?> - <?php echo date('M d, Y', strtotime($event['event_date_end']));
                        }
                        ?></strong></h5>
                <h5>Truck Fee (<?php echo $event['event_truckfee']; ?> trucks): <strong class="text-danger font-bold pull-right">$<span id="TF"></span></strong></h5>
                <h5>Hourly Rate (<?php echo $event['event_laborrate']; ?> men): <strong class="text-danger font-bold pull-right">$<span id="LR"></span></strong></h5>
                <h5>Travel Fee (<?php echo $event['event_countyfee']; ?> counties): <strong class="text-danger font-bold pull-right">$<span id="CF"></span></strong></h5>
                <hr/>

                <?php
                if(strpos($location['location_quote'], "view_quote_other") !== false){
                    ?>
                    <h4 class="form-title text-center" style="margin-top: 0px;"><strong>Other possible fees</strong> <br/> <span class="badge badge-danger">Optional</span></h4>
                    <h6>Booking Fee: <strong class="text-danger pull-right">$10.00</strong></h6>
                    <h6>Credit Card Processing Fee: <strong class="text-danger pull-right"><?php ?>3%</strong></h6>
                    <?php
                    if(strpos($location['location_quote'], "view_quote_other_extra") !== false){
                        ?>
                        <h6>Extra man/per hour: <strong class="text-danger font-bold pull-right">$<?php echo number_format($event['event_laborrate_rate'], 2); ?> (each)</strong></h6>
                        <?php
                    } if(strpos($location['location_quote'], "view_quote_other_cancel") !== false){
                        ?>
                        <h6>Cancel Charge (< 24hrs notice): <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_cancel'], 2); ?></strong></h6>
                        <?php
                    } if(strpos($location['location_quote'], "view_quote_other_overtime") !== false){
                        ?>
                        <h6>Overtime Rate (after <?php echo $location['location_quote_overtime_time']; ?>pm): <strong class="text-danger pull-right"><?php echo number_format($location['location_quote_overtime_rate'], 1); ?>x current rate</strong></h6>
                        <?php
                    }
                    ?>
                    <h6 class="text-muted text-center margin-top-10">Rates may change if your event date changes.</h6>
                    <hr/>
                    <?php
                }
                if(strpos($location['location_quote'], "view_quote_oversized") !== false){
                    ?>
                    <h4 class="form-title text-center" style="margin-top: 0px;"><strong>Oversized Items</strong> <br/> <span class="badge badge-danger">Call for details</span></h4>
                    <?php
                    if(strpos($location['location_quote'], "view_quote_oversized_safe") !== false){
                        ?>
                        <h6>Safe: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_oversized_safe'], 2); ?></strong></h6>
                        <?php
                    } if(strpos($location['location_quote'], "view_quote_oversized_playset") !== false){
                        ?>
                        <h6>Play Set: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_oversized_playset'], 2); ?></strong></h6>
                        <?php
                    } if(strpos($location['location_quote'], "view_quote_oversized_pooltable") !== false){
                        ?>
                        <h6>Pool Table: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_oversized_pooltable'], 2); ?></strong></h6>
                        <?php
                    } if(strpos($location['location_quote'], "view_quote_oversized_piano") !== false){
                        ?>
                        <h6>Piano: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_oversized_piano'], 2); ?></strong></h6>
                        <?php
                    } if(strpos($location['location_quote'], "view_quote_oversized_hottub") !== false){
                        ?>
                        <h6>Hot Tub: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_oversized_hottub'], 2); ?></strong></h6>
                        <?php
                    }
                    ?>
                    <h6 class="text-muted text-center margin-top-10">Rates only for oversized items you have.</h6>
                    <hr/>
                    <?php
                }
                ?>

                <?php
                if(strpos($location['location_quote'], "view_quote_oversized") !== false){
                    ?>
                    <h4 class="form-title text-center" style="margin-top: 0px;"><strong>Packing Materials</strong> <br/> <span class="badge badge-danger">Optional</span></h4>
                    <?php
                    if(strpos($location['location_quote'], "view_quote_packing_small") !== false){
                        ?>
                        <h6>Small Box: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_packing_small'], 2); ?></strong></h6>
                        <?php
                    } if(strpos($location['location_quote'], "view_quote_packing_medium") !== false){
                        ?>
                        <h6>Medium Box: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_packing_medium'], 2); ?></strong></h6>
                        <?php
                    } if(strpos($location['location_quote'], "view_quote_packing_large") !== false){
                        ?>
                        <h6>Large Box: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_packing_large'], 2); ?></strong></h6>
                        <?php
                    } if(strpos($location['location_quote'], "view_quote_packing_dishpack") !== false){
                        ?>
                        <h6>Dishpack: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_packing_dishpack'], 2); ?></strong></h6>
                        <?php
                    } if(strpos($location['location_quote'], "view_quote_packing_wardrobe") !== false){
                        ?>
                        <h6>Wardrobe: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_packing_wardrobe'], 2); ?> </strong></h6>
                        <?php
                    } if(strpos($location['location_quote'], "view_quote_packing_paper") !== false){
                        ?>
                        <h6>Packing Paper: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_packing_paper'], 2); ?></strong></h6>
                        <?php
                    } if(strpos($location['location_quote'], "view_quote_packing_tape") !== false){
                        ?>
                        <h6>Tape: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_packing_tape'], 2); ?></strong></h6>
                        <?php
                    } if(strpos($location['location_quote'], "view_quote_packing_shrinkwrap") !== false){
                        ?>
                        <h6>Shrinkwrap: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_packing_shrinkwrap'], 2); ?></strong></h6>
                        <?php
                    }
                    ?>
                    <hr/>
                    <?php
                }
                ?>
                <h6>Comments: </h6>
                <strong><?php echo $event['event_comments']; ?></strong>
                <hr/>
                <em>By confirming your move below, you acknowledge the rates listed above.</em>
                <br/> <br/>
                <button type="button" class="btn red confirm btn-block" style="margin: auto !important;" data-ev="<?php echo $event['event_token']; ?>">
                    Confirm my move now! <i class="m-icon-swapright m-icon-white"></i>
                </button>
                <br/><br/>
                <h5 class="text-muted text-center">
                    <?php echo companyName($event['event_company_token']); ?> <br/>
                    <?php echo companyAddress($event['event_company_token']); ?> <br/>
                    <?php echo clean_phone(locationPhone($event['event_location_token'])); ?> - <?php echo clean_phone(companyPhone3($event['event_company_token'])); ?> <br/>
                    <?php echo companyLicenses($event['event_company_token']); ?> <br/>
                </h5>
            </div>
            <?php
        }
    } elseif($_GET['ty'] == 'rates'){
        ?>
        <div class="login-form rates-form">
            <h3 class="form-title text-center">
                <strong><?php echo $event['event_name']; ?></strong>
                <br/>
                <span class="badge badge-danger">Your custom quote</span>
            </h3>
            <h5>Date: <strong class="text-danger pull-right">
                    <?php
                    if(date('M d, Y', strtotime($event['event_date_start'])) == date('M d, Y', strtotime($event['event_date_end']))) {
                        echo date('M d, Y', strtotime($event['event_date_start']));
                    } else {
                        echo date('M d, Y', strtotime($event['event_date_start'])); ?> - <?php echo date('M d, Y', strtotime($event['event_date_end']));
                    }
                    ?></strong></h5>
            <h5>Truck Fee (<?php echo $event['event_truckfee']; ?> trucks): <strong class="text-danger font-bold pull-right">$<span id="TF"></span></strong></h5>
            <h5>Hourly Rate (<?php echo $event['event_laborrate']; ?> men): <strong class="text-danger font-bold pull-right">$<span id="LR"></span></strong></h5>
            <h5>Travel Fee (<?php echo $event['event_countyfee']; ?> counties): <strong class="text-danger font-bold pull-right">$<span id="CF"></span></strong></h5>
            <hr/>
            <?php
            if(strpos($location['location_quote'], "view_quote_other") !== false){
                ?>
                <h4 class="form-title text-center" style="margin-top: 0px;"><strong>Other possible fees</strong> <br/> <span class="badge badge-danger">Optional</span></h4>
                <h6>Booking Fee: <strong class="text-danger pull-right">$10.00</strong></h6>
                <h6>Credit Card Processing Fee: <strong class="text-danger pull-right"><?php ?>3%</strong></h6>
                <?php
                if(strpos($location['location_quote'], "view_quote_other_extra") !== false){
                    ?>
                    <h6>Extra man/per hour: <strong class="text-danger font-bold pull-right">$<?php echo number_format($event['event_laborrate_rate'], 2); ?> (each)</strong></h6>
                    <?php
                } if(strpos($location['location_quote'], "view_quote_other_cancel") !== false){
                    ?>
                    <h6>Cancel Charge (< 24hrs notice): <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_cancel'], 2); ?></strong></h6>
                    <?php
                } if(strpos($location['location_quote'], "view_quote_other_overtime") !== false){
                    ?>
                    <h6>Overtime Rate (after <?php echo $location['location_quote_overtime_time']; ?>pm): <strong class="text-danger pull-right"><?php echo number_format($location['location_quote_overtime_rate'], 1); ?>x current rate</strong></h6>
                    <?php
                }
                ?>
                <h6 class="text-muted text-center margin-top-10">Rates may change if your event date changes.</h6>
                <hr/>
                <?php
            }
            if(strpos($location['location_quote'], "view_quote_oversized") !== false){
                ?>
                <h4 class="form-title text-center" style="margin-top: 0px;"><strong>Oversized Items</strong> <br/> <span class="badge badge-danger">Call for details</span></h4>
                <?php
                if(strpos($location['location_quote'], "view_quote_oversized_safe") !== false){
                    ?>
                    <h6>Safe: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_oversized_safe'], 2); ?></strong></h6>
                    <?php
                } if(strpos($location['location_quote'], "view_quote_oversized_playset") !== false){
                    ?>
                    <h6>Play Set: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_oversized_playset'], 2); ?></strong></h6>
                    <?php
                } if(strpos($location['location_quote'], "view_quote_oversized_pooltable") !== false){
                    ?>
                    <h6>Pool Table: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_oversized_pooltable'], 2); ?></strong></h6>
                    <?php
                } if(strpos($location['location_quote'], "view_quote_oversized_piano") !== false){
                    ?>
                    <h6>Piano: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_oversized_piano'], 2); ?></strong></h6>
                    <?php
                } if(strpos($location['location_quote'], "view_quote_oversized_hottub") !== false){
                    ?>
                    <h6>Hot Tub: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_oversized_hottub'], 2); ?></strong></h6>
                    <?php
                }
                ?>
                <h6 class="text-muted text-center margin-top-10">Rates only for oversized items you have.</h6>
                <hr/>
                <?php
            }
            ?>

            <?php
            if(strpos($location['location_quote'], "view_quote_oversized") !== false){
                ?>
                <h4 class="form-title text-center" style="margin-top: 0px;"><strong>Packing Materials</strong> <br/> <span class="badge badge-danger">Optional</span></h4>
                <?php
                if(strpos($location['location_quote'], "view_quote_packing_small") !== false){
                    ?>
                    <h6>Small Box: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_packing_small'], 2); ?></strong></h6>
                    <?php
                } if(strpos($location['location_quote'], "view_quote_packing_medium") !== false){
                    ?>
                    <h6>Medium Box: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_packing_medium'], 2); ?></strong></h6>
                    <?php
                } if(strpos($location['location_quote'], "view_quote_packing_large") !== false){
                    ?>
                    <h6>Large Box: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_packing_large'], 2); ?></strong></h6>
                    <?php
                } if(strpos($location['location_quote'], "view_quote_packing_dishpack") !== false){
                    ?>
                    <h6>Dishpack: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_packing_dishpack'], 2); ?></strong></h6>
                    <?php
                } if(strpos($location['location_quote'], "view_quote_packing_wardrobe") !== false){
                    ?>
                    <h6>Wardrobe: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_packing_wardrobe'], 2); ?> </strong></h6>
                    <?php
                } if(strpos($location['location_quote'], "view_quote_packing_paper") !== false){
                    ?>
                    <h6>Packing Paper: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_packing_paper'], 2); ?></strong></h6>
                    <?php
                } if(strpos($location['location_quote'], "view_quote_packing_tape") !== false){
                    ?>
                    <h6>Tape: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_packing_tape'], 2); ?></strong></h6>
                    <?php
                } if(strpos($location['location_quote'], "view_quote_packing_shrinkwrap") !== false){
                    ?>
                    <h6>Shrinkwrap: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_packing_shrinkwrap'], 2); ?></strong></h6>
                    <?php
                }
                ?>
                <hr/>
                <?php
            }
            ?>
            <h6>Comments: </h6>
            <strong><?php echo $event['event_comments']; ?></strong>
            <hr/>

            <blockquote class="hero">
                <p>
                    <em>Think you're ready to continue? It's easy! Book your move using our easy tool now. You can call back anytime and request me for further assistance</em>
                </p>
                <small><strong>Your CSR</strong>, <?php echo name($event['event_by_user_token']); ?></small>
            </blockquote>
            <br/> <br/>
            <button type="button" class="btn red book-move btn-block" style="margin: auto !important;">
                Book your move now! <i class="m-icon-swapright m-icon-white"></i>
            </button>
            <br/><br/>
             <h5 class="text-muted text-center">
                 <?php echo companyName($event['event_company_token']); ?> <br/>
                 <?php echo companyAddress($event['event_company_token']); ?> <br/>
                 <?php echo clean_phone(locationPhone($event['event_location_token'])); ?> - <?php echo clean_phone(companyPhone3($event['event_company_token'])); ?> <br/>
                 <?php echo companyLicenses($event['event_company_token']); ?> <br/>
             </h5>
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
<script src="../global/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
<script src="../global/plugins/moment.min.js"></script>
<script src="../global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="../global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script src="../global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="../global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="../global/plugins/bootstrap-toastr/toastr.min.js"></script
<script type="text/javascript" src="../global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src=../global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="../global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="../global/plugins/dropzone/dropzone.js"></script>
<script type="text/javascript" src="../global/plugins/select2/select2.min.js"></script>
<script src="../global/scripts/metronic.js" type="text/javascript"></script>
<script src="../admin/layout/scripts/layout.js" type="text/javascript"></script>
<script src="../admin/pages/scripts/login.js" type="text/javascript"></script>
<script src="../admin/pages/scripts/ui-toastr.js"></script>
<script type="text/javascript" src="../global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js"></script>
<script type="text/javascript" src="../global/plugins/jquery.input-ip-address-control-1.0.min.js"></script>
<script src="../global/plugins/xeditable/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script>
    jQuery(document).ready(function() {
        Metronic.init();
        Layout.init();
        Login.init();
        UIToastr.init();
        <?php
        if($_GET['ty'] == 'confirm'){
            if($event['event_status'] == 6 || $event['event_status'] == 2){
            ?>

            <?php
            } else {
            ?>
            var a = <?php echo $event['event_truckfee']; ?>;
            var b = <?php echo $event['event_laborrate']; ?>;
            var c = <?php echo $event['event_countyfee']; ?>;
            $.ajax({
                url: '../app/api/event.php?type=math&ev=<?php echo $event['event_token']; ?>',
                type: 'POST',
                data: {
                    a: a,
                    b: b,
                    c: c
                },
                success: function(d){
                    var e = JSON.parse(d);
                    $("#TF").html(e.truck_fee);
                    $("#LR").html(e.total_labor_rate);
                    $("#CF").html(e.county_fee);
                },
                error: function(e){

                }
            });
            $('.confirm').on('click', function(){
                var ev = $(this).attr('data-ev');
                $.ajax({
                    url: '../app/update_settings.php?update=event_fly&self=true&uuid=<?php echo $event['event_user_token']; ?>',
                    type: 'POST',
                    data: {
                        name: 'event_status',
                        value: 6,
                        pk: ev
                    },
                    success: function(s){
                        toastr.success("<strong>Logan says:</strong><br/>Your move has been confirmed and crews have been notified.");
                        location.reload();
                    },
                    error: function(s){
                        toastr.error("<strong>Logan says:</strong><br/>Something went wrong. Try again? Error #100392")
                    }
                });
            });
            <?php
            }
        } elseif($_GET['ty'] == 'rates'){
            ?>
            var a = <?php echo $event['event_truckfee']; ?>;
            var b = <?php echo $event['event_laborrate']; ?>;
            var c = <?php echo $event['event_countyfee']; ?>;
            $.ajax({
                url: '../app/api/event.php?type=math&ev=<?php echo $event['event_token']; ?>',
                type: 'POST',
                data: {
                    a: a,
                    b: b,
                    c: c
                },
                success: function(d){
                    var e = JSON.parse(d);
                    $("#TF").html(e.truck_fee);
                    $("#LR").html(e.total_labor_rate);
                    $("#CF").html(e.county_fee);
                },
                error: function(e){

                }
            });

            $('.book-move').click(function(){
                $.ajax({
                    url: '../app/api/event.php?type=book_now&ev=<?php echo $event['event_token']; ?>',
                    type: 'POST',
                    success: function(d){
                        $('.rates-form').html(d);
                    },
                    error: function(e){

                    }
                });
            });

            $(document).on('click', '.edit_inf', function(f) {
                var inf = $(this).attr('data-edit');
                f.stopPropagation();
                $('#'+inf).editable('toggle');
            });
            <?php
        }
        ?>
    });
</script>
</body>
</html>