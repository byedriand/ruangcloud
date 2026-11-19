@extends('layouts.app')

@section('title', 'RuangCloud — Satu ruang aman untuk semua file-mu')

@section('content')
<div class="landing-page relative min-h-screen overflow-hidden bg-[#f8fbfa] text-[#14231f]">
  <div class="landing-glow landing-glow-one"></div>
  <div class="landing-glow landing-glow-two"></div>
  <div class="landing-grid-bg"></div>
  <div class="glass-orb-a pointer-events-none absolute -left-28 top-24 h-80 w-80 rounded-full bg-emerald-300/20 blur-3xl"></div>
  <div class="glass-orb-b pointer-events-none absolute -right-24 top-[430px] h-96 w-96 rounded-full bg-teal-300/20 blur-3xl"></div>
  <div class="glass-orb-a pointer-events-none absolute -bottom-36 left-[34%] h-96 w-96 rounded-full bg-emerald-200/20 blur-3xl" style="animation-delay: -5s"></div>

  <header class="landing-header sticky top-0 z-50 border-b border-white/50 bg-[#f8fbfa]/85 backdrop-blur-md">
    <div class="mx-auto flex h-[72px] max-w-[1240px] items-center px-5 md:px-8">
      <x-brand />
      <nav class="mx-auto hidden items-center gap-8 text-[13px] font-bold text-[#64736e] lg:flex">
        <a href="#fitur" class="transition hover:text-[#0aa982]">Fitur</a>
        <a href="#cara-kerja" class="transition hover:text-[#0aa982]">Cara Kerja</a>
        <a href="#keamanan" class="transition hover:text-[#0aa982]">Penyimpanan Terdistribusi</a>
        <a href="#tim" class="transition hover:text-[#0aa982]">Tim</a>
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
          Satu ruang aman untuk semua <span class="grad-text-landing relative whitespace-nowrap">file-mu.<svg class="absolute -bottom-2 left-0 w-full" viewBox="0 0 300 14" fill="none"><path d="M4 10C78 2 195 2 296 8" stroke="#77d8c1" stroke-width="5" stroke-linecap="round" /></svg></span>
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
        <div class="absolute left-[4%] top-[5%] h-[84%] w-[92%] rounded-full bg-gradient-to-br from-emerald-300/50 via-teal-200/40 to-cyan-200/40 blur-3xl"></div>
        <div class="hero-orbit hero-orbit-one"><div class="orbit-dot"></div></div>
        <div class="hero-orbit hero-orbit-two"><div class="orbit-dot orbit-dot-small"></div></div>

        <div class="hero-dashboard absolute left-[8%] top-[10%] w-[84%] overflow-hidden rounded-[26px] border border-white/80 bg-white/90 p-3 shadow-[0_45px_100px_-12px_rgba(18,60,47,.30)] backdrop-blur-md sm:p-4">
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
          <div class="px-4 py-3 text-center"><p class="stat-value-grad text-xl font-extrabold tracking-[-0.04em] md:text-2xl">{{ $stat['value'] }}</p><p class="mt-1 text-xs font-bold uppercase tracking-wider text-[#899590]">{{ $stat['label'] }}</p></div>
        @endforeach
      </div>
    </section>

    <section class="relative mx-auto max-w-[1100px] px-5 pb-2 pt-12 md:px-8">
      <p class="text-center text-[11px] font-extrabold uppercase tracking-[0.22em] text-[#899590]">Didukung teknologi terpercaya</p>
      <div class="mt-5 flex flex-wrap items-center justify-center gap-x-8 gap-y-3 text-[15px] font-extrabold tracking-[-0.01em] text-[#3d554d]">
        <span>Laravel</span><span class="h-1 w-1 rounded-full bg-[#b9d8d0]"></span>
        <span>PostgreSQL</span><span class="h-1 w-1 rounded-full bg-[#b9d8d0]"></span>
        <span>Redis</span><span class="h-1 w-1 rounded-full bg-[#b9d8d0]"></span>
        <span>MinIO <span class="font-bold text-[#899590]">S3</span></span><span class="h-1 w-1 rounded-full bg-[#b9d8d0]"></span>
        <span>Docker</span><span class="h-1 w-1 rounded-full bg-[#b9d8d0]"></span>
        <span>Tailwind CSS</span>
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

    <section id="keamanan" class="relative overflow-hidden border-t border-[#e4ece8]">
      <div class="pointer-events-none absolute -left-28 top-16 h-80 w-80 rounded-full bg-indigo-300/15 blur-3xl"></div>
      <div class="pointer-events-none absolute -right-28 bottom-10 h-80 w-80 rounded-full bg-violet-300/15 blur-3xl"></div>
      <div class="relative mx-auto max-w-[1180px] px-5 py-20 md:px-8 md:py-24">
        <div class="mx-auto mb-12 max-w-2xl text-center">
          <p class="mb-3 text-xs font-bold uppercase tracking-widest text-[#7c5cd6]">Penyimpanan terdistribusi</p>
          <h2 class="text-3xl font-extrabold tracking-[-0.045em] text-[#1a2320] md:text-4xl">Satu file, tersimpan di <span class="grad-text-indigo">banyak tempat</span> sekaligus.</h2>
          <p class="mt-4 text-base leading-7 text-[#64748b]">RuangCloud tidak menaruh semua file di satu mesin. Setiap unggahan ditempatkan di satu node, lalu direplikasi ke node lain dan dipantau kesehatannya.</p>
        </div>

        <div class="floating-card relative z-10 mx-auto flex w-fit max-w-full items-center gap-3.5 rounded-2xl border border-[#e2e8f0] bg-white py-3.5 pl-4 pr-5 shadow-[0_24px_50px_-20px_rgba(30,41,59,.25)]">
          <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-violet-400 to-purple-500 text-white shadow-lg shadow-violet-500/30"><x-icon name="archive" class="h-5 w-5" /></span>
          <div class="min-w-0">
            <p class="truncate text-sm font-extrabold text-[#1a2320]">Dokumen-penting.zip</p>
            <p class="mt-0.5 text-xs text-[#84948f]">86,4 MB &bull; <span class="font-bold text-emerald-600">Checksum terverifikasi</span></p>
          </div>
          <span class="ml-1 shrink-0 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-extrabold text-emerald-700 ring-1 ring-emerald-200">Tersimpan</span>
        </div>

        <div class="relative mx-auto hidden h-[104px] max-w-[920px] md:block" aria-hidden="true">
          <svg class="absolute inset-0 h-full w-full" viewBox="0 0 920 104" preserveAspectRatio="none" fill="none">
            <path class="flow-line" d="M460 2 C460 52 153 52 153 102" stroke="#a5b4fc" stroke-width="2" stroke-dasharray="5 7" />
            <path class="flow-line" d="M460 2 L460 102" stroke="#c4b5fd" stroke-width="2" stroke-dasharray="5 7" style="animation-delay: -.4s" />
            <path class="flow-line" d="M460 2 C460 52 767 52 767 102" stroke="#7dd3fc" stroke-width="2" stroke-dasharray="5 7" style="animation-delay: -.8s" />
          </svg>
        </div>

        @php
          $nodes = [
            ['name' => 'Server 1', 'city' => 'Jakarta', 'grad' => 'from-sky-400 to-blue-500 shadow-sky-500/30', 'bar' => 'linear-gradient(90deg, #38bdf8, #3b82f6)', 'pct' => 64, 'badge' => 'Replika'],
            ['name' => 'Server 2', 'city' => 'Bandung', 'grad' => 'from-violet-400 to-purple-500 shadow-violet-500/30', 'bar' => 'linear-gradient(90deg, #a78bfa, #8b5cf6)', 'pct' => 82, 'badge' => 'Primer'],
            ['name' => 'Server 3', 'city' => 'Surabaya', 'grad' => 'from-cyan-400 to-teal-500 shadow-cyan-500/30', 'bar' => 'linear-gradient(90deg, #22d3ee, #14b8a6)', 'pct' => 47, 'badge' => 'Replika'],
          ];
        @endphp
        <div class="mx-auto grid max-w-[920px] gap-4 md:grid-cols-3">
          @foreach ($nodes as $node)
            <article class="feature-card rounded-[22px] border border-[#e2e8f0] bg-white p-5 shadow-[0_10px_36px_-18px_rgba(30,41,59,.18)]">
              <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br text-white shadow-lg {{ $node['grad'] }}"><x-icon name="archive" class="h-5 w-5" /></span>
                <div class="min-w-0 flex-1">
                  <p class="text-sm font-extrabold text-[#1a2320]">{{ $node['name'] }}</p>
                  <p class="truncate text-xs text-[#84948f]">{{ $node['city'] }} — node penyimpanan</p>
                </div>
                <span class="flex shrink-0 items-center gap-1.5 text-[11px] font-bold text-emerald-600"><span class="h-2 w-2 rounded-full bg-emerald-500"></span> Aktif</span>
              </div>
              <div class="mt-4">
                <div class="flex items-center justify-between text-[11px] font-bold text-[#84948f]"><span>Ruang terpakai</span><span>{{ $node['pct'] }}%</span></div>
                <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-[#eef2f1]"><div class="h-full rounded-full" style="width: {{ $node['pct'] }}%; background: {{ $node['bar'] }}"></div></div>
              </div>
              <div class="mt-4 flex flex-wrap items-center gap-2">
                @if ($node['badge'] === 'Primer')
                  <span class="rounded-full bg-violet-100 px-2.5 py-1 text-[11px] font-extrabold text-violet-700">Primer untuk file ini</span>
                @else
                  <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-extrabold text-slate-600">Replika</span>
                @endif
                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-extrabold text-slate-600">SHA-256 cocok</span>
              </div>
            </article>
          @endforeach
        </div>

        @php
          $points = [
            ['title' => 'Replikasi otomatis', 'text' => 'Setiap file disalin hingga minimal 2 salinan fisik di node berbeda.'],
            ['title' => 'Failover tanpa terasa', 'text' => 'Satu node bermasalah, unduhan berpindah ke replika sehat secara otomatis.'],
            ['title' => 'Tercatat di log', 'text' => 'Semua aktivitas penyimpanan tercatat rapi dan bisa diaudit.'],
          ];
        @endphp
        <div class="mx-auto mt-12 grid max-w-[920px] gap-5 sm:grid-cols-3">
          @foreach ($points as $point)
            <div class="flex gap-3">
              <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-500 ring-1 ring-indigo-100"><x-icon name="check" class="h-4 w-4" /></span>
              <div>
                <p class="text-sm font-extrabold text-[#1a2320]">{{ $point['title'] }}</p>
                <p class="mt-1 text-[13px] leading-5 text-[#64748b]">{{ $point['text'] }}</p>
              </div>
            </div>
          @endforeach
        </div>
        <p class="mt-10 text-center text-xs text-[#9aa8a3]">Ilustrasi penempatan file — status node sesungguhnya dipantau live dari Admin Console RuangCloud.</p>
      </div>
    </section>
    <section id="tim" class="relative mx-4 mb-5 overflow-hidden rounded-[28px] bg-[#0e2f27] text-white md:mx-6">
      <div class="landing-grid-bg-dark"></div>
      <div class="pointer-events-none absolute -left-24 -top-32 h-96 w-96 rounded-full bg-emerald-400/15 blur-3xl"></div>
      <div class="pointer-events-none absolute -bottom-40 -right-16 h-[420px] w-[420px] rounded-full bg-teal-400/10 blur-3xl"></div>
      <div class="pointer-events-none absolute -right-24 -top-24 h-80 w-80 rounded-full border-[46px] border-white/[.03]"></div>

      <div class="relative mx-auto max-w-[1180px] px-6 py-16 md:px-10 md:py-20">
        <div class="mx-auto mb-12 max-w-2xl text-center">
          <div class="mb-4 flex items-center justify-center gap-3">
            <span class="h-px w-10 bg-gradient-to-r from-transparent to-[#54dbb9]/70"></span>
            <p class="text-xs font-bold uppercase tracking-widest text-[#54dbb9]">Kelompok 4 — FIKSI UKRI</p>
            <span class="h-px w-10 bg-gradient-to-l from-transparent to-[#54dbb9]/70"></span>
          </div>
          <h2 class="text-3xl font-extrabold tracking-[-0.045em] md:text-[40px]">Tim di balik <span class="grad-text-landing">RuangCloud</span></h2>
          <p class="mt-4 text-base leading-7 text-[#a7c4bc]">Lima orang dengan peran masing-masing, membangun penyimpanan awan terdistribusi ini dari nol.</p>
        </div>
        @php
          $team = [
            ['name' => 'Nayla Rabia Gustari', 'npm' => '20241320034', 'role' => 'Project Manager', 'grad' => 'from-emerald-400 to-teal-500 shadow-emerald-500/30', 'gender' => 'f', 'hair' => '#5b3a29', 'shirt' => '#0aa982', 'g1' => '#34d399', 'g2' => '#0d9488', 'github' => '#', 'instagram' => '#'],
            ['name' => 'Aisma Haidy Putri Berry Ani N.R', 'npm' => '20241320001', 'role' => 'System Analyst & UI/UX', 'grad' => 'from-violet-400 to-purple-500 shadow-violet-500/30', 'gender' => 'f', 'hair' => '#3b2d4d', 'shirt' => '#8b5cf6', 'g1' => '#c4b5fd', 'g2' => '#7c3aed', 'github' => '#', 'instagram' => '#'],
            ['name' => 'Adrian Ronald Daga', 'npm' => '20241320011', 'role' => 'Frontend / Backend Developer', 'grad' => 'from-sky-400 to-blue-500 shadow-sky-500/30', 'gender' => 'm', 'hair' => '#1f2937', 'shirt' => '#0284c7', 'g1' => '#7dd3fc', 'g2' => '#2563eb', 'github' => 'https://github.com/byedriand', 'instagram' => '#'],
            ['name' => 'Fakhry Ahmad Fauzan', 'npm' => '20241320038', 'role' => 'Frontend / Backend Developer', 'grad' => 'from-amber-400 to-orange-500 shadow-amber-500/30', 'gender' => 'm', 'hair' => '#292524', 'shirt' => '#d97706', 'g1' => '#fcd34d', 'g2' => '#ea580c', 'github' => '#', 'instagram' => '#'],
            ['name' => 'Arya Adi Muhammad Iqbal', 'npm' => '20241320018', 'role' => 'QA Tester', 'grad' => 'from-cyan-400 to-teal-500 shadow-cyan-500/30', 'gender' => 'm', 'hair' => '#134e4a', 'shirt' => '#0891b2', 'g1' => '#67e8f9', 'g2' => '#0d9488', 'github' => '#', 'instagram' => '#'],
          ];
          $shifts = ['', 'xl:mt-6', 'xl:mt-12', 'xl:mt-6', ''];
        @endphp
        <div class="grid gap-x-6 gap-y-14 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
          @foreach ($team as $member)
            <article class="feature-card team-card team-float group rounded-[22px] p-6 pt-8 text-center {{ $shifts[$loop->index] ?? '' }}" style="animation-delay: {{ $loop->index * -0.7 }}s">
              <div class="char-peek {{ $loop->even ? 'char-peek-left' : '' }}" style="animation-delay: {{ $loop->index * -0.55 }}s" aria-hidden="true">
                <svg viewBox="0 0 100 100">
                  <defs>
                    <linearGradient id="tim-peek-{{ $loop->index }}" x1="0" y1="0" x2="1" y2="1">
                      <stop offset="0%" stop-color="{{ $member['g1'] }}" />
                      <stop offset="100%" stop-color="{{ $member['g2'] }}" />
                    </linearGradient>
                    <clipPath id="tim-clip-{{ $loop->index }}"><circle cx="50" cy="50" r="46" /></clipPath>
                  </defs>
                  <g clip-path="url(#tim-clip-{{ $loop->index }})">
                    <circle cx="50" cy="50" r="46" fill="url(#tim-peek-{{ $loop->index }})" />
                    <path d="M29 100 Q31 78 50 77 Q69 78 71 100 Z" fill="{{ $member['shirt'] }}" />
                    <circle class="char-pulse" cx="50" cy="88" r="3.5" fill="#ffd166" />
                    <rect x="44" y="63" width="12" height="12" rx="3" fill="#b9ccd1" />
                    @if ($loop->index !== 1)
                      <rect x="22.5" y="42" width="6" height="13" rx="3" fill="{{ $member['g2'] }}" />
                      <rect x="71.5" y="42" width="6" height="13" rx="3" fill="{{ $member['g2'] }}" />
                    @endif
                    <rect x="27" y="28" width="46" height="37" rx="12" fill="#e9f2f4" />
                    <rect x="32.5" y="34" width="35" height="25" rx="8" fill="#0e2a38" />
                    <ellipse cx="41" cy="38" rx="7" ry="2.6" fill="#ffffff" opacity="0.14" />
                    <rect class="char-eye" x="40" y="42" width="5.5" height="8" rx="2.75" fill="#3ff2c0" />
                    <rect class="char-eye" x="54.5" y="42" width="5.5" height="8" rx="2.75" fill="#3ff2c0" />
                    <path d="M44.5 53.5 Q50 56.5 55.5 53.5" stroke="#3ff2c0" stroke-width="1.6" fill="none" stroke-linecap="round" />
                    @if ($loop->index === 0)
                      <rect x="48.8" y="17" width="2.4" height="12" fill="#9fb3ba" />
                      <circle class="char-pulse" cx="50" cy="14.5" r="4.2" fill="#ffd166" />
                    @elseif ($loop->index === 1)
                      <path d="M27 44 Q25 21 50 20 Q75 21 73 44" stroke="#7c3aed" stroke-width="4.5" fill="none" />
                      <rect x="20.5" y="40" width="9" height="17" rx="4.5" fill="#8b5cf6" />
                      <rect x="70.5" y="40" width="9" height="17" rx="4.5" fill="#8b5cf6" />
                    @elseif ($loop->index === 2)
                      <g class="char-wifi" stroke="#22d3ee" stroke-width="2.4" fill="none" stroke-linecap="round">
                        <path d="M37 15 Q50 5 63 15" />
                        <path d="M42 21 Q50 14 58 21" />
                      </g>
                      <circle cx="50" cy="25.5" r="1.8" fill="#22d3ee" />
                    @elseif ($loop->index === 3)
                      <path d="M36 30 L29 15" stroke="#9fb3ba" stroke-width="2.4" stroke-linecap="round" />
                      <path d="M64 30 L71 15" stroke="#9fb3ba" stroke-width="2.4" stroke-linecap="round" />
                      <circle class="char-pulse" cx="28.5" cy="13" r="3.6" fill="#ffd166" />
                      <circle class="char-pulse" cx="71.5" cy="13" r="3.6" fill="#ffd166" style="animation-delay: -.6s" />
                    @else
                      <rect x="48.8" y="17" width="2.4" height="12" fill="#9fb3ba" />
                      <circle class="char-pulse" cx="50" cy="14.5" r="4.2" fill="#7dd3fc" />
                      <path class="char-pulse" d="M22 12 L23.8 16.2 L28 18 L23.8 19.8 L22 24 L20.2 19.8 L16 18 L20.2 16.2 Z" fill="#ffe08a" />
                      <path class="char-pulse" style="animation-delay: -.8s" d="M78 9 L79.3 12.7 L83 14 L79.3 15.3 L78 19 L76.7 15.3 L73 14 L76.7 12.7 Z" fill="#ffe08a" />
                    @endif
                  </g>
                  <circle cx="50" cy="50" r="46" fill="none" stroke="rgba(255, 255, 255, .85)" stroke-width="3" />
                </svg>
              </div>
              @php
                $initials = collect(explode(' ', $member['name']))->take(2)->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))->implode('');
              @endphp
              {{-- Slot foto anggota: ganti lingkaran inisial ini dengan tag <img class="h-full w-full rounded-full object-cover"> bila foto sudah tersedia --}}
              <div class="team-avatar mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-br text-[26px] font-extrabold text-white shadow-lg ring-1 ring-white/25 transition duration-300 group-hover:scale-105 {{ $member['grad'] }}">{{ $initials }}</div>
              <h3 class="mt-5 text-[15px] font-extrabold leading-snug tracking-[-0.02em] text-white">{{ $member['name'] }}</h3>
              <span class="mt-3 inline-flex items-center justify-center rounded-full border border-emerald-300/25 bg-emerald-400/10 px-3 py-1 text-xs font-bold leading-snug text-[#54dbb9]">{{ $member['role'] }}</span>
              <p class="mt-3 text-[11px] font-semibold tracking-wider text-[#8fb8ab]">NPM {{ $member['npm'] }}</p>
              <div class="mt-5 flex items-center justify-center gap-2.5 border-t border-white/10 pt-4">
                <a href="{{ $member['github'] }}" target="_blank" rel="noopener" aria-label="GitHub {{ $member['name'] }}" class="flex h-9 w-9 items-center justify-center rounded-full border border-white/15 bg-white/10 text-white/75 transition duration-300 hover:-translate-y-0.5 hover:border-emerald-300/50 hover:bg-emerald-400/20 hover:text-white">
                  <x-icon name="github" class="h-[18px] w-[18px]" />
                </a>
                <a href="{{ $member['instagram'] }}" target="_blank" rel="noopener" aria-label="Instagram {{ $member['name'] }}" class="flex h-9 w-9 items-center justify-center rounded-full border border-white/15 bg-white/10 text-white/75 transition duration-300 hover:-translate-y-0.5 hover:border-emerald-300/50 hover:bg-emerald-400/20 hover:text-white">
                  <x-icon name="instagram" class="h-[18px] w-[18px]" />
                </a>
              </div>
            </article>
          @endforeach
        </div>
        <p class="mt-16 text-center text-xs leading-6 text-[#7ba598]">Program Studi Sistem Informasi — Fakultas Ilmu Komputer dan Sistem Informasi<br class="hidden sm:block"> Universitas Kebangsaan Republik Indonesia, Bandung</p>
      </div>
    </section>
  </main>

  <footer id="tentang" class="mx-auto flex max-w-[1180px] flex-col items-center justify-between gap-4 px-5 py-8 text-center md:flex-row md:px-8 md:text-left">
    <x-brand />
    <nav class="flex items-center gap-6 text-xs font-bold text-[#64736e]">
      <a href="#fitur" class="transition hover:text-[#0aa982]">Fitur</a>
      <a href="#cara-kerja" class="transition hover:text-[#0aa982]">Cara Kerja</a>
      <a href="#keamanan" class="transition hover:text-[#0aa982]">Terdistribusi</a>
      <a href="#tim" class="transition hover:text-[#0aa982]">Tim</a>
      <a href="{{ route('login') }}" class="transition hover:text-[#0aa982]">Masuk</a>
    </nav>
    <p class="text-xs text-[#87938f]">© 2026 RuangCloud. Penyimpanan cloud yang tumbuh bersamamu.</p>
  </footer>
</div>
@endsection
