# Controller
In a nutshell, in a controller you should do the following:
 1. Validate input data
 2. Pass input data to the Service layer
 3. Pass data from the Controller and/or Service layer to the view component
 4. Handle system or custom exceptions 

This is so-called 'thin controller'. All application logic, including presentation logic, should go to the Service layer.

Following this rule of thumb will help to keep your code clean.

## Examples of controller methods
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

Presentation logic should be kept under the *./app/Services/View folder*. All presentational services are derived from the
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