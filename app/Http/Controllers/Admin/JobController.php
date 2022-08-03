<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateJobRequest;
use App\Http\Requests\StoreJobRequest;
use App\Models\Investor;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\Organization;
use Illuminate\Http\Request;
use Laravel\Scout\Scout;

class JobController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('admin.jobs.index');
    }

    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $jobCategories = JobCategory::all();

        $ownerTypes = collect([
            'organization' => Organization::class,
            'investor' => Investor::class
        ]);

        $ownerLists = [
            'organization' => Organization::all(),
            'investor' => Investor::all()
        ];

        return view('admin.jobs.create', [
            'jobCategories' => $jobCategories,
            'ownerTypes' => $ownerTypes,
            'ownerLists' => $ownerLists
        ]);
    }

    public function store(StoreJobRequest $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();

        $validated['owner_type'] = Organization::class;

        $job = Job::create($validated);

        $job->saveTrixRichText($request->input('job-trixFields'));

        return redirect()->route('admin.jobs.index')->with('success', 'Added ' . $job->name);
    }

    public function show($slug): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $job = Job::where('slug', $slug)->firstOrFail();

        return view('admin.jobs.show', [
            'job' => $job
        ]);
    }

    public function edit($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $jobCategories = JobCategory::all();

        $ownerTypes = collect([
            'organization' => Organization::class,
            'investor' => Investor::class
        ]);

        $ownerLists = [
            'organization' => Organization::all(),
            'investor' => Investor::all()
        ];

        $job = Job::with([
            'owner',
            'category'
        ])->findOrFail($id);

        return view('admin.jobs.edit', [
            'job' => $job,
            'jobCategories' => $jobCategories,
            'ownerTypes' => $ownerTypes,
            'ownerLists' => $ownerLists
        ]);
    }

    public function update(UpdateJobRequest $request, $id)
    {
        $validated = $request->validated();
        $job = Job::findOrFail($id);

        $job->update($validated);

        $job->saveTrixRichText($request->input('job-trixFields'));

        return redirect()->route('admin.jobs.show', $job->slug)->with('success', $job->name . ' updated!');
    }

    public function destroy($id)
    {
        //
    }
}
