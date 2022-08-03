@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        @include('admin.includes.status-messages')

        <div class="d-lg-flex align-items-start flex-shrink-0 pe-lg-5">
            <div class="overview d-flex flex-column justify-content-center mt-lg-5 mb-3">
                <h1 class="text-primary text-center">Longevity <span class="d-block">Stock Index</span></h1>
                <div class="d-flex align-items-center justify-content-center">
                    <div class="h3 pe-3 my-1">{{ $marketSnapshot->formatted_index_value }}</div>
                    <div class="my-1 {{ $marketSnapshot->index_change_color }}">
                       <span class="me-1 lead">{!! $marketSnapshot->index_change_icon !!}</span>
                       <span class="h4 fw-bold">{{ $marketSnapshot->formatted_index_change }}</span>
                    </div>
                </div>
                <p class="mb-1 text-center small text-muted px-2">
                    <span class="me-3 text-nowrap">{{ $marketSnapshot->formatted_timestamp }} {{ $marketSnapshot->formatted_timezone }}</span>
                    <span class="text-nowrap">Market Cap: {{ $marketSnapshot->formatted_market_cap }}</span>
                </p>
                <div class="d-md-flex">
                    <table class="table table-sm small">
                        <tr>
                            <td class="text-nowrap">Last</td><td class="text-right">{{ $marketSnapshot->formatted_index_value }}</td>
                        </tr>
                        <tr>
                            <td class="text-nowrap">$ Change</td><td class="text-right">{{ $marketSnapshot->previous_dollar_change }}</td>
                        </tr>
                        <tr>
                            <td class="text-nowrap">Open</td><td class="text-right">{{ $marketSnapshot->today_open }}</td>
                        </tr>
                        <tr>
                            <td class="text-nowrap">High</td><td class="text-right">{{ $marketSnapshot->today_high }}</td>
                        </tr>
                        <tr>
                            <td class="text-nowrap">Year Low</td><td class="text-right">{{ $marketSnapshot->year_low }}</td>
                        </tr>
                    </table>
                    <div>&nbsp;&nbsp;&nbsp;</div>
                    <table class="table table-sm small">
                        <tr>
                            <td class="text-nowrap">Volume</td><td class="text-right">{{ $marketSnapshot->volume }}</td>
                        </tr>
                        <tr>
                            <td class="text-nowrap">% Change</td><td class="text-right">{{ $marketSnapshot->formatted_index_change }}</td>
                        </tr>
                        <tr>
                            <td class="text-nowrap">Prev. Close</td><td class="text-right">{{ $marketSnapshot->previous_close }}</td>
                        </tr>
                        <tr>
                            <td class="text-nowrap">Low</td><td class="text-right">{{ $marketSnapshot->today_low }}</td>
                        </tr>
                        <tr>
                            <td class="text-nowrap">Year High</td><td class="text-right">{{ $marketSnapshot->year_high }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="flex-grow-1">
                <div class="chart-container py-3">
                    @include('admin.longevity-index.chart')
                </div>
            </div>
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
            </tr>
            </thead>
            <tbody>
            @foreach($marketSnapshot->stockSnapshots as $stockSnapshot)
                <tr>
                    <td>
                        <a href="{{ route('admin.organizations.show', $stockSnapshot->stock->organization->slug) }}">{{ $stockSnapshot->stock->name }}</a>
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

        <div class="mt-5">
            <a href="{{ route('admin.longevity-index.all') }}" class="btn btn-secondary">View past snapshots</a>
            <a href="{{ route('admin.longevity-index.update') }}" class="btn btn-tertiary">Update Index</a>
        </div>
    </div>
@endsection
