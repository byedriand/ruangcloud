<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ShareLink;
use App\Models\StoredFile;
use App\Support\Fmt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/** Tautan berbagi (KAK F-06): kata sandi opsional + masa berlaku. */
class ShareController extends Controller
{
    public function index(Request $request)
    {
        $shares = ShareLink::with(['file' => fn ($q) => $q->withTrashed()])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get()
            ->map(function (ShareLink $s) {
                $trashed = $s->file?->trashed() ?? true;
                $status = match (true) {
                    $s->revoked_at !== null => 'Dicabut',
                    $s->isExpired()         => 'Kedaluwarsa',
                    $trashed                => 'File di sampah',
                    default                 => 'Aktif',
                };

                return [
                    'id'        => $s->id,
                    'file'      => $s->file?->name ?? '(file dihapus)',
                    'icon'      => $s->file ? Fmt::meta($s->file->name, $s->file->mime)[1] : 'file',
                    'color'     => $s->file ? Fmt::meta($s->file->name, $s->file->mime)[2] : 'blue',
                    'url'       => $s->url(),
                    'protected' => $s->password_hash !== null,
                    'expires'   => $s->expires_at ? $s->expires_at->copy()->locale('id')->translatedFormat('j M Y, H.i') : 'Tanpa batas',
                    'downloads' => $s->download_count,
                    'status'    => $status,
                    'active'    => $status === 'Aktif',
                ];
            })->all();

        return view('user.shared', compact('shares'));
    }

    public function store(Request $request, StoredFile $file): JsonResponse
    {
        abort_unless($file->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'password'   => ['nullable', 'string', 'min:4', 'max:100'],
            'expires_in' => ['nullable', 'integer', 'in:1,7,30'],
        ]);

        $share = ShareLink::create([
            'file_id'       => $file->id,
            'user_id'       => $request->user()->id,
            'token'         => Str::random(40),
            'password_hash' => ! empty($data['password']) ? Hash::make($data['password']) : null,
            'expires_at'    => ! empty($data['expires_in']) ? now()->addDays((int) $data['expires_in']) : null,
        ]);

        ActivityLog::record('share', Str::before($request->user()->name, ' ') . ' membuat tautan berbagi untuk "' . $file->name . '"', ['file_id' => $file->id]);

        return response()->json(['url' => $share->url()]);
    }

    public function destroy(Request $request, ShareLink $share): JsonResponse
    {
        abort_unless($share->user_id === $request->user()->id, 403);

        $share->update(['revoked_at' => now()]);

        return response()->json(['ok' => true]);
    }
}
