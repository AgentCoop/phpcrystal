
## About LPB
LPB is a skeleton application based on Laravel PHP framework.

## How to install
```bash
composer create-project agentcoop/laravel-project-blueprint
```

## Running the app
```bash
docker-compose up --build
```
Go to https://localhost:60001 and, if everthing is good, you'll see the Larvel splash page. The SSL certificate is a
self-signed one, so don't be confused by a browser warning.

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
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
```
This is so-called 'thin controller', all application logic, including presentation logic, should go to the Service layer.
Controller is just responsible for passing data to the Presentation layer and handling exceptions.

In a nutshell, in controller you should do:
 1. Validate input data
 2. Pass input data to the Service layer
 3. Pass data from the Service layer to the Presentation layer

Following this rule of thumb will keep your code clean.

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

## License
Licensed under the [MIT license](https://opensource.org/licenses/MIT).
