<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FieldReportController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemSuggestionController;
use App\Http\Controllers\LogisticsController;
use App\Http\Controllers\SupportingPhotoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FinalizeController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::get('/lapangan', [FieldReportController::class, 'showIdentityForm'])->name('field-reports.identity');
Route::post('/lapangan', [FieldReportController::class, 'verifyIdentity'])->name('field-reports.verify');
Route::get('/lapangan/form', [FieldReportController::class, 'create'])->name('field-reports.create');
Route::post('/lapangan/form', [FieldReportController::class, 'store'])->name('field-reports.store');
Route::post('/lapangan/supporting-photos/{logistics}', [SupportingPhotoController::class, 'store'])->name('field-reports.supporting-photos.store');
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
    Route::get('/verifications', [VerificationController::class, 'index'])->name('verifications.index');
    Route::patch('/verifications/{logistics}', [VerificationController::class, 'update'])->name('verifications.update');
    Route::patch('/verifications/photos/{photo}/status', [VerificationController::class, 'updatePhotoStatus'])->name('verifications.photo-status');
    Route::post('/verifications/photos/{photo}/items', [VerificationController::class, 'addPhotoItem'])->name('verifications.photo-items.store');
    Route::delete('/verifications/photo-items/{photoItem}', [VerificationController::class, 'removePhotoItem'])->name('verifications.photo-items.destroy');
    Route::post('/logistics/{logistics}/supporting-photos', [SupportingPhotoController::class, 'store'])->name('logistics.supporting-photos.store');
    Route::delete('/supporting-photos/{photo}', [SupportingPhotoController::class, 'destroy'])->name('supporting-photos.destroy');
    Route::get('/finalisasi', [FinalizeController::class, 'index'])->name('finalisasi.index');
    Route::post('/finalisasi/{logistics}', [FinalizeController::class, 'finalize'])->name('finalisasi.finalize');
    Route::resource('items', ItemController::class)->except('show');
    Route::resource('branches', BranchController::class)->except('show');
    Route::get('/item-suggestions', [ItemSuggestionController::class, 'index'])->name('item-suggestions.index');
    Route::post('/item-suggestions/{suggestion}/approve', [ItemSuggestionController::class, 'approve'])->name('item-suggestions.approve');
    Route::post('/item-suggestions/{suggestion}/reject', [ItemSuggestionController::class, 'reject'])->name('item-suggestions.reject');
    Route::get('/users/identity-preview', [UserController::class, 'identityPreview'])->name('users.identity-preview');
    Route::get('/users/{user}/barcode', [UserController::class, 'barcode'])->name('users.barcode');
    Route::resource('users', UserController::class)->except('show');
});

Route::middleware(['auth', 'role:logistik,lapangan'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::resource('logistics', LogisticsController::class)->except(['show', 'create', 'store']);
    Route::patch('/logistics/{logistics}/office-note', [LogisticsController::class, 'updateOfficeNote'])->name('logistics.office-note');
    Route::get('/verifications', [VerificationController::class, 'index'])->name('verifications.index');
    Route::patch('/verifications/{logistics}', [VerificationController::class, 'update'])->name('verifications.update');
    Route::patch('/verifications/{logistics}/logistik-note', [VerificationController::class, 'updateLogistikNote'])->name('verifications.logistik-note');
    Route::patch('/verifications/photos/{photo}/status', [VerificationController::class, 'updatePhotoStatus'])->name('verifications.photo-status');
    Route::post('/verifications/photos/{photo}/items', [VerificationController::class, 'addPhotoItem'])->name('verifications.photo-items.store');
    Route::delete('/verifications/photo-items/{photoItem}', [VerificationController::class, 'removePhotoItem'])->name('verifications.photo-items.destroy');
    Route::get('/logistics/{logistics}/add-photos', [LogisticsController::class, 'addPhotosForm'])->name('logistics.add-photos.form');
    Route::post('/logistics/{logistics}/add-photos', [LogisticsController::class, 'addPhotos'])->name('logistics.add-photos');
    Route::post('/logistics/{logistics}/supporting-photos', [SupportingPhotoController::class, 'store'])->name('logistics.supporting-photos.store');
    Route::delete('/supporting-photos/{photo}', [SupportingPhotoController::class, 'destroy'])->name('supporting-photos.destroy');
    Route::get('/item-suggestions', [ItemSuggestionController::class, 'index'])->name('item-suggestions.index');
    Route::get('/item-suggestions/create', [ItemSuggestionController::class, 'create'])->name('item-suggestions.create');
    Route::post('/item-suggestions', [ItemSuggestionController::class, 'store'])->name('item-suggestions.store');
});
