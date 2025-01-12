<?php

declare(strict_types=1);

namespace App\Models;

use App\Database;

abstract class Model implements \MongoDB\BSON\Persistable
{
    protected static $collectionName;

    public $createdAt;
    public $_id;

    public function __construct()
    {
        $this->createdAt = new \MongoDB\BSON\UTCDateTime();
        $this->_id = new \MongoDB\BSON\ObjectID();
    }

    // TODO: fix id
    public function bsonSerialize()
    {
        return get_object_vars($this);
    }

    public function bsonUnserialize(array $data)
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    private static function collection()
    {
        return Database::selectCollection(static::$collectionName);
    }

    public static function find(array $filter = [], array $options = [])
    {
        return self::collection()->find($filter, $options);
    }

    public static function findOne(array $filter = [], array $options = [])
    {
        return self::collection()->findOne($filter, $options);
    }

    public static function count(array $filter = [], array $options = [])
    {
        return self::collection()->count($filter, $options);
    }

    public function save()
    {
        return self::collection()->insertOne($this);
    }
}
