<?php

use Elementor\Controls_Manager;
use \ElementorPro\Modules\Forms\Classes\Action_Base;

class Cademi_Action_After_Submit extends Action_Base
{
	public function get_name()
	{
		return 'cademi_redirect';
	}
	
	public function get_label()
	{
		return 'Redirect - Cademí';
	}
	
	public function register_settings_section( $widget )
	{
		$widget->start_controls_section(
			'cademi',
			[
				'label' => 'Redirect - Cademí',
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);
		
		$widget->add_control('cademi_url', [
			'label'         => esc_html__('Platform URL', 'elementor-forms-cademi'),
			'description'   => esc_html__('You can find your Platform URL on: Settings > Default Domain', 'elementor-forms-cademi'),
			'label_block'   => true,
			'type'          => Controls_Manager::TEXT,
			'placeholder'   => 'https://<your_subdomain>.cademi.com.br',
			'ai' => [
				'active' => false,
			]
		]);
		
		$widget->add_control('cademi_token', [
			'label'         => esc_html__('Platform Token', 'elementor-forms-cademi'),
			'description'   => esc_html__('You can find your Platform Token on: Settings > Platform Token', 'elementor-forms-cademi'),
			'label_block'   => true,
			'type'          => Controls_Manager::TEXT,
			'ai' => [
				'active' => false,
			]
		]);
		
		$widget->add_control('cademi_show_advanced', [
			'label' => esc_html__( 'Show advanced settings', 'elementor-forms-cademi'),
			'type' => Controls_Manager::SWITCHER,
			'default' => 'yes',
		]);
		
		$widget->add_control('cademi_entrega_id', [
			'label'         => esc_html__('Delivery ID (optional)', 'elementor-forms-cademi'),
			'description'   => esc_html__('When setting an ID, you will add the Delivery for the user, in addition to the configured free delivery', 'elementor-forms-cademi'),
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
			'label'         => esc_html__('Internal Redirect (optional)', 'elementor-forms-cademi'),
			'description'   => esc_html__('When setting a Internal Redirect, the user will be redirect to this URL after login', 'elementor-forms-cademi'),
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
			'label'         => esc_html__('Name\'s field id', 'elementor-forms-cademi'),
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
			'label'         => esc_html__('Email\'s field id', 'elementor-forms-cademi'),
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
			'label'         => esc_html__('Phone\'s field id', 'elementor-forms-cademi'),
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
	
	public function run( $record, $ajax_handler )
	{
		// Pegando parâmetros
		$params = [
			"url" => function($record)
			{
				$value = @$record->get("form_settings")['cademi_url'];
				$value = @trim($value);
				if(empty($value))
					return '';

				if(strpos($value, "://") === false)
					$value = "https://" . $value;

				$value = strtolower($value);
				$value = parse_url($value);
				$value = $value['scheme'] . '://' . $value['host'];
				return $value;
			},
			"token" => function($record)
			{
				$value = @$record->get("form_settings")['cademi_token'];
				$value = @trim($value);
				return $value;
			},
			"entrega_id" => function($record)
			{
				$value = @$record->get("form_settings")['cademi_entrega_id'];
				$value = preg_replace('/\D/', '', $value);
				return $value;
			},
			"redirect" => function($record)
			{
				$value = @$record->get("form_settings")['cademi_destino_url'];
				$value = @trim($value);
				if(empty($value))
					return '';

				if(strpos($value, "://") === false)
					$value = "https://" . $value;

				$value = strtolower(trim($value));
				$value = parse_url($value);
				$value = $value['path'];
				return $value;
			},
			"nome" => function($record)
			{
				$field_id = @$record->get('form_settings')['cademi_campo_nome'];
				$field_id = @trim($field_id);
				$value = @$record->get("fields")[$field_id]['value'];
				$value = @trim($value);
				return $value;
			},
			"email" => function($record)
			{
				$field_id = @$record->get('form_settings')['cademi_campo_email'];
				$field_id = @trim($field_id);
				$value = @$record->get("fields")[$field_id]['value'];
				$value = @trim($value);
				return $value;
			},
			"celular" => function($record)
			{
				$field_id = @$record->get('form_settings')['cademi_campo_celular'];
				$field_id = @trim($field_id);
				$value = @$record->get("fields")[$field_id]['value'];
				$value = @trim($value);
				return $value;
			}
		];
		foreach($params as $key => $func)
			$params[$key] = $func($record);
		
		// Filtrando e validando parâmetros obrigatórios
		$params = array_filter($params);
		foreach(["url","token","email"] as $key){
			
			if (isset($params[$key])) continue;
			
			$ajax_handler->add_error_message(sprintf(
				esc_html__('Cademí Redirect :: "%s" not defined', 'elementor-forms-cademi'),
				$key
			));
			
			return;
		}

		// Montando URL
		$url = $params['url'];
		unset($params['url']);
		$url = sprintf("%s/auth/cadastrar_via_url?%s",$url,http_build_query($params));
		
		// Devolvendo redirecionamento
		$ajax_handler->add_response_data( 'redirect_url', $url);
	}
}
