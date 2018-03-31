# Controller
In a nutshell, in a controller you should do the following:
 1. Validate input data
 2. Pass input data to the Service layer
 3. Pass data from the Controller and/or Service layer to the view component
 4. Handle system or custom exceptions 

This is so-called 'thin controller'. All application logic, including presentation logic, should go to the Service layer.

Following this rule of thumb will help to keep your code clean.

Presentation logic should be kept under the *<module_dir>/Services/View/*   directory. All presentational services are derived from the
abstract class **App\Component\Mvc\AbstractView**

## Summary
 * Location: *<module_dir>/Http/Controllers/*
 * Base classes:
   1. **App\Component\Mvc\Controller\AbstractView**
   2. **App\Component\Mvc\Controller\AbstractApi**
 * Annotations:
    1. @Route("/profile/edit/{userId}", methods={"post"}, name="edit_user_profile", requirements={"userId": "\d+"}))

## Examples
```php
namespace App\Frontoffice\Http\Controllers;
 
use Illuminate\Http\Request;
 
use App\Component\Mvc\Controller\AbstractView as Controller;
 
use App\Frontoffice\Services\View as ViewService;
 
class Index extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexPage(Request $request)
    {

        try {
            $data = [];
 
            $data = array_merge(
                ViewService\Index::create()->getData(),
                $data
            );
 
            return $this->i18View('frontend.pages.welcome', $data);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}

```

```php
namespace App\Api\Http\Controllers;
 
use Illuminate\Http\Request;
 
use App\Component\Mvc\Controller\AbstractApi as Controller;
  
class OrderController extends Controller
{
    /**
     * @Route("/webhooks/order/creation", name="order_creation_webhook")
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
}
```
