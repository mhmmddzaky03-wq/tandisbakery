<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_and_access_dashboard(): void
    {
        $this->seed();

        $response = $this->post(route('auth.login.submit'), [
            'username' => 'admin',
            'password' => 'admin123',
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->get(route('admin.dashboard'))->assertOk();
    }

    public function test_karyawan_cannot_access_admin_routes(): void
    {
        $this->seed();
        $user = User::where('username', 'karyawan')->first();
        $this->actingAs($user);

        $this->get(route('admin.dashboard'))->assertRedirect(route('karyawan.dashboard'));
    }
}
