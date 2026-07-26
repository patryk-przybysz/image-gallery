<?php

declare(strict_types=1);

namespace App\Controllers;

use App\{View, Models\User};

use function App\Utils\empty_recursive;

class AuthController
{
    public static function login(): View
    {
        $form = [
            'login' => '',
            'password' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $SANITIZED_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            $form = array_replace($form, $SANITIZED_POST);

            $errors = User::login($form);

            if (!$errors) {
                http_response_code(303);
                header('Location: /');
                exit;
            }
        }

        return View::make('auth/login', [
            'form' => $form,
            'errors' => $errors ?? [],
        ])->withLayout('main');
    }


    public static function register(): View
    {
        $form = [
            'email' => '',
            'login' => '',
            'password' => '',
            'repeatPassword' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $SANITIZED_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            $form = array_replace($form, $SANITIZED_POST);

            $errors = User::register($form);

            if (empty_recursive($errors)) {
                http_response_code(303);
                header('Location: /');
                exit;
            }
        }

        return View::make('auth/register', [
            'form' => $form,
            'errors' => $errors ?? [],
        ])->withLayout('main');
    }

    public static function logout(): void
    {
        User::logout();

        $referer = $_SERVER['HTTP_REFERER'] ?? '/';

        header("Location: $referer");
    }
}
