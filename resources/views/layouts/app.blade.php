<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>{{ $title ?? 'Tandi\'s Bakery' }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700" rel="stylesheet" />

        <meta name="dummy-toast" content="{{ __('ui.feature_in_progress') }}" />
        <meta name="dummy-toast-sub" content="{{ __('ui.still_on_progress') }}" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[color:var(--color-bakery-bg)] text-slate-800 bakery-scroll-root">
        <div class="min-h-screen w-full max-w-full">
            <div class="fixed inset-0 z-40 hidden bg-black/30 lg:hidden" data-sidebar-overlay></div>

            <aside
                class="fixed inset-y-0 left-0 z-50 w-[280px] -translate-x-full bg-white shadow-sm ring-1 ring-black/5 transition-transform lg:translate-x-0"
                data-sidebar
            >
                <div class="flex h-full flex-col overflow-y-auto">
                    <div class="flex items-center gap-3 px-6 py-5">
                        <x-app-logo />
                    </div>

                    <div class="px-3 pb-4">
                        <x-sidebar :role="$role ?? 'admin'" :active="$active ?? ''" />
                    </div>
                </div>
            </aside>

            <div class="lg:pl-[280px] w-full max-w-full overflow-x-hidden">
                <header class="sticky top-0 z-30 bg-[color:var(--color-bakery-bg)]/80 backdrop-blur">
                    <div class="mx-auto flex max-w-[1400px] items-center justify-between gap-4 px-4 sm:px-6 py-4">
                        <div class="flex items-center gap-3">
                            <button
                                type="button"
                                class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-white ring-1 ring-black/10 lg:hidden"
                                data-sidebar-toggle
                                aria-label="Buka menu"
                            >
                                <svg viewBox="0 0 24 24" class="h-5 w-5 text-slate-700" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>

                            <div class="hidden sm:block">
                                <div class="text-xs font-semibold text-slate-400">{{ $subtitle ?? '' }}</div>
                                <div class="text-lg font-semibold text-slate-800">{{ $pageTitle ?? '' }}</div>
                            </div>
                        </div>

                        <x-topbar :role="$role ?? 'admin'" :user="auth()->user()" />
                    </div>
                </header>

                <main class="mx-auto max-w-[1400px] px-4 sm:px-6 pb-10 w-full max-w-full overflow-x-hidden">
                    <x-flash />
                    @yield('content')
                </main>
            </div>
        </div>

        <div id="toast-root" class="pointer-events-none fixed right-6 top-6 z-[100] flex w-[360px] max-w-[92vw] flex-col gap-2"></div>
    </body>
</html>

