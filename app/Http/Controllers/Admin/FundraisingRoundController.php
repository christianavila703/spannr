<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FundraisingRoundRequest;
use App\Models\FundraisingRound;
use App\Models\Organization;
use Illuminate\Http\Request;

class FundraisingRoundController extends Controller
{
    public function index()
    {
        $fundraisingRounds = FundraisingRound::with(['organization', 'investors'])
            ->get();

        return view('admin.fundraising-rounds.index', [
            'fundraisingRounds' => $fundraisingRounds
        ]);
    }

    public function create()
    {
        //
    }

    public function store(FundraisingRoundRequest $request)
    {
        $validated = $request->validated();

        $organization = Organization::findOrFail($validated['organization_id']);

        $fundraisingRound = FundraisingRound::create($validated);
        $fundraisingRound->name = ' ';
        $fundraisingRound->save();

        if (!empty($validated['investors'])) {
            $fundraisingRound->investors()->syncWithoutDetaching($validated['investors']);
            $organization->investors()->syncWithoutDetaching($validated['investors']);
        }

        return redirect()->route('organizations.show', $organization->slug)->with('success', $fundraisingRound->name . ' added!');
    }

    public function show($id)
    {
        $fundraisingRound = FundraisingRound::findOrFail($id);

        return view('admin.fundraising-rounds.show', [
            'fundraisingRound' => $fundraisingRound
        ]);
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
}
