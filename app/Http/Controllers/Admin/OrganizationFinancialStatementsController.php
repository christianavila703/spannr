<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;

class OrganizationFinancialStatementsController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $organizations = Organization::whereHas('financialStatements')->orderBy('name')->get();

        return view('admin.organizations.financial-statements.index', [
            'organizations' => $organizations,
        ]);
    }

    public function show($slug): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $organization = Organization::with(['financialStatements' => function ($query) {
                $query->orderBy('end_date', 'desc')->orderBy('start_date', 'asc');
            }])
            ->whereHas('financialStatements')
            ->where('slug', $slug)
            ->firstOrFail();

        return view('admin.organizations.financial-statements.show', [
            'organization' => $organization,
        ]);
    }
}
