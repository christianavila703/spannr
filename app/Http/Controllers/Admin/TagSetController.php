<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TagSet;
use Illuminate\Http\Request;

class TagSetController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('admin.tag-sets.index');
    }

    public function show($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $tagSet = TagSet::findOrFail($id);
        return view('admin.tag-sets.show', [
            'tagSet' => $tagSet
        ]);
    }
}
