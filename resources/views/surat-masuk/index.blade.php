<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Surat Masuk
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Kelola seluruh data surat masuk dan disposisi instansi
                </p>
            </div>
            <a href="{{ route('surat-masuk.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Surat Masuk
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

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded-lg dark:bg-red-900/50 dark:border-red-700 dark:text-red-200 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Filter & Pencarian --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-4 mb-6">
        <form method="GET" action="{{ route('surat-masuk.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Cari Surat</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor, asal, perihal..."
                           class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="baru" {{ request('status') === 'baru' ? 'selected' : '' }}>Baru</option>
                    <option value="didistribusikan" {{ request('status') === 'didistribusikan' ? 'selected' : '' }}>Didistribusikan</option>
                    <option value="dalam_tindak_lanjut" {{ request('status') === 'dalam_tindak_lanjut' ? 'selected' : '' }}>Dalam Tindak Lanjut</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Sifat Surat</label>
                <select name="sifat" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Sifat</option>
                    <option value="biasa" {{ request('sifat') === 'biasa' ? 'selected' : '' }}>Biasa</option>
                    <option value="penting" {{ request('sifat') === 'penting' ? 'selected' : '' }}>Penting</option>
                    <option value="segera" {{ request('sifat') === 'segera' ? 'selected' : '' }}>Segera</option>
                    <option value="rahasia" {{ request('sifat') === 'rahasia' ? 'selected' : '' }}>Rahasia</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    Filter
                </button>
                <a href="{{ route('surat-masuk.index') }}" class="px-3 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors" title="Reset filter">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Tabel Surat Masuk --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-700 dark:text-gray-300">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-center w-12">No</th>
                        <th class="px-4 py-3">Nomor & Asal Surat</th>
                        <th class="px-4 py-3">Perihal</th>
                        <th class="px-4 py-3">Tanggal Diterima</th>
                        <th class="px-4 py-3 text-center">Sifat</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Lampiran</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($letters as $index => $letter)
                    <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-4 py-3 text-center text-xs text-gray-500 dark:text-gray-400">
                            {{ $letters->firstItem() + $index }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-gray-900 dark:text-gray-100">
                                {{ $letter->nomor_surat }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1 mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m3-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <span>Dari: {{ $letter->asal_surat }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 max-w-xs">
                            <p class="font-medium text-gray-800 dark:text-gray-200 line-clamp-2">
                                {{ $letter->perihal }}
                            </p>
                            <span class="text-xs text-gray-400 dark:text-gray-500">
                                Tgl Surat: {{ \Carbon\Carbon::parse($letter->tanggal_surat)->translatedFormat('d M Y') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600 dark:text-gray-300">
                            {{ \Carbon\Carbon::parse($letter->tanggal_diterima)->translatedFormat('d M Y') }}
                        </td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            @php
                                $sifatColors = [
                                    'biasa' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600',
                                    'penting' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300 border-amber-200 dark:border-amber-700',
                                    'segera' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/60 dark:text-orange-300 border-orange-200 dark:border-orange-700',
                                    'rahasia' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/60 dark:text-purple-300 border-purple-200 dark:border-purple-700',
                                ];
                            @endphp
                            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full border {{ $sifatColors[$letter->sifat] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst($letter->sifat) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
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
                        </td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            @if($letter->file_lampiran)
                                <a href="{{ route('surat-masuk.file', $letter) }}" target="_blank"
                                   class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 rounded hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors"
                                   title="Buka lampiran">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                    <span>Lihat</span>
                                </a>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('surat-masuk.show', $letter) }}"
                                   class="p-1.5 text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                   title="Detail Surat & Riwayat Disposisi">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('surat-masuk.edit', $letter) }}"
                                   class="p-1.5 text-gray-500 hover:text-amber-600 dark:hover:text-amber-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                   title="Edit Surat">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('surat-masuk.destroy', $letter) }}" method="POST"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus surat masuk dengan nomor {{ $letter->nomor_surat }}? Tindakan ini tidak dapat dibatalkan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="p-1.5 text-gray-500 hover:text-red-600 dark:hover:text-red-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                            title="Hapus Surat">
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
                        <td colspan="8" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="font-medium text-base text-gray-600 dark:text-gray-400">Belum ada data surat masuk</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Gunakan tombol "Tambah Surat Masuk" di atas untuk menambahkan surat baru.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($letters->hasPages())
        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800">
            {{ $letters->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
