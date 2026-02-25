<?php

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

/**
 * Create a mock Elementor form record.
 */
function make_record(array $settings = [], array $fields = []): object
{
    return new class($settings, $fields) {
        private array $data;

        public function __construct(array $settings, array $fields)
        {
            $this->data = [
                'form_settings' => $settings,
                'fields'        => $fields,
            ];
        }

        public function get(string $key)
        {
            return $this->data[$key] ?? [];
        }
    };
}

/**
 * Create a mock AJAX handler that captures calls.
 */
function make_ajax_handler(): object
{
    return new class {
        public array $errors = [];
        public array $data = [];

        public function add_error_message(string $msg): void
        {
            $this->errors[] = $msg;
        }

        public function add_response_data(string $key, $value): void
        {
            $this->data[$key] = $value;
        }
    };
}
