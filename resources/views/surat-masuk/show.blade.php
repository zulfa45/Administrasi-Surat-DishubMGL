<x-app-layout>
    <x-slot name="header">
        <div class="space-y-2">
            {{-- Breadcrumb Navigation --}}
            <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Dashboard
                </a>
                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                <a href="{{ route('surat-masuk.index') }}" class="hover:text-blue-600 transition-colors">Surat Masuk</a>
                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                <span class="text-gray-800 dark:text-gray-200 font-semibold truncate max-w-xs">
                    {{ $letter->nomor_agenda ? '#' . $letter->nomor_agenda : $letter->nomor_surat }}
                </span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex flex-wrap items-center gap-2.5">
                        <h2 class="font-bold text-xl text-gray-900 dark:text-gray-100 leading-tight">
                            Detail Surat Masuk
                        </h2>
                        @php
                            $statusLabels = [
                                'baru' => ['label' => 'Surat Baru', 'class' => 'bg-blue-100 text-blue-800 dark:bg-blue-950/40 dark:text-blue-300 border-blue-200 dark:border-blue-800'],
                                'didistribusikan' => ['label' => 'Didistribusikan', 'class' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950/40 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800'],
                                'dalam_tindak_lanjut' => ['label' => 'Dalam Tindak Lanjut', 'class' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-950/40 dark:text-yellow-300 border-yellow-200 dark:border-yellow-800'],
                                'selesai' => ['label' => 'Selesai & Diarsipkan', 'class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800'],
                            ];
                            $st = $statusLabels[$letter->status] ?? ['label' => ucfirst($letter->status), 'class' => 'bg-gray-100 text-gray-700'];
                        @endphp
                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full border {{ $st['class'] }}">
                            {{ $st['label'] }}
                        </span>
                    </div>
                </div>

                {{-- Action Buttons Header --}}
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('surat-masuk.index') }}"
                       class="inline-flex items-center gap-1.5 px-3 py-2 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 text-xs font-semibold rounded-xl transition-colors shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Kembali
                    </a>

                    @hasanyrole('admin|staf-loket')
                    <a href="{{ route('disposisi.create', $letter) }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        Teruskan Disposisi
                    </a>
                    @endhasanyrole

                    <a href="{{ route('surat-masuk.disposisi-pdf', $letter) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl transition-all shadow-sm"
                       title="Cetak Lembar Disposisi PDF">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Cetak Disposisi
                    </a>

                    <a href="{{ route('surat-masuk.edit', $letter) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold rounded-xl transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Edit
                    </a>

                    <form action="{{ route('surat-masuk.destroy', $letter) }}" method="POST"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus surat masuk ini? Tindakan ini tidak dapat dibatalkan.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/30 dark:text-rose-400 dark:hover:bg-rose-900/50 border border-rose-200 dark:border-rose-800 text-xs font-semibold rounded-xl transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Hapus
                        </button>
                    </form>
                </div>
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

        {{-- VISUAL TRACKING STEPPER (Timeline Perjalanan Surat) --}}
        @php
            $hasAssignments = $letter->assignments->count() > 0;
            $hasStaffWorking = $letter->assignments->whereIn('status', ['belum_dibaca', 'dibaca', 'dikerjakan', 'menunggu_verifikasi_kabid'])->count() > 0;
            $isCompleted = $letter->status === 'selesai';
            $step2Active = $hasAssignments;
            $step3Active = $hasStaffWorking || $isCompleted;
            $step4Active = $isCompleted;
        @endphp
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-6 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Alur & Status Perjalanan Surat
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 relative">
                {{-- Step 1: Registrasi --}}
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400 flex items-center justify-center font-bold text-xs flex-shrink-0 ring-4 ring-emerald-50 dark:ring-emerald-950/30">
                        ✓
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-gray-900 dark:text-gray-100">1. Registrasi Loket</p>
                        <p class="text-[11px] text-gray-500 mt-0.5">{{ $letter->creator?->name ?? 'Staf Loket' }}</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($letter->tanggal_diterima)->translatedFormat('d M Y') }}</p>
                    </div>
                </div>

                {{-- Step 2: Disposisi ke Bidang --}}
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full {{ $step2Active ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400 ring-4 ring-emerald-50' : 'bg-gray-100 text-gray-400 dark:bg-gray-700' }} flex items-center justify-center font-bold text-xs flex-shrink-0">
                        {{ $step2Active ? '✓' : '2' }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold {{ $step2Active ? 'text-gray-900 dark:text-gray-100' : 'text-gray-400' }}">2. Diteruskan ke Bidang</p>
                        @if($hasAssignments)
                            @php
                                $deptNames = $letter->assignments->map(fn($a) => $a->department?->name)->filter()->unique()->take(2);
                            @endphp
                            <p class="text-[11px] text-indigo-600 dark:text-indigo-400 mt-0.5 font-medium truncate">{{ $deptNames->implode(', ') }}</p>
                        @else
                            <p class="text-[11px] text-amber-500 mt-0.5 font-medium">Menunggu Disposisi</p>
                        @endif
                    </div>
                </div>

                {{-- Step 3: Tindak Lanjut Staf --}}
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full {{ $step3Active ? ($isCompleted ? 'bg-emerald-100 text-emerald-600 ring-4 ring-emerald-50' : 'bg-blue-100 text-blue-600 ring-4 ring-blue-50 animate-pulse') : 'bg-gray-100 text-gray-400 dark:bg-gray-700' }} flex items-center justify-center font-bold text-xs flex-shrink-0">
                        {{ $isCompleted ? '✓' : '3' }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold {{ $step3Active ? 'text-gray-900 dark:text-gray-100' : 'text-gray-400' }}">3. Tindak Lanjut Staf</p>
                        <p class="text-[11px] text-gray-500 mt-0.5">
                            @if($isCompleted)
                                Selesai Dikerjakan
                            @elseif($hasStaffWorking)
                                Sedang Diproses
                            @else
                                Menunggu Penugasan
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Step 4: Selesai & Arsip --}}
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full {{ $step4Active ? 'bg-emerald-100 text-emerald-600 ring-4 ring-emerald-50' : 'bg-gray-100 text-gray-400 dark:bg-gray-700' }} flex items-center justify-center font-bold text-xs flex-shrink-0">
                        {{ $step4Active ? '✓' : '4' }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold {{ $step4Active ? 'text-gray-900 dark:text-gray-100' : 'text-gray-400' }}">4. Selesai & Arsip</p>
                        <p class="text-[11px] {{ $step4Active ? 'text-emerald-600 font-semibold' : 'text-gray-400' }} mt-0.5">
                            {{ $step4Active ? 'Tuntas Diarsipkan' : 'Belum Selesai' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Grid Informasi Utama & Lampiran --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Data Surat (2 Kolom) --}}
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                <div class="border-b border-gray-100 dark:border-gray-700 pb-4 flex items-center justify-between">
                    <h3 class="text-base font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Informasi Dokumen Surat
                    </h3>
                    @php
                        $sifatColors = match(strtolower($letter->sifat)) {
                            'penting' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                            'segera' => 'bg-orange-100 text-orange-800 dark:bg-orange-950/40 dark:text-orange-300 border-orange-200 dark:border-orange-800',
                            'rahasia' => 'bg-purple-100 text-purple-800 dark:bg-purple-950/40 dark:text-purple-300 border-purple-200 dark:border-purple-800',
                            default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600'
                        };
                    @endphp
                    <span class="px-3 py-1 text-xs font-semibold rounded-full border {{ $sifatColors }}">
                        Sifat: {{ ucfirst($letter->sifat) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-sm">
                    <div>
                        <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider block font-medium">Nomor Surat</span>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="font-mono font-bold text-gray-900 dark:text-gray-100 text-sm">{{ $letter->nomor_surat }}</span>
                            <button @click="copyText('{{ $letter->nomor_surat }}')" type="button" class="text-gray-400 hover:text-blue-600 transition-colors" title="Salin Nomor Surat">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider block font-medium">Nomor Agenda</span>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="font-bold text-blue-700 dark:text-blue-400 font-mono">{{ $letter->nomor_agenda ? '#' . $letter->nomor_agenda : '-' }}</span>
                        </div>
                    </div>

                    <div>
                        <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider block font-medium">Asal Surat / Pengirim</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200 mt-0.5 block">{{ $letter->asal_surat }}</span>
                    </div>

                    <div>
                        <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider block font-medium">Tanggal Diterima Loket</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200 mt-0.5 block">
                            {{ \Carbon\Carbon::parse($letter->tanggal_diterima)->translatedFormat('l, d F Y') }}
                        </span>
                    </div>

                    <div>
                        <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider block font-medium">Tanggal Surat</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200 mt-0.5 block">
                            {{ \Carbon\Carbon::parse($letter->tanggal_surat)->translatedFormat('d F Y') }}
                        </span>
                    </div>

                    <div>
                        <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider block font-medium">Petugas Pencatat</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200 mt-0.5 block">
                            {{ $letter->creator?->name ?? 'Sistem' }}
                        </span>
                    </div>

                    <div class="sm:col-span-2">
                        <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider block font-medium">Perihal Surat</span>
                        <p class="font-bold text-gray-900 dark:text-gray-100 text-base mt-1 leading-snug">
                            {{ $letter->perihal }}
                        </p>
                    </div>

                    @if($letter->keterangan)
                    <div class="sm:col-span-2">
                        <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider block font-medium">Catatan / Ringkasan</span>
                        <div class="mt-1 p-3.5 bg-gray-50 dark:bg-gray-700/50 rounded-xl text-gray-700 dark:text-gray-300 text-xs border border-gray-100 dark:border-gray-700 leading-relaxed">
                            {{ $letter->keterangan }}
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Feedback Toast "Tersalin" --}}
                <div x-show="copiedNumber" x-transition style="display: none;" class="p-2 bg-emerald-600 text-white text-xs rounded-lg text-center font-medium shadow-sm">
                    Nomor surat berhasil disalin ke clipboard!
                </div>
            </div>

            {{-- Lampiran Berkas & In-App Preview (1 Kolom) --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col justify-between">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-gray-700 pb-4 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                        </svg>
                        Lampiran Digital
                    </h3>

                    @if($letter->file_lampiran)
                        @php
                            $ext = strtolower(pathinfo($letter->file_lampiran, PATHINFO_EXTENSION));
                            $isImage = in_array($ext, ['jpg', 'jpeg', 'png']);
                            $isPdf = $ext === 'pdf';
                        @endphp

                        <div class="space-y-4">
                            @if($isImage)
                                <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 p-2 text-center cursor-pointer hover:opacity-90 transition-opacity" @click="showPreviewModal = true">
                                    <img src="{{ route('surat-masuk.file', $letter) }}" alt="Lampiran Surat" class="max-h-44 mx-auto object-contain rounded-lg">
                                </div>
                            @else
                                <div class="p-6 bg-indigo-50/50 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900/40 rounded-xl text-center">
                                    <div class="w-12 h-12 mx-auto rounded-xl bg-indigo-100 dark:bg-indigo-900/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mb-2">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-xs font-bold text-gray-800 dark:text-gray-200 uppercase">Dokumen PDF Terlampir</p>
                                    <p class="text-[11px] text-gray-400 mt-1 font-mono truncate">{{ basename($letter->file_lampiran) }}</p>
                                </div>
                            @endif

                            <div class="space-y-2 pt-2">
                                <button type="button" @click="showPreviewModal = true"
                                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition-all shadow-xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Pratinjau Cepat (In-App)
                                </button>

                                <a href="{{ route('surat-masuk.file', $letter) }}" target="_blank"
                                   class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-xs font-medium rounded-xl transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    Buka di Tab Baru
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="py-10 text-center text-gray-400 dark:text-gray-500">
                            <div class="w-12 h-12 mx-auto rounded-xl bg-gray-100 dark:bg-gray-700/50 flex items-center justify-center mb-2">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400">Tidak ada lampiran</p>
                            <p class="text-[11px] text-gray-400 mt-0.5">Surat dicatat tanpa scan dokumen digital.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- RIWAYAT DISPOSISI BIDANG & STAF --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
            <div class="border-b border-gray-100 dark:border-gray-700 pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                        Riwayat Penugasan & Disposisi ({{ $letter->assignments->count() }})
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Daftar bidang dan staf pelaksana yang menerima instruksi tindak lanjut
                    </p>
                </div>
                @hasanyrole('admin|staf-loket')
                <a href="{{ route('disposisi.create', $letter) }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl transition-all shadow-xs self-start sm:self-auto">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Disposisi
                </a>
                @endhasanyrole
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
                        <tr>
                            <th class="px-5 py-3.5 font-semibold">Tujuan (Bidang / Staf)</th>
                            <th class="px-5 py-3.5 font-semibold">Instruksi Pimpinan</th>
                            <th class="px-5 py-3.5 font-semibold">Batas Waktu</th>
                            <th class="px-5 py-3.5 font-semibold text-center">Status</th>
                            <th class="px-5 py-3.5 font-semibold">Laporan Tindak Lanjut</th>
                            <th class="px-5 py-3.5 font-semibold text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($letter->assignments as $assignment)
                            @php
                                $assignBadge = match($assignment->status) {
                                    'selesai' => ['class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800', 'label' => 'Selesai'],
                                    'menunggu_verifikasi_kabid' => ['class' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 border-amber-200 dark:border-amber-800', 'label' => 'Menunggu Verifikasi'],
                                    'perlu_revisi' => ['class' => 'bg-rose-100 text-rose-800 dark:bg-rose-950/40 dark:text-rose-300 border-rose-200 dark:border-rose-800', 'label' => 'Perlu Revisi'],
                                    'belum_dibaca' => ['class' => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-600', 'label' => $assignment->user ? 'Belum Dibaca' : 'Menunggu Disposisi'],
                                    default => ['class' => 'bg-blue-100 text-blue-800 border-blue-200', 'label' => ucfirst(str_replace('_', ' ', $assignment->status))]
                                };
                            @endphp
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/40 transition-colors" x-data="{ openUpdate: false }">
                                <td class="px-5 py-4 align-top">
                                    <div class="font-bold text-gray-900 dark:text-gray-100 text-xs">
                                        {{ $assignment->department?->name ?? 'Bidang Belum Terdaftar' }}
                                    </div>
                                    <div class="flex items-center gap-1.5 mt-1">
                                        @if($assignment->user)
                                            <div class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-[10px]">
                                                {{ substr($assignment->user->name, 0, 1) }}
                                            </div>
                                            <span class="text-xs text-gray-600 dark:text-gray-300 font-medium">{{ $assignment->user->name }}</span>
                                        @else
                                            <span class="text-xs text-amber-600 font-medium">Ditujukan ke Kabid (Belum ada staf)</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-5 py-4 align-top max-w-xs text-xs">
                                    @if($assignment->catatan)
                                        <div class="text-gray-800 dark:text-gray-200">{{ $assignment->catatan }}</div>
                                    @endif
                                    @if($assignment->catatan_kabid)
                                        <div class="text-[11px] text-indigo-600 dark:text-indigo-400 mt-1 italic">
                                            Instruksi Kabid: "{{ $assignment->catatan_kabid }}"
                                        </div>
                                    @endif
                                    @if(!$assignment->catatan && !$assignment->catatan_kabid)
                                        <span class="text-gray-400 italic">Tidak ada catatan</span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 align-top whitespace-nowrap text-xs">
                                    @if($assignment->deadline)
                                        <span class="font-medium text-gray-800 dark:text-gray-200">
                                            {{ \Carbon\Carbon::parse($assignment->deadline)->translatedFormat('d M Y') }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 italic">Tidak ada batas</span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 align-top text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold rounded-full border {{ $assignBadge['class'] }}">
                                        {{ $assignBadge['label'] }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 align-top max-w-xs text-xs">
                                    @if($assignment->catatan_tindak_lanjut)
                                        <p class="text-gray-800 dark:text-gray-200 italic line-clamp-2">"{{ $assignment->catatan_tindak_lanjut }}"</p>
                                    @else
                                        <span class="text-gray-400 italic">Belum ada respon</span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 align-top text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" @click="openUpdate = !openUpdate"
                                                class="px-2.5 py-1 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                                            Status
                                        </button>

                                        @hasanyrole('admin|staf-loket')
                                        <form action="{{ route('disposisi.destroy', $assignment) }}" method="POST"
                                              onsubmit="return confirm('Batalkan penugasan disposisi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-gray-400 hover:text-rose-600 transition-colors" title="Batalkan Disposisi">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                        @endhasanyrole
                                    </div>

                                    {{-- Quick Update Dropdown --}}
                                    <div x-show="openUpdate" @click.away="openUpdate = false" x-transition
                                         class="mt-2 p-3 bg-gray-50 dark:bg-gray-700/80 rounded-xl border border-gray-200 dark:border-gray-600 text-left space-y-2.5" style="display: none;">
                                        <form action="{{ route('disposisi.status', $assignment) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <div>
                                                <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-300 mb-1">Ubah Status:</label>
                                                <select name="status" class="w-full text-xs p-1.5 border border-gray-300 rounded-lg bg-white dark:bg-gray-800">
                                                    <option value="belum_dibaca" {{ $assignment->status === 'belum_dibaca' ? 'selected' : '' }}>Belum Dibaca</option>
                                                    <option value="dibaca" {{ $assignment->status === 'dibaca' ? 'selected' : '' }}>Dibaca</option>
                                                    <option value="dikerjakan" {{ $assignment->status === 'dikerjakan' ? 'selected' : '' }}>Dikerjakan</option>
                                                    <option value="selesai" {{ $assignment->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                                </select>
                                            </div>
                                            <div class="flex items-center justify-end gap-1.5 pt-1">
                                                <button type="button" @click="openUpdate = false" class="text-[11px] px-2 py-1 bg-gray-200 rounded text-gray-600">Batal</button>
                                                <button type="submit" class="text-[11px] px-2.5 py-1 bg-blue-600 text-white rounded font-medium">Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-gray-400">
                                    <p class="text-xs font-medium">Surat ini belum didisposisikan ke bidang manapun.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- IN-APP DOCUMENT PREVIEW MODAL --}}
        @if($letter->file_lampiran)
        <div x-show="showPreviewModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/70 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-5xl overflow-hidden border border-gray-200 dark:border-gray-700 flex flex-col max-h-[92vh]" @click.away="showPreviewModal = false">
                {{-- Modal Header --}}
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-gray-800/80">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <h4 class="font-bold text-sm text-gray-900 dark:text-gray-100 truncate max-w-md">Pratinjau Dokumen: {{ basename($letter->file_lampiran) }}</h4>
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

                {{-- Modal Body --}}
                <div class="flex-1 overflow-auto bg-gray-100 dark:bg-gray-900 p-2 flex items-center justify-center min-h-[500px]">
                    @php
                        $ext = strtolower(pathinfo($letter->file_lampiran, PATHINFO_EXTENSION));
                    @endphp
                    @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                        <img src="{{ route('surat-masuk.file', $letter) }}" alt="Preview Dokumen" class="max-h-[75vh] object-contain rounded-lg">
                    @else
                        <iframe src="{{ route('surat-masuk.file', $letter) }}" class="w-full h-[75vh] rounded-lg border-0 bg-white"></iframe>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
