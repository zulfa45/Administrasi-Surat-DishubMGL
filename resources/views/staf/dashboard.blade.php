<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m3-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Dashboard Staf Loket
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Penerimaan, pencatatan surat masuk, dan pengelolaan distribusi disposisi.</p>
            </div>
            <div>
                <a href="{{ route('surat-masuk.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-lg transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Input Surat Masuk
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-ui.welcome-banner />

        {{-- Highlight Surat Segera / Penting --}}
        @if($suratSegera > 0)
            <div class="p-4 bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800 rounded-xl flex items-center justify-between text-amber-800 dark:text-amber-300">
                <div class="flex items-center gap-3">
                    <span class="p-2 rounded-lg bg-amber-100 dark:bg-amber-800/60 text-amber-600 dark:text-amber-200 flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </span>
                    <div>
                        <p class="text-sm font-bold">Perhatian: Ada {{ $suratSegera }} Surat Mendesak / Penting</p>
                        <p class="text-xs text-amber-700 dark:text-amber-400">Surat dengan sifat Segera atau Penting memerlukan tindak lanjut segera oleh penerima.</p>
                    </div>
                </div>
                <a href="{{ route('surat-masuk.index') }}" class="text-xs font-bold text-amber-900 dark:text-amber-200 underline hover:no-underline hidden sm:block whitespace-nowrap">
                    Periksa Surat &rarr;
                </a>
            </div>
        @endif

        {{-- Statistik Cards --}}
        <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-4">
            {{-- Surat Hari Ini --}}
            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-xs border border-gray-100 dark:border-gray-700 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-300 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Diterima Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-0.5">{{ $suratHariIni }}</p>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500">Total Keseluruhan: {{ $totalSurat }}</p>
                </div>
            </div>

            {{-- Perlu Disposisi (Baru) --}}
            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-xs border border-gray-100 dark:border-gray-700 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-300 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Surat Baru</p>
                    <p class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-0.5">{{ $suratBaru }}</p>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500">Belum didisposisikan</p>
                </div>
            </div>

            {{-- Dalam Tindak Lanjut --}}
            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-xs border border-gray-100 dark:border-gray-700 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/50 text-purple-600 dark:text-purple-300 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Proses Tindak Lanjut</p>
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-0.5">{{ $suratProses }}</p>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500">Sedang dikerjakan tim</p>
                </div>
            </div>

            {{-- Selesai --}}
            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-xs border border-gray-100 dark:border-gray-700 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-100 dark:bg-green-900/50 text-green-600 dark:text-green-300 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Surat Selesai</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-0.5">{{ $suratSelesai }}</p>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500">Tuntas ditindaklanjuti</p>
                </div>
            </div>
        </div>

        {{-- Quick Actions Navigation --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <a href="{{ route('surat-masuk.create') }}" class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-500 transition-all group flex flex-col items-center text-center">
                <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </div>
                <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">Input Surat Baru</span>
            </a>

            <a href="{{ route('surat-masuk.index') }}" class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-500 transition-all group flex flex-col items-center text-center">
                <div class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">Daftar Surat Masuk</span>
            </a>

            <a href="{{ route('laporan.index') }}" class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-500 transition-all group flex flex-col items-center text-center">
                <div class="w-10 h-10 rounded-lg bg-teal-50 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">Rekap Laporan PDF</span>
            </a>

            <a href="{{ route('notifications.index') }}" class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-500 transition-all group flex flex-col items-center text-center">
                <div class="w-10 h-10 rounded-lg bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </div>
                <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">Notifikasi Masuk</span>
            </a>
        </div>

        {{-- Tabel Surat Masuk Terbaru --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xs border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-base text-gray-900 dark:text-gray-100">Surat Masuk Terbaru</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Daftar surat yang baru dicatat di loket administrasi.</p>
                </div>
                <a href="{{ route('surat-masuk.index') }}" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline inline-flex items-center gap-1">
                    <span>Lihat Semua Surat</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700/50 dark:text-gray-300 border-b border-gray-100 dark:border-gray-700">
                        <tr>
                            <th scope="col" class="px-5 py-3">No. Agenda / Surat</th>
                            <th scope="col" class="px-5 py-3">Asal & Perihal</th>
                            <th scope="col" class="px-5 py-3">Tgl. Diterima</th>
                            <th scope="col" class="px-5 py-3">Sifat</th>
                            <th scope="col" class="px-5 py-3">Status</th>
                            <th scope="col" class="px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($recentLetters as $letter)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $letter->nomor_agenda ? '#' . $letter->nomor_agenda : '-' }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $letter->nomor_surat }}
                                    </div>
                                </td>
                                <td class="px-5 py-4 max-w-xs">
                                    <p class="font-medium text-gray-900 dark:text-gray-100 truncate">{{ $letter->asal_surat }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $letter->perihal }}</p>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-xs">
                                    {{ $letter->tanggal_diterima ? $letter->tanggal_diterima->translatedFormat('d M Y') : '-' }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    @php
                                        $sifatColor = match($letter->sifat) {
                                            'Sangat Segera', 'Segera' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                                            'Penting' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                                            'Rahasia' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300',
                                            default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full {{ $sifatColor }}">
                                        {{ $letter->sifat }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    @php
                                        $statusBadge = match($letter->status) {
                                            'baru' => ['bg' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300', 'label' => 'Baru'],
                                            'didistribusikan' => ['bg' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300', 'label' => 'Didistribusikan'],
                                            'dalam_tindak_lanjut' => ['bg' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300', 'label' => 'Tindak Lanjut'],
                                            'selesai' => ['bg' => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300', 'label' => 'Selesai'],
                                            default => ['bg' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300', 'label' => $letter->status]
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full {{ $statusBadge['bg'] }}">
                                        {{ $statusBadge['label'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-right text-xs">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('surat-masuk.show', $letter->id) }}" class="p-1.5 text-blue-600 hover:text-blue-800 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-gray-700 rounded-lg transition-colors" title="Lihat Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>

                                        @if($letter->status === 'baru')
                                            <a href="{{ route('disposisi.create', $letter->id) }}" class="px-2 py-1 bg-amber-500 hover:bg-amber-600 text-white rounded-md text-[11px] font-semibold transition-colors" title="Disposisikan">
                                                Disposisi
                                            </a>
                                        @else
                                            <a href="{{ route('surat-masuk.disposisi-pdf', $letter->id) }}" target="_blank" class="p-1.5 text-gray-500 hover:text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors" title="Cetak Lembar Disposisi">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-gray-400 dark:text-gray-500 text-xs">
                                    Belum ada surat masuk yang tercatat. Silakan klik <a href="{{ route('surat-masuk.create') }}" class="text-blue-600 underline">Input Surat Masuk</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
