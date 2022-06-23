@props([
    'records',
    'class' => 'lead text-muted'
])

<div class="{{ $class }}">
    @foreach ($records as $item)
        <span class="me-1">{{ $item->name }}</span>
        @if(!$loop->last) <span class="me-1">/</span> @endif
    @endforeach
</div>
