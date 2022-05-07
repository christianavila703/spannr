@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="text-primary">Organizations</h1>

        @include('admin.includes.status-messages')

        <table class="table">
            <thead>
            <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Founded</th>
                <th>Website</th>
                <th>Summary</th>
                <th>Fundraising Rounds</th>
                <th>Investors</th>
            </tr>
            </thead>
            <tbody>
            @foreach($organizations as $organization)
                <tr>
                    <td>
                        <a href="{{ route('organizations.show', $organization->slug) }}">{{ $organization->name }}</a>
                    </td>
                    <td>{{ $organization->type }}</td>
                    <td>{{ $organization->founded_date }}</td>
                    <td>{{ $organization->website }}</td>
                    <td>{{ $organization->summary }}</td>
                    <td>
                        @foreach ($organization->fundraisingRounds as $fundraisingRound)
                            <a href="{{ route('fundraising-rounds.show', $fundraisingRound->id) }}">{{ $fundraisingRound->name }}</a> @if (!$loop->last) / @endif
                        @endforeach
                    </td>
                    <td>
                        @foreach ($organization->investors as $investor)
                            <a href="{{ route('investors.show', $investor->id) }}">{{ $investor->name }}</a> @if (!$loop->last) / @endif
                        @endforeach
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection

