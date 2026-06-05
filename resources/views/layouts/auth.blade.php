<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>{{ $title ?? 'Tandi\'s Homemade Bakery' }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700" rel="stylesheet" />

        <meta name="dummy-toast" content="Fitur ini belum diaktifkan" />
        <meta name="dummy-toast-sub" content="Tugas Backend ah" />

        <meta name="toast-default-success" content="{{ __('app.flash.success') }}" />
        <meta name="toast-default-error" content="{{ __('app.flash.error') }}" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[color:var(--color-bakery-bg)] font-[family-name:var(--font-sans)] text-slate-800 antialiased">
        @php
            $locale = app()->getLocale();
            $langActiveClass = 'flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 ring-1 ring-amber-200';
            $langIdleClass = 'flex h-9 w-9 items-center justify-center rounded-xl hover:bg-white/80 transition';
        @endphp
        <div class="relative min-h-screen overflow-hidden">
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_15%_20%,rgba(245,158,11,0.14)_0%,transparent_40%),radial-gradient(circle_at_85%_10%,rgba(251,191,36,0.10)_0%,transparent_35%),radial-gradient(circle_at_50%_90%,rgba(120,113,108,0.06)_0%,transparent_45%)]"></div>
            </div>

            <div class="absolute right-4 top-4 z-10 flex gap-1 rounded-2xl bg-white/70 p-1.5 shadow-sm ring-1 ring-slate-200/80 backdrop-blur sm:right-6 sm:top-6">
                <a href="{{ route('locale.switch', 'id') }}" class="{{ $locale === 'id' ? $langActiveClass : $langIdleClass }}" aria-label="{{ __('app.common.language_id') }}" title="{{ __('app.common.language_id') }}">
                    <x-icons.flag-id class="h-4 w-6 rounded-sm ring-1 ring-black/10" />
                </a>
                <a href="{{ route('locale.switch', 'en') }}" class="{{ $locale === 'en' ? $langActiveClass : $langIdleClass }}" aria-label="{{ __('app.common.language_en') }}" title="{{ __('app.common.language_en') }}">
                    <x-icons.flag-us class="h-4 w-6 rounded-sm ring-1 ring-black/10" />
                </a>
            </div>

            <main class="relative flex min-h-screen w-full items-center justify-center px-4 py-8 sm:px-6 sm:py-12">
                @yield('content')
            </main>
        </div>

        @if (session('success'))
            <span class="hidden" data-flash-success>{{ session('success') }}</span>
        @endif
        @if (session('error'))
            <span class="hidden" data-flash-error>{{ session('error') }}</span>
        @endif

        <div id="toast-root" class="pointer-events-none fixed right-6 top-6 z-[100] flex w-[360px] max-w-[92vw] flex-col gap-2"></div>
    </body>
</html>
