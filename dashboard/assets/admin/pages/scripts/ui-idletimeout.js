var UIIdleTimeout = function () {

    return {

        //main function to initiate the module
        init: function () {

            // cache a reference to the countdown element so we don't have to query the DOM for it on each ping.
            var $countdown;
            var $uuid = $('html').attr('data-uuid');

            $('body').append('<div class="modal fade" id="idle-timeout-dialog" data-backdrop="static"><div class="modal-dialog modal-small"><div class="modal-content"><div class="modal-header"><h4 class="modal-title">Your session is about to expire.</h4></div><div class="modal-body"><p><i class="fa fa-warning"></i> You session will be locked in <span id="idle-timeout-counter"></span> seconds.</p><p>Do you want to continue your session?</p></div><div class="modal-footer"><button id="idle-timeout-dialog-logout" type="button" class="btn btn-default">No, Logout</button><button id="idle-timeout-dialog-keepalive" type="button" class="btn btn-primary" data-dismiss="modal">Yes, Keep Working</button></div></div></div></div>');

            Pace.ignore(function(){
                $.idleTimeout('#idle-timeout-dialog', '.modal-content button:last', {
                    idleAfter: 900, // 5 seconds
                    timeout: 30000, //30 seconds to timeout
                    pollingInterval: 900, // 5 seconds
                    keepAliveURL: 'assets/app/keepalive.php',
                    serverResponseEquals: 'OK',
                    onTimeout: function(){
                        window.location = "../lock.php?uuid="+$uuid;
                    },
                    onIdle: function(){
                        $('#idle-timeout-dialog').modal('show');
                        $countdown = $('#idle-timeout-counter');

                        $('#idle-timeout-dialog-keepalive').on('click', function () {
                            $('#idle-timeout-dialog').modal('hide');
                        });

                        $('#idle-timeout-dialog-logout').on('click', function () {
                            $('#idle-timeout-dialog').modal('hide');
                            $.idleTimeout.options.onTimeout.call(this);
                        });
                    },
                    onCountdown: function(counter){
                        $countdown.html(counter); // update the counter
                    }
                });
            });
            // start the idle timer plugin

            $('#update_password').validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",
                rules: {
                    current_password: {
                        required: true,
                        remote: 'dashboard/assets/app/search_pass.php'
                    },
                    npassword: {
                        required: true
                    },
                    rpassword: {
                        equalTo: "#npassword"
                    }
                },

                messages: {
                    current_password: {
                        remote: "Password is not correct. Try again."
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
                    label.remove();
                },


                submitHandler: function(form) {
                    $.ajax({
                        url: 'dashboard/assets/app/update_settings.php?update=password',
                        type: "POST",
                        data: $('#update_password').serialize(),
                        success: function() {
                            toastr.success("Password has been updated, you can use it next time you login.")
                        },
                        error: function() {
                            toastr.error("An unexpected error has occured. Please try again later.")
                        }
                    });
                }
            });

        }

    };

}();