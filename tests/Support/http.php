<?php

declare(strict_types=1);

namespace Tests\Support;

/**
 * @return array{status: int, body: string}
 */
function http_get(string $path, ?string $baseUrl = null): array
{
    $baseUrl ??= http_base_url();
    $url = rtrim($baseUrl, '/') . '/' . ltrim($path, '/');

    $ch = curl_init($url);
    if ($ch === false) {
        throw new \RuntimeException('Failed to initialize cURL for HTTP test request.');
    }

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_CONNECTTIMEOUT => 2,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_HEADER => false,
    ]);

    $body = curl_exec($ch);
    $errno = curl_errno($ch);
    $error = curl_error($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($errno !== 0 || $body === false) {
        throw new \RuntimeException(
            "HTTP test base URL {$baseUrl} is not reachable ({$error}). "
            . 'Start the stack with `devenv up` before running HTTP tests.'
        );
    }

    if ($status >= 500) {
        throw new \RuntimeException(
            "HTTP test base URL {$baseUrl} returned HTTP {$status}. "
            . 'Check that devenv processes (Caddy, PHP-FPM, MongoDB) are healthy.'
        );
    }

    return [
        'status' => $status,
        'body' => (string) $body,
    ];
}

function http_base_url(): string
{
    $fromEnv = getenv('HTTP_BASE_URL');
    if (is_string($fromEnv) && $fromEnv !== '') {
        return $fromEnv;
    }

    return 'http://127.0.0.1:8080';
}
