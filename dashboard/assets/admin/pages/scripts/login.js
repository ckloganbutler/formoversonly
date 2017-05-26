var Login = function() {

    var handleLogin = function() {


    }

    var handleForgetPassword = function() {
        $('.forget-form').validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",
            rules: {
                email: {
                    required: true,
                    email: true
                }
            },

            messages: {
                email: {
                    required: "Email is required."
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

            errorPlacement: function(error, element) {
                error.insertAfter(element.closest('.input-icon'));
            },

            submitHandler: function(form) {
                form.submit();
            }
        });

        $('.forget-form input').keypress(function(e) {
            if (e.which == 13) {
                if ($('.forget-form').validate().form()) {
                    $('.forget-form').submit();
                }
                return false;
            }
        });

        jQuery('#forget-password').click(function() {
            jQuery('.login-form').hide();
            jQuery('.forget-form').show();
        });

        jQuery('#back-btn').click(function() {
            jQuery('.login-form').show();
            jQuery('.forget-form').hide();
        });

    }

    var handleRegister = function() {

        function format(state) {
            if (!state.id) return state.text; // optgroup
            return "<img class='flag' src='../../assets/global/img/flags/" + state.id.toLowerCase() + ".png'/>&nbsp;&nbsp;" + state.text;
        }

        if (jQuery().select2) {
	        $("#select2_sample4").select2({
	            placeholder: '<i class="fa fa-map-marker"></i>&nbsp;Select a Country',
	            allowClear: true,
	            formatResult: format,
	            formatSelection: format,
	            escapeMarkup: function(m) {
	                return m;
	            }
	        });


	        $('#select2_sample4').change(function() {
	            $('.register-form').validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
	        });
    	}

        $('.register-form').validate({
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
                    maxlength: 10,
                    minlength: 10,
                    remote: '../../dashboard/assets/app/search_phone.php'
                },
                email: {
                    required: true,
                    email: true,
                    remote: '../../dashboard/assets/app/search_email.php'
                },
                company: {
                    required: true
                },
                address: {
                    required: true
                },
                city: {
                    required: true
                },
                state: {
                    required: true
                },
                zip: {
                    required: true
                },
                password: {
                    required: true
                },
                rpassword: {
                    equalTo: "#register_password"
                },
                tnc: {
                    required: true
                }
            },

            messages: {
                tnc: {
                    required: "Please accept TNC first."
                },
                phone: {
                    remote: "Phone number already taken"
                },
                email: {
                    remote: "Email address already taken"
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

            errorPlacement: function(error, element) {
                if (element.attr("name") == "tnc") { // insert checkbox errors after the container                  
                    error.insertAfter($('#register_tnc_error'));
                } else if (element.closest('.input-icon').size() === 1) {
                    error.insertAfter(element.closest('.input-icon'));
                } else {
                    error.insertAfter(element);
                }
            },

            submitHandler: function(form) {
                $.ajax({
                    url: 'dashboard/assets/app/register.php',
                    type: "POST",
                    data: $('.register-form').serialize(),
                    success: function() {
                        jQuery('.register-form').hide();
                        jQuery('.login-form').show();
                        jQuery('#alert-msg').html('<div class="alert alert-success display-hide" style="display: block;"> <button class="close" data-close="alert"></button> <span>Thanks for registering! You\'ll need to validate your account with the email we sent you before you can login. </span> </div>');
                    },
                    error: function() {
                        jQuery('.register-form').hide();
                        jQuery('.login-form').show();
                        jQuery('#alert-msg').html('<div class="alert alert-danger display-hide" style="display: block;"> <button class="close" data-close="alert"></button> <span> We couldn\'t proccess your request at this time, please try again later. </span> </div>');
                    }
                });
            }
        });

        jQuery('#register-btn').click(function() {
            jQuery('.login-form').hide();
            jQuery('.register-form').show();
        });

        jQuery('#register-back-btn').click(function() {
            jQuery('.login-form').show();
            jQuery('.register-form').hide();
        });
    }

    return {
        init: function() {

            handleLogin();
            handleForgetPassword();
            handleRegister();

        }

    };

}();