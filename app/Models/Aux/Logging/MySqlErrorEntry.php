<?php

namespace App\Models\Aux\Logging;

class MySqlErrorEntry
{
    use ErrorEntry;

    protected $table = 'error_log';
}