{{-- Formulir profil dipakai bersama halaman user & admin. Butuh variabel $user. --}}
@if (session('success'))
  <div class="mb-6 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-gradient-to-r from-emerald-50 to-teal-50 px-5 py-4 text-sm font-bold text-emerald-800 shadow-sm">
    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-white">
      <x-icon name="check" class="h-4.5 w-4.5" />
    </span>
    {{ session('success') }}
  </div>
@endif

{{-- ── Hero profil ── --}}
<section class="relative overflow-hidden rounded-[28px] bg-gradient-to-br from-[#0b2921] via-[#0f3d2e] to-teal-800 text-white shadow-xl shadow-emerald-950/20">
  <div class="pointer-events-none absolute -right-16 -top-24 h-64 w-64 rounded-full bg-emerald-400/15 blur-2xl"></div>
  <div class="pointer-events-none absolute -bottom-28 left-1/3 h-56 w-56 rounded-full bg-teal-300/10 blur-2xl"></div>
  <div class="relative flex flex-wrap items-center gap-6 p-7 md:p-9">
    <div class="relative shrink-0">
      @if ($user->avatarUrl())
        <img id="avatar-preview" src="{{ $user->avatarUrl() }}" alt="Foto profil {{ $user->name }}" class="h-24 w-24 rounded-3xl object-cover shadow-2xl ring-4 ring-white/20">
        <div id="avatar-initials" hidden class="flex h-24 w-24 items-center justify-center rounded-3xl bg-gradient-to-br from-emerald-400 to-teal-600 text-3xl font-extrabold text-white shadow-2xl ring-4 ring-white/20">{{ \App\Support\Fmt::initials($user->name) }}</div>
      @else
        <img id="avatar-preview" hidden src="" alt="Foto profil {{ $user->name }}" class="h-24 w-24 rounded-3xl object-cover shadow-2xl ring-4 ring-white/20">
        <div id="avatar-initials" class="flex h-24 w-24 items-center justify-center rounded-3xl bg-gradient-to-br from-emerald-400 to-teal-600 text-3xl font-extrabold text-white shadow-2xl ring-4 ring-white/20">{{ \App\Support\Fmt::initials($user->name) }}</div>
      @endif
      <span class="absolute -bottom-1.5 -right-1.5 flex h-7 w-7 items-center justify-center rounded-full bg-emerald-400 text-[#06231c] ring-4 ring-[#0e3527]">
        <x-icon name="camera" class="h-3.5 w-3.5" />
      </span>
    </div>
    <div class="min-w-0">
      <h2 class="truncate text-2xl font-extrabold tracking-tight md:text-[28px]">{{ $user->name }}</h2>
      <p class="mt-0.5 truncate text-sm text-emerald-100/80">{{ $user->email }}</p>
      <div class="mt-3.5 flex flex-wrap items-center gap-2">
        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1 text-[11px] font-extrabold uppercase tracking-wider text-emerald-100 ring-1 ring-white/15">
          <x-icon name="user" class="h-3 w-3" />
          {{ $user->role === 'admin' ? 'Administrator' : 'Pengguna' }}
        </span>
        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1 text-[11px] font-extrabold uppercase tracking-wider text-emerald-100 ring-1 ring-white/15">
          <span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span>
          Akun aktif
        </span>
        @if ($user->created_at)
          <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1 text-[11px] font-extrabold uppercase tracking-wider text-emerald-100 ring-1 ring-white/15">
            <x-icon name="clock" class="h-3 w-3" />
            Bergabung {{ $user->created_at->translatedFormat('F Y') }}
          </span>
        @endif
      </div>
    </div>
  </div>
</section>

