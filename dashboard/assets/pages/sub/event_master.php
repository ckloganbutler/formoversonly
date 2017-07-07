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
        $findItems = mysql_query("SELECT item_total, item_taxable FROM fmo_locations_events_items WHERE item_event_token='".mysql_real_escape_string($_GET['ev'])."'");
        $iTotalRecords = mysql_num_rows($findItems);

        $total = array();
        if($iTotalRecords > 0){
            while($item = mysql_fetch_assoc($findItems)){
                $total['sub_total'] += $item['item_total'];
                if($item['item_taxable'] == 1){
                    $total['tax']   += $item['item_total'] * .07;
                }
            }
            $total['total'] = $total['sub_total'] + $total['tax'];
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
                                        <h3>Payment Details <small class="hidden-xs hidden-sm"><span class="text-danger">| </span>collecting amount due.</small> <span class="pull-right">Amount due: <strong><span class="text-success">$<?php echo $total['total']; ?></span></strong></span></h3>
                                        <hr/>
                                        <div class="form-group form-md-line-input">
                                            <select class="form-control type" name="type" data-target=".tender-inputs">
                                                <option disabled selected value="">Select one..</option>
                                                <option value="Cash" data-show=".cash" data-input="cash">Cash</option>
                                                <option value="Check" data-show=".chec" data-input="chec">Check</option>
                                                <option value="Credit/Debt" data-show=".cc" data-input="cc">Credit/Debt Card (ckPay&trade;)</option>
                                                <option value="Other" data-show=".other" data-input="other">Credit/Debt Card (Other Payment Processor)</option>
                                            </select>
                                            <label for="form_control_1">Tender Type</label>
                                            <span class="help-block"></span>
                                        </div>
                                        <div class="tender-inputs">
                                            <div class="form-group form-md-line-input cash hidden">
                                                <input type="number" step="any" class="form-control input-sm" name="amount" id="cash" placeholder="<?php echo number_format($total['total'], 2); ?>">
                                                <label for="form_control_1">Tender Amount</label>
                                                <span class="help-block"></span>
                                            </div>
                                            <div class="form-group form-md-line-input cash hidden">
                                                <input type="text" step="any" class="form-control input-sm" name="notes" placeholder="...">
                                                <label for="form_control_1">Tender Notes</label>
                                                <span class="help-block"></span>
                                            </div>
                                            <div class="form-group form-md-line-input chec hidden">
                                                <input type="number" step="any" class="form-control input-sm" name="amount" id="chec" placeholder="<?php echo number_format($total['total'], 2); ?>">
                                                <label for="form_control_1">Tender Amount</label>
                                                <span class="help-block"></span>
                                            </div>
                                            <div class="form-group form-md-line-input chec hidden">
                                                <input type="number" step="any" class="form-control input-sm" name="notes" placeholder="...">
                                                <label for="form_control_1">Check Number</label>
                                                <span class="help-block"></span>
                                            </div>
                                            <div class="form-group form-md-line-input cc hidden">
                                                <input type="text" step="any" class="form-control input-sm" name="amount" id="cc" placeholder="<?php echo number_format($total['total'], 2); ?>">
                                                <label for="form_control_1">Tender Amount</label>
                                                <span class="help-block"></span>
                                            </div>
                                            <div class="form-group cc hidden">
                                                <button id="checkout" class="btn btn-block red ">Pay using card <strong>number</strong>, <strong>expiration</strong>, and <strong>CVC</strong> <i class="fa fa-credit-card"></i></button>
                                            </div>
                                            <div class="form-group form-md-line-input other hidden">
                                                <input type="number" step="any" class="form-control input-sm" name="amount" id="other" placeholder="<?php echo number_format($total['total'], 2); ?>">
                                                <label for="form_control_1">Tender Amount</label>
                                                <span class="help-block"></span>
                                            </div>
                                            <div class="form-group form-md-line-input other hidden" style="margin-bottom: 48px">
                                                <input type="text" step="any" class="form-control input-sm" name="notes" placeholder="...">
                                                <label for="form_control_1">Approval Number</label>
                                                <span class="help-block"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab2">
                                        <h3>Payment Details <small class="hidden-xs hidden-sm"><span class="text-danger">| </span>collecting amount due.</small> <span class="pull-right">Amount due: <strong><span class="text-success">$<?php echo number_format($total['total'], 2); ?></span></strong></span></h3>
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
                                        <button href="javascript:;" class="btn blue button-next pull-right">
                                            Continue <i class="m-icon-swapright m-icon-white"></i>
                                        </button>
                                        <button href="javascript:;" class="btn green button-submit pull-right" type="submit" name="status" value="1">
                                            Submit <i class="m-icon-swapright m-icon-white"></i>
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
                                $('#paid').DataTable().ajax.reload();
                            },
                            error: function(p){
                                toastr.error("Ooops. Something went wrong.")
                            }
                        })
                    });
                }).hide();


                $('.type').on('change', function() {
                    var type    = $(this).val();
                    var target  = $(this).data('target');
                    var show   =  $("option:selected", this).data('show');
                    $(target).children().addClass('hidden');
                    $(show).removeClass('hidden');
                    $(".tender-inputs input:hidden").attr('disabled', 'disabled');
                    $(".tender-inputs input:visible").removeAttr('disabled');
                    $('.tender-inputs input:visible[name="amount"]').focus();
                    /*
                     if(type == 'Credit/Debt'){
                     $('#cc_btn').addClass("hide");
                     } else {
                     $('#cc_btn').removeClass("hide");
                     }*/
                });
            });
        </script>
        <?php
    } elseif($_POST['type'] == 'iv'){

    }
}
