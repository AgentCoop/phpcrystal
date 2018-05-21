<?php

namespace App\Component\Base\Database;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class PostgresMigration extends Migration
{
    protected function enum($tableName, $colName, $typeName, array $values, $unique = false)
    {
        DB::transaction(function () use($tableName, $colName, $values, $typeName, $unique) {
            DB::statement(sprintf('CREATE TYPE %s AS ENUM (%s);', $typeName,
                join(',', array_map(function($v) {
                    return "'" . $v . "'";
                }, $values))));

            DB::statement(sprintf('ALTER TABLE %s ADD COLUMN %s %s',
                $tableName, $colName, $typeName));

            if ($unique) {
                DB::statement(sprintf('ALTER TABLE %s ADD CONSTRAINT %s_unique_constraint UNIQUE (%s);',
                    $tableName, $colName, $colName));
            }
        });
    }

    protected function createdAt($table)
    {
        return $this->defaultTime($table->timestamp('created_at', 0));
    }

    protected function defaultTime($table)
    {
        return $table->default(DB::raw('CURRENT_TIMESTAMP(0)'));
    }
}
