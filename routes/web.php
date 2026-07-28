<?php

use App\Http\Controllers\BudgetCategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Settings\AnggaranTahunController;
use App\Http\Controllers\Settings\MasterKomponenController;
use App\Http\Controllers\Settings\VendorController;
use App\Http\Controllers\Settings\PejabatController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('role:admin,bendahara')->group(function () {
        Route::get('/transaksi', [TransactionController::class, 'index'])->name('transactions.index');
        Route::get('/transaksi/tambah', [TransactionController::class, 'create'])->name('transactions.create');
        Route::get('/transaksi/{transaction}/bukti', [TransactionController::class, 'bukti'])->name('transactions.bukti');
        Route::post('/transaksi', [TransactionController::class, 'store'])->name('transactions.store');
        Route::get('/transaksi/{transaction}/edit', [TransactionController::class, 'edit'])->name('transactions.edit');
        Route::put('/transaksi/{transaction}', [TransactionController::class, 'update'])->name('transactions.update');

        Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/laporan/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
        Route::get('/laporan/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
        Route::get('/laporan/resmi/pdf', [ReportController::class, 'officialPdf'])->name('reports.official.pdf');

        Route::get('/pagu-anggaran', [BudgetCategoryController::class, 'index'])->name('budget-categories.index');
    });

    Route::middleware('role:admin')->group(function () {
        Route::delete('/transaksi/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');

        Route::get('/pagu-anggaran/tambah', [BudgetCategoryController::class, 'create'])->name('budget-categories.create');
        Route::post('/pagu-anggaran', [BudgetCategoryController::class, 'store'])->name('budget-categories.store');
        Route::put('/pagu-anggaran/{budgetCategory}', [BudgetCategoryController::class, 'update'])->name('budget-categories.update');

        Route::get('/pengguna', [UserController::class, 'index'])->name('users.index');
        Route::post('/pengguna', [UserController::class, 'store'])->name('users.store');
        Route::put('/pengguna/{user}', [UserController::class, 'update'])->name('users.update');

        Route::get('/audit-log', function () {
            $logs = \App\Models\ActivityLog::with('user')->orderByDesc('id')->paginate(30);
            return view('audit-log.index', compact('logs'));
        })->name('audit-log.index');

        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/tahun-anggaran', [AnggaranTahunController::class, 'index'])->name('anggaran-tahun.index');
            Route::post('/tahun-anggaran', [AnggaranTahunController::class, 'store'])->name('anggaran-tahun.store');
            Route::put('/tahun-anggaran/{anggaranTahun}/aktifkan', [AnggaranTahunController::class, 'aktifkan'])->name('anggaran-tahun.aktifkan');

            Route::get('/master-komponen', [MasterKomponenController::class, 'index'])->name('master-komponen.index');
            Route::post('/master-komponen', [MasterKomponenController::class, 'store'])->name('master-komponen.store');
            Route::put('/master-komponen/{masterKomponen}', [MasterKomponenController::class, 'update'])->name('master-komponen.update');

            Route::get('/vendor', [VendorController::class, 'index'])->name('vendors.index');
            Route::post('/vendor', [VendorController::class, 'store'])->name('vendors.store');
            Route::put('/vendor/{vendor}', [VendorController::class, 'update'])->name('vendors.update');

            Route::get('/pejabat', [PejabatController::class, 'index'])->name('pejabat.index');
            Route::post('/pejabat', [PejabatController::class, 'store'])->name('pejabat.store');
            Route::put('/pejabat/{pejabat}', [PejabatController::class, 'update'])->name('pejabat.update');
            Route::put('/pejabat/{pejabat}/aktifkan', [PejabatController::class, 'aktifkan'])->name('pejabat.aktifkan');
        });
    });
});

require __DIR__.'/auth.php';