@extends('layouts.app')

@section('title', 'Daftar Akun — RuangCloud')

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
    <a href="{{ route('login') }}" class="ml-auto flex items-center gap-1.5 rounded-full border border-white/70 bg-white/55 px-3.5 py-2 text-xs font-semibold text-[#5b6d67] shadow-[inset_0_1px_0_rgba(255,255,255,.8)] backdrop-blur-md transition hover:bg-white/85 hover:text-[#078d6d]">
      <x-icon name="chevron" class="h-3.5 w-3.5 rotate-180" /> Sudah punya akun? Masuk
    </a>
  </div>

  <div class="glass-soft relative z-10 grid w-full max-w-[1060px] overflow-hidden rounded-[28px] lg:grid-cols-[1fr_1.05fr]">
    {{-- Panel kiri: gradasi gelap + keunggulan kaca --}}
    <div class="relative hidden flex-col justify-between overflow-hidden bg-gradient-to-br from-[#0c2b23] via-[#124534] to-[#083f30] p-10 text-white lg:flex xl:p-12">
      <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-emerald-400/15 blur-3xl"></div>
      <div class="pointer-events-none absolute -bottom-32 -left-20 h-80 w-80 rounded-full bg-teal-400/10 blur-3xl"></div>
      <div class="absolute -bottom-40 -right-28 h-[420px] w-[420px] rounded-full border-[64px] border-white/[.03]"></div>

      <div class="relative z-10 flex items-center justify-between">
        <x-brand :light="true" />
        <span class="flex items-center gap-2 rounded-full border border-white/10 bg-white/[0.06] px-3 py-1.5 text-[11px] font-bold text-[#a7e8d5] backdrop-blur-md"><span class="h-1.5 w-1.5 rounded-full bg-[#33d4ad] ring-4 ring-[#33d4ad]/15"></span> Sistem normal</span>
      </div>

      <div class="relative z-10 my-auto py-10">
        <div class="mb-6 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-600 text-white shadow-lg shadow-emerald-950/40 ring-1 ring-white/25"><x-icon name="users" class="h-8 w-8" /></div>
        <h1 class="text-4xl font-extrabold leading-[1.12] tracking-[-0.04em] xl:text-[44px]">Bergabung dan<br>mulai <span class="text-[#3ed3ae]">unggah.</span></h1>
        <p class="mt-5 max-w-md text-sm leading-7 text-[#a9c7bf]">Buat akun RuangCloud gratis dan nikmati penyimpanan terdistribusi yang aman, cepat, dan dapat diandalkan kapan saja.</p>

        <div class="mt-8 space-y-2.5">
          @foreach ([
            ['icon' => 'archive', 'grad' => 'from-emerald-400 to-teal-500', 'title' => '5 GB Gratis', 'desc' => 'Kuota penyimpanan bawaan untuk setiap akun baru'],
            ['icon' => 'cloud',   'grad' => 'from-sky-400 to-blue-500',     'title' => 'Upload Chunked', 'desc' => 'File besar diunggah secara aman dengan resumable upload'],
            ['icon' => 'share',   'grad' => 'from-violet-400 to-purple-500', 'title' => 'Berbagi Mudah',  'desc' => 'Bagikan file dengan tautan publik yang bisa diproteksi'],
          ] as $f)
            <div class="flex items-center gap-3.5 rounded-2xl border border-white/10 bg-white/[0.05] px-4 py-3.5 backdrop-blur-md">
              <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br text-white shadow-lg ring-1 ring-white/20 {{ $f['grad'] }}"><x-icon :name="$f['icon']" class="h-4.5 w-4.5" /></span>
              <div class="min-w-0">
                <p class="text-sm font-extrabold leading-tight">{{ $f['title'] }}</p>
                <p class="mt-0.5 text-xs leading-4 text-[#8fbfb3]">{{ $f['desc'] }}</p>
              </div>
            </div>
          @endforeach
        </div>
      </div>

      <p class="relative z-10 text-xs text-[#789c92]">© 2026 RuangCloud</p>
    </div>

    {{-- Panel kanan: formulir di atas kaca --}}
    <div class="relative flex flex-col justify-center px-6 py-10 sm:px-10 xl:px-14">
      <p class="text-xs font-bold uppercase tracking-wider text-[#078d6d]">Buat Akun Baru</p>
      <h2 class="mt-2 text-[32px] font-extrabold leading-tight tracking-[-0.04em] text-[#16241f]">Daftar ke <span class="grad-text">RuangCloud</span></h2>
      <p class="mt-2 text-sm leading-5 text-[#64748b]">Isi formulir di bawah untuk membuat akun pengguna baru.</p>

      <form id="register-form" method="POST" action="{{ route('register.store') }}" class="mt-7 space-y-4" novalidate>
        @csrf

        {{-- Nama --}}
        <label class="block">
          <span class="mb-2 block text-xs font-bold text-[#334155]">Nama lengkap</span>
          <div class="relative">
            <span class="pointer-events-none absolute left-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-700 ring-1 ring-emerald-500/30"><x-icon name="user" class="h-4 w-4" /></span>
            <input id="name" type="text" name="name"
                   value="{{ old('name') }}"
                   placeholder="Contoh: Naufal Ardana"
                   autocomplete="name"
                   class="h-12 w-full rounded-xl border pl-12 pr-4 text-sm shadow-[inset_0_1px_0_rgba(255,255,255,.9)] outline-none backdrop-blur-md transition placeholder:text-slate-400
                          {{ $errors->has('name') ? 'border-red-300 bg-red-50/60 focus:border-red-400 focus:ring-4 focus:ring-red-400/15' : 'border-white/80 bg-white/60 focus:border-emerald-400 focus:bg-white/85 focus:ring-4 focus:ring-emerald-500/10' }}">
          </div>
          @error('name')
            <span class="mt-1.5 flex items-center gap-1 text-xs font-medium text-red-500">
              <svg class="h-3.5 w-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
              {{ $message }}
            </span>
          @enderror
        </label>

        {{-- Email --}}
        <label class="block">
          <span class="mb-2 block text-xs font-bold text-[#334155]">Alamat email</span>
          <div class="relative">
            <span class="pointer-events-none absolute left-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-700 ring-1 ring-emerald-500/30"><x-icon name="mail" class="h-4 w-4" /></span>
            <input id="email" type="email" name="email"
                   value="{{ old('email') }}"
                   placeholder="nama@email.com"
                   autocomplete="email"
                   class="h-12 w-full rounded-xl border pl-12 pr-4 text-sm shadow-[inset_0_1px_0_rgba(255,255,255,.9)] outline-none backdrop-blur-md transition placeholder:text-slate-400
                          {{ $errors->has('email') ? 'border-red-300 bg-red-50/60 focus:border-red-400 focus:ring-4 focus:ring-red-400/15' : 'border-white/80 bg-white/60 focus:border-emerald-400 focus:bg-white/85 focus:ring-4 focus:ring-emerald-500/10' }}">
          </div>
          @error('email')
            <span class="mt-1.5 flex items-center gap-1 text-xs font-medium text-red-500">
              <svg class="h-3.5 w-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
              {{ $message }}
            </span>
          @enderror
        </label>

        {{-- Password --}}
        <label class="block">
          <span class="mb-2 block text-xs font-bold text-[#334155]">Kata sandi</span>
          <div class="relative">
            <span class="pointer-events-none absolute left-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-700 ring-1 ring-emerald-500/30"><x-icon name="lock" class="h-4 w-4" /></span>
            <input id="password" type="password" name="password"
                   placeholder="Minimal 8 karakter"
                   autocomplete="new-password"
                   class="h-12 w-full rounded-xl border pl-12 pr-14 text-sm shadow-[inset_0_1px_0_rgba(255,255,255,.9)] outline-none backdrop-blur-md transition placeholder:text-slate-400
                          {{ $errors->has('password') ? 'border-red-300 bg-red-50/60 focus:border-red-400 focus:ring-4 focus:ring-red-400/15' : 'border-white/80 bg-white/60 focus:border-emerald-400 focus:bg-white/85 focus:ring-4 focus:ring-emerald-500/10' }}"
