@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between">
            <h1 class="text-primary">Longevity Index</h1>
        </div>

        @include('admin.includes.status-messages')

        <div class="row">
            <div class="col-12 col-xl-6">
                <table class="table width-auto">
                    <thead>
                    <tr>
                        <th>Date/Time</th>
                        <th>Type</th>
                        <th>Market Cap</th>
                        <th>Index Value</th>
                        <th>Index &Delta;</th>
                        <th>Cumulative &Delta;</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($marketSnapshots as $marketSnapshot)
                        <tr>
                            <td>
                                <a href="{{ route('longevity-index.show', $marketSnapshot->id) }}">{{ $marketSnapshot->formatted_timestamp }}</a>
                            </td>
                            <td>
                                {{ ucwords($marketSnapshot->type) }}
                                @if($marketSnapshot->notes)
                                    <i class="fa-solid fa-circle-info mx-2 text-quaternary" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $marketSnapshot->notes }}"></i>
                                @endif
                            </td>
                            <td>${{ number_format($marketSnapshot->market_cap, 0) }}</td>
                            <td>
                                <span class="{{ $marketSnapshot->index_change_color }}">{{ $marketSnapshot->formatted_index_value }}</span>
                            </td>
                            <td class="text-right">
                                {{ $marketSnapshot->formatted_index_change }}
                            </td>
                            <td class="text-right">
                                {{ $marketSnapshot->formatted_cumulative_change }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-12 col-xl-6">
                <h2 class="h5 text-tertiary">Index Stocks</h2>
                <table class="table width-auto">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Ticker</th>
                            <th>Status</th>
                            <th>Latest Market Cap</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stocks as $stock)
                            <tr>
                                <td>{{ $stock->name }}</td>
                                <td>{{ $stock->ticker }}</td>
                                <td>
                                    <span class="{{ $stock->status_color }}">
                                        {{ ucwords($stock->status) }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    @if($stock->snapshots->first())
                                        {{ $stock->snapshots->first()->formatted_market_cap }}
                                    @endif
                                </td>
                                <td>
                                    @if($stock->date_removed)
                                        <span class="small text-muted">removed {{ $stock->date_removed }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
