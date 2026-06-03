<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BahanDasarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\OperationalCostController;
use App\Http\Controllers\PdfReportController;
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
    Route::post('/', [AuthController::class, 'login'])->name('auth.login.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware(config('app.auth_enabled') ? 'auth' : [])
    ->name('auth.logout');

Route::middleware($access('admin'))->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');

    Route::get('/stok-bahan-baku', [RawMaterialController::class, 'index'])->name('admin.stok');
    Route::get('/stok-bahan-baku/{id}', [RawMaterialController::class, 'show'])->name('admin.stok.show');
    Route::post('/stok-bahan-baku', [RawMaterialController::class, 'store'])->name('admin.stok.store');
    Route::put('/stok-bahan-baku/{id}', [RawMaterialController::class, 'update'])->name('admin.stok.update');
    Route::post('/stok-bahan-baku/{id}/restock', [RawMaterialController::class, 'restock'])->name('admin.stok.restock');
    Route::delete('/stok-bahan-baku/{id}', [RawMaterialController::class, 'destroy'])->name('admin.stok.destroy');

    Route::get('/bahan-dasar', [BahanDasarController::class, 'index'])->name('admin.bahan_dasar');
    Route::get('/bahan-dasar/{id}', [BahanDasarController::class, 'show'])->name('admin.bahan_dasar.show');
    Route::post('/bahan-dasar', [BahanDasarController::class, 'store'])->name('admin.bahan_dasar.store');
    Route::put('/bahan-dasar/{id}', [BahanDasarController::class, 'update'])->name('admin.bahan_dasar.update');
    Route::delete('/bahan-dasar/{id}', [BahanDasarController::class, 'destroy'])->name('admin.bahan_dasar.destroy');
    Route::post('/bahan-dasar/{id}/buat-adonan', [BahanDasarController::class, 'buatAdonan'])->name('admin.bahan_dasar.buat_adonan');
    Route::delete('/bahan-dasar/{id}/batch/{batchId}', [BahanDasarController::class, 'destroyBatch'])->name('admin.bahan_dasar.batch.destroy');

    Route::post('/satuan', [UnitController::class, 'store'])->name('admin.satuan.store');
    Route::put('/satuan/{id}', [UnitController::class, 'update'])->name('admin.satuan.update');
    Route::delete('/satuan/{id}', [UnitController::class, 'destroy'])->name('admin.satuan.destroy');

    Route::get('/data-produksi', [ProductionController::class, 'index'])->name('admin.produksi');
    Route::get('/data-produksi/{id}', [ProductionController::class, 'show'])->name('admin.produksi.show');
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
    Route::get('/data-produk/{id}', [ProductController::class, 'show'])->name('admin.produk.show');
    Route::post('/data-produk', [ProductController::class, 'store'])->name('admin.produk.store');
    Route::put('/data-produk/{id}', [ProductController::class, 'update'])->name('admin.produk.update');
    Route::delete('/data-produk/{id}', [ProductController::class, 'destroy'])->name('admin.produk.destroy');

    Route::prefix('laporan')->group(function () {
        Route::get('/laba-rugi', [ReportController::class, 'incomeStatement'])->name('admin.laba_rugi');
        Route::get('/laporan-penjualan', [ReportController::class, 'salesReport'])->name('admin.laporan_penjualan');
        Route::get('/neraca', [ReportController::class, 'balanceSheet'])->name('admin.neraca');
    });

    Route::prefix('pdf')->name('admin.pdf.')->group(function () {
        Route::get('/trial-balance', [PdfReportController::class, 'trialBalance'])->name('tb');
        Route::get('/general-ledger', [PdfReportController::class, 'generalLedger'])->name('gl');
        Route::get('/jurnal-umum', [PdfReportController::class, 'journal'])->name('jurnal');
        Route::get('/coa', [PdfReportController::class, 'coa'])->name('coa');
        Route::get('/neraca', [PdfReportController::class, 'balanceSheet'])->name('neraca');
        Route::get('/laba-rugi', [PdfReportController::class, 'incomeStatement'])->name('laba_rugi');
        Route::get('/laporan-penjualan', [PdfReportController::class, 'salesReport'])->name('penjualan');
    });

    Route::prefix('akuntansi')->group(function () {
        Route::get('/coa', [AccountController::class, 'index'])->name('admin.coa');
        Route::post('/coa', [AccountController::class, 'store'])->name('admin.coa.store');
        Route::put('/coa/{kode}', [AccountController::class, 'update'])->name('admin.coa.update');
        Route::delete('/coa/{kode}', [AccountController::class, 'destroy'])->name('admin.coa.destroy');

        Route::get('/jurnal-umum', [JournalController::class, 'index'])->name('admin.jurnal');
        Route::delete('/jurnal-umum/{id}', [JournalController::class, 'destroy'])->name('admin.jurnal.destroy');

        Route::get('/general-ledger', [ReportController::class, 'generalLedger'])->name('admin.gl');
        Route::get('/trial-balance', [ReportController::class, 'trialBalance'])->name('admin.tb');
    });
});

Route::middleware($access('karyawan'))->prefix('karyawan')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'karyawan'])->name('karyawan.dashboard');

    Route::get('/data-produksi', [ProductionController::class, 'index'])->name('karyawan.produksi');
    Route::get('/data-produksi/{id}', [ProductionController::class, 'show'])->name('karyawan.produksi.show');
    Route::post('/data-produksi', [ProductionController::class, 'store'])->name('karyawan.produksi.store');

    Route::get('/transaksi-penjualan', [SalesTransactionController::class, 'index'])->name('karyawan.penjualan');
    Route::post('/transaksi-penjualan', [SalesTransactionController::class, 'store'])->name('karyawan.penjualan.store');
});

