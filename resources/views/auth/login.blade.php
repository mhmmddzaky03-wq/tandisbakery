@extends('layouts.auth')

@php
    $title = 'Login - Tandi\'s Bakery';
    $tabs = [
        ['key' => 'admin', 'label' => 'Admin', 'href' => route('auth.login.admin')],
        ['key' => 'karyawan', 'label' => 'Karyawan', 'href' => route('auth.login.karyawan')],
    ];
    $role = $role ?? 'admin';
@endphp

@section('content')
    <div class="w-full max-w-[980px]">
        <div class="grid gap-8 lg:grid-cols-2 lg:items-center">
            <div class="hidden lg:block">
                <div class="bakery-card p-8">
                    <x-app-logo />
                    <div class="mt-6">
                        <div class="text-3xl font-bold leading-tight text-slate-900">Sistem Keuangan Digital</div>
                        <div class="mt-3 text-sm font-semibold text-slate-500">Tandi's Bakery</div>
                    </div>
                </div>
            </div>

            <div class="bakery-card mx-auto w-full max-w-[460px] px-8 py-9">
                <div class="mx-auto flex justify-center"><x-app-logo /></div>
                <div class="mt-6 rounded-2xl bg-slate-100 p-1.5">
                    <div class="grid grid-cols-2 gap-1.5">
                        @foreach ($tabs as $t)
                            <a href="{{ $t['href'] }}" class="{{ $role === $t['key'] ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500' }} flex items-center justify-center rounded-xl px-3 py-2.5 text-sm font-bold transition">
                                <span>{{ $t['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <form class="mt-6 space-y-4" method="POST" action="{{ route('auth.login.submit') }}">
                    @csrf
                    <input type="hidden" name="role" value="{{ $role }}" />

                    <div>
                        <input
                            class="bakery-input w-full p-3 border rounded-xl @error('username') ring-2 ring-rose-300 @enderror"
                            name="username"
                            value="{{ old('username') }}"
                            placeholder="Username"
                            required
                            autocomplete="username"
                        />
                        @error('username')
                            <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <input
                            class="bakery-input w-full p-3 border rounded-xl @error('password') ring-2 ring-rose-300 @enderror"
                            type="password"
                            name="password"
                            placeholder="Password"
                            required
                            autocomplete="current-password"
                        />
                        @error('password')
                            <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="block w-full bg-slate-900 text-white text-center py-3 rounded-xl font-bold">
                        Login
                    </button>
                </form>

                @if (config('app.debug'))
                    <div class="mt-4 rounded-xl bg-amber-50 px-3 py-2.5 text-center text-[11px] leading-relaxed text-amber-900/80 ring-1 ring-amber-200/60">
                        <span class="font-bold">Akun demo:</span>
                        {{ $role === 'karyawan' ? 'karyawan / karyawan123' : 'admin / admin123' }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
