<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FundraisingRoundRequest;
use App\Models\FundraisingRound;
use App\Models\Investor;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FundraisingRoundController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
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

    public function store(FundraisingRoundRequest $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();
        $organization = Organization::findOrFail($validated['organization_id']);

        $fundraisingRound = new FundraisingRound($validated);
        $fundraisingRound->name = $organization->name . ' - ' . $validated['round'];
        $fundraisingRound->save();

        $logProperties = [];

        if (!empty($validated['investors'])) {
            $fundraisingRound->investors()->syncWithoutDetaching($validated['investors']);
            $organization->investors()->syncWithoutDetaching($validated['investors']);
            $logProperties['investors'] = $validated['investors'];
        }

        activity()
            ->useLog('organizations')
            ->causedBy(Auth::user())
            ->performedOn($organization)
            ->withProperties($logProperties)
            ->event('added fundraising round')
            ->log('Fundraising Round added to ' . $organization->name);

        return redirect()->route('admin.organizations.show', $organization->slug)->with('success', $fundraisingRound->name . ' added!');
    }

    public function show($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $fundraisingRound = FundraisingRound::with(['organization', 'investors'])->findOrFail($id);

        return view('admin.fundraising-rounds.show', [
            'fundraisingRound' => $fundraisingRound
        ]);
    }

    public function edit($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        $fundraisingRound = FundraisingRound::with(['investors'])->findOrFail($id);

        if ($fundraisingRound->enableLock(auth()->user()->id) === false) {
            return redirect()->route('admin.fundraising-rounds.index')->with('error', 'The entry has been temporarily locked by another user.');
        }

        $allInvestors = Investor::orderBy('name')->get();
        $currentInvestors = $fundraisingRound->investors->pluck('id')->toArray();

        return view('admin.fundraising-rounds.edit', [
            'fundraisingRound' => $fundraisingRound,
            'allInvestors' => $allInvestors,
            'currentInvestors' => $currentInvestors
        ]);
    }

    public function update(FundraisingRoundRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        $fundraisingRound = FundraisingRound::findOrFail($id);

        if ($fundraisingRound->disableLock(auth()->user()->id) === false) {
            return redirect()->route('admin.fundraising-rounds.index')->with('error', 'The entry has been temporarily locked by another user.');
        }

        $validated = $request->validated();
        $organization = Organization::findOrFail($validated['organization_id']);
        $fundraisingRound->name = $organization->name . ' - ' . $validated['round'];
        $fundraisingRound->update($validated);
        $fundraisingRound->save();

        if (!empty($validated['investors'])) {
            $fundraisingRound->investors()->sync($validated['investors']);
            $organization->investors()->syncWithoutDetaching($validated['investors']);
        }

        return redirect()->route('admin.fundraising-rounds.show', $fundraisingRound->id)->with('success', $fundraisingRound->name . ' updated!');
    }

    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        $fundraisingRound = FundraisingRound::findOrFail($id);
        $message = $fundraisingRound->name . ' was deleted';
        $fundraisingRound->delete();
        return redirect()->route('admin.fundraising-rounds.index')->with('success', $message);
    }
}
