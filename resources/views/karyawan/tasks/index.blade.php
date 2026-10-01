<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-xl text-gray-900 dark:text-gray-100 leading-tight">
                            Daftar Tugas Disposisi
                        </h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Kelola dan pantau seluruh surat masuk yang ditugaskan kepada Anda atau unit kerja Anda.
                        </p>
                    </div>
                </div>
            </div>
            <a href="{{ route('karyawan.dashboard') }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs font-semibold rounded-xl transition-colors shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Dashboard
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Status Filter Tabs / Chips --}}
        <div class="flex flex-wrap items-center gap-2 pb-1">
            @php
                $currentStatus = request('status', '');
            @endphp
            <a href="{{ route('karyawan.tasks.index', array_merge(request()->except(['status', 'page']))) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 {{ $currentStatus === '' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                <span>Semua Tugas</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $currentStatus === '' ? 'bg-blue-700 text-blue-100' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                    {{ $counts['all'] ?? 0 }}
                </span>
            </a>

            <a href="{{ route('karyawan.tasks.index', array_merge(request()->except(['status', 'page']), ['status' => 'baru'])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 {{ $currentStatus === 'baru' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                <span>Tugas Baru</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $currentStatus === 'baru' ? 'bg-blue-700 text-blue-100' : 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300' }}">
                    {{ $counts['baru'] ?? 0 }}
                </span>
            </a>

            <a href="{{ route('karyawan.tasks.index', array_merge(request()->except(['status', 'page']), ['status' => 'dikerjakan'])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 {{ $currentStatus === 'dikerjakan' ? 'bg-amber-600 text-white shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                <span>Sedang Dikerjakan</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $currentStatus === 'dikerjakan' ? 'bg-amber-700 text-amber-100' : 'bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300' }}">
                    {{ $counts['dikerjakan'] ?? 0 }}
                </span>
            </a>

            <a href="{{ route('karyawan.tasks.index', array_merge(request()->except(['status', 'page']), ['status' => 'selesai'])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 {{ $currentStatus === 'selesai' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                <span>Selesai</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $currentStatus === 'selesai' ? 'bg-emerald-700 text-emerald-100' : 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300' }}">
                    {{ $counts['selesai'] ?? 0 }}
                </span>
            </a>
        </div>

        {{-- Filter & Search Bar --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xs border border-gray-100 dark:border-gray-700 p-4">
            <form method="GET" action="{{ route('karyawan.tasks.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                {{-- Maintain Status if filtered --}}
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif

                {{-- Input Pencarian --}}
                <div class="sm:col-span-6 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari no. surat, perihal, atau asal instansi..."
                           class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50/50 dark:bg-gray-900/40 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-gray-900 transition-all"/>
                </div>

                {{-- Filter Sifat Surat --}}
                <div class="sm:col-span-3">
                    <select name="sifat"
                            class="w-full py-2 px-3 text-sm border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50/50 dark:bg-gray-900/40 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        <option value="">Semua Sifat Surat</option>
                        <option value="Sangat Segera" {{ request('sifat') === 'Sangat Segera' ? 'selected' : '' }}>Sangat Segera</option>
                        <option value="Segera" {{ request('sifat') === 'Segera' ? 'selected' : '' }}>Segera</option>
                        <option value="Penting" {{ request('sifat') === 'Penting' ? 'selected' : '' }}>Penting</option>
                        <option value="Biasa" {{ request('sifat') === 'Biasa' ? 'selected' : '' }}>Biasa</option>
                        <option value="Rahasia" {{ request('sifat') === 'Rahasia' ? 'selected' : '' }}>Rahasia</option>
                    </select>
                </div>

                {{-- Action Buttons --}}
                <div class="sm:col-span-3 flex items-center gap-2">
                    <button type="submit"
                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl transition-all shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'status', 'sifat']))
                        <a href="{{ route('karyawan.tasks.index') }}"
                           class="px-3 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-xs font-semibold rounded-xl transition-colors"
                           title="Reset Filter">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tabel Daftar Tugas --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xs border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
                        <tr>
                            <th class="px-5 py-3.5">No. Agenda / Surat</th>
                            <th class="px-5 py-3.5">Perihal & Pengirim</th>
                            <th class="px-5 py-3.5">Sifat</th>
                            <th class="px-5 py-3.5">Tgl. Disposisi / Batas Waktu</th>
                            <th class="px-5 py-3.5 text-center">Status</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($tasks as $task)
                            @php
                                $letter = $task->incomingLetter;
                                $sifatColor = match($letter?->sifat) {
                                    'Sangat Segera', 'Segera' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300 border-rose-200 dark:border-rose-800',
                                    'Penting' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                                    'Rahasia' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300 border-purple-200 dark:border-purple-800',
                                    default => 'bg-gray-100 text-gray-700 dark:bg-gray-700/60 dark:text-gray-300 border-gray-200 dark:border-gray-600'
                                };

                                $statusBadge = match($task->status) {
                                    'belum_dibaca' => ['class' => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-600', 'label' => 'Belum Dibaca'],
                                    'dibaca'       => ['class' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 border-blue-200 dark:border-blue-800', 'label' => 'Dibaca'],
                                    'dikerjakan'   => ['class' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300 border-amber-200 dark:border-amber-800', 'label' => 'Dikerjakan'],
                                    'selesai'      => ['class' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800', 'label' => 'Selesai'],
                                    default        => ['class' => 'bg-gray-100 text-gray-700 border-gray-200', 'label' => $task->status]
                                };

                                $isOverdue = $task->deadline && $task->status !== 'selesai' && $task->deadline->isPast();
                                $isNearDeadline = $task->deadline && $task->status !== 'selesai' && !$isOverdue && $task->deadline->diffInDays(now()) <= 2;
                            @endphp
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/40 transition-colors">
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="font-bold text-gray-900 dark:text-gray-100">
                                        {{ $letter?->nomor_agenda ? '#' . $letter->nomor_agenda : '-' }}
                                    </div>
                                    <div class="text-xs font-mono text-gray-500 dark:text-gray-400 mt-0.5">
                                        {{ $letter?->nomor_surat ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-5 py-4 max-w-xs">
                                    <p class="font-semibold text-gray-900 dark:text-gray-100 truncate" title="{{ $letter?->perihal }}">
                                        {{ $letter?->perihal ?? 'Tanpa Perihal' }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5" title="{{ $letter?->asal_surat }}">
                                        Asal: {{ $letter?->asal_surat ?? '-' }}
                                    </p>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 text-[11px] font-semibold rounded-full border {{ $sifatColor }}">
                                        {{ $letter?->sifat ?? 'Biasa' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-xs">
                                    <div class="text-gray-700 dark:text-gray-300">
                                        {{ $task->tanggal_disposisi ? $task->tanggal_disposisi->translatedFormat('d M Y') : '-' }}
                                    </div>
                                    @if($task->deadline)
                                        <div class="mt-0.5">
                                            @if($isOverdue)
                                                <span class="text-[11px] font-semibold text-red-600 dark:text-red-400 flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-red-600 animate-ping"></span>
                                                    Terlewat: {{ $task->deadline->translatedFormat('d M Y') }}
                                                </span>
                                            @elseif($isNearDeadline)
                                                <span class="text-[11px] font-semibold text-amber-600 dark:text-amber-400 flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                    Batas: {{ $task->deadline->translatedFormat('d M Y') }}
                                                </span>
                                            @else
                                                <span class="text-[11px] text-gray-400 dark:text-gray-500">
                                                    Batas: {{ $task->deadline->translatedFormat('d M Y') }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ $statusBadge['class'] }}">
                                        {{ $statusBadge['label'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    <a href="{{ route('karyawan.tasks.show', $task) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-all shadow-xs hover:shadow">
                                        <span>Buka Tugas</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-gray-400 dark:text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-400 flex items-center justify-center mb-3">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <p class="font-medium text-sm text-gray-700 dark:text-gray-300">Tidak ada tugas yang ditemukan</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Coba sesuaikan kata kunci pencarian atau filter status Anda.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($tasks->hasPages())
                <div class="p-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800">
                    {{ $tasks->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
