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


        <div id="entity_types_block" class="form-group mb-3" @if(isset($tag) && $tag->parent_id) style="display:none;" @endif>
            <label for="entity_types mb-1 fw-bold">Available for entity types (only root level):</label>
            <select id="entity_types" name="entity_types[]" class="form-control" multiple style="height: 10rem" @if(isset($tag) && $tag->parent_id) disabled @endif>
                @foreach (\App\Models\Tag::ENTITY_TYPES as $label => $value)
                    <option value="{{ $value }}"
                            @if (isset($tag) && !is_null($tag->entity_types) && in_array($value, $tag->entity_types)) selected @endif>
                        {{ ucfirst($label) }}
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
        const parentTagsInput = document.getElementById('parent_id');
        const parentTagsChoices = new Choices(parentTagsInput);
        const entityTypesBlock = document.getElementById('entity_types_block');
        const entityTypesInput = document.getElementById('entity_types');

        parentTagsInput.addEventListener(
            'change',
            function(event) {
                if (parentTagsChoices.getValue(true)) {
                    entityTypesBlock.style.display = 'none';
                    entityTypesInput.disabled = true;
                } else {
                    entityTypesBlock.style.display = 'inherit';
                    entityTypesInput.disabled = false;
                }

                console.log(parentTagsChoices.getValue(true));
            },
            false,
        );
    </script>
@endpush
