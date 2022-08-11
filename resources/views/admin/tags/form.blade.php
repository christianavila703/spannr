<form method="POST" action="{{ $route }}" enctype="multipart/form-data">
    @csrf
    @isset($method)
        @method($method)
    @endisset
    <div>
        <x-forms.input type="text" name="name" label="Name" class="mb-3" value="{{ $tag->name ?? old('name') }}" required />
        <div class="mb-3">
            <label for="parent_id" class="visually-hidden">Parent</label>
            <select id="parent_id" name="parent_id" class="form-select">
                <option value="" selected>Select Parent</option>
                @foreach ($parentTags as $parentTag)
                    <option value="{{ $parentTag->id }}"
                        @isset($tag)
                            @if ($tag->parent_id === $parentTag->id) selected @endif
                        @endisset
                    >
                        {{ $parentTag->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-floating mb-3">
            <textarea class="form-control floating-textarea" id="description" name="description" placeholder="Description">{{ $tag->description ?? old('description') }}</textarea>
            <label for="description">Description</label>
        </div>
        <div>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </div>
</form>

@push('end')
    <script>
        const parentTags = new Choices(document.getElementById('parent_id'));
    </script>
@endpush
