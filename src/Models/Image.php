<?php

declare(strict_types=1);

namespace App\Models;

use App\Exceptions\ImageProcessingException;
use App\Utils\{FileHelper, ImageProcessor};
use Chubbyphp\Parsing\Error;
use Chubbyphp\Parsing\ErrorsException;
use Chubbyphp\Parsing\Parser;

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
        $p = new Parser();

        $schema = $p->assoc([
            'title' => $p->string()->default('')->postParse(static function (string $title) {
                if ($title === '') {
                    throw new ErrorsException(new Error('required', 'Provide a title for the image', []));
                }

                return $title;
            }),
            'watermark' => $p->string()->default('')->postParse(static function (string $watermark) {
                if ($watermark === '') {
                    throw new ErrorsException(new Error('required', 'Provide a watermark', []));
                }

                return $watermark;
            }),
            'visibility' => $p->string()->default('')->postParse(static function (string $visibility) {
                if ($visibility === '') {
                    throw new ErrorsException(new Error('required', 'Please select the visibility setting', []));
                }
                if (!in_array($visibility, ['public', 'private'], true)) {
                    throw new ErrorsException(new Error('enum', 'Visibility can only be public or private', []));
                }
                if ($visibility === 'private' && !User::current()) {
                    throw new ErrorsException(new Error('auth', 'Anonymous users can only upload public images', []));
                }

                return $visibility;
            }),
            'file' => $p->assoc([
                'error' => $p->int()->postParse(static function (int $error) {
                    if ($error == UPLOAD_ERR_NO_FILE) {
                        throw new ErrorsException(new Error('upload', 'No file sent', []));
                    }
                    if ($error == UPLOAD_ERR_INI_SIZE || $error == UPLOAD_ERR_FORM_SIZE) {
                        throw new ErrorsException(new Error('upload', 'Exceeded 1MB filesize limit', []));
                    }

                    return $error;
                }),
                'tmp_name' => $p->string()->postParse(static function (string $tmpName) {
                    if (!FileHelper::isImage($tmpName)) {
                        throw new ErrorsException(new Error('upload', 'Invalid file format', []));
                    }

                    return $tmpName;
                }),
            ]),
        ]);

        $result = $schema->safeParse($data);

        return $result->success ? [] : $result->exception->errors->toTree();
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
