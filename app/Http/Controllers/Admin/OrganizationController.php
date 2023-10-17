<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Organization\UpdateTagsRequest;
use App\Http\Requests\OrganizationRequest;
use App\Models\Investor;
use App\Models\Organization;
use App\Models\Person;
use App\Models\Location;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OrganizationController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('admin.organizations.index', [
            'showRouteRootPrefix' => 'admin'
        ]);
    }

    public function create(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $parentTags = Tag::firstLevel()
            ->whereJsonContains('entity_types', Organization::class)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        return view('admin.organizations.create', [
            'parentTags' => $parentTags,
        ]);
    }

    public function store(OrganizationRequest $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();
        $organization = Organization::create($validated);

        if (isset($validated['image'])) {
            $organization->storeImage($request->file('image'));
        }

        if ($organization->longevity == 1) {
            $tags = [$validated['parent_tag']];

            if (isset($validated['child_tags'])) {
                $tags = array_merge($tags, $validated['child_tags']);
            }

            $organization->tags()->sync($tags);
        }

        return redirect()->route('admin.organizations.index')->with('success', 'Added ' . $organization->name);
    }

    public function show($slug): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $organization = Organization::where('slug', $slug)
            ->with(['stock', 'stock.latestSnapshot'])
            ->firstOrFail();

        $investors = Investor::orderBy('name')->get();
        $people = Person::orderBy('name')->get();

        $currentTags = $organization->tags->pluck('id')->toArray();
        $parentTags = Tag::firstLevel()
            ->whereJsonContains('entity_types', Organization::class)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
        $childTags = [];

        foreach ($parentTags as $id => $name) {
            if (in_array($id, $currentTags)) {
                $childTags = Tag::where('parent_id', $id)->orderBy('name')->pluck('name', 'id')->toArray();
                break;
            }
        }

        $hasStockChart = ($organization->stock('stock')->count() > 0);
        $latestSnapshot = $organization->stock->latestSnapshot ?? null;

        return view('admin.organizations.show', [
            'organization' => $organization,
            'investors' => $investors,
            'people' => $people,
            'currentTags' => $currentTags,
            'parentTags' => $parentTags,
            'childTags' => $childTags,
            'hasStockChart' => $hasStockChart,
            'latestSnapshot' => $latestSnapshot,
            'showRouteRootPrefix' => 'admin',
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

    public function edit($id): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        $organization = Organization::with([
            'investors',
            'people',
            'fundraisingRounds',
            'tags',
        ])->findOrFail($id);

        if ($organization->enableLock(auth()->user()->id) === false) {
            return redirect()->route('admin.organizations.index')->with('error', 'The entry has been temporarily locked by another user.');
        }

        $currentTags = $organization->tags->pluck('id')->toArray();
        $parentTags = Tag::firstLevel()
            ->whereJsonContains('entity_types', Organization::class)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
        $childTags = [];

        foreach ($parentTags as $id => $name) {
            if (in_array($id, $currentTags)) {
                $childTags = Tag::where('parent_id', $id)->orderBy('name')->pluck('name', 'id')->toArray();
                break;
            }
        }

        return view('admin.organizations.edit', [
            'organization' => $organization,
            'currentTags' => $currentTags,
            'parentTags' => $parentTags,
            'childTags' => $childTags,
        ]);
    }

    public function update(OrganizationRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();
        $organization = Organization::findOrFail($id);

        if ($organization->disableLock(auth()->user()->id) === false) {
            return redirect()->route('admin.organizations.index')->with('error', 'The entry has been temporarily locked by another user.');
        }

        $organization->update($validated);

        if (isset($validated['image'])) {
            $organization->storeImage($request->file('image'));
        }

        if ($request->input('remove_stock') && $organization->stock->indexed === 0) {
            $organization->stock()->delete();
        }

        if ($organization->longevity == 1) {
            $tags = [$validated['parent_tag']];

            if (isset($validated['child_tags'])) {
                $tags = array_merge($tags, $validated['child_tags']);
            }

            $organization->tags()->sync($tags);
        } else {
            $organization->tags()->detach();
        }

        return redirect()->route('admin.organizations.show', $organization->slug)->with('success', $organization->name . ' updated!');
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

        return redirect()->route('admin.organizations.show', $organization->slug)->with('success', 'Investors added!');
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

        return redirect()->route('admin.organizations.show', $organization->slug)->with('success', 'People added!');
    }

    public function updateTags(UpdateTagsRequest $request, $id)
    {
        $organization = Organization::findOrFail($id);
        $validated = $request->validated();

        $organization->longevity = $validated['longevity'];
        $organization->save();

        if ($organization->longevity == 1) {
            $tags = [$validated['parent_tag']];

            if (isset($validated['child_tags'])) {
                $tags = array_merge($tags, $validated['child_tags']);
            }

            $organization->tags()->sync($tags);
        } else {
            $organization->tags()->detach();
        }

        return redirect()->route('admin.organizations.show', $organization->slug)->with('success', 'Tags updated successfully!');
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
