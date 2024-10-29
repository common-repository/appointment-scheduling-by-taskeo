<?php
/**
 * Plugin Name:       Appointment Scheduling by Taskeo 
 * Description:       Appointment Scheduling by Taskeo Wordpress Integration
 * Version:           1.0.5
 * Author:            Taskeo.co
 * Author URI:        https://taskeo.co
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: 	  t-header-area
 * Domain Path: 	  /
 */


if ( ! class_exists( 'taskeo_header_area' ) ) {
    class taskeo_header_area {
		var $plugin_name = "";
		var $message = "";
		var $errorMessage = "";
		
        public function __construct() {
			$this->plugin_name = "t-header-area";
			
			add_action('admin_menu', array( $this, 'register_wp_setting_menu_page') );
			add_action('admin_init', array( $this, 'taskeo_settings_init') );
			add_action('activated_plugin', 'asbt_activation');

			$plugin = plugin_basename(__FILE__);
			add_filter("plugin_action_links_$plugin", array( $this, 'plugin_add_settings_link'));

			global $pagenow;

			if (!$this->taskeo_is_configured() && 
				(isset($_GET['page']) && $_GET['page'] != "Appointment_Scheduling_by_Taskeo")
				|| (!isset($_GET['page']) && !$this->taskeo_is_configured())
			) {

				add_action('admin_notices', array( $this, 'taskeo_admin_notice__error'));

			}
		}

		/*
		* Add a link in the plugin list
		*/
		function plugin_add_settings_link($links) {
			$settings_link = '<a href="admin.php?page=Appointment_Scheduling_by_Taskeo&utm_source=wordpress&utm_campaign=pluginlistsetup"> How to start?</a>';
			array_unshift($links, $settings_link);
			return $links;
		}

		/*
		* Redirect people to ASbT after activation
		*/
		function asbt_activation($plugin) {
			if ($plugin == plugin_basename(__FILE__)) {
				exit(wp_redirect(admin_url('admin.php?page=Appointment_Scheduling_by_Taskeo&utm_source=wordpress&utm_campaign=afterinstallredirect')));
			}
		}
		
		public function register_wp_setting_menu_page() {
			add_menu_page( 
				'Taskeo', 
				$this->taskeo_is_configured() ? 'Appointment Scheduling' : 'Appointment Scheduling <span class="awaiting-mod">1</span>',
				'manage_options', 
				'Appointment_Scheduling_by_Taskeo', 
				array( $this, 'appointment_scheduling_by_taskeo' ), 
				plugins_url('assets/img/logo-square2.svg', __FILE__), 
				3 
			);
		}

		public function taskeo_settings_init() {


			register_setting('asbtPlugin', 'taskeo_settings');

			if (is_admin()) {
				// for Admin Dashboard Only
				// Embed the Script on our Plugin's Option Page Only
				if (isset($_GET['page']) && $_GET['page'] == 'Appointment_Scheduling_by_Taskeo') {
					wp_enqueue_script('jquery');
					wp_enqueue_script('jquery-form');
				}
			}

			add_settings_field(
				'taskeo_text_field_session_token',
				__('Application Session Token', 'taskeo.co'),
				'taskeo_text_field_session_token_render',
				'asbtPlugin',
				'taskeo_asbtPlugin_section'
			);

			add_settings_field(
				'taskeo_text_field_access_token',
				__('Application Access Token', 'taskeo.co'),
				'taskeo_text_field_access_token_render',
				'asbtPlugin',
				'taskeo_asbtPlugin_section'
			);

			add_settings_field(
				'taskeo_text_field_form_id',
				__('Form Id', 'taskeo.co'),
				'taskeo_text_field_form_id_render',
				'asbtPlugin',
				'taskeo_asbtPlugin_section'
			);


		}

		function taskeo_text_field_session_token_render() {
			$options = get_option('taskeo_settings');
			$token = "";
			if (isset($options['taskeo_text_field_session_token'])) {
				$token = $options['taskeo_text_field_session_token'];
			}
			?>
			<input id="sessionToken" type='hidden'
				name='taskeo_settings[taskeo_text_field_session_token]'
				value='<?php echo $token; ?>'>

			<?php
		}

		function taskeo_text_field_access_token_render() {
			$options = get_option('taskeo_settings');
			$token = "";
			if (isset($options['taskeo_text_field_access_token'])) {
				$token = $options['taskeo_text_field_access_token'];
			}
			?>
			<input id="accessToken" type='hidden'
				name='taskeo_settings[taskeo_text_field_access_token]'
				value='<?php echo $token; ?>'>

			<?php
		}

		function taskeo_text_field_form_id_render() {
			$options = get_option('taskeo_settings');
			$token = "";
			if (isset($options['taskeo_text_field_form_id'])) {
				$token = $options['taskeo_text_field_form_id'];
			}
			?>
			<input id="formId" type='hidden'
				name='taskeo_settings[taskeo_text_field_form_id]'
				value='<?php echo $token; ?>'>

			<?php
		}


		public function appointment_scheduling_by_taskeo() {
			include_once("header.php");
			$options = get_option('taskeo_settings');
			//echo "-------<br />";
			//echo print_r($options, true);
			//echo "-------<br />";
			?>
		
		
			<form id="asbtSettings" action='options.php' method='post' style="display: none">
		
		
				<?php
		
				settings_fields('asbtPlugin');
				do_settings_sections('asbtPlugin');
		
				$this->taskeo_text_field_session_token_render();
				$this->taskeo_text_field_access_token_render();
				$this->taskeo_text_field_form_id_render();

				?>
		
			</form>
		
			<?php
		
			if ($this->taskeo_is_configured()) {
				include_once("loggedin.php");
			} else {
				include_once("loggedout.php");
			}
			?>
			<?php
			
			
			

			

		}

		public function taskeo_is_configured() {

			$options = get_option('taskeo_settings');

			if (!isset($options['taskeo_text_field_session_token']) || strlen($options['taskeo_text_field_session_token']) < 1) {
				if (isset($_GET['token'])) {
					return true;
				}
				return false;
			}
			return true;

		}

		function taskeo_admin_notice__error() {
    		include_once("notice.php");
		}
		
		
	}

	// Add Shortcode
	function taskeo_appointment_form_func($atts) {
		$tam_id = get_option( 'tam_id' );


		if(isset($atts) && $atts['id'] != '') {
			$tam_id = $atts['id'];
		}
		
		return '<div style="position: relative;padding: 0px;min-height: 670px;">
		<iframe style="position: absolute;top: 0;left: 0;width: 100%;height: 100%;border: 0;" src="https://taskeo.co/a/' . $tam_id . '" />
	  </div>';
	}

	add_shortcode( 'taskeo_appointment_form', 'taskeo_appointment_form_func' );

	$taskeo_header_area = new taskeo_header_area();
}
