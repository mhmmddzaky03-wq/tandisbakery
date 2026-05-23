@extends('layouts.auth')

@php $title = 'Login Basket'; $role = 'basket'; @endphp

@section('content')
    <div class="w-full max-w-md mx-auto bakery-card p-8">
        <x-app-logo />
        <form class="mt-6 space-y-4" method="POST" action="{{ route('auth.login.submit') }}">
            @csrf
            <input type="hidden" name="role" value="basket" />
            <input class="bakery-input w-full p-3 border rounded-xl" name="username" value="{{ old('username') }}" placeholder="Username" required />
            <input class="bakery-input w-full p-3 border rounded-xl" type="password" name="password" placeholder="Password" required />
            <button type="submit" class="w-full bg-slate-900 text-white py-3 rounded-xl font-bold">Login Basket</button>
        </form>
        <a href="{{ route('auth.login.admin') }}" class="mt-4 block text-center text-sm text-slate-500">← Kembali ke login admin</a>
    </div>
@endsection
