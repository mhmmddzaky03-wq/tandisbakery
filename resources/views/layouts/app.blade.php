<!doctype html>
<html lang="id">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>{{ $title ?? 'Tandi\'s Bakery' }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700" rel="stylesheet" />

        <meta name="dummy-toast" content="Fitur ini belum diaktifkan" />
        <meta name="dummy-toast-sub" content="Tugas Backend ah" />

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

            <div class="flex min-h-screen flex-col lg:pl-[280px]">
                <header class="sticky top-0 z-30 border-b border-slate-100 bg-white shadow-sm">
                    <div class="flex items-center gap-3 px-4 py-3 sm:px-6 lg:py-3.5">
                        <button
                            type="button"
                            class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-slate-700 ring-1 ring-black/10 lg:hidden"
                            data-sidebar-toggle
                            aria-label="Buka menu"
                        >
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <x-topbar :role="$role ?? 'admin'" :user="auth()->user()" />
                    </div>
                </header>

                <main class="mx-auto w-full max-w-[1400px] flex-1 px-4 py-6 sm:px-6 sm:py-8">
                    <x-flash />

                    @if (! empty($pageTitle) && ! ($hidePageHeader ?? false))
                        <x-page-header :title="$pageTitle" :subtitle="$pageSubtitle ?? null">
                            <x-slot:actions>
                                @stack('page-actions')
                            </x-slot:actions>
                        </x-page-header>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>

        <div id="toast-root" class="pointer-events-none fixed right-6 top-6 z-[200] flex w-[360px] max-w-[92vw] flex-col gap-2"></div>
    </body>
</html>
