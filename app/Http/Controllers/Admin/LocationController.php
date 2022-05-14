<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Investor;
use App\Models\Location;
use App\Models\Organization;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{

    public function attach($entityType, $entityId): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $entityClass = $this->matchEntityType($entityType);
        $entity = $entityClass::findOrFail($entityId);

        return view('admin.locations.add', [
            'entity' => $entity,
            'entityType' => $entityType,
        ]);
    }

    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'locationName' => 'required'
        ])->validate();

        $entityId = $request->input('entity_id');
        $relationship = $request->input('entity_type');
        $entityClass = $this->matchEntityType($relationship);
        $entity = $entityClass::findOrFail($entityId);

        $location = Location::where('name', $request->input('locationName'))->first();

        if ($location) {
            $entity->locations()->syncWithoutDetaching([$location->id]);
        } else {
            $newLocation = Location::create([
                'name' => $request->input('locationName'),
                'latitude' => $request->input('locationLatitude'),
                'longitude' => $request->input('locationLongitude')
            ]);

            $entity->locations()->syncWithoutDetaching([$newLocation->id]);
        }

        return redirect()->route($relationship . '.show', $entity->slug)->with('success', 'Location added!');

    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    private function matchEntityType($entityType) {
        return match($entityType) {
            'organizations' => Organization::class,
            'people' => Person::class,
            'investors' => Investor::class
        };
    }
}
