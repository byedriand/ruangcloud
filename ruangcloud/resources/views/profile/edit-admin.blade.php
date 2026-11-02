@extends('layouts.app')

@section('title', 'Profil Admin — RuangCloud')

@section('content')
<div class="min-h-screen bg-[#f8faf9] text-slate-900">
  <div class="mx-auto w-full max-w-4xl px-5 py-8 md:py-10">
    <a href="{{ route('admin') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 transition hover:text-emerald-700">
      <x-icon name="chevron" class="h-4 w-4 rotate-180" />
      Kembali ke Admin Console
    </a>

    <div class="mb-7 mt-5">
      <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-[11px] font-extrabold uppercase tracking-wider text-emerald-700">
        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
        Admin Console
      </span>
      <h1 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900">Profil Admin</h1>
      <p class="mt-1.5 text-sm text-slate-500">Atur foto, nama pengguna, email, dan kata sandi akun administratormu.</p>
    </div>

    @include('profile.partials.forms')
  </div>
</div>
@endsection
