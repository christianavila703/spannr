@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between">
            <h1 class="text-primary me-5">{{ $user->full_name }}</h1>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.users.index') }}" class="btn btn-quaternary">Back to Users</a>
                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Delete this user? This cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">Delete user</button>
                </form>
            </div>
        </div>

        @include('admin.includes.status-messages')

        <div class="mb-5">
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-4">
                    <div class="card card-body h-100">
                        <div class="h6">Email</div>
                        <div class="fs-5">
                            <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-4">
                    <div class="card card-body h-100">
                        <div class="h6">Roles</div>
                        <div>
                            @foreach ($user->roles as $role)
                                <span class="badge {{ $role->color}}">{{ $role->name }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
                @if($user->stripe_customer_id)
                    <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-4">
                        <div class="card card-body h-100">
                            <div class="h6">Stripe Customer ID</div>
                            {{ $user->stripe_customer_id }}
                        </div>
                    </div>
                @endif
                <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-4">
                    <div class="card card-body h-100">
                        <div class="h6">Slug</div>
                        <div class="small text-body-secondary">required for authors with pages</div>
                        @if($user->slug)
                            <div class="fs-5">
                                <a href="{{ route('public.content.about.user', $user->slug) }}" target="_blank" rel="noopener noreferrer">spannr.com/about/{{ $user->slug }}</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="my-5">
            <h2 class="h3">Related</h2>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-4">
                    <div class="card card-body h-100">
                        <h3 class="h5">Person</h3>
                        @if($user->person)
                            <a href="{{ route('admin.people.show', $user->person->slug) }}">{{ $user->person->name }}</a>
                        @endif
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-4">
                    <div class="card card-body h-100">
                        <h3 class="h5">Articles</h3>
                        {{ $user->articles_count }}
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-4">
                    <div class="card card-body h-100">
                        <h3 class="h5">Jobs</h3>
                        <div>Job Listings - {{ $user->jobs_count }}</div>
                        <div>Subscriptions - {{ $user->job_subscriptions_count }}</div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-4">
                    <div class="card card-body h-100">
                        <h3 class="h5">Marketplace</h3>
                        <div>Listings - {{ $user->listings_count }}</div>
                        <div>Logs - {{ $user->marketplace_logs_count }}</div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-4">
                    <div class="card card-body h-100">
                        <h3 class="h5">Spannr+</h3>
                        @if($user->subscription)
                            {{ $user->subscription->status }}
                        @else
                            &ndash;
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @can('edit users')
            <div class="my-5">
                <div class="mb-5">
                    <h2 class="h3">User Details</h2>
                    <livewire:users.user-profile :user="$user" />
                </div>

                <div>
                    <h2 class="h3">User Roles</h2>
                    <div class="max-width-800">
                        <livewire:admin.users.user-roles :user="$user" />
                    </div>
                </div>
            </div>
        @endcan

    </div>
@endsection
