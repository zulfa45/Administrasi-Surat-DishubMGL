<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-gray-900 dark:text-gray-100 leading-tight">
                        Monitoring Tugas Langsung Kabid
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Pantau dan kelola seluruh penugasan mandiri dari Kepala Bidang kepada staf pelaksana di seluruh bidang.
                    </p>
                </div>
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
            <a href="{{ route('admin.tugas.index', array_merge(request()->except(['status', 'page']))) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 {{ $currentStatus === '' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                <span>Semua Tugas</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $currentStatus === '' ? 'bg-blue-700 text-blue-100' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                    {{ $counts['all'] ?? 0 }}
                </span>
            </a>

            <a href="{{ route('admin.tugas.index', array_merge(request()->except(['status', 'page']), ['status' => 'baru'])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 {{ $currentStatus === 'baru' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                <span>Baru / Ditelaah</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $currentStatus === 'baru' ? 'bg-indigo-700 text-indigo-100' : 'bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300' }}">
                    {{ $counts['baru'] ?? 0 }}
                </span>
            </a>

            <a href="{{ route('admin.tugas.index', array_merge(request()->except(['status', 'page']), ['status' => 'dikerjakan'])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 {{ $currentStatus === 'dikerjakan' ? 'bg-amber-600 text-white shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                <span>Sedang Dikerjakan</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $currentStatus === 'dikerjakan' ? 'bg-amber-700 text-amber-100' : 'bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300' }}">
                    {{ $counts['dikerjakan'] ?? 0 }}
                </span>
            </a>

            <a href="{{ route('admin.tugas.index', array_merge(request()->except(['status', 'page']), ['status' => 'menunggu_verifikasi'])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 {{ $currentStatus === 'menunggu_verifikasi' ? 'bg-purple-600 text-white shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                <span>Menunggu Verifikasi</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $currentStatus === 'menunggu_verifikasi' ? 'bg-purple-700 text-purple-100' : 'bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300' }}">
                    {{ $counts['menunggu_verifikasi'] ?? 0 }}
                </span>
            </a>

            <a href="{{ route('admin.tugas.index', array_merge(request()->except(['status', 'page']), ['status' => 'perlu_revisi'])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 {{ $currentStatus === 'perlu_revisi' ? 'bg-rose-600 text-white shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                <span>Perlu Revisi</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $currentStatus === 'perlu_revisi' ? 'bg-rose-700 text-rose-100' : 'bg-rose-100 dark:bg-rose-900/50 text-rose-700 dark:text-rose-300' }}">
                    {{ $counts['perlu_revisi'] ?? 0 }}
                </span>
            </a>

            <a href="{{ route('admin.tugas.index', array_merge(request()->except(['status', 'page']), ['status' => 'selesai'])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 {{ $currentStatus === 'selesai' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                <span>Selesai</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $currentStatus === 'selesai' ? 'bg-emerald-700 text-emerald-100' : 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300' }}">
                    {{ $counts['selesai'] ?? 0 }}
                </span>
            </a>
        </div>

        {{-- Filter & Search Bar --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xs border border-gray-100 dark:border-gray-700 p-4">
            <form method="GET" action="{{ route('admin.tugas.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif

                {{-- Filter Bidang / Departemen --}}
                <div class="sm:col-span-3">
                    <select name="department_id" class="w-full text-xs rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 text-gray-700 dark:text-gray-300 focus:ring-blue-500 focus:border-blue-500 py-2">
                        <option value="">Semua Bidang / Bagian</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Sifat --}}
                <div class="sm:col-span-3">
                    <select name="sifat" class="w-full text-xs rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 text-gray-700 dark:text-gray-300 focus:ring-blue-500 focus:border-blue-500 py-2">
                        <option value="">Semua Sifat Tugas</option>
                        @foreach(['Biasa', 'Penting', 'Segera', 'Sangat Segera', 'Rahasia'] as $sifat)
                            <option value="{{ $sifat }}" {{ request('sifat') === $sifat ? 'selected' : '' }}>
                                {{ $sifat }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Search Box --}}
                <div class="sm:col-span-4">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Cari judul, arahan, staf, kabid..."
                               class="w-full text-xs pl-9 pr-3 py-2 rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 text-gray-700 dark:text-gray-300 focus:ring-blue-500 focus:border-blue-500">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                {{-- Submit & Reset Buttons --}}
                <div class="sm:col-span-2 flex items-center gap-2">
                    <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl transition-all shadow-xs text-center">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'sifat', 'department_id', 'status']))
                        <a href="{{ route('admin.tugas.index') }}" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors" title="Reset Filter">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Data Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xs border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
                        <tr>
                            <th class="px-6 py-4">Tugas & Arahan</th>
                            <th class="px-6 py-4">Bidang</th>
                            <th class="px-6 py-4">Pemberi Tugas</th>
                            <th class="px-6 py-4">Staf Pelaksana & Batas Waktu</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($tasks as $task)
                            @php
                                $sifatBadge = match($task->sifat) {
                                    'Penting'       => ['bg' => 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-900/30 dark:text-amber-400'],
                                    'Segera'        => ['bg' => 'bg-orange-100 text-orange-800 border-orange-200 dark:bg-orange-900/30 dark:text-orange-400'],
                                    'Sangat Segera' => ['bg' => 'bg-rose-100 text-rose-800 border-rose-200 dark:bg-rose-900/30 dark:text-rose-400 font-bold'],
                                    'Rahasia'       => ['bg' => 'bg-purple-100 text-purple-800 border-purple-200 dark:bg-purple-900/30 dark:text-purple-400'],
                                    default         => ['bg' => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-gray-700 dark:text-gray-300'],
                                };

                                $statusBadge = match($task->status) {
                                    'belum_dibaca'            => ['label' => 'Belum Dibaca', 'class' => 'bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-700 dark:text-gray-300'],
                                    'dibaca'                  => ['label' => 'Dilihat', 'class' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:text-blue-300'],
                                    'dikerjakan'              => ['label' => 'Sedang Dikerjakan', 'class' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/30 dark:text-amber-300'],
                                    'menunggu_verifikasi_kabid'=> ['label' => 'Menunggu Verifikasi', 'class' => 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-900/30 dark:text-purple-300 font-semibold'],
                                    'perlu_revisi'            => ['label' => 'Perlu Revisi', 'class' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-900/30 dark:text-rose-300'],
                                    'selesai'                 => ['label' => 'Selesai', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 font-semibold'],
                                    default                   => ['label' => ucfirst($task->status), 'class' => 'bg-gray-100 text-gray-700 border-gray-200'],
                                };

                                $isOverdue = $task->deadline && $task->status !== 'selesai' && now()->startOfDay()->gt($task->deadline->startOfDay());
                                $isNearDeadline = $task->deadline && $task->status !== 'selesai' && !$isOverdue && now()->startOfDay()->diffInDays($task->deadline->startOfDay(), false) <= 2;
                            @endphp
                            <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-start gap-2.5">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                                <span class="text-[10px] font-mono px-2 py-0.5 rounded-md bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-semibold">
                                                    {{ $task->nomor_referensi }}
                                                </span>
                                                <span class="text-[10px] px-2 py-0.5 rounded-md border font-semibold {{ $sifatBadge['bg'] }}">
                                                    {{ $task->sifat }}
                                                </span>
                                                @if($task->file_tugas)
                                                    <span class="inline-flex items-center gap-1 text-[10px] px-1.5 py-0.5 rounded-md bg-sky-50 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 font-medium border border-sky-200 dark:border-sky-800" title="Ada file lampiran petunjuk">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                        File Petunjuk
                                                    </span>
                                                @endif
                                            </div>
                                            <a href="{{ route('admin.tugas.show', $task) }}" class="font-bold text-gray-900 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 transition-colors line-clamp-1 text-sm">
                                                {{ $task->judul_tugas }}
                                            </a>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 mt-0.5">
                                                {{ Str::limit($task->catatan_kabid, 120) }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 dark:bg-gray-700 text-slate-700 dark:text-gray-200 border border-slate-200 dark:border-gray-600">
                                        <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m3-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        {{ $task->department?->name ?? 'Semua Bidang' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/60 dark:text-blue-300 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                            {{ strtoupper(substr($task->creator?->name ?? 'K', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-semibold text-gray-800 dark:text-gray-200 truncate">
                                                {{ $task->creator?->name ?? 'Kepala Bidang' }}
                                            </p>
                                            <p class="text-[10px] text-gray-400">
                                                {{ $task->created_at->translatedFormat('d M Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 dark:bg-gray-700 dark:text-gray-200 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                            {{ strtoupper(substr($task->user?->name ?? 'S', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-xs font-semibold text-gray-800 dark:text-gray-200 truncate">
                                                    {{ $task->user?->name ?? 'Belum Ditugaskan' }}
                                                </span>
                                                @if($task->user?->badge)
                                                    <span class="text-[10px] px-1.5 py-0.2 rounded bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 font-medium">
                                                        {{ $task->user->badge }}
                                                    </span>
                                                @endif
                                            </div>
                                            @if($task->deadline)
                                                <div class="mt-0.5">
                                                    @if($isOverdue)
                                                        <span class="text-[11px] font-bold text-rose-600 dark:text-rose-400 flex items-center gap-1">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                                                            Terlewat ({{ $task->deadline->translatedFormat('d M Y') }})
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
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ $statusBadge['class'] }}">
                                        {{ $statusBadge['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.tugas.show', $task) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl transition-all shadow-xs">
                                            <span>Detail</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>

                                        {{-- Tombol Hapus Tugas oleh Admin --}}
                                        <form action="{{ route('admin.tugas.destroy', $task) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tugas langsung ini? Seluruh data dan berkas terkait akan dihapus secara permanen.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 transition-colors rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30" title="Hapus Tugas">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
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
                                        <p class="font-medium text-sm">Tidak ada tugas langsung yang ditemukan.</p>
                                        <p class="text-xs text-gray-400 mt-1">Belum ada tugas langsung dari Kepala Bidang yang sesuai dengan filter ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($tasks->hasPages())
                <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $tasks->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
