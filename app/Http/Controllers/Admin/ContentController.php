<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Content\StoreContentRequest;
use App\Http\Requests\Admin\Content\UpdateContentRequest;
use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ContentController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $content = Content::all();
        $pageSlugs = Content::PAGES;

        return view('admin.content.index', [
            'content' => $content,
            'pageSlugs' => $pageSlugs
        ]);
    }

    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $parentsUrl = route('admin.json.content.parents');
        $tagUrl = route('admin.json.tags');

        return view('admin.content.create', [
            'parentsUrl' => $parentsUrl,
            'tagUrl' => $tagUrl
        ]);
    }

    public function store(StoreContentRequest $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();
        unset($validated['tags']);
        $content = Content::create($validated);
        $content->tags()->sync($request->input('tags'));
        return redirect()->route('admin.content.index')->with('success', 'Successfully created ' . $content->name . '!');
    }

    public function show($id)
    {
        //
    }

    public function edit($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $content = Content::findOrFail($id);
        $currentTags = $content->tags()->pluck('id')->toArray();

        $tagUrl = route('admin.json.tags');

        if (!empty($currentTags)) {
            $tagUrl .= '?default=' . implode(',', $currentTags);
        }

        $parentsUrl = route('admin.json.content.parents');

        if ($content->parent_id) {
            $parentsUrl .= '?default=' . $content->parent_id;
        }

        return view('admin.content.edit', [
            'content' => $content,
            'tagUrl' => $tagUrl,
            'parentsUrl' => $parentsUrl
        ]);
    }

    public function update(UpdateContentRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();
        unset($validated['tags']);
        $content = Content::findOrFail($id);
        $content->update($validated);
        $content->tags()->sync($request->input('tags'));

        return redirect()->route('admin.content.index')->with('success', 'Updated ' . $content->name);
    }

    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        $content = Content::findOrFail($id);
        $status = 'Successfully deleted ' . $content->name . '!';
        $content->delete();
        return redirect()->route('admin.content.index')->with('success', $status);
    }
}
