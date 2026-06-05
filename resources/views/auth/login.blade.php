@extends('layouts.auth')

@php
    $title = 'Login - Tandi\'s Homemade Bakery';
    $tabs = [
        ['key' => 'admin', 'label' => 'Admin', 'href' => route('auth.login.admin')],
        ['key' => 'karyawan', 'label' => 'Karyawan', 'href' => route('auth.login.karyawan')],
    ];
    $role = $role ?? 'admin';
@endphp

@section('content')
    <div class="mx-auto w-full max-w-6xl">
        <div class="grid items-center gap-10 lg:grid-cols-2 lg:gap-16 xl:gap-20">
            {{-- Kiri: logo & judul (di luar card) --}}
            <div class="flex flex-col items-center text-center lg:items-start lg:text-left">
                <div class="flex justify-center lg:justify-start">
                    <x-app-logo variant="auth-panel" />
                </div>

                <h1 class="mt-8 max-w-md text-3xl font-bold leading-[1.15] tracking-tight text-slate-900 sm:text-4xl lg:text-[2.35rem]">
                    Sistem Keuangan Digital
                    <span class="mt-2 block text-amber-700">Tandi's Bakery Homemade</span>
                </h1>

                <p class="mt-5 max-w-sm text-sm leading-relaxed text-slate-500 sm:text-[15px]">
                    Kelola produksi, penjualan, dan laporan keuangan bakery dalam satu sistem terpadu.
                </p>
            </div>

            {{-- Kanan: form dalam card --}}
            <div class="bakery-card mx-auto w-full max-w-[440px] px-7 py-8 sm:px-9 sm:py-10 lg:mx-0 lg:ml-auto lg:max-w-[460px]">
                <div class="mb-7">
                    <h2 class="text-xl font-bold text-slate-900">Masuk ke akun</h2>
                    <p class="mt-1.5 text-sm text-slate-500">Pilih peran dan masukkan kredensial Anda.</p>
                </div>

                <div class="rounded-2xl bg-slate-100/90 p-1 ring-1 ring-slate-200/60">
                    <div class="grid grid-cols-2 gap-1">
                        @foreach ($tabs as $t)
                            <a
                                href="{{ $t['href'] }}"
                                class="{{ $role === $t['key']
                                    ? 'bg-white text-slate-900 shadow-sm ring-1 ring-slate-200/80'
                                    : 'text-slate-500 hover:text-slate-700' }} flex items-center justify-center rounded-xl px-3 py-2.5 text-sm font-semibold transition"
                            >
                                {{ $t['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <form class="mt-7 space-y-5" method="POST" action="{{ route('auth.login.submit') }}">
                    @csrf
                    <input type="hidden" name="role" value="{{ $role }}" />

                    <div>
                        <label for="username" class="mb-2 block text-[11px] font-bold uppercase tracking-[0.14em] text-slate-400">
                            Username
                        </label>
                        <input
                            id="username"
                            class="bakery-input h-12 @error('username') bakery-input--error @enderror"
                            name="username"
                            value="{{ old('username') }}"
                            placeholder="Masukkan username"
                            required
                            autocomplete="username"
                        />
                        @error('username')
                            <p class="mt-1.5 text-xs font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-[11px] font-bold uppercase tracking-[0.14em] text-slate-400">
                            Password
                        </label>
                        <input
                            id="password"
                            class="bakery-input h-12 @error('password') bakery-input--error @enderror"
                            type="password"
                            name="password"
                            placeholder="Masukkan password"
                            required
                            autocomplete="current-password"
                        />
                        @error('password')
                            <p class="mt-1.5 text-xs font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="bakery-btn-dark mt-1"
                    >
                        Login
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
