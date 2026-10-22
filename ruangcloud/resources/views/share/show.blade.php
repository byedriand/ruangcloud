@extends('layouts.app')

@section('title', $file->name . ' — RuangCloud')

@section('content')
<div class="flex min-h-screen items-center justify-center bg-[#f5faf8] p-5">
  <div class="w-full max-w-[440px] rounded-[24px] border border-[#e1e8e5] bg-white p-8 text-center shadow-[0_20px_60px_rgba(27,53,45,.08)]">
    <div class="mb-6 flex justify-center"><x-brand /></div>
    @if ($locked)
      <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#e8faf5] text-[#0aa982]"><x-icon name="lock" class="h-7 w-7" /></div>
      <h1 class="text-xl font-extrabold text-[#1a2320]">File ini dilindungi kata sandi</h1>
      <p class="mt-1 text-sm text-[#64748b]">Masukkan kata sandi dari pemilik file untuk melanjutkan.</p>
      <form method="POST" action="{{ route('share.unlock', $token) }}" class="mt-5 space-y-3 text-left">
        @csrf
        <input type="password" name="password" autofocus placeholder="Kata sandi" class="h-12 w-full rounded-xl border border-[#dbe5e1] px-4 text-sm outline-none focus:border-[#0cb48c] focus:ring-2 focus:ring-[#0cb48c]/15">
        @error('password')<p class="text-xs font-semibold text-red-500">{{ $message }}</p>@enderror
        <button type="submit" class="h-12 w-full rounded-xl bg-[#0cb48c] text-sm font-extrabold text-white transition hover:bg-[#09a37e]">Buka</button>
      </form>
    @else
      <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#e8faf5] text-[#0aa982]"><x-icon name="file" class="h-7 w-7" /></div>
      <h1 class="break-words text-xl font-extrabold text-[#1a2320]">{{ $file->name }}</h1>
      <p class="mt-1.5 text-sm font-semibold text-[#64748b]">{{ $size }}</p>
      <a href="{{ route('share.download', $token) }}" class="mt-6 flex h-12 items-center justify-center gap-2 rounded-xl bg-[#0cb48c] text-sm font-extrabold text-white transition hover:bg-[#09a37e]"><x-icon name="download" class="h-4 w-4" /> Unduh file</a>
    @endif
    <p class="mt-6 text-xs text-[#929c98]">Dibagikan lewat RuangCloud</p>
  </div>
</div>
@endsection
