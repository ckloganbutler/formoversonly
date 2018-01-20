<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 12/4/2017
 * Time: 6:06 PM
 */
if(isset($_GET['e'])){
    session_start();
    include '../../../app/init.php';
    if($_GET['e'] == 'pYt'){
        $ct = struuid(true);
        $location = mysql_fetch_array(mysql_query("SELECT location_storage_stripe_public, location_storage_days_late, location_storage_days_auction, location_storage_tax, location_storage_deposit, location_storage_creditcard_fee, location_nickname, location_storage_late_fee, location_storage_auction_fee FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'"));
        $uuidperm = mysql_fetch_array(mysql_query("SELECT user_esc_permissions, user_email FROM fmo_users WHERE user_token='".mysql_real_escape_string($_SESSION['uuid'])."'"));

        $total = array();
        $total['sub_total'] = 0.00;
        $total['tax']       = 0.00;
        $total['taxable']   = 0.00;
        $total['cc_fees']   = 0.00;
        $total['total']     = 0.00;
        $total['paid']      = 0.00;
        $total['unpaid']    = 0.00;
        $findItems = mysql_query("SELECT item_total, item_taxable, item_taxable_amount, item_commission FROM fmo_locations_storages_contracts_items WHERE item_user_token='".mysql_real_escape_string($_POST['uuid'])."'");
        $iTotalRecords = mysql_num_rows($findItems);

        $findPaid = mysql_query("SELECT payment_amount, payment_type FROM fmo_locations_storages_contracts_payments WHERE payment_user_token='".mysql_real_escape_string($_POST['uuid'])."'");
        $bTotalRecords = mysql_num_rows($findPaid);

        if($iTotalRecords > 0){
            while($item = mysql_fetch_assoc($findItems)){
                $total['sub_total'] += $item['item_total'];
                if($item['item_taxable'] == 1){
                    $tax = $item['item_taxable_amount'];
                    $total['tax']     += number_format($item['item_total'] * $tax, 2, '.', '');
                    $total['taxable'] += number_format($item['item_total'], 2, '.', '');
                } else {
                    $total['tax']   += 0.00;
                }
                if($item['item_commission'] == 1){
                    $total['coms']  += number_format($item['item_total'], 2, '.', '');
                } else {
                    $total['coms'] += 0.00;
                }
            }
            $total['total'] = number_format($total['sub_total'] + $total['tax'], 2, '.', '');
        }

        if($bTotalRecords > 0){
            while($paid = mysql_fetch_assoc($findPaid)){
                $void = explode(" - ", $paid['payment_type']);
                if($void[1] != 'VOIDED' && $void[0] != 'Invoice'){
                    $total['paid'] += $paid['payment_amount'];
                    if($void[0] == 'Credit/Debt' && $void[1] != 'Booking Fee'){
                        $total['total']   += ($paid['payment_amount'] / 1 + $location['location_storage_creditcard_fee'] ) * $location['location_storage_creditcard_fee'] ;
                        $total['cc_fees'] += ($paid['payment_amount'] / 1 + $location['location_storage_creditcard_fee'] ) * $location['location_storage_creditcard_fee'] ;
                    }
                }

            }
            $total['unpaid'] = number_format($total['total'] - $total['paid'], 2, '.', '');
            $total['amount'] = intval(floatval(str_replace("$", "", $total['unpaid']))*100);
        } else {
            $total['unpaid'] = number_format($total['total'], 2, '.', '');
            $total['amount'] = intval(floatval(str_replace("$", "", $total['unpaid']))*100);
        }

        if($total['unpaid'] < 0){
            $due = "Credit";
            $new = number_format($total['unpaid'] * -1, 2);
            $total['unpaid'] = 0.00;
        } else {$due = "Due"; $new = number_format($total['unpaid'], 2); }
        ?>
        <div class="row">
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
                                                    <h3>Payment Details <small class="hidden-xs hidden-sm"><span class="text-danger">| </span>collecting amount due.</small> <span class="pull-right"><?php echo $due; ?>: <strong><span class="text-success">$<?php echo number_format($new, 2); ?></span></strong></span></h3>
                                                    <hr/>
                                                    <div class="form-group form-md-line-input">
                                                        <select class="form-control type" name="type" data-target=".tender-inputs">
                                                            <option disabled selected value="">Select one..</option>
                                                            <option value="Cash" data-show=".cash" data-input="cash">Cash</option>
                                                            <option value="Check" data-show=".chec" data-input="chec">Check</option>
                                                            <?php if($_SESSION['group'] == 1 || strpos($perms, "view_storage_create_writeoff") !== false){
                                                                ?>
                                                                <option value="Write Off" data-show=".writeoff" data-input="writeoff">Write Off</option>
                                                                <?php
                                                            }
                                                            ?>
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
                                                <div class="form-group form-md-line-input writeoff hidden">
                                                    <input type="number" step="any" class="form-control input-sm" name="amount" id="writeoff" value="<?php echo number_format($total['unpaid'], 2); ?>">
                                                    <label for="form_control_1">Write Off Amount</label>
                                                    <span class="help-block"></span>
                                                </div>
                                                <div class="form-group form-md-line-input writeoff hidden">
                                                    <input type="text" class="form-control input-sm" name="notes" id="writeoff_notes" placeholder="...">
                                                    <label for="form_control_1">Write Off Notes</label>
                                                    <span class="help-block"></span>
                                                </div>
                                                <div class="input-group margin-top-10 cc hidden margin-bottom-25">
                                                    <input onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)" type="number" step='any' class="form-control" name="amt_b4" id="amt_pay" value="<?php echo number_format($total['unpaid'], 2); ?>">
                                                    <span class="input-group-addon" id="surcharge">
                                                + <?php echo number_format($total['unpaid'] * $location['location_storage_creditcard_fee'], 2); ?> (<?php echo number_format($location['location_storage_creditcard_fee'] * 100, 0) ?>%)
                                            </span>
                                                    <input onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)" type="number" step='any' class="form-control" name="amount" id="cc" value="<?php echo number_format($total['unpaid'] + $total['unpaid'] * $location['location_storage_creditcard_fee'] , 2); ?>"  readonly>
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
                                                    <br/><br/>
                                                    <div class="row">

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
                                            <button href="javascript:;" class="btn red ccl pull-left">
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
        </div>
        <script>
            $(document).ready(function() {

                $('.ccl').on('click', function(){
                    $('.str-content').html("<h3 class='text-center'><i class='fa fa-spin fa-spinner'></i> Loading... </h3>");
                    $.ajax({
                        url: 'assets/pages/sub/sub/storage_py.php?e=mgr&luid=<?php echo $_GET['luid']; ?>',
                        type: 'POST',
                        data: {
                            uuid: '<?php echo $_POST['uuid']; ?>'
                        }, success: function(data){
                            $('.str-content').html(data)
                        }, error: function(){
                            toastr.error("<strong>Logan says:</strong><br/>Hmm..something didn't work right. Refresh your browser.")
                        }
                    });
                });

                function updateIn(){
                    $.ajax({
                        url: 'assets/app/api/storage.php?type=inv_c&luid=<?php echo $_GET['luid']; ?>',
                        type: 'POST',
                        data: {
                            uuid: '<?php echo $_POST['uuid']; ?>'
                        },
                        success: function(m){
                            var owe = JSON.parse(m);
                            if(owe.unpaid < 0){
                                var due     = "Credit";
                                var unpaid  = owe.unpaid * -1;
                            } else {var due = "Due"; var unpaid = owe.unpaid; }
                            $(document).find('#owe_rent').html(due + " $" + parseFloat(unpaid).toFixed(2));
                        },
                        error: function(e){

                        }
                    });
                }

                $('.datatable2').each(function(){
                    var url = $(this).attr('data-src');
                    $(this).DataTable({
                        "processing": true,
                        "serverSide": true,
                        //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                        "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
                        "bPaginate": false,
                        "bFilter": false,
                        "info": false,
                        "ajax": {
                            "url": url, // ajax source
                        },
                        "lengthMenu": [
                            [10, 20, 50, 100, 150, -1],
                            [10, 20, 50, 100, 150, "All"] // change per page values here
                        ],
                        "pageLength": 10, // default record count per page
                        "order": [
                            [1, "asc"]
                        ]// set first column as a default sort by asc
                    });
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
                            url: 'assets/app/update_settings.php?setting=su_pymt&uuid=<?php echo $_POST['uuid']; ?>&luid=<?php echo $_GET['luid']; ?>',
                            type: 'POST',
                            data: $('#submit_form').serialize(),
                            success: function(p){
                                toastr.success("Nice, we took your payment!");
                                $('.str-content').html("<h3 class='text-center'><i class='fa fa-spin fa-spinner'></i> Loading... </h3>");
                                $.ajax({
                                    url: 'assets/pages/sub/sub/storage_py.php?e=mgr&luid=<?php echo $_GET['luid']; ?>',
                                    type: 'POST',
                                    data: {
                                        uuid: '<?php echo $_POST['uuid']; ?>'
                                    }, success: function(data){
                                        $('.str-content').html(data)
                                    }, error: function(){
                                        toastr.error("<strong>Logan says:</strong><br/>Hmm..something didn't work right. Refresh your browser.")
                                    }
                                });
                                updateIn();
                            },
                            error: function(p){
                                toastr.error("Ooops. Something went wrong.")
                            }
                        });
                    });
                }).hide();

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
                            url: 'assets/app/checkout.php?cuid=<?php echo $_SESSION['cuid']; ?>&uuid=<?php echo $_POST['uuid']; ?>&e=LOL',
                            type: 'post',
                            data: {
                                token: token,
                                amount: $('#cc').val().replace('.', ''),
                                email: '<?php echo $uuidperm['user_email']; ?>',
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
                    var surcharge       = parseFloat(Math.round((+value * +<?php echo number_format($location['location_storage_creditcard_fee'], 2); ?>) * 100) / 100).toFixed(2);
                    var after           = parseFloat(Math.round((+value + +value * +<?php echo number_format($location['location_storage_creditcard_fee'], 2); ?>) * 100) / 100).toFixed(2);
                    $("#surcharge").html("+ " + parseFloat(surcharge).toFixed(2).replace (/,/g, "") + " (<?php echo number_format($location['location_storage_creditcard_fee'] * 100, 0) ?>%) =");
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

                $('.icheck').iCheck({
                    checkboxClass: 'icheckbox_minimal',
                    radioClass: 'iradio_minimal'
                });
            });
        </script>
        <?php
    } elseif($_GET['e'] ==  'rTl'){
        $ct = struuid(true);
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet" id="form_wizard_1">
                    <div class="portlet-body form">
                        <form action="#" id="submit_form" method="POST" editable-form name="textBtnForm">
                            <div class="form-body">
                                <ul class="nav nav-pills nav-justified steps hidden">
                                    <li>
                                        <a href="#tab5" data-toggle="tab" class="step">
                                            <span class="number">4 </span>
                                            <span class="desc"><i class="fa fa-check"></i> Payment </span>
                                        </a>
                                    </li>
                                </ul>
                                <div id="bar" class="progress progress-striped" role="progressbar">
                                    <div class="progress-bar progress-bar-success">
                                    </div>
                                </div>
                                <div class="tab-content">
                                    <div class="alert alert-danger display-none">
                                        <button class="close" data-dismiss="alert"></button>
                                        You have some form errors. Please check below.
                                    </div>

                                    <div class="tab-pane" id="tab5">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="scroller2" style="height: 290px;" data-always-visible="1" data-rail-visible1="1">
                                                    <div class="portlet">
                                                        <div class="portlet-body">
                                                            <div class="table-container">
                                                                <form role="form" id="add_service_rate">
                                                                    <table class="table table-striped table-hover datatable2" data-src="assets/app/api/storage.php?type=rates&luid=<?php echo $_GET['luid']; ?>&ct=<?php echo $ct; ?>&rt=true&uuid=<?php echo $_POST['uuid']; ?>">
                                                                        <thead>
                                                                            <tr role="row" class="heading">
                                                                                <th>
                                                                                    Service Name
                                                                                </th>
                                                                                <th width="12%" class="text-center">
                                                                                    Invoice item <i class="fa fa-arrow-right"></i>
                                                                                </th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>

                                                                        </tbody>
                                                                    </table>
                                                                </form>
                                                            </div>
                                                            <small class="bold">(<i class="fa fa-check text-danger light"></i> = Taxable | <i class="fa fa-check text-success light"></i> = Commissionable | <span class="text-danger bold">Discount</span>)</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="scroller2" style="height: 290px;" data-always-visible="1" data-rail-visible1="1">
                                                    <div class="portlet">
                                                        <div class="portlet-body" id="invoice">
                                                            <div class="invoice">
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="table-container">
                                                                            <form role="form" id="add_service_rate">
                                                                                <table class="table table-striped table-hover datatable2 sales" data-src="assets/app/api/storage.php?type=sales&uuid=<?php echo $_POST['uuid']; ?>&ct=<?php echo $ct; ?>&luid=<?php echo $_GET['luid']; ?>&rt=true">
                                                                                    <thead>
                                                                                    <tr role="row" class="heading">
                                                                                        <th>
                                                                                            Item
                                                                                            <span class="pull-right no_print">
                                                                                                Options
                                                                                            </span>
                                                                                        </th>
                                                                                        <th>
                                                                                            Description
                                                                                        </th>
                                                                                        <th>
                                                                                            Quantity
                                                                                        </th>
                                                                                        <th>
                                                                                            Unit Cost
                                                                                        </th>
                                                                                        <th>
                                                                                            <span class="pull-right">Total</span>
                                                                                        </th>
                                                                                    </tr>
                                                                                    </thead>
                                                                                    <tbody>

                                                                                    </tbody>
                                                                                </table>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-xs-6">
                                                                    </div>
                                                                    <div class="col-xs-6 invoice-block">
                                                                        <ul class="list-unstyled amounts" style="margin-bottom: 0;">
                                                                            <li>
                                                                                Sub Total: <h3 style="display: inline" class="text-danger bold">$<span id="owe_sub_total"></span></h3>
                                                                            </li>
                                                                            <li>
                                                                                <small class="bold" id="taxable_fees"></small> Taxes Due:  <h3 style="display: inline;" class="text-danger bold">$<span id="owe_tax"></span></h3>
                                                                            </li>
                                                                            <li>
                                                                                Grand Total: <h3 style="display: inline;" class="text-danger bold">$<span id="owe_total"></span></h3>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-12">
                                        <button href="javascript:;" class="btn red ccl pull-left">
                                            Cancel <i class="fa fa-times"></i>
                                        </button>
                                        <button href="javascript:;" class="btn green button-submit pull-right" type="submit" name="status" value="1" id="real_submit">
                                            <span class="text-danger">*</span> Finalize & Charge <i class="m-icon-swapright m-icon-white"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script>
        $(document).ready(function(){

            $('.ccl').on('click', function(){
                $('.str-content').html("<h3 class='text-center'><i class='fa fa-spin fa-spinner'></i> Loading... </h3>");
                $.ajax({
                    url: 'assets/pages/sub/sub/storage_py.php?e=mgr&luid=<?php echo $_GET['luid']; ?>',
                    type: 'POST',
                    data: {
                        uuid: '<?php echo $_POST['uuid']; ?>'
                    }, success: function(data){
                        $('.str-content').html(data)
                    }, error: function(){
                        toastr.error("<strong>Logan says:</strong><br/>Hmm..something didn't work right. Refresh your browser.")
                    }
                });
            });

            function updateIn() {
                $.ajax({
                    url: 'assets/app/api/storage.php?type=inv&luid=<?php echo $_GET['luid']; ?>&no_calc=true',
                    type: 'POST',
                    data: {
                        contract: '<?php echo $ct; ?>'
                    },
                    success: function (m) {
                        var owe = JSON.parse(m);
                        if (owe.unpaid < 0) {
                            var due = "Credit";
                            var unpaid = owe.unpaid * -1;
                        } else {
                            var due = "Due";
                            var unpaid = owe.unpaid;
                        }
                        $(document).find('#owe_sub_total').html(parseFloat(owe.sub_total).toFixed(2));
                        $(document).find('#owe_tax').html(parseFloat(owe.tax).toFixed(2));
                        $(document).find('#owe_total').html(parseFloat(owe.total).toFixed(2));
                        $(document).find('#PLPAP_SUBTOTAL').html(parseFloat(owe.sub_total).toFixed(2));
                        $(document).find('#PLPAP_TAXES').html(parseFloat(owe.tax).toFixed(2));
                        $(document).find('#PLPAP_TOTAL').html(parseFloat(owe.total).toFixed(2));
                        $(document).find('#owe_total_unpaid').html(parseFloat(owe.unpaid).toFixed(2));
                        $(document).find('#owe_paid').html(parseFloat(owe.paid).toFixed(2));
                        $(document).find('#owe_rent').html(parseFloat(owe.total).toFixed(2));
                        $(document).find('.amt').val(parseFloat(owe.total).toFixed(2));
                        $(document).find('#amt_pay').trigger('change');
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
                            $(document).find("#taxable_fees").html("($"+ parseFloat(owe.taxable).toFixed(2) +" taxable)");
                        } else {
                            $(document).find("#taxable_fees").hide();
                        }
                    },
                    error: function (e) {

                    }
                });
            }


            updateIn();

            $('.datatable2').each(function(){
                var url = $(this).attr('data-src');
                $(this).DataTable({
                    "processing": true,
                    "serverSide": true,
                    //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                    "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
                    "bPaginate": false,
                    "bFilter": false,
                    "info": false,
                    "ajax": {
                        "url": url, // ajax source
                    },
                    "lengthMenu": [
                        [10, 20, 50, 100, 150, -1],
                        [10, 20, 50, 100, 150, "All"] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "order": [
                        [1, "asc"]
                    ]// set first column as a default sort by asc
                });
            });

            $('.tablez').dataTable({
                "order": [[ 4, "asc" ]],
                "bFilter" : false,
                "bLengthChange": false,
                "bPaginate":false,
                "info": false
            });




            $('.scroller2').slimScroll({
                height: 670
            });


            if (!jQuery().bootstrapWizard) {
                return;
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

                },


                invalidHandler: function (event, validator) { //display error alert on form submit
                    success.hide();
                    error.show();
                    Metronic.scrollTo(error, -200);
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
                $('.step-title', $('#form_wizard_1')).text('Step ' + (index + 1) + ' of ' + total);
                // set done steps
                jQuery('li', $('#form_wizard_1')).removeClass("done");
                var li_list = navigation.find('li');
                for (var i = 0; i < index; i++) {
                    jQuery(li_list[i]).addClass("done");
                }

                if (current == 1) {
                    $('#form_wizard_1').find('.button-previous').hide();
                    $('#form_wizard_1').find('.button-submit').show();
                } else {
                    $('#form_wizard_1').find('.button-previous').show();
                }

                if (current >= total) {
                    $('#form_wizard_1').find('.button-next').hide();
                    $('#form_wizard_1').find('.button-submit').show();
                } else {
                    $('#form_wizard_1').find('.button-next').show();
                }
                Metronic.scrollTo($('.page-title'));
            }

            // default form wizard
            $('#form_wizard_1').bootstrapWizard({
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
                    $('#form_wizard_1').find('.progress-bar').css({
                        width: $percent + '%'
                    });
                }
            });

            $('#form_wizard_1').find('.button-previous').hide();
            $('#form_wizard_1 .button-submit').click(function () {
                Pace.track(function(){
                    $.ajax({
                        url: 'assets/app/update_settings.php?setting=add_str_item&ct=<?php echo $ct; ?>&rt=true&uuid=<?php echo $_POST['uuid']; ?>&luid=<?php echo $_GET['luid']; ?>',
                        type: 'POST',
                        data: $('#submit_form').serialize(),
                        success: function(p){
                            toastr.success("We've charged this user and added the record to the ledger.");
                            $('.str-content').html("<h3 class='text-center'><i class='fa fa-spin fa-spinner'></i> Loading... </h3>");
                            $.ajax({
                                url: 'assets/pages/sub/sub/storage_py.php?e=mgr&luid=<?php echo $_GET['luid']; ?>',
                                type: 'POST',
                                data: {
                                    uuid: '<?php echo $_POST['uuid']; ?>'
                                }, success: function(data){
                                    $('.str-content').html(data)
                                }, error: function(){
                                    toastr.error("<strong>Logan says:</strong><br/>Hmm..something didn't work right. Refresh your browser.")
                                }
                            });
                        },
                        error: function(p){
                            toastr.error("Ooops. Something went wrong.")
                        }
                    });
                });
            });

        });
    </script>
    <?php
    } elseif($_GET['e'] == 'mgr'){
        $location = mysql_fetch_array(mysql_query("SELECT location_storage_stripe_public, location_storage_days_late, location_storage_days_auction, location_storage_tax, location_storage_deposit, location_storage_creditcard_fee, location_nickname, location_storage_late_fee, location_storage_auction_fee FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'"));
        ?>
        <div class="row">
            <div class="col-md-12">
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
                        <div class="portfolio-block">
                            <div class="col-md-9">
                                <div class="portfolio-text">
                                    <div class="portfolio-text-info">
                                        <h3>
                                            <span class="<?php echo $badge; ?>"><?php echo $msg; ?></span>
                                            <strong>Unit #<?php echo $storage['storage_unit_name']; ?></strong>
                                            [<strong><?php echo $storage['storage_unit_lwh']; ?></strong>]
                                            (next due <strong><?php echo date('M dS', strtotime($contract['contract_next_due'])); ?></strong>)

                                            <a class="btn btn-sm default red-stripe move-out" data-sid="<?php echo $storage['storage_token']; ?>">Move out <i class="fa fa-cube"></i><i class="fa fa-arrow-right"></i></a>
                                            <a data-toggle="modal" href="#contract" class="btn btn-sm default blue-stripe print-contract" data-sid="<?php echo $storage['storage_token']; ?>" data-uuid="<?php echo $_POST['uuid']; ?>">Print contract <i class="fa fa-print"></i> <i class="fa fa-arrow-right"></i></a></h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 portfolio-stat ">
                                <div class="portfolio-info pull-right">RECCURING BILL<span><strong>$<?php echo number_format($storage['storage_price'] + $contract['contract_rate_adj'] , 2); ?></strong><small>/<?php echo $storage['storage_period']; ?></small></span>
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
        </div>
        <div class="btn-group-justified">
            <?php
            $alts = mysql_query("SELECT alt_id, alt_name, alt_address, alt_phone FROM fmo_locations_storages_alts WHERE alt_user_token='".mysql_real_escape_string($_POST['uuid'])."'");
            if(mysql_num_rows($alts) > 0){
                while($alt = mysql_fetch_assoc($alts)){
                    ?>
                    <div class="btn-group">
                        <button class="btn btn-md default red-stripe">
                             <a class="del_alt" data-id="<?php echo $alt['alt_id']; ?>"><i class="fa fa-times"></i></a>
                            <strong><a class="alts_<?php echo $alt['alt_id']; ?>" style="color:#333333" data-inputclass="form-control" data-name="alt_name" data-pk="<?php echo $alt['alt_id']; ?>" data-type="text" data-placement="right" data-title="Enter new name" data-url="assets/app/update_settings.php?setting=alts"><?php echo $alt['alt_name']; ?></a></strong>
                            (<strong><a class="alts_<?php echo $alt['alt_id']; ?>" style="color:#333333" data-inputclass="form-control" data-name="alt_address" data-pk="<?php echo $alt['alt_id']; ?>" data-type="text" data-placement="right" data-title="Enter new name" data-url="assets/app/update_settings.php?setting=alts"><?php echo $alt['alt_address']; ?></a></strong>)
                            <strong><a class="alts_<?php echo $alt['alt_id']; ?>" style="color:#333333" data-inputclass="form-control" data-name="alt_phone" data-pk="<?php echo $alt['alt_id']; ?>" data-type="text" data-placement="right" data-title="Enter new name" data-url="assets/app/update_settings.php?setting=alts"><?php echo clean_phone($alt['alt_phone']); ?></a></strong>
                            <a class="edit" data-edit="alts_<?php echo $alt['alt_id']; ?>" data-reload="alts" data-update="none" data-selec="autoselect"><i class="fa fa-pencil"></i> Edit</a>
                        </button>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
        <hr/>
        <h4 class="bold">Storage Ledger
            <a class="btn btn-md default red-stripe pull-right pym"><span class="bold text-success"><span id="owe_rent"><i class='fa fa-spinner fa-spin'></i></span></span> &nbsp; Take payment <i class="fa fa-arrow-right"></i></a>
            <a class="btn btn-md default red-stripe pull-right trl">Sell retail items <i class="fa fa-arrow-circle-right"></i></a>
            <a class="btn btn-md default red-stripe pull-right print" data-print="#ledger">Print this <i class="fa fa-print"></i></a>
        </h4> <br/>
        <div class="row" id="ledger">
            <div class="col-md-12">
                <ul class="feeds">
                    <?php
                    $findTimeline = mysql_query("SELECT timeline_id, timeline_by_user_token, timeline_type, timeline_reasoning, timeline_timestamp FROM fmo_locations_storages_contracts_timelines WHERE timeline_user_token='".mysql_real_escape_string($_POST['uuid'])."' AND DATE(timeline_timestamp)>='".date('Y-m-d', strtotime('today - 90 days'))."' AND NOT (timeline_type LIKE '%hidden%') ORDER BY timeline_id DESC");
                    $iTotalRecords = mysql_num_rows($findTimeline);

                    $records = array();
                    $records["data"] = array();

                    while($time = mysql_fetch_assoc($findTimeline)) {
                        switch($time['timeline_type']){
                            default: break;
                            case "Payment": $label = "label-success"; $icon = "dollar"; $desc = "text-success"; break;
                            case "Charge": $label = "label-danger"; $icon = "calendar"; $desc = "text-danger"; break;
                            case "Comment": $label = "label-info"; $icon = "comment"; $desc = "text-info"; break;
                            case "Late Fee": $label = "label-warning"; $icon = "exclamation-triangle"; $desc = "text-warning"; break;
                            case "Auction Fee": $label = "badge-purple"; $icon = "gavel"; $desc = "text-danger"; break;
                            case "Moveout": $label = "badge-default"; $icon = "external-link"; $desc = "text-default"; break;
                        }

                        ?>
                        <li>
                            <div class="col1">
                                <div class="cont" style="float: none; margin-right: 10px;">
                                    <div class="cont-col1">
                                        <div class="label label-sm <?php echo $label; ?>">
                                            <i class="fa fa-<?php echo $icon; ?>"></i>
                                        </div>
                                    </div>
                                    <div class="cont-col2">
                                        <div class="desc <?php echo $desc; ?>">
                                            <?php echo $time['timeline_reasoning']; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
                <a class="pull-right" style="margin-right: 20px; margin-top: 10px;"><i class="fa fa-arrow-down"></i> load 50 more</a>
            </div>
        </div>
        <hr/>
        <?php
        $contracts = mysql_query("SELECT contract_storage_token, contract_next_due FROM fmo_locations_storages_contracts WHERE contract_user_token='".mysql_real_escape_string($_POST['uuid'])."' AND contract_status=0");
        if(mysql_num_rows($contracts) > 0){
            while($contract = mysql_fetch_assoc($contracts)) {
                $storage = mysql_fetch_array(mysql_query("SELECT storage_unit_name FROM fmo_locations_storages WHERE storage_token='" . mysql_real_escape_string($contract['contract_storage_token']) . "'"));

                ?>
                <div class="portfolio-block">
                    <div class="col-md-8">
                        <div class="portfolio-text">
                            <div class="portfolio-text-info">
                                <h4><strong>Unit #: <?php echo $storage['storage_unit_name']; ?></strong> - <strong><?php echo $storage['storage_unit_lwh']; ?></strong> (last due on <?php echo date('M dS', strtotime($contract['contract_next_due'])); ?>) </h4>
                                <h5 class="bold"> PAST STORAGE UNIT - <span class="text-danger">NO LONGER ACTIVE.</span></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 portfolio-stat">

                    </div>
                </div>
                <?php

            }
            ?>
            <?php
        } else {
            ?>
            <div class="alert alert-warning"><strong>No past storage units found!</strong> Move tenant out of a unit to see it appear here.</div>
            <?php
        }
        ?>
        <form method="POST" action="" role="form" id="create_alts">
            <div class="modal fade bs-modal-lg" id="create_alt" tabindex="-1" role="basic" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content box red">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                            <h3 class="modal-title font-bold">Add new <strong>Alternate Contact</strong></h3>
                        </div>
                        <div class="modal-body">
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="control-label">Full Name <span class="font-red">*</span></label>
                                    <div class="input-icon">
                                        <i class="fa fa-user"></i>
                                        <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Full Name" name="fullname"/>
                                        <span class="help-block">This will be used as reference for the contact.</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label class="control-label">Phone Number<span class="font-red">*</span></label>
                                        <div class="input-icon">
                                            <i class="fa fa-phone"></i>
                                            <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Phone Number" id="phone" name="phone" value="<?php echo $_GET['p']; ?>"/>
                                            <span class="help-block">This will be the contacts mobile phone number.</span>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="control-label">Address and/or Relationship</label>
                                        <div class="input-icon">
                                            <i class="fa fa-user-secret"></i>
                                            <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Address and/or Relationship" name="address"/>
                                            <span class="help-block">This is pretty much extra notes, but something needs to be here.</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn red pull-right">Add contact to system </button>
                            <button type="button" class="btn default pull-left" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="modal fade bs-modal-lg" id="contract" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content box red">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h3 class="modal-title font-bold">Add new <strong>Comment</strong></h3>
                    </div>
                    <div class="modal-body" id="contract-content">

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn red pull-right print" data-print="#contract-content">Print </button>
                        <button type="button" class="btn default pull-left" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <form method="POST" action="" role="form" id="create_comments">
            <div class="modal fade bs-modal-lg" id="create_comment" tabindex="-1" role="basic" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content box red">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                            <h3 class="modal-title font-bold">Add new <strong>Comment</strong></h3>
                        </div>
                        <div class="modal-body">
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="control-label">Comment <span class="font-red">*</span></label>
                                    <div class="input-icon">
                                        <i class="fa fa-user"></i>
                                        <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Type your comment here..." name="comment"/>
                                        <span class="help-block">This will be used as reference for the contact.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn red pull-right">Add comment to system </button>
                            <button type="button" class="btn default pull-left" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="modal fade bs-modal-lg" id="new_unit" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content box red">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h3 class="modal-title font-bold">Add new <strong>Storage Unit</strong> in <strong><?php echo $location['location_nickname']; ?></strong></h3>
                    </div>
                    <div class="modal-body">
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label">Storage Unit <span class="font-red">*</span></label>
                                <div class="input-icon">
                                    <i class="fa fa-cubes"></i>
                                    <select class="form-control input-sm new-unit">
                                        <option disabled selected value="">Select unit..</option>
                                        <?php
                                        $findStorage = mysql_query("SELECT storage_id, storage_token, storage_available, storage_unit_name, storage_unit_lwh, storage_unit_desc, storage_price, storage_period, storage_occupant, storage_contract_token, storage_last_occupied, storage_status, storage_type_id FROM fmo_locations_storages WHERE storage_location_token='".mysql_real_escape_string($_GET['luid'])."' AND storage_status='Vacant' ORDER BY storage_id DESC") or die(mysql_error());
                                        if(mysql_num_rows($findStorage)){
                                            while($storage = mysql_fetch_assoc($findStorage)) {
                                                $type = mysql_fetch_array(mysql_query("SELECT type_floor, type_desc, type_climate FROM fmo_locations_storages_types WHERE type_id='".mysql_real_escape_string($storage['storage_type_id'])."'"));
                                                if(!empty($storage['storage_occupant'])){
                                                    continue;
                                                } else {
                                                    $name = "N/A";
                                                    $phone = "N/A";
                                                    $bal['unpaid'] = 0.00;
                                                    $d = 0;
                                                }
                                                ?>
                                                <option value="<?php echo $storage['storage_token']; ?>">
                                                    Unit #: <?php echo $storage['storage_unit_name']; ?> &nbsp; Floor <?php echo $type['type_floor'].", ".$type['type_desc']; ?> - <?php echo $storage['storage_unit_lwh']; ?> [Climate: <?php echo $type['type_climate']; ?>]
                                                </option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                    <span class="help-block">Select a unit to be taken to the storage configuration wizard.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn default pull-left" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            $(document).ready(function() {
                function updateIn(){
                    $.ajax({
                        url: 'assets/app/api/storage.php?type=inv_c&luid=<?php echo $_GET['luid']; ?>',
                        type: 'POST',
                        data: {
                            uuid: '<?php echo $_POST['uuid']; ?>'
                        },
                        success: function(m){
                            var owe = JSON.parse(m);
                            if(owe.unpaid < 0){
                                var due     = "Credit";
                                var unpaid  = owe.unpaid * -1;
                            } else {var due = "Due"; var unpaid = owe.unpaid; }
                            $(document).find('#owe_rent').html(due + " $" + parseFloat(unpaid).toFixed(2));
                        },
                        error: function(e){

                        }
                    });
                }
                $('.move-out').on('click', function() {
                    var sid = $(this).data('sid');
                    var but = $(this);
                    swal({
                        title: 'Are you sure?',
                        text: 'You will not be able to undo this!',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, move out!',
                        cancelButtonText: 'No, stay!'
                    }).then(function() {


                        if(sid.length > 0){
                            $.ajax({
                                url: 'assets/app/update_settings.php?setting=mv_out',
                                type: 'POST',
                                data: {
                                    sid: sid
                                },
                                success: function(data){
                                    but.closest('.portfolio-block').appendTo(".past");
                                    but.closest('.portfolio-block').remove();
                                    but.remove();
                                    toastr.success("<strong>Logan says:</strong><br/> "+data);
                                },
                                error: function() {

                                }
                            });
                        }

                        swal(
                            'Moved out!',
                            'The tenant has been moved out.',
                            'success'
                        );

                    }, function(dismiss) {
                        // dismiss can be 'overlay', 'cancel', 'close', 'esc', 'timer'
                        if (dismiss === 'cancel') {
                            swal(
                                'Cancelled',
                                'Your tenant is safe :)',
                                'error'
                            )
                        }
                    });

                });
                $(".new-unit").select2({
                    placeholder: 'Select new unit..'
                }).on('change', function() {
                    var value = $(this).val();
                    if(value.length > 0){
                        $.ajax({
                            url: 'assets/pages/profile.php?uuid=<?php echo $_POST['uuid']; ?>&su='+value+'',
                            success: function(data) {
                                $('#page_content').html(data);
                                document.title = "Storage Wizard - www.FORMOVERSONLY.com";
                            },
                            error: function() {
                                toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                            }
                        });
                    }
                });
                $('#create_alts').validate({
                    errorElement: 'span', //default input error message container
                    errorClass: 'help-block', // default input error message class
                    focusInvalid: false, // do not focus the last invalid input
                    ignore: "",
                    rules: {
                        fullname: {
                            required: true
                        },
                        phone: {
                            required: true,
                        },
                        address: {
                            required: true,
                        }
                    },


                    invalidHandler: function(event, validator) { //display error alert on form submit

                    },

                    highlight: function(element) { // hightlight error inputs
                        $(element)
                            .closest('.form-group').addClass('has-error'); // set error class to the control group
                    },

                    success: function(label) {
                        label.closest('.form-group').removeClass('has-error');
                    },


                    submitHandler: function(form) {
                        $.ajax({
                            url: 'assets/app/add_setting.php?setting=alt_contact&uuid=<?php echo $_POST['uuid']; ?>',
                            type: "POST",
                            data: $('#create_alts').serialize(),
                            success: function(data) {
                                toastr.success("<strong>Logan says</strong>:<br/>Nice! We've added your contact to the system, let me refresh the page for you.");
                                $.ajax({
                                    url: 'assets/pages/sub/sub/storage_py.php?e=mgr&luid=<?php echo $_GET['luid']; ?>',
                                    type: 'POST',
                                    data: {
                                        uuid: '<?php echo $_POST['uuid']; ?>'
                                    }, success: function(data){
                                        $('.str-content').html(data)
                                    }, error: function(){
                                        toastr.error("<strong>Logan says:</strong><br/>Hmm..something didn't work right. Refresh your browser.")
                                    }
                                });
                            },
                            error: function() {
                                toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                            }
                        });
                    }
                });
                $('#create_comments').validate({
                    errorElement: 'span', //default input error message container
                    errorClass: 'help-block', // default input error message class
                    focusInvalid: false, // do not focus the last invalid input
                    ignore: "",
                    rules: {
                        comment: {
                            required: true
                        }
                    },


                    invalidHandler: function(event, validator) { //display error alert on form submit

                    },

                    highlight: function(element) { // hightlight error inputs
                        $(element)
                            .closest('.form-group').addClass('has-error'); // set error class to the control group
                    },

                    success: function(label) {
                        label.closest('.form-group').removeClass('has-error');
                    },


                    submitHandler: function(form) {
                        $.ajax({
                            url: 'assets/app/add_setting.php?setting=su_comment&uuid=<?php echo $_POST['uuid']; ?>',
                            type: "POST",
                            data: $('#create_comments').serialize(),
                            success: function(data) {
                                toastr.success("<strong>Logan says</strong>:<br/>Nice! We've added your comment to the system, let me refresh the page for you.");
                                $.ajax({
                                    url: 'assets/pages/sub/sub/storage_py.php?e=mgr&luid=<?php echo $_GET['luid']; ?>',
                                    type: 'POST',
                                    data: {
                                        uuid: '<?php echo $_POST['uuid']; ?>'
                                    }, success: function(data){
                                        $('.str-content').html(data)
                                    }, error: function(){
                                        toastr.error("<strong>Logan says:</strong><br/>Hmm..something didn't work right. Refresh your browser.")
                                    }
                                });
                            },
                            error: function() {
                                toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                            }
                        });
                    }
                });
                $('.del_alt').on('click', function() {
                    var id = $(this).data('id');
                    var it = $(this);
                    $.ajax({
                        url: 'assets/app/update_settings.php?setting=del_alt',
                        type: 'POST',
                        data: {
                            id: id
                        }, success: function(data){
                            it.closest('.btn-group').remove();
                        }, error: function(){
                            toastr.error("<strong>Logan says:</strong><br/>Hmm..something didn't work right. Refresh your browser.")
                        }
                    });
                });
                $('.pym').on('click', function(){
                    $.ajax({
                        url: 'assets/pages/sub/sub/storage_py.php?e=pYt&luid=<?php echo $_GET['luid']; ?>',
                        type: 'POST',
                        data: {
                            uuid: '<?php echo $_POST['uuid']; ?>'
                        }, success: function(data){
                            $('.str-content').html(data)
                        }, error: function(){
                            toastr.error("<strong>Logan says:</strong><br/>Hmm..something didn't work right. Refresh your browser.")
                        }
                    });
                });
                $('.trl').on('click', function(){
                    $.ajax({
                        url: 'assets/pages/sub/sub/storage_py.php?e=rTl&luid=<?php echo $_GET['luid']; ?>',
                        type: 'POST',
                        data: {
                            uuid: '<?php echo $_POST['uuid']; ?>'
                        }, success: function(data){
                            $('.str-content').html(data)
                        }, error: function(){
                            toastr.error("<strong>Logan says:</strong><br/>Hmm..something didn't work right. Refresh your browser.")
                        }
                    });
                });

                $('#contract').on('show.bs.modal', function(e) {
                    //get data-id attribute of the clicked element
                    var uuid = $(e.relatedTarget).data('uuid');
                    var sid  = $(e.relatedTarget).data('sid');
                    $.ajax({
                        url: 'assets/app/api/storage.php?type=contract&uuid='+ uuid +'&su='+ sid +'',
                        type: 'POST',
                        success: function(s){
                            $('#contract-content').html(s);
                        }, error: function(e){

                        }
                    });
                });
                updateIn();
            });
        </script>
        <?php
    }
}