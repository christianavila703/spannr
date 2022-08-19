<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\WordPress\SyncArticles;
use Illuminate\Http\Request;

class ArticlesController extends Controller
{
    public function sync(): \Illuminate\Http\RedirectResponse
    {
        SyncArticles::dispatch();
        return redirect()->route('admin.home')->with('success', 'Articles are syncing!');
    }
}
