<?php

use App\Http\Controllers\Owner\BillController as OwnerBillController;
use App\Http\Controllers\Owner\ComplaintController as OwnerComplaintController;
use App\Http\Controllers\Owner\OwnerDashboardController;
use App\Http\Controllers\Owner\RoomController as OwnerRoomController;
use App\Http\Controllers\Owner\TenantController as OwnerTenantController;
use App\Http\Controllers\Owner\BillPaymentController;
use App\Http\Controllers\Tenant\ComplaintController as TenantComplaintController;
use App\Http\Controllers\Tenant\TenantPortalController;
use Illuminate\Support\Facades\Route;

// KODE YANG BENAR (Langsung panggil redirect di luar, tanpa fungsi closure)
Route::redirect('/', '/login');

Route::middleware(['auth'])->get('/dashboard', function () {
    return match (auth()->user()->role) {
        'pemilik' => redirect()->route('owner.dashboard'),
        'penyewa' => redirect()->route('tenant.dashboard'),
        default => redirect('/'),
    };
})->name('dashboard');

Route::middleware(['auth', 'verified', 'role:pemilik'])
    ->prefix('owner')
    ->name('owner.')
    ->group(function () {
        Route::get('/dashboard', [OwnerDashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('tenants', OwnerTenantController::class);
        Route::resource('rooms', OwnerRoomController::class);
        Route::resource('bills', OwnerBillController::class);

        Route::get('/complaints', [OwnerComplaintController::class, 'index'])
            ->name('complaints.index');

        Route::patch('/complaints/{complaint}', [OwnerComplaintController::class, 'update'])
            ->name('complaints.update');

        // Terima bukti pembayaran & validasi status lunas
        Route::patch('/bills/{bill}/confirm', [BillPaymentController::class, 'confirm'])
            ->name('bills.confirm');

        // Laporan Keuangan Kost
        Route::get('/laporan-keuangan-kost', [\App\Http\Controllers\Owner\FinancialReportController::class, 'index'])
            ->name('financial-reports.index');

        // Unduh laporan (CSV)
        Route::get('/laporan-keuangan-kost/unduh', [\App\Http\Controllers\Owner\FinancialReportExportController::class, 'export'])
            ->name('financial-reports.export');
    });



Route::middleware(['auth', 'verified', 'role:penyewa'])
    ->prefix('tenant')
    ->name('tenant.')
    ->group(function () {
        Route::get('/dashboard', [TenantPortalController::class, 'index'])
            ->name('dashboard');

        Route::get('/bills', [TenantPortalController::class, 'bills'])
            ->name('bills.index');

        Route::get('/bills/{bill}/download', [TenantPortalController::class, 'downloadInvoice'])
            ->name('bills.download');

        Route::post('/bills/{bill}/payment-proof', [TenantPortalController::class, 'uploadPaymentProof'])
            ->name('bills.payment-proof');

        // Serve bukti pembayaran lewat Laravel (bukan /storage agar tidak 404)
        Route::get('/bills/{bill}/proof', [\App\Http\Controllers\AssetProofController::class, 'proof'])
            ->name('bills.proof');

        // API-like endpoint untuk cek tagihan aktif tenant
        Route::get('/bills/active-status', [TenantPortalController::class, 'activeBillStatus'])
            ->name('bills.active-status');

        Route::get('/complaints/create', [TenantComplaintController::class, 'create'])
            ->name('complaints.create');


        Route::post('/complaints', [TenantComplaintController::class, 'store'])
            ->name('complaints.store');
    });

require __DIR__.'/auth.php';
