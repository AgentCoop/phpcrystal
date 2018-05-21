<?php

namespace Tests\Unit;

use App\Component\Base\Filesystem\Finder;

use App\Models\Physical\Repository\UserRole;
use App\Services\PackageManager;

use Tests\TestCase;
use Tests\Fixture as Fixture;

use App\Component\Package\Module\Manifest as ModuleManifest;

use App\Component\Exception\Loggable;
use App\Models\Physical\Support\Logging\MongoDB\ErrorEntry;

use App\Models\Physical\Repository\User;
use App\Models\Physical\Repository\Role;

use App\TestModule\Services as Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use Auth;

class PhpCrystalTest extends TestCase
{
    const TEST_EMAIL = 'test@mail.com';
    const TEST_EMAIL_2 = 'test2@mail.com';

    const MANIFEST_FILENAME = 'tests/Fixture/testmod/manifest.php';

    private function withUser(\Closure $cb, $role = Role::ROLE_ADMIN)
    {
        try {
            $success = false;

            $role = Role::create()
                ->setName($role)
            ;

            $role->save();
            $role->refresh();

            $user = new User();
            $user
                ->setEmail(self::TEST_EMAIL)
                ->setPassword(Hash::make('secret'))
            ;

            $user->save();
            $user->refresh();
            $user
                ->addRoles([$role])
                ->save()
            ;

            Auth::login($user);

            $success = $cb($user);
        } catch (\Exception $e) {
            $success = false;
        } finally {
            DB::table(User::TABLE_NAME)->delete();
            DB::table(Role::TABLE_NAME)->delete();
            DB::table(UserRole::TABLE_NAME)->delete();
        }

        return $success;
    }

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

    public function testAdminRole()
    {
        $this->withUser(function() {
            $this->get('/admin')
                ->assertStatus(200);

            return true;
        }, Role::ROLE_ADMIN);
    }

    public function testAdminLogin()
    {
        $this->get('/admin/login')
            ->assertStatus(200);
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
