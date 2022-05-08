@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="text-primary">People</h1>

        @include('admin.includes.status-messages')

        <table class="table">
            <thead>
            <tr>
                <th>Name</th>
                <th>Byline</th>
                <th>Website</th>
            </tr>
            </thead>
            <tbody>
            @foreach($people as $person)
                <tr>
                    <td>
                        <a href="{{ route('people.show', $person->slug) }}">{{ $person->name }}</a>
                    </td>
                    <td>{{ $person->byline }}</td>
                    <td>{{ $person->website }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection

