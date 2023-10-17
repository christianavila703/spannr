<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvestorPersonRequest;
use App\Http\Requests\InvestorRequest;
use App\Models\Investor;
use App\Models\Organization;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class InvestorController extends Controller
{
    public function index(): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('admin.investors.index', [
            'showRouteRootPrefix' => 'admin'
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

        return redirect()->route('admin.investors.index')->with('success', 'Added ' . $investor->name);
    }

    public function createFromEntity(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'entity_type' => 'required',
            'entity_id' => 'required',
            'type' => 'required'
        ]);

        if ($validated['entity_type'] === Organization::class) {
            $entity = Organization::findOrFail($validated['entity_id']);
        } elseif ($validated['entity_type'] === Person::class) {
            $entity = Person::findOrFail($validated['entity_id']);
        } else {
            $message = 'Unable to create investor from entity because entity was unexpected: ' . $validated['entity_type'];
            Log::warning($message);
            return redirect()->back()->with('error', $message);
        }

        try {
            $investor = Investor::create([
                'name' => $entity->name,
                'ownable_type' => $validated['entity_type'],
                'ownable_id' => $validated['entity_id'],
                'type' => $validated['type']
            ]);

            return redirect()->route('admin.investors.show', $investor->slug);
        } catch(\Throwable $throwable) {
            Log::critical('Unable to create Investor from entity' . "\n" . print_r($request->input(), true) . "\n" . $throwable->getMessage());
            return redirect()->back()->with('error', 'Unable to create Investor from entity because exception. Tell dev team please.');
        }
    }

    public function show($slug)
    {
        $investor = Investor::where('slug', $slug)->firstOrFail();

        return view('admin.investors.show', [
            'investor' => $investor,
            'showRouteRootPrefix' => 'admin',
        ]);
    }

    public function edit($id): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        $investor = Investor::findOrFail($id);

        if ($investor->enableLock(auth()->user()->id) === false) {
            return redirect()->route('admin.investors.index')->with('error', 'The entry has been temporarily locked by another user.');
        }

        return view('admin.investors.edit', [
            'investor' => $investor
        ]);
    }

    public function update(InvestorRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();
        $investor = Investor::findOrFail($id);

        if ($investor->disableLock(auth()->user()->id) === false) {
            return redirect()->route('admin.investors.index')->with('error', 'The entry has been temporarily locked by another user.');
        }

        $investor->update($validated);

        if (isset($validated['image'])) {
            $investor->storeImage($request->file('image'));
        }

        return redirect()->route('admin.investors.show', $investor->slug);
    }

    public function destroy($id)
    {
        //
    }

    public function people($slug): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $investor = Investor::with('people')->where('slug', $slug)->firstOrFail();
        $people = Person::orderBy('name')->get([
            'id AS value',
            'name AS label'
        ])->toJson();

        return view('admin.investors.manage-people', [
            'investor' => $investor,
            'people' => $people,
        ]);
    }

    public function savePerson(Request $request): \Illuminate\Http\JsonResponse
    {
        $investorId = $request->input('investor');
        $personId = $request->input('person');
        $role = $request->input('role');

        if ($investorId AND $personId AND $role) {
            $investor = Investor::findOrFail($investorId);
            $investor->people()->detach($personId);
            $investor->people()->attach($personId, ['role' => $role]);

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
}
