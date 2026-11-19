<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UploadController as WebUploadController;
use App\Models\UploadSession;
use App\Services\StorageNodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * REST API unggahan resumable. Seluruh langkah (init, chunk, complete,
 * status, cancel) didelegasikan ke controller web: satu jalur pipa,
 * satu aturan kuota, checksum, dan perakitan file.
 */
class UploadController extends Controller
{
    public function init(Request $request): JsonResponse
    {
        return app(WebUploadController::class)->init($request);
    }

    public function status(Request $request, UploadSession $session): JsonResponse
    {
        return app(WebUploadController::class)->status($request, $session, app(StorageNodeService::class));
    }

    public function chunk(Request $request, UploadSession $session): JsonResponse
    {
        return app(WebUploadController::class)->chunk($request, $session);
    }

    public function complete(Request $request, UploadSession $session): JsonResponse
    {
        return app(WebUploadController::class)->complete($request, $session);
    }

    public function cancel(Request $request, UploadSession $session): JsonResponse
    {
        return app(WebUploadController::class)->cancel($request, $session);
    }
}
