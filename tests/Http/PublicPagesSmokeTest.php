<?php

declare(strict_types=1);

use function Tests\Support\http_get;

it('serves the gallery home page', function () {
    $response = http_get('/');

    expect($response['status'])->toBe(200)
        ->and($response['body'])->toContain('<title>Image Gallery</title>')
        ->and($response['body'])->toContain('class="gallery"');
});

it('serves the login page', function () {
    $response = http_get('/auth/login');

    expect($response['status'])->toBe(200)
        ->and($response['body'])->toContain('<h1>Welcome back</h1>')
        ->and($response['body'])->toContain('name="login"')
        ->and($response['body'])->toContain('name="password"');
});

it('serves the register page', function () {
    $response = http_get('/auth/register');

    expect($response['status'])->toBe(200)
        ->and($response['body'])->toContain('<h1>Create an account</h1>')
        ->and($response['body'])->toContain('name="email"')
        ->and($response['body'])->toContain('name="repeatPassword"');
});

it('serves the search page', function () {
    $response = http_get('/search');

    expect($response['status'])->toBe(200)
        ->and($response['body'])->toContain('id="searchBar"')
        ->and($response['body'])->toContain('name="q"')
        ->and($response['body'])->toContain('id="gallery"');
});

it('serves the upload page to unauthenticated visitors', function () {
    $response = http_get('/upload');

    expect($response['status'])->toBe(200)
        ->and($response['body'])->toContain('Choose Photo')
        ->and($response['body'])->toContain('name="visibility"')
        ->and($response['body'])->toContain('href="/auth/login"')
        ->and($response['body'])->not->toContain('id="login-display"');
});

it('fails with a clear readiness signal when the stack is unreachable', function () {
    expect(fn () => http_get('/', 'http://127.0.0.1:1'))
        ->toThrow(
            RuntimeException::class,
            'HTTP test base URL http://127.0.0.1:1 is not reachable'
        );
});
