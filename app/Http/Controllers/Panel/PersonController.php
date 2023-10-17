<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Person;

class PersonController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('panel.people.index', [
            'showRouteRootPrefix' => 'panel',
        ]);
    }

    public function show($slug): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $person = Person::where('slug', $slug)->firstOrFail();

        return view('panel.people.show', [
            'person' => $person,
            'showRouteRootPrefix' => 'panel',
        ]);
    }
}
