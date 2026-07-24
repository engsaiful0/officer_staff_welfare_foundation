<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\LedgerEntryController;
use App\Http\Controllers\InvestmentImportController;
use App\Http\Controllers\InvestmentReportController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\DepositLedgerController;
use App\Http\Controllers\DepositImportController;
use App\Http\Controllers\DepositReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Investment Module API Routes
Route::prefix('investments')->group(function () {
    Route::post('/calculate', [InvestmentController::class, 'calculate']);

    // Investment CRUD API
    Route::get('/', [InvestmentController::class, 'index']);
    Route::post('/', [InvestmentController::class, 'store']);
    Route::get('/{investment}', [InvestmentController::class, 'show']);
    Route::put('/{investment}', [InvestmentController::class, 'update']);
    Route::delete('/{investment}', [InvestmentController::class, 'destroy']);
    Route::get('/member/{memberId}', [InvestmentController::class, 'getByMember']);
    
    // Ledger Entry API
    Route::prefix('{investment}/ledger-entries')->group(function () {
        Route::get('/', [LedgerEntryController::class, 'index']);
        Route::post('/', [LedgerEntryController::class, 'store']);
        Route::post('/accrual', [LedgerEntryController::class, 'createAccrual']);
        Route::get('/{ledgerEntry}', [LedgerEntryController::class, 'show']);
        Route::put('/{ledgerEntry}', [LedgerEntryController::class, 'update']);
        Route::delete('/{ledgerEntry}', [LedgerEntryController::class, 'destroy']);
    });
    
    // Import API
    Route::post('/import', [InvestmentImportController::class, 'import']);
    Route::get('/import/history', [InvestmentImportController::class, 'history']);
    
    // Reports API
    Route::prefix('reports')->group(function () {
        Route::get('/portfolio', [InvestmentReportController::class, 'portfolioReport']);
        Route::get('/interest', [InvestmentReportController::class, 'interestReport']);
        Route::get('/maturity', [InvestmentReportController::class, 'maturityReport']);
        Route::get('/export-pdf', [InvestmentReportController::class, 'exportPdf']);
        Route::get('/export-excel', [InvestmentReportController::class, 'exportExcel']);
    });
});

// Standalone Ledger Entry API
Route::prefix('ledger-entries')->group(function () {
    Route::get('/', [LedgerEntryController::class, 'index']);
    Route::post('/', [LedgerEntryController::class, 'store']);
    Route::get('/{ledgerEntry}', [LedgerEntryController::class, 'show']);
    Route::put('/{ledgerEntry}', [LedgerEntryController::class, 'update']);
    Route::delete('/{ledgerEntry}', [LedgerEntryController::class, 'destroy']);
    Route::post('/accrual', [LedgerEntryController::class, 'createAccrual']);
});

// Deposit Module API Routes
Route::prefix('deposits')->group(function () {
    // Deposit CRUD API
    Route::get('/', [DepositController::class, 'index']);
    Route::post('/', [DepositController::class, 'store']);
    Route::get('/{deposit}', [DepositController::class, 'show']);
    Route::put('/{deposit}', [DepositController::class, 'update']);
    Route::delete('/{deposit}', [DepositController::class, 'destroy']);
    Route::patch('/{deposit}/close', [DepositController::class, 'close']);
    Route::get('/member/{memberId}', [DepositController::class, 'getByMember']);
    
    // Deposit Ledger API
    Route::prefix('{deposit}/ledger')->group(function () {
        Route::get('/', [DepositLedgerController::class, 'index']);
        Route::post('/deposit', [DepositLedgerController::class, 'deposit']);
        Route::post('/withdrawal', [DepositLedgerController::class, 'withdrawal']);
        Route::post('/accrue', [DepositLedgerController::class, 'accrue']);
        Route::post('/adjustment', [DepositLedgerController::class, 'adjustment']);
        Route::put('/{ledgerEntry}', [DepositLedgerController::class, 'update']);
        Route::delete('/{ledgerEntry}', [DepositLedgerController::class, 'destroy']);
    });
    
    // Import API
    Route::post('/import', [DepositImportController::class, 'import']);
    Route::get('/import/history', [DepositImportController::class, 'history']);
    Route::get('/import/template', [DepositImportController::class, 'downloadTemplate']);
    
    // Reports API
    Route::prefix('reports')->group(function () {
        Route::get('/portfolio', [DepositReportController::class, 'portfolioReport']);
        Route::get('/interest', [DepositReportController::class, 'interestReport']);
        Route::get('/maturity', [DepositReportController::class, 'maturityReport']);
        Route::get('/export-pdf', [DepositReportController::class, 'exportPdf']);
        Route::get('/export-excel', [DepositReportController::class, 'exportExcel']);
    });
});
