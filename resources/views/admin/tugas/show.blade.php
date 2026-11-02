<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mb-2">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
                    <span>/</span>
                    <a href="{{ route('admin.tugas.index') }}" class="hover:text-blue-600 transition-colors">Tugas Langsung Kabid</a>
                    <span>/</span>
                    <span class="text-gray-800 dark:text-gray-200 font-semibold">{{ $task->nomor_referensi }}</span>
                </nav>
                <div class="flex items-center gap-3 flex-wrap">
                    <h2 class="font-bold text-xl text-gray-900 dark:text-gray-100 leading-tight">
                        {{ $task->judul_tugas }}
                    </h2>
                    @php
                        $sifatBadge = match($task->sifat) {
                            'Penting'       => 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-900/30 dark:text-amber-400',
                            'Segera'        => 'bg-orange-100 text-orange-800 border-orange-200 dark:bg-orange-900/30 dark:text-orange-400',
                            'Sangat Segera' => 'bg-rose-100 text-rose-800 border-rose-200 dark:bg-rose-900/30 dark:text-rose-400 font-bold',
                            'Rahasia'       => 'bg-purple-100 text-purple-800 border-purple-200 dark:bg-purple-900/30 dark:text-purple-400',
                            default         => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-gray-700 dark:text-gray-300',
                        };

                        $statusBadge = match($task->status) {
                            'belum_dibaca'            => ['label' => 'Belum Dibaca', 'class' => 'bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-700 dark:text-gray-300'],
                            'dibaca'                  => ['label' => 'Dilihat', 'class' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:text-blue-300'],
                            'dikerjakan'              => ['label' => 'Sedang Dikerjakan', 'class' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/30 dark:text-amber-300'],
                            'menunggu_verifikasi_kabid'=> ['label' => 'Menunggu Verifikasi', 'class' => 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-900/30 dark:text-purple-300 font-semibold'],
                            'perlu_revisi'            => ['label' => 'Perlu Revisi', 'class' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-900/30 dark:text-rose-300'],
                            'selesai'                 => ['label' => 'Selesai', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 font-semibold'],
                            default                   => ['label' => ucfirst($task->status), 'class' => 'bg-gray-100 text-gray-700 border-gray-200'],
                        };
                    @endphp
                    <span class="text-xs px-2.5 py-1 rounded-md border font-semibold {{ $sifatBadge }}">
                        {{ $task->sifat }}
                    </span>
                    <span class="text-xs px-2.5 py-1 rounded-full border font-semibold {{ $statusBadge['class'] }}">
                        {{ $statusBadge['label'] }}
                    </span>
                </div>
            </div>
            <div>
                <a href="{{ route('admin.tugas.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 text-xs font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Daftar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <x-ui.alert type="success" :message="session('success')" />
        @endif
        @if(session('error'))
            <x-ui.alert type="error" :message="session('error')" />
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            {{-- KOLOM KIRI (KONTEN UTAMA TUGAS) --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Detail Instruksi Kabid --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xs border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                            <h3 class="font-bold text-sm text-gray-900 dark:text-gray-100">Instruksi & Arahan Kepala Bidang</h3>
                        </div>
                        <span class="text-xs text-gray-400">Diberikan: {{ $task->created_at->translatedFormat('d F Y, H:i') }}</span>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 border border-gray-100 dark:border-gray-700/60 text-sm text-gray-800 dark:text-gray-200 leading-relaxed whitespace-pre-line">
                        {{ $task->catatan_kabid }}
                    </div>

                    {{-- Lampiran Petunjuk jika ada --}}
                    @if($task->file_tugas)
                        <div class="pt-2">
                            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-2">Berkas Petunjuk / Lampiran Kabid:</p>
                            <a href="{{ route('tasks.reference-file', $task) }}" target="_blank"
                               class="inline-flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 text-xs font-semibold hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                                <span>Buka / Unduh Berkas Petunjuk</span>
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Catatan Revisi jika ada --}}
                @if($task->catatan_revisi)
                    <div class="bg-rose-50 dark:bg-rose-950/20 rounded-2xl shadow-xs border border-rose-200 dark:border-rose-900/50 p-6 space-y-3">
                        <div class="flex items-center gap-2 text-rose-700 dark:text-rose-400 font-bold text-sm">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <span>Catatan Evaluasi / Arahan Revisi Kabid</span>
                        </div>
                        <p class="text-xs text-rose-800 dark:text-rose-300 leading-relaxed whitespace-pre-line pl-7">
                            {{ $task->catatan_revisi }}
                        </p>
                    </div>
                @endif

                {{-- Laporan Hasil Tindak Lanjut dari Staf --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xs border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full {{ in_array($task->status, ['menunggu_verifikasi_kabid', 'selesai']) ? 'bg-emerald-500' : 'bg-gray-300' }}"></span>
                            <h3 class="font-bold text-sm text-gray-900 dark:text-gray-100">Hasil Tindak Lanjut Staf Pelaksana</h3>
                        </div>
                        @if($task->tanggal_selesai)
                            <span class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold">
                                Diselesaikan: {{ \Carbon\Carbon::parse($task->tanggal_selesai)->translatedFormat('d F Y') }}
                            </span>
                        @endif
                    </div>

                    @if($task->catatan_tindak_lanjut || $task->file_tindak_lanjut)
                        <div class="space-y-4">
                            @if($task->catatan_tindak_lanjut)
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1.5">Laporan / Keterangan Staf:</p>
                                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 border border-gray-100 dark:border-gray-700/60 text-sm text-gray-800 dark:text-gray-200 leading-relaxed whitespace-pre-line">
                                        {{ $task->catatan_tindak_lanjut }}
                                    </div>
                                </div>
                            @endif

                            @if($task->file_tindak_lanjut)
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1.5">File Bukti Tindak Lanjut:</p>
                                    <a href="{{ route('tasks.file', $task) }}" target="_blank"
                                       class="inline-flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-colors">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span>Buka / Unduh Berkas Hasil</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="py-8 text-center text-gray-400 dark:text-gray-500 text-xs">
                            <svg class="w-10 h-10 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p>Staf pelaksana belum mengirimkan laporan hasil pengerjaan.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- KOLOM KANAN (SIDEBAR INFORMASI & AKSI ADMIN) --}}
            <div class="space-y-6">
                {{-- Info Detail Penugasan --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xs border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                    <h3 class="font-bold text-sm text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-gray-700 pb-3">
                        Informasi Penugasan
                    </h3>

                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Nomor Referensi:</span>
                            <span class="font-mono font-bold text-gray-700 dark:text-gray-300">{{ $task->nomor_referensi }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Bidang / Bagian:</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $task->department?->name ?? 'Semua Bidang' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Pemberi Tugas:</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $task->creator?->name ?? 'Kepala Bidang' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Staf Pelaksana:</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">
                                {{ $task->user?->name ?? 'Belum Ditugaskan' }}
                                @if($task->user?->badge)
                                    <span class="text-[10px] px-1 py-0.2 rounded bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                        {{ $task->user->badge }}
                                    </span>
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Tanggal Ditugaskan:</span>
                            <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $task->created_at->translatedFormat('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Batas Waktu (Deadline):</span>
                            @if($task->deadline)
                                @php
                                    $isOverdue = $task->status !== 'selesai' && now()->startOfDay()->gt($task->deadline->startOfDay());
                                @endphp
                                <span class="font-semibold {{ $isOverdue ? 'text-rose-600 font-bold' : 'text-gray-700 dark:text-gray-300' }}">
                                    {{ $task->deadline->translatedFormat('d M Y') }}
                                    @if($isOverdue)
                                        (Terlewat)
                                    @endif
                                </span>
                            @else
                                <span class="text-gray-400 font-normal">Tidak ditentukan</span>
                            @endif
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-gray-100 dark:border-gray-700">
                            <span class="text-gray-400">Status Saat Ini:</span>
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold border {{ $statusBadge['class'] }}">
                                {{ $statusBadge['label'] }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Action Card: Hapus Tugas oleh Admin --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xs border border-rose-100 dark:border-rose-900/30 p-6 space-y-3">
                    <div class="flex items-center gap-2 text-rose-600 dark:text-rose-400">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        <h4 class="font-bold text-xs">Hapus Penugasan</h4>
                    </div>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 leading-relaxed">
                        Sebagai Admin, Anda dapat menghapus tugas langsung ini. Seluruh data penugasan, instruksi, dan berkas lampiran terkait akan dibersihkan secara permanen.
                    </p>
                    <form action="{{ route('admin.tugas.destroy', $task) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tugas ini secara permanen? Data yang dihapus tidak dapat dipulihkan.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-xl transition-all shadow-xs flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            <span>Hapus Tugas Langsung</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
