<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.departments.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">Master Department</a>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <span class="text-gray-800 dark:text-gray-200 font-semibold">Detail Department</span>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="max-w-xl bg-white dark:bg-gray-800 rounded-lg shadow-xs p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m3-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                {{ $department->name }}
            </h3>
            <dl class="grid gap-3">
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Deskripsi</dt>
                    <dd class="mt-1 text-sm text-gray-800 dark:text-gray-200">{{ $department->description ?? '-' }}</dd>
                </div>
                <div class="flex items-center gap-4">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $department->status === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200' }}">
                                {{ $department->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Dibuat</dt>
                        <dd class="mt-1 text-sm text-gray-800 dark:text-gray-200">{{ $department->created_at->format('d M Y') }}</dd>
                    </div>
                </div>
            </dl>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ $department->monitor_url }}" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    Buka Layar Monitor TV
                </a>
                <a href="{{ route('admin.departments.edit', $department) }}" class="px-4 py-2 bg-yellow-500 text-white text-sm font-medium rounded-lg hover:bg-yellow-600 transition-colors">Edit</a>
                <a href="{{ route('admin.departments.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">Kembali</a>
            </div>
        </div>

        {{-- Kartu Akses Monitor TV Layar Penuh --}}
        <div class="max-w-xl bg-slate-900 border border-slate-800 text-white rounded-lg shadow-md p-6" x-data="{ copied: false }">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <span class="p-2 bg-emerald-500/20 text-emerald-400 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </span>
                    <div>
                        <h4 class="text-sm font-bold text-white">Layar Monitor TV Bidang (Real-Time)</h4>
                        <p class="text-xs text-slate-400">Gunakan link ini pada Smart TV atau layar monitor di ruangan {{ $department->name }}.</p>
                    </div>
                </div>
                <span class="px-2 py-0.5 text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 rounded">OTOMATIS</span>
            </div>

            <div class="mt-4">
                <label class="block text-xs font-medium text-slate-400 mb-1">Tautan Khusus Display TV:</label>
                <div class="flex items-center gap-2">
                    <input type="text" readonly value="{{ $department->monitor_url }}"
                           class="w-full text-xs font-mono bg-slate-950 border border-slate-700 text-cyan-300 rounded-lg px-3 py-2 select-all focus:outline-none">
                    <button type="button" 
                            @click="navigator.clipboard.writeText('{{ $department->monitor_url }}'); copied = true; setTimeout(() => copied = false, 2500)"
                            class="flex-shrink-0 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors flex items-center gap-1">
                        <template x-if="!copied">
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                Salin
                            </span>
                        </template>
                        <template x-if="copied">
                            <span class="flex items-center gap-1 text-emerald-300">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Tersalin!
                            </span>
                        </template>
                    </button>
                </div>
                <p class="text-[11px] text-slate-500 mt-2">
                    * Layar monitor tidak memerlukan login dan tidak akan logout otomatis. Layar otomatis memutar nada dering jika terdapat surat/tugas baru yang didisposisikan.
                </p>
            </div>
        </div>

        {{-- Anggota Department --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h4 class="font-semibold text-gray-700 dark:text-gray-300">Anggota ({{ $department->users->count() }})</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700 dark:text-gray-300">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-xs text-gray-500 dark:text-gray-400 uppercase">
                        <tr>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Jabatan</th>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($department->users as $u)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-4 py-3 font-medium">{{ $u->name }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $u->jabatan ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @foreach($u->roles as $r)
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200">{{ ucfirst($r->name) }}</span>
                                @endforeach
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 text-xs rounded-full {{ $u->status === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200' }}">
                                    {{ $u->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Tidak ada anggota.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
