=== Cademi for Elementor Forms ===
Contributors: cademi
Tags: elementor, forms, cademi, redirect, lms
Requires at least: 6.2
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add Cademi after-submit actions to Elementor Pro Forms.

== Description ==

Cademi for Elementor Forms adds a **Redirect - Cademi** action to the Elementor Pro form widget's after-submit actions. When a visitor submits a form, they are automatically registered on your Cademi platform and redirected to it.

**Features:**

* Automatic user registration on Cademi via form submission
* Configurable platform URL and token
* Optional delivery ID assignment
* Optional internal redirect path after login
* Customizable form field mappings for name, email, and phone

**Requirements:**

* WordPress 6.2 or higher
* PHP 7.4 or higher
* [Elementor](https://wordpress.org/plugins/elementor/) installed and active
* [Elementor Pro](https://elementor.com/pro/) installed and active

== Installation ==

1. Upload the `elementor-forms-cademi` folder to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Ensure Elementor and Elementor Pro are installed and active.
4. Edit a page with Elementor, add a Form widget, and select **Redirect - Cademi** from the after-submit actions.
5. Configure your Platform URL and Platform Token in the action settings.

== Frequently Asked Questions ==

= Does this plugin require Elementor Pro? =

Yes. This plugin extends Elementor Pro's form widget and requires both Elementor (free) and Elementor Pro to be installed and active. If Elementor Pro is missing, the plugin will display an admin notice.

= Where do I find my Platform URL and Token? =

Your Platform URL can be found at **Settings > Default Domain** in your Cademi dashboard. Your Platform Token can be found at **Settings > Platform Token**.

= What is the Internal Redirect field? =

The Internal Redirect field allows you to specify a path within your Cademi platform where the user will be redirected after login. For example, entering `dashboard` or `/dashboard` will redirect the user to the dashboard page after authentication.

= Should this action be the last in my actions list? =

Yes. Since this action redirects the user, any actions listed after it will not execute. Always place **Redirect - Cademi** as the last action in the list.

== Changelog ==

= 1.0.0 =
* Initial release.
* Cademi redirect after-submit action for Elementor Pro forms.
* Automatic user registration via platform URL and token.
* Optional delivery ID and internal redirect support.

== Upgrade Notice ==

= 1.0.0 =
Initial release.
