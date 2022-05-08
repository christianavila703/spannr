<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrganizationRequest;
use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $organizations = Organization::with(['fundraisingRounds', 'investors'])
            ->get();

        return view('admin.organizations.index', [
            'organizations' => $organizations
        ]);
    }

    public function create(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('admin.organizations.create');
    }

    public function store(OrganizationRequest $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();
        $organization = Organization::create($validated);

        return redirect()->route('organizations.index')->with('success', 'Added ' . $organization->name);

    }

    public function show($slug): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $organization = Organization::where('slug', $slug)->firstOrFail();

        return view('admin.organizations.show', [
            'organization' => $organization
        ]);
    }

    public function edit($id): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $organization = Organization::findOrFail($id);

        return view('admin.organizations.edit', [
            'organization' => $organization
        ]);
    }

    public function update(OrganizationRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();
        $organization = Organization::findOrFail($id);
        $organization->update($validated);

        return redirect()->route('organizations.show', $organization->slug);
    }

    public function destroy($id)
    {
        //
    }
}
