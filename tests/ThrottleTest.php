<?php

namespace Tests\Unit;


use App\User;
use B2Broker\Jobs\Throttle;
use B2Broker\Models\ThrottleQueue;
use Tests\TestCase;

class ThrottleTest extends TestCase
{
    /**
     * Test throttle job exception
     *
     * @return void
     */
    public function testException()
    {
        ThrottleQueue::truncate();
        $user1 = User::find(1);
        $user2 = User::find(1);
        Throttle::dispatch($user1);
        $this->expectException(\Exception::class);
        Throttle::dispatch($user2);
    }
}
