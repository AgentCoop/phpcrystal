# Annotated routing
A module controller supports the annotated routing mechanism that works out of the box. Edit your controller, and all Laravel routes will be auto-generated and dumped into the corresponding include file in the ./routes directory.

## Example of a controller with @Route annotation
```php
namespace App\Frontoffice\Http\Controllers;
 
use Illuminate\Http\Request;
use App\Component\Mvc\AbstractController;
use App\Frontoffice\Services\View as ViewService;
 
class Index extends AbstractController
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