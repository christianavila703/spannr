@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="heading-container d-lg-flex align-items-flex-end justify-content-between mb-4">
            <h1 class="text-primary me-5">Unreviewed {{ $formattedType }}</h1>

            <div>
                <a href="{{ route('admin.entity-reviews.index') }}" class="btn btn-sm text-dark text-nowrap"><i class="fa-regular fa-arrow-left me-1"></i>Back to Review / Audit</a>
            </div>
        </div>

        @include('admin.includes.status-messages')

        @livewire('admin.entity-reviews.reviews-index', ['entityClass' => $entityClass])
    </div>
@endsection
