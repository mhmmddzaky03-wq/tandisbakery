<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
        <title>{{ $title ?? 'Tandi\'s Bakery' }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700" rel="stylesheet" />

        <meta name="dummy-toast" content="Fitur ini belum diaktifkan" />
        <meta name="dummy-toast-sub" content="Tugas Backend ah" />

        <meta name="toast-default-success" content="{{ __('app.flash.success') }}" />
        <meta name="toast-default-error" content="{{ __('app.flash.error') }}" />
        <meta name="i18n-confirm-delete" content="{{ __('js.confirm_delete') }}" />
        <meta name="i18n-confirm-delete-production" content="{{ __('js.confirm_delete_production') }}" />
        <meta name="i18n-cannot-delete" content="{{ __('js.cannot_delete') }}" />
        <meta name="i18n-cannot-delete-linked" content="{{ __('js.cannot_delete_linked_product') }}" />
        <meta name="i18n-select-stock-batch" content="{{ __('js.select_stock_batch') }}" />
        <meta name="i18n-select-base-material" content="{{ __('js.select_base_material') }}" />
        <meta name="i18n-select-dough-batch" content="{{ __('js.select_dough_batch') }}" />
        <meta name="i18n-coa-subgroup" content="{{ __('js.coa_select_subgroup') }}" />
        <meta name="i18n-optional" content="{{ __('app.common.optional') }}" />
        <meta name="i18n-required" content="{{ __('app.common.required') }}" />
        <meta name="i18n-materials-hint-optional" content="{{ __('js.materials_hint_optional') }}" />
        <meta name="i18n-materials-hint-required" content="{{ __('js.materials_hint_required') }}" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[color:var(--color-bakery-bg)] text-slate-800 antialiased bakery-scroll-root">
        <div class="min-h-screen w-full max-w-full">
            <div
                class="fixed inset-0 z-40 hidden bg-slate-900/40 backdrop-blur-[2px] transition-opacity lg:hidden"
                data-sidebar-overlay
                aria-hidden="true"
            ></div>

            <aside
                class="fixed inset-y-0 left-0 z-50 flex w-[min(280px,88vw)] -translate-x-full flex-col bg-white shadow-xl ring-1 ring-slate-200/80 transition-transform duration-300 ease-out lg:w-[280px] lg:translate-x-0"
                data-sidebar
                aria-label="{{ __('app.common.main_nav') }}"
            >
                <div class="flex shrink-0 items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                    <x-app-logo variant="sidebar" />
                    <button
                        type="button"
                        class="bakery-icon-btn lg:hidden"
                        data-sidebar-close
                        aria-label="{{ __('app.common.close_menu') }}"
                    >
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" d="M6 6l12 12M18 6 6 18" />
                        </svg>
                    </button>
                </div>

                <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain px-3 py-4 bakery-scroll-y">
                    <x-sidebar :role="$role ?? 'admin'" :active="$active ?? ''" />
                </div>
            </aside>

            <div class="flex min-h-screen flex-col lg:pl-[280px]">
                <header class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/95 shadow-sm backdrop-blur-md supports-[backdrop-filter]:bg-white/90">
                    <div class="flex items-center gap-2 px-3 py-2.5 sm:gap-3 sm:px-6 sm:py-3">
                        <button
                            type="button"
                            class="bakery-icon-btn lg:hidden"
                            data-sidebar-toggle
                            aria-label="{{ __('app.common.open_menu') }}"
                            aria-expanded="false"
                        >
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <x-topbar :role="$role ?? 'admin'" :user="auth()->user()" />
                    </div>
                </header>

                <main class="mx-auto w-full max-w-[1400px] flex-1 px-3 py-5 sm:px-6 sm:py-8">
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

        <div id="toast-root" class="pointer-events-none fixed right-3 top-3 z-[200] flex w-[min(360px,calc(100vw-1.5rem))] flex-col gap-2 sm:right-6 sm:top-6"></div>
    </body>
</html>
