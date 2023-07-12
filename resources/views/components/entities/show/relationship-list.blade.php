@props([
    'records',
    'class' => 'lead text-muted'
])

<div class="{{ $class }}">
    @forelse ($records as $item)
        <span class="me-1">{{ $item->name }}</span>
        @if(!$loop->last) <span class="me-1">/</span> @endif
    @empty
        <p class="my-2 text-muted">&ndash;</p>
    @endforelse
</div>
