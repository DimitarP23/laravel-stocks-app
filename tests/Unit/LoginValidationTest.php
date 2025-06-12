<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Validator;

class LoginValidationTest extends TestCase
{
    /** @test */
    public function test_email_validation()
    {
        $validator = Validator::make([
            'email' => 'not-an-email'
        ], [
            'email' => ['required', 'email']
        ]);

        $this->assertTrue($validator->fails());
        
        $validator = Validator::make([
            'email' => 'valid@email.com'
        ], [
            'email' => ['required', 'email']
        ]);

        $this->assertFalse($validator->fails());
    }
}