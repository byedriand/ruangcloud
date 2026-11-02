<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ShareLink;
use App\Models\StoredFile;
use App\Services\FolderArchiveService;
use App\Services\StorageNodeService;
use App\Support\Fmt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/** Halaman publik untuk tautan berbagi (tanpa login): file maupun folder. */
class PublicShareController extends Controller
{
    public function show(Request $request, string $token)
    {
        $share = $this->find($token);
        if (! $share) {
            return response()->view('share.unavailable', [], 410);
        }

        $locked = $this->isLocked($request, $share);

        if ($share->folder_id !== null) {
            $folder = $share->folder;
            $entries = [];
            $total = 0;

            if (! $locked) {
                foreach ($folder->archiveEntries() as [$file, $relativePath]) {
                    $meta = Fmt::meta($file->name, $file->mime);
                    $entries[] = [
                        'id'    => $file->id,
                        'rel'   => $relativePath,
                        'name'  => $file->name,
                        'size'  => Fmt::bytes($file->size),
                        'icon'  => $meta[1],
                        'color' => $meta[2],
                    ];
                    $total += $file->size;
                }
            }

            return view('share.show-folder', [
                'token'     => $token,
                'folder'    => $folder,
                'owner'     => $share->user,
                'entries'   => $entries,
                'count'     => count($entries),
                'totalSize' => Fmt::bytes($total),
                'locked'    => $locked,
            ]);
        }

        return view('share.show', [
            'token'  => $token,
            'file'   => $share->file,
            'size'   => Fmt::bytes($share->file->size),
            'locked' => $locked,
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

    /** Unduh satu file dari dalam folder yang dibagikan. */
    public function downloadFile(Request $request, string $token, StoredFile $file, StorageNodeService $nodes)
    {
        $share = $this->find($token);
        if (! $share || $share->folder_id === null) {
            return response()->view('share.unavailable', [], 410);
        }
        if ($this->isLocked($request, $share)) {
            return redirect()->route('share.show', $token);
        }

        abort_unless($file->folder_id !== null && in_array($file->folder_id, $share->folder->subtreeIds(), true), 404);

        $response = $nodes->downloadResponse($file);
        abort_if($response === null, 503, 'File sementara tidak tersedia.');

        $share->increment('download_count');
        ActivityLog::record('download', 'File "' . $file->name . '" diunduh lewat tautan folder berbagi', ['share_id' => $share->id], $share->user_id);

        return $response;
    }

    /** Unduh seluruh isi folder yang dibagikan sebagai ZIP. */
    public function downloadAll(Request $request, string $token, FolderArchiveService $archive)
    {
        $share = $this->find($token);
        if (! $share || $share->folder_id === null) {
            return response()->view('share.unavailable', [], 410);
        }
        if ($this->isLocked($request, $share)) {
            return redirect()->route('share.show', $token);
        }

        [$zipPath, $count] = $archive->build($share->folder);
        abort_if($count === 0, 404, 'Folder ini belum berisi file yang bisa diunduh.');

        $share->increment('download_count');
        ActivityLog::record('download', 'Folder "' . $share->folder->name . '" diunduh sebagai ZIP lewat tautan berbagi', ['share_id' => $share->id], $share->user_id);

        $zipName = trim(preg_replace('/[^A-Za-z0-9]+/', '-', $share->folder->name), '-');
        $zipName = ($zipName !== '' ? $zipName : 'folder') . '.zip';

        return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
    }

    /** Tautan valid: ada, tidak dicabut, belum kedaluwarsa, dan targetnya masih ada. */
    private function find(string $token): ?ShareLink
    {
        $share = ShareLink::with(['file' => fn ($q) => $q->withTrashed(), 'folder', 'user'])
            ->where('token', $token)
            ->first();

        if (! $share || ! $share->isActive()) {
            return null;
        }

        if ($share->folder_id !== null) {
            return $share->folder ? $share : null;
        }

        if (! $share->file || $share->file->trashed()) {
            return null;
        }

        return $share;
    }

    private function isLocked(Request $request, ShareLink $share): bool
    {
        return $share->password_hash !== null && ! $request->session()->get("share_ok.{$share->id}", false);
    }
}
