<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FieldReportController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemPriceController;
use App\Http\Controllers\LogisticsController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::get('/lapangan', [FieldReportController::class, 'showIdentityForm'])->name('field-reports.identity');
Route::post('/lapangan', [FieldReportController::class, 'verifyIdentity'])->name('field-reports.verify');
Route::get('/lapangan/form', [FieldReportController::class, 'create'])->name('field-reports.create');
Route::post('/lapangan/form', [FieldReportController::class, 'store'])->name('field-reports.store');
Route::post('/lapangan/logout', [FieldReportController::class, 'destroySession'])->name('field-reports.logout');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route(request()->user()->dashboardRouteName());
    })->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', 'role:kantor'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::resource('logistics', LogisticsController::class)->except('show');
    Route::patch('/logistics/{logistics}/office-note', [LogisticsController::class, 'updateOfficeNote'])->name('logistics.office-note');
    Route::get('/uploads', [UploadController::class, 'index'])->name('uploads.index');
    Route::post('/uploads', [UploadController::class, 'store'])->name('uploads.store');
    Route::get('/verifications', [VerificationController::class, 'index'])->name('verifications.index');
    Route::patch('/verifications/{logistics}', [VerificationController::class, 'update'])->name('verifications.update');
    Route::resource('items', ItemController::class)->except('show');
    Route::get('/prices', [ItemPriceController::class, 'index'])->name('prices.index');
    Route::get('/prices/create', [ItemPriceController::class, 'create'])->name('prices.create');
    Route::post('/prices', [ItemPriceController::class, 'store'])->name('prices.store');
    Route::get('/prices/{price}/edit', [ItemPriceController::class, 'edit'])->name('prices.edit');
    Route::put('/prices/{price}', [ItemPriceController::class, 'update'])->name('prices.update');
    Route::delete('/prices/{price}', [ItemPriceController::class, 'destroy'])->name('prices.destroy');
    Route::resource('branches', BranchController::class)->except('show');
    Route::resource('users', UserController::class)->except('show');
});

Route::middleware(['auth', 'role:logistik,lapangan'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::resource('logistics', LogisticsController::class)->except('show');
    Route::patch('/logistics/{logistics}/office-note', [LogisticsController::class, 'updateOfficeNote'])->name('logistics.office-note');
    Route::get('/uploads', [UploadController::class, 'index'])->name('uploads.index');
    Route::post('/uploads', [UploadController::class, 'store'])->name('uploads.store');
    Route::get('/verifications', [VerificationController::class, 'index'])->name('verifications.index');
    Route::patch('/verifications/{logistics}', [VerificationController::class, 'update'])->name('verifications.update');
});
