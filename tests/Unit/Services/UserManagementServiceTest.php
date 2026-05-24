<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Models\UserCredential;
use App\Services\UserManagementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementServiceTest extends TestCase
{
    use RefreshDatabase;

    protected UserManagementService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(UserManagementService::class);
    }

    public function test_can_create_user_with_credentials()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ];

        $user = $this->service->createUserWithCredentials($userData);

        $this->assertNotNull($user);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertDatabaseHas('user_credentials', ['user_id' => $user->id]);
    }

    public function test_cannot_create_duplicate_user()
    {
        User::factory()->create(['email' => 'john@example.com']);

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $this->service->createUserWithCredentials([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    public function test_can_regenerate_password()
    {
        $user = User::factory()->create();

        $password = $this->service->regeneratePassword($user);

        $this->assertNotNull($password);
        $this->assertDatabaseHas('user_credentials', ['user_id' => $user->id]);
    }

    public function test_can_update_password()
    {
        $user = User::factory()->create();

        $this->service->updatePassword($user, 'NewPassword123');

        $credential = UserCredential::where('user_id', $user->id)->first();
        $this->assertNotNull($credential->password_changed_at);
    }

    public function test_can_generate_csv_template()
    {
        $template = $this->service->generateCsvTemplate();

        $this->assertStringContainsString('name,email,department,role,email_notifications', $template);
        $this->assertStringContainsString('john@example.com', $template);
    }
}
