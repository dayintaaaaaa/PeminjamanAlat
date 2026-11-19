<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ToolCategoryController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\BorrowedController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\MyBorrowedController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function (Request $request) {
    $user = $request->user();

    return $user
        ? redirect($user->homeUrl())
        : redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // user management
    Route::get('/users', [UserController::class, 'index'])->name('users.management-user');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.add-user');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit-user');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // tools category
    Route::get('/tool-categories', [ToolCategoryController::class, 'index'])->name('master.tools-category');
    Route::post('/tool-categories', [ToolCategoryController::class, 'store'])->name('tool-categories.store');
    Route::put('/tool-categories/{category}', [ToolCategoryController::class, 'update'])->name('tool-categories.update');
    Route::delete('/tool-categories/{category}', [ToolCategoryController::class, 'destroy'])->name('tool-categories.destroy');

    // tools data: hanya admin yang boleh menambah, mengubah, dan menghapus
    Route::get('/tools-data/create', [ToolController::class, 'create'])->name('master.add-tool');
    Route::post('/tools-data', [ToolController::class, 'store'])->name('tools.store');
    Route::get('/tools-data/{tool}/edit', [ToolController::class, 'edit'])->name('master.edit-tool');
    Route::put('/tools-data/{tool}', [ToolController::class, 'update'])->name('tools.update');
    Route::delete('/tools-data/{tool}', [ToolController::class, 'destroy'])->name('tools.destroy');

    // transaction
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.transaction');
    Route::get('/transactions/export/{type}', [TransactionController::class, 'export'])
        ->whereIn('type', ['borrowing', 'return'])->name('transactions.export');

    Route::post('/transactions/borrowings', [TransactionController::class, 'storeBorrowing'])->name('transactions.borrowings.store');
    Route::put('/transactions/borrowings/{borrowing}', [TransactionController::class, 'updateBorrowing'])->name('transactions.borrowings.update');
    Route::delete('/transactions/borrowings/{borrowing}', [TransactionController::class, 'destroyBorrowing'])->name('transactions.borrowings.destroy');

    Route::post('/transactions/returns', [TransactionController::class, 'storeReturn'])->name('transactions.returns.store');
    Route::put('/transactions/returns/{returnTool}', [TransactionController::class, 'updateReturn'])->name('transactions.returns.update');
    Route::delete('/transactions/returns/{returnTool}', [TransactionController::class, 'destroyReturn'])->name('transactions.returns.destroy');

    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-log.activity-log');
    Route::get('/activity-logs/export', [ActivityLogController::class, 'export'])->name('activity-log.export');
});

/*
|--------------------------------------------------------------------------
| Admin, petugas, dan peminjam boleh melihat daftar alat
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin|petugas|peminjam'])->group(function () {
    Route::get('/tools-data', [ToolController::class, 'index'])->name('master.tools-data');
});

/*
|--------------------------------------------------------------------------
| Petugas
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:petugas'])->group(function () {
    Route::get('/borrowed', [BorrowedController::class, 'index'])->name('borrowed.borrowed');
    Route::patch('/borrowed/{borrowing}/approve', [BorrowedController::class, 'approve'])->name('borrowed.approve');
    Route::patch('/borrowed/{borrowing}/reject', [BorrowedController::class, 'reject'])->name('borrowed.reject');

    Route::get('/return', [ReturnController::class, 'index'])->name('return.return');
    Route::patch('/return/{returnTool}/approve', [ReturnController::class, 'approve'])->name('return.approve');
    Route::patch('/return/{returnTool}/reject', [ReturnController::class, 'reject'])->name('return.reject');
});

/*
|--------------------------------------------------------------------------
| Peminjam
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:peminjam'])->group(function () {
    Route::get('/my-borrowed', [MyBorrowedController::class, 'index'])->name('my-borrowed.my-borrowed');
    Route::post('/my-borrowed', [MyBorrowedController::class, 'store'])->name('my-borrowed.store');
    Route::post('/my-borrowed/{borrowing}/return', [MyBorrowedController::class, 'requestReturn'])->name('my-borrowed.return');
});

/*
|--------------------------------------------------------------------------
| Profil (semua user yang login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';