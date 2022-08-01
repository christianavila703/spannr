<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class OrganizationsController extends Controller
{
    public function index() {
        return Organization::first();
    }

    public function stockList() {
        return Organization::whereHas('stock', function (Builder $query) {
                $query->active();
            })
            ->with(['stock', 'stock.latestSnapshot', 'locations'])
            ->orderBy('name')
            ->get();
    }

    public function show($id) {
        return Organization::findOrFail($id);
    }

    public function stockData($slug) {
        $organization = Organization::where('slug', $slug)->firstOrFail();
        return $organization->stock->data;
    }
}
