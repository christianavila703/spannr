<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Job;

class JobController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('panel.jobs.index', [
            'showRouteRootPrefix' => 'panel',
        ]);
    }

    public function show($slug): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $job = Job::where('slug', $slug)->firstOrFail();

        return view('panel.jobs.show', [
            'job' => $job,
            'showRouteRootPrefix' => 'panel',
        ]);
    }
}
