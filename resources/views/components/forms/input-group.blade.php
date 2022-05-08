<div {{ $attributes->merge(['class' => 'input-group']) }}>
    <label class="input-group-text" for="{{ $name }}">{{ $label }}</label>
    <input type="text" class="form-control" name="{{ $name }}" id="{{ $name }}" value="{{ $value }}">
</div>
