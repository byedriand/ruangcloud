<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ShareLink;
use App\Services\StorageNodeService;
use App\Support\Fmt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/** Halaman publik untuk tautan berbagi (tanpa login). */
class PublicShareController extends Controller
{
    public function show(Request $request, string $token)
    {
        $share = $this->find($token);
        if (! $share) {
            return response()->view('share.unavailable', [], 410);
        }

        return view('share.show', [
            'token'  => $token,
            'file'   => $share->file,
            'size'   => Fmt::bytes($share->file->size),
            'locked' => $this->isLocked($request, $share),
        ]);
    }

    public function unlock(Request $request, string $token)
    {
        $share = $this->find($token);
        if (! $share) {
            return response()->view('share.unavailable', [], 410);
        }

        $request->validate(['password' => ['required', 'string']]);

        if (! $share->password_hash || ! Hash::check($request->input('password'), $share->password_hash)) {
            return back()->withErrors(['password' => 'Kata sandi salah.']);
        }

        $request->session()->put("share_ok.{$share->id}", true);

        return redirect()->route('share.show', $token);
    }

    public function download(Request $request, string $token, StorageNodeService $nodes)
    {
        $share = $this->find($token);
        if (! $share) {
            return response()->view('share.unavailable', [], 410);
        }
        if ($this->isLocked($request, $share)) {
            return redirect()->route('share.show', $token);
        }

        $response = $nodes->downloadResponse($share->file);
        abort_if($response === null, 503, 'File sementara tidak tersedia.');

        $share->increment('download_count');
        ActivityLog::record('download', 'File "' . $share->file->name . '" diunduh lewat tautan berbagi', ['share_id' => $share->id], $share->user_id);

        return $response;
    }

    /** Tautan valid: ada, tidak dicabut, belum kedaluwarsa, file tidak di sampah. */
    private function find(string $token): ?ShareLink
    {
        $share = ShareLink::with('file')->where('token', $token)->first();

        if (! $share || ! $share->isActive() || ! $share->file) {
            return null;
        }

        return $share;
    }

    private function isLocked(Request $request, ShareLink $share): bool
    {
        return $share->password_hash !== null && ! $request->session()->get("share_ok.{$share->id}", false);
    }
}
