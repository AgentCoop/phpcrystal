<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\View\Frontend as ViewService;


use App\Services\Support\Filesystem\Scanner;

use PhpCrystal\Core\Services\Package\Manager as PackageManager;

use Tests\TestCase;
use Tests\Fixture as Fixture;

use App\Services\Support\Module\Manifest as ModuleManifest;

use App\Exceptions\Loggable;
use App\Models\Physical\Support\Logging\MongoDB\ErrorEntry;
use App\Models\Physical\Repository\User;

class IndexController extends AbstractController
{
    /**
     *
     */
    public function index(Request $request)
    {
        try {
            $data = [];
            $data = array_merge(
                ViewService\Index::create()->getData(),
                $data
            );

            $manager = new PackageManager();

            $manager->build();

            return $this->i18View('frontend.pages.welcome', $data);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
