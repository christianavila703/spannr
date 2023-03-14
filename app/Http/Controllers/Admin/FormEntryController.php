<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\FormNotifications\ResubmitMailchimpApplicableForms;
use Illuminate\Http\Request;

class FormEntryController extends Controller
{
    public function index()
    {
        return view('admin.form-entries.index');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
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

    public function resubmitMailchimpApplicableForms()
    {
        ResubmitMailchimpApplicableForms::dispatch();

        return redirect()->back()->with('success', 'Mailchimp applicable Form Entries with an empty response will be resubmitted.');
    }
}
