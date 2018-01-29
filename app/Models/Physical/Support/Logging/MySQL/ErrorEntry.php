<?php

namespace App\Models\Physical\Support\Logging\MySQL;

use App\Models\Logical\Support\Logging\ErrorEntry as ErrorEntryLogical;
use App\Models\Physical\DAL\AbstractMySql;

class ErrorEntry extends AbstractMySql
{
    use ErrorEntryLogical;

    protected $table = 'logs_errors';
}
