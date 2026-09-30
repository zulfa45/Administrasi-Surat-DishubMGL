<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SIMAS Dishub') }} - Login</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <script>
            if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
        <div class="flex min-h-screen">
            <!-- Left Side / Branding -->
            <div class="hidden lg:flex lg:w-1/2 relative bg-blue-600 dark:bg-gray-800 items-center justify-center overflow-hidden">
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
                <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-blue-700/50 to-indigo-900/80 pointer-events-none"></div>
                
                <div class="relative z-10 flex flex-col items-center text-center px-12">
                    <div class="flex items-center justify-center gap-4 mb-8">
                        <div class="w-16 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg shadow-lg flex items-center justify-center transform -rotate-3 hover:rotate-0 transition-transform duration-300">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m3-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div class="w-20 h-20 bg-white dark:bg-gray-800 rounded-full shadow-xl flex items-center justify-center border-4 border-blue-400 dark:border-gray-700 transform hover:scale-105 transition-transform duration-300">
                            <svg class="w-10 h-10 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                            </svg>
                        </div>
                    </div>
                    <h1 class="text-4xl font-extrabold text-white tracking-tight mb-4">SIMAS DISHUB</h1>
                    <p class="text-blue-100 text-lg font-medium max-w-md">
                        Sistem Informasi Manajemen Administrasi Surat Menyurat Dinas Perhubungan Magelang.
                    </p>
                </div>
            </div>

            <!-- Right Side / Form -->
            <div class="flex w-full lg:w-1/2 flex-col justify-center items-center px-6 py-12 relative bg-white dark:bg-gray-900 shadow-2xl lg:shadow-none">
                <!-- Back & Theme Toggle -->
                <div class="absolute top-6 left-6 lg:left-auto lg:right-6 w-full lg:w-auto flex justify-between lg:justify-end px-6 lg:px-0">
                    <a href="{{ route('home') }}" class="lg:hidden flex items-center text-sm font-medium text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        Kembali
                    </a>
                    <button onclick="toggleDark()" class="p-2 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm ml-auto lg:ml-0">
                        <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                        <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4.22 1.289a1 1 0 011.415 0l.707.707a1 1 0 01-1.414 1.414l-.707-.707a1 1 0 010-1.414zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zm-1.289 4.22a1 1 0 010 1.415l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 0zM10 16a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zm-4.22-1.289a1 1 0 010-1.415l-.707-.707a1 1 0 011.414-1.414l.707.707a1 1 0 01-1.414 1.414zM2 10a1 1 0 011-1h1a1 1 0 110 2H3a1 1 0 01-1-1zm1.289-4.22a1 1 0 011.415 0l.707.707a1 1 0 01-1.414 1.414L3.29 4.364a1 1 0 010-1.414z"></path></svg>
                    </button>
                </div>

                <!-- Mobile Logo -->
                <div class="lg:hidden flex flex-col items-center justify-center gap-3 mb-8 mt-6">
                    <div class="w-16 h-16 bg-white dark:bg-gray-800 rounded-full shadow-lg flex items-center justify-center border-2 border-blue-500">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">SIMAS <span class="text-blue-600 dark:text-blue-400">DISHUB</span></h2>
                </div>

                <div class="w-full max-w-md">
                    {{ $slot }}
                </div>
                
                <p class="mt-12 text-sm text-center text-gray-500 dark:text-gray-400">
                    &copy; {{ date('Y') }} Dinas Perhubungan Magelang.
                </p>
            </div>
        </div>

        <script>
            var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
            var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

            if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                themeToggleLightIcon.classList.remove('hidden');
            } else {
                themeToggleDarkIcon.classList.remove('hidden');
            }

            function toggleDark() {
                themeToggleDarkIcon.classList.toggle('hidden');
                themeToggleLightIcon.classList.toggle('hidden');

                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
            }
        </script>
    </body>
</html>
