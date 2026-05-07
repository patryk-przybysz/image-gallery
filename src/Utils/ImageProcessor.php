<?php

declare(strict_types=1);

namespace App\Utils;

use App\Exceptions\ImageProcessingException;
use App\Models\Image;

class ImageProcessor
{
    const STORAGE_PATH = __DIR__ . '/../../public/images';

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
        $this->originalFile = $image->file['tmp_name'] ?? '';

        if ($this->originalFile === '' || !is_file($this->originalFile)) {
            throw new ImageProcessingException('Uploaded file is missing');
        }

        if (!FileHelper::isImage($this->originalFile)) {
            throw new ImageProcessingException('Only JPEG and PNG images are supported');
        }

        $this->full = $this->originalFile . '-full';
        $this->thumbnail = $this->originalFile . '-thumbnail';
        $this->ext = FileHelper::getExtension($this->originalFile);
        $createFn = "imagecreatefrom{$this->ext}";

        if (!function_exists($createFn)) {
            throw new ImageProcessingException('Image processing support is unavailable');
        }

        $this->img = $createFn($this->originalFile);
        if (!$this->img) {
            throw new ImageProcessingException('Failed to load uploaded image');
        }

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

        $this->ensureDirectory($imageFolderPath);

        $originalPath = "{$imageFolderPath}/original.{$this->ext}";
        $fullPath = "{$imageFolderPath}/full.{$this->ext}";
        $thumbnailPath = "{$imageFolderPath}/thumbnail.{$this->ext}";

        if (!move_uploaded_file($this->originalFile, $originalPath)) {
            throw new ImageProcessingException('Failed to store original upload');
        }

        if (!rename($this->full, $fullPath)) {
            throw new ImageProcessingException('Failed to store processed image');
        }

        if (!rename($this->thumbnail, $thumbnailPath)) {
            throw new ImageProcessingException('Failed to store thumbnail image');
        }

        return [
            'original' => "/images/{$imageHash}/original.{$this->ext}",
            'full' => "/images/{$imageHash}/full.{$this->ext}",
            'thumbnail' => "/images/{$imageHash}/thumbnail.{$this->ext}"
        ];
    }

    private function ensureDirectory(string $path): void
    {
        if (is_dir($path)) {
            return;
        }

        if (!mkdir($path, 0777, true) && !is_dir($path)) {
            throw new ImageProcessingException('Failed to create image storage directory');
        }
    }
}
