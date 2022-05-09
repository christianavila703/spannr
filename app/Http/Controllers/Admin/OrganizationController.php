<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrganizationRequest;
use App\Models\Investor;
use App\Models\Organization;
use App\Models\Person;
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

        $investors = Investor::orderBy('name')->get();
        $people = Person::orderBy('name')->get();

        return view('admin.organizations.show', [
            'organization' => $organization,
            'investors' => $investors,
            'people' => $people
        ]);
    }

    public function edit($id): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $organization = Organization::with([
            'investors',
            'founders',
            'team',
            'fundraisingRounds'
        ])->findOrFail($id);

        return view('admin.organizations.edit', [
            'organization' => $organization
        ]);
    }

    public function update(OrganizationRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();
        $organization = Organization::findOrFail($id);
        $organization->update($validated);

        return redirect()->route('organizations.show', $organization->slug)->with('success', $organization->name . ' updated!');
    }

    public function addInvestors(Request $request): \Illuminate\Http\RedirectResponse
    {
        $investorIds = $request->input('investors');
        $organization = Organization::findOrFail($request->input('organization_id'));
        $organization->investors()->syncWithoutDetaching($investorIds);
        return redirect()->route('organizations.show', $organization->slug)->with('success', 'Investors added!');
    }

    public function addPeople(Request $request): \Illuminate\Http\RedirectResponse
    {
        $founderIds = $request->input('founders');
        $teamIds = $request->input('team');
        $organization = Organization::findOrFail($request->input('organization_id'));

        if (!empty($founderIds)) {
            $founderSync = array_fill_keys($founderIds, ['role' => Person::ORGANIZATION_ROLE_FOUNDER]);
            $organization->founders()->syncWithoutDetaching($founderSync);
        }

        if (!empty($teamIds)) {
            $teamSync = array_fill_keys($teamIds, ['role' => Person::ORGANIZATION_ROLE_TEAM]);
            $organization->team()->syncWithoutDetaching($teamSync);
        }

        return redirect()->route('organizations.show', $organization->slug)->with('success', 'People added!');
    }

    public function destroy($id)
    {
        //
    }
}
