<?php

namespace Tests\Unit;

use App\Component\Base\Filesystem\Finder;

use App\Services\PackageManager;

use Tests\TestCase;
use Tests\Fixture as Fixture;

use App\Component\Package\Module\Manifest as ModuleManifest;

use App\Component\Exception\Loggable;
use App\Models\Physical\Support\Logging\MongoDB\ErrorEntry;
use App\Models\Physical\Repository\User;

use App\TestModule\Services as Services;

class PhpCrystalTest extends TestCase
{
    const TEST_EMAIL = 'test@mail.com';
    const TEST_EMAIL_2 = 'test2@mail.com';

    const MANIFEST_FILENAME = 'tests/Fixture/testmod/manifest.php';

    public function testServiceSingleton()
    {
        $s1 = resolve(Services\Singleton::class);
        $s2 = resolve(Services\Singleton::class);

        $this->assertTrue(spl_object_id($s1) == spl_object_id($s2));
    }

    public function testServiceSimple()
    {
        $s1 = resolve(Services\Simple::class);
        $s2 = resolve(Services\Simple::class);

        $this->assertTrue(spl_object_id($s1) != spl_object_id($s2));
    }

    /**
     * @return void
     */
    public function testLoggableException()
    {
        try {
            Loggable::create("Something went wrong", 1)
                ->_throw();
        } catch (Loggable $e) {
            $lastEntry = ErrorEntry::getLast(1)->first();
            $this->assertEquals(1, $lastEntry->getCode());
            $lastEntry->delete();
        }
    }

    /**
     * @return void
     */
    public function testUserCreation()
    {
        try {
            $user = new User();
            $user
                ->setEmail(self::TEST_EMAIL)
                ->save();
            $user->refresh();

            $user1 = User::getByEmail(self::TEST_EMAIL);
            $this->assertEquals(self::TEST_EMAIL, $user1->getEmail());

            $user->delete();
        } catch (\Exception $e) {
            $this->assertTrue(false);
        }
    }

    /**
     * @return void
     */
    public function testDataPagination()
    {
        $user = new User();
        $user
            ->setEmail(self::TEST_EMAIL)
            ->save();
        $user->refresh();

        sleep(1);

        $user1 = new User();
        $user1
            ->setEmail(self::TEST_EMAIL_2)
            ->save();
        $user1->refresh();

        $pagedData = User::getPaged(1, 10, function($query) {
            User::orderByCreatedAt($query, User::ORDER_DIR_DESC);
        });

        $this->assertEquals(2, $pagedData['items']->count());
        $this->assertEquals(2, $pagedData['items.count']);
        $this->assertEquals(1, $pagedData['pages.current']);
        $this->assertEquals(1, $pagedData['pages.count']);
        $this->assertEquals(self::TEST_EMAIL_2, $pagedData['items'][0]->getEmail());

        $user->delete();
        $user1->delete();
    }

    /**
     * @return void
    */
    public function testBladeTemplateCompiling()
    {
        $compiled = Fixture\TestView::create()->testCompileBlateTemplate('Hello, {{ $var }}!', ['var' => 'World']);

        $this->assertEquals('Hello, World!', $compiled);
    }

    /**
     * @return void
    */
    public function testContainer()
    {
        $manifest = ModuleManifest::createFromFile(base_path(self::MANIFEST_FILENAME));

        $this->assertEquals('/', $manifest->get('router.prefix'));
    }
}
