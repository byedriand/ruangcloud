<x-user-layout title="Profil Saya" active="profile">
  <div class="mx-auto w-full max-w-4xl">
    <div class="mb-7">
      <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-[11px] font-extrabold uppercase tracking-wider text-emerald-700">
        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
        Ruang kerja saya
      </span>
      <h1 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900">Profil Saya</h1>
      <p class="mt-1.5 text-sm text-slate-500">Atur foto, nama pengguna, email, dan kata sandi akunmu.</p>
    </div>

    @include('profile.partials.forms')
  </div>
</x-user-layout>
