@extends('layouts.app')

@section('content')

    <x-entities.show>
        <x-slot:title>Add Location to {{ $entity->name }}</x-slot:title>
        <x-slot:buttons></x-slot:buttons>

        <div class="row">
            <div class="col-12 col-md-8 col-lg-6">
                <form method="POST" action="{{ route('admin.locations.create-from-entity') }}">
                    @csrf
                    <x-forms.location-field />

                    <div class="mt-3">
                        <input type="hidden" name="entity_id" value="{{ $entity->id }}">
                        <input type="hidden" name="entity_type" value="{{$entityType }}">
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </x-entities.show>

@endsection
