{{-- Panel Pengguna --}}
@php
    $avatarTone = [
        'mint'     => 'bg-[#dff6ef] text-[#087d62]',
        'lavender' => 'bg-[#ece8ff] text-[#6e5ec2]',
        'amber'    => 'bg-[#fff0d9] text-[#b1711a]',
        'blue'     => 'bg-[#e7f0ff] text-[#4777bc]',
    ];
    $tones = ['mint', 'lavender', 'amber', 'blue'];
@endphp

<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
  <div>
    <h2 class="text-2xl font-extrabold tracking-[-0.035em]">Manajemen Pengguna</h2>
    <p class="mt-1 text-xs text-[#7b8782]">Kelola akun, peran, dan kuota penyimpanan pengguna.</p>
  </div>
  <div class="flex items-center gap-2">
    <div class="relative">
      <x-icon name="search" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#8d9793]" />
      <input id="user-search" class="h-9 w-56 rounded-xl bg-white border border-[#e2e9e6] pl-9 pr-3 text-[10px] outline-none focus:ring-2 focus:ring-[#0cb48c]/20"
             placeholder="Cari pengguna..." oninput="filterUsers(this.value)">
    </div>
    <button type="button" onclick="openAddAdmin()"
            class="flex h-9 items-center gap-2 rounded-xl bg-[#0cb48c] px-4 text-[10px] font-extrabold text-white hover:bg-[#09a07c] transition-colors">
      <x-icon name="plus" class="h-4 w-4" /> Tambah Admin
    </button>
  </div>
</div>

{{-- Stats Mini --}}
<div class="mb-5 grid grid-cols-3 gap-3">
  <div class="rounded-2xl border border-[#e0e8e5] bg-white p-4">
    <p class="text-[9px] font-bold uppercase tracking-[.1em] text-[#9aa39f]">Total Pengguna</p>
    <p class="mt-2 text-2xl font-extrabold tracking-tight">{{ $allUsers->count() }}</p>
    <p class="mt-1 text-[9px] text-[#909a96]">+{{ $newUsersThisMonth }} bulan ini</p>
  </div>
  <div class="rounded-2xl border border-[#e0e8e5] bg-white p-4">
    <p class="text-[9px] font-bold uppercase tracking-[.1em] text-[#9aa39f]">Pengguna Aktif</p>
    <p class="mt-2 text-2xl font-extrabold tracking-tight text-[#0cb48c]">{{ $allUsers->where('is_active', true)->count() }}</p>
    <p class="mt-1 text-[9px] text-[#909a96]">{{ $allUsers->where('is_active', false)->count() }} nonaktif</p>
  </div>
  <div class="rounded-2xl border border-[#e0e8e5] bg-white p-4">
    <p class="text-[9px] font-bold uppercase tracking-[.1em] text-[#9aa39f]">Admin</p>
    <p class="mt-2 text-2xl font-extrabold tracking-tight text-[#6e5ec2]">{{ $allUsers->where('role', 'admin')->count() }}</p>
    <p class="mt-1 text-[9px] text-[#909a96]">{{ $allUsers->where('role', 'user')->count() }} pengguna biasa</p>
  </div>
</div>