<div class="mt-6 grid items-start gap-6 lg:grid-cols-5">
  {{-- ── Foto profil ── --}}
  <section class="rounded-[28px] glass-card p-6 shadow-lg shadow-slate-900/[0.04] md:p-7 lg:col-span-2">
    <div class="flex items-center gap-3">
      <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
        <x-icon name="camera" class="h-5 w-5" />
      </span>
      <div>
        <h2 class="text-base font-extrabold leading-tight text-slate-900">Foto Profil</h2>
        <p class="text-xs text-slate-500">Tampil di sidebar, header & daftar pengguna.</p>
      </div>
    </div>

    <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data" class="mt-6">
      @csrf
      <label class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-emerald-200 bg-emerald-50/40 px-4 py-7 text-center transition hover:border-emerald-400 hover:bg-emerald-50">
        <span class="flex h-11 w-11 items-center justify-center rounded-full bg-white/85 text-emerald-600 shadow-sm backdrop-blur">
          <x-icon name="upload" class="h-5 w-5" />
        </span>
        <span id="avatar-file-label" class="max-w-full truncate text-sm font-bold text-slate-700">Pilih foto baru…</span>
        <span class="text-xs text-slate-400">JPG, PNG, atau WebP — maksimum 10 MB</span>
        <input id="avatar-input" type="file" name="avatar" accept="image/jpeg,image/png,image/webp" class="hidden">
      </label>
      @error('avatar')
        <p class="mt-2 text-xs font-semibold text-red-500">{{ $message }}</p>
      @enderror
      <div class="mt-4 flex items-center gap-2.5">
        <button type="submit" class="inline-flex h-11 flex-1 items-center justify-center rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-5 text-sm font-bold text-white shadow-lg shadow-emerald-600/25 transition hover:from-emerald-500 hover:to-teal-500 active:scale-[0.98]">Simpan foto</button>
        @if ($user->avatar)
          <button type="submit" form="avatar-delete-form" class="inline-flex h-11 items-center rounded-xl border border-rose-200 px-4 text-sm font-bold text-rose-500 transition hover:bg-rose-50">Hapus</button>
        @endif
      </div>
    </form>
    @if ($user->avatar)
      <form id="avatar-delete-form" method="POST" action="{{ route('profile.avatar.destroy') }}" class="hidden">
        @csrf
        @method('DELETE')
      </form>
    @endif
  </section>

  {{-- ── Informasi akun ── --}}
  <section class="rounded-[28px] glass-card p-6 shadow-lg shadow-slate-900/[0.04] md:p-7 lg:col-span-3">
    <div class="flex items-center gap-3">
      <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
        <x-icon name="user" class="h-5 w-5" />
      </span>
      <div>
        <h2 class="text-base font-extrabold leading-tight text-slate-900">Informasi Akun</h2>
        <p class="text-xs text-slate-500">Nama pengguna dan email untuk masuk & tampil di aplikasi.</p>
      </div>
    </div>

    <form method="POST" action="{{ route('profile.update') }}" class="mt-6 grid gap-5">
      @csrf
      @method('PUT')
      <label class="block">
        <span class="mb-1.5 block text-sm font-bold text-slate-700">Nama pengguna</span>
        <div class="relative">
          <span class="pointer-events-none absolute left-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-700 ring-1 ring-emerald-500/30">
            <x-icon name="user" class="h-4 w-4" />
          </span>
          <input type="text" name="name" value="{{ old('name', $user->name) }}" required maxlength="255"
                 class="h-12 w-full rounded-xl border border-white/70 bg-white/70 pl-12 pr-4 backdrop-blur text-sm text-slate-800 transition focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/10">
        </div>
        @error('name')
          <span class="mt-1.5 block text-xs font-semibold text-red-500">{{ $message }}</span>
        @enderror
      </label>
      <label class="block">
        <span class="mb-1.5 block text-sm font-bold text-slate-700">Email</span>
        <div class="relative">
          <span class="pointer-events-none absolute left-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-700 ring-1 ring-emerald-500/30">
            <x-icon name="mail" class="h-4 w-4" />
          </span>
          <input type="email" name="email" value="{{ old('email', $user->email) }}" required maxlength="255"
                 class="h-12 w-full rounded-xl border border-white/70 bg-white/70 pl-12 pr-4 backdrop-blur text-sm text-slate-800 transition focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/10">
        </div>
        @error('email')
          <span class="mt-1.5 block text-xs font-semibold text-red-500">{{ $message }}</span>
        @enderror
      </label>
      <div>
        <button type="submit" class="inline-flex h-11 items-center rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-6 text-sm font-bold text-white shadow-lg shadow-emerald-600/25 transition hover:from-emerald-500 hover:to-teal-500 active:scale-[0.98]">Simpan perubahan</button>
      </div>
    </form>
  </section>
</div>

