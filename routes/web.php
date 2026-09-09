<?php

use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DestructionController;
use App\Http\Controllers\NumberingFormatController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - DMS PT Indraco
|--------------------------------------------------------------------------
*/

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard & Live Search
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/api/search-archives', [DashboardController::class, 'searchApi'])->name('archives.search_api');

    // Archives Management
    Route::get('/archives', [ArchiveController::class, 'index'])->name('archives.index');
    Route::get('/archives/create', [ArchiveController::class, 'create'])->name('archives.create');
    Route::post('/archives', [ArchiveController::class, 'store'])->name('archives.store');
    Route::get('/archives/{archive}', [ArchiveController::class, 'show'])->name('archives.show');
    Route::post('/archives/{archive}/verify', [ArchiveController::class, 'verify'])->name('archives.verify');
    Route::post('/archives/{archive}/checkin', [ArchiveController::class, 'checkin'])->name('archives.checkin');

    // Borrowing Workflow
    Route::get('/borrowings', [BorrowingController::class, 'index'])->name('borrowings.index');
    Route::get('/borrowings/create', [BorrowingController::class, 'create'])->name('borrowings.create');
    Route::post('/borrowings', [BorrowingController::class, 'store'])->name('borrowings.store');
    Route::post('/borrowings/{borrowing}/approve', [BorrowingController::class, 'approve'])->name('borrowings.approve');
    Route::post('/borrowings/{borrowing}/dispatch', [BorrowingController::class, 'dispatch'])->name('borrowings.dispatch');
    Route::post('/borrowings/{borrowing}/return', [BorrowingController::class, 'returnArchive'])->name('borrowings.return');

    // Retention Expiry & Destruction Workflow
    Route::get('/destructions', [DestructionController::class, 'index'])->name('destructions.index');
    Route::get('/destructions/propose/{archive}', [DestructionController::class, 'proposeForm'])->name('destructions.propose');
    Route::post('/destructions/propose/{archive}', [DestructionController::class, 'propose'])->name('destructions.store');
    Route::get('/destructions/bap/{destructionLog}', [DestructionController::class, 'showBap'])->name('destructions.bap');

    // Global Audit Trail Logs
    Route::get('/logs', [AuditLogController::class, 'index'])->name('logs.index');

    // Master Data Management (Admin & PIC Gudang)
    Route::middleware('role:admin,pic_gudang')->prefix('master')->name('master.')->group(function () {
        Route::get('/departments', [DepartmentController::class, 'index'])->name('departments');
        Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
        Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
        Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');

        Route::get('/warehouses', [WarehouseController::class, 'index'])->name('warehouses');
        Route::post('/warehouses', [WarehouseController::class, 'storeWarehouse'])->name('warehouses.store');
        Route::put('/warehouses/{warehouse}', [WarehouseController::class, 'updateWarehouse'])->name('warehouses.update');
        Route::delete('/warehouses/{warehouse}', [WarehouseController::class, 'destroyWarehouse'])->name('warehouses.destroy');

        Route::post('/warehouses/locations', [WarehouseController::class, 'storeLocation'])->name('warehouses.locations.store');
        Route::delete('/warehouses/locations/{location}', [WarehouseController::class, 'destroyLocation'])->name('warehouses.locations.destroy');

        Route::get('/numbering', [NumberingFormatController::class, 'index'])->name('numbering');
        Route::post('/numbering', [NumberingFormatController::class, 'store'])->name('numbering.store');
        Route::put('/numbering/{numberingFormat}', [NumberingFormatController::class, 'update'])->name('numbering.update');

        Route::get('/users', [UserController::class, 'index'])->name('users');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});
