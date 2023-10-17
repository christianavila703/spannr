<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Locations\StoreLocationRequest;
use App\Http\Requests\Admin\Locations\UpdateLocationRequest;
use App\Models\Event;
use App\Models\Investor;
use App\Models\Job;
use App\Models\Location;
use App\Models\Organization;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    public function index(): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('admin.locations.index', [
            'showRouteRootPrefix' => 'admin'
        ]);
    }

    public function create()
    {
        return view('admin.locations.create');
    }

    public function store(StoreLocationRequest $request)
    {
        $location = Location::create($request->validated());

        foreach (['organizations', 'investors', 'people', 'jobs'] as $relationName) {
            $location->{$relationName}()->sync($request->input($relationName));
        }

        return redirect()->route('admin.locations.index')->with('success', 'Location "' . $location->name . '" created!');
    }

    public function show($slug): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $location = Location::where('slug', $slug)->firstOrFail();

        return view('admin.locations.show', [
            'location' => $location,
            'showRouteRootPrefix' => 'admin'
        ]);
    }

    public function edit($slug)
    {
        $location = Location::with([
                'organizations',
                'investors',
                'people',
                'jobs',
                'jobApplications'
            ])
            ->where('slug', $slug)
            ->firstOrFail();

        if ($location->enableLock(auth()->user()->id) === false) {
            return redirect()->back()->with('error', 'The entry has been temporarily locked by another user.');
        }

        return view('admin.locations.edit', ['location' => $location]);
    }

    public function update(UpdateLocationRequest $request, $slug)
    {
        $location = Location::where('slug', $slug)->firstOrFail();

        if ($location->disableLock(auth()->user()->id) === false) {
            return redirect()->back()->with('error', 'The entry has been temporarily locked by another user.');
        }

        $location->fill($request->validated());
        $location->save();

        foreach (['organizations', 'investors', 'people', 'jobs'] as $relationName) {
            $location->{$relationName}()->sync($request->input($relationName));
        }

        return redirect()->route('admin.locations.index')->with('success', 'Location "' . $location->name . '" updated!');
    }

    public function destroy($id)
    {
        $location = Location::findOrFail($id);
        $name = $location->name;

        $location->delete();

        return redirect()->route('admin.locations.index')->with('success', 'Location "' . $name . '" deleted!');
    }

    public function attach($entityType, $entityId): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $entityClass = $this->matchEntityType($entityType);
        $entity = $entityClass::findOrFail($entityId);

        return view('admin.locations.add', [
            'entity' => $entity,
            'entityType' => $entityType,
        ]);
    }

    public function createFromEntity(Request $request)
    {
        Validator::make($request->all(), [
            'entity_id' => 'required',
            'entity_type' => 'required',
            'name' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ])->validate();

        $entityId = $request->input('entity_id');
        $relationship = $request->input('entity_type');
        $entityClass = $this->matchEntityType($relationship);
        $entity = $entityClass::findOrFail($entityId);

        $location = Location::where('name', $request->input('name'))->first();

        if (!$location) {
            $location = Location::create([
                'name' => $request->input('name'),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude')
            ]);
        }

        $entity->locations()->syncWithoutDetaching([$location->id]);

        return redirect()->route('admin.' . $relationship . '.show', $entity->slug)->with('success', 'Location added!');
    }

    private function matchEntityType($entityType) {
        return match($entityType) {
            'organizations' => Organization::class,
            'people' => Person::class,
            'investors' => Investor::class,
            'events' => Event::class,
            'jobs' => Job::class,
        };
    }
}
