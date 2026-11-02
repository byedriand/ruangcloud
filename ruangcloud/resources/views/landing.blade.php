@extends('layouts.app')

@section('title', 'RuangCloud — Satu ruang aman untuk semua file-mu')

@section('content')
<div class="landing-page relative min-h-screen overflow-hidden bg-[#f8fbfa] text-[#14231f]">
  <div class="landing-glow landing-glow-one"></div>
  <div class="landing-glow landing-glow-two"></div>
  <div class="glass-orb-a pointer-events-none absolute -left-28 top-24 h-80 w-80 rounded-full bg-emerald-300/20 blur-3xl"></div>
  <div class="glass-orb-b pointer-events-none absolute -right-24 top-[430px] h-96 w-96 rounded-full bg-teal-300/20 blur-3xl"></div>
  <div class="glass-orb-a pointer-events-none absolute -bottom-36 left-[34%] h-96 w-96 rounded-full bg-emerald-200/20 blur-3xl" style="animation-delay: -5s"></div>

  <header class="sticky top-0 z-50 border-b border-white/50 bg-[#f8fbfa]/85 backdrop-blur-md">
    <div class="mx-auto flex h-[72px] max-w-[1240px] items-center px-5 md:px-8">
      <x-brand />
      <nav class="mx-auto hidden items-center gap-8 text-[13px] font-bold text-[#64736e] lg:flex">
        <a href="#fitur" class="transition hover:text-[#0aa982]">Fitur</a>
        <a href="#cara-kerja" class="transition hover:text-[#0aa982]">Cara Kerja</a>
        <a href="#keamanan" class="transition hover:text-[#0aa982]">Penyimpanan Terdistribusi</a>
      </nav>
      <div class="ml-auto flex items-center gap-2.5 lg:ml-0">
        <a href="{{ route('login') }}" class="flex h-10 items-center rounded-xl px-4 text-sm font-bold text-[#33473f] transition hover:bg-white">Masuk</a>
        <a href="{{ route('register') }}" class="flex h-10 items-center rounded-xl bg-[#0cb48c] px-5 text-sm font-extrabold text-white shadow-[0_10px_24px_rgba(12,180,140,.28)] transition hover:-translate-y-0.5 hover:bg-[#09a77f]">Daftar gratis</a>
      </div>
    </div>
  </header>

  <main>
    <section class="relative mx-auto grid min-h-[650px] max-w-[1240px] items-center gap-12 px-5 pb-16 pt-14 md:px-8 lg:grid-cols-[1.02fr_.98fr] lg:pb-24 lg:pt-16">
      <div class="relative z-10 flex flex-col items-center text-center lg:items-start lg:text-left">
        <div class="landing-reveal landing-delay-1 mb-6 inline-flex items-center gap-2 rounded-full border border-[#cce9e1] bg-white/80 px-4 py-2 text-xs font-extrabold uppercase tracking-wider text-[#078d6d] shadow-sm backdrop-blur">
          <span class="relative flex h-2 w-2"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-[#0cb48c] opacity-50"></span><span class="relative inline-flex h-2 w-2 rounded-full bg-[#0cb48c]"></span></span>
          Cloud storage generasi baru
        </div>
        <h1 class="landing-reveal landing-delay-2 max-w-[680px] text-[44px] font-extrabold leading-[1.06] tracking-[-0.055em] sm:text-[56px] lg:text-[68px]">
          Satu ruang aman untuk semua <span class="relative whitespace-nowrap text-[#0cb48c]">file-mu.<svg class="absolute -bottom-2 left-0 w-full" viewBox="0 0 300 14" fill="none"><path d="M4 10C78 2 195 2 296 8" stroke="#77d8c1" stroke-width="5" stroke-linecap="round" /></svg></span>
        </h1>
        <p class="landing-reveal landing-delay-3 mt-7 max-w-[560px] text-base leading-7 text-[#667570]">
          Simpan, kelola, dan bagikan file berukuran besar dengan cepat. RuangCloud menjaga setiap unggahan tetap berjalan, bahkan saat koneksimu tidak stabil.
        </p>
        <div class="landing-reveal landing-delay-4 mt-8 flex flex-wrap items-center justify-center gap-3 lg:justify-start">
          <a href="{{ route('register') }}" class="landing-primary-button flex h-12 items-center gap-2.5 rounded-xl bg-[#0cb48c] px-6 text-sm font-extrabold text-white shadow-[0_12px_30px_rgba(12,180,140,.25)] transition hover:-translate-y-0.5 hover:bg-[#09a77f]">
            Mulai sekarang <x-icon name="chevron" class="h-4 w-4" />
          </a>
          <a href="#fitur" class="flex h-12 items-center gap-2 rounded-xl px-4 text-sm font-bold text-[#52635d] transition hover:bg-white">
            Pelajari fitur <span class="flex h-6 w-6 items-center justify-center rounded-full bg-white shadow-sm"><x-icon name="chevron" class="h-3 w-3 rotate-90" /></span>
          </a>
        </div>
        <div class="landing-reveal landing-delay-5 mt-9 flex flex-wrap items-center justify-center gap-x-7 gap-y-3 text-xs font-semibold text-[#64748b] lg:justify-start">
          <span class="flex items-center gap-2"><span class="landing-check">✓</span> Kuota 5 GB</span>
          <span class="flex items-center gap-2"><span class="landing-check">✓</span> Resume otomatis</span>
          <span class="flex items-center gap-2"><span class="landing-check">✓</span> Hingga 5 GB per file</span>
        </div>
      </div>

      <div class="landing-visual landing-reveal landing-delay-3 relative mx-auto h-[430px] w-full max-w-[530px] sm:h-[500px]">
        <div class="absolute left-[7%] top-[8%] h-[76%] w-[85%] rounded-full bg-[#dff6f0]/80 blur-2xl"></div>
        <div class="hero-orbit hero-orbit-one"><div class="orbit-dot"></div></div>
        <div class="hero-orbit hero-orbit-two"><div class="orbit-dot orbit-dot-small"></div></div>

        <div class="hero-dashboard absolute left-[8%] top-[10%] w-[84%] overflow-hidden rounded-[26px] border border-white/80 bg-white/90 p-3 shadow-[0_35px_80px_rgba(27,72,60,.17)] backdrop-blur-md sm:p-4">
          <div class="mb-4 flex items-center justify-between border-b border-[#edf2f0] pb-3">
            <x-brand />
            <div class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-[#0cb48c]"></span><div class="h-7 w-7 rounded-full bg-[#dff5ef]"></div></div>
          </div>
          <div class="grid grid-cols-[72px_1fr] gap-3 sm:grid-cols-[90px_1fr]">
            <div class="space-y-2 rounded-xl bg-[#f5f9f7] p-2">
              <div class="h-7 rounded-lg bg-[#def6ef]"></div>
              <div class="h-2 w-3/4 rounded bg-[#dce5e1]"></div>
              <div class="h-2 w-2/3 rounded bg-[#e1e9e6]"></div>
              <div class="h-2 w-4/5 rounded bg-[#e1e9e6]"></div>
            </div>
            <div>
              <div class="mb-3 flex items-center justify-between"><div><div class="h-2.5 w-24 rounded bg-[#263c35]"></div><div class="mt-2 h-1.5 w-36 rounded bg-[#dce5e2]"></div></div><div class="h-7 w-16 rounded-lg bg-[#0cb48c]"></div></div>
              <div class="grid grid-cols-3 gap-2">
                @foreach (['#e5f8f2', '#f0ebff', '#fff1d8'] as $color)
                  <div class="rounded-xl border border-[#edf1ef] p-2.5"><div class="mb-5 h-7 w-8 rounded-lg" style="background: {{ $color }}"></div><div class="h-2 w-4/5 rounded bg-[#bac7c2]"></div><div class="mt-1.5 h-1.5 w-1/2 rounded bg-[#e0e6e4]"></div></div>
                @endforeach
              </div>
              <div class="mt-3 space-y-2 rounded-xl border border-[#edf1ef] p-3">
                @php
                  $rowBg = ['bg-[#ffedeb]', 'bg-[#e9f2ff]', 'bg-[#e8f8f3]'];
                @endphp
                @foreach ([70, 54, 82] as $i => $width)
                  <div class="flex items-center gap-2"><div class="h-7 w-7 rounded-lg {{ $rowBg[$i] }}"></div><div class="flex-1"><div class="h-1.5 rounded bg-[#c1ccc8]" style="width: {{ $width }}%"></div><div class="mt-1.5 h-1 w-1/3 rounded bg-[#e5eae8]"></div></div><x-icon name="more" class="h-3 w-3 text-[#a4aeaa]" /></div>
                @endforeach
              </div>
            </div>
          </div>
        </div>

        <div class="floating-card floating-upload absolute -left-1 top-[48%] z-10 w-[215px] rounded-2xl border border-white bg-white p-3.5 shadow-[0_18px_45px_rgba(29,66,56,.17)] sm:-left-3">
          <div class="flex items-center gap-3"><div class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#e4f8f2] text-[#0aa982]"><x-icon name="upload" class="h-4 w-4" /></div><div class="min-w-0 flex-1"><div class="flex justify-between"><p class="text-xs font-bold text-slate-800">Project-final.zip</p><span class="text-xs font-extrabold text-[#0aa982]">82%</span></div><div class="mt-2 h-1.5 overflow-hidden rounded-full bg-[#e7eeeb]"><div class="upload-shimmer h-full w-[82%] rounded-full bg-[#0cb48c]"></div></div><p class="mt-1.5 text-xs text-[#8b9692]">14,2 MB/dtk • 1 menit lagi</p></div></div>
        </div>

        <div class="floating-card floating-security absolute -right-1 bottom-[6%] z-10 flex w-[185px] items-center gap-3 rounded-2xl border border-white bg-[#173b32] p-3.5 text-white shadow-[0_18px_45px_rgba(20,58,48,.22)] sm:-right-3">
          <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/10 text-[#50ddb9]"><x-icon name="archive" class="h-4 w-4" /></div>
          <div><p class="text-xs font-bold">File terlindungi</p><p class="mt-0.5 text-xs text-[#9bc3b8]">2 replika aktif</p></div><span class="ml-auto h-2 w-2 rounded-full bg-[#33d4ad] ring-4 ring-[#33d4ad]/15"></span>
        </div>
      </div>
    </section>

    <section class="border-y border-[#e4ece8] bg-white/75">
      <div class="mx-auto grid max-w-[1100px] grid-cols-2 divide-x divide-[#e7edea] px-5 py-7 md:grid-cols-4 md:px-8">
        @foreach ($stats as $stat)
          <div class="px-4 py-3 text-center"><p class="text-xl font-extrabold tracking-[-0.04em] text-[#17382f] md:text-2xl">{{ $stat['value'] }}</p><p class="mt-1 text-xs font-bold uppercase tracking-wider text-[#899590]">{{ $stat['label'] }}</p></div>
        @endforeach
      </div>
    </section>

    <section id="fitur" class="relative mx-auto max-w-[1180px] px-5 py-20 md:px-8 md:py-28">
      <div class="glass-orb-a pointer-events-none absolute -left-36 top-20 h-96 w-96 rounded-full bg-emerald-300/30 blur-3xl"></div>
      <div class="glass-orb-b pointer-events-none absolute -right-36 bottom-16 h-96 w-96 rounded-full bg-teal-300/30 blur-3xl"></div>
      <div class="mx-auto mb-12 max-w-2xl text-center">
        <p class="mb-3 text-xs font-bold uppercase tracking-widest text-[#0aa982]">Dirancang untuk ketenangan</p>
        <h2 class="text-3xl font-extrabold tracking-[-0.045em] md:text-4xl text-[#1a2320]">File-mu tetap bergerak.<br>Datamu tetap aman.</h2>
        <p class="mt-4 text-base leading-7 text-[#64748b]">Teknologi yang kuat, dibungkus dalam pengalaman yang sederhana untuk seluruh tim.</p>
      </div>
      @php
        $gradients = ['from-emerald-400 to-teal-500 shadow-emerald-500/30', 'from-sky-400 to-blue-500 shadow-sky-500/30', 'from-violet-400 to-purple-500 shadow-violet-500/30', 'from-amber-400 to-orange-500 shadow-amber-500/30', 'from-cyan-400 to-teal-500 shadow-cyan-500/30', 'from-fuchsia-400 to-pink-500 shadow-fuchsia-500/30'];
      @endphp
      <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($features as $feature)
          <article class="feature-card group rounded-[22px] border border-[#e1eae6] bg-white p-6 transition hover:-translate-y-1 hover:border-[#c3e5db] hover:shadow-[0_20px_50px_rgba(24,64,53,.08)]" style="animation-delay: {{ $loop->index * 120 }}ms">
            <div class="mb-6 flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br text-white shadow-lg transition group-hover:rotate-3 group-hover:scale-110 {{ $gradients[$loop->index % 6] }}"><x-icon :name="$feature['icon']" class="h-5 w-5" /></div>
            <h3 class="text-lg font-extrabold tracking-[-0.025em] text-[#1a2320]">{{ $feature['title'] }}</h3>
            <p class="mt-2 text-sm leading-6 text-[#64748b]">{{ $feature['description'] }}</p>
          </article>
        @endforeach
      </div>
    </section>

    <section id="cara-kerja" class="border-t border-[#e4ece8] bg-white/60">
      <div class="mx-auto max-w-[1180px] px-5 py-20 md:px-8 md:py-24">
        <div class="mx-auto mb-12 max-w-2xl text-center">
          <p class="mb-3 text-xs font-bold uppercase tracking-widest text-[#0aa982]">Cara kerja</p>
          <h2 class="text-3xl font-extrabold tracking-[-0.045em] text-[#1a2320] md:text-4xl">Dari perambanmu, tersebar aman<br class="hidden md:block"> ke tiga node.</h2>
          <p class="mt-4 text-base leading-7 text-[#64748b]">Tiga langkah sederhana di balik setiap file yang kamu simpan.</p>
        </div>
        <div class="relative grid gap-4 md:grid-cols-3">
          <div class="absolute left-[16%] right-[16%] top-10 hidden border-t-2 border-dashed border-[#cdeadd] md:block"></div>
          @php
            $steps = [
              ['icon' => 'upload', 'grad' => 'from-emerald-400 to-teal-500 shadow-emerald-500/30', 'title' => 'Unggah per bagian', 'text' => 'File besar dipecah menjadi potongan 5 MB dan dikirim satu per satu. Jeda, lanjutkan, bahkan tutup halamannya — progresmu tersimpan hingga 24 jam.'],
              ['icon' => 'archive', 'grad' => 'from-sky-400 to-blue-500 shadow-sky-500/30', 'title' => 'Digabung & direplikasi', 'text' => 'Potongan digabung kembali menjadi file utuh, lalu otomatis disalin ke 2 dari 3 node penyimpanan agar selalu ada cadangannya.'],
              ['icon' => 'download', 'grad' => 'from-violet-400 to-purple-500 shadow-violet-500/30', 'title' => 'Akses dari node aktif', 'text' => 'Unduhan dilayani oleh node yang sehat. Satu node bermasalah? Sistem berpindah ke replika lainnya tanpa kamu sadari.'],
            ];
          @endphp
          @foreach ($steps as $step)
            <article class="feature-card relative rounded-[22px] border border-[#e1eae6] bg-white p-6 text-center" style="animation-delay: {{ $loop->index * 120 }}ms">
              <div class="relative z-10 mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br text-white shadow-lg {{ $step['grad'] }}">
                <x-icon :name="$step['icon']" class="h-6 w-6" />
                <span class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-[#17382f] text-[11px] font-extrabold text-white ring-4 ring-white">{{ $loop->iteration }}</span>
              </div>
              <h3 class="text-lg font-extrabold tracking-[-0.025em] text-[#1a2320]">{{ $step['title'] }}</h3>
              <p class="mt-2 text-sm leading-6 text-[#64748b]">{{ $step['text'] }}</p>
            </article>
          @endforeach
        </div>
      </div>
    </section>

    <section id="keamanan" class="mx-4 mb-5 overflow-hidden rounded-[28px] bg-[#12372e] text-white md:mx-6">
      <div class="relative mx-auto max-w-[1140px] px-6 py-12 md:px-10 md:py-16">
        <div class="absolute -right-10 -top-32 h-72 w-72 rounded-full border-[42px] border-white/[.035]"></div>
        <div class="absolute -bottom-40 -left-16 h-80 w-80 rounded-full bg-[#0cb48c]/10 blur-3xl"></div>
        <div class="relative grid items-center gap-10 lg:grid-cols-[1fr_.9fr]">
          <div>
            <p class="mb-2 text-xs font-bold uppercase tracking-wider text-[#54dbb9]">Penyimpanan terdistribusi</p>
            <h2 class="text-2xl font-extrabold tracking-[-0.04em] md:text-[32px] md:leading-[1.15]">Satu file, tersimpan di banyak tempat sekaligus.</h2>
            <p class="mt-3 max-w-lg text-sm leading-6 text-[#a7c4bc]">RuangCloud tidak menaruh semua file di satu mesin. Setiap file direplikasi lintas node, dipantau kesehatannya, dan tetap bisa diunduh meski ada node yang sedang bermasalah.</p>
            <ul class="mt-6 space-y-3 text-sm font-semibold text-[#d7ebe4]">
              <li class="flex items-center gap-3"><span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-white/10 text-[#54dbb9]"><x-icon name="check" class="h-3.5 w-3.5" /></span> Replikasi otomatis — minimal 2 salinan untuk setiap file</li>
              <li class="flex items-center gap-3"><span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-white/10 text-[#54dbb9]"><x-icon name="check" class="h-3.5 w-3.5" /></span> Failover — unduhan berpindah ke node sehat secara otomatis</li>
              <li class="flex items-center gap-3"><span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-white/10 text-[#54dbb9]"><x-icon name="check" class="h-3.5 w-3.5" /></span> Semua aktivitas tercatat rapi di log</li>
            </ul>
            <div class="mt-8 flex flex-wrap gap-3">
              <a href="{{ route('register') }}" class="flex h-12 items-center gap-2.5 rounded-xl bg-[#0cb48c] px-6 text-sm font-extrabold text-white shadow-[0_12px_30px_rgba(12,180,140,.35)] transition hover:-translate-y-0.5 hover:bg-[#09a77f]">Buat akun gratis <x-icon name="chevron" class="h-4 w-4" /></a>
              <a href="{{ route('login') }}" class="flex h-12 items-center rounded-xl border border-white/15 bg-white/[0.06] px-6 text-sm font-bold text-white backdrop-blur-md transition hover:bg-white/10">Masuk</a>
            </div>
          </div>
          <div class="space-y-3">
            @foreach (['Server 1', 'Server 2', 'Server 3'] as $node)
              <div class="flex items-center gap-4 rounded-2xl border border-white/10 bg-white/[0.05] p-4 backdrop-blur-md">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-400 to-teal-600 text-white shadow-lg shadow-emerald-950/40"><x-icon name="archive" class="h-5 w-5" /></span>
                <div class="min-w-0 flex-1">
                  <p class="text-sm font-extrabold">{{ $node }}</p>
                  <p class="text-xs text-[#9bc3b8]">Node penyimpanan RuangCloud</p>
                </div>
                <span class="rounded-full bg-white/[0.07] px-3 py-1 text-[11px] font-bold text-[#a7e8d5]">2 replika</span>
                <span class="flex items-center gap-2 text-xs font-bold text-[#54dbb9]"><span class="h-2 w-2 rounded-full bg-[#33d4ad] ring-4 ring-[#33d4ad]/15"></span> Aktif</span>
              </div>
            @endforeach
            <p class="px-1 pt-1 text-center text-xs text-[#7ba598]">Status node dipantau berkala dari Admin Console RuangCloud.</p>
          </div>
        </div>
      </div>
    </section>
  </main>

  <footer id="tentang" class="mx-auto flex max-w-[1180px] flex-col items-center justify-between gap-4 px-5 py-8 text-center md:flex-row md:px-8 md:text-left">
    <x-brand />
    <nav class="flex items-center gap-6 text-xs font-bold text-[#64736e]">
      <a href="#fitur" class="transition hover:text-[#0aa982]">Fitur</a>
      <a href="#cara-kerja" class="transition hover:text-[#0aa982]">Cara Kerja</a>
      <a href="#keamanan" class="transition hover:text-[#0aa982]">Terdistribusi</a>
      <a href="{{ route('login') }}" class="transition hover:text-[#0aa982]">Masuk</a>
    </nav>
    <p class="text-xs text-[#87938f]">© 2026 RuangCloud. Penyimpanan cloud yang tumbuh bersamamu.</p>
  </footer>
</div>
@endsection
