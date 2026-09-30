<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Daftar Tugas Disposisi
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Daftar surat yang ditugaskan kepada Anda atau bagian Anda
                </p>
            </div>
            <a href="{{ route('karyawan.dashboard') }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-xs font-semibold rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Dashboard
            </a>
        </div>
    </x-slot>

    {{-- Filter Pencarian --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-4 mb-6">
        <form method="GET" action="{{ route('karyawan.tasks.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Cari Tugas / Surat</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor surat, asal, perihal..."
                       class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Status Pengerjaan</label>
                <div class="flex gap-2">
                    <select name="status" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        <option value="belum_dibaca" {{ request('status') === 'belum_dibaca' ? 'selected' : '' }}>Belum Dibaca</option>
                        <option value="dibaca" {{ request('status') === 'dibaca' ? 'selected' : '' }}>Dibaca</option>
                        <option value="dikerjakan" {{ request('status') === 'dikerjakan' ? 'selected' : '' }}>Dikerjakan</option>
                        <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                        Filter
                    </button>
                    <a href="{{ route('karyawan.tasks.index') }}" class="px-3 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 text-xs font-semibold rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Tabel Daftar Tugas --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-700 dark:text-gray-300">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3">Nomor Surat</th>
                        <th class="px-4 py-3">Perihal</th>
                        <th class="px-4 py-3">Asal Pengirim</th>
                        <th class="px-4 py-3">Tanggal Disposisi</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($tasks as $task)
                    <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-4 py-3 font-mono font-medium text-gray-900 dark:text-gray-100">
                            {{ $task->incomingLetter?->nomor_surat ?? '-' }}
                        </td>
                        <td class="px-4 py-3 max-w-xs font-medium text-gray-800 dark:text-gray-200">
                            {{ $task->incomingLetter?->perihal ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400">
                            {{ $task->incomingLetter?->asal_surat ?? '-' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600 dark:text-gray-400">
                            {{ \Carbon\Carbon::parse($task->tanggal_disposisi)->translatedFormat('d M Y') }}
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
                            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full border {{ $assignStatusColors[$task->status] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            <a href="{{ route('karyawan.tasks.show', $task) }}"
                               class="inline-flex items-center gap-1 px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors shadow-sm">
                                <span>Buka Tugas</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p class="font-medium text-base text-gray-600 dark:text-gray-400">Tidak ada tugas yang ditemukan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tasks->hasPages())
        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800">
            {{ $tasks->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
