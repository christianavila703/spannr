<div wire:poll>
    @foreach ($images as $image)
        <div wire:key="image-{{ $image->id }}" class="post-image-widget-item">
            <div class="py-2">
                <div class="post-inline-image">
                    <img src="{{ $image->getUrl('post') }}" alt="{{ $image->name }}" width="{{ $image->getCustomProperty('post-width') }}" height="{{ $image->getCustomProperty('post-height') }}">
                </div>
                <p class="my-1 text-small">
                    {{ $image->name }}
                </p>
            </div>
            @if(!$loop->last)
                <i class="fa-thin fa-horizontal-rule"></i>
            @endif
        </div>
    @endforeach
</div>
