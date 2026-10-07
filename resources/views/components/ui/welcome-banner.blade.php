<div class="mb-6 relative bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden flex flex-col md:flex-row items-center justify-between border border-gray-200 dark:border-gray-700">
    <div class="absolute inset-0 bg-gradient-to-r from-blue-50/50 to-transparent dark:from-blue-900/20 dark:to-transparent pointer-events-none"></div>
    <div class="relative p-6 sm:p-8 flex-1">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Selamat Datang, {{ auth()->user()->name }}!</h2>
        <p class="text-gray-600 dark:text-gray-400">Anda login sebagai <span class="font-semibold text-blue-600 dark:text-blue-400 uppercase">{{ auth()->user()->roles->first()->name ?? 'Pengguna' }}</span> di Sistem Informasi Manajemen Administrasi Surat Menyurat Dinas Perhubungan Magelang.</p>
    </div>
    <div class="relative p-6 sm:p-8 flex items-center justify-center gap-6">
        <img src="{{ asset('images/kota.png') }}" alt="Logo Kota" class="w-16 h-auto drop-shadow-md">
        <img src="{{ asset('images/logo.png') }}" alt="Logo Dishub" class="w-20 h-auto drop-shadow-md">
    </div>
</div>
