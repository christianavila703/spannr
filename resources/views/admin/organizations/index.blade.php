@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="text-primary">Organizations</h1>

        <table class="table">
            <thead>
            <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Founded</th>
                <th>Website</th>
                <th>Summary</th>
                <th>Fundraising Rounds</th>
            </tr>
            </thead>
            <tbody>
            @foreach($organizations as $organization)
                <tr>
                    <td>{{ $organization->name }}</td>
                    <td>{{ $organization->type }}</td>
                    <td>{{ $organization->founded_date }}</td>
                    <td>{{ $organization->website }}</td>
                    <td>{{ $organization->summary }}</td>
                    <td>
                        @foreach ($organization->fundraisingRounds as $fundraisingRound)
                            {{ $fundraisingRound->name }}
                        @endforeach
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection

