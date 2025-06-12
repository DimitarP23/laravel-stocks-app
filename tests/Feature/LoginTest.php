<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'email' => 'logintest@example.com',  // Changed email
            'password' => bcrypt('Password123!')
        ]);

        $response = $this->post('/login', [
            'email' => 'logintest@example.com',  // Changed email
            'password' => 'Password123!'
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/');
    }
}