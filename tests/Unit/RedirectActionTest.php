<?php

/*
|--------------------------------------------------------------------------
| Cademi_Action_After_Submit — Unit Tests
|--------------------------------------------------------------------------
*/

beforeEach(function () {
    $this->action = new Cademi_Action_After_Submit();
});

// -------------------------------------------------------
// Metadata
// -------------------------------------------------------

test('get_name returns cademi_redirect', function () {
    expect($this->action->get_name())->toBe('cademi_redirect');
});

test('get_label returns Redirect - Cademi', function () {
    expect($this->action->get_label())->toBe('Redirect - Cademi');
});

// -------------------------------------------------------
// Successful redirect
// -------------------------------------------------------

test('run builds correct redirect URL with all fields', function () {
    $record = make_record(
        settings: [
            'cademi_url'           => 'https://plataforma.cademi.com.br',
            'cademi_token'         => 'abc123',
            'cademi_entrega_id'    => '42',
            'cademi_destino_url'   => '/dashboard',
            'cademi_campo_nome'    => 'name',
            'cademi_campo_email'   => 'email',
            'cademi_campo_celular' => 'phone',
        ],
        fields: [
            'name'  => ['value' => 'João Silva'],
            'email' => ['value' => 'joao@example.com'],
            'phone' => ['value' => '11999998888'],
        ],
    );

    $handler = make_ajax_handler();
    $this->action->run($record, $handler);

    expect($handler->errors)->toBeEmpty();
    expect($handler->data)->toHaveKey('redirect_url');

    $url = $handler->data['redirect_url'];
    $parsed = parse_url($url);

    expect($parsed['scheme'])->toBe('https');
    expect($parsed['host'])->toBe('plataforma.cademi.com.br');
    expect($parsed['path'])->toBe('/auth/cadastrar_via_url');

    parse_str($parsed['query'], $query);
    expect($query['token'])->toBe('abc123');
    expect($query['email'])->toBe('joao@example.com');
    expect($query['nome'])->toBe('João Silva');
    expect($query['celular'])->toBe('11999998888');
    expect($query['entrega_id'])->toBe('42');
    expect($query['redirect'])->toBe('/dashboard');
});

test('run builds URL with only required fields', function () {
    $record = make_record(
        settings: [
            'cademi_url'           => 'https://plataforma.cademi.com.br',
            'cademi_token'         => 'token123',
            'cademi_campo_email'   => 'email',
        ],
        fields: [
            'email' => ['value' => 'test@example.com'],
        ],
    );

    $handler = make_ajax_handler();
    $this->action->run($record, $handler);

    expect($handler->errors)->toBeEmpty();

    $url = $handler->data['redirect_url'];
    parse_str(parse_url($url, PHP_URL_QUERY), $query);

    expect($query['token'])->toBe('token123');
    expect($query['email'])->toBe('test@example.com');
    expect($query)->not->toHaveKey('nome');
    expect($query)->not->toHaveKey('celular');
    expect($query)->not->toHaveKey('entrega_id');
    expect($query)->not->toHaveKey('redirect');
});

// -------------------------------------------------------
// URL normalization
// -------------------------------------------------------

test('run prepends https when URL has no protocol', function () {
    $record = make_record(
        settings: [
            'cademi_url'         => 'plataforma.cademi.com.br',
            'cademi_token'       => 'tok',
            'cademi_campo_email' => 'email',
        ],
        fields: [
            'email' => ['value' => 'a@b.com'],
        ],
    );

    $handler = make_ajax_handler();
    $this->action->run($record, $handler);

    expect($handler->errors)->toBeEmpty();
    expect($handler->data['redirect_url'])->toStartWith('https://plataforma.cademi.com.br/');
});

test('run lowercases the platform URL', function () {
    $record = make_record(
        settings: [
            'cademi_url'         => 'HTTPS://PLATAFORMA.CADEMI.COM.BR',
            'cademi_token'       => 'tok',
            'cademi_campo_email' => 'email',
        ],
        fields: [
            'email' => ['value' => 'a@b.com'],
        ],
    );

    $handler = make_ajax_handler();
    $this->action->run($record, $handler);

    expect($handler->data['redirect_url'])->toStartWith('https://plataforma.cademi.com.br/');
});

// -------------------------------------------------------
// Redirect path normalization (always relative)
// -------------------------------------------------------

test('redirect: relative path without slash becomes /path', function () {
    $record = make_record(
        settings: [
            'cademi_url'         => 'https://p.cademi.com.br',
            'cademi_token'       => 'tok',
            'cademi_destino_url' => 'dashboard',
            'cademi_campo_email' => 'email',
        ],
        fields: [
            'email' => ['value' => 'a@b.com'],
        ],
    );

    $handler = make_ajax_handler();
    $this->action->run($record, $handler);

    parse_str(parse_url($handler->data['redirect_url'], PHP_URL_QUERY), $query);
    expect($query['redirect'])->toBe('/dashboard');
});

test('redirect: absolute path stays as /path', function () {
    $record = make_record(
        settings: [
            'cademi_url'         => 'https://p.cademi.com.br',
            'cademi_token'       => 'tok',
            'cademi_destino_url' => '/my-area',
            'cademi_campo_email' => 'email',
        ],
        fields: [
            'email' => ['value' => 'a@b.com'],
        ],
    );

    $handler = make_ajax_handler();
    $this->action->run($record, $handler);

    parse_str(parse_url($handler->data['redirect_url'], PHP_URL_QUERY), $query);
    expect($query['redirect'])->toBe('/my-area');
});

