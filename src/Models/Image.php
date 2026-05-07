<?php

declare(strict_types=1);

namespace App\Models;

use App\Exceptions\ImageProcessingException;
use App\Utils\{FileHelper, ImageProcessor, ValidationSchema};
use function App\Utils\empty_recursive;

// https://www.mongodb.com/docs/php-library/v1.1/reference/bson/#persistable-classes
class Image extends Model
{
    protected static $collectionName = "images";

    public $title;
    public $watermark;
    /* The visibility of the image, either 'public' or 'private'. Default is 'public'. */
    public $visibility;
    /* The file data associated with the original image. */
    public $file;
    /* The paths array containing 'original', 'full' and 'thumbnail' paths. */
    public $paths;
    public $score;
    /** The name of the author as provided by the user in the form. 
     *  This string might be anything and is not always reliable
     * */
    public $author;
    /** The inner user ID reference for private photos. 
     *  This field is set only if the visibility is 'private'.
     */
    public $privateAuthorId;


    // https://www.mongodb.com/docs/v3.2/text-search/
    public static function textSearch(string $query = '')
    {

        $filter = [
            '$or' => [
                ['visibility' => 'public'],
            ]
        ];

        $filter['$text'] = ['$search' => $query];

        $user = User::current();
        if ($user) {
            $filter['$or'][] = [
                'visibility' => 'private',
                'privateAuthorId' => $user->_id
            ];
        }

        $options['projection'] = $options['sort'] = ['score' => ['$meta' => 'textScore']];

        return self::find($filter, $options);
    }

    public static function getPaginatedPublicOrUserUploadedImages(?User $user = null, int $page = 1, int $perPage = 10)
    {
        $filter = [
            '$or' => [
                ['visibility' => 'public'],
            ]
        ];

        if ($user) {
            $filter['$or'][] = [
                'visibility' => 'private',
                'privateAuthorId' => $user->_id
            ];
        }

        $options = [
            'skip' => ($page - 1) * $perPage,
            'limit' => $perPage,
            'sort' => [
                'createdAt' => -1
            ]
        ];


        $images = self::find($filter, $options);
        $totalCount = self::count($filter);

        return [
            'images' => $images,
            'totalCount' => $totalCount,
        ];
    }

    private static function validate(array $data)
    {
        $schema = new ValidationSchema();

        $titleSchema = (new ValidationSchema())
            ->string()
            ->required('Provide a title for the image');

        $watermarkSchema = (new ValidationSchema())
            ->string()
            ->required("Provide a watermark");

        $visibilitySchema = (new ValidationSchema())
            ->string()
            ->required('Please select the visibility setting')
            ->enum(['public', 'private'], 'Visibility can only be public or private')
            ->refine(function ($visibility) {
                $user = User::current();
                if ($visibility === "private" && !$user) {
                    return false;
                }
                return true;
            }, 'Anonymous users can only upload public images');

        $fileErrorSchema = (new ValidationSchema())
            ->refine(function ($error) {
                return $error != UPLOAD_ERR_NO_FILE;
            }, 'No file sent')
            ->refine(function ($error) {
                return $error != UPLOAD_ERR_INI_SIZE && $error != UPLOAD_ERR_FORM_SIZE;
            }, 'Exceeded 1MB filesize limit');


        $tmpFileSchema = (new ValidationSchema())
            ->refine(function ($tmpName) {
                return FileHelper::isImage($tmpName);
            }, 'Invalid file format');

        $fileSchema = (new ValidationSchema())
            ->array([
                'error' => $fileErrorSchema,
                'tmp_name' => $tmpFileSchema,
            ]);

        return $schema->array([
            'title' => $titleSchema,
            'watermark' => $watermarkSchema,
            'visibility' => $visibilitySchema,
            'file' => $fileSchema,
        ])->safeParse($data);
    }


    public static function create(array $data)
    {
        $image = new Image();

        $data['watermark'] = $data['watermark'] ?? '';
        $data['author'] = $data['author'] ?? '';
        $data['visibility'] = $data['visibility'] ?? 'public';


        $errors = self::validate($data);
        if (!empty_recursive($errors)) {
            return $errors;
        }

        foreach ($data as $key => $value) {
            $image->$key = $value;
        }

        if ($image->visibility == "private") {
            $image->privateAuthorId = User::current()->_id;
        }


        try {
            $imageProcessor = new ImageProcessor();
            $image->paths = $imageProcessor->process($image);
        } catch (ImageProcessingException $e) {
            return [
                'file' => [$e->getMessage()],
            ];
        }

        $image->save();
    }
}
