<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PersonRequest;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class PersonController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('admin.people.index');
    }

    public function create(): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('admin.people.create');
    }

    public function store(PersonRequest $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();
        $person = Person::create($validated);

        if (isset($validated['image'])) {
            $person->storeImage($request->file('image'));
        }

        return redirect()->route('admin.people.index')->with('success', 'Added ' . $person->name);
    }

    public function show($slug): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $person = Person::where('slug', $slug)->firstOrFail();

        return view('admin.people.show', [
            'person' => $person
        ]);
    }

    public function edit($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        $person = Person::findOrFail($id);

        if ($person->enableLock(auth()->user()->id) === false) {
            return redirect()->route('admin.people.index')->with('error', 'The entry has been temporarily locked by another user.');
        }

        return view('admin.people.edit', [
            'person' => $person
        ]);
    }

    public function update(PersonRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();
        $person = Person::findOrFail($id);

        if ($person->disableLock(auth()->user()->id) === false) {
            return redirect()->route('admin.people.index')->with('error', 'The entry has been temporarily locked by another user.');
        }

        $person->update($validated);

        if (isset($validated['image'])) {
            $person->storeImage($request->file('image'));
        }

        return redirect()->route('admin.people.show', $person->slug);
    }

    public function destroy($id)
    {
        //
    }
}
