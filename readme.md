
### About LPB
LPB is a skeleton application based on Laravel PHP framework.

### How to install
```bash
composer create-project agentcoop/laravel-project-blueprint
```

### How to run
```bash
docker-compose up --build
```
Go to https://localhost:60001 and, if everything is good, you'll see the Laravel splash page. The SSL certificate is a
self-signed one, so don't be confused by a browser warning.

## License
Licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Documenation

## Controller
In a nutshell, in a controller you should do the following:
 1. Validate input data
 2. Pass input data to the Service layer
 3. Pass data from the Service layer to the Presentation layer
 4. Handle system or custom exceptions 

This is so-called 'thin controller'. All application logic, including presentation logic, should go to the Service layer.

Following this rule of thumb will help to keep your code clean.

### Examples of controller methods
```php
    /**
     * @return Response
     */
    public function orderCreationWebhook(Request $request)
    {
        try {
            $order = OrderManager::updateOrCreate($request->all());

            $orderManager = new OrderManager($order);
            $orderManager->process();

            return response('', 200)
                ->header('Content-Type', 'text/plain');
        } catch (\RuntimeException $e) { // Handle custom exception
            // ...
        } catch (\Exception $e) { // Handle system exception
            return $this->handleException($e);
        }
    }
```

Presentation logic should be kept under the app\Services\View folder. All presentational services are derived from the
abstract class *AbstractView*

```php
  /**
   * @return Response
  */
  public function settingsAction(Request $request)
  {
    try {
      $user = Auth::user();

      $view = [];
      $view['profile_menu_flag'] = true;

      $view = array_merge($view,
          ProfileView\Common::create($user)->getData(),
          ViewService\Profile\Settings::create($user)->getData()
      );

      return $this->i10View('frontend.pages.profile.settings.index', $view);
    } catch (\Exception $e) {
      return $this->handleException($e);
    }
  }
```
## Model
Basically, the model component is divided into the two parts: logical and physical. The application PHP classes for those parts are located in **./app/Models/Logical/Repository/** and **./app/Models/Physical/Repository/** correspondingly. Every logical model has a related physical one, so, to ease navigation, the filesystem hierarchy of those two directories should be the same.

A logical model is being implemented by a PHP trait. It's nothing else than a set of settors and gettors. The corresponding physical model defines relations, database to use, and how to retrieve records from the underlying database using Laravel Eloquent ORM.

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
That's it.