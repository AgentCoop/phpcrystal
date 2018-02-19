<?php

namespace PhpCrystal\Core\Services;

use Illuminate\Support\Facades\Config;

use PhpCrystal\Core\Models\Physical\Support\Logging\MongoDB\ErrorEntry as ErrorEntryMongoDb;
use PhpCrystal\Core\Models\Physical\Support\Logging\MySQL\ErrorEntry as ErrorEntryMySql;

class Factory
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
