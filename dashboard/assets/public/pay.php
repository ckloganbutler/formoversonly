<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<?php
require '../app/init.php';
$event      = mysql_fetch_array(mysql_query("SELECT event_location_token, event_name, event_status, event_phone, event_truckfee, event_laborrate, event_countyfee, event_comments, event_date_start, event_date_end, event_company_token, event_token, event_laborrate_rate, event_weekend_upcharge_rate, event_user_token FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($_GET['ev'])."'"));
$companyinf = mysql_fetch_array(mysql_query("SELECT user_stripe_pk, user_stripe_sk FROM fmo_users WHERE user_company_token='".mysql_real_escape_string($event['event_company_token'])."'"));
$user       = mysql_fetch_assoc(mysql_query("SELECT user_fname, user_lname, user_email FROM fmo_users WHERE user_token='".$event['event_user_token']."'"));
if(!empty($event['event_token'])){
    $location = mysql_fetch_array(mysql_query("SELECT location_sales_tax, location_creditcard_fee FROM fmo_locations WHERE location_token='".mysql_real_escape_string($event['event_location_token'])."'"));

    if(!empty($location['location_sales_tax'])){
        $tax = $location['location_sales_tax'];
    } else {$tax = 0;}

    $findItems = mysql_query("SELECT item_total, item_taxable FROM fmo_locations_events_items WHERE item_event_token='".mysql_real_escape_string($_GET['ev'])."'");
    $iTotalRecords = mysql_num_rows($findItems);
    $findPaid = mysql_query("SELECT payment_amount, payment_type FROM fmo_locations_events_payments WHERE payment_event_token='".mysql_real_escape_string($_GET['ev'])."'");
    $bTotalRecords = mysql_num_rows($findPaid);

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
            if($void[1] != 'VOIDED'){
                $total['paid'] += $paid['payment_amount'];
            }
        }
        $total['unpaid'] = $total['total'] - $total['paid'];
    } else {
        $total['unpaid'] = $total['total'];
        $total['paid']   = 0;
    }
}
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
    if($_GET['ty'] == 'yap'){
        if($total['unpaid'] > 0){
            ?>
            <div class="login-form">
                <form id="submit_form">
                    <h3 class="text-center" style="margin-top: 0px; margin-bottom: 30px;"><strong><?php echo $event['event_name']; ?></strong> <br/> <span class="badge badge-danger">Paying your bill</span></h3>
                    <div class="portlet">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-file"></i>Invoice <small><span class="font-red">|</span></small> <button class="btn btn-xs red-stripe print" data-print="#plain_pap"><i class="fa fa-print"></i> Print</button>
                            </div>
                        </div>
                        <div class="portlet-body" id="invoice">
                            <div class="invoice">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-container">
                                            <form role="form" id="add_service_rate">
                                                <table class="table table-striped table-hover datatable" id="sales">
                                                    <thead>
                                                    <tr role="row" class="heading">
                                                        <th>
                                                            Item
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
                                                    <?php
                                                    $findItems = mysql_query("SELECT item_id, item_item, item_desc, item_qty, item_cost, item_total, item_redeemable FROM fmo_locations_events_items WHERE item_event_token='".mysql_real_escape_string($event['event_token'])."'");
                                                    $iTotalRecords = mysql_num_rows($findItems);

                                                    $records = array();
                                                    $records["data"] = array();

                                                    while($items = mysql_fetch_assoc($findItems)) {
                                                        ?>
                                                        <tr>
                                                            <td class="text-success"><?php echo $items['item_item']; ?></td>
                                                            <td class="text-success"><?php echo $items['item_desc']; ?></td>
                                                            <td class="text-success"><?php echo $items['item_qty']; ?></td>
                                                            <td class="text-success"><?php echo $items['item_cost']; ?></td>
                                                            <td class="text-danger pull-right"><?php echo $items['item_total']; ?></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    ?>
                                                    </tbody>
                                                </table>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-6">
                                        <div class="well">
                                            <address>
                                                <strong><?php echo $event['event_name']; ?></strong><br>
                                                <abbr title="Phone">P:</abbr> <?php echo clean_phone($event['event_phone']); ?> </address>
                                            <address>
                                                <strong><?php echo $user['user_fname']." ".$user['user_lname']; ?></strong><br>
                                                <a href="mailto:#">
                                                    <?php echo $user['user_email']; ?>
                                                </a>
                                            </address>
                                        </div>
                                    </div>
                                    <div class="col-xs-6 invoice-block">
                                        <ul class="list-unstyled amounts" style="margin-bottom: 0;">
                                            <li>
                                                Sub Total: <h3 style="display: inline" class="text-danger bold">$<span id="owe_sub_total"></span></h3>
                                            </li>
                                            <li>
                                                <small class="bold" id="taxable_fees"></small> Taxes Due:  <h3 style="display: inline;" class="text-danger bold">$<span id="owe_tax"></span></h3>
                                            </li>
                                            <li id="cc_fees">
                                                Credit Card Fees: <h3 style="display: inline;" class="text-danger bold">$<span id="owe_cc_fees"></span></h3>
                                            </li>
                                            <li>
                                                Grand Total: <h3 style="display: inline;" class="text-danger bold">$<span id="owe_total"></span></h3>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="row" id="payments-maked">
                                    <div class="col-md-12">
                                        <div class="table-container">
                                            <table class="table table-striped table-hover datatable" id="paid" data-src="../app/api/event.php?type=payments&ev=<?php echo $event['event_token']; ?>&luid=<?php echo $event['event_location_token']; ?>">
                                                <thead>
                                                <tr role="row" class="heading">
                                                    <th>
                                                        Tender Type
                                                    </th>
                                                    <th>
                                                        Notes
                                                    </th>
                                                    <th>Taken By</th>
                                                    <th class="text-right">
                                                        Tender Amount
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-4">

                                    </div>
                                    <div class="col-xs-8 invoice-block">
                                        <ul class="list-unstyled amounts">
                                            <li>
                                                Paid: <h3 style="display: inline;" class="text-success bold">$<span id="owe_paid"></span></h3>
                                            </li>
                                            <li>
                                                Amount Due: <h3 style="display: inline" class="text-danger bold">$<span id="owe_total_unpaid"></span></h3>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h4 class="text-center" style="margin-bottom: 50px;">You owe <br/><strong class="text-danger">$<?php echo number_format($total['unpaid'] + ($total['unpaid'] * $location['location_creditcard_fee']), 2); ?></strong><br/>and have already paid<br/><strong class="text-success"><?php echo number_format($total['paid'], 2); ?></strong></h4>
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
                    <input type="text" name="amount" id="amount" class="hidden" value="<?php echo number_format($total['unpaid'] + ($total['unpaid'] * $location['location_creditcard_fee']), 2); ?>"/>
                    <button id="checkout" class="btn btn-block red "><span class="error-handler">Pay now!</span> <i class="fa fa-credit-card"></i></button>
                </form>
            </div>
            <?php
        } else {
            ?>
            <div class="login-form">
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
                var token  = response.id;
                var amount = <?php echo number_format($total['unpaid'] + ($total['unpaid'] * $location['location_creditcard_fee']), 2, '.', ''); ?>;
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


        $('.card_num').inputmask("mask", {
            "mask": "9999 9999 9999 9999",
            "removeMaskOnSubmit": false
        });
        $('.exp_date').inputmask("mask", {
            "mask": "99/99",
            "removeMaskOnSubmit": false
        });
        $('.cvc_num').inputmask("mask", {
            "mask": "999"
        });
    });
</script>
</body>
</html>