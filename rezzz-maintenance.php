<?php
/*
Plugin Name: rezzz Maintenance
Plugin URI: http://www.rezzz.com
Description: Helper Plugin for the WordPress Maintenance provided by rezzz
Version: 1.0
Author: Jason Resnick
Author URI: http://www.rezzz.com
License: GPL2
GitHub Plugin URI: https://github.com/rezzz-dev/rezzz-maintenance
*/

class Rezzz_Maintenance_Helper {
	private static $instance;

	static function get_instance() {
		if ( ! self::$instance )
			self::$instance = new Rezzz_Maintenance_Helper;

		return self::$instance;
	}

	private function __construct() {
		$user_ID = get_current_user_id();
		if ($user_ID == 1) {
			add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		}
	}

	public function add_options_page() {
		add_management_page( 'Maintenance Helper', 'Maintenance Helper', 'manage_options', 'maintenance-helper.php', array( $this, 'generate_mailchimp_markup' ) );
	}

	public function generate_mailchimp_markup() { ?>

		<div class="wrap">
			<h1>Maintenance Helper</h1>
			<?php
				$content = $this->get_maintenance_markup();
				$editor_id = 'maintenancehelper';
				$settings = array(
					'teeny'         => true,
					'media_buttons' => false,
				);
				wp_editor( $content, $editor_id, $settings );
			?>
	    </div>
	<?php
	}

	public function get_maintenance_markup() {

		$markup = '<h3>Plugin &amp; WordPress Core Updates</h3>';

		global $wp_version;

		$updates = get_core_updates();

		if ( ! empty ( $updates ) ) {
			$markup .= 'We have upgraded WordPress Core:';
			$markup .= "<ul>";

			foreach( (array) $updates as $update ) {

				$markup .= "<li>From WordPress Version: " . esc_attr( $wp_version ) . " to WordPress ". $update->current . " on " . date( 'd F Y' ) . "</li>";
			}
			$markup .= "</ul>";
		}

		$plugins = get_plugin_updates();

		if ( ! empty ( $plugins ) ) {
			$markup .= "We have upgraded the following plugins on your site:";
			$markup .= "<ul>";
			foreach ( (array) $plugins as $plugin_file => $plugin_data ) {

				$details_url = self_admin_url('plugin-install.php?tab=plugin-information&plugin=' . $plugin_data->update->slug . '&section=changelog&TB_iframe=true&width=640&height=662');

				$markup .= "<li>";
				$markup .= $plugin_data->Name . ': ';
				$markup .= sprintf( __( 'Version %1$s to <a href="%2$s">%3$s</a>.' ), $plugin_data->Version, esc_url($details_url), $plugin_data->update->new_version );
				$markup .= "</li>";

			}
			$markup .= "</ul>";
		}

		$themes = get_theme_updates();

		if ( ! empty ( $themes ) ) {
			$markup .= "We have upgraded the following themes on your site:";
			$markup .= "<ul>";
			foreach ( (array) $themes as $theme_key => $theme ) {

				$markup .= "<li>";
				$markup .= $theme['Name'] . ': Version ' . $theme['Version'] . ' to '. $theme->update["new_version"] . '.';
				$markup .= "</li>";

			}
			$markup .= "</ul>";
		}

		$markup .= "<h3>Support For The Month of *|DATE:F Y|*</h3>";
		$markup .= "During the month of *|DATE:F Y|* we&#39;ve helped you with the following support requests:";
		$markup .= "<ul>";
		$markup .= "<li>Email support</li>";
		$markup .= "</ul>";
		$markup .= "<h3 class='null'><span class='mc-toc-title'>Want to learn more?</span></h3>";
		$markup .= "If you have any questions about your plan or the work our team have performed on your site please feel free to contact us.<br />";

		return $markup;
	}
}
add_action( 'plugins_loaded', array( 'Rezzz_Maintenance_Helper' , 'get_instance' ) );