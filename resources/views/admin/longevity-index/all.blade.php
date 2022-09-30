@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between">
            <h1 class="text-primary">Longevity Stock Index</h1>
        </div>

        @include('admin.includes.status-messages')

        <div class="row">
            <div class="col-12 col-xl-7">
                <livewire:longevity-index.market-snapshots />
            </div>
            <div class="col-12 col-xl-4">
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
                                    @if($stock->date_added)
                                        <span class="small text-success">added {{ $stock->date_added }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @include('admin.longevity-index._nav-group')
    </div>
@endsection
