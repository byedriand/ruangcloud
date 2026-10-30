@extends('layouts.app')

@section('title', 'Masuk — RuangCloud')

@section('content')
<div class="auth-page relative flex min-h-screen flex-col items-center justify-center overflow-hidden px-4 py-8 sm:px-6">
  <style>
    .auth-page {
      background:
        radial-gradient(720px 480px at 8% -6%, rgba(52, 211, 153, .24), transparent 62%),
        radial-gradient(840px 540px at 98% 4%, rgba(45, 212, 191, .22), transparent 60%),
        radial-gradient(780px 640px at 55% 112%, rgba(16, 185, 129, .16), transparent 62%),
        linear-gradient(180deg, #f2faf7 0%, #e9f7f1 52%, #f4faf8 100%);
    }
    .glass-soft {
      background: rgba(255, 255, 255, .55);
      -webkit-backdrop-filter: blur(18px) saturate(1.45);
      backdrop-filter: blur(18px) saturate(1.45);
      border: 1px solid rgba(255, 255, 255, .75);
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, .85), 0 35px 80px rgba(18, 60, 47, .16);
    }
    .grad-text {
      background: linear-gradient(92deg, #059669 0%, #0cb48c 45%, #14b8a6 80%, #06b6d4 115%);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
    }
    @keyframes orbFloatA {
      0%, 100% { transform: translate(0, 0) scale(1); }
      50% { transform: translate(52px, -34px) scale(1.14); }
    }
    @keyframes orbFloatB {
      0%, 100% { transform: translate(0, 0) scale(1.06); }
      50% { transform: translate(-58px, 30px) scale(0.94); }
    }
    .glass-orb-a { animation: orbFloatA 11s ease-in-out infinite; }
    .glass-orb-b { animation: orbFloatB 14s ease-in-out infinite; }
  </style>
  <div class="glass-orb-a pointer-events-none absolute -left-24 top-1/3 h-72 w-72 rounded-full bg-emerald-300/25 blur-3xl"></div>
  <div class="glass-orb-b pointer-events-none absolute -right-20 top-10 h-80 w-80 rounded-full bg-teal-300/25 blur-3xl"></div>
  <div class="glass-orb-b pointer-events-none absolute -bottom-32 left-[38%] h-80 w-80 rounded-full bg-emerald-200/25 blur-3xl" style="animation-delay: -6s"></div>

  <div class="relative z-10 flex w-full max-w-[1060px] items-center justify-between pb-4">
    <div class="lg:hidden"><x-brand /></div>
    <div class="ml-auto flex items-center gap-4">
      <a href="{{ route('register') }}" class="text-xs font-bold text-[#078d6d] hover:underline">Daftar akun baru</a>
      <a href="{{ route('landing') }}" class="flex items-center gap-1.5 rounded-full border border-white/70 bg-white/55 px-3.5 py-2 text-xs font-semibold text-[#5b6d67] shadow-[inset_0_1px_0_rgba(255,255,255,.8)] backdrop-blur-md transition hover:bg-white/85 hover:text-[#078d6d]"><x-icon name="chevron" class="h-3.5 w-3.5 rotate-180" /> Kembali</a>
    </div>
  </div>

  <div class="glass-soft relative z-10 grid w-full max-w-[1060px] overflow-hidden rounded-[28px] lg:grid-cols-[1fr_1.05fr]">
    {{-- Panel kiri: gradasi gelap + status node kaca --}}
    <div class="relative hidden flex-col justify-between overflow-hidden bg-gradient-to-br from-[#0c2b23] via-[#124534] to-[#083f30] p-10 text-white lg:flex xl:p-12">
      <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-emerald-400/15 blur-3xl"></div>
      <div class="pointer-events-none absolute -bottom-32 -left-20 h-80 w-80 rounded-full bg-teal-400/10 blur-3xl"></div>
      <div class="absolute -bottom-40 -right-28 h-[420px] w-[420px] rounded-full border-[64px] border-white/[.03]"></div>

      <div class="relative z-10 flex items-center justify-between">
        <x-brand :light="true" />
        <span class="flex items-center gap-2 rounded-full border border-white/10 bg-white/[0.06] px-3 py-1.5 text-[11px] font-bold text-[#a7e8d5] backdrop-blur-md"><span class="h-1.5 w-1.5 rounded-full bg-[#33d4ad] ring-4 ring-[#33d4ad]/15"></span> Sistem normal</span>
      </div>

      <div class="relative z-10 my-auto py-10">
        <div class="mb-6 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-600 text-white shadow-lg shadow-emerald-950/40 ring-1 ring-white/25"><x-icon name="cloud" class="h-8 w-8" /></div>
        <h1 class="text-4xl font-extrabold leading-[1.12] tracking-[-0.04em] xl:text-[44px]">Semua file penting,<br>dalam satu <span class="text-[#3ed3ae]">ruang.</span></h1>
        <p class="mt-5 max-w-md text-sm leading-7 text-[#a9c7bf]">Masuk untuk mengelola file, memantau penyimpanan, dan melanjutkan pekerjaanmu dari perangkat mana saja.</p>
        <div class="mt-8 flex flex-wrap gap-2.5 text-xs font-semibold text-[#cdeee3]">
          <span class="flex items-center gap-2 rounded-full border border-white/10 bg-white/[0.05] px-3.5 py-2 backdrop-blur-md"><span class="landing-check">✓</span> Aman</span>
          <span class="flex items-center gap-2 rounded-full border border-white/10 bg-white/[0.05] px-3.5 py-2 backdrop-blur-md"><span class="landing-check">✓</span> Terdistribusi</span>
          <span class="flex items-center gap-2 rounded-full border border-white/10 bg-white/[0.05] px-3.5 py-2 backdrop-blur-md"><span class="landing-check">✓</span> Andal</span>
        </div>

        <div class="mt-8 space-y-2.5">
          @foreach (['Server 1', 'Server 2', 'Server 3'] as $node)
            <div class="flex items-center gap-3.5 rounded-2xl border border-white/10 bg-white/[0.05] px-4 py-3 backdrop-blur-md">
              <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white/[0.07] text-[#54dbb9] ring-1 ring-white/10"><x-icon name="archive" class="h-4.5 w-4.5" /></span>
              <div class="min-w-0 flex-1">
                <p class="text-[13px] font-extrabold leading-tight">{{ $node }}</p>
                <p class="text-[11px] text-[#8fb8ab]">Node penyimpanan • 2 replika</p>
              </div>
              <span class="flex items-center gap-2 text-[11px] font-bold text-[#54dbb9]"><span class="h-2 w-2 rounded-full bg-[#33d4ad] ring-4 ring-[#33d4ad]/15"></span> Aktif</span>
            </div>
          @endforeach
        </div>
      </div>

      <p class="relative z-10 text-xs text-[#789c92]">© 2026 RuangCloud</p>
    </div>

    {{-- Panel kanan: formulir di atas kaca --}}
    <div class="relative flex flex-col justify-center px-6 py-10 sm:px-10 xl:px-14">
      <p class="text-xs font-bold uppercase tracking-wider text-[#078d6d]">Selamat datang kembali</p>
      <h2 class="mt-2 text-[32px] font-extrabold leading-tight tracking-[-0.04em] text-[#16241f]">Masuk ke <span class="grad-text">RuangCloud</span></h2>
      <p class="mt-2 text-sm leading-5 text-[#64748b]">Pilih peran untuk melihat demo dashboard yang sesuai.</p>

      <div class="mt-7 grid grid-cols-2 gap-2 rounded-2xl border border-white/70 bg-white/45 p-1.5 shadow-[inset_0_1px_0_rgba(255,255,255,.8)] backdrop-blur-md">
        <button type="button" data-action="set-role" data-role="user" class="flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-400 px-3 py-3 text-sm font-bold text-white shadow-lg shadow-emerald-500/30 transition"><x-icon name="users" class="h-4 w-4" /> Pengguna</button>
        <button type="button" data-action="set-role" data-role="admin" class="flex items-center justify-center gap-2 rounded-xl px-3 py-3 text-sm font-bold text-slate-500 transition"><x-icon name="activity" class="h-4 w-4" /> Administrator</button>
      </div>

      <form id="login-form" method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-4">
        @csrf
        <input type="hidden" id="role-input" name="role" value="{{ old('role', 'user') }}">

        <label class="block">
          <span class="mb-2 block text-xs font-bold text-[#334155]">Alamat email</span>
          <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" class="h-12 w-full rounded-xl border border-white/80 bg-white/60 px-4 text-sm shadow-[inset_0_1px_0_rgba(255,255,255,.9)] outline-none backdrop-blur-md transition placeholder:text-slate-400 focus:border-emerald-400 focus:bg-white/85 focus:ring-4 focus:ring-emerald-500/10">
          @error('email')<span class="mt-1.5 block text-xs font-medium text-red-500">{{ $message }}</span>@enderror
        </label>

        <label class="block">
          <span class="mb-2 block text-xs font-bold text-[#334155]">Kata sandi</span>
          <div class="relative">
            <input id="password" type="password" name="password" placeholder="Kata sandi Anda" class="h-12 w-full rounded-xl border border-white/80 bg-white/60 px-4 pr-16 text-sm shadow-[inset_0_1px_0_rgba(255,255,255,.9)] outline-none backdrop-blur-md transition placeholder:text-slate-400 focus:border-emerald-400 focus:bg-white/85 focus:ring-4 focus:ring-emerald-500/10">
            <button type="button" data-action="toggle-password" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-[#078d6d]">Lihat</button>
          </div>
          @error('password')<span class="mt-1.5 block text-xs font-medium text-red-500">{{ $message }}</span>@enderror
        </label>

        <div class="flex items-center justify-between text-xs">
          <label class="flex items-center gap-2 font-semibold text-[#5b6d67]"><input type="checkbox" name="remember" checked class="h-4 w-4 rounded accent-emerald-500"> Ingat saya</label>
          <button type="button" class="font-bold text-[#078d6d] hover:underline">Lupa kata sandi?</button>
        </div>

        <button type="submit" class="flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-400 text-sm font-extrabold text-white shadow-[0_16px_35px_rgba(16,185,129,.35)] transition hover:-translate-y-0.5 hover:brightness-110 active:translate-y-0">Masuk sebagai <span id="submit-label">Pengguna</span> <x-icon name="chevron" class="h-4 w-4" /></button>
      </form>
      <p class="mt-6 text-center text-xs text-[#64748b]">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-bold text-[#078d6d] hover:underline">Daftar sekarang</a>
      </p>
    </div>
  </div>
</div>
@endsection
