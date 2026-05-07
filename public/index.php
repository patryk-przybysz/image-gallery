<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\{Router, View};
use App\Controllers\{ApiController, AuthController, GalleryController};

session_name("auth");
session_start();

$router = new Router();
$router
    ->get('/', [GalleryController::class, 'index'])
    ->get('/upload', [GalleryController::class, 'upload'])
    ->post('/upload', [GalleryController::class, 'upload'])
    ->get('/search', [GalleryController::class, 'search'])


    ->get('/auth/login', [AuthController::class, 'login'])
    ->post('/auth/login', [AuthController::class, 'login'])

    ->get('/auth/register', [AuthController::class, 'register'])
    ->post('/auth/register', [AuthController::class, 'register'])

    ->get('/auth/logout', [AuthController::class, 'logout'])
    ->post('/auth/logout', [AuthController::class, 'logout'])


    ->post('/api/search', [ApiController::class, 'search']);


try {
    echo $router->resolve(
        $_SERVER['REQUEST_URI'],
        $_SERVER['REQUEST_METHOD']
    );
} catch (\Exception $e) {
    $code = $e->getCode();
    $message = $e->getMessage();
    http_response_code($code);
    echo View::make("error", [
        'message' => $message,
        'code' => $code,
    ])->withLayout('main', [
        'title' => "Image Gallery | Error"
    ]);
}
