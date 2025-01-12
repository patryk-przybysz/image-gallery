<?php

declare(strict_types=1);

namespace App\Controllers;

use App\{View, Models\Image};
use App\Models\User;

use function App\Utils\empty_recursive;

class GalleryController
{
    public static function index(): View
    {
        $user = User::current();
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        $perPage = filter_input(INPUT_GET, 'per_page', FILTER_VALIDATE_INT) ?: 10;

        $result = Image::getPaginatedPublicOrUserUploadedImages($user, $page, $perPage);
        $images = $result['images'];
        $totalCount = $result['totalCount'];

        $totalPages = (int)ceil($totalCount / $perPage);
        $totalPages = max($totalPages, 1);

        return View::make(
            'gallery',
            [
                'images' => $images,
                'totalCount' => $totalCount,
                'currentPage' => $page,
                'perPage' => $perPage,
                'totalPages' => $totalPages,
            ]
        )->withLayout("main");
    }


    public static function upload(): View
    {
        $form = [
            'title' => '',
            'watermark' => '',
            'author' => User::getName() ?? '',
            'visibility' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST != null) {
            $SANITIZED_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            $form = array_replace($form, $SANITIZED_POST + $_FILES);

            $errors = Image::create($form);

            if (empty_recursive($errors)) {
                http_response_code(303);
                header('Location: /');
                return;
            }
        }

        return View::make('upload', [
            'form' => $form,
            'errors' => $errors ?? [],
        ])->withLayout('main');
    }

    public static function search(): View
    {
        return View::make('search')->withLayout('main');
    }
}
