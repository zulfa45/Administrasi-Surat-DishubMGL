<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-xl text-gray-900 dark:text-gray-100 leading-tight">
                            Tugas Langsung Internal Bidang
                        </h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Penugasan mandiri dari Kepala Bidang kepada staf sebidang tanpa melalui surat masuk.
                        </p>
                    </div>
                </div>
            </div>
            <div>
                <a href="{{ route('kabid.tugas.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-lg transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Buat Tugas Baru
                </a>
            </div>
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
        <div class="flex flex-wrap items-center gap-2 pb-1">
            @php
                $currentStatus = request('status', '');
            @endphp
            <a href="{{ route('kabid.tugas.index', array_merge(request()->except(['status', 'page']))) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 {{ $currentStatus === '' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                <span>Semua Tugas</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $currentStatus === '' ? 'bg-emerald-700 text-emerald-100' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                    {{ $counts['all'] ?? 0 }}
                </span>
            </a>

            <a href="{{ route('kabid.tugas.index', array_merge(request()->except(['status', 'page']), ['status' => 'baru'])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 {{ $currentStatus === 'baru' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                <span>Baru / Ditelaah</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $currentStatus === 'baru' ? 'bg-blue-700 text-blue-100' : 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300' }}">
                    {{ $counts['baru'] ?? 0 }}
                </span>
            </a>

            <a href="{{ route('kabid.tugas.index', array_merge(request()->except(['status', 'page']), ['status' => 'dikerjakan'])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 {{ $currentStatus === 'dikerjakan' ? 'bg-amber-600 text-white shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                <span>Sedang Dikerjakan</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $currentStatus === 'dikerjakan' ? 'bg-amber-700 text-amber-100' : 'bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300' }}">
                    {{ $counts['dikerjakan'] ?? 0 }}
                </span>
            </a>

            <a href="{{ route('kabid.tugas.index', array_merge(request()->except(['status', 'page']), ['status' => 'menunggu_verifikasi'])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 {{ $currentStatus === 'menunggu_verifikasi' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                <span>Menunggu Verifikasi</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $currentStatus === 'menunggu_verifikasi' ? 'bg-indigo-700 text-indigo-100' : 'bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300' }}">
                    {{ $counts['menunggu_verifikasi'] ?? 0 }}
                </span>
            </a>

            <a href="{{ route('kabid.tugas.index', array_merge(request()->except(['status', 'page']), ['status' => 'perlu_revisi'])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 {{ $currentStatus === 'perlu_revisi' ? 'bg-rose-600 text-white shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                <span>Perlu Revisi</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $currentStatus === 'perlu_revisi' ? 'bg-rose-700 text-rose-100' : 'bg-rose-100 dark:bg-rose-900/50 text-rose-700 dark:text-rose-300' }}">
                    {{ $counts['perlu_revisi'] ?? 0 }}
                </span>
            </a>

            <a href="{{ route('kabid.tugas.index', array_merge(request()->except(['status', 'page']), ['status' => 'selesai'])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 {{ $currentStatus === 'selesai' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                <span>Selesai</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $currentStatus === 'selesai' ? 'bg-emerald-700 text-emerald-100' : 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300' }}">
                    {{ $counts['selesai'] ?? 0 }}
                </span>
            </a>
        </div>

        {{-- Filter & Search Bar --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xs border border-gray-100 dark:border-gray-700 p-4">
            <form method="GET" action="{{ route('kabid.tugas.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
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
                           placeholder="Cari judul tugas, catatan arahan, atau nama staf..."
                           class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50/50 dark:bg-gray-900/40 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white dark:focus:bg-gray-900 transition-all"/>
                </div>

                {{-- Filter Sifat Tugas --}}
                <div class="sm:col-span-3">
                    <select name="sifat"
                            class="w-full py-2 px-3 text-sm border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50/50 dark:bg-gray-900/40 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
                        <option value="">Semua Sifat Tugas</option>
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
                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl transition-all shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        <span>Terapkan</span>
                    </button>
                    @if(request('search') || request('sifat') || request('status'))
                        <a href="{{ route('kabid.tugas.index') }}"
                           class="px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-xs font-semibold rounded-xl transition-colors">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tabel Daftar Tugas Langsung --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
                        <tr>
                            <th class="px-6 py-4">Ref & Judul Tugas</th>
                            <th class="px-6 py-4">Staf Pelaksana</th>
                            <th class="px-6 py-4">Sifat</th>
                            <th class="px-6 py-4">Tgl Tugas / Batas Waktu</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($tasks as $task)
                            @php
                                $sifatColor = match($task->sifat) {
                                    'Sangat Segera', 'Segera' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300 border-rose-200 dark:border-rose-800',
                                    'Penting' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                                    'Rahasia' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300 border-purple-200 dark:border-purple-800',
                                    default => 'bg-gray-100 text-gray-700 dark:bg-gray-700/60 dark:text-gray-300 border-gray-200 dark:border-gray-600'
                                };

                                $statusBadge = match($task->status) {
                                    'belum_dibaca'              => ['class' => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300 border-slate-200', 'label' => 'Belum Dibaca'],
                                    'dibaca'                    => ['class' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 border-blue-200', 'label' => 'Sedang Ditelaah'],
                                    'dikerjakan'                => ['class' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300 border-amber-200', 'label' => 'Dikerjakan'],
                                    'menunggu_verifikasi_kabid' => ['class' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300 border-indigo-200 animate-pulse', 'label' => 'Menunggu Verifikasi'],
                                    'perlu_revisi'              => ['class' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300 border-rose-200', 'label' => 'Perlu Revisi'],
                                    'selesai'                   => ['class' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 border-emerald-200', 'label' => 'Selesai'],
                                    default                     => ['class' => 'bg-gray-100 text-gray-700 border-gray-200', 'label' => $task->status]
                                };

                                $isOverdue = $task->deadline && $task->status !== 'selesai' && $task->deadline->isPast();
                                $isNearDeadline = $task->deadline && $task->status !== 'selesai' && !$isOverdue && $task->deadline->diffInDays(now()) <= 2;
                            @endphp
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/40 transition-colors">
                                <td class="px-6 py-4 max-w-sm">
                                    <div class="flex items-center gap-1.5 mb-1">
                                        <span class="font-mono text-[11px] font-bold text-gray-400">#TUGAS-{{ str_pad($task->id, 4, '0', STR_PAD_LEFT) }}</span>
                                        @if($task->file_tugas)
                                            <span class="inline-flex items-center text-[10px] text-blue-600 bg-blue-50 dark:bg-blue-950/40 px-1.5 py-0.5 rounded" title="Memiliki berkas petunjuk">
                                                <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                Berkas
                                            </span>
                                        @endif
                                    </div>
                                    <p class="font-bold text-gray-900 dark:text-gray-100 text-sm line-clamp-2">
                                        {{ $task->judul_tugas }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-1">
                                        {{ $task->catatan_kabid }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($task->user)
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                                {{ substr($task->user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <span class="font-semibold text-gray-900 dark:text-gray-100 text-xs">{{ $task->user->name }}</span>
                                                    @if($task->user->badge)
                                                        <span class="inline-flex items-center gap-0.5 px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-amber-100 text-amber-900 border border-amber-300">
                                                            ⭐ {{ $task->user->badge }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="text-[11px] text-gray-400">
                                                    {{ $task->user->jabatan ?? 'Staf' }}
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 text-[11px] font-semibold rounded-full border {{ $sifatColor }}">
                                        {{ $task->sifat ?? 'Biasa' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs">
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
                                                <span class="text-[11px] text-gray-400">
                                                    Batas: {{ $task->deadline->translatedFormat('d M Y') }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ $statusBadge['class'] }}">
                                        {{ $statusBadge['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('kabid.tugas.show', $task) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl transition-all shadow-xs">
                                            <span>Detail / Evaluasi</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>

                                        @if($task->status !== 'selesai')
                                            <form action="{{ route('kabid.tugas.destroy', $task) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan dan menghapus tugas ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 transition-colors rounded-lg" title="Hapus Tugas">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400 dark:text-gray-500">
                                        <svg class="w-12 h-12 mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                        </svg>
                                        <p class="font-medium text-sm">Belum ada tugas internal bidang yang dibuat.</p>
                                        <p class="text-xs text-gray-400 mt-1">Gunakan tombol "Buat Tugas Baru" untuk menugaskan pekerjaan kepada staf sebidang.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($tasks->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $tasks->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
