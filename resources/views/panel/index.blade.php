@extends('layouts.panel')

@section('panel')
    <div class="container">
        @include('admin.includes.status-messages')
        <h1 class="text-primary">Spannr+</h1>

        @if(auth()->user()->isSubscriptionActive())
            <div class="row data-panel">
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3 card">
                    <a href="{{ route('panel.organizations.index') }}" class="card-body d-flex flex-column">Organizations</a>
                </div>
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3 card">
                    <a href="{{ route('panel.investors.index') }}" class="card-body d-flex flex-column">Investors</a>
                </div>
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3 card">
                    <a href="{{ route('panel.people.index') }}" class="card-body d-flex flex-column">People</a>
                </div>
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3 card">
                    <a href="{{ route('panel.jobs.index') }}" class="card-body d-flex flex-column">Jobs</a>
                </div>
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3 card">
                    <a href="{{ route('panel.events.index') }}" class="card-body d-flex flex-column">Events</a>
                </div>
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3 card">
                    <a href="{{ route('panel.fundraising-rounds.index') }}" class="card-body d-flex flex-column">Fundraising
                        Rounds</a>
                </div>
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3 card">
                    <a href="{{ route('panel.locations.index') }}" class="card-body d-flex flex-column">Locations</a>
                </div>
            </div>
        @else
            <div class="text-center mt-4">
                <p class="lead">Hi {{ Auth::user()->name }}! Activate your Spannr+ subscription to get full access to our data.</p>
                <p><a href="{{ $subscriptionUrl }}" class="btn btn-tertiary btn-lg">Subscribe</a></p>
            </div>
        @endif

        @if($subscription)
            <div class="row mt-4">
                <div class="col-12">
                    <h3 class="lead text-primary">Subscriptions</h3>
                    <table class="table table-borderless">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Card</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Spannr+</td>
                            <td>
                                @if($subscription->status === \App\Models\UserSubscription::STATUS_ACTIVE)
                                    <span class="badge bg-success">Active</span>
                                @elseif($subscription->status === \App\Models\UserSubscription::STATUS_INACTIVE)
                                    <span class="badge bg-danger">Inactive</span>
                                @elseif($subscription->status === \App\Models\UserSubscription::STATUS_CANCELLED)
                                    <span class="badge bg-warning">Canceled</span>
                                @endif
                            </td>
                            <td>**** {{ $subscription->card_number }}</td>
                            <td>{{ \Carbon\Carbon::parse($subscription->start_date)->format('M j, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($subscription->end_date)->format('M j, Y') }}</td>
                            <td>
                                @if($subscription->status === \App\Models\UserSubscription::STATUS_ACTIVE)
                                    <form action="{{ route('panel.subscriptions.cancel') }}" method="post">
                                        @csrf
                                        <input type="submit" class="btn btn-danger btn-sm confirm-button"
                                               value="Cancel"/>
                                    </form>
                                @elseif($subscription->status === \App\Models\UserSubscription::STATUS_CANCELLED)
                                    <form action="{{ route('panel.subscriptions.resume') }}" method="post">
                                        @csrf
                                        <input type="submit" class="btn btn-primary btn-sm confirm-button"
                                               value="Resume"/>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection

