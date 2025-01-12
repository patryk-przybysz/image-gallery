<?php

declare(strict_types=1);

namespace App\Utils;

use App\Models\Image;

class ImageProcessor
{
    const STORAGE_PATH = __DIR__ . '/../web/images';

    const THUMBNAIL_WIDTH = 200;
    const THUMBNAIL_HEIGHT = 125;

    private $originalFile;
    private $ext;
    private $img;
    private $full;
    private $thumbnail;


    public function process(Image $image)
    {
        $this->initializeProperties($image);

        return $this->addWatermark($image->watermark)
            ->generateThumbnail()
            ->moveToStorage();
    }


    private function initializeProperties(Image $image)
    {
        $this->originalFile = $image->file['tmp_name'];
        $this->full = $this->originalFile . '-full';
        $this->thumbnail = $this->originalFile . '-thumbnail';
        $this->ext = FileHelper::getExtension($this->originalFile);
        $createFn = "imagecreatefrom{$this->ext}";
        $this->img = $createFn($this->originalFile);
        imagesavealpha($this->img, true);
    }


    private function addWatermark(string $watermark)
    {
        $white = imagecolorallocate($this->img, 0xFF, 0xFF, 0xFF);
        imagestring($this->img, 5, 20, 10, html_entity_decode($watermark), $white);

        $saveFn = "image{$this->ext}";
        $saveFn($this->img, $this->full);

        return $this;
    }


    // https://salman-w.blogspot.com/2009/04/crop-to-fit-image-using-aspphp.html
    private function generateThumbnail()
    {

        $sourceWidth = imagesx($this->img);
        $sourceHeight = imagesy($this->img);

        $sourceAspectRatio = $sourceWidth / $sourceHeight;
        $thumbnailAspectRatio  = self::THUMBNAIL_WIDTH / self::THUMBNAIL_HEIGHT;

        if ($sourceAspectRatio > $thumbnailAspectRatio) {
            // Image is wider
            $newHeight = $sourceHeight;
            $newWidth = (int)round($sourceHeight * $thumbnailAspectRatio);
            $x = (int)round(($sourceWidth - $newWidth) / 2);
            $y = 0;
        } else {
            // Image is taller
            $newWidth = $sourceWidth;
            $newHeight = (int)round($sourceWidth / $thumbnailAspectRatio);
            $x = 0;
            $y = (int)round(($sourceHeight - $newHeight) / 2);
        }


        $thumbnail = imagecreatetruecolor(self::THUMBNAIL_WIDTH, self::THUMBNAIL_HEIGHT);
        imagesavealpha($thumbnail, true);

        imagecopyresampled(
            $thumbnail,
            $this->img,
            0,
            0,
            $x,
            $y,
            self::THUMBNAIL_WIDTH,
            self::THUMBNAIL_HEIGHT,
            $newWidth,
            $newHeight
        );


        $saveFn = "image{$this->ext}";
        $saveFn($thumbnail, $this->thumbnail);

        return $this;
    }


    private function moveToStorage()
    {
        $storagePath = self::STORAGE_PATH;
        $imageHash = sha1_file($this->full);

        $imageFolderPath = "{$storagePath}/{$imageHash}";

        // Emits warning if path already exists
        mkdir($imageFolderPath, 0777, true);

        $originalPath = "{$imageFolderPath}/original.{$this->ext}";
        $fullPath = "{$imageFolderPath}/full.{$this->ext}";
        $thumbnailPath = "{$imageFolderPath}/thumbnail.{$this->ext}";
        move_uploaded_file($this->originalFile, $originalPath);

        // Can't use move_uploaded_file on a non-user created file
        rename($this->full, $fullPath);
        rename($this->thumbnail, $thumbnailPath);

        return [
            'original' => "/images/{$imageHash}/full.{$this->ext}",
            'full' => "/images/{$imageHash}/full.{$this->ext}",
            'thumbnail' => "/images/{$imageHash}/thumbnail.{$this->ext}"
        ];
    }
}
