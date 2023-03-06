@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        @include('admin.includes.status-messages')

        <div class="row">
            <div class="col-4 offset-2">
                <h2 class="h4">Unreviewed records</h2>
                <div class="row">
                    <div class="col-12 col-lg-8">
                        <div class="list-group">
                            @foreach($unreviewedEntities as $type => $total)
                                <div class="list-group-item d-flex justify-content-between">
                                    <span class="label d-block">
                                        {{ ucwords(str_replace('_', ' ', $type)) }}

                                        @if(isset($reviewedNotApprovedEntities[$type]) && $reviewedNotApprovedEntities[$type] > 0)
                                            <span class="ms-2 badge bg-success">{{ $reviewedNotApprovedEntities[$type] }}</span>
                                        @endif
                                    </span>
                                    <span class="d-block">
                                        @if($total > 0)
                                            <a class="btn btn-warning" href="{{ route('admin.entity-reviews.reviews', $type) }}">{{ $total }}</a>
                                        @else
                                            0
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-4">
                <h2 class="h4">Need audit records</h2>
                <div class="row">
                    <div class="col-12 col-lg-8">
                        <div class="list-group">
                            @foreach($needAuditEntities as $type => $total)
                                <div class="list-group-item d-flex justify-content-between">
                                    <span class="label d-block">{{ ucwords(str_replace('_', ' ', $type)) }}</span>
                                    <span class="d-block">
                                        @if($total > 0)
                                            <a class="btn btn-warning" href="{{ route('admin.entity-reviews.audits', $type) }}">{{ $total }}</a>
                                        @else
                                            0
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
