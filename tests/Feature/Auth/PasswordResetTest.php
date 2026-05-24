<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_send_password_reset_link()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/password-reset/request', [
            'email' => $user->email,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // Verify token was created
        $this->assertDatabaseHas('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_cannot_request_reset_for_nonexistent_user()
    {
        $response = $this->postJson('/api/password-reset/request', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertStatus(404);
    }

    public function test_can_verify_valid_token()
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->getJson("/api/password-reset/verify/{$token}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_cannot_verify_invalid_token()
    {
        $response = $this->getJson('/api/password-reset/verify/invalid-token');

        $response->assertStatus(401);
    }

    public function test_can_reset_password_with_valid_token()
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->postJson('/api/password-reset/confirm', [
            'email' => $user->email,
            'token' => $token,
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // Verify token was deleted
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_password_reset_fails_with_mismatched_passwords()
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->postJson('/api/password-reset/confirm', [
            'email' => $user->email,
            'token' => $token,
            'password' => 'NewPassword123',
            'password_confirmation' => 'DifferentPassword123',
        ]);

        $response->assertStatus(422);
    }
}
