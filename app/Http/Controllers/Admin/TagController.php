<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

class TagController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $tags = Tag::firstLevel()
            ->with(['children', 'children.children', 'children.children.children'])
            ->orderBy('parent_id')
            ->orderBy('name')
            ->get();

        $parentTags = Tag::orderBy('name')->canHaveKids()->get();

        return view('admin.tags.index', [
            'tags' => $tags,
            'parentTags' => $parentTags
        ]);
    }

    public function tagsList(): bool|string
    {
        $tags = Tag::orderBy('name')->get();

        $tagsList = [];

        foreach ($tags as $tag) {
            $tagsList[] = [
                'label' => $tag->name,
                'value' => $tag->id
            ];
        }

        return json_encode($tagsList);
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

        $validated['depth'] = $this->getDepth($validated);

        $tag = Tag::create($validated);

        return redirect()->route('admin.tags.index')->with('success', 'Successfully added ' . $tag->name);
    }

    public function show($slug): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();
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
        $validated = $request->validated();

        $tag = Tag::findOrFail($id);
        $tag->update($validated);
        $validated['depth'] = $this->getDepth($validated);

        return redirect()->route('admin.tags.show', $tag->slug)->with('success', 'Successfully updated ' . $tag->name);
    }

    public function destroy($id)
    {
        //
    }

    public function syncEntities(Request $request): \Illuminate\Http\RedirectResponse
    {
        $tagIds = $request->input('tags');
        $entityType = $request->input('entity_type');

        $model = match($entityType) {
            'organizations' => Organization::class,
            'people' => Person::class,
            'investors' => Investor::class,
            'jobs' => Job::class,
            'events' => Event::class,
            'articles' => Article::class
        };

        $record = $model::findOrFail($request->input('entity_id'));
        $record->tags()->syncWithoutDetaching($tagIds);

        return redirect()->route('admin.' . $entityType . '.show', $record->slug)->with('success', 'Tags added!');
    }

    private function getDepth($tagAttributes): int
    {
        if ($tagAttributes['parent_id']) {
            $parent = Tag::findOrFail($tagAttributes['parent_id']);
            if ($parent->depth < 2) {
                return $parent->depth + 1;
            } else {
                return 2;
            }
        } else {
            return 0;
        }
    }
}
