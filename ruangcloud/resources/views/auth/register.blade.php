@extends('layouts.app')

@section('title', 'Daftar Akun — RuangCloud')

@section('content')
<div class="relative flex min-h-screen overflow-hidden bg-[#f5faf8]">

  {{-- ── Kiri: Ilustrasi ─────────────────────────────────────────── --}}
  <div class="relative hidden w-[46%] flex-col justify-between overflow-hidden bg-[#12372e] p-10 text-white lg:flex xl:p-14">
    <x-brand :light="true" />

    <div class="relative z-10 my-auto max-w-lg">
      <div class="mb-6 flex h-14 w-14 items-center justify-center rounded-2xl bg-white/10 text-[#54dbb9]">
        <x-icon name="users" class="h-8 w-8" />
      </div>
      <h1 class="text-4xl font-extrabold leading-tight tracking-[-0.045em] xl:text-5xl">
        Bergabung dan<br>mulai unggah.
      </h1>
      <p class="mt-5 max-w-md text-sm leading-7 text-[#a9c7bf]">
        Buat akun RuangCloud gratis dan nikmati penyimpanan terdistribusi yang aman, cepat, dan dapat diandalkan kapan saja.
      </p>
      <div class="mt-9 space-y-3">
        @foreach ([
          ['icon' => 'archive',  'title' => '100 GB Gratis',        'desc'  => 'Kuota penyimpanan bawaan untuk setiap akun baru'],
          ['icon' => 'cloud',    'title' => 'Upload Chunked',        'desc'  => 'File besar diunggah secara aman dengan resumable upload'],
          ['icon' => 'share',    'title' => 'Berbagi Mudah',         'desc'  => 'Bagikan file dengan tautan publik yang bisa diproteksi'],
        ] as $f)
          <div class="flex items-start gap-3.5">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white/10 text-[#54dbb9]">
              <x-icon :name="$f['icon']" class="h-4 w-4" />
            </div>
            <div>
              <p class="text-[11px] font-extrabold">{{ $f['title'] }}</p>
              <p class="mt-0.5 text-[9px] leading-4 text-[#8fbfb3]">{{ $f['desc'] }}</p>
            </div>
          </div>
        @endforeach
      </div>
    </div>

    <p class="relative z-10 text-[10px] text-[#789c92]">© 2026 RuangCloud</p>

    {{-- Dekor --}}
    <div class="absolute -bottom-40 -left-28 h-[460px] w-[460px] rounded-full border-[70px] border-white/[.025]"></div>
    <div class="absolute -right-32 top-12 h-[360px] w-[360px] rounded-full border-[55px] border-[#0cb48c]/10"></div>
  </div>

  {{-- ── Kanan: Form Registrasi ──────────────────────────────────── --}}
  <div class="flex flex-1 flex-col px-5 py-6 sm:px-10 lg:px-16">
    {{-- Top bar --}}
    <div class="flex items-center justify-between">
      <div class="lg:hidden"><x-brand /></div>
      <a href="{{ route('login') }}"
         class="ml-auto flex items-center gap-1.5 text-[11px] font-bold text-[#71807b] hover:text-[#0aa982] transition-colors">
        <x-icon name="chevron" class="h-3.5 w-3.5 rotate-180" /> Sudah punya akun? Masuk
      </a>
    </div>

    <div class="mx-auto flex w-full max-w-[430px] flex-1 flex-col justify-center py-8">
      <p class="text-[10px] font-extrabold uppercase tracking-[0.14em] text-[#0aa982]">Buat Akun Baru</p>
      <h2 class="mt-2 text-3xl font-extrabold tracking-[-0.04em]">Daftar ke RuangCloud</h2>
      <p class="mt-2 text-xs leading-5 text-[#7d8984]">Isi formulir di bawah untuk membuat akun pengguna baru.</p>

      {{-- Form --}}
      <form id="register-form" method="POST" action="{{ route('register.store') }}" class="mt-7 space-y-4" novalidate>
        @csrf

        {{-- Nama --}}
        <label class="block">
          <span class="mb-2 block text-[11px] font-bold text-[#40504a]">Nama lengkap</span>
          <div class="relative">
            <x-icon name="users" class="absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#adb9b4]" />
            <input id="name" type="text" name="name"
                   value="{{ old('name') }}"
                   placeholder="Contoh: Naufal Ardana"
                   autocomplete="name"
                   class="h-12 w-full rounded-xl border pl-11 pr-4 text-sm outline-none transition
                          {{ $errors->has('name') ? 'border-red-400 bg-red-50/50 focus:ring-red-400/20' : 'border-[#dbe5e1] bg-white focus:border-[#0cb48c] focus:ring-3 focus:ring-[#0cb48c]/10' }}">
          </div>
          @error('name')
            <span class="mt-1.5 flex items-center gap-1 text-[10px] text-red-500">
              <svg class="h-3 w-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
              {{ $message }}
            </span>
          @enderror
        </label>

        {{-- Email --}}
        <label class="block">
          <span class="mb-2 block text-[11px] font-bold text-[#40504a]">Alamat email</span>
          <div class="relative">
            <x-icon name="search" class="absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#adb9b4]" />
            <input id="email" type="email" name="email"
                   value="{{ old('email') }}"
                   placeholder="nama@email.com"
                   autocomplete="email"
                   class="h-12 w-full rounded-xl border pl-11 pr-4 text-sm outline-none transition
                          {{ $errors->has('email') ? 'border-red-400 bg-red-50/50 focus:ring-red-400/20' : 'border-[#dbe5e1] bg-white focus:border-[#0cb48c] focus:ring-3 focus:ring-[#0cb48c]/10' }}">
          </div>
          @error('email')
            <span class="mt-1.5 flex items-center gap-1 text-[10px] text-red-500">
              <svg class="h-3 w-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
              {{ $message }}
            </span>
          @enderror
        </label>

        {{-- Password --}}
        <label class="block">
          <span class="mb-2 block text-[11px] font-bold text-[#40504a]">Kata sandi</span>
          <div class="relative">
            <x-icon name="lock" class="absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#adb9b4]" />
            <input id="password" type="password" name="password"
                   placeholder="Minimal 8 karakter"
                   autocomplete="new-password"
                   class="h-12 w-full rounded-xl border pl-11 pr-14 text-sm outline-none transition
                          {{ $errors->has('password') ? 'border-red-400 bg-red-50/50 focus:ring-red-400/20' : 'border-[#dbe5e1] bg-white focus:border-[#0cb48c] focus:ring-3 focus:ring-[#0cb48c]/10' }}"
                   oninput="checkStrength(this.value)">
            <button type="button" id="toggle-pw" data-action="toggle-password"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-[9px] font-extrabold text-[#0aa982]">Lihat</button>
          </div>
          {{-- Password strength bar --}}
          <div class="mt-2 flex gap-1" id="strength-bars">
            <div class="h-1 flex-1 rounded-full bg-[#e5ebe8] transition-all" id="sb1"></div>
            <div class="h-1 flex-1 rounded-full bg-[#e5ebe8] transition-all" id="sb2"></div>
            <div class="h-1 flex-1 rounded-full bg-[#e5ebe8] transition-all" id="sb3"></div>
            <div class="h-1 flex-1 rounded-full bg-[#e5ebe8] transition-all" id="sb4"></div>
          </div>
          <p class="mt-1 text-[9px] text-[#9aa39f]" id="strength-label">Masukkan kata sandi untuk melihat kekuatannya</p>
          @error('password')
            <span class="mt-1.5 flex items-center gap-1 text-[10px] text-red-500">
              <svg class="h-3 w-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
              {{ $message }}
            </span>
          @enderror
        </label>

        {{-- Konfirmasi Password --}}
        <label class="block">
          <span class="mb-2 block text-[11px] font-bold text-[#40504a]">Konfirmasi kata sandi</span>
          <div class="relative">
            <x-icon name="lock" class="absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#adb9b4]" />
            <input id="password_confirmation" type="password" name="password_confirmation"
                   placeholder="Ulangi kata sandi"
                   autocomplete="new-password"
                   class="h-12 w-full rounded-xl border pl-11 pr-4 text-sm outline-none transition
                          border-[#dbe5e1] bg-white focus:border-[#0cb48c] focus:ring-3 focus:ring-[#0cb48c]/10"
                   oninput="checkMatch()">
          </div>
          <p class="mt-1 text-[9px] hidden" id="match-label"></p>
        </label>

        {{-- Syarat & Ketentuan --}}
        <label class="flex cursor-pointer items-start gap-2.5 rounded-xl border border-[#e3ebe7] bg-[#f7faf8] p-3.5 transition hover:border-[#0cb48c]/30">
          <input type="checkbox" name="terms" id="terms"
                 class="mt-0.5 h-4 w-4 shrink-0 rounded accent-[#0cb48c]" required>
          <span class="text-[10px] leading-5 text-[#526159]">
            Saya menyetujui
            <button type="button" class="font-bold text-[#0aa982] hover:underline">Syarat &amp; Ketentuan</button>
            serta
            <button type="button" class="font-bold text-[#0aa982] hover:underline">Kebijakan Privasi</button>
            RuangCloud.
          </span>
        </label>

        {{-- Submit --}}
        <button type="submit" id="submit-btn"
                class="group relative flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-[#0cb48c] text-sm font-extrabold text-white shadow-[0_12px_28px_rgba(12,180,140,.2)] transition hover:bg-[#09a37e] active:scale-[.98]">
          <span id="btn-text">Buat Akun</span>
          <x-icon name="chevron" class="h-4 w-4 transition-transform group-hover:translate-x-1" />
          <span id="btn-spinner" class="hidden absolute inset-0 flex items-center justify-center">
            <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
          </span>
        </button>

      </form>

      <p class="mt-6 text-center text-[10px] text-[#929c98]">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-bold text-[#0aa982] hover:underline">Masuk sekarang</a>
      </p>
    </div>
  </div>
</div>

<script>
// ── Toggle password visibility ────────────────────────────────────
document.querySelectorAll('[data-action="toggle-password"]').forEach(btn => {
  btn.addEventListener('click', () => {
    const pw = document.getElementById('password');
    const isHidden = pw.type === 'password';
    pw.type = isHidden ? 'text' : 'password';
    btn.textContent = isHidden ? 'Sembunyikan' : 'Lihat';
  });
});

// ── Password strength ─────────────────────────────────────────────
function checkStrength(pw) {
  const bars  = [sb1, sb2, sb3, sb4];
  const label = document.getElementById('strength-label');
  let score = 0;
  if (pw.length >= 8)  score++;
  if (/[A-Z]/.test(pw)) score++;
  if (/[0-9]/.test(pw)) score++;
  if (/[^A-Za-z0-9]/.test(pw)) score++;

  const colors = ['bg-red-400', 'bg-orange-400', 'bg-yellow-400', 'bg-[#0cb48c]'];
  const labels = ['Sangat lemah', 'Lemah', 'Cukup', 'Kuat'];

  bars.forEach((b, i) => {
    b.className = 'h-1 flex-1 rounded-full transition-all ' +
      (i < score ? colors[score - 1] : 'bg-[#e5ebe8]');
  });

  if (pw.length === 0) {
    label.textContent = 'Masukkan kata sandi untuk melihat kekuatannya';
    label.className = 'mt-1 text-[9px] text-[#9aa39f]';
  } else {
    label.textContent = labels[score - 1] || 'Sangat lemah';
    label.className = 'mt-1 text-[9px] font-bold ' +
      (['text-red-500','text-orange-500','text-yellow-600','text-[#0aa982]'][score - 1] || 'text-red-500');
  }

  checkMatch();
}

// ── Password match ────────────────────────────────────────────────
function checkMatch() {
  const pw1 = document.getElementById('password').value;
  const pw2 = document.getElementById('password_confirmation').value;
  const label = document.getElementById('match-label');
  if (pw2.length === 0) { label.classList.add('hidden'); return; }
  label.classList.remove('hidden');
  if (pw1 === pw2) {
    label.textContent = '✓ Kata sandi cocok';
    label.className = 'mt-1 text-[9px] font-bold text-[#0aa982]';
  } else {
    label.textContent = '✗ Kata sandi tidak cocok';
    label.className = 'mt-1 text-[9px] font-bold text-red-500';
  }
}

// ── Submit spinner ────────────────────────────────────────────────
document.getElementById('register-form').addEventListener('submit', function(e) {
  const terms = document.getElementById('terms');
  if (!terms.checked) {
    e.preventDefault();
    terms.closest('label').classList.add('border-red-400', 'bg-red-50/50');
    setTimeout(() => terms.closest('label').classList.remove('border-red-400', 'bg-red-50/50'), 2000);
    return;
  }
  document.getElementById('btn-text').classList.add('invisible');
  document.querySelector('.group svg.h-4').classList.add('invisible');
  document.getElementById('btn-spinner').classList.remove('hidden');
  document.getElementById('submit-btn').disabled = true;
});
</script>
@endsection
