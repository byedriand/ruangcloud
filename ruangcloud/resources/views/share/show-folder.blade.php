@extends('layouts.app')

@section('title', $folder->name . ' — RuangCloud')

@section('content')
<div class="flex min-h-screen items-center justify-center bg-[#f5faf8] p-5">
  <div class="w-full max-w-[560px] rounded-[24px] border border-[#e1e8e5] bg-white p-8 shadow-[0_20px_60px_rgba(27,53,45,.08)]">
    <div class="mb-6 flex justify-center"><x-brand /></div>
    @if ($locked)
      <div class="text-center">
        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#e8faf5] text-[#0aa982]"><x-icon name="lock" class="h-7 w-7" /></div>
        <h1 class="text-xl font-extrabold text-[#1a2320]">Folder ini dilindungi kata sandi</h1>
        <p class="mt-1 text-sm text-[#64748b]">Masukkan kata sandi dari pemilik folder untuk melanjutkan.</p>
      </div>
      <form method="POST" action="{{ route('share.unlock', $token) }}" class="mt-5 space-y-3 text-left">
        @csrf
        <input type="password" name="password" autofocus placeholder="Kata sandi" class="h-12 w-full rounded-xl border border-[#dbe5e1] px-4 text-sm outline-none focus:border-[#0cb48c] focus:ring-2 focus:ring-[#0cb48c]/15">
        @error('password')<p class="text-xs font-semibold text-red-500">{{ $message }}</p>@enderror
        <button type="submit" class="h-12 w-full rounded-xl bg-[#0cb48c] text-sm font-extrabold text-white transition hover:bg-[#09a37e]">Buka</button>
      </form>
    @else
      <div class="text-center">
        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#e8faf5] text-[#0aa982]"><x-icon name="folder" class="h-7 w-7" /></div>
        <h1 class="break-words text-xl font-extrabold text-[#1a2320]">{{ $folder->name }}</h1>
        <p class="mt-1.5 text-sm font-semibold text-[#64748b]">{{ $count }} file &bull; {{ $totalSize }}</p>
        @if ($owner)
          <div class="mt-4 flex items-center justify-center gap-2">
            @if ($owner->avatarUrl())
              <img src="{{ $owner->avatarUrl() }}" alt="Foto {{ $owner->name }}" class="h-6 w-6 rounded-lg object-cover">
            @else
              <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-400 to-teal-600 text-[10px] font-extrabold text-white">{{ \App\Support\Fmt::initials($owner->name) }}</span>
            @endif
            <span class="text-xs font-semibold text-[#64748b]">Dibagikan oleh {{ $owner->name }}</span>
          </div>
        @endif
      </div>

      @if ($count > 0)
        <a href="{{ route('share.zip', $token) }}" class="mt-6 flex h-12 items-center justify-center gap-2 rounded-xl bg-[#0cb48c] text-sm font-extrabold text-white transition hover:bg-[#09a37e]"><x-icon name="download" class="h-4 w-4" /> Unduh semua (ZIP)</a>
        <div class="mt-5 max-h-[320px] space-y-1 overflow-y-auto pr-1">
          @foreach ($entries as $entry)
            <div class="flex items-center gap-3 rounded-xl px-2 py-2 transition hover:bg-[#f5faf8]">
              <span class="file-icon file-{{ $entry['color'] }} flex h-9 w-9 shrink-0 items-center justify-center rounded-lg"><x-icon :name="$entry['icon']" class="h-4.5 w-4.5" /></span>
              <span class="min-w-0 flex-1">
                <span class="block truncate text-sm font-bold text-[#1a2320]">{{ $entry['rel'] }}</span>
                <span class="block text-xs font-medium text-[#929c98]">{{ $entry['size'] }}</span>
              </span>
              <a href="{{ route('share.file', [$token, $entry['id']]) }}" aria-label="Unduh {{ $entry['name'] }}" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-[#0aa982] transition hover:bg-[#e8faf5]"><x-icon name="download" class="h-4.5 w-4.5" /></a>
            </div>
          @endforeach
        </div>
      @else
        <p class="mt-6 rounded-xl bg-[#f5faf8] px-4 py-5 text-center text-sm font-semibold text-[#64748b]">Folder ini masih kosong.</p>
      @endif
    @endif
    <p class="mt-6 text-center text-xs text-[#929c98]">Dibagikan lewat RuangCloud</p>
  </div>
</div>
@endsection
