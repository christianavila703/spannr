@extends('layouts.app')

@section('content')

    <x-entities.create backUrl="{{ route('admin.locations.index') }}" backLabel="Locations">
        <x-slot:title>Create Location</x-slot:title>

        <div class="row">
            <div class="col-12 col-md-8 col-lg-6">
                <form method="post" action="{{ route('admin.locations.store') }}">
                    @csrf
                    <x-forms.location-field />

                    <p class="lead mt-3 mb-3"><strong>Relations:</strong></p>
                    @foreach(['organizations', 'investors', 'people', 'jobs'] as $relationName)
                        <x-forms.relation-choice-field :relationName="$relationName"/>
                    @endforeach

                    <button type="submit" class="btn btn-primary mt-3">Save changes</button>
                </form>
            </div>
        </div>
    </x-entities.create>

@endsection
