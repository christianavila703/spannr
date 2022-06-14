@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between">
            <h1 class="text-primary">Longevity Index</h1>
            <div class="mb-2">
                <a href="{{ route('longevity-index.update') }}" class="btn btn-tertiary">Update Index</a>
            </div>
        </div>

        @include('admin.includes.status-messages')

        <div class="d-flex flex-wrap mt-2">
            <div class="h3 pe-4 mb-2">{{ $marketSnapshot->formatted_index_value }}</div>
            <div class="h3 mb-2 {{ $marketSnapshot->index_change_color }} pe-4">&Delta; {{ $marketSnapshot->formatted_index_change }}</div>
            <div class="h3 mb-2">${{ number_format($marketSnapshot->market_cap, 0) }}</div>
        </div>

        <p class="my-1">
            {{ $marketSnapshot->formatted_timestamp }} <small class="text-muted">{{ $marketSnapshot->formatted_timezone }}</small>
        </p>

        <div class="chart-container py-3">
            @include('admin.longevity-index.chart')
        </div>

        <table class="table">
            <thead>
            <tr>
                <th>Name</th>
                <th>Weight</th>
                <th>Ticker</th>
                <th>Current Price</th>
                <th>Yesterday Close</th>
                <th>Market Cap</th>
                <th>Volume</th>
                <th>Today's High</th>
                <th>Today's Low</th>
                <th><span class="d-block">Previous Day</span> Price Change</th>
                <th><span class="d-block">Previous Day</span> % Change</th>
                <th>Outstanding Shares</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($marketSnapshot->stockSnapshots as $stockSnapshot)
                <tr>
                    <td>
                        <a href="{{ route('organizations.show', $stockSnapshot->stock->organization->slug) }}">{{ $stockSnapshot->stock->name }}</a>
                    </td>
                    <td>
                        {{ $stockSnapshot->weight_percent }}%
                    </td>
                    <td>{{ $stockSnapshot->stock->ticker }}</td>
                    <td>${{ number_format($stockSnapshot->current_price, 2) }}</td>
                    <td>${{ number_format($stockSnapshot->yesterday_close, 2) }}</td>
                    <td>${{ number_format($stockSnapshot->market_cap, 2) }}</td>
                    <td>{{ number_format($stockSnapshot->volume, 0) }}</td>
                    <td>${{ number_format($stockSnapshot->today_high, 2) }}</td>
                    <td>${{ number_format($stockSnapshot->today_low, 2) }}</td>
                    <td>
                        <span class="{{ $stockSnapshot->change_color }}">
                            ${{ number_format($stockSnapshot->previous_day_change_price, 2) }}
                        </span>
                    </td>
                    <td>
                        {{ $stockSnapshot->previous_day_change_percent }}%
                    </td>
                    <td>{{ number_format($stockSnapshot->outstanding_shares, 0) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div>
            <a href="{{ route('longevity-index.all') }}">View past snapshots</a>
        </div>
    </div>
@endsection
