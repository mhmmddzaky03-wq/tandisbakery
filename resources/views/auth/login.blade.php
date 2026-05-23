@extends('layouts.auth')

@php
    $title = 'Login - Tandi\'s Bakery';
    // Kita paksa definisikan di sini supaya tidak mungkin Undefined
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
                <div class="mt-6 space-y-4">
                    <input class="bakery-input w-full p-3 border rounded-xl" placeholder="Username" />
                    <input class="bakery-input w-full p-3 border rounded-xl" type="password" placeholder="Password" />
                    <a href="{{ $role === 'karyawan' ? route('karyawan.dashboard') : route('admin.dashboard') }}" class="block w-full bg-slate-900 text-white text-center py-3 rounded-xl font-bold">
                        Login
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection