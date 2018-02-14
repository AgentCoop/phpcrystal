# Model
Basically, the model component is divided into the two parts: logical and physical. The application classes for those parts are located in **./app/Models/Logical/Repository/** and **./app/Models/Physical/Repository/** correspondingly.

Every logical model has a related physical one, so, to ease navigation, the filesystem hierarchy of these two directories should be the same.

A logical model is being implemented by a PHP trait. It's nothing else than a set of settors and gettors. The corresponding physical model defines relations with other models, database to use, and how to retrieve records from the underlying database using Laravel Eloquent ORM.

Let's take a look at a real example below.

Logical model:
```php
namespace App\Models\Logical\Repository;

trait User
{
    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $this->sanitize($email);

        return $this;
    }
    
    /**
     * @return $this
     */
    public function addPreferences($preferences)
    {
        $this->preferences()->save($preferences);
        
        return $this;
    }
}
```

Physical model:
```php
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
     * @return \Jenssegers\Mongodb\Relations\HasMany
    */
    protected function preferences()
    {
        return $this->hasMany(Preferences::class);
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
```

Now, to create a new user:

```php
use App\Models\Physical\Repository\User;

$user = new User();
$user
    ->setEmail($data['email'])
    ->save();

```
