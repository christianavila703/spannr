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
        return view('admin.events.index', [
            'showRouteRootPrefix' => 'admin',
        ]);
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

        return redirect()->route('admin.events.index')->with('success', 'Added ' . $event->name);
    }

    public function show($slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        return view('admin.events.show', [
            'event' => $event,
            'showRouteRootPrefix' => 'admin',
        ]);
    }

    public function edit($id) :\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        $event = Event::with([
            'organizations',
            'investors',
            'people',
            'eventTypes'
        ])->findOrFail($id);

        if ($event->enableLock(auth()->user()->id) === false) {
            return redirect()->route('admin.events.index')->with('error', 'The entry has been temporarily locked by another user.');
        }

        $eventTypes = EventType::orderBy('name')->get();
        $organizations = Organization::orderBy('name')->get();
        $investors = Investor::orderBy('name')->get();
        $people = Person::orderBy('name')->get();

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

        if ($event->disableLock(auth()->user()->id) === false) {
            return redirect()->route('admin.events.index')->with('error', 'The entry has been temporarily locked by another user.');
        }

        $event->update($validated);

        $this->syncRelationships($event, $validated, $request);

        return redirect()->route('admin.events.show', $event->slug)->with('success', $event->name . ' updated!');
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
