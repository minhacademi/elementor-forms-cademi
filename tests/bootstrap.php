<?php

/**
 * Test bootstrap: define ABSPATH, stub WordPress functions,
 * and stub Elementor classes before loading the plugin files.
 */

define('ABSPATH', '/tmp/wordpress/');

require_once __DIR__ . '/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Stub WordPress functions
|--------------------------------------------------------------------------
|
| These are simple stubs for WordPress functions used by the plugin.
| They replicate the essential behavior for testing purposes.
|
*/

if (! function_exists('sanitize_text_field')) {
    function sanitize_text_field($str) {
        return trim(strip_tags((string) $str));
    }
}

if (! function_exists('sanitize_email')) {
    function sanitize_email($email) {
        $email = trim((string) $email);
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '';
    }
}

if (! function_exists('is_email')) {
    function is_email($email) {
        return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}

if (! function_exists('wp_parse_url')) {
    function wp_parse_url($url, $component = -1) {
        return $component === -1 ? parse_url($url) : parse_url($url, $component);
    }
}

if (! function_exists('esc_url_raw')) {
    function esc_url_raw($url) {
        return filter_var($url, FILTER_SANITIZE_URL);
    }
}

if (! function_exists('esc_html__')) {
    function esc_html__($text, $domain = 'default') {
        return $text;
    }
}

if (! function_exists('__')) {
    function __($text, $domain = 'default') {
        return $text;
    }
}

if (! function_exists('wp_kses_post')) {
    function wp_kses_post($data) {
        return $data;
    }
}

/*
|--------------------------------------------------------------------------
| Stub Elementor classes
|--------------------------------------------------------------------------
*/

if (! class_exists('Elementor\Controls_Manager')) {
    class Elementor_Controls_Manager_Stub
    {
        const TEXT = 'text';
        const SWITCHER = 'switcher';
    }
    class_alias('Elementor_Controls_Manager_Stub', 'Elementor\Controls_Manager');
}

if (! class_exists('ElementorPro\Modules\Forms\Classes\Action_Base')) {
    abstract class Elementor_Action_Base_Stub
    {
        abstract public function get_name();
        abstract public function get_label();
        abstract public function register_settings_section($widget);
        abstract public function run($record, $ajax_handler);
        public function on_export($element) { return $element; }
    }
    class_alias('Elementor_Action_Base_Stub', 'ElementorPro\Modules\Forms\Classes\Action_Base');
}

// Load the class under test
require_once __DIR__ . '/../form-actions/redirect.php';
