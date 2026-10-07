@php
    $message = session('success') ?? session('status') ?? session('error') ?? session('info');
    $type = 'success';
    if (session('error')) {
        $type = 'error';
    } elseif (session('info')) {
        $type = 'info';
    }
@endphp

@if($message)
<div x-data="{ show: true, progress: 100 }"
     x-init="
        let timer = setInterval(() => {
            progress -= 2.5;
            if (progress <= 0) {
                clearInterval(timer);
                show = false;
            }
        }, 100);
     "
     x-show="show"
     x-cloak
     x-transition:enter="transform ease-out duration-300 transition"
     x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
     x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0 scale-95"
     class="fixed top-5 right-5 z-50 max-w-sm w-full bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden pointer-events-auto">
    <div class="p-4 flex items-start gap-3">
        @if($type === 'success')
            <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-400 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        @elseif($type === 'error')
            <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 dark:bg-rose-900/40 dark:text-rose-400 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
        @else
            <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        @endif

        <div class="flex-1 pt-0.5 min-w-0">
            <h4 class="text-xs font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wider">
                @if($type === 'success')
                    Berhasil Diproses
                @elseif($type === 'error')
                    Terjadi Kesalahan
                @else
                    Pemberitahuan
                @endif
            </h4>
            <p class="text-xs text-gray-600 dark:text-gray-300 mt-0.5 leading-relaxed">
                {{ $message }}
            </p>
        </div>

        <button @click="show = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors p-1 -mr-1 -mt-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    {{-- Progress bar --}}
    <div class="h-1 w-full bg-gray-100 dark:bg-gray-700">
        <div class="h-full {{ $type === 'success' ? 'bg-emerald-500' : ($type === 'error' ? 'bg-rose-500' : 'bg-blue-500') }} transition-all duration-100 ease-linear"
             :style="'width: ' + progress + '%'"></div>
    </div>
</div>
@endif
