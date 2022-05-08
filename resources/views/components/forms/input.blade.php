<div {{ $attributes->merge(['class' => 'form-floating']) }}>
    <input type="{{ $type }}" class="form-control" name="{{ $name }}" id="{{ $name }}" placeholder="Name" value="{{ $value }}">
    <label for="{{ $name }}">{{ $label }}</label>
</div>
