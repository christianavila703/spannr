@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between">
            <h1 class="text-primary">Longevity Index</h1>
        </div>

        @include('admin.includes.status-messages')

        <table class="table width-auto">
            <thead>
            <tr>
                <th>Date/Time</th>
                <th>Market Cap</th>
                <th>Index Value</th>
                <th>Index &Delta;</th>
            </tr>
            </thead>
            <tbody>
            @foreach($marketSnapshots as $marketSnapshot)
                <tr>
                    <td>
                        <a href="{{ route('longevity-index.show', $marketSnapshot->id) }}">{{ $marketSnapshot->formatted_timestamp }}</a>
                    </td>
                    <td>${{ number_format($marketSnapshot->market_cap, 0) }}</td>
                    <td>
                        <span class="{{ $marketSnapshot->index_change_color }}">{{ $marketSnapshot->formatted_index_value }}</span>
                    </td>
                    <td>{{ $marketSnapshot->formatted_index_change }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
