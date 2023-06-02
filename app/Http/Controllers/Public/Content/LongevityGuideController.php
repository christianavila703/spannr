<?php

namespace App\Http\Controllers\Public\Content;

use App\Http\Controllers\Controller;
use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Spatie\SchemaOrg\Graph;

class LongevityGuideController extends Controller
{
    public function __construct() {
       View::share ('showFooter', true);
       View::share ('showHeader', true);
    }

    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $content = Content::where('slug', 'guide-to-longevity')->with(['children'])->firstOrFail();

        $graph = new Graph();

        // TODO: Graph

        return view('public.content.longevity-guide.index', [
            'content' => $content,
            'graph' => $graph,
        ]);
    }
}
