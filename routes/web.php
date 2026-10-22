<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PublicShareController;
use App\Http\Controllers\ShareController;
use App\Http\Controllers\TrashController;
use App\Http\Controllers\UploadController;
use App\Http\Middleware\EnsureRole;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Tautan berbagi publik (tanpa login)
Route::middleware('throttle:30,1')->group(function () {
    Route::get('/s/{token}', [PublicShareController::class, 'show'])->name('share.show');
    Route::post('/s/{token}', [PublicShareController::class, 'unlock'])->name('share.unlock');
    Route::get('/s/{token}/download', [PublicShareController::class, 'download'])->name('share.download');
});

Route::middleware('auth')->group(function () {
    // Halaman pengguna (RBAC: user)
    Route::middleware(EnsureRole::class . ':user')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/files', [FileController::class, 'index'])->name('files.index');
        Route::get('/recent', [FileController::class, 'recent'])->name('files.recent');
        Route::get('/shared', [ShareController::class, 'index'])->name('shares.index');
        Route::get('/activity', [ActivityController::class, 'index'])->name('activity.index');
        Route::get('/trash', [TrashController::class, 'index'])->name('trash.index');
    });

    // Administrator (RBAC: admin)
    Route::get('/admin', [AdminController::class, 'index'])
        ->middleware(EnsureRole::class . ':admin')
        ->name('admin');
    Route::post('/admin/admins', [AdminController::class, 'storeAdmin'])
        ->middleware(EnsureRole::class . ':admin')
        ->name('admin.admins.store');
    Route::patch('/admin/users/{user}', [AdminController::class, 'updateUser'])
        ->middleware(EnsureRole::class . ':admin')
        ->name('admin.users.update');
    Route::delete('/admin/users/{user}', [AdminController::class, 'destroyUser'])
        ->middleware(EnsureRole::class . ':admin')
        ->name('admin.users.destroy');

    // Resumable chunk upload
    Route::post('/upload/init', [UploadController::class, 'init'])->name('upload.init');
    Route::get('/upload/{session}', [UploadController::class, 'status'])->name('upload.status');
    Route::post('/upload/{session}/chunk', [UploadController::class, 'chunk'])->name('upload.chunk');
    Route::post('/upload/{session}/complete', [UploadController::class, 'complete'])->name('upload.complete');
    Route::delete('/upload/{session}', [UploadController::class, 'cancel'])->name('upload.cancel');

    // File
    Route::get('/files/{file}/download', [FileController::class, 'download'])->name('files.download');
    Route::get('/files/{file}/preview', [FileController::class, 'preview'])->name('files.preview');
    Route::patch('/files/{file}', [FileController::class, 'rename'])->name('files.rename');
    Route::post('/files/{file}/move', [FileController::class, 'move'])->name('files.move');
    Route::delete('/files/{file}', [FileController::class, 'destroy'])->name('files.destroy');
    Route::post('/files/{file}/share', [ShareController::class, 'store'])->name('files.share');

    // Folder
    Route::post('/folders', [FolderController::class, 'store'])->name('folders.store');
    Route::patch('/folders/{folder}', [FolderController::class, 'rename'])->name('folders.rename');
    Route::delete('/folders/{folder}', [FolderController::class, 'destroy'])->name('folders.destroy');

    // Tautan berbagi & sampah
    Route::delete('/shares/{share}', [ShareController::class, 'destroy'])->name('shares.destroy');
    Route::post('/trash/{id}/restore', [TrashController::class, 'restore'])->name('trash.restore');
    Route::delete('/trash/{id}', [TrashController::class, 'purge'])->name('trash.purge');
    Route::delete('/trash', [TrashController::class, 'empty'])->name('trash.empty');
});
