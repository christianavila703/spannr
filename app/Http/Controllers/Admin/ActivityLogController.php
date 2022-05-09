<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $activities = Activity::orderBy('created_at', 'desc')->get();

        return view('admin.activity-log.index', [
            'activities' => $activities
        ]);
    }
}
