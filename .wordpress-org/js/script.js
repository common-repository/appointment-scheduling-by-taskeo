function send_event(event_name, event_value) {

    try {
        ga('send', 'WordPress plugin', event_name, '', event_value);

    } catch (err) {
        console.log("We have got an error", err);
    }
    try {
        fbq('trackCustom', event_name, {
            email: jQuery('#email').val()
        });
    } catch (err) {
        console.log("We have got an error", err);
    }
}

function reset() {
    send_event('Reset Config', 0);
    jQuery('#accessToken').val("");
    jQuery('#sessionToken').val("");
    jQuery('#formId').val("");
    save_asbt_options();
}

function show_login() {
    send_event('Show Login', 0);
    jQuery('.asbt_login').slideDown();
    jQuery('.asbt_register').slideUp();
}

function show_register() {
    send_event('Show Register', 0);
    jQuery('.asbt_register').slideDown();
    jQuery('.asbt_login').slideUp();
}

function login() {

    if (jQuery('#loginpassword').val().length < 6) {
        show_error("login", "Please insert your Password");
        return;
    }

    jQuery('#login-button').hide();
    jQuery('#login-loader').show();

    let d = new Date();
    const defaultTimezone = (d.getTimezoneOffset() / 60) * -1;

    var post_data = JSON.stringify({
        api_key: "DDe80f841ea7bc9e78946e0b9d6d9b7e200ed5468a",
        email: jQuery('#loginemail').val(),
        password: jQuery('#loginpassword').val(),
        defaultTimezone: defaultTimezone
    });

    jQuery.post({
        url: 'https://app.taskeo.co/api/auth/loginWordpress',
        type: 'POST',
        processData: false,
        contentType: 'application/json',
        data: post_data,
        success: function (data) {

            send_event('Login', 0);
            console.log(data);

            if (data.token === undefined) {
                show_error("login", "Login error");
                jQuery('#login-button').show();
                jQuery('#login-loader').hide();
                return;
            }

            var token = data.token;
            var user = data.user;

            var formId = jQuery('#formId').val();

            var forms_data = JSON.stringify({
                "api_key": "DDe80f841ea7bc9e78946e0b9d6d9b7e200ed5468a",
                "token": data.token,
                "data": [
                  {
                    "added": new Date(),
                    "action": "GET",
                    "key": null,
                    "mod": "modules",
                    "func": "getModules",
                    "data": {
                      "type": "appointments"
                    }
                  }
                ]
            });

            //if(!formId || formId == '') {
                jQuery.post({
                    url: 'https://app.taskeo.co/api/sync/doSync',
                    type: 'POST',
                    processData: false,
                    contentType: 'application/json',
                    data: forms_data,
                    success: function (formData) {
            
                        send_event('Get Appointment Forms List', 0);
            
                        if (formData === undefined) {
                            show_error("login", "Error fetching data");
                            jQuery('#login-button').show();
                            jQuery('#login-loader').hide();
                            return;
                        }
                        console.log("LIST", formData.sync[0].data);

                        jQuery('#data-loader').hide();

                        if(formData && formData.sync && formData.sync[0].data && formData.sync[0].data[0] && formData.sync[0].data[0]._id) {

                            jQuery('#sessionToken').val(token);
                            jQuery('#accessToken').val(user.token);
                            jQuery('#formId').val(formData.sync[0].data[0]._id);

                            jQuery('.asbt_app_select').slideDown();
                            jQuery('.asbt_login').slideUp();

                            jQuery('#asbtSettings').submit();

                        } else {

                            jQuery('#sessionToken').val(token);
                            jQuery('#accessToken').val(user.token);

                            jQuery('.asbt_app_select').slideDown();
                            jQuery('.asbt_login').slideUp();

                            jQuery('#asbtSettings').submit();

                        }
            

                    }
                });

            /*} else {

                jQuery('#sessionToken').val(token);
                jQuery('#accessToken').val(user.token);

                jQuery('.asbt_app_select').slideDown();
                jQuery('.asbt_login').slideUp();

                jQuery('#asbtSettings').submit();

            }*/

            
        }
    });

}

