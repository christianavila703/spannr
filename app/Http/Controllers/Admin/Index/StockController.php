<?php

namespace App\Http\Controllers\Admin\Index;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class StockController extends Controller
{
    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $inactiveStatus = Stock::STATUS_INACTIVE;
        $organizations = Organization::doesntHave('stock')->orderBy('name')->pluck('name', 'id');

        return view('admin.longevity-index.stocks.create', [
            'inactiveStatus' => $inactiveStatus,
            'organizations' => $organizations,
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $organization = Organization::findOrFail($request->input('organization_id'));

        $ticker = $request->input('ticker');

        try {
            Stock::create([
                'name' => $organization->name,
                'status' => $request->input('status', Stock::STATUS_ACTIVE),
                'indexed' => $request->input('indexed', 0),
                'slug' => Str::slug($ticker),
                'ticker' => $ticker,
                'organization_id' => $organization->id,
                'date_added' => $request->input('date_added') ?? Carbon::now()
            ]);
        } catch (Throwable $throwable) {
            return redirect()->back()->with('error', $throwable->getMessage());
        }

        if ($request->input('redirect')) {
            $redirectRoute = 'admin.organizations.index';
        } else {
            $redirectRoute = 'admin.longevity-index.all';
        }

        return redirect()->route($redirectRoute)->with('success', 'Successfully added Stock to the ' . $organization->name . '!');
    }

    public function edit($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $stock = Stock::findOrFail($id);

        return view('admin.longevity-index.stocks.edit', [
            'stock' => $stock
        ]);
    }

    public function update($id, Request $request) {
        $stock = Stock::findOrFail($id);
        $fields = $request->input();
        $stock->update($fields);
        return redirect()->route('admin.longevity-index.all')->with('success', 'Stock successfully updated for ' . $stock->name . '!');
    }
}
