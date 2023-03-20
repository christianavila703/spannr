<?php

namespace App\Http\Controllers\Public\Content;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class JobsController extends Controller
{
    public function __construct() {
       View::share ('showFooter', true);
       View::share ('showHeader', true);
    }

    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $jobsCount = Job::open()->count();
        return view('public.content.jobs.index', [
            'jobsCount' => $jobsCount
        ]);
    }

    public function submit(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('public.content.jobs.submit');
    }

    public function apply($slug): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $job = Job::where('slug', $slug)->firstOrFail();
        return view('public.content.jobs.apply', [
            'job' => $job
        ]);
    }
}
