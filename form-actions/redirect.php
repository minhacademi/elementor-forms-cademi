<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use ElementorPro\Modules\Forms\Classes\Action_Base;

class Cademi_Action_After_Submit extends Action_Base
{
	public function get_name()
	{
		return 'cademi_redirect';
	}

	public function get_label()
	{
		return 'Redirect - Cademi';
	}

	public function register_settings_section( $widget )
	{
		$widget->start_controls_section(
			'cademi',
			[
				'label' => 'Redirect - Cademi',
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);

		$widget->add_control('cademi_url', [
			'label'         => esc_html__('Platform URL', 'cademi-for-elementor-forms'),
			'description'   => esc_html__('You can find your Platform URL on: Settings > Default Domain', 'cademi-for-elementor-forms'),
			'label_block'   => true,
			'type'          => Controls_Manager::TEXT,
			'placeholder'   => 'https://<your_subdomain>.cademi.com.br',
			'ai' => [
				'active' => false,
			]
		]);

		$widget->add_control('cademi_token', [
			'label'         => esc_html__('Platform Token', 'cademi-for-elementor-forms'),
			'description'   => esc_html__('You can find your Platform Token on: Settings > Platform Token', 'cademi-for-elementor-forms'),
			'label_block'   => true,
			'type'          => Controls_Manager::TEXT,
			'ai' => [
				'active' => false,
			]
		]);

		$widget->add_control('cademi_show_advanced', [
			'label' => esc_html__( 'Show advanced settings', 'cademi-for-elementor-forms'),
			'type' => Controls_Manager::SWITCHER,
			'default' => 'yes',
		]);

		$widget->add_control('cademi_entrega_id', [
			'label'         => esc_html__('Delivery ID (optional)', 'cademi-for-elementor-forms'),
			'description'   => esc_html__('When setting an ID, you will add the Delivery for the user, in addition to the configured free delivery', 'cademi-for-elementor-forms'),
			'label_block'   => true,
			'type'          => Controls_Manager::TEXT,
			'ai' => [
				'active' => false,
			],
			'condition' => [
				'cademi_show_advanced' => 'yes',
			]
		]);

		$widget->add_control('cademi_destino_url', [
			'label'         => esc_html__('Internal Redirect (optional)', 'cademi-for-elementor-forms'),
			'description'   => esc_html__('When setting a Internal Redirect, the user will be redirect to this URL after login', 'cademi-for-elementor-forms'),
			'label_block'   => true,
			'type'          => Controls_Manager::TEXT,
			'ai' => [
				'active' => false,
			],
			'condition' => [
				'cademi_show_advanced' => 'yes',
			]
		]);

		$widget->add_control('cademi_campo_nome', [
			'label'         => esc_html__('Name\'s field id', 'cademi-for-elementor-forms'),
			'label_block'   => false,
			'type'          => Controls_Manager::TEXT,
			'default'       => 'name',
			'ai' => [
				'active' => false,
			],
			'condition' => [
				'cademi_show_advanced' => 'yes',
			]
		]);

		$widget->add_control('cademi_campo_email', [
			'label'         => esc_html__('Email\'s field id', 'cademi-for-elementor-forms'),
			'label_block'   => false,
			'type'          => Controls_Manager::TEXT,
			'default'       => 'email',
			'ai' => [
				'active' => false,
			],
			'condition' => [
				'cademi_show_advanced' => 'yes',
			]
		]);

		$widget->add_control('cademi_campo_celular', [
			'label'         => esc_html__('Phone\'s field id', 'cademi-for-elementor-forms'),
			'label_block'   => false,
			'type'          => Controls_Manager::TEXT,
			'default'       => 'phone',
			'ai' => [
				'active' => false,
			],
			'condition' => [
				'cademi_show_advanced' => 'yes',
			]
		]);

		$widget->end_controls_section();
	}

	public function on_export( $element )
	{
		unset(
			$element['settings']['cademi']
		);
		return $element;
	}

	/**
	 * Safely retrieve a form setting value.
	 *
	 * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
	 * @param string $key
	 * @return string
	 */
	private function get_form_setting( $record, $key )
	{
		$settings = $record->get( 'form_settings' );
		return isset( $settings[ $key ] ) ? trim( (string) $settings[ $key ] ) : '';
	}

	/**
	 * Safely retrieve a submitted field value by its configured field ID setting.
	 *
	 * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
	 * @param string $setting_key The form setting that holds the field ID.
	 * @return string
	 */
	private function get_field_value( $record, $setting_key )
	{
		$field_id = $this->get_form_setting( $record, $setting_key );
		if ( '' === $field_id ) {
			return '';
		}
		$fields = $record->get( 'fields' );
		return isset( $fields[ $field_id ]['value'] ) ? trim( (string) $fields[ $field_id ]['value'] ) : '';
	}

	public function run( $record, $ajax_handler )
	{
		// Retrieve and sanitize form settings.
		$raw_url = $this->get_form_setting( $record, 'cademi_url' );
		$token   = sanitize_text_field( $this->get_form_setting( $record, 'cademi_token' ) );

		// Build the platform base URL.
		$platform_url = '';
		if ( '' !== $raw_url ) {
			if ( strpos( $raw_url, '://' ) === false ) {
				$raw_url = 'https://' . $raw_url;
			}
			$raw_url = strtolower( $raw_url );
			$parsed  = wp_parse_url( $raw_url );
			if ( ! empty( $parsed['scheme'] ) && ! empty( $parsed['host'] ) ) {
				$platform_url = $parsed['scheme'] . '://' . $parsed['host'];
			}
		}

		// Delivery ID: numeric only.
		$entrega_id = preg_replace( '/\D/', '', $this->get_form_setting( $record, 'cademi_entrega_id' ) );

		// Internal redirect path — always relative.
		$redirect = $this->get_form_setting( $record, 'cademi_destino_url' );
		if ( '' !== $redirect ) {
			if ( strpos( $redirect, '://' ) !== false ) {
				$parsed   = wp_parse_url( $redirect );
				$redirect = isset( $parsed['path'] ) ? $parsed['path'] : '/';
			}
			$redirect = preg_replace( '#^[a-zA-Z]+:#', '', $redirect );
			$redirect = '/' . ltrim( $redirect, '/' );
			$redirect = sanitize_text_field( $redirect );
		}

		// Form field values.
		$nome    = sanitize_text_field( $this->get_field_value( $record, 'cademi_campo_nome' ) );
		$email   = sanitize_email( $this->get_field_value( $record, 'cademi_campo_email' ) );
		$celular = sanitize_text_field( $this->get_field_value( $record, 'cademi_campo_celular' ) );

		// Validate email.
		if ( '' !== $email && ! is_email( $email ) ) {
			$ajax_handler->add_error_message(
				esc_html__( 'Cademi Redirect :: Invalid email address', 'cademi-for-elementor-forms' )
			);
			return;
		}

		// Validate required fields.
		$required = [
			'url'   => $platform_url,
			'token' => $token,
			'email' => $email,
		];
		foreach ( $required as $key => $value ) {
			if ( empty( $value ) ) {
				$ajax_handler->add_error_message(
					sprintf(
						/* translators: %s: field name (url, token, or email) */
						esc_html__( 'Cademi Redirect :: "%s" not defined', 'cademi-for-elementor-forms' ),
						$key
					)
				);
				return;
			}
		}

		// Build query parameters.
		$query_params = [
			'token' => $token,
			'email' => $email,
		];
		if ( '' !== $nome ) {
			$query_params['nome'] = $nome;
		}
		if ( '' !== $celular ) {
			$query_params['celular'] = $celular;
		}
		if ( '' !== $entrega_id ) {
			$query_params['entrega_id'] = $entrega_id;
		}
		if ( '' !== $redirect ) {
			$query_params['redirect'] = $redirect;
		}

		$url = sprintf(
			'%s/auth/cadastrar_via_url?%s',
			$platform_url,
			http_build_query( $query_params )
		);

		$ajax_handler->add_response_data( 'redirect_url', esc_url_raw( $url ) );
	}
}
