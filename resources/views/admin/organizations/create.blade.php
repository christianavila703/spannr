@extends('layouts.app')

@section('content')

    <x-entities.create>
        <x-slot:title>Add Organization</x-slot:title>
        @include('admin.organizations.form', ['route' => route('organizations.store')])
    </x-entities.create>

@endsection
