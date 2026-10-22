@extends('layouts.app')

@section('title', $file->name . ' — RuangCloud')

@section('content')
<div class="flex min-h-screen items-center justify-center bg-[#f5faf8] p-5">
  <div class="w-full max-w-[420px] rounded-[24px] border border-[#e1e8e5] bg-white p-7 text-center shadow-[0_20px_60px_rgba(27,53,45,.08)]">
    <div class="mb-6 flex justify-center"><x-brand /></div>
    @if ($locked)
      <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#e8faf5] text-[#0aa982]"><x-icon name="lock" class="h-7 w-7" /></div>
      <h1 class="text-lg font-extrabold">File ini dilindungi kata sandi</h1>
      <p class="mt-1 text-xs text-[#7c8883]">Masukkan kata sandi dari pemilik file untuk melanjutkan.</p>
      <form method="POST" action="{{ route('share.unlock', $token) }}" class="mt-5 space-y-3 text-left">
        @csrf
        <input type="password" name="password" autofocus placeholder="Kata sandi" class="h-12 w-full rounded-xl border border-[#dbe5e1] px-4 text-sm outline-none focus:border-[#0cb48c]">
        @error('password')<p class="text-[11px] font-bold text-red-500">{{ $message }}</p>@enderror
        <button type="submit" class="h-12 w-full rounded-xl bg-[#0cb48c] text-sm font-extrabold text-white hover:bg-[#09a37e]">Buka</button>
      </form>
    @else
      <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#e8faf5] text-[#0aa982]"><x-icon name="file" class="h-7 w-7" /></div>
      <h1 class="break-words text-lg font-extrabold">{{ $file->name }}</h1>
      <p class="mt-1 text-xs text-[#7c8883]">{{ $size }}</p>
      <a href="{{ route('share.download', $token) }}" class="mt-6 flex h-12 items-center justify-center gap-2 rounded-xl bg-[#0cb48c] text-sm font-extrabold text-white hover:bg-[#09a37e]"><x-icon name="download" class="h-4 w-4" /> Unduh file</a>
    @endif
    <p class="mt-6 text-[10px] text-[#929c98]">Dibagikan lewat RuangCloud</p>
  </div>
</div>
@endsection
