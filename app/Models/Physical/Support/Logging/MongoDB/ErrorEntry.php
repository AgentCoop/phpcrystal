<?php

namespace App\Models\Physical\Support\Logging\MongoDB;

use App\Models\Logical\Support\Logging\ErrorEntry as ErrorEntryLogical;
use App\Models\Physical\DAL\AbstractMongoDb;

use Carbon\Carbon;

class ErrorEntry extends AbstractMongoDb
{
    use ErrorEntryLogical;

    protected $collection = 'logs.errors';

    /**
     * @return \App\Models\Physical\Support\Logging\MongoDB\ErrorEntry[]
    */
    public static function getLast($limit = 10)
    {
        $query = static::createDefaultQuery();

        $query->take($limit);

        return $query->get();
    }

    /**
     * @return \App\Models\Physical\Support\Logging\MongoDB\ErrorEntry[]
    */
    public static function getBefore(Carbon $datetime)
    {
        $query = static::query();

        $query
            ->where(self::CREATED_AT, '<', $datetime);

        return $query->get();
    }
}
