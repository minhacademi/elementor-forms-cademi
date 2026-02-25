<?php
/**
 * Cademi for Elementor Forms
 *
 * @package Cademi_For_Elementor_Forms
 *
 * Plugin Name:       Cademi for Elementor Forms
 * Plugin URI:        https://github.com/minhacademi/elementor-forms-cademi
 * Description:       Add Cademi after-submit actions to Elementor Pro Forms.
 * Version:           1.0.0
 * Author:            Cademi
 * Author URI:        https://cademi.com.br
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       cademi-for-elementor-forms
 * Domain Path:       /languages
 * Requires at least: 6.2
 * Tested up to:      6.9
 * Requires PHP:      7.4
 * Stable tag:        1.0.0
 * Requires Plugins:  elementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ELEMENTOR_FORMS_CADEMI_VERSION', '1.0.0' );
define( 'ELEMENTOR_FORMS_CADEMI_FILE', __FILE__ );
define( 'ELEMENTOR_FORMS_CADEMI_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Show admin notice when Elementor Pro is not active.
 */
function elementor_forms_cademi_admin_notice_missing_pro() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	$message = sprintf(
		/* translators: 1: Plugin name, 2: Elementor Pro */
		esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'cademi-for-elementor-forms' ),
		'<strong>Cademi for Elementor Forms</strong>',
		'<strong>Elementor Pro</strong>'
	);

	echo wp_kses_post( '<div class="notice notice-warning is-dismissible"><p>' . $message . '</p></div>' );
}

/**
 * Register the Cademi redirect form action with Elementor Pro.
 *
 * @param \ElementorPro\Modules\Forms\Registrars\Form_Actions_Registrar $form_actions_registrar
 */
function elementor_forms_cademi_register_action( $form_actions_registrar ) {
	include_once ELEMENTOR_FORMS_CADEMI_DIR . 'form-actions/redirect.php';
	$form_actions_registrar->register( new \Cademi_Action_After_Submit() );
}

/**
 * Initialize the plugin after all plugins have loaded.
 */
function elementor_forms_cademi_init() {
	// Check for Elementor (free) — handled by Requires Plugins header in WP 6.5+,
	// but we also check at runtime for older WP versions.
	if ( ! did_action( 'elementor/loaded' ) ) {
		return;
	}

	// Check for Elementor Pro — this cannot be declared in the header.
	if ( ! did_action( 'elementor_pro/loaded' ) ) {
		add_action( 'admin_notices', 'elementor_forms_cademi_admin_notice_missing_pro' );
		return;
	}

	add_action( 'elementor_pro/forms/actions/register', 'elementor_forms_cademi_register_action' );
}
add_action( 'plugins_loaded', 'elementor_forms_cademi_init' );
