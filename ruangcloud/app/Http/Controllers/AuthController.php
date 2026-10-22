<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
            'quota_bytes'  => 100 * 1024 ** 3, // 100 GB
        ]);

        ActivityLog::record('login', Str::before($user->name, ' ') . ' mendaftar dan masuk ke sistem', [], $user->id);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Akun berhasil dibuat! Selamat datang di RuangCloud.');
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

        return redirect()->route($user->role === 'admin' ? 'admin' : 'dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }
}
