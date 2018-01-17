<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 10/29/2017
 * Time: 11:33 PM
 */
require '../../app/init.php';
if($_GET['px'] == 'lp'){
    $event      = mysql_fetch_array(mysql_query("SELECT event_location_token, event_name, event_status, event_phone, event_truckfee, event_laborrate, event_countyfee, event_comments, event_date_start, event_date_end, event_company_token, event_token, event_laborrate_rate, event_weekend_upcharge_rate, event_user_token FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($_GET['ev'])."'"));
    $companyinf = mysql_fetch_array(mysql_query("SELECT user_stripe_pk, user_stripe_sk FROM fmo_users WHERE user_company_token='".mysql_real_escape_string($event['event_company_token'])."'"));
    $user       = mysql_fetch_assoc(mysql_query("SELECT user_fname, user_lname, user_email FROM fmo_users WHERE user_token='".mysql_real_escape_string($event['event_user_token'])."'"));
    if(!empty($event['event_token'])){
        $location = mysql_fetch_array(mysql_query("SELECT location_sales_tax, location_creditcard_fee FROM fmo_locations WHERE location_token='".mysql_real_escape_string($event['event_location_token'])."'"));

        if(!empty($location['location_sales_tax'])){
            $tax = $location['location_sales_tax'];
        } else {$tax = 0;}

        $findItems = mysql_query("SELECT item_total, item_taxable, item_commission FROM fmo_locations_events_items WHERE item_event_token='".mysql_real_escape_string($event['event_token'])."'");
        $iTotalRecords = mysql_num_rows($findItems);

        $findPaid = mysql_query("SELECT payment_amount, payment_type FROM fmo_locations_events_payments WHERE payment_event_token='".mysql_real_escape_string($event['event_token'])."'");
        $bTotalRecords = mysql_num_rows($findPaid);


        $total = array();
        $total['sub_total'] = 0.00;
        $total['tax']       = 0.00;
        $total['taxable']   = 0.00;
        $total['cc_fees']   = 0.00;
        $total['total']     = 0.00;
        $total['paid']      = 0.00;
        $total['unpaid']    = 0.00;
        if($iTotalRecords > 0){
            while($item = mysql_fetch_assoc($findItems)){
                $total['sub_total'] += $item['item_total'];
                if($item['item_taxable'] == 1){
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
        } else {
            $total['total']     = 0.00;
            $total['sub_total'] = 0.00;
        }

        if($bTotalRecords > 0){
            while($paid = mysql_fetch_assoc($findPaid)){
                $void = explode(" - ", $paid['payment_type']);
                if($void[1] != 'VOIDED' && $void[0] != 'Invoice'){
                    $total['paid'] += $paid['payment_amount'];
                    if($void[0] == 'Credit/Debt' && $void[1] != 'Booking Fee'){
                        $total['total']   += ($paid['payment_amount'] / 1.03) * .03;
                        $total['cc_fees'] += ($paid['payment_amount'] / 1.03) * .03;
                    }
                }

            }
            $total['unpaid'] = number_format($total['total'] - $total['paid'], 2, '.', '');
        } else {
            $total['unpaid'] = number_format($total['total'], 2, '.', '');
            $total['paid']   = 0.00;
        }
    }
    if($total['unpaid'] > 0){
        ?>
        <div class="col-md-12" style="background-color: white;">
            <div class="portlet">
                <div class="portlet-body" id="invoice">
                            <h3 class="text-center" style="margin-bottom: 50px;">You owe <strong class="text-danger owe_total_unpaid">$</strong> and have already paid <strong class="text-success owe_paid"></strong></h3>
                            <div class="invoice">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-container">
                                            <form role="form" id="add_service_rate">
                                                <table class="table table-striped table-hover datatable sales" data-src="../app/api/event.php?type=sales&ev=<?php echo $event['event_token']; ?>&luid=<?php echo $event['event_location_token']; ?>&VmP=LoL">
                                                    <thead>
                                                    <tr role="row" class="heading">
                                                        <th>
                                                            Item
                                                        </th>
                                                        <th class="text-right">
                                                            Qty
                                                        </th>
                                                        <th class="text-right">
                                                            Unit Cost
                                                        </th>
                                                        <th class="text-right">
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
                                    <div class="col-xs-12 invoice-block">
                                        <ul class="list-unstyled amounts text-right" style="margin-bottom: 0;">
                                            <li>
                                                Sub Total: <h3 style="display: inline" class="text-danger bold">$<span id="owe_sub_total"></span></h3>
                                            </li>
                                            <li>
                                                <small class="bold" id="taxable_fees"></small> Taxes Due:  <h3 style="display: inline;" class="text-danger bold">$<span id="owe_tax"></span></h3>
                                            </li>
                                            <li id="cc_fees">
                                                Credit Card Fees: <h3 style="display: inline;" class="text-danger bold">$<span id="owe_cc_fees"><?php echo number_format($total['cc_fees'], 2) ?></span></h3>
                                            </li>
                                            <li>
                                                Grand Total: <h3 style="display: inline;" class="text-danger bold">$<span id="owe_total"></span></h3>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <hr/>
                                <div class="row">
                                    <div class="col-xs-12 invoice-block">
                                        <ul class="list-unstyled amounts text-right">
                                            <li>
                                                Paid: <h3 style="display: inline;" class="text-success bold">$<span class="owe_paid"></span></h3>
                                            </li>
                                            <li>
                                                Amount Due: <h3 style="display: inline" class="text-danger bold">$<span class="owe_total_unpaid"></span></h3>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                <form id="submit_form">
                        <div class="form-group">
                            <label class="control-label visible-ie8 visible-ie9">Carderholder Name <span class="required">*</span></label>
                            <div class="input-icon">
                                <i class="fa fa-user"></i>
                                <input type="text" data-stripe="name" class="form-control input-sm card_name" placeholder="Cardholder Name" value="<?php echo name($event['event_user_token']); ?>">
                            </div>
                        </div>
                        <div class="form-inline">
                            <div class="form-group">
                                <label class="control-label visible-ie8 visible-ie9">Credit/Debt Card Number <span class="required">*</span></label>
                                <div class="input-icon">
                                    <i class="fa fa-credit-card"></i>
                                    <input type="text" data-stripe="number" class="form-control input-sm card_num" placeholder="Card Number">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label visible-ie8 visible-ie9">Exp <span class="required">*</span></label>
                                <div class="input-icon">
                                    <i class="fa fa-calendar"></i>
                                    <input type="text" data-stripe="exp" class="form-control input-sm exp_date" placeholder="Exp">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label visible-ie8 visible-ie9">CVC <span class="required">*</span></label>
                                <div class="input-icon">
                                    <i class="fa fa-sort-numeric-asc"></i>
                                    <input type="text" data-stripe="cvc" class="form-control input-sm cvc_num" placeholder="CVC" >
                                </div>
                            </div>
                        </div>
                        <input type="text" name="notes" id="cc_notes" class="hidden"/>
                        <input type="text" name="charge" id="charge" class="hidden"/>
                        <input type="text" name="amount" id="amount" class="hidden" value=""/>
                        <button id="checkout" type="button" class="btn btn-block red "><span class="error-handler">Pay $<strong class="owe_total_unpaid"></strong> now!</span> <i class="fa fa-credit-card"></i></button>
                    </form>
            </div>
        </div>
        <script type="text/javascript">
            Stripe.setPublishableKey('<?php echo $companyinf['user_stripe_pk']; ?>');

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
                    var token  = response.id;
                    var amount = $('#amount').val();
                    // Insert the token ID into the form so it gets submitted to the server:
                    //$form.append($('<input type="hidden" name="auth">').val(token));

                    $.ajax({
                        url: '../app/checkout.php?cuid=<?php echo $event['event_company_token']; ?>',
                        type: 'post',
                        data: {
                            token: token,
                            amount: amount.replace('.', ''),
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
                                $.ajax({
                                    url: 'assets/app/update_settings.php?setting=pymt&ev=<?php echo $event['event_token']; ?>&uuid=<?php echo $event['event_user_token']; ?>&ckpay=true&luid=<?php echo $event['event_location_token']; ?>',
                                    type: 'POST',
                                    data: $('#submit_form').serialize(),
                                    success: function(p){
                                        toastr.success("Nice, we took your payment!");
                                        location.reload();
                                    },
                                    error: function(p){
                                        toastr.error("Ooops. Something went wrong.");
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
                    Stripe.card.createToken($form, stripeResponseHandler);

                    // Prevent the form from being submitted:
                    return false;
                });
            });
            $('.datatable').each(function(){
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
            $.ajax({
                url: '../app/api/event.php?type=inv&luid=<?php echo $event['event_location_token']; ?>&mpay=true',
                type: 'POST',
                data: {
                    event: '<?php echo $event['event_token']; ?>'
                },
                success: function(m){
                    var owe = JSON.parse(m);
                    $(document).find('#owe_sub_total').html(parseFloat(owe.sub_total).toFixed(2));
                    $(document).find('#owe_tax').html(parseFloat(owe.tax).toFixed(2));
                    $(document).find('#owe_total').html(parseFloat(owe.total).toFixed(2));
                    $(document).find('.owe_total_unpaid').html(parseFloat(owe.unpaid).toFixed(2));
                    $(document).find('.owe_paid').html(parseFloat(owe.paid).toFixed(2));
                    $(document).find('#amount').attr('value', owe.amount);
                    if(parseFloat(owe.cc_fees).toFixed(2) > 0){
                        $(document).find("#owe_cc_fees").html(parseFloat(owe.cc_fees).toFixed(2));
                    } else {

                    }
                    if(parseFloat(owe.taxable).toFixed(2) > 0){
                        $(document).find("#taxable_fees").show();
                        $(document).find("#taxable_fees").html("($"+ parseFloat(owe.taxable).toFixed(2) +" taxable)");
                    } else {
                        $(document).find("#taxable_fees").hide();
                    }
                },
                error: function(e){

                }
            });
        </script>
        <?php
    } else {
        ?>
        <div class="login-form" style="padding: 20px;">
            <center>
                <h3 class="form-title"><i class="fa fa-check" style="font-size: 25px;"></i><strong>Payment</strong> completed.</h3>
                <small>
                    You have no balance due, which tells the system you have paid your bill. Thank you for using our services.
                    <br/><br/>
                    <span class="badge badge-danger"><?php echo $event['event_name']; ?></span> <br/> <br/>
                </small>
                <br/>
            </center>
        </div>
        <?php
    }
}
