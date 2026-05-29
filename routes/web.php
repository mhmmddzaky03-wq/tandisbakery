<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\OperationalCostController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesTransactionController;
use App\Http\Controllers\UnitController;
use Illuminate\Support\Facades\Route;

$access = static function (string $role): array {
    return config('app.auth_enabled')
        ? ['auth', "role:{$role}"]
        : ["role:{$role}"];
};

Route::get('/', function () {
    if (! config('app.auth_enabled')) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('auth.login.admin');
});

Route::get('/login', function () {
    if (! config('app.auth_enabled')) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('auth.login.admin');
})->name('login');

Route::prefix('login')->group(function () {
    Route::get('/admin', fn () => app(AuthController::class)->showLogin('admin'))->name('auth.login.admin');
    Route::get('/karyawan', fn () => app(AuthController::class)->showLogin('karyawan'))->name('auth.login.karyawan');
    Route::get('/basket', fn () => app(AuthController::class)->showLogin('basket'))->name('auth.login.basket');
    Route::post('/', [AuthController::class, 'login'])->name('auth.login.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware(config('app.auth_enabled') ? 'auth' : [])
    ->name('auth.logout');

Route::middleware($access('admin'))->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');

    Route::get('/stok-bahan-baku', [RawMaterialController::class, 'index'])->name('admin.stok');
    Route::post('/stok-bahan-baku', [RawMaterialController::class, 'store'])->name('admin.stok.store');
    Route::put('/stok-bahan-baku/{id}', [RawMaterialController::class, 'update'])->name('admin.stok.update');
    Route::post('/stok-bahan-baku/{id}/restock', [RawMaterialController::class, 'restock'])->name('admin.stok.restock');
    Route::delete('/stok-bahan-baku/{id}', [RawMaterialController::class, 'destroy'])->name('admin.stok.destroy');

    Route::post('/satuan', [UnitController::class, 'store'])->name('admin.satuan.store');
    Route::put('/satuan/{id}', [UnitController::class, 'update'])->name('admin.satuan.update');
    Route::delete('/satuan/{id}', [UnitController::class, 'destroy'])->name('admin.satuan.destroy');

    Route::get('/data-produksi', [ProductionController::class, 'index'])->name('admin.produksi');
    Route::post('/data-produksi', [ProductionController::class, 'store'])->name('admin.produksi.store');
    Route::put('/data-produksi/{id}', [ProductionController::class, 'update'])->name('admin.produksi.update');
    Route::delete('/data-produksi/{id}', [ProductionController::class, 'destroy'])->name('admin.produksi.destroy');

    Route::get('/transaksi-penjualan', [SalesTransactionController::class, 'index'])->name('admin.penjualan');
    Route::post('/transaksi-penjualan', [SalesTransactionController::class, 'store'])->name('admin.penjualan.store');
    Route::put('/transaksi-penjualan/{id}', [SalesTransactionController::class, 'update'])->name('admin.penjualan.update');
    Route::delete('/transaksi-penjualan/{id}', [SalesTransactionController::class, 'destroy'])->name('admin.penjualan.destroy');

    Route::get('/biaya-operasional', [OperationalCostController::class, 'index'])->name('admin.operasional');
    Route::post('/biaya-operasional', [OperationalCostController::class, 'store'])->name('admin.operasional.store');
    Route::put('/biaya-operasional/{id}', [OperationalCostController::class, 'update'])->name('admin.operasional.update');
    Route::delete('/biaya-operasional/{id}', [OperationalCostController::class, 'destroy'])->name('admin.operasional.destroy');

    Route::post('/kategori-biaya', [ExpenseCategoryController::class, 'store'])->name('admin.kategori_biaya.store');
    Route::put('/kategori-biaya/{id}', [ExpenseCategoryController::class, 'update'])->name('admin.kategori_biaya.update');
    Route::delete('/kategori-biaya/{id}', [ExpenseCategoryController::class, 'destroy'])->name('admin.kategori_biaya.destroy');

    Route::get('/data-produk', [ProductController::class, 'index'])->name('admin.produk');
    Route::post('/data-produk', [ProductController::class, 'store'])->name('admin.produk.store');
    Route::put('/data-produk/{id}', [ProductController::class, 'update'])->name('admin.produk.update');
    Route::delete('/data-produk/{id}', [ProductController::class, 'destroy'])->name('admin.produk.destroy');

    Route::prefix('laporan')->group(function () {
        Route::get('/laba-rugi', [ReportController::class, 'incomeStatement'])->name('admin.laba_rugi');
        Route::get('/laporan-penjualan', [ReportController::class, 'salesReport'])->name('admin.laporan_penjualan');
        Route::get('/neraca', [ReportController::class, 'balanceSheet'])->name('admin.neraca');
    });

    Route::prefix('akuntansi')->group(function () {
        Route::get('/coa', [AccountController::class, 'index'])->name('admin.coa');
        Route::post('/coa', [AccountController::class, 'store'])->name('admin.coa.store');
        Route::put('/coa/{kode}', [AccountController::class, 'update'])->name('admin.coa.update');
        Route::delete('/coa/{kode}', [AccountController::class, 'destroy'])->name('admin.coa.destroy');

        Route::get('/jurnal-umum', [JournalController::class, 'index'])->name('admin.jurnal');
        Route::post('/jurnal-umum', [JournalController::class, 'store'])->name('admin.jurnal.store');
        Route::delete('/jurnal-umum/{id}', [JournalController::class, 'destroy'])->name('admin.jurnal.destroy');

        Route::get('/general-ledger', [ReportController::class, 'generalLedger'])->name('admin.gl');
        Route::get('/trial-balance', [ReportController::class, 'trialBalance'])->name('admin.tb');
    });
});

Route::middleware($access('karyawan'))->prefix('karyawan')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'karyawan'])->name('karyawan.dashboard');

    Route::get('/input-produksi', [ProductionController::class, 'index'])->name('karyawan.produksi');
    Route::post('/input-produksi', [ProductionController::class, 'store'])->name('karyawan.produksi.store');

    Route::get('/input-penjualan', [SalesTransactionController::class, 'index'])->name('karyawan.penjualan');
    Route::post('/input-penjualan', [SalesTransactionController::class, 'store'])->name('karyawan.penjualan.store');
    Route::put('/input-penjualan/{id}', [SalesTransactionController::class, 'update'])->name('karyawan.penjualan.update');
    Route::delete('/input-penjualan/{id}', [SalesTransactionController::class, 'destroy'])->name('karyawan.penjualan.destroy');

    Route::get('/input-persediaan', [RawMaterialController::class, 'index'])->name('karyawan.persediaan');
    Route::post('/input-persediaan', [RawMaterialController::class, 'store'])->name('karyawan.persediaan.store');
    Route::put('/input-persediaan/{id}', [RawMaterialController::class, 'update'])->name('karyawan.persediaan.update');
    Route::post('/input-persediaan/{id}/restock', [RawMaterialController::class, 'restock'])->name('karyawan.persediaan.restock');
    Route::delete('/input-persediaan/{id}', [RawMaterialController::class, 'destroy'])->name('karyawan.persediaan.destroy');

    Route::post('/satuan', [UnitController::class, 'store'])->name('karyawan.satuan.store');
    Route::put('/satuan/{id}', [UnitController::class, 'update'])->name('karyawan.satuan.update');
    Route::delete('/satuan/{id}', [UnitController::class, 'destroy'])->name('karyawan.satuan.destroy');

    Route::get('/input-operasional', [OperationalCostController::class, 'index'])->name('karyawan.operasional');
    Route::post('/input-operasional', [OperationalCostController::class, 'store'])->name('karyawan.operasional.store');
    Route::put('/input-operasional/{id}', [OperationalCostController::class, 'update'])->name('karyawan.operasional.update');
    Route::delete('/input-operasional/{id}', [OperationalCostController::class, 'destroy'])->name('karyawan.operasional.destroy');

    Route::get('/data-produk', [ProductController::class, 'index'])->name('karyawan.produk');
});

Route::middleware($access('basket'))->prefix('basket')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'basket'])->name('basket.dashboard');
});
