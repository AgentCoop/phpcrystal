<?php

namespace App\Models\Aux\Logging;

use App\Models\Aux\Database\AbstractMongoDb;

class MongoDbErrorEntry extends AbstractMongoDb
{
    use ErrorEntry;

    protected $collection = 'error_logs';
}
