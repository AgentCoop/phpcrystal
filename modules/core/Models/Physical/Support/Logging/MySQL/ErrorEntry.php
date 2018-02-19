<?php

namespace PhpCrystal\Core\Models\Physical\Support\Logging\MySQL;

use PhpCrystal\Core\Models\Logical\Support\Logging\ErrorEntry as ErrorEntryLogical;
use PhpCrystal\Core\Models\Physical\DAL\AbstractMySql;

class ErrorEntry extends AbstractMySql
{
    use ErrorEntryLogical;

    protected $table = 'logs_errors';
}
