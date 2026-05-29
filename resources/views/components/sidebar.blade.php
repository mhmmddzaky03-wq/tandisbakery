@props([
    'role' => 'admin',
    'active' => '',
])

@php
    $isAdmin = $role === 'admin';
    $isKaryawan = $role === 'karyawan';
    $isBasket = $role === 'basket';

    $icon = fn ($d) => '<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="'.$d.'" /></svg>';

    $sectionTitle = fn ($t) => '<div class="px-3 pt-5 pb-2 text-[10px] font-bold tracking-[0.22em] text-slate-400">'.$t.'</div>';
@endphp

<nav class="space-y-2">
    {!! $sectionTitle('MENU UTAMA') !!}

    @if ($isAdmin)
        <x-nav-link href="{{ route('admin.dashboard') }}" :active="$active === 'admin.dashboard'" :icon="$icon('M4 10.5 12 4l8 6.5V20a1 1 0 0 1-1 1h-5v-6H10v6H5a1 1 0 0 1-1-1v-9.5Z')">Dashboard</x-nav-link>
        <x-nav-link href="{{ route('admin.stok') }}" :active="$active === 'admin.stok'" :icon="$icon('M7 7h10M7 12h10M7 17h10M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z')">Stok Bahan Baku</x-nav-link>
        <x-nav-link href="{{ route('admin.produksi') }}" :active="$active === 'admin.produksi'" :icon="$icon('M8 7h8M6 21h12a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-1l-1-2H8L7 7H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2Z')">Data Produksi</x-nav-link>
        <x-nav-link href="{{ route('admin.penjualan') }}" :active="$active === 'admin.penjualan'" :icon="$icon('M4 7h16M4 11h16M8 15h4M6 19h12')">Transaksi Penjualan</x-nav-link>
        <x-nav-link href="{{ route('admin.operasional') }}" :active="$active === 'admin.operasional'" :icon="$icon('M12 3v18M17 8l-5-5-5 5M7 16l5 5 5-5')">Biaya Operasional</x-nav-link>
        <x-nav-link href="{{ route('admin.produk') }}" :active="$active === 'admin.produk'" :icon="$icon('M7 4h10l3 6-8 10L4 10l3-6Z')">Data Produk</x-nav-link>

        {!! $sectionTitle('LAPORAN KEUANGAN') !!}
        <x-nav-link href="{{ route('admin.laba_rugi') }}" :active="$active === 'admin.laba_rugi'" :icon="$icon('M4 18V6m0 12h16M8 14l3-3 3 3 5-5')">Laba Rugi</x-nav-link>
        <x-nav-link href="{{ route('admin.laporan_penjualan') }}" :active="$active === 'admin.laporan_penjualan'" :icon="$icon('M6 2h12v20H6zM9 6h6M9 10h6M9 14h6')">Laporan Penjualan</x-nav-link>
        <x-nav-link href="{{ route('admin.neraca') }}" :active="$active === 'admin.neraca'" :icon="$icon('M7 7h10M7 12h6M7 17h10M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z')">Neraca Keuangan</x-nav-link>

        {!! $sectionTitle('AKUNTANSI') !!}
        <x-nav-link href="{{ route('admin.coa') }}" :active="$active === 'admin.coa'" :icon="$icon('M4 6h16M6 10h12M6 14h12M6 18h12')">Chart of Accounts</x-nav-link>
        <x-nav-link href="{{ route('admin.jurnal') }}" :active="$active === 'admin.jurnal'" :icon="$icon('M7 3h10v18H7zM9 7h6M9 11h6M9 15h6')">Jurnal Umum</x-nav-link>
        <x-nav-link href="{{ route('admin.gl') }}" :active="$active === 'admin.gl'" :icon="$icon('M4 7h16M6 11h12M6 15h8M6 19h12')">General Ledger</x-nav-link>
        <x-nav-link href="{{ route('admin.tb') }}" :active="$active === 'admin.tb'" :icon="$icon('M4 19h16M6 17V7m6 10V7m6 10V7')">Trial Balance</x-nav-link>
    @endif

    @if ($isKaryawan)
        <x-nav-link href="{{ route('karyawan.dashboard') }}" :active="$active === 'karyawan.dashboard'" :icon="$icon('M4 10.5 12 4l8 6.5V20a1 1 0 0 1-1 1h-5v-6H10v6H5a1 1 0 0 1-1-1v-9.5Z')">Dashboard</x-nav-link>
        <x-nav-link href="{{ route('karyawan.produksi') }}" :active="$active === 'karyawan.produksi'" :icon="$icon('M6 21h12a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-1l-1-2H8L7 7H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2Z')">Input Data Produksi</x-nav-link>
        <x-nav-link href="{{ route('karyawan.penjualan') }}" :active="$active === 'karyawan.penjualan'" :icon="$icon('M4 7h16M4 11h16M8 15h4M6 19h12')">Input Data Penjualan</x-nav-link>
        <x-nav-link href="{{ route('karyawan.persediaan') }}" :active="$active === 'karyawan.persediaan'" :icon="$icon('M7 7h10M7 12h10M7 17h10M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z')">Input Data Persediaan</x-nav-link>
        <x-nav-link href="{{ route('karyawan.operasional') }}" :active="$active === 'karyawan.operasional'" :icon="$icon('M12 3v18M17 8l-5-5-5 5M7 16l5 5 5-5')">Input Data Operasional</x-nav-link>
        <x-nav-link href="{{ route('karyawan.produk') }}" :active="$active === 'karyawan.produk'" :icon="$icon('M7 4h10l3 6-8 10L4 10l3-6Z')">Data Produk</x-nav-link>
    @endif

    @if ($isBasket)
        <x-nav-link href="{{ route('basket.dashboard') }}" :active="$active === 'basket.dashboard'" :icon="$icon('M4 10.5 12 4l8 6.5V20a1 1 0 0 1-1 1h-5v-6H10v6H5a1 1 0 0 1-1-1v-9.5Z')">Dashboard Basket</x-nav-link>
    @endif
</nav>

