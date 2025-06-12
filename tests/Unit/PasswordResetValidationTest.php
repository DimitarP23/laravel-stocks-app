<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class PasswordResetValidationTest extends TestCase
{
    /** @test */
    public function test_new_password_meets_requirements()
    {
        $validator = Validator::make([
            'password' => 'StrongP@ssw0rd123!'
        ], [
            'password' => ['required', Password::min(10)->mixedCase()->numbers()->symbols()]
        ]);

        $this->assertFalse($validator->fails());
    }
}