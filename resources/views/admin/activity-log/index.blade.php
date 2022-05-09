@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="text-primary">Activity Log</h1>

        @include('admin.includes.status-messages')

        <table class="table">
            <thead>
            <tr>
                <th>Date</th>
                <th>Log</th>
                <th>Description</th>
                <th>Subject</th>
                <th>Causer</th>
            </tr>
            </thead>
            <tbody>
            @foreach($activities as $activity)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($activity->created_at)->format('Y-m-d') }}</td>
                    <td>{{ $activity->log_name }}</td>
                    <td>{{ $activity->description }}</td>
                    <td>
                        {!! $activity->subjectLink !!}
                    </td>
                    <td>
                        @if ($activity->causer)
                            {{ $activity->causer->name }}
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
