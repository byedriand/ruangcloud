@extends('layouts.app')

@section('title', 'Tautan tidak tersedia — RuangCloud')

@section('content')
<div class="flex min-h-screen items-center justify-center bg-[#f5faf8] p-5">
  <div class="w-full max-w-[420px] rounded-[24px] border border-[#e1e8e5] bg-white p-7 text-center">
    <div class="mb-6 flex justify-center"><x-brand /></div>
    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#fff1f0] text-[#d94f4f]"><x-icon name="link" class="h-7 w-7" /></div>
    <h1 class="text-lg font-extrabold">Tautan tidak tersedia</h1>
    <p class="mt-2 text-xs leading-5 text-[#7c8883]">Tautan ini sudah dicabut, kedaluwarsa, atau filenya telah dihapus. Minta pemilik file untuk membagikan tautan baru.</p>
  </div>
</div>
@endsection
