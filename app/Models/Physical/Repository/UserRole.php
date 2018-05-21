<?php

namespace App\Models\Physical\Repository;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserRole extends Pivot
{
    const TABLE_NAME = 'user_role';

    protected $table = self::TABLE_NAME;
}