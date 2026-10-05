<x-app-layout>
    <x-slot name="header">
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
                            'selesai'      => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                            default        => 'bg-gray-100 text-gray-700 border-gray-200'
                        };
                        $statusLabel = match($task->status) {
                            'belum_dibaca' => 'Belum Dibaca',
                            'dibaca'       => 'Sedang Ditelaah (Dibaca)',
                            'dikerjakan'   => 'Dalam Tindak Lanjut',
                            'selesai'      => 'Selesai Dikerjakan',
                            default        => $task->status
                        };
                    @endphp
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full border {{ $assignStatusColors }}">
                        {{ $statusLabel }}
                    </span>
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mt-1">
                    @if($letter->nomor_agenda)
                        <span class="font-bold text-blue-600 dark:text-blue-400">#Agenda {{ $letter->nomor_agenda }}</span>
                        <span>&bull;</span>
                    @endif
                    <span>No. Surat:</span>
                    <span class="font-mono font-medium text-gray-700 dark:text-gray-300">{{ $letter->nomor_surat }}</span>
                </div>
            </div>

            <a href="{{ route('karyawan.tasks.index') }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs font-semibold rounded-xl transition-colors shadow-xs self-start sm:self-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar Tugas
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-xl flex items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <span class="text-sm font-semibold">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Kolom Kiri: Instruksi & Form Tindak Lanjut (2 Kolom) --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Card Instruksi Disposisi --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xs border border-blue-100 dark:border-blue-900/40 overflow-hidden">
                <div class="p-5 bg-gradient-to-r from-blue-50/80 via-blue-50/40 to-transparent dark:from-blue-950/40 dark:via-blue-950/20 dark:to-transparent border-b border-blue-100 dark:border-blue-900/40 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center flex-shrink-0 shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-base text-gray-900 dark:text-gray-100">
                                Instruksi & Catatan Pimpinan
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
                        <div class="px-3 py-1.5 rounded-lg border text-xs font-semibold {{ $isOverdue ? 'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/40 dark:text-red-300 dark:border-red-800' : 'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-900/40 dark:text-amber-300 dark:border-amber-800' }} flex items-center gap-1.5">
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

                <div class="p-6">
                    <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-100 dark:border-gray-700/80 text-gray-800 dark:text-gray-200 text-sm leading-relaxed whitespace-pre-line font-medium">
                        {{ $task->catatan ?: 'Tidak ada instruksi khusus.' }}
                    </div>

                    @if($task->tanggal_selesai)
                        <div class="mt-4 p-3.5 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/80 rounded-xl flex items-center gap-2.5 text-emerald-800 dark:text-emerald-300 text-xs">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Tugas ini telah ditandai rampung pada <strong>{{ $task->tanggal_selesai->translatedFormat('l, d F Y') }}</strong>.</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Formulir Update Tindak Lanjut & Laporan Hasil --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xs border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center gap-2.5 pb-4 border-b border-gray-100 dark:border-gray-700 mb-5">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-base text-gray-900 dark:text-gray-100">
                            Perbarui Laporan Tindak Lanjut
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Perbarui status pengerjaan dan berikan laporan hasil tindak lanjut untuk pimpinan.
                        </p>
                    </div>
                </div>

                @if($task->status === 'perlu_revisi')
                    <div class="mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200">
                        <h4 class="text-sm font-bold text-rose-800 flex items-center gap-2 mb-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Tugas Dikembalikan untuk Direvisi
                        </h4>
                        <p class="text-sm text-rose-700 mt-2">{{ $task->catatan_revisi }}</p>
                    </div>
                @endif

                <form action="{{ route('karyawan.tasks.status', $task) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">
                            Status Pengerjaan <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <label class="relative flex flex-col p-3.5 border rounded-xl cursor-pointer transition-all {{ $task->status === 'dibaca' ? 'border-blue-500 bg-blue-50/50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300' }}">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-bold text-blue-700 dark:text-blue-300">1. Dibaca</span>
                                    <input type="radio" name="status" value="dibaca" {{ $task->status === 'dibaca' ? 'checked' : '' }} class="text-blue-600 focus:ring-blue-500">
                                </div>
                                <span class="text-[11px] text-gray-500 dark:text-gray-400">Sedang mempelajari instruksi surat</span>
                            </label>

                            <label class="relative flex flex-col p-3.5 border rounded-xl cursor-pointer transition-all {{ $task->status === 'dikerjakan' ? 'border-amber-500 bg-amber-50/50 dark:bg-amber-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300' }}">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-bold text-amber-700 dark:text-amber-300">2. Dikerjakan</span>
                                    <input type="radio" name="status" value="dikerjakan" {{ $task->status === 'dikerjakan' ? 'checked' : '' }} class="text-amber-600 focus:ring-amber-500">
                                </div>
                                <span class="text-[11px] text-gray-500 dark:text-gray-400">Proses tindak lanjut / koordinasi</span>
                            </label>

                            <label class="relative flex flex-col p-3.5 border rounded-xl cursor-pointer transition-all {{ in_array($task->status, ['menunggu_verifikasi_kabid', 'selesai']) ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300' }}">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-bold text-emerald-700 dark:text-emerald-300">3. Kirim Hasil</span>
                                    <input type="radio" name="status" value="menunggu_verifikasi_kabid" {{ in_array($task->status, ['menunggu_verifikasi_kabid', 'selesai']) ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                                </div>
                                <span class="text-[11px] text-gray-500 dark:text-gray-400">Kirim laporan ke Kabid</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">
                            Catatan Tindak Lanjut / Respon Pegawai
                        </label>
                        <textarea name="catatan_tindak_lanjut" rows="3"
                                  placeholder="Contoh: Surat telah dikonfirmasi dan berkas fisik telah diserahkan ke Seksi Angkutan untuk verifikasi lapangan."
                                  class="w-full text-sm px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50/50 dark:bg-gray-900/40 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-gray-900 transition-all">{{ old('catatan_tindak_lanjut', $task->catatan_tindak_lanjut) }}</textarea>
                        <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">Catatan ini akan tersimpan dalam riwayat disposisi surat pimpinan.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">
                            File Bukti / Hasil Tindak Lanjut (Opsional)
                        </label>
                        <input type="file" name="file_tindak_lanjut" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-700 dark:file:text-blue-300">
                        @if($task->file_tindak_lanjut)
                            <a href="{{ \Illuminate\Support\Facades\Storage::disk('google')->url($task->file_tindak_lanjut) }}" target="_blank" class="text-xs text-blue-600 mt-2 inline-block font-medium">Lihat file tersimpan saat ini</a>
                        @endif
                    </div>

                    <div class="flex items-center justify-end pt-2">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-xs font-bold rounded-xl transition-all shadow-md hover:shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Simpan Perubahan Laporan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Informasi Lengkap Dokumen Surat Masuk --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xs border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center gap-2.5 pb-4 border-b border-gray-100 dark:border-gray-700 mb-5">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-base text-gray-900 dark:text-gray-100">
                        Informasi Dokumen Surat
                    </h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div class="p-3 bg-gray-50/80 dark:bg-gray-700/40 rounded-xl border border-gray-100 dark:border-gray-700">
                        <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider block font-semibold">Nomor Surat</span>
                        <span class="font-mono font-medium text-gray-900 dark:text-gray-100 mt-0.5 block">{{ $letter->nomor_surat }}</span>
                    </div>

                    <div class="p-3 bg-gray-50/80 dark:bg-gray-700/40 rounded-xl border border-gray-100 dark:border-gray-700">
                        <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider block font-semibold">Asal Pengirim</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100 mt-0.5 block">{{ $letter->asal_surat }}</span>
                    </div>

                    <div class="p-3 bg-gray-50/80 dark:bg-gray-700/40 rounded-xl border border-gray-100 dark:border-gray-700">
                        <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider block font-semibold">Tanggal Surat</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100 mt-0.5 block">
                            {{ $letter->tanggal_surat ? $letter->tanggal_surat->translatedFormat('l, d F Y') : '-' }}
                        </span>
                    </div>

                    <div class="p-3 bg-gray-50/80 dark:bg-gray-700/40 rounded-xl border border-gray-100 dark:border-gray-700">
                        <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider block font-semibold">Tanggal Diterima</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100 mt-0.5 block">
                            {{ $letter->tanggal_diterima ? $letter->tanggal_diterima->translatedFormat('l, d F Y') : '-' }}
                        </span>
                    </div>

                    <div class="sm:col-span-2 p-3 bg-gray-50/80 dark:bg-gray-700/40 rounded-xl border border-gray-100 dark:border-gray-700">
                        <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider block font-semibold">Perihal Surat</span>
                        <p class="font-bold text-gray-900 dark:text-gray-100 text-sm mt-0.5 leading-snug">
                            {{ $letter->perihal }}
                        </p>
                    </div>

                    <div class="sm:col-span-2 p-3 bg-gray-50/80 dark:bg-gray-700/40 rounded-xl border border-gray-100 dark:border-gray-700">
                        <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider block font-semibold">Keterangan Tambahan</span>
                        <p class="text-gray-700 dark:text-gray-300 text-xs mt-0.5">
                            {{ $letter->keterangan ?: 'Tidak ada keterangan tambahan.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Lampiran Dokumen & Identitas Disposisi (1 Kolom) --}}
        <div class="space-y-6">
            {{-- Card Lampiran Berkas --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xs border border-gray-100 dark:border-gray-700 p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2.5 pb-4 border-b border-gray-100 dark:border-gray-700 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-teal-50 dark:bg-teal-900/40 text-teal-600 dark:text-teal-400 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-base text-gray-900 dark:text-gray-100">
                                Lampiran Surat
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Berkas digital hasil scan</p>
                        </div>
                    </div>

                    @if($letter->file_lampiran)
                        @php
                            $ext = strtolower(pathinfo($letter->file_lampiran, PATHINFO_EXTENSION));
                            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
                            $isPdf = $ext === 'pdf';
                        @endphp

                        @if($isImage)
                            <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/60 p-2 mb-4 group relative flex items-center justify-center">
                                <img src="{{ route('surat-masuk.file', $letter) }}" alt="Lampiran Surat" class="max-h-60 object-contain rounded-lg">
                            </div>
                        @elseif($isPdf)
                            <div class="p-6 bg-red-50/70 dark:bg-red-950/30 border border-red-200 dark:border-red-900/50 rounded-xl text-center mb-4">
                                <div class="w-12 h-12 mx-auto rounded-xl bg-red-100 dark:bg-red-900/60 text-red-600 dark:text-red-400 flex items-center justify-center mb-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <p class="text-xs font-bold text-gray-900 dark:text-gray-100">Dokumen PDF Terlampir</p>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Klik tombol di bawah untuk membaca berkas lengkap.</p>
                            </div>
                        @else
                            <div class="p-5 bg-gray-50 dark:bg-gray-700/50 rounded-xl text-center mb-4">
                                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">File Berkas Lampiran</p>
                            </div>
                        @endif
                    @else
                        <div class="py-10 text-center text-gray-400 dark:text-gray-500">
                            <svg class="w-10 h-10 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-xs">Tidak ada berkas scan yang dilampirkan.</p>
                        </div>
                    @endif
                </div>

                @if($letter->file_lampiran)
                    <div class="pt-3 border-t border-gray-100 dark:border-gray-700">
                        <a href="{{ route('surat-masuk.file', $letter) }}" target="_blank"
                           class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-xs font-semibold rounded-xl transition-all shadow-sm hover:shadow">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            Buka / Unduh Berkas Surat
                        </a>
                    </div>
                @endif
            </div>

            {{-- Card Target Disposisi --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xs border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="font-bold text-sm text-gray-900 dark:text-gray-100 pb-3 border-b border-gray-100 dark:border-gray-700 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Penerima Tugas
                </h3>
                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-gray-400 dark:text-gray-500 uppercase tracking-wider block font-medium">Pegawai Ditugaskan</span>
                        <span class="font-bold text-gray-800 dark:text-gray-200 mt-0.5 block text-sm">
                            {{ $task->user?->name ?? 'Semua Pegawai Unit' }}
                        </span>
                        @if($task->user?->nip)
                            <span class="text-[11px] font-mono text-gray-400 dark:text-gray-500">NIP: {{ $task->user->nip }}</span>
                        @endif
                    </div>
                    <div>
                        <span class="text-gray-400 dark:text-gray-500 uppercase tracking-wider block font-medium">Bidang / Unit Kerja</span>
                        <span class="font-semibold text-gray-700 dark:text-gray-300 mt-0.5 block">
                            {{ $task->department?->name ?? ($task->user?->department?->name ?? '-') }}
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-400 dark:text-gray-500 uppercase tracking-wider block font-medium">Pemberi Disposisi</span>
                        <span class="font-semibold text-gray-700 dark:text-gray-300 mt-0.5 block">
                            {{ $letter->creator?->name ?? 'Pimpinan / Staf Loket' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
