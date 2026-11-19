<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\FolderController;
use App\Http\Controllers\Api\UploadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| REST API RuangCloud (token Sanctum)
|--------------------------------------------------------------------------
| Login menukar email + kata sandi menjadi token Bearer. Semua endpoint
| lain wajib membawa header: Authorization: Bearer <token>.
| Operasi file, folder, dan unggahan memakai ulang logika controller web
| (validasi, hak akses per pengguna, dan pencatatan aktivitas yang sama).
*/

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:30,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/files', [FileController::class, 'index']);
    Route::get('/files/{file}', [FileController::class, 'show']);
    Route::patch('/files/{file}', [FileController::class, 'rename']);
    Route::post('/files/{file}/move', [FileController::class, 'move']);
    Route::delete('/files/{file}', [FileController::class, 'destroy']);
    Route::get('/files/{file}/download', [FileController::class, 'download']);

    Route::get('/folders', [FolderController::class, 'index']);
    Route::post('/folders', [FolderController::class, 'store']);
    Route::patch('/folders/{folder}', [FolderController::class, 'rename']);
    Route::post('/folders/{folder}/move', [FolderController::class, 'move']);
    Route::delete('/folders/{folder}', [FolderController::class, 'destroy']);

    // Unggahan resumable (alur sama seperti web: init -> chunk -> complete -> status).
    Route::post('/uploads/init', [UploadController::class, 'init']);
    Route::get('/uploads/{session}', [UploadController::class, 'status']);
    Route::post('/uploads/{session}/chunk', [UploadController::class, 'chunk']);
    Route::post('/uploads/{session}/complete', [UploadController::class, 'complete']);
    Route::delete('/uploads/{session}', [UploadController::class, 'cancel']);
});