function get_appointment_forms() {

    var login_data = JSON.stringify({
        "api_key": "DDe80f841ea7bc9e78946e0b9d6d9b7e200ed5468a",
        "token": jQuery('#accessToken').val()
    });

    jQuery.post({
        url: 'https://app.taskeo.co/api/auth/loginSso',
        type: 'POST',
        processData: false,
        contentType: 'application/json',
        data: login_data,
        success: function (data) {

            if (data.token === undefined) {
                show_error("login", "Error login");
                jQuery('#login-button').show();
                jQuery('#login-loader').hide();
                return;
            }

            var post_data = JSON.stringify({
                "api_key": "DDe80f841ea7bc9e78946e0b9d6d9b7e200ed5468a",
                "token": data.token,
                "data": [
                  {
                    "added": new Date(),
                    "action": "GET",
                    "key": null,
                    "mod": "modules",
                    "func": "getModules",
                    "data": {
                      "type": "appointments"
                    }
                  }
                ]
            });


            jQuery.post({
                url: 'https://app.taskeo.co/api/sync/doSync',
                type: 'POST',
                processData: false,
                contentType: 'application/json',
                data: post_data,
                success: function (data) {
        
                    send_event('Get Appointment Forms List', 0);
        
                    if (data === undefined) {
                        show_error("login", "Error fetching data");
                        jQuery('#login-button').show();
                        jQuery('#login-loader').hide();
                        return;
                    }

                    jQuery('#data-loader').hide();

                    if(data && data.sync && data.sync[0].data) {
                        let arr = data.sync[0].data;

                        arr.map((item) => {
                            show_available_appointment_forms(item);
                        })
                    }
                   

        

                }
            });
            

        }
    });

}



function show_available_appointment_forms(item) {

    jQuery('#appointmentFormsTable tbody').append('<tr class="c-table__row"><td class="c-table__cell">\n' +
        '' + item.name + '</td> <td class="c-table__cell">[taskeo_appointment_form id="' + item._id + '"]</td><td class="c-table__cell"> <a target="_new" href="https://app.taskeo.co/module/' + item._id + '/settings">Edit</a> | <a target="_new" href="https://app.taskeo.co/module/' + item._id + '/list">List</a> | <a target="_new" href="https://taskeo.co/a/' + item._id + '">Preview</a>  \n' +
        '</td></tr>');

}

function show_error(position, message) {

    if (position == 'login') {
        jQuery('#error_message_login').html(message);
        jQuery('#error_message_login').slideDown();

        setTimeout(function () {
            jQuery('#error_message_login').html("").slideUp();
        }, 10000);
    } else {
        jQuery('#error_message').html(message);
        jQuery('#error_message').slideDown();

        setTimeout(function () {
            jQuery('#error_message').html("").slideUp();
        }, 10000);
    }

}

function register_account() {


    if (jQuery('#password').val().length < 6) {
        show_error("register", "Password should be at least 8 characters long");
        return;
    }

    jQuery('#register-button').hide();
    jQuery('#register-loader').show();

    let d = new Date();
    const defaultTimezone = (d.getTimezoneOffset() / 60) * -1;

    var register_data = JSON.stringify({
        email: jQuery('#email').val(),
        affiliate: "",
        api_key: "DDe80f841ea7bc9e78946e0b9d6d9b7e200ed5468a",
        coupon: "",
        defaultTimezone: defaultTimezone,
        emailConfirmed: false,
        name: jQuery('#name').val(),
        password: jQuery('#password').val(),
        type: "appointment-scheduling",
    });


    jQuery.post({
        url: 'https://app.taskeo.co/api/auth/signup',
        type: 'POST',
        processData: false,
        contentType: 'application/json',
        data: register_data,
        success: function (data) {

            if (data.token === undefined) {
                show_error("register", "Signup error");
                jQuery('#register-button').show();
                jQuery('#register-loader').hide();
                return;
            }
            
            var post_data = JSON.stringify({
                api_key: "DDe80f841ea7bc9e78946e0b9d6d9b7e200ed5468a",
                email: jQuery('#email').val(),
                password: jQuery('#password').val(),
                defaultTimezone: defaultTimezone
            });
        
            jQuery.post({
                url: 'https://app.taskeo.co/api/auth/loginWordpress',
                type: 'POST',
                processData: false,
                contentType: 'application/json',
                data: post_data,
                success: function (data) {
        
                    send_event('Login', 0);
        
                    if (data.token === undefined) {
                        show_error("login", "Login error");
                        jQuery('#login-button').show();
                        jQuery('#login-loader').hide();
                        return;
                    }
                    console.log(data);
        
                    var token = data.token;
                    var user = data.user;
                    jQuery('#sessionToken').val(token);
                    jQuery('#accessToken').val(user.token);
        
                    jQuery('.asbt_app_select').slideDown();
                    jQuery('.asbt_register').slideUp();
        
                    jQuery('#asbtSettings').submit();
                }
            });

            
        }
    });

}

function save_asbt_options() {
    jQuery('#asbtSettings').submit();
}
