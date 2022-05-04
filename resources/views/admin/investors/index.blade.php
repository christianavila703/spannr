@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="text-primary">Investors</h1>

        <table class="table">
            <thead>
            <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Website</th>
                <th>Summary</th>
                <th>Fundraising Rounds</th>
            </tr>
            </thead>
            <tbody>
            @foreach($investors as $investor)
                <tr>
                    <td>{{ $investor->name }}</td>
                    <td>{{ $investor->type }}</td>
                    <td>{{ $investor->website }}</td>
                    <td>{{ $investor->summary }}</td>
                    <td>
                        @foreach ($investor->fundraisingRounds as $fundraisingRound)
                            {{ $fundraisingRound->name }}
                        @endforeach
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection

