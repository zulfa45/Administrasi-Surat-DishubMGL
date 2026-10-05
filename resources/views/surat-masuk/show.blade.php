<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        Detail Surat Masuk
                    </h2>
                    @php
                        $statusLabels = [
                            'baru' => ['label' => 'Baru', 'class' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300 border-blue-200 dark:border-blue-700'],
                            'didistribusikan' => ['label' => 'Didistribusikan', 'class' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-300 border-indigo-200 dark:border-indigo-700'],
                            'dalam_tindak_lanjut' => ['label' => 'Tindak Lanjut', 'class' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/60 dark:text-yellow-300 border-yellow-200 dark:border-yellow-700'],
                            'selesai' => ['label' => 'Selesai', 'class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-700'],
                        ];
                        $st = $statusLabels[$letter->status] ?? ['label' => $letter->status, 'class' => 'bg-gray-100 text-gray-700'];
                    @endphp
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full border {{ $st['class'] }}">
                        {{ $st['label'] }}
                    </span>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    No: <span class="font-mono font-medium text-gray-700 dark:text-gray-300">{{ $letter->nomor_surat }}</span>
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('surat-masuk.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
                @hasanyrole('admin|staf-loket')
                <a href="{{ route('disposisi.create', $letter) }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    Buat Disposisi
                </a>
                @endhasanyrole
                <a href="{{ route('surat-masuk.disposisi-pdf', $letter) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm"
                   title="Buka / Cetak Lembar Disposisi PDF di tab baru">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Cetak Disposisi (PDF)
                </a>
                <a href="{{ route('surat-masuk.edit', $letter) }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </a>
                <form action="{{ route('surat-masuk.destroy', $letter) }}" method="POST"
                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus surat masuk ini? Tindakan ini tidak dapat dibatalkan.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Hapus
                    </button>
                </form>
            </div>
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
        {{-- Card Informasi Utama Surat --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Data Surat --}}
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="border-b border-gray-100 dark:border-gray-700 pb-4 mb-4 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Informasi Dokumen Surat
                    </h3>
                    @php
                        $sifatColors = [
                            'biasa' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                            'penting' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300',
                            'segera' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/60 dark:text-orange-300',
                            'rahasia' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/60 dark:text-purple-300',
                        ];
                    @endphp
                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $sifatColors[$letter->sifat] ?? 'bg-gray-100 text-gray-700' }}">
                        Sifat: {{ ucfirst($letter->sifat) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider block">Nomor Agenda</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100 font-mono">{{ $letter->nomor_agenda }}</span>
                    </div>

                    <div>
                        <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider block">Nomor Surat (Fisik)</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100 font-mono">{{ $letter->nomor_surat }}</span>
                    </div>

                    <div>
                        <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider block">Asal Surat / Pengirim</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $letter->asal_surat }}</span>
                    </div>

                    <div>
                        <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider block">Tanggal Pada Naskah</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">
                            {{ \Carbon\Carbon::parse($letter->tanggal_surat)->translatedFormat('l, d F Y') }}
                        </span>
                    </div>

                    <div>
                        <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider block">Tanggal Diterima Loket</span>
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
                        <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider block">Keterangan</span>
                        <div class="mt-1 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-gray-700 dark:text-gray-300 text-sm">
                            {{ $letter->keterangan ?: 'Tidak ada keterangan tambahan.' }}
                        </div>
                    </div>

                    <div class="sm:col-span-2 pt-2 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span>Petugas Pencatat: <strong class="text-gray-700 dark:text-gray-300">{{ $letter->creator?->name ?? 'Sistem' }}</strong></span>
                        <span>Dicatat: {{ $letter->created_at->translatedFormat('d M Y H:i') }}</span>
                    </div>
                </div>
            </div>

            {{-- Card Lampiran Berkas (Tahap U-04) --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col justify-between">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-gray-700 pb-4 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                        </svg>
                        Lampiran Berkas
                    </h3>

                    @if($letter->file_lampiran)
                        @php
                            $ext = strtolower(pathinfo($letter->file_lampiran, PATHINFO_EXTENSION));
                            $isImage = in_array($ext, ['jpg', 'jpeg', 'png']);
                            $isPdf = $ext === 'pdf';
                        @endphp

                        <div class="space-y-4">
                            @if($isImage)
                                <div class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 max-h-56 flex items-center justify-center p-2">
                                    <img src="{{ route('surat-masuk.file', $letter) }}" alt="Lampiran Surat" class="max-h-52 object-contain rounded">
                                </div>
                            @elseif($isPdf)
                                <div class="p-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-center">
                                    <svg class="w-12 h-12 mx-auto text-red-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Dokumen PDF Terlampir</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-mono truncate">{{ basename($letter->file_lampiran) }}</p>
                                </div>
                            @else
                                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg text-center">
                                    <p class="text-xs font-mono text-gray-600 dark:text-gray-300">{{ basename($letter->file_lampiran) }}</p>
                                </div>
                            @endif

                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                Berkas tersimpan di penyimpanan aman sistem.
                            </div>
                        </div>
                    @else
                        <div class="py-10 text-center text-gray-400 dark:text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-sm font-medium">Tidak ada file lampiran</p>
                            <p class="text-xs mt-1">Surat dicatat tanpa scan dokumen digital.</p>
                        </div>
                    @endif
                </div>

                @if($letter->file_lampiran)
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <a href="{{ route('surat-masuk.file', $letter) }}" target="_blank"
                           class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            Buka / Unduh Lampiran
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Section Bagian Bawah: RIWAYAT DISPOSISI (Tahap U-05) --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <div class="border-b border-gray-100 dark:border-gray-700 pb-4 mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                        RIWAYAT DISPOSISI
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Daftar penerima penugasan dan status tindak lanjut surat
                    </p>
                </div>
                @hasanyrole('admin|staf-loket')
                <a href="{{ route('disposisi.create', $letter) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Disposisi
                </a>
                @endhasanyrole
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700 dark:text-gray-300">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3">Penerima Disposisi</th>
                            <th class="px-4 py-3">Tgl Disposisi</th>
                            <th class="px-4 py-3">Instruksi / Catatan</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3">Tindak Lanjut / Respon</th>
                            <th class="px-4 py-3 text-center">Aksi & Update</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($letter->assignments as $assignment)
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/50 transition-colors" x-data="{ openUpdate: false }">
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $assignment->user?->name ?? 'Seluruh Anggota Bagian' }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    Bagian: {{ $assignment->department?->name ?? ($assignment->user?->department?->name ?? '-') }}
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600 dark:text-gray-400">
                                <div>{{ $assignment->tanggal_disposisi ? \Carbon\Carbon::parse($assignment->tanggal_disposisi)->translatedFormat('d M Y') : $assignment->created_at->translatedFormat('d M Y') }}</div>
                                @if($assignment->tanggal_selesai)
                                    <span class="inline-block mt-1 text-[10px] text-emerald-600 dark:text-emerald-400 font-medium">
                                        Selesai: {{ \Carbon\Carbon::parse($assignment->tanggal_selesai)->translatedFormat('d M Y') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300 text-xs max-w-xs">
                                {{ $assignment->catatan ?: '-' }}
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                @php
                                    $assignStatusColors = [
                                        'belum_dibaca' => 'bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-700 dark:text-gray-300',
                                        'dibaca'       => 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-900/60 dark:text-blue-300',
                                        'dikerjakan'   => 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-900/60 dark:text-amber-300',
                                        'selesai'      => 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-900/60 dark:text-emerald-300',
                                    ];
                                @endphp
                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full border {{ $assignStatusColors[$assignment->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst(str_replace('_', ' ', $assignment->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300 text-xs max-w-xs">
                                @if($assignment->catatan_tindak_lanjut)
                                    <p class="italic text-gray-800 dark:text-gray-200">"{{ $assignment->catatan_tindak_lanjut }}"</p>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500 italic">Belum ada catatan tindak lanjut</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    {{-- Tombol Update Status (Tahap U-08 & U-11) --}}
                                    <button type="button" @click="openUpdate = !openUpdate"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/40 dark:text-blue-300 dark:hover:bg-blue-900/60 rounded-lg transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Update Status
                                    </button>

                                    @hasanyrole('admin|staf-loket')
                                    <form action="{{ route('disposisi.destroy', $assignment) }}" method="POST"
                                          onsubmit="return confirm('Batalkan penugasan disposisi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors" title="Batalkan Disposisi">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                    @endhasanyrole
                                </div>

                                {{-- Dropdown Form Update Status --}}
                                <div x-show="openUpdate" @click.away="openUpdate = false" x-transition
                                     class="mt-3 p-4 bg-gray-50 dark:bg-gray-700/80 rounded-lg border border-gray-200 dark:border-gray-600 text-left space-y-3" style="display: none;">
                                    <form action="{{ route('disposisi.status', $assignment) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Status Disposisi:</label>
                                            <select name="status" class="w-full text-xs px-2.5 py-1.5 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                                                <option value="belum_dibaca" {{ $assignment->status === 'belum_dibaca' ? 'selected' : '' }}>Belum Dibaca</option>
                                                <option value="dibaca" {{ $assignment->status === 'dibaca' ? 'selected' : '' }}>Dibaca</option>
                                                <option value="dikerjakan" {{ $assignment->status === 'dikerjakan' ? 'selected' : '' }}>Dikerjakan</option>
                                                <option value="selesai" {{ $assignment->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Catatan Tindak Lanjut:</label>
                                            <textarea name="catatan_tindak_lanjut" rows="2" placeholder="Tuliskan respon / laporan pengerjaan..."
                                                      class="w-full text-xs px-2.5 py-1.5 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">{{ $assignment->catatan_tindak_lanjut }}</textarea>
                                        </div>
                                        <div class="flex items-center justify-end gap-2 pt-1">
                                            <button type="button" @click="openUpdate = false" class="text-xs px-2 py-1 bg-gray-200 dark:bg-gray-600 rounded text-gray-700 dark:text-gray-300">Tutup</button>
                                            <button type="submit" class="text-xs px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded font-medium">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">
                                <svg class="w-10 h-10 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <p class="text-sm font-medium">Belum ada riwayat disposisi</p>
                                <p class="text-xs mt-0.5">Klik "Buat Disposisi" di atas untuk menugaskan surat ini ke pegawai atau bagian.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
