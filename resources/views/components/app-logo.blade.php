@props([
    'variant' => 'default', // default | sidebar | auth | pdf
])

@php
    [$sizeClass, $alignClass] = match ($variant) {
        'sidebar' => ['h-10 w-auto max-w-[168px]', 'object-left'],
        'auth' => ['h-16 w-auto max-w-[240px]', 'object-center'],
        'auth-panel' => ['h-28 w-auto max-w-[340px]', 'object-center'],
        'pdf' => ['h-12 w-auto max-w-[200px]', 'object-left'],
        default => ['h-11 w-auto max-w-[180px]', 'object-left'],
    };
@endphp

@php
    $logoHref = auth()->check()
        ? (auth()->user()->role === 'karyawan' ? route('karyawan.dashboard') : route('admin.dashboard'))
        : route('auth.login.admin');
@endphp

<a href="{{ $logoHref }}" class="inline-flex shrink-0 items-center" aria-label="Tadi's Homemade Bakery">
    <img
        src="{{ asset('images/tandis-logo.png') }}"
        alt="Tadi's Homemade Bakery"
        class="{{ $sizeClass }} object-contain {{ $alignClass }}"
        width="280"
        height="80"
        decoding="async"
    />
</a>
