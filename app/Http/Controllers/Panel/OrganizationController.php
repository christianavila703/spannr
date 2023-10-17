<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Organization;

class OrganizationController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('panel.organizations.index', [
            'showRouteRootPrefix' => 'panel',
        ]);
    }

    public function show($slug): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $organization = Organization::where('slug', $slug)
            ->with(['stock', 'stock.latestSnapshot'])
            ->firstOrFail();

        return view('panel.organizations.show', [
            'organization' => $organization,
            'showRouteRootPrefix' => 'panel',
        ]);
    }
}
