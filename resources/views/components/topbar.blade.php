@props([
    'role' => 'admin',
    'user' => null,
])

@php
    $roleLabel = match ($role) {
        'admin' => 'Administrator',
        'karyawan' => 'Karyawan',
        'basket' => 'Basket',
        default => ucfirst($role),
    };
@endphp

<div class="flex w-full min-w-0 items-center gap-3 sm:gap-4">
    <div class="flex min-w-0 flex-1 items-center gap-3 rounded-2xl bg-slate-50 px-4 py-2.5 ring-1 ring-black/5 sm:max-w-xl lg:max-w-2xl">
        <svg viewBox="0 0 24 24" class="h-5 w-5 shrink-0 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.2-4.2" />
        </svg>
        <input
            class="min-w-0 flex-1 bg-transparent text-sm text-slate-700 placeholder:text-slate-400 outline-none"
            placeholder="Cari..."
            aria-label="Cari..."
            data-global-search
        />
    </div>

    <div class="flex shrink-0 items-center gap-2 sm:gap-3">
        <div class="relative" data-dropdown>
            <button
                type="button"
                class="relative grid h-10 w-10 place-items-center rounded-xl bg-white text-slate-700 ring-1 ring-black/10 transition hover:bg-slate-50"
                data-dropdown-button
                aria-label="Notifikasi"
            >
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17H9a4 4 0 0 0 6 0Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 7h18s-3 0-3-7Z" />
                </svg>
                <span class="absolute right-2 top-2 h-2 w-2 rounded-full bg-rose-500 ring-2 ring-white" aria-hidden="true"></span>
            </button>
            <div class="absolute right-0 z-50 mt-2 hidden w-[260px] rounded-2xl bg-white p-3 shadow-lg ring-1 ring-black/10" data-dropdown-menu>
                <div class="text-xs font-bold text-slate-400">Notifikasi</div>
                <p class="mt-2 text-sm font-semibold text-slate-600">Tidak ada notifikasi baru.</p>
            </div>
        </div>

        {{-- Pemilih bahasa (tampilan saja; aplikasi tetap Bahasa Indonesia) --}}
        <div class="relative" data-dropdown>
            <button
                type="button"
                class="grid h-10 w-10 place-items-center rounded-xl bg-white ring-1 ring-black/10 transition hover:bg-slate-50"
                data-dropdown-button
                aria-label="Bahasa Indonesia"
            >
                <x-icons.flag-id class="h-4 w-6 rounded-sm ring-1 ring-black/10" />
            </button>
            <div
                class="absolute right-0 z-50 mt-2 hidden w-[72px] rounded-2xl bg-white p-2 shadow-lg ring-1 ring-black/10"
                data-dropdown-menu
            >
                <button
                    type="button"
                    class="flex w-full items-center justify-center rounded-xl px-3 py-2 bg-amber-50 ring-1 ring-amber-200"
                    data-lang-picker-active
                    aria-label="Bahasa Indonesia"
                    title="Bahasa Indonesia"
                >
                    <x-icons.flag-id class="h-4 w-6 rounded-sm ring-1 ring-black/10" />
                </button>
                <button
                    type="button"
                    class="mt-1 flex w-full items-center justify-center rounded-xl px-3 py-2 hover:bg-slate-50"
                    data-lang-picker-inactive
                    aria-label="English"
                    title="English (belum tersedia)"
                >
                    <x-icons.flag-us class="h-4 w-6 rounded-sm ring-1 ring-black/10" />
                </button>
            </div>
        </div>

        <div class="relative" data-dropdown>
            <button
                type="button"
                class="flex items-center gap-2.5 rounded-2xl bg-white py-1.5 pl-1.5 pr-2.5 ring-1 ring-black/10 transition hover:bg-slate-50 sm:gap-3 sm:pr-3"
                data-dropdown-button
            >
                <div class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-amber-100 text-amber-700">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" />
                    </svg>
                </div>
                <div class="hidden text-left leading-tight sm:block">
                    <div class="text-sm font-bold text-slate-800">{{ $user?->name ?? 'User' }}</div>
                    <div class="text-xs font-semibold text-slate-400">{{ $roleLabel }}</div>
                </div>
                <svg viewBox="0 0 24 24" class="hidden h-4 w-4 shrink-0 text-slate-500 sm:block" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                </svg>
            </button>

            <div class="absolute right-0 z-50 mt-2 hidden w-[220px] rounded-2xl bg-white p-2 shadow-lg ring-1 ring-black/10" data-dropdown-menu>
                @if (! config('app.auth_enabled'))
                    <div class="px-3 py-2 text-[11px] font-semibold uppercase tracking-wide text-amber-600">
                        Ganti role
                    </div>
                    <a href="{{ route('admin.dashboard') }}" class="block rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 {{ ($role ?? '') === 'admin' ? 'bg-amber-50 text-amber-800' : '' }}">
                        Admin
                    </a>
                    <a href="{{ route('karyawan.dashboard') }}" class="block rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 {{ ($role ?? '') === 'karyawan' ? 'bg-amber-50 text-amber-800' : '' }}">
                        Karyawan
                    </a>
                    <a href="{{ route('basket.dashboard') }}" class="block rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 {{ ($role ?? '') === 'basket' ? 'bg-amber-50 text-amber-800' : '' }}">
                        Basket
                    </a>
                @else
                    <form method="POST" action="{{ route('auth.logout') }}">
                        @csrf
                        <button type="submit" class="w-full rounded-xl px-3 py-2 text-left text-sm font-semibold text-rose-600 hover:bg-rose-50">
                            Keluar
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
