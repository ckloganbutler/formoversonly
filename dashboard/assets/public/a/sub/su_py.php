<?php
/**
 * Created by PhpStorm.
 * User: gameroom
 * Date: 1/18/2018
 * Time: 3:43 PM
 */
include '../../../app/init.php';

if(isset($_GET['t']) && $_GET['t'] == 'pyt'){
    $location = mysql_fetch_array(mysql_query("SELECT location_storage_stripe_public, location_storage_creditcard_fee, location_owner_company_token FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'"));
    $user     = mysql_fetch_array(mysql_query("SELECT user_email FROM fmo_users WHERE user_token='".mysql_real_escape_string($_POST['uuid'])."'"));
    ?>
    <style type="text/css">
        i {
            margin-top: 5px!important;
        }
    </style>
    <div class="col-md-12">
        <form id="submit_form">
            <h4>Your account balance:</h4>
            <h3 class="text-center" id="owe_bal"></h3>
            <hr/>
            <h5>How much would you like to pay?</h5>
            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">Amount <span class="required">*</span></label>
                <div class="input-icon">
                    <i class="fa fa-dollar"></i>
                    <input type="number" step=".01" id="amt_pay" class="form-control input-sm" placeholder="$20.00 (amount in dollars)" value="">
                    <span class="text-muted help-block" id="err">Amount in dollars. Minimum of 20$</span>
                </div>
            </div>
            <hr/>
            <div class="form-group">
                <label class="control-label">Carderholder Name <span class="required">*</span></label>
                <div class="input-icon">
                    <i class="fa fa-user"></i>
                    <input type="text" data-stripe="name" id="name" class="form-control input-sm card_name" placeholder="Cardholder Name" value="">
                </div>
            </div>
            <div class="form-inline">
                <div class="form-group">
                    <label class="control-label">Credit/Debt Card Number <span class="required">*</span></label>
                    <div class="input-icon">
                        <i class="fa fa-credit-card"></i>
                        <input type="text" data-stripe="number" id="number" class="form-control input-sm card_num" placeholder="Card Number">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label">Exp <span class="required">*</span></label>
                    <div class="input-icon">
                        <i class="fa fa-calendar"></i>
                        <input type="text" data-stripe="exp" id="exp" class="form-control input-sm exp_date" placeholder="Exp">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label">CVC <span class="required">*</span></label>
                    <div class="input-icon">
                        <i class="fa fa-sort-numeric-asc"></i>
                        <input type="text" data-stripe="cvc" id="cvc" class="form-control input-sm cvc_num" placeholder="CVC" >
                    </div>
                </div>
            </div>
            <div class="card-wrapper">

            </div>
            <br/>
            <div class="row text-center">
                <div class="col-md-12">
                    <h5><strong>Automatic payment</strong> tools</h5>
                    <div class="form-group">
                        <label class="control-label"><strong>Auto Pay&trade;</strong> mobile <span class="font-blue">*</span></label>
                        <div class="input-icon">
                            <div class="input-group input-md datepicker input-daterange" data-date="10/11/2012" data-date-format="mm/dd/yyyy" style="margin-top: -4px; width: 100% !important;">
                                <label>
                                    <input type="checkbox" class="icheck" value="1" name="auto"> Sign up for <strong>Auto Pay&trade;</strong>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <input type="text" name="notes" id="cc_notes" class="hidden"/>
            <input type="text" name="charge" id="charge" class="hidden"/>
            <input type="text" name="amount" id="amount" class="hidden" value=""/>
            <hr/>
            <button class="btn btn-block default red-stripe cancel" type="button"><i class="fa fa-times"></i> Cancel</button>
            <button id="checkout" type="button" class="btn btn-block red "><span class="error-handler">Pay $<strong class="owe_total_unpaid"></strong> now!</span> <i class="fa fa-credit-card"></i></button>
        </form>
    </div>
    <script type="text/javascript">
        $.ajax({
            url: '../app/api/storage.php?type=inv_c&luid=<?php echo $_GET['luid']; ?>',
            type: 'POST',
            data: {
                uuid: '<?php echo $_POST['uuid']; ?>'
            },
            success: function(m){
                var owe = JSON.parse(m);
                if(owe.unpaid < 0){
                    var due     = "Credit of";
                    var unpaid  = owe.unpaid * -1;
                    var clasc   = "text-success";
                } else {var due = "Due"; var clasc = "text-danger"; var unpaid = owe.unpaid; }
                $(document).find('#owe_bal').html("<span class='"+ clasc + "'>" + due + " $" + parseFloat(unpaid).toFixed(2) + "</span>");
            },
            error: function(e){

            }
        });

        var form = $('#submit_form');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);

        form.validate({
            doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                amount: {
                    required: true,
                    min: 20
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

        Stripe.setPublishableKey('<?php echo $location['location_storage_stripe_public']; ?>');

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
                    url: '../app/checkout.php?cuid=<?php echo $location['location_owner_company_token']; ?>&uuid=<?php echo $_POST['uuid']; ?>&e=LOL',
                    type: 'post',
                    data: {
                        token: token,
                        amount: $('#amount').val().replace('.', ''),
                        email: '<?php echo $user['user_email']; ?>',
                    },
                    success: function(data) {
                        if (data.length > 8) {
                            toastr.info("<strong>Logan says:</strong><br/>Card was charged successfully!");
                            $('.error-handler').html("");
                            $('#cc_notes').removeAttr('disabled');
                            $('#cc_notes').attr('value', "Approval: "+data);
                            $('#charge').removeAttr('disabled');
                            $('#charge').attr('value', data);
                            $.ajax({
                                url: '../app/update_settings.php?setting=su_pymt&uuid=<?php echo $_POST['uuid']; ?>&luid=<?php echo $_GET['luid']; ?>',
                                type: 'POST',
                                data: $('#submit_form').serialize(),
                                success: function(p){
                                    toastr.success("<strong>Logan says:</strong><br/>We have been notified of your payment.");
                                    $('.str-content').html("<h3 class='text-center'><i class='fa fa-spin fa-spinner'></i> Loading... </h3>");
                                    $.ajax({
                                        url: 'a/sub/su_py.php?t=mgr&luid=<?php echo $_GET['luid']; ?>',
                                        type: 'POST',
                                        data: {
                                            uuid: '<?php echo $_POST['uuid']; ?>'
                                        }, success: function(data){
                                            $('#pg_content_sub').html(data);
                                        }, error: function(){
                                            toastr.error("<strong>Logan says:</strong><br/>Hmm..something didn't work right. Refresh your browser.")
                                        }
                                    });
                                },
                                error: function(p){
                                    toastr.error("<strong>Logan says:</strong><br/>Ooops. Something went wrong.")
                                }
                            });
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
                var amt = $('#amount').val();
                if(parseFloat(amt) >= 20.00){
                    Stripe.card.createToken($form, stripeResponseHandler);
                } else {
                    $('#err').html('<span class="text-danger">Please enter at least $20.00!</span>');
                    $form.find('#checkout').prop('disabled', false); // Re-enable submission
                    $form.find('#checkout').html("Check your fields. Try again? <i class='fa fa-credit-card'></i>");
                }
                // Prevent the form from being submitted:
                return false;
            });
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
        var card = new Card({
            form: 'form', // *required*
            container: '.card-wrapper', // *required*

            formSelectors: {
                numberInput: 'input#number',
                expiryInput: 'input#expiry',
                cvcInput: 'input#cvc',
                nameInput: 'input#name'
            },

            width: 300,
            formatting: true,

            messages: {
                validDate: 'valid\ndate',
                monthYear: 'mm/yyyy',
            },

            placeholders: {
                number: '•••• •••• •••• ••••',
                name: 'Full Name',
                expiry: '••/••',
                cvc: '•••'
            },

            masks: {
                cardNumber: '•'
            },

            debug: false
        });
        $('.icheck').iCheck({
            checkboxClass: 'icheckbox_minimal',
            radioClass: 'iradio_minimal'
        });
        $('.cancel').on('click', function() {
            $.ajax({
                url: 'a/sub/su_py.php?t=mgr&luid=<?php echo $_GET['luid']; ?>',
                type: 'POST',
                data: {
                    uuid: '<?php echo $_POST['uuid']; ?>'
                },
                success: function(data){
                    $('#pg_content_sub').html(data);
                },
                error: function(){
                    toastr.error("<strong>Logan says:<br/><strong>An unexpected error occurred.");
                }
            });
        });
        $('#amt_pay').on('input', function() {
            var value           = $(this).val();
            $("#amount").attr('value', parseFloat(value).toFixed(2).replace (/,/g, ""));
        });
    </script>
    <?php
} elseif(isset($_GET['t']) && $_GET['t'] == 'mgr'){
    ?>
    <div class="col-md-12">
        <h4>Your rentals:</h4>
        <?php
        $contracts = mysql_query("SELECT contract_storage_token, contract_next_due, contract_rate_adj FROM fmo_locations_storages_contracts WHERE contract_user_token='".mysql_real_escape_string($_POST['uuid'])."' AND contract_status=1");
        if(mysql_num_rows($contracts) > 0){
            while($contract = mysql_fetch_assoc($contracts)) {
                $storage = mysql_fetch_array(mysql_query("SELECT storage_unit_name, storage_unit_lwh, storage_price, storage_period, storage_status, storage_token FROM fmo_locations_storages WHERE storage_token='" . mysql_real_escape_string($contract['contract_storage_token']) . "'"));
                switch($storage['storage_status']){
                    case "Reserved": $badge = 'badge badge-yellow badge-roundless'; $msg = $storage['storage_status']; break;
                    case "Vacant": $badge = 'badge badge-default badge-roundless'; $msg = $storage['storage_status']; break;
                    case "Occupied": $badge = 'badge badge-success badge-roundless'; $msg = $storage['storage_status']; break;
                    case "Damaged": $badge = 'badge badge-danger badge-roundless'; $msg = $storage['storage_status']; break;
                    case "Auction": $badge = 'badge badge-danger badge-roundless'; $msg = $storage['storage_status']; break;
                    case "Delinquent": $badge = 'badge badge-danger badge-roundless'; $msg = $storage['storage_status']; break;
                }
                ?>
                <div class="row well" style="padding-top: 0px; padding-bottom: 0px;">
                    <div class="col-md-12">
                        <div class="portfolio-text">
                            <div class="portfolio-text-info">
                                <h3>
                                    <span style="margin-top: -4px;" class="<?php echo $badge; ?>"><?php echo $msg; ?></span>
                                    <strong>Unit #<?php echo $storage['storage_unit_name']; ?></strong>
                                    <small>[<strong><?php echo $storage['storage_unit_lwh']; ?></strong>]</small><br/>
                                    <span class="text-info"><strong>$<?php echo number_format($storage['storage_price'] + $contract['contract_rate_adj'] , 2); ?></strong><small class="text-info">/<?php echo $storage['storage_period']; ?></small></span>
                                    <small class="text-info">(next due <strong><?php echo date('M dS', strtotime($contract['contract_next_due'])); ?></strong>)</small>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
                <?php

            }
        } else {
            ?>
            <div class="alert alert-warning">
                <strong>No storage units found!</strong> Add storage units to see them appear here.
            </div>
            <?php
        }
        ?>
    </div>
    <br/>
    <div class="col-md-12">
        <h4>Your authorized contacts:</h4>
    </div>
    <br/>
    <?php
    $alts = mysql_query("SELECT alt_id, alt_name, alt_address, alt_phone FROM fmo_locations_storages_alts WHERE alt_user_token='".mysql_real_escape_string($_POST['uuid'])."'");
    if(mysql_num_rows($alts) > 0){
        while($alt = mysql_fetch_assoc($alts)){
            ?>
            <div class="col-md-6 col-sm-12" style="margin-bottom: 3px;">
                <button class="btn btn-block default red-stripe">
                    <strong><a class="alts_<?php echo $alt['alt_id']; ?>" style="color:#333333" data-inputclass="form-control" data-name="alt_name" data-pk="<?php echo $alt['alt_id']; ?>" data-type="text" data-placement="right" data-title="Enter new name" data-url="assets/app/update_settings.php?setting=alts"><?php echo $alt['alt_name']; ?></a></strong>
                    (<strong><a class="alts_<?php echo $alt['alt_id']; ?>" style="color:#333333" data-inputclass="form-control" data-name="alt_address" data-pk="<?php echo $alt['alt_id']; ?>" data-type="text" data-placement="right" data-title="Enter new name" data-url="assets/app/update_settings.php?setting=alts"><?php echo $alt['alt_address']; ?></a></strong>)
                    <strong><a class="alts_<?php echo $alt['alt_id']; ?>" style="color:#333333" data-inputclass="form-control" data-name="alt_phone" data-pk="<?php echo $alt['alt_id']; ?>" data-type="text" data-placement="right" data-title="Enter new name" data-url="assets/app/update_settings.php?setting=alts"><?php echo clean_phone($alt['alt_phone']); ?></a></strong>
                </button>
            </div>
            <?php
        }
    }
    ?>
    <hr/>
    <div class="col-md-12">
        <h4>Your account balance: </h4>
        <br/>
        <a class="btn btn-block btn-md default red-stripe pym"><span id="owe_rent"><i class='fa fa-spinner fa-spin'></i> &nbsp;</span> <i class="fa fa-arrow-right"></i></a>
        <br/>
        <span class="text-muted"><span class="text-danger">*</span> All payments past the amount due will be applied as a credit on your account. Unlock adding extra funds (like pre-paying) when your account has no balance due.</span>
    </div>
    <script type="text/javascript">
        $('.pym').on('click', function() {
            $.ajax({
                url: 'a/sub/su_py.php?t=pyt&luid=<?php echo $_GET['luid']; ?>',
                type: 'POST',
                data: {
                    uuid: '<?php echo $_POST['uuid']; ?>'
                },
                success: function(data){
                    $('#pg_content_sub').html(data);
                }, error: function(){
                    toastr.error("<strong>Logan says:<br/></strong>An unexpected error occurred.");
                }
            });
        });
        function updateIn(){
            $.ajax({
                url: '../app/api/storage.php?type=inv_c&luid=<?php echo $_GET['luid']; ?>',
                type: 'POST',
                data: {
                    uuid: '<?php echo $_POST['uuid']; ?>'
                },
                success: function(m){
                    var owe = JSON.parse(m);
                    if(owe.unpaid < 0){
                        var due     = "Credit of";
                        var typ     = "Add more";
                        var clasc   = "text-success";
                        var unpaid  = owe.unpaid * -1;
                    } else {var due = "Due"; var typ = "Pay now"; var clasc = "text-danger"; var unpaid = owe.unpaid; }
                    $(document).find('#owe_rent').html("<span class='bold "+ clasc +"'>" + due + " $" + parseFloat(unpaid).toFixed(2) + "</span> &nbsp; "+typ);
                },
                error: function(e){

                }
            });
        }
        updateIn();
    </script>
    <?php
}