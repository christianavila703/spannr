@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="text-primary">Investors</h1>

        @include('admin.includes.status-messages')

        <table class="table">
            <thead>
            <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Website</th>
                <th>Summary</th>
                <th>Fundraising Rounds</th>
                <th>Organizations</th>
            </tr>
            </thead>
            <tbody>
            @foreach($investors as $investor)
                <tr>
                    <td>
                        <a href="{{ route('investors.show', $investor->slug) }}">{{ $investor->name }}</a>
                    </td>
                    <td>{{ $investor->type }}</td>
                    <td>{{ $investor->website }}</td>
                    <td>{{ $investor->summary }}</td>
                    <td>
                        @foreach ($investor->fundraisingRounds as $fundraisingRound)
                            <a href="{{ route('fundraising-rounds.show', $fundraisingRound->id) }}">{{ $fundraisingRound->name }}</a> @if (!$loop->last) / @endif
                        @endforeach
                    </td>
                    <td>
                        @foreach ($investor->organizations as $organization)
                            <a href="{{ route('organizations.show', $organization->slug) }}">{{ $organization->name }}</a> @if (!$loop->last) / @endif
                        @endforeach
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection

