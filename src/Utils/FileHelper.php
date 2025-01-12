<?php

declare(strict_types=1);

namespace App\Utils;

use \finfo;

class FileHelper
{
    public static function getMimeType(string $fileName)
    {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        return @$finfo->file($fileName);
    }

    public static function getExtension(string $fileName)
    {
        $mime = self::getMimeType($fileName);
        return explode('/', $mime)[1];
    }

    public static function isImage(string $fileName)
    {
        return in_array(
            self::getMimeType($fileName),
            ['image/jpeg', 'image/png']
        );
    }
}
