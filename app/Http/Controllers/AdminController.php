<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\FileReplica;
use App\Models\StorageNode;
use App\Models\StoredFile;
use App\Models\UploadSession;
use App\Models\User;
use App\Support\Fmt;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $navItems = [
            ['label' => 'Ringkasan',   'icon' => 'grid'],
            ['label' => 'Pengguna',    'icon' => 'users'],
            ['label' => 'Penyimpanan', 'icon' => 'archive'],
            ['label' => 'Unggahan',    'icon' => 'upload'],
            ['label' => 'Audit log',   'icon' => 'activity'],
        ];

        /* ─── RINGKASAN ─────────────────────────────────────────────── */
        $allNodes     = StorageNode::orderBy('id')->get();
        $nodesTotal   = $allNodes->count();
        $nodesActive  = $allNodes->where('status', 'active')->count();
        $usedAll      = (int) FileReplica::where('file_replicas.status', 'synced')
            ->join('files', 'files.id', '=', 'file_replicas.file_id')
            ->sum('files.size');
        $capacityAll  = (int) $allNodes->sum('capacity_bytes');
        $activeUploads = UploadSession::whereIn('status', ['uploading', 'assembling']);
        $bigUploads    = (clone $activeUploads)->where('file_size', '>', 1024 ** 3)->count();

        $stats = [
            ['label' => 'Total pengguna',     'value' => number_format(User::count(), 0, ',', '.'), 'note' => '+' . User::where('created_at', '>=', now()->startOfMonth())->count() . ' bulan ini', 'icon' => 'users',    'tone' => 'mint'],
            ['label' => 'Kapasitas terpakai', 'value' => Fmt::bytes($usedAll),                       'note' => 'dari ' . Fmt::bytes($capacityAll),                                                         'icon' => 'archive',  'tone' => 'blue'],
            ['label' => 'Unggahan aktif',     'value' => (string) $activeUploads->count(),            'note' => $bigUploads . ' file besar',                                                                'icon' => 'upload',   'tone' => 'lavender'],
            ['label' => 'Node aktif',         'value' => $nodesActive . '/' . $nodesTotal,            'note' => $nodesActive === $nodesTotal ? 'Semua normal' : 'Ada node bermasalah',                       'icon' => 'activity', 'tone' => 'amber'],
        ];

        $days = collect(range(6, 0))->map(fn ($i) => Carbon::today()->subDays($i));
        $perDay = $days->map(fn (Carbon $d) => (int) StoredFile::whereDate('created_at', $d)->sum('size'));
        $max = max(1, $perDay->max());
        $chartValues = $perDay->map(fn ($v) => max(2, (int) round($v / $max * 100)))->all();
        $chartTips   = $perDay->map(fn ($v) => Fmt::bytes($v))->all();
        $chartLabels = $days->map(fn (Carbon $d) => $d->copy()->locale('id')->isoFormat('ddd'))->all();

        $nodes = $allNodes->map(fn (StorageNode $n) => [
            'name'   => $n->name,
            'usage'  => $n->usagePercent(),
            'size'   => Fmt::bytes($n->usedBytes()),
            'status' => $n->status,
        ])->all();

        $tones = ['mint', 'lavender', 'amber', 'blue'];
        $users = User::latest()->take(4)->get()->values()->map(function (User $u, int $i) use ($tones) {
            $used = (int) StoredFile::withTrashed()->where('user_id', $u->id)->sum('size');
            return [
                'initials' => Fmt::initials($u->name),
                'name'     => $u->name,
                'email'    => $u->email,
                'used'     => Fmt::bytes($used),
                'total'    => Fmt::bytes($u->quota_bytes),
                'percent'  => $u->quota_bytes > 0 ? (int) min(100, round($used / $u->quota_bytes * 100)) : 0,
                'status'   => $u->is_active ? 'Aktif' : 'Nonaktif',
                'tone'     => $tones[$i % count($tones)],
            ];
        })->all();

        $icons = ['upload' => 'upload', 'login' => 'users', 'download' => 'download', 'delete' => 'trash', 'replicate' => 'archive', 'folder' => 'folder'];
        $activities = ActivityLog::latest()->take(4)->get()->map(fn (ActivityLog $a) => [
            'icon' => $icons[$a->action] ?? 'activity',
            'text' => $a->description,
            'time' => $a->created_at->copy()->locale('id')->diffForHumans(),
        ])->all();

        /* ─── PENGGUNA ───────────────────────────────────────────────── */
        $allUsers = User::orderBy('name')->get();
        $newUsersThisMonth = User::where('created_at', '>=', now()->startOfMonth())->count();

        /* ─── PENYIMPANAN ────────────────────────────────────────────── */
        $storagePercent  = $capacityAll > 0 ? (int) min(100, round($usedAll / $capacityAll * 100)) : 0;
        $storageUsedFmt  = Fmt::bytes($usedAll);
        $storageCapacityFmt = Fmt::bytes($capacityAll);

        $allNodesDetail = $allNodes->map(function (StorageNode $n) {
            $used = $n->usedBytes();
            return [
                'name'          => $n->name,
                'host'          => $n->host ?? null,
                'status'        => $n->status,
                'usedFmt'       => Fmt::bytes($used),
                'capacityFmt'   => Fmt::bytes($n->capacity_bytes),
                'freeFmt'       => Fmt::bytes($n->freeBytes()),
                'usagePercent'  => $n->usagePercent(),
                'fileCount'     => FileReplica::where('storage_node_id', $n->id)->where('status', 'synced')->count(),
                'replicaCount'  => FileReplica::where('storage_node_id', $n->id)->count(),
            ];
        })->all();

        $biggestFiles = StoredFile::withoutTrashed()
            ->with(['user', 'replicas'])
            ->orderByDesc('size')
            ->take(10)
            ->get();

        /* ─── UNGGAHAN ───────────────────────────────────────────────── */
        $activeSessions = UploadSession::with('user')
            ->whereIn('status', ['uploading', 'assembling'])
            ->latest()
            ->get();

        $uploadStats = [
            'active'         => UploadSession::where('status', 'uploading')->count(),
            'assembling'     => UploadSession::where('status', 'assembling')->count(),
            'completedToday' => StoredFile::whereDate('created_at', today())->count(),
            'totalSizeToday' => Fmt::bytes((int) StoredFile::whereDate('created_at', today())->sum('size')),
        ];

        $recentFiles = StoredFile::withoutTrashed()
            ->with('user')
            ->latest()
            ->take(20)
            ->get();

        /* ─── AUDIT LOG ──────────────────────────────────────────────── */
        $auditLogs = ActivityLog::with('user')
            ->latest()
            ->paginate(25);

        $auditStats = collect(['upload', 'login', 'download', 'delete', 'share'])
            ->map(fn ($act) => [
                'label' => ucfirst($act),
                'count' => ActivityLog::where('action', $act)->count(),
            ])->all();

        return view('admin.dashboard', compact(
            // Sidebar / nav
            'navItems',
            // Ringkasan
            'stats', 'chartValues', 'chartTips', 'chartLabels',
            'nodes', 'nodesActive', 'nodesTotal', 'users', 'activities',
            // Pengguna
            'allUsers', 'newUsersThisMonth',
            // Penyimpanan
            'storagePercent', 'storageUsedFmt', 'storageCapacityFmt',
            'allNodesDetail', 'biggestFiles',
            // Unggahan
            'activeSessions', 'uploadStats', 'recentFiles',
            // Audit log
            'auditLogs', 'auditStats'
        ));
    }

    /** Buat akun admin baru. */
    public function storeAdmin(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required'         => 'Nama wajib diisi.',
            'email.required'        => 'Email wajib diisi.',
            'email.email'           => 'Format email tidak valid.',
            'email.unique'          => 'Email sudah digunakan oleh akun lain.',
            'password.required'     => 'Kata sandi wajib diisi.',
            'password.min'          => 'Kata sandi minimal 8 karakter.',
            'password.confirmed'    => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $admin = User::create([
            'name'        => trim($data['name']),
            'email'       => strtolower(trim($data['email'])),
            'password'    => bcrypt($data['password']),
            'role'        => 'admin',
            'is_active'   => true,
            'quota_bytes' => 0,
        ]);

        ActivityLog::record(
            'login',
            'Admin baru "' . $admin->name . '" ditambahkan oleh ' . $request->user()->name,
            ['user_id' => $admin->id]
        );

        return response()->json([
            'ok'      => true,
            'message' => 'Akun admin "' . $admin->name . '" berhasil dibuat.',
        ]);
    }

    /** Update status pengguna. */
    public function updateUser(Request $request, User $user)
    {
        // Jangan izinkan admin menonaktifkan dirinya sendiri
        if ($user->id === $request->user()->id) {
            return response()->json([
                'ok' => false,
                'message' => 'Anda tidak dapat mengubah akun Anda sendiri.',
            ], 403);
        }

        $data = $request->validate([
            'action' => ['required', 'in:toggle_active'],
        ]);

        $message = '';
        if ($data['action'] === 'toggle_active') {
            $user->is_active = !$user->is_active;
            $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
            $message = "Akun {$user->name} berhasil {$status}.";
            
            ActivityLog::record(
                'login',
                "Akun {$user->name} {$status} oleh " . $request->user()->name,
                ['user_id' => $user->id]
            );
        }

        $user->save();

        return response()->json([
            'ok'      => true,
            'message' => $message,
        ]);
    }

    /** Hapus akun pengguna. */
    public function destroyUser(Request $request, User $user)
    {
        // Jangan izinkan admin menghapus dirinya sendiri
        if ($user->id === $request->user()->id) {
            return response()->json([
                'ok' => false,
                'message' => 'Anda tidak dapat menghapus akun Anda sendiri.',
            ], 403);
        }

        $userName = $user->name;
        
        // Hapus akun (cascade delete pada database akan menghapus stored_files terkait,
        // namun untuk file di node penyimpanan fisik mungkin perlu garbage collector terpisah di masa depan)
        $user->delete();

        ActivityLog::record(
            'delete',
            "Akun pengguna {$userName} beserta seluruh datanya telah dihapus oleh " . $request->user()->name,
            ['user_id' => $request->user()->id]
        );

        return response()->json([
            'ok'      => true,
            'message' => "Akun {$userName} berhasil dihapus permanen.",
        ]);
    }
}
