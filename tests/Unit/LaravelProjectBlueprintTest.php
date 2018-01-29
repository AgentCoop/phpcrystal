<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Exceptions\Loggable;
use App\Models\Physical\Support\Logging\MongoDB\ErrorEntry;
use App\Models\Physical\Repository\User;

class LaravelProjectBlueprintTest extends TestCase
{
    const TEST_EMAIL = 'test@mail.com';

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

            $user = User::getByEmail(self::TEST_EMAIL);
            $this->assertEquals(self::TEST_EMAIL, $user->getEmail());

        } catch (\Exception $e) {
            $this->assertTrue(false);
        }
    }
}
