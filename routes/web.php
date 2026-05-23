<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('auth.login.admin');
});

Route::get('/lang/{locale}', function (string $locale) {
    $supported = ['id', 'en'];
    if (! in_array($locale, $supported, true)) {
        $locale = 'id';
    }

    session(['locale' => $locale]);

    return redirect()->back();
})->name('lang.switch');

Route::prefix('login')->group(function () {
    Route::get('/admin', function () {
        return view('auth.login', ['role' => 'admin']);
    })->name('auth.login.admin');

    Route::get('/karyawan', function () {
        return view('auth.login', ['role' => 'karyawan']);
    })->name('auth.login.karyawan');
});

Route::get('/logout', function () {
    return redirect()->route('auth.login.admin');
})->name('auth.logout');

Route::prefix('admin')->group(function () {
    Route::get('/dashboard', fn () => view('admin.dashboard'))->name('admin.dashboard');
    Route::get('/stok-bahan-baku', fn () => view('admin.stok'))->name('admin.stok');
    Route::get('/data-produksi', fn () => view('admin.produksi'))->name('admin.produksi');
    Route::get('/transaksi-penjualan', fn () => view('admin.penjualan'))->name('admin.penjualan');
    Route::get('/biaya-operasional', fn () => view('admin.operasional'))->name('admin.operasional');
    Route::get('/data-produk', fn () => view('admin.produk'))->name('admin.produk');

    Route::prefix('laporan')->group(function () {
        Route::get('/laba-rugi', fn () => view('admin.laba-rugi'))->name('admin.laba_rugi');
        Route::get('/laporan-penjualan', fn () => view('admin.laporan-penjualan'))->name('admin.laporan_penjualan');
        Route::get('/neraca', fn () => view('admin.neraca'))->name('admin.neraca');
    });

    Route::prefix('akuntansi')->group(function () {
        Route::get('/coa', fn () => view('admin.coa'))->name('admin.coa');
        Route::get('/jurnal-umum', fn () => view('admin.jurnal-umum'))->name('admin.jurnal');
        Route::get('/general-ledger', fn () => view('admin.general-ledger'))->name('admin.gl');
        Route::get('/trial-balance', fn () => view('admin.trial-balance'))->name('admin.tb');
    });
});

Route::prefix('karyawan')->group(function () {
    Route::get('/dashboard', fn () => view('karyawan.dashboard'))->name('karyawan.dashboard');
    Route::get('/input-produksi', fn () => view('karyawan.produksi'))->name('karyawan.produksi');
    Route::get('/input-penjualan', fn () => view('karyawan.penjualan'))->name('karyawan.penjualan');
    Route::get('/input-persediaan', fn () => view('karyawan.persediaan'))->name('karyawan.persediaan');
    Route::get('/input-operasional', fn () => view('karyawan.operasional'))->name('karyawan.operasional');
    Route::get('/data-produk', fn () => view('karyawan.produk'))->name('karyawan.produk');
});

Route::prefix('basket')->group(function () {
    Route::get('/dashboard', fn () => view('basket.dashboard'))->name('basket.dashboard');
});
