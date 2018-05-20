<?php

namespace App\Models\Physical\Repository;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

use App\Models\Physical\DAL\AbstractMongoDb;
use Illuminate\Notifications\Notifiable;

use App\Models\Logical\Repository\User as UserLogical;

class User extends AbstractMongoDb implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    const ROLE_ADMIN = 'admin';

    use Authenticatable, Authorizable, CanResetPassword;
    use Notifiable;
    use UserLogical;

    protected $collection = 'users';

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
}
