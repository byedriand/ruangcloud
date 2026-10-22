<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\StorageNode;
use App\Models\StoredFile;
use App\Support\Fmt;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/** Halaman Beranda. */
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $account = $request->user();

        $files = StoredFile::where('user_id', $account->id)
            ->latest()->take(8)->get()
            ->map(fn (StoredFile $f) => Fmt::fileRow($f))->all();

        $tones = ['mint', 'lavender', 'amber', 'blue'];
        $folders = Folder::where('user_id', $account->id)
            ->whereNull('parent_id')
            ->withCount('files')
            ->latest()->take(4)->get()->values()
            ->map(fn (Folder $f, int $i) => [
                'id'    => $f->id,
                'name'  => $f->name,
                'files' => $f->files_count . ' file',
                'tone'  => $tones[$i % count($tones)],
            ])->all();

        $nodes = StorageNode::orderBy('id')->get()->map(fn (StorageNode $n) => [
            'name'  => 'Node ' . $n->name,
            'usage' => $n->usagePercent(),
        ])->all();

        $firstName = Str::before($account->name, ' ');

        return view('user.dashboard', compact('files', 'folders', 'nodes', 'firstName'));
    }
}
