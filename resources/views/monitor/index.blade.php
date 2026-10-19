<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor Penugasan - {{ $department->name }} | Dishub Kota Magelang</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS (Vite / CDN Fallback) -->
    @if(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        colors: {
                            brand: {
                                50: '#eff6ff',
                                500: '#3b82f6',
                                600: '#2563eb',
                                700: '#1d4ed8',
                                900: '#1e3a8a',
                            }
                        }
                    }
                }
            }
        </script>
        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @endif

    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.6);
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(59, 130, 246, 0.4);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(59, 130, 246, 0.7);
        }
        @keyframes pulse-ring {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.15); opacity: 0.3; }
            100% { transform: scale(0.95); opacity: 0.8; }
        }
        .pulse-live {
            animation: pulse-ring 2s infinite ease-in-out;
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen font-sans antialiased overflow-x-hidden selection:bg-blue-600 selection:text-white"
      x-data="monitorDashboard({
          dataUrl: '{{ route('monitor.bidang.data', ['department' => $department->slug, 'token' => $token]) }}',
          initialCounts: @js($counts),
          initialMembers: @js($members),
          initialUnassigned: @js($unassigned)
      })"
      x-init="initMonitor()">

    <!-- BACKGROUND GLOW EFFECTS -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-blue-600/10 rounded-full blur-3xl"></div>
        <div class="absolute top-1/3 -right-40 w-96 h-96 bg-indigo-600/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 left-1/3 w-96 h-96 bg-cyan-600/10 rounded-full blur-3xl"></div>
    </div>

    <!-- AUDIO NOTIFICATION PERMISSION BANNER -->
    <div x-show="!audioUnlocked" x-cloak
         class="bg-blue-600/90 text-white text-xs px-4 py-2 flex items-center justify-between border-b border-blue-400/30 backdrop-blur-md sticky top-0 z-50">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path></svg>
            <span>Klik tombol di kanan untuk mengaktifkan bunyi notifikasi tugas baru (Kebijakan Autoplay Browser).</span>
        </div>
        <button type="button" @click="unlockAudio()" class="px-3 py-1 bg-white text-blue-900 font-bold rounded-lg text-xs hover:bg-blue-50 transition shadow-sm">
            Aktifkan Suara Notifikasi
        </button>
    </div>

    <!-- NEW TASK TOAST NOTIFICATION -->
    <div x-show="showToast" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="-translate-y-8 opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="-translate-y-8 opacity-0"
         class="fixed top-6 right-6 z-50 max-w-md w-full bg-slate-900/95 border-2 border-emerald-500 rounded-2xl shadow-2xl shadow-emerald-500/20 p-4 backdrop-blur-xl flex items-start gap-3">
        <div class="p-2.5 bg-emerald-500/20 text-emerald-400 rounded-xl flex-shrink-0 animate-pulse">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400 bg-emerald-950/80 px-2 py-0.5 rounded border border-emerald-500/30">Tugas Baru Diterima</span>
                <span class="text-[10px] text-slate-400" x-text="toastTime">Baru saja</span>
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

    <!-- MAIN DISPLAY CONTAINER -->
    <div class="p-4 sm:p-6 lg:p-8 max-w-[1920px] mx-auto min-h-screen flex flex-col justify-between">

        <!-- HEADER SECTION -->
        <header class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 sm:p-5 backdrop-blur-xl shadow-2xl mb-6">
            <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
                
                <!-- Logo & Identitas Bidang -->
                <div class="flex items-center gap-4">
                    <img src="{{ asset('images/kota.png') }}" alt="Logo Pemkot Magelang" class="w-12 h-14 object-contain filter drop-shadow">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 bg-blue-500/20 text-blue-400 border border-blue-500/30 rounded text-[10px] font-bold tracking-wider uppercase">
                                MONITOR OPERASIONAL TV
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded text-[10px] font-semibold">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 pulse-live"></span>
                                REAL-TIME
                            </span>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight mt-1 flex items-center gap-2">
                            <span>{{ strtoupper($department->name) }}</span>
                        </h1>
                        <p class="text-xs text-slate-400">
                            Dinas Perhubungan Pemerintah Kota Magelang &bull; Papan Kendali Penugasan Karyawan
                        </p>
                    </div>
                </div>

                <!-- Clock & Control Buttons -->
                <div class="flex flex-wrap items-center gap-3 sm:gap-4 self-end xl:self-center">
                    
                    <!-- Digital Clock -->
                    <div class="bg-slate-950/80 border border-slate-800 px-4 py-2 rounded-xl text-right shadow-inner">
                        <div class="text-lg sm:text-xl font-mono font-bold text-cyan-400 tracking-wider" x-text="currentTime">
                            {{ \Carbon\Carbon::now('Asia/Jakarta')->format('H:i:s') }} WIB
                        </div>
                        <div class="text-[11px] text-slate-400 font-medium" x-text="currentDate">
                            {{ \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->translatedFormat('l, d F Y') }}
                        </div>
                    </div>

                    <!-- Sound Toggle Button -->
                    <button type="button" @click="toggleSound()" 
                            class="px-3 py-2.5 rounded-xl border transition flex items-center gap-2 text-xs font-semibold shadow-sm"
                            :class="soundEnabled ? 'bg-emerald-950/60 border-emerald-600/50 text-emerald-300 hover:bg-emerald-900/60' : 'bg-slate-800/80 border-slate-700 text-slate-400 hover:bg-slate-800'">
                        <template x-if="soundEnabled">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path></svg>
                        </template>
                        <template x-if="!soundEnabled">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"></path></svg>
                        </template>
                        <span x-text="soundEnabled ? 'Suara Aktif' : 'Suara Bisu'">Suara Aktif</span>
                    </button>

                    <!-- Test Chime Button -->
                    <button type="button" @click="playChime()" title="Tes Bunyi Notifikasi"
                            class="p-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-300 border border-slate-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path></svg>
                    </button>

                    <!-- Fullscreen Button -->
                    <button type="button" @click="toggleFullScreen()" title="Mode Layar Penuh (F11)"
                            class="p-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-300 border border-slate-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                    </button>
                </div>
            </div>

            <!-- METRIC CARDS / STAT ROW -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mt-5 pt-5 border-t border-slate-800/80">
                
                <!-- Card 1: Total Tugas Aktif -->
                <div class="bg-slate-950/60 border border-slate-800 rounded-xl p-3 flex flex-col justify-between">
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Tugas Aktif</span>
                    <div class="flex items-baseline justify-between mt-1">
                        <span class="text-2xl font-black text-white" x-text="counts.total">0</span>
                        <span class="text-[10px] text-blue-400 font-medium">Belum Selesai</span>
                    </div>
                </div>

                <!-- Card 2: Belum Dibaca -->
                <div class="bg-slate-950/60 border border-amber-900/30 rounded-xl p-3 flex flex-col justify-between">
                    <span class="text-[11px] font-semibold text-amber-400 uppercase tracking-wider">Belum Dibaca</span>
                    <div class="flex items-baseline justify-between mt-1">
                        <span class="text-2xl font-black text-amber-300" x-text="counts.belum_dibaca">0</span>
                        <span class="text-[10px] text-amber-400/80 font-medium">Baru Masuk</span>
                    </div>
                </div>

                <!-- Card 3: Sedang Dikerjakan -->
                <div class="bg-slate-950/60 border border-sky-900/30 rounded-xl p-3 flex flex-col justify-between">
                    <span class="text-[11px] font-semibold text-sky-400 uppercase tracking-wider">Dikerjakan</span>
                    <div class="flex items-baseline justify-between mt-1">
                        <span class="text-2xl font-black text-sky-300" x-text="counts.dikerjakan">0</span>
                        <span class="text-[10px] text-sky-400/80 font-medium">Proses</span>
                    </div>
                </div>

                <!-- Card 4: Menunggu Verifikasi Kabid -->
                <div class="bg-slate-950/60 border border-purple-900/30 rounded-xl p-3 flex flex-col justify-between">
                    <span class="text-[11px] font-semibold text-purple-400 uppercase tracking-wider">Verifikasi</span>
                    <div class="flex items-baseline justify-between mt-1">
                        <span class="text-2xl font-black text-purple-300" x-text="counts.menunggu_verifikasi">0</span>
                        <span class="text-[10px] text-purple-400/80 font-medium">Menunggu Kabid</span>
                    </div>
                </div>

                <!-- Card 5: Mendekati / Lewat Tenggat -->
                <div class="bg-slate-950/60 border rounded-xl p-3 flex flex-col justify-between transition-colors"
                     :class="counts.mendekati_tenggat > 0 ? 'border-red-600/60 bg-red-950/20' : 'border-slate-800'">
                    <span class="text-[11px] font-semibold text-red-400 uppercase tracking-wider flex items-center gap-1">
                        <template x-if="counts.mendekati_tenggat > 0">
                            <span class="w-2 h-2 rounded-full bg-red-500 animate-ping"></span>
                        </template>
                        Urgent / Tenggat
                    </span>
                    <div class="flex items-baseline justify-between mt-1">
                        <span class="text-2xl font-black text-red-400" x-text="counts.mendekati_tenggat">0</span>
                        <span class="text-[10px] text-red-400/80 font-medium">Perhatian</span>
                    </div>
                </div>

                <!-- Card 6: Belum Didisposisikan ke Staf -->
                <div class="bg-slate-950/60 border rounded-xl p-3 flex flex-col justify-between"
                     :class="counts.belum_diteruskan > 0 ? 'border-orange-500/40 bg-orange-950/20' : 'border-slate-800'">
                    <span class="text-[11px] font-semibold text-orange-400 uppercase tracking-wider">Belum Disposisi</span>
                    <div class="flex items-baseline justify-between mt-1">
                        <span class="text-2xl font-black text-orange-300" x-text="counts.belum_diteruskan">0</span>
                        <span class="text-[10px] text-orange-400/80 font-medium">Menunggu PIC</span>
                    </div>
                </div>

            </div>
        </header>

        <!-- MAIN KANBAN BOARD / TO-DO LIST PER KARYAWAN -->
        <main class="flex-1">

            <!-- SECTION: SURAT MENUNGGU PENUGASAN KABID (JIKA ADA) -->
            <template x-if="unassigned.length > 0">
                <div class="mb-6 bg-orange-950/30 border border-orange-500/40 rounded-2xl p-4 backdrop-blur-md">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="p-1 bg-orange-500/20 text-orange-400 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </span>
                            <h3 class="text-sm font-bold text-orange-300 uppercase tracking-wider">
                                Surat Masuk Bidang Belum Diteruskan ke Karyawan Pelaksana
                            </h3>
                        </div>
                        <span class="text-xs font-bold bg-orange-500/20 text-orange-300 px-2 py-0.5 rounded-full" x-text="unassigned.length + ' Surat'"></span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
                        <template x-for="task in unassigned" :key="task.id">
                            <div class="bg-slate-900/90 border border-orange-500/30 rounded-xl p-3.5 shadow-lg">
                                <div class="flex items-start justify-between gap-2">
                                    <span class="text-[10px] font-mono font-bold text-slate-300 truncate" x-text="task.nomor_surat"></span>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded uppercase font-bold"
                                          :class="task.sifat === 'rahasia' ? 'bg-red-950 text-red-300 border border-red-800' : (task.sifat === 'penting' ? 'bg-amber-950 text-amber-300 border border-amber-800' : 'bg-slate-800 text-slate-300')"
                                          x-text="task.sifat"></span>
                                </div>
                                <h4 class="text-xs font-bold text-white mt-1 line-clamp-2" x-text="task.perihal"></h4>
                                <div class="mt-2 pt-2 border-t border-slate-800 flex items-center justify-between text-[11px] text-slate-400">
                                    <span x-text="'Pengirim: ' + task.asal_surat" class="truncate max-w-[180px]"></span>
                                    <span class="text-orange-400 font-semibold text-[10px]">Menunggu Kabid</span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <!-- SECTION: KARTU TO-DO LIST PER KARYAWAN -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
                
                <template x-for="member in members" :key="member.id">
                    <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl flex flex-col backdrop-blur-xl shadow-xl overflow-hidden transition-all duration-300 hover:border-slate-700">
                        
                        <!-- Header Kartu Karyawan -->
                        <div class="p-4 bg-slate-950/60 border-b border-slate-800/80 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <!-- Avatar -->
                                <template x-if="member.avatar">
                                    <img :src="member.avatar" :alt="member.name" class="w-10 h-10 rounded-xl object-cover border border-slate-700 flex-shrink-0">
                                </template>
                                <template x-if="!member.avatar">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white font-bold flex items-center justify-center text-sm shadow flex-shrink-0"
                                         x-text="member.name.substring(0, 2).toUpperCase()">
                                    </div>
                                </template>

                                <!-- Nama & NIP -->
                                <div class="min-w-0">
                                    <h3 class="text-sm font-bold text-white truncate" x-text="member.name"></h3>
                                    <p class="text-[10px] text-slate-400 truncate" x-text="member.jabatan"></p>
                                </div>
                            </div>

                            <!-- Badge Jumlah Tugas -->
                            <div class="flex-shrink-0 text-right">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-extrabold shadow-inner"
                                      :class="member.total > 0 ? (member.total >= 4 ? 'bg-red-950/80 text-red-300 border border-red-800/60' : 'bg-blue-950/80 text-blue-300 border border-blue-800/60') : 'bg-slate-800/60 text-slate-400 border border-slate-700/60'"
                                      x-text="member.total + ' Tugas'">
                                </span>
                            </div>
                        </div>

                        <!-- Daftar Tugas Karyawan (Scrollable List) -->
                        <div class="p-3.5 space-y-3 flex-1 overflow-y-auto max-h-[600px] min-h-[160px]">
                            
                            <!-- Empty State: Jika Tidak Ada Tugas -->
                            <template x-if="member.tasks.length === 0">
                                <div class="h-full flex flex-col items-center justify-center text-center p-6 text-slate-500">
                                    <div class="w-10 h-10 rounded-full bg-emerald-500/10 text-emerald-400 flex items-center justify-center mb-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <p class="text-xs font-semibold text-slate-400">Tidak ada tanggungan</p>
                                    <p class="text-[10px] text-slate-500 mt-0.5">Semua tugas telah rampung</p>
                                </div>
                            </template>

                            <!-- Item Tugas -->
                            <template x-for="task in member.tasks" :key="task.id">
                                <div class="bg-slate-950/90 border rounded-xl p-3 shadow-md transition-all hover:bg-slate-950"
                                     :class="task.is_overdue ? 'border-red-600/70 shadow-red-900/10' : (task.is_today ? 'border-amber-500/70 shadow-amber-900/10' : 'border-slate-800/80')">
                                    
                                    <!-- Header Tugas: Nomor Surat & Badge Status -->
                                    <div class="flex items-start justify-between gap-1.5 mb-1.5">
                                        <span class="text-[10px] font-mono text-cyan-400 font-semibold truncate max-w-[140px]" x-text="task.nomor_surat"></span>
                                        
                                        <!-- Status Badge -->
                                        <span class="text-[9px] px-1.5 py-0.5 rounded font-extrabold uppercase tracking-wider whitespace-nowrap"
                                              :class="{
                                                  'bg-amber-950/90 text-amber-300 border border-amber-800/60': task.status === 'belum_dibaca',
                                                  'bg-blue-950/90 text-blue-300 border border-blue-800/60': task.status === 'dibaca' || task.status === 'dikerjakan',
                                                  'bg-purple-950/90 text-purple-300 border border-purple-800/60': task.status === 'menunggu_verifikasi_kabid',
                                                  'bg-rose-950/90 text-rose-300 border border-rose-800/60': task.status === 'perlu_revisi'
                                              }"
                                              x-text="task.status_label">
                                        </span>
                                    </div>

                                    <!-- Perihal Surat -->
                                    <h4 class="text-xs font-bold text-slate-100 line-clamp-2 leading-snug" x-text="task.perihal"></h4>

                                    <!-- Catatan / Arahan Kabid (Jika Ada) -->
                                    <template x-if="task.catatan_kabid">
                                        <div class="mt-2 bg-slate-900/80 border-l-2 border-blue-500 px-2 py-1 rounded text-[10px] text-slate-300 italic line-clamp-2">
                                            "<span x-text="task.catatan_kabid"></span>"
                                        </div>
                                    </template>

                                    <!-- Footer Tugas: Tenggat Waktu & Sifat -->
                                    <div class="mt-2.5 pt-2 border-t border-slate-900 flex items-center justify-between text-[10px]">
                                        
                                        <!-- Deadline Info -->
                                        <div class="flex items-center gap-1 font-semibold"
                                             :class="task.is_overdue ? 'text-red-400 font-bold' : (task.is_today ? 'text-amber-400 font-bold' : 'text-slate-400')">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span x-text="task.is_overdue ? 'Terlewat: ' + task.deadline : (task.is_today ? 'Tenggat Hari Ini!' : 'Tenggat: ' + task.deadline)"></span>
                                        </div>

                                        <!-- Asal Surat -->
                                        <span class="text-slate-500 truncate max-w-[100px]" x-text="task.asal_surat"></span>
                                    </div>

                                </div>
                            </template>

                        </div>

                    </div>
                </template>

            </div>

        </main>

        <!-- FOOTER INFO BAR -->
        <footer class="mt-6 pt-4 border-t border-slate-800/80 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
            <div class="flex items-center gap-4">
                <span>SIMAS Dishub Kota Magelang &copy; {{ date('Y') }}</span>
                <span>&bull;</span>
                <span class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Pembaruan data otomatis setiap 5 detik
                </span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[11px] text-slate-400">Sinkronisasi terakhir:</span>
                <span class="text-[11px] font-mono text-cyan-400 font-bold" x-text="lastSyncedTime">Baru saja</span>
            </div>
        </footer>

    </div>

    <!-- SCRIPT ALPINE.JS & AUDIO SYNTHESIZER -->
    <script>
        function monitorDashboard(config) {
            return {
                dataUrl: config.dataUrl,
                counts: config.initialCounts,
                members: config.initialMembers,
                unassigned: config.initialUnassigned,
                
                // Audio & Alert State
                soundEnabled: true,
                audioUnlocked: false,
                audioCtx: null,
                knownTaskIds: [],

                // Toast Notification
                showToast: false,
                toastTitle: '',
                toastAssignee: '',
                toastTime: 'Baru saja',
                toastTimer: null,

                // Clocks
                currentTime: '',
                currentDate: '',
                lastSyncedTime: 'Baru saja',
                timerInterval: null,
                pollInterval: null,

                initMonitor() {
                    // Extract initial known task IDs
                    this.extractInitialTaskIds();
                    
                    // Start clock
                    this.updateClock();
                    this.timerInterval = setInterval(() => this.updateClock(), 1000);

                    // Setup Audio Context
                    try {
                        const AudioContext = window.AudioContext || window.webkitAudioContext;
                        if (AudioContext) {
                            this.audioCtx = new AudioContext();
                            if (this.audioCtx.state === 'running') {
                                this.audioUnlocked = true;
                            }
                        }
                    } catch (e) {
                        console.warn('Web Audio API not supported on this browser', e);
                    }

                    // Start Realtime Polling (Every 5 seconds)
                    this.pollInterval = setInterval(() => this.fetchRealtimeData(), 5000);

                    // Event listener for user interaction to unlock audio
                    const unlockHandler = () => {
                        this.unlockAudio();
                        window.removeEventListener('click', unlockHandler);
                        window.removeEventListener('keydown', unlockHandler);
                    };
                    window.addEventListener('click', unlockHandler, { once: true });
                    window.addEventListener('keydown', unlockHandler, { once: true });
                },

                extractInitialTaskIds() {
                    let ids = [];
                    this.members.forEach(m => {
                        m.tasks.forEach(t => ids.push(t.id));
                    });
                    this.unassigned.forEach(t => ids.push(t.id));
                    this.knownTaskIds = ids;
                },

                updateClock() {
                    const now = new Date();
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    const seconds = String(now.getSeconds()).padStart(2, '0');
                    this.currentTime = `${hours}:${minutes}:${seconds} WIB`;

                    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                    this.currentDate = now.toLocaleDateString('id-ID', options);
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

                        // Tone 1: C5 (523.25 Hz)
                        const osc1 = ctx.createOscillator();
                        const gain1 = ctx.createGain();
                        osc1.type = 'sine';
                        osc1.frequency.setValueAtTime(523.25, now);
                        gain1.gain.setValueAtTime(0.25, now);
                        gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
                        osc1.connect(gain1);
                        gain1.connect(ctx.destination);
                        osc1.start(now);
                        osc1.stop(now + 0.35);

                        // Tone 2: G5 (783.99 Hz)
                        const osc2 = ctx.createOscillator();
                        const gain2 = ctx.createGain();
                        osc2.type = 'sine';
                        osc2.frequency.setValueAtTime(783.99, now + 0.18);
                        gain2.gain.setValueAtTime(0.3, now + 0.18);
                        gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.65);
                        osc2.connect(gain2);
                        gain2.connect(ctx.destination);
                        osc2.start(now + 0.18);
                        osc2.stop(now + 0.65);

                    } catch (e) {
                        console.warn('Audio chime playback failed:', e);
                    }
                },

                async fetchRealtimeData() {
                    try {
                        const response = await fetch(this.dataUrl, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!response.ok) return;

                        const result = await response.json();
                        if (result.status !== 'success') return;

                        // Check for new tasks
                        const incomingTaskIds = result.task_ids || [];
                        const newTasksFound = incomingTaskIds.filter(id => !this.knownTaskIds.includes(id));

                        if (newTasksFound.length > 0) {
                            // Trigger notification chime & toast!
                            this.playChime();

                            if (result.latest_task) {
                                this.toastTitle = result.latest_task.perihal || 'Tugas Baru Diterima';
                                this.toastAssignee = result.latest_task.penerima || 'Karyawan';
                                this.toastTime = 'Baru saja';
                                this.showToast = true;

                                if (this.toastTimer) clearTimeout(this.toastTimer);
                                this.toastTimer = setTimeout(() => {
                                    this.showToast = false;
                                }, 8000);
                            }
                        }

                        // Update reactive data
                        this.knownTaskIds = incomingTaskIds;
                        this.counts = result.counts;
                        this.members = result.members;
                        this.unassigned = result.unassigned;

                        const now = new Date();
                        const h = String(now.getHours()).padStart(2, '0');
                        const m = String(now.getMinutes()).padStart(2, '0');
                        const s = String(now.getSeconds()).padStart(2, '0');
                        this.lastSyncedTime = `${h}:${m}:${s} WIB`;

                    } catch (err) {
                        console.error('Error fetching monitor data:', err);
                    }
                },

                toggleFullScreen() {
                    if (!document.fullscreenElement) {
                        document.documentElement.requestFullscreen().catch(err => {
                            console.warn('Error enabling fullscreen mode:', err);
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
