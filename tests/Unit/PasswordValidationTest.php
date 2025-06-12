<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class PasswordValidationTest extends TestCase
{
    /** @test */
    public function test_password_meets_requirements()
    {
        $validator = Validator::make([
            'password' => 'WeakPass'
        ], [
            'password' => ['required', Password::min(10)->mixedCase()->numbers()->symbols()]
        ]);

        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function test_strong_password_passes_validation()
    {
        $validator = Validator::make([
            'password' => 'StrongP@ssw0rd123!'
        ], [
            'password' => ['required', Password::min(10)->mixedCase()->numbers()->symbols()]
        ]);

        $this->assertFalse($validator->fails());
    }
}
