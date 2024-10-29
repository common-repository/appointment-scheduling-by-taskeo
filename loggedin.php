<?php
include_once("header.php");

$options = get_option('taskeo_settings');
if (isset($options['taskeo_text_field_access_token'])) {
    $login =  "https://app.taskeo.co/sso/".$options['taskeo_text_field_access_token'];
}

if(!the_slug_exists('my-appointments-by-taskeo')) {
    if (isset($options['taskeo_text_field_form_id'])) {
        $formId = $options['taskeo_text_field_form_id'];
        add_asbt_custom_page($formId);
    }
    
}



?>
<script>
    jQuery(document).ready(function () {
        send_event('Wordpress Logged In');
        get_appointment_forms();
    });
</script>
<div class="floating-header-section" id="taskeo_configured">

    <div class="section-content" style="width: 100%; max-width:100%;">
        <div class="o-page__card">

            <img class="icon-taskeo" height="45"
                 src="<?php echo(plugins_url("assets/img/logo.png", __FILE__)); ?>">
            <h1>Appointment Scheduling</h1>
            <p class="margin-bottom">Your Appointment Scheduling is up and running. 🎉 To add appointment booking form to your page/post use the shortcodes below. To check your incoming appointments visit your Appointments List. </p>

            <div style="margin-bottom: 2rem;">
                <a class="button button-start" style="width: auto;max-width: auto;"
                   href="<?php echo $login; ?>"
                   target="_blank"> Go to Appointments</a>

                <?php if(the_slug_exists('my-appointments-by-taskeo')) { ?>
                    <a class="button button-start" style="width: auto;max-width: auto;"
                        href="<?php echo get_site_url(); ?>/my-appointments-by-taskeo"
                        target="_blank"> Example Form</a>

                <?php } ?>

                <a class="button button-start" style="width: auto;max-width: auto;"
                   href="https://taskeo.co/wordpress"
                   target="_blank">Tutorial</a>

                <a class="button button-start" style="width: auto;max-width: auto;"
                   href="https://taskeo.co/support"
                   target="_blank">Support</a>

                <button class="button button-hero"
                   onclick="reset()"
                   target="_blank">Reconfigure</button>

            </div>



            <div id="appointmentFormsList" >
                <div id="data-loader"  class="lds-ellipsis" style="margin: 0 auto;"><div></div><div></div><div></div><div></div></div>

                <table class="c-table" id="appointmentFormsTable">
                    <thead>
                        <tr class="c-table__row">
                            <th class="c-table__cell">Name</th>
                            <th class="c-table__cell">Shortcode</th>
                            <th class="c-table__cell">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        

                    </tbody>

                </table>
            </div>

        </div>
        <p>Loving Appointment Scheduling by Taskeo ❤️?<br />Rate us <b>★★★★★</b></p>

       

    </div>

    

</div>

<?php

function the_slug_exists($post_name) {
    global $wpdb;
    if($wpdb->get_row("SELECT post_name FROM wp_posts WHERE post_name = '" . $post_name . "'", 'ARRAY_A')) {
        return true;
    } else {
        return false;
    }
}

function add_asbt_custom_page($id) {
    // Create post object
    $my_post = array(
      'post_title'    => wp_strip_all_tags( 'My Appointments' ),
      'post_content'  => '[taskeo_appointment_form id="'.$id.'"]',
      'post_name'	  => 'my-appointments-by-taskeo',
      'post_status'   => 'publish',
      'post_author'   => 1,
      'post_type'     => 'page',
    );

    // Insert the post into the database
    wp_insert_post( $my_post );
}


?>