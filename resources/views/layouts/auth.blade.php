<!doctype html>
<html lang="id">
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
    <body class="min-h-screen bg-[color:var(--color-bakery-bg)] text-slate-800">
        <div class="relative min-h-screen overflow-hidden">
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_10%,rgba(245,158,11,0.18)_0%,rgba(245,158,11,0.0)_35%),radial-gradient(circle_at_80%_20%,rgba(245,158,11,0.16)_0%,rgba(245,158,11,0.0)_35%),radial-gradient(circle_at_50%_80%,rgba(245,158,11,0.10)_0%,rgba(245,158,11,0.0)_45%)]"></div>
                <div class="absolute inset-0 opacity-[0.15] [background-image:radial-gradient(circle_at_10%_30%,#f59e0b_0,transparent_24%),radial-gradient(circle_at_80%_70%,#f59e0b_0,transparent_26%)]"></div>
            </div>

            <main class="relative mx-auto flex min-h-screen max-w-[1100px] items-center justify-center px-6 py-12">
                @yield('content')
            </main>
        </div>

        <div id="toast-root" class="pointer-events-none fixed right-6 top-6 z-[100] flex w-[360px] max-w-[92vw] flex-col gap-2"></div>
    </body>
</html>

