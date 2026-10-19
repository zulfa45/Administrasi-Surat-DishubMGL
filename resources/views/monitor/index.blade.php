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
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
        }
        /* Custom Scrollbars */
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
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
    </style>
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen flex flex-col justify-between antialiased selection:bg-blue-600 selection:text-white"
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

    <!-- MAIN WRAPPER -->
    <div class="flex-1 flex flex-col justify-between">
        
        <!-- 1. TOP HEADER SECTION (IDENTIK MOCKUP DISHUB) -->
        <header class="bg-[#0b1d33] text-white relative overflow-hidden shadow-lg border-b border-slate-800">
            
            <!-- WATERMARK SILHOUETTE GEDUNG DISHUB -->
            <div class="absolute inset-0 pointer-events-none opacity-10 flex items-center justify-center">
                <svg class="w-full h-full object-cover" viewBox="0 0 800 200" fill="currentColor">
                    <rect x="100" y="40" width="600" height="140" rx="4"/>
                    <rect x="140" y="60" width="40" height="30" fill="#000"/>
                    <rect x="200" y="60" width="40" height="30" fill="#000"/>
                    <rect x="260" y="60" width="40" height="30" fill="#000"/>
                    <rect x="320" y="60" width="40" height="30" fill="#000"/>
                    <rect x="380" y="60" width="40" height="30" fill="#000"/>
                    <rect x="440" y="60" width="40" height="30" fill="#000"/>
                    <rect x="500" y="60" width="40" height="30" fill="#000"/>
                    <rect x="560" y="60" width="40" height="30" fill="#000"/>
                    <rect x="620" y="60" width="40" height="30" fill="#000"/>
                    <rect x="140" y="110" width="40" height="40" fill="#000"/>
                    <rect x="200" y="110" width="40" height="40" fill="#000"/>
                    <rect x="260" y="110" width="40" height="40" fill="#000"/>
                    <rect x="320" y="110" width="40" height="40" fill="#000"/>
                    <rect x="380" y="110" width="40" height="70" fill="#000"/>
                    <rect x="440" y="110" width="40" height="40" fill="#000"/>
                    <rect x="500" y="110" width="40" height="40" fill="#000"/>
                    <rect x="560" y="110" width="40" height="40" fill="#000"/>
                    <rect x="620" y="110" width="40" height="40" fill="#000"/>
                </svg>
            </div>

            <div class="max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8 py-4 relative z-10">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    
                    <!-- KIRI: LOGO KOTA MAGELANG & TITLE -->
                    <div class="flex items-center gap-4">
                        <img src="{{ asset('images/kota.png') }}" alt="Logo Kota Magelang" class="w-14 sm:w-16 h-auto object-contain filter drop-shadow">
                        <div>
                            <h2 class="text-sm sm:text-base font-semibold text-slate-200 tracking-wide leading-tight">
                                Dinas Perhubungan
                            </h2>
                            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight leading-tight">
                                Kota Magelang
                            </h1>
                            <p class="text-xs sm:text-sm text-slate-300 font-medium mt-0.5">
                                Sistem Informasi Manajemen Surat
                            </p>
                            <p class="text-[11px] text-slate-400 font-normal">
                                Monitoring Tugas Disposisi & Tindak Lanjut
                            </p>
                        </div>
                    </div>

                    <!-- TENGAH: SLOGAN RESMI DISHUB -->
                    <div class="hidden xl:flex flex-col items-center justify-center text-center px-6">
                        <p class="text-sm font-medium text-slate-200 italic max-w-xs leading-relaxed">
                            Melayani dengan Integritas untuk Transportasi yang Lebih Baik
                        </p>
                        <div class="w-20 h-0.5 bg-amber-400 mt-1.5 rounded-full"></div>
                    </div>

                    <!-- KANAN: JAM ANALOG + WAKTU DIGITAL & TANGGAL -->
                    <div class="flex items-center gap-4 sm:gap-6 self-end lg:self-center">
                        
                        <!-- JAM ANALOG (SVG DIAL DENGAN JARUM BERGERAK) -->
                        <div class="relative w-20 h-20 bg-white rounded-full border-2 border-slate-300 shadow-lg flex items-center justify-center flex-shrink-0">
                            <!-- Angka Jam -->
                            <span class="absolute top-1 text-[9px] font-bold text-slate-700">12</span>
                            <span class="absolute right-1.5 text-[9px] font-bold text-slate-700">3</span>
                            <span class="absolute bottom-1 text-[9px] font-bold text-slate-700">6</span>
                            <span class="absolute left-1.5 text-[9px] font-bold text-slate-700">9</span>
                            
                            <!-- Titik Poros Tengah -->
                            <div class="w-2 h-2 bg-slate-900 rounded-full z-20"></div>

                            <!-- Jarum Jam -->
                            <div class="absolute w-1 bg-slate-900 rounded-full origin-bottom z-10"
                                 style="height: 22px; bottom: 50%;"
                                 :style="`transform: rotate(${hourDegree}deg);`"></div>

                            <!-- Jarum Menit -->
                            <div class="absolute w-0.5 bg-slate-700 rounded-full origin-bottom z-10"
                                 style="height: 30px; bottom: 50%;"
                                 :style="`transform: rotate(${minuteDegree}deg);`"></div>

                            <!-- Jarum Detik -->
                            <div class="absolute w-[1.5px] bg-red-600 rounded-full origin-bottom z-15"
                                 style="height: 32px; bottom: 50%;"
                                 :style="`transform: rotate(${secondDegree}deg);`"></div>
                        </div>

                        <!-- JAM DIGITAL & TANGGAL -->
                        <div class="text-right">
                            <div class="text-xs font-semibold text-slate-300" x-text="dayName">
                                {{ \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->translatedFormat('l') }}
                            </div>
                            <div class="text-xs font-medium text-slate-400" x-text="dateFormatted">
                                {{ \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->translatedFormat('d F Y') }}
                            </div>
                            <div class="flex items-baseline justify-end gap-1.5 mt-0.5">
                                <span class="text-2xl sm:text-3xl font-extrabold text-white tracking-wider font-mono" x-text="digitalTime">
                                    {{ \Carbon\Carbon::now('Asia/Jakarta')->format('H:i') }}
                                </span>
                                <span class="text-xs font-bold text-slate-400">WIB</span>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </header>

        <!-- CONTAINER BODY KONTEN -->
        <div class="max-w-[1920px] w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6 flex-1">

            <!-- 2. TOP METRIC CARDS (4 KARTU IDENTIK MOCKUP) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                
                <!-- CARD 1: TOTAL SURAT MASUK (BIRU) -->
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/90 flex items-center justify-between transition-transform hover:-translate-y-0.5">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-md shadow-blue-500/20">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Total Surat Masuk</span>
                            <div class="flex items-baseline gap-2 mt-1">
                                <span class="text-3xl font-black text-slate-900" x-text="stats.total_surat">48</span>
                                <span class="inline-flex items-center text-xs font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded" x-text="stats.trend_surat">
                                    ↑ 12%
                                </span>
                            </div>
                            <span class="text-[11px] text-slate-400 mt-0.5 block">dari minggu lalu</span>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: TOTAL DISPOSISI (UNGU) -->
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/90 flex items-center justify-between transition-transform hover:-translate-y-0.5">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl bg-purple-600 text-white flex items-center justify-center shadow-md shadow-purple-500/20">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Total Disposisi</span>
                            <div class="flex items-baseline gap-2 mt-1">
                                <span class="text-3xl font-black text-slate-900" x-text="stats.total_disposisi">46</span>
                                <span class="inline-flex items-center text-xs font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded" x-text="stats.trend_disposisi">
                                    ↑ 8%
                                </span>
                            </div>
                            <span class="text-[11px] text-slate-400 mt-0.5 block">dari minggu lalu</span>
                        </div>
                    </div>
                </div>

                <!-- CARD 3: TUGAS BELUM SELESAI (KUNING / ORANGE) -->
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/90 flex items-center justify-between transition-transform hover:-translate-y-0.5">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-md shadow-amber-500/20">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Tugas Belum Selesai</span>
                            <div class="flex items-baseline gap-2 mt-1">
                                <span class="text-3xl font-black text-slate-900" x-text="stats.belum_selesai">18</span>
                                <span class="inline-flex items-center text-xs font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded" x-text="stats.trend_belum">
                                    ↓ 25%
                                </span>
                            </div>
                            <span class="text-[11px] text-slate-400 mt-0.5 block">dari minggu lalu</span>
                        </div>
                    </div>
                </div>

                <!-- CARD 4: TUGAS SELESAI (HIJAU) -->
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/90 flex items-center justify-between transition-transform hover:-translate-y-0.5">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-md shadow-emerald-500/20">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Tugas Selesai</span>
                            <div class="flex items-baseline gap-2 mt-1">
                                <span class="text-3xl font-black text-slate-900" x-text="stats.selesai">28</span>
                                <span class="inline-flex items-center text-xs font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded" x-text="stats.trend_selesai">
                                    ↑ 17%
                                </span>
                            </div>
                            <span class="text-[11px] text-slate-400 mt-0.5 block">dari minggu lalu</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- 3. SUBHEADER: TITLE "PER BIDANG" & INDIKATOR DATA REAL-TIME -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-2">
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <h3 class="text-lg font-extrabold text-slate-800 tracking-tight">
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
                <div class="flex items-center gap-4">
                    
                    <!-- Indikator Data Real-time -->
                    <div class="flex items-center gap-2 text-xs font-bold text-slate-700">
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

            <!-- 4. KOLOM KANBAN KARTU TUGAS PER BIDANG (IDENTIK PERSIS DENGAN GAMBAR) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 items-start">
                
                <template x-for="col in columns" :key="col.id">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/90 overflow-hidden flex flex-col justify-between transition-all duration-300 hover:shadow-md">
                        
                        <!-- HEADER KARTU (WARNA IDENTIK PER BIDANG DENGAN ICON) -->
                        <div class="p-3 text-white flex items-center justify-between shadow-sm cursor-pointer"
                             :class="col.theme.bg"
                             @click="window.location.href = col.link">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <!-- Ikon Bulat -->
                                <div class="w-8 h-8 rounded-full bg-white/20 backdrop-blur-xs flex items-center justify-center flex-shrink-0">
                                    <template x-if="col.theme.icon === 'bus'">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8m-8 4h8m-9 8h10a2 2 0 002-2V6a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2zm2-2a1 1 0 100-2 1 1 0 000 2zm6 0a1 1 0 100-2 1 1 0 000 2z"></path></svg>
                                    </template>
                                    <template x-if="col.theme.icon === 'truck'">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
                                    </template>
                                    <template x-if="col.theme.icon === 'cog'">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </template>
                                    <template x-if="col.theme.icon === 'archive'">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                    </template>
                                    <template x-if="col.theme.icon === 'chart'">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                    </template>
                                    <template x-if="col.theme.icon === 'shield'">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                    </template>
                                    <template x-if="col.theme.icon === 'clock'">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
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

                        <!-- BODY: DAFTAR TUGAS -->
                        <div class="p-3.5 flex-1 flex flex-col justify-between min-h-[380px]">
                            
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-3">
                                    Daftar Tugas
                                </span>

                                <!-- List Item Tugas -->
                                <div class="space-y-3.5">
                                    
                                    <!-- Empty State jika 0 tugas -->
                                    <template x-if="col.tasks.length === 0">
                                        <div class="py-12 text-center text-slate-400">
                                            <svg class="w-8 h-8 mx-auto text-emerald-500 mb-2 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            <p class="text-xs font-semibold text-slate-500">Semua Tugas Selesai</p>
                                        </div>
                                    </template>

                                    <!-- Tiap Item Tugas -->
                                    <template x-for="task in col.tasks" :key="task.id">
                                        <div class="flex items-start gap-2.5 group">
                                            
                                            <!-- Bullet Bulatan Kosong (Sesuai Mockup) -->
                                            <div class="w-3.5 h-3.5 rounded-full border-2 flex-shrink-0 mt-0.5"
                                                 :class="task.circle_color">
                                            </div>

                                            <!-- Konten Tugas -->
                                            <div class="flex-1 min-w-0">
                                                <h5 class="text-xs font-semibold text-slate-800 line-clamp-2 leading-snug group-hover:text-blue-600 transition-colors"
                                                    x-text="task.title">
                                                    Judul Perihal Surat
                                                </h5>
                                                
                                                <!-- Row Badge Urgensi & Tanggal -->
                                                <div class="flex items-center gap-2 mt-1.5">
                                                    <!-- Badge Urgensi (Urgent / Penting / Normal) -->
                                                    <span class="text-[9px] font-bold px-2 py-0.5 rounded-full"
                                                          :class="task.urgency_color"
                                                          x-text="task.urgency">
                                                        Normal
                                                    </span>

                                                    <!-- Tanggal -->
                                                    <span class="text-[11px] text-slate-400 font-medium" x-text="task.date">
                                                        20 Okt 2026
                                                    </span>
                                                </div>
                                            </div>

                                        </div>
                                    </template>

                                </div>
                            </div>

                            <!-- FOOTER LINK: LIHAT SEMUA > -->
                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                                <a :href="col.link" class="text-blue-600 hover:text-blue-800 font-semibold flex items-center justify-between w-full">
                                    <span>Lihat Semua</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </div>

                        </div>

                    </div>
                </template>

            </div>

        </div>

        <!-- 5. BOTTOM FOOTER SECTION (IDENTIK MOCKUP DISHUB) -->
        <footer class="bg-[#0b1d33] text-white py-3 px-4 sm:px-8 border-t border-slate-800 shadow-inner">
            <div class="max-w-[1920px] mx-auto flex flex-col sm:flex-row items-center justify-between gap-2 text-xs">
                
                <!-- KIRI: IKON GEDUNG + MOTTO DISHUB -->
                <div class="flex items-center gap-2 text-slate-300">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m3-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <span class="font-medium italic">"Disiplin dalam bekerja, pelayanan semakin baik"</span>
                </div>

                <!-- KANAN: GARIS BIRU + NAMA INSTANSI -->
                <div class="flex items-center gap-3">
                    <div class="w-12 h-0.5 bg-blue-500 rounded-full"></div>
                    <span class="font-semibold text-slate-200">Dinas Perhubungan Kota Magelang</span>
                </div>

            </div>
        </footer>

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
                }
            };
        }
    </script>
</body>
</html>
