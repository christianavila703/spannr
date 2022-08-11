<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobCategoryController extends Controller
{
    public function index()
    {
        //
    }

    public function create() {
        return view('admin.job-categories.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:job_categories|max:255',
        ]);

        if ($validator->fails()) {
            return redirect(route('job-categories.create'))
                ->withErrors($validator)
                ->withInput();
        }

        $jobCategory = JobCategory::create($validator->validated());

        return redirect()->route('admin.jobs.index')->with('success', 'Job category ' . $jobCategory->name . ' added!');
    }

    public function update($id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
