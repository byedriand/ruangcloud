@extends('layouts.app')

@section('title', 'Masuk — RuangCloud')

@section('content')
<div class="relative flex min-h-screen overflow-hidden bg-[#f5faf8]">
  <div class="relative hidden w-[46%] flex-col justify-between overflow-hidden bg-[#12372e] p-10 text-white lg:flex xl:p-14">
    <x-brand :light="true" />
    <div class="relative z-10 my-auto max-w-lg">
      <div class="mb-6 flex h-14 w-14 items-center justify-center rounded-2xl bg-white/10 text-[#54dbb9]"><x-icon name="cloud" class="h-8 w-8" /></div>
      <h1 class="text-4xl font-extrabold leading-tight tracking-[-0.045em] xl:text-5xl">Semua file penting,<br>dalam satu ruang.</h1>
      <p class="mt-5 max-w-md text-sm leading-7 text-[#a9c7bf]">Masuk untuk mengelola file, memantau penyimpanan, dan melanjutkan pekerjaanmu dari perangkat mana saja.</p>
      <div class="mt-9 flex gap-6 text-[10px] font-bold text-[#b8d1ca]">
        <span class="flex items-center gap-2"><span class="landing-check">✓</span> Aman</span>
        <span class="flex items-center gap-2"><span class="landing-check">✓</span> Terdistribusi</span>
        <span class="flex items-center gap-2"><span class="landing-check">✓</span> Andal</span>
      </div>
    </div>
    <p class="relative z-10 text-[10px] text-[#789c92]">© 2026 RuangCloud</p>
    <div class="absolute -bottom-40 -left-28 h-[460px] w-[460px] rounded-full border-[70px] border-white/[.025]"></div>
    <div class="absolute -right-32 top-12 h-[360px] w-[360px] rounded-full border-[55px] border-[#0cb48c]/10"></div>
  </div>

  <div class="flex flex-1 flex-col px-5 py-6 sm:px-10 lg:px-16">
    <div class="flex items-center justify-between lg:justify-end">
      <div class="lg:hidden"><x-brand /></div>
      <div class="ml-auto flex items-center gap-4">
        <a href="{{ route('register') }}" class="text-[11px] font-bold text-[#0aa982] hover:underline">Daftar akun baru</a>
        <a href="{{ route('landing') }}" class="flex items-center gap-1.5 text-[11px] font-bold text-[#71807b] hover:text-[#0aa982]"><x-icon name="chevron" class="h-3.5 w-3.5 rotate-180" /> Kembali</a>
      </div>
    </div>
    <div class="mx-auto flex w-full max-w-[430px] flex-1 flex-col justify-center py-10">
      <p class="text-[10px] font-extrabold uppercase tracking-[0.14em] text-[#0aa982]">Selamat datang kembali</p>
      <h2 class="mt-2 text-3xl font-extrabold tracking-[-0.04em]">Masuk ke RuangCloud</h2>
      <p class="mt-2 text-xs leading-5 text-[#7d8984]">Pilih peran untuk melihat demo dashboard yang sesuai.</p>

      <div class="mt-7 grid grid-cols-2 gap-2 rounded-2xl bg-[#eaf1ee] p-1.5">
        <button type="button" data-action="set-role" data-role="user" class="flex items-center justify-center gap-2 rounded-xl bg-white px-3 py-3 text-xs font-extrabold text-[#098568] shadow-sm transition"><x-icon name="users" class="h-4 w-4" /> Pengguna</button>
        <button type="button" data-action="set-role" data-role="admin" class="flex items-center justify-center gap-2 rounded-xl px-3 py-3 text-xs font-extrabold text-[#75817d] transition"><x-icon name="activity" class="h-4 w-4" /> Administrator</button>
      </div>

      <form id="login-form" method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-4">
        @csrf
        <input type="hidden" id="role-input" name="role" value="{{ old('role', 'user') }}">

        <label class="block">
          <span class="mb-2 block text-[11px] font-bold text-[#40504a]">Alamat email</span>
          <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" class="h-12 w-full rounded-xl border border-[#dbe5e1] bg-white px-4 text-sm outline-none transition focus:border-[#0cb48c] focus:ring-3 focus:ring-[#0cb48c]/10">
          @error('email')<span class="mt-1 block text-[10px] text-red-500">{{ $message }}</span>@enderror
        </label>

        <label class="block">
          <span class="mb-2 block text-[11px] font-bold text-[#40504a]">Kata sandi</span>
          <div class="relative">
            <input id="password" type="password" name="password" placeholder="Kata sandi Anda" class="h-12 w-full rounded-xl border border-[#dbe5e1] bg-white px-4 pr-16 text-sm outline-none transition focus:border-[#0cb48c] focus:ring-3 focus:ring-[#0cb48c]/10">
            <button type="button" data-action="toggle-password" class="absolute right-3 top-1/2 -translate-y-1/2 text-[9px] font-extrabold text-[#0aa982]">Lihat</button>
          </div>
          @error('password')<span class="mt-1 block text-[10px] text-red-500">{{ $message }}</span>@enderror
        </label>

        <div class="flex items-center justify-between text-[10px]">
          <label class="flex items-center gap-2 font-semibold text-[#687670]"><input type="checkbox" name="remember" checked class="accent-[#0cb48c]"> Ingat saya</label>
          <button type="button" class="font-bold text-[#0aa982]">Lupa kata sandi?</button>
        </div>

        <button type="submit" class="flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-[#0cb48c] text-sm font-extrabold text-white shadow-[0_12px_28px_rgba(12,180,140,.2)] transition hover:bg-[#09a37e]">Masuk sebagai <span id="submit-label">Pengguna</span> <x-icon name="chevron" class="h-4 w-4" /></button>
      </form>
      <p class="mt-5 text-center text-[10px] text-[#929c98]">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-bold text-[#0aa982] hover:underline">Daftar sekarang</a>
      </p>
    </div>
  </div>
</div>
@endsection
