<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-xl text-gray-900 dark:text-gray-100 leading-tight">
                            Anggota Bidang
                        </h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Daftar Karyawan yang berada di bawah bidang Anda (Aktif).
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if(session('error'))
            <x-ui.alert type="error" :message="session('error')" />
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Nama Pegawai</th>
                            <th class="px-6 py-4 font-semibold">NIP</th>
                            <th class="px-6 py-4 font-semibold">Jabatan</th>
                            <th class="px-6 py-4 font-semibold">Email</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($members as $member)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($member->avatar)
                                            <img src="{{ Storage::disk('s3')->url($member->avatar) }}" alt="{{ $member->name }}" class="w-10 h-10 rounded-full object-cover border border-gray-200">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-sm border border-indigo-200">
                                                {{ substr($member->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div class="font-medium text-gray-900 dark:text-gray-100">
                                            {{ $member->name }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">{{ $member->nip ?? '-' }}</td>
                                <td class="px-6 py-4">{{ $member->jabatan ?? '-' }}</td>
                                <td class="px-6 py-4">{{ $member->email }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    <p class="font-medium">Belum ada Karyawan yang terdaftar di bidang ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
