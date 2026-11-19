<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Laporan Admin Console: hanya admin yang dapat mengunduh CSV laporan
 * sistem dan laporan penyimpanan; pengguna biasa dialihkan ke
 * dashboard-nya dan tamu ke halaman login.
 */
class AdminReportTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'quota_bytes' => 0,
        ]);
    }

    public function test_admin_can_download_system_report_csv(): void
    {
        $response = $this->actingAs($this->makeAdmin())->get('/admin/laporan/sistem');

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('LAPORAN SISTEM RUANGCLOUD', $response->streamedContent());
    }

    public function test_admin_can_download_storage_report_csv(): void
    {
        $response = $this->actingAs($this->makeAdmin())->get('/admin/laporan/penyimpanan');

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('LAPORAN PENYIMPANAN RUANGCLOUD', $response->streamedContent());
    }

    public function test_admin_can_download_audit_report_csv(): void
    {
        $response = $this->actingAs($this->makeAdmin())->get('/admin/laporan/audit');

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('LAPORAN AUDIT LOG RUANGCLOUD', $response->streamedContent());
    }

    public function test_regular_user_cannot_download_reports(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'is_active' => true,
            'quota_bytes' => 5 * 1024 * 1024 * 1024,
        ]);

        $this->actingAs($user)->get('/admin/laporan/sistem')->assertRedirect('/dashboard');
        $this->actingAs($user)->get('/admin/laporan/penyimpanan')->assertRedirect('/dashboard');
    }

    public function test_guest_is_redirected_to_login_from_reports(): void
    {
        $this->get('/admin/laporan/sistem')->assertRedirect('/login');
        $this->get('/admin/laporan/penyimpanan')->assertRedirect('/login');
    }
}
