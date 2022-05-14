<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvestorRequest;
use App\Models\Investor;
use Illuminate\Http\Request;

class InvestorController extends Controller
{
    public function index(): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $investors = Investor::with(['fundraisingRounds'])
            ->get();

        return view('admin.investors.index', [
            'investors' => $investors
        ]);
    }

    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('admin.investors.create');
    }

    public function store(InvestorRequest $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();
        $investor = Investor::create($validated);

        if (isset($validated['image'])) {
            $investor->storeImage($request->file('image'));
        }

        return redirect()->route('investors.index')->with('success', 'Added ' . $investor->name);
    }

    public function show($slug)
    {
        $investor = Investor::where('slug', $slug)->firstOrFail();

        return view('admin.investors.show', [
            'investor' => $investor
        ]);
    }

    public function edit($id): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $investor = Investor::findOrFail($id);

        return view('admin.investors.edit', [
            'investor' => $investor
        ]);
    }

    public function update(InvestorRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();
        $investor = Investor::findOrFail($id);
        $investor->update($validated);

        if (isset($validated['image'])) {
            $investor->storeImage($request->file('image'));
        }

        return redirect()->route('investors.show', $investor->slug);
    }

    public function destroy($id)
    {
        //
    }
}
