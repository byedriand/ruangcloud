<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->role === 'admin' ? 'admin' : 'dashboard');
        }
        return view('auth.login');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:191', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'name.required'      => 'Nama lengkap wajib diisi.',
            'name.max'           => 'Nama terlalu panjang (maks. 100 karakter).',
            'email.required'     => 'Alamat email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email ini sudah terdaftar. Silakan masuk atau gunakan email lain.',
            'password.required'  => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min'       => 'Kata sandi minimal 8 karakter.',
        ]);

        $user = User::create([
            'name'         => trim($data['name']),
            'email'        => strtolower(trim($data['email'])),
            'password'     => Hash::make($data['password']),
            'role'         => 'user',
            'is_active'    => true,
            'quota_bytes'  => 5 * 1024 ** 3, // 5 GB
        ]);

        ActivityLog::record('login', Str::before($user->name, ' ') . ' mendaftar akun baru', [], $user->id);

        // Setelah daftar TIDAK langsung masuk: arahkan ke halaman login
        // membawa pesan sukses (popup beranimasi tampil di sana).
        return redirect()->route('login')->with('success', 'Akun berhasil dibuat! Silakan masuk dengan akun barumu untuk mulai menggunakan RuangCloud.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Akun nonaktif tidak bisa masuk.
        if (! Auth::attempt($credentials + ['is_active' => true], $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email atau kata sandi salah, atau akun tidak aktif.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();
        ActivityLog::record('login', Str::before($user->name, ' ') . ' masuk ke sistem');

        return redirect()->route($user->role === 'admin' ? 'admin' : 'dashboard')
            ->with('success', 'Login berhasil! Selamat datang kembali, ' . Str::before($user->name, ' ') . '.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }

    /* ─── Reset kata sandi (REQ-F-05) ─────────────────────────────── */

    public function showForgotPassword()
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->role === 'admin' ? 'admin' : 'dashboard');
        }
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
        ]);

        // Broker Laravel: token acak dibuat, HASH-nya disimpan di tabel
        // password_reset_tokens, dan email berisi tautan dikirim lewat
        // notifikasi pengguna. Status: passwords.sent / passwords.throttled.
        $status = PasswordBroker::sendResetLink([
            'email' => strtolower(trim($data['email'])),
        ]);

        // Tanggapan selalu sama agar tidak membocorkan email mana yang
        // terdaftar; tautan hanya benar-benar terkirim bila email ada.
        if ($status === 'passwords.throttled') {
            return back()->with('status', 'Permintaan baru saja dibuat. Tunggu sekitar satu menit sebelum meminta tautan lagi.');
        }

        return back()->with('status', 'Jika email tersebut terdaftar, tautan untuk mengatur ulang kata sandi telah dikirim. Periksa kotak masuk email kamu — untuk demo lokal, email tertangkap di Mailpit (http://localhost:8025). Tautan berlaku 60 menit.');
    }

    public function showResetPassword(Request $request, string $token)
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->role === 'admin' ? 'admin' : 'dashboard');
        }
        return view('auth.reset-password', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'token.required'     => 'Token reset tidak ditemukan. Gunakan tautan dari email.',
            'email.required'     => 'Alamat email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'password.required'  => 'Kata sandi baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min'       => 'Kata sandi minimal 8 karakter.',
        ]);

        // Broker memverifikasi token (cocok + belum kedaluwarsa 60 menit),
        // menjalankan callback, lalu menghapus token (sekali pakai).
        // Status sukses: passwords.reset.
        $status = PasswordBroker::reset(
            [
                'email'                 => strtolower(trim($data['email'])),
                'password'              => $data['password'],
                'password_confirmation' => $request->input('password_confirmation'),
                'token'                 => $data['token'],
            ],
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
                $user->tokens()->delete(); // cabut token API lama demi keamanan
                ActivityLog::record('login', Str::before($user->name, ' ') . ' mengatur ulang kata sandinya', [], $user->id);
            }
        );

        if ($status === 'passwords.reset') {
            return redirect()->route('login')
                ->with('success', 'Kata sandi berhasil diatur ulang! Silakan masuk dengan kata sandi barumu.');
        }

        return back()
            ->withErrors(['email' => 'Tautan reset tidak valid atau sudah kedaluwarsa. Silakan minta tautan baru.'])
            ->onlyInput('email');
    }
}
