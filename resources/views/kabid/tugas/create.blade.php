<x-app-layout>
    <x-slot name="header">
        <div class="space-y-2">
            <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                <a href="{{ route('kabid.dashboard') }}" class="hover:text-emerald-600 transition-colors flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Dashboard
                </a>
                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                <a href="{{ route('kabid.tugas.index') }}" class="hover:text-emerald-600 transition-colors">Tugas Langsung</a>
                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                <span class="text-gray-800 dark:text-gray-200 font-semibold">Buat Tugas Baru</span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="font-bold text-xl text-gray-900 dark:text-gray-100 leading-tight">
                        Formulir Tugas Mandiri Staf
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Berikan instruksi penugasan langsung kepada satu atau beberapa staf di bidang Anda.
                    </p>
                </div>
                <a href="{{ route('kabid.tugas.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs font-semibold rounded-xl transition-colors shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6" x-data="{
        selectAll: false,
        toggleAll(checked) {
            const checkboxes = document.querySelectorAll('.staff-checkbox');
            checkboxes.forEach(cb => cb.checked = checked);
        }
    }">
        @if($errors->any())
            <div class="p-4 rounded-xl bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 text-xs space-y-1">
                <p class="font-bold">Mohon perbaiki isian form berikut:</p>
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('kabid.tugas.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- Card Informasi Tugas --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-5">
                <div class="border-b border-gray-100 dark:border-gray-700 pb-3 flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <h3 class="font-bold text-base text-gray-900 dark:text-gray-100">
                        1. Informasi & Perihal Tugas
                    </h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Judul Tugas --}}
                    <div class="sm:col-span-2">
                        <label for="judul_tugas" class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                            Judul / Nama Tugas <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="judul_tugas" id="judul_tugas" value="{{ old('judul_tugas') }}" required
                               placeholder="Contoh: Pemeriksaan Fasilitas & Rambu Lalu Lintas Jl. Pemuda"
                               class="w-full px-3.5 py-2.5 text-sm border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50/50 dark:bg-gray-900/40 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white dark:focus:bg-gray-900 transition-all">
                        @error('judul_tugas')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Sifat Tugas --}}
                    <div>
                        <label for="sifat" class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                            Sifat / Prioritas Tugas <span class="text-rose-500">*</span>
                        </label>
                        <select name="sifat" id="sifat" required
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50/50 dark:bg-gray-900/40 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
                            <option value="Biasa" {{ old('sifat', 'Biasa') === 'Biasa' ? 'selected' : '' }}>Biasa</option>
                            <option value="Penting" {{ old('sifat') === 'Penting' ? 'selected' : '' }}>Penting</option>
                            <option value="Segera" {{ old('sifat') === 'Segera' ? 'selected' : '' }}>Segera</option>
                            <option value="Sangat Segera" {{ old('sifat') === 'Sangat Segera' ? 'selected' : '' }}>Sangat Segera</option>
                            <option value="Rahasia" {{ old('sifat') === 'Rahasia' ? 'selected' : '' }}>Rahasia</option>
                        </select>
                        @error('sifat')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Batas Waktu / Deadline --}}
                    <div>
                        <label for="deadline" class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                            Batas Waktu Penyelesaian (Deadline)
                        </label>
                        <input type="date" name="deadline" id="deadline" value="{{ old('deadline') }}" min="{{ date('Y-m-d') }}"
                               class="w-full px-3.5 py-2.5 text-sm border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50/50 dark:bg-gray-900/40 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
                        @error('deadline')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Instruksi / Catatan Arahan --}}
                    <div class="sm:col-span-2">
                        <label for="catatan_kabid" class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                            Instruksi / Arahan Lengkap dari Pimpinan <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="catatan_kabid" id="catatan_kabid" rows="4" required
                                  placeholder="Jelaskan ruang lingkup tugas, output yang diharapkan, serta petunjuk teknis pelaksanaan..."
                                  class="w-full px-3.5 py-2.5 text-sm border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50/50 dark:bg-gray-900/40 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">{{ old('catatan_kabid') }}</textarea>
                        @error('catatan_kabid')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Card Lampiran Berkas Petunjuk (Opsional) --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                <div class="border-b border-gray-100 dark:border-gray-700 pb-3 flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-base text-gray-900 dark:text-gray-100">
                            2. Berkas Petunjuk / Panduan (Opsional)
                        </h3>
                        <p class="text-xs text-gray-400">Lampirkan file acuan, format laporan, formulir, atau materi pendukung untuk staf.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="file_tugas" class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                            Pilih Dokumen (PDF, DOCX, XLSX, JPG, PNG - Maks 10MB)
                        </label>
                        <input type="file" name="file_tugas" id="file_tugas"
                               onchange="if(this.files[0] && !document.getElementById('nama_file_tugas').value) document.getElementById('nama_file_tugas').value = this.files[0].name.split('.').slice(0, -1).join('.')"
                               class="w-full text-xs border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50/50 dark:bg-gray-900/40 p-2 text-gray-700 dark:text-gray-300">
                    </div>

                    <div>
                        <label for="nama_file_tugas" class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                            Nama File Khusus (Opsional)
                        </label>
                        <input type="text" name="nama_file_tugas" id="nama_file_tugas" value="{{ old('nama_file_tugas') }}"
                               placeholder="Contoh: Panduan_Teknis_Operasi"
                               class="w-full px-3.5 py-2 text-xs border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50/50 dark:bg-gray-900/40 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
            </div>

            {{-- Card Pilih Staf Pelaksana --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                <div class="border-b border-gray-100 dark:border-gray-700 pb-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-base text-gray-900 dark:text-gray-100">
                                3. Pilih Staf Pelaksana <span class="text-rose-500">*</span>
                            </h3>
                            <p class="text-xs text-gray-400">Centang satu atau lebih staf yang bertanggung jawab melaksanakan tugas ini.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="selectAll = !selectAll; toggleAll(selectAll)"
                                class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-100 transition-colors">
                            <span x-text="selectAll ? 'Batal Pilih Semua' : 'Pilih Semua Staf'"></span>
                        </button>
                    </div>
                </div>

                @if($members->isEmpty())
                    <div class="py-8 text-center text-gray-400 text-xs">
                        Tidak ada staf/karyawan yang terdaftar di bidang Anda. Hubungi Administrator untuk menambahkan user.
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($members as $staff)
                            @php
                                $isChecked = (is_array(old('user_ids')) && in_array($staff->id, old('user_ids'))) || ((string)$selectedUserId === (string)$staff->id);
                            @endphp
                            <label class="relative flex items-center gap-3 p-3.5 rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-emerald-50/40 dark:hover:bg-emerald-950/20 cursor-pointer transition-all has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/60 dark:has-[:checked]:bg-emerald-950/30">
                                <input type="checkbox" name="user_ids[]" value="{{ $staff->id }}" {{ $isChecked ? 'checked' : '' }}
                                       class="staff-checkbox rounded text-emerald-600 focus:ring-emerald-500 h-4 w-4 border-gray-300">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                    {{ substr($staff->name, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-xs text-gray-900 dark:text-gray-100 truncate">
                                        {{ $staff->name }}
                                    </p>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">
                                        {{ $staff->jabatan ?? 'Staf Bidang' }}
                                    </p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endif
                @error('user_ids')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('kabid.tugas.index') }}"
                   class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-xl text-xs transition-colors">
                    Batalkan
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-xs transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Simpan & Kirim Penugasan</span>
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
