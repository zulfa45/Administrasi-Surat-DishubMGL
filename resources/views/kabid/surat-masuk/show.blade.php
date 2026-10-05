<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-gray-900 dark:text-gray-100 leading-tight">
                    Detail Surat Bidang & Disposisi
                </h2>
            </div>
            <a href="{{ route('kabid.surat-masuk.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs font-semibold rounded-xl transition-colors shadow-xs">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Informasi Surat --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-gray-700 pb-4 mb-4">
                    Informasi Surat Masuk
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Nomor Surat</p>
                        <p class="font-medium">{{ $assignment->incomingLetter->nomor_surat }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Nomor Agenda</p>
                        <p class="font-medium">{{ $assignment->incomingLetter->nomor_agenda ?? '-' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-xs text-gray-500 mb-1">Asal Surat</p>
                        <p class="font-medium">{{ $assignment->incomingLetter->asal_surat }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-xs text-gray-500 mb-1">Perihal</p>
                        <p class="font-medium">{{ $assignment->incomingLetter->perihal }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Tanggal Surat</p>
                        <p class="font-medium">{{ \Carbon\Carbon::parse($assignment->incomingLetter->tanggal_surat)->translatedFormat('d F Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Tanggal Diterima</p>
                        <p class="font-medium">{{ \Carbon\Carbon::parse($assignment->incomingLetter->tanggal_diterima)->translatedFormat('d F Y') }}</p>
                    </div>
                </div>

                @if($assignment->incomingLetter->file_lampiran)
                    <div class="mt-6">
                        <a href="{{ route('surat-masuk.file', $assignment->incomingLetter->id) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-xl text-sm font-medium transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Lihat File Surat
                        </a>
                    </div>
                @endif
            </div>

            {{-- Hasil Tindak Lanjut --}}
            @if($assignment->status === 'menunggu_verifikasi_kabid' || $assignment->status === 'selesai' || $assignment->status === 'perlu_revisi')
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-gray-700 pb-4 mb-4">
                    Hasil Tindak Lanjut Karyawan
                </h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Keterangan / Laporan</p>
                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl text-sm">
                            {!! nl2br(e($assignment->catatan_tindak_lanjut)) !!}
                        </div>
                    </div>
                    
                    @if($assignment->file_tindak_lanjut)
                    <div>
                        <p class="text-xs text-gray-500 mb-2">File Laporan Karyawan</p>
                        <a href="{{ Storage::disk('google')->url($assignment->file_tindak_lanjut) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 text-green-700 hover:bg-green-100 rounded-xl text-sm font-medium transition-colors">
                            Lihat File Hasil
                        </a>
                    </div>
                    @endif
                </div>

                @if($assignment->status === 'menunggu_verifikasi_kabid')
                <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
                    <form action="{{ route('kabid.surat-masuk.verifikasi', $assignment->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div x-data="{ keputusan: 'terima' }">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Keputusan Verifikasi</label>
                            <div class="flex items-center gap-4 mb-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="keputusan" value="terima" x-model="keputusan" class="text-emerald-600 focus:ring-emerald-500">
                                    <span class="text-sm font-medium text-emerald-700">Setujui (Selesai)</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="keputusan" value="revisi" x-model="keputusan" class="text-rose-600 focus:ring-rose-500">
                                    <span class="text-sm font-medium text-rose-700">Tolak (Revisi)</span>
                                </label>
                            </div>
                            
                            <div x-show="keputusan === 'revisi'" style="display: none;">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Catatan Revisi</label>
                                <textarea name="catatan_revisi" rows="3" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm" placeholder="Berikan alasan kenapa perlu direvisi..."></textarea>
                            </div>
                        </div>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700">Simpan Verifikasi</button>
                    </form>
                </div>
                @endif
            </div>
            @endif
        </div>

        <div class="space-y-6">
            {{-- Form Disposisi --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-gray-700 pb-4 mb-4">
                    Disposisi ke Karyawan
                </h3>
                
                @if($assignment->status === 'selesai')
                    <div class="p-4 bg-emerald-50 text-emerald-800 rounded-xl mb-4 text-sm font-medium">
                        Surat ini telah selesai ditindaklanjuti.
                    </div>
                @endif

                <form action="{{ route('kabid.surat-masuk.disposisi', $assignment->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan Admin</label>
                        <div class="p-3 bg-gray-50 rounded-xl text-sm text-gray-600 italic">
                            {{ $assignment->catatan ?: 'Tidak ada catatan dari admin' }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Teruskan Ke (Karyawan)</label>
                        <select name="user_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" {{ in_array($assignment->status, ['selesai', 'menunggu_verifikasi_kabid']) ? 'disabled' : '' }}>
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach($karyawan as $k)
                                <option value="{{ $k->id }}" {{ $assignment->user_id == $k->id ? 'selected' : '' }}>{{ $k->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Instruksi Anda</label>
                        <textarea name="catatan_kabid" rows="3" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Instruksi untuk karyawan..." {{ in_array($assignment->status, ['selesai', 'menunggu_verifikasi_kabid']) ? 'disabled' : '' }}>{{ $assignment->catatan_kabid }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Batas Waktu (Deadline)</label>
                        <input type="date" name="deadline" value="{{ $assignment->deadline ? \Carbon\Carbon::parse($assignment->deadline)->format('Y-m-d') : '' }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" {{ in_array($assignment->status, ['selesai', 'menunggu_verifikasi_kabid']) ? 'disabled' : '' }}>
                    </div>
                    
                    @if(!in_array($assignment->status, ['selesai', 'menunggu_verifikasi_kabid']))
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl transition-colors">
                            Tugaskan / Simpan
                        </button>
                    @endif
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
