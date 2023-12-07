<?php

namespace App\Http\Controllers\Admin\Imports;

use App\Http\Controllers\Controller;
use App\Imports\ListingsImport;
use App\Models\ImportResult;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportListingsController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('admin.imports.listings.index');
    }

    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $requiredFields = [
            'name' => 'string',
            'type' => 'string',
            'website' => 'string',
        ];

        $optionalFields = [
            'slug' => 'string',
            'status' => 'string',
            'phone' => 'string',
            'summary' => 'string',
            'description' => 'longText',
            'insurance' => 'bool',
            'virtual' => 'bool',
            'affiliate_link' => 'string',
            'emails' => 'string (comma separated)',
            'treatments' => 'string (comma separated)',
            'address' => 'string',
        ];

        return view('admin.imports.listings.create', [
            'requiredFields' => $requiredFields,
            'optionalFields' => $optionalFields
        ]);
    }

    public function process(Request $request): \Illuminate\Http\RedirectResponse
    {
        $path = $request->file('file')->store('imports');
        Excel::import(new ListingsImport(), $path);
        return redirect()->route('admin.import.listings.index')->with('success', 'Uploaded file!');
    }

    public function show($id) {
        $importResult = ImportResult::with(['entity', 'user'])->findOrFail($id);
        return view('admin.imports.listings.show', [
            'importResult' => $importResult
        ]);
    }
}
