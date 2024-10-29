<?php
include_once("header.php");
$current_user = wp_get_current_user();
$blogName = get_bloginfo('name');
$email = $current_user->user_email;
$domain = get_site_url();
?>
<div class="floating-header-section" >
    <div class="section-content">
        <div class="o-page__card">

            <img class="icon-taskeo" height="45"
                 src="<?php echo(plugins_url("assets/img/logo.png", __FILE__)); ?>">
            <h1>Appointment Scheduling</h1>
            <p class="asbt_login">To configure your Appointment Scheduling Form you need to create a Free Taskeo account.</p>
            <div class="asbt_register">

                <div style="margin: 10px 0">
                    <input class="input-field" type="text" placeholder="Your name" name="name" id="name" required="required"
                           value="<?php echo($current_user->first_name);?>"/>

                    <input class="input-field" placeholder="Your email" type="text" name="email" id="email" required="required"
                           value="<?php echo($current_user->user_email);?>"/>

                    <input class="input-field" placeholder="Password" type="password" name="password" id="password" required="required"
                           value=""/>
                    <input value="<?php echo($domain); ?>" type="hidden" id="domain"/>       

                    <label style="color: red; display: none" id="error_message"></label>
                </div>




                <div class="cta-container">
                    <div id="register-loader" class="lds-ellipsis" style="display: none"><div></div><div></div><div></div><div></div></div>
                    <input type="submit" name="submit" id="register-button" class="button button-start" onclick="register_account();"
                           value="Register"/>
                </div>


                <div style="margin-top: 10px;font-size: 10px;color: gray;" class="row text-center margin-top-5 text-sm-center">By continuing, you agree to
                    the
                    <a href="https://taskeo.co/terms-of-service" target="_blank">Terms of Service
                    </a>
                    and
                    <a href="https://taskeo.co/privacy-policy" target="_blank">Privacy Policy
                    </a>
                </div>

            </div>
            <div class="asbt_login" style="display: none">

                <div style="margin: 10px 0">

                    <input class="input-field" placeholder="Email..." type="text" name="loginemail" id="loginemail" required="required"
                    />

                    <input value="<?php echo($domain); ?>" type="hidden" id="domain"/>
                    <input class="input-field" placeholder="Password..." type="password" name="loginpassword" id="loginpassword" required="required"
                           value=""/>
                    <label style="color: red; display: none" id="error_message_login"></label>
                </div>

                <div class="cta-container">
                    <div id="login-loader"  class="lds-ellipsis" style="display: none"><div></div><div></div><div></div><div></div></div>
                    <input type="submit" name="submit" id="login-button" class="button button-start" onclick="login();"
                           value="Login"/>
                </div>


            </div>
            <div class="taskeo_app_select" style="display: none;">

                <div id="app_container">

                </div>

            </div>
        </div>
        <div class="asbt_register" style="margin: 20px"> Already have an account, <a onclick="show_login();" style="cursor: pointer">Login</a> instead</div>
        <div class="asbt_login" style="margin: 20px; display: none;"> Need an account? <a onclick="show_register();" style="cursor: pointer">Register</a></div>
    </div>
</div>