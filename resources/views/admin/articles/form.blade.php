<form method="POST" action="{{ $route }}" enctype="multipart/form-data">
    @csrf
    @isset($method)
        @method($method)
    @endisset
    <div class="row">
        <div class="col-12">
            <x-forms.input type="text" name="name" label="Title" class="mb-3"
                           value="{{ $article->name ?? old('name') }}"/>
        </div>
        <div class="col-12">
            <div class="d-flex flex-wrap">
                <x-forms.input type="datetime-local" name="date" label="Date" class="mb-3 me-3"
                               value="{{ $article->date ?? old('date') }}"/>
                <x-forms.select name="status" label="Status" class="mb-3 me-3">
                    @foreach (\App\Models\Article::STATUSES as $status)
                        <option value="{{ $status }}"
                                @isset($article)
                                    @if($article->status == $status) selected @endif
                                @else
                                    @if(old('status') == $status) selected @endif
                            @endisset
                        >{{ $status }}</option>
                    @endforeach
                </x-forms.select>
                <x-forms.input type="text" name="slug" label="Slug" class="mb-3 flex-grow-1"
                               value="{{ $article->slug ?? old('slug') }}"/>
            </div>
        </div>
        <div class="col-12">
            <x-forms.input type="text" name="page_title" label="Page Title" class="mb-3"
                           value="{{ $article->page_title ?? old('page_title') }}"/>
        </div>
        <div class="col-12 col-md-6 mb-3 mb-md-0">
            <div class="form-floating">
                <textarea class="form-control" placeholder="Excerpt" name="excerpt" id="excerpt"
                          style="height: 180px">{{ $article->excerpt ?? old('excerpt') }}</textarea>
                <label for="excerpt">Excerpt</label>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="form-floating">
                <textarea class="form-control" placeholder="Meta Description" name="meta_description" id="meta_description"
                          style="height: 180px">{{ $article->meta_description ?? old('meta_description') }}</textarea>
                <label for="meta_description">Meta Description</label>
                <div class="small">
                    <span id="meta_description_character_count" class="badge bg-gray-medium me-2">0</span>
                    <strong>characters</strong>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 mb-3 mb-md-0">
            <label for="authors" class="visually-hidden">Select Authors</label>
            <select multiple name="authors[]" id="authors" class="js-choice-authors" data-url="{{ $authorsUrl }}">
                <option value="" disabled>Select Authors</option>
            </select>
        </div>
        <div class="col-12 col-md-6">
            <label for="tags" class="visually-hidden">Select Tags</label>
            <select multiple name="tags[]" id="tags" class="js-choice-tags" data-url="{{ $tagsUrl }}">
                <option value="">Select Tags</option>
            </select>
        </div>
        <div class="col-12 my-3">
            <p class="h4">Content</p>
            <x-tiny-mce.tiny-editor id="content" name="content" value="{{ $article->content ?? old('content') }}"/>
        </div>
        <div class="col-12 my-3">
            <p class="h4">Sources</p>
            <x-tiny-mce.tiny-editor id="sources" name="sources" value="{{ $article->sources ?? old('sources') }}"/>
        </div>
        <div class="col-12 my-3">
            <p class="h4">Sidebar</p>
            <x-tiny-mce.tiny-editor id="sidebar" name="sidebar" value="{{ $article->sidebar ?? old('sidebar') }}"/>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary h5">Save Post</button>
        </div>
    </div>
</form>

@push('end')
    <x-tiny-mce.tiny-config/>

    <script>
        const authorChoice = new Choices('#authors', {
            removeItemButton: true,
        });
        const authorsUrl = document.getElementById('authors').getAttribute('data-url');

        authorChoice.setChoices(async () => {
            try {
                const items = await fetch(authorsUrl);
                return items.json();
            } catch (err) {
                console.error(err);
            }
        });

        const tagChoice = new Choices('#tags', {
            removeItemButton: true,
        });
        const tagsUrl = document.getElementById('tags').getAttribute('data-url');

        tagChoice.setChoices(async () => {
            try {
                const items = await fetch(tagsUrl);
                return items.json();
            } catch (err) {
                console.error(err);
            }
        });

    </script>

    @vite('resources/js/meta-descriptions.js')
@endpush
