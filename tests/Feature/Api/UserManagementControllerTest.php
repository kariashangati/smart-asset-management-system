<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_can_create_user_with_credentials()
    {
        $response = $this->actingAs($this->admin, 'api')
            ->postJson('/api/users/create-with-credentials', [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'role' => 'user',
            ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    }

    public function test_cannot_create_duplicate_user()
    {
        User::factory()->create(['email' => 'john@example.com']);

        $response = $this->actingAs($this->admin, 'api')
            ->postJson('/api/users/create-with-credentials', [
                'name' => 'John Doe',
                'email' => 'john@example.com',
            ]);

        $response->assertStatus(422);
    }

    public function test_can_get_bulk_import_template()
    {
        $response = $this->actingAs($this->admin, 'api')
            ->getJson('/api/users/bulk-import-template');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.headers', ['name', 'email', 'department', 'role', 'email_notifications']);
    }

    public function test_can_regenerate_user_password()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin, 'api')
            ->postJson("/api/users/{$user->id}/reset-password");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_unauthenticated_user_cannot_create_users()
    {
        $response = $this->postJson('/api/users/create-with-credentials', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $response->assertStatus(401);
    }
}
