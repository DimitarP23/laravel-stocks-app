<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_user_can_request_password_reset()
    {
        $user = User::factory()->create([
            'email' => 'resettest@example.com'  // Changed email
        ]);

        $response = $this->post('/forgot-password', [
            'email' => 'resettest@example.com'  // Changed email
        ]);

        $response->assertSessionHas('status');
    }

    /** @test */
    public function test_user_cannot_request_reset_with_invalid_email()
    {
        $response = $this->post('/forgot-password', [
            'email' => 'nonexistent@example.com'
        ]);

        $response->assertSessionHasErrors('email');
    }
}
