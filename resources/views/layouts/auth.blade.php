<!doctype html>
<html lang="id">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>{{ $title ?? 'Tandi\'s Homemade Bakery' }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700" rel="stylesheet" />

        <meta name="dummy-toast" content="Fitur ini belum diaktifkan" />
        <meta name="dummy-toast-sub" content="Tugas Backend ah" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[color:var(--color-bakery-bg)] font-[family-name:var(--font-sans)] text-slate-800 antialiased">
        <div class="relative min-h-screen overflow-hidden">
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_15%_20%,rgba(245,158,11,0.14)_0%,transparent_40%),radial-gradient(circle_at_85%_10%,rgba(251,191,36,0.10)_0%,transparent_35%),radial-gradient(circle_at_50%_90%,rgba(120,113,108,0.06)_0%,transparent_45%)]"></div>
            </div>

            <main class="relative flex min-h-screen w-full items-center justify-center px-4 py-8 sm:px-6 sm:py-12">
                @yield('content')
            </main>
        </div>

        <div id="toast-root" class="pointer-events-none fixed right-6 top-6 z-[100] flex w-[360px] max-w-[92vw] flex-col gap-2"></div>
    </body>
</html>
