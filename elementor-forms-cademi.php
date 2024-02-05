<?php
/*
Plugin Name: Elementor Pro Forms Cademí
Description: Add Cademí after submit actions to Elementor Forms
Version: 1.0
Author: Cademí
Author URI: https://cademi.com.br
Domain Path: /languages/
*/

// Garanta que o WordPress não permita chamar o arquivo diretamente.
if (!defined('ABSPATH')) exit;

// Carregando linguagens
load_plugin_textdomain( 'elementor-forms-cademi', false, dirname(plugin_basename( __FILE__ )) . '/languages/' );

// Adicionando Ação de Redirecionamento
function add_cademi_redirect_action( $form_actions_registrar )
{
	include_once( __DIR__ . '/form-actions/redirect.php');
	$form_actions_registrar->register(new \Cademi_Action_After_Submit());
}
add_action( 'elementor_pro/forms/actions/register', 'add_cademi_redirect_action');