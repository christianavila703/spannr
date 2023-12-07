<div class="mt-3">
    <div wire:key="featured-{{ $image->id ?? 'featured' }}">
        <div wire:poll class="text-end">
            @if ($image)
                <img src="{{ $image->getUrl('medium') }}" alt="{{ $image->name }}" width="488" height="275">
            @endif
        </div>
    </div>
</div>
