## Summary
 * Location: *<module_dir>/Services/* 
 * Base class: **App\Component\Mvc\Controller\AbstractService**
 * Annotations: @Service("simple|singleton", tag="string", lazyInit=boolen)

## Configuration
To specify a configuration for a service, declare corresponding section in the module manifest file as follows:
```php
use App\Services as Service;

$this->service(Service\Mailer::class);

    $this->set(Service\Mailer::ERR_REPORT_TEMPLATE_VARNAME, 'email.support.error_report');

$this->close();
```
Use *AbstractService::getConfig()* to retrieve specified config variables.

## Examples
```php
<?php

namespace App\Frontoffice\Services;

use App\Component\Mvc\Controller\AbstractService;

/**
 * @Service("singleton", tag="mytag", lazyInit=true)
*/
class AppService extends AbstractService
{
    private $appService1;
    
    /**
     *
    */
    public function init() : void
    {
        $this->appService1 = resolve(\App\Frontoffice\Services\AppService1::class);
        
        // Do some service initialization over here
        $config = $this->getConfig();
        $var1 = $config->get('var1');
    }
}
```