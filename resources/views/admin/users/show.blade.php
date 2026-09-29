<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">Master User</a>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <span class="text-gray-800 dark:text-gray-200 font-semibold">Detail User</span>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xs p-6">
            <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100 dark:border-gray-700">
                <img class="w-16 h-16 rounded-full object-cover bg-gray-200"
                     src="{{ $user->avatar ? Storage::url($user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&color=7F9CF5&background=EBF4FF&size=64' }}"
                     alt="{{ $user->name }}"/>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ $user->name }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                    <div class="mt-1 flex gap-2 flex-wrap">
                        @foreach($user->roles as $role)
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                                {{ $role->name === 'admin' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-200' :
                                   ($role->name === 'staf-loket' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' :
                                   'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200') }}">
                                {{ ucfirst($role->name) }}
                            </span>
                        @endforeach
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $user->status === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200' }}">
                            {{ $user->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                </div>
            </div>

            <dl class="grid gap-4 md:grid-cols-2">
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">NIP</dt>
                    <dd class="mt-1 text-sm text-gray-800 dark:text-gray-200">{{ $user->nip ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jabatan</dt>
                    <dd class="mt-1 text-sm text-gray-800 dark:text-gray-200">{{ $user->jabatan ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Department</dt>
                    <dd class="mt-1 text-sm text-gray-800 dark:text-gray-200">{{ $user->department?->name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Dibuat</dt>
                    <dd class="mt-1 text-sm text-gray-800 dark:text-gray-200">{{ $user->created_at->format('d M Y, H:i') }}</dd>
                </div>
            </dl>

            <div class="mt-6 flex items-center gap-3">
                <a href="{{ route('admin.users.edit', $user) }}" class="px-4 py-2 bg-yellow-500 text-white text-sm font-medium rounded-lg hover:bg-yellow-600 transition-colors">Edit</a>
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>
