<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('accounts', AccountController::class)->only([
        'index', 'create', 'store', 'edit', 'update', 'destroy',
    ]);
    Route::resource('categories', CategoryController::class)->only([
        'index', 'create', 'store', 'edit', 'update', 'destroy',
    ]);
    Route::resource('transactions', TransactionController::class)->only([
        'index', 'create', 'store', 'show', 'edit', 'update', 'destroy',
    ]);
});

require __DIR__.'/auth.php';
