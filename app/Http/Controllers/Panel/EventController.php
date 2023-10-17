<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Event;

class EventController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('panel.events.index', [
            'showRouteRootPrefix' => 'panel',
        ]);
    }

    public function show($slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        return view('panel.events.show', [
            'event' => $event,
            'showRouteRootPrefix' => 'panel',
        ]);
    }
}
