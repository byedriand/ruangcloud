<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Support\Fmt;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /** Gradasi simpul timeline per jenis aksi. */
    private const THEMES = [
        'login'     => 'from-sky-400 to-blue-500 shadow-sky-500/30',
        'upload'    => 'from-emerald-400 to-teal-500 shadow-emerald-500/30',
        'download'  => 'from-teal-400 to-cyan-500 shadow-teal-500/30',
        'delete'    => 'from-rose-400 to-red-500 shadow-rose-500/30',
        'replicate' => 'from-cyan-400 to-sky-500 shadow-cyan-500/30',
        'folder'    => 'from-amber-400 to-orange-500 shadow-amber-500/30',
        'share'     => 'from-violet-400 to-purple-500 shadow-violet-500/30',
        'restore'   => 'from-lime-400 to-emerald-500 shadow-lime-500/30',
        'profile'   => 'from-violet-400 to-purple-500 shadow-violet-500/30',
        'avatar'    => 'from-fuchsia-400 to-pink-500 shadow-fuchsia-500/30',
        'password'  => 'from-slate-500 to-slate-700 shadow-slate-500/30',
        'default'   => 'from-slate-400 to-slate-500 shadow-slate-400/30',
    ];

    public function index(Request $request)
    {
        $tz = config('app.timezone', 'Asia/Jakarta');

        $items = ActivityLog::where('user_id', $request->user()->id)
            ->latest()
            ->take(10)
            ->get()
            ->map(fn (ActivityLog $a) => [
                'icon' => Fmt::actionIcon($a->action),
                'theme' => self::THEMES[$a->action] ?? self::THEMES['default'],
                'text' => $a->description,
                'time' => $a->created_at->copy()->timezone($tz)->locale('id')->translatedFormat('j M Y, H.i'),
                'ago'  => $a->created_at->copy()->timezone($tz)->locale('id')->diffForHumans(),
            ])->all();

        return view('user.activity', compact('items'));
    }
}