{{-- Table --}}
<article class="overflow-hidden rounded-[20px] border border-[#e0e8e5] bg-white">
  <div class="flex items-center justify-between border-b border-[#e8edeb] px-5 py-4">
    <div>
      <h3 class="text-sm font-extrabold">Daftar Pengguna</h3>
      <p class="mt-1 text-[9px] text-[#8b9692]">{{ $allUsers->count() }} akun terdaftar</p>
    </div>
    <div class="flex items-center gap-2 text-[9px]">
      <span class="text-[#9aa39f]">Filter:</span>
      <button onclick="filterRole('all')" class="user-filter-btn active rounded-lg px-2.5 py-1 font-bold border border-[#e2e8e5]" data-role="all">Semua</button>
      <button onclick="filterRole('admin')" class="user-filter-btn rounded-lg px-2.5 py-1 font-bold border border-transparent text-[#9aa39f] hover:border-[#e2e8e5]" data-role="admin">Admin</button>
      <button onclick="filterRole('user')" class="user-filter-btn rounded-lg px-2.5 py-1 font-bold border border-transparent text-[#9aa39f] hover:border-[#e2e8e5]" data-role="user">Pengguna</button>
    </div>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full min-w-[700px]" id="users-table">
      <thead>
        <tr class="text-[8px] font-extrabold uppercase tracking-[.1em] text-[#9aa39f] border-b border-[#edf1ef]">
          <th class="px-5 py-3 text-left">Pengguna</th>
          <th class="px-5 py-3 text-left">Peran</th>
          <th class="px-5 py-3 text-left">Kuota Terpakai</th>
          <th class="px-5 py-3 text-left">Status</th>
          <th class="px-5 py-3 text-left">Bergabung</th>
          <th class="px-5 py-3 text-left"></th>
        </tr>
      </thead>
      <tbody>
        @foreach ($allUsers as $i => $user)
          @php
            $tone = $tones[$i % 4];
            $initials = \App\Support\Fmt::initials($user->name);
            $used = (int) \App\Models\StoredFile::withTrashed()->where('user_id', $user->id)->sum('size');
            $percent = $user->quota_bytes > 0 ? (int) min(100, round($used / $user->quota_bytes * 100)) : 0;
          @endphp
          <tr class="user-row border-t border-[#edf1ef] hover:bg-[#fafcfb] transition-colors" data-name="{{ strtolower($user->name) }}" data-email="{{ strtolower($user->email) }}" data-role="{{ $user->role }}">
            <td class="px-5 py-3">
              <div class="flex items-center gap-2.5">
                <div class="flex h-9 w-9 items-center justify-center rounded-full text-[9px] font-extrabold {{ $avatarTone[$tone] }}">{{ $initials }}</div>
                <div>
                  <p class="text-[10px] font-extrabold">{{ $user->name }}</p>
                  <p class="mt-0.5 text-[8px] text-[#8c9792]">{{ $user->email }}</p>
                </div>
              </div>
            </td>
            <td class="px-5 py-3">
              <span class="rounded-full px-2 py-1 text-[8px] font-extrabold {{ $user->role === 'admin' ? 'bg-[#ede9ff] text-[#6e5ec2]' : 'bg-[#f0f4f2] text-[#5e6d68]' }}">
                {{ ucfirst($user->role) }}
              </span>
            </td>
            <td class="px-5 py-3 w-48">
              <div class="flex justify-between text-[8px] mb-1">
                <span>{{ \App\Support\Fmt::bytes($used) }}</span>
                <span class="text-[#9aa39f]">{{ \App\Support\Fmt::bytes($user->quota_bytes) }}</span>
              </div>
              <div class="h-1.5 w-32 rounded-full bg-[#ebefed]">
                <div class="h-full rounded-full {{ $percent > 75 ? 'bg-[#f0a545]' : 'bg-[#0cb48c]' }}" style="width: {{ $percent }}%"></div>
              </div>
            </td>
            <td class="px-5 py-3">
              <span class="rounded-full px-2 py-1 text-[8px] font-extrabold {{ $user->is_active ? 'bg-[#e9faf5] text-[#098b6c]' : 'bg-[#f0f2f1] text-[#7d8985]' }}">
                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
              </span>
            </td>
            <td class="px-5 py-3 text-[9px] text-[#7d8985]">
              {{ $user->created_at->locale('id')->translatedFormat('j M Y') }}
            </td>
            <td class="px-5 py-3 relative">
              <details class="menu relative inline-block">
                <summary class="cursor-pointer list-none rounded-lg p-2 text-[#8c9692] hover:bg-[#edf3f0] [&::-webkit-details-marker]:hidden">
                  <x-icon name="more" class="h-4 w-4" />
                </summary>
                <div data-menu-panel class="fixed z-50 rounded-xl border border-[#e1e8e5] bg-white p-1 text-[10px] font-bold text-[#33423d] shadow-[0_16px_40px_rgba(27,53,45,.16)] w-44">
                  <button type="button" onclick="updateAdminUser({{ $user->id }}, 'toggle_active')" 
                          class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left hover:bg-[#f3f7f5]"
                          {{ $user->id === auth()->id() ? 'disabled opacity-50' : '' }}>
                    <x-icon name="lock" class="h-4 w-4" /> 
                    {{ $user->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}
                  </button>
                  <button type="button" onclick="deleteAdminUser({{ $user->id }})" 
                          class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-[#d94f4f] hover:bg-[#fff1f0]" 
                          {{ $user->id === auth()->id() ? 'disabled opacity-50' : '' }}>
                    <x-icon name="trash" class="h-4 w-4" /> 
                    Hapus Akun
                  </button>
                </div>
              </details>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</article>

{{-- Modal Tambah Admin --}}
<div id="add-admin-modal" hidden class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-[2px] p-4">
  <div class="w-full max-w-[420px] rounded-[22px] bg-white p-6 shadow-[0_30px_80px_rgba(12,39,31,.20)]">
    <div class="mb-5 flex items-start justify-between">
      <div>
        <h2 class="text-lg font-extrabold tracking-[-0.03em]">Tambah Admin Baru</h2>
        <p class="mt-1 text-[11px] text-[#7c8883]">Buat akun dengan hak akses administrator.</p>
      </div>
      <button type="button" onclick="closeAddAdmin()" class="rounded-xl p-2 text-[#7d8985] hover:bg-[#f2f6f4]">
        <x-icon name="x" class="h-5 w-5" />
      </button>
    </div>

    <form id="add-admin-form" onsubmit="submitAddAdmin(event)" class="space-y-4">
      @csrf
      <label class="block">
        <span class="mb-1.5 block text-[11px] font-bold text-[#40504a]">Nama lengkap</span>
        <input id="aa-name" name="name" type="text" autocomplete="off" placeholder="cth. Budi Santoso"
               class="h-11 w-full rounded-xl border border-[#dbe5e1] bg-white px-4 text-sm outline-none transition focus:border-[#0cb48c] focus:ring-2 focus:ring-[#0cb48c]/10">
        <p id="aa-name-err" class="mt-1 hidden text-[10px] text-red-500"></p>
      </label>

      <label class="block">
        <span class="mb-1.5 block text-[11px] font-bold text-[#40504a]">Alamat email</span>
        <input id="aa-email" name="email" type="email" autocomplete="off" placeholder="admin@contoh.com"
               class="h-11 w-full rounded-xl border border-[#dbe5e1] bg-white px-4 text-sm outline-none transition focus:border-[#0cb48c] focus:ring-2 focus:ring-[#0cb48c]/10">
        <p id="aa-email-err" class="mt-1 hidden text-[10px] text-red-500"></p>
      </label>

      <label class="block">
        <span class="mb-1.5 block text-[11px] font-bold text-[#40504a]">Kata sandi</span>
        <div class="relative">
          <input id="aa-password" name="password" type="password" placeholder="Min. 8 karakter"
                 class="h-11 w-full rounded-xl border border-[#dbe5e1] bg-white px-4 pr-16 text-sm outline-none transition focus:border-[#0cb48c] focus:ring-2 focus:ring-[#0cb48c]/10">
          <button type="button" onclick="toggleAaPass('aa-password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-[9px] font-extrabold text-[#0aa982]">Lihat</button>
        </div>
        <p id="aa-password-err" class="mt-1 hidden text-[10px] text-red-500"></p>
      </label>

      <label class="block">
        <span class="mb-1.5 block text-[11px] font-bold text-[#40504a]">Konfirmasi kata sandi</span>
        <div class="relative">
          <input id="aa-password-confirmation" name="password_confirmation" type="password" placeholder="Ulangi kata sandi"
                 class="h-11 w-full rounded-xl border border-[#dbe5e1] bg-white px-4 pr-16 text-sm outline-none transition focus:border-[#0cb48c] focus:ring-2 focus:ring-[#0cb48c]/10">
          <button type="button" onclick="toggleAaPass('aa-password-confirmation', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-[9px] font-extrabold text-[#0aa982]">Lihat</button>
        </div>
        <p id="aa-confirm-err" class="mt-1 hidden text-[10px] text-red-500"></p>
      </label>

      <div class="flex gap-2 pt-1">
        <button type="button" onclick="closeAddAdmin()" class="h-11 flex-1 rounded-xl border border-[#dce5e1] text-sm font-bold text-[#44514d] hover:bg-[#f5f8f7]">Batal</button>
        <button id="aa-submit" type="submit" class="flex h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-[#0cb48c] text-sm font-extrabold text-white hover:bg-[#09a37e] disabled:opacity-60">
          <x-icon name="plus" class="h-4 w-4" /> Buat akun
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function filterUsers(q) {
  q = q.toLowerCase();
  document.querySelectorAll('#users-table .user-row').forEach(row => {
    const match = row.dataset.name.includes(q) || row.dataset.email.includes(q);
    row.style.display = match ? '' : 'none';
  });
}
function filterRole(role) {
  document.querySelectorAll('.user-filter-btn').forEach(b => {
    b.classList.toggle('active', b.dataset.role === role);
    b.classList.toggle('border-[#e2e8e5]', b.dataset.role === role);
    b.classList.toggle('border-transparent', b.dataset.role !== role);
    b.classList.toggle('text-[#9aa39f]', b.dataset.role !== role);
  });
  document.querySelectorAll('#users-table .user-row').forEach(row => {
    row.style.display = (role === 'all' || row.dataset.role === role) ? '' : 'none';
  });
}

/* ── Tambah Admin ── */
function openAddAdmin() {
  document.getElementById('add-admin-form').reset();
  ['aa-name-err','aa-email-err','aa-password-err','aa-confirm-err'].forEach(id => {
    const el = document.getElementById(id);
    el.textContent = '';
    el.classList.add('hidden');
  });
  document.getElementById('add-admin-modal').hidden = false;
}
function closeAddAdmin() {
  document.getElementById('add-admin-modal').hidden = true;
}
function toggleAaPass(inputId, btn) {
  const input = document.getElementById(inputId);
  const show  = input.type === 'password';
  input.type  = show ? 'text' : 'password';
  btn.textContent = show ? 'Sembunyikan' : 'Lihat';
}

async function submitAddAdmin(event) {
  event.preventDefault();
  const btn = document.getElementById('aa-submit');
  btn.disabled = true;

  // Reset error
  ['aa-name-err','aa-email-err','aa-password-err','aa-confirm-err'].forEach(id => {
    const el = document.getElementById(id);
    el.textContent = '';
    el.classList.add('hidden');
  });

  const form   = document.getElementById('add-admin-form');
  const csrf   = form.querySelector('input[name="_token"]').value;
  const body   = new URLSearchParams({
    _token:                csrf,
    name:                  document.getElementById('aa-name').value,
    email:                 document.getElementById('aa-email').value,
    password:              document.getElementById('aa-password').value,
    password_confirmation: document.getElementById('aa-password-confirmation').value,
  });

  try {
    const res  = await fetch('{{ route("admin.admins.store") }}', { method: 'POST', body, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const data = await res.json();

    if (res.ok && data.ok) {
      closeAddAdmin();
      window.notify?.(data.message);
      setTimeout(() => window.location.reload(), 800);
      return;
    }

    // Validasi 422
    if (res.status === 422 && data.errors) {
      const map = { name: 'aa-name-err', email: 'aa-email-err', password: 'aa-password-err' };
      Object.entries(data.errors).forEach(([field, msgs]) => {
        const errId = map[field];
        if (errId) {
          const el = document.getElementById(errId);
          el.textContent = msgs[0];
          el.classList.remove('hidden');
        }
      });
    } else {
      window.notify?.(data.message || 'Terjadi kesalahan. Coba lagi.');
    }
  } catch {
    window.notify?.('Gagal terhubung ke server.');
  } finally {
    btn.disabled = false;
  }
}

// Klik backdrop tutup modal
document.getElementById('add-admin-modal').addEventListener('mousedown', function(e) {
  if (e.target === this) closeAddAdmin();
});
// ESC tutup modal
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeAddAdmin();
});

/* ── Aksi Pengguna (Titik 3) ── */
async function updateAdminUser(userId, action) {
  if (!confirm('Anda yakin ingin mengubah status/peran pengguna ini?')) return;
  
  const csrf = document.querySelector('input[name="_token"]')?.value || '{{ csrf_token() }}';
  const body = new URLSearchParams({ _token: csrf, _method: 'PATCH', action });

  try {
    const res = await fetch(`/admin/users/${userId}`, { method: 'POST', body, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const data = await res.json();
    if (res.ok && data.ok) {
      window.notify?.(data.message);
      setTimeout(() => window.location.reload(), 800);
    } else {
      window.notify?.(data.message || 'Terjadi kesalahan.');
    }
  } catch (err) {
    window.notify?.('Gagal terhubung ke server.');
  }
}

async function deleteAdminUser(userId) {
  if (!confirm('PERINGATAN: Semua file yang dimiliki pengguna ini akan ikut terhapus! Anda yakin ingin menghapus akun ini permanen?')) return;
  
  const csrf = document.querySelector('input[name="_token"]')?.value || '{{ csrf_token() }}';
  const body = new URLSearchParams({ _token: csrf, _method: 'DELETE' });

  try {
    const res = await fetch(`/admin/users/${userId}`, { method: 'POST', body, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const data = await res.json();
    if (res.ok && data.ok) {
      window.notify?.(data.message);
      setTimeout(() => window.location.reload(), 800);
    } else {
      window.notify?.(data.message || 'Terjadi kesalahan saat menghapus.');
    }
  } catch (err) {
    window.notify?.('Gagal terhubung ke server.');
  }
}
</script>


