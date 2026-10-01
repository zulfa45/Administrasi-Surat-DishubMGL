<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Tambah Surat Masuk
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Catat dokumen surat masuk baru ke dalam sistem
                </p>
            </div>
            <a href="{{ route('surat-masuk.index') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <form action="{{ route('surat-masuk.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf

                <div class="border-b border-gray-100 dark:border-gray-700 pb-4">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Informasi Pokok Surat</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Pastikan nomor dan asal surat sesuai dengan berkas fisik.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Nomor Surat --}}
                    <div>
                        <label for="nomor_surat" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nomor Surat <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nomor_surat" id="nomor_surat" value="{{ old('nomor_surat') }}" required
                               placeholder="Contoh: 005/123/Dishub/2026"
                               class="w-full px-3.5 py-2 text-sm border @error('nomor_surat') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('nomor_surat')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Asal Surat --}}
                    <div>
                        <label for="asal_surat" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Asal Surat / Pengirim <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="asal_surat" id="asal_surat" value="{{ old('asal_surat') }}" required
                               placeholder="Contoh: Sekretariat Daerah / PT. Mitra Transportasi"
                               class="w-full px-3.5 py-2 text-sm border @error('asal_surat') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('asal_surat')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tanggal Surat --}}
                    <div>
                        <label for="tanggal_surat" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Tanggal Surat (Pada Naskah) <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_surat" id="tanggal_surat" value="{{ old('tanggal_surat') }}" required
                               class="w-full px-3.5 py-2 text-sm border @error('tanggal_surat') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('tanggal_surat')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tanggal Diterima --}}
                    <div>
                        <label for="tanggal_diterima" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Tanggal Diterima di Loket <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_diterima" id="tanggal_diterima" value="{{ old('tanggal_diterima', date('Y-m-d')) }}" required
                               class="w-full px-3.5 py-2 text-sm border @error('tanggal_diterima') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('tanggal_diterima')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Perihal --}}
                <div>
                    <label for="perihal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Perihal Surat <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="perihal" id="perihal" value="{{ old('perihal') }}" required
                           placeholder="Contoh: Undangan Rapat Koordinasi Pengaturan Lalu Lintas Mudik"
                           class="w-full px-3.5 py-2 text-sm border @error('perihal') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('perihal')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Sifat Surat --}}
                    <div>
                        <label for="sifat" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Sifat Surat <span class="text-red-500">*</span>
                        </label>
                        <select name="sifat" id="sifat" required
                                class="w-full px-3.5 py-2 text-sm border @error('sifat') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="biasa" {{ old('sifat') === 'biasa' ? 'selected' : '' }}>Biasa</option>
                            <option value="penting" {{ old('sifat') === 'penting' ? 'selected' : '' }}>Penting</option>
                            <option value="segera" {{ old('sifat') === 'segera' ? 'selected' : '' }}>Segera</option>
                            <option value="rahasia" {{ old('sifat') === 'rahasia' ? 'selected' : '' }}>Rahasia</option>
                        </select>
                        @error('sifat')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Upload File Lampiran (Tahap U-04) --}}
                    <div>
                        <label for="file_lampiran" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            File Lampiran Dokumen <span class="text-xs text-gray-400 font-normal">(PDF, JPG, JPEG, PNG maks 5MB)</span>
                        </label>
                        <input type="file" name="file_lampiran" id="file_lampiran" accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full text-xs text-gray-500 dark:text-gray-400 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 dark:file:bg-gray-700 dark:file:text-blue-300 hover:file:bg-blue-100 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700">
                        @error('file_lampiran')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Keterangan --}}
                <div>
                    <label for="keterangan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Keterangan Tambahan / Catatan Ringkas
                    </label>
                    <textarea name="keterangan" id="keterangan" rows="3"
                              placeholder="Keterangan isi surat, lampiran fisik, atau catatan loket..."
                              class="w-full px-3.5 py-2 text-sm border @error('keterangan') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Opsi Disposisi Langsung --}}
                <div class="border-t border-gray-100 dark:border-gray-700 pt-6 mt-6" x-data="{ tujuanTipe: '{{ old('tujuan_tipe', 'none') }}' }">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-4">Opsi Disposisi Langsung (Opsional)</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-4">
                        <div>
                            <label for="tujuan_tipe" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Teruskan Ke</label>
                            <select name="tujuan_tipe" id="tujuan_tipe" x-model="tujuanTipe" class="w-full px-3.5 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="none">Hanya Simpan Surat (Tanpa Disposisi)</option>
                                <option value="department">Bidang / Departemen</option>
                                <option value="user">Perseorangan / Pegawai</option>
                            </select>
                        </div>
                        
                        <div x-show="tujuanTipe === 'department'" x-cloak>
                            <label for="department_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih Bidang</label>
                            <select name="department_id" id="department_id" class="w-full px-3.5 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih Bidang --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            @error('department_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div x-show="tujuanTipe === 'user'" x-cloak>
                            <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih Pegawai</label>
                            <select name="user_id" id="user_id" class="w-full px-3.5 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih Pegawai --</option>
                                @foreach($users as $usr)
                                    <option value="{{ $usr->id }}" {{ old('user_id') == $usr->id ? 'selected' : '' }}>{{ $usr->name }} - {{ $usr->department?->name ?? 'Tanpa Bidang' }}</option>
                                @endforeach
                            </select>
                            @error('user_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-4" x-show="tujuanTipe !== 'none'" x-cloak>
                        <div>
                            <label for="deadline" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Batas Waktu (Deadline)</label>
                            <input type="date" name="deadline" id="deadline" value="{{ old('deadline') }}" min="{{ date('Y-m-d') }}" class="w-full px-3.5 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('deadline') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="catatan_disposisi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan Instruksi</label>
                            <textarea name="catatan_disposisi" id="catatan_disposisi" rows="2" placeholder="Instruksi tambahan untuk penerima..." class="w-full px-3.5 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('catatan_disposisi') }}</textarea>
                            @error('catatan_disposisi') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex items-center justify-between gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <span class="text-xs text-gray-500 italic">* Nomor Agenda (Internal) akan di-generate otomatis oleh sistem.</span>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('surat-masuk.index') }}"
                           class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                            Batal
                        </a>
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Simpan Data
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
