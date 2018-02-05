<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;

use App\Models\Physical\Support\Logging\MongoDB\ErrorEntry as ErrorEntryMongoDb;
use App\Models\Physical\Support\Logging\MySQL\ErrorEntry as ErrorEntryMySql;

abstract class AbstractSupport
{
    const MYSQL_CONN_NAME = 'mysql';
    const MONGODB_CONN_NAME = 'mongodb';

    /**
     * @return string
    */
    final protected static function getDefaultConnName()
    {
        return Config::get('database.default');
    }

    /**
     * @return MySqlErrorEntry|MongoDbErrorEntry
     */
    final public static function logEntryFactory()
    {
        $conn = static::getDefaultConnName();

        switch ($conn) {
            case self::MYSQL_CONN_NAME:
                return new ErrorEntryMySql();

            case self::MONGODB_CONN_NAME:
                return new ErrorEntryMongoDb();

            default:
                throw new \RuntimeException(sprintf('Unsupported database %s', $conn));
        }
    }
}