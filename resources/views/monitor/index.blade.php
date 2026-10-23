<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Tugas Disposisi & Tindak Lanjut | Dishub Kota Magelang</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS (Vite / CDN Fallback) -->
    @if(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @endif

    <style>
        [x-cloak] { display: none !important; }
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
        }
        /* Custom Scrollbars */
        ::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }
        ::-webkit-scrollbar-track {
            background: #e2e8f0;
        }
        ::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }
        /* Live Pulse Dot */
        @keyframes livePulse {
            0% { transform: scale(0.95); opacity: 0.9; }
            50% { transform: scale(1.25); opacity: 0.4; }
            100% { transform: scale(0.95); opacity: 0.9; }
        }
        .pulse-live {
            animation: livePulse 2s infinite ease-in-out;
        }
        /* Sembunyikan scrollbar untuk auto-scroll cards */
        .no-scrollbar::-webkit-scrollbar {
            display: none !important;
        }
        .no-scrollbar {
            -ms-overflow-style: none !important;
            scrollbar-width: none !important;
        }
        .auto-rows-fr {
            grid-auto-rows: minmax(0, 1fr);
        }
        .card-equal-height {
            height: 100%;
            max-height: 100%;
            min-height: 0;
        }
        /* Kunci tinggi layar tepat 100vh pada Desktop & Fullscreen Mode */
        @media (min-width: 768px) {
            html, body {
                height: 100vh !important;
                max-height: 100vh !important;
                overflow: hidden !important;
            }
        }
        :fullscreen, :-webkit-full-screen, :-moz-full-screen, :-ms-fullscreen {
            height: 100vh !important;
            max-height: 100vh !important;
            overflow: hidden !important;
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen md:h-screen md:max-h-screen md:overflow-hidden flex flex-col justify-between antialiased selection:bg-blue-600 selection:text-white"
      x-data="dishubMonitorDashboard({
          dataUrl: '{{ $dataUrl }}',
          initialStats: @js($globalStats),
          initialColumns: @js($columnsData),
          initialTaskIds: @js($allTaskIds)
      })"
      x-init="initMonitor()">

    <!-- AUDIO UNLOCK BANNER (AUTOPLAY POLICY) -->
    <div x-show="!audioUnlocked" x-cloak
         class="bg-blue-700 text-white text-xs px-4 py-2 flex items-center justify-between shadow-md sticky top-0 z-50">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path></svg>
            <span>Klik tombol di sebelah kanan untuk mengaktifkan bel suara notifikasi saat ada tugas baru masuk.</span>
        </div>
        <button type="button" @click="unlockAudio()" class="px-3 py-1 bg-white text-blue-900 font-bold rounded-lg text-xs hover:bg-blue-50 transition shadow">
            Aktifkan Suara Notifikasi
        </button>
    </div>

    <!-- NEW TASK TOAST ALERT -->
    <div x-show="showToast" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="-translate-y-8 opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="-translate-y-8 opacity-0"
         class="fixed top-24 right-6 z-50 max-w-md w-full bg-slate-900 text-white border-2 border-emerald-400 rounded-2xl shadow-2xl p-4 backdrop-blur-xl flex items-start gap-3">
        <div class="p-2.5 bg-emerald-500/20 text-emerald-400 rounded-xl flex-shrink-0 animate-pulse">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400 bg-emerald-950 px-2 py-0.5 rounded border border-emerald-500/30">Tugas Baru Diterima</span>
                <span class="text-[10px] text-slate-400">Baru saja</span>
            </div>
            <h4 class="text-sm font-bold text-white mt-1 truncate" x-text="toastTitle">Perihal Tugas</h4>
            <p class="text-xs text-slate-300 mt-0.5">
                Ditugaskan kepada: <strong class="text-emerald-300" x-text="toastAssignee">-</strong>
            </p>
        </div>
        <button type="button" @click="showToast = false" class="text-slate-400 hover:text-white p-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <!-- 1. TOP HEADER SECTION (IDENTIK MOCKUP DISHUB) -->
    <header class="bg-[#0b1d33] text-white relative z-20 overflow-hidden shadow-2xl shadow-black/60 border-b border-black/30 flex-shrink-0">
        
        <!-- BACKGROUND GAMBAR (header.jpg) JELAS & CERAH -->
        <div class="absolute inset-0 pointer-events-none z-0">
            <img src="{{ asset('images/header.jpg') }}" alt="Header Background" 
                 class="w-full h-full object-cover object-center">
            <!-- Overlay gradien halus & transparan agar gambar latar terlihat jelas dan cerah -->
            <div class="absolute inset-0 bg-gradient-to-r from-[#071322]/55 via-[#0b1d33]/20 to-[#071322]/55"></div>
            <!-- Bayangan tipis di bagian atas dan bawah -->
            <div class="absolute inset-0 bg-gradient-to-b from-black/30 via-transparent to-black/45"></div>
        </div>

        <div class="max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8 py-2.5 sm:py-3 relative z-10">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 sm:gap-4">
                
                <!-- KIRI: LOGO KOTA MAGELANG & TITLE -->
                <div class="flex items-center gap-3 sm:gap-4">
                    <img src="{{ asset('images/kota.png') }}" alt="Logo Kota Magelang" class="w-12 sm:w-14 h-auto object-contain filter drop-shadow-lg">
                    <div>
                        <h2 class="text-xs sm:text-sm font-semibold text-slate-100 tracking-wide leading-tight drop-shadow-md">
                            Dinas Perhubungan
                        </h2>
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-black text-white tracking-tight leading-tight drop-shadow-lg">
                            Kota Magelang
                        </h1>
                        <p class="text-[11px] sm:text-xs text-slate-200 font-medium mt-0.5 drop-shadow-md">
                            Sistem Informasi Manajemen Surat
                        </p>
                        <p class="text-[10px] sm:text-[11px] text-slate-300 font-normal drop-shadow">
                            Monitoring Tugas Disposisi & Tindak Lanjut
                        </p>
                    </div>
                </div>

                <!-- TENGAH: SLOGAN RESMI DISHUB -->
                <div class="hidden xl:flex flex-col items-center justify-center text-center px-4">
                    <p class="text-xs sm:text-sm font-semibold text-white italic max-w-xs leading-relaxed drop-shadow-lg">
                        Melayani dengan Integritas untuk Transportasi yang Lebih Baik
                    </p>
                    <div class="w-16 h-0.5 bg-amber-400 mt-1 rounded-full shadow"></div>
                </div>

                <!-- KANAN: JAM ANALOG + WAKTU DIGITAL & TANGGAL -->
                <div class="flex items-center gap-3 sm:gap-5 self-end lg:self-center">
                    
                    <!-- JAM ANALOG (DIAL SVG DENGAN JARUM BERPUTAR REAL-TIME) -->
                    <div class="relative w-14 h-14 sm:w-16 sm:h-16 flex-shrink-0 drop-shadow-xl">
                        <svg class="w-14 h-14 sm:w-16 sm:h-16 shadow-md rounded-full bg-white flex-shrink-0" viewBox="0 0 100 100">
                            <!-- Dial Bulat & Border -->
                            <circle cx="50" cy="50" r="47" fill="#ffffff" stroke="#cbd5e1" stroke-width="3"/>
                            
                            <!-- Angka Jam 1 - 12 (Persis Gambar Mockup) -->
                            <text x="50" y="16" text-anchor="middle" font-size="9" font-weight="800" fill="#0f172a">12</text>
                            <text x="70" y="22" text-anchor="middle" font-size="8" font-weight="700" fill="#475569">1</text>
                            <text x="82" y="36" text-anchor="middle" font-size="8" font-weight="700" fill="#475569">2</text>
                            <text x="88" y="54" text-anchor="middle" font-size="9" font-weight="800" fill="#0f172a">3</text>
                            <text x="82" y="70" text-anchor="middle" font-size="8" font-weight="700" fill="#475569">4</text>
                            <text x="70" y="84" text-anchor="middle" font-size="8" font-weight="700" fill="#475569">5</text>
                            <text x="50" y="90" text-anchor="middle" font-size="9" font-weight="800" fill="#0f172a">6</text>
                            <text x="30" y="84" text-anchor="middle" font-size="8" font-weight="700" fill="#475569">7</text>
                            <text x="18" y="70" text-anchor="middle" font-size="8" font-weight="700" fill="#475569">8</text>
                            <text x="12" y="54" text-anchor="middle" font-size="9" font-weight="800" fill="#0f172a">9</text>
                            <text x="18" y="36" text-anchor="middle" font-size="8" font-weight="700" fill="#475569">10</text>
                            <text x="30" y="22" text-anchor="middle" font-size="8" font-weight="700" fill="#475569">11</text>
                            
                            <!-- Jarum Jam (Hour Hand) -->
                            <line x1="50" y1="50" x2="50" y2="28" stroke="#0f172a" stroke-width="3.5" stroke-linecap="round"
                                  :transform="`rotate(${hourDegree} 50 50)`"/>
                                  
                            <!-- Jarum Menit (Minute Hand) -->
                            <line x1="50" y1="50" x2="50" y2="18" stroke="#334155" stroke-width="2.5" stroke-linecap="round"
                                  :transform="`rotate(${minuteDegree} 50 50)`"/>
                                  
                            <!-- Jarum Detik (Second Hand Merah) -->
                            <line x1="50" y1="58" x2="50" y2="14" stroke="#dc2626" stroke-width="1.5" stroke-linecap="round"
                                  :transform="`rotate(${secondDegree} 50 50)`"/>
                                  
                            <!-- Titik Poros Tengah -->
                            <circle cx="50" cy="50" r="3.5" fill="#dc2626"/>
                            <circle cx="50" cy="50" r="1.5" fill="#ffffff"/>
                        </svg>
                    </div>

                    <!-- JAM DIGITAL & TANGGAL -->
                    <div class="text-right">
                        <div class="text-xs font-semibold text-slate-100 drop-shadow-md" x-text="dayName">
                            {{ \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->translatedFormat('l') }}
                        </div>
                        <div class="text-xs font-medium text-slate-200 drop-shadow-md" x-text="dateFormatted">
                            {{ \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->translatedFormat('d F Y') }}
                        </div>
                        <div class="flex items-baseline justify-end gap-1.5 mt-0.5">
                            <span class="text-2xl sm:text-3xl font-extrabold text-white tracking-wider font-mono drop-shadow-lg" x-text="digitalTime">
                                {{ \Carbon\Carbon::now('Asia/Jakarta')->format('H:i') }}
                            </span>
                            <span class="text-xs font-bold text-slate-300 drop-shadow">WIB</span>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </header>

    <!-- CONTAINER BODY KONTEN (1 HALAMAN VIEWPORT FIT) -->
    <main class="max-w-[1920px] w-full mx-auto px-3 sm:px-5 lg:px-6 py-2 space-y-2 flex-1 min-h-0 md:h-full flex flex-col overflow-hidden">

            <!-- 2. TOP METRIC CARDS (4 KARTU IDENTIK MOCKUP - COMPACT FIT) -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 flex-shrink-0">
                
                <!-- CARD 1: TOTAL SURAT MASUK (BIRU) -->
                <div class="bg-white rounded-xl p-3 shadow-xs border border-slate-200/90 flex items-center justify-between transition-transform hover:-translate-y-0.5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-md shadow-blue-500/20 flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Total Surat Masuk</span>
                            <div class="mt-0.5">
                                <span class="text-2xl font-black text-slate-900" x-text="stats.total_surat">48</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: TOTAL DISPOSISI (BIRU SERAGAM) -->
                <div class="bg-white rounded-xl p-3 shadow-xs border border-slate-200/90 flex items-center justify-between transition-transform hover:-translate-y-0.5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-md shadow-blue-500/20 flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Total Disposisi</span>
                            <div class="mt-0.5">
                                <span class="text-2xl font-black text-slate-900" x-text="stats.total_disposisi">46</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 3: TUGAS BELUM SELESAI (BIRU SERAGAM) -->
                <div class="bg-white rounded-xl p-3 shadow-xs border border-slate-200/90 flex items-center justify-between transition-transform hover:-translate-y-0.5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-md shadow-blue-500/20 flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Tugas Belum Selesai</span>
                            <div class="mt-0.5">
                                <span class="text-2xl font-black text-slate-900" x-text="stats.belum_selesai">18</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 4: TUGAS SELESAI (BIRU SERAGAM) -->
                <div class="bg-white rounded-xl p-3 shadow-xs border border-slate-200/90 flex items-center justify-between transition-transform hover:-translate-y-0.5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-md shadow-blue-500/20 flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Tugas Selesai</span>
                            <div class="mt-0.5">
                                <span class="text-2xl font-black text-slate-900" x-text="stats.selesai">28</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- 3. SUBHEADER: TITLE "PER BIDANG" & INDIKATOR DATA REAL-TIME -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pt-0.5 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <h3 class="text-base sm:text-lg font-extrabold text-slate-800 tracking-tight">
                            @if($mode === 'bidang')
                                Penugasan: {{ $activeDept->name }}
                            @else
                                Per Bidang
                            @endif
                        </h3>
                    </div>

                    <!-- Switcher Dropdown (Jika Ingin Berpindah Cepat Antar Bidang di TV) -->
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" 
                                class="text-xs bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold px-2.5 py-1 rounded-lg flex items-center gap-1.5 shadow-xs">
                            <span>Pilih Tampilan</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak
                             class="absolute left-0 mt-1 w-56 bg-white border border-slate-200 rounded-xl shadow-xl z-30 py-1 text-xs">
                            <a href="{{ route('monitor.index') }}" class="block px-3 py-2 text-slate-700 hover:bg-blue-50 font-semibold {{ $mode === 'all' ? 'text-blue-600 bg-blue-50/50' : '' }}">
                                🏢 Semua Bidang (Overview)
                            </a>
                            <div class="border-t border-slate-100 my-1"></div>
                            @foreach($departments as $d)
                                <a href="{{ $d->monitor_url }}" class="block px-3 py-1.5 text-slate-600 hover:bg-blue-50 hover:text-blue-700 truncate {{ $activeDept?->id === $d->id ? 'text-blue-600 font-bold bg-blue-50/50' : '' }}">
                                    &bull; {{ $d->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Sisi Kanan: Real-time Indicator & Controls -->
                <div class="flex items-center gap-3">
                    
                    <!-- Indikator Data Real-time -->
                    <div class="flex items-center gap-1.5 text-xs font-bold text-slate-700">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 pulse-live"></span>
                        <span>Data Real-time</span>
                    </div>

                    <!-- Tombol Refresh Cepat -->
                    <button type="button" @click="fetchData()" title="Segarkan Data Sekarang"
                            class="p-1.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-600 rounded-lg shadow-xs transition">
                        <svg class="w-4 h-4" :class="isRefreshing ? 'animate-spin text-blue-600' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </button>

                    <!-- Tombol Suara Notifikasi -->
                    <button type="button" @click="toggleSound()" title="Aktifkan / Bisukan Suara Notifikasi"
                            class="p-1.5 bg-white border border-slate-300 hover:bg-slate-50 rounded-lg shadow-xs transition text-xs flex items-center gap-1 font-semibold"
                            :class="soundEnabled ? 'text-emerald-700' : 'text-slate-400'">
                        <template x-if="soundEnabled">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path></svg>
                        </template>
                        <template x-if="!soundEnabled">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"></path></svg>
                        </template>
                    </button>

                    <!-- Tombol Fullscreen F11 -->
                    <button type="button" @click="toggleFullScreen()" title="Layar Penuh (F11)"
                            class="p-1.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-600 rounded-lg shadow-xs transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                    </button>
                </div>
            </div>

            <!-- 4. KOLOM KANBAN KARTU TUGAS PER BIDANG (1 HALAMAN VIEWPORT FIT DENGAN AUTO-SCROLL) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 items-stretch flex-1 min-h-0 md:h-full auto-rows-fr overflow-hidden"
                 :class="{
                     'xl:grid-cols-2': columns.length === 2,
                     'xl:grid-cols-3': columns.length === 3,
                     'xl:grid-cols-4': columns.length === 4,
                     'xl:grid-cols-5': columns.length === 5,
                     'xl:grid-cols-6': columns.length >= 6 || columns.length === 0,
                 }">
                
                <template x-for="col in columns" :key="col.id">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/90 overflow-hidden flex flex-col h-full max-h-full min-h-0 card-equal-height transition-all duration-300 hover:shadow-md">
                        
                        <!-- HEADER KARTU (WARNA IDENTIK PER BIDANG DENGAN ICON) -->
                        <div class="p-2 sm:p-2.5 text-white flex items-center justify-between shadow-xs cursor-pointer flex-shrink-0"
                             :class="col.theme.bg"
                             @click="openTaskModal(col)">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <!-- Avatar Karyawan / Personel Sesuai Foto Profil Sendiri -->
                                <div class="flex-shrink-0">
                                    <template x-if="col.avatar">
                                        <img :src="col.avatar" :alt="col.name" 
                                             class="w-8 h-8 rounded-full object-cover border-2 border-white/80 shadow-md bg-white">
                                    </template>
                                    <template x-if="!col.avatar">
                                        <div class="w-8 h-8 rounded-full bg-white/25 text-white font-extrabold flex items-center justify-center text-xs shadow-md border border-white/40 backdrop-blur-xs"
                                             x-text="col.initials || col.name.substring(0, 2).toUpperCase()">
                                        </div>
                                    </template>
                                </div>
                                
                                <!-- Judul Bidang & Subtitle -->
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-white truncate" x-text="col.name">Nama Bidang</h4>
                                    <p class="text-[10px] text-white/80 font-medium" x-text="col.total_tasks + ' tugas belum selesai'">0 tugas belum selesai</p>
                                </div>
                            </div>

                            <!-- Panah Kanan -->
                            <svg class="w-4 h-4 text-white/80 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </div>

                        <!-- BODY: DAFTAR TUGAS AUTO-SCROLL (PING-PONG ATAS BAWAH) -->
                        <div class="p-2 sm:p-2.5 flex-1 min-h-0 flex flex-col justify-between overflow-hidden"
                             x-data="columnAutoScroll()"
                             x-init="startScroll()"
                             @mouseenter="pause()"
                             @mouseleave="resume()">
                            
                            <div class="flex items-center justify-between mb-1.5 flex-shrink-0">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">
                                    Daftar Tugas
                                </span>
                                <span class="text-[9px] font-extrabold px-1.5 py-0.2 rounded-full"
                                      :class="col.total_tasks > 0 ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-500'"
                                      x-text="col.total_tasks + ' Tugas'">
                                </span>
                            </div>

                            <!-- Container List Tugas yang Auto-Scroll -->
                            <div x-ref="scrollContainer" 
                                 @wheel.passive="handleWheel()"
                                 class="flex-1 min-h-0 overflow-y-auto no-scrollbar space-y-2 pr-0.5">
                                
                                <!-- Empty State jika 0 tugas -->
                                <template x-if="col.tasks.length === 0">
                                    <div class="h-full flex flex-col items-center justify-center text-center text-slate-400 py-6">
                                        <svg class="w-7 h-7 mx-auto text-emerald-500 mb-1.5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        <p class="text-xs font-semibold text-slate-500">Semua Tugas Selesai</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">Tidak ada tugas aktif</p>
                                    </div>
                                </template>

                                <!-- Tiap Item Tugas (Card Informatif & Berisi) -->
                                <template x-for="task in col.tasks" :key="task.id">
                                    <div class="p-2.5 bg-white hover:bg-slate-50/90 rounded-xl border border-slate-200/90 shadow-xs hover:shadow-md transition-all duration-200 group flex flex-col justify-between">
                                        
                                        <div>
                                            <!-- Baris Atas: Bullet Urgensi + Tag Sumber/Status + Badge Prioritas -->
                                            <div class="flex items-center justify-between gap-1.5 mb-1">
                                                <div class="flex items-center gap-1.5 min-w-0 flex-wrap">
                                                    <!-- Indikator Bulat Urgensi -->
                                                    <span class="w-2 h-2 rounded-full flex-shrink-0" :class="task.circle_color"></span>

                                                    <!-- Tag Sumber: Tugas Kabid vs Surat Masuk -->
                                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded tracking-wide truncate"
                                                          :class="task.is_direct ? 'bg-blue-100 text-blue-800 border border-blue-200' : 'bg-slate-100 text-slate-700 border border-slate-200'"
                                                          x-text="task.is_direct ? 'Tugas Kabid' : 'Surat Masuk'">
                                                    </span>

                                                    <!-- Status Pengerjaan (Belum Dibaca, Dikerjakan, dll) -->
                                                    <span class="text-[9px] font-semibold px-1.5 py-0.2 rounded border tracking-wide"
                                                          :class="task.status_class"
                                                          x-text="task.status_label">
                                                    </span>
                                                </div>

                                                <!-- Badge Prioritas/Urgensi (Urgent / Penting / Biasa) -->
                                                <span class="text-[9px] font-extrabold px-2 py-0.2 rounded-full shadow-xs uppercase tracking-wider flex-shrink-0"
                                                      :class="task.urgency_color"
                                                      x-text="task.urgency">
                                                </span>
                                            </div>

                                            <!-- Judul / Perihal Tugas (Bukan 'Tanpa Perihal') -->
                                            <h5 class="text-xs font-bold text-slate-900 group-hover:text-blue-700 transition-colors line-clamp-2 leading-snug"
                                                x-text="task.title">
                                                Judul Perihal Surat
                                            </h5>

                                            <!-- Perintah / Arahan Langsung dari Kabid -->
                                            <template x-if="task.catatan_kabid">
                                                <div class="mt-1.5 p-1.5 bg-amber-50/90 rounded-lg border border-amber-200/70 text-[11px] text-amber-950 flex items-start gap-1.5 leading-snug">
                                                    <svg class="w-3.5 h-3.5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                                    </svg>
                                                    <div class="min-w-0">
                                                        <span class="font-extrabold text-amber-900 text-[10px] uppercase tracking-wider block">Perintah Kabid:</span>
                                                        <p class="font-medium text-slate-800 break-words" x-text="task.catatan_kabid"></p>
                                                    </div>
                                                </div>
                                            </template>

                                            <!-- Detail Surat Masuk (Nomor & Asal Surat jika ada) -->
                                            <template x-if="!task.is_direct && task.nomor_surat && task.nomor_surat !== '-'">
                                                <div class="mt-1 text-[10px] text-slate-500 flex items-center gap-1 truncate">
                                                    <svg class="w-3 h-3 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                    <span class="truncate">No: <span class="font-semibold text-slate-700" x-text="task.nomor_surat"></span></span>
                                                    <template x-if="task.asal_surat && task.asal_surat !== '-'">
                                                        <span class="truncate">• Dari: <span class="font-medium text-slate-600" x-text="task.asal_surat"></span></span>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>

                                        <!-- Bagian Bawah: Informasi Deadline & Tanggal Disposisi -->
                                        <div class="mt-2 pt-1.5 border-t border-slate-100 flex items-center justify-between text-[11px]">
                                            
                                            <!-- Deadline / Batas Waktu Pengerjaan -->
                                            <div class="flex items-center gap-1.5 min-w-0">
                                                <svg class="w-3.5 h-3.5 flex-shrink-0" :class="task.has_deadline ? 'text-rose-500' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                
                                                <template x-if="task.has_deadline">
                                                    <div class="flex items-center gap-1 truncate">
                                                        <span class="font-bold text-slate-700">Tenggat:</span>
                                                        <span class="font-semibold text-slate-900" x-text="task.deadline_formatted"></span>
                                                        <span class="text-[9px] px-1.5 py-0.2 rounded font-extrabold"
                                                              :class="task.deadline_class"
                                                              x-text="task.deadline_status">
                                                        </span>
                                                    </div>
                                                </template>
                                                
                                                <template x-if="!task.has_deadline">
                                                    <div class="flex items-center gap-1 text-slate-400 text-[10px]">
                                                        <span>Disposisi:</span>
                                                        <span class="font-medium text-slate-600" x-text="task.disposisi_formatted || task.date"></span>
                                                    </div>
                                                </template>
                                            </div>

                                            <!-- Assignee / Penerima (hanya jika di overview semua bidang) -->
                                            <template x-if="!col.is_user && task.assignee">
                                                <span class="text-[10px] text-slate-500 font-semibold truncate max-w-[80px] bg-slate-100 px-1.5 py-0.2 rounded text-right"
                                                      :title="'Ditugaskan: ' + task.assignee"
                                                      x-text="task.assignee">
                                                </span>
                                            </template>

                                        </div>

                                    </div>
                                </template>

                            </div>

                            <!-- FOOTER LINK: LIHAT SEMUA (POP-UP INTERAKTIF) -->
                            <div class="mt-1.5 pt-1.5 border-t border-slate-100 flex items-center justify-between text-xs flex-shrink-0">
                                <button type="button" 
                                        @click="openTaskModal(col)" 
                                        class="text-blue-600 hover:text-blue-800 font-bold flex items-center justify-between w-full group/btn transition-colors py-0.5">
                                    <span class="flex items-center gap-1.5">
                                        <span>Lihat Semua</span>
                                        <span class="text-[10px] bg-blue-50 text-blue-700 px-1.5 py-0.2 rounded-full font-extrabold border border-blue-200"
                                              x-text="col.total_tasks">
                                        </span>
                                    </span>
                                    <svg class="w-3.5 h-3.5 transform group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </button>
                            </div>

                        </div>

                    </div>
                </template>

            </div>

        </main>

        <!-- 5. BOTTOM FOOTER SECTION (IDENTIK MOCKUP DISHUB) -->
        <footer class="bg-[#0b1d33] text-white py-1.5 sm:py-2 px-4 sm:px-8 border-t border-slate-800 shadow-inner flex-shrink-0 z-20">
            <div class="max-w-[1920px] mx-auto flex flex-col sm:flex-row items-center justify-between gap-1 text-xs">
                
                <!-- KIRI: IKON GEDUNG + MOTTO DISHUB -->
                <div class="flex items-center gap-2 text-slate-300">
                    <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m3-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <span class="font-medium italic truncate">"Disiplin dalam bekerja, pelayanan semakin baik"</span>
                </div>

                <!-- KANAN: GARIS BIRU + NAMA INSTANSI -->
                <div class="flex items-center gap-3 flex-shrink-0">
                    <div class="w-12 h-0.5 bg-blue-500 rounded-full hidden sm:block"></div>
                    <span class="font-semibold text-slate-200">Dinas Perhubungan Kota Magelang</span>
                </div>

            </div>
        </footer>

    <!-- ======================================================== -->
    <!-- MODAL POP-UP: LIHAT SEMUA TUGAS KARYAWAN / BIDANG        -->
    <!-- ======================================================== -->
    <div x-show="modalOpen" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="closeTaskModal()"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/75 backdrop-blur-sm flex items-center justify-center p-3 sm:p-6">
        
        <!-- Modal Card Container -->
        <div @click.away="closeTaskModal()"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="bg-white rounded-3xl shadow-2xl border border-slate-200 max-w-3xl w-full max-h-[88vh] flex flex-col overflow-hidden text-slate-800">
            
            <!-- HEADER MODAL -->
            <div class="p-4 sm:p-5 text-white flex items-center justify-between shadow-md"
                 :class="modalCol?.theme?.bg || 'bg-blue-600'">
                <div class="flex items-center gap-3 min-w-0">
                    <!-- Avatar Profil -->
                    <template x-if="modalCol?.avatar">
                        <img :src="modalCol.avatar" :alt="modalCol.name" 
                             class="w-11 h-11 rounded-full object-cover border-2 border-white/90 shadow bg-white">
                    </template>
                    <template x-if="!modalCol?.avatar">
                        <div class="w-11 h-11 rounded-full bg-white/25 text-white font-black flex items-center justify-center text-sm shadow border border-white/40"
                             x-text="modalCol?.initials || modalCol?.name?.substring(0, 2).toUpperCase()">
                        </div>
                    </template>
                    
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <h3 class="text-base sm:text-lg font-black tracking-tight truncate text-white" 
                                x-text="modalCol?.name">
                                Nama Karyawan / Bidang
                            </h3>
                            <span class="text-xs font-bold bg-white/20 text-white px-2 py-0.5 rounded-full border border-white/30"
                                  x-text="modalCol?.total_tasks + ' Tugas'">
                            </span>
                        </div>
                        <p class="text-xs text-white/85 font-medium mt-0.5">
                            Daftar Lengkap Tugas Disposisi & Tindak Lanjut
                        </p>
                    </div>
                </div>

                <!-- Tombol Close (X) -->
                <button type="button" @click="closeTaskModal()" 
                        class="p-2 rounded-xl text-white/80 hover:text-white hover:bg-white/20 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- SEARCH & FILTER TOOLBAR -->
            <div class="px-5 py-3 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <!-- Search Input -->
                <div class="relative flex-1">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" x-model="modalSearch" placeholder="Cari judul, arahan, atau nomor surat..."
                           class="w-full pl-9 pr-3 py-1.5 text-xs bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <!-- Filter Chips -->
                <div class="flex items-center gap-1.5 text-xs">
                    <button type="button" @click="modalFilter = 'all'"
                            class="px-2.5 py-1 rounded-lg font-bold transition text-xs"
                            :class="modalFilter === 'all' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-100'">
                        Semua
                    </button>
                    <button type="button" @click="modalFilter = 'urgent'"
                            class="px-2.5 py-1 rounded-lg font-bold transition text-xs"
                            :class="modalFilter === 'urgent' ? 'bg-red-600 text-white shadow-xs' : 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-100'">
                        Urgent
                    </button>
                    <button type="button" @click="modalFilter = 'penting'"
                            class="px-2.5 py-1 rounded-lg font-bold transition text-xs"
                            :class="modalFilter === 'penting' ? 'bg-amber-600 text-white shadow-xs' : 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-100'">
                        Penting
                    </button>
                    <button type="button" @click="modalFilter = 'deadline'"
                            class="px-2.5 py-1 rounded-lg font-bold transition text-xs"
                            :class="modalFilter === 'deadline' ? 'bg-purple-600 text-white shadow-xs' : 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-100'">
                        Ada Tenggat
                    </button>
                </div>
            </div>

            <!-- MODAL BODY: DAFTAR TUGAS LENGKAP -->
            <div class="p-5 overflow-y-auto flex-1 space-y-3 custom-scrollbar">
                
                <!-- Empty State jika tidak ada tugas yang cocok -->
                <template x-if="filteredModalTasks().length === 0">
                    <div class="py-12 text-center text-slate-400">
                        <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p class="text-sm font-bold text-slate-600">Tidak ada tugas ditemukan</p>
                        <p class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian atau filter Anda.</p>
                    </div>
                </template>

                <!-- List Kartu Tugas Pop-up -->
                <template x-for="task in filteredModalTasks()" :key="task.id">
                    <div class="p-4 bg-slate-50 hover:bg-white rounded-2xl border border-slate-200/90 shadow-xs hover:shadow-md transition-all">
                        
                        <!-- Header Kartu -->
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" :class="task.circle_color"></span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded tracking-wide"
                                      :class="task.is_direct ? 'bg-blue-100 text-blue-800 border border-blue-200' : 'bg-slate-100 text-slate-700 border border-slate-200'"
                                      x-text="task.is_direct ? 'Tugas Langsung Kabid' : 'Disposisi Surat Masuk'">
                                </span>
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded border"
                                      :class="task.status_class"
                                      x-text="task.status_label">
                                </span>
                            </div>
                            <span class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full uppercase tracking-wider"
                                  :class="task.urgency_color"
                                  x-text="task.urgency">
                            </span>
                        </div>

                        <!-- Judul -->
                        <h4 class="text-sm font-bold text-slate-900 leading-snug" x-text="task.title"></h4>

                        <!-- Perintah Kabid -->
                        <template x-if="task.catatan_kabid">
                            <div class="mt-2.5 p-2.5 bg-amber-50 rounded-xl border border-amber-200 text-xs text-amber-950 flex items-start gap-2">
                                <svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                                <div>
                                    <span class="font-extrabold text-amber-900 block text-[10px] uppercase tracking-wider">Perintah / Arahan Kabid:</span>
                                    <p class="font-medium text-slate-800 mt-0.5" x-text="task.catatan_kabid"></p>
                                </div>
                            </div>
                        </template>

                        <!-- Nomor Surat & Asal Surat -->
                        <template x-if="!task.is_direct && task.nomor_surat && task.nomor_surat !== '-'">
                            <div class="mt-2 text-xs text-slate-600 flex items-center gap-2">
                                <span>No: <strong class="text-slate-800" x-text="task.nomor_surat"></strong></span>
                                <template x-if="task.asal_surat && task.asal_surat !== '-'">
                                    <span>&bull; Dari: <strong class="text-slate-800" x-text="task.asal_surat"></strong></span>
                                </template>
                            </div>
                        </template>

                        <!-- Footer Kartu -->
                        <div class="mt-3 pt-2.5 border-t border-slate-200/80 flex items-center justify-between text-xs text-slate-500">
                            <!-- Deadline -->
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 flex-shrink-0" :class="task.has_deadline ? 'text-rose-500' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <template x-if="task.has_deadline">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-bold text-slate-700">Batas Waktu:</span>
                                        <span class="font-bold text-slate-900" x-text="task.deadline_formatted"></span>
                                        <span class="text-[10px] px-2 py-0.5 rounded font-extrabold" :class="task.deadline_class" x-text="task.deadline_status"></span>
                                    </div>
                                </template>
                                <template x-if="!task.has_deadline">
                                    <span class="text-slate-400">Disposisi: <strong class="text-slate-600" x-text="task.disposisi_formatted || task.date"></strong></span>
                                </template>
                            </div>

                            <!-- Assignee -->
                            <template x-if="task.assignee">
                                <span class="bg-white border border-slate-200 px-2 py-0.5 rounded text-[11px] font-semibold text-slate-700">
                                    Ditugaskan: <span x-text="task.assignee"></span>
                                </span>
                            </template>
                        </div>

                    </div>
                </template>

            </div>

            <!-- MODAL FOOTER -->
            <div class="p-4 bg-slate-100 border-t border-slate-200 flex items-center justify-between">
                <span class="text-xs text-slate-500 font-medium">
                    Menampilkan <strong class="text-slate-800" x-text="filteredModalTasks().length"></strong> tugas
                </span>
                <div class="flex items-center gap-3">
                    <button type="button" @click="closeTaskModal()" 
                            class="px-4 py-2 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl shadow-xs transition">
                        Tutup
                    </button>
                    <template x-if="modalCol?.link && modalCol.link !== '#'">
                        <a :href="modalCol.link" 
                           class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow transition flex items-center gap-1.5">
                            <span>Buka Halaman Bidang</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </template>
                </div>
            </div>

        </div>

    </div>

    <!-- SCRIPT ALPINE.JS & ANALOG CLOCK & AUDIO CHIME -->
    <script>
        function dishubMonitorDashboard(config) {
            return {
                dataUrl: config.dataUrl,
                stats: config.initialStats,
                columns: config.initialColumns,
                knownTaskIds: config.initialTaskIds || [],

                // Jam Analog & Digital
                hourDegree: 0,
                minuteDegree: 0,
                secondDegree: 0,
                digitalTime: '',
                dayName: '',
                dateFormatted: '',
                clockInterval: null,

                // Audio Chime & Notifikasi
                soundEnabled: true,
                audioUnlocked: false,
                audioCtx: null,
                showToast: false,
                toastTitle: '',
                toastAssignee: '',
                toastTimer: null,

                // Modal Pop-up Lihat Semua Tugas
                modalOpen: false,
                modalCol: null,
                modalSearch: '',
                modalFilter: 'all',

                // Polling
                pollInterval: null,
                isRefreshing: false,

                initMonitor() {
                    // Inisialisasi Jam
                    this.updateClock();
                    this.clockInterval = setInterval(() => this.updateClock(), 1000);

                    // Inisialisasi Audio Context
                    try {
                        const AudioContext = window.AudioContext || window.webkitAudioContext;
                        if (AudioContext) {
                            this.audioCtx = new AudioContext();
                            if (this.audioCtx.state === 'running') {
                                this.audioUnlocked = true;
                            }
                        }
                    } catch (e) {
                        console.warn('Audio Context tidak didukung di browser ini', e);
                    }

                    // Polling data otomatis setiap 5 detik
                    this.pollInterval = setInterval(() => this.fetchData(), 5000);

                    // Event listener untuk unlock audio jika browser membatasi autoplay
                    const unlockHandler = () => {
                        this.unlockAudio();
                        window.removeEventListener('click', unlockHandler);
                        window.removeEventListener('keydown', unlockHandler);
                    };
                    window.addEventListener('click', unlockHandler, { once: true });
                    window.addEventListener('keydown', unlockHandler, { once: true });
                },

                updateClock() {
                    const now = new Date();
                    const hours = now.getHours();
                    const minutes = now.getMinutes();
                    const seconds = now.getSeconds();

                    // Derajat rotasi jarum jam analog
                    this.hourDegree = (hours % 12 + minutes / 60) * 30;
                    this.minuteDegree = (minutes + seconds / 60) * 6;
                    this.secondDegree = seconds * 6;

                    // Waktu Digital
                    const hStr = String(hours).padStart(2, '0');
                    const mStr = String(minutes).padStart(2, '0');
                    this.digitalTime = `${hStr}:${mStr}`;

                    // Hari & Tanggal
                    const dayOptions = { weekday: 'long' };
                    const dateOptions = { day: 'numeric', month: 'long', year: 'numeric' };
                    this.dayName = now.toLocaleDateString('id-ID', dayOptions);
                    this.dateFormatted = now.toLocaleDateString('id-ID', dateOptions);
                },

                unlockAudio() {
                    if (this.audioCtx && this.audioCtx.state === 'suspended') {
                        this.audioCtx.resume().then(() => {
                            this.audioUnlocked = true;
                        });
                    } else {
                        this.audioUnlocked = true;
                    }
                },

                toggleSound() {
                    this.soundEnabled = !this.soundEnabled;
                    if (this.soundEnabled) {
                        this.unlockAudio();
                        this.playChime();
                    }
                },

                playChime() {
                    if (!this.soundEnabled) return;

                    try {
                        if (!this.audioCtx) {
                            const AudioContext = window.AudioContext || window.webkitAudioContext;
                            this.audioCtx = new AudioContext();
                        }
                        if (this.audioCtx.state === 'suspended') {
                            this.audioCtx.resume();
                        }

                        const ctx = this.audioCtx;
                        const now = ctx.currentTime;

                        // Nada 1: C5 (523.25 Hz)
                        const osc1 = ctx.createOscillator();
                        const gain1 = ctx.createGain();
                        osc1.type = 'sine';
                        osc1.frequency.setValueAtTime(523.25, now);
                        gain1.gain.setValueAtTime(0.2, now);
                        gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
                        osc1.connect(gain1);
                        gain1.connect(ctx.destination);
                        osc1.start(now);
                        osc1.stop(now + 0.35);

                        // Nada 2: G5 (783.99 Hz)
                        const osc2 = ctx.createOscillator();
                        const gain2 = ctx.createGain();
                        osc2.type = 'sine';
                        osc2.frequency.setValueAtTime(783.99, now + 0.18);
                        gain2.gain.setValueAtTime(0.25, now + 0.18);
                        gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.65);
                        osc2.connect(gain2);
                        gain2.connect(ctx.destination);
                        osc2.start(now + 0.18);
                        osc2.stop(now + 0.65);

                    } catch (e) {
                        console.warn('Gagal memutar audio chime:', e);
                    }
                },

                async fetchData() {
                    this.isRefreshing = true;
                    try {
                        const response = await fetch(this.dataUrl, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!response.ok) {
                            this.isRefreshing = false;
                            return;
                        }

                        const result = await response.json();
                        if (result.status !== 'success') {
                            this.isRefreshing = false;
                            return;
                        }

                        // Cek apakah ada tugas baru masuk
                        const incomingIds = result.task_ids || [];
                        const newTasks = incomingIds.filter(id => !this.knownTaskIds.includes(id));

                        if (newTasks.length > 0) {
                            // Bunyikan lonceng dan tampilkan notifikasi toast!
                            this.playChime();

                            if (result.latest_task) {
                                this.toastTitle = result.latest_task.perihal || 'Tugas Baru Masuk';
                                this.toastAssignee = result.latest_task.penerima || 'Karyawan';
                                this.showToast = true;

                                if (this.toastTimer) clearTimeout(this.toastTimer);
                                this.toastTimer = setTimeout(() => {
                                    this.showToast = false;
                                }, 8000);
                            }
                        }

                        // Perbarui data reaktif
                        this.knownTaskIds = incomingIds;
                        this.stats = result.globalStats;
                        this.columns = result.columns;

                    } catch (err) {
                        console.error('Gagal mengambil data monitor:', err);
                    } finally {
                        setTimeout(() => this.isRefreshing = false, 500);
                    }
                },

                toggleFullScreen() {
                    if (!document.fullscreenElement) {
                        document.documentElement.requestFullscreen().catch(err => {
                            console.warn('Gagal masuk mode fullscreen:', err);
                        });
                    } else {
                        if (document.exitFullscreen) {
                            document.exitFullscreen();
                        }
                    }
                },

                // Modal Pop-up Handlers
                openTaskModal(col) {
                    this.modalCol = col;
                    this.modalSearch = '';
                    this.modalFilter = 'all';
                    this.modalOpen = true;
                },

                closeTaskModal() {
                    this.modalOpen = false;
                    this.modalCol = null;
                },

                filteredModalTasks() {
                    if (!this.modalCol || !this.modalCol.tasks) return [];
                    let list = this.modalCol.tasks;
                    if (this.modalFilter !== 'all') {
                        if (this.modalFilter === 'urgent') {
                            list = list.filter(t => t.urgency === 'Urgent' || t.urgency === 'Segera');
                        } else if (this.modalFilter === 'penting') {
                            list = list.filter(t => t.urgency === 'Penting');
                        } else if (this.modalFilter === 'deadline') {
                            list = list.filter(t => t.has_deadline);
                        }
                    }
                    if (this.modalSearch && this.modalSearch.trim() !== '') {
                        const q = this.modalSearch.toLowerCase();
                        list = list.filter(t => 
                            (t.title && t.title.toLowerCase().includes(q)) ||
                            (t.catatan_kabid && t.catatan_kabid.toLowerCase().includes(q)) ||
                            (t.nomor_surat && t.nomor_surat.toLowerCase().includes(q)) ||
                            (t.asal_surat && t.asal_surat.toLowerCase().includes(q)) ||
                            (t.assignee && t.assignee.toLowerCase().includes(q))
                        );
                    }
                    return list;
                }
            };
        }

        /**
         * Komponen Auto-Scroll Lembut Ping-Pong (Atas-Bawah) untuk Setiap Kartu Kolom
         * Mendukung akumulasi float-safe, jeda nyaman di batas atas/bawah, dan resume interaktif.
         */
        function columnAutoScroll() {
            return {
                isHovered: false,
                direction: 'down',
                scrollSpeed: 0.5, // Kecepatan gulir halus (pixel/frame)
                scrollPos: 0,
                pauseUntil: 0,
                pauseDuration: 2200, // Waktu jeda (ms) di atas dan di bawah agar nyaman dibaca
                rafId: null,
                wheelTimer: null,

                startScroll() {
                    this.scrollPos = 0;
                    this.pauseUntil = Date.now() + 2000;
                    
                    const animate = () => {
                        const el = this.$refs.scrollContainer;
                        if (!el) {
                            this.rafId = requestAnimationFrame(animate);
                            return;
                        }

                        const maxScroll = Math.max(0, el.scrollHeight - el.clientHeight);

                        // Gulir otomatis hanya jika isi tugas meluap dari border kartu
                        if (maxScroll > 6 && !this.isHovered) {
                            const now = Date.now();
                            if (now >= this.pauseUntil) {
                                if (this.direction === 'down') {
                                    this.scrollPos += this.scrollSpeed;
                                    if (this.scrollPos >= maxScroll) {
                                        this.scrollPos = maxScroll;
                                        el.scrollTop = Math.ceil(this.scrollPos);
                                        this.direction = 'up';
                                        this.pauseUntil = now + this.pauseDuration;
                                    } else {
                                        el.scrollTop = Math.round(this.scrollPos);
                                    }
                                } else if (this.direction === 'up') {
                                    this.scrollPos -= this.scrollSpeed;
                                    if (this.scrollPos <= 0) {
                                        this.scrollPos = 0;
                                        el.scrollTop = 0;
                                        this.direction = 'down';
                                        this.pauseUntil = now + this.pauseDuration;
                                    } else {
                                        el.scrollTop = Math.round(this.scrollPos);
                                    }
                                }
                            }
                        } else if (!this.isHovered && maxScroll <= 6) {
                            if (el.scrollTop !== 0) {
                                el.scrollTop = 0;
                                this.scrollPos = 0;
                            }
                        }

                        this.rafId = requestAnimationFrame(animate);
                    };

                    this.$nextTick(() => {
                        setTimeout(() => {
                            const el = this.$refs.scrollContainer;
                            if (el) this.scrollPos = el.scrollTop || 0;
                            this.rafId = requestAnimationFrame(animate);
                        }, 300);
                    });
                },

                pause() {
                    this.isHovered = true;
                    const el = this.$refs.scrollContainer;
                    if (el) this.scrollPos = el.scrollTop;
                },

                resume() {
                    this.isHovered = false;
                    const el = this.$refs.scrollContainer;
                    if (el) this.scrollPos = el.scrollTop;
                    this.pauseUntil = Date.now() + 1500; // jeda 1.5 detik sebelum bergerak lagi setelah kursor keluar
                },

                handleWheel() {
                    this.isHovered = true;
                    const el = this.$refs.scrollContainer;
                    if (el) this.scrollPos = el.scrollTop;
                    if (this.wheelTimer) clearTimeout(this.wheelTimer);
                    this.wheelTimer = setTimeout(() => {
                        this.isHovered = false;
                        const cEl = this.$refs.scrollContainer;
                        if (cEl) this.scrollPos = cEl.scrollTop;
                        this.pauseUntil = Date.now() + 2000;
                    }, 2000);
                }
            };
        }
    </script>
</body>
</html>
