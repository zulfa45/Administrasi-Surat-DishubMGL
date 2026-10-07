<header class="z-10 py-4 bg-white shadow-sm dark:bg-gray-800">
    <div class="container flex items-center justify-between h-full px-6 mx-auto text-blue-600 dark:text-blue-300">
        <!-- Mobile hamburger -->
        <button class="p-1 mr-5 -ml-1 rounded-md md:hidden focus:outline-none focus:shadow-outline-blue" @click="isSidebarOpen = !isSidebarOpen" aria-label="Menu">
            <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
            </svg>
        </button>

        <!-- Search input (optional) -->
        <div class="flex justify-center flex-1 lg:mr-32">
            <div class="relative w-full max-w-xl mr-6 focus-within:text-blue-500">
                <div class="absolute inset-y-0 flex items-center pl-2">
                    <svg class="w-4 h-4" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <input class="w-full pl-8 pr-2 text-sm text-gray-700 placeholder-gray-600 bg-gray-100 border-0 rounded-md dark:placeholder-gray-500 dark:focus:shadow-outline-gray dark:focus:placeholder-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:placeholder-gray-500 focus:bg-white focus:border-blue-300 focus:outline-none focus:shadow-outline-blue form-input" type="text" placeholder="Cari surat..." aria-label="Search" />
            </div>
        </div>

        <ul class="flex items-center flex-shrink-0 space-x-6">
            <!-- Theme toggler -->
            <li class="flex">
                <button class="p-2 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 focus:outline-none transition-colors" 
                        @click="toggleTheme" 
                        :title="dark ? 'Ganti ke Mode Terang' : 'Ganti ke Mode Gelap'"
                        aria-label="Toggle color mode">
                    <!-- Sun icon (shown when dark) -->
                    <svg x-show="dark" x-cloak class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4.22 1.289a1 1 0 011.415 0l.707.707a1 1 0 01-1.414 1.414l-.707-.707a1 1 0 010-1.414zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zm-1.289 4.22a1 1 0 010 1.415l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 0zM10 16a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zm-4.22-1.289a1 1 0 010-1.415l-.707-.707a1 1 0 011.414-1.414l.707.707a1 1 0 01-1.414 1.414zM2 10a1 1 0 011-1h1a1 1 0 110 2H3a1 1 0 01-1-1zm1.289-4.22a1 1 0 011.415 0l.707.707a1 1 0 01-1.414 1.414L3.29 4.364a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                    <!-- Moon icon (shown when light) -->
                    <svg x-show="!dark" x-cloak class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                </button>
            </li>

            <!-- Notifications menu (Tahap U-12) -->
            <li class="relative" x-data="{ isNotificationsMenuOpen: false }">
                <button type="button" 
                        class="relative p-2 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 focus:outline-none transition-colors" 
                        @click="isNotificationsMenuOpen = !isNotificationsMenuOpen" 
                        @keydown.escape="isNotificationsMenuOpen = false" 
                        aria-label="Notifikasi" 
                        title="Notifikasi"
                        aria-haspopup="true">
                    <!-- Bell Icon -->
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>

                    @php
                        $unreadCount = auth()->user()->unreadNotifications()->count();
                        $latestNotification = auth()->user()->unreadNotifications()->latest()->first();
                        $latestId = $latestNotification ? $latestNotification->id : null;
                    @endphp

                    <!-- Notification Badge Container -->
                    <span id="nav-badge-container">
                        @if($unreadCount > 0)
                            <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-600 text-[10px] font-bold text-white shadow-xs">
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                            </span>
                        @endif
                    </span>
                </button>

                <div x-show="isNotificationsMenuOpen"
                     x-cloak
                     @click.outside="isNotificationsMenuOpen = false" 
                     @keydown.escape.window="isNotificationsMenuOpen = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75" 
                     x-transition:leave-start="opacity-100 scale-100" 
                     x-transition:leave-end="opacity-0 scale-95" 
                     class="absolute right-0 w-80 sm:w-96 mt-2 origin-top-right bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 divide-y divide-gray-100 dark:divide-gray-700/60 z-50">
                        
                    <!-- Header -->
                    <div class="p-3.5 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-sm text-gray-900 dark:text-gray-100">Notifikasi</span>
                            @if($unreadCount > 0)
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/60 dark:text-blue-300">
                                    {{ $unreadCount }} Baru
                                </span>
                            @endif
                        </div>

                        @if($unreadCount > 0)
                            <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium">
                                    Tandai Semua Dibaca
                                </button>
                            </form>
                        @endif
                    </div>

                    <!-- Notification Items -->
                    <div class="max-h-80 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700/40">
                        @forelse(auth()->user()->notifications()->take(6)->get() as $notif)
                            @php
                                $isUnread = is_null($notif->read_at);
                                $nData = $notif->data;
                            @endphp
                            <a href="{{ route('notifications.read', $notif->id) }}" 
                               class="block p-3.5 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors {{ $isUnread ? 'bg-blue-50/50 dark:bg-blue-900/10' : '' }}">
                                <div class="flex items-start gap-3">
                                    <span class="inline-flex p-2 rounded-lg {{ $isUnread ? 'bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-300' : 'bg-gray-100 text-gray-400 dark:bg-gray-700 dark:text-gray-400' }} flex-shrink-0 mt-0.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </span>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-1 mb-0.5">
                                            <p class="text-xs font-semibold text-gray-900 dark:text-gray-100 truncate">
                                                {{ $nData['judul'] ?? 'Disposisi Baru' }}
                                            </p>
                                            @if($isUnread)
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 flex-shrink-0"></span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-600 dark:text-gray-300 line-clamp-2 leading-relaxed">
                                            {{ $nData['pesan'] ?? 'Ada disposisi baru untuk Anda.' }}
                                        </p>
                                        <span class="text-[11px] text-gray-400 dark:text-gray-500 mt-1 block">
                                            {{ $notif->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="py-8 text-center">
                                <svg class="w-8 h-8 mx-auto text-gray-300 dark:text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Tidak ada notifikasi saat ini</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Footer -->
                    <div class="p-2.5 text-center bg-gray-50/50 dark:bg-gray-800/80 rounded-b-xl">
                        <a href="{{ route('notifications.index') }}" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 inline-flex items-center gap-1">
                            <span>Lihat Seluruh Notifikasi</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </li>

            <!-- Profile menu -->
            <li class="relative" x-data="{ isProfileMenuOpen: false }">
                <button class="align-middle rounded-xl focus:outline-none flex items-center gap-2 p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700/60 transition-colors" 
                        @click="isProfileMenuOpen = !isProfileMenuOpen" 
                        @keydown.escape="isProfileMenuOpen = false" 
                        aria-label="Menu Profil" 
                        title="Klik untuk membuka menu profil & edit profil"
                        aria-haspopup="true">
                    <img class="object-cover w-8 h-8 rounded-full border border-gray-300 dark:border-gray-600 shadow-xs" 
                         src="{{ auth()->user()->avatar ? \Illuminate\Support\Facades\Storage::url(auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&color=7F9CF5&background=EBF4FF' }}" 
                         alt="{{ auth()->user()->name }}" />
                    <div class="text-left hidden md:block">
                        <span class="text-xs font-semibold text-gray-800 dark:text-gray-200 block leading-tight">{{ auth()->user()->name }}</span>
                        <span class="text-[10px] text-gray-500 dark:text-gray-400 capitalize block leading-tight">{{ auth()->user()->roles->first()->name ?? 'Pengguna' }}</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 hidden md:block" :class="{ 'rotate-180': isProfileMenuOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div x-show="isProfileMenuOpen" 
                     x-cloak
                     @click.outside="isProfileMenuOpen = false" 
                     @keydown.escape.window="isProfileMenuOpen = false"
                     x-transition:enter="transition ease-out duration-100" 
                     x-transition:enter-start="opacity-0 scale-95" 
                     x-transition:enter-end="opacity-100 scale-100" 
                     x-transition:leave="transition ease-in duration-75" 
                     x-transition:leave-start="opacity-100 scale-100" 
                     x-transition:leave-end="opacity-0 scale-95" 
                     class="absolute right-0 w-60 mt-2 origin-top-right bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 divide-y divide-gray-100 dark:divide-gray-700/60 z-50">
                    
                    <!-- Header Info Pengguna -->
                    <div class="px-4 py-3">
                        <p class="text-[11px] text-gray-500 dark:text-gray-400">Masuk sebagai</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate mt-0.5">{{ auth()->user()->name }}</p>
                        <span class="inline-block mt-1.5 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider rounded bg-blue-100 text-blue-700 dark:bg-blue-900/60 dark:text-blue-300">
                            {{ auth()->user()->roles->first()->name ?? 'Pengguna' }}
                        </span>
                    </div>

                    <!-- Menu Navigasi -->
                    <div class="py-1">
                        <a href="{{ route('profile.edit') }}" 
                           class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-gray-700/60 dark:hover:text-blue-400 transition-colors">
                            <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            <span>Edit Profil</span>
                        </a>
                    </div>

                    <!-- Menu Logout -->
                    <div class="py-1">
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <button type="submit" 
                                    class="flex items-center w-full px-4 py-2 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                <svg class="w-4 h-4 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span>Keluar (Log Out)</span>
                            </button>
                        </form>
                    </div>
                </div>
            </li>
        </ul>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let lastNotificationId = '{{ $latestId ?? "" }}';
            
            function playNotificationSound() {
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    if(!ctx) return;
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(880, ctx.currentTime);
                    osc.frequency.exponentialRampToValueAtTime(1760, ctx.currentTime + 0.1);
                    gain.gain.setValueAtTime(0, ctx.currentTime);
                    gain.gain.linearRampToValueAtTime(0.3, ctx.currentTime + 0.05);
                    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.5);
                    osc.start(ctx.currentTime);
                    osc.stop(ctx.currentTime + 0.5);
                } catch(e) {}
            }

            setInterval(() => {
                fetch('{{ route("notifications.check") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                    .then(res => {
                        if (res.status === 401) {
                            // Sesi habis, reload halaman untuk diarahkan ke halaman login
                            window.location.reload();
                            throw new Error('Sesi habis');
                        }
                        return res.json();
                    })
                    .then(data => {
                        const badgeContainer = document.getElementById('nav-badge-container');
                        
                        if (data.count > 0) {
                            badgeContainer.innerHTML = `<span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-600 text-[10px] font-bold text-white shadow-xs">${data.count > 9 ? '9+' : data.count}</span>`;
                        } else {
                            badgeContainer.innerHTML = '';
                        }

                        if (data.latest_id && data.latest_id !== lastNotificationId) {
                            playNotificationSound();
                            lastNotificationId = data.latest_id;
                        }
                    })
                    .catch(err => {
                        if(err.message !== 'Sesi habis') console.error(err);
                    });
            }, 10000); // Polling setiap 10 detik
        });
    </script>
</header>
