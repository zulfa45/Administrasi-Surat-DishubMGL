<x-app-layout>
    <x-slot name="header">
        <div class="space-y-2">
            {{-- Breadcrumb Navigation --}}
            <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                <a href="{{ route('karyawan.dashboard') }}" class="hover:text-blue-600 transition-colors flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Dashboard
                </a>
                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                <a href="{{ route('karyawan.tasks.index') }}" class="hover:text-blue-600 transition-colors">Daftar Tugas</a>
                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                <span class="text-gray-800 dark:text-gray-200 font-semibold truncate max-w-xs">
                    {{ $letter->nomor_agenda ? '#' . $letter->nomor_agenda : $letter->nomor_surat }}
                </span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex flex-wrap items-center gap-2.5">
                        <h2 class="font-bold text-xl text-gray-900 dark:text-gray-100 leading-tight">
                            Detail Tugas Disposisi
                        </h2>
                        @php
                            $assignStatusColors = match($task->status) {
                                'belum_dibaca' => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-600',
                                'dibaca'       => 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300 border-blue-200 dark:border-blue-800',
                                'dikerjakan'   => 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                                'menunggu_verifikasi_kabid' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 border-amber-200',
                                'perlu_revisi' => 'bg-rose-100 text-rose-800 dark:bg-rose-950/40 dark:text-rose-300 border-rose-200',
                                'selesai'      => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                                default        => 'bg-gray-100 text-gray-700 border-gray-200'
                            };
                            $statusLabel = match($task->status) {
                                'belum_dibaca' => 'Belum Dibaca',
                                'dibaca'       => 'Sedang Ditelaah',
                                'dikerjakan'   => 'Dalam Tindak Lanjut',
                                'menunggu_verifikasi_kabid' => 'Menunggu Verifikasi Kabid',
                                'perlu_revisi' => 'Perlu Revisi',
                                'selesai'      => 'Selesai & Disetujui',
                                default        => ucfirst(str_replace('_', ' ', $task->status))
                            };
                        @endphp
                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full border {{ $assignStatusColors }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                </div>

                <a href="{{ route('karyawan.tasks.index') }}"
                   class="inline-flex items-center gap-2 px-3.5 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs font-semibold rounded-xl transition-colors shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Daftar Tugas
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{ 
        showPreviewModal: false,
        copiedNumber: false,
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
            $isSubmitted = in_array($task->status, ['menunggu_verifikasi_kabid', 'selesai', 'perlu_revisi']);
            $isVerified = $task->status === 'selesai';
        @endphp
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-6 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Tahapan Pengerjaan Tugas Anda
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 relative">
                {{-- Step 1: Registrasi --}}
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400 flex items-center justify-center font-bold text-xs flex-shrink-0 ring-4 ring-emerald-50 dark:ring-emerald-950/30">
                        ✓
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-gray-900 dark:text-gray-100">1. Surat Diterima</p>
                        <p class="text-[11px] text-gray-500 mt-0.5">Dari {{ $letter->asal_surat }}</p>
                    </div>
                </div>

                {{-- Step 2: Disposisi Kabid --}}
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 ring-4 ring-emerald-50 flex items-center justify-center font-bold text-xs flex-shrink-0">
                        ✓
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-gray-900 dark:text-gray-100">2. Disposisi Pimpinan</p>
                        <p class="text-[11px] text-gray-500 mt-0.5">{{ $task->tanggal_disposisi ? $task->tanggal_disposisi->translatedFormat('d M Y') : '-' }}</p>
                    </div>
                </div>

                {{-- Step 3: Tindak Lanjut Anda --}}
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full {{ $isSubmitted ? 'bg-emerald-100 text-emerald-600 ring-4 ring-emerald-50' : 'bg-blue-100 text-blue-600 ring-4 ring-blue-50 animate-pulse' }} flex items-center justify-center font-bold text-xs flex-shrink-0">
                        {{ $isSubmitted ? '✓' : '3' }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold {{ $isSubmitted ? 'text-gray-900 dark:text-gray-100' : 'text-blue-600' }}">3. Laporan Kerja Anda</p>
                        <p class="text-[11px] text-gray-500 mt-0.5">
                            @if($isSubmitted)
                                Laporan Terkirim
                            @else
                                Perlu Dilaporkan
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Step 4: Verifikasi & Selesai --}}
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full {{ $isVerified ? 'bg-emerald-100 text-emerald-600 ring-4 ring-emerald-50' : ($task->status === 'menunggu_verifikasi_kabid' ? 'bg-amber-100 text-amber-700 ring-4 ring-amber-50 animate-pulse' : 'bg-gray-100 text-gray-400') }} flex items-center justify-center font-bold text-xs flex-shrink-0">
                        {{ $isVerified ? '✓' : '4' }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold {{ $isVerified ? 'text-gray-900 dark:text-gray-100' : 'text-gray-400' }}">4. Verifikasi Kabid</p>
                        <p class="text-[11px] {{ $isVerified ? 'text-emerald-600 font-bold' : ($task->status === 'menunggu_verifikasi_kabid' ? 'text-amber-600 font-medium' : 'text-gray-400') }} mt-0.5">
                            @if($isVerified)
                                Disetujui (Tuntas)
                            @elseif($task->status === 'menunggu_verifikasi_kabid')
                                Sedang Ditinjau Kabid
                            @elseif($task->status === 'perlu_revisi')
                                Diminta Revisi
                            @else
                                Belum Selesai
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Kolom Kiri: Instruksi & Form Tindak Lanjut (2 Kolom) --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Card Instruksi Disposisi --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-blue-100 dark:border-blue-900/40 overflow-hidden">
                    <div class="p-5 bg-gradient-to-r from-blue-50/80 via-blue-50/40 to-transparent dark:from-blue-950/40 dark:via-blue-950/20 dark:to-transparent border-b border-blue-100 dark:border-blue-900/40 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center flex-shrink-0 shadow-xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-base text-gray-900 dark:text-gray-100">
                                    Instruksi & Arahan Pimpinan
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Diberikan pada {{ $task->tanggal_disposisi ? $task->tanggal_disposisi->translatedFormat('l, d F Y') : '-' }}
                                </p>
                            </div>
                        </div>

                        @if($task->deadline)
                            @php
                                $isOverdue = $task->status !== 'selesai' && $task->deadline->isPast();
                            @endphp
                            <div class="px-3 py-1.5 rounded-xl border text-xs font-semibold {{ $isOverdue ? 'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/40 dark:text-red-300 dark:border-red-800' : 'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-900/40 dark:text-amber-300 dark:border-amber-800' }} flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Tenggat: {{ $task->deadline->translatedFormat('d M Y') }}</span>
                                @if($isOverdue)
                                    <span class="text-[10px] uppercase font-bold text-red-600 dark:text-red-400">(Terlewat)</span>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="p-6 space-y-3">
                        <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-100 dark:border-gray-700/80 text-gray-800 dark:text-gray-200 text-xs leading-relaxed whitespace-pre-line font-medium">
                            {{ $task->catatan_kabid ?: ($task->catatan ?: 'Tidak ada instruksi khusus.') }}
                        </div>

                        @if($task->catatan_revisi)
                            <div class="p-3.5 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-xl text-xs text-rose-800 dark:text-rose-300">
                                <strong class="block font-semibold mb-0.5">Catatan Revisi dari Kabid:</strong>
                                {{ $task->catatan_revisi }}
                            </div>
                        @endif

                        @if($task->tanggal_selesai)
                            <div class="p-3.5 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/80 rounded-xl flex items-center gap-2.5 text-emerald-800 dark:text-emerald-300 text-xs">
                                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Tugas ini telah ditandai rampung pada <strong>{{ $task->tanggal_selesai->translatedFormat('l, d F Y') }}</strong>.</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Formulir Update Tindak Lanjut & Laporan Hasil --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <div class="flex items-center gap-2.5 pb-4 border-b border-gray-100 dark:border-gray-700 mb-5">
                        <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-base text-gray-900 dark:text-gray-100">
                                Perbarui Laporan Tindak Lanjut
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Laporkan kemajuan atau upload berkas hasil kerja Anda untuk diverifikasi pimpinan.
                            </p>
                        </div>
                    </div>

                    @if($task->status === 'selesai')
                        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-800 dark:text-emerald-300 rounded-xl text-xs font-medium border border-emerald-200">
                            Tugas ini telah disetujui pimpinan dan berstatus Selesai.
                        </div>
                    @else
                        <form action="{{ route('karyawan.tasks.status', $task) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            @method('PATCH')

                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Status Pengerjaan</label>
                                <select name="status" class="w-full text-xs py-2 px-3 border border-gray-300 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500">
                                    <option value="dibaca" {{ $task->status === 'dibaca' ? 'selected' : '' }}>Sedang Ditelaah (Dibaca)</option>
                                    <option value="dikerjakan" {{ $task->status === 'dikerjakan' ? 'selected' : '' }}>Sedang Dalam Tindak Lanjut</option>
                                    <option value="menunggu_verifikasi_kabid" {{ $task->status === 'menunggu_verifikasi_kabid' ? 'selected' : '' }}>Selesai Dikerjakan (Ajukan ke Kabid)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Catatan / Laporan Tindak Lanjut</label>
                                <textarea name="catatan_tindak_lanjut" rows="4" class="w-full text-xs p-3 border border-gray-300 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500" placeholder="Tuliskan uraian hasil pelaksanaan tugas...">{{ old('catatan_tindak_lanjut', $task->catatan_tindak_lanjut) }}</textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Unggah Berkas Laporan / Bukti (PDF, DOCX, JPG, PNG)</label>
                                <input type="file" name="file_tindak_lanjut" class="w-full text-xs border border-gray-300 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-900 p-2">
                                @if($task->file_tindak_lanjut)
                                    <p class="text-[11px] text-emerald-600 mt-1">Berkas saat ini telah tersimpan: {{ basename($task->file_tindak_lanjut) }}</p>
                                @endif
                            </div>

                            <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs transition-all shadow-xs">
                                Simpan Laporan
                            </button>
                        </form>
                    @endif
                </div>

                {{-- Informasi Dokumen Surat --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                    <h3 class="font-bold text-base text-gray-900 dark:text-gray-100 pb-3 border-b border-gray-100 dark:border-gray-700">
                        Informasi Dokumen Surat
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-gray-400 block mb-0.5">Nomor Surat</span>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-gray-900 dark:text-gray-100 font-mono text-xs">{{ $letter->nomor_surat }}</span>
                                <button @click="copyText('{{ $letter->nomor_surat }}')" type="button" class="text-gray-400 hover:text-blue-600" title="Salin">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <span class="text-gray-400 block mb-0.5">Nomor Agenda</span>
                            <span class="font-bold text-blue-600 font-mono">{{ $letter->nomor_agenda ? '#' . $letter->nomor_agenda : '-' }}</span>
                        </div>

                        <div>
                            <span class="text-gray-400 block mb-0.5">Asal Surat</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $letter->asal_surat }}</span>
                        </div>

                        <div>
                            <span class="text-gray-400 block mb-0.5">Tanggal Diterima</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $letter->tanggal_diterima ? $letter->tanggal_diterima->translatedFormat('d F Y') : '-' }}</span>
                        </div>

                        <div class="sm:col-span-2">
                            <span class="text-gray-400 block mb-0.5">Perihal</span>
                            <p class="font-bold text-gray-900 dark:text-gray-100 text-xs leading-relaxed">{{ $letter->perihal }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Lampiran Dokumen Surat --}}
            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col justify-between">
                    <div>
                        <h3 class="font-bold text-base text-gray-900 dark:text-gray-100 pb-3 border-b border-gray-100 dark:border-gray-700 mb-4">
                            Lampiran Surat Masuk
                        </h3>

                        @if($letter->file_lampiran)
                            <div class="p-5 bg-indigo-50/50 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900/40 rounded-xl text-center mb-4">
                                <div class="w-10 h-10 mx-auto rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center mb-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                </div>
                                <p class="text-xs font-bold text-gray-800 dark:text-gray-200">Berkas Lampiran Digital</p>
                                <p class="text-[11px] text-gray-400 mt-0.5 truncate">{{ basename($letter->file_lampiran) }}</p>
                            </div>

                            <div class="space-y-2">
                                <button type="button" @click="showPreviewModal = true"
                                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition-all shadow-xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Pratinjau Berkas (In-App)
                                </button>

                                <a href="{{ route('surat-masuk.file', $letter) }}" target="_blank"
                                   class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs font-medium rounded-xl transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    Buka di Tab Baru
                                </a>
                            </div>
                        @else
                            <div class="py-8 text-center text-gray-400 text-xs">
                                Tidak ada berkas digital yang dilampirkan.
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Penerima Tugas Card --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-3 text-xs">
                    <h3 class="font-bold text-sm text-gray-900 dark:text-gray-100 pb-3 border-b border-gray-100 dark:border-gray-700">
                        Identitas Penugasan
                    </h3>
                    <div>
                        <span class="text-gray-400 block mb-0.5">Pegawai Ditugaskan</span>
                        <span class="font-bold text-gray-800 dark:text-gray-200 text-sm block">{{ $task->user?->name ?? 'Anda' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-0.5">Bidang / Unit Kerja</span>
                        <span class="font-semibold text-gray-700 dark:text-gray-300 block">{{ $task->department?->name ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-0.5">Pencatat Pertama</span>
                        <span class="font-semibold text-gray-700 dark:text-gray-300 block">{{ $letter->creator?->name ?? 'Staf Loket' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- IN-APP DOCUMENT PREVIEW MODAL --}}
        @if($letter->file_lampiran)
        <div x-show="showPreviewModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/70 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-5xl overflow-hidden border border-gray-200 dark:border-gray-700 flex flex-col max-h-[92vh]" @click.away="showPreviewModal = false">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-gray-800/80">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <h4 class="font-bold text-sm text-gray-900 dark:text-gray-100 truncate max-w-md">Pratinjau Surat: {{ basename($letter->file_lampiran) }}</h4>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('surat-masuk.file', $letter) }}" target="_blank" class="px-3 py-1.5 text-xs font-semibold bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 text-gray-700 dark:text-gray-200 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            Tab Baru
                        </a>
                        <button type="button" @click="showPreviewModal = false" class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>

                <div class="flex-1 overflow-auto bg-gray-100 dark:bg-gray-900 p-2 flex items-center justify-center min-h-[500px]">
                    <iframe src="{{ route('surat-masuk.file', $letter) }}" class="w-full h-[75vh] rounded-lg border-0 bg-white"></iframe>
                </div>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
