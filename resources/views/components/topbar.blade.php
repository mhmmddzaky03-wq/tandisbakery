@props([
    'role' => 'admin',
])

<div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
    <div class="hidden sm:flex items-center gap-3 rounded-2xl bg-white px-4 py-2.5 ring-1 ring-black/5 flex-1 min-w-0 max-w-[340px]">
        <svg viewBox="0 0 24 24" class="h-5 w-5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.2-4.2" />
        </svg>
        <input
            class="flex-1 min-w-0 bg-transparent text-sm text-slate-700 placeholder:text-slate-400 outline-none"
            placeholder="{{ __('ui.search') }}"
            aria-label="Search"
        />
    </div>

    <div class="relative" data-dropdown>
        <button
            type="button"
            class="grid h-10 w-10 place-items-center rounded-xl bg-white ring-1 ring-black/10"
            data-dropdown-button
            aria-label="Bahasa"
        >
            <div class="flex items-center gap-1 text-xs font-extrabold text-slate-700">
                <span>{{ strtoupper(app()->getLocale()) }}</span>
            </div>
        </button>
        <div
            class="absolute right-0 mt-2 hidden w-[200px] rounded-2xl bg-white p-2 shadow-lg ring-1 ring-black/10"
            data-dropdown-menu
        >
            <a class="block rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50" href="{{ route('lang.switch', ['locale' => 'id']) }}">
                Indonesia (ID)
            </a>
            <a class="block rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50" href="{{ route('lang.switch', ['locale' => 'en']) }}">
                English (EN)
            </a>
        </div>
    </div>

    <button type="button" class="grid h-10 w-10 place-items-center rounded-xl bg-white ring-1 ring-black/10" data-dummy>
        <svg viewBox="0 0 24 24" class="h-5 w-5 text-slate-700" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17H9a4 4 0 0 0 6 0Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 7h18s-3 0-3-7Z" />
        </svg>
    </button>

    <div class="relative" data-dropdown>
        <button
            type="button"
            class="flex items-center gap-3 rounded-2xl bg-white px-3 py-2.5 ring-1 ring-black/5"
            data-dropdown-button
        >
            <div class="grid h-9 w-9 place-items-center rounded-xl bg-amber-100 text-amber-700">
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" />
                </svg>
            </div>
            <div class="hidden sm:block text-left leading-tight">
                <div class="text-sm font-semibold text-slate-800">{{ $role === 'karyawan' ? 'Karyawan Toko' : ($role === 'basket' ? 'Basket' : 'Haris') }}</div>
                <div class="text-xs font-semibold text-slate-400">{{ $role === 'karyawan' ? 'Karyawan' : ($role === 'basket' ? 'Basket' : 'Administrator') }}</div>
            </div>
            <svg viewBox="0 0 24 24" class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
            </svg>
        </button>

        <div
            class="absolute right-0 mt-2 hidden w-[220px] rounded-2xl bg-white p-2 shadow-lg ring-1 ring-black/10"
            data-dropdown-menu
        >
            <a class="block rounded-xl px-3 py-2 text-sm font-semibold text-rose-600 hover:bg-rose-50" href="{{ route('auth.logout') }}">
                {{ __('ui.logout') }}
            </a>
        </div>
    </div>
</div>

