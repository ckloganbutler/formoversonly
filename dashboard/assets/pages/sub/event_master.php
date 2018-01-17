<?php
/**
 * Created by PhpStorm.
 * User: gameroom
 * Date: 7/3/2017
 * Time: 1:05 AM
 */
session_start();
include '../../app/init.php';

if(isset($_SESSION['logged'])){
    if($_POST['type'] == 'py') {
        $location = mysql_fetch_array(mysql_query("SELECT location_sales_tax, location_creditcard_fee FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'"));

        if(!empty($location['location_sales_tax'])){
            $tax = $location['location_sales_tax'];
        } else {$tax = 0;}

        $findItems = mysql_query("SELECT item_total, item_taxable FROM fmo_locations_events_items WHERE item_event_token='".mysql_real_escape_string($_GET['ev'])."'");
        $iTotalRecords = mysql_num_rows($findItems);
        $findPaid = mysql_query("SELECT payment_amount, payment_type FROM fmo_locations_events_payments WHERE payment_event_token='".mysql_real_escape_string($_GET['ev'])."'");
        $bTotalRecords = mysql_num_rows($findPaid);

        $event      = mysql_fetch_array(mysql_query("SELECT event_email, event_name, event_company_token, event_user_token, event_token FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($_GET['ev'])."'"));
        $companyinf = mysql_fetch_array(mysql_query("SELECT user_stripe_pk, user_stripe_sk FROM fmo_users WHERE user_company_token='".mysql_real_escape_string($event['event_company_token'])."'"));
        $user       = mysql_fetch_assoc(mysql_query("SELECT user_fname, user_lname FROM fmo_users WHERE user_token='".$event['event_user_token']."'"));


        $total = array();
        if($iTotalRecords > 0){
            while($item = mysql_fetch_assoc($findItems)){
                $total['sub_total'] += $item['item_total'];
                if($item['item_taxable'] == 1){
                    $total['tax']   += $item['item_total'] * $tax;
                }
            }
            $total['total'] = $total['sub_total'] + $total['tax'];
        } else {
            $total['total']     = 0;
            $total['sub_total'] = 0;
        }

        if($bTotalRecords > 0){
            while($paid = mysql_fetch_assoc($findPaid)){
                $void = explode(" - ", $paid['payment_type']);
                if($void[1] != 'VOIDED' && $void[0] != 'Invoice'){
                    $total['paid'] += $paid['payment_amount'];
                }
            }
            $total['unpaid'] = $total['total'] - $total['paid'];
        } else {
            $total['unpaid'] = $total['total'];
            $total['paid']   = 0;
        }
        ?>
        <div class="col-md-12">
            <div class="portlet" id="payments">
                <div class="portlet-body form">
                    <form action="#" id="submit_form" method="POST">
                        <div class="form-wizard">
                            <div class="form-body">
                                <ul class="nav nav-pills nav-justified steps hidden">
                                    <li>
                                        <a href="#tab1" data-toggle="tab" class="step">
                                            <span class="number">1 </span>
                                            <span class="desc"><i class="fa fa-check"></i> Payment Details </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#tab2" data-toggle="tab" class="step">
                                            <span class="number">3 </span>
                                            <span class="desc"><i class="fa fa-check"></i> Complete </span>
                                        </a>
                                    </li>
                                </ul>
                                <div id="bar" class="progress progress-striped" role="progressbar">
                                    <div class="progress-bar progress-bar-success">
                                    </div>
                                </div>
                                <div class="tab-content">
                                    <div class="tab-pane" id="tab1">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h3>Payment Details <small class="hidden-xs hidden-sm"><span class="text-danger">| </span>collecting amount due.</small> <span class="pull-right">Amount due: <strong><span class="text-success">$<?php echo number_format($total['unpaid'], 2); ?></span></strong></span></h3>
                                                <hr/>
                                                <div class="form-group form-md-line-input">
                                                    <select class="form-control type" name="type" data-target=".tender-inputs">
                                                        <option disabled selected value="">Select one..</option>
                                                        <option value="Cash" data-show=".cash" data-input="cash">Cash</option>
                                                        <option value="Check" data-show=".chec" data-input="chec">Check</option>
                                                        <option value="Invoice" data-show=".invoice" data-input="invoice">Invoice</option>
                                                        <option value="Credit/Debt" data-show=".cc" data-input="cc">Credit/Debt Card (ckPay&trade;)</option>
                                                        <option value="Other" data-show=".other" data-input="other">Credit/Debt Card (Other Payment Processor)</option>
                                                    </select>
                                                    <label for="form_control_1">Tender Type</label>
                                                    <span class="help-block"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <br/>
                                        <div class="tender-inputs">
                                            <div class="form-group form-md-line-input cash hidden">
                                                <input type="number" step="any" class="form-control input-sm" name="amount" id="cash" value="<?php echo number_format($total['unpaid'], 2); ?>">
                                                <label for="form_control_1">Cash Amount</label>
                                                <span class="help-block"></span>
                                            </div>
                                            <div class="form-group form-md-line-input cash hidden">
                                                <input type="text" step="any" class="form-control input-sm" name="notes" id="cash_notes" placeholder="...">
                                                <label for="form_control_1">Cash Notes</label>
                                                <span class="help-block"></span>
                                            </div>
                                            <div class="form-group form-md-line-input chec hidden">
                                                <input type="number" step="any" class="form-control input-sm" name="amount" id="chec" value="<?php echo number_format($total['unpaid'], 2); ?>">
                                                <label for="form_control_1">Check Amount</label>
                                                <span class="help-block"></span>
                                            </div>
                                            <div class="form-group form-md-line-input chec hidden">
                                                <input type="number" step="any" class="form-control input-sm" name="notes" id="chec_notes" placeholder="...">
                                                <label for="form_control_1">Check Number</label>
                                                <span class="help-block"></span>
                                            </div>
                                            <div class="form-group form-md-line-input invoice hidden">
                                                <input type="number" step="any" class="form-control input-sm" name="amount" id="invoice" value="<?php echo number_format($total['unpaid'], 2); ?>">
                                                <label for="form_control_1">Invoice Amount</label>
                                                <span class="help-block"></span>
                                            </div>
                                            <div class="form-group form-md-line-input invoice hidden">
                                                <input type="text" class="form-control input-sm" name="notes" id="invoice_notes" placeholder="...">
                                                <label for="form_control_1">Invoice Notes</label>
                                                <span class="help-block"></span>
                                            </div>
                                            <div class="input-group margin-top-10 cc hidden margin-bottom-25">
                                                <input onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)" type="number" step='any' class="form-control" name="amt_b4" id="amt_pay" value="<?php echo number_format($total['unpaid'], 2); ?>">
                                                <span class="input-group-addon" id="surcharge">
                                                    + <?php echo number_format($total['unpaid'] * .03, 2); ?> (3%)
                                                </span>
                                                <input onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)" type="number" step='any' class="form-control" name="amount" id="cc" value="<?php echo number_format($total['unpaid'] + $total['unpaid'] * .03, 2); ?>"  readonly>
                                            </div>
                                            <div class="form-inline cc hidden margin-bottom-25 text-center">
                                                <div class="form-group form-md-line-input">
                                                    <div class="input-icon">
                                                        <input type="text" size="20" data-stripe="name" class="form-control input-sm" value="<?php echo $user['user_fname']." ".$user['user_lname']; ?>">
                                                        <div class="form-control-focus">
                                                        </div>
                                                        <span class="help-block">Name on Card</span>
                                                        <i class="fa fa-user"></i>
                                                    </div>
                                                </div>
                                                <div class="form-group form-md-line-input">
                                                    <div class="input-icon">
                                                        <input type="text" size="20" data-stripe="number" class="form-control input-sm card">
                                                        <div class="form-control-focus">
                                                        </div>
                                                        <span class="help-block">Card number</span>
                                                        <i class="fa fa-credit-card"></i>
                                                    </div>
                                                </div>
                                                <div class="form-group form-md-line-input">
                                                    <div class="input-icon">
                                                        <input type="text" size="2" data-stripe="exp" class="form-control input-sm exp" style="width: 90px!important;">
                                                        <div class="form-control-focus">
                                                        </div>
                                                        <span class="help-block">Expiration</span>
                                                        <i class="fa fa-calendar"></i>
                                                    </div>
                                                </div>
                                                <div class="form-group form-md-line-input">
                                                    <div class="input-icon">
                                                        <input type="text" size="4" data-stripe="cvc" class="form-control input-sm cvc">
                                                        <div class="form-control-focus">
                                                        </div>
                                                        <span class="help-block">CVC</span>
                                                        <i class="fa fa-sort-numeric-asc"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group cc hidden">
                                                <input type="text" name="notes" id="cc_notes" class="hidden"/>
                                                <input type="text" name="charge" id="charge" class="hidden"/>
                                                <button id="checkout" class="btn btn-block red "><span class="error-handler">Pay now!</span> <i class="fa fa-credit-card"></i></button>
                                            </div>

                                            <div class="form-group form-md-line-input other hidden">
                                                <input type="number" step="any" class="form-control input-sm" name="amount" id="other" value="<?php echo number_format($total['unpaid'], 2); ?>">
                                                <label for="form_control_1">Credit/Debt Charge Amount</label>
                                                <span class="help-block"></span>
                                            </div>
                                            <div class="form-group form-md-line-input other hidden" style="margin-bottom: 48px">
                                                <input type="text" step="any" class="form-control input-sm" name="notes" placeholder="...">
                                                <label for="form_control_1">Credit/Debt Approval Number</label>
                                                <span class="help-block"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab2">
                                        <h3>Payment Details <small class="hidden-xs hidden-sm"><span class="text-danger">| </span>collecting amount due.</small> <span class="pull-right">Amount due: <strong><span class="text-success">$<?php echo number_format($total['unpaid'], 2); ?></span></strong></span></h3>
                                        <hr/>
                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                                Tender Type:
                                            </div>
                                            <div class="col-md-7 value">
                                                <strong id="t_type">

                                                </strong>
                                            </div>
                                        </div>
                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                                Tender Amount:
                                            </div>
                                            <div class="col-md-7 value">
                                                <strong class="text-success">$</strong><strong id="t_amount" class="text-success">

                                                </strong>
                                            </div>
                                        </div>
                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                                Tender Notes
                                            </div>
                                            <div class="col-md-7 value">
                                                <strong class="text-success"></strong><strong id="t_notes" class="text-success">

                                                </strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-12">
                                        <button href="javascript:;" class="btn default button-previous pull-left">
                                            <i class="m-icon-swapleft"></i> Back </button>
                                        <button href="javascript:;" class="btn red button-cancel pull-left" type="submit" name="status" value="0">
                                            Cancel <i class="fa fa-times"></i>
                                        </button>
                                        <button href="javascript:;" class="btn blue button-next pull-right" id="cc_btn">
                                            Continue <i class="m-icon-swapright m-icon-white"></i>
                                        </button>
                                        <button href="javascript:;" class="btn green button-submit pull-right" type="submit" name="status" value="1" id="real_submit">
                                            <span class="text-danger">*</span> Finalize & Save Payment <i class="m-icon-swapright m-icon-white"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script>
            $(document).ready(function() {

                function updateIn(){
                    $.ajax({
                        url: 'assets/app/api/event.php?type=inv&luid=<?php echo $_GET['luid']; ?>',
                        type: 'POST',
                        data: {
                            event: '<?php echo $_GET['ev']; ?>'
                        },
                        success: function(m){
                            var owe = JSON.parse(m);
                            $(document).find('#owe_sub_total').html(parseFloat(owe.sub_total).toFixed(2));
                            $(document).find('#owe_tax').html(parseFloat(owe.tax).toFixed(2));
                            $(document).find('#owe_total').html(parseFloat(owe.total).toFixed(2));
                            $(document).find('#PLPAP_SUBTOTAL').html(parseFloat(owe.sub_total).toFixed(2));
                            $(document).find('#PLPAP_TAXES').html(parseFloat(owe.tax).toFixed(2));
                            $(document).find('#PLPAP_TOTAL').html(parseFloat(owe.total).toFixed(2));
                            $(document).find('#owe_total_unpaid').html(parseFloat(owe.unpaid).toFixed(2));
                            $(document).find('#owe_paid').html(parseFloat(owe.paid).toFixed(2));
                            if(parseFloat(owe.unpaid).toFixed(2) > 0){
                                $(document).find('#owe_alert').show();
                                $(document).find('#owe_alert').html("<i class='fa fa-exclamation-triangle'></i> UNPAID - $" + parseFloat(owe.unpaid).toFixed(2));
                            } else {
                                $(document).find('#owe_alert').hide();
                                $(document).find('#owe_alert').html("");
                            }
                            if(parseFloat(owe.cc_fees).toFixed(2) > 0){
                                $(document).find("#cc_fees").show();
                                $(document).find("#owe_cc_fees").html(parseFloat(owe.cc_fees).toFixed(2));
                                $(document).find(".load_payments").removeClass("margin-top-15");
                            } else {
                                $(document).find("#cc_fees").hide();
                                $(document).find("#owe_cc_fees").html("");
                                $(document).find(".load_payments").addClass("margin-top-15");
                            }
                            if(parseFloat(owe.taxable).toFixed(2) > 0){
                                $(document).find("#taxable_fees").show();
                                $(document).find("#commie_fees").show();
                                $(document).find("#taxable_fees").html("($"+ parseFloat(owe.taxable).toFixed(2) +" taxable)");
                                $(document).find("#commie_fees").html("($"+ parseFloat(owe.coms).toFixed(2) +" commissionable)");
                            } else {
                                $(document).find("#taxable_fees").hide();
                                $(document).find("#commie_fees").hide();
                            }
                        },
                        error: function(e){

                        }
                    });
                }

                var form = $('#submit_form');
                var error = $('.alert-danger', form);
                var success = $('.alert-success', form);

                form.validate({
                    doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                    errorElement: 'span', //default input error message container
                    errorClass: 'help-block help-block-error', // default input error message class
                    focusInvalid: false, // do not focus the last invalid input
                    rules: {
                        type: {
                            required: true
                        },
                        amount: {
                            required: true
                        }
                    },


                    invalidHandler: function (event, validator) { //display error alert on form submit
                        success.hide();
                        error.show();
                    },

                    highlight: function (element) { // hightlight error inputs
                        $(element)
                            .closest('.form-group').removeClass('has-success').addClass('has-error'); // set error class to the control group
                    },

                    unhighlight: function (element) { // revert the change done by hightlight
                        $(element)
                            .closest('.form-group').removeClass('has-error'); // set error class to the control group
                    },

                    success: function (label) {
                        if (label.attr("for") == "gender" || label.attr("for") == "payment[]") { // for checkboxes and radio buttons, no need to show OK icon
                            label
                                .closest('.form-group').removeClass('has-error').addClass('has-success');
                            label.remove(); // remove error label here
                        } else { // display success icon for other inputs
                            label
                                .addClass('valid') // mark the current input as valid and display OK icon
                                .closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
                        }
                    },

                    submitHandler: function (form) {
                        success.show();
                        error.hide();
                        //add here some ajax code to submit your form or just call form.submit() if you want to submit the form without ajax
                    }

                });

                var handleTitle = function(tab, navigation, index) {
                    var total = navigation.find('li').length;
                    var current = index + 1;
                    // set wizard title
                    $('.step-title', $('#payments')).text('Step ' + (index + 1) + ' of ' + total);
                    // set done steps
                    jQuery('li', $('#payments')).removeClass("done");
                    var li_list = navigation.find('li');
                    for (var i = 0; i < index; i++) {
                        jQuery(li_list[i]).addClass("done");
                    }

                    if (current == 1) {
                        $('#payments').find('.button-previous').hide();
                    } else {
                        $('#payments').find('.button-previous').show();
                    }
                    if (current == 2){
                        var select = $('.type option:selected').data('input');
                        console.log(select);
                        $('#t_type').html($('.type').val());
                        $('#t_amount').html($('#'+select).val());
                        $('#t_notes').html($('#'+select+'_notes').val());
                    }

                    if (current >= total) {
                        $('#payments').find('.button-next').hide();
                        $('#payments').find('.button-submit').show();
                    } else {
                        $('#payments').find('.button-next').show();
                        $('#payments').find('.button-submit').hide();
                    }
                };

                // default form wizard
                $('#payments').bootstrapWizard({
                    'nextSelector': '.button-next',
                    'previousSelector': '.button-previous',
                    onTabClick: function (tab, navigation, index, clickedIndex) {
                        return false;
                        /*
                         success.hide();
                         error.hide();
                         if (form.valid() == false) {
                         return false;
                         }
                         handleTitle(tab, navigation, clickedIndex);
                         */
                    },
                    onNext: function (tab, navigation, index) {
                        success.hide();
                        error.hide();

                        if (form.valid() == false) {
                            return false;
                        }

                        handleTitle(tab, navigation, index);
                    },
                    onPrevious: function (tab, navigation, index) {
                        success.hide();
                        error.hide();

                        handleTitle(tab, navigation, index);
                    },
                    onTabShow: function (tab, navigation, index) {
                        var total = navigation.find('li').length;
                        var current = index + 1;
                        var $percent = (current / total) * 100;
                        $('#payments').find('.progress-bar').css({
                            width: $percent + '%'
                        });
                    }
                });

                $('#payments').find('.button-previous').hide();
                $('#payments .button-submit').click(function () {
                    Pace.track(function(){
                        $.ajax({
                            url: 'assets/app/update_settings.php?setting=pymt&ev=<?php echo $_GET['ev']; ?>&uuid=<?php echo $_GET['uuid']; ?>',
                            type: 'POST',
                            data: $('#submit_form').serialize(),
                            success: function(p){
                                toastr.success("Nice, we took your payment!");
                                $('#payments-content').html("");
                                $('#payments-maked').show();
                                $('.load_payments').html("Take another payment? <i class='fa fa-money'></i>");
                                $('.load_payments').removeClass("red");
                                $('.load_payments').addClass("green");
                                $('#paid').DataTable().ajax.reload();
                                updateIn();
                            },
                            error: function(p){
                                toastr.error("Ooops. Something went wrong.")
                            }
                        });
                    });
                }).hide();

                Stripe.setPublishableKey('<?php echo $companyinf['user_stripe_pk']; ?>');

                function stripeResponseHandler(status, response) {
                    // Grab the form:
                    var $form = $('#submit_form');

                    if (response.error) { // Problem!

                        // Show the errors on the form:
                        toastr.error("<strong>Logan says:</strong><br/>" + response.error.message);
                        $form.find('#checkout').prop('disabled', false); // Re-enable submission
                        $form.find('#checkout').html("Check your fields. Try again? <i class='fa fa-credit-card'></i>");

                    } else { // Token was created!

                        // Get the token ID:
                        var token = response.id;

                        // Insert the token ID into the form so it gets submitted to the server:
                        //$form.append($('<input type="hidden" name="auth">').val(token));

                        $.ajax({
                            url: 'assets/app/checkout.php?cuid=<?php echo $event['event_company_token']; ?>&ev=<?php echo $event['event_token']; ?>',
                            type: 'post',
                            data: {
                                token: token,
                                amount: $('#cc').val().replace('.', ''),
                                email: "<?php echo $event['event_email']; ?>"
                            },
                            success: function(data) {
                                if (data.length > 8) {
                                    toastr.info("<strong>Logan says:</strong><br/>Card was charged successfully, here's the confirmation token: " + data);
                                    $('.error-handler').html("");
                                    $('#cc_notes').removeAttr('disabled');
                                    $('#cc_notes').attr('value', "Approval: "+data);
                                    $('#charge').removeAttr('disabled');
                                    $('#charge').attr('value', data);
                                    $('#cc_btn').removeClass("hidden");
                                    $('#cc_btn').click();
                                    $('#real_submit').click();
                                }
                                if (data == 'error-4'){
                                    $form.find('#checkout').html("Card declined. Try again? <i class='fa fa-credit-card'></i>");
                                    toastr.error("<strong>Logan says:</strong><br/>Card was declined. Please try again using a different card, or review your card information for mistakes.");
                                    $form.find('#checkout').prop('disabled', false); // Re-enable submission
                                }

                                if (data == 'error-2'){
                                    $form.find('#checkout').html("Error. Try again? <i class='fa fa-credit-card'></i>")
                                    toastr.error("<strong>Logan says:</strong><br/>A programatic error has occured, and the card has not been charged.");
                                    $form.find('#checkout').prop('disabled', false);
                                }
                            },
                            error: function(data) {
                                console.log("Ajax Error!");
                                console.log(data);
                            }
                        });
                    }
                };

                $('#checkout').on('click', function() {
                    $(function(event) {
                        var $form  = $('#submit_form');
                        // Disable the submit button to prevent repeated clicks:
                        $('#checkout').prop('disabled', true);
                        $('#checkout').html("<i class='fa fa-spinner fa-spin'></i>");

                        // Request a token from Stripe:
                        Stripe.card.createToken($form, stripeResponseHandler);

                        // Prevent the form from being submitted:
                        return false;
                    });
                });

                $('.card').inputmask("mask", {
                    "mask": "9999 9999 9999 9999",
                    "removeMaskOnSubmit": false,
                    "placeholder": ""
                });
                $('.exp').inputmask("mask", {
                    "mask": "99/99",
                    "removeMaskOnSubmit": false,
                    "placeholder": ""
                });
                $('.cvc').inputmask("mask", {
                    "mask": "9999",
                    "placeholder": ""
                });
                $('#amt_pay').on('input', function() {
                    var value           = $(this).val();
                    var surcharge       = parseFloat(Math.round((+value * +<?php echo number_format($location['location_creditcard_fee'], 2); ?>) * 100) / 100).toFixed(2);
                    var after           = parseFloat(Math.round((+value + +value * +<?php echo number_format($location['location_creditcard_fee'], 2); ?>) * 100) / 100).toFixed(2);
                    $("#surcharge").html("+ " + parseFloat(surcharge).toFixed(2).replace (/,/g, "") + " (3%) =");
                    $("#cc").val(parseFloat(after).toFixed(2).replace (/,/g, ""));
                });

                $('.type').on('change', function() {
                    var type    = $(this).val();
                    var target  = $(this).data('target');
                    var show   =  $("option:selected", this).data('show');
                    $(target).children().addClass('hidden');
                    $(show).removeClass('hidden');
                    $(".tender-inputs input:hidden").attr('disabled', 'disabled');
                    $(".tender-inputs input:visible").removeAttr('disabled');
                    $('.tender-inputs input:visible[name="amount"]').focus();

                     if(type == 'Credit/Debt'){
                     $('#cc_btn').addClass("hidden");
                     } else {
                     $('#cc_btn').removeClass("hidden");
                     }
                });
            });
        </script>
        <?php
    } elseif($_POST['type'] == 'iv'){

    }
}
