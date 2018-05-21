<?php

namespace App\Models\Physical\Repository;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;

use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

use App\Models\Physical\DAL\AbstractPostgres;
use Illuminate\Notifications\Notifiable;

use App\Models\Logical\Repository\User as UserLogical;

class User extends AbstractPostgres implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    const TABLE_NAME = 'user';

    const ROLE_ADMIN = 'admin';

    use Authenticatable, Authorizable, CanResetPassword;
    use Notifiable;
    use UserLogical;

    protected $table = self::TABLE_NAME;

    /**
     * @return $this
    */
    public static function getByEmail($email)
    {
        return static::query()
            ->where('email', $email)
            ->firstOrFail();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The roles that belong to the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    protected function roles($joinTable = UserRole::TABLE_NAME)
    {
        return $this->belongsToMany(Role::class, $joinTable);
    }
}
