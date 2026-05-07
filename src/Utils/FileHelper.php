<?php

declare(strict_types=1);

namespace App\Utils;

use \finfo;

class FileHelper
{
    public static function getMimeType(string $fileName)
    {
        if ($fileName === '' || !is_file($fileName)) {
            return null;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        return $finfo->file($fileName) ?: null;
    }

    public static function getExtension(string $fileName)
    {
        $mime = self::getMimeType($fileName);

        if (!$mime) {
            return null;
        }

        return explode('/', $mime)[1] ?? null;
    }

    public static function isImage(string $fileName)
    {
        return in_array(
            self::getMimeType($fileName),
            ['image/jpeg', 'image/png'],
            true
        );
    }
}
