<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-xl text-gray-900 dark:text-gray-100 leading-tight">
                            Dashboard Pegawai
                        </h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Pelaksana Disposisi &bull; <span class="font-medium text-gray-700 dark:text-gray-300">{{ auth()->user()->department?->name ?? 'Dinas Perhubungan' }}</span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('karyawan.tasks.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-xs font-semibold rounded-xl transition-all shadow-sm hover:shadow">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    Daftar Semua Tugas
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-ui.welcome-banner />

        {{-- Alert Notifikasi Tugas Mendesak / Mendekati Tenggat --}}
        @if(isset($tugasMendesak) && $tugasMendesak > 0)
            <div class="p-4 bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-transparent border-l-4 border-amber-500 dark:border-amber-400 bg-white dark:bg-gray-800 rounded-r-xl shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100">
                            Perhatian: Terdapat {{ $tugasMendesak }} Tugas Prioritas / Mendekati Batas Waktu
                        </h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">
                            Periksa detail instruksi disposisi dan segera perbarui laporan tindak lanjut pengerjaan.
                        </p>
                    </div>
                </div>
                <a href="{{ route('karyawan.tasks.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold rounded-lg transition-colors whitespace-nowrap self-start sm:self-auto shadow-xs">
                    Lihat Tugas Mendesak &rarr;
                </a>
            </div>
        @endif

        {{-- Statistik Ringkasan Tugas Karyawan --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Tugas Baru --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xs border border-gray-100 dark:border-gray-700/80 p-5 hover:border-blue-300 dark:hover:border-blue-700 transition-all flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Tugas Baru</span>
                    <span class="text-3xl font-extrabold text-blue-600 dark:text-blue-400 mt-1 block tracking-tight">{{ $tugasBaru }}</span>
                    <span class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5 block">Perlu dipelajari / dimulai</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>

            {{-- Sedang Dikerjakan --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xs border border-gray-100 dark:border-gray-700/80 p-5 hover:border-amber-300 dark:hover:border-amber-700 transition-all flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Sedang Dikerjakan</span>
                    <span class="text-3xl font-extrabold text-amber-600 dark:text-amber-400 mt-1 block tracking-tight">{{ $sedangDikerjakan }}</span>
                    <span class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5 block">Dalam proses koordinasi</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>

            {{-- Tugas Selesai --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xs border border-gray-100 dark:border-gray-700/80 p-5 hover:border-emerald-300 dark:hover:border-emerald-700 transition-all flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Tugas Selesai</span>
                    <span class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1 block tracking-tight">{{ $tugasSelesai }}</span>
                    <span class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5 block">Tuntas ditindaklanjuti</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>

            {{-- Total Riwayat Tugas --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xs border border-gray-100 dark:border-gray-700/80 p-5 hover:border-gray-300 dark:hover:border-gray-600 transition-all flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Penugasan</span>
                    <span class="text-3xl font-extrabold text-gray-800 dark:text-gray-100 mt-1 block tracking-tight">{{ $totalTugas }}</span>
                    <span class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5 block">Riwayat keseluruhan</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Tabel Tugas Disposisi Terbaru --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xs border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="font-bold text-base text-gray-900 dark:text-gray-100 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        Tugas Disposisi Terbaru
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Surat masuk yang ditugaskan kepada Anda atau unit kerja Anda.
                    </p>
                </div>
                <a href="{{ route('karyawan.tasks.index') }}"
                   class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 inline-flex items-center gap-1">
                    <span>Lihat Semua Tugas</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
                        <tr>
                            <th class="px-5 py-3.5">No. Agenda / Surat</th>
                            <th class="px-5 py-3.5">Perihal & Pengirim</th>
                            <th class="px-5 py-3.5">Sifat</th>
                            <th class="px-5 py-3.5">Tgl. Disposisi / Batas Waktu</th>
                            <th class="px-5 py-3.5 text-center">Status</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($recentTasks as $task)
                            @php
                                $letter = $task->incomingLetter;
                                $sifatColor = match($letter?->sifat) {
                                    'Sangat Segera', 'Segera' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300 border-rose-200 dark:border-rose-800',
                                    'Penting' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                                    'Rahasia' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300 border-purple-200 dark:border-purple-800',
                                    default => 'bg-gray-100 text-gray-700 dark:bg-gray-700/60 dark:text-gray-300 border-gray-200 dark:border-gray-600'
                                };

                                $statusBadge = match($task->status) {
                                    'belum_dibaca' => ['class' => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-600', 'label' => 'Belum Dibaca'],
                                    'dibaca'       => ['class' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 border-blue-200 dark:border-blue-800', 'label' => 'Dibaca'],
                                    'dikerjakan'   => ['class' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300 border-amber-200 dark:border-amber-800', 'label' => 'Dikerjakan'],
                                    'selesai'      => ['class' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800', 'label' => 'Selesai'],
                                    default        => ['class' => 'bg-gray-100 text-gray-700 border-gray-200', 'label' => $task->status]
                                };

                                $isOverdue = $task->deadline && $task->status !== 'selesai' && $task->deadline->isPast();
                                $isNearDeadline = $task->deadline && $task->status !== 'selesai' && !$isOverdue && $task->deadline->diffInDays(now()) <= 2;
                            @endphp
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/40 transition-colors">
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="font-bold text-gray-900 dark:text-gray-100">
                                        {{ $letter?->nomor_agenda ? '#' . $letter->nomor_agenda : '-' }}
                                    </div>
                                    <div class="text-xs font-mono text-gray-500 dark:text-gray-400 mt-0.5">
                                        {{ $letter?->nomor_surat ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-5 py-4 max-w-xs">
                                    <p class="font-semibold text-gray-900 dark:text-gray-100 truncate" title="{{ $letter?->perihal }}">
                                        {{ $letter?->perihal ?? 'Tanpa Perihal' }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5" title="{{ $letter?->asal_surat }}">
                                        Asal: {{ $letter?->asal_surat ?? '-' }}
                                    </p>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 text-[11px] font-semibold rounded-full border {{ $sifatColor }}">
                                        {{ $letter?->sifat ?? 'Biasa' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-xs">
                                    <div class="text-gray-700 dark:text-gray-300">
                                        {{ $task->tanggal_disposisi ? $task->tanggal_disposisi->translatedFormat('d M Y') : '-' }}
                                    </div>
                                    @if($task->deadline)
                                        <div class="mt-0.5">
                                            @if($isOverdue)
                                                <span class="text-[11px] font-semibold text-red-600 dark:text-red-400 flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-red-600 animate-ping"></span>
                                                    Terlewat: {{ $task->deadline->translatedFormat('d M Y') }}
                                                </span>
                                            @elseif($isNearDeadline)
                                                <span class="text-[11px] font-semibold text-amber-600 dark:text-amber-400 flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                    Batas: {{ $task->deadline->translatedFormat('d M Y') }}
                                                </span>
                                            @else
                                                <span class="text-[11px] text-gray-400 dark:text-gray-500">
                                                    Batas: {{ $task->deadline->translatedFormat('d M Y') }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ $statusBadge['class'] }}">
                                        {{ $statusBadge['label'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    <a href="{{ route('karyawan.tasks.show', $task) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-all shadow-xs hover:shadow">
                                        <span>Buka Tugas</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-gray-400 dark:text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-400 flex items-center justify-center mb-3">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <p class="font-medium text-sm text-gray-700 dark:text-gray-300">Belum ada tugas disposisi</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Saat surat baru didisposisikan kepada Anda, tugas akan tampil di sini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
