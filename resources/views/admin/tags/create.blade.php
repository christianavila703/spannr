@extends('layouts.app')

@section('content')

    <x-entities.create backUrl="{{ route('admin.tags.index') }}" backLabel="Tags">
        <x-slot:title>Add Tag</x-slot:title>
        <div class="row">
            <div class="col-12 col-md-6 col-lg-5">
                @include('admin.tags.form', ['route' => route('admin.tags.store')])
            </div>
        </div>
    </x-entities.create>

@endsection
