<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Disposisi Surat Masuk
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Distribusikan instruksi surat ke pegawai atau department terkait
                </p>
            </div>
            <a href="{{ route('surat-masuk.show', $letter) }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Detail Surat
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6" x-data="{ targetType: '{{ old('target_type', 'user') }}' }">
        {{-- Ringkasan Surat --}}
        <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg p-5">
            <h3 class="text-xs font-semibold text-blue-700 dark:text-blue-300 uppercase tracking-wider mb-2">
                Surat yang Didisposisikan
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                <div>
                    <span class="text-xs text-gray-500 dark:text-gray-400 block">Nomor Surat:</span>
                    <strong class="text-gray-900 dark:text-gray-100 font-mono">{{ $letter->nomor_surat }}</strong>
                </div>
                <div>
                    <span class="text-xs text-gray-500 dark:text-gray-400 block">Asal Pengirim:</span>
                    <strong class="text-gray-900 dark:text-gray-100">{{ $letter->asal_surat }}</strong>
                </div>
                <div class="sm:col-span-2">
                    <span class="text-xs text-gray-500 dark:text-gray-400 block">Perihal:</span>
                    <strong class="text-gray-900 dark:text-gray-100">{{ $letter->perihal }}</strong>
                </div>
            </div>
        </div>

        {{-- Form Disposisi --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <form action="{{ route('disposisi.store', $letter) }}" method="POST" class="p-6 space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Tujuan Disposisi <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer transition-colors"
                               :class="targetType === 'user' ? 'border-blue-500 bg-blue-50/50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700'">
                            <input type="radio" name="target_type" value="user" x-model="targetType" class="text-blue-600 focus:ring-blue-500">
                            <div>
                                <span class="block text-sm font-semibold text-gray-800 dark:text-gray-200">Individu Pegawai</span>
                                <span class="block text-xs text-gray-500 dark:text-gray-400">Tugaskan ke staf tertentu</span>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer transition-colors"
                               :class="targetType === 'department' ? 'border-blue-500 bg-blue-50/50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700'">
                            <input type="radio" name="target_type" value="department" x-model="targetType" class="text-blue-600 focus:ring-blue-500">
                            <div>
                                <span class="block text-sm font-semibold text-gray-800 dark:text-gray-200">Bagian / Department</span>
                                <span class="block text-xs text-gray-500 dark:text-gray-400">Tugaskan ke seluruh bidang</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Pilih Pegawai --}}
                <div x-show="targetType === 'user'" x-transition>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Pilih Pegawai Penerima Disposisi <span class="text-red-500">*</span>
                    </label>
                    <select name="user_id" id="user_id"
                            class="w-full px-3.5 py-2 text-sm border @error('user_id') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Pegawai --</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ $u->jabatan ?: 'Staff' }} - {{ $u->department?->name ?? 'Dishub' }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Pilih Department --}}
                <div x-show="targetType === 'department'" x-transition style="display: none;">
                    <label for="department_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Pilih Bagian / Department <span class="text-red-500">*</span>
                    </label>
                    <select name="department_id" id="department_id"
                            class="w-full px-3.5 py-2 text-sm border @error('department_id') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Bagian --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tanggal Disposisi --}}
                <div>
                    <label for="tanggal_disposisi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Tanggal Disposisi <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal_disposisi" id="tanggal_disposisi" value="{{ old('tanggal_disposisi', date('Y-m-d')) }}" required
                           class="w-full px-3.5 py-2 text-sm border @error('tanggal_disposisi') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('tanggal_disposisi')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Instruksi / Catatan Disposisi --}}
                <div>
                    <label for="catatan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Instruksi / Catatan Disposisi <span class="text-red-500">*</span>
                    </label>
                    <textarea name="catatan" id="catatan" rows="4" required
                              placeholder="Tuliskan petunjuk penugasan, batas waktu, atau arahan tindak lanjut..."
                              class="w-full px-3.5 py-2 text-sm border @error('catatan') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('catatan') }}</textarea>
                    @error('catatan')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror

                    {{-- Opsi Template Cepat --}}
                    <div class="mt-2 flex flex-wrap gap-1.5 items-center">
                        <span class="text-xs text-gray-400">Template Cepat:</span>
                        <button type="button" @click="document.getElementById('catatan').value = 'Mohon ditindaklanjuti dan dikoordinasikan sesuai prosedur.'"
                                class="text-xs px-2 py-0.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded text-gray-600 dark:text-gray-300 transition-colors">
                            Tindaklanjuti & Koordinasikan
                        </button>
                        <button type="button" @click="document.getElementById('catatan').value = 'Untuk dipelajari dan disiapkan draft tanggapan/balasan.'"
                                class="text-xs px-2 py-0.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded text-gray-600 dark:text-gray-300 transition-colors">
                            Siapkan Tanggapan
                        </button>
                        <button type="button" @click="document.getElementById('catatan').value = 'Untuk diketahui dan diarsipkan.'"
                                class="text-xs px-2 py-0.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded text-gray-600 dark:text-gray-300 transition-colors">
                            Untuk Diketahui & Arsipkan
                        </button>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('surat-masuk.show', $letter) }}"
                       class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Simpan & Distribusikan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
