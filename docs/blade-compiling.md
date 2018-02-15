# Compiling Blade templates on the fly

## Overview
Sometimes it might come in hady to compile Blade templates on the fly. For instance, you can use that feature in an admin dashboard to allow parts of your site to be changed without deploying any code.

The method for doing that is *AbstractView::renderBladeMarkup($tpl, $data)*

## Example

```php
<?php

namespace Tests\Fixture;

use App\Services\View\AbstractView;

class TestView extends AbstractView
{
    public function getData()
    {
        return [];
    }

    /**
     * @return string
    */
    public function testCompileBlateTemplate($tpl, $data)
    {
        return $this->renderBladeMarkup($tpl, $data);
    }
}
```

```php
    /**
     * @return void
    */
    public function testBladeTemplateCompiling()
    {
        $compiled = Fixture\TestView::create()->testCompileBlateTemplate('Hello, {{ $var }}!', ['var' => 'World']);

        $this->assertEquals('Hello, World!', $compiled);
    }
```