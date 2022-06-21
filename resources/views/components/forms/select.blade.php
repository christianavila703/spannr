@props([
    'name' => 'name',
    'label' => 'Name',
])

<div {{ $attributes->merge(['class' => 'form-floating mb-3']) }}>
    <select class="form-select" id="{{ $name }}" name="{{ $name }}" aria-label="{{ $label }}">
        {{ $slot }}
    </select>
    <label for="{{ $name }}">{{ $label }}</label>
</div>
