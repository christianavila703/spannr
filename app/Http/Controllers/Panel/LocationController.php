<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Location;

class LocationController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('panel.locations.index', [
            'showRouteRootPrefix' => 'panel',
        ]);
    }

    public function show($slug): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $location = Location::where('slug', $slug)->firstOrFail();

        return view('panel.locations.show', [
            'location' => $location,
            'showRouteRootPrefix' => 'panel',
        ]);
    }
}
