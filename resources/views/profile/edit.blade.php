<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-gray-900 dark:text-gray-100 leading-tight">
                    Pengaturan Profil Pengguna
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Kelola foto profil, informasi akun, dan kata sandi Anda.
                </p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300 uppercase tracking-wider self-start sm:self-auto">
                {{ auth()->user()->roles->first()->name ?? 'Pengguna' }}
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
