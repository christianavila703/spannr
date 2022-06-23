<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Investor;
use App\Models\Organization;
use App\Models\Person;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('admin.events.index');
    }

    public function create(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $eventTypes = EventType::orderBy('name')->get();
        $organizations = Organization::orderBy('name')->get();
        $investors = Investor::orderBy('name')->get();
        $people = Person::orderBy('name')->get();

        return view('admin.events.create', [
            'eventTypes' => $eventTypes,
            'organizations' => $organizations,
            'investors' => $investors,
            'people' => $people,
        ]);
    }

    public function store(StoreEventRequest $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();
        $event = Event::create($validated);

        $this->syncRelationships($event, $validated, $request);

        return redirect()->route('events.index')->with('success', 'Added ' . $event->name);
    }

    public function show($slug)
    {
        $event = Event::where('slug', $slug)
            ->with([
                'organizations',
                'investors',
                'people',
                'eventTypes'
            ])->firstOrFail();

        return view('admin.events.show', [
            'event' => $event,
        ]);
    }

    public function edit($id)
    {
        $eventTypes = EventType::orderBy('name')->get();
        $organizations = Organization::orderBy('name')->get();
        $investors = Investor::orderBy('name')->get();
        $people = Person::orderBy('name')->get();

        $event = Event::with([
            'organizations',
            'investors',
            'people',
            'eventTypes'
        ])->findOrFail($id);

        return view('admin.events.edit', [
            'event' => $event,
            'eventTypes' => $eventTypes,
            'organizations' => $organizations,
            'investors' => $investors,
            'people' => $people,
        ]);
    }

    public function update(UpdateEventRequest $request, $id)
    {
        $validated = $request->validated();
        $event = Event::findOrFail($id);
        $event->update($validated);

        $this->syncRelationships($event, $validated, $request);

        return redirect()->route('events.show', $event->slug)->with('success', $event->name . ' updated!');
    }

    public function destroy($id)
    {
        //
    }

    private function syncRelationships($event, $validated, $request) {
        if (isset($validated['image'])) {
            $event->storeImage($request->file('image'));
        }

        if (isset($validated['organizations'])) {
            $event->organizations()->sync($validated['organizations']);
        }

        if (isset($validated['investors'])) {
            $event->investors()->sync($validated['investors']);
        }

        if (isset($validated['people'])) {
            $event->people()->sync($validated['people']);
        }

        if (isset($validated['event_types'])) {
            $event->eventTypes()->sync($validated['event_types']);
        }
    }
}
