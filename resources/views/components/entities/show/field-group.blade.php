@props([
    'icon' => 'fa-duotone fa-buildings',
    'tooltip' => 'Unknown',
])

<div class="entity-field-group d-flex my-3">
    <div class="icon me-3">
        <i class="{{ $icon }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $tooltip }}"></i>
    </div>
    <div class="field-value">
        {{ $slot }}
    </div>
</div>
