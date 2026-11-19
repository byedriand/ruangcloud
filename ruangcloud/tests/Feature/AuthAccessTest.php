<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Uji asap akses dasar: halaman publik terbuka untuk tamu,
 * halaman pengguna terkunci untuk tamu, dan terbuka setelah login.
 */
class AuthAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_landing_page(): void
    {
        $this->withoutVite();

        $this->get('/')->assertOk();
    }

    public function test_guest_can_view_login_page(): void
    {
        $this->withoutVite();

        $this->get('/login')->assertOk();
    }

    public function test_guest_is_redirected_to_login_from_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_guest_is_redirected_to_login_from_files(): void
    {
        $this->get('/files')->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_dashboard(): void
    {
        $this->withoutVite();

        $user = User::factory()->create([
            'role' => 'user',
            'is_active' => true,
            'quota_bytes' => 5 * 1024 * 1024 * 1024,
        ]);

        $this->actingAs($user)->get('/dashboard')->assertOk();
    }
}
