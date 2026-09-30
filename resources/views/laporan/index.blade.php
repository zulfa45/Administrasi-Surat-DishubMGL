<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Laporan Surat Masuk
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Rekapitulasi dan ekspor laporan berkas surat masuk instansi
                </p>
            </div>
            <div>
                <a href="{{ route('laporan.pdf', request()->query()) }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Preview / Cetak PDF
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Filter Form (Tahap U-14) --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-5 mb-6">
        <form method="GET" action="{{ route('laporan.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-1">
                    Tanggal Mulai
                </label>
                <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}"
                       class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-1">
                    Tanggal Sampai
                </label>
                <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}"
                       class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-1">
                    Status Surat
                </label>
                <select name="status" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="baru" {{ request('status') === 'baru' ? 'selected' : '' }}>Baru</option>
                    <option value="didistribusikan" {{ request('status') === 'didistribusikan' ? 'selected' : '' }}>Didistribusikan</option>
                    <option value="dalam_tindak_lanjut" {{ request('status') === 'dalam_tindak_lanjut' ? 'selected' : '' }}>Dalam Tindak Lanjut</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-1">
                    Bagian / Department
                </label>
                <select name="department_id" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Bagian</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2 lg:col-span-4 flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-700">
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    Menampilkan <strong>{{ $letters->total() }}</strong> berkas surat sesuai filter.
                </span>
                <div class="flex gap-2">
                    <a href="{{ route('laporan.index') }}"
                       class="px-3.5 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs font-semibold rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                        Reset Filter
                    </a>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors">
                        Terapkan Filter
                    </button>
                    <a href="{{ route('laporan.pdf', request()->query()) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Preview PDF
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Tabel Pratinjau Laporan --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-700 dark:text-gray-300">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-center w-12">No</th>
                        <th class="px-4 py-3">Nomor Surat</th>
                        <th class="px-4 py-3">Asal Pengirim</th>
                        <th class="px-4 py-3">Perihal</th>
                        <th class="px-4 py-3">Tanggal Diterima</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($letters as $index => $item)
                    <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-4 py-3 text-center text-xs text-gray-500 dark:text-gray-400">
                            {{ $letters->firstItem() + $index }}
                        </td>
                        <td class="px-4 py-3 font-mono font-medium text-gray-900 dark:text-gray-100">
                            {{ $item->nomor_surat }}
                        </td>
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                            {{ $item->asal_surat }}
                        </td>
                        <td class="px-4 py-3 max-w-xs font-medium text-gray-800 dark:text-gray-200">
                            {{ $item->perihal }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600 dark:text-gray-400">
                            {{ \Carbon\Carbon::parse($item->tanggal_diterima)->translatedFormat('d M Y') }}
                        </td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            @php
                                $statusLabels = [
                                    'baru' => ['label' => 'Baru', 'class' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300 border-blue-200 dark:border-blue-700'],
                                    'didistribusikan' => ['label' => 'Didistribusikan', 'class' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-300 border-indigo-200 dark:border-indigo-700'],
                                    'dalam_tindak_lanjut' => ['label' => 'Tindak Lanjut', 'class' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/60 dark:text-yellow-300 border-yellow-200 dark:border-yellow-700'],
                                    'selesai' => ['label' => 'Selesai', 'class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-700'],
                                ];
                                $st = $statusLabels[$item->status] ?? ['label' => $item->status, 'class' => 'bg-gray-100 text-gray-700'];
                            @endphp
                            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full border {{ $st['class'] }}">
                                {{ $st['label'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('surat-masuk.show', $item) }}" class="p-1 text-gray-500 hover:text-blue-600 dark:hover:text-blue-400" title="Detail Surat">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('surat-masuk.disposisi-pdf', $item) }}" target="_blank" class="p-1 text-gray-500 hover:text-red-600 dark:hover:text-red-400" title="Cetak Lembar Disposisi">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="font-medium text-base text-gray-600 dark:text-gray-400">Tidak ada data surat masuk</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Coba sesuaikan filter pencarian tanggal atau status.</p>
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
