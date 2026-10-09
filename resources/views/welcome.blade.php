<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Administrasi Surat - Dishub MGL</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script>
        // Init Dark Mode
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="antialiased bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 selection:bg-blue-500 selection:text-white">
    <div class="relative flex min-h-screen flex-col overflow-hidden justify-center items-center py-6 sm:py-12">
        <!-- Background decorative elements -->
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-5 dark:opacity-10"></div>
        <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-b from-blue-50/50 to-transparent dark:from-blue-900/20 dark:to-transparent pointer-events-none"></div>

        <!-- Theme Toggle Button in top right -->
        <div class="absolute top-6 right-6 z-20">
            <button onclick="toggleDarkTheme()" class="p-2.5 rounded-full bg-white/80 dark:bg-gray-800/80 backdrop-blur border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-800 transition-colors shadow-sm focus:outline-none" title="Ganti Tema">
                <!-- Moon icon (when light) -->
                <svg id="welcome-moon-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                </svg>
                <!-- Sun icon (when dark) -->
                <svg id="welcome-sun-icon" class="w-5 h-5 hidden text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4.22 1.289a1 1 0 011.415 0l.707.707a1 1 0 01-1.414 1.414l-.707-.707a1 1 0 010-1.414zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zm-1.289 4.22a1 1 0 010 1.415l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 0zM10 16a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zm-4.22-1.289a1 1 0 010-1.415l-.707-.707a1 1 0 011.414-1.414l.707.707a1 1 0 01-1.414 1.414zM2 10a1 1 0 011-1h1a1 1 0 110 2H3a1 1 0 01-1-1zm1.289-4.22a1 1 0 011.415 0l.707.707a1 1 0 01-1.414 1.414L3.29 4.364a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>

        <div class="relative w-full max-w-4xl px-6 lg:px-8">
            <div class="text-center">
                <!-- Logos container -->
                <div class="flex justify-center items-center gap-6 mb-8">
                    <!-- Logo Kota/Kabupaten (Kiri) -->
                    <img src="{{ asset('images/kota.png') }}" alt="Logo Kota" class="w-20 h-auto transform hover:scale-105 transition-transform duration-300 drop-shadow-lg" />
                    
                    <!-- Logo Dishub (Kanan) -->
                    <img src="{{ asset('images/logo.png') }}" alt="Logo Dishub" class="w-24 h-auto transform hover:scale-105 transition-transform duration-300 drop-shadow-xl" />
                </div>

                <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-gray-900 dark:text-white mb-4">
                    SIMAS <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400">DISHUB</span>
                </h1>
                <p class="mt-4 text-lg sm:text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto font-medium">
                    Sistem Informasi Manajemen Administrasi Surat Menyurat <br class="hidden sm:block"/>
                    Dinas Perhubungan Magelang.
                </p>

                <div class="mt-10 flex flex-col sm:flex-row justify-center items-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="group relative inline-flex items-center justify-center px-8 py-3.5 text-base font-semibold text-white bg-blue-600 rounded-full overflow-hidden shadow-lg hover:bg-blue-700 hover:shadow-xl transition-all duration-300">
                            <span class="absolute w-0 h-0 transition-all duration-500 ease-out bg-white rounded-full group-hover:w-56 group-hover:h-56 opacity-10"></span>
                            Kembali ke Dashboard
                            <svg class="w-5 h-5 ml-2 -mr-1 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="group relative inline-flex items-center justify-center px-8 py-3.5 text-base font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full overflow-hidden shadow-lg hover:shadow-indigo-500/30 transition-all duration-300 transform hover:-translate-y-1">
                            <span class="absolute w-0 h-0 transition-all duration-500 ease-out bg-white rounded-full group-hover:w-56 group-hover:h-56 opacity-10"></span>
                            Masuk ke Aplikasi
                            <svg class="w-5 h-5 ml-2 -mr-1 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Features Grid -->
            <div class="mt-20 grid grid-cols-1 md:grid-cols-3 gap-8 text-center border-t border-gray-200 dark:border-gray-800 pt-10">
                <div class="p-4">
                    <div class="w-12 h-12 mx-auto bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Surat Menyurat Digital</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Kelola surat masuk secara digital dengan cepat dan efisien.</p>
                </div>
                <div class="p-4">
                    <div class="w-12 h-12 mx-auto bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Disposisi Berjenjang</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Sistem disposisi terstruktur untuk setiap tingkatan departemen.</p>
                </div>
                <div class="p-4">
                    <div class="w-12 h-12 mx-auto bg-teal-100 dark:bg-teal-900/50 text-teal-600 dark:text-teal-400 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Aman & Terpusat</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Data tersimpan dengan aman dengan hak akses berbasis role (RBAC).</p>
                </div>
            </div>
        </div>

        <footer class="absolute bottom-4 text-sm text-gray-500 dark:text-gray-400 text-center w-full">
            &copy; {{ date('Y') }} Dinas Perhubungan Magelang. Hak Cipta Dilindungi.
        </footer>
    </div>

    <script>
        const moonIcon = document.getElementById('welcome-moon-icon');
        const sunIcon = document.getElementById('welcome-sun-icon');

        function updateIcons() {
            if (document.documentElement.classList.contains('dark')) {
                sunIcon.classList.remove('hidden');
                moonIcon.classList.add('hidden');
            } else {
                moonIcon.classList.remove('hidden');
                sunIcon.classList.add('hidden');
            }
        }
        updateIcons();

        function toggleDarkTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
            }
            updateIcons();
        }
    </script>
</body>
</html>
