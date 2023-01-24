<div {{ $attributes->merge(['class' => 'form-floating']) }}>
    <input type="{{ $type }}" class="form-control" name="{{ $name }}" id="{{ $name }}" placeholder="{{ $label }}" value="{!! $value !!}" @if ($required === true) required @endif>
    <label for="{{ $name }}">{{ $label }}</label>
</div>
