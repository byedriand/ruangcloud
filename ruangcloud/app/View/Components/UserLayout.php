<?php

namespace App\View\Components;

use App\Models\Folder;
use App\Models\ShareLink;
use App\Models\StoredFile;
use App\Support\Fmt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use Illuminate\View\View;

/** Kerangka halaman pengguna: sidebar, header, kartu progres unggah, dan modal bersama. */
class UserLayout extends Component
{
    public array $user;
    public array $nav;
    public array $storage;
    public $folders;

    public function __construct(public string $title = 'Dashboard', public string $active = 'home', public ?int $folderId = null)
    {
        $account = Auth::user();

        $this->user = [
            'name'       => $account->name,
            'first_name' => Str::before($account->name, ' '),
            'email'      => $account->email,
            'initials'   => Fmt::initials($account->name),
        ];

        $activeShares = ShareLink::where('user_id', $account->id)
            ->whereNull('revoked_at')
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->count();
        $trashed = StoredFile::onlyTrashed()->where('user_id', $account->id)->count();

        $this->nav = [
            ['key' => 'home',     'label' => 'Beranda',   'icon' => 'home',     'route' => 'dashboard',      'badge' => null],
            ['key' => 'files',    'label' => 'File saya', 'icon' => 'folder',   'route' => 'files.index',    'badge' => null],
            ['key' => 'shared',   'label' => 'Dibagikan', 'icon' => 'users',    'route' => 'shares.index',   'badge' => $activeShares ?: null],
            ['key' => 'recent',   'label' => 'Terbaru',   'icon' => 'clock',    'route' => 'files.recent',   'badge' => null],
            ['key' => 'downloads', 'label' => 'Download', 'icon' => 'download', 'route' => 'files.downloads', 'badge' => null],
            ['key' => 'activity', 'label' => 'Aktivitas', 'icon' => 'activity', 'route' => 'activity.index', 'badge' => null],
            ['key' => 'trash',    'label' => 'Sampah',    'icon' => 'trash',    'route' => 'trash.index',    'badge' => $trashed ?: null],
        ];

        // Kuota: file di sampah tetap dihitung sampai dihapus permanen.
        $used = (int) StoredFile::withTrashed()->where('user_id', $account->id)->sum('size');
        $this->storage = [
            'percent' => $account->quota_bytes > 0 ? (int) min(100, round($used / $account->quota_bytes * 100)) : 0,
            'used'    => Fmt::bytes($used),
            'total'   => Fmt::bytes($account->quota_bytes),
        ];

        $this->folders = Folder::where('user_id', $account->id)->orderBy('name')->get(['id', 'name']);
    }

    public function render(): View
    {
        return view('components.user-layout');
    }
}
