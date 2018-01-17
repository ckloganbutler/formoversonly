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
    $exp      = mysql_fetch_array(mysql_query("SELECT user_license_exp, user_email FROM fmo_users WHERE user_company_token='".mysql_real_escape_string($_SESSION['cuid'])."'"));

    if($exp['user_license_exp'] == '0000-00-00 00:00:00'){
        $license = date('Y-m-d G:i:s', strtotime('today -1 days'));
    } else {
        $license = $exp['user_license_exp'];
    }
    ?>
    <div class="page-content">
        <h3 class="page-title">
            <strong>Subscriptions</strong>
        </h3>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light">
                    <div class="portlet-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab">
                                <h3 style="margin-top: 0px;">
                                    <strong>FORMOVERSONLY&trade; Subscriptions</strong>
                                    <small class="pull-right text-danger" id="stat" style="margin-top: 5px;"></small></h3>
                                <hr/>
                                <div class="row margin-bottom-40 margin-top-20">
                                    <div class="col-md-12">
                                        <h1 class="text-center" id="countdown"></h1>
                                        <h3 class="text-center">Your license expires on <strong class="text-danger"><?php echo date('M d, Y', strtotime($license)); ?></strong> at <strong class="text-danger"><?php echo date('g:ia', strtotime($exp['user_license_exp'])); ?></strong>. Once your license expires, you will not be able to book new moves into our system. However, you will still have access to all the information you have generated through our system--we won't keep that from you.<br/><br/><strong class="bold text-info"><i class="fa fa-question-circle" style="font-size: 28px;"></i> Did you know?</strong> <strong>Each booking fee you process through our system automatically credits you +3 days to any license you have!</strong></h3>
                                    </div>
                                </div>
                                <div class="row margin-bottom-40">
                                    <!-- Pricing -->
                                    <div class="col-md-3">
                                        <div class="pricing pricing-active hover-effect">
                                            <div class="pricing-head  pricing-head-active">
                                                <h3>FORMOVERSONLY&trade; <strong>Standard License</strong>
                                                    <span>monthly subscription/payment plan</span>
                                                </h3>
                                                <h4>
                                                    <i>$</i>99<i>.99</i>
                                                    <span> Per Month </span>
                                                </h4>
                                            </div>
                                            <ul class="pricing-content list-unstyled">
                                                <li>
                                                    <i class="fa fa-building"></i> <strong>1</strong> Location
                                                </li>
                                                <li>
                                                    <i class="fa fa-tags"></i> <strong>Unlimited Event Data</strong>
                                                </li>
                                                <li>
                                                    <i class="fa fa-user-plus"></i> <strong>Unlimited Customer Data</strong> (and <strong>Repeat Clients</strong>)
                                                </li>
                                                <li>
                                                    <i class="fa fa-users"></i> <strong>Unlimited Employee Data</strong>
                                                </li>
                                                <li>
                                                    <i class="fa fa-paperclip"></i> <strong>Data Tracking & Reporting</strong>
                                                </li>
                                                <li>
                                                    <i class="fa fa-star"></i> <strong>24/7 Support</strong>
                                                </li>
                                                <li>
                                                    <i class="fa fa-dollar"></i> <strong>mPay</strong>&trade;
                                                </li>
                                                <li>
                                                    <i class="fa fa-mobile-phone"></i> <strong>mAlert</strong>&trade;
                                                </li>
                                                <li>
                                                    <i class="fa fa-external-link-square"></i> <strong>mAccess</strong>&trade;
                                                </li>
                                            </ul>
                                            <div class="pricing-footer text-left">
                                                <a class="btn red sub" data-toggle="modal" href="#subs" data-type="standard" data-title="Standard" data-i="STD" data-price="$99.99/Month">
                                                    Sign up! <i class="m-icon-swapright m-icon-white"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="pricing hover-effect">
                                            <div class="pricing-head">
                                                <h3>FORMOVERSONLY&trade; <strong>Standard+ License</strong>
                                                    <span>yearly subscription/payment plan </span>
                                                </h3>
                                                <h4>
                                                    <i>$</i>999<i>.99</i>
                                                    <span>Per Year </span>
                                                </h4>
                                            </div>
                                            <ul class="pricing-content list-unstyled">
                                                <li>
                                                    <i class="fa fa-building"></i> <strong>1</strong> Location
                                                </li>
                                                <li>
                                                    <i class="fa fa-tags"></i> <strong>Unlimited Event Data</strong>
                                                </li>
                                                <li>
                                                    <i class="fa fa-user-plus"></i> <strong>Unlimited Customer Data</strong> (and <strong>Repeat Clients</strong>)
                                                </li>
                                                <li>
                                                    <i class="fa fa-users"></i> <strong>Unlimited Employee Data</strong>
                                                </li>
                                                <li>
                                                    <i class="fa fa-paperclip"></i> <strong>Data Tracking & Reporting</strong>
                                                </li>
                                                <li>
                                                    <i class="fa fa-star"></i> <strong>24/7 Support</strong>
                                                </li>
                                                <li>
                                                    <i class="fa fa-dollar"></i> <strong>mPay</strong>&trade;
                                                </li>
                                                <li>
                                                    <i class="fa fa-mobile-phone"></i> <strong>mAlert</strong>&trade;
                                                </li>
                                                <li>
                                                    <i class="fa fa-external-link-square"></i> <strong>mAccess</strong>&trade;
                                                </li>
                                            </ul>
                                            <div class="pricing-footer">
                                                <p>

                                                </p>
                                                <a class="btn red sub" data-toggle="modal" href="#subs" data-type="standard_plus" data-title="Standard+" data-i="STDPLUS" data-price="$999.99/Year">
                                                    Sign up <i class="m-icon-swapright m-icon-white"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="pricing pricing-active hover-effect">
                                            <div class="pricing-head">
                                                <h3>FORMOVERSONLY&trade; <strong>Enterprise License</strong>
                                                    <span>monthly subscription/payment plan </span>
                                                </h3>
                                                <h4>
                                                    <i>$</i>149<i>.99</i>
                                                    <span>Per Month </span>
                                                </h4>
                                            </div>
                                            <ul class="pricing-content list-unstyled">
                                                <li>
                                                    <i class="fa fa-building"></i> <strong>Unlimited</strong> Locations
                                                </li>
                                                <li>
                                                    <i class="fa fa-tags"></i> <strong>Unlimited Event Data</strong>
                                                </li>
                                                <li>
                                                    <i class="fa fa-user-plus"></i> <strong>Unlimited Customer Data</strong> (and <strong>Repeat Clients</strong>)
                                                </li>
                                                <li>
                                                    <i class="fa fa-users"></i> <strong>Unlimited Employee Data</strong>
                                                </li>
                                                <li>
                                                    <i class="fa fa-paperclip"></i> <strong>Data Tracking & Reporting</strong>
                                                </li>
                                                <li>
                                                    <i class="fa fa-star"></i> <strong>24/7 Support</strong>
                                                </li>
                                                <li>
                                                    <i class="fa fa-dollar"></i> <strong>mPay</strong>&trade;
                                                </li>
                                                <li>
                                                    <i class="fa fa-mobile-phone"></i> <strong>mAlert</strong>&trade;
                                                </li>
                                                <li>
                                                    <i class="fa fa-external-link-square"></i> <strong>mAccess</strong>&trade;
                                                </li>
                                            </ul>
                                            <div class="pricing-footer">
                                                <p>

                                                </p>
                                                <a class="btn red sub" data-toggle="modal" href="#subs" data-type="enterprise" data-title="Enterprise" data-i="ENTPRI" data-price="$149.99/Month">
                                                    Sign up! <i class="m-icon-swapright m-icon-white"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="pricing hover-effect">
                                            <div class="pricing-head">
                                                <h3>FORMOVERSONLY&trade; <strong>Enterprise+ License</strong>
                                                    <span>yearly subscription/payment plan </span>
                                                </h3>
                                                <h4>
                                                    <i>$</i>1,499<i>.99</i>
                                                    <span>Per Year </span>
                                                </h4>
                                            </div>
                                            <ul class="pricing-content list-unstyled">
                                                <li>
                                                    <i class="fa fa-building"></i> <strong>Unlimited</strong> Locations
                                                </li>
                                                <li>
                                                    <i class="fa fa-tags"></i> <strong>Unlimited Event Data</strong>
                                                </li>
                                                <li>
                                                    <i class="fa fa-user-plus"></i> <strong>Unlimited Customer Data</strong> (and <strong>Repeat Clients</strong>)
                                                </li>
                                                <li>
                                                    <i class="fa fa-users"></i> <strong>Unlimited Employee Data</strong>
                                                </li>
                                                <li>
                                                    <i class="fa fa-paperclip"></i> <strong>Data Tracking & Reporting</strong>
                                                </li>
                                                <li>
                                                    <i class="fa fa-star"></i> <strong>24/7 Support</strong>
                                                </li>
                                                <li>
                                                    <i class="fa fa-dollar"></i> <strong>mPay</strong>&trade;
                                                </li>
                                                <li>
                                                    <i class="fa fa-mobile-phone"></i> <strong>mAlert</strong>&trade;
                                                </li>
                                                <li>
                                                    <i class="fa fa-external-link-square"></i> <strong>mAccess</strong>&trade;
                                                </li>
                                            </ul>
                                            <div class="pricing-footer">
                                                <p>

                                                </p>
                                                <a class="btn red sub" data-toggle="modal" href="#subs" data-type="enterprise_plus" data-title="Enterprise+" data-i="ENTPRIPLUS" data-price="$1,499.99/Year">
                                                    Sign up! <i class="m-icon-swapright m-icon-white"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="text-left">
                                            <strong><span class="text-danger">*</span>mPay:</strong> Simple text-messaged based tool used to request payment from customers using credit card.<br/>
                                            <strong><span class="text-danger">*</span>mAlert:</strong> Simple text-messaged based tool used to alert employees, administrators, and other crewmen of software activity relating to them.<br/>
                                            <strong><span class="text-danger">*</span>mAccess:</strong> Simple employee/role based access control for all users under your companies account.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade bs-modal-lg" id="subs" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content box red">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h3 class="modal-title font-bold"><strong class="sub_title"></strong> license purchase for <?php echo companyName($_SESSION['cuid']); ?></h3>
                </div>
                <div class="modal-body">
                    <div class="sign_sub" id="sign_sub_standard">
                        <h3>Standard License <small class="bold text-success text-muted sub_price"></small></h3>
                        <p>Purchasing this license will extend your license expiration by one month (plus any previous licenses). You can buy multiple months, or take it one month at a time.</p> <br/><br/>
                    </div>
                    <div class="sign_sub" id="sign_sub_standard_plus">
                        <h3>Standard License <small class="bold text-success text-muted sub_price"></small></h3>
                        <p>Purchasing this license will extend your license expiration by one year (plus any previous licenses). You can buy multiple years, or take it one year at a time.</p> <br/><br/>
                    </div>
                    <div class="sign_sub" id="sign_sub_enterprise">
                        <h3>Standard License <small class="bold text-success text-muted sub_price"></small></h3>
                        <p>Purchasing this license will extend your license expiration by one month (plus any previous licenses). You can buy multiple months, or take it one month at a time.</p> <br/><br/>
                    </div>
                    <div class="sign_sub" id="sign_sub_enterprise_plus">
                        <h3>Standard License <small class="bold text-success text-muted sub_price"></small></h3>
                        <p>Purchasing this license will extend your license expiration by one year (plus any previous licenses). You can buy multiple years, or take it one year at a time.</p> <br/><br/>
                    </div>
                    <form id="subscr_form">
                        <div class="form-inline margin-bottom-25 text-center">
                            <div class="form-group form-md-line-input">
                                <div class="input-icon">
                                    <input type="text" size="20" data-stripe="name" class="form-control input-sm card_name" value="<?php echo name($_SESSION['uuid']); ?>">
                                    <div class="form-control-focus">
                                    </div>
                                    <span class="help-block">Card Holder Name</span>
                                    <i class="fa fa-user"></i>
                                </div>
                            </div>
                            <div class="form-group form-md-line-input">
                                <div class="input-icon">
                                    <input type="text" size="20" data-stripe="number" class="form-control input-sm card_num">
                                    <div class="form-control-focus">
                                    </div>
                                    <span class="help-block">Card number</span>
                                    <i class="fa fa-credit-card"></i>
                                </div>
                            </div>
                            <div class="form-group form-md-line-input">
                                <div class="input-icon">
                                    <input type="text" size="2" data-stripe="exp" class="form-control input-sm exp_date" style="width: 80px!important;">
                                    <div class="form-control-focus">
                                    </div>
                                    <span class="help-block">Expiration</span>
                                    <i class="fa fa-calendar"></i>
                                </div>
                            </div>
                            <div class="form-group form-md-line-input">
                                <div class="input-icon">
                                    <input type="text" size="4" data-stripe="cvc" class="form-control input-sm cvc_num">
                                    <div class="form-control-focus">
                                    </div>
                                    <span class="help-block">CVC</span>
                                    <i class="fa fa-sort-numeric-asc"></i>
                                </div>
                            </div>
                        </div>
                        <input type="text" name="notes" id="booking_notes" class="hidden"/>
                        <input type="text" class="i hidden">
                        <button id="subscr" class="btn btn-block red" type="button"><span class="error-handler">Pay <strong class="sub_price"></strong> for <strong class="sub_title"></strong> license!</span> <i class="fa fa-credit-card"></i></button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            <?php

            ?>
            var countDownDate = new Date("<?php echo date('M d, Y G:i:s', strtotime($license));  ?>").getTime();
            var x = setInterval(function() {
                var now = new Date().getTime();
                var distance = countDownDate - now;
                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                document.getElementById("countdown").innerHTML = "<strong class='font-red'>" + days + " days</strong>, <strong class='font-red'>" + hours + " hours</strong>, <strong class='font-red'>" + minutes + " minutes</strong> & <strong class='font-red'>" + seconds + " seconds</strong> until your current license expires.";
                document.getElementById("stat").innerHTML = "Your software license is valid. <strong class='text-success bold'>Software is operating normally.</strong>";
                if (distance < 0) {
                    clearInterval(x);
                    document.getElementById("countdown").innerHTML = "Your software license has expired. <strong class='text-danger'>No new events can be booked!</strong>";
                    document.getElementById("stat").innerHTML = "Your software license has expired. <strong class='text-danger'>No new events can be booked!</strong>";
                }
            }, 1000);
            $('.sub').on('click', function(e) {
                var type  = $(this).attr("data-type");
                var title = $(this).attr("data-title");
                var price = $(this).attr("data-price");
                var real  = $(this).attr('data-i');

                $('.sub_title').html(title);
                $('.sub_price').html(price);
                $('.i').val(real);
                $('.sign_sub').hide();
                $('#sign_sub_'+type).show();
            });
            $('.card_num').inputmask("mask", {
                "mask": "9999 9999 9999 9999",
                "removeMaskOnSubmit": false,
                "placeholder": ""
            });
            $('.exp_date').inputmask("mask", {
                "mask": "99/99",
                "removeMaskOnSubmit": false,
                "placeholder": ""
            });
            $('.cvc_num').inputmask("mask", {
                "mask": "9999",
                "placeholder": ""
            });
            Stripe.setPublishableKey('pk_live_ftqBPIkJ6eBemXHToHiU8Eqa');

            function stripeResponseHandler(status, response) {
                // Grab the form:
                var $form = $('#subscr_form');

                if (response.error) { // Problem!

                    // Show the errors on the form:
                    toastr.error("<strong>Logan says:</strong><br/>" + response.error.message);
                    $form.find('#subscr').prop('disabled', false); // Re-enable submission
                    $('#subscr').html("Check your fields. Try again? <i class='fa fa-credit-card'></i>");

                } else { // Token was created!

                    // Get the token ID:
                    var token = response.id;

                    // Insert the token ID into the form so it gets submitted to the server:
                    //$form.append($('<input type="hidden" name="auth">').val(token));

                    $.ajax({
                        url: 'assets/app/charge.php?e=subscr',
                        type: 'post',
                        data: {
                            token: token,
                            i: $('.i').val(),
                            email: "<?php echo $exp['user_email']; ?>"
                        },
                        success: function(data) {
                            if (data.length > 8) {
                                toastr.info("<strong>Logan says:</strong><br/>Card was charged successfully, here's the confirmation token: " + data);
                                $.ajax({
                                    url: 'assets/app/update_settings.php?update=subscr',
                                    type: 'POST',
                                    data: {
                                        i: $('.i').val()
                                    },
                                    success: function(s){
                                        $('#subs').modal('toggle');
                                        $('#subscr_form')[0].reset();
                                        $.ajax({
                                            url: 'assets/pages/subscriptions.php',
                                            success: function(data) {
                                                $('#page_content').html(data);
                                            },
                                            error: function() {
                                                toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                                            }
                                        });
                                        toastr.success("<strong>Logan says:</strong><br/>" + s);
                                    },
                                    error: function(s){

                                    }
                                });
                            }
                            if (data == 'error-4'){
                                $('#subscr').html("Card declined. Try again? <i class='fa fa-credit-card'></i>");
                                toastr.error("<strong>Logan says:</strong><br/>Card was declined. Please try again using a different card, or review your card information for mistakes.");
                                $form.find('#subscr').prop('disabled', false); // Re-enable submission
                            }

                            if (data == 'error-2'){
                                $('#subscr').html("Error. Try again? <i class='fa fa-credit-card'></i>")
                                toastr.error("<strong>Logan says:</strong><br/>A programatic error has occured, and the card has not been charged.");
                                $form.find('#subscr').prop('disabled', false);
                            }
                        },
                        error: function(data) {
                            console.log("Ajax Error!");
                            console.log(data);
                        }
                    });
                }
            };


            $('#subscr').unbind().click(function(ee) {
                var $form  = $('#subscr_form');
                // Disable the submit button to prevent repeated clicks:
                $('#subscr').attr("disabled","disabled");
                $('#subscr').html("<i class='fa fa-spinner fa-spin'></i>");
                console.log("disabled!");

                // Request a token from Stripe:
                Stripe.card.createToken($form, stripeResponseHandler);

                // Prevent the form from being submitted:
                return false;
            });
        });
    </script>
    <?php
} else {
    header("Location: ../../../index.php?err=no_access");
}
?>
