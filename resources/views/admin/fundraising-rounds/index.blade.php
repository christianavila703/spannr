@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="text-primary">Fundraising Rounds</h1>

        <table class="table">
            <thead>
            <tr>
                <th>Date</th>
                <th>Name</th>
                <th>Round</th>
                <th>Amount Raised</th>
                <th>Organization</th>
                <th>Investors</th>
            </tr>
            </thead>
            <tbody>
            @foreach($fundraisingRounds as $fundraisingRound)
                <tr>
                    <td>{{ $fundraisingRound->date }}</td>
                    <td>
                        <a href="{{ route('fundraising-rounds.show', $fundraisingRound->id) }}">{{ $fundraisingRound->name }}</a>
                    </td>
                    <td>{{ $fundraisingRound->round }}</td>
                    <td>${{ number_format($fundraisingRound->amount_raised, 0) }}</td>
                    <td>
                        <a href="{{ route('organizations.show', $fundraisingRound->organization->slug) }}">{{ $fundraisingRound->organization->name }}</a>
                    </td>
                    <td>
                        @foreach ($fundraisingRound->investors as $investor)
                            <a href="{{ route('investors.show', $investor->slug) }}">{{ $investor->name }}</a> @if (!$loop->last) / @endif
                        @endforeach
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection

