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
            <div class="mt-6 flex gap-3">
                <a href="{{ route('admin.departments.edit', $department) }}" class="px-4 py-2 bg-yellow-500 text-white text-sm font-medium rounded-lg hover:bg-yellow-600 transition-colors">Edit</a>
                <a href="{{ route('admin.departments.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">Kembali</a>
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
