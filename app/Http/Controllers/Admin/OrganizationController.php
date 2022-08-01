<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrganizationRequest;
use App\Models\Investor;
use App\Models\Organization;
use App\Models\Person;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OrganizationController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('admin.organizations.index');
    }

    public function create(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('admin.organizations.create');
    }

    public function store(OrganizationRequest $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();
        $organization = Organization::create($validated);

        if (isset($validated['image'])) {
            $organization->storeImage($request->file('image'));
        }

        return redirect()->route('organizations.index')->with('success', 'Added ' . $organization->name);

    }

    public function show($slug): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $organization = Organization::where('slug', $slug)
            ->with(['stock.latestSnapshot'])
            ->firstOrFail();

        $investors = Investor::orderBy('name')->get();
        $people = Person::orderBy('name')->get();

        $hasStockChart = ($organization->stock('stock')->count() > 0);
        $latestSnapshot = $organization->stock->latestSnapshot ?? null;

        return view('admin.organizations.show', [
            'organization' => $organization,
            'investors' => $investors,
            'people' => $people,
            'hasStockChart' => $hasStockChart,
            'latestSnapshot' => $latestSnapshot
        ]);
    }

    public function stockData($slug) {
        $organization = Organization::where('slug', $slug)->firstOrFail();
        $chartData = json_decode($organization->stock->data, true);

        $chartLabels = [];
        $valuesClose = [];
        $valuesOpen = [];
        $valuesHigh = [];
        $valuesLow = [];

        foreach ($chartData as $data) {
            $chartLabels[] = Carbon::createFromTimestampMsUTC($data['date'])->format('M d, Y');

            $valuesClose[] = [
                'x' => $data['date'],
                'y' => $data['close']
            ];

            $valuesOpen[] = [
                'x' => $data['date'],
                'y' => $data['open']
            ];

            $valuesHigh[] = [
                'x' => $data['date'],
                'y' => $data['high']
            ];

            $valuesLow[] = [
                'x' => $data['date'],
                'y' => $data['low']
            ];
        }

        return json_encode([
            'chartLabels' => $chartLabels,
            'valuesClose' => $valuesClose,
            'valuesOpen' => $valuesOpen,
            'valuesHigh' => $valuesHigh,
            'valuesLow' => $valuesLow
        ], true);

    }

    public function stockChart($slug) {
        $organization = Organization::where('slug', $slug)->firstOrFail();
        return view('admin.organizations.stock-chart', [
            'slug' => $organization->slug
        ]);
    }

    public function edit($id): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $organization = Organization::with([
            'investors',
            'people',
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

        if (isset($validated['image'])) {
            $organization->storeImage($request->file('image'));
        }

        return redirect()->route('organizations.show', $organization->slug)->with('success', $organization->name . ' updated!');
    }

    public function addInvestors(Request $request): \Illuminate\Http\RedirectResponse
    {
        $investorIds = $request->input('investors');
        $organization = Organization::findOrFail($request->input('organization_id'));
        $organization->investors()->syncWithoutDetaching($investorIds);

        // Log Activity
        activity()
            ->useLog('organizations')
            ->causedBy(Auth::user())
            ->performedOn($organization)
            ->withProperties(['investors' => $investorIds])
            ->event('added investors')
            ->log('Investors added to ' . $organization->name);

        return redirect()->route('organizations.show', $organization->slug)->with('success', 'Investors added!');
    }

    public function addPeople(Request $request): \Illuminate\Http\RedirectResponse
    {
        $founderIds = $request->input('founders');
        $teamIds = $request->input('team');
        $organization = Organization::findOrFail($request->input('organization_id'));

        $logProperties = [];

        if (!empty($founderIds)) {
            $founderSync = array_fill_keys($founderIds, ['role' => Person::ORGANIZATION_ROLE_FOUNDER]);
            $organization->founders()->syncWithoutDetaching($founderSync);
            $logProperties['founders'] = $founderIds;

            // TODO: dispatch job for the founders to add activity log for those person records!
        }

        if (!empty($teamIds)) {
            $teamSync = array_fill_keys($teamIds, ['role' => Person::ORGANIZATION_ROLE_TEAM]);
            $organization->team()->syncWithoutDetaching($teamSync);
            $logProperties['team'] = $teamIds;

            // TODO: dispatch job for the team to add activity log for those person records!
        }

        activity()
            ->useLog('organizations')
            ->causedBy(Auth::user())
            ->performedOn($organization)
            ->withProperties($logProperties)
            ->event('added people')
            ->log('People added to ' . $organization->name);

        return redirect()->route('organizations.show', $organization->slug)->with('success', 'People added!');
    }

    public function people($slug): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $organization = Organization::with('people')->where('slug', $slug)->firstOrFail();
        $people = Person::orderBy('name')->get([
            'id AS value',
            'name AS label'
        ])->toJson();

        return view('admin.organizations.manage-people', [
            'organization' => $organization,
            'people' => $people,
        ]);
    }

    public function savePerson(Request $request): \Illuminate\Http\JsonResponse
    {
        $organizationId = $request->input('organization');
        $personId = $request->input('person');
        $role = $request->input('role');

        if ($organizationId AND $personId AND $role) {
            $organization = Organization::findOrFail($organizationId);
            $organization->people()->detach($personId);
            $organization->people()->attach($personId, ['role' => $role]);

            return response()->json([
                'status' => 'success',
                'message' => 'Updated!'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'You must include the person and role'
            ]);
        }
    }

    public function destroy($id)
    {
        //
    }
}
