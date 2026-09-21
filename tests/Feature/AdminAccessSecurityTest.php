<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAccessSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_non_admin_cannot_access_the_admin_panel(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_admin_can_access_the_admin_panel(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk();
    }

    public function test_seeder_uses_only_explicit_non_default_admin_credentials(): void
    {
        config()->set('admin.seed', [
            'name' => 'Production Admin',
            'email' => 'owner@example.com',
            'password' => 'a-secure-16-char-password',
        ]);

        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'owner@example.com')->sole();

        $this->assertTrue($admin->is_admin);
        $this->assertTrue(Hash::check('a-secure-16-char-password', $admin->password));
        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
    }
}
