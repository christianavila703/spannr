@extends('layouts.app')

@section('content')

    <x-entities.create backUrl="{{ route('admin.locations.show', $location->slug) }}" backLabel="{{ $location->name }}">
        <x-slot:title>Edit Location</x-slot:title>

        <div class="row">
            <div class="col-12 col-md-8 col-lg-6">
                <p class="lead mb-3"><strong>Current:</strong> {{ $location->name }}</p>
                <form method="post" action="{{ route('admin.locations.update', $location->slug) }}">
                    @csrf
                    @method('put')
                    <x-forms.location-field :location="$location"/>

                    <p class="lead mt-3 mb-3"><strong>Relations:</strong></p>
                    @foreach(['organizations', 'investors', 'people', 'jobs'] as $relationName)
                        <x-forms.relation-choice-field :relationName="$relationName" :entity="$location" />
                    @endforeach

                    <button type="submit" class="btn btn-primary mt-3">Save changes</button>
                </form>

                <form method="post" action="{{ route('admin.locations.destroy', $location->id) }}">
                    @csrf
                    @method('delete')
                    <button type="submit" class="btn btn-danger mt-3 confirm-button">Delete</button>
                </form>
            </div>
        </div>
    </x-entities.create>

@endsection
