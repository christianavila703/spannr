<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TagDetachRequest;
use App\Http\Requests\TagRequest;
use App\Models\Article;
use App\Models\Content;
use App\Models\Event;
use App\Models\Investor;
use App\Models\Job;
use App\Models\Organization;
use App\Models\Person;
use App\Models\Tag;
use Illuminate\Http\Request;
use Throwable;

class TagController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $tags = Tag::firstLevel()
            ->with(['children'])
            ->orderBy('parent_id')
            ->orderBy('name')
            ->get();

        $parentTags = Tag::orderBy('name')->canHaveKids()->get();

        return view('admin.tags.index', [
            'tags' => $tags,
            'parentTags' => $parentTags
        ]);
    }

    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $parentTags = Tag::orderBy('name')->canHaveKids()->get();

        return view('admin.tags.create', [
            'parentTags' => $parentTags
        ]);
    }

    public function store(TagRequest $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();
        $validated['depth'] = $this->getDepthValue($validated['parent_id']);

        if ($validated['parent_id']) {
            $validated['entity_types'] = null;
        }

        $tag = Tag::create($validated);

        return redirect()->route('admin.tags.index')->with('success', 'Successfully added ' . $tag->name);
    }

    public function show($slug): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $tag = Tag::where('slug', $slug)
            ->with([
                'children',
                'articles',
                'content',
                'events',
                'investors',
                'jobs',
                'organizations',
                'people'
            ])
            ->firstOrFail();

        return view('admin.tags.show', [
            'tag' => $tag
        ]);
    }

    public function edit($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $tag = Tag::findOrFail($id);
        $parentTags = Tag::orderBy('name')->canHaveKids()->get();

        return view('admin.tags.edit', [
            'tag' => $tag,
            'parentTags' => $parentTags
        ]);
    }

    public function update(TagRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        $tag = Tag::findOrFail($id);
        $validated = $request->validated();
        $validated['depth'] = $this->getDepthValue($validated['parent_id']);

        if ($validated['parent_id']) {
            $validated['entity_types'] = null;
        }

        $tag->update($validated);

        return redirect()->route('admin.tags.show', $tag->slug)->with('success', 'Successfully updated ' . $tag->name);
    }

    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        $tag = Tag::findOrFail($id);
        $tagName = $tag->name;

        try {
            $tag->delete();
            return redirect()->route('admin.tags.index')->with('success', 'Successfully deleted ' . $tagName);
        } catch(Throwable $exception) {
            return redirect()->route('admin.tags.show', $tag->slug)->with('error', 'Error deleting ' . $tag->name . ': ' . $exception->getMessage());
        }
    }

    public function syncEntities(Request $request): \Illuminate\Http\RedirectResponse
    {
        $tagIds = $request->input('tags');
        $taggableId = $request->input('taggable_id');
        $taggableType = $request->input('taggable_type');
        $routes = config('route-mapping.admin.' . $taggableType);

        try {
            $taggable = $taggableType::findOrFail($taggableId);
            $taggable->tags()->sync($tagIds);
        } catch (\Throwable $throwable) {
            return redirect()->back()->with('error', $throwable->getMessage());
        }

        return redirect()->route($routes['show'], $taggable->slug)->with('success', 'Tags added!');
    }

    public function detachFromEntity(TagDetachRequest $request)
    {
        $tagId = $request->input('tag_id');
        $taggableId = $request->input('taggable_id');
        $taggableType = $request->input('taggable_type');

        try {
            $taggable = $taggableType::findOrFail($taggableId);
            $taggable->tags()->detach($tagId);
        } catch (\Throwable $throwable) {
            return response()->json([
                'status' => 'error',
                'message' => $throwable->getMessage(),
            ]);
        }

        return response()->json([
            'status' => 'success'
        ]);
    }

    private function getDepthValue($parentId): int
    {
        if ($parentId) {
            $parent = Tag::findOrFail($parentId);

            return $parent->depth + 1;
        }

        return 0;
    }
}
