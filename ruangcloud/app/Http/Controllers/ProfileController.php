<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/** Pengaturan profil akun sendiri — dipakai bersama oleh pengguna & admin. */
class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();

        return view($user->role === 'admin' ? 'profile.edit-admin' : 'profile.edit-user', compact('user'));
    }

    /** Nama pengguna & email. */
    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ], [
            'name.required'  => 'Nama pengguna wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email sudah dipakai akun lain.',
        ]);

        $oldName  = $user->name;
        $oldEmail = $user->email;

        $user->update($data);

        $actor = Str::before($user->name, ' ');
        if ($data['name'] !== $oldName) {
            ActivityLog::record('profile', $actor . ' mengubah nama pengguna dari "' . $oldName . '" menjadi "' . $data['name'] . '"');
        }
        if ($data['email'] !== $oldEmail) {
            ActivityLog::record('profile', $actor . ' mengubah email dari "' . $oldEmail . '" menjadi "' . $data['email'] . '"');
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /** Foto profil (disk public, folder avatars). */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ], [
            'avatar.required' => 'Pilih foto terlebih dahulu.',
            'avatar.image'    => 'File harus berupa gambar.',
            'avatar.mimes'    => 'Format foto harus JPG, PNG, atau WebP.',
            'avatar.max'      => 'Ukuran foto maksimum 10 MB.',
        ]);

        $user = Auth::user();
        $path = $request->file('avatar')->store('avatars', 'public');

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->update(['avatar' => $path]);

        ActivityLog::record('avatar', Str::before($user->name, ' ') . ' memperbarui foto profil');

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }

    public function destroyAvatar()
    {
        $user = Auth::user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);

            ActivityLog::record('avatar', Str::before($user->name, ' ') . ' menghapus foto profil');
        }

        return back()->with('success', 'Foto profil dihapus.');
    }

    /** Ganti kata sandi (wajib tahu sandi lama). */
    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required'       => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini salah.',
            'password.required'               => 'Kata sandi baru wajib diisi.',
            'password.confirmed'              => 'Konfirmasi kata sandi baru tidak cocok.',
            'password.min'                    => 'Kata sandi baru minimal 8 karakter.',
        ]);

        $user = Auth::user();
        $user->update(['password' => Hash::make($data['password'])]);

        ActivityLog::record('password', Str::before($user->name, ' ') . ' mengubah kata sandi');

        return back()->with('success', 'Kata sandi berhasil diubah.');
    }
}