{{-- ── Kata sandi ── --}}
<section class="mt-6 rounded-[28px] glass-card p-6 shadow-lg shadow-slate-900/[0.04] md:p-7">
  <div class="flex items-center gap-3">
    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
      <x-icon name="lock" class="h-5 w-5" />
    </span>
    <div>
      <h2 class="text-base font-extrabold leading-tight text-slate-900">Ganti Kata Sandi</h2>
      <p class="text-xs text-slate-500">Demi keamanan, masukkan kata sandi saat ini sebelum menggantinya.</p>
    </div>
  </div>

  <form method="POST" action="{{ route('profile.password') }}" class="mt-6 grid gap-5">
    @csrf
    @method('PUT')
    <label class="block sm:max-w-md">
      <span class="mb-1.5 block text-sm font-bold text-slate-700">Kata sandi saat ini</span>
      <div class="relative">
        <span class="pointer-events-none absolute left-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-700 ring-1 ring-emerald-500/30"><x-icon name="lock" class="h-4 w-4" /></span>
        <input type="password" id="pw-current" name="current_password" required autocomplete="current-password"
               class="h-12 w-full rounded-xl border border-white/70 bg-white/70 pl-12 pr-12 backdrop-blur text-sm text-slate-800 transition focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/10">
        <button type="button" data-toggle-pw="pw-current" aria-label="Tampilkan kata sandi" class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg p-1 text-slate-500 transition hover:text-emerald-600">
          <span class="pw-eye"><x-icon name="eye" class="h-5 w-5" /></span>
          <span class="pw-eye-off" hidden><x-icon name="eye-off" class="h-5 w-5" /></span>
        </button>
      </div>
      @error('current_password')
        <span class="mt-1.5 block text-xs font-semibold text-red-500">{{ $message }}</span>
      @enderror
    </label>

    <div class="grid gap-5 sm:grid-cols-2">
      <div>
        <label class="block">
          <span class="mb-1.5 block text-sm font-bold text-slate-700">Kata sandi baru</span>
          <div class="relative">
            <span class="pointer-events-none absolute left-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-700 ring-1 ring-emerald-500/30"><x-icon name="lock" class="h-4 w-4" /></span>
            <input type="password" id="pw-new" name="password" required autocomplete="new-password"
                   class="h-12 w-full rounded-xl border border-white/70 bg-white/70 pl-12 pr-12 backdrop-blur text-sm text-slate-800 transition focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/10">
            <button type="button" data-toggle-pw="pw-new" aria-label="Tampilkan kata sandi" class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg p-1 text-slate-500 transition hover:text-emerald-600">
              <span class="pw-eye"><x-icon name="eye" class="h-5 w-5" /></span>
              <span class="pw-eye-off" hidden><x-icon name="eye-off" class="h-5 w-5" /></span>
            </button>
          </div>
          @error('password')
            <span class="mt-1.5 block text-xs font-semibold text-red-500">{{ $message }}</span>
          @enderror
        </label>
        {{-- Meter kekuatan kata sandi --}}
        <div id="pw-strength" hidden class="mt-3">
          <div class="flex gap-1.5">
            <span class="pw-seg h-1.5 flex-1 rounded-full bg-slate-100 transition-colors duration-300"></span>
            <span class="pw-seg h-1.5 flex-1 rounded-full bg-slate-100 transition-colors duration-300"></span>
            <span class="pw-seg h-1.5 flex-1 rounded-full bg-slate-100 transition-colors duration-300"></span>
            <span class="pw-seg h-1.5 flex-1 rounded-full bg-slate-100 transition-colors duration-300"></span>
          </div>
          <p class="mt-2 text-xs text-slate-500">Kekuatan kata sandi: <span id="pw-strength-label" class="font-extrabold text-slate-700">—</span></p>
          <p id="pw-strength-hint" class="mt-0.5 text-xs text-slate-400">Pakai minimal 8 karakter dengan campuran huruf besar, angka, dan simbol.</p>
        </div>
      </div>
      <label class="block">
        <span class="mb-1.5 block text-sm font-bold text-slate-700">Konfirmasi kata sandi baru</span>
        <div class="relative">
          <span class="pointer-events-none absolute left-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-700 ring-1 ring-emerald-500/30"><x-icon name="lock" class="h-4 w-4" /></span>
          <input type="password" id="pw-confirm" name="password_confirmation" required autocomplete="new-password"
                 class="h-12 w-full rounded-xl border border-white/70 bg-white/70 pl-12 pr-12 backdrop-blur text-sm text-slate-800 transition focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/10">
          <button type="button" data-toggle-pw="pw-confirm" aria-label="Tampilkan kata sandi" class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg p-1 text-slate-500 transition hover:text-emerald-600">
            <span class="pw-eye"><x-icon name="eye" class="h-5 w-5" /></span>
            <span class="pw-eye-off" hidden><x-icon name="eye-off" class="h-5 w-5" /></span>
          </button>
        </div>
      </label>
    </div>

    <div>
      <button type="submit" class="inline-flex h-11 items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-6 text-sm font-bold text-white shadow-lg shadow-emerald-600/25 transition hover:from-emerald-500 hover:to-teal-500 active:scale-[0.98]">
        <x-icon name="lock" class="h-4 w-4" />
        Ubah kata sandi
      </button>
    </div>
  </form>
