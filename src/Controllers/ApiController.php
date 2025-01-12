<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Image;
use App\View;

class ApiController
{
    public static function search(): View
    {
        $query = $_POST['q'] ?? '';

        $images = Image::textSearch($query);

        return View::make("search-results", [
            'images' => $images,
        ]);
    }
}
