<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogisticsController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::resource('logistics', LogisticsController::class)->except('show');

    Route::get('/uploads', [UploadController::class, 'index'])->name('uploads.index');
    Route::post('/uploads', [UploadController::class, 'store'])->name('uploads.store');

    Route::get('/verifications', [VerificationController::class, 'index'])
        ->middleware('role:admin_cabang,super_admin')
        ->name('verifications.index');
    Route::patch('/verifications/{logistics}', [VerificationController::class, 'update'])
        ->middleware('role:admin_cabang,super_admin')
        ->name('verifications.update');

    Route::resource('branches', BranchController::class)
        ->middleware('role:super_admin')
        ->except('show');

    Route::resource('users', UserController::class)
        ->middleware('role:super_admin')
        ->except('show');
});
