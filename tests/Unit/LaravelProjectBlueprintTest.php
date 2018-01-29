<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Exceptions\Loggable;
use App\Models\Physical\Support\Logging\MongoDB\ErrorEntry;

class LaravelProjectBlueprintTest extends TestCase
{
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
}
