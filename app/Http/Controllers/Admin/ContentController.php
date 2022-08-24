<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ContentController extends Controller
{
    public function index()
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
        $currentContent = Content::pluck('id')->toArray();

        $username = config('services.wordpress.username');
        $password = config('services.wordpress.password');

        $rest_api_url = config('services.wordpress.url') . "/wp-json/wp/v2/pages";

        $response = Http::withBasicAuth($username, $password)
            ->get($rest_api_url, [
                'per_page' => 99,
            ]);

        $results = $response->json();

        $wordpressPages = [];

        foreach ($results as $result) {
            if (!in_array($result['id'], $currentContent)) {
                $wordpressPages[] = [
                    'value' => $result['id'],
                    'label' => $result['title']['rendered']
                ];
            }
        }

        $wordpressPages = json_encode($wordpressPages, true);

        return view('admin.content.create', [
            'wordpressPages' => $wordpressPages
        ]);
    }

    public function store(Request $request) {
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

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
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