>
            <button type="button" id="toggle-pw" data-action="toggle-password"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-[#078d6d]">Lihat</button>
          </div>
          {{-- Password strength bar --}}
          <div class="mt-2.5 flex gap-1" id="strength-bars">
            <div class="h-1.5 flex-1 rounded-full bg-slate-200/90 transition-all" id="sb1"></div>
            <div class="h-1.5 flex-1 rounded-full bg-slate-200/90 transition-all" id="sb2"></div>
            <div class="h-1.5 flex-1 rounded-full bg-slate-200/90 transition-all" id="sb3"></div>
            <div class="h-1.5 flex-1 rounded-full bg-slate-200/90 transition-all" id="sb4"></div>
          </div>
          <p class="mt-1.5 text-xs text-[#64748b]" id="strength-label">Masukkan kata sandi untuk melihat kekuatannya</p>
          @error('password')
            <span class="mt-1.5 flex items-center gap-1 text-xs font-medium text-red-500">
              <svg class="h-3.5 w-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
              {{ $message }}
            </span>
          @enderror
        </label>

        {{-- Konfirmasi Password --}}
        <label class="block">
          <span class="mb-2 block text-xs font-bold text-[#334155]">Konfirmasi kata sandi</span>
          <div class="relative">
            <span class="pointer-events-none absolute left-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-700 ring-1 ring-emerald-500/30"><x-icon name="lock" class="h-4 w-4" /></span>
            <input id="password_confirmation" type="password" name="password_confirmation"
                   placeholder="Ulangi kata sandi"
                   autocomplete="new-password"
                   class="h-12 w-full rounded-xl border border-white/80 bg-white/60 pl-12 pr-14 text-sm shadow-[inset_0_1px_0_rgba(255,255,255,.9)] outline-none backdrop-blur-md transition placeholder:text-slate-400 focus:border-emerald-400 focus:bg-white/85 focus:ring-4 focus:ring-emerald-500/10"
>
            <button type="button" data-action="toggle-password"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-[#078d6d]">Lihat</button>
          </div>
          <p class="mt-1.5 hidden text-xs" id="match-label"></p>
        </label>

        {{-- Syarat & Ketentuan --}}
        <label class="flex cursor-pointer items-start gap-2.5 rounded-2xl border border-white/70 bg-white/45 p-3.5 shadow-[inset_0_1px_0_rgba(255,255,255,.8)] backdrop-blur-md transition hover:border-emerald-300/70 hover:bg-white/60">
          <input type="checkbox" name="terms" id="terms"
                 class="mt-0.5 h-4 w-4 shrink-0 rounded accent-emerald-500" required>
          <span class="text-xs leading-5 text-[#526159]">
            Saya menyetujui
            <button type="button" class="font-bold text-[#078d6d] hover:underline">Syarat &amp; Ketentuan</button>
            serta
            <button type="button" class="font-bold text-[#078d6d] hover:underline">Kebijakan Privasi</button>
            RuangCloud.
          </span>
        </label>

        {{-- Submit --}}
        <button type="submit" id="submit-btn"
                class="group relative flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-400 text-sm font-extrabold text-white shadow-[0_16px_35px_rgba(16,185,129,.35)] transition hover:-translate-y-0.5 hover:brightness-110 active:translate-y-0">
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

      <p class="mt-6 text-center text-xs text-[#64748b]">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-bold text-[#078d6d] hover:underline">Masuk sekarang</a>
      </p>
    </div>
  </div>
</div>

<script>
// ── Toggle password visibility ────────────────────────────────────
// Toggle Lihat/Sembunyikan kata sandi ditangani global oleh ruangcloud.js
// lewat data-action="toggle-password" -- jangan diikat lagi di sini, klik
// akan memicu dua kali dan saling meniadakan (tombol terlihat mati).

// ── Password strength ─────────────────────────────────────────────
function checkStrength(pw) {
  const bars  = [sb1, sb2, sb3, sb4];
  const label = document.getElementById('strength-label');
  let score = 0;
  if (pw.length >= 8)  score++;
  if (/[A-Z]/.test(pw)) score++;
  if (/[0-9]/.test(pw)) score++;
  if (/[^A-Za-z0-9]/.test(pw)) score++;

  const colors = ['bg-red-400', 'bg-orange-400', 'bg-amber-400', 'bg-emerald-500'];
  const labels = ['Sangat lemah', 'Lemah', 'Cukup', 'Kuat'];

  bars.forEach((b, i) => {
    b.className = 'h-1.5 flex-1 rounded-full transition-all ' +
      (i < score ? colors[score - 1] : 'bg-slate-200');
  });

  if (pw.length === 0) {
    label.textContent = 'Masukkan kata sandi untuk melihat kekuatannya';
    label.className = 'mt-1.5 text-xs text-[#64748b]';
  } else {
    label.textContent = labels[score - 1] || 'Sangat lemah';
    label.className = 'mt-1.5 text-xs font-bold ' +
      (['text-red-500','text-orange-500','text-amber-600','text-emerald-600'][score - 1] || 'text-red-500');
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
    label.className = 'mt-1.5 text-xs font-bold text-emerald-600';
  } else {
    label.textContent = '✗ Kata sandi tidak cocok';
    label.className = 'mt-1.5 text-xs font-bold text-red-500';
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
