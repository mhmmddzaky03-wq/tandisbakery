@props([
    'role' => 'admin',
    'user' => null,
    'lowStockMaterials' => collect(),
])

@php
    use App\Support\FormatHelper;

    $roleLabel = match ($role) {
        'admin' => 'Administrator',
        'karyawan' => 'Karyawan',
        default => ucfirst($role),
    };

    $hasLowStock = $role === 'admin' && $lowStockMaterials->isNotEmpty();
    $lowStockCount = $lowStockMaterials->count();
@endphp

<div class="flex w-full min-w-0 items-center gap-3 sm:gap-4">
    <div class="flex min-w-0 flex-1 items-center gap-2 rounded-2xl bg-slate-50 px-3 py-2 ring-1 ring-slate-200/80 transition focus-within:bg-white focus-within:ring-2 focus-within:ring-amber-400/70 sm:gap-3 sm:px-4 sm:py-2.5 sm:max-w-xl lg:max-w-2xl">
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
        @if ($role === 'admin')
            <div class="relative" data-dropdown>
                <button
                    type="button"
                    class="bakery-icon-btn relative h-10 w-10"
                    data-dropdown-button
                    aria-label="{{ $hasLowStock ? 'Notifikasi: '.$lowStockCount.' bahan baku perlu diisi' : 'Notifikasi stok bahan baku' }}"
                    aria-expanded="false"
                >
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17H9a4 4 0 0 0 6 0Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 7h18s-3 0-3-7Z" />
                    </svg>
                    @if ($hasLowStock)
                        <span
                            class="absolute right-2 top-2 h-2 w-2 rounded-full bg-rose-500 ring-2 ring-white"
                            aria-hidden="true"
                        ></span>
                    @endif
                </button>
                <div class="absolute right-0 z-50 mt-2 hidden w-[min(100vw-2rem,320px)] rounded-2xl bg-white p-3 shadow-lg ring-1 ring-black/10" data-dropdown-menu>
                    <div class="flex items-center justify-between gap-2 border-b border-slate-100 pb-2.5">
                        <div class="text-xs font-bold text-slate-400">Notifikasi Stok</div>
                        @if ($hasLowStock)
                            <span class="rounded-full bg-rose-50 px-2 py-0.5 text-[10px] font-bold text-rose-600">{{ $lowStockCount }}</span>
                        @endif
                    </div>

                    @if ($hasLowStock)
                        <ul class="mt-2 max-h-72 space-y-1 overflow-y-auto">
                            @foreach ($lowStockMaterials as $material)
                                <li>
                                    <a
                                        href="{{ route('admin.stok.show', $material->id) }}"
                                        class="flex items-start gap-3 rounded-xl px-2.5 py-2.5 transition hover:bg-rose-50/70"
                                    >
                                        <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-rose-500" aria-hidden="true"></span>
                                        <span class="min-w-0 flex-1">
                                            <span class="block truncate text-sm font-semibold text-slate-800">{{ $material->nama }}</span>
                                            <span class="mt-0.5 block text-xs font-medium text-rose-600">
                                                {{ $material->stockStatusLabel() }}
                                                · {{ FormatHelper::formatQtyOne($material->jumlah) }} / {{ FormatHelper::formatQtyOne($material->min) }} {{ $material->satuan ?? 'kg' }}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <a
                            href="{{ route('admin.stok') }}"
                            class="mt-2 block rounded-xl bg-slate-50 px-3 py-2 text-center text-xs font-bold text-slate-600 transition hover:bg-slate-100"
                        >
                            Buka Stok Bahan Baku
                        </a>
                    @else
                        <p class="mt-3 text-sm font-semibold text-slate-600">Semua stok bahan baku aman.</p>
                    @endif
                </div>
            </div>
        @endif

        {{-- Pemilih bahasa (tampilan saja; aplikasi tetap Bahasa Indonesia) --}}
        <div class="relative" data-dropdown>
            <button
                type="button"
                class="bakery-icon-btn h-10 w-10"
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
