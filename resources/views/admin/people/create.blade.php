@extends('layouts.app')

@section('content')

    <x-entities.create>
        <x-slot:title>Add Person</x-slot:title>
        @include('admin.people.form', ['route' => route('people.store')])
    </x-entities.create>

@endsection
