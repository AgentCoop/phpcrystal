<?php

namespace App\Models\Physical\Repository;

use App\Models\Physical\DAL\AbstractPostgres;

use App\Models\Logical\Repository\Role as RoleLogical;

class Role extends AbstractPostgres
{
    const TABLE_NAME = 'role';

    const COL_ID = 'id';
    const COL_NAME = 'name';

    const ROLE_OWNER = 'owner';
    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'manager';

    const ROLE_NAMES = [self::ROLE_OWNER, self::ROLE_ADMIN, self::ROLE_MANAGER];

    use RoleLogical;

    protected $table = self::TABLE_NAME;

    protected function users()
    {
        return $this->belongsToMany(User::class, UserRole::TABLE_NAME)
            ->using(UserRole::class);
    }
}

