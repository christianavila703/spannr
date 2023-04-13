<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Glossary\StoreGlossaryRequest;
use App\Http\Requests\Admin\Glossary\UpdateGlossaryRequest;
use App\Models\Glossary;
use Illuminate\Http\Request;

class GlossaryController extends Controller
{
    public function index()
    {
        return view('admin.glossary.index');
    }

    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('admin.glossary.create');
    }

    public function store(StoreGlossaryRequest $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();
        $glossary = Glossary::create($validated);
        return redirect()->route('public.content.glossary.show', $glossary->slug);
    }

    public function show($id)
    {
        //
    }

    public function edit($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $glossary = Glossary::findOrFail($id);

        if ($glossary->enableLock(auth()->user()->id) === false) {
            return redirect()->route('admin.glossary.index')->with('error', 'The entry has been temporarily locked by another user.');
        }

        return view('admin.glossary.edit', [
            'glossary' => $glossary
        ]);
    }

    public function update(UpdateGlossaryRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();
        $glossary = Glossary::findOrFail($id);

        if ($glossary->disableLock(auth()->user()->id) === false) {
            return redirect()->route('admin.glossary.index')->with('error', 'The entry has been temporarily locked by another user.');
        }

        $glossary->update($validated);

        return redirect()->route('public.content.glossary.show', $glossary->slug);
    }

    public function destroy($id)
    {
        //
    }
}
