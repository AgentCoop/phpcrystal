<?php

namespace PhpCrystal\Core\Models\Physical\Support\Logging\MongoDB;

use PhpCrystal\Core\Models\Logical\Support\Logging\ErrorEntry as ErrorEntryLogical;
use PhpCrystal\Core\Models\Physical\DAL\AbstractMongoDb;

use Carbon\Carbon;

class ErrorEntry extends AbstractMongoDb
{
    use ErrorEntryLogical;

    protected $collection = 'logs.errors';

    /**
     * @return \PhpCrystal\Core\Models\Physical\Support\Logging\MongoDB\ErrorEntry[]
    */
    public static function getLast($limit = 10)
    {
        $query = static::createDefaultQuery();

        $query->take($limit);

        return $query->get();
    }

    /**
     * @return \PhpCrystal\Core\Models\Physical\Support\Logging\MongoDB\ErrorEntry[]
    */
    public static function getBefore(Carbon $datetime)
    {
        $query = static::query();

        $query
            ->where(self::CREATED_AT, '<', $datetime);

        return $query->get();
    }
}
