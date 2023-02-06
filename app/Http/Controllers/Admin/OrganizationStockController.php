<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrganizationStockController extends Controller
{
    public function create()
    {
        $organizations = Organization::doesntHave('stock')
            ->where('type', Organization::TYPE_PUBLIC)
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('admin.organizations.stock.create', [
            'organizations' => $organizations
        ]);
    }

    public function add($slug)
    {
        $organization = Organization::where('slug', $slug)->firstOrFail();

        return view('admin.organizations.stock.create', [
            'organization' => $organization,
        ]);
    }

    public function store(Request $request)
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

        return redirect()->route('admin.organizations.show', $organization->slug)->with('success', 'Successfully added stock to the ' . $organization->name . '!');
    }

    public function edit($id)
    {
        $stock = Stock::with('organization')->findOrFail($id);

        return view('admin.organizations.stock.edit', [
            'stock' => $stock,
        ]);
    }

    public function update($id, Request $request)
    {
        $stock = Stock::with('organization')->findOrFail($id);
        $fields = $request->input();
        $stock->update($fields);

        return redirect()->route('admin.organizations.show', $stock->organization->slug)->with('success', 'Stock successfully updated for ' . $stock->name . '!');
    }

    public function destroy($id)
    {
        $stock = Stock::findOrFail($id);
        $stockName = $stock->name;
        $stock->delete();

        return redirect()->route('admin.organizations.index')->with('success', 'Stock ' . $stockName . ' removed!');
    }
}
