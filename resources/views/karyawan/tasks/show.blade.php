<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        Detail Tugas Disposisi
                    </h2>
                    @php
                        $assignStatusColors = [
                            'belum_dibaca' => 'bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-700 dark:text-gray-300',
                            'dibaca'       => 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-900/60 dark:text-blue-300',
                            'dikerjakan'   => 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-900/60 dark:text-amber-300',
                            'selesai'      => 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-900/60 dark:text-emerald-300',
                        ];
                    @endphp
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full border {{ $assignStatusColors[$task->status] ?? 'bg-gray-100 text-gray-700' }}">
                        Status: {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                    </span>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    No. Surat: <span class="font-mono font-medium text-gray-700 dark:text-gray-300">{{ $letter->nomor_surat }}</span>
                </p>
            </div>

            <a href="{{ route('karyawan.tasks.index') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-xs font-semibold rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar Tugas
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg dark:bg-green-900/50 dark:border-green-700 dark:text-green-200 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="space-y-6">
        {{-- Card Instruksi Disposisi --}}
        <div class="bg-blue-50/70 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
            <div class="flex items-center justify-between pb-3 border-b border-blue-200 dark:border-blue-800 mb-4">
                <h3 class="text-base font-semibold text-blue-900 dark:text-blue-200 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                    </svg>
                    Instruksi & Catatan Disposisi
                </h3>
                <span class="text-xs text-blue-700 dark:text-blue-300">
                    Tanggal Disposisi: <strong>{{ \Carbon\Carbon::parse($task->tanggal_disposisi)->translatedFormat('d F Y') }}</strong>
                </span>
            </div>

            <p class="text-sm font-medium text-gray-800 dark:text-gray-200 leading-relaxed bg-white/70 dark:bg-gray-800/80 p-4 rounded-lg border border-blue-100 dark:border-blue-900">
                {{ $task->catatan }}
            </p>

            @if($task->tanggal_selesai)
                <p class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold mt-3 flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Tugas ini telah ditandai selesai pada: {{ \Carbon\Carbon::parse($task->tanggal_selesai)->translatedFormat('l, d F Y') }}
                </p>
            @endif
        </div>

        {{-- Form Tindak Lanjut & Action Karyawan (Tahap U-10 & U-11) --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 pb-3 border-b border-gray-100 dark:border-gray-700 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                Tindak Lanjut & Laporan Pengerjaan
            </h3>

            <form action="{{ route('karyawan.tasks.status', $task) }}" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-1">
                            Perbarui Status Pengerjaan
                        </label>
                        <select name="status" class="w-full text-sm px-3.5 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="dibaca" {{ $task->status === 'dibaca' ? 'selected' : '' }}>Dibaca (Mempelajari surat)</option>
                            <option value="dikerjakan" {{ $task->status === 'dikerjakan' ? 'selected' : '' }}>Dikerjakan (Sedang menindaklanjuti)</option>
                            <option value="selesai" {{ $task->status === 'selesai' ? 'selected' : '' }}>Selesai (Penugasan rampung)</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-1">
                            Catatan Tindak Lanjut / Respon (Tahap U-11)
                        </label>
                        <textarea name="catatan_tindak_lanjut" rows="2"
                                  placeholder="Contoh: Surat telah ditindaklanjuti dan koordinasi dengan pihak terkait telah dilakukan."
                                  class="w-full text-sm px-3.5 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('catatan_tindak_lanjut', $task->catatan_tindak_lanjut) }}</textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end pt-2">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Perubahan Tindak Lanjut
                    </button>
                </div>
            </form>
        </div>

        {{-- Detail Dokumen Surat Masuk --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Data Pokok Surat --}}
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 pb-3 border-b border-gray-100 dark:border-gray-700 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Informasi Dokumen Surat
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider block">Nomor Surat</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100 font-mono">{{ $letter->nomor_surat }}</span>
                    </div>

                    <div>
                        <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider block">Asal Surat / Pengirim</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $letter->asal_surat }}</span>
                    </div>

                    <div>
                        <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider block">Tanggal Surat</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">
                            {{ \Carbon\Carbon::parse($letter->tanggal_surat)->translatedFormat('l, d F Y') }}
                        </span>
                    </div>

                    <div>
                        <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider block">Tanggal Diterima</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">
                            {{ \Carbon\Carbon::parse($letter->tanggal_diterima)->translatedFormat('l, d F Y') }}
                        </span>
                    </div>

                    <div class="sm:col-span-2">
                        <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider block">Perihal</span>
                        <p class="font-semibold text-gray-900 dark:text-gray-100 text-base mt-0.5">
                            {{ $letter->perihal }}
                        </p>
                    </div>

                    <div class="sm:col-span-2">
                        <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider block">Keterangan Dokumen</span>
                        <div class="mt-1 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-gray-700 dark:text-gray-300 text-sm">
                            {{ $letter->keterangan ?: 'Tidak ada keterangan khusus.' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Lampiran Berkas --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col justify-between">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 pb-3 border-b border-gray-100 dark:border-gray-700 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                        </svg>
                        Lampiran Dokumen
                    </h3>

                    @if($letter->file_lampiran)
                        @php
                            $ext = strtolower(pathinfo($letter->file_lampiran, PATHINFO_EXTENSION));
                            $isImage = in_array($ext, ['jpg', 'jpeg', 'png']);
                            $isPdf = $ext === 'pdf';
                        @endphp

                        @if($isImage)
                            <div class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 max-h-48 flex items-center justify-center p-2 mb-3">
                                <img src="{{ route('surat-masuk.file', $letter) }}" alt="Lampiran Surat" class="max-h-44 object-contain rounded">
                            </div>
                        @elseif($isPdf)
                            <div class="p-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-center mb-3">
                                <svg class="w-10 h-10 mx-auto text-red-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-xs font-semibold text-gray-800 dark:text-gray-200">File PDF Terlampir</p>
                            </div>
                        @endif
                    @else
                        <div class="py-8 text-center text-gray-400 dark:text-gray-500">
                            <p class="text-xs">Tidak ada lampiran scan surat.</p>
                        </div>
                    @endif
                </div>

                @if($letter->file_lampiran)
                    <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                        <a href="{{ route('surat-masuk.file', $letter) }}" target="_blank"
                           class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            Buka / Unduh Lampiran Surat
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
