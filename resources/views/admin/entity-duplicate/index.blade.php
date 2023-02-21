@extends('layouts.app')

@section('content')
    <style>
        .merge-buttons-column .btn:not(:disabled):not(.disabled).active,
        .merge-buttons-column .btn:not(:disabled):not(.disabled):active {
            color: #fff;
            background-color: #2e66b5;
            border-color: #2b60ab;
        }
    </style>

    <section class="container-fluid">
        <h2>
            <span class="text-capitalize">Merge Duplicates</span>
        </h2>

        @include('admin.includes.status-messages')

        <form class="js-entity-merge-form" method="post" action="{{ route('admin.entity-duplicate.merge') }}">
            @csrf
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="entity-type">Choose entity type:</label>
                        <select id="entity-type" class="form-control js-entity-type-select" name="entity_type"
                                data-action-get-list="{{ route('admin.entity-duplicate.get-entity-list-json') }}"
                                data-action-get-entity-form="{{ route('admin.entity-duplicate.get-entity-form') }}">
                            <option selected disabled></option>
                            @foreach($entityTypes as $key => $class)
                                <option value="{{$key}}">{{ \App\Helpers\Entity\MergeHelper::makeLabelFromFieldName($key) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="card js-merge-container" style="display: none;">
                <div class="row">
                    <div class="col-12">

                        <div class="card-header">
                            <div class="row">
                                <div class="col-5">
                                    <div class="form-group">
                                        <label for="master-entity" class="h5">Master entity:</label><br>
                                        <select id="master-entity" class="form-control js-master-entity-field" name="master_entity">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-5 offset-2">
                                    <div class="form-group">
                                        <label for="secondary-entity" class="h5">Secondary entity:</label><br>
                                        <select id="secondary-entity" class="form-control js-secondary-entity-field" name="secondary_entity">
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body js-merge-body">
                            Choose 2 entities to start merging
                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary js-submit-merge-btn" disabled>
                                        <i class="la la-sync"></i> Merge
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </form>

    </section>
@endsection

@push('end')
    <script type="module" src="{{ Vite::asset('resources/js/entity-duplicate-merge.js') }}"></script>
@endpush
