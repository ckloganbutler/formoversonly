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
    ?>
    <div class="col-md-12">
        <div class="portlet" id="payments">
            <div class="portlet-body form">
                <form action="#" class="form-horizontal" id="submit_form" method="POST">
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
                                    <a href="#tab3" data-toggle="tab" class="step">
                                        <span class="number">2 </span>
                                        <span class="desc"><i class="fa fa-check"></i> Payment Finalization </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#tab4" data-toggle="tab" class="step">
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
                                    <h3>Payment Details <small class="hidden-xs hidden-sm"><span class="text-danger">| </span>verify invoice matches amount due.</small> <span class="pull-right">Amount due: <strong><span class="text-success">$X,XXX</span></strong></span></h3>
                                </div>
                                <div class="tab-pane" id="tab3">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-12">
                                            <div class="form-group">
                                                <label class="control-label col-md-3">Tender Type <span class="required">*</span></label>
                                                <div class="col-md-9">
                                                    <select class="form-control input-sm type" name="type" data-target=".tender-inputs">
                                                        <option disabled selected value="">Select one..</option>
                                                        <option value="Cash" data-show=".cash">Cash</option>
                                                        <option value="Check" data-show=".chec">Check</option>
                                                        <option value="Credit/Debt" data-show=".cc">Credit/Debt Card (ckPay&trade;)</option>
                                                        <option value="Other" data-show=".other">Credit/Debt Card (Other Payment Processor)</option>
                                                    </select>
                                                    <span class="help-block">This should the type of payment in which the customer choose to use.</span>
                                                </div>
                                            </div>
                                            <div class="tender-inputs">
                                                <div class="form-group cash hidden">
                                                    <label class="control-label col-md-3">Tender Amount <span class="required">*</span></label>
                                                    <div class="col-md-9">
                                                        <input type="number" step="any" class="form-control input-sm" name="amount" placeholder="XXXX.XX">
                                                        <span class="help-block">This is the amount you are receiving in cash.</span>
                                                    </div>
                                                </div>
                                                <div class="form-group chec hidden">
                                                    <label class="control-label col-md-3">Tender Amount <span class="required">*</span></label>
                                                    <div class="col-md-9">
                                                        <input type="number" step="any" class="form-control input-sm" name="amount" placeholder="XXXX.XX">
                                                        <span class="help-block">This is the amount you are receiving from this check.</span>
                                                    </div>
                                                </div>
                                                <div class="form-group cc hidden">
                                                    <label class="control-label col-md-3">Tender Amount <span class="required">*</span></label>
                                                    <div class="col-md-9">
                                                        <input type="number" step="any" class="form-control input-sm" name="amount" placeholder="XXXX.XX">
                                                        <span class="help-block">This is the amount you are charging the card for.</span>
                                                    </div>
                                                </div>
                                                <div class="form-group other hidden">
                                                    <label class="control-label col-md-3">Tender Amount <span class="required">*</span></label>
                                                    <div class="col-md-9">
                                                        <input type="number" step="any" class="form-control input-sm" name="amount" placeholder="XXXX.XX">
                                                        <span class="help-block">This is the amount you are receiving from another type of payment.</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab4">

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

                });
            }).hide();

            $('.type').on('change', function() {
                var type    = $(this).val();
                var target  = $(this).data('target');
                var show   =  $("option:selected", this).data('show');
                $(target).children().addClass('hidden');
                $(show).removeClass('hidden');
                $("input:hidden").attr('disabled', 'disabled');
                $("input:visible").removeAttr('disabled');
                $('input[name="amount"]').focus();
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
}

?>

