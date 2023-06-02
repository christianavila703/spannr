<form method="POST" action="{{ $route }}" enctype="multipart/form-data">
    @csrf
    @isset($method)
        @method($method)
    @endisset
    <div class="row">
        <div class="col-12">
            <x-forms.input type="text" name="name" label="Title" class="mb-3"
                           value="{{ $content->name ?? old('name') }}"/>
        </div>
        <div class="col-12">
            <div class="d-flex flex-wrap">
                <x-forms.select name="status" label="Status" class="mb-3 me-3">
                    @foreach (\App\Models\Content::STATUSES as $status)
                        <option value="{{ $status }}"
                                @isset($content)
                                    @if($content->status == $status) selected @endif
                                @else
                                    @if(old('status') == $status) selected @endif
                            @endisset
                        >{{ $status }}</option>
                    @endforeach
                </x-forms.select>
                <x-forms.input type="text" name="slug" label="Slug" class="mb-3 flex-grow-1"
                               value="{{ $content->slug ?? old('slug') }}"/>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="form-floating">
                <textarea class="form-control" placeholder="Meta Description" name="meta_description" id="meta_description"
                          style="height: 180px">{{ $content->meta_description ?? old('meta_description') }}</textarea>
                <label for="meta_description">Meta Description</label>
                <div class="small">
                    <span id="meta_description_character_count" class="badge bg-gray-medium me-2">0</span>
                    <strong>characters</strong>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div>
                <label for="tags" class="visually-hidden">Select Tags</label>
                <select multiple name="tags[]" id="tags" class="js-choice-tags" data-url="{{ $tagUrl }}">
                    <option value="">Select Tags</option>
                </select>
            </div>
            <div class="mt-4">
                <label for="parent_id" class="h5">Parent Content</label>
                <select name="parent_id" id="parent_id" class="js-choice-parent" data-url="{{ $parentsUrl }}">
                    <option value="">Select Parent</option>
                </select>
            </div>
        </div>
        <div class="col-12 my-3">
            <x-tiny-mce.tiny-editor id="content" name="content" value="{{ $content->content ?? old('content') }}"/>
        </div>
        <div class="col-12 my-3">
            <p class="h4">References</p>
            <x-forms.input type="text" name="sources_label" label="Sources Label" class="mb-3 flex-grow-1"
                               value="{{ $content->sources_label ?? old('sources_label') }}"/>
            <x-tiny-mce.tiny-editor id="sources" name="sources" value="{{ $content->sources ?? old('sources') }}"/>
        </div>
        <div class="col-12 my-3">
            <p class="h4">Sidebar</p>
            <x-tiny-mce.tiny-editor id="sidebar" name="sidebar" value="{{ $content->sidebar ?? old('sidebar') }}"/>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary h5">Save Content</button>
        </div>
    </div>
</form>

@push('end')
    <x-tiny-mce.tiny-config/>

    <script>
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

        const parentChoice = new Choices('#parent_id');
        const parentUrl = document.getElementById('parent_id').getAttribute('data-url');

        parentChoice.setChoices(async () => {
            try {
                const items = await fetch(parentUrl);
                return items.json();
            } catch (err) {
                console.error(err);
            }
        });
    </script>

    @vite('resources/js/meta-descriptions.js')
@endpush
