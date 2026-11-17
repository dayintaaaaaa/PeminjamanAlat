<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ToolCategoryController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\BorrowedController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\MyBorrowedController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function (Request $request) {
    $user = $request->user();

    return $user
        ? redirect($user->homeUrl())
        : redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'role:admin'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.management-user');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.add-user');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit-user');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/tool-categories', [ToolCategoryController::class, 'index'])->name('master.tools-category');
    Route::post('/tool-categories', [ToolCategoryController::class, 'store'])->name('tool-categories.store');
    Route::put('/tool-categories/{category}', [ToolCategoryController::class, 'update'])->name('tool-categories.update');
    Route::delete('/tool-categories/{category}', [ToolCategoryController::class, 'destroy'])->name('tool-categories.destroy');
});

// admin, petugas, dan peminjam boleh melihat
Route::middleware(['auth', 'role:admin|petugas|peminjam'])->group(function () {
    Route::get('/tools-data', [ToolController::class, 'index'])->name('master.tools-data');
});

// hanya admin yang boleh menambah, mengubah, dan menghapus
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/tools-data/create', [ToolController::class, 'create'])->name('master.add-tool');
    Route::post('/tools-data', [ToolController::class, 'store'])->name('tools.store');
    Route::get('/tools-data/{tool}/edit', [ToolController::class, 'edit'])->name('master.edit-tool');
    Route::put('/tools-data/{tool}', [ToolController::class, 'update'])->name('tools.update');
    Route::delete('/tools-data/{tool}', [ToolController::class, 'destroy'])->name('tools.destroy');
}); 

Route::view('/transactions', 'transactions.transaction')->middleware('auth')->name('transactions.transaction');

Route::view('/activity-logs', 'activity-log.activity-log')->middleware('auth')->name('activity-log.activity-log');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:petugas'])->group(function () {
    Route::get('/borrowed', [BorrowedController::class, 'index'])->name('borrowed.borrowed');
    Route::patch('/borrowed/{borrowing}/approve', [BorrowedController::class, 'approve'])->name('borrowed.approve');
    Route::patch('/borrowed/{borrowing}/reject', [BorrowedController::class, 'reject'])->name('borrowed.reject');
});

Route::middleware(['auth', 'role:petugas'])->group(function () {
    Route::get('/return', [ReturnController::class, 'index'])->name('return.return');
    Route::patch('/return/{returnTool}/approve', [ReturnController::class, 'approve'])->name('return.approve');
    Route::patch('/return/{returnTool}/reject', [ReturnController::class, 'reject'])->name('return.reject');
});

Route::middleware(['auth', 'role:peminjam'])->group(function () {
    Route::get('/my-borrowed', [MyBorrowedController::class, 'index'])->name('my-borrowed.my-borrowed');
    Route::post('/my-borrowed', [MyBorrowedController::class, 'store'])->name('my-borrowed.store');
    Route::post('/my-borrowed/{borrowing}/return', [MyBorrowedController::class, 'requestReturn'])->name('my-borrowed.return');
});

require __DIR__.'/auth.php';
