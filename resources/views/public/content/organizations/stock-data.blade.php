<div class="stock-details">
    <span class="ticker">
        {{ $organization->stock->ticker }}
    </span>
    @if ($organization->stock->latestSnapshot)
        <span class="current-price">
            ${{ $organization->stock->latestSnapshot->current_price }}
        </span>
        <span class="change-percent {{ ($organization->stock->latestSnapshot->previous_day_change_percent > 0) ? 'text-success' : 'text-danger' }}">
            @if ($organization->stock->latestSnapshot->previous_day_change_percent > 0)
                <i class="fa-duotone fa-circle-up text-success"></i>
            @else
                <i class="fa-duotone fa-circle-down text-danger"></i>
            @endif
            {{ $organization->stock->latestSnapshot->previous_day_change_percent }}%
        </span>
        <span class="change-dollar">
            @if ($organization->stock->latestSnapshot->previous_day_change_price < 0)
                @php $amount = $organization->stock->latestSnapshot->previous_day_change_price * -1; @endphp
                $({{ $amount }})
            @else
                ${{ $organization->stock->latestSnapshot->previous_day_change_price }}
            @endif
        </span>
    @endif
</div>
