@props([
    'id' => 'tiny-editor',
    'name' => 'name',
    'value' => ''
])

<div class="tiny-editor">
    <textarea class="tiny-editor" id="{{ $id }}" name="{{ $name }}" aria-label="Content Editor">{!! $value !!}</textarea>
</div>
