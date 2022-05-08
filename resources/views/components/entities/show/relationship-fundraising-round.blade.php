@props([
    'records',
])

<div>
    @forelse ($records as $item)
        <p class="my-2">
            <a href="{{ route( 'fundraising-rounds.show', $item->id) }}">{{ $item->name }}</a> - ${{ number_format($item->amount_raised, 0) }}
        </p>
    @empty
        <p class="my-2 text-muted">&ndash;</p>
    @endforelse
</div>
