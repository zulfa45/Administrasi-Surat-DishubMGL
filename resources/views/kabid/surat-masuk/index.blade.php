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
                            Kelola disposisi dan pantau tindak lanjut surat yang masuk ke bidang Anda.
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

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                        <tr>
                            <th class="px-6 py-4 font-semibold whitespace-nowrap">Surat</th>
                            <th class="px-6 py-4 font-semibold">Tujuan Personal</th>
                            <th class="px-6 py-4 font-semibold">Instruksi Kabid</th>
                            <th class="px-6 py-4 font-semibold text-center">Status</th>
                            <th class="px-6 py-4 font-semibold text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($assignments as $assignment)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 align-top">
                                    <div class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $assignment->incomingLetter->nomor_surat }}
                                    </div>
                                    <div class="text-xs mt-1 text-gray-500 font-medium">
                                        {{ $assignment->incomingLetter->asal_surat }}
                                    </div>
                                    <div class="text-xs mt-1.5 text-gray-600 dark:text-gray-400 line-clamp-2" title="{{ $assignment->incomingLetter->perihal }}">
                                        {{ $assignment->incomingLetter->perihal }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 align-top">
                                    @if($assignment->user)
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold">
                                                {{ substr($assignment->user->name, 0, 1) }}
                                            </div>
                                            <span class="font-medium">{{ $assignment->user->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-xs font-medium text-rose-500">Belum Disposisi</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 align-top">
                                    @if($assignment->catatan_kabid)
                                        <p class="text-xs line-clamp-3" title="{{ $assignment->catatan_kabid }}">{{ $assignment->catatan_kabid }}</p>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Belum ada instruksi</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 align-top text-center">
                                    @php
                                        $statusClass = match($assignment->status) {
                                            'selesai' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/50',
                                            'menunggu_verifikasi_kabid' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300 border border-amber-200 dark:border-amber-800/50',
                                            'belum_dibaca' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600',
                                            'perlu_revisi' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300 border border-rose-200 dark:border-rose-800/50',
                                            default => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 border border-blue-200 dark:border-blue-800/50',
                                        };
                                        $statusText = match($assignment->status) {
                                            'menunggu_verifikasi_kabid' => 'Menunggu Verifikasi',
                                            'belum_dibaca' => $assignment->user ? 'Belum Dibaca' : 'Menunggu Disposisi',
                                            'perlu_revisi' => 'Perlu Revisi',
                                            default => ucfirst(str_replace('_', ' ', $assignment->status))
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 align-top text-center">
                                    <a href="{{ route('kabid.surat-masuk.show', $assignment->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 rounded-lg text-xs font-medium transition-colors">
                                        Buka Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="font-medium">Tidak ada data surat masuk untuk bidang Anda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($assignments->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    {{ $assignments->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
