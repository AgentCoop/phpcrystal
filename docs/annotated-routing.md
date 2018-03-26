# Annotated routing
A module controller supports the annotated routing mechanism that works out of the box. Edit your controller, and all Laravel routes will be auto-generated and dumped into the corresponding include file in the ./routes directory.

## Example of a controller with @Route annotation
```php
namespace App\Frontoffice\Http\Controllers;
 
use Illuminate\Http\Request;
use App\Component\Mvc\Controller\AbstractView as Controller;
use App\Frontoffice\Services\View as ViewService;
 
class UserProfile extends Controller
{
    /**
     * @Route("/profile/edit/{userId}", methods={"post"}, name="edit_user_profile", requirements={"userId": "\d+"}))
     */
    public function editAction(Request $request, $userId)
    {
        try {
            // ...
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
```