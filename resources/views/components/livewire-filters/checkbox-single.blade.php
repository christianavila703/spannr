<div class="form-check {{ $class }}">
    <input wire:model.lazy="{{ $wireModel }}" class="form-check-input" type="checkbox" value="true" id="{{ $id }}">
    <label class="form-check-label" for="{{ $id }}">
        {{ $label }}
    </label>
</div>
