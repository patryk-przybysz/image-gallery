<?php

declare(strict_types=1);

namespace App;

use MongoDB\Client;

// seeding https://www.mongodb.com/docs/php-library/v1.1/tutorial/example-data/
/** @mixin \MongoDB\Database */
class Database
{
    private static $db;


    public static function selectCollection(string $collectionName)
    {
        if (!isset(self::$db)) {
            $uri = getenv("DB_URI");
            $client = new Client($uri);

            self::$db = $client->selectDatabase('wai');

            try {
                self::$db->images->createIndex(['title' => 'text'], ['background' => true]);
            } catch (\Throwable $e) {
                // Ignore index creation failures on newer PHP/MongoDB extension combos.
            }
        }

        return self::$db->selectCollection($collectionName);
    }
}