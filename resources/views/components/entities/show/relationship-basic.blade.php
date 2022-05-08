@props([
    'records',
    'routeName'
])

<div>
    @forelse ($records as $item)
        <p class="my-2">
            <a href="{{ route( $routeName, $item->slug) }}">{{ $item->name }}</a>
        </p>
    @empty
        <p class="my-2 text-muted">&ndash;</p>
    @endforelse
</div>
