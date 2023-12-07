<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class MarketplaceController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('admin.marketplace.index');
    }
}
