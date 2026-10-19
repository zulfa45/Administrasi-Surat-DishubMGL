<x-app-layout>
    <x-slot name="header">
        <div class="space-y-2">
            <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                <a href="{{ route('kabid.dashboard') }}" class="hover:text-emerald-600 transition-colors flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Dashboard
                </a>
                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                <a href="{{ route('kabid.tugas.index') }}" class="hover:text-emerald-600 transition-colors">Tugas Langsung</a>
                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                <span class="text-gray-800 dark:text-gray-200 font-semibold truncate max-w-xs">
                    #TUGAS-{{ str_pad($task->id, 4, '0', STR_PAD_LEFT) }}
                </span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex flex-wrap items-center gap-2.5">
                        <h2 class="font-bold text-xl text-gray-900 dark:text-gray-100 leading-tight">
                            Detail Tugas Langsung
                        </h2>
                        @php
                            $statusBadge = match($task->status) {
                                'belum_dibaca'              => ['class' => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300 border-slate-200', 'label' => 'Belum Dibaca'],
                                'dibaca'                    => ['class' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 border-blue-200', 'label' => 'Sedang Ditelaah'],
                                'dikerjakan'                => ['class' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300 border-amber-200', 'label' => 'Sedang Dikerjakan'],
                                'menunggu_verifikasi_kabid' => ['class' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300 border-indigo-200 animate-pulse', 'label' => 'Menunggu Verifikasi Anda'],
                                'perlu_revisi'              => ['class' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300 border-rose-200', 'label' => 'Perlu Revisi'],
                                'selesai'                   => ['class' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 border-emerald-200', 'label' => 'Selesai & Disetujui'],
                                default                     => ['class' => 'bg-gray-100 text-gray-700 border-gray-200', 'label' => $task->status]
                            };
                        @endphp
                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full border {{ $statusBadge['class'] }}">
                            {{ $statusBadge['label'] }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('kabid.tugas.index') }}"
                       class="inline-flex items-center gap-2 px-3.5 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs font-semibold rounded-xl transition-colors shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{
        showPreviewModal: false,
        previewModalUrl: '',
        previewModalTitle: '',
        showRevisiModal: false,
        openPreview(url, title) {
            this.previewModalUrl = url;
            this.previewModalTitle = title;
            this.showPreviewModal = true;
        }
    }">
        @if(session('success'))
            <x-ui.alert type="success" :message="session('success')" />
        @endif
        @if(session('error'))
            <x-ui.alert type="error" :message="session('error')" />
        @endif

        {{-- VISUAL TRACKING STEPPER --}}
        @php
            $hasReport = in_array($task->status, ['menunggu_verifikasi_kabid', 'selesai', 'perlu_revisi']);
            $isCompleted = $task->status === 'selesai';
        @endphp
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-6 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Tahapan Pelaksanaan Tugas Langsung
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 relative">
                {{-- Step 1: Diberikan --}}
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400 flex items-center justify-center font-bold text-xs flex-shrink-0 ring-4 ring-emerald-50 dark:ring-emerald-950/30">
                        ✓
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-gray-900 dark:text-gray-100">1. Penugasan Kabid</p>
                        <p class="text-[11px] text-gray-500 mt-0.5">{{ $task->tanggal_disposisi ? $task->tanggal_disposisi->translatedFormat('d M Y') : '-' }}</p>
                    </div>
                </div>

                {{-- Step 2: Diterima Staf --}}
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full {{ $task->status !== 'belum_dibaca' ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400 ring-4 ring-emerald-50' : 'bg-slate-100 text-slate-500 ring-4 ring-slate-50' }} flex items-center justify-center font-bold text-xs flex-shrink-0">
                        {{ $task->status !== 'belum_dibaca' ? '✓' : '2' }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold {{ $task->status !== 'belum_dibaca' ? 'text-gray-900 dark:text-gray-100' : 'text-gray-400' }}">2. Ditelaah / Dikerjakan</p>
                        <p class="text-[11px] text-gray-500 mt-0.5">
                            @if($task->status === 'belum_dibaca')
                                Menunggu dibaca staf
                            @elseif($task->status === 'dibaca')
                                Telah dibaca staf
                            @else
                                Dalam proses kerja
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Step 3: Laporan Hasil --}}
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full {{ $hasReport ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400 ring-4 ring-emerald-50' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center font-bold text-xs flex-shrink-0">
                        {{ $hasReport ? '✓' : '3' }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold {{ $hasReport ? 'text-gray-900 dark:text-gray-100' : 'text-gray-400' }}">3. Laporan Staf</p>
                        <p class="text-[11px] text-gray-500 mt-0.5">
                            @if($hasReport)
                                Berkas/Catatan dikirim
                            @else
                                Menunggu laporan staf
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Step 4: Verifikasi & Selesai --}}
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full {{ $isCompleted ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400 ring-4 ring-emerald-50' : ($task->status === 'menunggu_verifikasi_kabid' ? 'bg-amber-100 text-amber-700 ring-4 ring-amber-50 animate-pulse' : 'bg-slate-100 text-slate-400') }} flex items-center justify-center font-bold text-xs flex-shrink-0">
                        {{ $isCompleted ? '✓' : '4' }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold {{ $isCompleted ? 'text-gray-900 dark:text-gray-100' : ($task->status === 'menunggu_verifikasi_kabid' ? 'text-amber-600 font-bold' : 'text-gray-400') }}">4. Verifikasi Kabid</p>
                        <p class="text-[11px] {{ $isCompleted ? 'text-emerald-600 font-bold' : ($task->status === 'menunggu_verifikasi_kabid' ? 'text-amber-600 font-medium' : 'text-gray-400') }} mt-0.5">
                            @if($isCompleted)
                                Tuntas & Disetujui
                            @elseif($task->status === 'menunggu_verifikasi_kabid')
                                Perlu diverifikasi
                            @elseif($task->status === 'perlu_revisi')
                                Menunggu revisi staf
                            @else
                                Menunggu pengerjaan
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Kolom Kiri: Detail Instruksi & Laporan Tindak Lanjut --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Card Instruksi Kabid --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-emerald-100 dark:border-emerald-900/40 p-6 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-gray-100 dark:border-gray-700 pb-4">
                        <div>
                            <span class="text-[11px] font-mono text-gray-400 font-bold">#TUGAS-{{ str_pad($task->id, 4, '0', STR_PAD_LEFT) }}</span>
                            <h3 class="font-bold text-lg text-gray-900 dark:text-gray-100 mt-0.5">
                                {{ $task->judul_tugas }}
                            </h3>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full border bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300">
                                Sifat: {{ $task->sifat }}
                            </span>
                            @if($task->deadline)
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full border bg-gray-50 text-gray-700 border-gray-200 dark:bg-gray-700 dark:text-gray-300">
                                    Batas: {{ $task->deadline->translatedFormat('d M Y') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Instruksi & Arahan:</h4>
                        <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-100 dark:border-gray-700 text-gray-800 dark:text-gray-200 text-xs leading-relaxed whitespace-pre-line font-medium">
                            {{ $task->catatan_kabid }}
                        </div>
                    </div>

                    {{-- Lampiran Petunjuk jika ada --}}
                    @if($task->file_tugas)
                        <div class="pt-2">
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Berkas Petunjuk / Acuan:</h4>
                            <div class="p-4 rounded-xl border border-blue-100 dark:border-blue-900/40 bg-blue-50/40 dark:bg-blue-950/20 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-gray-800 dark:text-gray-200 truncate">{{ basename($task->file_tugas) }}</p>
                                        <p class="text-[11px] text-gray-400">Berkas panduan dari Anda</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <button type="button" @click="openPreview('{{ route('kabid.tugas.file', $task) }}', 'Panduan Tugas: {{ basename($task->file_tugas) }}')"
                                            class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors">
                                        Pratinjau
                                    </button>
                                    <a href="{{ route('kabid.tugas.file', $task) }}" target="_blank"
                                       class="px-3 py-1.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-xs font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                                        Unduh
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Card Laporan Tindak Lanjut dari Staf --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                    <div class="border-b border-gray-100 dark:border-gray-700 pb-3 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h3 class="font-bold text-base text-gray-900 dark:text-gray-100">
                                Laporan Hasil Kerja Staf
                            </h3>
                        </div>
                        @if($task->status === 'selesai' && $task->tanggal_selesai)
                            <span class="text-xs font-semibold text-emerald-600 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Disetujui: {{ $task->tanggal_selesai->translatedFormat('d M Y') }}
                            </span>
                        @endif
                    </div>

                    @if($task->catatan_tindak_lanjut || $task->file_tindak_lanjut)
                        <div class="space-y-4">
                            <div>
                                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Keterangan / Laporan Pelaksanaan:</h4>
                                <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-100 dark:border-gray-700 text-gray-800 dark:text-gray-200 text-xs leading-relaxed whitespace-pre-line font-medium">
                                    {{ $task->catatan_tindak_lanjut }}
                                </div>
                            </div>

                            @if($task->file_tindak_lanjut)
                                <div>
                                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Berkas Laporan / Bukti Lapangan:</h4>
                                    <div class="p-4 rounded-xl border border-emerald-100 dark:border-emerald-900/40 bg-emerald-50/40 dark:bg-emerald-950/20 flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-xs font-bold text-gray-800 dark:text-gray-200 truncate">{{ basename($task->file_tindak_lanjut) }}</p>
                                                <p class="text-[11px] text-gray-400">Diunggah oleh staf</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 flex-shrink-0">
                                            <button type="button" @click="openPreview('{{ route('tasks.file', $task->id) }}', 'Bukti Laporan: {{ basename($task->file_tindak_lanjut) }}')"
                                                    class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors">
                                                Pratinjau
                                            </button>
                                            <a href="{{ route('tasks.file', $task->id) }}" target="_blank"
                                               class="px-3 py-1.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-xs font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                                                Unduh
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($task->catatan_revisi)
                                <div class="p-3.5 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/40 rounded-xl text-xs">
                                    <p class="font-bold text-rose-700 dark:text-rose-300 mb-0.5">Catatan Revisi dari Anda Sebelumnya:</p>
                                    <p class="text-rose-600 dark:text-rose-400 leading-relaxed">{{ $task->catatan_revisi }}</p>
                                </div>
                            @endif

                            {{-- Form Tombol Verifikasi Kabid --}}
                            @if(in_array($task->status, ['menunggu_verifikasi_kabid', 'perlu_revisi', 'dikerjakan']))
                                <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex flex-wrap items-center justify-end gap-3">
                                    <button type="button" @click="showRevisiModal = true"
                                            class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-semibold rounded-xl text-xs transition-colors">
                                        Minta Revisi
                                    </button>

                                    <form method="POST" action="{{ route('kabid.tugas.verifikasi', $task) }}" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui hasil kerja ini dan menandai tugas sebagai SELESAI?')">
                                        @csrf
                                        <input type="hidden" name="keputusan" value="terima">
                                        <button type="submit"
                                                class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-xs transition-all shadow-md flex items-center gap-1.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            <span>Setujui (Tandai Selesai)</span>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="py-8 text-center text-gray-400 text-xs">
                            Staf pelaksana belum mengirimkan laporan hasil pengerjaan.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Kolom Kanan: Identitas Staf & Info Penugasan --}}
            <div class="space-y-6">
                {{-- Identitas Staf Pelaksana Card --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                    <h3 class="font-bold text-sm text-gray-900 dark:text-gray-100 pb-3 border-b border-gray-100 dark:border-gray-700">
                        Staf Pelaksana
                    </h3>

                    @if($task->user)
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 flex items-center justify-center font-bold text-base flex-shrink-0">
                                {{ substr($task->user->name, 0, 1) }}
                            </div>
                            <div class="min-w-0">
                                <h4 class="font-bold text-sm text-gray-900 dark:text-gray-100 truncate">{{ $task->user->name }}</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $task->user->jabatan ?? 'Staf Pelaksana' }}</p>
                                <p class="text-[11px] text-gray-400 truncate">{{ $task->user->email }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="pt-3 border-t border-gray-100 dark:border-gray-700 space-y-2.5 text-xs">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Bidang:</span>
                            <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $task->department?->name ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Tanggal Diberikan:</span>
                            <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $task->tanggal_disposisi ? $task->tanggal_disposisi->translatedFormat('d M Y') : '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Batas Waktu:</span>
                            <span class="font-semibold {{ $task->deadline && $task->deadline->isPast() && $task->status !== 'selesai' ? 'text-rose-600 font-bold' : 'text-gray-700 dark:text-gray-300' }}">
                                {{ $task->deadline ? $task->deadline->translatedFormat('d M Y') : 'Tidak ditentukan' }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Pemberi Tugas:</span>
                            <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $task->creator?->name ?? 'Kepala Bidang' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Action Card: Hapus / Batalkan Tugas --}}
                @if($task->status !== 'selesai')
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-3">
                        <h4 class="font-bold text-xs text-gray-700 dark:text-gray-300">Batalkan Penugasan</h4>
                        <p class="text-[11px] text-gray-400 leading-relaxed">
                            Jika tugas ini dibatalkan, data penugasan dan seluruh berkas terkait akan dihapus secara permanen.
                        </p>
                        <form action="{{ route('kabid.tugas.destroy', $task) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus penugasan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-semibold rounded-xl transition-colors">
                                Hapus / Batalkan Tugas
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        {{-- MODAL REVISI HASIL KERJA --}}
        <div x-show="showRevisiModal" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg p-6 border border-gray-100 dark:border-gray-700" @click.away="showRevisiModal = false">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-base text-gray-900 dark:text-gray-100">Minta Perbaikan / Revisi</h3>
                        <p class="text-xs text-gray-500">Berikan catatan apa saja yang perlu disempurnakan oleh staf.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('kabid.tugas.verifikasi', $task) }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="keputusan" value="revisi">
                    <div>
                        <label for="catatan_revisi" class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                            Catatan & Arahan Revisi <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="catatan_revisi" id="catatan_revisi" rows="4" required
                                  placeholder="Contoh: Tolong lengkapi dokumentasi foto perbaikan rambu titik barat dan sertakan rincian biaya..."
                                  class="w-full text-xs border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-900 p-3 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-rose-500">{{ old('catatan_revisi', $task->catatan_revisi) }}</textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-2">
                        <button type="button" @click="showRevisiModal = false"
                                class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 rounded-xl transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-5 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-xs transition-colors">
                            Kirim Instruksi Revisi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- IN-APP DOCUMENT PREVIEW MODAL --}}
        <div x-show="showPreviewModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/70 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-5xl overflow-hidden border border-gray-200 dark:border-gray-700 flex flex-col max-h-[92vh]" @click.away="showPreviewModal = false">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-gray-800/80">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <h4 class="font-bold text-sm text-gray-900 dark:text-gray-100 truncate max-w-md" x-text="previewModalTitle">Pratinjau Dokumen</h4>
                    </div>
                    <div class="flex items-center gap-2">
                        <a :href="previewModalUrl" target="_blank" class="px-3 py-1.5 text-xs font-semibold bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 text-gray-700 dark:text-gray-200 flex items-center gap-1 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            Tab Baru
                        </a>
                        <button type="button" @click="showPreviewModal = false" class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>

                <div class="flex-1 overflow-auto bg-gray-100 dark:bg-gray-900 p-2 flex items-center justify-center min-h-[500px]">
                    <template x-if="showPreviewModal">
                        <iframe :src="previewModalUrl" class="w-full h-[75vh] rounded-lg border-0 bg-white"></iframe>
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
