@props([
    'entity' => null,
    'relationName',
])

<?php
$actionUrl = route("admin.json.$relationName");
$currentItems = [];

if ($entity) {
    $currentItems = $entity->{$relationName}()->pluck('id')->toArray();
}

if (!empty($currentItems)) {
    $actionUrl .= '?default=' . implode(',', $currentItems);
}
?>

<div class="col-12 mt-3 mb-3">
    <label for="{{ $relationName }}" class="visually-hidden">Select {{$relationName}}</label>
    <select multiple class="js-choice" name="{{$relationName}}[]" id="{{$relationName}}" data-url="{{ $actionUrl }}">
        <option value="" disabled>Select {{$relationName}}</option>
    </select>
</div>

@push('end')
    <script>
        let {{$relationName}}Choices = new Choices('#{{$relationName}}', {
            removeItemButton: true,
        });
        let {{$relationName}}ChoicesUrl = document.getElementById('{{$relationName}}').getAttribute('data-url');

        {{$relationName}}Choices.setChoices(async () => {
            try {
                let items = await fetch({{$relationName}}ChoicesUrl);
                return items.json();
            } catch (err) {
                console.error(err);
            }
        });
    </script>
@endpush