</section>

<script>
    // ── Tampilkan / sembunyikan kata sandi ──
    document.querySelectorAll('[data-toggle-pw]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const input = document.getElementById(btn.dataset.togglePw);
            if (!input) return;
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.querySelector('.pw-eye').hidden = show;
            btn.querySelector('.pw-eye-off').hidden = !show;
        });
    });

    // ── Pratinjau foto sebelum disimpan ──
    const avatarInput = document.getElementById('avatar-input');
    avatarInput?.addEventListener('change', () => {
        const file = avatarInput.files?.[0];
        const label = document.getElementById('avatar-file-label');
        if (label) label.textContent = file ? file.name : 'Pilih foto baru…';
        if (!file) return;
        const img = document.getElementById('avatar-preview');
        const initials = document.getElementById('avatar-initials');
        if (img) {
            img.src = URL.createObjectURL(file);
            img.hidden = false;
        }
        if (initials) initials.hidden = true;
    });

    // ── Meter kekuatan kata sandi baru ──
    const pwNew = document.getElementById('pw-new');
    const pwBox = document.getElementById('pw-strength');
    const pwSegs = [...document.querySelectorAll('.pw-seg')];
    const pwLabel = document.getElementById('pw-strength-label');
    const pwHint = document.getElementById('pw-strength-hint');
    const levels = [
        { upto: 0, text: '—', color: '', filled: 0 },
        { upto: 1, text: 'Lemah', color: 'text-rose-500', bar: 'bg-rose-500', filled: 1, hint: 'Terlalu mudah ditebak — tambahkan karakter lagi.' },
        { upto: 2, text: 'Sedang', color: 'text-amber-500', bar: 'bg-amber-400', filled: 2, hint: 'Lumayan — campur huruf besar, angka, atau simbol biar lebih kuat.' },
        { upto: 3, text: 'Kuat', color: 'text-emerald-500', bar: 'bg-emerald-500', filled: 3, hint: 'Sudah kuat. Sedikit lagi jadi sangat kuat.' },
        { upto: 99, text: 'Sangat kuat', color: 'text-emerald-600', bar: 'bg-emerald-600', filled: 4, hint: 'Sempurna — kata sandi ini susah ditebak.' },
    ];
    pwNew?.addEventListener('input', () => {
        const v = pwNew.value;
        pwBox.hidden = v.length === 0;
        if (!v) return;
        let score = 0;
        if (v.length >= 8) score += 1;
        if (v.length >= 12) score += 1;
        if (/[a-z]/.test(v) && /[A-Z]/.test(v)) score += 1;
        if (/\d/.test(v)) score += 1;
        if (/[^A-Za-z0-9]/.test(v)) score += 1;
        const level = levels.slice(1).find((l) => score <= l.upto) || levels[1];
        pwSegs.forEach((seg, i) => {
            seg.className = `pw-seg h-1.5 flex-1 rounded-full transition-colors duration-300 ${i < level.filled ? level.bar : 'bg-slate-100'}`;
        });
        pwLabel.textContent = level.text;
        pwLabel.className = `font-extrabold ${level.color}`;
        if (level.hint) pwHint.textContent = level.hint;
    });
</script>
