<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;

class ThrottleTest extends TestCase
{
    /** @test */
    public function test_throttle_key_generation()
    {
        $request = Request::create('/login', 'POST', [
            'email' => 'test@example.com'
        ]);

        $key = $request->ip() . ':' . $request->input('email');

        $this->assertEquals(
            '127.0.0.1:test@example.com',
            $key
        );
    }

    /** @test */
    public function test_rate_limiter_increments()
    {
        $rateLimiter = app(RateLimiter::class);
        $key = '127.0.0.1:test@example.com';

        $rateLimiter->clear($key);

        // Hit the rate limiter
        $rateLimiter->hit($key);
        $rateLimiter->hit($key);

        $this->assertEquals(2, $rateLimiter->attempts($key));
    }
}
