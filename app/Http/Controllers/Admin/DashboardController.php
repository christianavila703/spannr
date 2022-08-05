<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\FundraisingRound;
use App\Models\Investor;
use App\Models\Job;
use App\Models\Organization;
use App\Models\Person;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $organizations = Organization::all()->count();
        $people = Person::all()->count();
        $investors = Investor::all()->count();
        $jobs = Job::all()->count();
        $events = Event::all()->count();
        $fundraisingRounds = FundraisingRound::all()->count();

        return view('admin.dashboard.index', [
            'organizations' => $organizations,
            'people' => $people,
            'investors' => $investors,
            'jobs' => $jobs,
            'events' => $events,
            'fundraisingRounds' => $fundraisingRounds,
        ]);
    }
}
