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
                <div class="mb-3 me-3" style="min-width: 150px">
                    <select name="user_id" id="user_id" aria-label="Author"
                            data-url="{{ route('admin.json.authors') }}{{ isset($article->author->id) ? '?default=' . $article->author->id : '' }}">
                        <option value="" disabled>Author</option>
                    </select>
                </div>
                <x-forms.input type="text" name="slug" label="Slug" class="mb-3 flex-grow-1"
                               value="{{ $article->slug ?? old('slug') }}"/>
            </div>
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
        @isset($article)
            <div class="col-12 py-2">
                <label for="tags" class="visually-hidden">Select Tags</label>
                <select multiple name="tags[]" id="tags" class="js-choice-tags" data-url="{{ $tagUrl }}">
                    <option value="">Select Tags</option>
                </select>
            </div>
        @endisset
        <div class="col-12 my-3">
            <x-tiny-mce.tiny-editor id="content" name="content" value="{{ $article->content ?? old('content') }}"/>
        </div>
        <div class="col-12 my-3">
            <p class="h4">Sources</p>
            <x-tiny-mce.tiny-editor id="sources" name="sources" value="{{ $article->sources ?? old('sources') }}"/>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary h5">Save Post</button>
        </div>
    </div>
</form>

@push('end')
    <x-tiny-mce.tiny-config/>

    <script>
        const authorChoice = new Choices('#user_id');
        const authorUrl = document.getElementById('user_id').getAttribute('data-url');

        authorChoice.setChoices(async () => {
            try {
                const items = await fetch(authorUrl);
                return items.json();
            } catch (err) {
                console.error(err);
            }
        });

        const tagChoice = new Choices('#tags');
        const tagUrl = document.getElementById('tags').getAttribute('data-url');

        tagChoice.setChoices(async () => {
            try {
                const items = await fetch(tagUrl);
                return items.json();
            } catch (err) {
                console.error(err);
            }
        });

    </script>

    @vite('resources/js/meta-descriptions.js')
@endpush
