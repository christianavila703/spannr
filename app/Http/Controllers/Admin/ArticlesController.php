<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Articles\StoreArticleRequest;
use App\Http\Requests\Admin\Articles\UpdateArticleRequest;
use App\Jobs\WordPress\SyncArticles;
use App\Jobs\WordPress\SyncGlossary;
use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArticlesController extends Controller
{
    public function sync(): \Illuminate\Http\RedirectResponse
    {
        SyncArticles::dispatch();
        SyncGlossary::dispatch();
        return redirect()->route('admin.home')->with('success', 'Articles and glossary items are syncing!');
    }

    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('admin.articles.create', [
            'tagUrl' => route('admin.json.tags')
        ]);
    }

    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('admin.articles.index');
    }

    public function store(StoreArticleRequest $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();
        $article = Article::create($validated);
        return redirect()->route('admin.articles.index')->with('success', 'Saved ' . $article->name);
    }

    public function uploadImage(Request $request): \Illuminate\Http\JsonResponse
    {
        $file = $request->file('file');
        $fileName = Carbon::now()->format('Ymdhis') . '-' . $file->getClientOriginalName();
        Storage::putFileAs('public/images/articles', $file, $fileName);
        return response()->json([
            'location' => 'public/images/articles/' . $fileName,
        ]);
    }

    public function media($id) {
        $article = Article::findOrFail($id);

        return view('admin.articles.media', [
            'article' => $article,
        ]);
    }

    public function show($slug) {

    }

    public function edit($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        $article = Article::with('tags')->findOrFail($id);

        if ($article->enableLock(auth()->user()->id) === false) {
            return redirect()->route('admin.articles.index')->with('error', 'The entry has been temporarily locked by another user.');
        }

        $currentTags = $article->tags()->pluck('id')->toArray();
        $tagUrl = route('admin.json.tags');

        if (!empty($currentTags)) {
            $tagUrl .= '?default=' . implode(',', $currentTags);
        }

        return view('admin.articles.edit', [
            'article' => $article,
            'tagUrl' => $tagUrl
        ]);
    }

    public function update(UpdateArticleRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();
        $article = Article::findOrFail($id);

        if ($article->disableLock(auth()->user()->id) === false) {
            return redirect()->route('admin.articles.index')->with('error', 'The entry has been temporarily locked by another user.');
        }

        if (array_key_exists('tags', $validated)) {
            $article->tags()->sync($validated['tags']);
            unset($validated['tags']);
        }

        $article->update($validated);

        return redirect()->route('public.content.articles.show', $article->slug);
    }

    public function destroy($id) {

    }
}
