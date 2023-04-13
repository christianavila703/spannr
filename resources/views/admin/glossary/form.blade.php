<form method="POST" action="{{ $route }}" enctype="multipart/form-data">
    @csrf
    @isset($method)
        @method($method)
    @endisset
    <div class="row">
        <div class="col-12 col-md-6">
            <x-forms.input type="text" name="name" label="Name" class="mb-3" value="{{ $glossary->name ?? old('name') }}" />
        </div>
        <div class="col-12 col-md-6">
            <x-forms.input type="text" name="slug" label="Slug" class="mb-3 flex-grow-1" value="{{ $glossary->slug ?? old('slug') }}" />
        </div>
        <div class="col-12 col-md-6">
            <x-forms.select name="status" label="Status">
                @foreach (\App\Models\Glossary::STATUSES as $status)
                    <option value="{{ $status }}"
                        {{ (isset($glossary) && $glossary->status == $status) || old('status') == $status ? 'selected' :  '' }}>{{ $status }}</option>
                @endforeach
            </x-forms.select>
        </div>
        <div class="col-12 col-md-6">
            <x-forms.input type="text" name="page_title" label="Page title" class="mb-3" value="{{ $glossary->page_title ?? old('page_title') }}" />
        </div>
        <div class="col-12">
            <div class="d-flex flex-wrap">
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="form-floating">
                <textarea class="form-control" placeholder="Excerpt" name="excerpt" id="excerpt" style="height: 180px">{{ $glossary->excerpt ?? old('excerpt') }}</textarea>
                <label for="excerpt">Excerpt</label>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="form-floating">
                <textarea class="form-control" placeholder="Excerpt" name="meta_description" id="meta_description" style="height: 180px">{{ $glossary->meta_description ?? old('meta_description') }}</textarea>
                <label for="excerpt">Meta Description</label>
            </div>
            <div class="small">
                <span id="meta_description_character_count" class="badge bg-gray-medium me-2">0</span>
                <strong>characters</strong>
            </div>
        </div>
        <div class="col-12 my-3">
            <x-tiny-mce.tiny-editor id="content" name="content" value="{{ $glossary->content ?? old('content') }}"/>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </div>
</form>

@push('end')
    <x-tiny-mce.tiny-config />
    @vite('resources/js/meta-descriptions.js')
@endpush
