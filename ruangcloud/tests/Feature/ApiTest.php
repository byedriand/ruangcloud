<?php

namespace Tests\Feature;

use App\Models\Folder;
use App\Models\StoredFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Uji REST API: login token, hak akses per pengguna, CRUD folder dan file, sesi unggah. */
class ApiTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(array $attrs = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'user',
            'is_active' => true,
            'quota_bytes' => 5 * 1024 * 1024 * 1024,
        ], $attrs));
    }

    private function tokenFor(User $user): string
    {
        return $user->createToken('test')->plainTextToken;
    }

    private function makeFile(User $user, array $attrs = []): StoredFile
    {
        return StoredFile::create(array_merge([
            'user_id' => $user->id,
            'folder_id' => null,
            'name' => 'laporan.pdf',
            'mime' => 'application/pdf',
            'size' => 123,
            'checksum_sha256' => str_repeat('a', 64),
            'stored_path' => $user->id . '/2026/11/berkas-uji',
            'status' => 'ready',
        ], $attrs));
    }

    public function test_login_returns_token_and_rejects_wrong_password(): void
    {
        $user = $this->makeUser();

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertOk()->assertJsonStructure(['token', 'user' => ['id', 'email']]);

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'bukan-password',
        ])->assertUnauthorized();
    }

    public function test_api_endpoints_require_token(): void
    {
        $this->getJson('/api/files')->assertUnauthorized();
        $this->getJson('/api/folders')->assertUnauthorized();
        $this->getJson('/api/me')->assertUnauthorized();
    }

    public function test_logout_revokes_token(): void
    {
        $user = $this->makeUser();
        $token = $this->tokenFor($user);

        $this->withToken($token)->getJson('/api/me')->assertOk();
        $this->withToken($token)->postJson('/api/logout')->assertOk();
        $this->withToken($token)->getJson('/api/me')->assertUnauthorized();
    }

    public function test_folder_crud_and_cycle_guard(): void
    {
        $user = $this->makeUser();
        $token = $this->tokenFor($user);

        $parent = $this->withToken($token)->postJson('/api/folders', ['name' => 'Dokumen'])
            ->assertOk()->assertJsonPath('name', 'Dokumen')->json('id');

        $child = $this->withToken($token)->postJson('/api/folders', ['name' => 'Kuliah', 'parent_id' => $parent])
            ->assertOk()->json('id');

        $this->withToken($token)->getJson('/api/folders')
            ->assertOk()->assertJsonCount(1, 'data');

        $this->withToken($token)->patchJson('/api/folders/' . $child, ['name' => 'Kuliah 2026'])
            ->assertOk()->assertJsonPath('name', 'Kuliah 2026');

        // Folder induk tidak boleh dipindah ke dalam anaknya sendiri.
        $this->withToken($token)->postJson('/api/folders/' . $parent . '/move', ['parent_id' => $child])
            ->assertStatus(422);

        // Folder berisi subfolder tidak bisa dihapus.
        $this->withToken($token)->deleteJson('/api/folders/' . $parent)->assertStatus(422);

        $this->withToken($token)->deleteJson('/api/folders/' . $child)->assertOk();
        $this->withToken($token)->deleteJson('/api/folders/' . $parent)->assertOk();
    }

    public function test_file_rename_move_delete_and_owner_scope(): void
    {
        $user = $this->makeUser();
        $other = $this->makeUser();
        $token = $this->tokenFor($user);

        $file = $this->makeFile($user);
        $foreign = $this->makeFile($other, ['name' => 'milik-orang.pdf']);
        $folder = Folder::create(['user_id' => $user->id, 'parent_id' => null, 'name' => 'Arsip']);

        // File orang lain tidak muncul di daftar dan tidak bisa dibuka.
        $this->withToken($token)->getJson('/api/files')
            ->assertOk()->assertJsonCount(1, 'data');
        $this->withToken($token)->getJson('/api/files/' . $foreign->id)->assertForbidden();

        // Ubah nama: ekstensi asli dipertahankan otomatis.
        $this->withToken($token)->patchJson('/api/files/' . $file->id, ['name' => 'baru'])
            ->assertOk()->assertJsonPath('name', 'baru.pdf');

        // Pindah ke folder.
        $this->withToken($token)->postJson('/api/files/' . $file->id . '/move', ['folder_id' => $folder->id])
            ->assertOk();
        $this->assertSame($folder->id, $file->fresh()->folder_id);

        // Hapus = masuk sampah (soft delete).
        $this->withToken($token)->deleteJson('/api/files/' . $file->id)->assertOk();
        $this->assertSoftDeleted('files', ['id' => $file->id]);
    }

    public function test_upload_session_can_be_opened_via_api(): void
    {
        $user = $this->makeUser();
        $token = $this->tokenFor($user);

        $sessionId = $this->withToken($token)->postJson('/api/uploads/init', [
            'name' => 'catatan.txt',
            'size' => 12,
            'mime' => 'text/plain',
            'fingerprint' => str_repeat('b', 64),
        ])->assertOk()
            ->assertJsonStructure(['session_id', 'chunk_size', 'total_chunks', 'received', 'resumed'])
            ->json('session_id');

        $this->withToken($token)->getJson('/api/uploads/' . $sessionId)
            ->assertOk()->assertJsonPath('status', 'uploading');
    }
}
