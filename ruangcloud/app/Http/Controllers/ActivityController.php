<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Support\Fmt;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $tz = config('app.timezone', 'Asia/Jakarta');

        $items = ActivityLog::where('user_id', $request->user()->id)
            ->latest()
            ->take(10)
            ->get()
            ->map(fn (ActivityLog $a) => [
                'icon' => Fmt::actionIcon($a->action),
                'text' => $a->description,
                'time' => $a->created_at->copy()->timezone($tz)->locale('id')->translatedFormat('j M Y, H.i'),
                'ago'  => $a->created_at->copy()->timezone($tz)->locale('id')->diffForHumans(),
            ])->all();

        return view('user.activity', compact('items'));
    }
}
