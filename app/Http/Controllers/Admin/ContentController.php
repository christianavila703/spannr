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
        return view('admin.content.create');
    }

    public function store(StoreContentRequest $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();
        $content = Content::create($validated);
        return redirect()->route('admin.content.index')->with('success', 'Successfully created ' . $content->name . '!');
    }

    public function storeWordpress(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'wordpress' => 'required',
        ]);

        $username = config('services.wordpress.username');
        $password = config('services.wordpress.password');

        $rest_api_url = config('services.wordpress.url') . "/wp-json/wp/v2/pages/" . $validated['wordpress'];

        $response = Http::withBasicAuth($username, $password)
            ->get($rest_api_url);

        $wpPage = $response->json();

        $slug = $wpPage['yoast_head_json']['canonical'];
        $slug = str_replace('https://spannr.com/', '', $slug);
        $slug = rtrim($slug, '/');
        $slug = str_replace('/', '--', $slug);

        $attributes = [
            'name' => html_entity_decode($wpPage['title']['rendered']),
            'slug' => $slug,
            'link' => $wpPage['yoast_head_json']['canonical'],
            'status' => $this->getStatus($wpPage['status']),
            'seo' => $wpPage['yoast_head'],
            'content' => html_entity_decode($wpPage['content']['rendered']),
        ];

        $content = Content::updateOrCreate(
            ['id' => $wpPage['id']],
            $attributes
        );

        return redirect()->route('admin.content.index')->with('success', 'Successfully added ' . $content->name . '!');
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

        return view('admin.content.edit', [
            'content' => $content,
            'tagUrl' => $tagUrl
        ]);
    }

    public function update(UpdateContentRequest $request, $id)
    {
        $validated = $request->validated();
        $content = Content::findOrFail($id);

        $content->update($validated);
        return redirect()->route('admin.content.index')->with('success', 'Updated ' . $content->name);
    }

    public function destroy($id)
    {
        $content = Content::findOrFail($id);
        $status = 'Successfully deleted ' . $content->name . '!';
        $content->delete();
        return redirect()->route('admin.content.index')->with('success', $status);
    }

    private function getStatus($status): string
    {
        return match($status) {
            'publish' => Content::STATUS_PUBLISHED,
            'private' => Content::STATUS_PRIVATE,
            default => Content::STATUS_DRAFT
        };
    }
}