test('redirect: full URL is stripped to path only', function () {
    $record = make_record(
        settings: [
            'cademi_url'         => 'https://p.cademi.com.br',
            'cademi_token'       => 'tok',
            'cademi_destino_url' => 'https://other.cademi.com.br/curso/123',
            'cademi_campo_email' => 'email',
        ],
        fields: [
            'email' => ['value' => 'a@b.com'],
        ],
    );

    $handler = make_ajax_handler();
    $this->action->run($record, $handler);

    parse_str(parse_url($handler->data['redirect_url'], PHP_URL_QUERY), $query);
    expect($query['redirect'])->toBe('/curso/123');
});

test('redirect: empty value does not add redirect param', function () {
    $record = make_record(
        settings: [
            'cademi_url'         => 'https://p.cademi.com.br',
            'cademi_token'       => 'tok',
            'cademi_destino_url' => '',
            'cademi_campo_email' => 'email',
        ],
        fields: [
            'email' => ['value' => 'a@b.com'],
        ],
    );

    $handler = make_ajax_handler();
    $this->action->run($record, $handler);

    parse_str(parse_url($handler->data['redirect_url'], PHP_URL_QUERY), $query);
    expect($query)->not->toHaveKey('redirect');
});

test('redirect: multiple slashes are normalized', function () {
    $record = make_record(
        settings: [
            'cademi_url'         => 'https://p.cademi.com.br',
            'cademi_token'       => 'tok',
            'cademi_destino_url' => '///dashboard',
            'cademi_campo_email' => 'email',
        ],
        fields: [
            'email' => ['value' => 'a@b.com'],
        ],
    );

    $handler = make_ajax_handler();
    $this->action->run($record, $handler);

    parse_str(parse_url($handler->data['redirect_url'], PHP_URL_QUERY), $query);
    expect($query['redirect'])->toBe('/dashboard');
});

// -------------------------------------------------------
// Delivery ID
// -------------------------------------------------------

test('entrega_id strips non-numeric characters', function () {
    $record = make_record(
        settings: [
            'cademi_url'         => 'https://p.cademi.com.br',
            'cademi_token'       => 'tok',
            'cademi_entrega_id'  => 'abc42def',
            'cademi_campo_email' => 'email',
        ],
        fields: [
            'email' => ['value' => 'a@b.com'],
        ],
    );

    $handler = make_ajax_handler();
    $this->action->run($record, $handler);

    parse_str(parse_url($handler->data['redirect_url'], PHP_URL_QUERY), $query);
    expect($query['entrega_id'])->toBe('42');
});

// -------------------------------------------------------
// Validation errors
// -------------------------------------------------------

test('error when platform URL is missing', function () {
    $record = make_record(
        settings: [
            'cademi_token'       => 'tok',
            'cademi_campo_email' => 'email',
        ],
        fields: [
            'email' => ['value' => 'a@b.com'],
        ],
    );

    $handler = make_ajax_handler();
    $this->action->run($record, $handler);

    expect($handler->errors)->toHaveCount(1);
    expect($handler->errors[0])->toContain('url');
    expect($handler->data)->not->toHaveKey('redirect_url');
});

test('error when token is missing', function () {
    $record = make_record(
        settings: [
            'cademi_url'         => 'https://p.cademi.com.br',
            'cademi_campo_email' => 'email',
        ],
        fields: [
            'email' => ['value' => 'a@b.com'],
        ],
    );

    $handler = make_ajax_handler();
    $this->action->run($record, $handler);

    expect($handler->errors)->toHaveCount(1);
    expect($handler->errors[0])->toContain('token');
});

test('error when email is missing', function () {
    $record = make_record(
        settings: [
            'cademi_url'   => 'https://p.cademi.com.br',
            'cademi_token' => 'tok',
        ],
        fields: [],
    );

    $handler = make_ajax_handler();
    $this->action->run($record, $handler);

    expect($handler->errors)->toHaveCount(1);
    expect($handler->errors[0])->toContain('email');
});

test('invalid email is sanitized to empty and triggers required error', function () {
    $record = make_record(
        settings: [
            'cademi_url'         => 'https://p.cademi.com.br',
            'cademi_token'       => 'tok',
            'cademi_campo_email' => 'email',
        ],
        fields: [
            'email' => ['value' => 'not-an-email'],
        ],
    );

    $handler = make_ajax_handler();
    $this->action->run($record, $handler);

    // sanitize_email strips invalid emails to '', hitting the required field check
    expect($handler->errors)->toHaveCount(1);
    expect($handler->errors[0])->toContain('email');
    expect($handler->data)->not->toHaveKey('redirect_url');
});

// -------------------------------------------------------
// on_export
// -------------------------------------------------------

test('on_export removes cademi settings', function () {
    $element = [
        'settings' => [
            'cademi'      => ['some' => 'data'],
            'other_stuff' => true,
        ],
    ];

    $result = $this->action->on_export($element);

    expect($result['settings'])->not->toHaveKey('cademi');
    expect($result['settings'])->toHaveKey('other_stuff');
});
