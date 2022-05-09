@props([
    'records',
])

<div>
    @forelse ($records as $item)
        <p class="my-2">
            <span class="me-1">{{ \Carbon\Carbon::parse($item->date)->format('m/d/Y') }}</span>
            <a href="{{ route( 'fundraising-rounds.show', $item->id) }}">
                {{ $item->name }} -
                ${{ number_format($item->amount_raised, 0) }}
            </a>
        </p>
    @empty
        <p class="my-2 text-muted">&ndash;</p>
    @endforelse
</div>
