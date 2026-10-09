<x-app-layout>
    <x-slot name="header">
        <div class="space-y-2">
            {{-- Breadcrumb Navigation --}}
            <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                <a href="{{ route('kabid.dashboard') }}" class="hover:text-indigo-600 transition-colors flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Dashboard
                </a>
                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                <a href="{{ route('kabid.surat-masuk.index') }}" class="hover:text-indigo-600 transition-colors">Surat Masuk Bidang</a>
                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                <span class="text-gray-800 dark:text-gray-200 font-semibold truncate max-w-xs">
                    {{ $assignment->incomingLetter->nomor_agenda ? '#' . $assignment->incomingLetter->nomor_agenda : $assignment->incomingLetter->nomor_surat }}
                </span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="font-bold text-xl text-gray-900 dark:text-gray-100 leading-tight">
                        Detail Surat Bidang & Disposisi
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Kelola penugasan staf, pantau berkas lampiran, dan verifikasi hasil kerja.
                    </p>
                </div>
                <a href="{{ route('kabid.surat-masuk.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs font-semibold rounded-xl transition-colors shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Daftar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{ 
        showPreviewModal: false,
        previewModalUrl: '',
        previewModalTitle: '',
        copiedNumber: false,
        openPreview(url, title) {
            this.previewModalUrl = url;
            this.previewModalTitle = title;
            this.showPreviewModal = true;
        },
        copyText(text) {
            navigator.clipboard.writeText(text);
            this.copiedNumber = true;
            setTimeout(() => this.copiedNumber = false, 2000);
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
            $isAssigned = !is_null($assignment->user_id) || $assignment->status === 'selesai';
            $hasReportSubmitted = in_array($assignment->status, ['menunggu_verifikasi_kabid', 'selesai', 'perlu_revisi']);
            $isFullyCompleted = $assignment->status === 'selesai';
        @endphp
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-6 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Tahapan Disposisi & Tindak Lanjut Bidang
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 relative">
                {{-- Step 1: Diterima Bidang --}}
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400 flex items-center justify-center font-bold text-xs flex-shrink-0 ring-4 ring-emerald-50 dark:ring-emerald-950/30">
                        ✓
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-gray-900 dark:text-gray-100">1. Diterima Bidang</p>
                        <p class="text-[11px] text-gray-500 mt-0.5">Dari Staf / Loket</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($assignment->created_at)->translatedFormat('d M Y') }}</p>
                    </div>
                </div>

                {{-- Step 2: Arahan Kabid --}}
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full {{ $isAssigned ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400 ring-4 ring-emerald-50' : 'bg-indigo-100 text-indigo-600 ring-4 ring-indigo-50 animate-pulse' }} flex items-center justify-center font-bold text-xs flex-shrink-0">
                        {{ $isAssigned ? '✓' : '2' }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold {{ $isAssigned ? 'text-gray-900 dark:text-gray-100' : 'text-indigo-600' }}">2. Disposisi Kabid</p>
                        @if($assignment->user)
                            <p class="text-[11px] text-indigo-600 dark:text-indigo-400 mt-0.5 font-medium truncate">Ke: {{ $assignment->user->name }}</p>
                        @elseif($assignment->status === 'selesai')
                            <p class="text-[11px] text-emerald-600 mt-0.5 font-medium">Diarsipkan Bidang</p>
                        @else
                            <p class="text-[11px] text-rose-500 mt-0.5 font-medium">Menunggu Arahan Anda</p>
                        @endif
                    </div>
                </div>

                {{-- Step 3: Tindak Lanjut Karyawan --}}
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full {{ $hasReportSubmitted ? 'bg-emerald-100 text-emerald-600 ring-4 ring-emerald-50' : ($assignment->user_id ? 'bg-blue-100 text-blue-600 ring-4 ring-blue-50 animate-pulse' : 'bg-gray-100 text-gray-400') }} flex items-center justify-center font-bold text-xs flex-shrink-0">
                        {{ $hasReportSubmitted ? '✓' : '3' }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold {{ $hasReportSubmitted ? 'text-gray-900 dark:text-gray-100' : 'text-gray-400' }}">3. Laporan Staf</p>
                        <p class="text-[11px] text-gray-500 mt-0.5">
                            @if($hasReportSubmitted)
                                Laporan Terkirim
                            @elseif($assignment->user_id)
                                Staf Sedang Mengerjakan
                            @else
                                Belum Ditugaskan
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Step 4: Verifikasi & Selesai --}}
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full {{ $isFullyCompleted ? 'bg-emerald-100 text-emerald-600 ring-4 ring-emerald-50' : ($assignment->status === 'menunggu_verifikasi_kabid' ? 'bg-amber-100 text-amber-700 ring-4 ring-amber-50 animate-pulse' : 'bg-gray-100 text-gray-400') }} flex items-center justify-center font-bold text-xs flex-shrink-0">
                        {{ $isFullyCompleted ? '✓' : '4' }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold {{ $isFullyCompleted ? 'text-gray-900 dark:text-gray-100' : 'text-gray-400' }}">4. Verifikasi Kabid</p>
                        <p class="text-[11px] {{ $isFullyCompleted ? 'text-emerald-600 font-semibold' : ($assignment->status === 'menunggu_verifikasi_kabid' ? 'text-amber-600 font-bold' : 'text-gray-400') }} mt-0.5">
                            @if($isFullyCompleted)
                                Disetujui (Selesai)
                            @elseif($assignment->status === 'menunggu_verifikasi_kabid')
                                Perlu Verifikasi Anda
                            @elseif($assignment->status === 'perlu_revisi')
                                Diminta Revisi
                            @else
                                Belum Verifikasi
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Kolom Kiri: Informasi Surat & Hasil Tindak Lanjut --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Informasi Surat --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-5">
                    <div class="border-b border-gray-100 dark:border-gray-700 pb-4 flex items-center justify-between">
                        <h3 class="text-base font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Informasi Dokumen Surat
                        </h3>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                            Sifat: {{ ucfirst($assignment->incomingLetter->sifat) }}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6 text-sm">
                        <div>
                            <span class="text-xs text-gray-400 uppercase tracking-wider block font-medium">Nomor Surat (Fisik)</span>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="font-mono font-bold text-gray-900 dark:text-gray-100 text-sm">{{ $assignment->incomingLetter->nomor_surat }}</span>
                                <button @click="copyText('{{ $assignment->incomingLetter->nomor_surat }}')" type="button" class="text-gray-400 hover:text-indigo-600 transition-colors" title="Salin Nomor Surat">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <span class="text-xs text-gray-400 uppercase tracking-wider block font-medium">Nomor Agenda</span>
                            <span class="font-bold text-indigo-600 font-mono mt-0.5 block">{{ $assignment->incomingLetter->nomor_agenda ? '#' . $assignment->incomingLetter->nomor_agenda : '-' }}</span>
                        </div>

                        <div>
                            <span class="text-xs text-gray-400 uppercase tracking-wider block font-medium">Asal Surat / Instansi</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200 mt-0.5 block">{{ $assignment->incomingLetter->asal_surat }}</span>
                        </div>

                        <div>
                            <span class="text-xs text-gray-400 uppercase tracking-wider block font-medium">Tanggal Diterima Loket</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200 mt-0.5 block">
                                {{ \Carbon\Carbon::parse($assignment->incomingLetter->tanggal_diterima)->translatedFormat('l, d F Y') }}
                            </span>
                        </div>

                        <div class="md:col-span-2">
                            <span class="text-xs text-gray-400 uppercase tracking-wider block font-medium">Perihal Surat</span>
                            <p class="font-bold text-gray-900 dark:text-gray-100 text-base mt-1 leading-snug">
                                {{ $assignment->incomingLetter->perihal }}
                            </p>
                        </div>
                    </div>

                    @if($assignment->incomingLetter->file_lampiran)
                        <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex flex-wrap items-center gap-3">
                            <button type="button" @click="openPreview('{{ route('surat-masuk.file', $assignment->incomingLetter->id) }}', 'Berkas Surat: {{ basename($assignment->incomingLetter->file_lampiran) }}')"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold transition-all shadow-xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                Pratinjau Surat Cepat (In-App)
                            </button>

                            <a href="{{ route('surat-masuk.file', $assignment->incomingLetter->id) }}" target="_blank"
                               class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl text-xs font-medium transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                Buka di Tab Baru
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Hasil Tindak Lanjut Karyawan --}}
                @if($assignment->status === 'menunggu_verifikasi_kabid' || $assignment->status === 'selesai' || $assignment->status === 'perlu_revisi')
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-4">
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">
                                Laporan Hasil Kerja: {{ $assignment->user ? $assignment->user->name : 'Staf' }}
                            </h3>
                            <p class="text-xs text-gray-400 mt-0.5">Tindak lanjut yang diajukan oleh staf pelaksana</p>
                        </div>
                        @php
                            $badgeClass = match($assignment->status) {
                                'selesai' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300 border-emerald-200',
                                'menunggu_verifikasi_kabid' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 border-amber-200',
                                'perlu_revisi' => 'bg-rose-100 text-rose-800 dark:bg-rose-950/40 dark:text-rose-300 border-rose-200',
                                default => 'bg-blue-100 text-blue-800'
                            };
                            $badgeLabel = match($assignment->status) {
                                'selesai' => 'Selesai & Disetujui',
                                'menunggu_verifikasi_kabid' => 'Menunggu Verifikasi Anda',
                                'perlu_revisi' => 'Perlu Revisi',
                                default => ucfirst($assignment->status)
                            };
                        @endphp
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ $badgeClass }}">
                            {{ $badgeLabel }}
                        </span>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <p class="text-xs text-gray-400 font-medium mb-1">Catatan Laporan Pelaksanaan</p>
                            <div class="p-3.5 bg-gray-50 dark:bg-gray-700/50 rounded-xl text-xs text-gray-800 dark:text-gray-200 border border-gray-100 dark:border-gray-700 leading-relaxed">
                                {!! nl2br(e($assignment->catatan_tindak_lanjut ?: 'Belum ada catatan laporan.')) !!}
                            </div>
                        </div>
                        
                        @if($assignment->file_tindak_lanjut)
                        <div>
                            <p class="text-xs text-gray-400 font-medium mb-2">Berkas Bukti / Laporan Hasil</p>
                            <button type="button" @click="openPreview('{{ route('tasks.file', $assignment->id) }}', 'Bukti Laporan: {{ basename($assignment->file_tindak_lanjut) }}')"
                                    class="inline-flex items-center gap-2 px-3.5 py-2 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 rounded-xl text-xs font-semibold transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                Pratinjau Berkas Bukti (In-App)
                            </button>
                        </div>
                        @endif
                    </div>

                    @if($assignment->status === 'menunggu_verifikasi_kabid')
                    <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700"
                         x-data="{ 
                             keputusan: 'terima',
                             showConfirmModal: false
                         }">
                        <form action="{{ route('kabid.surat-masuk.verifikasi', $assignment->id) }}" 
                              method="POST" 
                              class="space-y-4"
                              x-ref="verifForm"
                              @submit.prevent="showConfirmModal = true">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-gray-800 dark:text-gray-200 mb-2 uppercase tracking-wider">Keputusan Verifikasi Kabid</label>
                                <div class="grid grid-cols-2 gap-3 mb-4">
                                    <label class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition-all"
                                           :class="keputusan === 'terima' ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/30 text-emerald-700' : 'border-gray-200 dark:border-gray-700'">
                                        <input type="radio" name="keputusan" value="terima" x-model="keputusan" class="text-emerald-600 focus:ring-emerald-500">
                                        <span class="text-xs font-bold">Setujui (Selesai)</span>
                                    </label>
                                    <label class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition-all"
                                           :class="keputusan === 'revisi' ? 'border-rose-500 bg-rose-50/50 dark:bg-rose-950/30 text-rose-700' : 'border-gray-200 dark:border-gray-700'">
                                        <input type="radio" name="keputusan" value="revisi" x-model="keputusan" class="text-rose-600 focus:ring-rose-500">
                                        <span class="text-xs font-bold">Tolak (Minta Revisi)</span>
                                    </label>
                                </div>
                                
                                <div x-show="keputusan === 'revisi'" style="display: none;" class="space-y-1">
                                    <label class="block text-xs font-semibold text-rose-600 mb-1">Catatan Revisi untuk Karyawan <span class="text-rose-500">*</span></label>
                                    <textarea name="catatan_revisi" rows="3" class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-xs" placeholder="Berikan instruksi apa yang perlu diperbaiki..."></textarea>
                                </div>
                            </div>
                            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition-all shadow-sm text-xs flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Simpan Keputusan Verifikasi</span>
                            </button>

                            {{-- Popup Modal Konfirmasi Kecil Kabid --}}
                            <div x-show="showConfirmModal" 
                                 x-cloak 
                                 class="fixed inset-0 z-50 overflow-y-auto"
                                 aria-labelledby="modal-verif-title" role="dialog" aria-modal="true">
                                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity" 
                                     @click="showConfirmModal = false"></div>

                                <div class="flex min-h-full items-center justify-center p-4 text-center">
                                    <div x-show="showConfirmModal"
                                         x-transition:enter="ease-out duration-300"
                                         x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
                                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                         x-transition:leave="ease-in duration-200"
                                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                         x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
                                         class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md p-6 border border-gray-100 dark:border-gray-700">
                                        
                                        <div class="flex items-center gap-3.5 mb-4">
                                            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                                                 :class="keputusan === 'terima' ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400' : 'bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400'">
                                                <template x-if="keputusan === 'terima'">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </template>
                                                <template x-if="keputusan === 'revisi'">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                    </svg>
                                                </template>
                                            </div>
                                            <div>
                                                <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100" id="modal-verif-title"
                                                    x-text="keputusan === 'terima' ? 'Konfirmasi Selesaikan Surat' : 'Konfirmasi Minta Revisi'">
                                                </h3>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    Verifikasi Hasil Kerja Staf
                                                </p>
                                            </div>
                                        </div>

                                        <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed mb-6"
                                           x-text="keputusan === 'terima' ? 'Apakah Anda yakin ingin menyetujui hasil kerja dan menandai tindak lanjut surat ini telah Selesai?' : 'Apakah Anda yakin ingin mengembalikan tugas ini kepada karyawan untuk direvisi sesuai catatan?'">
                                        </p>

                                        <div class="flex items-center justify-end gap-2.5">
                                            <button type="button" 
                                                    @click="showConfirmModal = false"
                                                    class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-xl transition-colors">
                                                Batal
                                            </button>
                                            <button type="button" 
                                                    @click="$refs.verifForm.submit()"
                                                    class="px-4 py-2 text-xs font-semibold text-white rounded-xl shadow-xs transition-colors flex items-center gap-1.5"
                                                    :class="keputusan === 'terima' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-rose-600 hover:bg-rose-700'">
                                                <span x-text="keputusan === 'terima' ? 'Ya, Setujui & Selesai' : 'Ya, Kirim Revisi'"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>
                @endif
            </div>

            {{-- Kolom Kanan: Panel Disposisi & Tim Staf --}}
            <div class="space-y-6">
                @if(is_null($assignment->user_id))
                    {{-- Kasus 1: Surat Baru Masuk ke Bidang (Belum didisposisikan ke staf) --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6" x-data="{ 
                        mode: 'karyawan', 
                        selectAll: false,
                        toggleAll(checked) {
                            this.selectAll = checked;
                            let cbs = $refs.staffList ? $refs.staffList.querySelectorAll('input[type=checkbox]') : [];
                            cbs.forEach(cb => cb.checked = checked);
                        }
                    }">
                        <h3 class="text-base font-bold text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-gray-700 pb-3 mb-4">
                            Disposisi ke Staf Bidang
                        </h3>

                        @if($assignment->status === 'selesai')
                            <div class="p-3.5 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-800 dark:text-emerald-300 rounded-xl mb-4 text-xs font-medium border border-emerald-200">
                                Surat ini telah disimpan sebagai arsip bidang (selesai).
                            </div>
                        @else
                            <form action="{{ route('kabid.surat-masuk.disposisi', $assignment->id) }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Catatan Staf / Loket</label>
                                    <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl text-xs text-gray-600 dark:text-gray-300 italic border border-gray-100 dark:border-gray-700">
                                        {{ $assignment->catatan ?: 'Tidak ada catatan khusus dari staf / loket.' }}
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Tindakan Bidang</label>
                                    <div class="grid grid-cols-2 gap-2.5">
                                        <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer transition-all"
                                               :class="mode === 'karyawan' ? 'border-indigo-500 bg-indigo-50/50 text-indigo-700 font-bold' : 'border-gray-200 text-gray-600'">
                                            <input type="radio" name="tipe_tindakan" value="karyawan" x-model="mode" class="text-indigo-600 focus:ring-indigo-500">
                                            <span class="text-xs">Tugaskan Staf</span>
                                        </label>
                                        <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer transition-all"
                                               :class="mode === 'arsip' ? 'border-amber-500 bg-amber-50/50 text-amber-700 font-bold' : 'border-gray-200 text-gray-600'">
                                            <input type="radio" name="tipe_tindakan" value="arsip" x-model="mode" class="text-amber-600 focus:ring-amber-500">
                                            <span class="text-xs">Simpan Arsip</span>
                                        </label>
                                    </div>
                                </div>

                                {{-- Opsi Penugasan Staf --}}
                                <div x-show="mode === 'karyawan'" class="space-y-4">
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <label class="text-xs font-semibold text-gray-700 dark:text-gray-300">
                                                Pilih Staf Pelaksana <span class="text-rose-500">*</span>
                                            </label>
                                            @if($karyawan->count() > 0)
                                                <label class="inline-flex items-center gap-1.5 text-xs text-indigo-600 font-semibold cursor-pointer hover:underline select-none">
                                                    <input type="checkbox" @change="toggleAll($event.target.checked)" class="rounded text-indigo-600 focus:ring-indigo-500">
                                                    <span>Pilih Semua ({{ $karyawan->count() }})</span>
                                                </label>
                                            @endif
                                        </div>

                                        @if($karyawan->count() > 0)
                                            <div x-ref="staffList" class="space-y-2 max-h-52 overflow-y-auto pr-1">
                                                @foreach($karyawan as $k)
                                                    <label class="flex items-center justify-between p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors">
                                                        <div class="flex items-center gap-2.5">
                                                            <input type="checkbox" name="user_ids[]" value="{{ $k->id }}" class="rounded text-indigo-600 focus:ring-indigo-500">
                                                            <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs">
                                                                {{ substr($k->name, 0, 1) }}
                                                            </div>
                                                            <div>
                                                                <div class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ $k->name }}</div>
                                                                <div class="text-[11px] text-gray-400">{{ $k->email }}</div>
                                                            </div>
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="p-3 bg-amber-50 text-amber-800 rounded-xl text-xs">
                                                Belum ada staf yang terdaftar di bidang Anda. Anda dapat memilih opsi "Simpan Arsip".
                                            </div>
                                        @endif
                                        @error('user_ids')
                                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                            Instruksi Kabid <span class="text-rose-500">*</span>
                                        </label>
                                        <textarea name="catatan_kabid" rows="3" class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs" placeholder="Tuliskan arahan / instruksi tugas untuk staf..."></textarea>
                                        @error('catatan_kabid')
                                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Batas Waktu (Deadline)</label>
                                        <input type="date" name="deadline" class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs">
                                        @error('deadline')
                                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Opsi Simpan Arsip --}}
                                <div x-show="mode === 'arsip'" style="display: none;" class="space-y-3">
                                    <div class="p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 rounded-xl text-xs text-amber-800">
                                        Surat akan disimpan sebagai arsip bidang dan statusnya otomatis dinyatakan <strong>Selesai</strong>.
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Catatan Pengarsipan (Opsional)</label>
                                        <textarea name="catatan_arsip" rows="3" class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs" placeholder="Alasan diarsipkan tanpa penugasan..."></textarea>
                                    </div>
                                </div>

                                <button type="submit" class="w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition-colors shadow-sm text-xs">
                                    <span x-text="mode === 'karyawan' ? 'Kirim Disposisi ke Staf Terpilih' : 'Simpan sebagai Arsip Bidang'"></span>
                                </button>
                            </form>
                        @endif
                    </div>
                @else
                    {{-- Kasus 2: Penugasan Staf yang Sedang Dilihat --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3">
                            <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">
                                Status Penugasan Staf
                            </h3>
                            @if(in_array($assignment->status, ['belum_dibaca', 'dibaca']))
                                <form action="{{ route('kabid.surat-masuk.batal', $assignment->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan tugas untuk staf ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-[11px] font-bold text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 dark:bg-red-900/30 dark:hover:bg-red-900/50 px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Batalkan Tugas
                                    </button>
                                </form>
                            @endif
                        </div>

                        <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-100 dark:border-gray-700">
                            <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                {{ substr($assignment->user->name ?? '?', 0, 1) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="font-bold text-sm text-gray-900 dark:text-gray-100 truncate">{{ $assignment->user->name ?? 'Staf' }}</div>
                                <div class="text-xs text-gray-500 truncate">{{ $assignment->user->email ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div class="p-2.5 bg-gray-50 dark:bg-gray-700/40 rounded-xl">
                                <span class="text-gray-400 block mb-0.5 font-medium">Status Tugas</span>
                                <span class="font-bold text-gray-800 dark:text-gray-200">{{ ucfirst(str_replace('_', ' ', $assignment->status)) }}</span>
                            </div>
                            <div class="p-2.5 bg-gray-50 dark:bg-gray-700/40 rounded-xl">
                                <span class="text-gray-400 block mb-0.5 font-medium">Batas Waktu</span>
                                <span class="font-bold text-gray-800 dark:text-gray-200">
                                    {{ $assignment->deadline ? \Carbon\Carbon::parse($assignment->deadline)->translatedFormat('d M Y') : 'Tidak ada' }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <span class="text-xs text-gray-400 block mb-1 font-medium">Instruksi Anda</span>
                            <div class="p-3 bg-gray-50 dark:bg-gray-700/40 rounded-xl text-xs text-gray-700 dark:text-gray-300 leading-relaxed">
                                {{ $assignment->catatan_kabid ?: 'Tidak ada instruksi khusus' }}
                            </div>
                        </div>
                    </div>

                    {{-- Daftar Staf Lain dalam Surat Ini --}}
                    @if(isset($bidangAssignments) && $bidangAssignments->count() > 1)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                            <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-gray-700 pb-3 mb-3">
                                Tim Staf Ditugaskan ({{ $bidangAssignments->count() }})
                            </h3>
                            <div class="space-y-2">
                                @foreach($bidangAssignments as $ba)
                                    <div class="flex items-center justify-between p-2.5 rounded-xl transition-colors {{ $ba->id === $assignment->id ? 'bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200' : 'bg-gray-50 dark:bg-gray-700/40' }}">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                                {{ substr($ba->user->name ?? '?', 0, 1) }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="text-xs font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $ba->user->name ?? 'Arsip' }}</div>
                                                <div class="text-[11px] text-gray-400">{{ ucfirst(str_replace('_', ' ', $ba->status)) }}</div>
                                            </div>
                                        </div>
                                        @if($ba->id !== $assignment->id)
                                            <a href="{{ route('kabid.surat-masuk.show', $ba->id) }}" class="text-xs text-indigo-600 font-semibold hover:underline flex-shrink-0">
                                                Buka &rarr;
                                            </a>
                                        @else
                                            <span class="text-[11px] font-bold text-indigo-600 flex-shrink-0">Aktif</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Form Tambah Disposisi ke Staf Lain --}}
                    @if(isset($unassignedKaryawan) && $unassignedKaryawan->count() > 0 && $assignment->status !== 'selesai')
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6" x-data="{ open: false, selectAllAdd: false, toggleAllAdd(c) { this.selectAllAdd = c; $refs.addList.querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = c); } }">
                            <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
                                <h3 class="text-xs font-bold text-gray-900 dark:text-gray-100 flex items-center gap-1.5 uppercase tracking-wider">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Tugaskan Staf Tambahan
                                </h3>
                                <svg class="w-4 h-4 text-gray-400 transform transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>

                            <form x-show="open" style="display: none;" action="{{ route('kabid.surat-masuk.disposisi', $assignment->id) }}" method="POST" class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 space-y-4">
                                @csrf
                                <input type="hidden" name="tipe_tindakan" value="karyawan">

                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="text-xs font-semibold text-gray-700 dark:text-gray-300">Pilih Staf Tambahan</label>
                                        <label class="inline-flex items-center gap-1 text-xs text-indigo-600 font-semibold cursor-pointer hover:underline">
                                            <input type="checkbox" @change="toggleAllAdd($event.target.checked)" class="rounded text-indigo-600 focus:ring-indigo-500">
                                            <span>Semua ({{ $unassignedKaryawan->count() }})</span>
                                        </label>
                                    </div>
                                    <div x-ref="addList" class="space-y-1.5 max-h-44 overflow-y-auto pr-1">
                                        @foreach($unassignedKaryawan as $uk)
                                            <label class="flex items-center gap-2.5 p-2 rounded-xl border border-gray-100 dark:border-gray-700 hover:bg-gray-50 cursor-pointer">
                                                <input type="checkbox" name="user_ids[]" value="{{ $uk->id }}" class="rounded text-indigo-600 focus:ring-indigo-500">
                                                <span class="text-xs font-medium text-gray-800 dark:text-gray-200">{{ $uk->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Instruksi untuk Staf Tambahan</label>
                                    <textarea name="catatan_kabid" rows="2" class="w-full rounded-xl border-gray-300 text-xs" placeholder="Instruksi tugas...">{{ $assignment->catatan_kabid }}</textarea>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Deadline</label>
                                    <input type="date" name="deadline" value="{{ $assignment->deadline ? \Carbon\Carbon::parse($assignment->deadline)->format('Y-m-d') : '' }}" class="w-full rounded-xl border-gray-300 text-xs">
                                </div>

                                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-xs transition-colors">
                                    Tambahkan Penugasan Staf
                                </button>
                            </form>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        {{-- IN-APP DOCUMENT PREVIEW MODAL --}}
        <div x-show="showPreviewModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/70 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-5xl overflow-hidden border border-gray-200 dark:border-gray-700 flex flex-col max-h-[92vh]" @click.away="showPreviewModal = false">
                {{-- Modal Header --}}
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-gray-800/80">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <h4 class="font-bold text-sm text-gray-900 dark:text-gray-100 truncate max-w-md" x-text="previewModalTitle">Pratinjau Dokumen</h4>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="document.getElementById('kabid-preview-iframe').contentWindow.print()" class="px-3 py-1.5 text-xs font-semibold bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 text-gray-700 dark:text-gray-200 flex items-center gap-1 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Cetak
                        </button>
                        <a :href="previewModalUrl" target="_blank" class="px-3 py-1.5 text-xs font-semibold bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 text-gray-700 dark:text-gray-200 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            Tab Baru
                        </a>
                        <button type="button" @click="showPreviewModal = false" class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>

                {{-- Modal Body --}}
                <div class="flex-1 overflow-auto bg-gray-100 dark:bg-gray-900 p-2 flex items-center justify-center min-h-[500px]">
                    <template x-if="showPreviewModal">
                        <iframe id="kabid-preview-iframe" :src="previewModalUrl" class="w-full h-[75vh] rounded-lg border-0 bg-white"></iframe>
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
