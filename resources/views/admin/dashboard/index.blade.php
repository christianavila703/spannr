@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="container">
            @include('admin.includes.status-messages')
        </div>

        <div class="container">
            <h2 class="h4">Database Stats</h2>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                    <div class="list-group">
                        <div class="list-group-item d-flex justify-content-between">
                            <span class="label d-block">Organizations</span>
                            <span class="d-block">
                                <a href="{{ route('admin.organizations.index') }}">{{ $organizations }}</a>
                            </span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span class="label d-block">People</span>
                            <span class="d-block">
                                <a href="{{ route('admin.people.index') }}">{{ $people }}</a>
                            </span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span class="label d-block">Investors</span>
                            <span class="d-block">
                                <a href="{{ route('admin.investors.index') }}">{{ $investors }}</a>
                            </span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span class="label d-block">Jobs</span>
                            <span class="d-block">
                                <a href="{{ route('admin.jobs.index') }}">{{ $jobs }}</a>
                            </span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span class="label d-block">Events</span>
                            <span class="d-block">
                                <a href="{{ route('admin.events.index') }}">{{ $events }}</a>
                            </span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span class="label d-block">Fundraising Rounds</span>
                            <span class="d-block">
                                <a href="{{ route('admin.fundraising-rounds.index') }}">{{ $fundraisingRounds }}</a>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
