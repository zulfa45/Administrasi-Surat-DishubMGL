<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-xl text-gray-900 dark:text-gray-100 leading-tight">
                            Surat Masuk Bidang
                        </h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Kelola disposisi staf dan pantau tindak lanjut surat yang masuk ke bidang Anda.
                        </p>
                    </div>
                </div>
            </div>
            <a href="{{ route('kabid.dashboard') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs font-semibold rounded-xl transition-colors shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Dashboard
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <x-ui.alert type="success" :message="session('success')" />
        @endif
        @if(session('error'))
            <x-ui.alert type="error" :message="session('error')" />
        @endif

        {{-- Status Filter Tabs / Chips --}}
        @php
            $currentStatus = request('status', '');
        @endphp
        <div class="flex flex-wrap items-center gap-2 pb-1">
            <a href="{{ route('kabid.surat-masuk.index', array_merge(request()->except(['status', 'page']))) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 {{ $currentStatus === '' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                <span>Semua Surat</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $currentStatus === '' ? 'bg-indigo-700 text-indigo-100' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                    {{ $counts['all'] ?? 0 }}
                </span>
            </a>

            <a href="{{ route('kabid.surat-masuk.index', array_merge(request()->except(['status', 'page']), ['status' => 'belum_disposisi'])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 {{ $currentStatus === 'belum_disposisi' ? 'bg-rose-600 text-white shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                <span>Belum Disposisi</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $currentStatus === 'belum_disposisi' ? 'bg-rose-700 text-rose-100' : ($counts['belum_disposisi'] > 0 ? 'bg-rose-100 text-rose-700 font-bold' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300') }}">
                    {{ $counts['belum_disposisi'] ?? 0 }}
                </span>
            </a>

            <a href="{{ route('kabid.surat-masuk.index', array_merge(request()->except(['status', 'page']), ['status' => 'sedang_proses'])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 {{ $currentStatus === 'sedang_proses' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                <span>Sedang Diproses Staf</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $currentStatus === 'sedang_proses' ? 'bg-blue-700 text-blue-100' : 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300' }}">
                    {{ $counts['sedang_proses'] ?? 0 }}
                </span>
            </a>

            <a href="{{ route('kabid.surat-masuk.index', array_merge(request()->except(['status', 'page']), ['status' => 'menunggu_verifikasi'])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 {{ $currentStatus === 'menunggu_verifikasi' ? 'bg-amber-600 text-white shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                <span class="flex items-center gap-1.5">
                    @if(($counts['menunggu_verifikasi'] ?? 0) > 0)
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                    @endif
                    Menunggu Verifikasi
                </span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $currentStatus === 'menunggu_verifikasi' ? 'bg-amber-700 text-amber-100' : ($counts['menunggu_verifikasi'] > 0 ? 'bg-amber-100 text-amber-800 font-bold' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300') }}">
                    {{ $counts['menunggu_verifikasi'] ?? 0 }}
                </span>
            </a>

            <a href="{{ route('kabid.surat-masuk.index', array_merge(request()->except(['status', 'page']), ['status' => 'selesai'])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 {{ $currentStatus === 'selesai' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                <span>Selesai / Arsip</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $currentStatus === 'selesai' ? 'bg-emerald-700 text-emerald-100' : 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300' }}">
                    {{ $counts['selesai'] ?? 0 }}
                </span>
            </a>
        </div>

        {{-- Filter & Search Bar --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xs border border-gray-100 dark:border-gray-700 p-4">
            <form method="GET" action="{{ route('kabid.surat-masuk.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif

                <div class="sm:col-span-9 relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari nomor surat, perihal, asal instansi, no. agenda, atau nama staf..."
                           class="w-full pl-10 pr-4 py-2 text-sm border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50/50 dark:bg-gray-900/40 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white dark:focus:bg-gray-900 transition-all"/>
                </div>

                <div class="sm:col-span-3 flex items-center gap-2">
                    <button type="submit"
                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition-all shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Cari
                    </button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('kabid.surat-masuk.index') }}"
                           class="px-3.5 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-xs font-semibold rounded-xl transition-colors"
                           title="Reset Pencarian & Filter">
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            @if(request('search'))
                <div class="mt-2.5 pt-2.5 border-t border-gray-100 dark:border-gray-700 text-xs text-gray-500 flex items-center justify-between">
                    <span>Menampilkan hasil pencarian untuk: <strong class="text-gray-800 dark:text-gray-200">"{{ request('search') }}"</strong></span>
                    <a href="{{ route('kabid.surat-masuk.index', request()->except('search')) }}" class="text-indigo-600 hover:underline">Hapus pencarian</a>
                </div>
            @endif
        </div>

        {{-- Tabel Data Surat Masuk --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600 dark:text-gray-400">
                    <thead class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Surat & Asal</th>
                            <th class="px-6 py-4 font-semibold">Tujuan Staf</th>
                            <th class="px-6 py-4 font-semibold">Batas Waktu</th>
                            <th class="px-6 py-4 font-semibold text-center">Status</th>
                            <th class="px-6 py-4 font-semibold text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($assignments as $assignment)
                            @php
                                $letter = $assignment->incomingLetter;
                                $isOverdue = $assignment->deadline && $assignment->status !== 'selesai' && \Carbon\Carbon::parse($assignment->deadline)->isPast();
                                $isNearDeadline = $assignment->deadline && $assignment->status !== 'selesai' && !$isOverdue && \Carbon\Carbon::parse($assignment->deadline)->diffInDays(now()) <= 2;

                                $statusBadge = match($assignment->status) {
                                    'selesai' => ['class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800', 'label' => 'Selesai'],
                                    'menunggu_verifikasi_kabid' => ['class' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 border-amber-200 dark:border-amber-800', 'label' => 'Menunggu Verifikasi'],
                                    'perlu_revisi' => ['class' => 'bg-rose-100 text-rose-800 dark:bg-rose-950/40 dark:text-rose-300 border-rose-200 dark:border-rose-800', 'label' => 'Perlu Revisi'],
                                    'belum_dibaca' => ['class' => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-600', 'label' => $assignment->user ? 'Belum Dibaca' : 'Menunggu Disposisi'],
                                    default => ['class' => 'bg-blue-100 text-blue-800 dark:bg-blue-950/40 dark:text-blue-300 border-blue-200 dark:border-blue-800', 'label' => ucfirst(str_replace('_', ' ', $assignment->status))]
                                };
                            @endphp
                            <tr class="hover:bg-indigo-50/30 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 align-top max-w-sm">
                                    <div class="flex items-center gap-2">
                                        @if($letter?->nomor_agenda)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                                #{{ $letter->nomor_agenda }}
                                            </span>
                                        @endif
                                        <span class="font-semibold text-gray-900 dark:text-gray-100 font-mono text-xs">
                                            {{ $letter?->nomor_surat ?? '-' }}
                                        </span>
                                    </div>
                                    <div class="text-xs mt-1 text-gray-500 font-medium">
                                        Dari: {{ $letter?->asal_surat ?? '-' }}
                                    </div>
                                    <div class="text-xs mt-1 text-gray-800 dark:text-gray-200 font-medium line-clamp-2" title="{{ $letter?->perihal }}">
                                        {{ $letter?->perihal ?? 'Tanpa Perihal' }}
                                    </div>
                                    <div class="text-[11px] text-gray-400 mt-1">
                                        Tgl Surat: {{ $letter?->tanggal_surat ? \Carbon\Carbon::parse($letter->tanggal_surat)->translatedFormat('d M Y') : '-' }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 align-top">
                                    @if($assignment->user)
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                                {{ substr($assignment->user->name, 0, 1) }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-semibold text-xs text-gray-900 dark:text-gray-100 truncate">{{ $assignment->user->name }}</div>
                                                <div class="text-[11px] text-gray-400 truncate">{{ $assignment->user->email }}</div>
                                            </div>
                                        </div>
                                    @else
                                        @if($assignment->status === 'selesai')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200">
                                                Arsip Bidang
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300 border border-rose-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-ping"></span>
                                                Belum Disposisi
                                            </span>
                                        @endif
                                    @endif

                                    @if($assignment->catatan_kabid)
                                        <div class="mt-2 text-[11px] text-gray-500 italic bg-gray-50 dark:bg-gray-700/40 p-2 rounded-lg line-clamp-2" title="{{ $assignment->catatan_kabid }}">
                                            "{{ $assignment->catatan_kabid }}"
                                        </div>
                                    @endif
                                </td>

                                <td class="px-6 py-4 align-top whitespace-nowrap text-xs">
                                    @if($assignment->deadline)
                                        <div>{{ \Carbon\Carbon::parse($assignment->deadline)->translatedFormat('d M Y') }}</div>
                                        @if($isOverdue)
                                            <div class="mt-1">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-600 animate-ping"></span>
                                                    Terlewat: {{ \Carbon\Carbon::parse($assignment->deadline)->diffForHumans() }}
                                                </span>
                                            </div>
                                        @elseif($isNearDeadline)
                                            <div class="mt-1">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                    Mendekati Batas
                                                </span>
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-gray-400 italic">Tidak ada batas</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 align-top text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $statusBadge['class'] }}">
                                        {{ $statusBadge['label'] }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 align-top text-center whitespace-nowrap">
                                    <a href="{{ route('kabid.surat-masuk.show', $assignment->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 rounded-xl text-xs font-semibold transition-colors">
                                        <span>Buka Detail</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-12 h-12 rounded-full bg-indigo-50 dark:bg-indigo-950/40 text-indigo-500 flex items-center justify-center mb-3">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                            </svg>
                                        </div>
                                        <p class="font-semibold text-sm text-gray-800 dark:text-gray-200">
                                            {{ request('search') ? 'Tidak ada surat yang cocok dengan pencarian' : 'Tidak ada data surat masuk' }}
                                        </p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 max-w-sm">
                                            {{ request('search') ? 'Coba periksa ejaan atau gunakan kata kunci pencarian yang lebih umum.' : 'Saat ini belum ada disposisi surat yang masuk ke bidang Anda.' }}
                                        </p>
                                        @if(request()->hasAny(['search', 'status']))
                                            <a href="{{ route('kabid.surat-masuk.index') }}" class="mt-3 inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-medium transition-colors">
                                                Hapus Filter & Tampilkan Semua
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($assignments->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                    {{ $assignments->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
